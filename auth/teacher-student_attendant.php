<?php
session_start();
require_once '../config.php';
$page = 'attendance'; // Set identifier

// Data Pelajar Mockup
$pelajar = [
    ['nama' => 'Ali bin Abu', 'kelas' => '4 Dinamik'],
    ['nama' => 'Siti Sarah', 'kelas' => '4 Dinamik'],
    ['nama' => 'Muthu Kumar', 'kelas' => '5 Kreatif'],
];
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Kehadiran | Mathventure</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../asset/cssdashboard-teacher.css">
</head>
<body>
    <?php include 'sidebar-teacher.php'; ?>

    <main class="main-content">
        <div class="section-header">
            <h2>Rekod Kehadiran Pelajar</h2>
            <p>Tandakan kehadiran hari ini.</p>
        </div>

        <div class="card-container">
            <form method="POST" action=""> <table class="styled-table">
                    <thead>
                        <tr>
                            <th>Nama Murid</th>
                            <th>Kelas</th>
                            <th>Kehadiran (H / TH)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($pelajar as $p): ?>
                        <tr>
                            <td>
                                <div class="student-info">
                                    <div class="mini-avatar"><?php echo substr($p['nama'], 0, 1); ?></div>
                                    <span><?php echo $p['nama']; ?></span>
                                </div>
                            </td>
                            <td><span class="badge-class"><?php echo $p['kelas']; ?></span></td>
                            <td>
                                <div class="attendance-toggle">
                                    <label class="radio-h"><input type="radio" name="att_<?php echo str_replace(' ', '', $p['nama']); ?>" value="H" checked> H</label>
                                    <label class="radio-th"><input type="radio" name="att_<?php echo str_replace(' ', '', $p['nama']); ?>" value="TH"> TH</label>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="submit" class="btn-save">Simpan Kehadiran</button>
            </form>
        </div>
    </main>
</body>
</html>