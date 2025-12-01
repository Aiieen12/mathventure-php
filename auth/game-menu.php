<?php
// auth/game-menu.php
require_once '../config.php';

// Pastikan user sudah login & role = pelajar
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'pelajar') {
    $_SESSION['error'] = 'Sila log masuk sebagai pelajar untuk akses halaman ini.';
    header('Location: index.php');
    exit;
}

$nama = $_SESSION['username'] ?? 'Pelajar';

// Senarai tahun & level (boleh ubah kemudian)
$tahunList = [4, 5, 6];
$levelPerYear = 3; // sekarang 3 level dulu setiap tahun
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

<nav>
    <a href="dashboard-student.php">Kembali ke Dashboard</a> |
    <a href="logout.php">Log Keluar</a>
</nav>

<hr>

<p>Pilih <strong>Tahun</strong> dan <strong>Level</strong> untuk mula permainan.</p>

<?php foreach ($tahunList as $tahun): ?>
    <h2>Tahun <?php echo $tahun; ?></h2>
    <ul>
        <?php for ($level = 1; $level <= $levelPerYear; $level++): ?>
            <li>
                <a href="game-play.php?tahun=<?php echo $tahun; ?>&level=<?php echo $level; ?>">
                    Mula Tahun <?php echo $tahun; ?> — Level <?php echo $level; ?>
                </a>
            </li>
        <?php endfor; ?>
    </ul>
<?php endforeach; ?>

</body>
</html>
