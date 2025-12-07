<?php
require_once '../config.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pelajar') {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$nama = $_SESSION['username'] ?? 'Pelajar';

// --- DATA PELAJAR ---
$stmt = $conn->prepare("SELECT * FROM student WHERE id_user = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

if (!$student) {
    $student = ['firstname'=>'Pelajar', 'level'=>1, 'current_xp'=>0, 'max_xp'=>100, 'avatar'=>'dinasour2.png'];
}
$xp_percentage = ($student['max_xp'] > 0) ? ($student['current_xp'] / $student['max_xp']) * 100 : 0;

// --- DATA NOTA ---
$topics = [
    [
        'title' => 'Nombor Bulat',
        'desc' => 'Kenali nilai tempat, cerakin digit dan teknik pembundaran.',
        'icon' => '1️⃣',
        'color' => 'green',
        'content' => '<div class="note-section"><h3>🏠 Nilai Tempat & Nilai Digit</h3><p>Setiap digit mempunyai "rumah" dan nilai tersendiri.</p><div class="table-box"><table><tr><th>Digit</th><td>8</td><td>3</td><td>6</td><td>1</td><td>2</td></tr><tr><th>Tempat</th><td>P.Ribu</td><td>Ribu</td><td>Ratus</td><td>Puluh</td><td>Sa</td></tr><tr><th>Nilai</th><td>80,000</td><td>3,000</td><td>600</td><td>10</td><td>2</td></tr></table></div><h3>🔧 Cerakin Nombor</h3><div class="math-card"><strong>Ikut Nilai Digit:</strong><br>80 000 + 3 000 + 600 + 10 + 2<hr><strong>Ikut Nilai Tempat:</strong><br>8 puluh ribu + 3 ribu + 6 ratus + 1 puluh + 2 sa</div><h3>🎯 Bundarkan Nombor</h3><p>Lihat digit di sebelah <strong>KANAN</strong>:</p><ul class="dino-list"><li><strong>0-4 (Miskin):</strong> Kekal digit, kanan jadi 0.</li><li><strong>5-9 (Kaya):</strong> Tambah 1, kanan jadi 0.</li></ul></div>'
    ],
    [
        'title' => 'Pecahan',
        'desc' => 'Fahami pecahan wajar, setara dan selesaikan operasi asas.',
        'icon' => '🍕',
        'color' => 'orange',
        'content' => '<div class="note-section"><h3>🍰 Jenis Pecahan</h3><ul class="dino-list"><li><strong>Pecahan Wajar:</strong> Kepala (atas) kecil dari badan (bawah).</li><li><strong>Pecahan Tak Wajar:</strong> Kepala besar dari badan.</li></ul><h3>➕ Tambah & Tolak</h3><p class="alert-box">⚠️ Pastikan <strong>PENYEBUT (BAWAH)</strong> sama dahulu!</p><div class="calculation-box">  1     1      (Samakan penyebut jadi 10)\n--- + ---  \n  2     5     \n\n(1×5) + (1×2)     5 + 2      7\n-------------  =  -----  =  ----\n     10            10        10</div></div>'
    ],
    [
        'title' => 'Perpuluhan',
        'desc' => 'Kuasai titik perpuluhan dalam tambah, tolak & darab.',
        'icon' => '💠',
        'color' => 'blue',
        'content' => '<div class="note-section"><h3>📍 Operasi Tambah (Bentuk Lazim)</h3><p>Susun supaya <strong>TITIK PERPULUHAN SEBARIS</strong>.</p><div class="calculation-box vertical">  1 2 . 3 6\n+ 0 0 . 7 8\n-----------\n  1 3 . 1 4\n-----------</div><h3>✖️ Gerakan Titik</h3><p>Bila darab dengan 10, 100, 1000, gerak titik ke <strong>KANAN</strong>.</p><div class="math-card">5.123 × 10 = <strong>51.23</strong> (Gerak 1 kali)<br>5.123 × 100 = <strong>512.3</strong> (Gerak 2 kali)</div></div>'
    ],
    [
        'title' => 'Wang (Duit)',
        'desc' => 'Pengurusan wang, sebutan nilai dan pembundaran.',
        'icon' => '💰',
        'color' => 'purple',
        'content' => '<div class="note-section"><h3>🏷️ Bundarkan Kepada Ringgit</h3><p>Lihat nilai sen:</p><div class="table-box"><table><tr><th>Nilai Sen</th><th>Tindakan</th><th>Contoh</th></tr><tr><td>< 50 sen</td><td>Kekal & Buang sen</td><td>RM12.30 ➝ RM12</td></tr><tr><td>≥ 50 sen</td><td>Tambah RM1</td><td>RM12.70 ➝ RM13</td></tr></table></div></div>'
    ],
    [
        'title' => 'Masa & Waktu',
        'desc' => 'Sistem 12 jam, 24 jam dan hubungan unit masa.',
        'icon' => '⏰',
        'color' => 'red',
        'content' => '<div class="note-section"><h3>🔄 Hubungan Unit Masa</h3><ul class="dino-list"><li>1 minit = 60 saat</li><li>1 jam = 60 minit</li><li>1 hari = 24 jam</li><li>1 dekad = 10 tahun</li></ul><h3>➕ Operasi Masa</h3><div class="math-card"><strong>Soalan:</strong> 85 minit + 50 minit<br>= 135 minit<br><br><em>Tukar ke Jam:</em><br>135 ÷ 60 = <strong>2 jam 15 minit</strong></div></div>'
    ]
];
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Modul Belajar | Mathventure</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="../asset/css/dashboard-student.css">
    <link rel="stylesheet" href="../asset/css/modul-belajar.css?v=<?php echo time(); ?>">
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
            <a href="dashboard-student.php" class="nav-item"><span class="icon">🏠</span> Dashboard</a>
            <a href="game-menu.php" class="nav-item"><span class="icon">🗺️</span> Peta Dunia</a>
            <a href="modul-belajar.php" class="nav-item active"><span class="icon">📚</span> Modul</a>
            <a href="#" class="nav-item"><span class="icon">🏆</span> Pencapaian</a>
            <div class="divider"></div>
            <a href="logout.php" class="nav-item logout"><span class="icon">🚪</span> Log Keluar</a>
        </nav>

        <div class="sidebar-footer">
            <div class="player-card">
                <div class="avatar-frame">
                    <img src="../asset/images/<?php echo htmlspecialchars($student['avatar']); ?>" alt="Avatar">
                </div>
                <div class="player-info">
                    <div class="lvl-badge">LVL <?php echo $student['level']; ?></div>
                    <div class="xp-track">
                        <div class="xp-fill" style="width: <?php echo $xp_percentage; ?>%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <main class="main-content" id="mainContent">
        <button class="floating-menu-btn" id="openSidebarBtn">☰</button>

        <header class="top-bar">
            <div class="welcome-badge">
                <span class="icon">📖</span> Nota Pintar
            </div>
            <div class="game-clock">Jom Ulangkaji! 🦖</div>
        </header>

        <div class="page-title-area">
            <h1>Matematik Tahun 4</h1>
            <p>Pilih topik di bawah untuk mula membaca nota.</p>
        </div>

        <section class="menu-grid">
            <?php foreach($topics as $topic): ?>
                <div class="module-card <?php echo $topic['color']; ?>" 
                     onclick="openNoteModal(<?php echo htmlspecialchars(json_encode($topic)); ?>)">
                    
                    <div class="glow-effect"></div>
                    <div class="card-icon"><?php echo $topic['icon']; ?></div>
                    <div class="card-content">
                        <h3><?php echo $topic['title']; ?></h3>
                        <p><?php echo $topic['desc']; ?></p>
                    </div>
                    
                    <button class="btn-gempak">
                        <span class="btn-text">BACA NOTA</span>
                    </button>
                </div>
            <?php endforeach; ?>
        </section>

    </main>

    <div id="noteModal" class="modal-overlay">
        <div class="modal-box note-book-style">
            <div class="modal-header <?php echo $topic['color'] ?? 'blue'; ?>" id="modalHeaderColor">
                <h2 id="modalTitle">Tajuk</h2>
                <button class="close-modal" onclick="closeNoteModal()">✖</button>
            </div>
            <div class="modal-body-scroll" id="modalContent"></div>
            <div class="modal-footer-btn">
                <button onclick="closeNoteModal()" class="btn-gempak small">Faham & Tutup</button>
            </div>
        </div>
    </div>

<script>
    // Sidebar Logic
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

    // Modal Logic
    const modal = document.getElementById('noteModal');
    const mTitle = document.getElementById('modalTitle');
    const mContent = document.getElementById('modalContent');
    const mHeader = document.getElementById('modalHeaderColor');

    function openNoteModal(data) {
        mTitle.innerText = data.title;
        mContent.innerHTML = data.content;
        mHeader.className = 'modal-header ' + data.color;
        modal.classList.add('active');
    }

    function closeNoteModal() {
        modal.classList.remove('active');
    }
    
    modal.addEventListener('click', (e) => {
        if(e.target === modal) closeNoteModal();
    });
</script>

</body>
</html>