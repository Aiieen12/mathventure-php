<?php
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pelajar') {
    header('Location: index.php');
    exit;
}

$userId = (int)$_SESSION['user_id'];
$successMsg = false;

// 1. PROSES SIMPAN DATA (Sama seperti sebelum ini)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $fname = $_POST['firstname'];
    $lname = $_POST['lastname'];
    $year  = (int)$_POST['year_level'];
    $kelas = trim($_POST['nama_kelas']);

    $update = $conn->prepare("UPDATE student SET firstname = ?, lastname = ?, year_level = ?, nama_kelas = ? WHERE id_user = ?");
    $update->bind_param("ssisi", $fname, $lname, $year, $kelas, $userId);
    
    if ($update->execute()) {
        $successMsg = true;
    }
    $update->close();
}

// 2. AMBIL DATA UNTUK PAPARAN
$stmt = $conn->prepare("SELECT * FROM student WHERE id_user = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();

$username = $_SESSION['username'] ?? 'pelajar';
$namaPenuh = $data['firstname'] . ' ' . $data['lastname'];
$xpPercent = ($data['current_xp'] / ($data['max_xp'] ?: 100)) * 100;
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Profil Pelajar | Mathventure</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="asset/css/student-layout.css">
    <link rel="stylesheet" href="asset/css/profile.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<div class="game-bg" style="background: url('asset/images/bgdino.png') no-repeat center center fixed; background-size: cover; position: fixed; width: 100%; height: 100%; z-index: -1;"></div>

<aside class="sidebar">
    <div class="sidebar-header">
        <div class="logo-text">Mathventure</div>
        <small>Student Mode</small>
    </div>
    <nav class="side-nav">
        <a href="dashboard-student.php" class="nav-item">🏠 <span>Dashboard</span></a>
        <a href="game-menu.php" class="nav-item">🗺️ <span>Peta Permainan</span></a>
        <a href="nota.php" class="nav-item">📚 <span>Nota Matematik</span></a>
        <a href="badges.php" class="nav-item">🏅 <span>Pencapaian</span></a>
        <a href="profile.php" class="nav-item active">👤 <span>Profil</span></a>
        <a href="logout.php" class="nav-item logout">🚪 <span>Log Keluar</span></a>
    </nav>
    <div class="sidebar-footer">
        <div class="player-card">
            <img src="asset/images/<?php echo $data['avatar'] ?: 'avatar.png'; ?>" style="width:40px; height:40px; border-radius:50%; object-fit:cover;">
            <div class="player-info">
                <strong><?php echo htmlspecialchars($data['firstname']); ?></strong>
                <div class="lvl-badge">Lvl <?php echo $data['level']; ?></div>
            </div>
        </div>
    </div>
</aside>

<main class="main-content">
    <header class="profile-hero">
        <div class="profile-hero-text">
            <h1>Profil <?php echo htmlspecialchars($data['firstname']); ?> ✨</h1>
            <p>Kekal hebat dan teruskan pengembaraan matematik anda untuk kumpul lebih banyak lencana!</p>
        </div>
        <div class="profile-hero-badge">
            <span class="mini-label">STATUS AKAUN</span>
            <strong>PENGGUNA AKTIF</strong>
            <small>ID: #<?php echo str_pad($data['id_user'], 4, '0', STR_PAD_LEFT); ?></small>
        </div>
    </header>

    <?php if ($successMsg): ?>
        <div style="background:#55E6C1; color:#006266; padding:15px; border-radius:15px; margin-bottom:20px; font-weight:bold; text-align:center; border: 2px solid #fff;">
            🎉 Maklumat anda telah berjaya dikemaskini!
        </div>
    <?php endif; ?>

    <div class="profile-layout">
        <article class="profile-card">
            <div class="profile-avatar">
                <img src="asset/images/<?php echo $data['avatar'] ?: 'avatar.png'; ?>" alt="Avatar">
            </div>
            <h2><?php echo htmlspecialchars($namaPenuh); ?></h2>
            <div class="profile-username">@<?php echo htmlspecialchars($username); ?></div>

            <div class="profile-meta">
                <div class="meta-item">
                    <span class="meta-label">KELAS</span>
                    <span class="meta-value">Thn <?php echo $data['year_level']; ?> <?php echo htmlspecialchars($data['nama_kelas']); ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">NYAWA</span>
                    <span class="meta-value"><?php echo $data['lives']; ?> ❤️</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">COINS</span>
                    <span class="meta-value"><?php echo $data['coins']; ?> ⭐</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">LEVEL</span>
                    <span class="meta-value"><?php echo $data['level']; ?></span>
                </div>
            </div>

            <div class="profile-progress">
                <span class="progress-title">Kemajuan XP</span>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?php echo $xpPercent; ?>%;"></div>
                </div>
                <small><?php echo $data['current_xp']; ?> / <?php echo $data['max_xp']; ?> XP</small>
            </div>
        </article>

        <article class="profile-edit">
            <h2>Kemaskini Maklumat</h2>
            <p class="edit-desc">Sila pastikan nama dan kelas adalah betul supaya cikgu boleh semak markah anda.</p>
            
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label>Nama Depan</label>
                        <input type="text" name="firstname" value="<?php echo htmlspecialchars($data['firstname']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Belakang</label>
                        <input type="text" name="lastname" value="<?php echo htmlspecialchars($data['lastname']); ?>" required>
                    </div>
                </div>

                <hr class="form-divider">

                <div class="form-row">
                    <div class="form-group">
                        <label>Tahun</label>
                        <select name="year_level" style="padding: 9px 10px; border-radius: 12px; border: 2px solid #dfe6e9; font-family: 'Varela Round';">
                            <option value="4" <?php echo ($data['year_level'] == 4) ? 'selected' : ''; ?>>Tahun 4</option>
                            <option value="5" <?php echo ($data['year_level'] == 5) ? 'selected' : ''; ?>>Tahun 5</option>
                            <option value="6" <?php echo ($data['year_level'] == 6) ? 'selected' : ''; ?>>Tahun 6</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Nama Kelas (Cth: Alfa, Beta)</label>
                        <input type="text" name="nama_kelas" value="<?php echo htmlspecialchars($data['nama_kelas']); ?>" placeholder="Cth: Alfa" required>
                    </div>
                </div>

                <button type="submit" name="update_profile" class="save-btn">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                </button>
            </form>
        </article>
    </div>
</main>
</body>
</html>