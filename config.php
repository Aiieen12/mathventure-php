<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host     = 'localhost';
$user     = 'root';
$password = '';
$dbname   = 'mathventure';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// TAMBAHAN: Update status aktif pelajar jika sudah login
if (isset($_SESSION['user_id'])) {
    $uid = (int)$_SESSION['user_id'];
    // Memastikan kolum last_activity sudah ada di table users
    $conn->query("UPDATE users SET last_activity = NOW() WHERE id_user = $uid");
}