<!-- TOPBAR -->
<div class="topbar">
    <div class="topbar-title">
        <button class="sidebar-toggle-btn" id="sidebarToggle" onclick="toggleSidebar()">
            <i class="fa fa-bars"></i>
        </button>
        <h5><?= isset($topbar_icon) ? $topbar_icon : '<i class="fa fa-chart-pie"></i>' ?> <?= htmlspecialchars($topbar_title) ?></h5>
    </div>
    
    <div class="topbar-profile">
        <div class="topbar-user">
            <i class="fa fa-user-circle"></i> <span><?= htmlspecialchars($user_name) ?></span>
        </div>
    </div>
</div>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    if (sidebar && backdrop) {
        sidebar.classList.toggle('show');
        backdrop.classList.toggle('show');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const backdrop = document.getElementById('sidebarBackdrop');
    if (backdrop) {
        backdrop.addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            if (sidebar) sidebar.classList.remove('show');
            backdrop.classList.remove('show');
        });
    }
});
</script>
