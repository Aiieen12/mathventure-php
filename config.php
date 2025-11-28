<?php
// config.php
session_start();

$host = "localhost";
$user = "root";   // XAMPP default
$pass = "";       // selalunya kosong
$db   = "mathventure";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
