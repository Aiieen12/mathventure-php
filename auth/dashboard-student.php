<?php
// auth/dashboard-student.php
require_once '../config.php';

// Pastikan user sudah login & role = pelajar
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'pelajar') {
    $_SESSION['error'] = 'Sila log masuk sebagai pelajar untuk akses halaman ini.';
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Pelajar | Mathventure</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../asset/css/auth-base.css">
</head>
<body>

<h1>Hai <?php echo htmlspecialchars($_SESSION['username']); ?>! 🎒</h1>

<p>Ini adalah dashboard pelajar.</p>
<ul>
    <li>Nanti boleh tambah butang “Mula Permainan”</li>
    <li>Senarai topik matematik</li>
    <li>Markah kuiz sebelum ini</li>
</ul>

<p>
    <a href="dashboard-teacher.php">⚠️ Cuba masuk dashboard guru (sepatutnya akan dihalang)</a>
</p>

<p>
    <a href="logout.php">Log Keluar</a>
</p>

</body>
</html>
