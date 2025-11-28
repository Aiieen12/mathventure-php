<?php
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pelajar') {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Pelajar</title>
</head>
<body>
    <h2>Dashboard Pelajar</h2>
    <p>Selamat datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
    <p><a href="logout.php">Log Keluar</a></p>
</body>
</html>
