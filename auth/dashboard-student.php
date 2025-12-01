<?php
// auth/dashboard-student.php
require_once '../config.php';

// Pastikan user sudah login & role = pelajar
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'pelajar') {
    $_SESSION['error'] = 'Sila log masuk sebagai pelajar untuk akses halaman ini.';
    header('Location: index.php');
    exit;
}

$nama = $_SESSION['username'] ?? 'Pelajar';
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Pelajar | Mathventure</title>
</head>
<body>

<h1>Dashboard Pelajar</h1>
<p>Selamat datang, <strong><?php echo htmlspecialchars($nama); ?></strong>!</p>

<!-- Nav bar ringkas (kosong dulu, hanya link asas) -->
<nav>
    <a href="dashboard-student.php">Dashboard</a> |
    <a href="game-menu.php">Peta</a> |
    <a href="logout.php">Log Keluar</a>
</nav>

<hr>

<h2>Menu Utama</h2>
<p>
    Buat masa ini, kita fokus untuk lihat pergerakan sistem permainan.
</p>
<ul>
    <li>Klik <strong>Permainan Matematik</strong> di bahagian nav untuk ke menu game.</li>
</ul>

</body>
</html>
