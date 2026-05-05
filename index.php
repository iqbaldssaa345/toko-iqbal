<?php include 'koneksi.php'; ?>
<!DOCTYPE html>
<html>
<head>
<title> Catering Ibu Iqbal</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Poppins;
    scroll-behavior:smooth;
}

body{
    background:#fff;
}

/* NAVBAR */
.navbar{
    position:fixed;
    width:100%;
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:20px 60px;
    transition:0.3s;
    z-index:1000;
}

.navbar.scrolled{
    background:white;
    box-shadow:0 5px 15px rgba(0,0,0,0.1);
}

.navbar h2{
    color:white;
}

.navbar.scrolled h2{
    color:#333;
}

.navbar a{
    color:white;
    text-decoration:none;
    margin:0 10px;
}

.navbar.scrolled a{
    color:#333;
}

.btn{
    background:linear-gradient(45deg,orange,red);
    padding:8px 18px;
    border-radius:25px;
    color:white;
}

/* HERO */
.slider{
    height:100vh;
    background:linear-gradient(rgba(0,0,0,0.6),rgba(0,0,0,0.6)), url('https://i.pinimg.com/736x/d1/1d/62/d11d62838e3facfa6e3c030fa975c6d0.jpg') center/cover;
    display:flex;
    align-items:center;
    justify-content:center;
    text-align:center;
    color:white;
}

.hero-text h1{
    font-size:55px;
    animation:fadeUp 1s ease;
}

.hero-text p{
    margin:20px 0;
}

.hero-text a{
    background:linear-gradient(45deg,orange,red);
    padding:12px 30px;
    border-radius:30px;
    color:white;
    text-decoration:none;
}

/* ANIMATION */
@keyframes fadeUp{
    from{opacity:0; transform:translateY(50px);}
    to{opacity:1; transform:translateY(0);}
}

/* ABOUT */
.about{
    padding:100px 20px;
    text-align:center;
}

/* DISCOVER */
.discover{
    display:flex;
    flex-wrap:wrap;
}

.discover div{
    flex:1;
    min-height:300px;
    background-size:cover;
    display:flex;
    align-items:center;
    justify-content:center;
    color:white;
    font-size:25px;
    font-weight:bold;
}

/* PRODUK */
.produk{
    padding:80px 50px;
    background:#f7f7f7;
}

.grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
    gap:25px;
}

.card{
    background:rgba(255,255,255,0.9);
    border-radius:20px;
    overflow:hidden;
    backdrop-filter:blur(10px);
    box-shadow:0 10px 30px rgba(0,0,0,0.1);
    transition:0.3s;
}

.card:hover{
    transform:scale(1.05);
}

.card img{
    width:100%;
    height:200px;
    object-fit:cover;
}

.card-body{
    padding:20px;
    text-align:center;
}

.price{
    color:orange;
    font-size:18px;
    font-weight:bold;
}

.buy{
    display:inline-block;
    margin-top:10px;
    padding:8px 20px;
    background:linear-gradient(45deg,orange,red);
    color:white;
    border-radius:20px;
    text-decoration:none;
}

/* FOOTER */
.footer{
    background:#111;
    color:#aaa;
    padding:50px;
    text-align:center;
}

.footer h3{
    color:white;
}

/* RESPONSIVE */
@media(max-width:768px){
    .navbar{
        padding:15px;
    }
}
</style>
</head>

<body>

<!-- NAVBAR -->
<div class="navbar" id="navbar">
    <h2>Katering Ibu Iqbal</h2>
    <div>
        <a href="#about">About</a>
        <a href="#produk">Menu</a>
        <a href="login.php" class="btn">Login</a>
        <a href="daftar.php" class="btn">Daftar</a>
    </div>
</div>

<!-- HERO -->
<div class="slider">
    <div class="hero-text">
        <h1>Sajian Khas Indonesia</h1>
        <p>Lezat • Higienis • Modern</p>
        <a href="#produk">Pesan Sekarang</a>
    </div>
</div>

<!-- ABOUT -->
<div class="about" id="about">
    <h2>Tentang Kami</h2>
    <p>Kami menghadirkan makanan terbaik dengan kualitas premium dan rasa autentik Indonesia.</p>
</div>

<!-- DISCOVER -->
<div class="discover">
    <div style="background:url('https://i.pinimg.com/1200x/0c/a2/51/0ca25176b92519f64ea6a91f2a776334.jpg') center/cover;">Tempe lalapan </div>
    <div style="background:url('https://i.pinimg.com/736x/81/70/66/817066a3e0587d60592d31920f31f69a.jpg') center/cover;">Sate Ayam</div>
    <div style="background:url('https://down-id.img.susercontent.com/file/3d03fead177caa123e9538140e64cc79@resize_w900_nl.webp') center/cover;">Ayam Geprek</div>
</div>
<!-- PRODUK -->
<div class="produk" id="produk">
    <h2 style="text-align:center;margin-bottom:40px;">Menu Favorit</h2>

    <div class="grid">
        <?php
        $data = mysqli_query($conn,"SELECT * FROM produk");
        while($d = mysqli_fetch_array($data)){
        ?>
        <div class="card">

            <?php if($d['gambar'] != "" && file_exists("upload/".$d['gambar'])){ ?>
                <img src="upload/<?= $d['gambar']; ?>" alt="<?= $d['nama']; ?>">
            <?php } else { ?>
                <img src="https://via.placeholder.com/300x200?text=No+Image">
            <?php } ?>

            <div class="card-body">
                <h3><?= $d['nama']; ?></h3>
                <p><?= $d['deskripsi']; ?></p>
                <p class="price">Rp <?= number_format($d['harga']); ?></p>
                <a href="login.php" class="buy">Beli</a>
            </div>
        </div>
        <?php } ?>
    </div>
</div>

<!-- FOOTER -->
<div class="footer">
    <h3>Catering Ibu Iqbal</h3>
    <p>jalan puri nirwana 1 </p>
    <p>Telp: 08123456789</p>
    <p>© 2026 Catering Ibu Iqbal. </p>
</div>

<script>
// NAVBAR SCROLL EFFECT
window.addEventListener("scroll", function(){
    let nav = document.getElementById("navbar");
    nav.classList.toggle("scrolled", window.scrollY > 50);
});
</script>

</body>
</html>