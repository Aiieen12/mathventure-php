<?php
require_once '../config.php';

// Pastikan pelajar login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pelajar') {
    header('Location: index.php');
    exit;
}

$nama = $_SESSION['username'];
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : 4;
$level = isset($_GET['level']) ? (int)$_GET['level'] : 1;

// Ambil set soalan ikut tahun
if ($tahun == 4) {
    $questionSets = include 'soalan_tahun4.php';
} else {
    $questionSets = [];
}

$currentQuestions = $questionSets[$level] ?? [];
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Permainan Tahun <?php echo $tahun; ?> - Level <?php echo $level; ?></title>
</head>
<body>

<h1>Permainan Matematik (Tahun <?php echo $tahun; ?> - Level <?php echo $level; ?>)</h1>
<p>Pelajar: <?php echo htmlspecialchars($nama); ?></p>
<hr>

<?php if (empty($currentQuestions)): ?>
    <p>Soalan untuk level ini belum disediakan.</p>
<?php else: ?>
    <div id="game"></div>
<?php endif; ?>

<script>
<?php if (!empty($currentQuestions)): ?>
const questions = <?php echo json_encode($currentQuestions, JSON_UNESCAPED_UNICODE); ?>;
let current = 0, score = 0;

function showQuestion() {
    const q = questions[current];
    let html = `<h2>Soalan ${current + 1}</h2><p>${q.text}</p><ul>`;
    q.options.forEach((opt, i) => {
        html += `<li><button onclick="checkAnswer(${i})">${opt}</button></li>`;
    });
    html += '</ul>';
    document.getElementById('game').innerHTML = html;
}

function checkAnswer(i) {
    const q = questions[current];
    if (i === q.correct) {
        score++;
        alert('Betul!');
    } else {
        alert('Salah! Jawapan betul: ' + q.options[q.correct]);
    }
    nextQuestion();
}

function nextQuestion() {
    current++;
    if (current < questions.length) {
        showQuestion();
    } else {
        document.getElementById('game').innerHTML = `
            <h2>Tamat!</h2>
            <p>Markah anda: ${score} / ${questions.length}</p>
            <a href="game-menu.php">Kembali ke Menu</a>
        `;
    }
}

showQuestion();
<?php endif; ?>
</script>
</body>
</html>
