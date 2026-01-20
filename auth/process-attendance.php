<?php
require_once '../config.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// 1. Kawalan Keselamatan: Pastikan hanya guru yang boleh proses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'guru') {
    header('Location: ../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['attendance'])) {
    // Gunakan timezone Malaysia untuk tarikh yang tepat
    date_default_timezone_set('Asia/Kuala_Lumpur');
    $today = date('Y-m-d');
    
    $success_count = 0;

    foreach ($_POST['attendance'] as $id_user => $status) {
        // Pastikan status adalah nilai yang dibenarkan (Hadir, Tak Hadir, Sakit, dll)
        // SQL ON DUPLICATE KEY UPDATE untuk elakkan duplicate entry pada tarikh yang sama
        $sql = "INSERT INTO attendance (id_user, status, date_recorded) VALUES (?, ?, ?) 
                ON DUPLICATE KEY UPDATE status = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isss", $id_user, $status, $today, $status);
        
        if ($stmt->execute()) {
            $success_count++;
        }
    }

    if ($success_count > 0) {
        $_SESSION['success'] = "Kehadiran bagi $success_count pelajar telah berjaya direkodkan.";
    } else {
        $_SESSION['error'] = "Tiada rekod yang dikemaskini.";
    }

    header("Location: teacher-attendance.php");
    exit;
} else {
    // Jika akses terus tanpa POST
    header("Location: teacher-attendance.php");
    exit;
}