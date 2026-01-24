<?php
// auth/register_student_process.php

require_once 'config.php';

// Pastikan request guna POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register-student.php');
    exit;
}

// Ambil data dari form
$firstname  = trim($_POST['firstname'] ?? '');
$lastname   = trim($_POST['lastname'] ?? '');
$dob        = trim($_POST['dob'] ?? '');        // format: YYYY-MM-DD
$class      = trim($_POST['class'] ?? '');
$year_level = trim($_POST['year_level'] ?? '');
$bio        = trim($_POST['bio'] ?? '');

$username   = trim($_POST['username'] ?? '');
$password   = $_POST['password'] ?? '';
$confirm    = $_POST['password_confirm'] ?? '';

$errors = [];

// =========== VALIDASI ASAS ===========
if ($firstname === '' || $lastname === '') {
    $errors[] = 'Sila isi nama pertama dan nama terakhir.';
}

if ($username === '') {
    $errors[] = 'Sila isi nama pengguna.';
}

if ($class === '') {
    $errors[] = 'Sila isi kelas pelajar.';
}

if ($year_level === '') {
    $errors[] = 'Sila pilih tahun pelajar.';
}

if ($password === '' || $confirm === '') {
    $errors[] = 'Sila isi kata laluan dan pengesahan kata laluan.';
} elseif ($password !== $confirm) {
    $errors[] = 'Kata laluan dan pengesahan tidak sepadan.';
}

// Kalau ada error, balik ke form
if (!empty($errors)) {
    $_SESSION['error'] = implode('<br>', $errors);
    header('Location: register-student.php');
    exit;
}

// =========== SEMAK USERNAME UNIK ===========
$stmt = $conn->prepare("SELECT id_user FROM users WHERE username = ?");
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();
$exists = $result->num_rows > 0;
$stmt->close();

if ($exists) {
    $_SESSION['error'] = 'Nama pengguna sudah digunakan. Sila pilih nama lain.';
    header('Location: register-student.php');
    exit;
}

// =========== 1. INSERT KE `users` ===========
$role      = 'pelajar';
$pass_hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("
    INSERT INTO users (username, password_hash, role, created_at)
    VALUES (?, ?, ?, NOW())
");
$stmt->bind_param('sss', $username, $pass_hash, $role);

if (!$stmt->execute()) {
    $_SESSION['error'] = 'Ralat semasa mendaftar akaun pengguna: ' . $stmt->error;
    $stmt->close();
    header('Location: register-student.php');
    exit;
}

$user_id = $stmt->insert_id;
$stmt->close();

// =========== 2. INSERT KE `student` ===========
$avatar = '';                 // buat masa ni kosong
$year_level_int = (int)$year_level;

// Struktur table student:
// (id_user, firstname, lastname, dob, class, year_level, bio, avatar, created_at)
$stmt = $conn->prepare("
    INSERT INTO student (id_user, firstname, lastname, dob, class, year_level, bio, avatar, created_at)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
");
$stmt->bind_param(
    'issssiss',
    $user_id,
    $firstname,
    $lastname,
    $dob,
    $class,
    $year_level_int,
    $bio,
    $avatar
);

if (!$stmt->execute()) {
    $_SESSION['error'] = 'Akaun pengguna dibuat, tetapi gagal simpan maklumat pelajar: ' . $stmt->error;
    $stmt->close();
    header('Location: register-student.php');
    exit;
}

$stmt->close();

// =========== 3. AUTO LOGIN & REDIRECT ===========
$_SESSION['user_id']  = $user_id;
$_SESSION['username'] = $username;
$_SESSION['role']     = 'pelajar';

header('Location: dashboard-student.php');
exit;
