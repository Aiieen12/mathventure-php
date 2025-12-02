<?php
// choose-role.php
require_once '../config.php';
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Pilih Peranan | Mathventure</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS umum + CSS khas role -->
    <link rel="stylesheet" href="../asset/css/auth-base.css">
    <link rel="stylesheet" href="../asset/css/role.css">

    <style>
        /* ================================
           GAMBAR GANTI EMOJI (BARU)
        ================================= */
        .role-img {
            display: flex;
            justify-content: center;
            margin-bottom: 10px;
        }

        .role-img img {
            width: 130px;
            height: 130px;
            object-fit: contain;
            filter: drop-shadow(0 5px 12px rgba(0,0,0,0.25));
            transition: transform .35s ease, filter .35s ease;
        }

        .role-card:hover .role-img img {
            transform: scale(1.15);
            filter: drop-shadow(0 8px 20px rgba(0,0,0,0.3));
        }
    </style>
</head>
<body>

<!-- BUTANG SOUND ATAS KANAN + AUDIO -->
<button class="mute-btn" id="muteBtn">🔊</button>
<audio id="bgAudio" src="../asset/sounds/bg_sound.mp3" loop></audio>

<!-- KAD PILIH PERANAN -->
<div class="role-page-wrapper">
    <div class="role-glass-card aura-float">
        <h1 class="role-title">Pilih Peranan Anda</h1>
        <div class="title-underline"></div>

        <div class="role-grid">

            <!-- Kad Guru -->
            <a href="register-teacher.php" class="role-card role-card-guru">
                <div class="role-img">
                    <img src="../asset/images/teacher.png" alt="Guru">
                </div>
                <div class="role-label">Guru</div>
            </a>

            <!-- Kad Pelajar -->
            <a href="register-student.php" class="role-card role-card-pelajar">
                <div class="role-img">
                    <img src="../asset/images/student.png" alt="Pelajar">
                </div>
                <div class="role-label">Pelajar</div>
            </a>

        </div>
    </div>
</div>

<!-- OVERLAY LOADING DINO -->
<div id="loadingOverlay" class="loading-overlay hidden">
    <div class="loading-card">
        <img src="../asset/images/egg.png" alt="Dino Egg" class="loading-dino" id="loadingEgg">
        <p class="loading-text" id="loadingText">Dino Sedang Menetas...</p>
    </div>
</div>

<script>
// ====================== SOUND BUTTON ======================
const audio = document.getElementById('bgAudio');
const muteBtn = document.getElementById('muteBtn');
let isPlaying = false;

muteBtn.addEventListener('click', async function () {
    try {
        if (!isPlaying) {
            await audio.play();
            isPlaying = true;
            muteBtn.textContent = '🔈';
        } else {
            audio.pause();
            isPlaying = false;
            muteBtn.textContent = '🔊';
        }
    } catch (e) {
        console.error(e);
    }
});

// ====================== LOADING DINO ======================
const loadingOverlay = document.getElementById('loadingOverlay');
const loadingEgg     = document.getElementById('loadingEgg');
const loadingText    = document.getElementById('loadingText');
const roleCards      = document.querySelectorAll('.role-card');

roleCards.forEach(card => {
    card.addEventListener('click', function (e) {
        e.preventDefault();

        const targetUrl = this.getAttribute('href');

        loadingEgg.src = '../asset/images/egg.png';
        loadingText.textContent = 'Dino Sedang Menetas...';
        loadingOverlay.classList.remove('hidden');

        setTimeout(() => {
            loadingEgg.src = '../asset/images/egg-crack.png';
            loadingText.textContent = 'Dino Hampir Siap! 🐣';
        }, 600);

        setTimeout(() => {
            window.location.href = targetUrl;
        }, 1800);
    });
});
</script>

</body>
</html>
