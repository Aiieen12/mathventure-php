<?php
// auth/dashboard-teacher.php
require_once '../config.php';

// Pastikan user sudah login & role = guru
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'guru') {
    $_SESSION['error'] = 'Sila log masuk sebagai guru untuk akses halaman ini.';
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Guru | Mathventure</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS umum, kalau ada css khas dashboard boleh tambah lagi -->
    <link rel="stylesheet" href="../asset/css/auth-base.css">
</head>
<body>

<h1>Selamat datang, Cikgu <?php echo htmlspecialchars($_SESSION['username']); ?>! 🦕</h1>

<p>Ini adalah dashboard guru.</p>
<ul>
    <li>Nanti boleh tambah senarai kelas / murid</li>
    <li>Laporan markah kuiz</li>
    <li>Butang ke modul permainan, dsb.</li>
</ul>

<p>
    <a href="dashboard-student.php">⚠️ Cuba masuk dashboard pelajar (sepatutnya akan dihalang)</a>
</p>

<p>
    <a href="logout.php">Log Keluar</a>
</p>

</body>
</html>
