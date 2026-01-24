<?php
//sidebar-teacher.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$namaPaparan  = isset($teacherName) ? $teacherName : ($_SESSION['username'] ?? 'Cikgu');
$kelasPaparan = isset($teacherClass) ? $teacherClass : '-';
$yearPaparan  = isset($teacherYear) ? $teacherYear : '-';
$halaman      = $page ?? '';
?>
<aside class="sidebar">
    <div class="sidebar-header">
        <div class="logo-area">
            <div class="logo-icon">🦖</div>
            <div class="logo-text">Mathventure</div>
        </div>
    </div>

    <div class="teacher-mini-card">
        <div class="teacher-mini-avatar">
            <i class="fa-solid fa-chalkboard-user"></i>
        </div>
        <div class="teacher-mini-info">
            <div class="teacher-name">
                <?php echo htmlspecialchars($namaPaparan); ?>
            </div>
            <div class="teacher-meta">
                <span><i class="fa-solid fa-people-group"></i>
                    <?php echo htmlspecialchars($kelasPaparan); ?>
                </span>
                <span><i class="fa-solid fa-calendar-days"></i>
                    <?php echo htmlspecialchars($yearPaparan); ?>
                </span>
            </div>
        </div>
    </div>

    <div class="side-nav">
        <div class="nav-label">Menu Guru</div>

        <a href="dashboard-teacher.php"
           class="nav-item <?php echo $halaman === 'dashboard' ? 'active' : ''; ?>">
            <span class="icon"><i class="fa-solid fa-house"></i></span>
            <span>Dashboard</span>
        </a>

        <a href="teacher-attendance.php"
           class="nav-item <?php echo $halaman === 'attendance' ? 'active' : ''; ?>">
            <span class="icon"><i class="fa-solid fa-user-check"></i></span>
            <span>Kehadiran Pelajar</span>
        </a>

        <a href="teacher-marks.php"
           class="nav-item <?php echo $halaman === 'marks' ? 'active' : ''; ?>">
            <span class="icon"><i class="fa-solid fa-chart-column"></i></span>
            <span>Markah Pelajar</span>
        </a>

        <a href="teacher-profile.php"
           class="nav-item <?php echo $halaman === 'profile' ? 'active' : ''; ?>">
            <span class="icon"><i class="fa-solid fa-id-card"></i></span>
            <span>Profil Guru</span>
        </a>

        <a href="logout.php" class="nav-item logout">
            <span class="icon"><i class="fa-solid fa-right-from-bracket"></i></span>
            <span>Log Keluar</span>
        </a>
    </div>
</aside>
