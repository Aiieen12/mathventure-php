<aside class="sidebar">
    <div class="logo-section">
        <h2>Mathventure</h2>
        <span class="badge-role">Teacher Mode</span>
    </div>

    <nav class="nav-links">
        <a href="dashboard-teacher.php" class="nav-item <?php echo ($page == 'home') ? 'active' : ''; ?>">
            <i class="fa-solid fa-house"></i> Halaman Utama
        </a>
        <a href="teacher-attendance.php" class="nav-item <?php echo ($page == 'attendance') ? 'active' : ''; ?>">
            <i class="fa-solid fa-calendar-check"></i> Kehadiran Pelajar
        </a>
        <a href="teacher-marks.php" class="nav-item <?php echo ($page == 'marks') ? 'active' : ''; ?>">
            <i class="fa-solid fa-chart-pie"></i> Markah Pelajar
        </a>
        <a href="teacher-profile.php" class="nav-item <?php echo ($page == 'profile') ? 'active' : ''; ?>">
            <i class="fa-solid fa-user-pen"></i> Profil Guru
        </a>
    </nav>

    <div class="logout-section">
        <a href="logout.php" class="btn-logout">
            <i class="fa-solid fa-right-from-bracket"></i> Log Keluar
        </a>
    </div>
</aside>