<?php
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pelajar') {
    header('Location: index.php');
    exit;
}

$userId = (int)$_SESSION['user_id'];

// Tarik data real dari database
$stmt = $conn->prepare("SELECT s.*, u.username FROM student s JOIN users u ON s.id_user = u.id_user WHERE s.id_user = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$data = $result = $stmt->get_result()->fetch_assoc();
$stmt->close();

$nama = $data['firstname'] . ' ' . $data['lastname'];
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Nota Matematik - Mathventure</title>
    <link rel="stylesheet" href="asset/css/student-layout.css">
    <link rel="stylesheet" href="asset/css/nota.css">

    <style>
        /* CSS MODAL YANG DIPERBAIKI */
        .mv-modal {
            position: fixed !important;
            inset: 0 !important;
            z-index: 9999;
            display: none; /* Akan ditukar ke 'grid' via JS */
            place-items: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .mv-modal.is-open {
            display: grid !important;
            opacity: 1;
            pointer-events: auto;
        }

        .mv-modal__backdrop {
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(3px);
        }

        .mv-modal__box {
            position: relative;
            width: min(700px, 90%);
            max-height: 85vh;
            background: white;
            padding: 30px;
            border-radius: 20px;
            overflow-y: auto;
            transform: translateY(20px);
            transition: transform 0.3s ease;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }

        .mv-modal.is-open .mv-modal__box {
            transform: translateY(0);
        }

        .mv-modal__close {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #ff4757;
            color: white;
            border: none;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 20px;
            z-index: 10;
        }

        .mv-modal-lock { overflow: hidden; }
        
        /* Tambahan untuk scrollbar modal */
        .mv-modal__box::-webkit-scrollbar { width: 8px; }
        .mv-modal__box::-webkit-scrollbar-thumb { background: #ccc; border-radius: 10px; }
    </style>
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
        <a href="nota.php" class="nav-item active">📘 <span>Nota Matematik</span></a>
        <a href="badges.php" class="nav-item">🏅 <span>Pencapaian</span></a>
        <a href="profile.php" class="nav-item">👤 <span>Profil</span></a>
        <a href="logout.php" class="nav-item logout">🚪 <span>Log Keluar</span></a>
    </nav>

    <div class="sidebar-footer">
        <div class="player-card">
            <div class="avatar-frame">
                <img src="asset/images/<?php echo $data['avatar'] ?: 'avatar.png'; ?>">
            </div>
            <div class="player-info">
                <div class="lvl-badge">Level <?php echo $data['level']; ?></div>
                <strong><?php echo htmlspecialchars($nama); ?></strong>
                <div class="xp-track">
                    <div class="xp-fill" style="width: <?php echo ($data['current_xp'] / ($data['max_xp'] ?: 100)) * 100; ?>%;"></div>
                </div>
            </div>
        </div>
    </div>
</aside>

<main class="main-content">
    <div class="top-bar">
        <div class="welcome-badge">Hai <span class="highlight"><?php echo htmlspecialchars($data['firstname']); ?></span>, jom rujukan nota! ✨</div>
        <div class="game-clock" id="gameClock">--:--</div>
    </div>

    <section class="note-hero">
        <h1>Pusat Nota Matematik</h1>
        <p>Klik butang di bawah untuk mengulangkaji pelajaran mengikut tahap.</p>
    </section>

    <div class="note-main">
        <div class="note-card-grid">
            <article class="note-card">
                <div class="note-card-banner banner-4"></div>
                <div class="note-card-body">
                    <h3>Darjah 4</h3>
                    <p>Asas nombor, operasi tambah, tolak, darab dan bahagi.</p>
                    <button class="note-btn" type="button" data-modal-target="#nota4Modal">Buka Nota Darjah 4</button>
                </div>
            </article>

            <article class="note-card">
                <div class="note-card-banner banner-5"></div>
                <div class="note-card-body">
                    <h3>Darjah 5</h3>
                    <p>Pecahan, peratus, dan pengiraan lebih mencabar.</p>
                    <button class="note-btn" type="button" data-modal-target="#nota5Modal">Buka Nota Darjah 5</button>
                </div>
            </article>

            <article class="note-card">
                <div class="note-card-banner banner-6"></div>
                <div class="note-card-body">
                    <h3>Darjah 6</h3>
                    <p>Wang, masa, pengurusan data dan graf.</p>
                    <button class="note-btn" type="button" data-modal-target="#nota6Modal">Buka Nota Darjah 6</button>
                </div>
            </article>
        </div>
    </div>
</main>

<div class="mv-modal" id="nota4Modal">
    <div class="mv-modal__backdrop" onclick="closeAllModals()"></div>
    <div class="mv-modal__box">
        <button class="mv-modal__close" onclick="closeAllModals()">✕</button>
        <h2>Nota Darjah 4</h2>
        <p><strong>1. Nilai Tempat:</strong> Sa, Puluh, Ratus, Ribu, Puluh Ribu.</p>
        <p><strong>2. Operasi Asas:</strong> Cara menambah dan menolak nombor besar.</p>
        <p><strong>3. Darab & Bahagi:</strong> Menghafal sifir 1-12.</p>
    </div>
</div>

<div class="mv-modal" id="nota5Modal">
    <div class="mv-modal__backdrop" onclick="closeAllModals()"></div>
    <div class="mv-modal__box">
        <button class="mv-modal__close" onclick="closeAllModals()">✕</button>
        <h2>Nota Darjah 5</h2>
        <p><strong>1. Pecahan:</strong> Menambah pecahan dengan penyebut berbeza.</p>
        <p><strong>2. Peratus:</strong> Menukar pecahan kepada peratus.</p>
        <p><strong>3. Nombor Perpuluhan:</strong> Darab dan bahagi perpuluhan.</p>
    </div>
</div>

<div class="mv-modal" id="nota6Modal">
    <div class="mv-modal__backdrop" onclick="closeAllModals()"></div>
    <div class="mv-modal__box">
        <button class="mv-modal__close" onclick="closeAllModals()">✕</button>
        <h2>Nota Darjah 6</h2>
        <p><strong>1. Wang:</strong> Untung, rugi, harga kos, dan harga jual.</p>
        <p><strong>2. Masa:</strong> Sistem 12 jam dan 24 jam.</p>
        <p><strong>3. Koordinat:</strong> Paksi-x dan paksi-y.</p>
    </div>
</div>

<script>
// Toggle sidebar
function toggleSidebar() { document.getElementById('sidebar').classList.toggle('collapsed'); }

// Jam
function updateClock() {
    const el = document.getElementById('gameClock');
    const now = new Date();
    el.textContent = now.toLocaleTimeString('ms-MY', { hour: '2-digit', minute: '2-digit' });
}
setInterval(updateClock, 1000); updateClock();

// FUNGSI MODAL UNIVERSAL (Satu kod untuk semua)
document.querySelectorAll('[data-modal-target]').forEach(button => {
    button.addEventListener('click', () => {
        const modal = document.querySelector(button.dataset.modalTarget);
        openModal(modal);
    });
});

function openModal(modal) {
    if (modal == null) return;
    modal.classList.add('is-open');
    document.body.classList.add('mv-modal-lock');
}

function closeAllModals() {
    document.querySelectorAll('.mv-modal.is-open').forEach(modal => {
        modal.classList.remove('is-open');
    });
    document.body.classList.remove('mv-modal-lock');
}

// Tutup guna butang ESC
document.addEventListener('keydown', e => {
    if (e.key === "Escape") closeAllModals();
});
</script>

</body>
</html>