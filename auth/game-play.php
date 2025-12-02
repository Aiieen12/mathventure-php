<?php
// auth/game-play.php
require_once '../config.php';

// Pastikan pelajar login
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'pelajar') {
    header('Location: index.php');
    exit;
}

$nama  = $_SESSION['username'] ?? 'Pelajar';
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : 4;
$level = isset($_GET['level']) ? (int)$_GET['level'] : 1;

// Ambil set soalan ikut tahun
$questionSets = [];
if ($tahun === 4) {
    $questionSets = include 'soalan_tahun4.php';
    // nanti boleh tambah: elseif ($tahun === 5) { ... }
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
<p>Pelajar: <strong><?php echo htmlspecialchars($nama); ?></strong></p>
<hr>

<?php if (empty($currentQuestions)): ?>
    <p>Soalan untuk level ini belum disediakan.</p>
    <p><a href="game-menu.php">Kembali ke Menu Permainan</a></p>
<?php else: ?>
    <div id="game"></div>
<?php endif; ?>

<?php if (!empty($currentQuestions)): ?>
<script>
// Soalan dari PHP
const questions = <?php echo json_encode($currentQuestions, JSON_UNESCAPED_UNICODE); ?>;

let current = 0;
let score = 0;

// Papar skrin "Mulakan Permainan" (ikut storyboard)
function showStartScreen() {
    const total = questions.length;
    document.getElementById('game').innerHTML = `
        <h2>Mulakan Permainan</h2>
        <p>Tahap ini mempunyai <strong>${total}</strong> soalan.</p>
        <button onclick="startGame()">▶ Mulakan</button>
    `;
}

function startGame() {
    current = 0;
    score = 0;
    showQuestion();
}

function showQuestion() {
    const q = questions[current];
    let html = `<h2>Soalan ${current + 1}</h2>`;

    // Jika ada gambar (untuk soalan visual)
    if (q.image) {
        html += `
            <div>
                <img src="${q.image}" alt="Gambar soalan" style="max-width:300px;display:block;margin-bottom:10px;">
            </div>
        `;
    }

    html += `<p>${q.text}</p>`;

    if (q.type === 'mcq') {
        // Soalan aneka pilihan
        html += '<ul>';
        q.options.forEach((opt, i) => {
            html += `
                <li style="list-style:none;margin-bottom:6px;">
                    <button onclick="checkAnswerMCQ(${i})">${opt}</button>
                </li>
            `;
        });
        html += '</ul>';
    } else {
        // Soalan isian / esei / visual = jawapan ditaip
        html += `
            <input type="text" id="answerInput" autocomplete="off" style="min-width:250px;">
            <button onclick="checkAnswerText()">Hantar Jawapan</button>
        `;
    }

    document.getElementById('game').innerHTML = html;
}

function normalise(str) {
    return str.trim().toLowerCase().replace(/\s+/g, ' ');
}

function checkAnswerMCQ(index) {
    const q = questions[current];
    if (index === q.correct) {
        score++;
        alert('Betul!');
    } else {
        alert('Salah! Jawapan betul: ' + q.options[q.correct]);
    }
    nextQuestion();
}

function checkAnswerText() {
    const input = document.getElementById('answerInput');
    if (!input) return;

    const userAns = normalise(input.value);
    const q = questions[current];
    const correctAns = Array.isArray(q.answer)
        ? q.answer.map(a => normalise(a))
        : [normalise(q.answer)];

    if (correctAns.includes(userAns)) {
        score++;
        alert('Betul!');
    } else {
        alert('Salah! Jawapan yang diterima: ' + correctAns[0]);
    }
    nextQuestion();
}

function nextQuestion() {
    current++;
    if (current < questions.length) {
        showQuestion();
    } else {
        showEndScreen();
    }
}

// Skrin "TAHNIAH" + badge + kembali ke peta/menu
function showEndScreen() {
    const total = questions.length;
    document.getElementById('game').innerHTML = `
        <h2>TAHNIAH!</h2>
        <p>Anda telah menamatkan tahap ini.</p>
        <p>Markah: <strong>${score} / ${total}</strong></p>

        <div style="margin:20px 0;padding:15px;border:1px solid #ccc;display:inline-block;">
            <p>Lencana (Badge) yang diperoleh:</p>
            <div style="width:80px;height:80px;border-radius:50%;border:3px solid #000;display:flex;align-items:center;justify-content:center;margin:0 auto;font-size:24px;">
                ★
            </div>
        </div>

        <p>
            <a href="game-menu.php">Kembali ke Peta / Menu Permainan</a>
        </p>
    `;
}

// Mula dengan skrin "Mulakan Permainan"
showStartScreen();
</script>
<?php endif; ?>

</body>
</html>
