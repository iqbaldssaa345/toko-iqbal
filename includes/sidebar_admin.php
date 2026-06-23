<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <h4><i class="fa fa-utensils"></i> Catering Ibu Iqbal</h4>
    </div>
    
    <ul class="sidebar-menu">
        <li>
            <a href="index.php" class="<?= $current_page == 'index.php' ? 'active' : '' ?>">
                <i class="fa fa-home"></i> <span>Dashboard</span>
            </a>
        </li>
        <li>
            <a href="users.php" class="<?= $current_page == 'users.php' ? 'active' : '' ?>">
                <i class="fa fa-users"></i> <span>Data User</span>
            </a>
        </li>
        <li>
            <a href="produk.php" class="<?= $current_page == 'produk.php' ? 'active' : '' ?>">
                <i class="fa fa-box"></i> <span>Produk</span>
            </a>
        </li>
        <li>
            <a href="ongkir.php" class="<?= $current_page == 'ongkir.php' ? 'active' : '' ?>">
                <i class="fa fa-truck"></i> <span>Ongkir</span>
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
