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

    echo "<script>location='pesanan_saya.php';</script>";
}

/* ================= HAPUS ================= */
if(isset($_GET['hapus'])){
    $id = intval($_GET['hapus']);

    // hapus detail + pesanan
    mysqli_query($conn,"DELETE dp FROM detail_pesanan dp 
        JOIN pesanan p ON dp.pesanan_id=p.id 
        WHERE dp.id='$id' AND p.user_id='$user_id'");

    echo "<script>location='pesanan_saya.php';</script>";
}

/* ================= DATA ================= */
$data = mysqli_query($conn,"
SELECT dp.*, pr.nama, pr.harga 
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
<html>
<head>
<title>Pesanan Saya</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    font-family:Poppins;
    background:linear-gradient(135deg,#eef2f7,#dbe9f4);
}
.sidebar{
    width:250px;
    height:100vh;
    position:fixed;
    background:linear-gradient(180deg,#141e30,#243b55);
    padding:20px;
}
.sidebar h4{color:white;}
.sidebar a{
    display:flex;
    gap:10px;
    padding:12px;
    color:#ccc;
    text-decoration:none;
    border-radius:10px;
}
.sidebar a:hover{
    background:rgba(255,255,255,0.1);
    color:white;
}
.content{
    margin-left:270px;
    padding:25px;
}
.card{
    border-radius:20px;
    box-shadow:0 10px 25px rgba(0,0,0,0.1);
}
.pesanan-box{
    background:white;
    padding:15px;
    border-radius:15px;
    margin-bottom:15px;
    box-shadow:0 5px 15px rgba(0,0,0,0.08);
    transition:0.3s;
}
.pesanan-box:hover{
    transform:translateY(-3px);
}
.btn{
    border-radius:10px;
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h4>🛒 Pengunjung</h4>
    <a href="index.php"><i class="fa fa-home"></i> Dashboard</a>
    <a href="alamat.php"><i class="fa fa-map-marker-alt"></i> Alamat</a>
    <a href="pesanan_saya.php"><i class="fa fa-shopping-cart"></i> Pesanan Saya</a>
    <a href="../logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
</div>

<div class="content">

<h3 class="mb-4">🛍️ Pesanan Saya</h3>

<!-- FORM -->
<div class="card p-4 mb-4">
<h5><i class="fa fa-plus"></i> Tambah Pesanan</h5>

<form method="POST">
<select name="produk_id" class="form-control mb-2" required>
<option value="">-- Pilih Produk --</option>
<?php while($p=mysqli_fetch_array($produk)){ ?>
<option value="<?= $p['id'] ?>">
<?= $p['nama'] ?> - Rp<?= number_format($p['harga']) ?>
</option>
<?php } ?>
</select>

<input type="number" name="jumlah" class="form-control mb-3" placeholder="Jumlah" required>

<button name="tambah" class="btn btn-success w-100">
<i class="fa fa-save"></i> Simpan
</button>
</form>
</div>

<!-- DATA -->
<div class="card p-4">
<h5><i class="fa fa-shopping-cart"></i> Daftar Pesanan</h5>

<?php while($d=mysqli_fetch_array($data)){ ?>
<div class="pesanan-box">

<h6><?= $d['nama'] ?></h6>
<p class="mb-1">Harga: Rp<?= number_format($d['harga']) ?></p>
<p class="mb-1">Jumlah: <?= $d['jumlah'] ?></p>
<p><b>Subtotal: Rp<?= number_format($d['subtotal']) ?></b></p>

<a href="?hapus=<?= $d['id'] ?>" 
   class="btn btn-danger btn-sm"
   onclick="return confirm('Hapus pesanan ini?')">
<i class="fa fa-trash"></i> Hapus
</a>

</div>
<?php } ?>

</div>

</div>

</body>
</html>