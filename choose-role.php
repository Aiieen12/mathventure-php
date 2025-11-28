<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Pilih Peranan | Mathventure</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Guna CSS yang sama dengan login -->
    <link rel="stylesheet" href="asset/css/login-style.css">
</head>
<body>

<!-- BUTANG SOUND ATAS KANAN + AUDIO -->
<button class="mute-btn" id="muteBtn">🔊</button>
<audio id="bgAudio" src="asset/audio/login-theme.mp3" loop></audio>

<!-- KAD PILIH PERANAN -->
<div class="role-page-wrapper">
    <div class="role-glass-card">
        <h1 class="role-title">Pilih Peranan Anda</h1>

        <div class="role-grid">
            <!-- Kad Guru -->
            <a href="register-teacher.php" class="role-card role-card-guru">
                <div class="role-emoji">👩‍🏫</div>
                <div class="role-label">Guru</div>
            </a>

            <!-- Kad Pelajar -->
            <a href="register-student.php" class="role-card role-card-pelajar">
                <div class="role-emoji">🧒</div>
                <div class="role-label">Pelajar</div>
            </a>
        </div>
    </div>
</div>

<!-- OVERLAY LOADING DINO -->
<div id="loadingOverlay" class="loading-overlay hidden">
    <div class="loading-card">
        <img src="asset/images/egg.png" alt="Dino Egg" class="loading-dino" id="loadingEgg">
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
            muteBtn.textContent = '🔈';   // icon bila sedang main
        } else {
            audio.pause();
            isPlaying = false;
            muteBtn.textContent = '🔊';   // icon bila mute
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
        e.preventDefault();                        // jangan terus pergi link

        const targetUrl = this.getAttribute('href');

        // Reset state overlay (kalau user reload balik page)
        loadingEgg.src = 'asset/images/egg.png';
        loadingText.textContent = 'Dino Sedang Menetas...';

        // Tunjuk overlay
        loadingOverlay.classList.remove('hidden');

        // Lepas 0.6s → tukar kepada egg-crack
        setTimeout(() => {
            loadingEgg.src = 'asset/images/egg-crack.png';
            loadingText.textContent = 'Dino Hampir Siap! 🐣';
        }, 600);

        // Lepas 1.8s → pergi ke page register
        setTimeout(() => {
            window.location.href = targetUrl;
        }, 1800);
    });
});
</script>

</body>
</html>
