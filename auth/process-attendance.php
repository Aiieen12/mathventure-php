<?php
require_once '../config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['attendance'])) {
    $date = date('Y-m-d');
    
    foreach ($_POST['attendance'] as $id_user => $status) {
        // Guna INSERT ... ON DUPLICATE KEY UPDATE supaya tak berlaku double record jika cikgu tekan banyak kali
        $stmt = $conn->prepare("INSERT INTO attendance (id_user, status, date_recorded) VALUES (?, ?, ?) 
                                ON DUPLICATE KEY UPDATE status = VALUES(status)");
        $stmt->bind_param("iss", $id_user, $status, $date);
        $stmt->execute();
    }
    
    header('Location: dashboard-teacher.php?msg=success');
    exit;
}