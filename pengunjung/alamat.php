<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role']!="pengunjung"){
    header("location:../login.php");
    exit;
}
include '../koneksi.php';

$user_id = $_SESSION['id'];
$q_pengunjung = mysqli_query($conn, "SELECT username FROM users WHERE id='$user_id'");
$d_pengunjung = mysqli_fetch_assoc($q_pengunjung);
$nama_pengunjung = $d_pengunjung['username'];

/* TAMBAH */
if(isset($_POST['tambah'])){
    $nama = mysqli_real_escape_string($conn,$_POST['nama']);
    $alamat = mysqli_real_escape_string($conn,$_POST['alamat']);
    $kota = mysqli_real_escape_string($conn,$_POST['kota']);

    if($nama && $alamat && $kota){
        mysqli_query($conn,"INSERT INTO alamat(user_id,nama_penerima,alamat,kota)
        VALUES('$user_id','$nama','$alamat','$kota')");
    }

    header("location:alamat.php?pesan=sukses_tambah");
    exit;
}

/* HAPUS */
if(isset($_GET['hapus'])){
    $id = intval($_GET['hapus']);
    mysqli_query($conn,"DELETE FROM alamat WHERE id='$id' AND user_id='$user_id'");
    header("location:alamat.php?pesan=sukses_hapus");
    exit;
}

$data = mysqli_query($conn,"SELECT * FROM alamat WHERE user_id='$user_id' ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alamat Saya - Catering Ibu Iqbal</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/dashboard.css" rel="stylesheet">
    
    <style>
        .alamat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }

        .alamat-box {
            background: white;
            border: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 18px;
            padding: 25px;
            position: relative;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.01);
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .alamat-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(212,175,55,0.08);
            border-color: rgba(212,175,55,0.2);
        }

        .alamat-icon {
            width: 48px;
            height: 48px;
            background: var(--gold-light);
            color: var(--gold-dark);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            margin-bottom: 15px;
        }

        .alamat-box h6 {
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 8px;
            font-size: 1.1rem;
        }

        .alamat-box p {
            color: #8c8c9a;
            margin-bottom: 15px;
            font-size: 0.95rem;
            line-height: 1.5;
            flex-grow: 1;
        }

        .kota-badge {
            background: var(--bg-light);
            color: var(--dark);
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
            align-self: flex-start;
            border: 1px solid rgba(0,0,0,0.03);
        }

        .btn-hapus-alamat {
            position: absolute;
            top: 20px;
            right: 20px;
            background: #ffe3e3;
            color: #f03e3e;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .btn-hapus-alamat:hover {
            background: #f03e3e;
            color: white;
            transform: scale(1.05);
        }
    </style>
</head>

<body>

    <!-- SIDEBAR -->
    <?php include '../includes/sidebar_pengunjung.php'; ?>

    <!-- MAIN CONTENT -->
    <div class="main-content">

        <!-- TOPBAR -->
        <?php 
        $topbar_title = "Alamat Pengiriman";
        $user_name = $nama_pengunjung;
        $topbar_icon = '<i class="fa fa-map-marker-alt text-primary"></i>';
        include '../includes/topbar.php';
        ?>

        <div class="row">
            <!-- FORM TAMBAH -->
            <div class="col-lg-4 mb-4">
                <div class="card-premium">
                    <h5 class="card-title-premium"><i class="fa fa-plus-circle"></i> Tambah Alamat</h5>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label-muted">Nama Penerima</label>
                            <input type="text" name="nama" placeholder="Contoh: Rumah Utama, Iqbal" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label-muted">Alamat Lengkap</label>
                            <textarea name="alamat" placeholder="Nama Jalan, Nomor Rumah, RT/RW, Kelurahan" class="form-control" rows="4" required></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="form-label-muted">Kota / Kabupaten</label>
                            <input type="text" name="kota" placeholder="Contoh: Cibinong, Bogor" class="form-control" required>
                        </div>
                        
                        <button name="tambah" class="btn-premium-primary w-100 justify-content-center">
                            <i class="fa fa-save"></i> Simpan Alamat
                        </button>
                    </form>
                </div>
            </div>

            <!-- DAFTAR ALAMAT -->
            <div class="col-lg-8">
                <div class="card-premium">
                    <h5 class="card-title-premium mb-4"><i class="fa fa-list"></i> Daftar Alamat Saya</h5>
                    
                    <div class="alamat-grid">
                        <?php while($d=mysqli_fetch_array($data)){ ?>
                        <div class="alamat-box">
                            <button type="button" class="btn-hapus-alamat" onclick="konfirmasiHapus('?hapus=<?= $d['id'] ?>')" title="Hapus Alamat">
                                <i class="fa fa-trash"></i>
                            </button>

                            <div class="alamat-icon">
                                <i class="fa fa-home"></i>
                            </div>

                            <h6><?= htmlspecialchars($d['nama_penerima']) ?></h6>
                            <p><?= htmlspecialchars($d['alamat']) ?></p>
                            <div class="kota-badge"><i class="fa fa-map-pin me-1 text-danger"></i> <?= htmlspecialchars($d['kota']) ?></div>
                        </div>
                        <?php } ?>
                    </div>

                    <?php if(mysqli_num_rows($data) == 0){ ?>
                        <div class="text-center py-5 text-muted">
                            <i class="fa fa-map-marked-alt fa-3x mb-3 text-light"></i>
                            <p>Belum ada alamat yang tersimpan. Silakan tambahkan alamat baru.</p>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    function konfirmasiHapus(url) {
        Swal.fire({
            title: 'Yakin hapus alamat ini?',
            text: "Alamat yang dihapus tidak bisa dikembalikan!",
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
            Swal.fire({ title: 'Berhasil!', text: 'Alamat berhasil ditambahkan.', icon: 'success', confirmButtonClass: 'btn btn-primary rounded-pill px-4', buttonsStyling: false });
        <?php }elseif($_GET['pesan'] == 'sukses_hapus'){ ?>
            Swal.fire({ title: 'Berhasil!', text: 'Alamat berhasil dihapus.', icon: 'success', confirmButtonClass: 'btn btn-primary rounded-pill px-4', buttonsStyling: false });
        <?php } ?>
    </script>
    <?php } ?>

</body>
</html>