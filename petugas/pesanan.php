<?php
session_start();
if($_SESSION['role']!="petugas"){
    header("location:../login.php");
}
include '../koneksi.php';

/* ================= TAMBAH ================= */
if(isset($_POST['tambah'])){
    $user_id   = intval($_POST['user_id']);
    $alamat_id = intval($_POST['alamat_id']);
    $ongkir_id = intval($_POST['ongkir_id']);
    $total     = intval($_POST['total']);

    mysqli_query($conn,"INSERT INTO pesanan (user_id,alamat_id,ongkir_id,total)
    VALUES('$user_id','$alamat_id','$ongkir_id','$total')");

    header("location:pesanan.php");
    exit;
}

/* ================= HAPUS (FIX) ================= */
if(isset($_GET['hapus'])){
    $id = intval($_GET['hapus']); // WAJIB pakai ini biar tidak error

    mysqli_query($conn,"DELETE FROM pesanan WHERE id='$id'");

    header("location:pesanan.php");
    exit;
}

/* ================= EDIT ================= */
if(isset($_POST['edit'])){
    $id        = intval($_POST['id']);
    $user_id   = intval($_POST['user_id']);
    $alamat_id = intval($_POST['alamat_id']);
    $ongkir_id = intval($_POST['ongkir_id']);
    $total     = intval($_POST['total']);

    mysqli_query($conn,"UPDATE pesanan SET
        user_id='$user_id',
        alamat_id='$alamat_id',
        ongkir_id='$ongkir_id',
        total='$total'
        WHERE id='$id'
    ");

    header("location:pesanan.php");
    exit;
}

$data = mysqli_query($conn,"SELECT * FROM pesanan ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Data Pesanan</title>

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
.table tr:hover{
    background:#f5f7fa;
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

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>📦 Data Pesanan</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambah">
        <i class="fa fa-plus"></i> Tambah
    </button>
</div>

<table class="table table-hover">
<thead>
<tr>
<th>ID</th>
<th>User</th>
<th>Alamat</th>
<th>Ongkir</th>
<th>Tanggal</th>
<th>Total</th>
<th>Aksi</th>
</tr>
</thead>

<tbody>
<?php while($d = mysqli_fetch_array($data)){ ?>
<tr>
<td><?= $d['id'] ?></td>
<td><?= $d['user_id'] ?></td>
<td><?= $d['alamat_id'] ?></td>
<td><?= $d['ongkir_id'] ?></td>
<td><?= $d['tanggal'] ?></td>
<td><b>Rp <?= number_format($d['total']) ?></b></td>
<td>

<button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#edit<?= $d['id'] ?>">
<i class="fa fa-edit"></i>
</button>

<a href="pesanan.php?hapus=<?= $d['id'] ?>" 
   class="btn btn-danger btn-sm"
   onclick="return confirm('Yakin hapus data?')">
<i class="fa fa-trash"></i>
</a>

</td>
</tr>

<!-- MODAL EDIT -->
<div class="modal fade" id="edit<?= $d['id'] ?>">
<div class="modal-dialog">
<div class="modal-content">

<form method="POST">
<div class="modal-header">
<h5>Edit Pesanan</h5>
</div>

<div class="modal-body">

<input type="hidden" name="id" value="<?= $d['id'] ?>">

<input type="number" name="user_id" class="form-control mb-2" value="<?= $d['user_id'] ?>" required>

<input type="number" name="alamat_id" class="form-control mb-2" value="<?= $d['alamat_id'] ?>" required>

<input type="number" name="ongkir_id" class="form-control mb-2" value="<?= $d['ongkir_id'] ?>" required>

<input type="number" name="total" class="form-control" value="<?= $d['total'] ?>" required>

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

<div class="modal-header">
<h5>Tambah Pesanan</h5>
</div>

<div class="modal-body">

<input type="number" name="user_id" class="form-control mb-2" placeholder="User ID" required>

<input type="number" name="alamat_id" class="form-control mb-2" placeholder="Alamat ID" required>

<input type="number" name="ongkir_id" class="form-control mb-2" placeholder="Ongkir ID" required>

<input type="number" name="total" class="form-control" placeholder="Total" required>

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