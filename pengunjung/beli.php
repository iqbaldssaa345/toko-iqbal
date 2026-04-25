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

/* ALAMAT USER */
$alamat = mysqli_query($conn,"SELECT * FROM alamat WHERE user_id='$user_id'");

/* ONGKIR DB */
$ongkir = mysqli_query($conn,"SELECT * FROM ongkir");

/* TAMBAH */
if(isset($_POST['beli'])){
    $jumlah = intval($_POST['jumlah']);
    $alamat_id = intval($_POST['alamat_id']);
    $ongkir_id = intval($_POST['ongkir_id']);

    $cek_ongkir = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM ongkir WHERE id='$ongkir_id'"));
    $biaya = $cek_ongkir['biaya'];

    if($jumlah <= 0){
        echo "<script>alert('Jumlah tidak valid');</script>";
    } else {
        $total = ($produk['harga'] * $jumlah) + $biaya;

        mysqli_query($conn,"INSERT INTO pesanan
        (user_id, alamat_id, ongkir_id, total)
        VALUES
        ('$user_id','$alamat_id','$ongkir_id','$total')");

        echo "<script>alert('Pesanan berhasil');location='beli.php?id=$id';</script>";
    }
}

/* HAPUS */
if(isset($_GET['hapus'])){
    $hapus = intval($_GET['hapus']);
    mysqli_query($conn,"DELETE FROM pesanan WHERE id='$hapus' AND user_id='$user_id'");
    echo "<script>location='beli.php?id=$id';</script>";
}

/* DATA JOIN */
$data = mysqli_query($conn,"
SELECT p.*, a.nama_penerima, a.kota, o.nama_jasa, o.biaya 
FROM pesanan p
LEFT JOIN alamat a ON p.alamat_id = a.id
LEFT JOIN ongkir o ON p.ongkir_id = o.id
WHERE p.user_id='$user_id'
ORDER BY p.id ASC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Beli Produk</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

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
    display:block;
    padding:12px;
    color:#bbb;
    text-decoration:none;
    border-radius:10px;
}
.sidebar a:hover{
    background:#1f2f4a;
    color:white;
}
.content{
    margin-left:270px;
    padding:25px;
}

/* CARD */
.card{
    border-radius:20px;
    box-shadow:0 10px 25px rgba(0,0,0,0.1);
}

/* BADGE */
.badge-harga{
    background:#e9f7ef;
    color:#28a745;
    padding:8px 15px;
    border-radius:20px;
    font-weight:bold;
}

/* BUTTON */
.btn-beli{
    background:linear-gradient(45deg,#28a745,#20c997);
    color:white;
    border-radius:25px;
}
.btn-beli:hover{
    background:linear-gradient(45deg,#218838,#17a589);
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h4>🛒 Pengunjung</h4>

    <a href="index.php"><i class="fa fa-home"></i> Dashboard</a>
    <a href="alamat.php"><i class="fa fa-map-marker-alt"></i> Alamat</a>
    <a href="pembayaran.php"><i class="fa fa-credit-card"></i> Pembayaran</a>
    <a href="pesanan_saya.php"><i class="fa fa-shopping-cart"></i> Pesanan Saya</a>
    <a href="../logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
</div>

<div class="content">

<!-- PRODUK -->
<div class="card p-4 mb-4">
<h4><?= $produk['nama'] ?></h4>

<div class="badge-harga mb-3">
Rp <?= number_format($produk['harga']) ?>
</div>

<form method="POST">

<input type="number" name="jumlah" class="form-control mb-3" placeholder="Jumlah" required min="1">

<!-- ALAMAT -->
<select name="alamat_id" class="form-control mb-3" required>
<option value="">-- Pilih Alamat --</option>
<?php while($a=mysqli_fetch_array($alamat)){ ?>
<option value="<?= $a['id'] ?>">
<?= $a['nama_penerima'] ?> - <?= $a['kota'] ?>
</option>
<?php } ?>
</select>

<!-- ONGKIR -->
<select name="ongkir_id" class="form-control mb-3" required>
<option value="">-- Pilih Ongkir --</option>
<?php while($o=mysqli_fetch_array($ongkir)){ ?>
<option value="<?= $o['id'] ?>">
<?= $o['nama_jasa'] ?> - Rp <?= number_format($o['biaya']) ?>
</option>
<?php } ?>
</select>

<button name="beli" class="btn btn-beli w-100">
<i class="fa fa-cart-plus"></i> Beli Sekarang
</button>

</form>
</div>

<!-- PESANAN -->
<div class="card p-4">
<h5><i class="fa fa-shopping-cart"></i> Pesanan Saya</h5>

<table class="table mt-3">
<tr class="table-dark">
<th>Alamat</th>
<th>Ongkir</th>
<th>Total</th>
<th>Aksi</th>
</tr>

<?php while($d=mysqli_fetch_array($data)){ ?>
<tr>
<td><?= $d['nama_penerima'] ?> (<?= $d['kota'] ?>)</td>
<td><?= $d['nama_jasa'] ?> (Rp <?= number_format($d['biaya']) ?>)</td>
<td class="text-success">Rp <?= number_format($d['total']) ?></td>
<td>

<a href="?id=<?= $id ?>&hapus=<?= $d['id'] ?>" class="btn btn-danger btn-sm">
<i class="fa fa-trash"></i>
</a>

<a href="pembayaran.php?id=<?= $d['id'] ?>" class="btn btn-primary btn-sm">
<i class="fa fa-credit-card"></i>
</a>

</td>
</tr>
<?php } ?>

</table>
</div>

</div>

</body>
</html>