<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin"){
    header("location:../login.php");
    exit;
}
include '../koneksi.php';

// Data Admin
$id_admin = $_SESSION['id'];
$q_admin = mysqli_query($conn, "SELECT username FROM users WHERE id='$id_admin'");
$d_admin = mysqli_fetch_assoc($q_admin);
$nama_admin = $d_admin['username'];

/* ================= HAPUS ================= */
if(isset($_GET['hapus'])){
    $id = intval($_GET['hapus']);
    
    // Jangan biarkan admin menghapus dirinya sendiri
    if($id == $id_admin){
        header("Location: users.php?pesan=gagal_hapus_diri");
        exit;
    }

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
    <link href="../css/dashboard.css" rel="stylesheet">
    
    <style>
        .badge-role {
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
        }

        .badge-role.admin { background-color: #ffe3e3; color: #f03e3e; border: 1px solid rgba(240,62,62,0.2); }
        .badge-role.petugas { background-color: #fff4e6; color: #fd7e14; border: 1px solid rgba(253,126,20,0.2); }
        .badge-role.pengunjung { background-color: #e6fcf5; color: #0ca678; border: 1px solid rgba(12,166,120,0.2); }

        .search-form {
            display: flex;
            gap: 10px;
        }

        .search-form input {
            border-radius: 30px;
            padding: 8px 18px;
            border: 1px solid rgba(0,0,0,0.08);
            font-size: 0.9rem;
        }
    </style>
</head>

<body>

    <!-- SIDEBAR -->
    <?php include '../includes/sidebar_admin.php'; ?>

    <!-- MAIN CONTENT -->
    <div class="main-content">

        <!-- TOPBAR -->
        <div class="topbar">
            <div class="topbar-title">
                <button class="sidebar-toggle-btn" id="sidebarToggle" onclick="toggleSidebar()">
                    <i class="fa fa-bars"></i>
                </button>
                <h5><i class="fa fa-users text-primary"></i> Data User</h5>
            </div>
            
            <form method="GET" class="search-form align-items-center">
                <input type="text" name="cari" class="form-control" placeholder="Cari username / role..." value="<?= htmlspecialchars($keyword) ?>">
                <button class="btn btn-dark rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="fa fa-search"></i>
                </button>
            </form>
        </div>

        <!-- CARD BOX -->
        <div class="card-premium">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title-premium mb-0"><i class="fa fa-list"></i> Daftar Pengguna Aplikasi</h5>
                <button class="btn-premium-primary" data-bs-toggle="modal" data-bs-target="#tambah">
                    <i class="fa fa-user-plus"></i> Tambah User
                </button>
            </div>

            <div class="table-responsive">
                <table class="table-premium">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Hak Akses (Role)</th>
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
                                    <span class="fw-semibold"><?= htmlspecialchars($d['username']) ?></span>
                                </div>
                            </td>
                            <td>
                                <span class="badge-role <?= $d['role'] ?>">
                                    <?= strtoupper($d['role']) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="btn-action-premium btn-edit-premium me-1" data-bs-toggle="modal" data-bs-target="#edit<?= $d['id'] ?>" title="Edit">
                                    <i class="fa fa-edit"></i>
                                </button>

                                <button type="button" class="btn-action-premium btn-delete-premium" onclick="konfirmasiHapus('?hapus=<?= $d['id'] ?>')" title="Hapus">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>

                        <?php ob_start(); ?>
                        <!-- MODAL EDIT -->
                        <div class="modal fade" id="edit<?= $d['id'] ?>" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <form method="POST">
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold"><i class="fa fa-user-edit me-2 text-warning"></i> Edit User</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body text-start">
                                            <input type="hidden" name="id" value="<?= $d['id'] ?>">
                                            
                                            <div class="mb-3">
                                                <label class="form-label-muted">Username</label>
                                                <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($d['username']) ?>" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label-muted">Password Baru (Opsional)</label>
                                                <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diubah">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label-muted">Role Pengguna</label>
                                                <select name="role" class="form-select">
                                                    <option value="admin" <?= $d['role']=="admin"?"selected":"" ?>>Admin</option>
                                                    <option value="petugas" <?= $d['role']=="petugas"?"selected":"" ?>>Petugas</option>
                                                    <option value="pengunjung" <?= $d['role']=="pengunjung"?"selected":"" ?>>Pengunjung</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" name="edit" class="btn-premium-primary"><i class="fa fa-save"></i> Simpan</button>
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
    <div class="modal fade" id="tambah" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold"><i class="fa fa-user-plus me-2 text-primary"></i> Tambah User Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-start">
                        <div class="mb-3">
                            <label class="form-label-muted">Username</label>
                            <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label-muted">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label-muted">Role Pengguna</label>
                            <select name="role" class="form-select">
                                <option value="admin">Admin</option>
                                <option value="petugas">Petugas</option>
                                <option value="pengunjung">Pengunjung</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah" class="btn-premium-primary"><i class="fa fa-save"></i> Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        if (sidebar && backdrop) {
            sidebar.classList.toggle('show');
            backdrop.classList.toggle('show');
        }
    }
    document.addEventListener('DOMContentLoaded', function() {
        const backdrop = document.getElementById('sidebarBackdrop');
        if (backdrop) {
            backdrop.addEventListener('click', function() {
                const sidebar = document.getElementById('sidebar');
                if (sidebar) sidebar.classList.remove('show');
                backdrop.classList.remove('show');
            });
        }
    });

    function konfirmasiHapus(url) {
        Swal.fire({
            title: 'Yakin hapus data ini?',
            text: "Data yang dihapus tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-danger rounded-pill px-4 mx-2',
                cancelButton: 'btn btn-secondary rounded-pill px-4 mx-2'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        })
    }
    </script>
    <?php if(isset($_GET['pesan'])){ ?>
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
        <?php if($_GET['pesan'] == 'sukses_tambah'){ ?>
            Toast.fire({ icon: 'success', title: 'User berhasil ditambahkan.' });
        <?php }elseif($_GET['pesan'] == 'sukses_edit'){ ?>
            Toast.fire({ icon: 'success', title: 'User berhasil diperbarui.' });
        <?php }elseif($_GET['pesan'] == 'sukses_hapus'){ ?>
            Toast.fire({ icon: 'success', title: 'User berhasil dihapus.' });
        <?php }elseif($_GET['pesan'] == 'gagal_hapus_diri'){ ?>
            Toast.fire({ icon: 'error', title: 'Gagal menghapus akun sendiri!' });
        <?php } ?>
    </script>
    <?php } ?>

</body>
</html>