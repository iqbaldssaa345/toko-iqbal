<?php
session_start();
include '../koneksi.php';

/* ================= HAPUS ================= */
if(isset($_GET['hapus'])){
    $id = intval($_GET['hapus']);
    mysqli_query($conn,"DELETE FROM users WHERE id='$id'");
    header("Location: users.php?pesan=sukses_hapus");
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

    header("Location: users.php?pesan=sukses_edit");
    exit;
}

/* ================= TAMBAH ================= */
if(isset($_POST['tambah'])){
    $username = mysqli_real_escape_string($conn,$_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    mysqli_query($conn,"INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')");

    header("Location: users.php?pesan=sukses_tambah");
    exit;
}

/* ================= SEARCH ================= */
$keyword = "";
if(isset($_GET['cari'])){
    $keyword = mysqli_real_escape_string($conn,$_GET['cari']);
    $data = mysqli_query($conn,"SELECT * FROM users 
    WHERE username LIKE '%$keyword%' 
    OR role LIKE '%$keyword%' 
    ORDER BY id ASC");
}else{
    $data = mysqli_query($conn,"SELECT * FROM users ORDER BY id ASC");
}
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
}
.sidebar a:hover{
    background:rgba(255,255,255,0.1);
    color:white;
}

.content{
    margin-left:270px;
    padding:30px;
}

.topbar{
    background:white;
    padding:15px 20px;
    border-radius:15px;
    margin-bottom:20px;
    box-shadow:0 5px 20px rgba(0,0,0,0.2);
}

.card-box{
    background:white;
    border-radius:20px;
    padding:25px;
    box-shadow:0 15px 40px rgba(0,0,0,0.3);
}

.badge-role{
    padding:6px 12px;
    border-radius:20px;
    color:white;
    font-size:12px;
}
.admin{background:#ff4d4d;}
.petugas{background:#ff9800;}
.pengunjung{background:#4caf50;}

.btn{border-radius:20px;}
input{border-radius:20px;}
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

<!-- TOPBAR + SEARCH -->
<div class="topbar d-flex justify-content-between align-items-center">
    <h5>👥 Data User</h5>

    <form method="GET" class="d-flex gap-2">
        <input type="text" name="cari" class="form-control"
        placeholder="Cari username / role..."
        value="<?= $keyword ?>">

        <button class="btn btn-dark">
            <i class="fa fa-search"></i>
        </button>
    </form>
</div>

<div class="card-box">

<button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#tambah">
    <i class="fa fa-plus"></i> Tambah User
</button>

<table class="table table-hover">
<tr class="table-dark">
<th>Username</th>
<th>Role</th>
<th>Aksi</th>
</tr>

<?php 
$modals = "";
while($d = mysqli_fetch_array($data)){ 
?>
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

<button type="button" class="btn btn-danger btn-sm" onclick="konfirmasiHapus('?hapus=<?= $d['id'] ?>')">
<i class="fa fa-trash"></i>
</button>
</td>
</tr>

<?php ob_start(); ?>
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
<?php 
$modals .= ob_get_clean();
} 
?>

</table>

<?= $modals ?>

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