<?php
session_start();
require_once '../config.php';

$page = 'marks'; // untuk highlight menu "Markah" di sidebar

// Mock data – nanti boleh ganti dengan data dari database
$pelajar = [
    ['nama' => 'Ali bin Abu',   'markah' => 85],
    ['nama' => 'Muthu Kumar',   'markah' => 38],
    ['nama' => 'Siti Sarah',    'markah' => 92],
];

// Kira lulus / gagal
$lulus = 0;
$gagal = 0;
foreach ($pelajar as $p) {
    if ($p['markah'] >= 40) {
        $lulus++;
    } else {
        $gagal++;
    }
}
$jumlahPelajar = count($pelajar);
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Markah Pelajar | Mathventure</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- CSS global: background + sidebar + main-content cerah -->
    <link rel="stylesheet" href="../asset/css/teacher-shell.css?v=1">
    <!-- CSS khas untuk halaman markah -->
    <link rel="stylesheet" href="../asset/css/teacher-marks.css?v=1">
</head>
<body class="teacher-mode">

<div class="teacher-layout">
    <?php include 'sidebar-teacher.php'; ?>

    <main class="main-content marks-page">
        <!-- Tajuk seksyen -->
        <header class="marks-header">
            <div>
                <h2>Analisis Prestasi Pelajar</h2>
                <p>Lihat ringkasan lulus/gagal dan markah terkini murid.</p>
            </div>
            <div class="marks-meta">
                <span class="chip-count">
                    <i class="fa-solid fa-users"></i>
                    <span><?php echo $jumlahPelajar; ?> murid</span>
                </span>
            </div>
        </header>

        <section class="marks-layout">
            <!-- Kad carta -->
            <article class="chart-card">
                <div class="card-header">
                    <h3>Peratusan Lulus vs Gagal</h3>
                    <p>Ringkasan prestasi berdasarkan ambang 40%.</p>
                </div>
                <div class="chart-container">
                    <canvas id="performanceChart"></canvas>
                </div>
                <div class="chart-legend-mini">
                    <span class="legend-dot lulus"></span> Lulus
                    <span class="legend-separator">•</span>
                    <span class="legend-dot gagal"></span> Gagal
                </div>
            </article>

            <!-- Kad jadual markah -->
            <article class="table-card">
                <div class="card-header">
                    <h3>Senarai Markah Terkini</h3>
                    <p>Markah untuk ujian / latihan terkini.</p>
                </div>
                <div class="table-wrapper">
                    <table class="styled-table">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Markah</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pelajar as $p): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($p['nama']); ?></td>
                                    <td><strong><?php echo $p['markah']; ?>%</strong></td>
                                    <td>
                                        <?php if ($p['markah'] >= 40): ?>
                                            <span class="badge success">Berjaya</span>
                                        <?php else: ?>
                                            <span class="badge fail">Gagal</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </article>
        </section>
    </main>
</div>

<script>
    const lulus = <?php echo $lulus; ?>;
    const gagal = <?php echo $gagal; ?>;

    const ctx = document.getElementById('performanceChart').getContext('2d');

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Berjaya', 'Gagal'],
            datasets: [{
                data: [lulus, gagal],
                backgroundColor: ['#55E6C1', '#ff9f7b'],
                borderWidth: 0,
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            cutout: '65%'
        }
    });
</script>

</body>
</html>
