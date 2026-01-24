<?php
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pelajar') {
    $_SESSION['error'] = 'Sila log masuk sebagai pelajar untuk akses halaman ini.';
    header('Location: index.php');
    exit;
}

$id_user = (int)$_SESSION['user_id'];

// ==========================================================
// FIX: UPDATE STATUS & KEHADIRAN (CARA PALING SELAMAT)
// ==========================================================
// 1. Update status aktif
$conn->query("UPDATE users SET last_activity = NOW() WHERE id_user = $id_user");

// 2. Semak kehadiran tanpa sebut nama kolum ID (Guna COUNT)
$checkAtt = $conn->prepare("SELECT COUNT(*) as wujud FROM attendance WHERE id_user = ? AND date_recorded = CURDATE()");
$checkAtt->bind_param("i", $id_user);
$checkAtt->execute();
$resCheck = $checkAtt->get_result()->fetch_assoc();

if ($resCheck['wujud'] == 0) {
    // Jika belum ada rekod hari ini (0), baru masukkan
    $insAtt = $conn->prepare("INSERT INTO attendance (id_user, status, date_recorded) VALUES (?, 'H', CURDATE())");
    $insAtt->bind_param("i", $id_user);
    $insAtt->execute();
    $insAtt->close();
}
$checkAtt->close();
// ==========================================================

// AMBIL DATA REAL DARI DATABASE
$stmt = $conn->prepare("SELECT s.*, u.username FROM student s JOIN users u ON s.id_user = u.id_user WHERE s.id_user = ?");
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();

$nama        = $data['firstname'] . ' ' . $data['lastname'];
$nyawaMaks   = 5;
$nyawaBaki   = $data['lives'];
$jumlahMata  = $data['coins']; 
$currentXp   = $data['current_xp'];
$maxXp       = $data['max_xp'] ?: 100;

$progressPeta = "Level semasa: Year " . ($data['year_level'] ?? '4') . " • Level " . ($data['level'] ?? '1');
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Pelajar</title>
    <link rel="stylesheet" href="asset/css/student-layout.css">
    <link rel="stylesheet" href="asset/css/dashboard-student.css">
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
        <a href="dashboard-student.php" class="nav-item active">🏠 <span>Dashboard</span></a>
        <a href="game-menu.php" class="nav-item">🗺️ <span>Peta Permainan</span></a>
        <a href="nota.php" class="nav-item">📚 <span>Nota Matematik</span></a>
        <a href="badges.php" class="nav-item">🏅 <span>Pencapaian</span></a>
        <a href="profile.php" class="nav-item">👤 <span>Profil</span></a>
        
        <a href="logout.php" class="nav-item logout">🚪 <span>Log Keluar</span></a>
    </nav>

    <div class="sidebar-footer">
        <div class="player-card">
            <div class="avatar-frame">
                <img src="asset/images/<?php echo $data['avatar'] ?: 'avatar.png'; ?>" alt="Avatar">
            </div>
            <div class="player-info">
                <div class="lvl-badge">Level <?php echo $data['level']; ?></div>
                <strong><?php echo htmlspecialchars($nama); ?></strong>
                <div class="xp-track">
                    <div class="xp-fill" style="width: <?php echo ($currentXp/$maxXp)*100; ?>%;"></div>
                </div>
            </div>
        </div>
    </div>
</aside>

<main class="main-content">
    <div class="top-bar">
        <div class="welcome-badge">Hai <span class="highlight"><?php echo htmlspecialchars($data['firstname']); ?></span>, jom belajar! 🌟</div>
        <div class="game-clock" id="gameClock">--:--</div>
    </div>

    <section class="hero-section">
        <div class="hero-card">
            <div class="hero-text">
                <h1>Selamat datang ke Mathventure!</h1>
                <p>Jawab soalan, buka level baharu dan kumpul lencana hebat bersama Dino.</p>
            </div>
            <div class="hero-dino-small">
                <img src="asset/images/dinasour2.png" alt="Dino comel">
            </div>
        </div>

        <div class="hud-stats">
            <div class="stat-box yellow">
                <div class="stat-icon">❤️</div>
                <div class="stat-info">
                    <small>NYAWA</small>
                    <strong><?php echo $nyawaBaki . ' / ' . $nyawaMaks; ?></strong>
                </div>
            </div>
            <div class="stat-box blue">
                <div class="stat-icon">⭐</div>
                <div class="stat-info">
                    <small>COINS</small>
                    <strong><?php echo $jumlahMata; ?></strong>
                </div>
            </div>
        </div>
    </section>

    <section class="quick-links">
        <article class="quick-card q-map" onclick="location.href='game-menu.php'">
            <h3>Peta Permainan</h3>
            <p><?php echo $progressPeta; ?></p>
        </article>
        <article class="quick-card q-note" onclick="location.href='nota.php'">
            <h3>Nota Matematik</h3>
            <p>Jom baca nota Tahun <?php echo $data['year_level']; ?></p>
        </article>
    </section>
</main>

<script>
function toggleSidebar() { 
    const sb = document.getElementById('sidebar');
    sb.classList.toggle('collapsed'); 
}
function updateClock() {
    const el = document.getElementById('gameClock');
    const now = new Date();
    el.textContent = now.toLocaleTimeString('ms-MY', { hour: '2-digit', minute: '2-digit' });
}
setInterval(updateClock, 1000); updateClock();
</script>
</body>
</html>