<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role']!="petugas"){
    header("location:../login.php");
    exit;
}
include '../koneksi.php';

/* ================= TAMBAH ================= */
if(isset($_POST['tambah'])){
    $pesanan_id = intval($_POST['pesanan_id']);
    $produk_id  = intval($_POST['produk_id']);
    $jumlah     = intval($_POST['jumlah']);

    // ambil harga produk
    $p = mysqli_fetch_array(mysqli_query($conn,"SELECT harga FROM produk WHERE id='$produk_id'"));
    $subtotal = $p['harga'] * $jumlah;

    mysqli_query($conn,"INSERT INTO detail_pesanan (pesanan_id,produk_id,jumlah,subtotal)
    VALUES('$pesanan_id','$produk_id','$jumlah','$subtotal')");

    echo "<script>alert('Berhasil tambah');location='detail_pesanan.php';</script>";
}

/* ================= HAPUS ================= */
if(isset($_GET['hapus'])){
    $id = intval($_GET['hapus']);

    mysqli_query($conn,"DELETE FROM detail_pesanan WHERE id='$id'");

    echo "<script>alert('Berhasil hapus');location='detail_pesanan.php';</script>";
}

/* ================= EDIT ================= */
if(isset($_POST['edit'])){
    $id         = intval($_POST['id']);
    $produk_id  = intval($_POST['produk_id']);
    $jumlah     = intval($_POST['jumlah']);

    $p = mysqli_fetch_array(mysqli_query($conn,"SELECT harga FROM produk WHERE id='$produk_id'"));
    $subtotal = $p['harga'] * $jumlah;

    mysqli_query($conn,"UPDATE detail_pesanan SET
        produk_id='$produk_id',
        jumlah='$jumlah',
        subtotal='$subtotal'
        WHERE id='$id'
    ");

    echo "<script>alert('Berhasil update');location='detail_pesanan.php';</script>";
}

/* ================= DATA ================= */
$data = mysqli_query($conn,"
SELECT dp.*, produk.nama 
FROM detail_pesanan dp
LEFT JOIN produk ON dp.produk_id = produk.id
ORDER BY dp.id ASC
");

/* dropdown */
$pesanan = mysqli_query($conn,"SELECT * FROM pesanan");
$produk  = mysqli_query($conn,"SELECT * FROM produk");
?>

<!DOCTYPE html>
<html>
<head>
<title>Detail Pesanan</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
body{
    font-family:Poppins;
    background:linear-gradient(135deg,#eef2f7,#dbe9f4);
}
.sidebar{
    width:250px;
    height:100vh;
    position:fixed;
    background:linear-gradient(180deg,#141e30,#243b55);
    color:white;
    padding:20px;
}
.sidebar a{
    display:flex;
    gap:10px;
    padding:12px;
    color:#ccc;
    text-decoration:none;
    border-radius:10px;
    margin-bottom:10px;
}
.sidebar a:hover{
    background:rgba(255,255,255,0.1);
    color:white;
}
.content{
    margin-left:270px;
    padding:25px;
}
.card-box{
    background:white;
    padding:25px;
    border-radius:20px;
    box-shadow:0 10px 25px rgba(0,0,0,0.1);
}
.table thead{
    background:#243b55;
    color:white;
}
.btn{
    border-radius:20px;
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
<div class="card-box">

<div class="d-flex justify-content-between mb-3">
<h4>📦 Detail Pesanan</h4>
<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambah">
<i class="fa fa-plus"></i> Tambah
</button>
</div>

<table class="table table-hover">
<thead>
<tr>
<th>ID</th>
<th>Pesanan</th>
<th>Produk</th>
<th>Jumlah</th>
<th>Subtotal</th>
<th>Aksi</th>
</tr>
</thead>

<tbody>
<?php while($d = mysqli_fetch_array($data)){ ?>
<tr>
<td><?= $d['id'] ?></td>
<td>#<?= $d['pesanan_id'] ?></td>
<td><?= $d['nama'] ?></td>
<td><?= $d['jumlah'] ?></td>
<td><b>Rp <?= number_format($d['subtotal']) ?></b></td>

<td>
<button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#edit<?= $d['id'] ?>">
<i class="fa fa-edit"></i>
</button>

<a href="?hapus=<?= $d['id'] ?>" class="btn btn-danger btn-sm"
onclick="return confirm('Yakin hapus?')">
<i class="fa fa-trash"></i>
</a>
</td>
</tr>

<!-- MODAL EDIT -->
<div class="modal fade" id="edit<?= $d['id'] ?>">
<div class="modal-dialog">
<div class="modal-content">

<form method="POST">
<div class="modal-header"><h5>Edit</h5></div>

<div class="modal-body">

<input type="hidden" name="id" value="<?= $d['id'] ?>">

<select name="produk_id" class="form-control mb-2">
<?php
$p2 = mysqli_query($conn,"SELECT * FROM produk");
while($p = mysqli_fetch_array($p2)){
?>
<option value="<?= $p['id'] ?>" <?= $p['id']==$d['produk_id']?'selected':'' ?>>
<?= $p['nama'] ?> (Rp <?= number_format($p['harga']) ?>)
</option>
<?php } ?>
</select>

<input type="number" name="jumlah" class="form-control" value="<?= $d['jumlah'] ?>" required>

</div>

<div class="modal-footer">
<button type="submit" name="edit" class="btn btn-success">Simpan</button>
</div>

</form>

</div>
</div>
</div>

<?php } ?>
</tbody>
</table>

</div>
</div>

<!-- MODAL TAMBAH -->
<div class="modal fade" id="tambah">
<div class="modal-dialog">
<div class="modal-content">

<form method="POST">

<div class="modal-header"><h5>Tambah</h5></div>

<div class="modal-body">

<select name="pesanan_id" class="form-control mb-2" required>
<option value="">-- Pilih Pesanan --</option>
<?php while($p = mysqli_fetch_array($pesanan)){ ?>
<option value="<?= $p['id'] ?>">Pesanan #<?= $p['id'] ?></option>
<?php } ?>
</select>

<select name="produk_id" class="form-control mb-2" required>
<option value="">-- Pilih Produk --</option>
<?php while($p = mysqli_fetch_array($produk)){ ?>
<option value="<?= $p['id'] ?>">
<?= $p['nama'] ?> (Rp <?= number_format($p['harga']) ?>)
</option>
<?php } ?>
</select>

<input type="number" name="jumlah" class="form-control" placeholder="Jumlah" required>

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