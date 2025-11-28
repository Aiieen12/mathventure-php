<?php
// config.php di root projek (C:\xampp\htdocs\mathventure-php\config.php)

// Pastikan session hanya start sekali
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Setting database
$host     = 'localhost';
$user     = 'root';
$password = '';              // kalau XAMPP default, biasanya kosong
$dbname   = 'mathventure';

// Sambungan ke MySQL
$conn = new mysqli($host, $user, $password, $dbname);

// Semak sambungan
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
