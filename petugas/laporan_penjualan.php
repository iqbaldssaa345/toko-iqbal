<?php
session_start();
if($_SESSION['role'] != "petugas"){
    header("location:../login.php");
    exit;
}
include '../koneksi.php';

// Data Petugas
$id_petugas = $_SESSION['id'];
$q_petugas = mysqli_query($conn, "SELECT username FROM users WHERE id='$id_petugas'");
$d_petugas = mysqli_fetch_assoc($q_petugas);
$nama_petugas = $d_petugas['username'];

// Rentang Tanggal Default: 30 Hari terakhir s/d Hari ini
$tgl_mulai = isset($_GET['tgl_mulai']) && !empty($_GET['tgl_mulai']) ? mysqli_real_escape_string($conn, $_GET['tgl_mulai']) : date('Y-m-d', strtotime('-30 days'));
$tgl_selesai = isset($_GET['tgl_selesai']) && !empty($_GET['tgl_selesai']) ? mysqli_real_escape_string($conn, $_GET['tgl_selesai']) : date('Y-m-d');

// Query Rekapitulasi Keuangan & Transaksi dalam Rentang Tanggal
$q_ringkasan = mysqli_query($conn, "
    SELECT 
        COUNT(p.id) as total_transaksi,
        SUM(p.total) as total_kotor,
        SUM(CASE WHEN pem.status = 'lunas' THEN p.total ELSE 0 END) as total_lunas
    FROM pesanan p
    LEFT JOIN pembayaran pem ON p.id = pem.pesanan_id
    WHERE DATE(p.tanggal) BETWEEN '$tgl_mulai' AND '$tgl_selesai'
");
$d_ringkasan = mysqli_fetch_assoc($q_ringkasan);
$total_transaksi = $d_ringkasan['total_transaksi'] ? $d_ringkasan['total_transaksi'] : 0;
$total_kotor = $d_ringkasan['total_kotor'] ? $d_ringkasan['total_kotor'] : 0;
$total_lunas = $d_ringkasan['total_lunas'] ? $d_ringkasan['total_lunas'] : 0;

// Query List Pesanan
$q_pesanan = mysqli_query($conn, "
    SELECT 
        p.id, p.tanggal, p.total, 
        u.username, 
        pem.metode, pem.status as status_pembayaran
    FROM pesanan p
    LEFT JOIN users u ON p.user_id = u.id
    LEFT JOIN pembayaran pem ON p.id = pem.pesanan_id
    WHERE DATE(p.tanggal) BETWEEN '$tgl_mulai' AND '$tgl_selesai'
    ORDER BY p.tanggal DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan - Panel Petugas</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,400&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../css/dashboard.css" rel="stylesheet">

    <style>
        h1, h2, h3, h4, .price, .print-header h3 {
            font-family: 'Playfair Display', serif;
        }

        /* Customize Status Badges for high-end look */
        .badge-luxury {
            font-size: 0.825rem;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 30px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            letter-spacing: 0.3px;
        }

        .badge-luxury.pending {
            background-color: rgba(245, 159, 0, 0.08);
            border: 1.5px solid rgba(245, 159, 0, 0.25);
            color: #f59f00;
        }

        .badge-luxury.lunas {
            background-color: rgba(55, 178, 77, 0.08);
            border: 1.5px solid rgba(55, 178, 77, 0.25);
            color: #37b24d;
        }

        .badge-luxury .dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            display: inline-block;
        }

        .badge-luxury.pending .dot {
            background-color: #f59f00;
            box-shadow: 0 0 8px #f59f00;
        }

        .badge-luxury.lunas .dot {
            background-color: #37b24d;
            box-shadow: 0 0 8px #37b24d;
        }

        /* Clean Buttons for Filter Control */
        .btn-action-saring {
            height: 48px;
            border-radius: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-action-cetak {
            height: 48px;
            border-radius: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background-color: var(--dark);
            color: var(--gold) !important;
            border: 1.5px solid var(--gold);
            transition: all 0.3s ease;
        }

        .btn-action-cetak:hover {
            background-color: var(--gold);
            color: var(--dark) !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(212, 175, 55, 0.2);
        }

        .btn-action-saring:hover {
            transform: translateY(-2px);
        }

        /* Printing adjustments */
        @media print {
            body {
                background: #ffffff !important;
                color: #000000 !important;
            }
            .sidebar, .topbar, .sidebar-backdrop, .no-print, form, .btn {
                display: none !important;
            }
            .main-content {
                margin-left: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }
            .card-premium {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                background: transparent !important;
            }
            .print-header {
                display: block !important;
                text-align: center;
                margin-bottom: 35px;
                border-bottom: 2.5px solid #1a1a1d;
                padding-bottom: 20px;
            }
            .print-header h3 {
                font-size: 26pt !important;
                color: #1a1a1d !important;
                margin-bottom: 6px;
            }
            .table-premium th {
                border-bottom: 2px solid #000 !important;
                color: #000000 !important;
                font-weight: 700 !important;
                text-transform: uppercase !important;
            }
            .table-premium td {
                border-bottom: 1px solid #ccc !important;
                color: #000000 !important;
                padding: 12px 10px !important;
            }
            .price-badge {
                background: none !important;
                color: #000 !important;
                padding: 0 !important;
                font-weight: 700 !important;
            }
            .badge-luxury {
                border: none !important;
                background: none !important;
                padding: 0 !important;
                font-weight: 700 !important;
                color: #000000 !important;
            }
            .badge-luxury .dot {
                display: none !important;
            }
            .stat-card-gold, .stat-card-slate, .stat-card-bronze {
                border: 1px solid #000 !important;
                background: none !important;
                box-shadow: none !important;
                color: #000 !important;
                padding: 15px !important;
                border-radius: 8px !important;
            }
            .stat-card-gold .stat-card-val, 
            .stat-card-slate .stat-card-val, 
            .stat-card-bronze .stat-card-val {
                color: #000 !important;
                text-shadow: none !important;
                font-size: 1.8rem !important;
            }
            .stat-card-gold .stat-card-title, 
            .stat-card-slate .stat-card-title, 
            .stat-card-bronze .stat-card-title {
                color: #000 !important;
                font-weight: bold !important;
            }
            .stat-card-icon {
                display: none !important;
            }
        }

        .print-header {
            display: none;
        }
    </style>
</head>

<body>

    <!-- SIDEBAR -->
    <?php include '../includes/sidebar_petugas.php'; ?>

    <!-- MAIN CONTENT -->
    <div class="main-content">

        <!-- TOPBAR -->
        <?php 
        $topbar_title = "Laporan Penjualan";
        $user_name = $nama_petugas;
        $topbar_icon = '<i class="fa fa-file-invoice-dollar text-primary"></i>';
        include '../includes/topbar.php';
        ?>

        <!-- KOP SURAT PRINT VIEW -->
        <div class="print-header">
            <h3>Catering Ibu Iqbal</h3>
            <p style="margin: 0; font-size: 10pt; letter-spacing: 0.5px;">Jalan Puri Nirwana 1, Cibinong, Bogor &bull; Telp: +62 812 3456 7890</p>
            <hr style="margin-top: 15px; border: 0; border-top: 1px solid #1a1a1d;">
            <h4 style="margin-top: 15px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; font-size: 14pt;">Laporan Transaksi Penjualan</h4>
            <p style="margin: 5px 0 0 0; font-size: 10pt; font-style: italic;">
                Periode Pencarian: <strong><?= date('d F Y', strtotime($tgl_mulai)) ?></strong> s/d <strong><?= date('d F Y', strtotime($tgl_selesai)) ?></strong>
            </p>
        </div>

        <!-- FORM FILTER (no-print) -->
        <div class="card-premium no-print mb-5">
            <div class="d-flex align-items-center gap-2 mb-4">
                <div class="bg-light p-2 rounded-circle text-primary d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                    <i class="fa fa-sliders" style="font-size: 0.9rem;"></i>
                </div>
                <h5 class="card-title-premium mb-0" style="font-size: 1.05rem;">Saring Periode Laporan</h5>
            </div>
            
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-lg-4 col-md-6">
                    <label class="form-label form-label-muted">Mulai Tanggal</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa fa-calendar-alt"></i></span>
                        <input type="date" name="tgl_mulai" class="form-control border-start-0" value="<?= htmlspecialchars($tgl_mulai) ?>" required>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <label class="form-label form-label-muted">Sampai Tanggal</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa fa-calendar-alt"></i></span>
                        <input type="date" name="tgl_selesai" class="form-control border-start-0" value="<?= htmlspecialchars($tgl_selesai) ?>" required>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12 d-flex gap-2">
                    <button type="submit" class="btn-premium-primary btn-action-saring flex-grow-1">
                        <i class="fa fa-filter"></i> Saring Data
                    </button>
                    <button type="button" onclick="window.print()" class="btn-action-cetak flex-grow-1">
                        <i class="fa fa-print"></i> Cetak PDF
                    </button>
                </div>
            </form>
        </div>

        <!-- REKAP KEUANGAN LAPORAN -->
        <h6 class="section-title-dashboard"><i class="fa-solid fa-chart-pie"></i> Ringkasan Hasil Laporan</h6>
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="stat-card stat-card-gold">
                    <div class="stat-card-val">Rp <?= number_format($total_lunas, 0, ',', '.') ?></div>
                    <p class="stat-card-title">Pemasukan Lunas</p>
                    <i class="fa-solid fa-wallet stat-card-icon"></i>
                </div>
            </div>

            <div class="col-md-4">
                <div class="stat-card stat-card-slate">
                    <div class="stat-card-val">Rp <?= number_format($total_kotor, 0, ',', '.') ?></div>
                    <p class="stat-card-title">Omset Penjualan</p>
                    <i class="fa-solid fa-chart-line stat-card-icon"></i>
                </div>
            </div>

            <div class="col-md-4">
                <div class="stat-card stat-card-bronze">
                    <div class="stat-card-val"><?= $total_transaksi ?></div>
                    <p class="stat-card-title">Volume Transaksi</p>
                    <i class="fa-solid fa-receipt stat-card-icon"></i>
                </div>
            </div>
        </div>

        <!-- DATA TRANSAKSI -->
        <div class="row">
            <div class="col-12">
                <div class="card-premium">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title-premium mb-0">
                            <i class="fa fa-list text-warning"></i> Rincian Penjualan
                        </h5>
                        <span class="badge bg-light text-dark px-3 py-2 border rounded-pill no-print" style="font-size: 0.8rem; font-weight: 500;">
                            Menampilkan <?= mysqli_num_rows($q_pesanan) ?> pesanan
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="table-premium">
                            <thead>
                                <tr>
                                    <th>ID Pesanan</th>
                                    <th>Tanggal Order</th>
                                    <th>Nama Pelanggan</th>
                                    <th>Metode Bayar</th>
                                    <th>Status</th>
                                    <th class="text-end">Total Pembayaran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                if(mysqli_num_rows($q_pesanan) > 0) {
                                    while($d = mysqli_fetch_assoc($q_pesanan)){ 
                                        $formatted_date = date('d M Y, H:i', strtotime($d['tanggal']));
                                        $invoice_id = sprintf('#PES-%04d', $d['id']);
                                ?>
                                <tr>
                                    <td><strong><?= $invoice_id ?></strong></td>
                                    <td><span class="text-muted" style="font-size: 0.9rem;"><?= $formatted_date ?></span></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="bg-light p-2 rounded-circle text-primary d-flex align-items-center justify-content-center no-print" style="width: 32px; height: 32px;">
                                                <i class="fa fa-user" style="font-size: 0.8rem;"></i>
                                            </div>
                                            <span style="font-weight: 600;"><?= htmlspecialchars($d['username'] ? $d['username'] : 'Tamu / Akun Dihapus') ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-dark-emphasis" style="font-size: 0.9rem; font-weight: 500;"><?= htmlspecialchars($d['metode'] ? $d['metode'] : '-') ?></span>
                                    </td>
                                    <td>
                                        <span class="badge-luxury <?= $d['status_pembayaran'] == 'lunas' ? 'lunas' : 'pending' ?>">
                                            <span class="dot"></span>
                                            <?= strtoupper(htmlspecialchars($d['status_pembayaran'])) ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <span class="price-badge">Rp <?= number_format($d['total'] ? $d['total'] : 0, 0, ',', '.') ?></span>
                                    </td>
                                </tr>
                                <?php 
                                    }
                                } else { 
                                ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fa fa-box-open d-block mb-3" style="font-size: 2.2rem; opacity: 0.3;"></i>
                                        Tidak ada catatan transaksi penjualan pada rentang tanggal ini.
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
