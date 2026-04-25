<?php
session_start();
include '../koneksi.php';

/* TAMBAH */
if(isset($_POST['tambah'])){
    $jasa = $_POST['nama_jasa'];
    $biaya = $_POST['biaya'];

    mysqli_query($conn,"INSERT INTO ongkir (nama_jasa,biaya)
    VALUES('$jasa','$biaya')");
    header("location:ongkir.php");
}

/* HAPUS */
if(isset($_GET['hapus'])){
    mysqli_query($conn,"DELETE FROM ongkir WHERE id='$_GET[id]'");
    header("location:ongkir.php");
}

/* EDIT */
if(isset($_POST['edit'])){
    $id = $_POST['id'];
    $jasa = $_POST['nama_jasa'];
    $biaya = $_POST['biaya'];

    mysqli_query($conn,"UPDATE ongkir SET
    nama_jasa='$jasa',
    biaya='$biaya'
    WHERE id='$id'");

    header("location:ongkir.php");
}

$data = mysqli_query($conn,"SELECT * FROM ongkir");
$total = mysqli_num_rows($data);
?>

<!DOCTYPE html>
<html>
<head>
<title>Ongkir</title>

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

/* STAT */
.stat{
    background:linear-gradient(45deg,#00c6ff,#0072ff);
    color:white;
    padding:15px;
    border-radius:15px;
    margin-bottom:20px;
}

/* TABLE */
.table thead{
    background:#1d2671;
    color:white;
}

/* SEARCH */
.search{
    border-radius:20px;
    padding:8px 15px;
    border:1px solid #ccc;
}

/* BUTTON */
.btn{
    border-radius:20px;
}

/* BADGE */
.badge-ongkir{
    background:#00c6ff;
    color:white;
    padding:5px 10px;
    border-radius:10px;
}
</style>

<script>
function cari(){
    let input = document.getElementById("search").value.toLowerCase();
    let rows = document.querySelectorAll("tbody tr");

    rows.forEach(row=>{
        let jasa = row.children[1].innerText.toLowerCase();
        row.style.display = jasa.includes(input) ? "" : "none";
    });
}
</script>

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

<!-- STAT -->
<div class="stat">
    <h5>Total Jasa Ongkir: <?= $total ?></h5>
</div>

<div class="card-box">

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>🚚 Data Ongkir</h4>

    <div>
        <input type="text" id="search" onkeyup="cari()" placeholder="Cari jasa..." class="search">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambah">
            <i class="fa fa-plus"></i>
        </button>
    </div>
</div>

<table class="table table-hover">
<thead>
<tr>
<th>No</th>
<th>Jasa</th>
<th>Biaya</th>
<th>Aksi</th>
</tr>
</thead>

<tbody>
<?php $no=1; mysqli_data_seek($data,0); while($d = mysqli_fetch_array($data)){ ?>
<tr>
<td><?= $no++ ?></td>
<td><span class="badge-ongkir"><?= $d['nama_jasa'] ?></span></td>
<td><b>Rp <?= number_format($d['biaya']) ?></b></td>
<td>
    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#edit<?= $d['id'] ?>">
        <i class="fa fa-edit"></i>
    </button>

    <a href="?hapus=<?= $d['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus data?')">
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
<h5>Edit Ongkir</h5>
</div>

<div class="modal-body">
<input type="hidden" name="id" value="<?= $d['id'] ?>">

<input type="text" name="nama_jasa" class="form-control mb-2" value="<?= $d['nama_jasa'] ?>" required>

<input type="number" name="biaya" class="form-control" value="<?= $d['biaya'] ?>" required>
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
<h5>Tambah Ongkir</h5>
</div>

<div class="modal-body">

<input type="text" name="nama_jasa" class="form-control mb-2" placeholder="Contoh: Maxim, Lalamove" required>

<input type="number" name="biaya" class="form-control" placeholder="Biaya" required>

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