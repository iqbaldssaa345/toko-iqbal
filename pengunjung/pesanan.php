<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role']!="pengunjung"){
    header("location:../login.php");
    exit;
}
include '../koneksi.php';

$user_id = $_SESSION['id'];
$q_pengunjung = mysqli_query($conn, "SELECT username FROM users WHERE id='$user_id'");
$d_pengunjung = mysqli_fetch_assoc($q_pengunjung);
$nama_pengunjung = $d_pengunjung['username'];

/* ================= TAMBAH (REDIRECT KE PESAN) ================= */
if(isset($_POST['tambah'])){
    $produk_id = intval($_POST['produk_id']);
    $jumlah = intval($_POST['jumlah']);
    header("location:pesan.php?id=$produk_id&jumlah=$jumlah");
    exit;
}

/* ================= HAPUS SINKRONISASI DATABASE ================= */
if(isset($_GET['hapus'])){
    $id = intval($_GET['hapus']);

    // Cari pesanan_id, produk_id, dan jumlah dari detail_pesanan
    $q_dp = mysqli_query($conn, "SELECT pesanan_id, produk_id, jumlah FROM detail_pesanan dp JOIN pesanan p ON dp.pesanan_id=p.id WHERE dp.id='$id' AND p.user_id='$user_id'");
    if(mysqli_num_rows($q_dp) > 0) {
        $r_dp = mysqli_fetch_assoc($q_dp);
        $pesanan_id = $r_dp['pesanan_id'];
        $produk_id = $r_dp['produk_id'];
        $jumlah = intval($r_dp['jumlah']);
        
        // Kembalikan stok produk
        mysqli_query($conn, "UPDATE produk SET stok = stok + $jumlah WHERE id='$produk_id'");
        
        // Hapus detail, pembayaran, lalu pesanan utamanya
        mysqli_query($conn,"DELETE FROM detail_pesanan WHERE id='$id'");
        mysqli_query($conn,"DELETE FROM pembayaran WHERE pesanan_id='$pesanan_id'");
        mysqli_query($conn,"DELETE FROM pesanan WHERE id='$pesanan_id' AND user_id='$user_id'");
    }

    header("location:pesanan.php?pesan=sukses_hapus");
    exit;
}

/* ================= DATA ================= */
$data = mysqli_query($conn,"
SELECT dp.*, pr.nama, pr.harga, pr.gambar, pr.pre_order, o.nama_jasa, o.estimasi as estimasi_ongkir, p.id as pesanan_id, p.total as pesanan_total, p.status_pengiriman, pb.status as status_bayar 
FROM detail_pesanan dp
JOIN pesanan p ON dp.pesanan_id=p.id
JOIN produk pr ON dp.produk_id=pr.id
LEFT JOIN ongkir o ON p.ongkir_id=o.id
LEFT JOIN pembayaran pb ON p.id = pb.pesanan_id
WHERE p.user_id='$user_id'
ORDER BY dp.id DESC
");

/* ================= PRODUK FOR DROP-DOWN ================= */
$produk = mysqli_query($conn,"SELECT * FROM produk");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Saya - Catering Ibu Iqbal</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/dashboard.css" rel="stylesheet">
    
    <style>
        .pesanan-box {
            background: white;
            border: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 18px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.01);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .pesanan-box:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(212,175,55,0.08);
            border-color: rgba(212,175,55,0.2);
        }

        .pesanan-img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 12px;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .pesanan-details {
            flex-grow: 1;
        }

        .pesanan-details h6 {
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 5px;
            font-size: 1.1rem;
        }

        .pesanan-meta {
            color: #8c8c9a;
            font-size: 0.9rem;
            margin-bottom: 8px;
        }

        .pesanan-price {
            font-weight: 700;
            color: var(--gold-dark);
            font-size: 1.15rem;
        }

        @media (max-width: 768px) {
            .pesanan-box {
                flex-direction: column;
                text-align: center;
                padding: 25px;
            }
            .pesanan-details {
                width: 100%;
            }
            .action-wrapper {
                justify-content: center !important;
                margin-top: 15px;
                width: 100%;
            }
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
        $topbar_title = "Pesanan Saya";
        $user_name = $nama_pengunjung;
        $topbar_icon = '<i class="fa fa-shopping-cart text-primary"></i>';
        include '../includes/topbar.php';
        ?>

        <div class="row">
            <!-- FORM PESAN CEPAT -->
            <div class="col-lg-4 mb-4">
                <div class="card-premium">
                    <h5 class="card-title-premium"><i class="fa fa-bolt"></i> Pesan Cepat</h5>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label-muted">Pilih Produk</label>
                            <select name="produk_id" class="form-select" required>
                                <option value="">-- Pilih Produk --</option>
                                <?php while($p=mysqli_fetch_array($produk)){ ?>
                                <option value="<?= $p['id'] ?>">
                                    <?= htmlspecialchars($p['nama']) ?> - Rp <?= number_format($p['harga'], 0, ',', '.') ?>
                                </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label-muted">Jumlah Porsi</label>
                            <input type="number" name="jumlah" class="form-control" placeholder="Masukkan Jumlah Porsi" min="1" required>
                        </div>
                        
                        <button name="tambah" class="btn-premium-primary w-100 justify-content-center">
                            <i class="fa fa-shopping-bag"></i> Lanjutkan ke Pengiriman
                        </button>
                    </form>
                </div>
            </div>

            <!-- DAFTAR PESANAN -->
            <div class="col-lg-8">
                <div class="card-premium">
                    <h5 class="card-title-premium mb-4"><i class="fa fa-list"></i> Daftar Pesanan Aktif</h5>
                    
                    <div>
                        <?php if(mysqli_num_rows($data) > 0){ ?>
                            <?php while($d=mysqli_fetch_array($data)){ ?>
                            <div class="pesanan-box">
                                <?php if(!empty($d['gambar']) && file_exists("../upload/".$d['gambar'])){ ?>
                                    <img src="../upload/<?= htmlspecialchars($d['gambar']) ?>" class="pesanan-img" alt="<?= htmlspecialchars($d['nama']) ?>">
                                <?php } else { ?>
                                    <div class="pesanan-img bg-light d-flex align-items-center justify-content-center text-muted">
                                        <i class="fa fa-image fa-2x"></i>
                                    </div>
                                <?php } ?>

                                <div class="pesanan-details">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                                        <div>
                                            <h6><?= htmlspecialchars($d['nama']) ?></h6>
                                            <div class="pesanan-meta">
                                                ID Pesanan: #<?= $d['pesanan_id'] ?> &bull; 
                                                Harga: Rp <?= number_format($d['harga'], 0, ',', '.') ?> &bull; 
                                                Jumlah: <?= $d['jumlah'] ?> Porsi
                                            </div>
                                            <div class="pesanan-meta mt-1">
                                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 rounded-pill me-1" style="font-size: 0.75rem;">
                                                    <i class="fa fa-clock me-1"></i> Pre-Order: <?= htmlspecialchars($d['pre_order'] ? $d['pre_order'] : '1 Hari') ?>
                                                </span>
                                                <?php if($d['nama_jasa']) { ?>
                                                <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-2 py-1 rounded-pill me-1" style="font-size: 0.75rem;">
                                                    <i class="fa fa-truck me-1"></i> <?= htmlspecialchars($d['nama_jasa']) ?> (<?= htmlspecialchars($d['estimasi_ongkir'] ? $d['estimasi_ongkir'] : '1-2 Hari') ?>)
                                                </span>
                                                <?php } ?>
                                                <?php 
                                                    $st_pengirim = isset($d['status_pengiriman']) && !empty($d['status_pengiriman']) ? $d['status_pengiriman'] : 'Proses (Estimasi)';
                                                    if($st_pengirim == 'Sampai') { 
                                                ?>
                                                    <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle px-2 py-1 rounded-pill" style="font-size: 0.75rem;">
                                                        <i class="fa fa-check-circle me-1"></i> Status: Sampai
                                                    </span>
                                                <?php } else { ?>
                                                    <span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle px-2 py-1 rounded-pill" style="font-size: 0.75rem;">
                                                        <i class="fa fa-truck-fast me-1"></i> Status: <?= htmlspecialchars($st_pengirim) ?>
                                                    </span>
                                                <?php } ?>
                                            </div>
                                            <div class="pesanan-price mt-2">Total: Rp <?= number_format($d['subtotal'], 0, ',', '.') ?></div>
                                        </div>
                                        
                                        <div class="d-flex gap-2 align-items-center action-wrapper">
                                            <?php if($d['status_bayar'] == 'lunas') { ?>
                                                <span class="status-badge lunas"><i class="fa fa-check-circle"></i> Lunas</span>
                                            <?php } else { ?>
                                                <?php if($d['status_bayar'] == 'pending') { ?>
                                                    <span class="status-badge pending"><i class="fa fa-clock"></i> Pending (Menunggu)</span>
                                                <?php } else { ?>
                                                    <a href="pembayaran.php?pesanan_id=<?= $d['pesanan_id'] ?>" class="btn-premium-primary py-2 px-3 shadow-none" style="font-size: 0.825rem; border-radius: 20px;">
                                                        <i class="fa fa-credit-card"></i> Bayar Sekarang
                                                    </a>
                                                <?php } ?>
                                                
                                                <button type="button" class="btn-action-premium btn-delete-premium" onclick="konfirmasiHapus('?hapus=<?= $d['id'] ?>')" title="Batalkan Pesanan">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                        <?php } else { ?>
                            <div class="text-center py-5 text-muted">
                                <i class="fa fa-shopping-basket fa-3x mb-3 text-light"></i>
                                <p>Kamu belum memiliki pesanan aktif.<br>Silakan pesan makanan favoritmu terlebih dahulu!</p>
                            </div>
                        <?php } ?>
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
            title: 'Batalkan Pesanan?',
            text: "Pesanan ini akan dibatalkan secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Batalkan!',
            cancelButtonText: 'Kembali',
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
            Swal.fire({ title: 'Berhasil!', text: 'Pesanan berhasil ditambahkan.', icon: 'success', confirmButtonClass: 'btn btn-primary rounded-pill px-4', buttonsStyling: false });
        <?php }elseif($_GET['pesan'] == 'sukses_hapus'){ ?>
            Swal.fire({ title: 'Berhasil!', text: 'Pesanan berhasil dibatalkan.', icon: 'success', confirmButtonClass: 'btn btn-primary rounded-pill px-4', buttonsStyling: false });
        <?php } ?>
    </script>
    <?php } ?>

</body>
</html>
