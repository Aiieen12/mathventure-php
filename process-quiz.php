<?php
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uid = (int)$_SESSION['user_id'];
    $thn = (int)$_POST['tahun'];
    $lvl = (int)$_POST['level'];
    $scr = (int)$_POST['score']; // Markah yang pelajar dapat
    $ttl = (int)$_POST['total_questions']; // Jumlah soalan (biasanya 3)

    /* ==========================================
       LOGIK PENGURANGAN NYAWA
       Tolak nyawa jika markah KURANG daripada jumlah soalan
       (Dapat 0/3, 1/3, atau 2/3 semua tolak nyawa)
       ========================================== */
    if ($scr < $ttl) {
        // Ambil nyawa semasa
        $getLives = $conn->query("SELECT lives FROM student WHERE id_user = $uid");
        $currData = $getLives->fetch_assoc();
        
        if ($currData && $currData['lives'] > 0) {
            // Tolak 1 nyawa & set last_life_update untuk mula regen di dashboard
            $conn->query("UPDATE student SET 
                          lives = lives - 1, 
                          last_life_update = NOW() 
                          WHERE id_user = $uid");
        }
    }

    /* ==========================================
       LOGIK HADIAH XP & COINS (ASAL)
       ========================================== */
    $coins = $scr * 5;
    $xp = $scr * 10;
    $conn->query("UPDATE student SET coins = coins + $coins, current_xp = current_xp + $xp WHERE id_user = $uid");

    /* ==========================================
       LOGIK PROGRESS & LENCANA (ASAL)
       Hanya jika dapat skor penuh (3/3)
       ========================================== */
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
?>