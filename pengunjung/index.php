<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role']!="pengunjung"){
    header("location:../login.php");
    exit;
}
include '../koneksi.php';
?>

<!DOCTYPE html>
<html>
<head>
<title>Dashboard Pengunjung</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    font-family:Poppins;
    background:linear-gradient(135deg,#eef2f7,#dbe9f4);
}

/* SIDEBAR */
.sidebar{
    width:250px;
    height:100vh;
    position:fixed;
    background:linear-gradient(180deg,#141e30,#243b55);
    padding:20px;
}
.sidebar h4{
    color:white;
    margin-bottom:25px;
}
.sidebar a{
    display:flex;
    align-items:center;
    gap:10px;
    padding:12px;
    color:#ccc;
    text-decoration:none;
    border-radius:12px;
    margin-bottom:8px;
    transition:0.3s;
}
.sidebar a:hover{
    background:rgba(255,255,255,0.1);
    color:white;
}

/* CONTENT */
.content{
    margin-left:270px;
    padding:25px;
}

/* TOPBAR */
.topbar{
    background:white;
    padding:15px 20px;
    border-radius:15px;
    box-shadow:0 5px 15px rgba(0,0,0,0.05);
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
}

/* TITLE BOX */
.header-box{
    margin-bottom:20px;
}
.header-box h4{
    font-weight:600;
}
.header-box p{
    color:#777;
    margin:0;
}

/* CARD */
.produk-card{
    border:none;
    border-radius:20px;
    overflow:hidden;
    transition:0.3s;
    background:white;
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
}
.produk-card:hover{
    transform:translateY(-10px);
    box-shadow:0 15px 30px rgba(0,0,0,0.15);
}

/* IMAGE */
.produk-img{
    height:200px;
    object-fit:cover;
}

/* PRICE */
.harga{
    background:#e9f7ef;
    color:#28a745;
    padding:8px 15px;
    border-radius:20px;
    display:inline-block;
    font-weight:bold;
}

/* BUTTON */
.btn-beli{
    background:linear-gradient(45deg,#28a745,#20c997);
    color:white;
    border:none;
    border-radius:30px;
    padding:10px;
    transition:0.3s;
}
.btn-beli:hover{
    background:linear-gradient(45deg,#218838,#17a589);
    transform:scale(1.05);
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h4>🛒 Pengunjung</h4>

    <a href="index.php"><i class="fa fa-home"></i> Dashboard</a>
    <a href="alamat.php"><i class="fa fa-map-marker-alt"></i> Alamat</a>
    <a href="pesanan_saya.php"><i class="fa fa-shopping-cart"></i> Pesanan Saya</a>
    <a href="../logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
</div>

<!-- CONTENT -->
<div class="content">

    <!-- TOPBAR -->
    <div class="topbar">
        <h5>Dashboard Pengunjung</h5>
        <div>👤 <?= $_SESSION['username'] ?? 'User'; ?></div>
    </div>

    <!-- HEADER -->
    <div class="header-box">
        <h4>🍽️ Daftar Makanan</h4>
        <p>Pilih makanan favorit kamu dan langsung pesan dengan mudah</p>
    </div>

    <div class="row">

    <?php
    $data = mysqli_query($conn,"SELECT * FROM produk ORDER BY id DESC");
    while($d = mysqli_fetch_array($data)){
    ?>

    <div class="col-md-4 mb-4">
        <div class="card produk-card">

            <img src="../upload/<?= $d['gambar']; ?>" class="card-img-top produk-img">

            <div class="card-body text-center">

                <h5><?= $d['nama']; ?></h5>

                <div class="harga my-3">
                    Rp <?= number_format($d['harga']); ?>
                </div>

                <a href="beli.php?id=<?= $d['id']; ?>" class="btn btn-beli w-100">
                    <i class="fa fa-cart-plus"></i> Beli Sekarang
                </a>

            </div>
        </div>
    </div>

    <?php } ?>

    </div>

</div>

</body>
</html>