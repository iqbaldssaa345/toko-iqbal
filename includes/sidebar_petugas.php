<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <h4><i class="fa fa-utensils"></i> Panel Petugas</h4>
    </div>
    
    <ul class="sidebar-menu">
        <li>
            <a href="index.php" class="<?= $current_page == 'index.php' ? 'active' : '' ?>">
                <i class="fa fa-home"></i> <span>Dashboard</span>
            </a>
        </li>
        <li>
            <a href="pesanan.php" class="<?= $current_page == 'pesanan.php' ? 'active' : '' ?>">
                <i class="fa fa-receipt"></i> <span>Data Pesanan</span>
            </a>
        </li>
        <li>
            <a href="pembayaran.php" class="<?= $current_page == 'pembayaran.php' ? 'active' : '' ?>">
                <i class="fa fa-credit-card"></i> <span>Data Pembayaran</span>
            </a>
        </li>
        <li>
            <a href="detail_pesanan.php" class="<?= $current_page == 'detail_pesanan.php' ? 'active' : '' ?>">
                <i class="fa fa-eye"></i> <span>Detail Pesanan</span>
            </a>
        </li>
        <li>
            <a href="alamat.php" class="<?= $current_page == 'alamat.php' ? 'active' : '' ?>">
                <i class="fa fa-map-marker-alt"></i> <span>Alamat User</span>
            </a>
        </li>
        <li>
            <a href="laporan_penjualan.php" class="<?= $current_page == 'laporan_penjualan.php' ? 'active' : '' ?>">
                <i class="fa fa-file-invoice-dollar"></i> <span>Laporan Penjualan</span>
            </a>
        </li>
        <li>
            <a href="../logout.php" class="mt-auto text-danger">
                <i class="fa fa-sign-out-alt"></i> <span>Logout</span>
            </a>
        </li>
    </ul>
</div>
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>
