<?php
// auth/profile.php

require_once '../config.php';

// Pastikan user sudah login & role = pelajar
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'pelajar') {
    $_SESSION['error'] = 'Sila log masuk sebagai pelajar untuk akses halaman ini.';
    header('Location: ../index.php');
    exit;
}

$nama     = $_SESSION['username'] ?? 'Pelajar';
$email    = $_SESSION['email']    ?? 'pelajar@example.com'; // jika tiada dlm session, guna placeholder
$kelas    = $_SESSION['kelas']    ?? 'Tahun 4 - 6';
$avatar   = '../asset/images/avatar-default.png';           // boleh tukar ikut sistem awak

// placeholder statistik – nanti boleh diganti dengan data sebenar dari DB
$nyawa          = 5;
$totalMata      = 120;
$levelDibuka    = '3 / 15';
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Profil Pelajar - Mathventure</title>

    <!-- Layout asas pelajar (sidebar, background, top bar) -->
    <link rel="stylesheet" href="../asset/css/student-layout.css">
    <!-- Gaya khas untuk profil -->
    <link rel="stylesheet" href="../asset/css/profile.css">
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
        <a href="badges.php" class="nav-item">
            <span class="icon">🏅</span>
            <span>Pencapaian</span>
        </a>
        <a href="profile.php" class="nav-item active">
            <span class="icon">👤</span>
            <span>Profil</span>
        </a>
        <a href="index.php" class="nav-item logout">
            <span class="icon">🚪</span>
            <span>Log Keluar</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="player-card">
            <div class="avatar-frame">
                <img src="../asset/images/avatar.png" alt="Avatar">
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
            Hai <span class="highlight"><?php echo htmlspecialchars($nama); ?></span>, ini adalah
            <strong>profil Mathventure</strong> anda ✨
        </div>
        <div class="game-clock" id="gameClock">--:--</div>
    </div>

    <!-- HERO PROFIL -->
    <section class="profile-hero">
        <div class="profile-hero-text">
            <h1>Profil Pelajar</h1>
            <p>
                Di sini anda boleh melihat maklumat akaun, kemajuan pembelajaran dan menukar
                beberapa tetapan asas seperti nama paparan dan kata laluan.
            </p>
        </div>
        <div class="profile-hero-badge">
            <span class="mini-label">Status</span>
            <strong>Peneroka Mathventure</strong>
            <small>Teruskan menjawab soalan untuk kumpul lebih banyak lencana!</small>
        </div>
    </section>

    <!-- LAYOUT UTAMA: kad profil + borang edit -->
    <section class="profile-layout">
        <!-- Kad ringkasan profil -->
        <article class="profile-card">
            <div class="profile-avatar">
                <img src="../asset/images/avatar.png" alt="Avatar">
            </div>
            <h2><?php echo htmlspecialchars($nama); ?></h2>
            <p class="profile-username">@<?php echo htmlspecialchars($nama); ?></p>

            <div class="profile-meta">
                <div>
                    <span class="meta-label">Kelas</span>
                    <span class="meta-value"><?php echo htmlspecialchars($kelas); ?></span>
                </div>
                <div>
                    <span class="meta-label">Nyawa</span>
                    <span class="meta-value"><?php echo $nyawa; ?> ❤️</span>
                </div>
                <div>
                    <span class="meta-label">Mata</span>
                    <span class="meta-value"><?php echo $totalMata; ?></span>
                </div>
                <div>
                    <span class="meta-label">Level Dibuka</span>
                    <span class="meta-value"><?php echo $levelDibuka; ?></span>
                </div>
            </div>

            <div class="profile-progress">
                <span class="progress-title">Kemajuan Tahap</span>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 35%;"></div>
                </div>
                <small>Anggaran kemajuan keseluruhan. Nanti boleh sambung dengan data sebenar.</small>
            </div>
        </article>

        <!-- Borang edit maklumat asas -->
        <article class="profile-edit">
            <h2>Kemaskini Maklumat</h2>
            <p class="edit-desc">
                Tukar nama paparan, emel atau kata laluan. (Buat masa ini, borang ini belum disambung ke database –
                boleh guna sebagai template UI dahulu.)
            </p>

            <form method="post" action="#">
                <div class="form-row">
                    <div class="form-group">
                        <label for="nama_paparan">Nama paparan</label>
                        <input type="text" id="nama_paparan" name="nama_paparan"
                               value="<?php echo htmlspecialchars($nama); ?>">
                    </div>

                    <div class="form-group">
                        <label for="email">Emel</label>
                        <input type="email" id="email" name="email"
                               value="<?php echo htmlspecialchars($email); ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="kelas">Kelas / Tahun</label>
                        <input type="text" id="kelas" name="kelas"
                               value="<?php echo htmlspecialchars($kelas); ?>">
                    </div>

                    <div class="form-group">
                        <label for="avatar_url">URL Avatar (pilihan)</label>
                        <input type="text" id="avatar_url" name="avatar_url"
                               placeholder="Contoh: https://.../avatar.png">
                    </div>
                </div>

                <hr class="form-divider">

                <div class="form-row">
                    <div class="form-group">
                        <label for="password_baru">Kata laluan baharu</label>
                        <input type="password" id="password_baru" name="password_baru" placeholder="Biarkan kosong jika tidak mahu tukar">
                    </div>

                    <div class="form-group">
                        <label for="password_sah">Sahkan kata laluan</label>
                        <input type="password" id="password_sah" name="password_sah">
                    </div>
                </div>

                <button type="submit" class="save-btn">Simpan Perubahan</button>
            </form>
        </article>
    </section>

    <!-- Ringkasan tambahan -->
    <section class="profile-stats">
        <div class="stat-pill">
            <span class="stat-label">Total Nyawa Kini</span>
            <strong><?php echo $nyawa; ?></strong>
        </div>
        <div class="stat-pill">
            <span class="stat-label">Mata Terkumpul</span>
            <strong><?php echo $totalMata; ?></strong>
        </div>
        <div class="stat-pill">
            <span class="stat-label">Level Dibuka</span>
            <strong><?php echo $levelDibuka; ?></strong>
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
