<?php
require_once '../config.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'guru') {
    header('Location: ../index.php'); exit;
}

$id_guru = $_SESSION['user_id'];

// 1. Dapatkan info guru dan kelasnya
$stmtG = $conn->prepare("SELECT * FROM teacher WHERE id_user = ?");
$stmtG->bind_param("i", $id_guru);
$stmtG->execute();
$teacher = $stmtG->get_result()->fetch_assoc();

if (!$teacher) {
    die("Profil guru tidak ditemui. Sila kemaskini profil anda terlebih dahulu.");
}

$myClass = $teacher['class'];

// 2. Ambil senarai pelajar mengikut penapisan kelas
// Disusun mengikut XP Tertinggi (Ranking)
$sqlS = "SELECT s.*, u.username 
         FROM student s
         JOIN users u ON s.id_user = u.id_user
         WHERE TRIM(s.class) = TRIM(?) 
         ORDER BY s.current_xp DESC";
$stmtS = $conn->prepare($sqlS);
$stmtS->bind_param("s", $myClass);
$stmtS->execute();
$studentsList = $stmtS->get_result();

date_default_timezone_set('Asia/Kuala_Lumpur');
$currentTime = date('h:i A');
$page = 'marks'; 
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Markah Pelajar | Mathventure</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../asset/css/dashboard-teacher.css?v=<?php echo time(); ?>">
    <style>
        .table-card { background: white; padding: 20px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-top: 20px; overflow-x: auto; }
        .nice-table { width: 100%; border-collapse: collapse; text-align: left; }
        .nice-table th { padding: 12px 15px; border-bottom: 2px solid #f0f0f0; color: #7f8c8d; font-size: 11px; text-transform: uppercase; }
        .nice-table td { padding: 15px; border-bottom: 1px solid #f9f9f9; vertical-align: middle; font-size: 14px; }
        .rank-badge { background: #f1c40f; color: white; padding: 4px 10px; border-radius: 8px; font-weight: 800; font-size: 13px; }
        .status-pill { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
        .xp-text { color: #2ecc71; font-weight: 800; }
        .lvl-box { font-size: 12px; color: #34495e; background: #ebf5fb; padding: 2px 6px; border-radius: 4px; margin-right: 2px; }
    </style>
</head>
<body>
<div class="teacher-layout">
    
    <aside class="sidebar">
        <div class="sidebar-top">
            <div class="brand">
                <div class="brand-avatar">M</div>
                <div class="brand-text">
                    <h1>Mathventure</h1>
                    <span>Teacher Mode</span>
                </div>
            </div>
            <nav class="side-nav">
                <a href="dashboard-teacher.php" class="nav-item"><div class="nav-icon"><i class="fa-solid fa-house"></i></div><div class="nav-label">Dashboard</div></a>
                <a href="teacher-attendance.php" class="nav-item"><div class="nav-icon"><i class="fa-solid fa-calendar-check"></i></div><div class="nav-label">Kehadiran</div></a>
                <a href="teacher-marks.php" class="nav-item active"><div class="nav-icon"><i class="fa-solid fa-chart-column"></i></div><div class="nav-label">Markah Pelajar</div></a>
                <a href="teacher-profile.php" class="nav-item"><div class="nav-icon"><i class="fa-solid fa-id-badge"></i></div><div class="nav-label">Profil Guru</div></a>
            </nav>
        </div>
        <div class="sidebar-bottom">
            <div class="teacher-mini">
                <div class="teacher-mini-avatar"><?php echo strtoupper(substr($teacher['firstname'] ?? 'G', 0, 1)); ?></div>
                <div class="teacher-mini-info">
                    <div class="teacher-mini-name"><?php echo htmlspecialchars($teacher['firstname'] . ' ' . $teacher['lastname']); ?></div>
                    <div class="teacher-mini-role">Kelas: <?php echo htmlspecialchars($myClass); ?></div>
                </div>
            </div>
            <form action="../logout.php" method="post"><button type="submit" class="btn-logout"><i class="fa-solid fa-right-from-bracket"></i> Keluar</button></form>
        </div>
    </aside>

    <main class="main-content">
        <div class="hero-row">
            <div>
                <div class="hero-text-top">Pencapaian Murid</div>
                <div class="hero-text-main">Prestasi Keseluruhan Kelas <?php echo htmlspecialchars($myClass); ?></div>
            </div>
            <div class="hero-clock"><i class="fa-solid fa-clock"></i> <?php echo $currentTime; ?></div>
        </div>

        <section class="cards-grid">
            <article class="card-simple">
                <div class="card-simple-title"><span>🏆</span> JUARA KELAS (XP TERTINGGI)</div>
                <div class="card-simple-body" style="font-size: 24px; font-weight: 800; color: #2c3e50;">
                    <?php 
                        $firstRow = $studentsList->fetch_assoc();
                        if ($firstRow) {
                            echo htmlspecialchars($firstRow['firstname'] ?: $firstRow['username']);
                            $studentsList->data_seek(0); // Reset balik untuk table
                        } else {
                            echo "Tiada Data";
                        }
                    ?>
                </div>
            </article>
        </section>

        <section class="table-card">
            <table class="nice-table">
                <thead>
                    <tr>
                        <th>Ranking</th>
                        <th>Nama Murid</th>
                        <th>Progress (T4 | T5 | T6)</th>
                        <th>XP Terkumpul</th>
                        <th>Coins ⭐</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $rank = 1;
                    while($s = $studentsList->fetch_assoc()): 
                        $displayName = !empty($s['firstname']) ? $s['firstname'] . ' ' . $s['lastname'] : $s['username'];
                    ?>
                    <tr>
                        <td><span class="rank-badge">#<?php echo $rank++; ?></span></td>
                        <td><strong><?php echo htmlspecialchars($displayName); ?></strong></td>
                        <td>
                            <span class="lvl-box">Lvl <?php echo $s['level_t4']; ?></span>
                            <span class="lvl-box">Lvl <?php echo $s['level_t5']; ?></span>
                            <span class="lvl-box">Lvl <?php echo $s['level_t6']; ?></span>
                        </td>
                        <td><span class="xp-text"><?php echo number_format($s['current_xp']); ?> XP</span></td>
                        <td><span style="color:#f39c12; font-weight:bold;"><?php echo $s['coins']; ?></span></td>
                        <td>
                            <?php if($s['current_xp'] >= 1000): ?>
                                <span class="status-pill" style="background:#e8f8f5; color:#1abc9c;">Cemerlang</span>
                            <?php elseif($s['current_xp'] >= 500): ?>
                                <span class="status-pill" style="background:#ebf5fb; color:#3498db;">Gigih</span>
                            <?php else: ?>
                                <span class="status-pill" style="background:#fef5e7; color:#f39c12;">Aktif</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>

                    <?php if($studentsList->num_rows == 0): ?>
                        <tr><td colspan="6" style="text-align:center; padding:50px; color:#95a5a6;">Tiada murid berdaftar dalam kelas ini.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>
</div>
</body>
</html>