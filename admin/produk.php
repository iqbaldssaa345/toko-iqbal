<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin"){
    header("location:../login.php");
    exit;
}
include '../koneksi.php';

// Data Admin
$id_admin = $_SESSION['id'];
$q_admin = mysqli_query($conn, "SELECT username FROM users WHERE id='$id_admin'");
$d_admin = mysqli_fetch_assoc($q_admin);
$nama_admin = $d_admin['username'];

/* ================= TAMBAH ================= */
if(isset($_POST['tambah'])){
    $nama = mysqli_real_escape_string($conn,$_POST['nama']);
    $des = mysqli_real_escape_string($conn,$_POST['deskripsi']);
    $harga = intval($_POST['harga']);

    $gambar = $_FILES['gambar']['name'];
    $tmp = $_FILES['gambar']['tmp_name'];

    if($gambar){
        move_uploaded_file($tmp,"../upload/".$gambar);
    }

    mysqli_query($conn,"INSERT INTO produk (nama,deskripsi,harga,gambar)
    VALUES('$nama','$des','$harga','$gambar')");

    header("location:produk.php?pesan=sukses_tambah");
    exit;
}

/* ================= HAPUS ================= */
if(isset($_GET['hapus'])){
    $id = intval($_GET['hapus']);
    
    // Hapus detail pesanan terkait agar tidak error foreign key
    mysqli_query($conn,"DELETE FROM detail_pesanan WHERE produk_id='$id'");
    mysqli_query($conn,"DELETE FROM produk WHERE id='$id'");
    
    header("location:produk.php?pesan=sukses_hapus");
    exit;
}

/* ================= EDIT ================= */
if(isset($_POST['edit'])){
    $id = intval($_POST['id']);
    $nama = mysqli_real_escape_string($conn,$_POST['nama']);
    $des = mysqli_real_escape_string($conn,$_POST['deskripsi']);
    $harga = intval($_POST['harga']);

    if(!empty($_FILES['gambar']['name'])){
        $gambar = $_FILES['gambar']['name'];
        $tmp = $_FILES['gambar']['tmp_name'];

        move_uploaded_file($tmp,"../upload/".$gambar);

        mysqli_query($conn,"UPDATE produk SET
        nama='$nama',
        deskripsi='$des',
        harga='$harga',
        gambar='$gambar'
        WHERE id='$id'");
    }else{
        mysqli_query($conn,"UPDATE produk SET
        nama='$nama',
        deskripsi='$des',
        harga='$harga'
        WHERE id='$id'");
    }

    header("location:produk.php?pesan=sukses_edit");
    exit;
}

/* ================= DATA LIST ================= */
$data = mysqli_query($conn,"SELECT * FROM produk ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Produk - Admin</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="../css/dashboard.css" rel="stylesheet">
    
    <style>
        .img-produk {
            width: 65px;
            height: 65px;
            object-fit: cover;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .img-produk:hover {
            transform: scale(1.1);
        }
    </style>
</head>

<body>

    <!-- SIDEBAR -->
    <?php include '../includes/sidebar_admin.php'; ?>

    <!-- MAIN CONTENT -->
    <div class="main-content">

        <!-- TOPBAR -->
        <?php 
        $topbar_title = "Data Produk";
        $user_name = $nama_admin;
        $topbar_icon = '<i class="fa fa-box text-primary"></i>';
        include '../includes/topbar.php';
        ?>

        <!-- CARD BOX -->
        <div class="card-premium">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <h5 class="card-title-premium mb-0"><i class="fa fa-list"></i> Daftar Produk Makanan Catering</h5>
                <button class="btn-premium-primary" data-bs-toggle="modal" data-bs-target="#tambah">
                    <i class="fa fa-plus"></i> Tambah Produk
                </button>
            </div>

            <div class="table-responsive">
                <table class="table-premium">
                    <thead>
                        <tr>
                            <th class="text-center" width="10%">Gambar</th>
                            <th>Nama Produk</th>
                            <th>Deskripsi</th>
                            <th>Harga Satuan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $modals = "";
                        while($d = mysqli_fetch_array($data)){ 
                        ?>
                        <tr>
                            <td class="text-center">
                                <?php if(!empty($d['gambar']) && file_exists("../upload/".$d['gambar'])){ ?>
                                    <img src="../upload/<?= htmlspecialchars($d['gambar']) ?>" class="img-produk" alt="<?= htmlspecialchars($d['nama']) ?>">
                                <?php } else { ?>
                                    <div class="bg-light rounded p-2 text-muted d-inline-flex justify-content-center align-items-center" style="width: 60px; height: 60px;">
                                        <i class="fa fa-utensils"></i>
                                    </div>
                                <?php } ?>
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($d['nama']) ?></div>
                            </td>
                            <td>
                                <span class="text-muted" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; font-size: 0.9rem;">
                                    <?= htmlspecialchars($d['deskripsi']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="price-badge">Rp <?= number_format($d['harga'], 0, ',', '.') ?></span>
                            </td>
                            <td class="text-center">
                                <button class="btn-action-premium btn-edit-premium me-1" data-bs-toggle="modal" data-bs-target="#edit<?= $d['id'] ?>" title="Edit">
                                    <i class="fa fa-edit"></i>
                                </button>

                                <button type="button" class="btn-action-premium btn-delete-premium" onclick="konfirmasiHapus('?hapus=<?= $d['id'] ?>')" title="Hapus">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>

                        <?php ob_start(); ?>
                        <!-- MODAL EDIT -->
                        <div class="modal fade" id="edit<?= $d['id'] ?>" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <form method="POST" enctype="multipart/form-data">
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold"><i class="fa fa-edit me-2 text-warning"></i> Edit Produk</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body text-start">
                                            <input type="hidden" name="id" value="<?= $d['id'] ?>">
                                            
                                            <div class="mb-3">
                                                <label class="form-label-muted">Nama Produk</label>
                                                <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($d['nama']) ?>" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label-muted">Deskripsi Singkat</label>
                                                <textarea name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($d['deskripsi']) ?></textarea>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label-muted">Harga (Rp)</label>
                                                <input type="number" name="harga" class="form-control" value="<?= $d['harga'] ?>" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label-muted">Upload Gambar Baru (Opsional)</label>
                                                <input type="file" name="gambar" class="form-control">
                                            </div>

                                            <div class="mt-3 text-center bg-light p-3 rounded-3">
                                                <p class="small text-muted mb-2">Gambar Saat Ini:</p>
                                                <?php if(!empty($d['gambar']) && file_exists("../upload/".$d['gambar'])){ ?>
                                                    <img src="../upload/<?= htmlspecialchars($d['gambar']) ?>" class="rounded shadow-sm" width="120" style="object-fit: cover;">
                                                <?php } else { ?>
                                                    <span class="text-muted small">Tidak ada gambar</span>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" name="edit" class="btn-premium-primary"><i class="fa fa-save"></i> Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php 
                        $modals .= ob_get_clean();
                        } 
                        ?>
                    </tbody>
                </table>
            </div>

            <?= $modals ?>

        </div>
    </div>

    <!-- MODAL TAMBAH -->
    <div class="modal fade" id="tambah" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold"><i class="fa fa-box-open me-2 text-primary"></i> Tambah Produk Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-start">
                        <div class="mb-3">
                            <label class="form-label-muted">Nama Produk</label>
                            <input type="text" name="nama" class="form-control" placeholder="Contoh: Nasi Liwet Gurih" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label-muted">Deskripsi Singkat</label>
                            <textarea name="deskripsi" class="form-control" rows="3" placeholder="Masukkan deskripsi produk..."></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label-muted">Harga (Rp)</label>
                            <input type="number" name="harga" class="form-control" placeholder="Contoh: 25000" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label-muted">Upload Gambar</label>
                            <input type="file" name="gambar" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah" class="btn-premium-primary"><i class="fa fa-save"></i> Simpan</button>
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
            title: 'Yakin hapus produk ini?',
            text: "Seluruh rincian pemesanan pelanggan yang mencantumkan produk ini juga akan terhapus!",
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
            Swal.fire({ title: 'Berhasil!', text: 'Produk berhasil ditambahkan.', icon: 'success', confirmButtonClass: 'btn btn-primary rounded-pill px-4', buttonsStyling: false });
        <?php }elseif($_GET['pesan'] == 'sukses_edit'){ ?>
            Swal.fire({ title: 'Berhasil!', text: 'Produk berhasil diperbarui.', icon: 'success', confirmButtonClass: 'btn btn-primary rounded-pill px-4', buttonsStyling: false });
        <?php }elseif($_GET['pesan'] == 'sukses_hapus'){ ?>
            Swal.fire({ title: 'Berhasil!', text: 'Produk berhasil dihapus.', icon: 'success', confirmButtonClass: 'btn btn-primary rounded-pill px-4', buttonsStyling: false });
        <?php } ?>
    </script>
    <?php } ?>

</body>
</html>