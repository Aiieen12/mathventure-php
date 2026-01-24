<?php
require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }

// 1. Pastikan user sudah login & role = pelajar
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pelajar') {
    $_SESSION['error'] = 'Sila log masuk sebagai pelajar untuk akses halaman ini.';
    header('Location: index.php');
    exit;
}

$id_user = $_SESSION['user_id'];

// 2. AMBIL DATA PROFIL UNTUK SIDEBAR
$stmt = $conn->prepare("SELECT s.*, u.username FROM student s JOIN users u ON s.id_user = u.id_user WHERE s.id_user = ?");
$stmt->bind_param("i", $id_user);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();

$nama = ($data['firstname'] ?? 'Pelajar') . ' ' . ($data['lastname'] ?? '');

// 3. Ambil data lencana dari database (student_badges)
$unlockedBadges = [];
$sqlB = "SELECT tahun, level FROM student_badges WHERE id_user = ?"; 
$stmtB = $conn->prepare($sqlB);
$stmtB->bind_param("i", $id_user);
$stmtB->execute();
$resB = $stmtB->get_result();
while ($row = $resB->fetch_assoc()) {
    $unlockedBadges[$row['tahun'] . '-' . $row['level']] = true;
}
$stmtB->close();

$years = [4, 5, 6];
$levelsPerYear = 5;
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Pencapaian Anda - Mathventure</title>
    <link rel="stylesheet" href="asset/css/student-layout.css">
    <link rel="stylesheet" href="asset/css/badges.css">
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
                <img src="asset/images/<?php echo !empty($data['avatar']) ? $data['avatar'] : 'avatar.png'; ?>" alt="Avatar">
            </div>
            <div class="player-info">
                <div class="lvl-badge">Level <?php echo $data['level'] ?? 1; ?></div>
                <div><strong><?php echo htmlspecialchars($nama); ?></strong></div>
                <div class="xp-track">
                    <?php 
                        $max_xp = ($data['max_xp'] > 0) ? $data['max_xp'] : 100;
                        $xp_percent = (($data['current_xp'] ?? 0) / $max_xp) * 100;
                    ?>
                    <div class="xp-fill" style="width: <?php echo $xp_percent; ?>%;"></div>
                </div>
            </div>
        </div>
    </div>
</aside>

<main class="main-content">
    <div class="top-bar">
        <div class="welcome-badge">
            Hai <span class="highlight"><?php echo htmlspecialchars($data['firstname'] ?? 'Pelajar'); ?></span>, ini koleksi lencana anda! 🎉
        </div>
        <div class="game-clock" id="gameClock">--:--</div>
    </div>

    <section class="achv-hero">
        <h1>🏅 Pencapaian Lencana</h1>
        <p>Kumpulkan semua lencana dengan menyelesaikan level di setiap tahun!</p>
    </section>

    <div class="achv-main">
        <?php foreach ($years as $tahun): ?>
            <div class="year-section" style="margin-bottom: 30px;">
                <h2 style="color: #2c3e50; border-left: 5px solid #3498db; padding-left: 15px; margin-bottom: 20px;">Tahun <?php echo $tahun; ?></h2>
                <div class="badge-row" style="display: flex; flex-wrap: wrap; gap: 20px;">
                    <?php for ($lvl = 1; $lvl <= $levelsPerYear; $lvl++): ?>
                        <?php
                            $badgeKey = "{$tahun}-{$lvl}";
                            $isUnlocked = isset($unlockedBadges[$badgeKey]);
                            $cardClass = $isUnlocked ? 'badge-card unlocked' : 'badge-card locked';
                        ?>
                        <article class="<?php echo $cardClass; ?>" style="background: white; padding: 15px; border-radius: 15px; width: 140px; text-align: center; box-shadow: 0 4px 10px rgba(0,0,0,0.05); opacity: <?php echo $isUnlocked ? '1' : '0.6'; ?>;">
                            <div class="badge-circle" style="width: 70px; height: 70px; margin: 0 auto 10px; background: #f0f0f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid <?php echo $isUnlocked ? '#f1c40f' : '#ddd'; ?>;">
                                <?php if ($isUnlocked): ?>
                                    <span style="font-size: 35px;">🏅</span>
                                <?php else: ?>
                                    <span style="font-size: 24px; color: #bdc3c7;">🔒</span>
                                <?php endif; ?>
                            </div>
                            <h4 style="margin: 5px 0; font-size: 14px; color: #333;">Level <?php echo $lvl; ?></h4>
                            <small style="color: <?php echo $isUnlocked ? '#27ae60' : '#95a5a6'; ?>; font-weight: bold; font-size: 10px; text-transform: uppercase;">
                                <?php echo $isUnlocked ? 'DIBUKA' : 'TERKUNCI'; ?>
                            </small>
                        </article>
                    <?php endfor; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<script>
function toggleSidebar() { document.getElementById('sidebar').classList.toggle('collapsed'); }
function updateClock() {
    const el = document.getElementById('gameClock');
    const now = new Date();
    el.textContent = now.toLocaleTimeString('ms-MY', { hour: '2-digit', minute: '2-digit' });
}
setInterval(updateClock, 1000); updateClock();
</script>
</body>
</html>