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
    $bintang = intval($_POST['bintang']);
    $isi = mysqli_real_escape_string($conn, $_POST['isi_testimoni']);

    if($bintang >= 1 && $bintang <= 5 && !empty($isi)){
        mysqli_query($conn,"INSERT INTO testimoni (user_id, bintang, isi_testimoni) VALUES ('$user_id', '$bintang', '$isi')");
        header("location:testimoni.php?pesan=sukses_tambah");
        exit;
    }
}

/* HAPUS */
if(isset($_GET['hapus'])){
    $id = intval($_GET['hapus']);
    mysqli_query($conn,"DELETE FROM testimoni WHERE id='$id' AND user_id='$user_id'");
    header("location:testimoni.php?pesan=sukses_hapus");
    exit;
}

/* EDIT */
if(isset($_POST['edit'])){
    $id = intval($_POST['id']);
    $bintang = intval($_POST['bintang']);
    $isi = mysqli_real_escape_string($conn, $_POST['isi_testimoni']);

    if($bintang >= 1 && $bintang <= 5 && !empty($isi)){
        mysqli_query($conn,"UPDATE testimoni SET bintang='$bintang', isi_testimoni='$isi' WHERE id='$id' AND user_id='$user_id'");
        header("location:testimoni.php?pesan=sukses_edit");
        exit;
    }
}

$data = mysqli_query($conn,"SELECT * FROM testimoni WHERE user_id='$user_id' ORDER BY tanggal DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testimoni Saya - Catering Ibu Iqbal</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/dashboard.css" rel="stylesheet">
    
    <style>
        .testi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
        }

        .testi-box {
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

        .testi-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(212,175,55,0.08);
            border-color: rgba(212,175,55,0.2);
        }

        .star-rating {
            color: #ffac0a;
            margin-bottom: 12px;
            font-size: 1.1rem;
        }

        .testi-content {
            color: #555;
            font-style: italic;
            line-height: 1.6;
            margin-bottom: 15px;
            flex-grow: 1;
        }

        .testi-date {
            color: #8c8c9a;
            font-size: 0.85rem;
            margin-bottom: 15px;
            display: block;
        }

        .star-select {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
            gap: 5px;
        }

        .star-select input {
            display: none;
        }

        .star-select label {
            font-size: 2rem;
            color: #ccc;
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .star-select input:checked ~ label,
        .star-select label:hover,
        .star-select label:hover ~ label {
            color: #ffac0a;
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
        $topbar_title = "Testimoni Saya";
        $user_name = $nama_pengunjung;
        $topbar_icon = '<i class="fa fa-comment-dots text-primary"></i>';
        include '../includes/topbar.php';
        ?>

        <!-- CARD BOX -->
        <div class="card-premium">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <div>
                    <h5 class="card-title-premium mb-1"><i class="fa fa-star text-gold"></i> Testimoni Pengalaman Anda</h5>
                    <p class="text-muted small mb-0">Ulasan Anda membantu kami menjaga kualitas cita rasa dan pelayanan catering.</p>
                </div>
                <button class="btn-premium-primary" data-bs-toggle="modal" data-bs-target="#tambah">
                    <i class="fa fa-pen-fancy"></i> Tulis Testimoni
                </button>
            </div>

            <!-- TESTIMONI LIST -->
            <div class="testi-grid">
                <?php 
                $modals = "";
                if(mysqli_num_rows($data) > 0) {
                    while($d = mysqli_fetch_array($data)){ 
                ?>
                <div class="testi-box">
                    <div class="star-rating">
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
                    </div>
                    <p class="testi-content">"<?= htmlspecialchars($d['isi_testimoni']) ?>"</p>
                    <span class="testi-date"><i class="fa-regular fa-calendar-alt me-1"></i> <?= date('d F Y - H:i', strtotime($d['tanggal'])) ?> WIB</span>
                    
                    <div class="mt-auto d-flex gap-2">
                        <button class="btn-premium-primary py-2 px-3 rounded-pill btn-sm text-white" style="font-size:0.85rem;" data-bs-toggle="modal" data-bs-target="#edit<?= $d['id'] ?>">
                            <i class="fa fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-outline-danger btn-sm py-2 px-3 rounded-pill" style="font-size:0.85rem;" onclick="konfirmasiHapus('?hapus=<?= $d['id'] ?>')">
                            <i class="fa fa-trash"></i> Hapus
                        </button>
                    </div>
                </div>

                <?php ob_start(); ?>
                <!-- MODAL EDIT -->
                <div class="modal fade" id="edit<?= $d['id'] ?>" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <form method="POST">
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold"><i class="fa fa-edit text-warning me-2"></i> Edit Ulasan Anda</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-start">
                                    <input type="hidden" name="id" value="<?= $d['id'] ?>">
                                    
                                    <div class="mb-4">
                                        <label class="form-label-muted d-block mb-2">Rating Bintang</label>
                                        <div class="star-select">
                                            <input type="radio" id="star5_edit<?= $d['id'] ?>" name="bintang" value="5" <?= $stars == 5 ? 'checked' : '' ?> required />
                                            <label for="star5_edit<?= $d['id'] ?>" class="fa fa-star"></label>
                                            <input type="radio" id="star4_edit<?= $d['id'] ?>" name="bintang" value="4" <?= $stars == 4 ? 'checked' : '' ?> />
                                            <label for="star4_edit<?= $d['id'] ?>" class="fa fa-star"></label>
                                            <input type="radio" id="star3_edit<?= $d['id'] ?>" name="bintang" value="3" <?= $stars == 3 ? 'checked' : '' ?> />
                                            <label for="star3_edit<?= $d['id'] ?>" class="fa fa-star"></label>
                                            <input type="radio" id="star2_edit<?= $d['id'] ?>" name="bintang" value="2" <?= $stars == 2 ? 'checked' : '' ?> />
                                            <label for="star2_edit<?= $d['id'] ?>" class="fa fa-star"></label>
                                            <input type="radio" id="star1_edit<?= $d['id'] ?>" name="bintang" value="1" <?= $stars == 1 ? 'checked' : '' ?> />
                                            <label for="star1_edit<?= $d['id'] ?>" class="fa fa-star"></label>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label-muted">Tulis Ulasan</label>
                                        <textarea name="isi_testimoni" class="form-control" rows="4" placeholder="Tulis masukan, pujian, atau saran mengenai masakan kami..." required><?= htmlspecialchars($d['isi_testimoni']) ?></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" name="edit" class="btn-premium-primary"><i class="fa fa-save"></i> Simpan Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php 
                $modals .= ob_get_clean();
                    }
                } else {
                ?>
                <div class="w-100 text-center py-5 text-muted" style="grid-column: 1 / -1;">
                    <i class="fa-regular fa-comment-dots fa-3x mb-3 text-gold" style="font-size: 3rem;"></i>
                    <p class="mb-0">Anda belum pernah mengirimkan testimoni. Yuk, bagikan kepuasan rasa makanan kami!</p>
                </div>
                <?php } ?>
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
                        <h5 class="modal-title fw-bold"><i class="fa fa-pen-fancy text-primary me-2"></i> Tulis Ulasan Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-start">
                        <div class="mb-4">
                            <label class="form-label-muted d-block mb-2">Rating Bintang</label>
                            <div class="star-select">
                                <input type="radio" id="star5_add" name="bintang" value="5" checked required />
                                <label for="star5_add" class="fa fa-star"></label>
                                <input type="radio" id="star4_add" name="bintang" value="4" />
                                <label for="star4_add" class="fa fa-star"></label>
                                <input type="radio" id="star3_add" name="bintang" value="3" />
                                <label for="star3_add" class="fa fa-star"></label>
                                <input type="radio" id="star2_add" name="bintang" value="2" />
                                <label for="star2_add" class="fa fa-star"></label>
                                <input type="radio" id="star1_add" name="bintang" value="1" />
                                <label for="star1_add" class="fa fa-star"></label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label-muted">Tulis Ulasan</label>
                            <textarea name="isi_testimoni" class="form-control" rows="4" placeholder="Bagikan kepuasan Anda mengenai kualitas rasa masakan, kebersihan, dan pengiriman kami..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah" class="btn-premium-primary"><i class="fa fa-save"></i> Kirim Ulasan</button>
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
            title: 'Hapus Ulasan?',
            text: "Ulasan ini akan dihapus secara permanen dari halaman utama!",
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
            Swal.fire({ title: 'Berhasil!', text: 'Terima kasih! Ulasan Anda berhasil diterbitkan.', icon: 'success', confirmButtonClass: 'btn btn-primary rounded-pill px-4', buttonsStyling: false });
        <?php }elseif($_GET['pesan'] == 'sukses_edit'){ ?>
            Swal.fire({ title: 'Berhasil!', text: 'Ulasan Anda berhasil diperbarui.', icon: 'success', confirmButtonClass: 'btn btn-primary rounded-pill px-4', buttonsStyling: false });
        <?php }elseif($_GET['pesan'] == 'sukses_hapus'){ ?>
            Swal.fire({ title: 'Berhasil!', text: 'Ulasan telah dihapus.', icon: 'success', confirmButtonClass: 'btn btn-primary rounded-pill px-4', buttonsStyling: false });
        <?php } ?>
    </script>
    <?php } ?>

</body>
</html>
