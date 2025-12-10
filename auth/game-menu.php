<?php
require_once '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pelajar') {
    $_SESSION['error'] = 'Sila log masuk sebagai pelajar untuk akses halaman ini.';
    header('Location: ../index.php');
    exit;
}

$nama = $_SESSION['username'] ?? 'Pelajar Dino';

// sementara – nanti ganti dengan data DB
$maxLevelPerYear = 5;
$unlocked = [
    4 => 1,
    5 => 1,
    6 => 1
];
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Peta Pengembaraan</title>
    <link rel="stylesheet" href="../asset/css/student-layout.css">
    <link rel="stylesheet" href="../asset/css/game-menu.css">
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
        <a href="dashboard-student.php" class="nav-item">
            <span class="icon">🏠</span> <span>Dashboard</span>
        </a>
        <a href="game-menu.php" class="nav-item active">
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
                <img src="../images/avatar1.png" alt="Avatar">
            </div>
            <div class="player-info">
                <div class="lvl-badge">Level Dino</div>
                <div><strong><?php echo htmlspecialchars($nama); ?></strong></div>
                <div class="xp-track">
                    <div class="xp-fill" style="width: 30%;"></div>
                </div>
            </div>
        </div>
    </div>
</aside>

<main class="main-content">
    <div class="top-bar">
        <div class="welcome-badge">
            Ini adalah <span class="highlight">Peta Pengembaraan</span>,
            <?php echo htmlspecialchars($nama); ?>! Pilih tahun untuk mula bermain. 🌈
        </div>
        <div class="game-clock" id="gameClock">--:--</div>
    </div>

    <section class="adventure-map">
        <h2 class="map-title">Pilih Pengembaraan Matematik</h2>
        <p class="map-subtitle">
            Mulakan dari <strong>Level 1</strong>, buka laluan baharu dan kumpul lencana kegemilangan!
        </p>

        <!-- TAHUN 4 -->
        <div class="map-row">
            <div class="year-flag year-4">
                <span class="year-label">Tahun 4</span>
            </div>
            <div class="level-path">
                <?php for ($lvl = 1; $lvl <= $maxLevelPerYear; $lvl++):
                    $isUnlocked = $lvl <= $unlocked[4];
                    $isCurrent  = $lvl == $unlocked[4];
                ?>
                    <button
                        class="level-node <?php echo $isUnlocked ? 'unlocked' : 'locked'; ?> <?php echo $isCurrent ? 'current' : ''; ?>"
                        <?php if (!$isUnlocked) echo 'disabled'; ?>
                        onclick="location.href='game-play.php?tahun=4&level=<?php echo $lvl; ?>'">
                        <span class="lvl-text">L<?php echo $lvl; ?></span>
                        <span class="lvl-tag"><?php echo $lvl == 1 ? 'Main' : 'Stage '.$lvl; ?></span>
                        <span class="lock-icon">🔒</span>
                    </button>
                <?php endfor; ?>
            </div>
        </div>

        <!-- TAHUN 5 -->
        <div class="map-row">
            <div class="year-flag year-5">
                <span class="year-label">Tahun 5</span>
            </div>
            <div class="level-path">
                <?php for ($lvl = 1; $lvl <= $maxLevelPerYear; $lvl++):
                    $isUnlocked = $lvl <= $unlocked[5];
                    $isCurrent  = $lvl == $unlocked[5];
                ?>
                    <button
                        class="level-node <?php echo $isUnlocked ? 'unlocked' : 'locked'; ?> <?php echo $isCurrent ? 'current' : ''; ?>"
                        <?php if (!$isUnlocked) echo 'disabled'; ?>
                        onclick="location.href='game-play.php?tahun=5&level=<?php echo $lvl; ?>'">
                        <span class="lvl-text">L<?php echo $lvl; ?></span>
                        <span class="lvl-tag"><?php echo $lvl == 1 ? 'Main' : 'Stage '.$lvl; ?></span>
                        <span class="lock-icon">🔒</span>
                    </button>
                <?php endfor; ?>
            </div>
        </div>

        <!-- TAHUN 6 -->
        <div class="map-row">
            <div class="year-flag year-6">
                <span class="year-label">Tahun 6</span>
            </div>
            <div class="level-path">
                <?php for ($lvl = 1; $lvl <= $maxLevelPerYear; $lvl++):
                    $isUnlocked = $lvl <= $unlocked[6];
                    $isCurrent  = $lvl == $unlocked[6];
                ?>
                    <button
                        class="level-node <?php echo $isUnlocked ? 'unlocked' : 'locked'; ?> <?php echo $isCurrent ? 'current' : ''; ?>"
                        <?php if (!$isUnlocked) echo 'disabled'; ?>
                        onclick="location.href='game-play.php?tahun=6&level=<?php echo $lvl; ?>'">
                        <span class="lvl-text">L<?php echo $lvl; ?></span>
                        <span class="lvl-tag"><?php echo $lvl == 1 ? 'Main' : 'Stage '.$lvl; ?></span>
                        <span class="lock-icon">🔒</span>
                    </button>
                <?php endfor; ?>
            </div>
        </div>

        <p class="map-hint">
            Tip: Jawab semua <strong>3/3 soalan</strong> dalam satu level untuk membuka level seterusnya! 🏆
        </p>
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
