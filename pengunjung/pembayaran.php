<?php
session_start();
include '../koneksi.php';

if(!isset($_SESSION['id'])){
    die("Harus login!");
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
ORDER BY pembayaran.id ASC
");

/* ================= PESANAN ================= */
$pesanan = mysqli_query($conn,"SELECT * FROM pesanan WHERE user_id='$user_id'");
?>

<!DOCTYPE html>
<html>
<head>
<title>Pembayaran Saya</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
body{
    margin:0;
    font-family:Poppins, sans-serif;
    background:#eef2f7;
}

/* SIDEBAR */
.sidebar{
    width:250px;
    height:100vh;
    position:fixed;
    background:linear-gradient(180deg,#141e30,#243b55);
    padding:20px;
}
.sidebar h4{
    color:white;
    margin-bottom:20px;
}
.sidebar a{
    display:block;
    padding:12px;
    color:#ccc;
    text-decoration:none;
    border-radius:10px;
    margin-bottom:5px;
}
.sidebar a:hover{
    background:#1f2f4a;
    color:white;
}

/* CONTENT */
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
.badge-pending{
    background:#ffc107;
    padding:6px 12px;
    border-radius:10px;
}
.badge-lunas{
    background:#28a745;
    color:white;
    padding:6px 12px;
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

<!-- CONTENT -->
<div class="content">

<h3 class="mb-4">💳 Pembayaran Saya</h3>

<!-- FORM -->
<div class="card p-4 mb-4">
<h5><i class="fa fa-plus"></i> Tambah Pembayaran</h5>

<form method="POST">

<select name="pesanan_id" class="form-control mb-3" required>
<option value="">-- Pilih Pesanan --</option>
<?php while($p=mysqli_fetch_array($pesanan)){ ?>
<option value="<?= $p['id'] ?>">
Pesanan #<?= $p['id'] ?> - Rp <?= number_format($p['total']) ?>
</option>
<?php } ?>
</select>

<select name="metode" class="form-control mb-3" required>
<option value="">-- Metode Pembayaran --</option>
<option>Transfer Bank</option>
<option>E-Wallet</option>
<option>COD</option>
</select>

<button name="tambah" class="btn btn-success w-100">
<i class="fa fa-save"></i> Tambah Pembayaran
</button>

</form>
</div>

<!-- DATA -->
<div class="card p-4">
<h5><i class="fa fa-credit-card"></i> Data Pembayaran</h5>

<table class="table mt-3">
<tr class="table-dark">
<th>ID</th>
<th>Pesanan</th>
<th>Total</th>
<th>Metode</th>
<th>Status</th>
<th>Aksi</th>
</tr>

<?php while($d=mysqli_fetch_array($data)){ ?>
<tr>
<td><?= $d['id'] ?></td>
<td>#<?= $d['pesanan_id'] ?></td>
<td class="text-success">Rp <?= number_format($d['total']) ?></td>
<td><?= $d['metode'] ?></td>

<td>
<?php if($d['status']=="pending"){ ?>
<span class="badge-pending">Pending</span>
<?php } else { ?>
<span class="badge-lunas">Lunas</span>
<?php } ?>
</td>

<td>
<button type="button" class="btn btn-danger btn-sm" onclick="konfirmasiHapus('?hapus=<?= $d['id'] ?>')">
<i class="fa fa-trash"></i>
</button>
</td>
</tr>
<?php } ?>

</table>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function konfirmasiHapus(url) {
    Swal.fire({
        title: 'Yakin hapus data ini?',
        text: "Data yang dihapus tidak bisa dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
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
        Swal.fire('Berhasil!', 'Data berhasil ditambahkan.', 'success');
    <?php }elseif($_GET['pesan'] == 'sukses_edit'){ ?>
        Swal.fire('Berhasil!', 'Data berhasil diperbarui.', 'success');
    <?php }elseif($_GET['pesan'] == 'sukses_hapus'){ ?>
        Swal.fire('Berhasil!', 'Data berhasil dihapus.', 'success');
    <?php } ?>
</script>
<?php } ?>

</body>
</html>