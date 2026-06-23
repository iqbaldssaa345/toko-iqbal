<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role']!="petugas"){
    header("location:../login.php");
    exit;
}
include '../koneksi.php';

// Data Petugas
$id_petugas = $_SESSION['id'];
$q_petugas = mysqli_query($conn, "SELECT username FROM users WHERE id='$id_petugas'");
$d_petugas = mysqli_fetch_assoc($q_petugas);
$nama_petugas = $d_petugas['username'];

/* ================= TAMBAH ================= */
if(isset($_POST['tambah'])){
    $pesanan_id = intval($_POST['pesanan_id']);
    $produk_id  = intval($_POST['produk_id']);
    $jumlah     = intval($_POST['jumlah']);

    // ambil harga produk
    $p = mysqli_fetch_array(mysqli_query($conn,"SELECT harga FROM produk WHERE id='$produk_id'"));
    $subtotal = $p['harga'] * $jumlah;

    mysqli_query($conn,"INSERT INTO detail_pesanan (pesanan_id,produk_id,jumlah,subtotal)
    VALUES('$pesanan_id','$produk_id','$jumlah','$subtotal')");

    header("location:detail_pesanan.php?pesan=sukses_tambah");
    exit;
}

/* ================= HAPUS ================= */
if(isset($_GET['hapus'])){
    $id = intval($_GET['hapus']);

    mysqli_query($conn,"DELETE FROM detail_pesanan WHERE id='$id'");

    header("location:detail_pesanan.php?pesan=sukses_hapus");
    exit;
}

/* ================= EDIT ================= */
if(isset($_POST['edit'])){
    $id         = intval($_POST['id']);
    $produk_id  = intval($_POST['produk_id']);
    $jumlah     = intval($_POST['jumlah']);

    $p = mysqli_fetch_array(mysqli_query($conn,"SELECT harga FROM produk WHERE id='$produk_id'"));
    $subtotal = $p['harga'] * $jumlah;

    mysqli_query($conn,"UPDATE detail_pesanan SET
        produk_id='$produk_id',
        jumlah='$jumlah',
        subtotal='$subtotal'
        WHERE id='$id'
    ");

    header("location:detail_pesanan.php?pesan=sukses_edit");
    exit;
}

/* ================= DATA LIST ================= */
$data = mysqli_query($conn,"
    SELECT dp.*, produk.nama 
    FROM detail_pesanan dp
    LEFT JOIN produk ON dp.produk_id = produk.id
    ORDER BY dp.id ASC
");

/* dropdown data */
$pesanan = mysqli_query($conn,"SELECT id FROM pesanan ORDER BY id DESC");
$produk  = mysqli_query($conn,"SELECT id, nama, harga FROM produk ORDER BY nama ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan - Catering Ibu Iqbal</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../css/dashboard.css" rel="stylesheet">
</head>

<body>

    <!-- SIDEBAR -->
    <?php include '../includes/sidebar_petugas.php'; ?>

    <!-- MAIN CONTENT -->
    <div class="main-content">

        <!-- TOPBAR -->
        <?php 
        $topbar_title = "Rincian Item Pesanan";
        $user_name = $nama_petugas;
        $topbar_icon = '<i class="fa fa-eye text-primary"></i>';
        include '../includes/topbar.php';
        ?>

        <!-- TABLE WRAPPER -->
        <div class="card-premium">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <h5 class="card-title-premium mb-0">
                    <i class="fa fa-list"></i> Rincian Produk Pesanan
                </h5>
                <button class="btn-premium-primary" data-bs-toggle="modal" data-bs-target="#tambah">
                    <i class="fa fa-plus"></i> Tambah Item
                </button>
            </div>

            <div class="table-responsive">
                <table class="table-premium">
                    <thead>
                        <tr>
                            <th>ID Detail</th>
                            <th>ID Pesanan</th>
                            <th>Nama Produk</th>
                            <th>Jumlah (Qty)</th>
                            <th>Subtotal</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($d = mysqli_fetch_array($data)){ ?>
                        <tr>
                            <td><strong>#<?= $d['id'] ?></strong></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-light p-2 rounded-circle text-primary d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                        <i class="fa fa-receipt"></i>
                                    </div>
                                    #<?= htmlspecialchars($d['pesanan_id']) ?>
                                </div>
                            </td>
                            <td>
                                <span class="text-dark fw-semibold">
                                    <i class="fa fa-box-open me-1 text-muted"></i> <?= htmlspecialchars($d['nama'] ? $d['nama'] : 'Produk Dihapus') ?>
                                </span>
                            </td>
                            <td><span class="price-badge" style="background-color: #f1f3f5; color: #495057;"> <?= htmlspecialchars($d['jumlah']) ?> Porsi</span></td>
                            <td><span class="fw-bold text-dark">Rp <?= number_format($d['subtotal']) ?></span></td>

                            <td class="text-center">
                                <button class="btn-action-premium btn-edit-premium me-1" data-bs-toggle="modal" data-bs-target="#edit<?= $d['id'] ?>" title="Edit">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button type="button" class="btn-action-premium btn-delete-premium" onclick="konfirmasiHapus('?hapus=<?= $d['id'] ?>')" title="Hapus">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- MODAL EDIT -->
                        <div class="modal fade" id="edit<?= $d['id'] ?>" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <form method="POST">
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold">Edit Item Rincian #<?= $d['id'] ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body text-start">
                                            <input type="hidden" name="id" value="<?= $d['id'] ?>">
                                            
                                            <div class="mb-3">
                                                <label class="form-label-muted">Pilih Produk</label>
                                                <select name="produk_id" class="form-select" required>
                                                    <?php
                                                    mysqli_data_seek($produk, 0);
                                                    while($p = mysqli_fetch_array($produk)){
                                                    ?>
                                                    <option value="<?= $p['id'] ?>" <?= $p['id']==$d['produk_id']?'selected':'' ?>>
                                                        <?= htmlspecialchars($p['nama']) ?> (Rp <?= number_format($p['harga']) ?>)
                                                    </option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label-muted">Jumlah Porsi</label>
                                                <input type="number" name="jumlah" class="form-control" value="<?= $d['jumlah'] ?>" required min="1">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" name="edit" class="btn btn-premium-primary">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- MODAL TAMBAH -->
    <div class="modal fade" id="tambah" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Tambah Detail Pesanan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-start">
                        <div class="mb-3">
                            <label class="form-label-muted">Pilih ID Pesanan</label>
                            <select name="pesanan_id" class="form-select" required>
                                <option value="">-- Pilih Pesanan --</option>
                                <?php 
                                mysqli_data_seek($pesanan, 0);
                                while($p = mysqli_fetch_array($pesanan)){ ?>
                                <option value="<?= $p['id'] ?>">Pesanan #<?= $p['id'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label-muted">Pilih Produk</label>
                            <select name="produk_id" class="form-select" required>
                                <option value="">-- Pilih Produk --</option>
                                <?php 
                                mysqli_data_seek($produk, 0);
                                while($p = mysqli_fetch_array($produk)){ ?>
                                <option value="<?= $p['id'] ?>">
                                    <?= htmlspecialchars($p['nama']) ?> (Rp <?= number_format($p['harga']) ?>)
                                </option>
                                <?php } ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label-muted">Jumlah Porsi</label>
                            <input type="number" name="jumlah" class="form-control" placeholder="Contoh: 10" required min="1">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah" class="btn btn-premium-primary">Simpan Data</button>
                    </div>
                </form>
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
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
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
            Swal.fire({ title: 'Berhasil!', text: 'Data berhasil ditambahkan.', icon: 'success', confirmButtonClass: 'btn btn-primary rounded-pill px-4', buttonsStyling: false });
        <?php }elseif($_GET['pesan'] == 'sukses_edit'){ ?>
            Swal.fire({ title: 'Berhasil!', text: 'Data berhasil diperbarui.', icon: 'success', confirmButtonClass: 'btn btn-primary rounded-pill px-4', buttonsStyling: false });
        <?php }elseif($_GET['pesan'] == 'sukses_hapus'){ ?>
            Swal.fire({ title: 'Berhasil!', text: 'Data berhasil dihapus.', icon: 'success', confirmButtonClass: 'btn btn-primary rounded-pill px-4', buttonsStyling: false });
        <?php } ?>
    </script>
    <?php } ?>

</body>
</html>