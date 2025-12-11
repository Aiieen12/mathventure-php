<?php
// sidebar-teacher.php
if (!isset($_SESSION)) {
    session_start();
}

$guruNama = $_SESSION['username'] ?? 'cikguDemo';
$guruInisial = strtoupper(substr($guruNama, 0, 1));
$currentPage = $page ?? '';   // dari setiap file: $page = 'attendance' dsb.
?>
<aside class="teacher-sidebar">
    <div class="sidebar-brand">
        <div class="brand-logo">M</div>
        <div class="brand-text">
            <span class="brand-name">Mathventure</span>
            <span class="brand-mode">Teacher Mode</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <a href="dashboard-teacher.php"
           class="nav-link <?php echo ($currentPage === 'dashboard') ? 'active' : ''; ?>">
            <span class="nav-icon">🏠</span>
            <span class="nav-label">Dashboard</span>
        </a>

        <a href="teacher-student_attendant.php"
           class="nav-link <?php echo ($currentPage === 'attendance') ? 'active' : ''; ?>">
            <span class="nav-icon">🗓️</span>
            <span class="nav-label">Kehadiran</span>
        </a>

        <a href="teacher-student_marks.php"
           class="nav-link <?php echo ($currentPage === 'marks') ? 'active' : ''; ?>">
            <span class="nav-icon">📊</span>
            <span class="nav-label">Markah Pelajar</span>
        </a>

        <a href="teacher-profile.php"
           class="nav-link <?php echo ($currentPage === 'profile') ? 'active' : ''; ?>">
            <span class="nav-icon">👤</span>
            <span class="nav-label">Profil Guru</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="teacher-mini">
            <div class="avatar-circle"><?php echo $guruInisial; ?></div>
            <div class="teacher-mini-text">
                <span class="teacher-name"><?php echo htmlspecialchars($guruNama); ?></span>
                <span class="teacher-role">Guru Kelas</span>
            </div>
        </div>

        <a href="../logout.php" class="btn-logout">
            Log Keluar
        </a>
    </div>
</aside>
