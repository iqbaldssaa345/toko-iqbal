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
    $jasa = mysqli_real_escape_string($conn,$_POST['nama_jasa']);
    $biaya = intval($_POST['biaya']);
    $estimasi = mysqli_real_escape_string($conn,$_POST['estimasi']);
    if(empty($estimasi)) $estimasi = "1-2 Hari";

    mysqli_query($conn,"INSERT INTO ongkir (nama_jasa,biaya,estimasi)
    VALUES('$jasa','$biaya','$estimasi')");

    header("Location: ongkir.php?pesan=sukses_tambah");
    exit;
}

/* ================= HAPUS ================= */
if(isset($_GET['hapus'])){
    $id = intval($_GET['hapus']);
    mysqli_query($conn,"DELETE FROM ongkir WHERE id='$id'");

    header("Location: ongkir.php?pesan=sukses_hapus");
    exit;
}

/* ================= EDIT ================= */
if(isset($_POST['edit'])){
    $id = intval($_POST['id']);
    $jasa = mysqli_real_escape_string($conn,$_POST['nama_jasa']);
    $biaya = intval($_POST['biaya']);
    $estimasi = mysqli_real_escape_string($conn,$_POST['estimasi']);
    if(empty($estimasi)) $estimasi = "1-2 Hari";

    mysqli_query($conn,"UPDATE ongkir SET
    nama_jasa='$jasa',
    biaya='$biaya',
    estimasi='$estimasi'
    WHERE id='$id'");

    header("Location: ongkir.php?pesan=sukses_edit");
    exit;
}

/* ================= DATA LIST ================= */
$data = mysqli_query($conn,"SELECT * FROM ongkir ORDER BY id ASC");
$total = mysqli_num_rows($data);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Ongkir - Admin</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="../css/dashboard.css" rel="stylesheet">
    
    <style>
        .search-container {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .search-input {
            border-radius: 30px;
            padding: 8px 18px;
            border: 1px solid rgba(0, 0, 0, 0.08);
            font-size: 0.95rem;
            width: 250px;
        }

        .search-input:focus {
            box-shadow: 0 0 0 3px var(--gold-light);
            border-color: var(--gold);
            outline: none;
        }

        @media(max-width:768px){
            .search-container {
                width: 100%;
                flex-direction: column;
                align-items: stretch;
            }
            .search-input {
                width: 100%;
            }
        }
    </style>
    
    <script>
    function cari(){
        let input = document.getElementById("search").value.toLowerCase();
        let rows = document.querySelectorAll("tbody tr");

        rows.forEach(row=>{
            let jasa = row.children[1].innerText.toLowerCase();
            row.style.display = jasa.includes(input) ? "" : "none";
        });
    }
    </script>
</head>

<body>

    <!-- SIDEBAR -->
    <?php include '../includes/sidebar_admin.php'; ?>

    <!-- MAIN CONTENT -->
    <div class="main-content">

        <!-- TOPBAR -->
        <?php 
        $topbar_title = "Data Ongkos Kirim";
        $user_name = $nama_admin;
        $topbar_icon = '<i class="fa fa-truck text-primary"></i>';
        include '../includes/topbar.php';
        ?>

        <!-- STAT CARD -->
        <div class="row mb-4">
            <div class="col-lg-4 col-md-6">
                <div class="stat-card stat-card-bg-3" style="min-height: 120px; padding: 20px;">
                    <div class="d-flex align-items-center gap-3">
                        <i class="fa fa-map-location-dot" style="font-size: 3rem; opacity: 0.4;"></i>
                        <div>
                            <p class="stat-card-title mb-1">Total Jasa Pengiriman</p>
                            <div class="stat-card-val fs-3"><?= $total ?> Tersedia</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CARD BOX -->
        <div class="card-premium">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                <h5 class="card-title-premium mb-0"><i class="fa fa-list"></i> Daftar Jasa Kurir Pengiriman</h5>

                <div class="search-container">
                    <input type="text" id="search" onkeyup="cari()" placeholder="Cari jasa pengiriman..." class="search-input">
                    <button class="btn-premium-primary" data-bs-toggle="modal" data-bs-target="#tambah">
                        <i class="fa fa-plus"></i> Tambah Ongkir
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table-premium">
                    <thead>
                        <tr>
                            <th class="text-center" width="8%">No</th>
                            <th>Jasa Pengiriman</th>
                            <th>Biaya Pengiriman</th>
                            <th>Estimasi Waktu</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no=1; while($d = mysqli_fetch_array($data)){ 
                            $est_val = isset($d['estimasi']) && !empty($d['estimasi']) ? $d['estimasi'] : '1-2 Hari';
                            
                            $icon = 'fa-motorcycle';
                            $nama_lower = strtolower($d['nama_jasa']);
                            if(strpos($nama_lower, 'mobil') !== false || strpos($nama_lower, 'cargo') !== false || strpos($nama_lower, 'bulk') !== false) {
                                $icon = 'fa-truck-field';
                            } elseif(strpos($nama_lower, 'jne') !== false || strpos($nama_lower, 'j&t') !== false || strpos($nama_lower, 'sicepat') !== false) {
                                $icon = 'fa-box-archive';
                            } elseif(strpos($nama_lower, 'gosend') !== false || strpos($nama_lower, 'grab') !== false) {
                                $icon = 'fa-bolt';
                            }
                        ?>
                        <tr>
                            <td class="text-center fw-bold text-muted"><?= $no++ ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-light p-2 rounded-circle text-info d-flex justify-content-center align-items-center" style="width: 40px; height: 40px;">
                                        <i class="fa <?= $icon ?>"></i>
                                    </div>
                                    <span class="price-badge" style="background-color:#eef2f7; color:#243b55; font-weight:600;"><?= htmlspecialchars($d['nama_jasa']) ?></span>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">Rp <?= number_format($d['biaya'], 0, ',', '.') ?></div>
                            </td>
                            <td>
                                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">
                                    <i class="fa fa-clock me-1"></i> <?= htmlspecialchars($est_val) ?>
                                </span>
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
                                    <form method="POST">
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold"><i class="fa fa-edit me-2 text-warning"></i> Edit Ongkir</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body text-start">
                                            <input type="hidden" name="id" value="<?= $d['id'] ?>">
                                            
                                            <div class="mb-3">
                                                <label class="form-label-muted">Nama Jasa Pengiriman</label>
                                                <input type="text" name="nama_jasa" class="form-control" value="<?= htmlspecialchars($d['nama_jasa']) ?>" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label-muted">Biaya (Rp)</label>
                                                <input type="number" name="biaya" class="form-control" value="<?= $d['biaya'] ?>" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label-muted">Estimasi Pengiriman (Hari)</label>
                                                <input type="text" name="estimasi" class="form-control" value="<?= htmlspecialchars($est_val) ?>" placeholder="Contoh: 1 Hari, 1-2 Hari, 2-3 Hari" required>
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
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold"><i class="fa fa-plus-circle me-2 text-primary"></i> Tambah Ongkir Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-start">
                        <div class="mb-3">
                            <label class="form-label-muted">Nama Jasa Pengiriman</label>
                            <input type="text" name="nama_jasa" class="form-control" placeholder="Contoh: Kurir Toko, GoSend, J&T" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label-muted">Biaya (Rp)</label>
                            <input type="number" name="biaya" class="form-control" placeholder="Contoh: 15000" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label-muted">Estimasi Pengiriman (Hari)</label>
                            <input type="text" name="estimasi" class="form-control" placeholder="Contoh: 1 Hari, 1-2 Hari" value="1-2 Hari" required>
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
            title: 'Yakin hapus data ongkir ini?',
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
            Swal.fire({ title: 'Berhasil!', text: 'Jasa ongkir berhasil ditambahkan.', icon: 'success', confirmButtonClass: 'btn btn-primary rounded-pill px-4', buttonsStyling: false });
        <?php }elseif($_GET['pesan'] == 'sukses_edit'){ ?>
            Swal.fire({ title: 'Berhasil!', text: 'Jasa ongkir berhasil diperbarui.', icon: 'success', confirmButtonClass: 'btn btn-primary rounded-pill px-4', buttonsStyling: false });
        <?php }elseif($_GET['pesan'] == 'sukses_hapus'){ ?>
            Swal.fire({ title: 'Berhasil!', text: 'Jasa ongkir berhasil dihapus.', icon: 'success', confirmButtonClass: 'btn btn-primary rounded-pill px-4', buttonsStyling: false });
        <?php } ?>
    </script>
    <?php } ?>

</body>
</html>