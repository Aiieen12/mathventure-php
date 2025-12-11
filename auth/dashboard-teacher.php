<?php
session_start();
require_once '../config.php';
require_once 'dummy-data.php';

// Pastikan role guru (kalau ada guna sistem role)
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'guru') {
    $_SESSION['error'] = 'Sila log masuk sebagai guru.';
    header('Location: ../index.php');
    exit;
}

$page     = 'dashboard'; // untuk highlight menu di sidebar
$guruNama = $_SESSION['username'] ?? 'cikguDemo';

// Kiraan ringkasan dari data dummy
$jumlahMurid       = count($students);
$muridHadirHari    = 0;
$jumlahLevel       = 0;
$muridSiapUjian    = 0;
$muridBelumMainMggu = 0;
$badgesMinggu      = 0;

foreach ($students as $s) {
    if (!empty($s['hadir_hari_ini'])) {
        $muridHadirHari++;
    }
    $jumlahLevel += $s['purata_level'] ?? 0;

    if (isset($s['markah_ujian1'])) {
        $muridSiapUjian++;
    }

    if (($s['purata_level'] ?? 0) < 1) {
        $muridBelumMainMggu++;
    }

    $badgesMinggu += $s['badges_minggu'] ?? 0;
}

$peratusHadirHari = $jumlahMurid > 0 ? round(($muridHadirHari / $jumlahMurid) * 100) : 0;
$muridTidakHadir  = $jumlahMurid - $muridHadirHari;
$purataLevel      = $jumlahMurid > 0 ? round($jumlahLevel / $jumlahMurid, 1) : 0;

$ujianTerkini = 'Ujian 1 – Nombor Bulat';

// Masa sekarang untuk chip di atas
$masaNow = date('h:i A');
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Guru | Mathventure</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- CSS global: background + sidebar + main-content -->
    <link rel="stylesheet" href="../asset/css/teacher-shell.css?v=1">
    <!-- CSS khas untuk page dashboard guru -->
    <link rel="stylesheet" href="../asset/css/dashboard-teacher.css?v=1">
</head>
<body class="teacher-mode">

<div class="teacher-layout">
    <?php include 'sidebar-teacher.php'; ?>

    <main class="main-content teacher-dashboard">

        <!-- Bar atas: welcome + masa -->
        <header class="teacher-topbar">
            <div class="welcome-pill">
                <span>Selamat kembali, <strong><?php echo htmlspecialchars($guruNama); ?></strong>!</span>
                <span class="welcome-sub">Siap sedia untuk mengajar murid hari ini? 🍎</span>
            </div>
            <div class="time-chip">
                <i class="fa-regular fa-clock"></i>
                <span><?php echo htmlspecialchars($masaNow); ?></span>
            </div>
        </header>

        <!-- Hero panel -->
        <section class="hero-panel">
            <div class="hero-text">
                <h1>Panel Kawalan Guru</h1>
                <p>Pantau kehadiran, markah kuiz dan kemajuan permainan Mathventure murid dalam satu paparan ringkas.</p>
                <div class="hero-meta">
                    <span class="hero-chip">
                        <i class="fa-solid fa-chalkboard-user"></i>
                        Kelas utama: <?php echo htmlspecialchars($kelasUtama); ?>
                    </span>
                    <span class="hero-chip secondary">
                        <i class="fa-solid fa-users"></i>
                        <?php echo $jumlahMurid; ?> murid berdaftar
                    </span>
                    <span class="hero-chip secondary">
                        <i class="fa-solid fa-gamepad"></i>
                        Purata level: <?php echo number_format($purataLevel, 1); ?> / 5
                    </span>
                    <span class="hero-chip secondary">
                        <i class="fa-solid fa-award"></i>
                        <?php echo $badgesMinggu; ?> lencana baharu minggu ini
                    </span>
                </div>
            </div>
            <div class="hero-illustration">
                🎮
            </div>
        </section>

        <!-- Ringkasan statistik kecil -->
        <section class="summary-row">
            <!-- Kehadiran Hari Ini -->
            <article class="summary-card">
                <div class="summary-icon green">
                    <i class="fa-solid fa-user-check"></i>
                </div>
                <div class="summary-body">
                    <div class="summary-label">Kehadiran Hari Ini</div>
                    <div class="summary-value"><?php echo $peratusHadirHari; ?>%</div>
                    <div class="summary-note">
                        <?php echo $muridHadirHari; ?> daripada <?php echo $jumlahMurid; ?> murid hadir.<br>
                        <?php echo $muridTidakHadir; ?> murid tidak hadir pagi ini.
                    </div>
                </div>
            </article>

            <!-- Kemajuan Level Mathventure -->
            <article class="summary-card">
                <div class="summary-icon blue">
                    <i class="fa-solid fa-medal"></i>
                </div>
                <div class="summary-body">
                    <div class="summary-label">Kemajuan Level</div>
                    <div class="summary-value small">
                        Purata Level <?php echo number_format($purataLevel, 1); ?> / 5
                    </div>
                    <div class="summary-note">
                        <?php echo $muridSiapUjian; ?> murid telah menyiapkan <?php echo htmlspecialchars($ujianTerkini); ?>.
                    </div>
                </div>
            </article>

            <!-- Peringatan Murid -->
            <article class="summary-card">
                <div class="summary-icon orange">
                    <i class="fa-solid fa-bell"></i>
                </div>
                <div class="summary-body">
                    <div class="summary-label">Peringatan Murid</div>
                    <div class="summary-note">
                        <?php echo $muridBelumMainMggu; ?> murid belum bermain Mathventure minggu ini.<br>
                        Galakkan mereka cuba sekurang-kurangnya 1 level sebelum Jumaat.
                    </div>
                </div>
            </article>
        </section>

        <!-- Card fungsi utama -->
        <section class="feature-grid">

            <!-- Kehadiran -->
            <article class="feature-card">
                <div class="feature-header">
                    <div>
                        <h2>Kehadiran Pelajar</h2>
                        <p>Rekod kehadiran harian murid dan kenal pasti yang kerap tidak hadir atau lewat.</p>
                    </div>
                    <span class="feature-tag">Hari ini</span>
                </div>
                <div class="feature-body">
                    <p>
                        Hadir: <strong><?php echo $muridHadirHari; ?> murid</strong><br>
                        Tidak hadir: <strong><?php echo $muridTidakHadir; ?> murid</strong>
                    </p>
                </div>
                <div class="feature-footer">
                    <!-- link ke page kehadiran; default = Anis -->
                    <a href="teacher-student_attendant.php?id=anis21" class="btn-outline">
                        <i class="fa-regular fa-calendar-check"></i>
                        Buka Kehadiran
                    </a>
                </div>
            </article>

            <!-- Markah & Prestasi -->
            <article class="feature-card">
                <div class="feature-header">
                    <div>
                        <h2>Markah &amp; Prestasi</h2>
                        <p>Lihat pencapaian murid mengikut ujian, tahap dan topik Mathventure.</p>
                    </div>
                    <span class="feature-tag tag-green">Dinamik</span>
                </div>
                <div class="feature-body">
                    <p>
                        Data markah <strong><?php echo htmlspecialchars($ujianTerkini); ?></strong> siap dikemaskini.<br>
                        <strong><?php echo $muridSiapUjian; ?> murid</strong> telah menyiapkan ujian ini.
                    </p>
                </div>
                <div class="feature-footer">
                    <!-- link ke page markah; default = Anis -->
                    <a href="teacher-marks.php?id=anis21" class="btn-solid">
                        <i class="fa-solid fa-chart-column"></i>
                        Lihat Markah
                    </a>
                </div>
            </article>

            <!-- Pencapaian Murid -->
            <article class="feature-card">
                <div class="feature-header">
                    <div>
                        <h2>Pencapaian Murid</h2>
                        <p>Lihat lencana dan level Mathventure yang berjaya dibuka oleh murid.</p>
                    </div>
                </div>
                <div class="feature-body">
                    <p>
                        Minggu ini, <strong><?php echo $badgesMinggu; ?> lencana baharu</strong> telah dibuka oleh murid anda.
                    </p>
                </div>
                <div class="feature-footer">
                    <!-- guna versi guru supaya tak kacau badges pelajar -->
                    <a href="teacher-badges.php?id=anis21" class="btn-outline">
                        <i class="fa-solid fa-trophy"></i>
                        Lihat Pencapaian
                    </a>
                </div>
            </article>

        </section>

    </main>
</div>

</body>
</html>
