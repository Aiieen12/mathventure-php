<?php
// auth/update-progress.php
require_once '../config.php';

// Pastikan session bermula untuk membaca data user
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Pastikan user sudah login dan mempunyai role pelajar
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pelajar') {
    http_response_code(401); 
    exit('NOT_AUTH');
}

$userId = (int)$_SESSION['user_id'];
$tahun  = (int)($_POST['tahun'] ?? 0);
$level  = (int)($_POST['level'] ?? 0);
$score  = (int)($_POST['score'] ?? 0);
$total  = (int)($_POST['total'] ?? 0);
// Topik boleh dihantar dari game, jika tiada, kita set default berdasarkan tahun/level
$topic_name = $_POST['topic'] ?? "Tahun $tahun - Level $level";

if ($tahun <= 0 || $level <= 0) {
    exit('BAD_REQUEST');
}

// --- 1. SIMPAN MARKAH KE JADUAL student_scores (Untuk paparan Guru) ---
$sqlScore = "INSERT INTO student_scores (id_user, topic_name, score, total_questions) VALUES (?, ?, ?, ?)";
$stmtScore = $conn->prepare($sqlScore);
$stmtScore->bind_param("isii", $userId, $topic_name, $score, $total);
$stmtScore->execute();

// --- 2. TAMBAH XP DAN COINS (Untuk Profil Murid) ---
$xp_gain = $score * 10;
$coin_gain = $score * 2;

$sqlLevel = "UPDATE student SET 
        current_xp = current_xp + ?, 
        coins = coins + ? 
        WHERE id_user = ?";
$stmtLevel = $conn->prepare($sqlLevel);
$stmtLevel->bind_param("iii", $xp_gain, $coin_gain, $userId);
$stmtLevel->execute();


// --- 3. LOGIK BUKA LEVEL SETERUSNYA ---
if ($score === $total && $total > 0) {
    // Tentukan kolom mana nak dikemaskini
    $kolomLevel = "level_t" . $tahun; // Hasilnya: level_t4, level_t5, atau level_t6

    // Ambil level semasa bagi tahun tersebut
    $res = $conn->query("SELECT $kolomLevel FROM student WHERE id_user = $userId");
    if ($row = $res->fetch_assoc()) {
        $currentMaxLevel = (int)$row[$kolomLevel];
        
        // Hanya naikkan level jika murid main level tertinggi yang dia ada untuk TAHUN TERSEBUT
        if ($level == $currentMaxLevel) {
            $nextLevel = $currentMaxLevel + 1;
            $updateLevel = $conn->prepare("UPDATE student SET $kolomLevel = ? WHERE id_user = ?");
            $updateLevel->bind_param("ii", $nextLevel, $userId);
            $updateLevel->execute();
        }
    }
}

// Respon balik ke game
echo 'OK';