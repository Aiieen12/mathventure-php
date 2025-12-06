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
            <a href="#" class="nav-item"><span class="icon">📚</span> Modul</a>
            <a href="#" class="nav-item"><span class="icon">🏆</span> Pencapaian</a>
            <div class="divider"></div>
            <a href="logout.php" class="nav-item logout"><span class="icon">🚪</span> Log Keluar</a>
        </nav>

        <div class="sidebar-footer">
            <div class="player-card">
                <div class="avatar-frame">
                    <img src="../asset/images/<?php echo htmlspecialchars($student['avatar'] ?? 'dinasour2.png'); ?>" alt="Avatar">
                </div>
                <div class="player-info">
                    <div class="lvl-badge">LVL <?php echo $student['level']; ?></div>
                    <div class="xp-track" title="XP: <?php echo $student['current_xp']; ?>">
                        <div class="xp-fill" style="width: <?php echo $xp_percentage; ?>%;"></div>
                    </div>
                </div>
                <button class="edit-profile-btn" onclick="openModal()">✏️</button>
            </div>
        </div>
    </aside>

    <main class="main-content" id="mainContent">
        <button class="floating-menu-btn" id="openSidebarBtn">☰</button>
        <div class="particles" id="particles"></div>

        <header class="top-bar">
            <div class="welcome-badge">
                Hai, <span class="highlight"><?php echo htmlspecialchars($student['firstname']); ?></span>!
            </div>
            <div class="game-clock"><span id="timeDisplay">00:00</span> 🕒</div>
        </header>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="notify success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <section class="hero-section">
            <div class="hero-card">
                <div class="hero-text">
                    <h1>Misi Harian</h1>
                    <p>"<?php echo htmlspecialchars($student['bio'] ?? 'Jom belajar!'); ?>"</p>
                    <button class="cta-btn">Mula Sekarang!</button>
                </div>
                <div class="dino-mascot">
                    <img src="../asset/images/<?php echo htmlspecialchars($student['avatar'] ?? 'dinasour2.png'); ?>" id="dinoHero">
                </div>
            </div>

            <div class="hud-stats">
                <div class="stat-box red">
                    <span class="stat-icon">❤️</span>
                    <div class="stat-info"><small>Nyawa</small><strong><?php echo $student['lives']; ?>/5</strong></div>
                </div>
                <div class="stat-box yellow">
                    <span class="stat-icon">⭐</span>
                    <div class="stat-info"><small>Mata</small><strong><?php echo number_format($student['current_xp']); ?></strong></div>
                </div>
                <div class="stat-box blue">
                    <span class="stat-icon">💎</span>
                    <div class="stat-info"><small>Permata</small><strong><?php echo number_format($student['coins']); ?></strong></div>
                </div>
            </div>
        </section>

        <section class="menu-grid">
            <a href="game-menu.php" class="game-card green">
                <div class="card-icon">🗺️</div><h3>Peta Permainan</h3><p>Jelajah hutan matematik.</p>
            </a>
            <a href="#" class="game-card orange soon">
                <div class="card-icon">📖</div><h3>Modul Belajar</h3><p>Ulang kaji topik.</p><span class="locked-badge">🔒 SOON</span>
            </a>
            <a href="#" class="game-card purple">
                <div class="card-icon">🏆</div><h3>Leaderboard</h3><p>Lihat ranking kawan.</p>
            </a>
        </section>
    </main>

    <div id="profileModal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h2>Ubah Suai Watak</h2>
                <button class="close-modal" onclick="closeModal()">✖</button>
            </div>
            <form action="process-profile.php" method="POST">
                
                <div class="avatar-selection-area">
                    <p class="label">Pilih Avatar:</p>
                    <div class="avatar-grid">
                        <?php 
                        // Nota: Pastikan gambar2 ini wujud dalam folder ../asset/images/
                        // Jika tiada gambar lain, ia akan tunjuk alt text atau gambar broken.
                        $avatars = ['dinasour2.png', 'dino-red.png', 'dino-blue.png', 'dino-purple.png'];
                        foreach($avatars as $av): 
                            $isSelected = ($student['avatar'] == $av) ? 'checked' : '';
                        ?>
                        <label class="avatar-option">
                            <input type="radio" name="avatar_selection" value="<?php echo $av; ?>" <?php echo $isSelected; ?>>
                            <img src="../asset/images/<?php echo $av; ?>" onerror="this.src='../asset/images/dinasour2.png'"> </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label>Nama Pertama</label>
                    <input type="text" name="firstname" value="<?php echo htmlspecialchars($student['firstname']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Nama Akhir</label>
                    <input type="text" name="lastname" value="<?php echo htmlspecialchars($student['lastname']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Bio / Kata-kata Semangat</label>
                    <textarea name="bio" rows="2"><?php echo htmlspecialchars($student['bio']); ?></textarea>
                </div>

                <button type="submit" class="save-btn">Simpan Perubahan</button>
            </form>
        </div>
    </div>

<script>
    // Sidebar logic
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const closeBtn = document.getElementById('closeSidebarBtn');
    const openBtn = document.getElementById('openSidebarBtn');

    closeBtn.addEventListener('click', () => {
        sidebar.classList.add('collapsed');
        mainContent.classList.add('expanded');
        setTimeout(() => openBtn.classList.add('visible'), 300);
    });
    openBtn.addEventListener('click', () => {
        sidebar.classList.remove('collapsed');
        mainContent.classList.remove('expanded');
        openBtn.classList.remove('visible');
    });

    // Time logic
    setInterval(() => {
        document.getElementById('timeDisplay').innerText = 
            new Date().toLocaleTimeString('en-US', {hour:'2-digit', minute:'2-digit', hour12:true});
    }, 1000);

    // Parallax logic
    document.addEventListener('mousemove', (e) => {
        const dino = document.getElementById('dinoHero');
        if(dino) {
            dino.style.transform = `translate(${(window.innerWidth - e.pageX*2)/90}px, ${(window.innerHeight - e.pageY*2)/90}px)`;
        }
    });

    // MODAL LOGIC
    function openModal() {
        document.getElementById('profileModal').classList.add('active');
    }
    function closeModal() {
        document.getElementById('profileModal').classList.remove('active');
    }
    // Tutup jika klik luar kotak
    document.getElementById('profileModal').addEventListener('click', (e) => {
        if(e.target === document.getElementById('profileModal')) closeModal();
    });
</script>

</body>
</html>