<?php
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'guru') {
    header('Location: index.php'); exit;
}

$id_guru = $_SESSION['user_id'];
$stmtG = $conn->prepare("SELECT * FROM teacher WHERE id_user = ?");
$stmtG->bind_param("i", $id_guru);
$stmtG->execute();
$teacher = $stmtG->get_result()->fetch_assoc();
$myClass = $teacher['class'] ?? 'Tiada Kelas';

$sqlS = "SELECT s.*, u.username FROM student s
         JOIN users u ON s.id_user = u.id_user
         WHERE TRIM(s.class) = TRIM(?) 
         GROUP BY s.id_user
         ORDER BY s.current_xp DESC";
$stmtS = $conn->prepare($sqlS);
$stmtS->bind_param("s", $myClass);
$stmtS->execute();
$result = $stmtS->get_result();

$students = [];
$chartData = ['Cemerlang' => 0, 'Gigih' => 0, 'Aktif' => 0];
while($row = $result->fetch_assoc()) {
    $students[] = $row;
    if($row['current_xp'] >= 1000) $chartData['Cemerlang']++;
    elseif($row['current_xp'] >= 500) $chartData['Gigih']++;
    else $chartData['Aktif']++;
}
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Analisis Prestasi | Mathventure</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="asset/css/dashboard-teacher.css">
    <link rel="stylesheet" href="asset/css/teacher-marks.css?v=<?php echo time(); ?>">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="teacher-layout">
    <aside class="sidebar">
        <div class="sidebar-top">
            <div class="brand">
                <div class="brand-avatar">M</div>
                <div class="brand-text"><h1>Mathventure</h1><span>Teacher Mode</span></div>
            </div>
            <nav class="side-nav">
                <a href="dashboard-teacher.php" class="nav-item"><div class="nav-icon"><i class="fa-solid fa-house"></i></div><div class="nav-label">Dashboard</div></a>
                <a href="teacher-attendance.php" class="nav-item"><div class="nav-icon"><i class="fa-solid fa-calendar-check"></i></div><div class="nav-label">Kehadiran</div></a>
                <a href="teacher-marks.php" class="nav-item active"><div class="nav-icon"><i class="fa-solid fa-chart-column"></i></div><div class="nav-label">Markah Pelajar</div></a>
                <a href="teacher-profile.php" class="nav-item"><div class="nav-icon"><i class="fa-solid fa-id-badge"></i></div><div class="nav-label">Profil Guru</div></a>
            </nav>
        </div>
        <div class="sidebar-bottom">
            <form action="logout.php" method="post"><button type="submit" class="btn-logout"><i class="fa-solid fa-right-from-bracket"></i> Keluar</button></form>
        </div>
    </aside>

    <main class="main-content">
        <header class="content-header">
            <h2 class="main-title">Analisis Prestasi: <?php echo htmlspecialchars($myClass); ?></h2>
        </header>

        <div class="stats-container">
            <div class="champion-card">
                <div class="champ-info">
                    <span class="tag">TOP PLAYER</span>
                    <h3 class="name"><?php echo !empty($students) ? htmlspecialchars($students[0]['firstname'] ?: $students[0]['username']) : "---"; ?></h3>
                    <div class="xp-badge"><?php echo !empty($students) ? number_format($students[0]['current_xp']) : '0'; ?> XP</div>
                </div>
                <i class="fa-solid fa-trophy trophy-bg"></i>
            </div>

            <div class="chart-card">
                <p class="chart-label">Taburan Prestasi</p>
                <div class="canvas-wrapper">
                    <canvas id="marksChart"></canvas>
                </div>
            </div>
        </div>

        <div class="table-card">
            <table class="marks-table">
                <thead>
                    <tr>
                        <th>RANK</th>
                        <th>NAMA</th>
                        <th>LEVEL</th>
                        <th>XP</th>
                        <th>STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $rank=1; foreach($students as $s): ?>
                    <tr>
                        <td><span class="rank">#<?php echo $rank++; ?></span></td>
                        <td><strong><?php echo htmlspecialchars($s['firstname'] ?: $s['username']); ?></strong></td>
                        <td class="lvl-txt">T4:<?php echo $s['level_t4']; ?> | T5:<?php echo $s['level_t5']; ?></td>
                        <td class="xp-bold"><?php echo $s['current_xp']; ?></td>
                        <td><span class="status">Aktif</span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<script>
const ctx = document.getElementById('marksChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Cem.', 'Gigih', 'Aktif'],
        datasets: [{
            data: [<?php echo $chartData['Cemerlang']; ?>, <?php echo $chartData['Gigih']; ?>, <?php echo $chartData['Aktif']; ?>],
            backgroundColor: ['#2ecc71', '#3498db', '#f39c12'],
            borderRadius: 4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { display: false }, grid: { display: false } }, x: { grid: { display: false } } }
    }
});
</script>
</body>
</html>