<?php
session_start();
if($_SESSION['role']!="petugas"){
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
    $metode     = mysqli_real_escape_string($conn,$_POST['metode']);
    $status     = mysqli_real_escape_string($conn,$_POST['status']);

    mysqli_query($conn,"INSERT INTO pembayaran (pesanan_id,metode,status)
    VALUES('$pesanan_id','$metode','$status')");

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

/* ================= EDIT ================= */
if(isset($_POST['edit'])){
    $id         = intval($_POST['id']);
    $pesanan_id = intval($_POST['pesanan_id']);
    $metode     = mysqli_real_escape_string($conn,$_POST['metode']);
    $status     = mysqli_real_escape_string($conn,$_POST['status']);

    mysqli_query($conn,"UPDATE pembayaran SET
        pesanan_id='$pesanan_id',
        metode='$metode',
        status='$status'
        WHERE id='$id'
    ");

    header("location:pembayaran.php?pesan=sukses_edit");
    exit;
}

/* ================= DATA LIST ================= */
$data = mysqli_query($conn,"
    SELECT pb.*, p.total, u.username 
    FROM pembayaran pb
    JOIN pesanan p ON pb.pesanan_id = p.id
    JOIN users u ON p.user_id = u.id
    ORDER BY pb.id ASC
");

/* Fetch Pesanan Options */
$pesanan_opt = mysqli_query($conn, "SELECT p.id, p.total, u.username FROM pesanan p JOIN users u ON p.user_id = u.id ORDER BY p.id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pembayaran - Catering Ibu Iqbal</title>
    
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
        $topbar_title = "Kelola Pembayaran";
        $user_name = $nama_petugas;
        $topbar_icon = '<i class="fa fa-credit-card text-primary"></i>';
        include '../includes/topbar.php';
        ?>

        <!-- TABLE WRAPPER -->
        <div class="card-premium">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <h5 class="card-title-premium mb-0">
                    <i class="fa fa-money-bill-wave text-success"></i> Kelola Pembayaran Pelanggan
                </h5>
                <button class="btn-premium-primary" data-bs-toggle="modal" data-bs-target="#tambah">
                    <i class="fa fa-plus"></i> Tambah Pembayaran
                </button>
            </div>

            <div class="table-responsive">
                <table class="table-premium">
                    <thead>
                        <tr>
                            <th>ID Bayar</th>
                            <th>ID Pesanan</th>
                            <th>Pelanggan</th>
                            <th>Metode Pembayaran</th>
                            <th>Total Tagihan</th>
                            <th>Status Verifikasi</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($d = mysqli_fetch_array($data)){ ?>
                        <tr>
                            <td><strong>#<?= $d['id']; ?></strong></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-light p-2 rounded-circle text-primary d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                        <i class="fa fa-receipt"></i>
                                    </div>
                                    #<?= htmlspecialchars($d['pesanan_id']) ?>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($d['username']) ?></td>
                            <td>
                                <span class="text-dark fw-medium">
                                    <i class="fa fa-wallet me-1 text-muted"></i> <?= htmlspecialchars($d['metode']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="fw-bold">
                                    Rp <?= number_format($d['total'] ? $d['total'] : 0, 0, ',', '.') ?>
                                </span>
                            </td>
                            <td>
                                <?php if($d['status']=="lunas"){ ?>
                                    <span class="status-badge lunas"><i class="fa fa-check-circle"></i> Lunas</span>
                                <?php } else { ?>
                                    <span class="status-badge pending"><i class="fa fa-clock"></i> Pending</span>
                                <?php } ?>
                            </td>

                            <td class="text-center">
                                <button class="btn-action-premium btn-edit-premium me-1" data-bs-toggle="modal" data-bs-target="#edit<?= $d['id'] ?>" title="Edit">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button type="button" class="btn-action-premium btn-delete-premium" onclick="konfirmasiHapus('pembayaran.php?hapus=<?= $d['id'] ?>')" title="Hapus">
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
                                            <h5 class="modal-title fw-bold">Edit Pembayaran #<?= $d['id'] ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body text-start">
                                            <input type="hidden" name="id" value="<?= $d['id'] ?>">
                                            
                                            <div class="mb-3">
                                                <label class="form-label-muted">Pilih Pesanan</label>
                                                <select name="pesanan_id" class="form-select" required>
                                                    <option value="">-- Pilih Pesanan --</option>
                                                    <?php mysqli_data_seek($pesanan_opt, 0); while($po = mysqli_fetch_array($pesanan_opt)){ ?>
                                                    <option value="<?= $po['id'] ?>" <?= ($d['pesanan_id'] == $po['id']) ? 'selected' : '' ?>>
                                                        #<?= $po['id'] ?> - <?= htmlspecialchars($po['username']) ?> (Rp <?= number_format($po['total']) ?>)
                                                    </option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label-muted">Metode Pembayaran</label>
                                                <select name="metode" class="form-select">
                                                    <option value="Transfer Bank" <?= $d['metode']=='Transfer Bank'?'selected':'' ?>>Transfer Bank</option>
                                                    <option value="E-Wallet" <?= $d['metode']=='E-Wallet'?'selected':'' ?>>E-Wallet</option>
                                                    <option value="COD" <?= $d['metode']=='COD'?'selected':'' ?>>COD</option>
                                                </select>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label-muted">Status Pembayaran</label>
                                                <select name="status" class="form-select">
                                                    <option value="pending" <?= $d['status']=='pending'?'selected':'' ?>>Pending</option>
                                                    <option value="lunas" <?= $d['status']=='lunas'?'selected':'' ?>>Lunas</option>
                                                </select>
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
                        <h5 class="modal-title fw-bold">Tambah Pembayaran Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-start">
                        <div class="mb-3">
                            <label class="form-label-muted">Pilih Pesanan</label>
                            <select name="pesanan_id" class="form-select" required>
                                <option value="">-- Pilih Pesanan --</option>
                                <?php mysqli_data_seek($pesanan_opt, 0); while($po = mysqli_fetch_array($pesanan_opt)){ ?>
                                <option value="<?= $po['id'] ?>">
                                    #<?= $po['id'] ?> - <?= htmlspecialchars($po['username']) ?> (Rp <?= number_format($po['total']) ?>)
                                </option>
                                <?php } ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label-muted">Metode Pembayaran</label>
                            <select name="metode" class="form-select" required>
                                <option value="">-- Pilih Metode --</option>
                                <option value="Transfer Bank">Transfer Bank</option>
                                <option value="E-Wallet">E-Wallet</option>
                                <option value="COD">COD</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label-muted">Status Pembayaran</label>
                            <select name="status" class="form-select" required>
                                <option value="pending">Pending</option>
                                <option value="lunas">Lunas</option>
                            </select>
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