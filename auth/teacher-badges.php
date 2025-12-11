<?php
session_start();
require_once '../config.php';
require_once 'dummy-data.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'guru') {
    $_SESSION['error'] = 'Sila log masuk sebagai guru.';
    header('Location: ../index.php');
    exit;
}

$page     = 'badges';
$guruNama = $_SESSION['username'] ?? 'cikguDemo';

$selectedId = $_GET['id'] ?? 'anis21';
$pelajar    = getStudentById($selectedId, $students);
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Pencapaian & Lencana | Mathventure</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../asset/css/teacher-shell.css?v=1">
    <link rel="stylesheet" href="../asset/css/dashboard-teacher.css?v=1">
</head>
<body class="teacher-mode">

<div class="teacher-layout">
    <?php include 'sidebar-teacher.php'; ?>

    <main class="main-content teacher-dashboard">

        <header class="teacher-topbar">
            <div class="welcome-pill">
                <span>Pencapaian & lencana permainan Mathventure</span>
                <span class="welcome-sub">Paparan demo untuk inovasi, bukan rekod sebenar.</span>
            </div>
        </header>

        <section class="feature-grid">

            <!-- Kad pencapaian murid -->
            <article class="feature-card">
                <div class="feature-header">
                    <div>
                        <h2><?php echo htmlspecialchars($pelajar['nama']); ?> (<?php echo htmlspecialchars($pelajar['id']); ?>)</h2>
                        <p>Kelas: <?php echo htmlspecialchars($pelajar['kelas']); ?></p>
                    </div>
                    <span class="feature-tag">Lencana</span>
                </div>
                <div class="feature-body">
                    <p>
                        Jumlah lencana terkumpul: <strong><?php echo $pelajar['jumlah_badge']; ?></strong><br>
                        Lencana dibuka minggu ini: <strong><?php echo $pelajar['badges_minggu']; ?></strong><br>
                        Purata level: <strong><?php echo $pelajar['purata_level']; ?> / 5</strong>
                    </p>
                    <p>Contoh nama lencana:</p>
                    <ul>
                        <li>🌟 Semakin Hebat! – capai Level 3</li>
                        <li>➕➖ Pakar Tambah & Tolak! – skor ≥ 80% Ujian 1</li>
                        <li>🎯 Fokus Hebat! – hadir penuh minggu ini</li>
                    </ul>
                </div>
                <div class="feature-footer">
                    <a href="teacher-student_attendant.php?id=<?php echo urlencode($pelajar['id']); ?>" class="btn-outline">
                        <i class="fa-regular fa-calendar-check"></i>
                        Lihat kehadiran murid ini
                    </a>
                    <a href="teacher-marks.php?id=<?php echo urlencode($pelajar['id']); ?>" class="btn-outline">
                        <i class="fa-solid fa-chart-column"></i>
                        Lihat markah murid ini
                    </a>
                </div>
            </article>

            <!-- Ringkasan badge seluruh kelas -->
            <article class="feature-card">
                <div class="feature-header">
                    <div>
                        <h2>Lencana Kelas <?php echo htmlspecialchars($kelasUtama); ?></h2>
                        <p>Ringkasan pencapaian Mathventure bagi semua murid.</p>
                    </div>
                </div>
                <div class="feature-body">
                    <table class="table-simple">
                        <thead>
                        <tr>
                            <th>Nama</th>
                            <th>ID</th>
                            <th>Jumlah Lencana</th>
                            <th>Level</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($students as $s): ?>
                            <tr>
                                <td>
                                    <a href="teacher-badges.php?id=<?php echo urlencode($s['id']); ?>">
                                        <?php echo htmlspecialchars($s['nama']); ?>
                                    </a>
                                </td>
                                <td><?php echo htmlspecialchars($s['id']); ?></td>
                                <td><?php echo $s['jumlah_badge']; ?></td>
                                <td><?php echo $s['purata_level']; ?>/5</td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </article>

        </section>

    </main>
</div>

</body>
</html>
