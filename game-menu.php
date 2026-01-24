<?php
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pelajar') {
    header('Location: index.php'); exit;
}

$userId = (int)$_SESSION['user_id'];
$stmtUser = $conn->prepare("SELECT * FROM student WHERE id_user = ?");
$stmtUser->bind_param("i", $userId);
$stmtUser->execute();
$profData = $stmtUser->get_result()->fetch_assoc();

$nama = $profData['firstname'] . ' ' . $profData['lastname'];
$unlocked = [
    4 => (int)($profData['level_t4'] ?? 1),
    5 => (int)($profData['level_t5'] ?? 1),
    6 => (int)($profData['level_t6'] ?? 1)
];
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Mathventure | Peta Pengembaraan</title>
    <link rel="stylesheet" href="asset/css/student-layout.css">
    <link rel="stylesheet" href="asset/css/game-menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&display=swap" rel="stylesheet">
</head>
<body>
<div class="game-bg"></div>
<button class="floating-menu-btn visible" onclick="toggleSidebar()">☰</button>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div><div class="logo-text">Mathventure</div><small>Student Mode</small></div>
        <button class="close-btn" onclick="toggleSidebar()">✕</button>
    </div>
    <nav class="side-nav">
        <a href="dashboard-student.php" class="nav-item">🏠 <span>Dashboard</span></a>
        <a href="game-menu.php" class="nav-item active">🗺️ <span>Peta Permainan</span></a>
        <a href="nota.php" class="nav-item">📚 <span>Nota Matematik</span></a>
        <a href="badges.php" class="nav-item">🏅 <span>Pencapaian</span></a>
        <a href="profile.php" class="nav-item">👤 <span>Profil</span></a>
        <a href="logout.php" class="nav-item logout">🚪 <span>Log Keluar</span></a>
    </nav>
    <div class="sidebar-footer">
        <div class="player-card">
            <div class="avatar-frame">
                <img src="asset/images/<?php echo $profData['avatar'] ?: 'avatar.png'; ?>">
            </div>
            <div class="player-info">
                <div class="lvl-badge">Level <?php echo $profData['level']; ?></div>
                <div><strong><?php echo htmlspecialchars($nama); ?></strong></div>
                <div class="xp-track">
                    <div class="xp-fill" style="width: <?php echo ($profData['current_xp']/($profData['max_xp']?:100))*100; ?>%;"></div>
                </div>
            </div>
        </div>
    </div>
</aside>

<main class="main-content">
    <div class="top-bar">
        <div class="welcome-badge">Sedia mengembara, <span class="highlight"><?php echo htmlspecialchars($profData['firstname']); ?></span>? 🌈</div>
        <div class="game-clock" id="gameClock">--:--</div>
    </div>
    
    <section class="adventure-map">
        <h2 class="map-title">Pilih Pengembaraan</h2>
        <p class="map-subtitle">Selesaikan <strong>skor penuh</strong> untuk membuka laluan baharu!</p>

        <?php foreach ([4, 5, 6] as $yr): ?>
        <div class="map-row">
            <div class="year-flag year-<?php echo $yr; ?>">
                <span class="year-label">Tahun <?php echo $yr; ?></span>
                <span class="year-sub">Matematik</span>
            </div>
            
            <div class="level-path">
                <?php for ($lvl = 1; $lvl <= 5; $lvl++): 
                    $isUnlocked = $lvl <= $unlocked[$yr];
                    // Level "Current" adalah level tertinggi yang terbuka dan belum selesai
                    $isCurrent = $lvl == $unlocked[$yr];
                ?>
                    <button class="level-node <?php echo $isUnlocked ? 'unlocked' : 'locked'; ?> <?php echo ($isCurrent && $isUnlocked) ? 'current' : ''; ?>" 
                            <?php echo !$isUnlocked ? 'disabled' : ''; ?>
                            onclick="location.href='quiz-engine.php?tahun=<?php echo $yr; ?>&level=<?php echo $lvl; ?>'">
                        
                        <span class="lvl-text">L<?php echo $lvl; ?></span>
                        <span class="lvl-tag"><?php echo ($lvl == 1) ? 'Mula' : 'Tahap '.$lvl; ?></span>
                        
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
function toggleSidebar() { document.getElementById('sidebar').classList.toggle('collapsed'); }

function updateClock() {
    const el = document.getElementById('gameClock');
    const now = new Date();
    el.textContent = now.toLocaleTimeString('ms-MY', { hour: '2-digit', minute: '2-digit', hour12: false });
}
setInterval(updateClock, 1000);
updateClock();
</script>
</body>
</html>