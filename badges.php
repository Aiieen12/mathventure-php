<?php
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'pelajar') {
    header('Location: index.php');
    exit;
}

$id_user = (int)$_SESSION['user_id'];

// Ambil data profil pelajar
$stmt = $conn->prepare("SELECT s.*, u.username 
                        FROM student s 
                        JOIN users u ON s.id_user = u.id_user 
                        WHERE s.id_user = ?");
$stmt->bind_param("i", $id_user);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Ambil rekod lencana (Hanya skor penuh 3/3)
$unlockedBadges = [];
$resB = $conn->query("SELECT tahun, level FROM student_badges WHERE id_user = $id_user");
if ($resB) {
    while ($row = $resB->fetch_assoc()) {
        $unlockedBadges[$row['tahun'] . '-' . $row['level']] = true;
    }
}

$years  = [4, 5, 6];
$levels = [1, 2, 3, 4, 5];

$nama = trim(($data['firstname'] ?? '') . ' ' . ($data['lastname'] ?? ''));
$username = $data['username'] ?? 'Pelajar';

$avatar = !empty($data['avatar']) ? $data['avatar'] : 'avatar.png';
$levelUser = (int)($data['level'] ?? 1);

$currentXp = (float)($data['current_xp'] ?? 0);
$maxXp = (float)($data['max_xp'] ?? 100);
$pct = $maxXp > 0 ? ($currentXp / $maxXp) * 100 : 0;
$pct = max(0, min(100, $pct));
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencapaian Lencana - Mathventure</title>

    <!-- Consistent layout -->
    <link rel="stylesheet" href="asset/css/badges.css">
    <link rel="stylesheet" href="asset/css/student-layout.css">
</head>
<body>

<div class="game-bg"></div>
<button class="floating-menu-btn visible" onclick="toggleSidebar()">☰</button>

<!-- ✅ Sidebar ikut template page lain -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div>
            <div class="logo-text">Mathventure</div>
            <small>Student Mode</small>
        </div>
        <button class="close-btn" onclick="toggleSidebar()">✕</button>
    </div>

    <nav class="side-nav">
        <a href="dashboard-student.php" class="nav-item">🏠 <span>Dashboard</span></a>
        <a href="game-menu.php" class="nav-item">🗺️ <span>Peta Permainan</span></a>
        <a href="nota.php" class="nav-item">📚 <span>Nota Matematik</span></a>
        <a href="badges.php" class="nav-item active">🏅 <span>Pencapaian</span></a>
        <a href="profile.php" class="nav-item">👤 <span>Profil</span></a>
        <a href="logout.php" class="nav-item logout">🚪 <span>Log Keluar</span></a>
    </nav>

    <div class="sidebar-footer">
        <div class="player-card">
            <div class="avatar-frame">
                <img src="asset/images/<?php echo htmlspecialchars($avatar); ?>" alt="avatar">
            </div>
            <div class="player-info">
                <div class="lvl-badge">Level <?php echo $levelUser; ?></div>
                <div><strong><?php echo htmlspecialchars($nama ?: $username); ?></strong></div>
                <div class="xp-track">
                    <div class="xp-fill" style="width: <?php echo $pct; ?>%;"></div>
                </div>
            </div>
        </div>
    </div>
</aside>

<main class="main-content">
    <div class="top-bar">
        <div class="welcome-badge">
            Hai <span class="highlight"><?php echo htmlspecialchars($username); ?></span>, ini koleksi lencana anda! 🎉
        </div>
        <div class="game-clock" id="gameClock">--:--</div>
    </div>

    <section class="badges-page">
        <div class="badges-hero">
            <h1 class="badges-title">🏅 Pencapaian Lencana</h1>
            <p class="badges-subtitle">Kumpulkan semua lencana dengan menyelesaikan level di setiap tahun!</p>
        </div>

        <?php foreach ($years as $tahun): ?>
            <div class="year-block">
                <div class="year-head">
                    <h2 class="year-label">Tahun <?php echo $tahun; ?></h2>
                    <span class="year-tip">
                        Tip: Skor penuh <b>3/3</b> untuk buka lencana ✨
                    </span>
                </div>

                <div class="badges-grid">
                    <?php foreach ($levels as $lvl): ?>
                        <?php
                            $key = "$tahun-$lvl";
                            $isUnlocked = isset($unlockedBadges[$key]);
                            $imageSource = "badges/T{$tahun}L{$lvl}.png"; // folder badges/
                        ?>
                        <div class="badge-card <?php echo $isUnlocked ? 'is-unlocked' : 'is-locked'; ?>">
                            <div class="badge-visual">
                                <?php if ($isUnlocked): ?>
                                    <img src="<?php echo htmlspecialchars($imageSource); ?>" alt="Badge Tahun <?php echo $tahun; ?> Level <?php echo $lvl; ?>">
                                    <div class="badge-glow"></div>
                                <?php else: ?>
                                    <div class="badge-lock">
                                        <i class="fas fa-lock"></i>
                                    </div>
                                    <div class="badge-glass"></div>
                                <?php endif; ?>
                            </div>

                            <div class="badge-info">
                                <div class="badge-level">Level <?php echo $lvl; ?></div>
                                <div class="badge-status <?php echo $isUnlocked ? 'open' : 'closed'; ?>">
                                    <?php echo $isUnlocked ? 'DIBUKA' : 'TERKUNCI'; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

    </section>
</main>

<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('collapsed');
}

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
