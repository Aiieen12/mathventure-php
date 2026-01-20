<?php
require_once '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pelajar') {
    header('Location: ../index.php');
    exit;
}

$userId = (int)$_SESSION['user_id'];

// Tarik data paling tepat dari database
$stmt = $conn->prepare("SELECT * FROM student WHERE id_user = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();

$username = $_SESSION['username'] ?? 'pelajar';
$namaPenuh = $data['firstname'] . ' ' . $data['lastname'];
$currentXp = (int)$data['current_xp'];
$maxXp = (int)($data['max_xp'] ?: 100);
$xpPercent = ($currentXp / $maxXp) * 100;
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Profil - Mathventure</title>
    <link rel="stylesheet" href="../asset/css/student-layout.css">
    <link rel="stylesheet" href="../asset/css/profile.css">
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
        <a href="game-menu.php" class="nav-item">🗺️ <span>Peta Permainan</span></a>
        <a href="nota.php" class="nav-item">📚 <span>Nota Matematik</span></a>
        <a href="badges.php" class="nav-item">🏅 <span>Pencapaian</span></a>
        <a href="profile.php" class="nav-item active">👤 <span>Profil</span></a>
        <a href="../logout.php" class="nav-item logout">🚪 <span>Log Keluar</span></a>
    </nav>
    <div class="sidebar-footer">
        <div class="player-card">
            <div class="avatar-frame">
                <img src="../asset/images/<?php echo $data['avatar'] ?: 'avatar.png'; ?>">
            </div>
            <div class="player-info">
                <div class="lvl-badge">Level <?php echo $data['level']; ?></div>
                <strong><?php echo htmlspecialchars($namaPenuh); ?></strong>
                <div class="xp-track">
                    <div class="xp-fill" style="width: <?php echo $xpPercent; ?>%;"></div>
                </div>
            </div>
        </div>
    </div>
</aside>

<main class="main-content">
    <div class="top-bar">
        <div class="welcome-badge">Profil <span class="highlight"><?php echo htmlspecialchars($data['firstname']); ?></span> ✨</div>
        <div class="game-clock" id="gameClock">--:--</div>
    </div>

    <section class="profile-layout">
        <article class="profile-card">
            <div class="profile-avatar">
                <img src="../asset/images/<?php echo $data['avatar'] ?: 'avatar.png'; ?>">
            </div>
            <h2><?php echo htmlspecialchars($namaPenuh); ?></h2>
            <p>@<?php echo htmlspecialchars($username); ?></p>

            <div class="profile-meta">
                <div><span class="meta-label">Kelas</span><span class="meta-value">Tahun <?php echo $data['year_level']; ?></span></div>
                <div><span class="meta-label">Nyawa</span><span class="meta-value"><?php echo $data['lives']; ?> ❤️</span></div>
                <div><span class="meta-label">Coins</span><span class="meta-value"><?php echo $data['coins']; ?> ⭐</span></div>
                <div><span class="meta-label">Level</span><span class="meta-value"><?php echo $data['level']; ?></span></div>
            </div>

            <div class="profile-progress">
                <span>Kemajuan XP</span>
                <div class="progress-bar"><div class="progress-fill" style="width: <?php echo $xpPercent; ?>%;"></div></div>
                <small><?php echo $currentXp; ?> / <?php echo $maxXp; ?> XP</small>
            </div>
        </article>

        <article class="profile-edit">
            <h2>Maklumat Akaun</h2>
            <div class="form-group"><label>Nama Penuh</label><input type="text" value="<?php echo htmlspecialchars($namaPenuh); ?>" readonly></div>
            <div class="form-group"><label>ID Pengguna</label><input type="text" value="<?php echo htmlspecialchars($username); ?>" readonly></div>
            <div class="form-group"><label>Tahun</label><input type="text" value="Tahun <?php echo $data['year_level']; ?>" readonly></div>
        </article>
    </section>
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