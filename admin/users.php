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
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Data User - Admin</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
:root {
    --bg-gradient: linear-gradient(135deg,#1d2671,#c33764);
    --sidebar-bg: linear-gradient(180deg,#141e30,#243b55);
    --badge-admin: #ff4d4d;
    --badge-petugas: #ff9800;
    --badge-pengunjung: #4caf50;
}

body {
    font-family: 'Poppins', sans-serif;
    background: var(--bg-gradient);
    min-height: 100vh;
    overflow-x: hidden;
}

/* SIDEBAR */
.sidebar {
    width: 260px;
    height: 100vh;
    position: fixed;
    background: var(--sidebar-bg);
    color: white;
    padding: 30px 20px;
    box-shadow: 4px 0 20px rgba(0,0,0,0.15);
    z-index: 1000;
    display: flex;
    flex-direction: column;
}

.sidebar h4 {
    text-align: center;
    font-weight: 700;
    margin-bottom: 40px;
    font-size: 1.4rem;
    letter-spacing: 1px;
    color: #fff;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    padding-bottom: 20px;
}

.sidebar a {
    display: flex;
    align-items: center;
    gap: 15px;
    color: rgba(255,255,255,0.7);
    padding: 14px 20px;
    border-radius: 12px;
    text-decoration: none;
    margin-bottom: 12px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.sidebar a i {
    font-size: 1.2rem;
    width: 24px;
    text-align: center;
}

.sidebar a:hover, .sidebar a.active {
    background: rgba(255,255,255,0.1);
    color: white;
    transform: translateX(8px);
}

.sidebar a.active {
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    border-left: 4px solid #fff;
}

/* CONTENT AREA */
.main-content {
    margin-left: 260px;
    padding: 30px 40px;
}

/* TOPBAR */
.topbar {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    padding: 18px 30px;
    border-radius: 16px;
    margin-bottom: 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 8px 30px rgba(0,0,0,0.1);
}

.topbar h5 {
    margin: 0;
    font-weight: 600;
    color: #243b55;
    font-size: 1.2rem;
    display: flex;
    align-items: center;
    gap: 10px;
}

/* SEARCH FORM */
.search-form {
    display: flex;
    gap: 10px;
}

.search-form input {
    border-radius: 30px;
    padding: 10px 20px;
    border: 1px solid #e0e0e0;
    box-shadow: inset 0 2px 5px rgba(0,0,0,0.02);
    transition: all 0.3s ease;
}

.search-form input:focus {
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    border-color: #141e30;
}

.search-form button {
    border-radius: 30px;
    padding: 10px 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* CARD BOX */
.card-box {
    background: white;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.2);
}

/* BADGES */
.badge-role {
    padding: 6px 16px;
    border-radius: 30px;
    color: white;
    font-size: 0.85rem;
    font-weight: 600;
    letter-spacing: 0.5px;
    display: inline-block;
}

.admin { background: var(--badge-admin); }
.petugas { background: var(--badge-petugas); }
.pengunjung { background: var(--badge-pengunjung); }

/* TABLE */
.table-responsive::-webkit-scrollbar {
    display: none;
}
.table-responsive {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.table {
    margin-top: 15px;
    vertical-align: middle;
}

.table th {
    background: #141e30;
    color: white;
    font-weight: 500;
    border: none;
    padding: 15px;
    text-transform: uppercase;
    font-size: 0.9rem;
    letter-spacing: 0.5px;
}

.table th:first-child { border-top-left-radius: 12px; border-bottom-left-radius: 12px; }
.table th:last-child { border-top-right-radius: 12px; border-bottom-right-radius: 12px; }

.table td {
    padding: 15px;
    color: #495057;
    font-weight: 500;
    border-bottom: 1px solid #f1f1f1;
}

.table tbody tr {
    transition: all 0.2s ease;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
    transform: scale(1.005);
}

/* BUTTONS */
.btn-rounded {
    border-radius: 30px;
    padding: 10px 24px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.btn-rounded:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.15);
}

.btn-action {
    border-radius: 10px;
    width: 38px;
    height: 38px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-right: 5px;
    transition: all 0.2s ease;
}

.btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0,0,0,0.15);
}

/* MODALS */
.modal-content {
    border-radius: 20px;
    border: none;
    overflow: hidden;
}

.modal-header {
    background: #f8f9fa;
    border-bottom: 1px solid #eef2f7;
    padding: 20px 25px;
}

.modal-header h5 {
    font-weight: 600;
    color: #141e30;
    margin: 0;
}

.modal-body {
    padding: 25px;
}

.modal-body input, .modal-body select {
    border-radius: 12px;
    padding: 12px 15px;
    border: 1px solid #e0e0e0;
}

.modal-footer {
    border-top: none;
    padding: 15px 25px 25px;
}

.modal-footer .btn {
    border-radius: 30px;
    padding: 10px 25px;
}

/* RESPONSIVE */
@media (max-width: 991px) {
    .sidebar { width: 80px; padding: 20px 10px; }
    .sidebar h4, .sidebar a span { display: none; }
    .sidebar a { justify-content: center; padding: 15px; }
    .main-content { margin-left: 80px; padding: 20px; }
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h4><i class="fa fa-utensils me-2"></i> Admin</h4>

    <a href="index.php"><i class="fa fa-home"></i> <span>Dashboard</span></a>
    <a href="users.php" class="active"><i class="fa fa-users"></i> <span>Data User</span></a>
    <a href="produk.php"><i class="fa fa-box"></i> <span>Produk</span></a>
    <a href="ongkir.php"><i class="fa fa-truck"></i> <span>Ongkir</span></a>
    <a href="../logout.php" class="mt-auto mb-2"><i class="fa fa-sign-out-alt"></i> <span>Logout</span></a>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">

    <!-- TOPBAR + SEARCH -->
    <div class="topbar">
        <h5><i class="fa fa-users text-primary"></i> Data User</h5>

        <form method="GET" class="search-form">
            <input type="text" name="cari" class="form-control"
            placeholder="Cari username / role..."
            value="<?= htmlspecialchars($keyword) ?>">

            <button class="btn btn-dark">
                <i class="fa fa-search"></i>
            </button>
        </form>
    </div>

    <!-- CARD BOX -->
    <div class="card-box">

        <button class="btn btn-primary btn-rounded mb-4" data-bs-toggle="modal" data-bs-target="#tambah">
            <i class="fa fa-plus"></i> Tambah User Baru
        </button>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Role</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $modals = "";
                    while($d = mysqli_fetch_array($data)){ 
                    ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-light p-2 rounded-circle text-secondary d-flex justify-content-center align-items-center" style="width: 40px; height: 40px;">
                                    <i class="fa fa-user"></i>
                                </div>
                                <?= htmlspecialchars($d['username']) ?>
                            </div>
                        </td>
                        <td>
                            <span class="badge-role <?= $d['role'] ?>">
                                <?= strtoupper($d['role']) ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-warning text-white btn-action" data-bs-toggle="modal" data-bs-target="#edit<?= $d['id'] ?>" title="Edit">
                                <i class="fa fa-edit"></i>
                            </button>

                            <button type="button" class="btn btn-danger btn-action" onclick="konfirmasiHapus('?hapus=<?= $d['id'] ?>')" title="Hapus">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <?php ob_start(); ?>
                    <!-- MODAL EDIT -->
                    <div class="modal fade" id="edit<?= $d['id'] ?>">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <form method="POST">
                                    <div class="modal-header">
                                        <h5 class="modal-title"><i class="fa fa-user-edit me-2 text-warning"></i> Edit User</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="id" value="<?= $d['id'] ?>">
                                        
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold text-secondary small">Username</label>
                                            <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($d['username']) ?>" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-semibold text-secondary small">Password Baru</label>
                                            <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diubah">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-semibold text-secondary small">Role Pengguna</label>
                                            <select name="role" class="form-select" style="border-radius:12px; padding:12px 15px; border:1px solid #e0e0e0;">
                                                <option value="admin" <?= $d['role']=="admin"?"selected":"" ?>>Admin</option>
                                                <option value="petugas" <?= $d['role']=="petugas"?"selected":"" ?>>Petugas</option>
                                                <option value="pengunjung" <?= $d['role']=="pengunjung"?"selected":"" ?>>Pengunjung</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                        <button name="edit" class="btn btn-success"><i class="fa fa-save me-1"></i> Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php 
                    $modals .= ob_get_clean();
                    } 
                    ?>
                </tbody>
            </table>
        </div>

        <?= $modals ?>

    </div>
</div>

<!-- MODAL TAMBAH -->
<div class="modal fade" id="tambah">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa fa-user-plus me-2 text-primary"></i> Tambah User Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small">Username</label>
                        <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small">Role Pengguna</label>
                        <select name="role" class="form-select" style="border-radius:12px; padding:12px 15px; border:1px solid #e0e0e0;">
                            <option value="admin">Admin</option>
                            <option value="petugas">Petugas</option>
                            <option value="pengunjung">Pengunjung</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button name="tambah" class="btn btn-primary"><i class="fa fa-save me-1"></i> Simpan</button>
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