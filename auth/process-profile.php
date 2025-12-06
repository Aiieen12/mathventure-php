<?php
require_once '../config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard-student.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$firstname = trim($_POST['firstname']);
$lastname = trim($_POST['lastname']);
$bio = trim($_POST['bio']);
$avatar = $_POST['avatar_selection'];

// Validasi ringkas
if (empty($firstname) || empty($lastname)) {
    $_SESSION['error'] = "Nama tidak boleh kosong!";
    header('Location: dashboard-student.php');
    exit;
}

// Update Database
$stmt = $conn->prepare("UPDATE student SET firstname = ?, lastname = ?, bio = ?, avatar = ? WHERE id_user = ?");
$stmt->bind_param("ssssi", $firstname, $lastname, $bio, $avatar, $user_id);

if ($stmt->execute()) {
    // Update session nama jika perlu dipaparkan segera tanpa query semula
    $_SESSION['username'] = $firstname; 
    $_SESSION['success'] = "Profil berjaya dikemaskini!";
} else {
    $_SESSION['error'] = "Ralat sistem. Sila cuba lagi.";
}

header('Location: dashboard-student.php');
exit;