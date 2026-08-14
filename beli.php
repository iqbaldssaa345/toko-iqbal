<?php
session_start();
include 'koneksi.php';

// Jika pengguna sudah login, alihkan langsung ke pengunjung/pesan.php yang terautentikasi
if(isset($_SESSION['login']) && $_SESSION['login'] === true && isset($_SESSION['role']) && $_SESSION['role'] === 'pengunjung') {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    header("Location: pengunjung/pesan.php?id=" . $id);
    exit;
}

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

/* AMBIL TOTAL TERJUAL UNTUK RATING DYNAMIC */
$q_terjual = mysqli_query($conn, "SELECT SUM(dp.jumlah) as total_terjual FROM detail_pesanan dp JOIN pesanan p ON dp.pesanan_id = p.id WHERE dp.produk_id='$id'");
$d_terjual = mysqli_fetch_assoc($q_terjual);
$terjual = intval($d_terjual['total_terjual']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan <?= htmlspecialchars($produk['nama']); ?> - Catering Ibu Iqbal</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --gold: #D4AF37;
            --gold-dark: #AA8C2C;
            --gold-light: rgba(212, 175, 55, 0.12);
            --gold-glow: rgba(212, 175, 55, 0.35);
            --dark: #0F0F11;
            --light: #F6F5F2;
            --white: #FFFFFF;
            --transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--light);
            color: var(--dark);
            min-height: 100vh;
        }

        h1, h2, h3, h4, .brand-name {
            font-family: 'Playfair Display', serif;
        }

        /* NAVBAR */
        .navbar {
            background: var(--dark) !important;
            padding: 20px 8%;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            border-bottom: 1px solid rgba(212, 175, 55, 0.15);
            transition: var(--transition);
        }

        .brand-name {
            font-size: 26px;
            font-weight: 700;
            color: var(--gold) !important;
            text-decoration: none;
            text-shadow: 0 0 10px rgba(212, 175, 55, 0.2);
        }

        .nav-links a {
            color: var(--white);
            text-decoration: none;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            transition: var(--transition);
            margin-left: 20px;
        }

        .nav-links a:hover {
            color: var(--gold);
        }

        .btn-gold {
            background: transparent;
            border: 1px solid var(--gold);
            color: var(--gold) !important;
            padding: 8px 22px;
            border-radius: 30px;
            transition: var(--transition) !important;
            font-weight: 500;
        }

        .btn-gold:hover {
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
            color: var(--dark) !important;
            box-shadow: 0 5px 15px rgba(212, 175, 55, 0.3);
            border-color: transparent;
        }

        /* CONTAINER LAYOUT */
        .page-container {
            max-width: 1100px;
            margin: 50px auto;
            padding: 0 20px;
        }

        .product-detail-container {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.03);
            border: 1px solid rgba(0,0,0,0.02);
            overflow: hidden;
            transition: var(--transition);
        }
        
        .product-detail-container:hover {
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.06);
        }
        
        .product-gallery-side {
            padding: 40px;
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
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.04);
            margin-bottom: 25px;
            width: 100%;
            max-width: 320px;
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(0,0,0,0.02);
            transition: var(--transition);
        }

        .product-img-card:hover {
            transform: scale(1.03);
        }

        .product-img-card img {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
            border-radius: 12px;
        }

        .product-desc-box {
            width: 100%;
            max-width: 320px;
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
        }

        .product-desc-box p {
            font-size: 0.9rem;
            color: #6c757d;
            line-height: 1.5;
        }

        .product-info-side {
            padding: 40px;
        }

        .shopee-title {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 12px;
        }

        /* Rating Row */
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
        }
        
        .shopee-penilaian-count span {
            color: #757575;
            font-weight: 400;
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
            color: var(--gold);
            margin-bottom: 0;
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
            color: #00bfa5;
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
            color: var(--gold-dark);
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
            transition: var(--transition);
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

        /* Guest Alert Box */
        .guest-checkout-alert {
            background: rgba(212, 175, 55, 0.08);
            border: 1px dashed var(--gold);
            padding: 16px 20px;
            border-radius: 12px;
            color: #8c721c;
            font-size: 0.9rem;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            line-height: 1.4;
        }

        .guest-checkout-alert a {
            font-weight: 700;
            color: var(--gold-dark);
            text-decoration: underline;
        }

        /* Shopee Action Buttons */
        .shopee-btn-container {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        .btn-shopee-cart {
            background: rgba(212, 175, 55, 0.08);
            border: 1px solid var(--gold);
            color: var(--gold);
            border-radius: 30px;
            padding: 14px 24px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-size: 0.95rem;
            transition: var(--transition);
            flex-grow: 1;
            text-decoration: none;
            box-shadow: 0 2px 8px rgba(212, 175, 55, 0.05);
        }

        .btn-shopee-cart:hover {
            background: rgba(212, 175, 55, 0.15);
            transform: translateY(-2px);
            color: var(--gold);
        }

        .btn-shopee-buy {
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
            border: none;
            color: var(--dark);
            border-radius: 30px;
            padding: 14px 30px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-size: 0.95rem;
            transition: var(--transition);
            flex-grow: 1.5;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.25);
        }

        .btn-shopee-buy:hover {
            background: linear-gradient(135deg, #ffffff 0%, #f1e2c3 100%);
            transform: translateY(-2px);
            color: var(--dark);
            box-shadow: 0 8px 25px rgba(212, 175, 55, 0.35);
        }

        /* PREMIUM FOOTER STYLE */
        .premium-footer {
            background: #0F0F11;
            color: #a0a0a5;
            padding: 70px 0 35px 0;
            border-top: 1px solid rgba(212, 175, 55, 0.25);
            margin-top: 80px;
        }

        .footer-container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1.2fr;
            gap: 40px;
            margin-bottom: 50px;
        }

        .footer-col h4 {
            font-family: 'Playfair Display', serif;
            color: var(--gold);
            font-weight: 700;
            font-size: 1.6rem;
            margin-bottom: 6px;
        }

        .footer-col h5 {
            font-family: 'Playfair Display', serif;
            color: #ffffff;
            font-weight: 600;
            font-size: 1.25rem;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 10px;
        }

        .footer-col h5::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 2px;
            background-color: var(--gold);
        }

        .footer-col .tagline {
            color: #ffffff;
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 15px;
        }

        .footer-col .bio {
            font-size: 0.9rem;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .footer-socials {
            display: flex;
            gap: 12px;
        }

        .footer-socials a {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(212, 175, 55, 0.15);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            text-decoration: none;
            transition: var(--transition);
        }

        .footer-socials a:hover {
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
            color: var(--dark);
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(212, 175, 55, 0.35);
            border-color: transparent;
        }

        .footer-links, .footer-contact {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 12px;
            font-size: 0.9rem;
        }

        .footer-links a {
            color: #a0a0a5;
            text-decoration: none;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .footer-links a i {
            font-size: 0.75rem;
            transition: transform 0.3s ease;
        }

        .footer-links a:hover {
            color: var(--gold);
            padding-left: 5px;
        }

        .footer-links a:hover i {
            transform: translateX(3px);
        }

        .footer-contact li {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            line-height: 1.5;
        }

        .footer-contact li i {
            margin-top: 4px;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            padding-top: 25px;
            text-align: center;
            font-size: 0.85rem;
        }

        @media (max-width: 768px) {
            .footer-grid {
                grid-template-columns: 1fr;
                gap: 30px;
            }
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid px-5">
            <a href="index.php" class="brand-name">Catering Ibu Iqbal</a>
            <div class="nav-links ms-auto">
                <a href="index.php">Beranda</a>
                <a href="login.php" class="btn-gold">Login</a>
            </div>
        </div>
    </nav>

    <!-- PAGE CONTAINER -->
    <div class="page-container">
        
        <div class="product-detail-container">
            <div class="row g-0">
                
                <!-- Left Side: Product Gallery & Description -->
                <div class="col-lg-5 col-md-6">
                    <div class="product-gallery-side">
                        <div class="product-img-card">
                            <?php if(!empty($produk['gambar']) && file_exists("upload/".$produk['gambar'])){ ?>
                                <img src="upload/<?= htmlspecialchars($produk['gambar']) ?>" alt="<?= htmlspecialchars($produk['nama']) ?>">
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

                        <!-- Spec / Checkout Options Group -->
                        <div class="shopee-spec-group">


                            <!-- Shipping -->
                            <div class="shopee-spec-label">Pengiriman</div>
                            <div class="shopee-spec-value shopee-shipping-details">
                                <div class="shopee-shipping-main">
                                    <i class="fa fa-truck-fast"></i> 
                                    <span><?= date('d M', strtotime('+2 days')); ?></span>
                                    <i class="fa fa-chevron-right small text-muted"></i>
                                </div>
                                <div class="shopee-shipping-sub">
                                    Dapatkan Voucher s/d Rp10.000 jika pesanan terlambat.
                                </div>
                            </div>

                            <!-- Jaminan -->
                            <div class="shopee-spec-label">Jaminan</div>
                            <div class="shopee-spec-value">
                                <span class="shopee-guarantee">
                                    <i class="fa fa-shield-halved"></i> Bebas Pengembalian
                                </span>
                            </div>

                            <!-- Quantity Selector -->
                            <div class="shopee-spec-label mt-2">Kuantitas</div>
                            <div class="shopee-spec-value d-flex align-items-center">
                                <div class="shopee-quantity-control">
                                    <button type="button" class="shopee-quantity-btn" onclick="decreaseQty()"><i class="fa fa-minus"></i></button>
                                    <input type="number" id="qty-input" class="shopee-quantity-input" value="1" min="1" readonly>
                                    <button type="button" class="shopee-quantity-btn" onclick="increaseQty()"><i class="fa fa-plus"></i></button>
                                </div>
                                <div class="shopee-stock-info">
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle py-2 px-3 rounded-pill">
                                        <i class="fa fa-clock me-1"></i> Pre-Order: <?= htmlspecialchars($produk['pre_order']) ?>
                                    </span>
                                </div>
                            </div>

                        </div>

                        <!-- Guest checkout warning -->
                        <div class="guest-checkout-alert">
                            <i class="fa fa-circle-exclamation fa-lg"></i>
                            <div>
                                Anda belum masuk ke sistem. Silakan <a href="login.php?redirect=pengunjung/pesan.php?id=<?= $id; ?>">Login</a> terlebih dahulu untuk memilih alamat pengiriman dan memproses checkout.
                            </div>
                        </div>

                        <!-- Buttons action linked to login with redirection -->
                        <div class="shopee-btn-container">
                            <a href="login.php?redirect=pengunjung/pesan.php?id=<?= $id; ?>" class="btn-shopee-cart">
                                <i class="fa fa-cart-plus"></i> Masukkan Keranjang
                            </a>
                            <a href="login.php?redirect=pengunjung/pesan.php?id=<?= $id; ?>&action=beli" class="btn-shopee-buy">
                                Beli Sekarang
                            </a>
                        </div>

                    </div>
                </div>

    </div>

    <!-- PREMIUM FOOTER -->
    <footer class="premium-footer">
        <div class="footer-container">
            <div class="footer-grid">
                <!-- Col 1: Brand & Bio -->
                <div class="footer-col">
                    <h4>Catering Ibu Iqbal</h4>
                    <p class="tagline">Premium Taste & Quality Nusantara</p>
                    <p class="bio">Menyajikan hidangan khas Nusantara dengan bahan premium, standar kebersihan tertinggi, dan cita rasa autentik warisan keluarga.</p>
                    <div class="footer-socials">
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-whatsapp"></i></a>
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                    </div>
                </div>

                <!-- Col 2: Navigation -->
                <div class="footer-col">
                    <h5>Navigasi</h5>
                    <ul class="footer-links">
                        <li><a href="index.php"><i class="fa fa-chevron-right"></i> Beranda</a></li>
                        <li><a href="index.php#produk"><i class="fa fa-chevron-right"></i> Menu Pilihan</a></li>
                        <li><a href="login.php"><i class="fa fa-chevron-right"></i> Login Pelanggan</a></li>
                        <li><a href="daftar.php"><i class="fa fa-chevron-right"></i> Registrasi Akun</a></li>
                    </ul>
                </div>

                <!-- Col 3: Contact Info -->
                <div class="footer-col">
                    <h5>Kontak Kami</h5>
                    <ul class="footer-contact">
                        <li><i class="fa fa-map-marker-alt text-warning"></i> Jalan Puri Nirwana 1, Cibinong, Bogor</li>
                        <li><i class="fa fa-phone-alt text-warning"></i> +62 812 3456 7890</li>
                        <li><i class="fa fa-envelope text-warning"></i> info@ibuiqbalcatering.com</li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2026 Catering Ibu Iqbal. All Rights Reserved. Crafted with <i class="fa fa-heart text-danger"></i> for gourmet excellence.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    const qtyInput = document.getElementById('qty-input');
    const loginLinks = document.querySelectorAll('.btn-shopee-cart, .btn-shopee-buy');
    const baseRedirect = "login.php?redirect=pengunjung/pesan.php?id=<?= $id; ?>";

    function updateRedirectLinks(qty) {
        if (loginLinks.length >= 2) {
            loginLinks[0].href = baseRedirect + "&jumlah=" + qty;
            loginLinks[1].href = baseRedirect + "&jumlah=" + qty + "&action=beli";
        }
    }

    function increaseQty() {
        let val = parseInt(qtyInput.value);
        if (isNaN(val)) val = 0;
        qtyInput.value = val + 1;
        updateRedirectLinks(qtyInput.value);
    }
    
    function decreaseQty() {
        let val = parseInt(qtyInput.value);
        if (isNaN(val)) val = 1;
        if (val > 1) {
            qtyInput.value = val - 1;
            updateRedirectLinks(qtyInput.value);
        }
    }

    // Initialize links
    updateRedirectLinks(qtyInput.value);
    </script>
</body>
</html>
