<?php
session_start();
include '../koneksi.php';

/* ================= TAMBAH ================= */
if(isset($_POST['tambah'])){
    $username = mysqli_real_escape_string($conn,$_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    mysqli_query($conn,"INSERT INTO users (username,password,role)
    VALUES('$username','$password','$role')");

    header("Location: users.php");
    exit;
}

/* ================= HAPUS ================= */
if(isset($_GET['hapus'])){
    $id = intval($_GET['hapus']);
    mysqli_query($conn,"DELETE FROM users WHERE id='$id'");
    header("Location: users.php");
    exit;
}

/* ================= EDIT ================= */
if(isset($_POST['edit'])){
    $id = intval($_POST['id']);
    $username = mysqli_real_escape_string($conn,$_POST['username']);
    $role = $_POST['role'];

    if(!empty($_POST['password'])){
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        mysqli_query($conn,"UPDATE users SET
        username='$username',
        password='$password',
        role='$role'
        WHERE id='$id'");
    } else {
        mysqli_query($conn,"UPDATE users SET
        username='$username',
        role='$role'
        WHERE id='$id'");
    }

    header("Location: users.php");
    exit;
}

/* ================= DATA ================= */
$data = mysqli_query($conn,"SELECT * FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Data User</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

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
    padding:20px;
}
.sidebar h4{color:white;text-align:center;}
.sidebar a{
    display:flex;
    gap:10px;
    padding:12px;
    color:#ccc;
    text-decoration:none;
    border-radius:12px;
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
    padding:30px;
}

/* TOPBAR */
.topbar{
    background:white;
    padding:15px 20px;
    border-radius:15px;
    margin-bottom:20px;
    box-shadow:0 5px 20px rgba(0,0,0,0.2);
}

/* CARD */
.card-box{
    background:rgba(255,255,255,0.95);
    border-radius:20px;
    padding:25px;
    box-shadow:0 15px 40px rgba(0,0,0,0.3);
}

/* BADGE */
.badge-role{
    padding:6px 12px;
    border-radius:20px;
    color:white;
    font-size:12px;
}
.admin{background:#ff4d4d;}
.petugas{background:#ff9800;}
.pengunjung{background:#4caf50;}

/* BUTTON */
.btn{border-radius:20px;}
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

<div class="topbar">
    <h5>👥 Data User</h5>
</div>

<div class="card-box">

<div class="d-flex justify-content-between mb-3">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambah">
        <i class="fa fa-plus"></i> Tambah User
    </button>
</div>

<div class="table-responsive">
<table class="table table-hover">
<tr class="table-dark">
<th>Username</th>
<th>Role</th>
<th>Aksi</th>
</tr>

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

<a href="?hapus=<?= $d['id'] ?>" 
class="btn btn-danger btn-sm"
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

<div class="modal-header">
<h5>Edit User</h5>
</div>

<div class="modal-body">
<input type="hidden" name="id" value="<?= $d['id'] ?>">

<input type="text" name="username" class="form-control mb-2" value="<?= $d['username'] ?>" required>

<input type="password" name="password" class="form-control mb-2" placeholder="Kosongkan jika tidak diubah">

<select name="role" class="form-control">
<option value="admin" <?= $d['role']=="admin"?"selected":"" ?>>admin</option>
<option value="petugas" <?= $d['role']=="petugas"?"selected":"" ?>>petugas</option>
<option value="pengunjung" <?= $d['role']=="pengunjung"?"selected":"" ?>>pengunjung</option>
</select>
</div>

<div class="modal-footer">
<button name="edit" class="btn btn-success">Simpan</button>
</div>

</form>
</div>
</div>
</div>

<?php } ?>

</table>
</div>

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
<input type="password" name="password" class="form-control mb-2" placeholder="Password" required>

<select name="role" class="form-control">
<option value="admin">admin</option>
<option value="petugas">petugas</option>
<option value="pengunjung">pengunjung</option>
</select>
</div>

<div class="modal-footer">
<button name="tambah" class="btn btn-primary">Simpan</button>
</div>

</form>

</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>