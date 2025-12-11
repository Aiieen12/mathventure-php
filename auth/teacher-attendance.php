<?php
// auth/teacher-attendance.php
require_once '../config.php';
require_once 'dummy-data.php'; // <-- guna data dummy

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Pastikan user sudah login & role = guru
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'guru') {
    $_SESSION['error'] = 'Sila log masuk sebagai guru untuk akses halaman ini.';
    header('Location: index.php');
    exit;
}

$user_id = (int) $_SESSION['user_id'];

// =================== DATA GURU (ringkas) ===================

$teacherName  = $_SESSION['username'] ?? 'Guru';
$teacherClass = $kelasUtama ?? '4 Dinamik'; // datang dari dummy-data.php
$teacherYear  = 'Tahun 4';                  // boleh tukar ikut citarasa

// Nama pertama untuk avatar
$firstNameOnly = $teacherName;
if (strpos($teacherName, ' ') !== false) {
    $parts = explode(' ', $teacherName);
    $firstNameOnly = $parts[0];
}

// =================== SENARAI MURID DARI DUMMY ===================

$studentsList = array_values($students); // $students dari dummy-data.php

$totalStudents = count($studentsList);
$hadirHariIni  = 0;

foreach ($studentsList as $stu) {
    if (!empty($stu['hadir_hari_ini'])) {
        $hadirHariIni++;
    }
}

$absent               = $totalStudents - $hadirHariIni;
$attendancePercentDay = $totalStudents > 0 ? round(($hadirHariIni / $totalStudents) * 100) : 0;

date_default_timezone_set('Asia/Kuala_Lumpur');
$currentTime = date('h:i A');
$currentDate = date('d/m/Y');

// untuk highlight menu
$page = 'attendance';
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Kehadiran Pelajar | Mathventure</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../asset/css/dashboard-teacher.css?v=<?php echo time(); ?>">
</head>
<body>

<div class="teacher-layout">

    <!-- ================= SIDEBAR (santai) ================= -->
    <aside class="sidebar">
        <div class="sidebar-top">
            <div class="brand">
                <div class="brand-avatar">M</div>
                <div class="brand-text">
                    <h1>Mathventure</h1>
                    <span>Teacher Mode</span>
                </div>
            </div>

            <div>
                <div class="nav-group-label">Menu</div>
                <nav class="side-nav">
                    <a href="dashboard-teacher.php"
                       class="nav-item <?php echo $page === 'dashboard' ? 'active' : ''; ?>">
                        <div class="nav-icon"><i class="fa-solid fa-house"></i></div>
                        <div class="nav-label">Dashboard</div>
                    </a>

                    <a href="teacher-attendance.php"
                       class="nav-item <?php echo $page === 'attendance' ? 'active' : ''; ?>">
                        <div class="nav-icon"><i class="fa-solid fa-calendar-check"></i></div>
                        <div class="nav-label">Kehadiran</div>
                    </a>

                    <a href="teacher-marks.php"
                       class="nav-item <?php echo $page === 'marks' ? 'active' : ''; ?>">
                        <div class="nav-icon"><i class="fa-solid fa-chart-column"></i></div>
                        <div class="nav-label">Markah Pelajar</div>
                    </a>

                    <a href="teacher-profile.php"
                       class="nav-item <?php echo $page === 'profile' ? 'active' : ''; ?>">
                        <div class="nav-icon"><i class="fa-solid fa-id-badge"></i></div>
                        <div class="nav-label">Profil Guru</div>
                    </a>
                </nav>
            </div>
        </div>

        <div class="sidebar-bottom">
            <div class="teacher-mini">
                <div class="teacher-mini-avatar">
                    <?php echo strtoupper(substr($firstNameOnly, 0, 1)); ?>
                </div>
                <div class="teacher-mini-info">
                    <div class="teacher-mini-name"><?php echo htmlspecialchars($teacherName); ?></div>
                    <div class="teacher-mini-role">
                        Guru Kelas · <?php echo htmlspecialchars($teacherClass); ?>
                    </div>
                </div>
            </div>

            <form action="../logout.php" method="post">
                <button type="submit" class="btn-logout">
                    <i class="fa-solid fa-right-from-bracket"></i> Log Keluar
                </button>
            </form>
        </div>
    </aside>

    <!-- ================= MAIN CONTENT ================= -->
    <main class="main-content">
        <div class="dashboard-shell">

            <!-- Hero atas -->
            <div class="hero-row">
                <div>
                    <div class="hero-text-top">
                        Kehadiran untuk <span><?php echo htmlspecialchars($teacherClass); ?></span>
                    </div>
                    <div class="hero-text-main">
                        Tandakan kehadiran murid hari ini ✅
                    </div>
                    <div style="font-size:13px;color:#7f8c8d;margin-top:4px;">
                        Tarikh: <strong><?php echo $currentDate; ?></strong>
                    </div>
                </div>
                <div>
                    <div class="hero-clock">
                        <i class="fa-solid fa-clock"></i>
                        <span><?php echo $currentTime; ?></span>
                    </div>
                </div>
            </div>

            <!-- Panel ringkasan kehadiran (guna dummy) -->
            <section class="panel-guru" style="margin-top:10px;">
                <div class="panel-text">
                    <div class="panel-title">Ringkasan Kehadiran Hari Ini</div>

                    <div class="panel-tags">
                        <div class="tag-pill">
                            <i class="fa-solid fa-users"></i>
                            <span>Kelas: <?php echo htmlspecialchars($teacherClass); ?></span>
                        </div>
                        <div class="tag-pill">
                            <i class="fa-solid fa-percent"></i>
                            <span><?php echo $attendancePercentDay; ?>% hadir</span>
                        </div>
                        <div class="tag-pill">
                            <i class="fa-solid fa-user-xmark"></i>
                            <span><?php echo $absent; ?> murid tidak hadir</span>
                        </div>
                    </div>
                </div>
                <div class="panel-icon-box">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
            </section>

            <!-- Jadual kehadiran -->
            <section class="table-card">
                <div class="table-header">
                    <h3>Senarai Murid Kelas <?php echo htmlspecialchars($teacherClass); ?></h3>
                    <small>
                        Pilih H (Hadir) atau TH (Tidak Hadir) untuk setiap murid.
                    </small>
                </div>

                <?php if (empty($studentsList)): ?>
                    <div class="empty-state">
                        Belum ada murid dalam <code>$students</code> di <strong>dummy-data.php</strong>.
                    </div>
                <?php else: ?>
                    <!-- Hanya UI: tidak simpan ke DB -->
                    <form method="post" action="#">
                        <div class="table-wrapper">
                            <table class="nice-table">
                                <thead>
                                <tr>
                                    <th>Nama Murid</th>
                                    <th>Kelas</th>
                                    <th>Kehadiran Hari Ini</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($studentsList as $s): ?>
                                    <?php
                                    $isHadir = !empty($s['hadir_hari_ini']);
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($s['nama']); ?></td>
                                        <td><?php echo htmlspecialchars($s['kelas']); ?></td>
                                        <td>
                                            <label style="font-size:13px;margin-right:10px;">
                                                <input type="radio"
                                                       name="attendance[<?php echo htmlspecialchars($s['id']); ?>]"
                                                       value="H"
                                                    <?php echo $isHadir ? 'checked' : ''; ?>>
                                                H (Hadir)
                                            </label>
                                            <label style="font-size:13px;">
                                                <input type="radio"
                                                       name="attendance[<?php echo htmlspecialchars($s['id']); ?>]"
                                                       value="TH"
                                                    <?php echo !$isHadir ? 'checked' : ''; ?>>
                                                TH (Tidak Hadir)
                                            </label>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="form-actions" style="margin-top:12px;">
                            <button type="button" class="btn-primary">
                                <i class="fa-solid fa-floppy-disk"></i>
                                Simpan Kehadiran
                            </button>
                            <button type="reset" class="btn-outline">
                                <i class="fa-solid fa-rotate-left"></i>
                                Set Semula
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </section>

        </div>
    </main>

</div>

</body>
</html>
