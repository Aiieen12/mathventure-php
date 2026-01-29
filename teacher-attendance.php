<?php
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'guru') {
    header('Location: index.php');
    exit;
}

$id_guru = $_SESSION['user_id'];

// 1. Ambil data kelas guru
$stmtG = $conn->prepare("SELECT class FROM teacher WHERE id_user = ?");
$stmtG->bind_param("i", $id_guru);
$stmtG->execute();
$teacher = $stmtG->get_result()->fetch_assoc();
$myClass = $teacher['class'] ?? 'Tiada Kelas';

// 2. LOGIK SIMPAN (Dibetulkan mengikut kolum id_attendance)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_attendance'])) {
    foreach ($_POST['attendance'] as $student_id => $status) {
        $student_id = (int)$student_id;
        
        // Semak rekod sedia ada (Guna id_attendance jika perlu, tapi id_user sudah cukup)
        $check = $conn->prepare("SELECT id_attendance FROM attendance WHERE id_user = ? AND date_recorded = CURDATE()");
        $check->bind_param("i", $student_id);
        $check->execute();
        $exists = $check->get_result()->num_rows > 0;

        if ($exists) {
            $stmt = $conn->prepare("UPDATE attendance SET status = ? WHERE id_user = ? AND date_recorded = CURDATE()");
            $stmt->bind_param("si", $status, $student_id);
        } else {
            $stmt = $conn->prepare("INSERT INTO attendance (id_user, status, date_recorded) VALUES (?, ?, CURDATE())");
            $stmt->bind_param("is", $student_id, $status);
        }
        $stmt->execute();
    }
    $_SESSION['success_msg'] = "Kehadiran kelas $myClass berjaya disimpan!";
    header('Location: dashboard-teacher.php');
    exit;
}

// 3. AMBIL SENARAI MURID (Guna GROUP BY untuk elak nama berulang 3 kali)
$sqlS = "SELECT s.id_user, s.firstname, s.lastname, u.username, a.status 
         FROM student s
         JOIN users u ON s.id_user = u.id_user
         LEFT JOIN attendance a ON s.id_user = a.id_user AND a.date_recorded = CURDATE()
         WHERE TRIM(s.class) = TRIM(?)
         GROUP BY s.id_user
         ORDER BY s.firstname ASC";
         
$stmtS = $conn->prepare($sqlS);
$stmtS->bind_param("s", $myClass);
$stmtS->execute();
$students = $stmtS->get_result();
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Kehadiran | Mathventure</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- ✅ UI sahaja: guna layout teacher yang sama (nav bar/ sidebar) -->
    <link rel="stylesheet" href="asset/css/dashboard-teacher.css">
    <link rel="stylesheet" href="asset/css/teacher-attendance.css?v=<?php echo time(); ?>">
</head>
<body>

<div class="teacher-layout">
    <!-- SIDEBAR / NAVBAR -->
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
                <a href="dashboard-teacher.php" class="nav-item">
                    <div class="nav-icon"><i class="fa-solid fa-house"></i></div>
                    <div class="nav-label">Dashboard</div>
                </a>

                <a href="teacher-attendance.php" class="nav-item active">
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
            <form action="logout.php" method="post">
                <button type="submit" class="btn-logout">
                    <i class="fa-solid fa-right-from-bracket"></i> Keluar
                </button>
            </form>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <div class="dashboard-shell attendance-shell">

            <div class="page-header">
                <a href="dashboard-teacher.php" class="btn-back">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>

                <div class="page-title">
                    <h2>Kehadiran Murid</h2>
                    <p>Kelas <?php echo htmlspecialchars($myClass); ?></p>
                </div>
            </div>

            <div class="attendance-card">
                <form action="" method="POST">
                    <div class="table-wrap">
                        <table class="nice-table attendance-table">
                            <thead>
                                <tr>
                                    <th>Nama Murid</th>
                                    <th class="th-center">Status</th>
                                </tr>
                            </thead>

                            <tbody>
                            <?php if ($students->num_rows > 0): ?>
                                <?php while($s = $students->fetch_assoc()):
                                    $name = !empty($s['firstname']) ? $s['firstname'] . ' ' . $s['lastname'] : $s['username'];
                                    $status = $s['status'] ?? 'H';
                                ?>
                                <tr>
                                    <td>
                                        <div class="student-box">
                                            <div class="avatar"><?php echo strtoupper($name[0]); ?></div>
                                            <div class="student-meta">
                                                <strong class="student-name"><?php echo htmlspecialchars($name); ?></strong>
                                                <small class="student-hint">Tandakan status untuk hari ini</small>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="td-center">
                                        <div class="status-options">
                                            <label class="status-pill hadir">
                                                <input type="radio"
                                                       name="attendance[<?php echo $s['id_user']; ?>]"
                                                       value="H"
                                                       <?php echo ($status == 'H') ? 'checked' : ''; ?>>
                                                <span class="status-btn">
                                                    <i class="fa-solid fa-check"></i> Hadir
                                                </span>
                                            </label>

                                            <label class="status-pill tiada">
                                                <input type="radio"
                                                       name="attendance[<?php echo $s['id_user']; ?>]"
                                                       value="TH"
                                                       <?php echo ($status == 'TH') ? 'checked' : ''; ?>>
                                                <span class="status-btn">
                                                    <i class="fa-solid fa-xmark"></i> Tiada
                                                </span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2" class="empty-row">Tiada murid dijumpai untuk kelas ini.</td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <button type="submit" name="save_attendance" class="btn-save">
                        <i class="fa-solid fa-cloud-arrow-up"></i> Simpan Kehadiran
                    </button>
                </form>
            </div>

        </div>
    </main>
</div>

</body>
</html>
