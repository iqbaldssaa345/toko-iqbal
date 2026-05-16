<?php
session_start();
include '../koneksi.php';

if(!isset($_SESSION['id'])){
    die("Harus login");
}

$user_id = $_SESSION['id'];
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

/* PRODUK */
$produk = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM produk WHERE id='$id'"));
if(!$produk){
    die("Produk tidak ditemukan");
}

/* ALAMAT */
$alamat = mysqli_query($conn,"SELECT * FROM alamat WHERE user_id='$user_id'");

/* ONGKIR */
$ongkir = mysqli_query($conn,"SELECT * FROM ongkir");

/* =========================
   TAMBAH / UPDATE
========================= */
if(isset($_POST['simpan'])){
    $jumlah = intval($_POST['jumlah']);
    $alamat_id = intval($_POST['alamat_id']);
    $ongkir_id = intval($_POST['ongkir_id']);
    $edit_id = intval($_POST['edit_id']);

    if($jumlah <= 0){
        echo "<script>alert('Jumlah tidak valid');</script>";
    } else {

        $cek_ongkir = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM ongkir WHERE id='$ongkir_id'"));
        $biaya = $cek_ongkir['biaya'];

        $total = ($produk['harga'] * $jumlah) + $biaya;

        /* UPDATE */
        if($edit_id > 0){
            mysqli_query($conn,"UPDATE pesanan SET
                alamat_id='$alamat_id',
                ongkir_id='$ongkir_id',
                total='$total'
                WHERE id='$edit_id' AND user_id='$user_id'
            ");
            
            $subtotal = $produk['harga'] * $jumlah;
            $cek_dp = mysqli_query($conn,"SELECT * FROM detail_pesanan WHERE pesanan_id='$edit_id' AND produk_id='$id'");
            if(mysqli_num_rows($cek_dp) > 0){
                mysqli_query($conn,"UPDATE detail_pesanan SET jumlah='$jumlah', subtotal='$subtotal' WHERE pesanan_id='$edit_id' AND produk_id='$id'");
            } else {
                mysqli_query($conn,"INSERT INTO detail_pesanan (pesanan_id, produk_id, jumlah, subtotal) VALUES ('$edit_id', '$id', '$jumlah', '$subtotal')");
            }
            
            header("location:beli.php?id=$id&pesan=sukses_edit");
            exit;
        } 
        /* INSERT */
        else{
            mysqli_query($conn,"INSERT INTO pesanan
            (user_id, alamat_id, ongkir_id, total)
            VALUES
            ('$user_id','$alamat_id','$ongkir_id','$total')");
            $pesanan_id = mysqli_insert_id($conn);
            
            $subtotal = $produk['harga'] * $jumlah;
            mysqli_query($conn,"INSERT INTO detail_pesanan (pesanan_id, produk_id, jumlah, subtotal) VALUES ('$pesanan_id', '$id', '$jumlah', '$subtotal')");

            header("location:beli.php?id=$id&pesan=sukses_tambah");
            exit;
        }
    }
}

/* =========================
   HAPUS
========================= */
if(isset($_GET['hapus'])){
    $hapus = intval($_GET['hapus']);
    // hapus detail dan pembayaran agar tidak terjadi error foreign key
    mysqli_query($conn,"DELETE FROM detail_pesanan WHERE pesanan_id='$hapus'");
    mysqli_query($conn,"DELETE FROM pembayaran WHERE pesanan_id='$hapus'");
    mysqli_query($conn,"DELETE FROM pesanan WHERE id='$hapus' AND user_id='$user_id'");
    header("location:beli.php?id=$id&pesan=sukses_hapus");
    exit;
}

/* =========================
   AMBIL DATA EDIT
========================= */
$edit_data = null;
if(isset($_GET['edit'])){
    $edit_id = intval($_GET['edit']);
    $edit_data = mysqli_fetch_array(mysqli_query($conn,"
        SELECT p.*, dp.jumlah 
        FROM pesanan p
        LEFT JOIN detail_pesanan dp ON p.id = dp.pesanan_id
        WHERE p.id='$edit_id' AND p.user_id='$user_id'
    "));
}

/* =========================
   DATA LIST
========================= */
$data = mysqli_query($conn,"
SELECT p.*, a.nama_penerima, a.kota, o.nama_jasa, o.biaya, dp.jumlah, pr.nama as nama_produk 
FROM pesanan p
LEFT JOIN alamat a ON p.alamat_id = a.id
LEFT JOIN ongkir o ON p.ongkir_id = o.id
LEFT JOIN detail_pesanan dp ON p.id = dp.pesanan_id
LEFT JOIN produk pr ON dp.produk_id = pr.id
WHERE p.user_id='$user_id' AND dp.produk_id='$id'
ORDER BY p.id DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Beli Produk - Catering Ibu Iqbal</title>

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

/* PRODUCT PREVIEW */
.product-preview {
    background: #f8f9fa;
    border-radius: 15px;
    padding: 20px;
    text-align: center;
    margin-bottom: 25px;
    border: 1px solid #eef2f7;
}

.product-preview img {
    width: 150px;
    height: 150px;
    object-fit: cover;
    border-radius: 15px;
    margin-bottom: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.product-price {
    background: #eef2f7;
    color: #11998e;
    padding: 8px 20px;
    border-radius: 30px;
    display: inline-block;
    font-weight: 700;
    font-size: 1.2rem;
    margin-top: 10px;
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
    padding: 15px;
    font-weight: 600;
    font-size: 1.1rem;
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
    <a href="pembayaran.php"><i class="fa fa-credit-card"></i> <span>Pembayaran</span></a>
    <a href="../logout.php" class="mt-auto mb-2"><i class="fa fa-sign-out-alt"></i> <span>Logout</span></a>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">

    <!-- TOPBAR -->
    <div class="topbar">
        <h5><i class="fa fa-shopping-bag me-2 text-primary"></i> Proses Pemesanan</h5>
        <div class="user-profile">
            <i class="fa fa-user-circle"></i> <?= htmlspecialchars($_SESSION['username'] ?? 'User') ?>
        </div>
    </div>

    <div class="row">
        <!-- FORM PEMBELIAN -->
        <div class="col-lg-5 mb-4">
            <div class="card-box">
                <h5 class="card-title"><i class="fa fa-cart-plus text-success"></i> <?= $edit_data ? 'Update Pesanan' : 'Beli Sekarang' ?></h5>
                
                <div class="product-preview">
                    <img src="../upload/<?= htmlspecialchars($produk['gambar']) ?>" alt="Produk">
                    <h5 class="mb-0 fw-bold"><?= htmlspecialchars($produk['nama']) ?></h5>
                    <div class="product-price">Rp <?= number_format($produk['harga'], 0, ',', '.') ?> / porsi</div>
                </div>

                <form method="POST">
                    <input type="hidden" name="edit_id" value="<?= $edit_data ? $edit_data['id'] : 0 ?>">

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted ms-1">Jumlah Porsi</label>
                        <input type="number" name="jumlah" class="form-control"
                        value="<?= $edit_data && isset($edit_data['jumlah']) ? $edit_data['jumlah'] : '' ?>" placeholder="Masukkan Jumlah" required min="1">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted ms-1">Alamat Pengiriman</label>
                        <select name="alamat_id" class="form-select" required>
                            <option value="">-- Pilih Alamat --</option>
                            <?php mysqli_data_seek($alamat,0); while($a=mysqli_fetch_array($alamat)){ ?>
                            <option value="<?= $a['id'] ?>" <?= ($edit_data && $edit_data['alamat_id']==$a['id'])?'selected':'' ?>>
                                <?= htmlspecialchars($a['nama_penerima']) ?> - <?= htmlspecialchars($a['kota']) ?>
                            </option>
                            <?php } ?>
                        </select>
                        <div class="text-end mt-1">
                            <a href="alamat.php" class="text-decoration-none small"><i class="fa fa-plus"></i> Tambah Alamat Baru</a>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted ms-1">Metode Pengiriman (Ongkir)</label>
                        <select name="ongkir_id" class="form-select" required>
                            <option value="">-- Pilih Ongkir --</option>
                            <?php mysqli_data_seek($ongkir,0); while($o=mysqli_fetch_array($ongkir)){ ?>
                            <option value="<?= $o['id'] ?>" <?= ($edit_data && $edit_data['ongkir_id']==$o['id'])?'selected':'' ?>>
                                <?= htmlspecialchars($o['nama_jasa']) ?> - Rp <?= number_format($o['biaya'], 0, ',', '.') ?>
                            </option>
                            <?php } ?>
                        </select>
                    </div>

                    <button name="simpan" class="btn btn-success-custom w-100">
                        <i class="fa <?= $edit_data ? 'fa-save' : 'fa-check-circle' ?> me-2"></i> 
                        <?= $edit_data ? 'SIMPAN PERUBAHAN' : 'BUAT PESANAN' ?>
                    </button>
                </form>
            </div>
        </div>

        <!-- RIWAYAT PESANAN PRODUK INI -->
        <div class="col-lg-7">
            <div class="card-box">
                <h5 class="card-title mb-4"><i class="fa fa-list-alt text-info"></i> Pesanan Produk Ini</h5>
                
                <div class="table-responsive">
                    <table class="table table-borderless align-middle">
                        <thead>
                            <tr>
                                <th>Detail</th>
                                <th>Pengiriman</th>
                                <th>Total</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($data) > 0){ ?>
                                <?php while($d=mysqli_fetch_array($data)){ ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($d['nama_produk'] ? $d['nama_produk'] : '-') ?></div>
                                        <div class="small text-muted"><?= $d['jumlah'] ? $d['jumlah'] : '-' ?> Porsi</div>
                                    </td>
                                    <td>
                                        <div class="fw-medium"><i class="fa fa-map-marker-alt text-danger me-1"></i> <?= htmlspecialchars($d['kota']) ?></div>
                                        <div class="small text-muted"><i class="fa fa-truck text-info me-1"></i> <?= htmlspecialchars($d['nama_jasa']) ?></div>
                                    </td>
                                    <td>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill">
                                            Rp <?= number_format($d['total'], 0, ',', '.') ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="?id=<?= $id ?>&edit=<?= $d['id'] ?>" class="btn btn-warning btn-action text-white shadow-sm" title="Edit">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger btn-action shadow-sm" onclick="konfirmasiHapus('?id=<?= $id ?>&hapus=<?= $d['id'] ?>')" title="Batal">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="fa fa-inbox fa-2x mb-2 text-light"></i><br>
                                        Belum ada pesanan untuk produk ini.
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
        Swal.fire({ title: 'Berhasil!', text: 'Pesanan berhasil dibuat.', icon: 'success', confirmButtonClass: 'btn btn-primary rounded-pill px-4', buttonsStyling: false });
    <?php }elseif($_GET['pesan'] == 'sukses_edit'){ ?>
        Swal.fire({ title: 'Berhasil!', text: 'Pesanan berhasil diupdate.', icon: 'success', confirmButtonClass: 'btn btn-primary rounded-pill px-4', buttonsStyling: false });
    <?php }elseif($_GET['pesan'] == 'sukses_hapus'){ ?>
        Swal.fire({ title: 'Berhasil!', text: 'Pesanan berhasil dibatalkan.', icon: 'success', confirmButtonClass: 'btn btn-primary rounded-pill px-4', buttonsStyling: false });
    <?php } ?>
</script>
<?php } ?>

</body>
</html>