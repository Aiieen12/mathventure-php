<?php
session_start();
require_once '../config.php';

$page = 'attendance'; // untuk highlight menu

// Data pelajar mockup
$pelajar = [
    ['nama' => 'Ali bin Abu',   'kelas' => '4 Dinamik'],
    ['nama' => 'Siti Sarah',    'kelas' => '4 Dinamik'],
    ['nama' => 'Muthu Kumar',   'kelas' => '5 Kreatif'],
];

$tarikhHariIni = date('d/m/Y');
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Kehadiran | Mathventure</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- CSS global guru: nav + background cerah -->
    <link rel="stylesheet" href="../asset/css/teacher-shell.css?v=1">
    <!-- CSS khusus page kehadiran (table + button) -->
    <link rel="stylesheet" href="../asset/css/teacher-attendance.css?v=1">
</head>
<body class="teacher-mode">

<div class="teacher-layout">
    <?php include 'sidebar-teacher.php'; ?>

    <main class="main-content attendance-page">
        <div class="attendance-header">
            <div>
                <h2>Rekod Kehadiran Pelajar</h2>
                <p>Tandakan kehadiran murid untuk tarikh berikut.</p>
            </div>
            <div class="attendance-meta">
                <span class="chip-date">
                    <i class="fa-regular fa-calendar"></i>
                    <span><?php echo $tarikhHariIni; ?></span>
                </span>
            </div>
        </div>

        <div class="attendance-card">
            <form method="POST" action="" class="attendance-form">
                <table class="attendance-table">
                    <thead>
                        <tr>
                            <th>Nama Murid</th>
                            <th>Kelas</th>
                            <th>Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($pelajar as $p): ?>
                        <?php $keyName = 'att_' . preg_replace('/\s+/', '', strtolower($p['nama'])); ?>
                        <tr>
                            <td>
                                <div class="student-info">
                                    <div class="mini-avatar">
                                        <?php echo strtoupper(substr($p['nama'], 0, 1)); ?>
                                    </div>
                                    <span class="student-name"><?php echo htmlspecialchars($p['nama']); ?></span>
                                </div>
                            </td>
                            <td>
                                <span class="badge-class"><?php echo htmlspecialchars($p['kelas']); ?></span>
                            </td>
                            <td>
                                <div class="attendance-toggle">
                                    <label class="pill pill-h">
                                        <input type="radio" name="<?php echo $keyName; ?>" value="H" checked>
                                        <span>Hadir</span>
                                    </label>
                                    <label class="pill pill-th">
                                        <input type="radio" name="<?php echo $keyName; ?>" value="TH">
                                        <span>Tidak Hadir</span>
                                    </label>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="attendance-actions">
                    <button type="submit" class="btn-save-attendance">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Simpan Kehadiran</span>
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>

</body>
</html>
