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

/* HAPUS */
if(isset($_GET['hapus'])){
    $id = intval($_GET['hapus']);
    mysqli_query($conn,"DELETE FROM testimoni WHERE id='$id'");
    header("location:testimoni.php?pesan=sukses_hapus");
    exit;
}

$data = mysqli_query($conn,"
    SELECT t.*, u.username 
    FROM testimoni t
    JOIN users u ON t.user_id = u.id
    ORDER BY t.tanggal DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moderasi Testimoni - Admin</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="../css/dashboard.css" rel="stylesheet">
</head>

<body>

    <!-- SIDEBAR -->
    <?php include '../includes/sidebar_admin.php'; ?>

    <!-- MAIN CONTENT -->
    <div class="main-content">

        <!-- TOPBAR -->
        <?php 
        $topbar_title = "Moderasi Testimoni";
        $user_name = $nama_admin;
        $topbar_icon = '<i class="fa fa-comments text-primary"></i>';
        include '../includes/topbar.php';
        ?>

        <!-- CARD BOX -->
        <div class="card-premium">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <h5 class="card-title-premium mb-0"><i class="fa fa-star text-warning"></i> Daftar Testimoni Masuk</h5>
            </div>

            <div class="table-responsive">
                <table class="table-premium">
                    <thead>
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th width="15%">Pengguna</th>
                            <th width="15%">Rating Bintang</th>
                            <th>Isi Testimoni</th>
                            <th width="15%">Tanggal Kirim</th>
                            <th width="10%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        if(mysqli_num_rows($data) > 0) {
                            while($d = mysqli_fetch_array($data)){ 
                        ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td><strong><?= htmlspecialchars($d['username']) ?></strong></td>
                            <td>
                                <span style="color: #ffac0a;">
                                    <?php 
                                    $stars = intval($d['bintang']);
                                    for($i=1; $i<=5; $i++) {
                                        if($i <= $stars) {
                                            echo '<i class="fa fa-star"></i>';
                                        } else {
                                            echo '<i class="fa-regular fa-star"></i>';
                                        }
                                    }
                                    ?>
                                </span>
                            </td>
                            <td>
                                <span class="text-muted" style="font-size: 0.95rem; font-style: italic;">
                                    "<?= htmlspecialchars($d['isi_testimoni']) ?>"
                                </span>
                            </td>
                            <td><?= date('d M Y - H:i', strtotime($d['tanggal'])) ?> WIB</td>
                            <td class="text-center">
                                <button type="button" class="btn-action-premium btn-delete-premium" onclick="konfirmasiHapus('?hapus=<?= $d['id'] ?>')" title="Hapus">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php 
                            }
                        } else {
                        ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada testimoni masuk dari pelanggan.</td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    function konfirmasiHapus(url) {
        Swal.fire({
            title: 'Hapus Testimoni?',
            text: "Ulasan ini akan dihapus secara permanen dari sistem dan halaman utama!",
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
    <?php if(isset($_GET['pesan']) && $_GET['pesan'] == 'sukses_hapus'){ ?>
    <script>
        Swal.fire({ title: 'Berhasil!', text: 'Testimoni telah dihapus dari sistem.', icon: 'success', confirmButtonClass: 'btn btn-primary rounded-pill px-4', buttonsStyling: false });
    </script>
    <?php } ?>

</body>
</html>
