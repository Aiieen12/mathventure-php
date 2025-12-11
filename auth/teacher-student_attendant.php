<?php
session_start();
require_once '../config.php';
require_once 'dummy-data.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'guru') {
    $_SESSION['error'] = 'Sila log masuk sebagai guru.';
    header('Location: ../index.php');
    exit;
}

$page     = 'attendance';
$guruNama = $_SESSION['username'] ?? 'cikguDemo';

// Pelajar dipilih melalui ?id=anis21
$selectedId = $_GET['id'] ?? 'anis21';
$pelajar    = getStudentById($selectedId, $students);

$peratusHadir = $pelajar['jumlah_hadir'] > 0
    ? round(($pelajar['jumlah_hadir'] / $pelajar['jumlah_hari']) * 100)
    : 0;

$tarikhHariIni = date('d M Y');
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Kehadiran Pelajar | Mathventure</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../asset/css/teacher-shell.css?v=1">
    <link rel="stylesheet" href="../asset/css/dashboard-teacher.css?v=1">
    <link rel="stylesheet" href="../asset/css/teacher-attendance.css?v=1">
</head>
<body class="teacher-mode">

<div class="teacher-layout">
    <?php include 'sidebar-teacher.php'; ?>

    <main class="main-content teacher-dashboard">
        <div class="attendance-page">

            <!-- HEADER ATAS -->
            <header class="attendance-header">
                <div>
                    <h2>Kehadiran pelajar kelas <strong><?php echo htmlspecialchars($kelasUtama); ?></strong></h2>
                    <p>Paparan ini menggunakan data dummy untuk projek inovasi Mathventure.</p>
                </div>
                <div class="attendance-meta">
                    <span class="badge-class">
                        <i class="fa-solid fa-users"></i>
                        <?php echo htmlspecialchars($kelasUtama); ?>
                    </span>
                    <span class="chip-date">
                        <i class="fa-regular fa-calendar"></i>
                        <?php echo htmlspecialchars($tarikhHariIni); ?>
                    </span>
                </div>
            </header>

            <!-- CARD MAKLUMAT PELAJAR TERPILIH -->
            <section class="attendance-card">
                <div class="student-info" style="margin-bottom: 8px;">
                    <div class="mini-avatar">
                        <?php echo strtoupper(substr($pelajar['nama'], 0, 1)); ?>
                    </div>
                    <div>
                        <div class="student-name">
                            <?php echo htmlspecialchars($pelajar['nama']); ?>
                            <span style="font-weight:400;">(<?php echo htmlspecialchars($pelajar['id']); ?>)</span>
                        </div>
                        <p style="margin:2px 0 0;">Kelas: <?php echo htmlspecialchars($pelajar['kelas']); ?></p>
                    </div>
                </div>

                <h3 style="margin-top:10px;">Kehadiran</h3>
                <p>
                    Jumlah hadir:
                    <strong><?php echo $pelajar['jumlah_hadir']; ?> / <?php echo $pelajar['jumlah_hari']; ?></strong><br>
                    Peratus hadir: <strong><?php echo $peratusHadir; ?>%</strong>
                </p>

                <div class="attendance-link-group">
                    <a href="teacher-marks.php?id=<?php echo urlencode($pelajar['id']); ?>" class="attendance-link">
                        <i class="fa-solid fa-chart-column"></i>
                        Lihat markah murid ini
                    </a>
                    <a href="teacher-badges.php?id=<?php echo urlencode($pelajar['id']); ?>" class="attendance-link">
                        <i class="fa-solid fa-trophy"></i>
                        Lihat lencana murid ini
                    </a>
                </div>
            </section>

            <!-- CARD SENARAI KEHADIRAN KELAS -->
            <section class="attendance-card">
                <h3>Senarai Kehadiran Kelas <?php echo htmlspecialchars($kelasUtama); ?></h3>
                <p>Klik nama murid untuk tukar paparan kehadiran.</p>

                <table class="attendance-table">
                    <thead>
                    <tr>
                        <th>Nama</th>
                        <th>ID</th>
                        <th>Hadir / Hari</th>
                        <th>% Hadir</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($students as $s): ?>
                        <?php
                        $p = $s['jumlah_hadir'] > 0
                            ? round(($s['jumlah_hadir'] / $s['jumlah_hari']) * 100)
                            : 0;
                        ?>
                        <tr>
                            <td>
                                <div class="student-info">
                                    <div class="mini-avatar">
                                        <?php echo strtoupper(substr($s['nama'], 0, 1)); ?>
                                    </div>
                                    <div class="student-name">
                                        <a href="teacher-student_attendant.php?id=<?php echo urlencode($s['id']); ?>">
                                            <?php echo htmlspecialchars($s['nama']); ?>
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($s['id']); ?></td>
                            <td><?php echo $s['jumlah_hadir']; ?> / <?php echo $s['jumlah_hari']; ?></td>
                            <td><?php echo $p; ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </section>

        </div>
    </main>
</div>

</body>
</html>
