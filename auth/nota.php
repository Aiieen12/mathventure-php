<?php
// auth/nota.php

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Matematik - Mathventure</title>

    <!-- Layout & sidebar yang sama dengan dashboard -->
    <link rel="stylesheet" href="../asset/css/student-layout.css">
    <!-- Gaya khas untuk halaman nota -->
    <link rel="stylesheet" href="../asset/css/nota.css">

    <style>
/* FORCE modal overlay (takkan jatuh bawah) */
.mv-modal{
  position: fixed !important;
  inset: 0 !important;
  z-index: 9999999 !important;

  display: none;              /* default tutup */
  place-items: center;

  opacity: 0;
  pointer-events: none;
  transition: opacity .2s ease;
}

.mv-modal.is-open{
  opacity: 1;
  pointer-events: auto;
}

.mv-modal__backdrop{
  position: absolute !important;
  inset: 0 !important;
  background: rgba(0,0,0,.55);
}

.mv-modal__box{
  position: relative !important;
  width: min(760px, 92%);
  max-height: 80vh;
  overflow: auto;

  background: #fff;
  border-radius: 18px;
  padding: 24px;
  box-shadow: 0 18px 50px rgba(0,0,0,.25);

  transform: translateY(12px) scale(.98);
  opacity: 0;
  transition: transform .2s ease, opacity .2s ease;
}

.mv-modal.is-open .mv-modal__box{
  transform: translateY(0) scale(1);
  opacity: 1;
}

.mv-modal__close{
  position: absolute !important;
  top: 12px;
  right: 12px;

  width: 36px;
  height: 36px;
  border: none;
  border-radius: 10px;
  background: #ff7675;
  color: #fff;
  cursor: pointer;
  font-weight: bold;
}

.mv-modal-lock{ overflow: hidden; }
</style>


</head>
<body>

<!-- background sama macam dashboard -->
<div class="game-bg"></div>

<!-- Butang floating untuk buka sidebar (mobile) -->
<button class="floating-menu-btn visible" id="openSidebarBtn">☰</button>

<!-- SIDEBAR -->
<aside class="sidebar collapsed" id="sidebar">
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

    <div class="sidebar-footer">
        <div class="player-card">
            <div class="avatar-frame">
                <img src="../asset/images/avatar.png" alt="Avatar">
            </div>
            <div class="player-info">
                <div class="lvl-badge">Level Dino</div>
                <div style="font-size:0.85rem; margin-bottom:6px;">
                    <?php echo htmlspecialchars($nama); ?>
                </div>
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
            Hai <span class="highlight"><?php echo htmlspecialchars($nama); ?></span>, jom belajar dengan
            <strong>Nota Matematik</strong> hari ini! ✨
        </div>
        <div class="game-clock" id="gameClock">--:--</div>
    </div>

    <!-- HERO -->
    <section class="note-hero">
        <h1>Nota Untuk Kamu</h1>
        <p>
            Halaman ini ialah pusat sumber digital untuk semua nota Matematik kamu.
            Nota disusun mengikut <strong>Darjah 4, 5 dan 6</strong> dengan deskripsi ringkas supaya
            kamu boleh pilih bahan rujukan yang paling sesuai.
        </p>
    </section>

    <!-- PANEL KANAN -->
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
                    <button class="note-btn" type="button" id="btnNota4">Buka Nota Darjah 4</button>

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
                    <button class="note-btn" type="button" data-modal="nota5Modal">
                        Buka Nota Darjah 5
                    </button>
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
                    <button class="note-btn" type="button" data-modal="nota6Modal">
                        Buka Nota Darjah 6
                    </button>
                </div>
            </article>
        </div>
    </div>
</main>

<!-- =========================
     POPUP SMOOTH: NOTA DARJAH 4
     ========================= -->
<div class="mv-modal" id="nota4Modal" aria-hidden="true" style="display:none;">
  <div class="mv-modal__backdrop" data-close="true"></div>

  <div class="mv-modal__box" role="dialog" aria-modal="true" aria-labelledby="nota4Title">
    <button class="mv-modal__close" type="button" data-close="true">✕</button>

    <h2 id="nota4Title">Nota Darjah 4</h2>

    <p><strong>1. Nilai Tempat</strong><br>
      Nombor mempunyai nilai tempat seperti sa, puluh, ratus dan ribu.<br>
      Contoh: 23,489 = 2 puluh ribu + 3 ribu + 4 ratus + 8 puluh + 9
    </p>

    <p><strong>2. Operasi Tambah</strong><br>
      Tambah ialah mencantumkan dua nombor.<br>
      Contoh: 245 + 123 = 368
    </p>

    <p><strong>3. Operasi Tolak</strong><br>
      Tolak ialah mencari baki.<br>
      Contoh: 500 − 275 = 225
    </p>

    <p><strong>4. Operasi Darab</strong><br>
      Darab ialah tambah berulang.<br>
      Contoh: 4 × 6 = 24
    </p>

    <p><strong>5. Operasi Bahagi</strong><br>
      Bahagi ialah membahagi sama rata.<br>
      Contoh: 20 ÷ 4 = 5
    </p>
  </div>
</div>

<!-- =========================
     POPUP NOTA DARJAH 5
     ========================= -->
<div class="mv-modal" id="nota5Modal" aria-hidden="true">
  <div class="mv-modal__backdrop" data-close="true"></div>

  <div class="mv-modal__box" role="dialog">
    <button class="mv-modal__close" data-close="true">✕</button>

    <h2>Nota Darjah 5</h2>

    <p><strong>1. Darab & Bahagi</strong><br>
    Darab ialah tambah berulang dan bahagi ialah pengagihan sama rata.<br>
    Contoh: 6 × 4 = 24, 24 ÷ 6 = 4
    </p>

    <p><strong>2. Pecahan</strong><br>
    Pecahan mewakili sebahagian daripada keseluruhan.<br>
    Contoh: ½, ¾
    </p>

    <p><strong>3. Peratus</strong><br>
    Peratus ialah pecahan daripada 100.<br>
    Contoh: 25% = 25/100
    </p>
  </div>
</div>

<!-- =========================
     POPUP NOTA DARJAH 6
     ========================= -->
<div class="mv-modal" id="nota6Modal" aria-hidden="true">
  <div class="mv-modal__backdrop" data-close="true"></div>

  <div class="mv-modal__box" role="dialog">
    <button class="mv-modal__close" data-close="true">✕</button>

    <h2>Nota Darjah 6</h2>

    <p><strong>1. Wang</strong><br>
    Pengiraan melibatkan tambah, tolak dan baki wang.<br>
    Contoh: RM10 − RM3.50 = RM6.50
    </p>

    <p><strong>2. Masa</strong><br>
    Pengiraan masa melibatkan jam dan minit.<br>
    Contoh: 2:15 PTG ke 3:00 PTG = 45 minit
    </p>

    <p><strong>3. Data & Graf</strong><br>
    Data dipersembahkan dalam bentuk graf bar atau piktograf.
    </p>
  </div>
</div>


<script>
// ===== Jam comel =====
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

// ===== Toggle sidebar untuk mobile =====
const sidebar         = document.getElementById('sidebar');
const openSidebarBtn  = document.getElementById('openSidebarBtn');
const closeSidebarBtn = document.getElementById('closeSidebarBtn');

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

/* =========================
   MODAL SMOOTH (Darjah 4)
   ========================= */
document.addEventListener("DOMContentLoaded", () => {
  const btnNota4 = document.getElementById("btnNota4");
  const nota4Modal = document.getElementById("nota4Modal");

  if (!btnNota4 || !nota4Modal) return;

  function openNota4() {
    nota4Modal.style.display = "grid";      // wajib supaya modal muncul
    requestAnimationFrame(() => {           // bagi browser render dulu
      nota4Modal.classList.add("is-open");  // baru animasi jalan
    });
    nota4Modal.setAttribute("aria-hidden", "false");
    document.body.classList.add("mv-modal-lock");
  }

  function closeNota4() {
    nota4Modal.classList.remove("is-open");
    nota4Modal.setAttribute("aria-hidden", "true");
    document.body.classList.remove("mv-modal-lock");

    setTimeout(() => {
      nota4Modal.style.display = "none";
    }, 200);
  }

  btnNota4.addEventListener("click", openNota4);

  nota4Modal.addEventListener("click", (e) => {
    if (e.target && e.target.dataset && e.target.dataset.close === "true") {
      closeNota4();
    }
  });

  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && nota4Modal.classList.contains("is-open")) {
      closeNota4();
    }
  });
});

document.addEventListener("DOMContentLoaded", () => {

  function openModal(modal) {
    modal.style.display = "grid";
    requestAnimationFrame(() => modal.classList.add("is-open"));
    document.body.classList.add("mv-modal-lock");
  }

  function closeModal(modal) {
    modal.classList.remove("is-open");
    document.body.classList.remove("mv-modal-lock");
    setTimeout(() => modal.style.display = "none", 200);
  }

  // Buka modal ikut data-modal
  document.querySelectorAll("[data-modal]").forEach(btn => {
    btn.addEventListener("click", () => {
      const modalId = btn.dataset.modal;
      const modal = document.getElementById(modalId);
      if (modal) openModal(modal);
    });
  });

  // Tutup modal
  document.querySelectorAll(".mv-modal").forEach(modal => {
    modal.addEventListener("click", e => {
      if (e.target.dataset.close === "true") {
        closeModal(modal);
      }
    });
  });

  // ESC
  document.addEventListener("keydown", e => {
    if (e.key === "Escape") {
      document.querySelectorAll(".mv-modal.is-open").forEach(m => closeModal(m));
    }
  });

});


</script>

</body>
</html>
