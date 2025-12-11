<?php
session_start();
require_once '../config.php';
$page = 'marks';

$pelajar = [
    ['nama' => 'Ali bin Abu', 'markah' => 85],
    ['nama' => 'Muthu Kumar', 'markah' => 38],
    ['nama' => 'Siti Sarah', 'markah' => 92],
];
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Markah | Mathventure</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="dashboard-teacher.css">
</head>
<body>
    <?php include 'sidebar-teacher.php'; ?>

    <main class="main-content">
        <div class="section-header">
            <h2>Analisis Prestasi</h2>
        </div>

        <div class="marks-layout">
            <div class="chart-card">
                <h3>Peratusan Lulus vs Gagal</h3>
                <div class="chart-container">
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>

            <div class="table-card">
                <h3>Senarai Markah Terkini</h3>
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Markah</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($pelajar as $p): ?>
                        <tr>
                            <td><?php echo $p['nama']; ?></td>
                            <td><strong><?php echo $p['markah']; ?>%</strong></td>
                            <td>
                                <?php if($p['markah'] >= 40): ?>
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
        </div>
    </main>

    <script>
        const ctx = document.getElementById('performanceChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Berjaya', 'Gagal'],
                datasets: [{
                    data: [80, 20],
                    backgroundColor: ['#2ecc71', '#e74c3c'],
                    borderWidth: 0
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
        });
    </script>
</body>
</html>