<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Mathventure | Log Masuk</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="asset/css/login-style.css">
</head>
<body>

<div class="container">
    <!-- butang mute -->
    <button class="mute-btn" id="muteBtn">🔊</button>
    <audio id="bgAudio" src="asset/sounds/bg_sound.mp3" loop></audio>

    <!-- kad log masuk -->
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

        <form action="auth/login_process.php" method="POST">
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

            <select name="role_ui">
                <option value="" disabled selected>Pilih peranan</option>
                <option value="pelajar">Pelajar</option>
                <option value="guru">Guru</option>
            </select>

            <a href="forgot-password.php" class="forgot-link">
                Lupa kata laluan?
            </a>

            <button type="submit">Masuk Sekarang</button>
        </form>

        <div class="register-link">
            Tiada Akaun? <a href="choose-role.php">Daftar Sekarang</a>
        </div>

    </div>

    <!-- mascot dino -->
    <div class="dino-box">
        <div class="signboard">
            Bersedia untuk meneroka matematik? ✨
        </div>
        <img src="asset/images/dino.png" alt="Mathventure Dino" class="dino-img">
    </div>
</div>


<script>
document.getElementById('muteBtn').addEventListener('click', function () {
    this.textContent = this.textContent === '🔊' ? '🔈' : '🔊';
});
</script>

</body>
</html>
