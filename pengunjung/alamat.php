<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role']!="pengunjung"){
    header("location:../login.php");
    exit;
}
include '../koneksi.php';

$user_id = $_SESSION['id'];

/* TAMBAH */
if(isset($_POST['tambah'])){
    $nama = mysqli_real_escape_string($conn,$_POST['nama']);
    $alamat = mysqli_real_escape_string($conn,$_POST['alamat']);
    $kota = mysqli_real_escape_string($conn,$_POST['kota']);

    if($nama && $alamat && $kota){
        mysqli_query($conn,"INSERT INTO alamat(user_id,nama_penerima,alamat,kota)
        VALUES('$user_id','$nama','$alamat','$kota')");
    }

    echo "<script>location='alamat.php';</script>";
}

/* HAPUS */
if(isset($_GET['hapus'])){
    $id = intval($_GET['hapus']);
    mysqli_query($conn,"DELETE FROM alamat WHERE id='$id' AND user_id='$user_id'");
    echo "<script>location='alamat.php';</script>";
}

$data = mysqli_query($conn,"SELECT * FROM alamat WHERE user_id='$user_id' ORDER BY id ASC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Alamat Saya</title>

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
    background:linear-gradient(180deg,#0f2027,#203a43,#2c5364);
    padding:20px;
}
.sidebar h4{color:white;}
.sidebar a{
    display:flex;
    gap:10px;
    padding:12px;
    color:#ccc;
    text-decoration:none;
    border-radius:10px;
    transition:0.3s;
}
.sidebar a:hover{
    background:rgba(255,255,255,0.1);
    color:white;
}

/* CONTENT */
.content{
    margin-left:270px;
    padding:30px;
}

/* HEADER */
.header{
    background:white;
    padding:15px 20px;
    border-radius:15px;
    box-shadow:0 5px 15px rgba(0,0,0,0.1);
    margin-bottom:20px;
}

/* CARD */
.card{
    border-radius:20px;
    box-shadow:0 10px 25px rgba(0,0,0,0.1);
}

/* FORM BUTTON */
.btn-success{
    background:linear-gradient(45deg,#28a745,#20c997);
    border:none;
}

/* GRID */
.grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(300px,1fr));
    gap:15px;
}

/* BOX */
.alamat-box{
    padding:20px;
    border-radius:20px;
    background:white;
    box-shadow:0 10px 20px rgba(0,0,0,0.08);
    transition:0.3s;
    position:relative;
}
.alamat-box:hover{
    transform:translateY(-5px);
}

/* ICON */
.icon{
    font-size:30px;
    color:#0d6efd;
    margin-bottom:10px;
}

/* DELETE BTN */
.btn-hapus{
    position:absolute;
    top:10px;
    right:10px;
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

<div class="content">

<!-- HEADER -->
<div class="header">
    <h5>👋 Halo, User!</h5>
    <small>Kelola alamat pengiriman kamu di sini</small>
</div>

<!-- FORM -->
<div class="card p-4 mb-4">
<h5><i class="fa fa-plus"></i> Tambah Alamat</h5>

<form method="POST">
<input type="text" name="nama" placeholder="Nama Penerima" class="form-control mb-2" required>
<textarea name="alamat" placeholder="Alamat Lengkap" class="form-control mb-2" required></textarea>
<input type="text" name="kota" placeholder="Kota" class="form-control mb-3" required>

<button name="tambah" class="btn btn-success w-100">
<i class="fa fa-save"></i> Simpan
</button>
</form>
</div>

<!-- DATA -->
<div class="card p-4">
<h5><i class="fa fa-map-marker-alt"></i> Daftar Alamat</h5>

<div class="grid">
<?php while($d=mysqli_fetch_array($data)){ ?>
<div class="alamat-box">

<a href="?hapus=<?= $d['id'] ?>" 
   class="btn btn-danger btn-sm btn-hapus"
   onclick="return confirm('Yakin hapus alamat ini?')">
<i class="fa fa-trash"></i>
</a>

<div class="icon"><i class="fa fa-map-pin"></i></div>

<h6><?= $d['nama_penerima'] ?></h6>
<p><?= $d['alamat'] ?></p>
<small class="text-muted"><?= $d['kota'] ?></small>

</div>
<?php } ?>
</div>

</div>

</div>

</body>
</html>