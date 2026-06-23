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
    $user_id        = intval($_POST['user_id']);
    $nama_penerima  = mysqli_real_escape_string($conn,$_POST['nama_penerima']);
    $alamat         = mysqli_real_escape_string($conn,$_POST['alamat']);
    $kota           = mysqli_real_escape_string($conn,$_POST['kota']);

    mysqli_query($conn,"INSERT INTO alamat (user_id,nama_penerima,alamat,kota)
    VALUES('$user_id','$nama_penerima','$alamat','$kota')");

    header("location:alamat.php?pesan=sukses_tambah");
    exit;
}

/* ================= HAPUS ================= */
if(isset($_GET['hapus'])){
    $id = intval($_GET['hapus']);

    mysqli_query($conn,"DELETE FROM alamat WHERE id='$id'");

    header("location:alamat.php?pesan=sukses_hapus");
    exit;
}

/* ================= EDIT ================= */
if(isset($_POST['edit'])){
    $id             = intval($_POST['id']);
    $user_id        = intval($_POST['user_id']);
    $nama_penerima  = mysqli_real_escape_string($conn,$_POST['nama_penerima']);
    $alamat         = mysqli_real_escape_string($conn,$_POST['alamat']);
    $kota           = mysqli_real_escape_string($conn,$_POST['kota']);

    mysqli_query($conn,"UPDATE alamat SET
        user_id='$user_id',
        nama_penerima='$nama_penerima',
        alamat='$alamat',
        kota='$kota'
        WHERE id='$id'
    ");

    header("location:alamat.php?pesan=sukses_edit");
    exit;
}

/* ================= DATA LIST ================= */
$data = mysqli_query($conn,"
    SELECT a.*, u.username 
    FROM alamat a
    JOIN users u ON a.user_id = u.id
    ORDER BY a.id ASC
");

/* Fetch Users Options */
$users_opt = mysqli_query($conn, "SELECT id, username FROM users ORDER BY username ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Alamat - Catering Ibu Iqbal</title>
    
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
        $topbar_title = "Kelola Alamat Pelanggan";
        $user_name = $nama_petugas;
        $topbar_icon = '<i class="fa fa-map-marker-alt text-primary"></i>';
        include '../includes/topbar.php';
        ?>

        <!-- TABLE WRAPPER -->
        <div class="card-premium">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <h5 class="card-title-premium mb-0">
                    <i class="fa fa-map text-danger"></i> Kelola Alamat User
                </h5>
                <button class="btn-premium-primary" data-bs-toggle="modal" data-bs-target="#tambah">
                    <i class="fa fa-plus"></i> Tambah Alamat
                </button>
            </div>

            <div class="table-responsive">
                <table class="table-premium">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Pelanggan</th>
                            <th>Nama Penerima</th>
                            <th>Alamat Lengkap</th>
                            <th>Kota / Kabupaten</th>
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
                                        <i class="fa fa-user"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold"><?= htmlspecialchars($d['username']) ?></div>
                                        <div class="small text-muted">ID: <?= $d['user_id'] ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="text-dark fw-semibold"><?= htmlspecialchars($d['nama_penerima']) ?></span></td>
                            <td>
                                <span class="d-inline-block text-truncate" style="max-width: 250px;" title="<?= htmlspecialchars($d['alamat']) ?>">
                                    <i class="fa fa-map-pin me-1 text-muted"></i> <?= htmlspecialchars($d['alamat']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="kota-badge"><?= htmlspecialchars($d['kota']) ?></span>
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

                        <!-- MODAL EDIT -->
                        <div class="modal fade" id="edit<?= $d['id'] ?>" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <form method="POST">
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold">Edit Alamat #<?= $d['id'] ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body text-start">
                                            <input type="hidden" name="id" value="<?= $d['id'] ?>">
                                            
                                            <div class="mb-3">
                                                <label class="form-label-muted">Pilih Pelanggan (User)</label>
                                                <select name="user_id" class="form-select" required>
                                                    <option value="">-- Pilih User --</option>
                                                    <?php mysqli_data_seek($users_opt, 0); while($u = mysqli_fetch_array($users_opt)){ ?>
                                                    <option value="<?= $u['id'] ?>" <?= ($d['user_id'] == $u['id']) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($u['username']) ?> (ID: <?= $u['id'] ?>)
                                                    </option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label-muted">Nama Penerima</label>
                                                <input type="text" name="nama_penerima" class="form-control" value="<?= htmlspecialchars($d['nama_penerima']) ?>" required>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label-muted">Alamat Lengkap</label>
                                                <textarea name="alamat" class="form-control" rows="3" required><?= htmlspecialchars($d['alamat']) ?></textarea>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label-muted">Kota / Kabupaten</label>
                                                <input type="text" name="kota" class="form-control" value="<?= htmlspecialchars($d['kota']) ?>" required>
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
                        <h5 class="modal-title fw-bold">Tambah Alamat Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-start">
                        <div class="mb-3">
                            <label class="form-label-muted">Pilih Pelanggan (User)</label>
                            <select name="user_id" class="form-select" required>
                                <option value="">-- Pilih User --</option>
                                <?php mysqli_data_seek($users_opt, 0); while($u = mysqli_fetch_array($users_opt)){ ?>
                                <option value="<?= $u['id'] ?>">
                                    <?= htmlspecialchars($u['username']) ?> (ID: <?= $u['id'] ?>)
                                </option>
                                <?php } ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label-muted">Nama Penerima</label>
                            <input type="text" name="nama_penerima" class="form-control" placeholder="Contoh: Rumah Utama, Iqbal" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label-muted">Alamat Lengkap</label>
                            <textarea name="alamat" class="form-control" rows="3" placeholder="Nama Jalan, Blok, RT/RW, Kelurahan" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label-muted">Kota / Kabupaten</label>
                            <input type="text" name="kota" class="form-control" placeholder="Contoh: Cibinong, Bogor" required>
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