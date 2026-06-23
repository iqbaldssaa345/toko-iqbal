<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <h4><i class="fa fa-utensils"></i> Area Pelanggan</h4>
    </div>
    
    <ul class="sidebar-menu">
        <li>
            <a href="index.php" class="<?= $current_page == 'index.php' ? 'active' : '' ?>">
                <i class="fa fa-home"></i> <span>Dashboard</span>
            </a>
        </li>
        <li>
            <a href="pesan.php" class="<?= $current_page == 'pesan.php' ? 'active' : '' ?>">
                <i class="fa fa-shopping-bag"></i> <span>Pesan Makanan</span>
            </a>
        </li>
        <li>
            <a href="alamat.php" class="<?= $current_page == 'alamat.php' ? 'active' : '' ?>">
                <i class="fa fa-map-marker-alt"></i> <span>Alamat Pengiriman</span>
            </a>
        </li>
        <li>
            <a href="pesanan.php" class="<?= $current_page == 'pesanan.php' ? 'active' : '' ?>">
                <i class="fa fa-shopping-cart"></i> <span>Pesanan Saya</span>
            </a>
        </li>
        <li>
            <a href="pembayaran.php" class="<?= $current_page == 'pembayaran.php' ? 'active' : '' ?>">
                <i class="fa fa-credit-card"></i> <span>Pembayaran</span>
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
