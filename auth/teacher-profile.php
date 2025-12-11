<?php
session_start();
require_once '../config.php';
$page = 'profile';
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Profil | Mathventure</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="dashboard-teacher.css">
</head>
<body>
    <?php include 'sidebar-teacher.php'; ?>

    <main class="main-content">
        <div class="section-header">
            <h2>Tetapan Profil</h2>
        </div>

        <div class="profile-card">
            <div class="profile-header">
                <div class="profile-avatar">
                    <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Avatar">
                    <button class="edit-avatar-btn"><i class="fa-solid fa-camera"></i></button>
                </div>
                <h3><?php echo $_SESSION['username'] ?? 'Cikgu'; ?></h3>
                <p>Guru Matematik</p>
            </div>

            <form class="profile-form">
                <div class="form-row">
                    <div class="form-group"><label>First Name</label><input type="text" value="Aisyah"></div>
                    <div class="form-group"><label>Last Name</label><input type="text" value="Maisarah"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Date of Birth</label><input type="date" value="1990-05-15"></div>
                    <div class="form-group"><label>Subjek</label><input type="text" value="Matematik" readonly style="background:#f0f0f0;"></div>
                </div>
                <button type="submit" class="btn-save">Kemaskini Profil</button>
            </form>
        </div>
    </main>
</body>
</html>