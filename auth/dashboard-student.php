<?php
require_once '../config.php';

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../asset/css/dashboard-student.css?v=9999">
</head>
<body>

<div class="container">
    <!-- Background Dino Parallax -->
    <div class="dino-bg"></div>

    <!-- Topbar -->
    <header class="topbar">
        <div class="logo">🦖 Mathventure</div>
        <nav class="navbar">
            <a href="dashboard-student.php">Dashboard</a>
            <a href="game-menu.php">Peta Permainan</a>
            <a href="logout.php" class="logout">Log Keluar</a>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <div class="signboard">
                Selamat Datang, <span><?php echo htmlspecialchars($nama); ?></span>! 🌟
            </div>
            <p>Selamat bersedia untuk pengembaraan matematik dalam dunia dinosaur!</p>
        </div>
        <img src="../asset/img/dino-student.png" alt="Dinosaur Student" class="dino-hero">
    </section>

    <!-- Menu Section -->
    <section class="menu-section">
        <h2>Menu Utama</h2>
        <div class="menu-grid">
            <a href="game-menu.php" class="card">
                <img src="../asset/img/dino-map.png" class="icon">
                <h3>Peta Permainan</h3>
                <p>Terokai dunia dinosaur dan cabar diri dengan permainan matematik!</p>
            </a>

            <a href="#" class="card soon">
                <img src="../asset/img/dino-book.png" class="icon">
                <h3>Modul Pembelajaran</h3>
                <p>Segera hadir! Modul latihan matematik untuk pelajar.</p>
            </a>

            <a href="#" class="card soon">
                <img src="../asset/img/dino-trophy.png" class="icon">
                <h3>Pencapaian</h3>
                <p>Lihat markah dan pencapaian sepanjang pengembaraan.</p>
            </a>
        </div>
    </section>
</div>

<script>
// Parallax Hero & Dino
document.addEventListener('mousemove', (e) => {
    const x = (e.clientX / window.innerWidth - 0.5) * 20;
    const y = (e.clientY / window.innerHeight - 0.5) * 20;
    const dino = document.querySelector('.dino-hero');
    const bg = document.querySelector('.dino-bg');
    if(dino) dino.style.transform = `translateY(-10px) rotateX(${y}deg) rotateY(${x}deg)`;
    if(bg) bg.style.transform = `translate(${x/2}px, ${y/2}px)`;
});
</script>

</body>
</html>
