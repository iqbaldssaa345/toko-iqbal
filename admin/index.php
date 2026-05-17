<?php
session_start();
if($_SESSION['role']!="admin"){
    header("location:../login.php");
}
include '../koneksi.php';

// Data Admin
$id_admin = $_SESSION['id'];
$q_admin = mysqli_query($conn, "SELECT username FROM users WHERE id='$id_admin'");
$d_admin = mysqli_fetch_assoc($q_admin);
$nama_admin = $d_admin['username'];

// STATISTIK
$user = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM users"));
$produk = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM produk"));
$ongkir = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM ongkir"));

// DATA TERBARU PRODUK
$recent = mysqli_query($conn,"SELECT * FROM produk ORDER BY id DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Admin - Catering Ibu Iqbal</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
:root {
    --bg-gradient: linear-gradient(135deg,#eef2f7,#dbe9f4);
    --sidebar-bg: linear-gradient(180deg,#141e30,#243b55);
    --card-user: linear-gradient(45deg,#36d1dc,#5b86e5);
    --card-produk: linear-gradient(45deg,#ff9966,#ff5e62);
    --card-ongkir: linear-gradient(45deg,#00b09b,#96c93d);
    --btn-info: linear-gradient(45deg,#00c6ff,#0072ff);
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

/* CARDS */
.card-box {
    border-radius: 20px;
    padding: 30px;
    color: white;
    position: relative;
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    border: none;
    height: 100%;
    z-index: 1;
}

.card-box::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255,255,255,0.15);
    z-index: -1;
    transform: skewX(-15deg) translateX(-150%);
    transition: all 0.5s ease;
}

.card-box:hover::before {
    transform: skewX(-15deg) translateX(150%);
}

.card-box:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.2);
}

.card-box h3 {
    font-size: 2.8rem;
    font-weight: 700;
    margin-bottom: 5px;
    letter-spacing: 1px;
}

.card-box p {
    font-size: 1.1rem;
    margin: 0;
    opacity: 0.9;
    font-weight: 500;
}

.card-box i {
    position: absolute;
    right: 25px;
    bottom: 25px;
    font-size: 65px;
    opacity: 0.2;
    transition: all 0.4s ease;
}

.card-box:hover i {
    transform: scale(1.2) rotate(10deg);
    opacity: 0.3;
}

.bg-user { background: var(--card-user); }
.bg-produk { background: var(--card-produk); }
.bg-ongkir { background: var(--card-ongkir); }

/* SECTION TITLE */
.section-title {
    font-size: 1.3rem;
    font-weight: 600;
    color: #141e30;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

/* TABLE SECTION */
.table-wrapper {
    background: white;
    padding: 30px;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
}

.table-responsive::-webkit-scrollbar {
    display: none;
}
.table-responsive {
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;  /* Firefox */
}

.table {
    margin-bottom: 0;
}

.table th {
    border-top: none;
    border-bottom: 2px solid #eef2f7;
    color: #6c757d;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
    padding: 15px;
}

.table td {
    vertical-align: middle;
    padding: 15px;
    color: #495057;
    font-weight: 500;
    border-bottom: 1px solid #eef2f7;
}

.table tr:last-child td {
    border-bottom: none;
}

.table tbody tr {
    transition: all 0.2s ease;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
    transform: scale(1.01);
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    border-radius: 10px;
}

.price-badge {
    background: #eef2f7;
    padding: 6px 14px;
    border-radius: 20px;
    color: #243b55;
    font-weight: 600;
    font-size: 0.95rem;
}

/* BUTTON */
.btn-info-custom {
    background: var(--btn-info);
    border: none;
    color: white;
    border-radius: 30px;
    padding: 10px 25px;
    font-weight: 500;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    box-shadow: 0 4px 15px rgba(0, 198, 255, 0.3);
}

.btn-info-custom:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 198, 255, 0.4);
    color: white;
}

/* RESPONSIVE */
@media (max-width: 991px) {
    .sidebar { width: 80px; padding: 20px 10px; }
    .sidebar h4, .sidebar a span { display: none; }
    .sidebar a { justify-content: center; padding: 15px; }
    .main-content { margin-left: 80px; padding: 20px; }
    .card-box { margin-bottom: 20px; }
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h4><i class="fa fa-utensils me-2"></i> Admin</h4>

    <a href="index.php" class="active"><i class="fa fa-home"></i> <span>Dashboard</span></a>
    <a href="users.php"><i class="fa fa-users"></i> <span>Data User</span></a>
    <a href="produk.php"><i class="fa fa-box"></i> <span>Produk</span></a>
    <a href="ongkir.php"><i class="fa fa-truck"></i> <span>Ongkir</span></a>
    <a href="../logout.php" class="mt-auto mb-2"><i class="fa fa-sign-out-alt"></i> <span>Logout</span></a>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">

    <!-- TOPBAR -->
    <div class="topbar">
        <h5><i class="fa fa-chart-pie me-2 text-primary"></i> Dashboard Admin</h5>
        <div class="user-profile">
            <i class="fa fa-user-circle"></i> <?= htmlspecialchars($nama_admin) ?>
        </div>
    </div>

    <!-- STATISTIK CARDS -->
    <div class="row g-4 mb-5">

        <div class="col-lg-4 col-md-6">
            <div class="card-box bg-user">
                <h3><?= $user ?></h3>
                <p>Total User</p>
                <i class="fa fa-users"></i>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card-box bg-produk">
                <h3><?= $produk ?></h3>
                <p>Total Produk</p>
                <i class="fa fa-box-open"></i>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card-box bg-ongkir">
                <h3><?= $ongkir ?></h3>
                <p>Total Ongkir</p>
                <i class="fa fa-truck-fast"></i>
            </div>
        </div>

    </div>

    <!-- DATA PRODUK TERBARU -->
    <div class="row">
        <div class="col-12">
            <div class="table-wrapper">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="section-title mb-0">
                        <i class="fa fa-star text-warning"></i> Produk Terbaru
                    </h5>
                    
                    <a href="produk.php" class="btn-info-custom">
                        <i class="fa fa-arrow-right"></i> Kelola Produk
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-borderless align-middle">
                        <thead>
                            <tr>
                                <th>Nama Produk</th>
                                <th class="text-end">Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($r = mysqli_fetch_assoc($recent)){ ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-light p-2 rounded-circle text-primary d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="fa fa-cube"></i>
                                        </div>
                                        <?= htmlspecialchars($r['nama']) ?>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <span class="price-badge">Rp <?= number_format($r['harga'], 0, ',', '.') ?></span>
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