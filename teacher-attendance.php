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
    <link rel="stylesheet" href="asset/css/teacher-attendance.css">
</head>
<body>

<div class="container">
    <div class="page-header">
        <a href="dashboard-teacher.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
        <h2 style="margin:0;">Kelas <?php echo htmlspecialchars($myClass); ?></h2>
    </div>

    <div class="attendance-card">
        <form action="" method="POST">
            <table class="nice-table">
                <thead>
                    <tr>
                        <th>Nama Murid</th>
                        <th style="text-align: center;">Status</th>
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
                                    <strong><?php echo htmlspecialchars($name); ?></strong>
                                </div>
                            </td>
                            <td>
                                <div class="status-options">
                                    <label>
                                        <input type="radio" name="attendance[<?php echo $s['id_user']; ?>]" value="H" <?php echo ($status == 'H') ? 'checked' : ''; ?>>
                                        <span class="status-btn">HADIR</span>
                                    </label>
                                    <label>
                                        <input type="radio" name="attendance[<?php echo $s['id_user']; ?>]" value="TH" <?php echo ($status == 'TH') ? 'checked' : ''; ?>>
                                        <span class="status-btn">TIADA</span>
                                    </label>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <button type="submit" name="save_attendance" class="btn-save">
                <i class="fa-solid fa-cloud-arrow-up"></i> SIMPAN KEHADIRAN
            </button>
        </form>
    </div>
</div>

</body>
</html>