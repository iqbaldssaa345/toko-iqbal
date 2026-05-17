<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role']!="pengunjung"){
    header("location:../login.php");
    exit;
}
include '../koneksi.php';

$user_id = $_SESSION['id'];
$q_pengunjung = mysqli_query($conn, "SELECT username FROM users WHERE id='$user_id'");
$d_pengunjung = mysqli_fetch_assoc($q_pengunjung);
$nama_pengunjung = $d_pengunjung['username'];

/* TAMBAH */
if(isset($_POST['tambah'])){
    $nama = mysqli_real_escape_string($conn,$_POST['nama']);
    $alamat = mysqli_real_escape_string($conn,$_POST['alamat']);
    $kota = mysqli_real_escape_string($conn,$_POST['kota']);

    if($nama && $alamat && $kota){
        mysqli_query($conn,"INSERT INTO alamat(user_id,nama_penerima,alamat,kota)
        VALUES('$user_id','$nama','$alamat','$kota')");
    }

    header("location:alamat.php?pesan=sukses_tambah");
    exit;
}

/* HAPUS */
if(isset($_GET['hapus'])){
    $id = intval($_GET['hapus']);
    mysqli_query($conn,"DELETE FROM alamat WHERE id='$id' AND user_id='$user_id'");
    header("location:alamat.php?pesan=sukses_hapus");
    exit;
}

$data = mysqli_query($conn,"SELECT * FROM alamat WHERE user_id='$user_id' ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Alamat Saya - Catering Ibu Iqbal</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
:root {
    --bg-gradient: linear-gradient(135deg,#eef2f7,#dbe9f4);
    --sidebar-bg: linear-gradient(180deg,#141e30,#243b55);
    --btn-info: linear-gradient(45deg,#00c6ff,#0072ff);
    --btn-success: linear-gradient(45deg,#11998e,#38ef7d);
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

/* CARD */
.card-box {
    background: white;
    padding: 30px;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    margin-bottom: 30px;
}

.card-title {
    font-weight: 700;
    color: #141e30;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

/* FORM ELEMENTS */
.form-control {
    border-radius: 10px;
    padding: 12px 15px;
    border: 1px solid #eef2f7;
    background: #f8f9fa;
    transition: all 0.3s ease;
}
.form-control:focus {
    box-shadow: 0 0 0 0.25rem rgba(17, 153, 142, 0.25);
    border-color: #11998e;
    background: white;
}

.btn-success-custom {
    background: var(--btn-success);
    color: white;
    border: none;
    border-radius: 10px;
    padding: 12px 25px;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(17, 153, 142, 0.3);
}
.btn-success-custom:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(17, 153, 142, 0.4);
    color: white;
}

/* GRID ALAMAT */
.alamat-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

.alamat-box {
    background: white;
    border: 1px solid #eef2f7;
    border-radius: 15px;
    padding: 25px;
    position: relative;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(0,0,0,0.02);
}

.alamat-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    border-color: #dbe9f4;
}

.alamat-icon {
    width: 50px;
    height: 50px;
    background: #eef2f7;
    color: #11998e;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-bottom: 15px;
}

.alamat-box h6 {
    font-weight: 700;
    color: #243b55;
    margin-bottom: 8px;
    font-size: 1.1rem;
}

.alamat-box p {
    color: #6c757d;
    margin-bottom: 15px;
    font-size: 0.95rem;
    line-height: 1.5;
}

.kota-badge {
    background: #f8f9fa;
    color: #243b55;
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    display: inline-block;
}

.btn-hapus {
    position: absolute;
    top: 20px;
    right: 20px;
    background: #fff0f0;
    color: #dc3545;
    border: none;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s;
}
.btn-hapus:hover {
    background: #dc3545;
    color: white;
    transform: scale(1.1);
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
    <h4><i class="fa fa-utensils me-2"></i> Pengunjung</h4>

    <a href="index.php"><i class="fa fa-home"></i> <span>Dashboard</span></a>
    <a href="alamat.php" class="active"><i class="fa fa-map-marker-alt"></i> <span>Alamat</span></a>
    <a href="pesanan_saya.php"><i class="fa fa-shopping-cart"></i> <span>Pesanan Saya</span></a>
    <a href="pembayaran.php"><i class="fa fa-credit-card"></i> <span>Pembayaran</span></a>
    <a href="../logout.php" class="mt-auto mb-2"><i class="fa fa-sign-out-alt"></i> <span>Logout</span></a>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">

    <!-- TOPBAR -->
    <div class="topbar">
        <h5><i class="fa fa-map-marker-alt me-2 text-primary"></i> Kelola Alamat</h5>
        <div class="user-profile">
            <i class="fa fa-user-circle"></i> <?= htmlspecialchars($nama_pengunjung) ?>
        </div>
    </div>

    <div class="row">
        <!-- FORM TAMBAH -->
        <div class="col-lg-4 mb-4">
            <div class="card-box">
                <h5 class="card-title"><i class="fa fa-plus-circle text-success"></i> Tambah Alamat</h5>
                
                <form method="POST">
                    <div class="mb-3">
                        <input type="text" name="nama" placeholder="Nama Penerima" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <textarea name="alamat" placeholder="Alamat Lengkap" class="form-control" rows="4" required></textarea>
                    </div>
                    <div class="mb-4">
                        <input type="text" name="kota" placeholder="Kota" class="form-control" required>
                    </div>
                    
                    <button name="tambah" class="btn btn-success-custom w-100">
                        <i class="fa fa-save me-1"></i> Simpan Alamat
                    </button>
                </form>
            </div>
        </div>

        <!-- DAFTAR ALAMAT -->
        <div class="col-lg-8">
            <div class="card-box">
                <h5 class="card-title mb-4"><i class="fa fa-list text-info"></i> Daftar Alamat Saya</h5>
                
                <div class="alamat-grid">
                    <?php while($d=mysqli_fetch_array($data)){ ?>
                    <div class="alamat-box">
                        <button type="button" class="btn-hapus" onclick="konfirmasiHapus('?hapus=<?= $d['id'] ?>')" title="Hapus Alamat">
                            <i class="fa fa-trash"></i>
                        </button>

                        <div class="alamat-icon">
                            <i class="fa fa-home"></i>
                        </div>

                        <h6><?= htmlspecialchars($d['nama_penerima']) ?></h6>
                        <p><?= htmlspecialchars($d['alamat']) ?></p>
                        <div class="kota-badge"><i class="fa fa-map-pin me-1"></i> <?= htmlspecialchars($d['kota']) ?></div>
                    </div>
                    <?php } ?>
                </div>

                <?php if(mysqli_num_rows($data) == 0){ ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fa fa-map-marked-alt fa-3x mb-3 text-light"></i>
                        <p>Belum ada alamat yang tersimpan. Silakan tambahkan alamat baru.</p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function konfirmasiHapus(url) {
    Swal.fire({
        title: 'Yakin hapus alamat ini?',
        text: "Alamat yang dihapus tidak bisa dikembalikan!",
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
    <?php if($_GET['pesan'] == 'sukses_tambah'){ ?>
        Swal.fire({ title: 'Berhasil!', text: 'Alamat berhasil ditambahkan.', icon: 'success', confirmButtonClass: 'btn btn-primary rounded-pill px-4', buttonsStyling: false });
    <?php }elseif($_GET['pesan'] == 'sukses_hapus'){ ?>
        Swal.fire({ title: 'Berhasil!', text: 'Alamat berhasil dihapus.', icon: 'success', confirmButtonClass: 'btn btn-primary rounded-pill px-4', buttonsStyling: false });
    <?php } ?>
</script>
<?php } ?>

</body>
</html>