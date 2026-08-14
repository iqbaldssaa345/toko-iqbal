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

// Query List Pesanan dengan Detail Lengkap Alamat, Kurir, Estimasi, & Status Pengiriman
$q_pesanan = mysqli_query($conn, "
    SELECT 
        p.id, p.tanggal, p.total, p.status_pengiriman,
        u.username, 
        a.nama_penerima, a.kota, a.alamat,
        o.nama_jasa, o.biaya as biaya_ongkir, o.estimasi as estimasi_pengiriman,
        pem.metode, pem.status as status_pembayaran
    FROM pesanan p
    LEFT JOIN users u ON p.user_id = u.id
    LEFT JOIN alamat a ON p.alamat_id = a.id
    LEFT JOIN ongkir o ON p.ongkir_id = o.id
    LEFT JOIN pembayaran pem ON p.id = pem.pesanan_id
    WHERE DATE(p.tanggal) BETWEEN '$tgl_mulai' AND '$tgl_selesai'
    ORDER BY p.tanggal DESC
");

// Export ke Excel jika diminta
if(isset($_GET['export']) && $_GET['export'] == 'excel'){
    header("Content-Type: application/vnd.ms-excel; charset=utf-8");
    header("Content-Disposition: attachment; filename=laporan_penjualan_" . $tgl_mulai . "_sd_" . $tgl_selesai . ".xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <style>
            body { font-family: Arial, sans-serif; font-size: 10pt; }
            table { border-collapse: collapse; width: 100%; margin-top: 15px; }
            th, td { border: 1px solid #000000; padding: 7px 9px; text-align: left; vertical-align: middle; }
            th { background-color: #d4af37; color: #ffffff; font-weight: bold; text-align: center; }
            .text-center { text-align: center; }
            .text-right { text-align: right; }
            .header-title { font-size: 16pt; font-weight: bold; text-align: center; margin-bottom: 4px; }
            .header-sub { font-size: 11pt; text-align: center; margin-bottom: 15px; }
            .summary-box td { background-color: #f2f2f2; font-weight: bold; }
            .total-row td { background-color: #fff3cd; font-weight: bold; font-size: 11pt; }
        </style>
    </head>
    <body>
        <div class="header-title">CATERING IBU IQBAL</div>
        <div class="header-sub">LAPORAN PENJUALAN TRANSAKSI KEUANGAN &amp; ESTIMASI PENGIRIMAN<br>Periode: <?= date('d/m/Y', strtotime($tgl_mulai)) ?> s/d <?= date('d/m/Y', strtotime($tgl_selesai)) ?></div>
        
        <table>
            <tr class="summary-box">
                <td colspan="4">Pemasukan Lunas: Rp <?= number_format($total_lunas, 0, ',', '.') ?></td>
                <td colspan="5">Total Omset Penjualan: Rp <?= number_format($total_kotor, 0, ',', '.') ?></td>
                <td colspan="2">Volume Transaksi: <?= $total_transaksi ?> Pesanan</td>
            </tr>
        </table>

        <br>

        <table>
            <thead>
                <tr>
                    <th width="4%">No</th>
                    <th>ID Pesanan</th>
                    <th>Tanggal Order</th>
                    <th>Nama Pelanggan</th>
                    <th>Alamat Kirim &amp; Kota</th>
                    <th>Jasa Kurir Pengiriman</th>
                    <th>Estimasi Sampai</th>
                    <th>Status Pengiriman</th>
                    <th>Metode Bayar</th>
                    <th>Status Bayar</th>
                    <th class="text-right">Total Pembayaran (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                $grand_total = 0;
                if(mysqli_num_rows($q_pesanan) > 0) {
                    while($d = mysqli_fetch_assoc($q_pesanan)){ 
                        $formatted_date = date('d/m/Y H:i', strtotime($d['tanggal']));
                        $invoice_id = sprintf('#PES-%04d', $d['id']);
                        $total_val = $d['total'] ? intval($d['total']) : 0;
                        $grand_total += $total_val;
                        $penerima_kota = $d['nama_penerima'] ? $d['nama_penerima'] . ' (' . $d['kota'] . ')' : '-';
                        $kurir_info = $d['nama_jasa'] ? $d['nama_jasa'] : '-';
                        $est_info = $d['estimasi_pengiriman'] ? $d['estimasi_pengiriman'] : '1-2 Hari';
                        $st_pengiriman = $d['status_pengiriman'] ? $d['status_pengiriman'] : 'Proses (Estimasi)';
                        $st_bayar = $d['status_pembayaran'] ? strtoupper($d['status_pembayaran']) : 'PENDING';
                ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td><?= $invoice_id ?></td>
                    <td><?= $formatted_date ?></td>
                    <td><?= htmlspecialchars($d['username'] ? $d['username'] : 'Tamu / Akun Dihapus') ?></td>
                    <td><?= htmlspecialchars($penerima_kota) ?></td>
                    <td><?= htmlspecialchars($kurir_info) ?></td>
                    <td><?= htmlspecialchars($est_info) ?></td>
                    <td><?= htmlspecialchars($st_pengiriman) ?></td>
                    <td><?= htmlspecialchars($d['metode'] ? $d['metode'] : '-') ?></td>
                    <td><?= htmlspecialchars($st_bayar) ?></td>
                    <td class="text-right"><?= number_format($total_val, 0, ',', '.') ?></td>
                </tr>
                <?php 
                    }
                ?>
                <tr class="total-row">
                    <td colspan="10" style="text-align: right;">TOTAL KESELURUHAN PENJUALAN PERIODE INI:</td>
                    <td class="text-right">Rp <?= number_format($grand_total, 0, ',', '.') ?></td>
                </tr>
                <?php
                } else { 
                ?>
                <tr>
                    <td colspan="11" class="text-center">Tidak ada catatan transaksi penjualan pada rentang tanggal ini.</td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </body>
    </html>
    <?php
    exit;
}
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

        .btn-action-excel {
            height: 48px;
            border-radius: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background-color: #1d6f42;
            color: #ffffff !important;
            border: 1.5px solid #107c41;
            transition: all 0.3s ease;
        }

        .btn-action-excel:hover {
            background-color: #107c41;
            color: #ffffff !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(16, 124, 65, 0.3);
        }

        .btn-action-saring:hover {
            transform: translateY(-2px);
        }

        /* Printing adjustments for PDF Export & Print */
        @media print {
            @page {
                size: A4 portrait;
                margin: 12mm 10mm 15mm 10mm;
            }
            body {
                background: #ffffff !important;
                color: #000000 !important;
                font-family: Arial, Helvetica, sans-serif !important;
                font-size: 10pt !important;
            }
            .sidebar, .topbar, .sidebar-backdrop, .no-print, form, .btn, a.btn-action-excel, .btn-action-saring, .btn-action-cetak, nav, footer, .section-title-dashboard {
                display: none !important;
            }
            .main-content {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }
            .card-premium {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
                background: transparent !important;
            }
            .print-header {
                display: block !important;
                text-align: center;
                margin-bottom: 20px;
                border-bottom: 3px double #000000;
                padding-bottom: 12px;
            }
            .print-header h3 {
                font-size: 20pt !important;
                font-weight: 800 !important;
                color: #000000 !important;
                margin-bottom: 4px;
                text-transform: uppercase;
                letter-spacing: 1px;
            }
            .print-header p {
                font-size: 9.5pt !important;
                color: #222222 !important;
                margin: 2px 0;
            }
            .print-header h4 {
                font-size: 13pt !important;
                font-weight: 700 !important;
                margin-top: 15px;
                margin-bottom: 4px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .row.g-4 {
                margin-bottom: 15px !important;
            }

            .stat-card {
                border: 1px solid #000000 !important;
                background: #f8f9fa !important;
                box-shadow: none !important;
                color: #000000 !important;
                padding: 10px 14px !important;
                border-radius: 6px !important;
            }
            .stat-card-gold, .stat-card-slate, .stat-card-bronze {
                border: 1px solid #000000 !important;
                background: #f8f9fa !important;
                box-shadow: none !important;
                color: #000000 !important;
            }
            .stat-card-val {
                color: #000000 !important;
                font-size: 1.25rem !important;
                font-weight: bold !important;
                text-shadow: none !important;
            }
            .stat-card-title {
                color: #000000 !important;
                font-size: 0.85rem !important;
                font-weight: bold !important;
                margin: 0 !important;
            }
            .stat-card-icon {
                display: none !important;
            }

            .table-premium {
                width: 100% !important;
                border-collapse: collapse !important;
                margin-top: 10px !important;
            }
            .table-premium th, .table-premium td {
                border: 1px solid #000000 !important;
                padding: 7px 9px !important;
                color: #000000 !important;
                font-size: 9.5pt !important;
                vertical-align: middle !important;
            }
            .table-premium th {
                background-color: #e9ecef !important;
                font-weight: 700 !important;
                text-align: left !important;
                text-transform: uppercase !important;
            }
            .price-badge, .badge-luxury {
                background: none !important;
                border: none !important;
                color: #000000 !important;
                padding: 0 !important;
                font-weight: bold !important;
                font-size: 9.5pt !important;
            }
            .badge-luxury .dot {
                display: none !important;
            }

            .print-footer-signatures {
                display: flex !important;
                justify-content: space-between;
                margin-top: 35px;
                page-break-inside: avoid;
            }
            .signature-box {
                text-align: center;
                width: 220px;
                font-size: 10pt;
            }
            .signature-space {
                height: 65px;
            }
        }

        .print-header, .print-footer-signatures {
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
                <div class="col-lg-3 col-md-6">
                    <label class="form-label form-label-muted">Mulai Tanggal</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa fa-calendar-alt"></i></span>
                        <input type="date" name="tgl_mulai" class="form-control border-start-0" value="<?= htmlspecialchars($tgl_mulai) ?>" required>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="form-label form-label-muted">Sampai Tanggal</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa fa-calendar-alt"></i></span>
                        <input type="date" name="tgl_selesai" class="form-control border-start-0" value="<?= htmlspecialchars($tgl_selesai) ?>" required>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 d-flex gap-2 flex-wrap flex-sm-nowrap">
                    <button type="submit" class="btn-premium-primary btn-action-saring flex-grow-1">
                        <i class="fa fa-filter"></i> Saring
                    </button>
                    <button type="button" onclick="window.print()" class="btn-action-cetak flex-grow-1">
                        <i class="fa fa-print"></i> Cetak PDF
                    </button>
                    <a href="?tgl_mulai=<?= urlencode($tgl_mulai) ?>&tgl_selesai=<?= urlencode($tgl_selesai) ?>&export=excel" class="btn-action-excel flex-grow-1 text-decoration-none">
                        <i class="fa fa-file-excel"></i> Cetak Excel
                    </a>
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
                                    <th>Pelanggan &amp; Alamat</th>
                                    <th>Kurir &amp; Estimasi</th>
                                    <th>Status Pengiriman</th>
                                    <th>Metode Bayar</th>
                                    <th>Status Bayar</th>
                                    <th class="text-end">Total Pembayaran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                if(mysqli_num_rows($q_pesanan) > 0) {
                                    while($d = mysqli_fetch_assoc($q_pesanan)){ 
                                        $formatted_date = date('d M Y, H:i', strtotime($d['tanggal']));
                                        $invoice_id = sprintf('#PES-%04d', $d['id']);
                                        $est_display = $d['estimasi_pengiriman'] ? $d['estimasi_pengiriman'] : '1-2 Hari';
                                        $st_pengiriman = $d['status_pengiriman'] ? $d['status_pengiriman'] : 'Proses (Estimasi)';
                                ?>
                                <tr>
                                    <td><strong><?= $invoice_id ?></strong></td>
                                    <td><span class="text-muted" style="font-size: 0.85rem;"><?= $formatted_date ?></span></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="bg-light p-2 rounded-circle text-primary d-flex align-items-center justify-content-center no-print" style="width: 32px; height: 32px;">
                                                <i class="fa fa-user" style="font-size: 0.8rem;"></i>
                                            </div>
                                            <div>
                                                <div style="font-weight: 600;"><?= htmlspecialchars($d['username'] ? $d['username'] : 'Tamu / Akun Dihapus') ?></div>
                                                <?php if($d['nama_penerima']) { ?>
                                                <div class="small text-muted" style="font-size: 0.8rem;"><i class="fa fa-map-marker-alt text-danger me-1"></i><?= htmlspecialchars($d['nama_penerima']) ?> (<?= htmlspecialchars($d['kota']) ?>)</div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if($d['nama_jasa']) { ?>
                                            <div class="fw-semibold" style="font-size: 0.88rem;"><?= htmlspecialchars($d['nama_jasa']) ?></div>
                                            <div class="small text-muted" style="font-size: 0.8rem;"><i class="fa fa-clock text-warning me-1"></i><?= htmlspecialchars($est_display) ?></div>
                                        <?php } else { ?>
                                            <span class="text-muted small">-</span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php if($st_pengiriman == 'Sampai') { ?>
                                            <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle px-3 py-2 rounded-pill fw-semibold" style="font-size: 0.78rem;">
                                                <i class="fa fa-check-circle me-1"></i> Sampai
                                            </span>
                                        <?php } else { ?>
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-2 rounded-pill fw-semibold" style="font-size: 0.78rem;">
                                                <i class="fa fa-truck-fast me-1"></i> <?= htmlspecialchars($st_pengiriman) ?>
                                            </span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <span class="text-dark-emphasis" style="font-size: 0.88rem; font-weight: 500;"><?= htmlspecialchars($d['metode'] ? $d['metode'] : '-') ?></span>
                                    </td>
                                    <td>
                                        <span class="badge-luxury <?= $d['status_pembayaran'] == 'lunas' ? 'lunas' : 'pending' ?>">
                                            <span class="dot"></span>
                                            <?= strtoupper(htmlspecialchars($d['status_pembayaran'] ? $d['status_pembayaran'] : 'pending')) ?>
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
                                    <td colspan="8" class="text-center py-5 text-muted">
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

        <!-- LEMBAR PENGESAHAN / TANDA TANGAN PRINT PDF -->
        <div class="print-footer-signatures">
            <div class="signature-box">
                <p>Mengetahui,</p>
                <p><strong>Manajer Operasional</strong></p>
                <div class="signature-space"></div>
                <p>_______________________</p>
            </div>
            <div class="signature-box">
                <p>Bogor, <?= date('d F Y') ?></p>
                <p><strong>Petugas / Penanggung Jawab</strong></p>
                <div class="signature-space"></div>
                <p><strong>( <?= htmlspecialchars($nama_petugas) ?> )</strong></p>
            </div>
        </div>

    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
