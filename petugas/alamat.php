<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role']!="petugas"){
    header("location:../login.php");
    exit;
}
include '../koneksi.php';

/* ================= TAMBAH ================= */
if(isset($_POST['tambah'])){
    $user_id        = intval($_POST['user_id']);
    $nama_penerima  = mysqli_real_escape_string($conn,$_POST['nama_penerima']);
    $alamat         = mysqli_real_escape_string($conn,$_POST['alamat']);
    $kota           = mysqli_real_escape_string($conn,$_POST['kota']);

    mysqli_query($conn,"INSERT INTO alamat (user_id,nama_penerima,alamat,kota)
    VALUES('$user_id','$nama_penerima','$alamat','$kota')");

    header("location:alamat.php?pesan=sukses_tambah");
    exit;
}

/* ================= HAPUS ================= */
if(isset($_GET['hapus'])){
    $id = intval($_GET['hapus']);

    mysqli_query($conn,"DELETE FROM alamat WHERE id='$id'");

    header("location:alamat.php?pesan=sukses_hapus");
    exit;
}

/* ================= EDIT ================= */
if(isset($_POST['edit'])){
    $id             = intval($_POST['id']);
    $user_id        = intval($_POST['user_id']);
    $nama_penerima  = mysqli_real_escape_string($conn,$_POST['nama_penerima']);
    $alamat         = mysqli_real_escape_string($conn,$_POST['alamat']);
    $kota           = mysqli_real_escape_string($conn,$_POST['kota']);

    mysqli_query($conn,"UPDATE alamat SET
        user_id='$user_id',
        nama_penerima='$nama_penerima',
        alamat='$alamat',
        kota='$kota'
        WHERE id='$id'
    ");

    header("location:alamat.php?pesan=sukses_edit");
    exit;
}

/* ================= DATA ================= */
$data = mysqli_query($conn,"SELECT * FROM alamat ORDER BY id ASC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Alamat</title>

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
<h4>📍 Data Alamat</h4>
<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambah">
<i class="fa fa-plus"></i> Tambah
</button>
</div>

<table class="table table-hover">
<thead>
<tr>
<th>ID</th>
<th>User</th>
<th>Nama Penerima</th>
<th>Alamat</th>
<th>Kota</th>
<th>Aksi</th>
</tr>
</thead>

<tbody>
<?php while($d = mysqli_fetch_array($data)){ ?>
<tr>
<td><?= $d['id'] ?></td>
<td><?= $d['user_id'] ?></td>
<td><?= $d['nama_penerima'] ?></td>
<td><?= $d['alamat'] ?></td>
<td><?= $d['kota'] ?></td>

<td>
<button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#edit<?= $d['id'] ?>">
<i class="fa fa-edit"></i>
</button>

<button type="button" class="btn btn-danger btn-sm" onclick="konfirmasiHapus('?hapus=<?= $d['id'] ?>')">
<i class="fa fa-trash"></i>
</button>
</td>
</tr>

<!-- MODAL EDIT -->
<div class="modal fade" id="edit<?= $d['id'] ?>">
<div class="modal-dialog">
<div class="modal-content">

<form method="POST">
<div class="modal-header"><h5>Edit Alamat</h5></div>

<div class="modal-body">

<input type="hidden" name="id" value="<?= $d['id'] ?>">

<input type="number" name="user_id" class="form-control mb-2" value="<?= $d['user_id'] ?>" required>

<input type="text" name="nama_penerima" class="form-control mb-2" value="<?= $d['nama_penerima'] ?>" required>

<textarea name="alamat" class="form-control mb-2" required><?= $d['alamat'] ?></textarea>

<input type="text" name="kota" class="form-control" value="<?= $d['kota'] ?>" required>

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

<div class="modal-header"><h5>Tambah Alamat</h5></div>

<div class="modal-body">

<input type="number" name="user_id" class="form-control mb-2" placeholder="User ID" required>

<input type="text" name="nama_penerima" class="form-control mb-2" placeholder="Nama Penerima" required>

<textarea name="alamat" class="form-control mb-2" placeholder="Alamat lengkap" required></textarea>

<input type="text" name="kota" class="form-control" placeholder="Kota" required>

</div>

<div class="modal-footer">
<button type="submit" name="tambah" class="btn btn-primary">Simpan</button>
</div>

</form>

</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function konfirmasiHapus(url) {
    Swal.fire({
        title: 'Yakin hapus data ini?',
        text: "Data yang dihapus tidak bisa dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    })
}
</script>
<?php if(isset($_GET['pesan'])){ ?>
<script>
    <?php if($_GET['pesan'] == 'sukses_tambah'){ ?>
        Swal.fire('Berhasil!', 'Data berhasil ditambahkan.', 'success');
    <?php }elseif($_GET['pesan'] == 'sukses_edit'){ ?>
        Swal.fire('Berhasil!', 'Data berhasil diperbarui.', 'success');
    <?php }elseif($_GET['pesan'] == 'sukses_hapus'){ ?>
        Swal.fire('Berhasil!', 'Data berhasil dihapus.', 'success');
    <?php } ?>
</script>
<?php } ?>

</body>
</html>