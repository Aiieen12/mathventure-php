<?php
require_once '../config.php';

// Pastikan session bermula
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Semak akses pelajar
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pelajar') {
    $_SESSION['error'] = 'Sila log masuk sebagai pelajar untuk akses halaman ini.';
    header('Location: ../index.php');
    exit;
}

$userId = (int)$_SESSION['user_id'];
$nama = $_SESSION['username'] ?? 'Pelajar Dino';

// Ambil semua level progres dari DB
$sqlUser = "SELECT level_t4, level_t5, level_t6 FROM student WHERE id_user = ?";
$stmtUser = $conn->prepare($sqlUser);
$stmtUser->bind_param("i", $userId);
$stmtUser->execute();
$resUser = $stmtUser->get_result();
$userData = $resUser->fetch_assoc();

$maxLevelPerYear = 5;

// Sekarang kita setkan level unik untuk setiap tahun
$unlocked = [
    4 => (int)($userData['level_t4'] ?? 1),
    5 => (int)($userData['level_t5'] ?? 1),
    6 => (int)($userData['level_t6'] ?? 1)
];
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mathventure | Peta Pengembaraan</title>
    <link rel="stylesheet" href="../asset/css/student-layout.css">
    <link rel="stylesheet" href="../asset/css/game-menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        <a href="../logout.php" class="nav-item logout">
            <span class="icon">🚪</span> <span>Log Keluar</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="player-card">
            <div class="avatar-frame">
                <img src="../asset/images/avatar.png" alt="Avatar">
            </div>
            <div class="player-info">
                <div class="lvl-badge">Explorer</div>
                <div><strong><?php echo htmlspecialchars($nama); ?></strong></div>
                <div class="xp-track">
                    <div class="xp-fill" style="width: 50%;"></div>
                </div>
            </div>
        </div>
    </div>
</aside>

<main class="main-content">
    <div class="top-bar">
        <div class="welcome-badge">
            Sedia untuk mengembara, <span class="highlight"><?php echo htmlspecialchars($nama); ?></span>? 🌈
        </div>
        <div class="game-clock" id="gameClock">--:--</div>
    </div>

    <section class="adventure-map">
        <h2 class="map-title">Pilih Pengembaraan Matematik</h2>
        <p class="map-subtitle">
            Selesaikan <strong>skor penuh (3/3)</strong> untuk membuka laluan baharu!
        </p>

        <?php 
        $years = [4, 5, 6];
        foreach ($years as $yr): 
        ?>
        <div class="map-row">
            <div class="year-flag year-<?php echo $yr; ?>">
                <span class="year-label">Tahun <?php echo $yr; ?></span>
            </div>
            <div class="level-path">
                <?php for ($lvl = 1; $lvl <= $maxLevelPerYear; $lvl++):
                    // LOGIK UTAMA:
                    // 1. Level terbuka jika ia kurang atau sama dengan currentMaxLevel dari DB
                    $isUnlocked = $lvl <= $unlocked[$yr];
                    // 2. Tandakan level terkini yang perlu diselesaikan
                    $isCurrent  = $lvl == $unlocked[$yr];
                ?>
                    <button
                        class="level-node <?php echo $isUnlocked ? 'unlocked' : 'locked'; ?> <?php echo $isCurrent ? 'current' : ''; ?>"
                        <?php echo !$isUnlocked ? 'disabled' : ''; ?>
                        onclick="location.href='game-play.php?tahun=<?php echo $yr; ?>&level=<?php echo $lvl; ?>'">
                        <span class="lvl-text">L<?php echo $lvl; ?></span>
                        <span class="lvl-tag"><?php echo $lvl == 1 ? 'Mula' : 'Tahap '.$lvl; ?></span>
                        <?php if (!$isUnlocked): ?>
                            <span class="lock-icon"><i class="fas fa-lock"></i></span>
                        <?php endif; ?>
                    </button>
                <?php endfor; ?>
            </div>
        </div>
        <?php endforeach; ?>

        <p class="map-hint">
            <i class="fas fa-info-circle"></i> Tip: Level seterusnya akan terbuka secara automatik setelah anda berjaya mendapat markah penuh.
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