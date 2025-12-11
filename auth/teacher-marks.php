<?php
// auth/teacher-marks.php
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
$teacherClass = $kelasUtama ?? '4 Dinamik';
$teacherYear  = 'Tahun 4';

$firstNameOnly = $teacherName;
if (strpos($teacherName, ' ') !== false) {
    $parts = explode(' ', $teacherName);
    $firstNameOnly = $parts[0];
}

// =================== DATA PELAJAR & MARKAH DUMMY ===================

$studentsList = array_values($students);

$totalStudents = count($studentsList);
$scores        = [];

foreach ($studentsList as $stu) {
    if (isset($stu['markah_ujian1'])) {
        $scores[] = (int) $stu['markah_ujian1'];
    }
}

$dummyLatestTestName = 'Ujian 1 – Nombor Bulat';

$dummyTotalStudents = $totalStudents;
$dummyAvgScore      = ($dummyTotalStudents > 0 && count($scores) > 0)
    ? round(array_sum($scores) / count($scores))
    : 0;
$dummyBestScore     = count($scores) > 0 ? max($scores) : 0;
$dummyPassCount     = 0;

foreach ($scores as $score) {
    if ($score >= 50) {
        $dummyPassCount++;
    }
}

date_default_timezone_set('Asia/Kuala_Lumpur');
$currentTime = date('h:i A');

$page = 'marks';
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Markah Pelajar | Mathventure</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../asset/css/dashboard-teacher.css?v=<?php echo time(); ?>">
</head>
<body>

<div class="teacher-layout">

    <!-- ================= SIDEBAR ================= -->
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

            <!-- Hero -->
            <div class="hero-row">
                <div>
                    <div class="hero-text-top">
                        Prestasi murid kelas <span><?php echo htmlspecialchars($teacherClass); ?></span>
                    </div>
                    <div class="hero-text-main">
                        Lihat markah 📊
                    </div>
                    <div style="font-size:13px;color:#7f8c8d;margin-top:4px;">
                        Ujian terkini: <strong><?php echo htmlspecialchars($dummyLatestTestName); ?></strong>
                    </div>
                </div>
                <div>
                    <div class="hero-clock">
                        <i class="fa-solid fa-clock"></i>
                        <span><?php echo $currentTime; ?></span>
                    </div>
                </div>
            </div>

            <!-- Panel ringkasan prestasi -->
            <section class="panel-guru" style="margin-top:10px;">
                <div class="panel-text">
                    <div class="panel-title">Ringkasan Ujian Terkini</div>

                    <div class="panel-tags">
                        <div class="tag-pill">
                            <i class="fa-solid fa-percent"></i>
                            <span>Purata markah: <?php echo $dummyAvgScore; ?>%</span>
                        </div>
                        <div class="tag-pill">
                            <i class="fa-solid fa-trophy"></i>
                            <span>Tertinggi: <?php echo $dummyBestScore; ?>%</span>
                        </div>
                        <div class="tag-pill">
                            <i class="fa-solid fa-user-graduate"></i>
                            <span><?php echo $dummyPassCount; ?> / <?php echo $dummyTotalStudents; ?> murid lulus</span>
                        </div>
                    </div>
                </div>
                <div class="panel-icon-box">
                    <i class="fa-solid fa-chart-simple"></i>
                </div>
            </section>

            <!-- Jadual markah -->
            <section class="table-card">
                <div class="table-header">
                    <h3>Senarai Markah Kelas <?php echo htmlspecialchars($teacherClass); ?></h3>
                    
                </div>

                <?php if (empty($studentsList)): ?>
                    <div class="empty-state">
                        Belum ada murid dalam <code>$students</code> di dummy-data.php.
                    </div>
                <?php else: ?>
                    <div class="table-wrapper">
                        <table class="nice-table">
                            <thead>
                            <tr>
                                <th>Nama Murid</th>
                                <th>Kelas</th>
                                <th>Markah Ujian 1</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($studentsList as $s): ?>
                                <?php
                                $score       = isset($s['markah_ujian1']) ? (int) $s['markah_ujian1'] : 0;
                                $statusLulus = $score >= 50;
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($s['nama']); ?></td>
                                    <td><?php echo htmlspecialchars($s['kelas']); ?></td>
                                    <td><?php echo $score; ?>%</td>
                                    <td>
                                        <?php if ($statusLulus): ?>
                                            <span class="badge status-lulus">Lulus</span>
                                        <?php else: ?>
                                            <span class="badge status-belum">Belum lulus</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>

        </div>
    </main>

</div>

</body>
</html>
