<?php
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pelajar') {
    header('Location: index.php');
    exit;
}

$id_user = (int)$_SESSION['user_id'];

// Ambil data profil pelajar
$stmt = $conn->prepare("SELECT s.*, u.username FROM student s JOIN users u ON s.id_user = u.id_user WHERE s.id_user = ?");
$stmt->bind_param("i", $id_user);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Ambil rekod lencana (Hanya skor penuh 3/3 yang masuk ke sini mengikut process-quiz.php)
$unlockedBadges = [];
$resB = $conn->query("SELECT tahun, level FROM student_badges WHERE id_user = $id_user");
while ($row = $resB->fetch_assoc()) {
    $unlockedBadges[$row['tahun'] . '-' . $row['level']] = true;
}

$years = [4, 5, 6];
$levels = [1, 2, 3, 4, 5];
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencapaian Lencana - Mathventure</title>
    <link rel="stylesheet" href="asset/css/badges.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<div class="app-container">
    <aside class="sidebar-main">
        <div class="sidebar-top">
            <div class="brand-info">
                <h2 class="brand-name">Mathventure</h2>
                <span class="brand-sub">Student Mode</span>
            </div>
            <div class="close-box"><i class="fas fa-times"></i></div>
        </div>
        
        <nav class="sidebar-nav">
            <a href="dashboard-student.php" class="nav-link"><i class="fas fa-home"></i> Dashboard</a>
            <a href="game-menu.php" class="nav-link"><i class="fas fa-map"></i> Peta Permainan</a>
            <a href="nota.php" class="nav-link"><i class="fas fa-book"></i> Nota Matematik</a>
            <a href="badges.php" class="nav-link active"><i class="fas fa-award"></i> Pencapaian</a>
            <a href="profile.php" class="nav-link"><i class="fas fa-user"></i> Profil</a>
            <a href="logout.php" class="nav-link nav-logout"><i class="fas fa-sign-out-alt"></i> Log Keluar</a>
        </nav>

        <div class="sidebar-profile">
            <img src="asset/images/avatar.png" alt="Avatar">
            <div class="profile-text">
                <span class="badge-level">Level 1</span>
                <p class="profile-name"><?php echo htmlspecialchars($data['username'] ?? 'anis21 nadirah'); ?></p>
            </div>
        </div>
    </aside>

    <main class="main-view">
        <div class="header-content">
            <div class="welcome-tag">Hai <strong><?php echo htmlspecialchars($data['username']); ?></strong>, ini koleksi lencana anda! 🎉</div>
            <div class="title-area">
                <h1>🏅 Pencapaian Lencana</h1>
                <p>Kumpulkan semua lencana dengan menyelesaikan level di setiap tahun!</p>
            </div>
        </div>

        <div class="content-scroll">
            <?php foreach ($years as $tahun): ?>
            <div class="year-block">
                <h2 class="year-header">Tahun <?php echo $tahun; ?></h2>
                <div class="badges-layout">
                    <?php foreach ($levels as $lvl): ?>
                        <?php 
                            $isUnlocked = isset($unlockedBadges["$tahun-$lvl"]);
                            // Path folder badges di luar asset mengikut gambar anda
                            $imageSource = "badges/T{$tahun}L{$lvl}.png"; 
                        ?>
                        <div class="badge-item <?php echo $isUnlocked ? 'state-unlocked' : 'state-locked'; ?>">
                            <div class="visual-wrapper">
                                <?php if ($isUnlocked): ?>
                                    <img src="<?php echo $imageSource; ?>" alt="Badge">
                                <?php else: ?>
                                    <div class="lock-circle"><i class="fas fa-lock"></i></div>
                                <?php endif; ?>
                            </div>
                            <div class="badge-detail">
                                <h3>Level <?php echo $lvl; ?></h3>
                                <span class="label-status">
                                    <?php echo $isUnlocked ? 'DIBUKA' : 'TERKUNCI'; ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </main>
</div>

</body>
</html>