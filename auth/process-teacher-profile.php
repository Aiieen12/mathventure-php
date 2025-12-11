<?php
// auth/process-teacher-profile.php
require_once '../config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'guru' ||
    $_SERVER['REQUEST_METHOD'] !== 'POST'
) {
    $_SESSION['error'] = 'Akses tidak sah.';
    header('Location: teacher-profile.php');
    exit;
}

$user_id   = (int) $_SESSION['user_id'];
$firstname = trim($_POST['firstname'] ?? '');
$lastname  = trim($_POST['lastname'] ?? '');
$class     = trim($_POST['class'] ?? '');
$year      = trim($_POST['year'] ?? '');
$bio       = trim($_POST['bio'] ?? '');

// Validasi ringkas
if ($firstname === '' || $lastname === '') {
    $_SESSION['error'] = 'Nama pertama dan nama terakhir wajib diisi.';
    header('Location: teacher-profile.php');
    exit;
}

// Semak rekod guru
$stmt = $conn->prepare("SELECT COUNT(*) FROM teacher WHERE id_user = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($countTeacher);
$stmt->fetch();
$stmt->close();

if ($countTeacher > 0) {
    // Update
    $stmt = $conn->prepare(
        "UPDATE teacher 
         SET firstname = ?, lastname = ?, class = ?, year = ?, bio = ?
         WHERE id_user = ?"
    );
    $stmt->bind_param("sssssi", $firstname, $lastname, $class, $year, $bio, $user_id);
} else {
    // Insert (fallback jika kali pertama)
    $stmt = $conn->prepare(
        "INSERT INTO teacher (id_user, firstname, lastname, class, year, bio)
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param("isssss", $user_id, $firstname, $lastname, $class, $year, $bio);
}

if ($stmt->execute()) {
    $_SESSION['username'] = $firstname; // untuk sapaan ringkas
    $_SESSION['success']  = 'Profil guru berjaya dikemaskini.';
} else {
    $_SESSION['error']    = 'Ralat sistem semasa mengemaskini profil.';
}
$stmt->close();

header('Location: teacher-profile.php');
exit;
