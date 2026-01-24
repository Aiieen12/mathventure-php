<?php
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uid = (int)$_SESSION['user_id'];
    $thn = (int)$_POST['tahun'];
    $lvl = (int)$_POST['level'];
    $scr = (int)$_POST['score'];
    $ttl = (int)$_POST['total_questions'];

    // Hadiah XP & Coins
    $coins = $scr * 5;
    $xp = $scr * 10;
    $conn->query("UPDATE student SET coins = coins + $coins, current_xp = current_xp + $xp WHERE id_user = $uid");

    // Jika skor penuh, buka level seterusnya & lencana
    if ($scr >= $ttl) {
        // Update progress peta
        $col = "level_t" . $thn;
        $conn->query("UPDATE student SET $col = $lvl + 1 WHERE id_user = $uid AND $col <= $lvl");

        // Simpan Lencana
        $check = $conn->query("SELECT id_badge_win FROM student_badges WHERE id_user = $uid AND tahun = $thn AND level = $lvl");
        if ($check->num_rows == 0) {
            $name = "Hero T$thn L$lvl";
            $stmt = $conn->prepare("INSERT INTO student_badges (id_user, badge_name, tahun, level, date_unlocked) VALUES (?, ?, ?, ?, NOW())");
            $stmt->bind_param("isii", $uid, $name, $thn, $lvl);
            $stmt->execute();
        }
    }
}