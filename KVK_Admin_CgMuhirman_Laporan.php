<?php
session_start();
$admin_name = "Cikgu Muhirman";

// Database connection
$pdo = new PDO("mysql:host=localhost;dbname=kvkaunsel_db", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Base condition for accepted bookings only
$accepted_where = "WHERE status = 'Selesai'";

// Total Accepted Bookings
$total_accepted = $pdo->query("SELECT COUNT(*) FROM tempahan_kaunseling $accepted_where")->fetchColumn();

// SVM vs DVM Breakdown
$svm_count = $pdo->query("SELECT COUNT(*) FROM tempahan_kaunseling $accepted_where AND tahap = 'SVM'")->fetchColumn();
$dvm_count = $pdo->query("SELECT COUNT(*) FROM tempahan_kaunseling $accepted_where AND tahap = 'DVM'")->fetchColumn();

// Monthly Trend (Last 12 months)
$monthly_sql = "
    SELECT DATE_FORMAT(tarikh_masa, '%Y-%m') AS bulan, 
           COUNT(*) AS jumlah
    FROM tempahan_kaunseling
    $accepted_where
      AND tarikh_masa >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
    GROUP BY bulan
    ORDER BY bulan ASC
";
$monthly_data = $pdo->query($monthly_sql)->fetchAll(PDO::FETCH_KEY_PAIR); // 'YYYY-MM' => count

// Prepare labels and data for chart (oldest → newest)
$months = [];
$counts = [];
for ($i = 11; $i >= 0; $i--) {
    $date = date('Y-m', strtotime("-$i months"));
    $months[] = date('M Y', strtotime($date)); // e.g., Jan 2025
    $counts[] = $monthly_data[$date] ?? 0;
}

// Top Jenis Kaunseling (Popular types)
$top_jenis = $pdo->query("
    SELECT jenis_kaunseling, COUNT(*) AS jumlah
    FROM tempahan_kaunseling
    $accepted_where
      AND jenis_kaunseling IS NOT NULL
      AND jenis_kaunseling != ''
    GROUP BY jenis_kaunseling
    ORDER BY jumlah DESC
    LIMIT 6
")->fetchAll(PDO::FETCH_ASSOC);

// Calculate most common jenis kaunseling percentage for the stats card
$most_common_percent = 0;
$most_common_jenis = 'Tiada Data';
if ($total_accepted > 0 && !empty($top_jenis)) {
    $highest_count = $top_jenis[0]['jumlah'];
    $most_common_percent = round(($highest_count / $total_accepted) * 100);
    $most_common_jenis = $top_jenis[0]['jenis_kaunseling'];
}

// Recent 10 Accepted Bookings
$recent = $pdo->query("
    SELECT nama, tahap, tarikh_masa, kaunselor
    FROM tempahan_kaunseling
    $accepted_where
    ORDER BY tarikh_masa DESC
    LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin KVKaunsel - Laporan</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --purple: #8b5cf6;
            --pink: #ec4899;
            --darkpurple: #6b21a8;
            --light: #f8f9ff;
            --green: #10b981;
            --blue: #3b82f6;
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: 'Inter', sans-serif; background:var(--light); display:flex; color:#333; min-height:100vh; }

        .sidebar {
            width: 280px; background: var(--darkpurple); color: white; height: 100vh;
            padding: 30px 24px; position:fixed; overflow-y:auto;
        }
        .logo { display:flex; align-items:center; margin-bottom:50px; font-size:24px; font-weight:800; }
        .logo i { font-size:32px; margin-right:14px; background:var(--purple); width:52px; height:52px;
            border-radius:16px; display:flex; align-items:center; justify-content:center; }
        .menu-item {
            display:flex; align-items:center; padding:16px 20px; border-radius:14px;
            margin-bottom:10px; cursor:pointer; transition:0.3s; font-weight:600;
        }
        .menu-item:hover { background:rgba(255,255,255,0.15); }
        .menu-item.active { background:rgba(255,255,255,0.25); box-shadow:0 8px 20px rgba(0,0,0,0.2); }
        .menu-item i { width:40px; font-size:19px; text-align:center; }
        .menu-item span { margin-left:16px; font-size:15.5px; }

        .main { margin-left:280px; width:calc(100% - 280px); padding:40px; }

        .header {
            background: linear-gradient(135deg, var(--purple), var(--pink));
            color:white; padding:30px 40px; border-radius:18px;
            display:flex; justify-content:space-between; align-items:center; margin-bottom:40px;
            box-shadow:0 10px 30px rgba(139,92,246,0.3);
        }
        .header h1 { font-size:28px; font-weight:700; }
        .header .subtitle { font-size:18px; opacity:0.9; margin-top:8px; }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
            margin-bottom: 40px;
        }
        .stat-card {
            background: white;
            padding: 28px;
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            text-align: center;
        }
        .stat-card h3 {
            font-size: 42px;
            font-weight: 800;
            color: var(--darkpurple);
            margin: 12px 0;
        }
        .stat-card p {
            font-size: 16px;
            color: #666;
            font-weight: 600;
        }
        .stat-card .icon {
            font-size: 48px;
            margin-bottom: 16px;
            opacity: 0.9;
        }
        .svm { color: #f59e0b; }
        .dvm { color: #0891b2; }

        .chart-container {
            background: white;
            padding: 30px;
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            margin-bottom: 40px;
        }
        .chart-container h2 {
            font-size: 22px;
            margin-bottom: 24px;
            color: var(--darkpurple);
            font-weight: 700;
        }

        .two-cols {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }
        @media (max-width: 992px) {
            .two-cols { grid-template-columns: 1fr; }
        }

        .list-card {
            background: white;
            padding: 28px;
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }
        .list-card h2 {
            font-size: 20px;
            margin-bottom: 20px;
            color: var(--darkpurple);
            font-weight: 700;
            border-bottom: 2px solid var(--purple);
            padding-bottom: 12px;
        }
        .list-item {
            display: flex;
            justify-content: space-between;
            padding: 14px 0;
            border-bottom: 1px solid #eee;
            font-size: 16px;
        }
        .list-item:last-child { border-bottom: none; }
        .list-item strong { color: #444; }

        .recent-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
        }
        .recent-table th {
            text-align: left;
            padding: 12px 16px;
            background: var(--darkpurple);
            color: white;
            font-weight: 600;
        }
        .recent-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #eee;
        }
        .recent-table tr:hover {
            background: #f3e8ff;
        }
        .badge {
            padding: 6px 12px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .SVM { background:#fff3cd; color:#856404; }
        .DVM { background:#d0f2ff; color:#0879a0; }
    </style>
</head>
<body>

    <!-- SIDEBAR -->
    <div class="sidebar">
        <div class="logo"><i class="fas fa-heart-pulse"></i>KVKaunsel</div>
        
        <div class="menu-item" onclick="location.href='KVK_Admin_CgMuhirman_Utama.php'">
            <i class="fas fa-home"></i><span>Laman Utama</span>
        </div>
        
        <div class="menu-item" onclick="location.href='KVK_Admin_CgMuhirman_Tempahan.php'">
            <i class="fas fa-book-open-reader"></i><span>Tempahan Pelajar</span>
        </div>

        <div class="menu-item" onclick="location.href='KVK_Admin_CgMuhirman_Temujanji.php'">
            <i class="fas fa-calendar-check"></i><span>Temujanji</span>
        </div>
        
        <div class="menu-item active">
            <i class="fas fa-chart-line"></i><span>Laporan</span>
        </div>
        
        <div class="menu-item" style="margin-top:auto;padding-top:60px;" onclick="if(confirm('Log keluar?')) location.href='logout.php'">
            <i class="fas fa-sign-out-alt"></i><span>Log Keluar</span>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main">
        <div class="header">
            <div>
                <h1>Laporan Kaunseling</h1>
                <div class="subtitle">Statistik tempahan yang telah DITERIMA sahaja (Status: Selesai)</div>
            </div>
            <div style="text-align:right;">
                <div style="font-size:18px;">Jumlah Keseluruhan Diterima</div>
                <b style="font-size:36px;"><?= $total_accepted ?></b>
            </div>
        </div>

        <!-- KEY STATS -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon" style="color:var(--green);"><i class="fas fa-check-circle"></i></div>
                <h3><?= $total_accepted ?></h3>
                <p>Tempahan Diterima</p>
            </div>
            <div class="stat-card">
                <div class="icon svm"><i class="fas fa-user-graduate"></i></div>
                <h3><?= $svm_count ?></h3>
                <p>Pelajar SVM</p>
            </div>
            <div class="stat-card">
                <div class="icon dvm"><i class="fas fa-user-tie"></i></div>
                <h3><?= $dvm_count ?></h3>
                <p>Pelajar DVM</p>
            </div>
            <div class="stat-card">
                <div class="icon" style="color:var(--purple);"><i class="fas fa-brain"></i></div>
                <h3><?= $most_common_percent ?>%</h3>
                <p>Nisbah Jenis Kaunseling</p>
            </div>
        </div>

        <!-- MONTHLY TREND CHART -->
        <div class="chart-container">
            <h2>Tren Bulanan (12 Bulan Terakhir)</h2>
            <canvas id="monthlyChart" height="100"></canvas>
        </div>

        <!-- TOP LISTS -->
        <div class="two-cols">
            <div class="list-card">
                <h2>Jenis Kaunseling Popular</h2>
                <?php if (count($top_jenis) > 0): ?>
                    <?php foreach ($top_jenis as $item): ?>
                        <div class="list-item">
                            <span><?= htmlspecialchars($item['jenis_kaunseling'] ?: 'Tiada Spesifik') ?></span>
                            <strong><?= $item['jumlah'] ?> kes</strong>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color:#888; text-align:center; padding:30px;">Tiada data jenis kaunseling lagi</p>
                <?php endif; ?>
            </div>

            <div class="list-card">
                <h2>Pecahan Mengikut Tahap Pelajar</h2>
                
                <?php if ($total_accepted > 0): ?>
                    <?php 
                    $svm_percent = round(($svm_count / $total_accepted) * 100);
                    $dvm_percent = round(($dvm_count / $total_accepted) * 100);
                    ?>

                    <!-- SVM -->
                    <div class="list-item" style="align-items:center;">
                        <div>
                            <strong>Pelajar SVM</strong><br>
                            <span style="font-size:14px; color:#666;"><?= $svm_count ?> kes</span>
                        </div>
                        <div style="text-align:right;">
                            <strong style="font-size:20px; color:#f59e0b;"><?= $svm_percent ?>%</strong>
                        </div>
                    </div>
                    <div style="margin: 12px 0;">
                        <div style="background:#eee; border-radius:12px; height:20px; overflow:hidden;">
                            <div style="width:<?= $svm_percent ?>%; background:linear-gradient(90deg, #f59e0b, #d97706); height:100%; border-radius:12px; transition:width 0.8s ease;"></div>
                        </div>
                    </div>

                    <!-- DVM -->
                    <div class="list-item" style="align-items:center;">
                        <div>
                            <strong>Pelajar DVM</strong><br>
                            <span style="font-size:14px; color:#666;"><?= $dvm_count ?> kes</span>
                        </div>
                        <div style="text-align:right;">
                            <strong style="font-size:20px; color:#0891b2;"><?= $dvm_percent ?>%</strong>
                        </div>
                    </div>
                    <div style="margin: 12px 0;">
                        <div style="background:#eee; border-radius:12px; height:20px; overflow:hidden;">
                            <div style="width:<?= $dvm_percent ?>%; background:linear-gradient(90deg, #0891b2, #06b6d4); height:100%; border-radius:12px; transition:width 0.8s ease;"></div>
                        </div>
                    </div>

                    <div style="margin-top:24px; padding-top:16px; border-top:1px dashed #ddd; text-align:center; color:#666;">
                        <strong>Jumlah Keseluruhan: <?= $total_accepted ?> kes</strong>
                    </div>

                <?php else: ?>
                    <p style="color:#888; text-align:center; padding:40px;">
                        <i class="fas fa-users-slash" style="font-size:48px; display:block; margin-bottom:16px; color:#ccc;"></i>
                        Belum ada tempahan yang diterima lagi.
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <!-- RECENT ACCEPTED SESSIONS -->
        <div class="chart-container">
            <h2>Sesi Kaunseling Terbaru (10 Terkini)</h2>
            <?php if (count($recent) > 0): ?>
                <table class="recent-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Pelajar</th>
                            <th>Tahap</th>
                            <th>Tarikh & Masa</th>
                            <th>Kaunselor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent as $i => $r): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><b><?= htmlspecialchars($r['nama']) ?></b></td>
                                <td><span class="badge <?= $r['tahap'] ?>"><?= $r['tahap'] ?></span></td>
                                <td><?= date('d/m/Y h:i A', strtotime($r['tarikh_masa'])) ?></td>
                                <td><b><?= htmlspecialchars($r['kaunselor'] ?: 'Belum Ditentukan') ?></b></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="text-align:center; color:#888; padding:50px; font-size:18px;">
                    <i class="fas fa-calendar-times" style="font-size:48px; display:block; margin-bottom:20px; color:#ccc;"></i>
                    Tiada sesi kaunseling diterima lagi.
                </p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('monthlyChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode($months) ?>,
                datasets: [{
                    label: 'Bilangan Tempahan Diterima',
                    data: <?= json_encode($counts) ?>,
                    borderColor: '#8b5cf6',
                    backgroundColor: 'rgba(139, 92, 246, 0.15)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#8b5cf6',
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    x: {
                        grid: { display: false }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    </script>
</body>
</html>