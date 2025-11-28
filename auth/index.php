<?php
require_once '../config.php';
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Mathventure | Log Masuk</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- dari /auth/ ke /asset/ kena naik satu level: ../ -->
    <link rel="stylesheet" href="../asset/css/auth-base.css">
    <link rel="stylesheet" href="../asset/css/login-style.css">
</head>
<body>

<div class="container">
    <button class="mute-btn" id="muteBtn">🔊</button>
    <audio id="bgAudio" src="../asset/sounds/bg_sound.mp3" loop></audio>

    <div class="login-box">
        <h2>Log Masuk</h2>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert-error">
                <?php 
                    echo $_SESSION['error']; 
                    unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <form action="login_process.php" method="POST">
            <input 
                type="text" 
                name="username" 
                placeholder="nama pengguna" 
                required
            >

            <input 
                type="password" 
                name="password" 
                placeholder="kata laluan" 
                required
            >

            <a href="forgot-password.php" class="forgot-link">
                Lupa kata laluan?
            </a>

            <button type="submit">Masuk Sekarang</button>
        </form>

        <div class="register-link">
            Tiada Akaun? <a href="choose-role.php">Daftar Sekarang</a>
        </div>
    </div>

    <div class="dino-box">
        <div class="signboard">
            Bersedia untuk meneroka matematik? ✨
        </div>
        <img src="../asset/images/dino.png" alt="Mathventure Dino" class="dino-img">
    </div>
</div>

<script>
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
</script>

</body>
</html>
