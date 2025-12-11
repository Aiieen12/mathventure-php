<?php
// auth/dashboard-teacher.php
session_start();
require_once '../config.php';

// Cek User Login sebagai GURU
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'guru') {
    $_SESSION['error'] = 'Sila log masuk sebagai guru untuk akses halaman ini.';
    header('Location: ../index.php');
    exit;
}

// Data Guru
$namaGuru = $_SESSION['username'] ?? 'Cikgu Aisyah';
$subjek   = "Matematik";

/*
 * Data Placeholder (Ganti dengan database nanti)
 * Untuk Guru, kita ganti:
 * - Nyawa -> Peratus Kehadiran Murid Hari Ini
 * - Mata  -> Jumlah Murid
 */
$peratusKehadiran = 92; // Contoh: 92% murid hadir
$jumlahMurid      = 35; // Contoh: 35 murid dalam kelas

// Status ringkas untuk dipaparkan di kad bawah
$statusKehadiran = "2 murid tidak hadir hari ini (Ali, Muthu).";
$statusMarkah    = "Data markah Ujian 1 telah dikemaskini.";
$statusProfil    = "Profil guru lengkap.";

?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Guru | Mathventure</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="../asset/css/dashboard-teacher.css">
</head>
<body>

<div class="game-bg"></div>

<button class="floating-menu-btn" onclick="toggleSidebar()">☰</button>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div>
            <div class="logo-text">Mathventure</div>
            <small class="badge-role">Teacher Mode</small>
        </div>
        <button class="close-btn" onclick="toggleSidebar()">✕</button>
    </div>

    <nav class="side-nav">
        <a href="dashboard-teacher.php" class="nav-item active">
            <span class="icon">🏠</span> <span>Dashboard</span>
        </a>
        
        <a href="teacher-student_attendant.php" class="nav-item">
            <span class="icon">📅</span> <span>Kehadiran</span>
        </a>
        
        <a href="teacher-student_marks.php" class="nav-item">
            <span class="icon">📊</span> <span>Markah Pelajar</span>
        </a>
        
        <a href="teacher-profile.php" class="nav-item">
            <span class="icon">👤</span> <span>Profil Guru</span>
        </a>
        
        <a href="logout.php" class="nav-item logout">
            <span class="icon">🚪</span> <span>Log Keluar</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="player-card">
            <div class="avatar-frame">
                <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Avatar">
            </div>
            <div class="player-info">
                <div class="lvl-badge">Guru Kelas</div>
                <div><strong><?php echo htmlspecialchars($namaGuru); ?></strong></div>
                <div class="xp-track">
                    <div class="xp-fill" style="width: 75%;"></div>
                </div>
                <small style="font-size:0.6rem; color:#888;">Sesi Pengajaran 75%</small>
            </div>
        </div>
    </div>
</aside>

<main class="main-content">
    <div class="top-bar">
        <div class="welcome-badge">
            Selamat Kembali, <span class="highlight"><?php echo htmlspecialchars($namaGuru); ?></span>! Siap sedia untuk mengajar? 🍎
        </div>
        <div class="game-clock" id="gameClock">--:--</div>
    </div>

    <section class="hero-section">
        <div class="hero-card">
            <div class="hero-text">
                <h1>Panel Kawalan Guru</h1>
                <p>
                    Pantau prestasi murid, rekod kehadiran, dan uruskan markah dalam satu paparan mudah.
                </p>
            </div>
            <div class="hero-dino-small">
                <img src="https://cdn-icons-png.flaticon.com/512/3429/3429149.png" alt="Teacher Icon">
            </div>
        </div>

        <div class="hud-stats">
            <div class="stat-box yellow">
                <div class="stat-icon">📈</div>
                <div class="stat-info">
                    <small>KEHADIRAN HARINI</small>
                    <strong><?php echo $peratusKehadiran; ?>%</strong>
                    <div class="stat-desc">Purata kehadiran kelas 4 Dinamik.</div>
                </div>
            </div>

            <div class="stat-box blue">
                <div class="stat-icon">👨‍🎓</div>
                <div class="stat-info">
                    <small>JUMLAH MURID</small>
                    <strong><?php echo $jumlahMurid; ?></strong>
                    <div class="stat-desc">Bilangan total pelajar aktif.</div>
                </div>
            </div>
        </div>
    </section>

    <section class="quick-links">
        
        <article class="quick-card q-map" onclick="location.href='teacher-attendance.php'">
            <div class="quick-header">
                <div class="quick-icon">📅</div>
                <div>
                    <h3>Kehadiran Pelajar</h3>
                    <span class="quick-sub">Rekod kehadiran harian</span>
                </div>
            </div>
            <p class="quick-progress">
                <?php echo htmlspecialchars($statusKehadiran); ?>
            </p>
            <button class="quick-btn">Buka Kehadiran</button>
        </article>

        <article class="quick-card q-note" onclick="location.href='teacher-marks.php'">
            <div class="quick-header">
                <div class="quick-icon">📊</div>
                <div>
                    <h3>Markah & Prestasi</h3>
                    <span class="quick-sub">Analisis markah ujian</span>
                </div>
            </div>
            <p class="quick-progress">
                <?php echo htmlspecialchars($statusMarkah); ?>
            </p>
            <button class="quick-btn">Lihat Markah</button>
        </article>

        <article class="quick-card q-badge" onclick="location.href='teacher-profile.php'">
            <div class="quick-header">
                <div class="quick-icon">⚙️</div>
                <div>
                    <h3>Tetapan Profil</h3>
                    <span class="quick-sub">Info diri & kelas</span>
                </div>
            </div>
            <p class="quick-progress">
                <?php echo htmlspecialchars($statusProfil); ?>
            </p>
            <button class="quick-btn">Kemaskini Profil</button>
        </article>

    </section>
</main>

<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('active');
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