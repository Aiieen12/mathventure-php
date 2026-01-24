<?php
// auth/dashboard-teacher.php
require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. KESELAMATAN: Pastikan user sudah login & role = guru
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'guru') {
    $_SESSION['error'] = 'Sila log masuk sebagai guru untuk akses halaman ini.';
    header('Location: index.php');
    exit;
}

$user_id = (int) $_SESSION['user_id'];

// 2. AMBIL DATA GURU SEBENAR
$teacherName  = $_SESSION['username'] ?? 'Guru';
$teacherClass = 'Belum Set'; 

$stmt = $conn->prepare("SELECT * FROM teacher WHERE id_user = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $fname = $row['firstname'] ?? '';
    $lname = $row['lastname'] ?? '';
    $full  = trim($fname . ' ' . $lname);
    if ($full !== '') { $teacherName = $full; }
    if (!empty($row['class'])) { $teacherClass = $row['class']; }
}
$stmt->close();

$firstNameOnly = (strpos($teacherName, ' ') !== false) ? explode(' ', $teacherName)[0] : $teacherName;
$sapaanGuru = 'cikgu ' . $firstNameOnly;

// 3. LOGIK DATA DINAMIK
// A. Kira Jumlah Murid Aktif
$stmtCount = $conn->prepare("SELECT COUNT(DISTINCT id_user) as total FROM student WHERE TRIM(class) = TRIM(?)");
$stmtCount->bind_param("s", $teacherClass);
$stmtCount->execute();
$countData = $stmtCount->get_result()->fetch_assoc();
$activeStudents = $countData['total'] ?? 0;

// B. Kira Kehadiran Hari Ini
$stmtAtt = $conn->prepare("
    SELECT 
        COUNT(CASE WHEN a.status = 'H' THEN 1 END) as total_hadir,
        COUNT(CASE WHEN a.status = 'TH' THEN 1 END) as total_tak_hadir
    FROM attendance a
    JOIN student s ON a.id_user = s.id_user
    WHERE TRIM(s.class) = TRIM(?) AND a.date_recorded = CURDATE()
");
$stmtAtt->bind_param("s", $teacherClass);
$stmtAtt->execute();
$attData = $stmtAtt->get_result()->fetch_assoc();

$hadirCount    = $attData['total_hadir'] ?? 0;
$takHadirCount = $attData['total_tak_hadir'] ?? 0;
$totalRecord   = $hadirCount + $takHadirCount;
$attendancePercent = ($totalRecord > 0) ? round(($hadirCount / $totalRecord) * 100) : 0;

// C. Mesej Notifikasi
$successMsg = '';
if (isset($_SESSION['success_msg'])) {
    $successMsg = $_SESSION['success_msg'];
    unset($_SESSION['success_msg']);
}

date_default_timezone_set('Asia/Kuala_Lumpur');
$currentTime = date('h:i A');
$page = 'dashboard';
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Guru | Mathventure</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="asset/css/dashboard-teacher.css?v=<?php echo time(); ?>">
    <style>
        .alert-success {
            position: fixed; top: 20px; right: 20px; background: #2ecc71; color: white; 
            padding: 15px 25px; border-radius: 12px; box-shadow: 0 10px 20px rgba(0,0,0,0.1); 
            z-index: 9999; display: flex; align-items: center; gap: 10px; animation: slideIn 0.5s ease-out;
        }
        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    </style>
</head>
<body>

<?php if ($successMsg): ?>
    <div class="alert-success" id="successAlert">
        <i class="fa-solid fa-circle-check"></i> <strong>Berjaya!</strong> <?php echo $successMsg; ?>
    </div>
    <script>setTimeout(() => { document.getElementById('successAlert').style.display = 'none'; }, 3000);</script>
<?php endif; ?>

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
                <a href="dashboard-teacher.php" class="nav-item <?php echo $page === 'dashboard' ? 'active' : ''; ?>">
                    <div class="nav-icon"><i class="fa-solid fa-house"></i></div>
                    <div class="nav-label">Dashboard</div>
                </a>
                <a href="teacher-attendance.php" class="nav-item">
                    <div class="nav-icon"><i class="fa-solid fa-calendar-check"></i></div>
                    <div class="nav-label">Kehadiran</div>
                </a>
                <a href="teacher-marks.php" class="nav-item">
                    <div class="nav-icon"><i class="fa-solid fa-chart-column"></i></div>
                    <div class="nav-label">Markah Pelajar</div>
                </a>
                <a href="teacher-profile.php" class="nav-item">
                    <div class="nav-icon"><i class="fa-solid fa-id-badge"></i></div>
                    <div class="nav-label">Profil Guru</div>
                </a>
            </nav>
        </div>
        <div class="sidebar-bottom">
            <div class="teacher-mini">
                <div class="teacher-mini-avatar"><?php echo strtoupper(substr($firstNameOnly, 0, 1)); ?></div>
                <div class="teacher-mini-info">
                    <div class="teacher-mini-name"><?php echo htmlspecialchars($teacherName); ?></div>
                    <div class="teacher-mini-role">Guru Kelas · <?php echo htmlspecialchars($teacherClass); ?></div>
                </div>
            </div>
            <form action="logout.php" method="post">
                <button type="submit" class="btn-logout"><i class="fa-solid fa-right-from-bracket"></i> Log Keluar</button>
            </form>
        </div>
    </aside>

    <main class="main-content">
        <div class="dashboard-shell">
            <div class="hero-row">
                <div>
                    <div class="hero-text-top">Selamat kembali, <span><?php echo htmlspecialchars($sapaanGuru); ?>!</span></div>
                    <div class="hero-text-main">Siap sedia untuk mengajar hari ini? 🍎</div>
                </div>
                <div class="hero-clock">
                    <i class="fa-solid fa-clock"></i> <span><?php echo $currentTime; ?></span>
                </div>
            </div>

            <section class="panel-guru">
                <div class="panel-text">
                    <div class="panel-title">Panel Kawalan Guru</div>
                    <div class="panel-desc">Pantau perkembangan murid dalam kelas anda secara real-time.</div>
                    <div class="panel-tags">
                        <div class="tag-pill"><i class="fa-solid fa-people-group"></i> <span>Kelas: <?php echo htmlspecialchars($teacherClass); ?></span></div>
                        <div class="tag-pill"><i class="fa-solid fa-user-graduate"></i> <span><?php echo $activeStudents; ?> murid aktif</span></div>
                    </div>
                </div>
                <div class="panel-icon-box"><i class="fa-solid fa-book-open"></i></div>
            </section>

            <section class="cards-grid">
                <article class="card-kpi">
                    <div class="card-kpi-header">
                        <span class="card-kpi-label">KEHADIRAN HARI INI</span>
                        <span class="card-kpi-chip">Live</span>
                    </div>
                    <div class="card-kpi-main">
                        <div class="card-kpi-value"><?php echo $attendancePercent; ?>%</div>
                        <div class="card-kpi-sub">Purata kehadiran kelas <?php echo htmlspecialchars($teacherClass); ?> hari ini.</div>
                    </div>
                </article>

                <article class="card-simple">
                    <div class="card-simple-title"><span>🏅</span> <span>STATUS REKOD</span></div>
                    <div class="card-simple-body">
                        <strong>Kehadiran:</strong> 
                        <?php echo ($totalRecord > 0) ? '<span style="color:#2ecc71">Sudah dikemaskini</span>' : '<span style="color:#e74c3c">Belum diambil</span>'; ?><br>
                        Sila pastikan semua data murid tepat.
                    </div>
                </article>

                <article class="card-simple">
                    <div class="card-simple-title"><span>🔔</span> <span>PERINGATAN</span></div>
                    <div class="card-simple-body">
                        Semak markah topik "Nombor Bulat" murid sebelum akhir minggu ini.
                    </div>
                </article>
            </section>

            <section class="cards-grid">
                <article class="card-action">
                    <div class="card-action-header">
                        <div class="card-action-icon"><i class="fa-solid fa-user-check"></i></div>
                        <div>
                            <div class="card-action-title">Kehadiran Pelajar</div>
                            <div class="card-action-desc">Tanda kehadiran atau lihat statistik harian.</div>
                        </div>
                    </div>
                    <div class="card-action-desc" style="font-size:12px;color:#7f8c8d;margin:10px 0;">
                        <?php echo $takHadirCount; ?> murid dilaporkan tidak hadir hari ini.
                    </div>
                    <div class="card-action-footer">
                        <a href="teacher-attendance.php" class="btn-outline">Buka Kehadiran</a>
                    </div>
                </article>

                <article class="card-action">
                    <div class="card-action-header">
                        <div class="card-action-icon"><i class="fa-solid fa-chart-line"></i></div>
                        <div>
                            <div class="card-action-title">Markah & Prestasi</div>
                            <div class="card-action-desc">Analisis pencapaian murid dalam kuiz.</div>
                        </div>
                    </div>
                    <div class="card-action-footer" style="margin-top:25px;">
                        <a href="teacher-marks.php" class="btn-primary">Lihat Markah</a>
                    </div>
                </article>
            </section>
        </div>
    </main>
</div>

</body>
</html>