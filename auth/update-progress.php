<?php
require_once '../config.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pelajar') {
    http_response_code(401); 
    exit('NOT_AUTH');
}

$userId = (int)$_SESSION['user_id'];
$tahun  = (int)($_POST['tahun'] ?? 0);
$level  = (int)($_POST['level'] ?? 0);
$score  = (int)($_POST['score'] ?? 0);
$total  = (int)($_POST['total'] ?? 0);
$topic_name = $_POST['topic'] ?? "Tahun $tahun - Level $level";

if ($tahun <= 0 || $level <= 0) { exit('BAD_REQUEST'); }

// 1. Simpan Markah
$sqlScore = "INSERT INTO student_scores (id_user, topic_name, score, total_questions) VALUES (?, ?, ?, ?)";
$stmtScore = $conn->prepare($sqlScore);
$stmtScore->bind_param("isii", $userId, $topic_name, $score, $total);
$stmtScore->execute();

// 2. Kemaskini XP & Coins
$xp_gain = $score * 10;
$coin_gain = $score * 2;
$conn->query("UPDATE student SET current_xp = current_xp + $xp_gain, coins = coins + $coin_gain WHERE id_user = $userId");

// 3. Logik Buka Level & Lencana
if ($score === $total && $total > 0) {
    $kolomLevel = "level_t" . $tahun;
    $res = $conn->query("SELECT $kolomLevel FROM student WHERE id_user = $userId");
    $row = $res->fetch_assoc();
    $currentMaxLevel = (int)$row[$kolomLevel];

    if ($level == $currentMaxLevel) {
        $nextLevel = $currentMaxLevel + 1;
        $conn->query("UPDATE student SET $kolomLevel = $nextLevel WHERE id_user = $userId");
    }

    // AUTO-UNLOCK BADGE
    $badge_name = "Wira T$tahun L$level";
    $checkBadge = $conn->query("SELECT id_badge_win FROM student_badges WHERE id_user = $userId AND badge_name = '$badge_name'");
    if ($checkBadge->num_rows == 0) {
        $stmtB = $conn->prepare("INSERT INTO student_badges (id_user, badge_name, tahun, level) VALUES (?, ?, ?, ?)");
        $stmtB->bind_param("isii", $userId, $badge_name, $tahun, $level);
        $stmtB->execute();
    }
}
echo 'OK';