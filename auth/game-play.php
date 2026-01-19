<?php
// auth/game-play.php
require_once '../config.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Pastikan pelajar sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pelajar') {
    header('Location: ../login.php');
    exit;
}

// Ambil parameter Tahun dan Level dari URL (Contoh: game-play.php?tahun=4&level=1)
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : 4;
$level = isset($_GET['level']) ? (int)$_GET['level'] : 1;

// Load fail soalan yang betul
$file_soalan = "soalan_tahun$tahun.php";
if (!file_exists($file_soalan)) {
    die("Fail soalan tidak ditemui.");
}

$semua_soalan = include $file_soalan;
$soalan_level = $semua_soalan[$level] ?? null;

if (!$soalan_level) {
    die("Level ini belum tersedia.");
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Mathventure | Tahun <?php echo $tahun; ?> Level <?php echo $level; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Nunito', sans-serif; background: #f0f2f5; display: flex; justify-content: center; padding: 20px; }
        .game-container { background: white; width: 100%; max-width: 600px; padding: 30px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); text-align: center; }
        .question-box { margin-bottom: 20px; display: none; }
        .question-box.active { display: block; }
        .q-img { max-width: 100%; height: auto; border-radius: 10px; margin-bottom: 15px; border: 2px solid #eee; }
        .options-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 20px; }
        .btn-opt { padding: 15px; border: 2px solid #e0e0e0; border-radius: 10px; cursor: pointer; transition: 0.3s; background: white; }
        .btn-opt:hover { border-color: #3498db; background: #ebf5fb; }
        input[type="text"] { width: 100%; padding: 12px; border-radius: 10px; border: 2px solid #ddd; font-size: 16px; box-sizing: border-box; }
        .btn-next { margin-top: 20px; background: #2ecc71; color: white; padding: 10px 25px; border: none; border-radius: 50px; cursor: pointer; font-weight: bold; }
        #result-screen { display: none; }
        .xp-text { color: #f1c40f; font-weight: bold; font-size: 24px; }
    </style>
</head>
<body>

<div class="game-container">
    <div id="game-ui">
        <div class="header">
            <h3>Tahun <?php echo $tahun; ?>: Level <?php echo $level; ?></h3>
            <p>Soalan <span id="q-num">1</span> dari <?php echo count($soalan_level); ?></p>
        </div>

        <hr>

        <?php foreach ($soalan_level as $index => $q): ?>
            <div class="question-box" id="q-<?php echo $index; ?>">
                <p style="font-size: 18px; font-weight: 600;"><?php echo $q['text']; ?></p>
                
                <?php if ($q['type'] === 'visual'): ?>
                    <img src="<?php echo $q['image']; ?>" class="q-img">
                <?php endif; ?>

                <?php if ($q['type'] === 'mcq'): ?>
                    <div class="options-grid">
                        <?php foreach ($q['options'] as $i => $opt): ?>
                            <button class="btn-opt" onclick="checkAnswer(<?php echo $index; ?>, <?php echo $i; ?>)">
                                <?php echo $opt; ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <input type="text" id="input-<?php echo $index; ?>" placeholder="Taip jawapan anda di sini...">
                    <button class="btn-next" onclick="checkTextAnswer(<?php echo $index; ?>)">Hantar Jawapan</button>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div id="result-screen">
        <h2 id="status-msg">Syabas!</h2>
        <p>Anda mendapat markah:</p>
        <div style="font-size: 48px; font-weight: 800; margin: 10px 0;">
            <span id="final-score">0</span> / <?php echo count($soalan_level); ?>
        </div>
        <p class="xp-text">+ <span id="xp-gain">0</span> XP</p>
        <button class="btn-next" onclick="window.location.href='dashboard-student.php'">Kembali ke Dashboard</button>
    </div>
</div>

<script>
    let currentQ = 0;
    let totalScore = 0;
    const totalQuestions = <?php echo count($soalan_level); ?>;
    const questionsData = <?php echo json_encode($soalan_level); ?>;
    const tahun = <?php echo $tahun; ?>;
    const level = <?php echo $level; ?>;

    // Tunjukkan soalan pertama
    document.getElementById('q-0').classList.add('active');

    // NO 1: Penambahbaikan checkAnswer dengan maklum balas visual
    function checkAnswer(index, chosenIdx) {
        const options = document.querySelectorAll(`#q-${index} .btn-opt`);
        const correctIdx = questionsData[index].correct;

        // Kunci butang supaya tidak boleh klik banyak kali
        options.forEach(btn => btn.style.pointerEvents = 'none');

        if (chosenIdx === correctIdx) {
            options[chosenIdx].style.background = '#2ecc71'; // Hijau
            options[chosenIdx].style.color = 'white';
            totalScore++;
        } else {
            options[chosenIdx].style.background = '#e74c3c'; // Merah
            options[chosenIdx].style.color = 'white';
            options[correctIdx].style.background = '#2ecc71'; // Tunjuk jawapan betul
        }

        setTimeout(nextQuestion, 1000); // Tunggu 1 saat sebelum ke soalan seterusnya
    }

    // NO 2: Penambahbaikan checkTextAnswer
    function checkTextAnswer(index) {
        const inputEl = document.getElementById('input-' + index);
        let userAns = inputEl.value.trim().toLowerCase();
        let correctAnswers = questionsData[index].answer;
        let isCorrect = false;
        
        if (Array.isArray(correctAnswers)) {
            isCorrect = correctAnswers.some(a => a.toLowerCase() === userAns);
        } else {
            isCorrect = (userAns === correctAnswers.toLowerCase());
        }

        if (isCorrect) {
            inputEl.style.borderColor = '#2ecc71';
            inputEl.style.background = '#e8f8f5';
            totalScore++;
        } else {
            inputEl.style.borderColor = '#e74c3c';
            inputEl.style.background = '#fdedec';
        }

        setTimeout(nextQuestion, 1000);
    }

    function nextQuestion() {
        document.getElementById('q-' + currentQ).classList.remove('active');
        currentQ++;

        if (currentQ < totalQuestions) {
            document.getElementById('q-' + currentQ).classList.add('active');
            document.getElementById('q-num').innerText = currentQ + 1;
        } else {
            finishGame();
        }
    }

    // NO 5: Kemaskini finishGame dengan Lencana Dinamik & XP
    function finishGame() {
        document.getElementById('game-ui').style.display = 'none';
        const resultScreen = document.getElementById('result-screen');
        resultScreen.style.display = 'block';
        
        document.getElementById('final-score').innerText = totalScore;
        document.getElementById('xp-gain').innerText = totalScore * 10;

        const statusMsg = document.getElementById('status-msg');

        // Papar Lencana jika markah penuh
        if (totalScore === totalQuestions) {
            statusMsg.innerHTML = "TAHNIAH! ANDA TERBAIK! 🏆";
            
            // Cipta elemen imej badge secara dinamik
            const badgeImg = document.createElement('img');
            badgeImg.src = `badges/T${tahun}L${level}.png`;
            badgeImg.style.width = '150px';
            badgeImg.style.display = 'block';
            badgeImg.style.margin = '20px auto';
            badgeImg.alt = "Lencana Pencapaian";
            
            // Masukkan badge sebelum butang dashboard
            resultScreen.insertBefore(badgeImg, statusMsg.nextSibling);
        } else {
            statusMsg.innerText = "Bagus! Cuba lagi untuk skor penuh.";
        }

        // Hantar data ke server
        const formData = new FormData();
        formData.append('tahun', tahun);
        formData.append('level', level);
        formData.append('score', totalScore);
        formData.append('total', totalQuestions);
        formData.append('topic', 'Mathventure Tahun ' + tahun + ' Level ' + level);

        fetch('update-progress.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.text())
        .then(data => console.log("Progress Updated"))
        .catch(err => console.error("Update Error:", err));
    }
</script>

</body>
</html>