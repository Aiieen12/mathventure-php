<?php
session_start();
require_once '../config.php';

$page = 'profile';

$guruNama = $_SESSION['username'] ?? 'Cikgu';
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Profil Guru | Mathventure</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- CSS global: navbar + background cerah -->
    <link rel="stylesheet" href="../asset/css/teacher-shell.css?v=1">
    <!-- CSS khas untuk halaman profil guru -->
    <link rel="stylesheet" href="../asset/css/teacher-profile.css?v=1">
</head>
<body class="teacher-mode">

<div class="teacher-layout">
    <?php include 'sidebar-teacher.php'; ?>

    <main class="main-content profile-page">
        <!-- Tajuk atas -->
        <div class="profile-top-header">
            <div>
                <h2>Tetapan Profil Guru</h2>
                <p>Kemaskini maklumat asas anda dalam sistem Mathventure.</p>
            </div>
        </div>

        <!-- Kad profil -->
        <section class="profile-card">
            <header class="profile-header">
                <div class="profile-avatar">
                    <div class="avatar-frame">
                        <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Avatar Guru">
                    </div>
                    <button type="button" class="edit-avatar-btn" title="Tukar gambar">
                        <i class="fa-solid fa-camera"></i>
                    </button>
                </div>
                <h3 class="profile-name"><?php echo htmlspecialchars($guruNama); ?></h3>
                <p class="profile-role">Guru Matematik</p>
            </header>

            <form class="profile-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input id="first_name" type="text" value="Aisyah">
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input id="last_name" type="text" value="Maisarah">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="dob">Date of Birth</label>
                        <input id="dob" type="date" value="1990-05-15">
                    </div>
                    <div class="form-group">
                        <label for="subject">Subjek</label>
                        <input id="subject" type="text" value="Matematik" readonly>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Emel</label>
                        <input id="email" type="email" placeholder="contoh@sekolah.edu.my">
                    </div>
                    <div class="form-group">
                        <label for="phone">No. Telefon</label>
                        <input id="phone" type="text" placeholder="01X-XXXXXXX">
                    </div>
                </div>

                <div class="form-row form-row-password">
                    <div class="form-group">
                        <label for="password">Kata Laluan Baharu</label>
                        <input id="password" type="password" placeholder="••••••••">
                    </div>
                    <div class="form-group">
                        <label for="password_confirm">Sahkan Kata Laluan</label>
                        <input id="password_confirm" type="password" placeholder="••••••••">
                    </div>
                </div>

                <div class="profile-actions">
                    <button type="submit" class="btn-save-profile">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Kemaskini Profil</span>
                    </button>
                </div>
            </form>
        </section>
    </main>
</div>

</body>
</html>
