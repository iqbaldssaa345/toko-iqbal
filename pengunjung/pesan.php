<?php
session_start();
include '../koneksi.php';

if(!isset($_SESSION['id']) || $_SESSION['role']!="pengunjung"){
    header("location:../login.php");
    exit;
}

$user_id = $_SESSION['id'];
$q_pengunjung = mysqli_query($conn, "SELECT username FROM users WHERE id='$user_id'");
$d_pengunjung = mysqli_fetch_assoc($q_pengunjung);
$nama_pengunjung = $d_pengunjung['username'];

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    $first_prod = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM produk ORDER BY id ASC LIMIT 1"));
    $id = $first_prod ? intval($first_prod['id']) : 0;
}

/* PRODUK */
$produk = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM produk WHERE id='$id'"));
if(!$produk){
    die("Produk tidak ditemukan. Silakan tambahkan produk terlebih dahulu di panel admin.");
}

/* ALAMAT */
$alamat = mysqli_query($conn,"SELECT * FROM alamat WHERE user_id='$user_id'");

/* ONGKIR */
$ongkir = mysqli_query($conn,"SELECT * FROM ongkir");

/* AMBIL TOTAL TERJUAL UNTUK RATING DYNAMIC */
$q_terjual = mysqli_query($conn, "SELECT SUM(dp.jumlah) as total_terjual FROM detail_pesanan dp JOIN pesanan p ON dp.pesanan_id = p.id WHERE dp.produk_id='$id'");
$d_terjual = mysqli_fetch_assoc($q_terjual);
$terjual = intval($d_terjual['total_terjual']);

/* =========================
   TAMBAH / UPDATE
   ========================= */
if(isset($_POST['simpan']) || isset($_POST['action'])){
    $jumlah = intval($_POST['jumlah']);
    $alamat_id = intval($_POST['alamat_id']);
    $ongkir_id = intval($_POST['ongkir_id']);
    $edit_id = intval($_POST['edit_id']);
    $action_type = isset($_POST['action']) ? $_POST['action'] : 'simpan';

    if($jumlah <= 0){
        echo "<script>alert('Jumlah tidak valid');</script>";
    } else {
        $cek_ongkir = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM ongkir WHERE id='$ongkir_id'"));
        $biaya = $cek_ongkir['biaya'];

        $total = ($produk['harga'] * $jumlah) + $biaya;

        /* UPDATE */
        if($edit_id > 0){
            $cek_dp = mysqli_query($conn,"SELECT * FROM detail_pesanan WHERE pesanan_id='$edit_id' AND produk_id='$id'");
            
            mysqli_query($conn,"UPDATE pesanan SET
                alamat_id='$alamat_id',
                ongkir_id='$ongkir_id',
                total='$total'
                WHERE id='$edit_id' AND user_id='$user_id'
            ");
            
            $subtotal = $produk['harga'] * $jumlah;
            if(mysqli_num_rows($cek_dp) > 0){
                mysqli_query($conn,"UPDATE detail_pesanan SET jumlah='$jumlah', subtotal='$subtotal' WHERE pesanan_id='$edit_id' AND produk_id='$id'");
            } else {
                mysqli_query($conn,"INSERT INTO detail_pesanan (pesanan_id, produk_id, jumlah, subtotal) VALUES ('$edit_id', '$id', '$jumlah', '$subtotal')");
            }
            
            if($action_type == 'beli'){
                header("location:pembayaran.php?pesanan_id=$edit_id&pesan=sukses_edit");
            } else {
                header("location:pesan.php?id=$id&pesan=sukses_edit");
            }
            exit;
        } 
        /* INSERT */
        else {
            mysqli_query($conn,"INSERT INTO pesanan
            (user_id, alamat_id, ongkir_id, total)
            VALUES
            ('$user_id','$alamat_id','$ongkir_id','$total')");
            $pesanan_id = mysqli_insert_id($conn);
            
            $subtotal = $produk['harga'] * $jumlah;
            mysqli_query($conn,"INSERT INTO detail_pesanan (pesanan_id, produk_id, jumlah, subtotal) VALUES ('$pesanan_id', '$id', '$jumlah', '$subtotal')");

            if($action_type == 'beli'){
                header("location:pembayaran.php?pesanan_id=$pesanan_id&pesan=sukses_tambah");
            } else {
                header("location:pesan.php?id=$id&pesan=sukses_tambah");
            }
            exit;
        }
    }
}

/* =========================
   HAPUS
   ========================= */
if(isset($_GET['hapus'])){
    $hapus = intval($_GET['hapus']);
    
    mysqli_query($conn,"DELETE FROM detail_pesanan WHERE pesanan_id='$hapus'");
    mysqli_query($conn,"DELETE FROM pembayaran WHERE pesanan_id='$hapus'");
    mysqli_query($conn,"DELETE FROM pesanan WHERE id='$hapus' AND user_id='$user_id'");
    header("location:pesan.php?id=$id&pesan=sukses_hapus");
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
   DATA LIST (RIWAYAT)
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
    <title>Pesan Menu - Catering Ibu Iqbal</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/dashboard.css" rel="stylesheet">
    
    <style>
        /* Modern Premium Shopee-Style Styling */
        .product-detail-container {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.03);
            border: 1px solid rgba(0,0,0,0.02);
            overflow: hidden;
            margin-bottom: 30px;
        }
        
        .product-gallery-side {
            padding: 35px;
            background: #fafafa;
            border-right: 1px solid rgba(0,0,0,0.03);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 450px;
        }

        .product-img-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
            width: 100%;
            max-width: 350px;
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(0,0,0,0.02);
            position: relative;
            overflow: hidden;
        }

        .product-img-card img {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
            border-radius: 12px;
            transition: all 0.5s ease;
        }
        
        .product-img-card:hover img {
            transform: scale(1.06);
        }

        .product-desc-box {
            width: 100%;
            max-width: 350px;
            text-align: left;
        }

        .product-desc-box h6 {
            font-weight: 700;
            color: var(--dark);
            border-left: 3px solid var(--gold);
            padding-left: 10px;
            margin-bottom: 12px;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .product-desc-box p {
            font-size: 0.9rem;
            color: #6c757d;
            line-height: 1.5;
            margin-bottom: 0;
        }

        .product-info-side {
            padding: 35px;
        }

        .shopee-title {
            font-size: 1.45rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 12px;
            line-height: 1.3;
        }

        /* Rating & Terjual Row */
        .shopee-rating-row {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            color: #757575;
            flex-wrap: wrap;
        }

        .shopee-rating-stars {
            color: #ffac0a;
            display: flex;
            align-items: center;
            gap: 2px;
            border-right: 1px solid rgba(0, 0, 0, 0.1);
            padding-right: 15px;
        }
        
        .shopee-rating-stars span {
            color: var(--dark);
            margin-left: 5px;
            font-weight: 600;
        }

        .shopee-penilaian-count {
            border-right: 1px solid rgba(0, 0, 0, 0.1);
            padding-right: 15px;
            color: var(--dark);
            font-weight: 600;
            text-decoration: underline;
        }
        
        .shopee-penilaian-count span {
            color: #757575;
            font-weight: 400;
            text-decoration: none;
            display: inline-block;
        }

        .shopee-terjual-count {
            color: var(--dark);
            font-weight: 600;
        }

        .shopee-terjual-count span {
            color: #757575;
            font-weight: 400;
        }

        /* Shopee Price Box */
        .shopee-price-box {
            background: #fafafa;
            padding: 18px 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            border: 1px solid rgba(0, 0, 0, 0.01);
        }

        .shopee-price-big {
            font-size: 2.1rem;
            font-weight: 700;
            color: #ee4d2d; /* Authentic Shopee Orange-Red */
            margin-bottom: 0;
            font-family: 'Poppins', sans-serif;
        }

        /* Specs Table Style Details */
        .shopee-spec-group {
            display: grid;
            grid-template-columns: 130px 1fr;
            row-gap: 20px;
            align-items: start;
            margin-bottom: 25px;
            font-size: 0.95rem;
        }

        .shopee-spec-label {
            color: #757575;
            font-weight: 500;
        }

        .shopee-spec-value {
            color: var(--dark);
            font-weight: 500;
        }

        .shopee-cicilan-link {
            color: #0056b3;
            text-decoration: none;
            font-weight: 500;
            margin-left: 10px;
        }

        .shopee-cicilan-link:hover {
            text-decoration: underline;
        }

        .shopee-shipping-details {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .shopee-shipping-main {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
        }
        
        .shopee-shipping-main i {
            color: #00bfa5; /* Teal delivery color */
            font-size: 1.1rem;
        }

        .shopee-shipping-sub {
            color: #757575;
            font-size: 0.85rem;
            margin-left: 24px;
        }

        .shopee-guarantee {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #d0011b; /* Shopee Guarantee Red */
            font-weight: 600;
            font-size: 0.9rem;
        }

        /* Quantity Selector */
        .shopee-quantity-control {
            display: flex;
            align-items: center;
            border: 1px solid rgba(0, 0, 0, 0.12);
            border-radius: 6px;
            overflow: hidden;
            width: fit-content;
        }

        .shopee-quantity-btn {
            background: #ffffff;
            border: none;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #555;
            cursor: pointer;
            transition: background 0.2s;
        }

        .shopee-quantity-btn:hover {
            background: #f5f5f5;
        }

        .shopee-quantity-input {
            width: 50px;
            height: 36px;
            border: none;
            border-left: 1px solid rgba(0, 0, 0, 0.12);
            border-right: 1px solid rgba(0, 0, 0, 0.12);
            text-align: center;
            font-weight: 600;
            color: var(--dark);
            outline: none;
            -moz-appearance: textfield;
        }
        
        .shopee-quantity-input::-webkit-outer-spin-button,
        .shopee-quantity-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .shopee-stock-info {
            color: #757575;
            font-size: 0.9rem;
            margin-left: 15px;
            display: inline-block;
            vertical-align: middle;
        }

        /* Select Styling */
        .shopee-select-box {
            border: 1.5px solid rgba(0, 0, 0, 0.08);
            border-radius: 12px;
            padding: 10px 15px;
            background-color: #fafafa;
            transition: all 0.3s;
            cursor: pointer;
            width: 100%;
        }

        .shopee-select-box:focus {
            border-color: #ee4d2d;
            background-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(238, 77, 45, 0.1);
        }

        /* Shopee Action Buttons */
        .shopee-btn-container {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        .btn-shopee-cart {
            background: rgba(238, 77, 45, 0.08);
            border: 1px solid #ee4d2d;
            color: #ee4d2d;
            border-radius: 6px;
            padding: 14px 24px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-size: 0.95rem;
            transition: all 0.3s;
            flex-grow: 1;
            box-shadow: 0 2px 4px rgba(238, 77, 45, 0.04);
        }

        .btn-shopee-cart:hover {
            background: rgba(238, 77, 45, 0.14);
            transform: translateY(-1px);
        }

        .btn-shopee-buy {
            background: #ee4d2d;
            border: 1px solid #ee4d2d;
            color: #ffffff;
            border-radius: 6px;
            padding: 14px 30px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-size: 0.95rem;
            transition: all 0.3s;
            flex-grow: 1.5;
            box-shadow: 0 4px 12px rgba(238, 77, 45, 0.2);
        }

        .btn-shopee-buy:hover {
            background: #d0011b;
            border-color: #d0011b;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(238, 77, 45, 0.3);
        }
        
        .btn-shopee-edit {
            background: #0080ff;
            border: 1px solid #0080ff;
            color: #ffffff;
            border-radius: 6px;
            padding: 14px 30px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-size: 0.95rem;
            transition: all 0.3s;
            width: 100%;
            box-shadow: 0 4px 12px rgba(0, 128, 255, 0.2);
        }

        .btn-shopee-edit:hover {
            background: #0066cc;
            border-color: #0066cc;
            transform: translateY(-1px);
        }
    </style>
</head>

<body>

    <!-- SIDEBAR -->
    <?php include '../includes/sidebar_pengunjung.php'; ?>

    <!-- MAIN CONTENT -->
    <div class="main-content">

        <!-- TOPBAR -->
        <?php 
        $topbar_title = "Detail Menu & Pemesanan";
        $user_name = $nama_pengunjung;
        $topbar_icon = '<i class="fa fa-shopping-bag text-primary"></i>';
        include '../includes/topbar.php';
        ?>

        <!-- PREMIUM DETAIL CONTAINER -->
        <div class="product-detail-container">
            <div class="row g-0">
                
                <!-- Left Side: Product Gallery & Description -->
                <div class="col-lg-5 col-md-6">
                    <div class="product-gallery-side">
                        <div class="product-img-card">
                            <?php if(!empty($produk['gambar']) && file_exists("../upload/".$produk['gambar'])){ ?>
                                <img src="../upload/<?= htmlspecialchars($produk['gambar']) ?>" alt="<?= htmlspecialchars($produk['nama']) ?>">
                            <?php } else { ?>
                                <i class="fa fa-utensils fa-5x text-muted"></i>
                            <?php } ?>
                        </div>
                        
                        <div class="product-desc-box">
                            <h6><i class="fa fa-info-circle text-warning me-1"></i> Deskripsi Menu</h6>
                            <p><?= nl2br(htmlspecialchars($produk['deskripsi'])) ?></p>
                        </div>
                    </div>
                </div>

                <!-- Right Side: Order details in Shopee-Style -->
                <div class="col-lg-7 col-md-6">
                    <div class="product-info-side">
                        
                        <h3 class="shopee-title"><?= htmlspecialchars($produk['nama']) ?></h3>
                        
                        <!-- Rating & Sales Info -->
                        <div class="shopee-rating-row">
                            <div class="shopee-rating-stars">
                                <span>4.8</span>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star-half-alt"></i>
                            </div>
                            
                            <div class="shopee-penilaian-count">
                                <?= max(1, round($terjual / 2) + 2); ?> <span>Penilaian</span>
                            </div>
                            
                            <div class="shopee-terjual-count">
                                <?= $terjual; ?> <span>Terjual</span>
                            </div>
                        </div>

                        <!-- Price Box -->
                        <div class="shopee-price-box">
                            <h2 class="shopee-price-big">Rp <?= number_format($produk['harga'], 0, ',', '.'); ?></h2>
                        </div>

                        <form method="POST">
                            <input type="hidden" name="edit_id" value="<?= $edit_data ? $edit_data['id'] : 0 ?>">

                            <!-- Spec / Checkout Options Group -->
                            <div class="shopee-spec-group">


                                <!-- Shipping -->
                                <div class="shopee-spec-label">Pengiriman</div>
                                <div class="shopee-spec-value shopee-shipping-details">
                                    <div class="shopee-shipping-main">
                                        <i class="fa fa-truck-fast"></i> 
                                        <span>Estimasi Tiba: <?= date('d M', strtotime('+2 days')); ?> (1-2 Hari)</span>
                                        <i class="fa fa-chevron-right small text-muted"></i>
                                    </div>
                                    <div class="shopee-shipping-sub">
                                        Pengiriman cepat dan terjamin ke alamat tujuan Anda.
                                    </div>
                                </div>

                                <!-- Jaminan -->
                                <div class="shopee-spec-label">Jaminan</div>
                                <div class="shopee-spec-value">
                                    <span class="shopee-guarantee">
                                        <i class="fa fa-shield-halved"></i> Bebas Pengembalian
                                    </span>
                                </div>

                                <!-- Alamat Selector -->
                                <div class="shopee-spec-label mt-2">Alamat Kirim</div>
                                <div class="shopee-spec-value">
                                    <select name="alamat_id" class="shopee-select-box" required>
                                        <option value="">-- Pilih Alamat Pengiriman --</option>
                                        <?php mysqli_data_seek($alamat, 0); while($a = mysqli_fetch_array($alamat)){ ?>
                                        <option value="<?= $a['id'] ?>" <?= ($edit_data && $edit_data['alamat_id']==$a['id'])?'selected':'' ?>>
                                            <?= htmlspecialchars($a['nama_penerima']) ?> - <?= htmlspecialchars($a['kota']) ?> (<?= htmlspecialchars($a['alamat']) ?>)
                                        </option>
                                        <?php } ?>
                                    </select>
                                    <div class="mt-2 text-start">
                                        <a href="alamat.php" class="text-decoration-none small text-warning"><i class="fa fa-plus-circle"></i> Tambah Alamat Baru</a>
                                    </div>
                                </div>

                                <!-- Ongkir Selector -->
                                <div class="shopee-spec-label mt-2">Metode Kirim</div>
                                <div class="shopee-spec-value">
                                    <select name="ongkir_id" class="shopee-select-box" required>
                                        <option value="">-- Pilih Jasa Kurir --</option>
                                        <?php mysqli_data_seek($ongkir, 0); while($o = mysqli_fetch_array($ongkir)){ 
                                            $o_est = isset($o['estimasi']) && !empty($o['estimasi']) ? $o['estimasi'] : '1-2 Hari';
                                        ?>
                                        <option value="<?= $o['id'] ?>" <?= ($edit_data && $edit_data['ongkir_id']==$o['id'])?'selected':'' ?>>
                                            <?= htmlspecialchars($o['nama_jasa']) ?> - Rp <?= number_format($o['biaya'], 0, ',', '.') ?> (Estimasi: <?= htmlspecialchars($o_est) ?>)
                                        </option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <!-- Quantity Selector -->
                                <div class="shopee-spec-label mt-2">Kuantitas</div>
                                <div class="shopee-spec-value d-flex align-items-center">
                                    <div class="shopee-quantity-control">
                                        <button type="button" class="shopee-quantity-btn" onclick="decreaseQty()"><i class="fa fa-minus"></i></button>
                                         <input type="number" id="qty-input" name="jumlah" class="shopee-quantity-input" 
                                                value="<?= $edit_data && isset($edit_data['jumlah']) ? $edit_data['jumlah'] : (isset($_GET['jumlah']) ? intval($_GET['jumlah']) : 1) ?>" 
                                                min="1" readonly required>
                                         <button type="button" class="shopee-quantity-btn" onclick="increaseQty()"><i class="fa fa-plus"></i></button>
                                     </div>
                                     <div class="shopee-stock-info">
                                         <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle py-2 px-3 rounded-pill">
                                             <i class="fa fa-clock me-1"></i> Pre-Order: <?= htmlspecialchars($produk['pre_order']) ?>
                                         </span>
                                     </div>
                                 </div>

                            </div>

                            <!-- Buttons action -->
                            <div class="shopee-btn-container">
                                <?php if($edit_data){ ?>
                                    <button type="submit" name="action" value="beli" class="btn-shopee-edit">
                                        <i class="fa fa-save"></i> SIMPAN PERUBAHAN PESANAN
                                    </button>
                                <?php } else { ?>
                                    <button type="submit" name="action" value="keranjang" class="btn-shopee-cart">
                                        <i class="fa fa-cart-plus"></i> Masukkan Keranjang
                                    </button>
                                    <button type="submit" name="action" value="beli" class="btn-shopee-buy">
                                        Beli Sekarang
                                    </button>
                                <?php } ?>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>

        <!-- RIWAYAT PESANAN PRODUK INI -->
        <div class="card-premium">
            <h5 class="card-title-premium mb-4"><i class="fa fa-history text-warning"></i> Riwayat Pembelian Menu Ini</h5>
            
            <div class="table-responsive">
                <table class="table-premium">
                    <thead>
                        <tr>
                            <th>ID Pesanan</th>
                            <th>Porsi</th>
                            <th>Penerima / Kota</th>
                            <th>Jasa Kirim</th>
                            <th>Total Bayar</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($data) > 0){ ?>
                            <?php while($d=mysqli_fetch_array($data)){ ?>
                            <tr>
                                <td>
                                    <span class="fw-bold">#<?= $d['id'] ?></span>
                                    <div class="small text-muted"><?= date('d-m-Y H:i', strtotime($d['tanggal'])) ?></div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary rounded-pill px-3 py-2"><?= $d['jumlah'] ? $d['jumlah'] : '1' ?> Porsi</span>
                                </td>
                                <td>
                                    <div class="fw-medium"><?= htmlspecialchars($d['nama_penerima'] ? $d['nama_penerima'] : '-') ?></div>
                                    <div class="small text-muted"><i class="fa fa-map-marker-alt text-danger me-1"></i> <?= htmlspecialchars($d['kota'] ? $d['kota'] : '-') ?></div>
                                </td>
                                <td>
                                    <div class="fw-medium text-info"><i class="fa fa-truck me-1"></i> <?= htmlspecialchars($d['nama_jasa'] ? $d['nama_jasa'] : '-') ?></div>
                                    <div class="small text-muted">Biaya: Rp <?= number_format($d['biaya'], 0, ',', '.') ?></div>
                                </td>
                                <td>
                                    <span class="price-badge">
                                        Rp <?= number_format($d['total'], 0, ',', '.') ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="?id=<?= $id ?>&edit=<?= $d['id'] ?>" class="btn-action-premium btn-edit-premium me-1" title="Edit Rincian">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn-action-premium btn-delete-premium" onclick="konfirmasiHapus('?id=<?= $id ?>&hapus=<?= $d['id'] ?>')" title="Batalkan">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fa fa-inbox fa-3x mb-3 text-light"></i><br>
                                    Belum ada riwayat pesanan untuk menu ini.
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
    const qtyInput = document.getElementById('qty-input');
    
    function increaseQty() {
        let val = parseInt(qtyInput.value);
        if (isNaN(val)) val = 0;
        qtyInput.value = val + 1;
    }
    
    function decreaseQty() {
        let val = parseInt(qtyInput.value);
        if (isNaN(val)) val = 1;
        if (val > 1) {
            qtyInput.value = val - 1;
        }
    }

    function konfirmasiHapus(url) {
        Swal.fire({
            title: 'Batalkan Pesanan?',
            text: "Pesanan ini akan dihapus secara permanen!",
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
            Swal.fire({ 
                title: 'Pesanan Dibuat!', 
                text: 'Pesanan Anda telah dimasukkan ke daftar pembelian.', 
                icon: 'success',
                confirmButtonText: 'Buka Daftar Pesanan',
                showCancelButton: true,
                cancelButtonText: 'Tetap di Sini',
                customClass: {
                    confirmButton: 'btn btn-primary rounded-pill px-4 mx-2',
                    cancelButton: 'btn btn-outline-secondary rounded-pill px-4 mx-2'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'pesanan.php';
                }
            });
        <?php }elseif($_GET['pesan'] == 'sukses_edit'){ ?>
            Swal.fire({ 
                title: 'Berhasil Diperbarui!', 
                text: 'Pesanan berhasil disesuaikan.', 
                icon: 'success', 
                confirmButtonClass: 'btn btn-primary rounded-pill px-4', 
                buttonsStyling: false 
            });
        <?php }elseif($_GET['pesan'] == 'sukses_hapus'){ ?>
            Swal.fire({ 
                title: 'Berhasil Dibatalkan!', 
                text: 'Pesanan berhasil dihapus.', 
                icon: 'success', 
                confirmButtonClass: 'btn btn-primary rounded-pill px-4', 
                buttonsStyling: false 
            });
        <?php } ?>
    </script>
    <?php } ?>

</body>
</html>
