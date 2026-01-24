<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Mathventure | Log Masuk</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="asset/css/auth-base.css">
    <link rel="stylesheet" href="asset/css/login-style.css?v=12345">


</head>
<body>

<div class="container">
    <button class="mute-btn" id="muteBtn">🔊</button>
    <audio id="bgAudio" src="asset/sounds/bg_sound.mp3" loop></audio>

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

            <div class="input-wrapper">
                <input 
                    type="text" 
                    name="username" 
                    id="username" 
                    placeholder="Nama Pengguna"
                    required
                >
                <span id="userLevel" class="user-level">—</span>
            </div>

            <input 
                type="password" 
                name="password" 
                placeholder="Kata Laluan"
                required
            >

            <a href="forgot-password.php" class="forgot-link">Lupa kata laluan?</a>

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
        <img src="asset/images/dino.png" class="dino-img" alt="">
    </div>
</div>

<script>
// 🎵 Sound Button
const audio = document.getElementById('bgAudio');
const muteBtn = document.getElementById('muteBtn');
let isPlaying = false;

muteBtn.onclick = async () => {
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
    } catch (e) { console.error(e); }
};

// 🧠 Auto Detect User Level (Guru/Pelajar)
const usernameInput = document.getElementById("username");
const userLevel = document.getElementById("userLevel");

usernameInput.addEventListener("keyup", async () => {
    let username = usernameInput.value.trim();

    if (username.length < 3) {
        userLevel.textContent = "—";
        userLevel.className = "user-level";
        return;
    }

    userLevel.textContent = "⌛";
    userLevel.className = "user-level loading";

    let res = await fetch("check_user.php?u=" + username);
    let data = await res.json();

    userLevel.textContent = data.label;

    if (data.status === "notfound") {
        userLevel.className = "user-level notfound";
    } else if (data.level === "guru") {
        userLevel.className = "user-level guru";
    } else if (data.level === "pelajar") {
        userLevel.className = "user-level pelajar";
    } else {
        userLevel.className = "user-level";
    }
});

</script>

</body>
</html>
