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
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Data Alamat - Catering Ibu Iqbal</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
:root {
    --bg-gradient: linear-gradient(135deg,#eef2f7,#dbe9f4);
    --sidebar-bg: linear-gradient(180deg,#141e30,#243b55);
    --btn-info: linear-gradient(45deg,#00c6ff,#0072ff);
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
    box-shadow: 4px 0 20px rgba(0,0,0,0.05);
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
    margin-bottom: 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 8px 30px rgba(0,0,0,0.04);
}

.topbar h5 {
    margin: 0;
    font-weight: 600;
    color: #243b55;
    font-size: 1.2rem;
}

.user-profile {
    display: flex;
    align-items: center;
    gap: 12px;
    font-weight: 600;
    color: #141e30;
    background: #f5f7fa;
    padding: 8px 18px;
    border-radius: 30px;
}

.user-profile i {
    font-size: 1.2rem;
    color: #5b86e5;
}

/* SECTION TITLE */
.section-title {
    font-size: 1.3rem;
    font-weight: 600;
    color: #141e30;
    margin-bottom: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

/* TABLE SECTION */
.table-wrapper {
    background: white;
    padding: 30px;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
}

.table-responsive::-webkit-scrollbar {
    display: none;
}
.table-responsive {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.table {
    margin-bottom: 0;
}

.table th {
    border-top: none;
    border-bottom: 2px solid #eef2f7;
    color: #6c757d;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
    padding: 15px;
}

.table td {
    vertical-align: middle;
    padding: 15px;
    color: #495057;
    font-weight: 500;
    border-bottom: 1px solid #eef2f7;
}

.table tr:last-child td {
    border-bottom: none;
}

.table tbody tr {
    transition: all 0.2s ease;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
    transform: scale(1.01);
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    border-radius: 10px;
}

/* BUTTONS */
.btn-info-custom {
    background: var(--btn-info);
    border: none;
    color: white;
    border-radius: 30px;
    padding: 10px 25px;
    font-weight: 500;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    box-shadow: 0 4px 15px rgba(0, 198, 255, 0.3);
}

.btn-info-custom:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 198, 255, 0.4);
    color: white;
}

.btn-action {
    border-radius: 50px;
    width: 35px;
    height: 35px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    margin: 0 2px;
    transition: all 0.2s;
}

.btn-action:hover {
    transform: scale(1.1);
}

/* MODALS */
.modal-content {
    border-radius: 15px;
    border: none;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.modal-header {
    background: #f8f9fa;
    border-bottom: 1px solid #eef2f7;
    border-radius: 15px 15px 0 0;
}

.modal-footer {
    border-top: 1px solid #eef2f7;
}

.form-control, .form-select {
    border-radius: 10px;
    padding: 10px 15px;
    border: 1px solid #eef2f7;
    margin-bottom: 15px;
}

.form-control:focus, .form-select:focus {
    box-shadow: 0 0 0 0.25rem rgba(0, 198, 255, 0.25);
    border-color: #00c6ff;
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
    <h4><i class="fa fa-utensils me-2"></i> Petugas</h4>

    <a href="index.php"><i class="fa fa-home"></i> <span>Dashboard</span></a>
    <a href="pesanan.php"><i class="fa fa-receipt"></i> <span>Pesanan</span></a>
    <a href="pembayaran.php"><i class="fa fa-credit-card"></i> <span>Pembayaran</span></a>
    <a href="detail_pesanan.php"><i class="fa fa-eye"></i> <span>Detail</span></a>
    <a href="alamat.php" class="active"><i class="fa fa-map-marker-alt"></i> <span>Alamat</span></a>
    <a href="../logout.php" class="mt-auto mb-2"><i class="fa fa-sign-out-alt"></i> <span>Logout</span></a>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">

    <!-- TOPBAR -->
    <div class="topbar">
        <h5><i class="fa fa-map-marker-alt me-2 text-primary"></i> Data Alamat</h5>
        <div class="user-profile">
            <i class="fa fa-user-circle"></i> Petugas
        </div>
    </div>

    <!-- TABLE WRAPPER -->
    <div class="table-wrapper">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="section-title">
                <i class="fa fa-map text-danger"></i> Kelola Alamat User
            </h5>
            <button class="btn-info-custom" data-bs-toggle="modal" data-bs-target="#tambah">
                <i class="fa fa-plus"></i> Tambah
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-borderless align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Nama Penerima</th>
                        <th>Alamat Lengkap</th>
                        <th>Kota</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($d = mysqli_fetch_array($data)){ ?>
                    <tr>
                        <td><strong>#<?= $d['id'] ?></strong></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-light p-2 rounded-circle text-primary d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                    <i class="fa fa-user"></i>
                                </div>
                                <?= htmlspecialchars($d['user_id']) ?>
                            </div>
                        </td>
                        <td><span class="text-dark fw-medium"><?= htmlspecialchars($d['nama_penerima']) ?></span></td>
                        <td>
                            <span class="d-inline-block text-truncate" style="max-width: 200px;" title="<?= htmlspecialchars($d['alamat']) ?>">
                                <i class="fa fa-map-pin me-1 text-muted"></i> <?= htmlspecialchars($d['alamat']) ?>
                            </span>
                        </td>
                        <td><span class="badge bg-light text-dark px-3 py-2 rounded-pill border"><?= htmlspecialchars($d['kota']) ?></span></td>

                        <td class="text-center">
                            <button class="btn btn-warning btn-action text-white shadow-sm" data-bs-toggle="modal" data-bs-target="#edit<?= $d['id'] ?>" title="Edit">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-action shadow-sm" onclick="konfirmasiHapus('?hapus=<?= $d['id'] ?>')" title="Hapus">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- MODAL EDIT -->
                    <div class="modal fade" id="edit<?= $d['id'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <form method="POST">
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold">Edit Alamat #<?= $d['id'] ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="id" value="<?= $d['id'] ?>">
                                        
                                        <label class="form-label text-muted small fw-bold mb-1 mt-2">User ID</label>
                                        <input type="number" name="user_id" class="form-control" value="<?= $d['user_id'] ?>" required>
                                        
                                        <label class="form-label text-muted small fw-bold mb-1 mt-2">Nama Penerima</label>
                                        <input type="text" name="nama_penerima" class="form-control" value="<?= htmlspecialchars($d['nama_penerima']) ?>" required>
                                        
                                        <label class="form-label text-muted small fw-bold mb-1 mt-2">Alamat Lengkap</label>
                                        <textarea name="alamat" class="form-control" rows="3" required><?= htmlspecialchars($d['alamat']) ?></textarea>
                                        
                                        <label class="form-label text-muted small fw-bold mb-1 mt-2">Kota</label>
                                        <input type="text" name="kota" class="form-control" value="<?= htmlspecialchars($d['kota']) ?>" required>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" name="edit" class="btn btn-primary rounded-pill px-4">Simpan Perubahan</button>
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

</div>

<!-- MODAL TAMBAH -->
<div class="modal fade" id="tambah" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Alamat Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label text-muted small fw-bold mb-1">User ID</label>
                    <input type="number" name="user_id" class="form-control" placeholder="Masukkan ID User" required>
                    
                    <label class="form-label text-muted small fw-bold mb-1 mt-2">Nama Penerima</label>
                    <input type="text" name="nama_penerima" class="form-control" placeholder="Masukkan Nama Penerima" required>
                    
                    <label class="form-label text-muted small fw-bold mb-1 mt-2">Alamat Lengkap</label>
                    <textarea name="alamat" class="form-control" rows="3" placeholder="Masukkan Alamat Lengkap" required></textarea>
                    
                    <label class="form-label text-muted small fw-bold mb-1 mt-2">Kota</label>
                    <input type="text" name="kota" class="form-control" placeholder="Masukkan Kota" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="tambah" class="btn btn-primary rounded-pill px-4">Simpan Data</button>
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
    <?php if($_GET['pesan'] == 'sukses_tambah'){ ?>
        Swal.fire({ title: 'Berhasil!', text: 'Data berhasil ditambahkan.', icon: 'success', confirmButtonClass: 'btn btn-primary rounded-pill px-4', buttonsStyling: false });
    <?php }elseif($_GET['pesan'] == 'sukses_edit'){ ?>
        Swal.fire({ title: 'Berhasil!', text: 'Data berhasil diperbarui.', icon: 'success', confirmButtonClass: 'btn btn-primary rounded-pill px-4', buttonsStyling: false });
    <?php }elseif($_GET['pesan'] == 'sukses_hapus'){ ?>
        Swal.fire({ title: 'Berhasil!', text: 'Data berhasil dihapus.', icon: 'success', confirmButtonClass: 'btn btn-primary rounded-pill px-4', buttonsStyling: false });
    <?php } ?>
</script>
<?php } ?>

</body>
</html>