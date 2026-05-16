<?php
session_start();
include '../koneksi.php';

if(!isset($_SESSION['id']) || $_SESSION['role']!="pengunjung"){
    header("location:../login.php");
    exit;
}

$user_id = $_SESSION['id'];

/* ================= TAMBAH ================= */
if(isset($_POST['tambah'])){
    $pesanan_id = intval($_POST['pesanan_id']);
    $metode = $_POST['metode'];

    mysqli_query($conn,"INSERT INTO pembayaran (pesanan_id, metode) 
    VALUES ('$pesanan_id','$metode')");

    header("location:pembayaran.php?pesan=sukses_tambah");
    exit;
}

/* ================= HAPUS ================= */
if(isset($_GET['hapus'])){
    $id = intval($_GET['hapus']);
    mysqli_query($conn,"DELETE FROM pembayaran WHERE id='$id'");
    header("location:pembayaran.php?pesan=sukses_hapus");
    exit;
}

/* ================= DATA ================= */
$data = mysqli_query($conn,"
SELECT pembayaran.*, pesanan.total 
FROM pembayaran 
JOIN pesanan ON pembayaran.pesanan_id = pesanan.id
WHERE pesanan.user_id='$user_id'
ORDER BY pembayaran.id DESC
");

/* ================= PESANAN ================= */
$pesanan = mysqli_query($conn,"SELECT * FROM pesanan WHERE user_id='$user_id'");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pembayaran - Catering Ibu Iqbal</title>

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

/* TABLE SECTION */
.table-responsive::-webkit-scrollbar { display: none; }
.table-responsive { -ms-overflow-style: none; scrollbar-width: none; }
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
.table tbody tr:hover {
    background-color: #f8f9fa;
    border-radius: 10px;
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

/* BADGES SIMPLE PILL */
.badge-status {
    display: inline-flex;
    align-items: center;
    padding: 6px 16px 6px 8px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 14px;
    gap: 8px;
}

.badge-status.pending {
    background-color: #fff8e1;
    border: 1px solid #ffe082;
    color: #f57f17;
}

.badge-status.lunas {
    background-color: #e8f5e9;
    border: 1px solid #a5d6a7;
    color: #00796b;
}

.badge-status .icon-wrapper {
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    color: white;
}

.badge-status.pending .icon-wrapper {
    background-color: #fbc02d;
}

.badge-status.lunas .icon-wrapper {
    background-color: #00897b;
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
    <a href="alamat.php"><i class="fa fa-map-marker-alt"></i> <span>Alamat</span></a>
    <a href="pesanan_saya.php"><i class="fa fa-shopping-cart"></i> <span>Pesanan Saya</span></a>
    <a href="pembayaran.php" class="active"><i class="fa fa-credit-card"></i> <span>Pembayaran</span></a>
    <a href="../logout.php" class="mt-auto mb-2"><i class="fa fa-sign-out-alt"></i> <span>Logout</span></a>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">

    <!-- TOPBAR -->
    <div class="topbar">
        <h5><i class="fa fa-credit-card me-2 text-primary"></i> Pembayaran Saya</h5>
        <div class="user-profile">
            <i class="fa fa-user-circle"></i> <?= htmlspecialchars($_SESSION['username'] ?? 'User') ?>
        </div>
    </div>

    <div class="row">
        <!-- FORM TAMBAH -->
        <div class="col-lg-4 mb-4">
            <div class="card-box">
                <h5 class="card-title"><i class="fa fa-plus-circle text-success"></i> Konfirmasi Pembayaran</h5>
                
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted ms-1">Pilih Pesanan</label>
                        <select name="pesanan_id" class="form-select" required>
                            <option value="">-- Pilih Pesanan --</option>
                            <?php while($p=mysqli_fetch_array($pesanan)){ ?>
                            <option value="<?= $p['id'] ?>">
                                #<?= $p['id'] ?> - Rp <?= number_format($p['total'], 0, ',', '.') ?>
                            </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted ms-1">Metode Pembayaran</label>
                        <select name="metode" class="form-select" required>
                            <option value="">-- Pilih Metode --</option>
                            <option value="Transfer Bank">Transfer Bank</option>
                            <option value="E-Wallet">E-Wallet (OVO/GoPay/Dana)</option>
                            <option value="COD">Bayar di Tempat (COD)</option>
                        </select>
                    </div>
                    
                    <button name="tambah" class="btn btn-success-custom w-100">
                        <i class="fa fa-paper-plane me-1"></i> Kirim Konfirmasi
                    </button>
                </form>
            </div>
        </div>

        <!-- DAFTAR PEMBAYARAN -->
        <div class="col-lg-8">
            <div class="card-box">
                <h5 class="card-title mb-4"><i class="fa fa-history text-info"></i> Riwayat Pembayaran</h5>
                
                <div class="table-responsive">
                    <table class="table table-borderless align-middle">
                        <thead>
                            <tr>
                                <th>Pesanan</th>
                                <th>Metode</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($data) > 0){ ?>
                                <?php while($d=mysqli_fetch_array($data)){ ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="bg-light p-2 rounded-circle text-primary d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                                <i class="fa fa-receipt"></i>
                                            </div>
                                            <strong>#<?= htmlspecialchars($d['pesanan_id']) ?></strong>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-dark fw-medium">
                                            <i class="fa fa-wallet me-1 text-muted"></i> <?= htmlspecialchars($d['metode']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.9rem;">
                                            Rp <?= number_format($d['total'], 0, ',', '.') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if($d['status']=="pending"){ ?>
                                            <div class="badge-status pending">
                                                <div class="icon-wrapper"><i class="fa fa-clock"></i></div>
                                                <span>Pending</span>
                                            </div>
                                        <?php } else { ?>
                                            <div class="badge-status lunas">
                                                <div class="icon-wrapper"><i class="fa fa-check"></i></div>
                                                <span>Lunas</span>
                                            </div>
                                        <?php } ?>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-action shadow-sm" onclick="konfirmasiHapus('?hapus=<?= $d['id'] ?>')" title="Hapus Data">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="fa fa-credit-card fa-3x mb-3 text-light"></i><br>
                                        Belum ada riwayat pembayaran.
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
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
        title: 'Hapus Konfirmasi?',
        text: "Data pembayaran ini akan dihapus permanen!",
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
        Swal.fire({ title: 'Berhasil!', text: 'Konfirmasi pembayaran terkirim.', icon: 'success', confirmButtonClass: 'btn btn-primary rounded-pill px-4', buttonsStyling: false });
    <?php }elseif($_GET['pesan'] == 'sukses_hapus'){ ?>
        Swal.fire({ title: 'Berhasil!', text: 'Data pembayaran dihapus.', icon: 'success', confirmButtonClass: 'btn btn-primary rounded-pill px-4', buttonsStyling: false });
    <?php } ?>
</script>
<?php } ?>

</body>
</html>