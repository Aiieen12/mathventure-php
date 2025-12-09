<?php
// auth/game-menu.php

require_once '../config.php';

// Pastikan user sudah login & role = pelajar
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'pelajar') {
    $_SESSION['error'] = 'Sila log masuk sebagai pelajar untuk akses halaman ini.';
    header('Location: index.php');
    exit;
}

$userId = $_SESSION['user_id'];
$nama   = $_SESSION['username'] ?? 'Pelajar';

// Senarai tahun dalam permainan
$tahunList = [4, 5, 6];

// Tetapkan bilangan level maksimum bagi setiap tahun
$maxLevelByYear = [
    4 => 5,
    5 => 5,
    6 => 5,
];

// Inisialisasi progress dalam SESSION
if (!isset($_SESSION['progress'])) {
    $_SESSION['progress'] = [];
}
if (!isset($_SESSION['progress'][$userId])) {
    $_SESSION['progress'][$userId] = [];
}

// Untuk setiap tahun, kalau belum ada rekod, level 1 sahaja yang dibuka
foreach ($tahunList as $t) {
    if (!isset($_SESSION['progress'][$userId][$t])) {
        $_SESSION['progress'][$userId][$t] = 1;  // level dibuka
    }
}
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Menu Permainan Matematik</title>
</head>
<body>

<h1>Menu Permainan Matematik</h1>

<p>Pelajar: <strong><?php echo htmlspecialchars($nama); ?></strong></p>
<p>Sila pilih tahun dan level untuk memulakan permainan.</p>
<hr>

<?php foreach ($tahunList as $tahun): ?>
    <h2>Tahun <?php echo $tahun; ?></h2>
    <ul>
        <?php
        $maxLevel    = $maxLevelByYear[$tahun] ?? 1;
        $maxUnlocked = $_SESSION['progress'][$userId][$tahun] ?? 1;

        for ($level = 1; $level <= $maxLevel; $level++):
        ?>
            <li>
                <?php if ($level <= $maxUnlocked): ?>
                    <a href="game-play.php?tahun=<?php echo $tahun; ?>&level=<?php echo $level; ?>">
                        Mula Tahun <?php echo $tahun; ?> — Level <?php echo $level; ?>
                    </a>
                <?php else: ?>
                    Level <?php echo $level; ?> (Terkunci)
                <?php endif; ?>
            </li>
        <?php endfor; ?>
    </ul>
<?php endforeach; ?>

<p>
    <a href="dashboard-student.php">← Kembali ke Dashboard Pelajar</a>
</p>

</body>
</html>
