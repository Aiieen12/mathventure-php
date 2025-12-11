<?php
session_start();
require_once '../config.php';

// Pastikan role guru (kalau ada guna sistem role)
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'guru') {
    $_SESSION['error'] = 'Sila log masuk sebagai guru.';
    header('Location: ../index.php');
    exit;
}

$page = 'dashboard'; // untuk highlight menu di sidebar

$guruNama  = $_SESSION['username'] ?? 'cikguDemo';

// Mock data – nanti boleh ganti dengan data sebenar dari DB
$kelasUtama       = '4 Dinamik';
$jumlahMurid      = 35;
$peratusHadirHari = 92;
$ujianTerkini     = 'Ujian 1 – Nombor Bulat';

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
                <span class="welcome-sub">Siap sedia untuk mengajar hari ini? 🍎</span>
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
                <p>Pantau kehadiran, markah dan perkembangan murid anda dalam satu paparan yang ringkas.</p>
                <div class="hero-meta">
                    <span class="hero-chip">
                        <i class="fa-solid fa-chalkboard-user"></i>
                        Kelas utama: <?php echo htmlspecialchars($kelasUtama); ?>
                    </span>
                    <span class="hero-chip secondary">
                        <i class="fa-solid fa-users"></i>
                        <?php echo $jumlahMurid; ?> murid aktif
                    </span>
                </div>
            </div>
            <div class="hero-illustration">
                📚
            </div>
        </section>

        <!-- Ringkasan statistik kecil -->
        <section class="summary-row">
            <article class="summary-card">
                <div class="summary-icon green">
                    <i class="fa-solid fa-user-check"></i>
                </div>
                <div class="summary-body">
                    <div class="summary-label">Kehadiran Hari Ini</div>
                    <div class="summary-value"><?php echo $peratusHadirHari; ?>%</div>
                    <div class="summary-note">Purata kehadiran kelas <?php echo htmlspecialchars($kelasUtama); ?>.</div>
                </div>
            </article>

            <article class="summary-card">
                <div class="summary-icon blue">
                    <i class="fa-solid fa-medal"></i>
                </div>
                <div class="summary-body">
                    <div class="summary-label">Tugasan Terkini</div>
                    <div class="summary-value small"><?php echo htmlspecialchars($ujianTerkini); ?></div>
                    <div class="summary-note">Markah telah dikemaskini dalam sistem.</div>
                </div>
            </article>

            <article class="summary-card">
                <div class="summary-icon orange">
                    <i class="fa-solid fa-bell"></i>
                </div>
                <div class="summary-body">
                    <div class="summary-label">Peringatan</div>
                    <div class="summary-note">Pastikan kehadiran minggu ini direkod sepenuhnya.</div>
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
                        <p>Rekod kehadiran harian dan kenal pasti murid yang tidak hadir.</p>
                    </div>
                    <span class="feature-tag">Hari ini</span>
                </div>
                <div class="feature-body">
                    <p><strong>2 murid</strong> dilaporkan tidak hadir pagi ini.</p>
                </div>
                <div class="feature-footer">
                    <a href="teacher-student_attendant.php" class="btn-outline">
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
                        <p>Lihat pencapaian murid mengikut ujian, tahap dan topik.</p>
                    </div>
                    <span class="feature-tag tag-green">Dinamik</span>
                </div>
                <div class="feature-body">
                    <p>Data markah <strong><?php echo htmlspecialchars($ujianTerkini); ?></strong> siap dikemaskini.</p>
                </div>
                <div class="feature-footer">
                    <a href="teacher-marks.php" class="btn-solid">
                        <i class="fa-solid fa-chart-column"></i>
                        Lihat Markah
                    </a>
                </div>
            </article>

            <!-- Tetapan Profil -->
            <article class="feature-card">
                <div class="feature-header">
                    <div>
                        <h2>Tetapan Profil</h2>
                        <p>Kemaskini maklumat diri, kelas yang diuruskan dan kata laluan.</p>
                    </div>
                </div>
                <div class="feature-body">
                    <p>Profil guru berada dalam status <strong>lengkap</strong>.</p>
                </div>
                <div class="feature-footer">
                    <a href="teacher-profile.php" class="btn-outline">
                        <i class="fa-regular fa-id-badge"></i>
                        Kemaskini Profil
                    </a>
                </div>
            </article>

        </section>

    </main>
</div>

</body>
</html>
