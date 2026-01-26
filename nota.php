<?php
require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pelajar') {
    header('Location: index.php');
    exit;
}

$userId = (int)$_SESSION['user_id'];

// Tarik data real dari database
$stmt = $conn->prepare("SELECT s.*, u.username FROM student s JOIN users u ON s.id_user = u.id_user WHERE s.id_user = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();

$nama = ($data['firstname'] ?? '') . ' ' . ($data['lastname'] ?? '');
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Matematik - Mathventure</title>
    <link rel="stylesheet" href="asset/css/nota.css">
    <link rel="stylesheet" href="asset/css/student-layout.css">

    <style>
        /* =========================
           MODAL (sedia ada - kemas)
           ========================= */
        .mv-modal {
            position: fixed !important;
            inset: 0 !important;
            z-index: 9999;
            display: none;
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
            width: min(820px, 92%);
            max-height: 85vh;
            background: #ffffff;
            padding: 26px 26px 22px;
            border-radius: 22px;
            overflow-y: auto;
            transform: translateY(22px) scale(0.98);
            opacity: 0.98;
            transition: transform 0.3s ease;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }
        .mv-modal.is-open .mv-modal__box { transform: translateY(0) scale(1); }

        .mv-modal__close {
            position: absolute;
            top: 14px;
            right: 14px;
            background: #ff4757;
            color: white;
            border: none;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 20px;
            z-index: 10;
        }
        .mv-modal-lock { overflow: hidden; }

        .mv-modal__box::-webkit-scrollbar { width: 8px; }
        .mv-modal__box::-webkit-scrollbar-thumb { background: #cfcfcf; border-radius: 10px; }

        /* =========================
           NOTE UI (tabs + accordion)
           ========================= */
        .note-header {
            display: flex;
            gap: 12px;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 10px;
            border-bottom: 2px dashed rgba(0,0,0,0.12);
            margin-bottom: 14px;
        }
        .note-header h2 {
            margin: 0;
            font-family: 'Fredoka One', cursive;
            font-size: 1.4rem;
            color: #1f2937;
        }
        .note-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(99,102,241,0.12);
            color: #3730a3;
            font-weight: 700;
            font-size: 0.85rem;
            white-space: nowrap;
        }

        .note-intro {
            background: linear-gradient(135deg, rgba(16,185,129,0.12), rgba(59,130,246,0.10));
            border: 1px solid rgba(0,0,0,0.08);
            border-radius: 16px;
            padding: 12px 14px;
            margin: 10px 0 14px;
            color: #111827;
        }
        .note-intro strong { color: #065f46; }

        .mv-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin: 10px 0 12px;
        }
        .mv-tab-btn {
            border: 0;
            cursor: pointer;
            padding: 10px 12px;
            border-radius: 12px;
            font-weight: 800;
            background: rgba(0,0,0,0.06);
            color: #111827;
            transition: transform 0.15s ease, background 0.15s ease;
        }
        .mv-tab-btn:hover { transform: translateY(-1px); }
        .mv-tab-btn.is-active {
            background: #111827;
            color: #ffffff;
        }

        .mv-tab-panel {
            display: none;
            animation: mvFadeUp 0.35s ease both;
        }
        .mv-tab-panel.is-active { display: block; }

        @keyframes mvFadeUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .mv-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        @media (max-width: 680px) {
            .mv-grid-2 { grid-template-columns: 1fr; }
            .mv-modal__box { padding: 20px 18px 18px; }
        }

        .note-card {
            border: 1px solid rgba(0,0,0,0.10);
            border-radius: 16px;
            padding: 12px 12px 10px;
            background: rgba(255,255,255,0.95);
        }
        .note-card h3 {
            margin: 0 0 8px;
            font-size: 1rem;
            color: #0f172a;
        }

        .mv-accordion {
            display: grid;
            gap: 10px;
            margin-top: 10px;
        }
        .mv-acc-item {
            border: 1px solid rgba(0,0,0,0.10);
            border-radius: 14px;
            overflow: hidden;
            background: #fff;
        }
        .mv-acc-btn {
            width: 100%;
            text-align: left;
            border: 0;
            background: rgba(59,130,246,0.08);
            padding: 12px 12px;
            cursor: pointer;
            font-weight: 900;
            color: #0f172a;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .mv-acc-btn .chev {
            width: 34px; height: 34px;
            display: grid;
            place-items: center;
            border-radius: 10px;
            background: rgba(0,0,0,0.06);
            transition: transform 0.2s ease;
            font-size: 14px;
        }
        .mv-acc-item.is-open .mv-acc-btn .chev { transform: rotate(180deg); }

        .mv-acc-panel {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.28s ease;
            background: #fff;
        }
        .mv-acc-content {
            padding: 10px 12px 12px;
            color: #111827;
            line-height: 1.55;
        }

        .note-kf {
            display: grid;
            gap: 6px;
            margin: 8px 0;
        }
        .note-kf .row {
            background: rgba(16,185,129,0.10);
            border: 1px dashed rgba(16,185,129,0.35);
            padding: 8px 10px;
            border-radius: 12px;
            font-weight: 800;
        }

        .note-ex {
            margin-top: 8px;
            padding: 10px 10px;
            border-radius: 14px;
            background: rgba(245,158,11,0.12);
            border: 1px solid rgba(245,158,11,0.25);
        }
        .note-ex b { color: #92400e; }

        .note-tip {
            margin-top: 8px;
            padding: 10px 10px;
            border-radius: 14px;
            background: rgba(99,102,241,0.10);
            border: 1px solid rgba(99,102,241,0.22);
        }

        .note-mini {
            font-size: 0.92rem;
            color: #111827;
            margin: 0;
        }
        .note-list {
            margin: 8px 0 0;
            padding-left: 18px;
        }
        .note-list li { margin: 6px 0; }

        /* Animasi kecil ikon */
        .sparkle {
            display: inline-block;
            animation: sparkle 1.2s infinite ease-in-out;
        }
        @keyframes sparkle {
            0%,100% { transform: translateY(0); }
            50% { transform: translateY(-2px); }
        }
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
                <img src="asset/images/<?php echo !empty($data['avatar']) ? htmlspecialchars($data['avatar']) : 'avatar.png'; ?>" alt="avatar">
            </div>
            <div class="player-info">
                <div class="lvl-badge">Level <?php echo (int)($data['level'] ?? 1); ?></div>
                <strong><?php echo htmlspecialchars(trim($nama)); ?></strong>
                <div class="xp-track">
                    <?php
                        $currentXp = (float)($data['current_xp'] ?? 0);
                        $maxXp = (float)($data['max_xp'] ?? 100);
                        $pct = $maxXp > 0 ? ($currentXp / $maxXp) * 100 : 0;
                        $pct = max(0, min(100, $pct));
                    ?>
                    <div class="xp-fill" style="width: <?php echo $pct; ?>%;"></div>
                </div>
            </div>
        </div>
    </div>
</aside>

<main class="main-content">
    <div class="top-bar">
        <div class="welcome-badge">Hai <span class="highlight"><?php echo htmlspecialchars($data['firstname'] ?? ''); ?></span>, jom rujukan nota! ✨</div>
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
                    <p>Asas nombor, operasi, pecahan-peratus, wang, masa dan ukuran.</p>
                    <button class="note-btn" type="button" data-modal-target="#nota4Modal">Buka Nota Darjah 4</button>
                </div>
            </article>

            <article class="note-card">
                <div class="note-card-banner banner-5"></div>
                <div class="note-card-body">
                    <h3>Darjah 5</h3>
                    <p>Pembundaran, nombor perdana, pecahan/peratus, wang & masa.</p>
                    <button class="note-btn" type="button" data-modal-target="#nota5Modal">Buka Nota Darjah 5</button>
                </div>
            </article>

            <article class="note-card">
                <div class="note-card-banner banner-6"></div>
                <div class="note-card-body">
                    <h3>Darjah 6</h3>
                    <p>Operasi campuran, peratus & diskaun, geometri, data & kebarangkalian.</p>
                    <button class="note-btn" type="button" data-modal-target="#nota6Modal">Buka Nota Darjah 6</button>
                </div>
            </article>
        </div>
    </div>
</main>

<!-- =========================
     MODAL DARJAH 4 (LENGKAP)
     ========================= -->
<div class="mv-modal" id="nota4Modal" aria-hidden="true">
    <div class="mv-modal__backdrop" onclick="closeAllModals()"></div>
    <div class="mv-modal__box" role="dialog" aria-modal="true" aria-label="Nota Darjah 4">
        <button class="mv-modal__close" onclick="closeAllModals()" aria-label="Tutup">✕</button>

        <div class="note-header">
            <h2>Nota Darjah 4 <span class="sparkle">✨</span></h2>
            <span class="note-chip">Fokus: Asas + Kemas</span>
        </div>

        <div class="note-intro">
            <p class="note-mini">
                Gunakan nota ini untuk kuasai <strong>nilai tempat</strong>, <strong>operasi asas</strong>,
                <strong>pecahan/peratus</strong>, <strong>wang</strong>, <strong>masa</strong> dan <strong>ukuran</strong>.
            </p>
        </div>

        <div class="mv-tabs" data-tabs="t4">
            <button class="mv-tab-btn is-active" type="button" data-tab="t4-a">Nombor & Operasi</button>
            <button class="mv-tab-btn" type="button" data-tab="t4-b">Pecahan • Perpuluhan • Peratus</button>
            <button class="mv-tab-btn" type="button" data-tab="t4-c">Wang • Masa</button>
            <button class="mv-tab-btn" type="button" data-tab="t4-d">Ukuran</button>
        </div>

        <!-- TAB A -->
        <div class="mv-tab-panel is-active" id="t4-a">
            <div class="mv-grid-2">
                <div class="note-card">
                    <h3>Nilai Tempat & Susun Nombor</h3>
                    <p class="note-mini">Sa, Puluh, Ratus, Ribu, Puluh Ribu.</p>
                    <div class="note-kf">
                        <div class="row">23,849 > 23,489 kerana ratus: 8 ratus > 4 ratus</div>
                        <div class="row">Tertib menurun: terbesar → terkecil</div>
                    </div>
                    <div class="note-ex">
                        <b>Contoh:</b> 50,107 = <b>lima puluh ribu</b> + <b>satu ratus</b> + <b>tujuh</b>
                    </div>
                </div>
                <div class="note-card">
                    <h3>Operasi Asas (Tambah & Tolak)</h3>
                    <ul class="note-list">
                        <li>Susun nombor ikut nilai tempat (sa/puluh/ratus...).</li>
                        <li>Tambah: bawa (carry) bila ≥ 10.</li>
                        <li>Tolak: pinjam (borrow) bila digit atas kecil.</li>
                    </ul>
                    <div class="note-tip">
                        <b>Tip laju:</b> Semak jawapan dengan operasi songsang:
                        <br>Tambah ↔ Tolak
                    </div>
                </div>
            </div>

            <div class="mv-accordion">
                <div class="mv-acc-item">
                    <button class="mv-acc-btn" type="button">
                        Darab & Bahagi (Sifir + Strategi)
                        <span class="chev">▼</span>
                    </button>
                    <div class="mv-acc-panel">
                        <div class="mv-acc-content">
                            <p class="note-mini">
                                <b>Darab</b> ialah tambah berulang. <b>Bahagi</b> ialah kongsi sama rata.
                            </p>
                            <div class="note-kf">
                                <div class="row">Darab: 450 × 2 = 900</div>
                                <div class="row">Bahagi: 800 ÷ 5 = 160</div>
                            </div>
                            <div class="note-tip">
                                <b>Tip sifir:</b> Pecahkan nombor:
                                <br>12 × 6 = (10×6) + (2×6)
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mv-acc-item">
                    <button class="mv-acc-btn" type="button">
                        Semak Jawapan (Elak Silap)
                        <span class="chev">▼</span>
                    </button>
                    <div class="mv-acc-panel">
                        <div class="mv-acc-content">
                            <ul class="note-list">
                                <li>Check nilai tempat: jangan tertukar ratus/ribu.</li>
                                <li>Anggar (estimate): jawapan patut dekat-dekat.</li>
                                <li>Pastikan unit betul (RM, jam, m, cm, ml).</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB B -->
        <div class="mv-tab-panel" id="t4-b">
            <div class="note-card">
                <h3>Pecahan → Peratus / Perpuluhan</h3>
                <div class="note-kf">
                    <div class="row">Peratus = (bahagian ÷ jumlah) × 100%</div>
                    <div class="row">0.75 = 75% (baca: tujuh puluh lima peratus)</div>
                    <div class="row">6/10 = 60%</div>
                </div>
                <div class="note-ex">
                    <b>Contoh:</b> 3/8 daripada 40 = 3 × (40 ÷ 8) = 3 × 5 = 15
                </div>
            </div>

            <div class="mv-accordion">
                <div class="mv-acc-item">
                    <button class="mv-acc-btn" type="button">
                        Cara cepat tukar perpuluhan ke peratus
                        <span class="chev">▼</span>
                    </button>
                    <div class="mv-acc-panel">
                        <div class="mv-acc-content">
                            <p class="note-mini"><b>Darab 100%</b> (gerak titik 2 tempat ke kanan).</p>
                            <div class="note-kf">
                                <div class="row">0.6 → 60%</div>
                                <div class="row">1.45 → 145%</div>
                            </div>
                            <div class="note-tip">
                                <b>Tip:</b> Kalau ada titik, “gerak titik” je — jangan panik 😊
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mv-acc-item">
                    <button class="mv-acc-btn" type="button">
                        Bahagi pecahan sama penyebut (asas)
                        <span class="chev">▼</span>
                    </button>
                    <div class="mv-acc-panel">
                        <div class="mv-acc-content">
                            <p class="note-mini">
                                Kalau penyebut sama: tolak/tambah pembilang je.
                            </p>
                            <div class="note-ex">
                                <b>Contoh:</b> 7/9 − 2/9 = 5/9
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB C -->
        <div class="mv-tab-panel" id="t4-c">
            <div class="mv-grid-2">
                <div class="note-card">
                    <h3>Wang (RM & sen)</h3>
                    <div class="note-kf">
                        <div class="row">RM450 × 2 = RM900</div>
                        <div class="row">Jumlah = tambah semua item</div>
                        <div class="row">Baki = jumlah asal − jumlah belanja</div>
                    </div>
                    <div class="note-tip">
                        <b>Tip:</b> Susun titik perpuluhan RM & sen dengan betul.
                    </div>
                </div>
                <div class="note-card">
                    <h3>Masa (12 jam & 24 jam)</h3>
                    <ul class="note-list">
                        <li>Sistem 24 jam: 20:15 = 8:15 malam</li>
                        <li>Tempoh: tolak masa tamat dengan durasi</li>
                    </ul>
                    <div class="note-ex">
                        <b>Contoh:</b> Tamat 10:00 malam, durasi 1 jam 30 minit → mula 8:30 malam
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB D -->
        <div class="mv-tab-panel" id="t4-d">
            <div class="mv-grid-2">
                <div class="note-card">
                    <h3>Panjang & Jarak</h3>
                    <div class="note-kf">
                        <div class="row">1 km = 1000 m</div>
                        <div class="row">2 km 50 m = 2050 m</div>
                    </div>
                    <div class="note-tip">
                        <b>Tip pembaris:</b> Panjang sebenar = bacaan akhir − bacaan awal
                    </div>
                </div>
                <div class="note-card">
                    <h3>Isipadu & Unit</h3>
                    <div class="note-kf">
                        <div class="row">ml = mililiter</div>
                        <div class="row">Liter (L) lebih besar dari ml</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- =========================
     MODAL DARJAH 5 (LENGKAP)
     ========================= -->
<div class="mv-modal" id="nota5Modal" aria-hidden="true">
    <div class="mv-modal__backdrop" onclick="closeAllModals()"></div>
    <div class="mv-modal__box" role="dialog" aria-modal="true" aria-label="Nota Darjah 5">
        <button class="mv-modal__close" onclick="closeAllModals()" aria-label="Tutup">✕</button>

        <div class="note-header">
            <h2>Nota Darjah 5 <span class="sparkle">✨</span></h2>
            <span class="note-chip">Fokus: Pembundaran + Aplikasi</span>
        </div>

        <div class="note-intro">
            <p class="note-mini">
                Darjah 5 banyak guna <strong>pecahan/peratus</strong>, <strong>pembundaran</strong>, <strong>waktu</strong>,
                <strong>wang</strong> dan <strong>ukuran</strong>.
            </p>
        </div>

        <div class="mv-tabs" data-tabs="t5">
            <button class="mv-tab-btn is-active" type="button" data-tab="t5-a">Nombor</button>
            <button class="mv-tab-btn" type="button" data-tab="t5-b">Pecahan • Peratus</button>
            <button class="mv-tab-btn" type="button" data-tab="t5-c">Wang</button>
            <button class="mv-tab-btn" type="button" data-tab="t5-d">Masa & Ukuran</button>
        </div>

        <div class="mv-tab-panel is-active" id="t5-a">
            <div class="mv-grid-2">
                <div class="note-card">
                    <h3>Nombor Perdana</h3>
                    <p class="note-mini">Nombor yang hanya boleh dibahagi dengan <b>1</b> dan <b>dirinya</b>.</p>
                    <div class="note-kf">
                        <div class="row">23 = perdana (1 dan 23)</div>
                        <div class="row">21 bukan perdana (boleh ÷ 3, 7)</div>
                    </div>
                </div>
                <div class="note-card">
                    <h3>Pembundaran (ribu terdekat)</h3>
                    <div class="note-kf">
                        <div class="row">Lihat 3 digit terakhir untuk bundar ke ribu</div>
                        <div class="row">318,509 → 319,000</div>
                    </div>
                    <div class="note-tip">
                        <b>Tip:</b> 500 ke atas → naik 1 ribu.
                    </div>
                </div>
            </div>

            <div class="mv-accordion">
                <div class="mv-acc-item">
                    <button class="mv-acc-btn" type="button">
                        Banding data dalam graf / jadual
                        <span class="chev">▼</span>
                    </button>
                    <div class="mv-acc-panel">
                        <div class="mv-acc-content">
                            <ul class="note-list">
                                <li>Kenal pasti nilai paling kecil / paling besar.</li>
                                <li>Banding ikut digit dari kiri (ratus ribu → puluh ribu → ribu...).</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mv-tab-panel" id="t5-b">
            <div class="note-card">
                <h3>Pecahan daripada suatu nombor</h3>
                <div class="note-kf">
                    <div class="row">(3/8) × 40 = 3 × (40 ÷ 8) = 15</div>
                </div>
                <div class="note-tip">
                    <b>Tip laju:</b> Bahagi dulu (40 ÷ 8), baru darab.
                </div>
            </div>

            <div class="mv-accordion">
                <div class="mv-acc-item">
                    <button class="mv-acc-btn" type="button">
                        Perpuluhan → Peratus
                        <span class="chev">▼</span>
                    </button>
                    <div class="mv-acc-panel">
                        <div class="mv-acc-content">
                            <p class="note-mini"><b>Darab 100%</b> (gerak titik 2 tempat).</p>
                            <div class="note-kf">
                                <div class="row">1.45 → 145%</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mv-acc-item">
                    <button class="mv-acc-btn" type="button">
                        Isipadu / jisim berulang (darab)
                        <span class="chev">▼</span>
                    </button>
                    <div class="mv-acc-panel">
                        <div class="mv-acc-content">
                            <div class="note-kf">
                                <div class="row">0.35 kg × 3 = 1.05 kg</div>
                            </div>
                            <div class="note-tip">
                                <b>Tip:</b> Kira seperti nombor biasa, kemudian letak titik semula.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mv-tab-panel" id="t5-c">
            <div class="mv-grid-2">
                <div class="note-card">
                    <h3>Baki Simpanan</h3>
                    <div class="note-kf">
                        <div class="row">Baki = Simpanan asal − belanja</div>
                        <div class="row">12,500 − 1,899 − 750 = 9,851</div>
                    </div>
                </div>
                <div class="note-card">
                    <h3>Faedah (bank)</h3>
                    <p class="note-mini"><b>Faedah</b> ialah ganjaran wang yang bank bayar kepada penyimpan.</p>
                    <div class="note-tip">
                        <b>Nota:</b> Dalam soalan, “ganjaran bank kepada penyimpan” biasanya jawapan: <b>faedah</b>.
                    </div>
                </div>
            </div>

            <div class="mv-accordion">
                <div class="mv-acc-item">
                    <button class="mv-acc-btn" type="button">
                        Tunai vs Ansuran (beza harga)
                        <span class="chev">▼</span>
                    </button>
                    <div class="mv-acc-panel">
                        <div class="mv-acc-content">
                            <div class="note-kf">
                                <div class="row">Jumlah ansuran = bayaran bulanan × bilangan bulan</div>
                                <div class="row">Beza = jumlah ansuran − bayaran tunai</div>
                            </div>
                            <div class="note-ex">
                                <b>Contoh:</b> RM110 × 12 = RM1320; beza RM1320 − RM1200 = RM120
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mv-tab-panel" id="t5-d">
            <div class="mv-grid-2">
                <div class="note-card">
                    <h3>Masa: Tukar Hari → Jam</h3>
                    <div class="note-kf">
                        <div class="row">1 hari = 24 jam</div>
                        <div class="row">5 hari 10 jam = (5×24)+10 = 130 jam</div>
                    </div>
                </div>
                <div class="note-card">
                    <h3>Unit Ukuran</h3>
                    <div class="note-kf">
                        <div class="row">1 tan metrik = 1,000 kg</div>
                        <div class="row">1.08 tan = 1,080 kg</div>
                    </div>
                    <div class="note-tip">
                        <b>Tip:</b> Pastikan unit pada jawapan sama macam soalan minta.
                    </div>
                </div>
            </div>

            <div class="mv-accordion">
                <div class="mv-acc-item">
                    <button class="mv-acc-btn" type="button">
                        ml → L dan ml
                        <span class="chev">▼</span>
                    </button>
                    <div class="mv-acc-panel">
                        <div class="mv-acc-content">
                            <div class="note-kf">
                                <div class="row">1 L = 1000 ml</div>
                                <div class="row">4,025 ml = 4 L 25 ml</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mv-acc-item">
                    <button class="mv-acc-btn" type="button">
                        Sela masa antara dua waktu
                        <span class="chev">▼</span>
                    </button>
                    <div class="mv-acc-panel">
                        <div class="mv-acc-content">
                            <ul class="note-list">
                                <li>Kira dari waktu awal ke waktu akhir secara langkah demi langkah.</li>
                                <li>Jumlahkan jam dan minit.</li>
                            </ul>
                            <div class="note-ex">
                                <b>Contoh:</b> 10:30 pagi → 1:15 petang = 2 jam 45 minit
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- =========================
     MODAL DARJAH 6 (LENGKAP)
     ========================= -->
<div class="mv-modal" id="nota6Modal" aria-hidden="true">
    <div class="mv-modal__backdrop" onclick="closeAllModals()"></div>
    <div class="mv-modal__box" role="dialog" aria-modal="true" aria-label="Nota Darjah 6">
        <button class="mv-modal__close" onclick="closeAllModals()" aria-label="Tutup">✕</button>

        <div class="note-header">
            <h2>Nota Darjah 6 <span class="sparkle">✨</span></h2>
            <span class="note-chip">Fokus: Aplikasi + HOTS</span>
        </div>

        <div class="note-intro">
            <p class="note-mini">
                Darjah 6 banyak gabung topik: <strong>operasi campuran</strong>, <strong>peratus/diskaun</strong>,
                <strong>untung rugi</strong>, <strong>geometri</strong> dan <strong>statistik</strong>.
            </p>
        </div>

        <div class="mv-tabs" data-tabs="t6">
            <button class="mv-tab-btn is-active" type="button" data-tab="t6-a">Operasi & Nombor</button>
            <button class="mv-tab-btn" type="button" data-tab="t6-b">Peratus & Diskaun</button>
            <button class="mv-tab-btn" type="button" data-tab="t6-c">Wang (Untung/Aset)</button>
            <button class="mv-tab-btn" type="button" data-tab="t6-d">Geometri • Data • Kebarangkalian</button>
        </div>

        <div class="mv-tab-panel is-active" id="t6-a">
            <div class="mv-grid-2">
                <div class="note-card">
                    <h3>Operasi Campuran</h3>
                    <div class="note-kf">
                        <div class="row">Ikut turutan: ( ) → × ÷ → + −</div>
                        <div class="row">40 × (2,500 + 750) ÷ 100</div>
                    </div>
                    <div class="note-ex">
                        <b>Contoh:</b> (2,500 + 750) = 3,250 → 40×3,250=130,000 → ÷100=1,300
                    </div>
                </div>
                <div class="note-card">
                    <h3>Juta & Perpuluhan (ejaan)</h3>
                    <div class="note-kf">
                        <div class="row">2.08 juta = dua perpuluhan sifar lapan juta</div>
                    </div>
                    <div class="note-tip">
                        <b>Tip:</b> Sebut digit selepas titik satu-satu.
                    </div>
                </div>
            </div>

            <div class="mv-accordion">
                <div class="mv-acc-item">
                    <button class="mv-acc-btn" type="button">
                        Nombor Perdana (ulang kaji)
                        <span class="chev">▼</span>
                    </button>
                    <div class="mv-acc-panel">
                        <div class="mv-acc-content">
                            <p class="note-mini">Perdana: faktor cuma 1 dan dirinya.</p>
                            <div class="note-kf">
                                <div class="row">53, 59 ialah nombor perdana</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mv-tab-panel" id="t6-b">
            <div class="note-card">
                <h3>Peratus daripada Pecahan/Perpuluhan</h3>
                <div class="note-kf">
                    <div class="row">Peratus = (bahagian ÷ jumlah) × 100%</div>
                    <div class="row">4/5 × 100% = 80%</div>
                    <div class="row">3.2 × 100% = 320%</div>
                </div>
            </div>

            <div class="mv-accordion">
                <div class="mv-acc-item">
                    <button class="mv-acc-btn" type="button">
                        Diskaun (harga selepas diskaun)
                        <span class="chev">▼</span>
                    </button>
                    <div class="mv-acc-panel">
                        <div class="mv-acc-content">
                            <div class="note-kf">
                                <div class="row">Diskaun = peratus × harga asal</div>
                                <div class="row">Harga baharu = harga asal − diskaun</div>
                            </div>
                            <div class="note-ex">
                                <b>Contoh:</b> 20% × RM150 = RM30 → RM150 − RM30 = RM120
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mv-tab-panel" id="t6-c">
            <div class="mv-grid-2">
                <div class="note-card">
                    <h3>Untung / Rugi</h3>
                    <div class="note-kf">
                        <div class="row">Untung = Harga Jual − Harga Kos</div>
                        <div class="row">Rugi = Harga Kos − Harga Jual</div>
                    </div>
                    <div class="note-ex">
                        <b>Contoh:</b> RM920 − RM800 = RM120 (untung)
                    </div>
                </div>
                <div class="note-card">
                    <h3>Aset & Liabiliti</h3>
                    <ul class="note-list">
                        <li><b>Aset</b>: barang/duit yang ada nilai (rumah, simpanan bank).</li>
                        <li><b>Liabiliti</b>: hutang/bil yang perlu dibayar.</li>
                    </ul>
                </div>
            </div>

            <div class="mv-accordion">
                <div class="mv-acc-item">
                    <button class="mv-acc-btn" type="button">
                        Jumlah liabiliti (campur bil)
                        <span class="chev">▼</span>
                    </button>
                    <div class="mv-acc-panel">
                        <div class="mv-acc-content">
                            <p class="note-mini">Jumlahkan semua bil yang disenaraikan untuk dapat total liabiliti.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mv-tab-panel" id="t6-d">
            <div class="mv-grid-2">
                <div class="note-card">
                    <h3>Poligon Sekata</h3>
                    <div class="note-kf">
                        <div class="row">Pentagon = 5 sisi</div>
                        <div class="row">Heksagon = 6 sisi</div>
                        <div class="row">Heptagon = 7 sisi</div>
                        <div class="row">Oktagon = 8 sisi</div>
                    </div>
                </div>
                <div class="note-card">
                    <h3>Bulatan: Diameter</h3>
                    <p class="note-mini">
                        <b>Diameter</b> ialah garis lurus melalui pusat bulatan (lebih panjang dari jejari).
                    </p>
                </div>
            </div>

            <div class="mv-accordion">
                <div class="mv-acc-item">
                    <button class="mv-acc-btn" type="button">
                        Statistik: Median & Min (purata)
                        <span class="chev">▼</span>
                    </button>
                    <div class="mv-acc-panel">
                        <div class="mv-acc-content">
                            <div class="note-kf">
                                <div class="row">Median: susun data, ambil nilai tengah</div>
                                <div class="row">Min: jumlah ÷ bilangan data</div>
                            </div>
                            <div class="note-ex">
                                <b>Contoh median:</b> 10,12,8,10,15 → susun 8,10,10,12,15 → median 10
                                <br><b>Contoh min:</b> (8+10+6+6)=30 → 30÷4 = 7.5
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mv-acc-item">
                    <button class="mv-acc-btn" type="button">
                        Kebarangkalian: Mustahil / Pasti
                        <span class="chev">▼</span>
                    </button>
                    <div class="mv-acc-panel">
                        <div class="mv-acc-content">
                            <ul class="note-list">
                                <li><b>Mustahil:</b> perkara yang tak boleh berlaku (contoh: ayam melahirkan anak).</li>
                                <li><b>Pasti:</b> perkara yang memang akan berlaku.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

<script>
// Toggle sidebar
function toggleSidebar() { document.getElementById('sidebar').classList.toggle('collapsed'); }

// Jam
function updateClock() {
    const el = document.getElementById('gameClock');
    const now = new Date();
    el.textContent = now.toLocaleTimeString('ms-MY', { hour: '2-digit', minute: '2-digit', hour12:false });
}
setInterval(updateClock, 1000); updateClock();

// =========================
// MODAL UNIVERSAL
// =========================
document.querySelectorAll('[data-modal-target]').forEach(button => {
    button.addEventListener('click', () => {
        const modal = document.querySelector(button.dataset.modalTarget);
        openModal(modal);
    });
});

function openModal(modal) {
    if (!modal) return;
    modal.classList.add('is-open');
    document.body.classList.add('mv-modal-lock');

    // Set aria
    modal.setAttribute('aria-hidden', 'false');

    // Reset tabs & accordion dalam modal (supaya setiap kali buka kemas)
    initModalUI(modal);
}

function closeAllModals() {
    document.querySelectorAll('.mv-modal.is-open').forEach(modal => {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
    });
    document.body.classList.remove('mv-modal-lock');
}

// Tutup guna ESC
document.addEventListener('keydown', e => {
    if (e.key === "Escape") closeAllModals();
});

// =========================
// TABS + ACCORDION (ANIMASI)
// =========================
function initModalUI(modal) {
    // Tabs
    const tabsWraps = modal.querySelectorAll('[data-tabs]');
    tabsWraps.forEach(wrap => {
        const group = wrap.getAttribute('data-tabs');
        const btns = wrap.querySelectorAll('.mv-tab-btn');
        const panels = modal.querySelectorAll(`#${group}-a, #${group}-b, #${group}-c, #${group}-d`);

        // fallback: cari semua panel id bermula group-
        const allPanels = modal.querySelectorAll(`[id^="${group}-"]`);

        // default aktif: yang ada class is-active, kalau tak ada -> first
        let anyActive = false;
        btns.forEach(b => { if (b.classList.contains('is-active')) anyActive = true; });
        if (!anyActive && btns[0]) btns[0].classList.add('is-active');
        // pastikan panel ikut btn aktif
        btns.forEach((b, idx) => {
            const target = b.getAttribute('data-tab');
            const panel = modal.querySelector(`#${target}`);
            if (b.classList.contains('is-active') && panel) panel.classList.add('is-active');
            if (!b.classList.contains('is-active') && panel) panel.classList.remove('is-active');
        });

        btns.forEach(btn => {
            btn.onclick = () => {
                btns.forEach(b => b.classList.remove('is-active'));
                allPanels.forEach(p => p.classList.remove('is-active'));
                btn.classList.add('is-active');
                const target = btn.getAttribute('data-tab');
                const panel = modal.querySelector(`#${target}`);
                if (panel) panel.classList.add('is-active');
            };
        });
    });

    // Accordion
    const accItems = modal.querySelectorAll('.mv-acc-item');
    accItems.forEach(item => {
        const btn = item.querySelector('.mv-acc-btn');
        const panel = item.querySelector('.mv-acc-panel');
        if (!btn || !panel) return;

        // reset tutup
        item.classList.remove('is-open');
        panel.style.maxHeight = '0px';

        btn.onclick = () => {
            const isOpen = item.classList.contains('is-open');

            // tutup semua dulu (optional: kalau nak boleh buka banyak, buang loop ni)
            accItems.forEach(i => {
                const p = i.querySelector('.mv-acc-panel');
                i.classList.remove('is-open');
                if (p) p.style.maxHeight = '0px';
            });

            if (!isOpen) {
                item.classList.add('is-open');
                panel.style.maxHeight = panel.scrollHeight + 'px';
            }
        };
    });
}
</script>

</body>
</html>
