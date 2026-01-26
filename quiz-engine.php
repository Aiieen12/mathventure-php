<?php
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$tahun = (int)$_GET['tahun'];
$level = (int)$_GET['level'];
$file = "soalan_tahun{$tahun}.php";
$questions = (include $file)[$level];
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Mathventure Quiz</title>
    <link rel="stylesheet" href="asset/css/student-layout.css">
    <link rel="stylesheet" href="asset/css/game-play.css">
    <style>
        .quiz-card { max-width: 600px; margin: 50px auto; background: white; padding: 30px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); text-align: center; }
        .q-img { max-width: 100%; height: auto; border-radius: 10px; margin: 15px 0; border: 3px solid #f0f0f0; }
        .option-btn { display: block; width: 100%; padding: 12px; margin: 10px 0; border: 2px solid #3498db; border-radius: 10px; background: none; cursor: pointer; transition: 0.3s; }
        .option-btn:hover { background: #3498db; color: white; }
        input[type="text"] { width: 100%; padding: 12px; border: 2px solid #3498db; border-radius: 10px; margin-top: 15px; text-align: center; }
        .next-btn { background: #27ae60; color: white; padding: 12px 30px; border: none; border-radius: 10px; margin-top: 20px; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body class="game-bg">
    <div class="quiz-card">
        <div id="quiz-area">
            <h3 id="q-text"></h3>
            <div id="media-area"></div>
            <div id="answer-area"></div>
            <button class="next-btn" id="submit-btn" onclick="checkAnswer()">Seterusnya</button>
        </div>
    </div>

<script>
const questions = <?php echo json_encode($questions); ?>;
let currentQ = 0;
let score = 0;

function loadQuestion() {
    const q = questions[currentQ];
    document.getElementById('q-text').innerText = q.text;
    const media = document.getElementById('media-area');
    const ansArea = document.getElementById('answer-area');
    media.innerHTML = q.type === 'visual' ? `<img src="${q.image}" class="q-img">` : '';
    
    if(q.type === 'mcq') {
        ansArea.innerHTML = q.options.map((opt, i) => `<button class="option-btn" onclick="selectMcq(${i})">${opt}</button>`).join('');
        document.getElementById('submit-btn').style.display = 'none';
    } else {
        ansArea.innerHTML = `<input type="text" id="text-ans" placeholder="Jawapan anda...">`;
        document.getElementById('submit-btn').style.display = 'block';
    }
}

function selectMcq(idx) {
    if(idx === questions[currentQ].correct) score++;
    next();
}

function checkAnswer() {
    const userAns = document.getElementById('text-ans').value.trim().toLowerCase();
    const correctOnes = Array.isArray(questions[currentQ].answer) ? questions[currentQ].answer : [questions[currentQ].answer];
    if(correctOnes.some(a => a.toLowerCase() === userAns)) score++;
    next();
}

function next() {
    currentQ++;
    if(currentQ < questions.length) loadQuestion();
    else finish();
}

function finish() {
    const fd = new FormData();
    fd.append('tahun', <?php echo $tahun; ?>);
    fd.append('level', <?php echo $level; ?>);
    fd.append('score', score);
    fd.append('total_questions', questions.length);

    fetch('process-quiz.php', { method: 'POST', body: fd })
    .then(() => {
        alert("Tahniah! Skor: " + score + "/" + questions.length);
        window.location.href = 'badges.php';
    });
}
loadQuestion();
</script>
</body>
</html>