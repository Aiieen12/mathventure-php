<?php
// auth/update_progress.php
//
// Dipanggil oleh JavaScript (fetch) dari game-play.php
// untuk kemas kini level yang dibuka dalam SESSION.

require_once '../config.php';

// Pastikan user login
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'pelajar') {
    http_response_code(401);
    echo 'NOT_AUTH';
    exit;
}

$userId = $_SESSION['user_id'];

// Tetapan bilangan level maksimum setiap tahun
$maxLevelByYear = [
    4 => 5,
    5 => 3,
    6 => 3,
];

$tahun = isset($_POST['tahun']) ? (int)$_POST['tahun'] : 0;
$level = isset($_POST['level']) ? (int)$_POST['level'] : 0;
$score = isset($_POST['score']) ? (int)$_POST['score'] : 0;
$total = isset($_POST['total']) ? (int)$_POST['total'] : 0;

if ($tahun <= 0 || $level <= 0 || $total <= 0) {
    http_response_code(400);
    echo 'BAD_REQUEST';
    exit;
}

// Inisialisasi struktur SESSION jika belum ada
if (!isset($_SESSION['progress'])) {
    $_SESSION['progress'] = [];
}
if (!isset($_SESSION['progress'][$userId])) {
    $_SESSION['progress'][$userId] = [];
}
if (!isset($_SESSION['progress'][$userId][$tahun])) {
    $_SESSION['progress'][$userId][$tahun] = 1;
}

$maxLevel = $maxLevelByYear[$tahun] ?? 1;
$currentMaxUnlocked = $_SESSION['progress'][$userId][$tahun];

// Hanya jika murid dapat markah penuh, dan level ini adalah level tertinggi yang dibuka sekarang,
// barulah kita buka level seterusnya.
if ($score === $total && $level === $currentMaxUnlocked && $currentMaxUnlocked < $maxLevel) {
    $_SESSION['progress'][$userId][$tahun] = $currentMaxUnlocked + 1;
}

echo 'OK';
