<?php 
session_start();
include 'koneksi.php'; 

// Check if user is logged in
$logged_in = false;
$username_session = "";
$dashboard_url = "";
if(isset($_SESSION['login']) && $_SESSION['login'] === true && isset($_SESSION['role'])) {
    $logged_in = true;
    $user_id = $_SESSION['id'];
    $q_user = mysqli_query($conn, "SELECT username FROM users WHERE id='$user_id'");
    if(mysqli_num_rows($q_user) > 0) {
        $d_user = mysqli_fetch_assoc($q_user);
        $username_session = $d_user['username'];
    }
    
    // Determine dashboard url based on role
    if($_SESSION['role'] == "admin") {
        $dashboard_url = "admin/index.php";
    } elseif($_SESSION['role'] == "petugas") {
        $dashboard_url = "petugas/index.php";
    } else {
        $dashboard_url = "pengunjung/index.php";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catering Ibu Iqbal | Premium Taste</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --gold: #D4AF37;
            --gold-dark: #AA8C2C;
            --dark: #1A1A1D;
            --light: #F9F6F0;
            --white: #FFFFFF;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--light);
            color: var(--dark);
            line-height: 1.6;
        }

        h1, h2, h3, h4, .brand-name, .price {
            font-family: 'Playfair Display', serif;
        }

        /* NAVBAR */
        .navbar {
            position: fixed;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 25px 8%;
            transition: all 0.4s ease;
            z-index: 1000;
            background: transparent;
        }

        .navbar.scrolled {
            background: rgba(26, 26, 29, 0.95);
            padding: 15px 8%;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
        }

        .brand-name {
            font-size: 28px;
            font-weight: 700;
            color: var(--white);
            text-decoration: none;
            letter-spacing: 1px;
        }

        .navbar.scrolled .brand-name {
            color: var(--gold);
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 30px;
        }

        .nav-links a {
            color: var(--white);
            text-decoration: none;
            font-size: 15px;
            font-weight: 400;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            transition: color 0.3s;
            position: relative;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -5px;
            left: 0;
            background-color: var(--gold);
            transition: width 0.3s;
        }

        .nav-links a:not(.btn-gold):not(.btn-gold-filled):hover::after {
            width: 100%;
        }

        .nav-links a:hover {
            color: var(--gold);
        }

        .btn-gold {
            background: transparent;
            border: 1px solid var(--gold);
            color: var(--gold) !important;
            padding: 10px 24px;
            border-radius: 0;
            transition: all 0.3s ease !important;
        }

        .btn-gold:hover {
            background: var(--gold);
            color: var(--white) !important;
        }

        .btn-gold-filled {
            background: var(--gold);
            border: 1px solid var(--gold);
            color: var(--white) !important;
            padding: 10px 24px;
            border-radius: 0;
            transition: all 0.3s ease !important;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-gold-filled:hover {
            background: transparent;
            color: var(--gold) !important;
        }

        /* HERO */
        .hero {
            height: 100vh;
            background: linear-gradient(rgba(26, 26, 29, 0.7), rgba(26, 26, 29, 0.8)), url('https://i.pinimg.com/736x/d1/1d/62/d11d62838e3facfa6e3c030fa975c6d0.jpg') center/cover fixed;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: var(--white);
        }

        .hero-content {
            max-width: 800px;
            padding: 0 20px;
        }

        .hero-subtitle {
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 4px;
            color: var(--gold);
            margin-bottom: 20px;
            display: block;
            animation: fadeInDown 1s ease;
        }

        .hero-title {
            font-size: 64px;
            font-weight: 700;
            margin-bottom: 30px;
            line-height: 1.2;
            animation: fadeInUp 1s ease 0.3s forwards;
            opacity: 0;
        }

        .hero-desc {
            font-size: 18px;
            font-weight: 300;
            margin-bottom: 40px;
            color: #DDDDDD;
            animation: fadeInUp 1s ease 0.6s forwards;
            opacity: 0;
        }

        .hero-btn {
            display: inline-block;
            padding: 15px 40px;
            background: var(--gold);
            color: var(--white);
            text-decoration: none;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 2px;
            border: 1px solid var(--gold);
            transition: all 0.4s;
            animation: fadeInUp 1s ease 0.9s forwards;
            opacity: 0;
        }

        .hero-btn:hover {
            background: transparent;
            color: var(--gold);
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* SECTION PADDING & TITLES */
        .section-padding {
            padding: 100px 8%;
        }

        .section-title {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-title h2 {
            font-size: 40px;
            color: var(--dark);
            margin-bottom: 15px;
        }

        .section-title .separator {
            width: 60px;
            height: 2px;
            background: var(--gold);
            margin: 0 auto;
        }

        /* ABOUT */
        .about-wrapper {
            display: flex;
            align-items: center;
            gap: 60px;
        }
        .about-text {
            flex: 1;
        }
        .about-text h3 {
            font-size: 28px;
            color: var(--dark);
            margin-bottom: 20px;
        }
        .about-text p {
            font-size: 16px;
            color: #555;
            margin-bottom: 20px;
            text-align: justify;
        }
        .about-image {
            flex: 1;
            position: relative;
        }
        .about-image img {
            width: 100%;
            border-radius: 5px;
            box-shadow: -20px 20px 0px rgba(212, 175, 55, 0.2);
        }

        /* DISCOVER */
        .discover {
            display: flex;
            flex-wrap: wrap;
        }

        .discover-item {
            flex: 1;
            min-height: 400px;
            min-width: 300px;
            background-size: cover !important;
            background-position: center !important;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            cursor: pointer;
        }

        .discover-item::before {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(26, 26, 29, 0.4);
            transition: all 0.5s;
        }

        .discover-item:hover::before {
            background: rgba(26, 26, 29, 0.1);
        }

        .discover-item h3 {
            position: relative;
            color: var(--white);
            font-size: 30px;
            font-weight: 600;
            letter-spacing: 2px;
            z-index: 2;
            transition: all 0.5s;
        }

        .discover-item:hover h3 {
            transform: scale(1.1);
        }

        /* PRODUK */
        .produk {
            background: var(--white);
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 40px;
        }

        .card {
            background: var(--light);
            border: 1px solid #EAEAEA;
            transition: all 0.4s;
            position: relative;
            overflow: hidden;
        }

        .card:hover {
            box-shadow: 0 15px 40px rgba(0,0,0,0.08);
            transform: translateY(-10px);
        }

        .card-img-wrapper {
            position: relative;
            width: 100%;
            height: 250px;
            overflow: hidden;
        }

        .card-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.6s;
        }

        .card:hover .card-img-wrapper img {
            transform: scale(1.08);
        }

        .card-body {
            padding: 30px 25px;
            text-align: center;
        }

        .card-body h3 {
            font-size: 22px;
            margin-bottom: 10px;
            color: var(--dark);
        }

        .card-body p {
            color: #666;
            font-size: 14px;
            margin-bottom: 20px;
            min-height: 42px;
        }

        .price {
            color: var(--gold) !important;
            font-size: 22px !important;
            font-weight: 600;
            margin-bottom: 25px !important;
        }

        .buy-btn {
            display: inline-block;
            padding: 10px 30px;
            background: transparent;
            color: var(--dark);
            border: 1px solid var(--dark);
            text-decoration: none;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 1px;
            transition: all 0.3s;
        }

        .buy-btn:hover {
            background: var(--gold);
            border-color: var(--gold);
            color: var(--white);
        }

        /* FOOTER */
        .footer {
            background: #111112;
            color: #a0a0a5;
            padding: 80px 8% 40px;
            border-top: 2px solid var(--gold);
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 60px;
        }

        .footer-col h4 {
            font-family: 'Playfair Display', serif;
            color: var(--gold);
            font-weight: 700;
            font-size: 1.6rem;
            margin-bottom: 20px;
        }

        .footer-col p {
            font-size: 0.9rem;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .footer-col p i {
            color: var(--gold);
            margin-right: 10px;
        }

        .social-links {
            display: flex;
            gap: 12px;
            margin-top: 20px;
        }

        .social-links a {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(212, 175, 55, 0.2);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .social-links a:hover {
            background: var(--gold);
            color: #111112;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(212, 175, 55, 0.3);
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            padding-top: 30px;
            text-align: center;
            font-size: 0.85rem;
        }

        /* RESPONSIVE */
        @media(max-width: 991px) {
            .navbar { padding: 20px 5%; }
            .navbar.scrolled { padding: 15px 5%; }
            .hero-title { font-size: 48px; }
            .about-wrapper { flex-direction: column; }
        }

        @media(max-width: 768px) {
            .nav-links { gap: 10px; }
            .nav-links a { font-size: 12px; }
            .brand-name { font-size: 24px; }
            .hero-title { font-size: 36px; }
            .btn-gold, .btn-gold-filled { padding: 8px 15px; }
            .discover-item { min-width: 100%; }
        }
        
        @media(max-width: 480px) {
            .nav-links { display: flex; flex-wrap: wrap; justify-content: center; }
            .navbar { flex-direction: column; gap: 15px; }
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar" id="navbar">
        <a href="#" class="brand-name"> Catering Ibu Iqbal</a>
        <div class="nav-links">
            <a href="#about">Tentang Kami</a>
            <a href="#produk">Menu</a>
            <?php if($logged_in){ ?>
                <a href="<?= $dashboard_url ?>" class="btn-gold-filled"><i class="fa fa-user"></i> <?= htmlspecialchars($username_session) ?></a>
            <?php } else { ?>
                <a href="login.php" class="btn-gold">Login</a>
                <a href="daftar.php" class="btn-gold-filled">Daftar</a>
            <?php } ?>
        </div>
    </nav>

    <!-- HERO -->
    <section class="hero">
        <div class="hero-content">
            <span class="hero-subtitle">Premium Catering Service</span>
            <h1 class="hero-title">Sajian Khas Nusantara<br>dengan Sentuhan Elegan</h1>
            <p class="hero-desc">Menghadirkan hidangan lezat, higienis, dan berkualitas tinggi untuk setiap momen istimewa Anda.</p>
            <a href="#produk" class="hero-btn">Lihat Menu Kami</a>
        </div>
    </section>

    <!-- ABOUT -->
    <section class="section-padding" id="about">
        <div class="section-title">
            <h2>Tentang Kami</h2>
            <div class="separator"></div>
        </div>
        <div class="about-wrapper">
            <div class="about-image">
                <img src="https://i.pinimg.com/736x/81/70/66/817066a3e0587d60592d31920f31f69a.jpg" alt="Tentang Kami">
            </div>
            <div class="about-text">
                <h3>Dedikasi pada Rasa & Kualitas</h3>
                <p>Catering Ibu Iqbal didirikan dengan satu tujuan: menyajikan makanan rumahan dengan standar restoran bintang lima. Kami percaya bahwa setiap hidangan harus menceritakan sebuah kisah rasa yang tak terlupakan.</p>
                <p>Menggunakan bahan-bahan segar pilihan dan resep rahasia keluarga yang telah diwariskan turun-temurun, kami siap menyempurnakan berbagai acara Anda, dari pertemuan bisnis hingga perayaan pernikahan.</p>
            </div>
        </div>
    </section>

    <!-- DISCOVER -->
    <section class="discover">
        <div class="discover-item" style="background: url('https://i.pinimg.com/1200x/0c/a2/51/0ca25176b92519f64ea6a91f2a776334.jpg');">
            <h3>Cita Rasa Autentik</h3>
        </div>
        <div class="discover-item" style="background: url('https://down-id.img.susercontent.com/file/3d03fead177caa123e9538140e64cc79@resize_w900_nl.webp');">
            <h3>Bahan Premium</h3>
        </div>
        <div class="discover-item" style="background: url('https://i.pinimg.com/736x/d1/1d/62/d11d62838e3facfa6e3c030fa975c6d0.jpg');">
            <h3>Higienis & Sehat</h3>
        </div>
    </section>

    <!-- PRODUK -->
    <section class="section-padding produk" id="produk">
        <div class="section-title">
            <h2>Pilihan Menu Eksklusif</h2>
            <div class="separator"></div>
        </div>

        <div class="grid">
            <?php
            $data = mysqli_query($conn,"SELECT * FROM produk");
            while($d = mysqli_fetch_array($data)){
            ?>
            <div class="card">
                <div class="card-img-wrapper">
                    <?php if($d['gambar'] != "" && file_exists("upload/".$d['gambar'])){ ?>
                        <img src="upload/<?= $d['gambar']; ?>" alt="<?= htmlspecialchars($d['nama']); ?>">
                    <?php } else { ?>
                        <img src="https://via.placeholder.com/400x300?text=No+Image" alt="No Image">
                    <?php } ?>
                </div>
                <div class="card-body">
                    <h3><?= htmlspecialchars($d['nama']); ?></h3>
                    <p><?= htmlspecialchars($d['deskripsi']); ?></p>
                    <div class="price">Rp <?= number_format($d['harga'], 0, ',', '.'); ?></div>
                    <?php if($logged_in) { ?>
                        <a href="<?= $_SESSION['role'] == 'pengunjung' ? 'pengunjung/pesan.php?id='.$d['id'] : $dashboard_url ?>" class="buy-btn">Pesan Sekarang</a>
                    <?php } else { ?>
                        <a href="beli.php?id=<?= $d['id']; ?>" class="buy-btn">Pesan Sekarang</a>
                    <?php } ?>
                </div>
            </div>
            <?php } ?>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-col">
                <h4>Ibu Iqbal Catering</h4>
                <p>Menghadirkan kelezatan sejati dalam setiap hidangan. Pilihan utama untuk layanan katering premium di kota Anda.</p>
                <div class="social-links">
                    <a href="#"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#"><i class="fa-brands fa-whatsapp"></i></a>
                    <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                </div>
            </div>
            <div class="footer-col">
                <h4>Hubungi Kami</h4>
                <p><i class="fa-solid fa-map-marker-alt"></i> Jalan Puri Nirwana 1, Cibinong, Bogor</p>
                <p><i class="fa-solid fa-phone-alt"></i> +62 812 3456 7890</p>
                <p><i class="fa-solid fa-envelope"></i> info@ibuiqbalcatering.com</p>
            </div>
            <div class="footer-col">
                <h4>Menu Cepat</h4>
                <p><a href="#about" style="color:#999;text-decoration:none;">Tentang Kami</a></p>
                <p><a href="#produk" style="color:#999;text-decoration:none;">Menu Eksklusif</a></p>
                <?php if($logged_in) { ?>
                    <p><a href="<?= $dashboard_url ?>" style="color:#999;text-decoration:none;">Dashboard Anda</a></p>
                <?php } else { ?>
                    <p><a href="login.php" style="color:#999;text-decoration:none;">Login Pelanggan</a></p>
                <?php } ?>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 Catering Ibu Iqbal. </i></p>
        </div>
    </footer>

    <script>
    // NAVBAR SCROLL EFFECT
    window.addEventListener("scroll", function(){
        let nav = document.getElementById("navbar");
        nav.classList.toggle("scrolled", window.scrollY > 50);
    });
    </script>

</body>
</html>