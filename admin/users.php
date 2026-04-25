<?php
session_start();
include '../koneksi.php';

/* TAMBAH */
if(isset($_POST['tambah'])){
    mysqli_query($conn,"INSERT INTO users (username,password,role)
    VALUES('$_POST[username]','$_POST[password]','$_POST[role]')");
    header("location:users.php");
}

/* HAPUS */
if(isset($_GET['hapus'])){
    mysqli_query($conn,"DELETE FROM users WHERE id='$_GET[id]'");
    header("location:users.php");
}

/* EDIT */
if(isset($_POST['edit'])){
    mysqli_query($conn,"UPDATE users SET
    username='$_POST[username]',
    password='$_POST[password]',
    role='$_POST[role]'
    WHERE id='$_POST[id]'");
    header("location:users.php");
}

$data = mysqli_query($conn,"SELECT * FROM users");
?>

<!DOCTYPE html>
<html>
<head>
<title>Data User</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
body{
    background:linear-gradient(135deg,#1d2671,#c33764);
    font-family:'Poppins',sans-serif;
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
    color:#bbb;
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
    margin-bottom:20px;
    display:flex;
    justify-content:space-between;
    box-shadow:0 5px 15px rgba(0,0,0,0.1);
}

/* CARD */
.card-box{
    background:white;
    border-radius:20px;
    padding:25px;
    box-shadow:0 10px 25px rgba(0,0,0,0.15);
    animation:fade 0.5s ease;
}

@keyframes fade{
    from{opacity:0; transform:translateY(20px);}
    to{opacity:1;}
}

/* TABLE */
.table thead{
    background:#f1f3f9;
}

.table tr:hover{
    background:#f9f9f9;
    transition:0.2s;
}

/* BADGE */
.admin{background:#ff4d4d;}
.petugas{background:#ff9800;}
.pengunjung{background:#4caf50;}

.badge-role{
    padding:6px 12px;
    border-radius:20px;
    color:white;
    font-size:12px;
}

/* BUTTON */
.btn{
    border-radius:20px;
}

/* SEARCH */
.search-box{
    max-width:250px;
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

<!-- TOPBAR -->
<div class="topbar">
    <h5>👥 Data User</h5>
    <input type="text" id="search" class="form-control search-box" placeholder="Cari user...">
</div>

<div class="card-box">

<div class="d-flex justify-content-between mb-3">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambah">
        <i class="fa fa-plus"></i> Tambah User
    </button>
</div>

<table class="table table-hover" id="tabelUser">
<thead>
<tr>
    <th>Username</th>
    <th>Role</th>
    <th>Aksi</th>
</tr>
</thead>

<tbody>
<?php while($d = mysqli_fetch_array($data)){ ?>
<tr>
<td><?= $d['username'] ?></td>
<td>
    <span class="badge-role <?= $d['role'] ?>">
        <?= strtoupper($d['role']) ?>
    </span>
</td>
<td>
    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#edit<?= $d['id'] ?>">
        <i class="fa fa-edit"></i>
    </button>

    <a href="?hapus=<?= $d['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')">
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
<h5>Edit User</h5>
</div>

<div class="modal-body">
<input type="hidden" name="id" value="<?= $d['id'] ?>">

<input type="text" name="username" class="form-control mb-2" value="<?= $d['username'] ?>" required>

<input type="text" name="password" class="form-control mb-2" value="<?= $d['password'] ?>" required>

<select name="role" class="form-control">
<option <?= $d['role']=="admin"?"selected":"" ?>>admin</option>
<option <?= $d['role']=="petugas"?"selected":"" ?>>petugas</option>
<option <?= $d['role']=="pengunjung"?"selected":"" ?>>pengunjung</option>
</select>
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
<h5>Tambah User</h5>
</div>

<div class="modal-body">
<input type="text" name="username" class="form-control mb-2" placeholder="Username" required>
<input type="text" name="password" class="form-control mb-2" placeholder="Password" required>

<select name="role" class="form-control">
<option>admin</option>
<option>petugas</option>
<option>pengunjung</option>
</select>
</div>

<div class="modal-footer">
<button type="submit" name="tambah" class="btn btn-primary">Simpan</button>
</div>

</form>
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// SEARCH LIVE
document.getElementById("search").addEventListener("keyup", function(){
    let value = this.value.toLowerCase();
    let rows = document.querySelectorAll("#tabelUser tbody tr");

    rows.forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(value) ? "" : "none";
    });
});
</script>

</body>
</html>