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

$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : 4;
$level = isset($_GET['level']) ? (int)$_GET['level'] : 1;

// Tetapan bilangan level maksimum setiap tahun
$maxLevelByYear = [
    4 => 5,
    5 => 3,
    6 => 3,
];
$maxLevel = $maxLevelByYear[$tahun] ?? 1;

// Baca fail soalan ikut tahun
$soalanFile = "soalan_tahun{$tahun}.php";
$currentQuestions = [];

if (file_exists($soalanFile)) {
    $questionSets = include $soalanFile;
    if (isset($questionSets[$level])) {
        $currentQuestions = $questionSets[$level];
    }
}

$totalSoalan = count($currentQuestions);
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Permainan Matematik (Tahun <?php echo $tahun; ?> - Level <?php echo $level; ?>)</title>
</head>
<body>

<h1>Permainan Matematik (Tahun <?php echo $tahun; ?> - Level <?php echo $level; ?>)</h1>
<p>Pelajar: <strong><?php echo htmlspecialchars($nama); ?></strong></p>
<hr>

<?php if ($totalSoalan === 0): ?>

    <p>Soalan untuk Tahun <?php echo $tahun; ?> Level <?php echo $level; ?> belum disediakan.</p>
    <p><a href="game-menu.php">Kembali ke Peta / Menu Permainan</a></p>

<?php else: ?>

    <!-- Bahagian Soalan -->
    <div id="quiz-section">
        <p><strong>Soalan <span id="qNumber">1</span> daripada <?php echo $totalSoalan; ?></strong></p>
        <p id="qText"></p>

        <div id="qImageWrapper" style="margin: 10px 0; display:none;">
            <img id="qImage" src="" alt="Rajah soalan" style="max-width:400px;">
        </div>

        <!-- Pilihan jawapan (MCQ) -->
        <div id="mcqOptions" style="margin: 10px 0;"></div>

        <!-- Jawapan teks / visual -->
        <div id="textAnswerWrapper" style="margin: 10px 0; display:none;">
            <label for="textAnswer">Jawapan anda:</label><br>
            <input type="text" id="textAnswer" style="width:300px;">
        </div>

        <button id="submitBtn">Hantar Jawapan</button>
        <p id="feedback" style="font-weight:bold; margin-top:10px;"></p>
    </div>

    <!-- Bahagian Keputusan Akhir -->
    <div id="result-section" style="display:none; margin-top:20px;">
        <h2>TAHNIAH!</h2>
        <p>Anda telah menamatkan tahap ini.</p>
        <p>Markah: <strong><span id="finalScore"></span> / <?php echo $totalSoalan; ?></strong></p>

        <!-- Mesej bila layak / tidak layak ke level seterusnya -->
        <p id="msgLayak" style="color:green; display:none;">
            Syabas! Anda mendapat markah penuh dan layak ke level seterusnya.
        </p>
        <p id="msgTakLayak" style="color:red; display:none;">
            Untuk ke level seterusnya, anda perlu mendapat 3/3.
            Sila cuba lagi level ini.
        </p>

        <!-- Lencana ringkas -->
        <div style="margin:20px 0;">
            <p>Lencana (Badge) yang diperoleh:</p>
            <div style="width:200px; height:200px; border:1px solid #555; display:flex; align-items:center; justify-content:center;">
                <div style="width:120px; height:120px; border-radius:50%; border:3px solid #000; display:flex; align-items:center; justify-content:center; font-size:30px;">
                    ★
                </div>
            </div>
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
        </p>
        <p>
            <a href="game-menu.php">← Kembali ke Peta / Menu Permainan</a>
        </p>
    </div>

    <script>
        const tahun       = <?php echo $tahun; ?>;
        const level       = <?php echo $level; ?>;
        const maxLevel    = <?php echo $maxLevel; ?>;
        const questions   = <?php echo json_encode($currentQuestions, JSON_UNESCAPED_UNICODE); ?>;
        const totalSoalan = questions.length;

        let currentIndex = 0;
        let score        = 0;

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

        function normalise(str) {
            return str.toString().trim().toLowerCase();
        }

        function showQuestion() {
            const q = questions[currentIndex];

            qNumberEl.textContent = currentIndex + 1;
            qTextEl.textContent   = q.text || '';
            feedbackEl.textContent = '';

            // Reset paparan
            qImageWrapperEl.style.display = 'none';
            qImageEl.src = '';
            mcqOptionsEl.innerHTML = '';
            textAnswerWrapper.style.display = 'none';
            textAnswerInput.value = '';

            // Gambar (visual)
            if (q.type === 'visual' && q.image) {
                qImageWrapperEl.style.display = 'block';
                qImageEl.src = q.image;
            }

            if (q.type === 'mcq') {
                renderMcq(q);
            } else {
                renderText(q);
            }
        }

        function renderMcq(q) {
            textAnswerWrapper.style.display = 'none';
            mcqOptionsEl.innerHTML = '';

            if (!q.options || !Array.isArray(q.options)) return;

            q.options.forEach(function(opt, index) {
                const id = 'opt_' + index;
                const label = document.createElement('label');
                const radio = document.createElement('input');

                radio.type  = 'radio';
                radio.name  = 'mcq';
                radio.value = index;
                radio.id    = id;

                label.htmlFor = id;
                label.textContent = opt;

                const div = document.createElement('div');
                div.appendChild(radio);
                div.appendChild(document.createTextNode(' '));
                div.appendChild(label);

                mcqOptionsEl.appendChild(div);
            });
        }

        function renderText(q) {
            mcqOptionsEl.innerHTML = '';
            textAnswerWrapper.style.display = 'block';
        }

        function checkAnswer() {
            const q = questions[currentIndex];
            let betul = false;

            if (q.type === 'mcq') {
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
                feedbackEl.textContent = 'Betul!';
            } else {
                feedbackEl.textContent = 'Jawapan kurang tepat.';
            }

            currentIndex++;
            if (currentIndex < totalSoalan) {
                setTimeout(function() {
                    showQuestion();
                }, 700);
            } else {
                setTimeout(function() {
                    tamatLevel();
                }, 700);
            }
        }

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

        function tamatLevel() {
            quizSection.style.display   = 'none';
            resultSection.style.display = 'block';

            finalScoreEl.textContent = score;

            // Hantar progress ke server (akan unlock level seterusnya jika markah penuh)
            updateProgressOnServer();

            if (score === totalSoalan) {
                msgLayakEl.style.display    = 'block';
                msgTakLayakEl.style.display = 'none';

                // Jika masih ada level seterusnya
                if (level < maxLevel) {
                    nextLevelWrapperEl.style.display = 'block';
                    const nextLevel = level + 1;
                    nextLevelLinkEl.href = 'game-play.php?tahun=' + tahun + '&level=' + nextLevel;
                } else {
                    nextLevelWrapperEl.style.display = 'none';
                    msgLayakEl.textContent = 'Syabas! Anda telah menamatkan semua level bagi Tahun ' + tahun + '.';
                }

                // Lulus → tak perlu ulang
                repeatWrapperEl.style.display = 'none';

            } else {
                // Tidak layak ke level seterusnya, level seterusnya kekal terkunci
                msgLayakEl.style.display    = 'none';
                msgTakLayakEl.style.display = 'block';

                nextLevelWrapperEl.style.display = 'none';

                // Papar butang "Ulang Permainan Level Ini"
                repeatWrapperEl.style.display = 'block';
                repeatLinkEl.href = 'game-play.php?tahun=' + tahun + '&level=' + level;
            }
        }

        submitBtn.addEventListener('click', checkAnswer);

        // Mulakan permainan
        showQuestion();
    </script>

<?php endif; ?>

</body>
</html>
