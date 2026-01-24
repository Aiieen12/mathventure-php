<?php
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $user_id   = (int)$_SESSION['user_id'];
    $firstname = $_POST['firstname'];
    $lastname  = $_POST['lastname'];
    $class     = $_POST['class'];
    $year      = $_POST['year'];
    $bio       = $_POST['bio'];

    // Guna INSERT ... ON DUPLICATE KEY UPDATE supaya:
    // 1. Jika rekod guru belum ada, ia akan dicipta (INSERT).
    // 2. Jika rekod guru sudah ada, ia akan dikemaskini (UPDATE).
    $sql = "INSERT INTO teacher (id_user, firstname, lastname, class, year, bio) 
            VALUES (?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
            firstname = VALUES(firstname), 
            lastname = VALUES(lastname), 
            class = VALUES(class), 
            year = VALUES(year), 
            bio = VALUES(bio)";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssss", $user_id, $firstname, $lastname, $class, $year, $bio);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Profil anda telah berjaya dikemaskini!";
    } else {
        $_SESSION['error'] = "Gagal mengemaskini profil: " . $conn->error;
    }
    
    header('Location: teacher-profile.php');
    exit;
}