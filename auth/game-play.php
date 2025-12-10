<?php
// auth/game-play.php

require_once '../config.php';

// Pastikan user sudah login & role = pelajar
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'pelajar') {
    $_SESSION['error'] = 'Sila log masuk sebagai pelajar untuk akses halaman ini.';
    header('Location: index.php');
    exit;
}

$userId = $_SESSION['user_id'];
$nama   = $_SESSION['username'] ?? 'Pelajar';

// Tahun & Level dari URL (default Tahun 4 Level 1)
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : 4;
$level = isset($_GET['level']) ? (int)$_GET['level'] : 1;

// Tetapan bilangan level maksimum setiap tahun
$maxLevelByYear = [
    4 => 5,
    5 => 5,
    6 => 5,
];
$maxLevel = $maxLevelByYear[$tahun] ?? 1;

// ===============================
// Baca fail soalan ikut tahun
// ===============================
$soalanFile = __DIR__ . "/soalan_tahun{$tahun}.php";
$currentQuestions = [];

if (file_exists($soalanFile)) {
    $questionSets = include $soalanFile;

    // Pastikan fail soalan return array dan level wujud
    if (is_array($questionSets) && isset($questionSets[$level])) {
        $currentQuestions = $questionSets[$level];
    }
}

$totalSoalan = count($currentQuestions);

// Sediakan laluan gambar badge ikut Tahun & Level
// Contoh: auth/badges/T4L1.png
$badgePath = "badges/T{$tahun}L{$level}.png";

// Body class untuk ubah background ikut tahun (tahun-4, tahun-5, tahun-6)
$bodyClass = 'tahun-' . $tahun;
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Mathventure - Tahun <?php echo $tahun; ?> Level <?php echo $level; ?></title>

    <!-- CSS layout umum + gaya khas game-play -->
    <link rel="stylesheet" href="../asset/css/student-layout.css">
    <link rel="stylesheet" href="../asset/css/game-play.css">
</head>
<body class="<?php echo htmlspecialchars($bodyClass); ?>">

<?php if ($totalSoalan === 0): ?>

    <div id="result-section">
        <h2>Ops!</h2>
        <p>Soalan untuk Tahun <?php echo $tahun; ?> Level <?php echo $level; ?> belum disediakan.</p>
        <p>
            <a href="game-menu.php">← Kembali ke Peta / Menu Permainan</a>
            <a href="dashboard-student.php">← Kembali ke Dashboard Pelajar</a>
        </p>
    </div>

<?php else: ?>

    <!-- Bahagian Soalan -->
    <div id="quiz-section">
        <h1>Tahun <?php echo $tahun; ?> • Level <?php echo $level; ?></h1>
        <p>Pelajar: <strong><?php echo htmlspecialchars($nama); ?></strong></p>
        <p><strong>Soalan <span id="qNumber">1</span> daripada <?php echo $totalSoalan; ?></strong></p>

        <p id="qText"></p>

        <div id="qImageWrapper">
            <img id="qImage" src="" alt="Rajah soalan">
        </div>

        <!-- Pilihan jawapan (MCQ) -->
        <div id="mcqOptions"></div>

        <!-- Jawapan teks / visual -->
        <div id="textAnswerWrapper">
            <label for="textAnswer">Jawapan anda:</label><br>
            <input type="text" id="textAnswer">
        </div>

        <button id="submitBtn">Hantar Jawapan</button>
        <p id="feedback"></p>
    </div>

    <!-- Bahagian Keputusan Akhir -->
    <div id="result-section" style="display:none;">
        <h2>TAHNIAH!</h2>
        <p>Anda telah menamatkan tahap ini.</p>
        <p>Markah: <strong><span id="finalScore"></span> / <?php echo $totalSoalan; ?></strong></p>

        <!-- Mesej bila layak / tidak layak ke level seterusnya -->
        <p id="msgLayak" style="display:none;">
            Syabas! Anda mendapat markah penuh dan layak ke level seterusnya.
        </p>
        <p id="msgTakLayak" style="display:none;">
            Untuk ke level seterusnya, anda perlu mendapat markah penuh. Sila cuba lagi level ini.
        </p>

        <!-- Lencana (Badge) bergambar – hanya dipaparkan bila markah penuh -->
        <div id="badgeWrapper" class="badge-wrapper">
            <img id="badgeImage"
                 src="<?php echo htmlspecialchars($badgePath); ?>"
                 alt="Badge pencapaian">
        </div>

        <!-- BUTANG ULANG PERMAINAN (dipaparkan bila markah tidak penuh) -->
        <p id="repeatWrapper" style="display:none;">
            <a id="repeatLink" href="#">↻ Ulang Permainan Level Ini</a>
        </p>

        <!-- Butang ke Level Seterusnya (hanya jika markah penuh & level masih ada) -->
        <p id="nextLevelWrapper" style="display:none;">
            <a id="nextLevelLink" href="#">Pergi ke Level Seterusnya →</a>
        </p>

        <!-- Pautan balik -->
        <p>
            <a href="dashboard-student.php">← Kembali ke Dashboard Pelajar</a>
            <a href="game-menu.php">← Kembali ke Peta / Menu Permainan</a>
        </p>
    </div>

    <script>
        // Data daripada PHP ke JavaScript
        const tahun       = <?php echo $tahun; ?>;
        const level       = <?php echo $level; ?>;
        const maxLevel    = <?php echo $maxLevel; ?>;
        const questions   = <?php echo json_encode($currentQuestions, JSON_UNESCAPED_UNICODE); ?>;
        const totalSoalan = questions.length;

        let currentIndex = 0;
        let score        = 0;

        // Elemen DOM
        const qNumberEl          = document.getElementById('qNumber');
        const qTextEl            = document.getElementById('qText');
        const qImageWrapperEl    = document.getElementById('qImageWrapper');
        const qImageEl           = document.getElementById('qImage');
        const mcqOptionsEl       = document.getElementById('mcqOptions');
        const textAnswerWrapper  = document.getElementById('textAnswerWrapper');
        const textAnswerInput    = document.getElementById('textAnswer');
        const feedbackEl         = document.getElementById('feedback');
        const submitBtn          = document.getElementById('submitBtn');

        const quizSection        = document.getElementById('quiz-section');
        const resultSection      = document.getElementById('result-section');
        const finalScoreEl       = document.getElementById('finalScore');
        const msgLayakEl         = document.getElementById('msgLayak');
        const msgTakLayakEl      = document.getElementById('msgTakLayak');
        const nextLevelWrapperEl = document.getElementById('nextLevelWrapper');
        const nextLevelLinkEl    = document.getElementById('nextLevelLink');
        const repeatWrapperEl    = document.getElementById('repeatWrapper');
        const repeatLinkEl       = document.getElementById('repeatLink');
        const badgeWrapperEl     = document.getElementById('badgeWrapper');
        const badgeImgEl         = document.getElementById('badgeImage');

        function normalise(str) {
            return str.toString().trim().toLowerCase();
        }

        // Papar soalan semasa
        function showQuestion() {
            const q = questions[currentIndex];

            qNumberEl.textContent = currentIndex + 1;
            qTextEl.textContent   = q.text || '';
            feedbackEl.textContent = '';
            feedbackEl.classList.remove('salah');

            // Reset paparan
            qImageWrapperEl.style.display = 'none';
            qImageEl.src = '';
            mcqOptionsEl.innerHTML = '';
            textAnswerWrapper.style.display = 'none';
            textAnswerInput.value = '';

            // Gambar (visual)
            if ((q.type === 'visual' || q.type === 'mcq-visual') && q.image) {
                qImageWrapperEl.style.display = 'flex';
                qImageEl.src = q.image;
            }

            if (q.type === 'mcq' || q.type === 'mcq-visual') {
                renderMcq(q);
            } else {
                renderText(q);
            }
        }

        // Papar pilihan MCQ
        function renderMcq(q) {
            textAnswerWrapper.style.display = 'none';
            mcqOptionsEl.innerHTML = '';

            if (!q.options || !Array.isArray(q.options)) return;

            q.options.forEach(function (opt, index) {
                const id = 'opt_' + index;

                // wrapper kad
                const wrapper = document.createElement('div');
                wrapper.className = 'option-card';

                // radio (disorok dalam CSS)
                const radio = document.createElement('input');
                radio.type  = 'radio';
                radio.name  = 'mcq';
                radio.value = index;
                radio.id    = id;

                // label sebagai kad klik
                const label = document.createElement('label');
                label.className = 'option-label';
                label.htmlFor   = id;

                // huruf A/B/C/D
                const letterSpan = document.createElement('span');
                letterSpan.className = 'option-letter';
                letterSpan.textContent = String.fromCharCode(65 + index);

                // teks jawapan
                const textSpan = document.createElement('span');
                textSpan.className = 'option-text';
                textSpan.textContent = opt;

                label.appendChild(letterSpan);
                label.appendChild(textSpan);

                wrapper.appendChild(radio);
                wrapper.appendChild(label);

                mcqOptionsEl.appendChild(wrapper);
            });
        }

        // Papar input teks
        function renderText(q) {
            mcqOptionsEl.innerHTML = '';
            textAnswerWrapper.style.display = 'block';
        }

        // Semak jawapan
        function checkAnswer() {
            const q = questions[currentIndex];
            let betul = false;

            if (q.type === 'mcq' || q.type === 'mcq-visual') {
                const selected = document.querySelector('input[name="mcq"]:checked');
                if (!selected) {
                    alert('Sila pilih satu jawapan.');
                    return;
                }
                const jawapanIndex = parseInt(selected.value, 10);
                betul = (jawapanIndex === q.correct);

            } else {
                let userAns = textAnswerInput.value;
                if (!userAns.trim()) {
                    alert('Sila tulis jawapan anda.');
                    return;
                }

                let possibleAnswers = q.answer;
                if (!Array.isArray(possibleAnswers)) {
                    possibleAnswers = [possibleAnswers];
                }

                const userNorm = normalise(userAns);
                betul = possibleAnswers.some(function(ans) {
                    return normalise(ans) === userNorm;
                });
            }

            if (betul) {
                score++;
                feedbackEl.classList.remove('salah');
                feedbackEl.textContent = 'Betul! 🎉';
            } else {
                feedbackEl.classList.add('salah');
                feedbackEl.textContent = 'Jawapan kurang tepat. Cuba lagi!';
            }

            currentIndex++;
            if (currentIndex < totalSoalan) {
                setTimeout(showQuestion, 800);
            } else {
                setTimeout(tamatLevel, 800);
            }
        }

        // Hantar progress ke server (unlock level seterusnya dsb.)
        function updateProgressOnServer() {
            const formData = new FormData();
            formData.append('tahun', tahun);
            formData.append('level', level);
            formData.append('score', score);
            formData.append('total', totalSoalan);

            fetch('update_progress.php', {
                method: 'POST',
                body: formData
            }).catch(function (err) {
                console.error('Gagal kemas kini progress:', err);
            });
        }

        // Tamat level
        function tamatLevel() {
            quizSection.style.display   = 'none';
            resultSection.style.display = 'block';

            finalScoreEl.textContent = score;

            // Hantar progress ke server
            updateProgressOnServer();

            if (score === totalSoalan) {
                // LULUS PENUH
                msgLayakEl.style.display    = 'block';
                msgTakLayakEl.style.display = 'none';

                // Tunjuk badge di tengah
                badgeWrapperEl.style.display = 'block';
                badgeImgEl.style.display     = 'block';

                // Ada level seterusnya?
                if (level < maxLevel) {
                    nextLevelWrapperEl.style.display = 'block';
                    const nextLevel = level + 1;
                    nextLevelLinkEl.href = 'game-play.php?tahun=' + tahun + '&level=' + nextLevel;
                } else {
                    nextLevelWrapperEl.style.display = 'none';
                    msgLayakEl.textContent = 'Syabas! Anda telah menamatkan semua level bagi Tahun ' + tahun + '.';
                }

                // Tak perlu ulang
                repeatWrapperEl.style.display = 'none';

            } else {
                // TAK LAYAK KE LEVEL SETERUSNYA
                msgLayakEl.style.display    = 'none';
                msgTakLayakEl.style.display = 'block';

                nextLevelWrapperEl.style.display = 'none';

                // Sorok badge dan kotaknya terus
                badgeWrapperEl.style.display = 'none';
                badgeImgEl.style.display     = 'none';

                // Papar butang "Ulang"
                repeatWrapperEl.style.display = 'block';
                repeatLinkEl.href = 'game-play.php?tahun=' + tahun + '&level=' + level;
            }
        }

        submitBtn.addEventListener('click', checkAnswer);

        // Mula permainan
        showQuestion();
    </script>

<?php endif; ?>

</body>
</html>
