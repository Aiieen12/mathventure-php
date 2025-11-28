<?php
// auth/login_process.php

require_once '../config.php';

// Pastikan request datang dari form POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Ambil data dari form
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// Validasi asas
if ($username === '' || $password === '') {
    $_SESSION['error'] = 'Sila isi nama pengguna dan kata laluan.';
    header('Location: index.php');
    exit;
}

// ====================================
// 1. CARI USER DALAM JADUAL `users`
// ====================================
$stmt = $conn->prepare("
    SELECT id_user, username, password_hash, role 
    FROM users 
    WHERE username = ?
");

if (!$stmt) {
    $_SESSION['error'] = 'Ralat sistem. Sila cuba lagi.';
    header('Location: index.php');
    exit;
}

$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();
$user   = $result->fetch_assoc();
$stmt->close();

// 2. USERNAME TAK WUJUD
if (!$user) {
    $_SESSION['error'] = 'Nama pengguna tidak wujud.';
    header('Location: index.php');
    exit;
}

// 3. SEMAK KATA LALUAN (HASHED)
if (!password_verify($password, $user['password_hash'])) {
    $_SESSION['error'] = 'Kata laluan tidak sah.';
    header('Location: index.php');
    exit;
}

// 4. LOGIN BERJAYA – SET SESSION
$_SESSION['user_id']  = $user['id_user'];
$_SESSION['username'] = $user['username'];
$_SESSION['role']     = $user['role'];   // 'guru' / 'pelajar' / 'admin'

// 5. REDIRECT IKUT PERANAN
if ($user['role'] === 'guru') {
    header('Location: dashboard-teacher.php');
    exit;
} elseif ($user['role'] === 'pelajar') {
    header('Location: dashboard-student.php');
    exit;
} elseif ($user['role'] === 'admin') {
    // nanti kalau ada admin panel, boleh tukar sini
    header('Location: dashboard-teacher.php');
    exit;
} else {
    $_SESSION['error'] = 'Peranan pengguna tidak sah.';
    header('Location: index.php');
    exit;
}
