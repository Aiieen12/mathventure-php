<?php
// Mula session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Buang semua data session
$_SESSION = array();

// Musnahkan session
session_destroy();

// Redirect ke index.php yang berada di folder yang sama dengan logout.php
header("Location: index.php");
exit;