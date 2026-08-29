<?php
session_start();
include '../koneksi.php';

if(!isset($_SESSION['id']) || $_SESSION['role']!="pengunjung"){
    header("location:../login.php");
    exit;
}

$user_id = $_SESSION['id'];
$q_pengunjung = mysqli_query($conn, "SELECT username FROM users WHERE id='$user_id'");
$d_pengunjung = mysqli_fetch_assoc($q_pengunjung);
$nama_pengunjung = $d_pengunjung['username'];

$selected_pesanan_id = isset($_GET['pesanan_id']) ? intval($_GET['pesanan_id']) : 0;
if ($selected_pesanan_id > 0) {
    $cek_pesanan = mysqli_query($conn, "SELECT * FROM pesanan WHERE id='$selected_pesanan_id' AND user_id='$user_id'");
    if (mysqli_num_rows($cek_pesanan) == 0) {
        $selected_pesanan_id = 0;
    }
}

/* ================= TAMBAH ================= */
if(isset($_POST['tambah'])){
    $pesanan_id = intval($_POST['pesanan_id']);
    $metode = $_POST['metode'];

    $bukti_pembayaran = "";
    if (isset($_FILES['bukti_pembayaran']) && $_FILES['bukti_pembayaran']['error'] == UPLOAD_ERR_OK) {
        $file_name = $_FILES['bukti_pembayaran']['name'];
        $file_tmp = $_FILES['bukti_pembayaran']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        $allowed_ext = array("jpg", "jpeg", "png");
        if (in_array($file_ext, $allowed_ext)) {
            $new_filename = "pembayaran_" . time() . "_" . rand(1000, 9999) . "." . $file_ext;
            if (move_uploaded_file($file_tmp, "../upload/" . $new_filename)) {
                $bukti_pembayaran = $new_filename;
            }
        }
    }

    // Cek apakah pembayaran untuk pesanan ini sudah terdaftar sebelumnya
    $cek_pembayaran = mysqli_query($conn, "SELECT * FROM pembayaran WHERE pesanan_id='$pesanan_id'");
    if(mysqli_num_rows($cek_pembayaran) > 0) {
        $d_old = mysqli_fetch_assoc($cek_pembayaran);
        // Hapus file lama jika ada file baru diupload
        if ($bukti_pembayaran != "") {
            if ($d_old['bukti_pembayaran'] && file_exists("../upload/" . $d_old['bukti_pembayaran'])) {
                unlink("../upload/" . $d_old['bukti_pembayaran']);
            }
            mysqli_query($conn,"UPDATE pembayaran SET metode='$metode', status='pending', bukti_pembayaran='$bukti_pembayaran' WHERE pesanan_id='$pesanan_id'");
        } else {
            // Update metode saja tanpa ganti bukti pembayaran
            mysqli_query($conn,"UPDATE pembayaran SET metode='$metode', status='pending' WHERE pesanan_id='$pesanan_id'");
        }
    } else {
        // Buat baru
        mysqli_query($conn,"INSERT INTO pembayaran (pesanan_id, metode, status, bukti_pembayaran) VALUES ('$pesanan_id','$metode','pending','$bukti_pembayaran')");
    }

    header("location:pembayaran.php?pesan=sukses_tambah");
    exit;
}

/* ================= HAPUS ================= */
if(isset($_GET['hapus'])){
    $id = intval($_GET['hapus']);
    
    // Cari bukti pembayaran dulu untuk didelete dari disk
    $cek_del = mysqli_query($conn, "SELECT bukti_pembayaran FROM pembayaran WHERE id='$id'");
    if (mysqli_num_rows($cek_del) > 0) {
        $row_del = mysqli_fetch_assoc($cek_del);
        $file_del = $row_del['bukti_pembayaran'];
        if ($file_del && file_exists("../upload/" . $file_del)) {
            unlink("../upload/" . $file_del);
        }
    }
    
    mysqli_query($conn,"DELETE FROM pembayaran WHERE id='$id'");
    header("location:pembayaran.php?pesan=sukses_hapus");
    exit;
}

/* ================= DATA LIST ================= */
$data = mysqli_query($conn,"
SELECT pembayaran.*, pesanan.total 
FROM pembayaran 
JOIN pesanan ON pembayaran.pesanan_id = pesanan.id
WHERE pesanan.user_id='$user_id'
ORDER BY pembayaran.id DESC
");

/* ================= FILTER PESANAN DROPDOWN ================= */
// Hanya tampilkan pesanan yang totalnya tidak NULL, dan belum lunas
$pesanan = mysqli_query($conn,"
    SELECT p.* 
    FROM pesanan p
    LEFT JOIN pembayaran pb ON p.id = pb.pesanan_id
    WHERE p.user_id='$user_id' 
    AND p.total IS NOT NULL 
    AND (pb.status IS NULL OR pb.status = 'pending')
    GROUP BY p.id
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - Catering Ibu Iqbal</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/dashboard.css" rel="stylesheet">

    <style>
        .upload-zone {
            border: 2px dashed rgba(212, 175, 55, 0.4);
            border-radius: 16px;
            padding: 25px 15px;
            text-align: center;
            background: rgba(212, 175, 55, 0.02);
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }
        .upload-zone:hover {
            border-color: #D4AF37;
            background: rgba(212, 175, 55, 0.05);
        }
        .upload-zone i {
            font-size: 2.2rem;
            color: #D4AF37;
            margin-bottom: 10px;
            display: block;
        }
        .upload-zone input[type="file"] {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }
        .upload-preview {
            margin-top: 15px;
            display: none;
            text-align: center;
        }
        .upload-preview img {
            max-width: 100%;
            max-height: 180px;
            border-radius: 12px;
            border: 1px solid rgba(0, 0, 0, 0.1);
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        .payment-instruction-card {
            border-radius: 16px;
            padding: 18px;
            margin-bottom: 20px;
            background: #1e1e24;
            color: #ffffff;
            border: 1px solid rgba(212, 175, 55, 0.3);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            display: none;
        }
        .payment-instruction-card.show {
            display: block;
            animation: fadeIn 0.3s ease-in-out;
        }
        .payment-instruction-card h6 {
            color: #D4AF37;
            font-weight: 700;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .instruction-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            padding: 8px 0;
        }
        .instruction-item:last-child {
            border-bottom: none;
        }
        .copy-btn {
            background: transparent;
            border: 1px solid rgba(212, 175, 55, 0.5);
            color: #D4AF37;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 0.7rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .copy-btn:hover {
            background: #D4AF37;
            color: #1e1e24;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .text-gold {
            color: #D4AF37;
        }
    </style>
</head>

<body>

    <!-- SIDEBAR -->
    <?php include '../includes/sidebar_pengunjung.php'; ?>

    <!-- MAIN CONTENT -->
    <div class="main-content">

        <!-- TOPBAR -->
        <?php 
        $topbar_title = "Pembayaran Saya";
        $user_name = $nama_pengunjung;
        $topbar_icon = '<i class="fa fa-credit-card text-primary"></i>';
        include '../includes/topbar.php';
        ?>

        <div class="row">
            <!-- FORM KONFIRMASI -->
            <div class="col-lg-5 mb-4">
                <!-- INSTRUCTIONS CARDS -->
                <div id="transferBcaCard" class="payment-instruction-card">
                    <h6><i class="fa fa-university text-gold"></i> Rekening Transfer Bank</h6>
                    <div class="instruction-item">
                        <div>
                            <small class="text-muted d-block">Bank BCA</small>
                            <strong id="rekBca">7710294819</strong>
                        </div>
                        <button type="button" class="copy-btn" onclick="copyText('rekBca', this)"><i class="fa fa-clone"></i> Salin</button>
                    </div>
                    <div class="instruction-item">
                        <div>
                            <small class="text-muted d-block">Bank Mandiri</small>
                            <strong id="rekMandiri">1330029384910</strong>
                        </div>
                        <button type="button" class="copy-btn" onclick="copyText('rekMandiri', this)"><i class="fa fa-clone"></i> Salin</button>
                    </div>
                    <div class="instruction-item">
                        <div>
                            <small class="text-muted d-block">Atas Nama Penerima</small>
                            <strong>Catering Ibu Iqbal</strong>
                        </div>
                    </div>
                </div>

                <div id="eWalletCard" class="payment-instruction-card">
                    <h6><i class="fa fa-qrcode text-gold"></i> E-Wallet (OVO / GoPay / Dana)</h6>
                    <div class="instruction-item">
                        <div>
                            <small class="text-muted d-block">Nomor OVO / GoPay / DANA</small>
                            <strong id="noEwallet">081234567890</strong>
                        </div>
                        <button type="button" class="copy-btn" onclick="copyText('noEwallet', this)"><i class="fa fa-clone"></i> Salin</button>
                    </div>
                    <div class="instruction-item">
                        <div>
                            <small class="text-muted d-block">Atas Nama Penerima</small>
                            <strong>Catering Ibu Iqbal</strong>
                        </div>
                    </div>
                </div>

                <div class="card-premium">
                    <h5 class="card-title-premium"><i class="fa fa-plus-circle text-gold"></i> Konfirmasi Pembayaran</h5>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label-muted">Pilih Pesanan Anda</label>
                            <select name="pesanan_id" class="form-select" required>
                                <option value="">-- Pilih Pesanan --</option>
                                <?php mysqli_data_seek($pesanan, 0); while($p=mysqli_fetch_array($pesanan)){ ?>
                                <option value="<?= $p['id'] ?>" <?= ($selected_pesanan_id == $p['id']) ? 'selected' : '' ?>>
                                    #<?= $p['id'] ?> - Rp <?= number_format($p['total'], 0, ',', '.') ?>
                                </option>
                                <?php } ?>
                                
                                <?php 
                                if ($selected_pesanan_id > 0) {
                                    mysqli_data_seek($pesanan, 0);
                                    $printed = false;
                                    while($p=mysqli_fetch_array($pesanan)) {
                                        if ($p['id'] == $selected_pesanan_id) { $printed = true; break; }
                                    }
                                    if (!$printed) {
                                        $p_sel = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM pesanan WHERE id='$selected_pesanan_id'"));
                                        if ($p_sel) {
                                        ?>
                                        <option value="<?= $p_sel['id'] ?>" selected>
                                            #<?= $p_sel['id'] ?> - Rp <?= number_format($p_sel['total'], 0, ',', '.') ?>
                                        </option>
                                        <?php
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label-muted">Metode Pembayaran</label>
                            <select id="selectMetode" name="metode" class="form-select" required>
                                <option value="">-- Pilih Metode --</option>
                                <option value="Transfer Bank">Transfer Bank</option>
                                <option value="E-Wallet">E-Wallet (OVO/GoPay/Dana)</option>
                                <option value="COD">Bayar di Tempat (COD)</option>
                            </select>
                        </div>

                        <!-- UPLOAD SECTION -->
                        <div id="uploadSection" class="mb-4" style="display: none;">
                            <label class="form-label-muted">Unggah Bukti Pembayaran</label>
                            <div class="upload-zone">
                                <i class="fa fa-cloud-upload-alt"></i>
                                <span class="d-block text-dark fw-medium">Pilih berkas bukti pembayaran</span>
                                <small class="text-muted">Mendukung format JPG, JPEG, PNG (Maks. 2MB)</small>
                                <input type="file" id="buktiPembayaran" name="bukti_pembayaran" accept="image/*">
                            </div>
                            <div id="previewContainer" class="upload-preview">
                                <img id="uploadPreview" src="" alt="Pratinjau Bukti">
                                <small class="d-block text-success mt-1"><i class="fa fa-check-circle"></i> Berkas siap diunggah</small>
                            </div>
                        </div>
                        
                        <button name="tambah" class="btn-premium-primary w-100 justify-content-center">
                            <i class="fa fa-paper-plane"></i> Kirim Konfirmasi
                        </button>
                    </form>
                </div>
            </div>

            <!-- DAFTAR RIWAYAT -->
            <div class="col-lg-7">
                <div class="card-premium">
                    <h5 class="card-title-premium mb-4"><i class="fa fa-history text-gold"></i> Riwayat Pembayaran</h5>
                    
                    <div class="table-responsive">
                        <table class="table-premium">
                            <thead>
                                <tr>
                                    <th>Pesanan</th>
                                    <th>Metode</th>
                                    <th>Bukti</th>
                                    <th>Tagihan</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(mysqli_num_rows($data) > 0){ ?>
                                    <?php while($d=mysqli_fetch_array($data)){ ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="bg-light p-2 rounded-circle text-primary d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                                    <i class="fa fa-receipt text-gold"></i>
                                                </div>
                                                <strong>#<?= htmlspecialchars($d['pesanan_id']) ?></strong>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-dark fw-medium">
                                                <i class="fa fa-wallet me-1 text-muted"></i> <?= htmlspecialchars($d['metode']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($d['bukti_pembayaran'] != "" && file_exists('../upload/' . $d['bukti_pembayaran'])) { ?>
                                                <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-1" onclick="showProofModal('<?= '../upload/' . htmlspecialchars($d['bukti_pembayaran']) ?>')">
                                                    <i class="fa fa-image text-gold"></i> Lihat
                                                </button>
                                            <?php } else { ?>
                                                <span class="text-muted small">-</span>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <span class="fw-bold">
                                                Rp <?= number_format($d['total'], 0, ',', '.') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if($d['status']=="pending"){ ?>
                                                <span class="status-badge pending">
                                                    <i class="fa fa-clock"></i> Pending
                                                </span>
                                            <?php } else { ?>
                                                <span class="status-badge lunas">
                                                    <i class="fa fa-check-circle"></i> Lunas
                                                </span>
                                            <?php } ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if($d['status']=="pending"){ ?>
                                                <button type="button" class="btn-action-premium btn-delete-premium" onclick="konfirmasiHapus('?hapus=<?= $d['id'] ?>')" title="Hapus Data">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            <?php } else { ?>
                                                <button type="button" class="btn-action-premium border-0" style="background: rgba(0,0,0,0.03); color: #8c8c9a;" disabled title="Sudah Terverifikasi">
                                                    <i class="fa fa-lock"></i>
                                                </button>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            <i class="fa fa-credit-card fa-3x mb-3 text-light"></i><br>
                                            Belum ada riwayat pembayaran.
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL PREVIEW BUKTI -->
    <div class="modal fade" id="modalBukti" tabindex="-1" aria-labelledby="modalBuktiLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 22px; border: none; overflow: hidden; background: #ffffff; box-shadow: 0 15px 50px rgba(0,0,0,0.15);">
                <div class="modal-header p-4" style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                    <h5 class="modal-title fw-bold text-dark" id="modalBuktiLabel"><i class="fa fa-file-invoice text-gold me-2"></i>Bukti Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-4" style="background: #F8F9FA;">
                    <img id="imgBuktiFull" src="" alt="Bukti Pembayaran" class="img-fluid rounded-4 shadow-sm" style="max-height: 450px; object-fit: contain; border: 1px solid rgba(0,0,0,0.08);">
                </div>
                <div class="modal-footer p-4" style="border-top: 1px solid rgba(0,0,0,0.05); justify-content: center;">
                    <a id="btnDownloadBukti" href="" download class="btn-premium-primary px-4 justify-content-center">
                        <i class="fa fa-download"></i> Unduh Gambar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    // Copy Clipboard
    function copyText(id, btn) {
        const text = document.getElementById(id).innerText;
        navigator.clipboard.writeText(text).then(() => {
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fa fa-check"></i> Disalin';
            setTimeout(() => {
                btn.innerHTML = originalText;
            }, 2000);
        });
    }

    // Modal Bukti Pembayaran
    function showProofModal(imgPath) {
        document.getElementById('imgBuktiFull').src = imgPath;
        document.getElementById('btnDownloadBukti').href = imgPath;
        const modal = new bootstrap.Modal(document.getElementById('modalBukti'));
        modal.show();
    }

    // Dynamic Option Inputs
    const selectMetode = document.getElementById('selectMetode');
    const transferBcaCard = document.getElementById('transferBcaCard');
    const eWalletCard = document.getElementById('eWalletCard');
    const uploadSection = document.getElementById('uploadSection');

    selectMetode.addEventListener('change', function() {
        transferBcaCard.classList.remove('show');
        eWalletCard.classList.remove('show');
        uploadSection.style.display = 'none';

        if (this.value === 'Transfer Bank') {
            transferBcaCard.classList.add('show');
            uploadSection.style.display = 'block';
        } else if (this.value === 'E-Wallet') {
            eWalletCard.classList.add('show');
            uploadSection.style.display = 'block';
        }
    });

    // Image File Preview
    const inputGambar = document.getElementById('buktiPembayaran');
    const previewContainer = document.getElementById('previewContainer');
    const uploadPreview = document.getElementById('uploadPreview');

    inputGambar.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                uploadPreview.src = e.target.result;
                previewContainer.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            previewContainer.style.display = 'none';
        }
    });

    function konfirmasiHapus(url) {
        Swal.fire({
            title: 'Hapus Konfirmasi?',
            text: "Data pembayaran ini akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-danger rounded-pill px-4 mx-2',
                cancelButton: 'btn btn-secondary rounded-pill px-4 mx-2'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        })
    }
    </script>
    <?php if(isset($_GET['pesan'])){ ?>
    <script>
        <?php if($_GET['pesan'] == 'sukses_tambah'){ ?>
            Swal.fire({ title: 'Berhasil!', text: 'Konfirmasi pembayaran berhasil dikirim.', icon: 'success', confirmButtonClass: 'btn btn-primary rounded-pill px-4', buttonsStyling: false });
        <?php }elseif($_GET['pesan'] == 'sukses_hapus'){ ?>
            Swal.fire({ title: 'Berhasil!', text: 'Konfirmasi pembayaran berhasil dihapus.', icon: 'success', confirmButtonClass: 'btn btn-primary rounded-pill px-4', buttonsStyling: false });
        <?php } ?>
    </script>
    <?php } ?>

</body>
</html>
