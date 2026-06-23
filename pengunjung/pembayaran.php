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

    // Cek apakah pembayaran untuk pesanan ini sudah terdaftar sebelumnya
    $cek_pembayaran = mysqli_query($conn, "SELECT * FROM pembayaran WHERE pesanan_id='$pesanan_id'");
    if(mysqli_num_rows($cek_pembayaran) > 0) {
        // Update saja
        mysqli_query($conn,"UPDATE pembayaran SET metode='$metode', status='pending' WHERE pesanan_id='$pesanan_id'");
    } else {
        // Buat baru
        mysqli_query($conn,"INSERT INTO pembayaran (pesanan_id, metode, status) VALUES ('$pesanan_id','$metode','pending')");
    }

    header("location:pembayaran.php?pesan=sukses_tambah");
    exit;
}

/* ================= HAPUS ================= */
if(isset($_GET['hapus'])){
    $id = intval($_GET['hapus']);
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
            <div class="col-lg-4 mb-4">
                <div class="card-premium">
                    <h5 class="card-title-premium"><i class="fa fa-plus-circle"></i> Konfirmasi Pembayaran</h5>
                    
                    <form method="POST">
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

                        <div class="mb-4">
                            <label class="form-label-muted">Metode Pembayaran</label>
                            <select name="metode" class="form-select" required>
                                <option value="">-- Pilih Metode --</option>
                                <option value="Transfer Bank">Transfer Bank</option>
                                <option value="E-Wallet">E-Wallet (OVO/GoPay/Dana)</option>
                                <option value="COD">Bayar di Tempat (COD)</option>
                            </select>
                        </div>
                        
                        <button name="tambah" class="btn-premium-primary w-100 justify-content-center">
                            <i class="fa fa-paper-plane"></i> Kirim Konfirmasi
                        </button>
                    </form>
                </div>
            </div>

            <!-- DAFTAR RIWAYAT -->
            <div class="col-lg-8">
                <div class="card-premium">
                    <h5 class="card-title-premium mb-4"><i class="fa fa-history"></i> Riwayat Pembayaran</h5>
                    
                    <div class="table-responsive">
                        <table class="table-premium">
                            <thead>
                                <tr>
                                    <th>Pesanan</th>
                                    <th>Metode</th>
                                    <th>Total Tagihan</th>
                                    <th>Status Verifikasi</th>
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
                                                    <i class="fa fa-receipt"></i>
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
                                            <button type="button" class="btn-action-premium btn-delete-premium" onclick="konfirmasiHapus('?hapus=<?= $d['id'] ?>')" title="Hapus Data">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
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