<?php
session_start();
if($_SESSION['role']!="admin"){
    header("location:../login.php");
}
include '../koneksi.php';

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
<title>Dashboard Admin - Toko Ibu Iqbal</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
body{
    font-family:'Poppins',sans-serif;
    background:linear-gradient(135deg,#eef2f7,#dbe9f4);
}

/* SIDEBAR */
.sidebar{
    width:250px;
    height:100vh;
    position:fixed;
    background:linear-gradient(180deg,#141e30,#243b55);
    color:white;
    padding:20px;
}

.sidebar h4{
    text-align:center;
    margin-bottom:30px;
}

.sidebar a{
    display:flex;
    align-items:center;
    gap:10px;
    color:#ccc;
    padding:12px;
    border-radius:12px;
    text-decoration:none;
    margin-bottom:10px;
    transition:0.3s;
}

.sidebar a:hover{
    background:rgba(255,255,255,0.1);
    color:white;
    transform:translateX(5px);
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
    margin-bottom:25px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 8px 20px rgba(0,0,0,0.05);
}

/* CARD */
.card-box{
    border-radius:20px;
    padding:25px;
    color:white;
    position:relative;
    overflow:hidden;
    transition:0.3s;
}

.card-box i{
    position:absolute;
    right:15px;
    bottom:15px;
    font-size:40px;
    opacity:0.3;
}

.card-box:hover{
    transform:translateY(-8px);
    box-shadow:0 10px 25px rgba(0,0,0,0.2);
}

.bg-user{background:linear-gradient(45deg,#36d1dc,#5b86e5);}
.bg-produk{background:linear-gradient(45deg,#ff9966,#ff5e62);}
.bg-ongkir{background:linear-gradient(45deg,#00b09b,#96c93d);}

/* TABLE */
.table-box{
    background:white;
    padding:25px;
    border-radius:20px;
    box-shadow:0 8px 20px rgba(0,0,0,0.05);
}

.table tr:hover{
    background:#f5f7fa;
}

/* BUTTON */
.btn-info-custom{
    background:linear-gradient(45deg,#00c6ff,#0072ff);
    border:none;
    color:white;
    border-radius:25px;
    padding:10px 20px;
    transition:0.3s;
}

.btn-info-custom:hover{
    transform:scale(1.05);
    box-shadow:0 5px 15px rgba(0,0,0,0.2);
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h4>🛒 Admin</h4>

    <a href="index.php"><i class="fa fa-home"></i> Dashboard</a>
    <a href="users.php"><i class="fa fa-users"></i> Data User</a>
    <a href="produk.php"><i class="fa fa-box"></i> Produk</a>
    <a href="ongkir.php"><i class="fa fa-truck"></i> Ongkir</a>
    <a href="../logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
</div>

<!-- CONTENT -->
<div class="content">

    <!-- TOPBAR -->
    <div class="topbar">
        <h5>Dashboard Admin</h5>
        <div>👤 Admin</div>
    </div>

    <!-- STATISTIK -->
    <div class="row g-4">

        <div class="col-md-4">
            <div class="card-box bg-user">
                <h3><?= $user ?></h3>
                <p>Total User</p>
                <i class="fa fa-users"></i>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card-box bg-produk">
                <h3><?= $produk ?></h3>
                <p>Total Produk</p>
                <i class="fa fa-box"></i>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card-box bg-ongkir">
                <h3><?= $ongkir ?></h3>
                <p>Total Ongkir</p>
                <i class="fa fa-truck"></i>
            </div>
        </div>

    </div>

    <!-- DATA PRODUK -->
    <div class="mt-4">
        <div class="table-box">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>Produk Terbaru</h5>

                <!-- DIGANTI JADI INFO -->
                <a href="produk.php" class="btn btn-info-custom">
                    <i class="fa fa-info-circle"></i> Info Produk
                </a>
            </div>

            <table class="table table-hover">
                <tr>
                    <th>Nama</th>
                    <th>Harga</th>
                </tr>

                <?php while($r = mysqli_fetch_assoc($recent)){ ?>
                <tr>
                    <td><?= $r['nama'] ?></td>
                    <td>Rp <?= number_format($r['harga']) ?></td>
                </tr>
                <?php } ?>

            </table>
        </div>
    </div>

</div>

</body>
</html>