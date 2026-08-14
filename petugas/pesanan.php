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
    $user_id           = intval($_POST['user_id']);
    $alamat_id         = intval($_POST['alamat_id']);
    $ongkir_id         = intval($_POST['ongkir_id']);
    $total             = intval($_POST['total']);
    $status_pengiriman = mysqli_real_escape_string($conn, $_POST['status_pengiriman']);

    mysqli_query($conn,"INSERT INTO pesanan (user_id,alamat_id,ongkir_id,total,status_pengiriman)
    VALUES('$user_id','$alamat_id','$ongkir_id','$total','$status_pengiriman')");

    header("location:pesanan.php?pesan=sukses_tambah");
    exit;
}

/* ================= HAPUS CASCADE ================= */
if(isset($_GET['hapus'])){
    $id = intval($_GET['hapus']);

    mysqli_query($conn,"DELETE FROM detail_pesanan WHERE pesanan_id='$id'");
    mysqli_query($conn,"DELETE FROM pembayaran WHERE pesanan_id='$id'");
    mysqli_query($conn,"DELETE FROM pesanan WHERE id='$id'");

    header("location:pesanan.php?pesan=sukses_hapus");
    exit;
}

/* ================= EDIT ================= */
if(isset($_POST['edit'])){
    $id                = intval($_POST['id']);
    $user_id           = intval($_POST['user_id']);
    $alamat_id         = intval($_POST['alamat_id']);
    $ongkir_id         = intval($_POST['ongkir_id']);
    $total             = intval($_POST['total']);
    $status_pengiriman = mysqli_real_escape_string($conn, $_POST['status_pengiriman']);

    mysqli_query($conn,"UPDATE pesanan SET
        user_id='$user_id',
        alamat_id='$alamat_id',
        ongkir_id='$ongkir_id',
        total='$total',
        status_pengiriman='$status_pengiriman'
        WHERE id='$id'
    ");

    header("location:pesanan.php?pesan=sukses_edit");
    exit;
}

/* ================= DATA LIST ================= */
$data = mysqli_query($conn,"
    SELECT p.*, u.username, a.nama_penerima, a.kota, o.nama_jasa, o.estimasi 
    FROM pesanan p
    JOIN users u ON p.user_id = u.id
    LEFT JOIN alamat a ON p.alamat_id = a.id
    LEFT JOIN ongkir o ON p.ongkir_id = o.id
    ORDER BY p.id ASC
");

/* Fetch Dropdown Data */
$users_opt = mysqli_query($conn, "SELECT id, username FROM users ORDER BY username ASC");
$alamat_opt = mysqli_query($conn, "SELECT a.id, a.nama_penerima, a.kota, u.username FROM alamat a JOIN users u ON a.user_id=u.id ORDER BY u.username ASC");
$ongkir_opt = mysqli_query($conn, "SELECT id, nama_jasa, biaya, estimasi FROM ongkir ORDER BY nama_jasa ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pesanan - Catering Ibu Iqbal</title>
    
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
        $topbar_title = "Kelola Pesanan";
        $user_name = $nama_petugas;
        $topbar_icon = '<i class="fa fa-receipt text-primary"></i>';
        include '../includes/topbar.php';
        ?>

        <!-- TABLE WRAPPER -->
        <div class="card-premium">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <h5 class="card-title-premium mb-0">
                    <i class="fa fa-list"></i> Daftar Pesanan Pelanggan
                </h5>
                <button class="btn-premium-primary" data-bs-toggle="modal" data-bs-target="#tambah">
                    <i class="fa fa-plus"></i> Tambah Pesanan
                </button>
            </div>

            <div class="table-responsive">
                <table class="table-premium">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Pelanggan</th>
                            <th>Alamat Penerima</th>
                            <th>Kurir (Ongkir)</th>
                            <th>Status Pengiriman</th>
                            <th>Tanggal</th>
                            <th>Total Tagihan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($d = mysqli_fetch_array($data)){ 
                            $st_pengiriman = isset($d['status_pengiriman']) && !empty($d['status_pengiriman']) ? $d['status_pengiriman'] : 'Proses (Estimasi)';
                        ?>
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
                            <td>
                                <?php if($d['alamat_id']) { ?>
                                    <div class="fw-semibold"><?= htmlspecialchars($d['nama_penerima']) ?></div>
                                    <div class="small text-muted"><?= htmlspecialchars($d['kota']) ?> (ID: <?= $d['alamat_id'] ?>)</div>
                                <?php } else { ?>
                                    <span class="text-danger small">Belum diisi (NULL)</span>
                                <?php } ?>
                            </td>
                            <td>
                                <?php if($d['ongkir_id']) { 
                                    $o_est = isset($d['estimasi']) && !empty($d['estimasi']) ? $d['estimasi'] : '1-2 Hari';
                                ?>
                                    <div class="fw-semibold"><?= htmlspecialchars($d['nama_jasa']) ?></div>
                                    <div class="small text-muted"><i class="fa fa-clock text-warning me-1"></i><?= htmlspecialchars($o_est) ?></div>
                                <?php } else { ?>
                                    <span class="text-danger small">Belum diisi (NULL)</span>
                                <?php } ?>
                            </td>
                            <td>
                                <?php if($st_pengiriman == 'Sampai') { ?>
                                    <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle px-3 py-2 rounded-pill fw-semibold">
                                        <i class="fa fa-check-circle me-1"></i> Sampai
                                    </span>
                                <?php } else { ?>
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-2 rounded-pill fw-semibold">
                                        <i class="fa fa-truck-fast me-1"></i> <?= htmlspecialchars($st_pengiriman) ?>
                                    </span>
                                <?php } ?>
                            </td>
                            <td><?= htmlspecialchars($d['tanggal']) ?></td>
                            <td>
                                <span class="price-badge">
                                    Rp <?= number_format($d['total'] ? $d['total'] : 0, 0, ',', '.') ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="btn-action-premium btn-edit-premium me-1" data-bs-toggle="modal" data-bs-target="#edit<?= $d['id'] ?>" title="Edit">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button type="button" class="btn-action-premium btn-delete-premium" onclick="konfirmasiHapus('pesanan.php?hapus=<?= $d['id'] ?>')" title="Hapus">
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
                                            <h5 class="modal-title fw-bold">Edit Pesanan #<?= $d['id'] ?></h5>
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
                                                <label class="form-label-muted">Pilih Alamat Pengiriman</label>
                                                <select name="alamat_id" class="form-select" required>
                                                    <option value="">-- Pilih Alamat --</option>
                                                    <?php mysqli_data_seek($alamat_opt, 0); while($a = mysqli_fetch_array($alamat_opt)){ ?>
                                                    <option value="<?= $a['id'] ?>" <?= ($d['alamat_id'] == $a['id']) ? 'selected' : '' ?>>
                                                        [<?= htmlspecialchars($a['username']) ?>] <?= htmlspecialchars($a['nama_penerima']) ?> - <?= htmlspecialchars($a['kota']) ?> (ID: <?= $a['id'] ?>)
                                                    </option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label-muted">Pilih Jasa Ongkir</label>
                                                <select name="ongkir_id" class="form-select" required>
                                                    <option value="">-- Pilih Ongkir --</option>
                                                    <?php mysqli_data_seek($ongkir_opt, 0); while($o = mysqli_fetch_array($ongkir_opt)){ ?>
                                                    <option value="<?= $o['id'] ?>" <?= ($d['ongkir_id'] == $o['id']) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($o['nama_jasa']) ?> - Rp <?= number_format($o['biaya']) ?> (ID: <?= $o['id'] ?>)
                                                    </option>
                                                    <?php } ?>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label-muted">Status Pengiriman</label>
                                                <select name="status_pengiriman" class="form-select" required>
                                                    <option value="Proses (Estimasi)" <?= ($st_pengiriman == 'Proses (Estimasi)') ? 'selected' : '' ?>>🚚 Dalam Pengiriman / Proses (Estimasi)</option>
                                                    <option value="Sampai" <?= ($st_pengiriman == 'Sampai') ? 'selected' : '' ?>>✅ Sampai (Tiba di Tujuan)</option>
                                                </select>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label-muted">Total Harga (Rp)</label>
                                                <input type="number" name="total" class="form-control" value="<?= $d['total'] ?>" required>
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
                        <h5 class="modal-title fw-bold">Tambah Pesanan Baru</h5>
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
                            <label class="form-label-muted">Pilih Alamat Pengiriman</label>
                            <select name="alamat_id" class="form-select" required>
                                <option value="">-- Pilih Alamat --</option>
                                <?php mysqli_data_seek($alamat_opt, 0); while($a = mysqli_fetch_array($alamat_opt)){ ?>
                                <option value="<?= $a['id'] ?>">
                                    [<?= htmlspecialchars($a['username']) ?>] <?= htmlspecialchars($a['nama_penerima']) ?> - <?= htmlspecialchars($a['kota']) ?> (ID: <?= $a['id'] ?>)
                                </option>
                                <?php } ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label-muted">Pilih Jasa Ongkir</label>
                            <select name="ongkir_id" class="form-select" required>
                                <option value="">-- Pilih Ongkir --</option>
                                <?php mysqli_data_seek($ongkir_opt, 0); while($o = mysqli_fetch_array($ongkir_opt)){ ?>
                                <option value="<?= $o['id'] ?>">
                                    <?= htmlspecialchars($o['nama_jasa']) ?> - Rp <?= number_format($o['biaya']) ?> (ID: <?= $o['id'] ?>)
                                </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label-muted">Status Pengiriman</label>
                            <select name="status_pengiriman" class="form-select" required>
                                <option value="Proses (Estimasi)" selected>🚚 Dalam Pengiriman / Proses (Estimasi)</option>
                                <option value="Sampai">✅ Sampai (Tiba di Tujuan)</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label-muted">Total Harga (Rp)</label>
                            <input type="number" name="total" class="form-control" placeholder="Contoh: 35000" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah" class="btn btn-premium-primary">Simpan Pesanan</button>
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
            text: "Seluruh rincian produk dan pembayaran terkait pesanan ini juga akan dihapus permanen!",
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