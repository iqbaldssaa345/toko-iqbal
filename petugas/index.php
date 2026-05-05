<?php
session_start();
if($_SESSION['role']!="petugas"){
    header("location:../login.php");
}
include '../koneksi.php';

/* HITUNG DATA */
$user = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM users"));
$produk = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM produk"));
$pesanan = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM pesanan"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard Petugas - Catering Ibu Iqbal</title>

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
}

.bg-user{background:linear-gradient(45deg,#36d1dc,#5b86e5);}
.bg-produk{background:linear-gradient(45deg,#ff9966,#ff5e62);}
.bg-pesanan{background:linear-gradient(45deg,#00b09b,#96c93d);}

/* TABLE */
.table-box{
    background:white;
    padding:25px;
    border-radius:20px;
    box-shadow:0 8px 20px rgba(0,0,0,0.05);
}

.table thead{
    background:#243b55;
    color:white;
}

.table tr:hover{
    background:#f5f7fa;
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h4>🛒 Petugas</h4>

    <a href="index.php"><i class="fa fa-home"></i> Dashboard</a>
    <a href="pesanan.php"><i class="fa fa-receipt"></i> Pesanan</a>
    <a href="pembayaran.php"><i class="fa fa-credit-card"></i> Pembayaran</a>
    <a href="detail_pesanan.php"><i class="fa fa-eye"></i> Detail</a>
    <a href="alamat.php"><i class="fa fa-map-marker-alt"></i> Alamat</a>

    <a href="../logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
</div>

<!-- CONTENT -->
<div class="content">

<!-- TOPBAR -->
<div class="topbar">
    <h5>Dashboard Petugas</h5>
    <div>👤 Petugas</div>
</div>

<!-- STATISTIK -->
<div class="row g-4">

    <div class="col-md-4">
        <div class="card-box bg-user">
            <h3><?= $user ?></h3>
            <p>Total User</p>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card-box bg-produk">
            <h3><?= $produk ?></h3>
            <p>Total Produk</p>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card-box bg-pesanan">
            <h3><?= $pesanan ?></h3>
            <p>Total Pesanan</p>
        </div>
    </div>

</div>

<!-- DATA PESANAN -->
<div class="mt-4">
<div class="table-box">

<h5 class="mb-3">📦 Data Pesanan</h5>

<table class="table table-hover">
<thead>
<tr>
<th>User</th>
<th>Produk</th>
<th>Jumlah</th>
<th>Ongkir</th>
<th>Total</th>
</tr>
</thead>

<tbody>
<?php
$data = mysqli_query($conn,"SELECT * FROM pesanan");

while($d = mysqli_fetch_array($data)){
?>
<tr>
<td><?= $d['user_id']; ?></td>
<td><?= $d['alamat_id']; ?></td>
<td><?= $d['ongkir_id']; ?></td>
<td><?= $d['tanggal']; ?></td>
<td><b>Rp <?= number_format($d['total']); ?></b></td>
</tr>
<?php } ?>
</tbody>

</table>

</div>
</div>

</div>

</body>
</html>