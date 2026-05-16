<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role']!="pengunjung"){
    header("location:../login.php");
    exit;
}
include '../koneksi.php';

$user_id = $_SESSION['id'];

/* ================= TAMBAH ================= */
if(isset($_POST['tambah'])){
    $produk_id = intval($_POST['produk_id']);
    $jumlah = intval($_POST['jumlah']);

    // ambil harga produk
    $produk = mysqli_fetch_assoc(mysqli_query($conn,"SELECT harga FROM produk WHERE id='$produk_id'"));
    $harga = $produk['harga'];

    $subtotal = $harga * $jumlah;

    // buat pesanan baru (simple)
    mysqli_query($conn,"INSERT INTO pesanan(user_id) VALUES('$user_id')");
    $pesanan_id = mysqli_insert_id($conn);

    // detail pesanan
    mysqli_query($conn,"INSERT INTO detail_pesanan(pesanan_id,produk_id,jumlah,subtotal)
    VALUES('$pesanan_id','$produk_id','$jumlah','$subtotal')");

    header("location:pesanan_saya.php?pesan=sukses_tambah");
    exit;
}

/* ================= HAPUS ================= */
if(isset($_GET['hapus'])){
    $id = intval($_GET['hapus']);

    // hapus detail + pesanan
    mysqli_query($conn,"DELETE dp FROM detail_pesanan dp 
        JOIN pesanan p ON dp.pesanan_id=p.id 
        WHERE dp.id='$id' AND p.user_id='$user_id'");

    header("location:pesanan_saya.php?pesan=sukses_hapus");
    exit;
}

/* ================= DATA ================= */
$data = mysqli_query($conn,"
SELECT dp.*, pr.nama, pr.harga, pr.gambar, p.id as pesanan_id 
FROM detail_pesanan dp
JOIN pesanan p ON dp.pesanan_id=p.id
JOIN produk pr ON dp.produk_id=pr.id
WHERE p.user_id='$user_id'
ORDER BY dp.id DESC
");

/* ================= PRODUK ================= */
$produk = mysqli_query($conn,"SELECT * FROM produk");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pesanan Saya - Catering Ibu Iqbal</title>

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
.form-control, .form-select {
    border-radius: 10px;
    padding: 12px 15px;
    border: 1px solid #eef2f7;
    background: #f8f9fa;
    transition: all 0.3s ease;
}
.form-control:focus, .form-select:focus {
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

/* PESANAN LIST */
.pesanan-box {
    background: white;
    border: 1px solid #eef2f7;
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.02);
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 20px;
}

.pesanan-box:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    border-color: #dbe9f4;
}

.pesanan-img {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 12px;
}

.pesanan-details {
    flex-grow: 1;
}

.pesanan-details h6 {
    font-weight: 700;
    color: #243b55;
    margin-bottom: 5px;
    font-size: 1.1rem;
}

.pesanan-meta {
    color: #6c757d;
    font-size: 0.9rem;
    margin-bottom: 8px;
}

.pesanan-price {
    font-weight: 700;
    color: #11998e;
    font-size: 1.1rem;
}

.btn-hapus-pesanan {
    background: #fff0f0;
    color: #dc3545;
    border: none;
    padding: 10px 20px;
    border-radius: 30px;
    font-weight: 600;
    transition: all 0.3s;
    font-size: 0.9rem;
}
.btn-hapus-pesanan:hover {
    background: #dc3545;
    color: white;
    transform: scale(1.05);
}

/* RESPONSIVE */
@media (max-width: 991px) {
    .sidebar { width: 80px; padding: 20px 10px; }
    .sidebar h4, .sidebar a span { display: none; }
    .sidebar a { justify-content: center; padding: 15px; }
    .main-content { margin-left: 80px; padding: 20px; }
    .pesanan-box { flex-direction: column; text-align: center; }
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h4><i class="fa fa-utensils me-2"></i> Pengunjung</h4>

    <a href="index.php"><i class="fa fa-home"></i> <span>Dashboard</span></a>
    <a href="alamat.php"><i class="fa fa-map-marker-alt"></i> <span>Alamat</span></a>
    <a href="pesanan_saya.php" class="active"><i class="fa fa-shopping-cart"></i> <span>Pesanan Saya</span></a>
    <a href="pembayaran.php"><i class="fa fa-credit-card"></i> <span>Pembayaran</span></a>
    <a href="../logout.php" class="mt-auto mb-2"><i class="fa fa-sign-out-alt"></i> <span>Logout</span></a>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">

    <!-- TOPBAR -->
    <div class="topbar">
        <h5><i class="fa fa-shopping-cart me-2 text-primary"></i> Pesanan Saya</h5>
        <div class="user-profile">
            <i class="fa fa-user-circle"></i> <?= htmlspecialchars($_SESSION['username'] ?? 'User') ?>
        </div>
    </div>

    <div class="row">
        <!-- FORM PESAN CEPAT -->
        <div class="col-lg-4 mb-4">
            <div class="card-box">
                <h5 class="card-title"><i class="fa fa-bolt text-warning"></i> Pesan Cepat</h5>
                
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted ms-1">Pilih Produk</label>
                        <select name="produk_id" class="form-select" required>
                            <option value="">-- Pilih Produk --</option>
                            <?php while($p=mysqli_fetch_array($produk)){ ?>
                            <option value="<?= $p['id'] ?>">
                                <?= htmlspecialchars($p['nama']) ?> - Rp <?= number_format($p['harga'], 0, ',', '.') ?>
                            </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted ms-1">Jumlah Porsi</label>
                        <input type="number" name="jumlah" class="form-control" placeholder="Masukkan Jumlah Porsi" min="1" required>
                    </div>
                    
                    <button name="tambah" class="btn btn-success-custom w-100">
                        <i class="fa fa-cart-plus me-1"></i> Tambah ke Pesanan
                    </button>
                </form>
            </div>
        </div>

        <!-- DAFTAR PESANAN -->
        <div class="col-lg-8">
            <div class="card-box">
                <h5 class="card-title mb-4"><i class="fa fa-list text-info"></i> Daftar Pesanan Aktif</h5>
                
                <div>
                    <?php if(mysqli_num_rows($data) > 0){ ?>
                        <?php while($d=mysqli_fetch_array($data)){ ?>
                        <div class="pesanan-box">
                            <?php if(!empty($d['gambar'])){ ?>
                                <img src="../upload/<?= htmlspecialchars($d['gambar']) ?>" class="pesanan-img" alt="<?= htmlspecialchars($d['nama']) ?>">
                            <?php } else { ?>
                                <div class="pesanan-img bg-light d-flex align-items-center justify-content-center text-muted">
                                    <i class="fa fa-image fa-2x"></i>
                                </div>
                            <?php } ?>

                            <div class="pesanan-details">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6><?= htmlspecialchars($d['nama']) ?></h6>
                                        <div class="pesanan-meta">
                                            ID Pesanan: #<?= $d['pesanan_id'] ?> &bull; 
                                            Harga: Rp <?= number_format($d['harga'], 0, ',', '.') ?> &bull; 
                                            Jumlah: <?= $d['jumlah'] ?> Porsi
                                        </div>
                                        <div class="pesanan-price">Total: Rp <?= number_format($d['subtotal'], 0, ',', '.') ?></div>
                                    </div>
                                    
                                    <button type="button" class="btn-hapus-pesanan" onclick="konfirmasiHapus('?hapus=<?= $d['id'] ?>')" title="Batalkan Pesanan">
                                        <i class="fa fa-trash me-1"></i> Batal
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    <?php } else { ?>
                        <div class="text-center py-5 text-muted">
                            <i class="fa fa-shopping-basket fa-3x mb-3 text-light"></i>
                            <p>Kamu belum memiliki pesanan aktif.<br>Silakan pesan makanan favoritmu terlebih dahulu!</p>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function konfirmasiHapus(url) {
    Swal.fire({
        title: 'Batalkan Pesanan?',
        text: "Pesanan ini akan dibatalkan secara permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Batalkan!',
        cancelButtonText: 'Kembali',
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
        Swal.fire({ title: 'Berhasil!', text: 'Pesanan berhasil ditambahkan.', icon: 'success', confirmButtonClass: 'btn btn-primary rounded-pill px-4', buttonsStyling: false });
    <?php }elseif($_GET['pesan'] == 'sukses_hapus'){ ?>
        Swal.fire({ title: 'Berhasil!', text: 'Pesanan berhasil dibatalkan.', icon: 'success', confirmButtonClass: 'btn btn-primary rounded-pill px-4', buttonsStyling: false });
    <?php } ?>
</script>
<?php } ?>

</body>
</html>