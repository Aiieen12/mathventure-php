<?php
require_once '../config.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'pelajar') {
    $_SESSION['error'] = 'Sila log masuk.';
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$nama = $_SESSION['username'] ?? 'Pelajar';

// --- TARIK DATA PELAJAR ---
$stmt = $conn->prepare("SELECT * FROM student WHERE id_user = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

// Default data jika pelajar baru
if (!$student) {
    $student = ['firstname'=>'Pelajar', 'lastname'=>'Baru', 'bio'=>'Saya suka matematik!', 'level'=>1, 'current_xp'=>0, 'max_xp'=>100, 'coins'=>0, 'lives'=>5, 'avatar'=>'dino-1.png'];
    // Auto insert data kosong jika perlu, tapi setakat ini kita guna default array display
}

// Senarai Avatar (Pastikan nama file ini wujud dalam folder images, atau guna placeholder)
$avatar_list = [
    'dinasour2.png', // Default anda
    'dino-red.png',  // Sila letak gambar lain jika ada
    'dino-blue.png',
    'dino-girl.png'
];

$xp_percentage = ($student['max_xp'] > 0) ? ($student['current_xp'] / $student['max_xp']) * 100 : 0;
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | Mathventure</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Varela+Round&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../asset/css/dashboard-student.css?v=<?php echo time(); ?>">
</head>
<body>

    <div class="game-bg"></div>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="logo-area">
                <div class="logo-icon">🦖</div>
                <div class="logo-text">Mathventure</div>
            </div>
            <button class="close-btn" id="closeSidebarBtn">✖</button>
        </div>

        <nav class="side-nav">
            <a href="dashboard-student.php" class="nav-item active"><span class="icon">🏠</span> Dashboard</a>
            <a href="game-menu.php" class="nav-item"><span class="icon">🗺️</span> Peta Dunia</a>
            <a href="modul-belajar.php" class="nav-item"><span class="icon">📚</span> Modul</a>
            <a href="#" class="nav-item"><span class="icon">🏆</span> Pencapaian</a>
            <div class="divider"></div>
            <a href="logout.php" class="nav-item logout"><span class="icon">🚪</span> Log Keluar</a>
        </nav>

<h2>Menu Utama</h2>
<p>
    Buat masa ini, kita fokus untuk lihat pergerakan sistem permainan.
</p>
<ul>
    <li>Klik <strong>Peta</strong> di bahagian nav untuk ke menu game.</li>
</ul>

</body>
</html>