<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Mathventure | Daftar Masuk Pelajar</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="asset/css/login-style.css">
</head>
<body>

<div class="reg-page-wrapper">
    <!-- butang sound + audio -->
    <button class="mute-btn" id="muteBtn">🔊</button>
    <audio id="bgAudio" src="asset/audio/login-theme.mp3" loop></audio>

    <div class="reg-card-glass">

        <div class="reg-header">
            <div class="reg-title-icon">🎒</div>
            <h1 class="reg-title">Daftar Masuk Pelajar</h1>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert-error">
                <?php 
                    echo $_SESSION['error']; 
                    unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <form action="auth/register_student_process.php" method="POST" class="reg-form">
            <!-- BLOK 1: Maklumat Peribadi -->
            <h2 class="reg-section-title">Maklumat Peribadi</h2>

            <div class="reg-grid-2">
                <div class="reg-field">
                    <label class="reg-label">Nama Pertama</label>
                    <input type="text" name="firstname" class="reg-input" required>
                </div>

                <div class="reg-field">
                    <label class="reg-label">Nama Terakhir</label>
                    <input type="text" name="lastname" class="reg-input" required>
                </div>

                <div class="reg-field">
                    <label class="reg-label">Tarikh Lahir</label>
                    <input type="date" name="dob" class="reg-input">
                </div>

                <div class="reg-field">
                    <label class="reg-label">Kelas</label>
                    <input type="text" name="class" class="reg-input" placeholder="cth. 5 Bestari">
                </div>

                <div class="reg-field">
                    <label class="reg-label">Tahun</label>
                    <select name="year_level" class="reg-input">
                        <option value="">Pilih Tahun</option>
                        <option value="4">Tahun 4</option>
                        <option value="5">Tahun 5</option>
                        <option value="6">Tahun 6</option>
                    </select>
                </div>

                <div class="reg-field">
                    <label class="reg-label">Biodata (Opsyen)</label>
                    <textarea name="bio" class="reg-textarea reg-textarea-bio" rows="4"
                              placeholder="Sedikit tentang diri anda..."></textarea>
                </div>
            </div>

            <hr class="reg-divider">

            <!-- BLOK 2: Akaun Log Masuk -->
            <h2 class="reg-section-title">Maklumat Akaun</h2>

            <div class="reg-grid-2 reg-grid-2-bottom">
                <div class="reg-field">
                    <label class="reg-label">Nama Pengguna</label>
                    <input type="text" name="username" class="reg-input" required>
                </div>

                <div class="reg-field">
                    <label class="reg-label">Kata Laluan</label>
                    <input type="password" name="password" class="reg-input" required>
                </div>

                <div class="reg-field">
                    <label class="reg-label">Sahkan Kata Laluan</label>
                    <input type="password" name="password_confirm" class="reg-input" required>
                </div>
            </div>

            <div class="reg-actions">
                <a href="choose-role.php" class="reg-secondary-btn">Kembali</a>
                <button type="submit" class="reg-primary-btn">Daftar Sebagai Pelajar</button>
            </div>

            <p class="reg-footer-text">
                Sudah ada akaun? <a href="index.php">Log Masuk</a>
            </p>
        </form>
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
