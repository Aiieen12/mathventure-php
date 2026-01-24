<?php
// auth/teacher-profile.php
require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Pastikan user sudah login & role = guru
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'guru') {
    $_SESSION['error'] = 'Sila log masuk sebagai guru untuk akses halaman ini.';
    header('Location: index.php');
    exit;
}

$user_id = (int) $_SESSION['user_id'];

// =================== AMBIL DATA GURU ===================

$teacherName  = $_SESSION['username'] ?? 'Guru';
$teacherClass = '';
$teacherYear  = '';
$teacherBio   = '';
$firstname    = '';
$lastname     = '';

$stmt = $conn->prepare("SELECT * FROM teacher WHERE id_user = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
if ($row = $res->fetch_assoc()) {
    $firstname    = $row['firstname'] ?? '';
    $lastname     = $row['lastname'] ?? '';
    $teacherName  = trim(($row['firstname'] ?? '') . ' ' . ($row['lastname'] ?? '')) ?: $teacherName;
    $teacherClass = $row['class'] ?? '';
    $teacherYear  = $row['year'] ?? '';
    $teacherBio   = $row['bio'] ?? '';
}
$stmt->close();

// nama pertama untuk avatar
$firstNameOnly = $teacherName;
if (strpos($teacherName, ' ') !== false) {
    $parts = explode(' ', $teacherName);
    $firstNameOnly = $parts[0];
}

date_default_timezone_set('Asia/Kuala_Lumpur');
$currentTime = date('h:i A');

$page = 'profile';
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Profil Guru | Mathventure</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="asset/css/dashboard-teacher.css?v=<?php echo time(); ?>">
</head>
<body>

<div class="teacher-layout">

    <!-- ================= SIDEBAR ================= -->
    <aside class="sidebar">
        <div class="sidebar-top">
            <div class="brand">
                <div class="brand-avatar">M</div>
                <div class="brand-text">
                    <h1>Mathventure</h1>
                    <span>Teacher Mode</span>
                </div>
            </div>

            <div>
                <div class="nav-group-label">Menu</div>
                <nav class="side-nav">
                    <a href="dashboard-teacher.php"
                       class="nav-item <?php echo $page === 'dashboard' ? 'active' : ''; ?>">
                        <div class="nav-icon"><i class="fa-solid fa-house"></i></div>
                        <div class="nav-label">Dashboard</div>
                    </a>

                    <a href="teacher-attendance.php"
                       class="nav-item <?php echo $page === 'attendance' ? 'active' : ''; ?>">
                        <div class="nav-icon"><i class="fa-solid fa-calendar-check"></i></div>
                        <div class="nav-label">Kehadiran</div>
                    </a>

                    <a href="teacher-marks.php"
                       class="nav-item <?php echo $page === 'marks' ? 'active' : ''; ?>">
                        <div class="nav-icon"><i class="fa-solid fa-chart-column"></i></div>
                        <div class="nav-label">Markah Pelajar</div>
                    </a>

                    <a href="teacher-profile.php"
                       class="nav-item <?php echo $page === 'profile' ? 'active' : ''; ?>">
                        <div class="nav-icon"><i class="fa-solid fa-id-badge"></i></div>
                        <div class="nav-label">Profil Guru</div>
                    </a>
                </nav>
            </div>
        </div>

        <div class="sidebar-bottom">
            <div class="teacher-mini">
                <div class="teacher-mini-avatar">
                    <?php echo strtoupper(substr($firstNameOnly, 0, 1)); ?>
                </div>
                <div class="teacher-mini-info">
                    <div class="teacher-mini-name"><?php echo htmlspecialchars($teacherName); ?></div>
                    <div class="teacher-mini-role">
                        <?php echo $teacherClass ? 'Guru Kelas · ' . htmlspecialchars($teacherClass) : 'Guru Matematik'; ?>
                    </div>
                </div>
            </div>

            <form action="logout.php" method="post">
                <button type="submit" class="btn-logout">
                    <i class="fa-solid fa-right-from-bracket"></i> Log Keluar
                </button>
            </form>
        </div>
    </aside>

    <!-- ================= MAIN CONTENT ================= -->
    <main class="main-content">
        <div class="dashboard-shell">

            <!-- Hero -->
            <div class="hero-row">
                <div>
                    <div class="hero-text-top">
                        Tetapan untuk <span><?php echo htmlspecialchars($teacherName); ?></span>
                    </div>
                    <div class="hero-text-main">
                        Kemas kini profil guru dengan lebih santai 🧑‍🏫
                    </div>
                    <div style="font-size:13px;color:#7f8c8d;margin-top:4px;">
                        Maklumat ini digunakan di dashboard, kehadiran dan markah pelajar.
                    </div>
                </div>
                <div>
                    <div class="hero-clock">
                        <i class="fa-solid fa-clock"></i>
                        <span><?php echo $currentTime; ?></span>
                    </div>
                </div>
            </div>

            <!-- Notis berjaya / ralat -->
            <?php if (!empty($_SESSION['success'])): ?>
                <div style="margin-top:10px;">
                    <div class="panel-guru" style="background:#e8faf1;color:#1e8449;">
                        <div class="panel-text" style="max-width:100%;">
                            <div class="panel-title" style="color:#1e8449;font-size:16px;">
                                <i class="fa-solid fa-circle-check"></i>
                                &nbsp;Profil dikemaskini
                            </div>
                            <div class="panel-desc" style="color:#1e8449;">
                                <?php 
                                echo htmlspecialchars($_SESSION['success']); 
                                unset($_SESSION['success']);
                                ?>
                            </div>
                        </div>
                        <div class="panel-icon-box" style="background:white;color:#1e8449;">
                            <i class="fa-solid fa-thumbs-up"></i>
                        </div>
                    </div>
                </div>
            <?php elseif (!empty($_SESSION['error'])): ?>
                <div style="margin-top:10px;">
                    <div class="panel-guru" style="background:#fdecea;color:#c0392b;">
                        <div class="panel-text" style="max-width:100%;">
                            <div class="panel-title" style="color:#c0392b;font-size:16px;">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                &nbsp;Ralat
                            </div>
                            <div class="panel-desc" style="color:#c0392b;">
                                <?php 
                                echo htmlspecialchars($_SESSION['error']); 
                                unset($_SESSION['error']);
                                ?>
                            </div>
                        </div>
                        <div class="panel-icon-box" style="background:white;color:#c0392b;">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Kad borang profil -->
            <section class="form-card" style="margin-top:14px;">
                <div class="profile-top">
                    <div class="profile-avatar">
                        <i class="fa-solid fa-chalkboard-user"></i>
                    </div>
                    <div class="profile-main-info">
                        <h2><?php echo htmlspecialchars($teacherName); ?></h2>
                        <p>
                            <?php if ($teacherClass): ?>
                                Guru Kelas <strong><?php echo htmlspecialchars($teacherClass); ?></strong>
                            <?php else: ?>
                                Guru Matematik
                            <?php endif; ?>
                            <?php if ($teacherYear): ?>
                                · Mengajar <strong><?php echo htmlspecialchars($teacherYear); ?></strong>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>

                <form action="process-teacher-profile.php" method="post" style="margin-top:16px;">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Nama Pertama</label>
                            <input type="text" name="firstname" class="form-control"
                                   value="<?php echo htmlspecialchars($firstname); ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Nama Terakhir</label>
                            <input type="text" name="lastname" class="form-control"
                                   value="<?php echo htmlspecialchars($lastname); ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Kelas Utama</label>
                            <input type="text" name="class" class="form-control"
                                   placeholder="Contoh: 4 Dinamik / 5 Cendekia"
                                   value="<?php echo htmlspecialchars($teacherClass); ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Tahun Diajarkan</label>
                            <input type="text" name="year" class="form-control"
                                   placeholder="Contoh: Tahun 4, atau Tahun 4–6"
                                   value="<?php echo htmlspecialchars($teacherYear); ?>">
                        </div>
                    </div>

                    <div class="form-group" style="margin-top:10px;">
                        <label class="form-label">Bio Ringkas</label>
                        <textarea name="bio" class="form-textarea"
                                  placeholder="Contoh: Guru Matematik Tahap 2 yang meminati pembelajaran berasaskan permainan."><?php
                            echo htmlspecialchars($teacherBio);
                        ?></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">
                            <i class="fa-solid fa-floppy-disk"></i> Simpan Profil
                        </button>
                        <a href="dashboard-teacher.php" class="btn-outline">
                            <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
                        </a>
                    </div>
                </form>
            </section>

        </div>
    </main>

</div>

</body>
</html>
