<?php
require_once '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pelajar') {
    $_SESSION['error'] = 'Sila log masuk sebagai pelajar untuk akses halaman ini.';
    header('Location: ../index.php');
    exit;
}

$nama = $_SESSION['username'] ?? 'Pelajar Dino';

/*
 * Placeholder data – nanti boleh ganti dengan data sebenar dari DB
 * Contoh:
 *  - nyawa disimpan dalam jadual pelajar
 *  - mata = total markah yang pelajar kumpul
 *  - progress peta / nota / lencana ikut jadual lain
 */
$nyawaMaks   = 5;
$nyawaBaki   = $_SESSION['nyawa']        ?? 5;   // buat sementara, default 5
$jumlahMata  = $_SESSION['total_mata']   ?? 120; // contoh, sama macam total markah lama

$progressPeta  = $_SESSION['progress_peta']
    ?? 'Belum mula — Jom buka Tahun 4 • Level 1!';
$progressNota  = $_SESSION['progress_nota']
    ?? 'Belum baca — Pilih mana-mana nota untuk bermula.';
$progressBadge = $_SESSION['progress_badge']
    ?? '0 lencana terkumpul — Jawab soalan untuk kumpul badge!';
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Pelajar</title>
    <link rel="stylesheet" href="../asset/css/student-layout.css">
    <link rel="stylesheet" href="../asset/css/dashboard-student.css">
</head>
<body>

<div class="game-bg"></div>

<button class="floating-menu-btn visible" onclick="toggleSidebar()">☰</button>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div>
            <div class="logo-text">Mathventure</div>
            <small>Student Mode</small>
        </div>
        <button class="close-btn" onclick="toggleSidebar()">✕</button>
    </div>

    <nav class="side-nav">
        <a href="dashboard-student.php" class="nav-item active">
            <span class="icon">🏠</span> <span>Dashboard</span>
        </a>
        <a href="game-menu.php" class="nav-item">
            <span class="icon">🗺️</span> <span>Peta Permainan</span>
        </a>
        <a href="nota.php" class="nav-item">
            <span class="icon">📚</span> <span>Nota Matematik</span>
        </a>
        <a href="badges.php" class="nav-item">
            <span class="icon">🏅</span> <span>Pencapaian</span>
        </a>
        <a href="profile.php" class="nav-item">
            <span class="icon">👤</span> <span>Profil</span>
        </a>
        <a href="logout.php" class="nav-item logout">
            <span class="icon">🚪</span> <span>Log Keluar</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="player-card">
            <div class="avatar-frame">
                <img src="../asset/images/avatar1.png" alt="Avatar">
            </div>
            <div class="player-info">
                <div class="lvl-badge">Level Dino</div>
                <div><strong><?php echo htmlspecialchars($nama); ?></strong></div>
                <div class="xp-track">
                    <div class="xp-fill" style="width: 40%;"></div>
                </div>
            </div>
        </div>
    </div>
</aside>

<main class="main-content">
    <!-- bar atas: greeting + jam -->
    <div class="top-bar">
        <div class="welcome-badge">
            Hai <span class="highlight"><?php echo htmlspecialchars($nama); ?></span>, jom mulakan pengembaraan Matematik hari ini! 🌟
        </div>
        <div class="game-clock" id="gameClock">--:--</div>
    </div>

    <!-- HERO + KAD NYAWA / MATA -->
    <section class="hero-section">
        <div class="hero-card">
            <div class="hero-text">
                <h1>Selamat datang ke Mathventure!</h1>
                <p>
                    Jawab soalan, buka level baharu dan kumpul lencana hebat bersama Dino.
                </p>
            </div>

            <!-- gambar kecil dinosour2 dalam kotak -->
            <div class="hero-dino-small">
                <img src="../asset/images/dinasour2.png" alt="Dino comel">
            </div>
        </div>

        <div class="hud-stats">
            <!-- NYAWA -->
            <div class="stat-box yellow">
                <div class="stat-icon">❤️</div>
                <div class="stat-info">
                    <small>NYAWA</small>
                    <strong><?php echo $nyawaBaki . ' / ' . $nyawaMaks; ?></strong>
                    <div class="stat-desc">Satu jawapan salah akan tolak satu nyawa.</div>
                </div>
            </div>

            <!-- MATA -->
            <div class="stat-box blue">
                <div class="stat-icon">⭐</div>
                <div class="stat-info">
                    <small>MATA TERKUMPUL</small>
                    <strong><?php echo $jumlahMata; ?></strong>
                    <div class="stat-desc">Setiap jawapan betul akan menambah mata kamu.</div>
                </div>
            </div>
        </div>
    </section>

    <!-- SEKSYEN BAWAH: 3 KOLUM PROGRESS -->
    <section class="quick-links">
        <!-- Peta Permainan -->
        <article class="quick-card q-map" onclick="location.href='game-menu.php'">
            <div class="quick-header">
                <div class="quick-icon">🗺️</div>
                <div>
                    <h3>Peta Permainan</h3>
                    <span class="quick-sub">Lihat level yang telah dibuka</span>
                </div>
            </div>
            <p class="quick-progress">
                <?php echo htmlspecialchars($progressPeta); ?>
            </p>
            <button class="quick-btn">Pergi ke Peta Permainan</button>
        </article>

        <!-- Nota Matematik -->
        <article class="quick-card q-note" onclick="location.href='nota.php'">
            <div class="quick-header">
                <div class="quick-icon">📚</div>
                <div>
                    <h3>Nota Matematik</h3>
                    <span class="quick-sub">Sambung bacaan nota terakhir</span>
                </div>
            </div>
            <p class="quick-progress">
                <?php echo htmlspecialchars($progressNota); ?>
            </p>
            <button class="quick-btn">Pergi ke Nota Matematik</button>
        </article>

        <!-- Pencapaian -->
        <article class="quick-card q-badge" onclick="location.href='badges.php'">
            <div class="quick-header">
                <div class="quick-icon">🏅</div>
                <div>
                    <h3>Pencapaian</h3>
                    <span class="quick-sub">Lihat lencana yang telah dikumpul</span>
                </div>
            </div>
            <p class="quick-progress">
                <?php echo htmlspecialchars($progressBadge); ?>
            </p>
            <button class="quick-btn">Pergi ke Pencapaian</button>
        </article>
    </section>
</main>

<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('collapsed');
}

function updateClock() {
    const el = document.getElementById('gameClock');
    const now = new Date();
    el.textContent = now.toLocaleTimeString('ms-MY', { hour: '2-digit', minute: '2-digit' });
}
setInterval(updateClock, 1000);
updateClock();
</script>

</body>
</html>
