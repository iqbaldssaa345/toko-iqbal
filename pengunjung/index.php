<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role']!="pengunjung"){
    header("location:../login.php");
    exit;
}
include '../koneksi.php';

$id_pengunjung = $_SESSION['id'];
$q_pengunjung = mysqli_query($conn, "SELECT username FROM users WHERE id='$id_pengunjung'");
$d_pengunjung = mysqli_fetch_assoc($q_pengunjung);
$nama_pengunjung = $d_pengunjung['username'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pengunjung - Catering Ibu Iqbal</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/dashboard.css" rel="stylesheet">
    
    <style>
        .header-welcome {
            background: linear-gradient(135deg, #1A1A1D 0%, #2D2D30 100%);
            border-radius: 22px;
            padding: 35px;
            color: white;
            margin-bottom: 35px;
            box-shadow: var(--card-shadow);
            position: relative;
            overflow: hidden;
        }

        .header-welcome::after {
            content: '🍽️';
            position: absolute;
            right: 40px;
            bottom: 10px;
            font-size: 8rem;
            opacity: 0.08;
        }

        .header-welcome h4 {
            font-weight: 700;
            font-size: 1.6rem;
            margin-bottom: 10px;
            color: #ffffff;
        }

        .header-welcome p {
            color: rgba(255, 255, 255, 0.75);
            margin: 0;
            font-size: 1.05rem;
            max-width: 650px;
            font-weight: 300;
            line-height: 1.5;
        }

        /* PRODUCT CARD SPECIFIC */
        .produk-card {
            background: #ffffff;
            border-radius: 22px;
            border: 1px solid rgba(0,0,0,0.03);
            overflow: hidden;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .produk-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--hover-shadow);
        }

        .produk-img-wrapper {
            width: 100%;
            height: 220px;
            overflow: hidden;
            position: relative;
            background-color: #f8f9fa;
        }

        .produk-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s ease;
        }

        .produk-card:hover .produk-img {
            transform: scale(1.06);
        }

        .produk-body {
            padding: 25px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            text-align: center;
            align-items: center;
        }

        .produk-body h5 {
            font-weight: 700;
            color: var(--dark);
            font-size: 1.15rem;
            margin-bottom: 12px;
            min-height: 27px;
        }

        .produk-body p {
            color: #8c8c9a;
            font-size: 0.9rem;
            margin-bottom: 20px;
            line-height: 1.4;
            min-height: 38px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .btn-beli {
            width: 100%;
            margin-top: auto;
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
        $topbar_title = "Menu Utama";
        $user_name = $nama_pengunjung;
        $topbar_icon = '<i class="fa fa-th-large text-primary"></i>';
        include '../includes/topbar.php';
        ?>

        <!-- WELCOME HEADER -->
        <div class="header-welcome">
            <h4>Selamat Datang Kembali, <?= htmlspecialchars($nama_pengunjung) ?>!</h4>
            <p>Pilih menu katering terbaik buatan Ibu Iqbal untuk menyempurnakan hari istimewa Anda. Dijamin higienis, sehat, dan lezat.</p>
        </div>

        <!-- PRODUCT LIST -->
        <h5 class="fw-bold mb-4"><i class="fa fa-utensils text-warning me-2"></i> Pilihan Hidangan Eksklusif</h5>
        
        <div class="row g-4">
            <?php
            $data = mysqli_query($conn,"SELECT * FROM produk ORDER BY id DESC");
            while($d = mysqli_fetch_array($data)){
            ?>
            <div class="col-lg-4 col-md-6">
                <div class="produk-card">
                    <div class="produk-img-wrapper">
                        <?php if(!empty($d['gambar']) && file_exists("../upload/".$d['gambar'])){ ?>
                            <img src="../upload/<?= htmlspecialchars($d['gambar']) ?>" class="produk-img" alt="<?= htmlspecialchars($d['nama']) ?>">
                        <?php } else { ?>
                            <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted bg-light">
                                <i class="fa fa-utensils fa-3x"></i>
                            </div>
                        <?php } ?>
                    </div>

                    <div class="produk-body">
                        <h5><?= htmlspecialchars($d['nama']) ?></h5>
                        <p><?= htmlspecialchars($d['deskripsi']) ?></p>

                        <div class="price-badge mb-3">
                            Rp <?= number_format($d['harga'], 0, ',', '.') ?>
                        </div>

                        <a href="pesan.php?id=<?= $d['id'] ?>" class="btn-premium-primary btn-beli justify-content-center">
                            <i class="fa fa-cart-plus"></i> Pesan Sekarang
                        </a>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>