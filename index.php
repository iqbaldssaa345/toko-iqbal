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
            --gold-light: rgba(212, 175, 55, 0.12);
            --gold-glow: rgba(212, 175, 55, 0.35);
            --dark: #0F0F11;
            --light: #F6F5F2;
            --white: #FFFFFF;
            --transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
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
            transition: var(--transition);
            z-index: 1000;
            background: transparent;
        }

        .navbar.scrolled {
            background: rgba(15, 15, 17, 0.92);
            padding: 18px 8%;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(212, 175, 55, 0.15);
        }

        .brand-name {
            font-size: 28px;
            font-weight: 700;
            color: var(--white);
            text-decoration: none;
            letter-spacing: 1px;
            transition: var(--transition);
        }

        .navbar.scrolled .brand-name {
            color: var(--gold);
            text-shadow: 0 0 15px rgba(212, 175, 55, 0.3);
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
            transition: var(--transition);
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
            transition: var(--transition);
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

        .btn-gold-filled {
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
            border: 1px solid var(--gold);
            color: var(--dark) !important;
            padding: 10px 24px;
            border-radius: 30px;
            transition: var(--transition) !important;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-weight: 600;
            box-shadow: 0 5px 15px rgba(212, 175, 55, 0.25);
        }

        .btn-gold-filled:hover {
            background: linear-gradient(135deg, #ffffff 0%, #f1e2c3 100%);
            color: var(--dark) !important;
            box-shadow: 0 8px 20px rgba(212, 175, 55, 0.35);
            border-color: transparent;
        }

        /* HERO */
        .hero {
            height: 100vh;
            background: linear-gradient(rgba(15, 15, 17, 0.75), rgba(15, 15, 17, 0.85)), url('https://i.pinimg.com/736x/d1/1d/62/d11d62838e3facfa6e3c030fa975c6d0.jpg') center/cover fixed;
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
            background: linear-gradient(135deg, #ffffff 20%, #f4e3c1 60%, #d4af37 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
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
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
            color: var(--dark);
            text-decoration: none;
            font-size: 16px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
            border-radius: 40px;
            border: none;
            transition: var(--transition);
            animation: fadeInUp 1s ease 0.9s forwards;
            opacity: 0;
            box-shadow: 0 8px 25px rgba(212, 175, 55, 0.3);
        }

        .hero-btn:hover {
            background: linear-gradient(135deg, #ffffff 0%, #f1e2c3 100%);
            color: var(--dark);
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(212, 175, 55, 0.45);
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
            border-radius: 12px;
            box-shadow: -20px 20px 0px rgba(212, 175, 55, 0.15);
            border: 1px solid rgba(212, 175, 55, 0.3);
            transition: var(--transition);
        }
        .about-image img:hover {
            transform: translate(5px, -5px);
            box-shadow: -25px 25px 30px rgba(212, 175, 55, 0.22);
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
            background: rgba(15, 15, 17, 0.4);
            transition: var(--transition);
        }

        .discover-item:hover::before {
            background: rgba(15, 15, 17, 0.15);
        }

        .discover-item h3 {
            position: relative;
            color: var(--white);
            font-size: 30px;
            font-weight: 600;
            letter-spacing: 2px;
            z-index: 2;
            transition: var(--transition);
            font-family: 'Playfair Display', serif;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
        }

        .discover-item:hover h3 {
            transform: scale(1.08);
            text-shadow: 0 4px 15px rgba(0, 0, 0, 0.7);
        }

        /* PRODUK */
        .produk {
            background: #FAF9F6;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 40px;
        }

        .card {
            background: var(--white);
            border: 1px solid rgba(0, 0, 0, 0.04);
            border-radius: 16px;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
        }

        .card:hover {
            box-shadow: 0 20px 40px rgba(212, 175, 55, 0.08);
            border-color: rgba(212, 175, 55, 0.25);
            transform: translateY(-8px);
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
            transition: var(--transition);
        }

        .card:hover .card-img-wrapper img {
            transform: scale(1.06);
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
            border-radius: 30px;
            text-decoration: none;
            text-transform: uppercase;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 1px;
            transition: var(--transition);
        }

        .buy-btn:hover {
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
            border-color: transparent;
            color: var(--dark);
            box-shadow: 0 8px 20px rgba(212, 175, 55, 0.25);
        }

        /* FOOTER */
        .footer {
            background: #0F0F11;
            color: #a0a0a5;
            padding: 80px 8% 40px;
            border-top: 1px solid rgba(212, 175, 55, 0.25);
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

        .social-links a:hover {
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
            color: var(--dark);
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(212, 175, 55, 0.35);
            border-color: transparent;
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
            <a href="#testimoni">Testimoni</a>
            <a href="#lokasi">Lokasi</a>
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
                    
                    <div class="mb-3" style="font-size: 0.9rem;">
                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle py-2 px-3 rounded-pill">
                            <i class="fa fa-clock me-1"></i> Pre-Order: <?= htmlspecialchars($d['pre_order']) ?>
                        </span>
                    </div>

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

    </section>
 
    <!-- TESTIMONI PELANGGAN -->
    <section class="section-padding" id="testimoni" style="background: #fafafa; border-top: 1px solid rgba(212, 175, 55, 0.15);">
        <div class="section-title">
            <h2>Testimoni Pelanggan</h2>
            <div class="separator"></div>
        </div>
        <div style="max-width: 1100px; margin: 0 auto; padding: 0 20px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
                <?php
                $q_testi = mysqli_query($conn, "
                    SELECT t.*, u.username 
                    FROM testimoni t
                    JOIN users u ON t.user_id = u.id
                    ORDER BY t.tanggal DESC LIMIT 3
                ");
                if(mysqli_num_rows($q_testi) > 0) {
                    while($testi = mysqli_fetch_array($q_testi)) {
                ?>
                <div class="card" style="padding: 30px; text-align: left; background: #ffffff; border-radius: 16px; border: 1px solid rgba(212, 175, 55, 0.2); box-shadow: 0 4px 15px rgba(0,0,0,0.02); transition: var(--transition);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <span style="font-weight: 600; color: var(--dark); font-size: 1.1rem;"><i class="fa fa-user-circle text-gold me-2"></i> <?= htmlspecialchars($testi['username']) ?></span>
                        <span class="text-muted small"><?= date('d M Y', strtotime($testi['tanggal'])) ?></span>
                    </div>
                    <div style="color: #ffac0a; margin-bottom: 15px; font-size: 0.95rem;">
                        <?php 
                        $stars = intval($testi['bintang']);
                        for($i=1; $i<=5; $i++) {
                            if($i <= $stars) {
                                echo '<i class="fa fa-star"></i>';
                            } else {
                                echo '<i class="fa-regular fa-star"></i>';
                            }
                        }
                        ?>
                    </div>
                    <p style="color: #555; font-size: 0.95rem; font-style: italic; line-height: 1.6;">"<?= htmlspecialchars($testi['isi_testimoni']) ?>"</p>
                </div>
                <?php 
                    }
                } else {
                ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #777;">
                    <i class="fa-regular fa-comment-dots fa-3x text-gold-dark mb-3" style="font-size: 3rem; color: var(--gold);"></i>
                    <p>Belum ada testimoni. Jadilah yang pertama memberikan ulasan melalui panel pelanggan!</p>
                </div>
                <?php } ?>
            </div>
        </div>
    </section>

    <!-- LOKASI & KONTAK KAMI -->
    <section class="section-padding" id="lokasi" style="background: #ffffff; border-top: 1px solid rgba(212, 175, 55, 0.15);">
        <div class="section-title">
            <h2>Lokasi & Kontak Kami</h2>
            <div class="separator"></div>
        </div>
        <div style="max-width: 1100px; margin: 0 auto; padding: 0 20px;">
            <div style="display: flex; flex-wrap: wrap; gap: 50px; align-items: center;">
                <div style="flex: 1.2; min-width: 320px;">
                    <div style="position: relative; overflow: hidden; border-radius: 20px; box-shadow: 0 15px 40px rgba(0,0,0,0.08); border: 1px solid rgba(212, 175, 55, 0.2);">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3964.2828359740597!2d106.84072227448888!3d-6.485811793505708!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69c11579ff100d%3A0xe968b556b1b51e04!2sPuri%20Nirwana%201%2C%20Cibinong%2C%20Bogor%20Regency%2C%20West%20Java!5e0!3m2!1sen!2sid!4v1720850000000!5m2!1sen!2sid" width="100%" height="380" style="border:0; display: block;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
                <div style="flex: 1; min-width: 300px; display: flex; flex-direction: column; gap: 20px;">
                    <div>
                        <h3 style="font-size: 22px; color: var(--dark); margin-bottom: 12px; font-family: 'Playfair Display', serif;"><i class="fa-solid fa-map-location-dot text-gold me-2"></i> Kunjungi Outlet Kami</h3>
                        <p style="color: #666; font-size: 15px; line-height: 1.6; margin-bottom: 12px;">Kami berlokasi di area strategis Cibinong. Datang langsung untuk berkonsultasi mengenai menu acara khusus Anda atau mencicipi tester menu kami.</p>
                        <p style="color: var(--dark); font-weight: 600; font-size: 15px;"><i class="fa-solid fa-map-marker-alt text-gold me-2"></i> Jalan Puri Nirwana 1, Cibinong, Bogor, Jawa Barat</p>
                    </div>
                    <div>
                        <h4 style="font-size: 16px; color: var(--dark); margin-bottom: 6px; font-weight: 600;"><i class="fa-solid fa-clock text-gold me-2"></i> Jam Operasional</h4>
                        <p style="color: #666; font-size: 14px;">Setiap Hari: 08:00 - 21:00 WIB</p>
                    </div>
                    <div>
                        <h4 style="font-size: 16px; color: var(--dark); margin-bottom: 10px; font-weight: 600;"><i class="fa-solid fa-comments text-gold me-2"></i> Hubungi Langsung</h4>
                        <div style="display: flex; gap: 12px;">
                            <a href="https://wa.me/6281234567890" target="_blank" class="btn-gold-filled" style="padding: 10px 20px; text-decoration: none; font-size: 14px;"><i class="fa-brands fa-whatsapp me-2"></i> WhatsApp</a>
                            <a href="tel:+6281234567890" class="btn-gold" style="padding: 10px 20px; text-decoration: none; font-size: 14px;"><i class="fa-solid fa-phone me-2"></i> Telepon</a>
                        </div>
                    </div>
                </div>
            </div>
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
                <p><a href="#testimoni" style="color:#999;text-decoration:none;">Testimoni Pelanggan</a></p>
                <p><a href="#lokasi" style="color:#999;text-decoration:none;">Lokasi Outlet</a></p>
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