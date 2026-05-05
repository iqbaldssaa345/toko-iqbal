<?php
session_start();
include '../koneksi.php';

/* TAMBAH */
if(isset($_POST['tambah'])){
    $nama = $_POST['nama'];
    $des = $_POST['deskripsi'];
    $harga = $_POST['harga'];

    $gambar = $_FILES['gambar']['name'];
    $tmp = $_FILES['gambar']['tmp_name'];
    move_uploaded_file($tmp,"../upload/".$gambar);

    mysqli_query($conn,"INSERT INTO produk (nama,deskripsi,harga,gambar)
    VALUES('$nama','$des','$harga','$gambar')");
    header("location:produk.php");
}

/* HAPUS */
if(isset($_GET['hapus'])){
    mysqli_query($conn,"DELETE FROM produk WHERE id='$_GET[id]'");
    header("location:produk.php");
}

/* EDIT */
if(isset($_POST['edit'])){
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $des = $_POST['deskripsi'];
    $harga = $_POST['harga'];

    if($_FILES['gambar']['name']!=""){
        $gambar = $_FILES['gambar']['name'];
        move_uploaded_file($_FILES['gambar']['tmp_name'],"../upload/".$gambar);

        mysqli_query($conn,"UPDATE produk SET
        nama='$nama',
        deskripsi='$des',
        harga='$harga',
        gambar='$gambar'
        WHERE id='$id'");
    }else{
        mysqli_query($conn,"UPDATE produk SET
        nama='$nama',
        deskripsi='$des',
        harga='$harga'
        WHERE id='$id'");
    }

    header("location:produk.php");
}

$data = mysqli_query($conn,"SELECT * FROM produk");
?>

<!DOCTYPE html>
<html>
<head>
<title>Produk </title>

<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
body{
    font-family:Poppins;
    background:linear-gradient(135deg,#1d2671,#c33764);
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

/* CARD */
.card-box{
    background:white;
    border-radius:20px;
    padding:25px;
    box-shadow:0 10px 25px rgba(0,0,0,0.2);
}

/* IMAGE */
.img-produk{
    width:70px;
    height:70px;
    object-fit:cover;
    border-radius:10px;
}

/* TABLE */
.table tr:hover{
    background:#f5f5f5;
}

/* BUTTON */
.btn{
    border-radius:20px;
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

<div class="content">

<div class="card-box">

<div class="d-flex justify-content-between mb-3">
    <h4>Data Produk</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambah">
        <i class="fa fa-plus"></i> Tambah
    </button>
</div>

<table class="table table-hover">
<tr>
<th>Gambar</th>
<th>Nama</th>
<th>Deskripsi</th>
<th>Harga</th>
<th>Aksi</th>
</tr>

<?php while($d = mysqli_fetch_array($data)){ ?>
<tr>
<td><img src="../upload/<?= $d['gambar'] ?>" class="img-produk"></td>
<td><?= $d['nama'] ?></td>
<td><?= $d['deskripsi'] ?></td>
<td>Rp <?= number_format($d['harga']) ?></td>
<td>
    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#edit<?= $d['id'] ?>">
        <i class="fa fa-edit"></i>
    </button>

    <a href="?hapus=<?= $d['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus produk?')">
        <i class="fa fa-trash"></i>
    </a>
</td>
</tr>

<!-- MODAL EDIT -->
<div class="modal fade" id="edit<?= $d['id'] ?>">
<div class="modal-dialog">
<div class="modal-content">
<form method="POST" enctype="multipart/form-data">

<div class="modal-header">
<h5>Edit Produk</h5>
</div>

<div class="modal-body">
<input type="hidden" name="id" value="<?= $d['id'] ?>">

<input type="text" name="nama" class="form-control mb-2" value="<?= $d['nama'] ?>" required>

<textarea name="deskripsi" class="form-control mb-2"><?= $d['deskripsi'] ?></textarea>

<input type="number" name="harga" class="form-control mb-2" value="<?= $d['harga'] ?>" required>

<input type="file" name="gambar" class="form-control mb-2">

<img src="../upload/<?= $d['gambar'] ?>" width="100">
</div>

<div class="modal-footer">
<button type="submit" name="edit" class="btn btn-success">Simpan</button>
</div>

</form>
</div>
</div>
</div>

<?php } ?>

</table>

</div>
</div>

<!-- MODAL TAMBAH -->
<div class="modal fade" id="tambah">
<div class="modal-dialog">
<div class="modal-content">

<form method="POST" enctype="multipart/form-data">

<div class="modal-header">
<h5>Tambah Produk</h5>
</div>

<div class="modal-body">
<input type="text" name="nama" class="form-control mb-2" placeholder="Nama Produk" required>

<textarea name="deskripsi" class="form-control mb-2" placeholder="Deskripsi"></textarea>

<input type="number" name="harga" class="form-control mb-2" placeholder="Harga" required>

<input type="file" name="gambar" class="form-control mb-2" required>
</div>

<div class="modal-footer">
<button type="submit" name="tambah" class="btn btn-primary">Simpan</button>
</div>

</form>

</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>