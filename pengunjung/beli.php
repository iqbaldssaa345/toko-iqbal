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
WHERE p.user_id='$user_id'
ORDER BY p.id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Beli Produk</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
body{
    font-family: 'Segoe UI';
    background: linear-gradient(135deg,#eef2f7,#dbe9f4);
}

/* SIDEBAR */
.sidebar{
    width:250px;
    height:100vh;
    position:fixed;
    background: linear-gradient(180deg,#141e30,#243b55);
    padding:20px;
}
.sidebar h4{
    color:white;
}
.sidebar a{
    display:block;
    padding:12px;
    color:#bbb;
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
    padding:30px;
}

/* CARD */
.card{
    border-radius:20px;
    box-shadow:0 10px 25px rgba(0,0,0,0.1);
}

/* BUTTON */
.btn-custom{
    border-radius:25px;
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
    <a href="pembayaran.php"><i class="fa fa-credit-card"></i> Pembayaran</a>
    <a href="../logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
</div>

<div class="content">

<div class="container-fluid">

<!-- PRODUK -->
<div class="card p-4 mb-4">
<h4><?= $produk['nama'] ?></h4>
<p class="text-success fw-bold fs-5">Rp <?= number_format($produk['harga']) ?></p>

<form method="POST">

<input type="hidden" name="edit_id" value="<?= $edit_data ? $edit_data['id'] : 0 ?>">

<input type="number" name="jumlah" class="form-control mb-3"
value="<?= $edit_data && isset($edit_data['jumlah']) ? $edit_data['jumlah'] : '' ?>" placeholder="Jumlah" required>

<!-- ALAMAT -->
<select name="alamat_id" class="form-control mb-3" required>
<option value="">-- Pilih Alamat --</option>
<?php mysqli_data_seek($alamat,0); while($a=mysqli_fetch_array($alamat)){ ?>
<option value="<?= $a['id'] ?>"
<?= ($edit_data && $edit_data['alamat_id']==$a['id'])?'selected':'' ?>>
<?= $a['nama_penerima'] ?> - <?= $a['kota'] ?>
</option>
<?php } ?>
</select>

<!-- ONGKIR -->
<select name="ongkir_id" class="form-control mb-3" required>
<option value="">-- Pilih Ongkir --</option>
<?php mysqli_data_seek($ongkir,0); while($o=mysqli_fetch_array($ongkir)){ ?>
<option value="<?= $o['id'] ?>"
<?= ($edit_data && $edit_data['ongkir_id']==$o['id'])?'selected':'' ?>>
<?= $o['nama_jasa'] ?> - Rp <?= number_format($o['biaya']) ?>
</option>
<?php } ?>
</select>

<button name="simpan" class="btn btn-success w-100 btn-custom">
<?= $edit_data ? 'UPDATE PESANAN' : 'BELI SEKARANG' ?>
</button>

</form>
</div>

<!-- DATA PESANAN -->
<div class="card p-4">
<h5><i class="fa fa-shopping-cart"></i> Pesanan Saya</h5>

<table class="table table-hover mt-3">
<tr class="table-dark">
<th>Produk</th>
<th>Jumlah</th>
<th>Alamat</th>
<th>Ongkir</th>
<th>Total</th>
<th>Aksi</th>
</tr>

<?php if(mysqli_num_rows($data) > 0){ ?>
<?php while($d=mysqli_fetch_array($data)){ ?>
<tr>
<td><?= $d['nama_produk'] ? $d['nama_produk'] : '-' ?></td>
<td><?= $d['jumlah'] ? $d['jumlah'] : '-' ?></td>
<td><?= $d['nama_penerima'] ?> (<?= $d['kota'] ?>)</td>
<td><?= $d['nama_jasa'] ?> (Rp <?= number_format($d['biaya']) ?>)</td>
<td class="text-success fw-bold">Rp <?= number_format($d['total']) ?></td>
<td>

<a href="?id=<?= $id ?>&edit=<?= $d['id'] ?>" class="btn btn-warning btn-sm">
<i class="fa fa-edit"></i>
</a>

<button type="button" class="btn btn-danger btn-sm" onclick="konfirmasiHapus('?id=<?= $id ?>&hapus=<?= $d['id'] ?>')">
<i class="fa fa-trash"></i>
</button>

</td>
</tr>
<?php } ?>
<?php } else { ?>
<tr><td colspan="6" class="text-center">Belum ada pesanan</td></tr>
<?php } ?>

</table>
</div>

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