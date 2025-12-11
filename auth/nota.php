<?php
// auth/modul-belajar.php

require_once '../config.php';

// Pastikan user sudah login & role = pelajar
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'pelajar') {
    $_SESSION['error'] = 'Sila log masuk sebagai pelajar untuk akses halaman ini.';
    header('Location: ../index.php');
    exit;
}

$nama = $_SESSION['username'] ?? 'Pelajar';
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Nota Matematik - Mathventure</title>

    <!-- Layout & sidebar yang sama dengan dashboard -->
    <link rel="stylesheet" href="../asset/css/student-layout.css">
    <!-- Gaya khas untuk halaman nota -->
    <link rel="stylesheet" href="../asset/css/nota.css">
</head>
<body>

<!-- background sama macam dashboard -->
<div class="game-bg"></div>

<!-- Butang floating untuk buka sidebar (mobile) -->
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
        <a href="nota.php" class="nav-item active">
            <span class="icon">📘</span>
            <span>Nota Matematik</span>
        </a>
        <a href="badges.php" class="nav-item">
            <span class="icon">🏅</span>
            <span>Pencapaian</span>
        </a>
        <a href="profile.php" class="nav-item">
            <span class="icon">👤</span>
            <span>Profil</span>
        </a>
        <a href="index.php" class="nav-item logout">
            <span class="icon">🚪</span>
            <span>Log Keluar</span>
        </a>
    </nav>

    <!-- Boleh kekalkan / ubah ikut dashboard awak -->
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
    <!-- Top bar (hai anis21 + jam) -->
    <div class="top-bar">
        <div class="welcome-badge">
            Hai <span class="highlight"><?php echo htmlspecialchars($nama); ?></span>, jom belajar dengan
            <strong>Nota Matematik</strong> hari ini! ✨
        </div>
        <div class="game-clock" id="gameClock">--:--</div>
    </div>

    <!-- HERO / PENGENALAN NOTA -->
    <section class="note-hero">
        <h1>Nota Untuk Kamu</h1>
        <p>
            Halaman ini ialah pusat sumber digital untuk semua nota Matematik kamu.
            Nota disusun mengikut <strong>Darjah 4, 5 dan 6</strong> dengan deskripsi ringkas supaya
            kamu boleh pilih bahan rujukan yang paling sesuai.
        </p>
    </section>

    <!-- SUSUNAN UTAMA (ikut wireframe) -->
    <section class="note-wrapper">
        <!-- Panel kiri: logo + senarai nota -->
        <aside class="note-sidebar">
            <div class="note-logo-box">
                <span class="note-logo-text">Logo</span>
            </div>

            <div class="note-sidebar-title">NOTA</div>

            <ul class="note-list">
                <!-- Nanti awak boleh tukar href="" ke fail nota sebenar (PDF / halaman lain) -->
                <li><a href="#">Darjah 4 – Nombor & Operasi</a></li>
                <li><a href="#">Darjah 4 – Pecahan</a></li>
                <li><a href="#">Darjah 5 – Nombor Bulat</a></li>
                <li><a href="#">Darjah 5 – Peratus</a></li>
                <li><a href="#">Darjah 6 – Wang & Masa</a></li>
                <li><a href="#">Darjah 6 – Data & Graf</a></li>
            </ul>
        </aside>

        <!-- Panel kanan: 3 kad kategori darjah -->
        <div class="note-main">
            <header class="note-main-header">
                <span>NOTA UNTUK KAMU</span>
            </header>

            <div class="note-card-grid">
                <!-- Darjah 4 -->
                <article class="note-card">
                    <div class="note-card-banner banner-4"></div>
                    <div class="note-card-body">
                        <h3>Darjah 4</h3>
                        <p>
                            Koleksi nota asas nombor, tambah dan tolak, serta topik-topik pengenalan
                            lain yang sesuai untuk pemula.
                        </p>
                        <a href="#" class="note-btn">Buka Nota Darjah 4</a>
                    </div>
                </article>

                <!-- Darjah 5 -->
                <article class="note-card">
                    <div class="note-card-banner banner-5"></div>
                    <div class="note-card-body">
                        <h3>Darjah 5</h3>
                        <p>
                            Ulangkaji konsep yang lebih mencabar seperti darab, bahagi, pecahan,
                            peratus dan aplikasi dalam situasi harian.
                        </p>
                        <a href="#" class="note-btn">Buka Nota Darjah 5</a>
                    </div>
                </article>

                <!-- Darjah 6 -->
                <article class="note-card">
                    <div class="note-card-banner banner-6"></div>
                    <div class="note-card-body">
                        <h3>Darjah 6</h3>
                        <p>
                            Sedia untuk UPSR versi kamu! Nota meliputi topik campuran,
                            wang, masa, graf dan penyelesaian masalah.
                        </p>
                        <a href="#" class="note-btn">Buka Nota Darjah 6</a>
                    </div>
                </article>
            </div>
        </div>
    </section>
</main>

<script>
// Jam comel di penjuru atas (sama konsep dengan dashboard)
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

// Toggle sidebar untuk mobile
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
