<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role']!="pengunjung"){
    header("location:../login.php");
    exit;
}
include '../koneksi.php';
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

<style>
:root {
    --bg-gradient: linear-gradient(135deg,#eef2f7,#dbe9f4);
    --sidebar-bg: linear-gradient(180deg,#141e30,#243b55);
    --btn-info: linear-gradient(45deg,#00c6ff,#0072ff);
    --btn-success: linear-gradient(45deg,#11998e,#38ef7d);
}

body {
    font-family: 'Poppins', sans-serif;
    background: var(--bg-gradient);
    min-height: 100vh;
    overflow-x: hidden;
}

/* SIDEBAR */
.sidebar {
    width: 260px;
    height: 100vh;
    position: fixed;
    background: var(--sidebar-bg);
    color: white;
    padding: 30px 20px;
    box-shadow: 4px 0 20px rgba(0,0,0,0.05);
    z-index: 1000;
    display: flex;
    flex-direction: column;
}

.sidebar h4 {
    text-align: center;
    font-weight: 700;
    margin-bottom: 40px;
    font-size: 1.4rem;
    letter-spacing: 1px;
    color: #fff;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    padding-bottom: 20px;
}

.sidebar a {
    display: flex;
    align-items: center;
    gap: 15px;
    color: rgba(255,255,255,0.7);
    padding: 14px 20px;
    border-radius: 12px;
    text-decoration: none;
    margin-bottom: 12px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.sidebar a i {
    font-size: 1.2rem;
    width: 24px;
    text-align: center;
}

.sidebar a:hover, .sidebar a.active {
    background: rgba(255,255,255,0.1);
    color: white;
    transform: translateX(8px);
}

.sidebar a.active {
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    border-left: 4px solid #fff;
}

/* CONTENT AREA */
.main-content {
    margin-left: 260px;
    padding: 30px 40px;
}

/* TOPBAR */
.topbar {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    padding: 18px 30px;
    border-radius: 16px;
    margin-bottom: 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 8px 30px rgba(0,0,0,0.04);
}

.topbar h5 {
    margin: 0;
    font-weight: 600;
    color: #243b55;
    font-size: 1.2rem;
}

.user-profile {
    display: flex;
    align-items: center;
    gap: 12px;
    font-weight: 600;
    color: #141e30;
    background: #f5f7fa;
    padding: 8px 18px;
    border-radius: 30px;
}

.user-profile i {
    font-size: 1.2rem;
    color: #5b86e5;
}

/* HEADER BOX */
.header-box {
    background: white;
    padding: 25px 30px;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    margin-bottom: 30px;
    display: flex;
    flex-direction: column;
}

.header-box h4 {
    font-weight: 700;
    color: #141e30;
    margin-bottom: 8px;
    font-size: 1.5rem;
}

.header-box p {
    color: #6c757d;
    margin: 0;
    font-size: 1rem;
    font-weight: 400;
}

/* PRODUCT CARDS */
.produk-card {
    border: none;
    border-radius: 20px;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    background: white;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    height: 100%;
}

.produk-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
}

.produk-img-wrapper {
    overflow: hidden;
    height: 220px;
    position: relative;
}

.produk-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.produk-card:hover .produk-img {
    transform: scale(1.1);
}

.card-body {
    padding: 25px;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.card-body h5 {
    font-weight: 600;
    color: #243b55;
    margin-bottom: 15px;
    font-size: 1.2rem;
}

.harga-badge {
    background: #eef2f7;
    color: #11998e;
    padding: 8px 20px;
    border-radius: 30px;
    display: inline-block;
    font-weight: 700;
    font-size: 1.1rem;
    margin-bottom: 20px;
    border: 1px solid rgba(17, 153, 142, 0.2);
}

.btn-beli {
    background: var(--btn-success);
    color: white;
    border: none;
    border-radius: 30px;
    padding: 12px 25px;
    font-weight: 600;
    transition: all 0.3s ease;
    width: 100%;
    box-shadow: 0 4px 15px rgba(17, 153, 142, 0.3);
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    text-decoration: none;
}

.btn-beli:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(17, 153, 142, 0.4);
    color: white;
}

/* RESPONSIVE */
@media (max-width: 991px) {
    .sidebar { width: 80px; padding: 20px 10px; }
    .sidebar h4, .sidebar a span { display: none; }
    .sidebar a { justify-content: center; padding: 15px; }
    .main-content { margin-left: 80px; padding: 20px; }
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h4><i class="fa fa-utensils me-2"></i> Pengunjung</h4>

    <a href="index.php" class="active"><i class="fa fa-home"></i> <span>Dashboard</span></a>
    <a href="alamat.php"><i class="fa fa-map-marker-alt"></i> <span>Alamat</span></a>
    <a href="pesanan_saya.php"><i class="fa fa-shopping-cart"></i> <span>Pesanan Saya</span></a>
    <a href="../logout.php" class="mt-auto mb-2"><i class="fa fa-sign-out-alt"></i> <span>Logout</span></a>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">

    <!-- TOPBAR -->
    <div class="topbar">
        <h5><i class="fa fa-th-large me-2 text-primary"></i> Menu Utama</h5>
        <div class="user-profile">
            <i class="fa fa-user-circle"></i> <?= htmlspecialchars($_SESSION['username'] ?? 'User') ?>
        </div>
    </div>

    <!-- HEADER BOX -->
    <div class="header-box">
        <h4>🍽️ Daftar Menu Catering</h4>
        <p>Pilih hidangan favorit kamu dan langsung pesan dengan mudah. Kami siap mengantarkan pesanan terbaik untuk Anda.</p>
    </div>

    <!-- PRODUCT LIST -->
    <div class="row g-4">

    <?php
    $data = mysqli_query($conn,"SELECT * FROM produk ORDER BY id DESC");
    while($d = mysqli_fetch_array($data)){
    ?>

    <div class="col-lg-4 col-md-6">
        <div class="card produk-card">
            <div class="produk-img-wrapper">
                <img src="../upload/<?= htmlspecialchars($d['gambar']) ?>" class="produk-img" alt="<?= htmlspecialchars($d['nama']) ?>">
            </div>

            <div class="card-body">
                <h5><?= htmlspecialchars($d['nama']) ?></h5>

                <div class="harga-badge">
                    Rp <?= number_format($d['harga'], 0, ',', '.') ?>
                </div>

                <a href="beli.php?id=<?= $d['id'] ?>" class="btn btn-beli">
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