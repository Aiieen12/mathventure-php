<?php
// auth/badges.php

require_once '../config.php';

// Pastikan user sudah login & role = pelajar
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'pelajar') {
    $_SESSION['error'] = 'Sila log masuk sebagai pelajar untuk akses halaman ini.';
    header('Location: ../index.php');
    exit;
}

$nama = $_SESSION['username'] ?? 'Pelajar';

// Kita TIDAK gunakan database di sini supaya tak timbul error table tak wujud.
// Status "unlocked" ditentukan berdasarkan kewujudan fail gambar dalam folder qimages.
// Contoh nama fail: auth/qimages/T4L1.png
$years         = [4, 5, 6];
$levelsPerYear = 5;

/**
 * Tentukan sama ada badge untuk Tahun X Level Y wujud (fail png ada).
 */
function badgeExists(int $tahun, int $level): bool {
    $filePath = __DIR__ . "/qimages/T{$tahun}L{$level}.png";
    return file_exists($filePath);
}
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Pencapaian Anda - Mathventure</title>

    <!-- Layout & sidebar yang sama dengan dashboard -->
    <link rel="stylesheet" href="../asset/css/student-layout.css">
    <!-- Gaya khas untuk halaman pencapaian -->
    <link rel="stylesheet" href="../asset/css/badges.css">
</head>
<body>

<div class="game-bg"></div>

<button class="floating-menu-btn visible" id="openSidebarBtn">☰</button>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div>
            <div class="logo-text">Mathventure</div>
            <small>Student Mode</small>
        </div>
        <button class="close-btn" id="closeSidebarBtn">✕</button>
    </div>

    <nav class="side-nav">
        <a href="dashboard-student.php" class="nav-item">
            <span class="icon">🏠</span>
            <span>Dashboard</span>
        </a>
        <a href="game-menu.php" class="nav-item">
            <span class="icon">🗺️</span>
            <span>Peta Permainan</span>
        </a>
        <a href="nota.php" class="nav-item">
            <span class="icon">📘</span>
            <span>Nota Matematik</span>
        </a>
        <a href="badges.php" class="nav-item active">
            <span class="icon">🏅</span>
            <span>Pencapaian</span>
        </a>
        <a href="process-profile.php" class="nav-item">
            <span class="icon">👤</span>
            <span>Profil</span>
        </a>
        <a href="../logout.php" class="nav-item logout">
            <span class="icon">🚪</span>
            <span>Log Keluar</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="player-card">
            <div class="avatar-frame">
                <img src="../asset/images/avatar-default.png" alt="Avatar">
            </div>
            <div class="player-info">
                <div class="lvl-badge">Level Dino</div>
                <div style="font-size:0.85rem; margin-bottom:6px;"><?php echo htmlspecialchars($nama); ?></div>
                <div class="xp-track">
                    <div class="xp-fill" style="width: 40%;"></div>
                </div>
            </div>
        </div>
    </div>
</aside>

<!-- MAIN CONTENT -->
<main class="main-content">
    <!-- Top bar -->
    <div class="top-bar">
        <div class="welcome-badge">
            Hai <span class="highlight"><?php echo htmlspecialchars($nama); ?></span>, jom tengok
            <strong>Pencapaian Anda!</strong> 🎉
        </div>
        <div class="game-clock" id="gameClock">--:--</div>
    </div>

    <!-- Pengenalan Pencapaian -->
    <section class="achv-hero">
        <h1>Pencapaian Anda!</h1>
        <p>
            Halaman pencapaian ini memaparkan lencana yang anda peroleh dalam Mathventure.
            Setiap lencana akan muncul apabila anda menamatkan sesuatu level. Lencana
            disusun mengikut Tahun 4, 5 dan 6, dengan 5 level bagi setiap tahun.
        </p>
    </section>


        <!-- Panel kanan: lencana mengikut tahun -->
        <div class="achv-main">
            <header class="achv-main-header">
                <span>Galeri Lencana Mathventure</span>
            </header>

            <?php foreach ($years as $tahun): ?>
                <section class="year-section" id="tahun-<?php echo $tahun; ?>">
                    <h2>Tahun <?php echo $tahun; ?></h2>
                    <div class="badge-row">
                        <?php for ($lvl = 1; $lvl <= $levelsPerYear; $lvl++): ?>
                            <?php
                                $exists    = badgeExists($tahun, $lvl);
                                $cardClass = $exists ? 'badge-card unlocked' : 'badge-card locked';
                                $status    = $exists ? 'Dibuka' : 'Terkunci';

                                // laluan gambar relative dari badges.php
                                $imgRelPath = "qimages/T{$tahun}L{$lvl}.png";
                            ?>
                            <article class="<?php echo $cardClass; ?>">
                                <div class="badge-circle">
                                    <?php if ($exists): ?>
                                        <img src="<?php echo htmlspecialchars($imgRelPath); ?>"
                                             alt="Badge Tahun <?php echo $tahun; ?> Level <?php echo $lvl; ?>">
                                    <?php else: ?>
                                        <span class="badge-lock">🔒</span>
                                    <?php endif; ?>
                                </div>
                                <div class="badge-body">
                                    <h3>Level <?php echo $lvl; ?></h3>
                                    <p>
                                        <?php if ($exists): ?>
                                            Tahniah! Anda telah menamatkan Level <?php echo $lvl; ?> Tahun <?php echo $tahun; ?>.
                                        <?php else: ?>
                                            Selesaikan Level <?php echo $lvl; ?> Tahun <?php echo $tahun; ?>
                                            untuk membuka lencana ini.
                                        <?php endif; ?>
                                    </p>
                                    <span class="badge-status"><?php echo $status; ?></span>
                                </div>
                            </article>
                        <?php endfor; ?>
                    </div>
                </section>
            <?php endforeach; ?>
        </div>
    </section>
</main>

<script>
// Jam comel
function updateClock() {
    const el = document.getElementById('gameClock');
    if (!el) return;
    const now = new Date();
    let h = now.getHours();
    const m = now.getMinutes().toString().padStart(2, '0');
    let suffix = 'PG';
    if (h >= 12) {
        suffix = 'PTG';
        if (h > 12) h -= 12;
    }
    if (h === 0) h = 12;
    el.textContent = h + ':' + m + ' ' + suffix;
}
updateClock();
setInterval(updateClock, 60000);

// Toggle sidebar (mobile)
const sidebar        = document.getElementById('sidebar');
const openSidebarBtn = document.getElementById('openSidebarBtn');
const closeSidebarBtn= document.getElementById('closeSidebarBtn');

if (openSidebarBtn && sidebar) {
    openSidebarBtn.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
    });
}
if (closeSidebarBtn && sidebar) {
    closeSidebarBtn.addEventListener('click', () => {
        sidebar.classList.add('collapsed');
    });
}
</script>

</body>
</html>
