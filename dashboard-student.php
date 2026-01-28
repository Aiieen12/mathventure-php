<?php
require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'pelajar') {
    $_SESSION['error'] = 'Sila log masuk sebagai pelajar untuk akses halaman ini.';
    header('Location: index.php');
    exit;
}

$id_user = (int)$_SESSION['user_id'];

/* ==========================
   UPDATE STATUS & KEHADIRAN
   ========================== */

// 1) Update status aktif
$conn->query("UPDATE users SET last_activity = NOW() WHERE id_user = $id_user");

// 2) Auto-kehadiran hari ini (jika belum wujud)
$checkAtt = $conn->prepare("SELECT COUNT(*) as wujud FROM attendance WHERE id_user = ? AND date_recorded = CURDATE()");
$checkAtt->bind_param("i", $id_user);
$checkAtt->execute();
$resCheck = $checkAtt->get_result()->fetch_assoc();

if ((int)$resCheck['wujud'] === 0) {
    $insAtt = $conn->prepare("INSERT INTO attendance (id_user, status, date_recorded) VALUES (?, 'H', CURDATE())");
    $insAtt->bind_param("i", $id_user);
    $insAtt->execute();
    $insAtt->close();
}
$checkAtt->close();

/* ==========================
   AMBIL DATA PELAJAR
   ========================== */
$stmt = $conn->prepare("SELECT s.*, u.username FROM student s JOIN users u ON s.id_user = u.id_user WHERE s.id_user = ?");
$stmt->bind_param("i", $id_user);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();

$firstname   = $data['firstname'] ?? '';
$lastname    = $data['lastname'] ?? '';
$nama        = trim($firstname . ' ' . $lastname);
$avatar      = $data['avatar'] ?: 'avatar.png';

$nyawaMaks   = 5;
$nyawaBaki   = (int)($data['lives'] ?? 0);
$jumlahMata  = (int)($data['coins'] ?? 0);

$currentXp   = (int)($data['current_xp'] ?? 0);
$maxXp       = (int)($data['max_xp'] ?? 100);
if ($maxXp <= 0) $maxXp = 100;

$xpPercent   = ($currentXp / $maxXp) * 100;
if ($xpPercent < 0) $xpPercent = 0;
if ($xpPercent > 100) $xpPercent = 100;

$tahunSemasa = (int)($data['year_level'] ?? 4);
$levelSemasa = (int)($data['level'] ?? 1);

$progressPeta = "Tahun $tahunSemasa • Level $levelSemasa";
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Dashboard Pelajar • Mathventure</title>

    <!-- ✅ Load layout dulu, kemudian dashboard -->
    <link rel="stylesheet" href="asset/css/student-layout.css">
    <link rel="stylesheet" href="asset/css/dashboard-student.css">

</head>
<body>
<div class="game-bg"></div>

<!-- Backdrop untuk mobile drawer -->
<div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebar(true)"></div>

<!-- Floating button (muncul bila mobile/ sidebar tutup) -->
<button class="floating-menu-btn" id="menuBtn" type="button" onclick="toggleSidebar()"
        aria-label="Buka menu">☰</button>

<aside class="sidebar" id="sidebar" aria-label="Menu Pelajar">
    <div class="sidebar-header">
        <div class="brand">
            <div class="logo-text">Mathventure</div>
            <small>Student Mode</small>
        </div>
        <button class="close-btn" type="button" onclick="toggleSidebar(true)" aria-label="Tutup menu">✕</button>
    </div>

    <nav class="side-nav" aria-label="Navigasi">
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
                <img src="asset/images/<?php echo htmlspecialchars($avatar); ?>" alt="Avatar pelajar">
            </div>
            <div class="player-info">
                <div class="lvl-badge">Level <?php echo (int)$levelSemasa; ?></div>
                <strong title="<?php echo htmlspecialchars($nama); ?>">
                    <?php echo htmlspecialchars($nama ?: 'Pelajar'); ?>
                </strong>

                <div class="xp-row">
                    <span class="xp-label">XP</span>
                    <span class="xp-value"><?php echo $currentXp; ?>/<?php echo $maxXp; ?></span>
                </div>

                <div class="xp-track" aria-label="Kemajuan XP">
                    <div class="xp-fill" style="width: <?php echo $xpPercent; ?>%;"></div>
                </div>
            </div>
        </div>
    </div>
</aside>

<main class="main-content">
    <header class="top-bar">
        <div class="welcome-pill">
            Hai <span class="highlight"><?php echo htmlspecialchars($firstname ?: 'kawan'); ?></span>! Jom main & belajar 🎒✨
        </div>

        <div class="top-actions">
            <div class="game-clock" id="gameClock">--:--</div>
        </div>
    </header>

    <!-- HERO + STATS -->
    <!-- HERO + STATS -->
<section class="hero-section">
  <div class="hero-card" role="region" aria-label="Kad Selamat Datang">

    <!-- LEFT: TEXT -->
    <div class="hero-text">
      <h1>Selamat datang ke Mathventure!</h1>

      <p class="hero-sub">
        Jom jadi hebat Matematik! Sebelum main, kita <span class="hero-strong">baca nota dulu</span> supaya senang jawab soalan 🦖✨
      </p>

      <div class="hero-meta">
        <span class="hero-pill">📌 <?php echo htmlspecialchars($progressPeta); ?></span>
        <span class="hero-pill">🏅 Cari lencana baru!</span>
      </div>

      <!-- BOX ARAHAN MEMBACA -->
      <div class="hero-readbox" aria-label="Arahan Membaca">
        <div class="read-title">📚 Masa Membaca 3 Minit!</div>

        <div class="read-desc">
          Sebelum jawab soalan, <b>baca nota dulu</b> ya 😊
          <span class="read-tip">Tip: baca perlahan-lahan & ikut langkah kiraan.</span>
        </div>

        <div class="read-step">
          ① Baca contoh &nbsp;•&nbsp; ② Faham cara &nbsp;•&nbsp; ③ Baru jawab kuiz ✨
        </div>
      </div>
    </div>

    <!-- RIGHT: DINO -->
    <div class="hero-dino" aria-hidden="true">
      <img src="asset/images/dinasour2.png" alt="Dino comel">
    </div>
  </div>

  <!-- STATS -->
  <div class="hud-stats" aria-label="Status Pelajar">
    <div class="stat-box stat-life">
      <div class="stat-icon" aria-hidden="true">❤️</div>
      <div class="stat-info">
        <small>NYAWA</small>
        <strong><?php echo $nyawaBaki . ' / ' . $nyawaMaks; ?></strong>
      </div>
      <div class="stat-badge">Jaga nyawa!</div>
    </div>

    <div class="stat-box stat-coin">
      <div class="stat-icon" aria-hidden="true">⭐</div>
      <div class="stat-info">
        <small>COINS</small>
        <strong><?php echo $jumlahMata; ?></strong>
      </div>
      <div class="stat-badge">Kumpul lagi!</div>
    </div>

    <div class="stat-box stat-xp">
      <div class="stat-icon" aria-hidden="true">🚀</div>
      <div class="stat-info">
        <small>XP</small>
        <strong><?php echo (int)round($xpPercent); ?>%</strong>
      </div>
      <div class="stat-badge">Level up!</div>
    </div>
  </div>
</section>


    <!-- QUICK MENU -->
    <section class="quick-links" aria-label="Menu Pantas">
        <article class="quick-card q-map" onclick="location.href='game-menu.php'">
            <div class="q-left">
                <div class="q-icon">🗺️</div>
            </div>
            <div class="q-mid">
                <h3>Peta Permainan</h3>
                <p>Sambung dari <b><?php echo htmlspecialchars($progressPeta); ?></b></p>
            </div>
            <div class="q-right">➡️</div>
        </article>

        <article class="quick-card q-note" onclick="location.href='nota.php'">
            <div class="q-left">
                <div class="q-icon">📚</div>
            </div>
            <div class="q-mid">
                <h3>Nota Matematik</h3>
                <p>Jom baca nota Tahun <b><?php echo (int)$tahunSemasa; ?></b></p>
            </div>
            <div class="q-right">➡️</div>
        </article>

        <article class="quick-card q-badge" onclick="location.href='badges.php'">
            <div class="q-left">
                <div class="q-icon">🏅</div>
            </div>
            <div class="q-mid">
                <h3>Pencapaian</h3>
                <p>Lihat lencana yang kamu dah dapat!</p>
            </div>
            <div class="q-right">➡️</div>
        </article>

        <article class="quick-card q-profile" onclick="location.href='profile.php'">
            <div class="q-left">
                <div class="q-icon">👤</div>
            </div>
            <div class="q-mid">
                <h3>Profil</h3>
                <p>Avatar, maklumat & kemajuan kamu</p>
            </div>
            <div class="q-right">➡️</div>
        </article>
    </section>

    <!-- Bottom safe space untuk mobile nav -->
    <div class="safe-space"></div>
</main>

<!-- MOBILE BOTTOM NAV -->
<nav class="mobile-nav" aria-label="Navigasi Mudah Alih">
    <a class="mnav-item active" href="dashboard-student.php">
        <span class="mnav-ico">🏠</span><span class="mnav-txt">Home</span>
    </a>
    <a class="mnav-item" href="game-menu.php">
        <span class="mnav-ico">🗺️</span><span class="mnav-txt">Peta</span>
    </a>
    <a class="mnav-item" href="nota.php">
        <span class="mnav-ico">📚</span><span class="mnav-txt">Nota</span>
    </a>
    <a class="mnav-item" href="badges.php">
        <span class="mnav-ico">🏅</span><span class="mnav-txt">Lencana</span>
    </a>
    <button class="mnav-item mnav-btn" type="button" onclick="toggleSidebar()"
            aria-label="Menu">
        <span class="mnav-ico">☰</span><span class="mnav-txt">Menu</span>
    </button>
</nav>

<script>
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    const menuBtn = document.getElementById('menuBtn');

    function isMobile(){
        return window.matchMedia('(max-width: 900px)').matches;
    }

    function toggleSidebar(forceClose = false){
        const willOpen = forceClose ? false : sidebar.classList.contains('collapsed');

        if (forceClose) {
            sidebar.classList.add('collapsed');
        } else {
            sidebar.classList.toggle('collapsed');
        }

        // Mobile: backdrop + lock scroll
        if (isMobile()) {
            const isOpen = !sidebar.classList.contains('collapsed');
            backdrop.classList.toggle('show', isOpen);
            document.body.classList.toggle('no-scroll', isOpen);
        }

        // floating btn
        syncMenuBtn();
    }

    function syncMenuBtn(){
        // On desktop: menu button hidden
        // On mobile: show floating btn when sidebar closed
        if (!isMobile()) {
            menuBtn.classList.remove('show');
            backdrop.classList.remove('show');
            document.body.classList.remove('no-scroll');
            sidebar.classList.remove('collapsed'); // desktop default open
            return;
        }
        const closed = sidebar.classList.contains('collapsed');
        menuBtn.classList.toggle('show', closed);
    }

    function updateClock() {
        const el = document.getElementById('gameClock');
        const now = new Date();
        el.textContent = now.toLocaleTimeString('ms-MY', { hour: '2-digit', minute: '2-digit' });
    }
    setInterval(updateClock, 1000); updateClock();

    // init: mobile default collapsed
    window.addEventListener('load', () => {
        if (isMobile()) sidebar.classList.add('collapsed');
        syncMenuBtn();
    });
    window.addEventListener('resize', syncMenuBtn);
</script>

</body>
</html>
