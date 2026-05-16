<?php
session_start();
include '../koneksi.php';

/* ================= TAMBAH ================= */
if(isset($_POST['tambah'])){
    $nama = mysqli_real_escape_string($conn,$_POST['nama']);
    $des = mysqli_real_escape_string($conn,$_POST['deskripsi']);
    $harga = intval($_POST['harga']);

    $gambar = $_FILES['gambar']['name'];
    $tmp = $_FILES['gambar']['tmp_name'];

    if($gambar){
        move_uploaded_file($tmp,"../upload/".$gambar);
    }

    mysqli_query($conn,"INSERT INTO produk (nama,deskripsi,harga,gambar)
    VALUES('$nama','$des','$harga','$gambar')");

    header("location:produk.php?pesan=sukses_tambah");
}

/* ================= HAPUS ================= */
if(isset($_GET['hapus'])){
    $id = intval($_GET['hapus']);
    mysqli_query($conn,"DELETE FROM produk WHERE id='$id'");
    header("location:produk.php?pesan=sukses_hapus");
}

/* ================= EDIT ================= */
if(isset($_POST['edit'])){
    $id = intval($_POST['id']);
    $nama = mysqli_real_escape_string($conn,$_POST['nama']);
    $des = mysqli_real_escape_string($conn,$_POST['deskripsi']);
    $harga = intval($_POST['harga']);

    if(!empty($_FILES['gambar']['name'])){
        $gambar = $_FILES['gambar']['name'];
        $tmp = $_FILES['gambar']['tmp_name'];

        move_uploaded_file($tmp,"../upload/".$gambar);

        mysqli_query($conn,"UPDATE produk SET
        nama='$nama',
        deskripsi='$des',
        harga='$harga',
        gambar='$gambar'
        WHERE id='$id'");
    }else{
        mysqli_query($conn,"UPDATE produk SET
        nama='$nama',
        deskripsi='$des',
        harga='$harga'
        WHERE id='$id'");
    }

    header("location:produk.php?pesan=sukses_edit");
}

/* ================= DATA ================= */
$data = mysqli_query($conn,"SELECT * FROM produk ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Data Produk - Admin</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
:root {
    --bg-gradient: linear-gradient(135deg,#1d2671,#c33764);
    --sidebar-bg: linear-gradient(180deg,#141e30,#243b55);
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

/* CARD BOX */
.card-box {
    background: white;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.2);
}

/* IMAGE */
.img-produk {
    width: 65px;
    height: 65px;
    object-fit: cover;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.img-produk:hover {
    transform: scale(1.1);
}

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

.price-badge {
    background: #eef2f7;
    padding: 6px 14px;
    border-radius: 20px;
    color: #243b55;
    font-weight: 600;
    font-size: 0.95rem;
    display: inline-block;
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

.modal-body input, .modal-body textarea {
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
    <a href="users.php"><i class="fa fa-users"></i> <span>Data User</span></a>
    <a href="produk.php" class="active"><i class="fa fa-box"></i> <span>Produk</span></a>
    <a href="ongkir.php"><i class="fa fa-truck"></i> <span>Ongkir</span></a>
    <a href="../logout.php" class="mt-auto mb-2"><i class="fa fa-sign-out-alt"></i> <span>Logout</span></a>
</div>

<div class="main-content">

    <!-- TOPBAR -->
    <div class="topbar">
        <h5><i class="fa fa-box-open text-primary"></i> Data Produk</h5>
    </div>

    <!-- CARD BOX -->
    <div class="card-box">
        
        <button class="btn btn-primary btn-rounded mb-4" data-bs-toggle="modal" data-bs-target="#tambah">
            <i class="fa fa-plus"></i> Tambah Produk Baru
        </button>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th class="text-center">Gambar</th>
                        <th>Nama Produk</th>
                        <th>Deskripsi</th>
                        <th>Harga</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $modals = "";
                    while($d = mysqli_fetch_array($data)){ 
                    ?>
                    <tr>
                        <td class="text-center">
                            <img src="../upload/<?= htmlspecialchars($d['gambar']) ?>" class="img-produk" alt="<?= htmlspecialchars($d['nama']) ?>">
                        </td>
                        <td>
                            <div class="fw-semibold text-dark"><?= htmlspecialchars($d['nama']) ?></div>
                        </td>
                        <td>
                            <span class="text-muted" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; font-size: 0.9rem;">
                                <?= htmlspecialchars($d['deskripsi']) ?>
                            </span>
                        </td>
                        <td>
                            <span class="price-badge">Rp <?= number_format($d['harga'], 0, ',', '.') ?></span>
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
                                <form method="POST" enctype="multipart/form-data">
                                    <div class="modal-header">
                                        <h5 class="modal-title"><i class="fa fa-edit me-2 text-warning"></i> Edit Produk</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="id" value="<?= $d['id'] ?>">
                                        
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold text-secondary small">Nama Produk</label>
                                            <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($d['nama']) ?>" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-semibold text-secondary small">Deskripsi Singkat</label>
                                            <textarea name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($d['deskripsi']) ?></textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-semibold text-secondary small">Harga (Rp)</label>
                                            <input type="number" name="harga" class="form-control" value="<?= $d['harga'] ?>" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-semibold text-secondary small">Upload Gambar Baru (Opsional)</label>
                                            <input type="file" name="gambar" class="form-control">
                                        </div>

                                        <div class="mt-3 text-center bg-light p-3 rounded-3">
                                            <p class="small text-muted mb-2">Gambar Saat Ini:</p>
                                            <img src="../upload/<?= htmlspecialchars($d['gambar']) ?>" class="rounded shadow-sm" width="120" style="object-fit: cover;">
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
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa fa-box-open me-2 text-primary"></i> Tambah Produk Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small">Nama Produk</label>
                        <input type="text" name="nama" class="form-control" placeholder="Contoh: Nasi Goreng Spesial" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small">Deskripsi Singkat</label>
                        <textarea name="deskripsi" class="form-control" rows="3" placeholder="Masukkan deskripsi produk..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small">Harga (Rp)</label>
                        <input type="number" name="harga" class="form-control" placeholder="Contoh: 25000" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small">Upload Gambar</label>
                        <input type="file" name="gambar" class="form-control" required>
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
        title: 'Yakin hapus produk ini?',
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
        Swal.fire('Berhasil!', 'Produk berhasil ditambahkan.', 'success');
    <?php }elseif($_GET['pesan'] == 'sukses_edit'){ ?>
        Swal.fire('Berhasil!', 'Produk berhasil diperbarui.', 'success');
    <?php }elseif($_GET['pesan'] == 'sukses_hapus'){ ?>
        Swal.fire('Berhasil!', 'Produk berhasil dihapus.', 'success');
    <?php } ?>
</script>
<?php } ?>

</body>
</html>