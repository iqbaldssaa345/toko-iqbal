<?php
session_start();
if($_SESSION['role']!="petugas"){
    header("location:../login.php");
    exit;
}
include '../koneksi.php';

// Data Petugas
$id_petugas = $_SESSION['id'];
$q_petugas = mysqli_query($conn, "SELECT username FROM users WHERE id='$id_petugas'");
$d_petugas = mysqli_fetch_assoc($q_petugas);
$nama_petugas = $d_petugas['username'];

/* HITUNG DATA */
$user = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM users"));
$produk = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM produk"));
$pesanan = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM pesanan"));

// STATISTIK KEUANGAN & PENJUALAN
$q_pemasukan = mysqli_query($conn, "SELECT SUM(total) as total FROM pesanan WHERE id IN (SELECT DISTINCT pesanan_id FROM pembayaran WHERE status='lunas')");
$d_pemasukan = mysqli_fetch_assoc($q_pemasukan);
$total_pemasukan = $d_pemasukan['total'] ? $d_pemasukan['total'] : 0;

$q_penjualan = mysqli_query($conn, "SELECT SUM(total) as total FROM pesanan");
$d_penjualan = mysqli_fetch_assoc($q_penjualan);
$total_penjualan = $d_penjualan['total'] ? $d_penjualan['total'] : 0;

// DATA PESANAN TERBARU
$recent = mysqli_query($conn,"SELECT * FROM pesanan ORDER BY id DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Petugas - Catering Ibu Iqbal</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/dashboard.css" rel="stylesheet">
</head>

<body>

    <!-- SIDEBAR -->
    <?php include '../includes/sidebar_petugas.php'; ?>

    <!-- MAIN CONTENT -->
    <div class="main-content">

        <!-- TOPBAR -->
        <?php 
        $topbar_title = "Dashboard Petugas";
        $user_name = $nama_petugas;
        $topbar_icon = '<i class="fa fa-home text-primary"></i>';
        include '../includes/topbar.php';
        ?>

        <!-- LAPORAN KEUANGAN & PENJUALAN -->
        <h6 class="section-title-dashboard"><i class="fa-solid fa-wallet"></i> Laporan Keuangan & Penjualan</h6>
        <div class="row g-4 mb-5">
            <div class="col-lg-6 col-md-12">
                <div class="stat-card stat-card-gold">
                    <div class="stat-card-val">Rp <?= number_format($total_pemasukan, 0, ',', '.') ?></div>
                    <p class="stat-card-title">Total Pemasukan (Lunas)</p>
                    <i class="fa fa-circle-check stat-card-icon"></i>
                </div>
            </div>

            <div class="col-lg-6 col-md-12">
                <div class="stat-card stat-card-slate">
                    <div class="stat-card-val">Rp <?= number_format($total_penjualan, 0, ',', '.') ?></div>
                    <p class="stat-card-title">Total Penjualan (Semua Pesanan)</p>
                    <i class="fa fa-chart-line stat-card-icon"></i>
                </div>
            </div>
        </div>

        <!-- RINGKASAN OPERASIONAL -->
        <h6 class="section-title-dashboard"><i class="fa-solid fa-cubes"></i> Ringkasan Operasional</h6>
        <div class="row g-4 mb-5">
            <div class="col-lg-4 col-md-6">
                <div class="stat-card stat-card-bg-1">
                    <div class="stat-card-val"><?= $user ?></div>
                    <p class="stat-card-title">Total User</p>
                    <i class="fa fa-users stat-card-icon"></i>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="stat-card stat-card-bg-2">
                    <div class="stat-card-val"><?= $produk ?></div>
                    <p class="stat-card-title">Total Produk</p>
                    <i class="fa fa-box-open stat-card-icon"></i>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="stat-card stat-card-bronze">
                    <div class="stat-card-val"><?= $pesanan ?></div>
                    <p class="stat-card-title">Total Pesanan</p>
                    <i class="fa fa-receipt stat-card-icon"></i>
                </div>
            </div>
        </div>

        <!-- DATA PESANAN TERBARU -->
        <div class="row">
            <div class="col-12">
                <div class="card-premium">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title-premium mb-0">
                            <i class="fa fa-star text-warning"></i> Pesanan Terbaru
                        </h5>
                        
                        <a href="pesanan.php" class="btn-premium-primary">
                            <i class="fa fa-arrow-right"></i> Kelola Pesanan
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table-premium">
                            <thead>
                                <tr>
                                    <th>ID Pesanan</th>
                                    <th>User ID</th>
                                    <th>Alamat ID</th>
                                    <th>Ongkir ID</th>
                                    <th>Tanggal</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($d = mysqli_fetch_assoc($recent)){ ?>
                                <tr>
                                    <td><strong>#<?= $d['id'] ?></strong></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="bg-light p-2 rounded-circle text-primary d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                                <i class="fa fa-user"></i>
                                            </div>
                                            <?= htmlspecialchars($d['user_id']) ?>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($d['alamat_id'] ? $d['alamat_id'] : '-') ?></td>
                                    <td><?= htmlspecialchars($d['ongkir_id'] ? $d['ongkir_id'] : '-') ?></td>
                                    <td><?= htmlspecialchars($d['tanggal']) ?></td>
                                    <td class="text-end">
                                        <span class="price-badge">Rp <?= number_format($d['total'] ? $d['total'] : 0, 0, ',', '.') ?></span>
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
</body>
</html>