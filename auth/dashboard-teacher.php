<?php
// auth/dashboard-teacher.php
require_once '../config.php';

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

// =================== DATA GURU SEBENAR ===================

$teacherName  = $_SESSION['username'] ?? 'Guru';
$teacherClass = '4 Dinamik'; // fallback jika dalam DB kosong
$teacherYear  = 'Tahun 4';

$stmt = $conn->prepare("SELECT * FROM teacher WHERE id_user = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $fname = $row['firstname'] ?? '';
    $lname = $row['lastname'] ?? '';
    $full  = trim($fname . ' ' . $lname);

    if ($full !== '') {
        $teacherName = $full;
    }

    if (!empty($row['class'])) {
        $teacherClass = $row['class'];
    }

    if (!empty($row['year'])) {
        $teacherYear = $row['year'];
    }
}
$stmt->close();

// Untuk sapaan "cikguDemo" (ambil nama pertama sahaja)
$firstNameOnly = $teacherName;
if (strpos($teacherName, ' ') !== false) {
    $parts = explode(' ', $teacherName);
    $firstNameOnly = $parts[0];
}
$sapaanGuru = 'cikgu' . $firstNameOnly; // contoh: cikguDemo / cikguAin

// =================== DUMMY DATA PANEL ===================

$dummyAttendancePercent = 92;            // 92%
$dummyActiveStudents    = 35;            // 35 murid aktif
$dummyAbsentStudents    = 2;             // 2 murid tak hadir
$dummyLatestTestName    = 'Ujian 1 – Nombor Bulat';
$dummyLatestTestInfo    = 'Markah telah dikemaskini dalam sistem.';
$dummyReminderText      = 'Pastikan kehadiran minggu ini direkod sepenuhnya.';
$dummyAttendanceNote    = 'Purata kehadiran kelas ' . $teacherClass . '.';

// Masa sekarang (untuk jam di penjuru)
date_default_timezone_set('Asia/Kuala_Lumpur');
$currentTime = date('h:i A');

// Page key untuk highlight menu
$page = 'dashboard';
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Guru | Mathventure</title>
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

            <!-- Hero greeting -->
            <div class="hero-row">
                <div>
                    <div class="hero-text-top">
                        Selamat kembali, <span><?php echo htmlspecialchars($sapaanGuru); ?>!</span>
                    </div>
                    <div class="hero-text-main">
                        Siap sedia untuk mengajar hari ini? 🍎
                    </div>
                </div>
                <div>
                    <div class="hero-clock">
                        <i class="fa-solid fa-clock"></i>
                        <span><?php echo $currentTime; ?></span>
                    </div>
                </div>
            </div>

            <!-- Panel Kawalan Guru (hijau) -->
            <section class="panel-guru">
                <div class="panel-text">
                    <div class="panel-title">Panel Kawalan Guru</div>
                    <div class="panel-desc">
                        Pantau kehadiran, markah dan perkembangan murid anda dalam satu paparan yang ringkas.
                    </div>

                    <div class="panel-tags">
                        <div class="tag-pill">
                            <i class="fa-solid fa-people-group"></i>
                            <span>Kelas utama: <?php echo htmlspecialchars($teacherClass); ?></span>
                        </div>
                        <div class="tag-pill">
                            <i class="fa-solid fa-user-graduate"></i>
                            <span>3 murid aktif</span>
                        </div>
                    </div>
                </div>
                <div class="panel-icon-box">
                    <i class="fa-solid fa-book-open"></i>
                </div>
            </section>

            <!-- Row 1: Kehadiran, Tugasan, Peringatan -->
            <section class="cards-grid">
                <!-- Kad KPI kehadiran -->
                <article class="card-kpi">
                    <div class="card-kpi-header">
                        <span class="card-kpi-label">KEHADIRAN HARI INI</span>
                        <span class="card-kpi-chip">Hari ini</span>
                    </div>
                    <div class="card-kpi-main">
                        <div class="card-kpi-value"><?php echo $dummyAttendancePercent; ?>%</div>
                        <div class="card-kpi-sub">
                            <?php echo htmlspecialchars($dummyAttendanceNote); ?>
                        </div>
                    </div>
                </article>

                <!-- Tugasan terkini -->
                <article class="card-simple">
                    <div class="card-simple-title">
                        <span>🏅</span>
                        <span>TUGASAN TERKINI</span>
                        <span class="badge-soft">Dinamik</span>
                    </div>
                    <div class="card-simple-body">
                        <strong><?php echo htmlspecialchars($dummyLatestTestName); ?></strong><br>
                        <?php echo htmlspecialchars($dummyLatestTestInfo); ?>
                    </div>
                </article>

                <!-- Peringatan -->
                <article class="card-simple">
                    <div class="card-simple-title">
                        <span>🔔</span>
                        <span>PERINGATAN</span>
                    </div>
                    <div class="card-simple-body">
                        <?php echo htmlspecialchars($dummyReminderText); ?>
                    </div>
                </article>
            </section>

            <!-- Row 2: Tindakan pantas -->
            <section class="cards-grid">
                <!-- Kehadiran pelajar -->
                <article class="card-action">
                    <div class="card-action-header">
                        <div class="card-action-icon">
                            <i class="fa-solid fa-user-check"></i>
                        </div>
                        <div>
                            <div class="card-action-title">Kehadiran Pelajar</div>
                            <div class="card-action-desc">
                                Rekod kehadiran harian dan kenal pasti murid yang tidak hadir.
                            </div>
                        </div>
                    </div>
                    <div class="card-action-desc" style="font-size:12px;color:#7f8c8d;">
                        <?php echo $dummyAbsentStudents; ?> murid dilaporkan tidak hadir pagi ini.
                    </div>
                    <div class="card-action-footer">
                        <a href="teacher-attendance.php" class="btn-outline">
                            <i class="fa-solid fa-calendar-check"></i> Buka Kehadiran
                        </a>
                    </div>
                </article>

                <!-- Markah & prestasi -->
                <article class="card-action">
                    <div class="card-action-header">
                        <div class="card-action-icon">
                            <i class="fa-solid fa-chart-line"></i>
                        </div>
                        <div>
                            <div class="card-action-title">Markah &amp; Prestasi</div>
                            <div class="card-action-desc">
                                Lihat pencapaian murid mengikut ujian, tahap dan topik.
                            </div>
                        </div>
                    </div>
                    <div class="card-action-desc" style="font-size:12px;color:#7f8c8d;">
                        Data markah <strong><?php echo htmlspecialchars($dummyLatestTestName); ?></strong> siap dikemaskini.
                    </div>
                    <div class="card-action-footer">
                        <a href="teacher-marks.php" class="btn-primary">
                            <i class="fa-solid fa-chart-column"></i> Lihat Markah
                        </a>
                    </div>
                </article>

                <!-- Tetapan profil -->
                <article class="card-action">
                    <div class="card-action-header">
                        <div class="card-action-icon">
                            <i class="fa-solid fa-id-badge"></i>
                        </div>
                        <div>
                            <div class="card-action-title">Tetapan Profil</div>
                            <div class="card-action-desc">
                                Kemas kini maklumat diri, kelas yang diuruskan dan kata laluan.
                            </div>
                        </div>
                    </div>
                    <div class="card-action-desc" style="font-size:12px;color:#7f8c8d;">
                        Profil guru berada dalam status <strong>lengkap</strong>.
                    </div>
                    <div class="card-action-footer">
                        <a href="teacher-profile.php" class="btn-outline">
                            <i class="fa-solid fa-pen-to-square"></i> Kemaskini Profil
                        </a>
                    </div>
                </article>
            </section>

        </div>
    </main>

</div>

</body>
</html>
