<?php
require_once '../config.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Pastikan hanya guru boleh akses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'guru') {
    header('Location: ../index.php');
    exit;
}

$id_guru = $_SESSION['user_id'];

// 1. Ambil data guru untuk tahu kelas mana dia ajar
$stmtG = $conn->prepare("SELECT * FROM teacher WHERE id_user = ?");
$stmtG->bind_param("i", $id_guru);
$stmtG->execute();
$teacher = $stmtG->get_result()->fetch_assoc();

if (!$teacher) {
    die("<div style='padding:50px; text-align:center; font-family:sans-serif;'>
            <h2>Akses Ditolak</h2>
            <p>ID Guru ($id_guru) tidak ditemui dalam jadual 'teacher'. Sila pastikan profil guru telah didaftarkan.</p>
            <a href='dashboard-teacher.php'>Kembali</a>
         </div>");
}

$myClass = $teacher['class'];

// 2. LOGIK AUTOMATIK: Pastikan semua user yang role='pelajar' ada dalam jadual 'student'
// Ini penting supaya cikgu tak perlu tambah murid manual.
$conn->query("INSERT IGNORE INTO student (id_user, firstname, lastname, class, level, current_xp, coins, lives)
              SELECT id_user, username, '', '$myClass', 1, 0, 0, 5 
              FROM users 
              WHERE role = 'pelajar'");

// 3. Ambil senarai pelajar yang kelasnya sepadan dengan guru
// Menggunakan TRIM untuk elakkan ralat "hidden space"
$sqlS = "SELECT s.*, u.username 
         FROM student s
         JOIN users u ON s.id_user = u.id_user
         WHERE TRIM(s.class) = TRIM(?) 
         ORDER BY s.firstname ASC";
         
$stmtS = $conn->prepare($sqlS);
$stmtS->bind_param("s", $myClass);
$stmtS->execute();
$studentsList = $stmtS->get_result();
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .teacher-layout { font-family: 'Nunito', sans-serif; background: #f4f7f6; min-height: 100vh; padding: 20px; }
        .main-content { max-width: 900px; margin: 0 auto; }
        .hero-text-main { color: #2c3e50; margin-bottom: 25px; }
        .table-card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .nice-table { width: 100%; border-collapse: collapse; }
        .nice-table th { text-align: left; padding: 15px; border-bottom: 2px solid #eee; color: #7f8c8d; }
        .nice-table td { padding: 15px; border-bottom: 1px solid #f9f9f9; }
        .btn-primary { transition: 0.3s; }
        .btn-primary:hover { background: #27ae60 !important; transform: translateY(-2px); }
        .radio-group { display: flex; gap: 20px; }
        .radio-label { cursor: pointer; display: flex; align-items: center; gap: 8px; font-weight: 600; }
    </style>
</head>
<body>

<div class="teacher-layout">
    <main class="main-content">
        <h2 class="hero-text-main">
            <i class="fa-solid fa-clipboard-user"></i> Tanda Kehadiran Kelas: <?php echo htmlspecialchars($myClass); ?>
        </h2>
        
        <section class="table-card">
            <form action="process-attendance.php" method="POST">
                <table class="nice-table">
                    <thead>
                        <tr>
                            <th>Nama Murid</th>
                            <th>Tanda Kehadiran (Hari Ini)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($studentsList->num_rows > 0): ?>
                            <?php while($s = $studentsList->fetch_assoc()): 
                                // Jika firstname kosong, guna username dari table users
                                $displayName = !empty($s['firstname']) ? $s['firstname'] . ' ' . $s['lastname'] : $s['username'];
                            ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($displayName); ?></strong></td>
                                <td>
                                    <div class="radio-group">
                                        <label class="radio-label">
                                            <input type="radio" name="attendance[<?php echo $s['id_user']; ?>]" value="H" checked> 
                                            <span style="color: #2ecc71;">Hadir</span>
                                        </label>
                                        <label class="radio-label">
                                            <input type="radio" name="attendance[<?php echo $s['id_user']; ?>]" value="TH"> 
                                            <span style="color: #e74c3c;">Tidak Hadir</span>
                                        </label>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="2" style="padding:50px; text-align:center;">
                                    <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="80" style="opacity: 0.3;">
                                    <p style="color:#999; margin-top:15px;">Tiada murid ditemui dalam kelas <strong><?php echo htmlspecialchars($myClass); ?></strong>.</p>
                                    <small>Pastikan murid telah mendaftar dan memilih kelas yang betul.</small>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                
                <?php if ($studentsList->num_rows > 0): ?>
                    <button type="submit" class="btn-primary" style="margin-top:30px; background:#2ecc71; color:white; border:none; padding:15px 35px; border-radius:10px; cursor:pointer; font-weight:800; font-size: 16px; box-shadow: 0 4px 15px rgba(46, 204, 113, 0.3);">
                        <i class="fa-solid fa-check-double"></i> SIMPAN KEHADIRAN
                    </button>
                <?php endif; ?>
            </form>
        </section>
    </main>
</div>

</body>
</html>