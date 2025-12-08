<?php
session_start();
if(!isset($_SESSION['kaunselor_id'])) {
    header("Location: login_kaunselor.php");
    exit;
}
$kaunselor_id = $_SESSION['kaunselor_id'];
$kaunselor_nama = $_SESSION['kaunselor_nama'] ?? 'Kaunselor';

$pdo = new PDO("mysql:host=localhost;dbname=kvkaunsel_db", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Tukar query — hanya tunjuk tempahan untuk kaunselor yang login
$stmt = $pdo->prepare("SELECT * FROM tempahan_kaunseling WHERE kaunselor_id = ? ORDER BY tarikh_tempahan DESC");
$stmt->execute([$kaunselor_id]);
$data = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin KVKaunsel - Tempahan Pelajar</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --purple: #8b5cf6;
            --pink: #ec4899;
            --darkpurple: #6b21a8;
            --light: #f8f9ff;
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: 'Inter', sans-serif; background:var(--light); display:flex; color:#333; min-height:100vh; }

        /* SIDEBAR */
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

        /* MAIN CONTENT */
        .main { margin-left:280px; width:calc(100% - 280px); padding:40px; }

        /* HEADER */
        .header {
            background: linear-gradient(135deg, var(--purple), var(--pink));
            color:white; padding:25px 35px; border-radius:18px;
            display:flex; justify-content:space-between; align-items:center; margin-bottom:35px;
            box-shadow:0 10px 30px rgba(139,92,246,0.3);
        }
        .header h1 { font-size:24px; font-weight:700; }
        .header .info { text-align:right; }
        .header .info b { font-size:18px; display:block; margin-top:6px; }

        /* FILTER BUTTON */
        .filter { margin-bottom:25px; display:flex; gap:12px; flex-wrap:wrap; }
        .filter a {
            padding:12px 24px; background:var(--purple); color:white; text-decoration:none;
            border-radius:12px; font-weight:600; transition:0.3s; box-shadow:0 4px 15px rgba(139,92,246,0.4);
        }
        .filter a:hover { transform:translateY(-3px); box-shadow:0 10px 25px rgba(139,92,246,0.5); }
        .filter a.active { background:#4c1d95; }

        /* TABLE */
        table { width:100%; border-collapse:collapse; background:white; border-radius:16px;
            overflow:hidden; box-shadow:0 10px 40px rgba(0,0,0,0.08); }
        th { background: var(--darkpurple); color:white; padding:18px 20px; text-align:left; font-weight:600; }
        td { padding:18px 20px; border-bottom:1px solid #eee; vertical-align:top; }
        tr:hover { background:#f8f5ff; }
        .badge {
            padding:7px 14px; border-radius:30px; font-size:12px; font-weight:700; text-transform:uppercase;
        }
        .SVM { background:#fff3cd; color:#856404; }
        .DVM { background:#d0f2ff; color:#0879a0; }
        .Baru { background:#fee2e2; color:#991b1b; }
        .Selesai { background:#dcfce7; color:#166534; }

        .no-data {
            text-align:center; padding:80px 20px; color:#666; font-size:18px;
        }
    </style>
</head>
<body>

    <!-- SIDEBAR -->
    <div class="sidebar">
        <div class="logo"><i class="fas fa-heart-pulse"></i>KVKaunsel</div>
        
        <div class="menu-item" onclick="location.href='KVK_Admin_CgMuhirman_Utama.php'">
            <i class="fas fa-home"></i><span>Dashboard Utama</span>
        </div>
        
        <div class="menu-item active">
            <i class="fas fa-book-open-reader"></i><span>Tempahan Pelajar</span>
        </div>
        
        <div class="menu-item"><i class="fas fa-envelope"></i><span>Ruang Mesej</span></div>
        <div class="menu-item"><i class="fas fa-calendar-check"></i><span>Temujanji</span></div>
        <div class="menu-item"><i class="fas fa-chart-line"></i><span>Laporan & Statistik</span></div>
        
        <div class="menu-item" style="margin-top:auto;padding-top:60px;" onclick="if(confirm('Log keluar?')) location.href='logout.php'">
            <i class="fas fa-sign-out-alt"></i><span>Log Keluar</span>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main">
        <!-- HEADER -->
        <div class="header">
            <div>
                <h1>Selamat Datang, <?= htmlspecialchars($admin_name) ?></h1>
                <p>Sistem Pengurusan Tempahan Kaunseling</p>
            </div>
            <div class="info">
                <div>Jumlah Tempahan Hari Ini</div>
                <b>
                    <?php
                    $today = $pdo->query("SELECT COUNT(*) FROM tempahan_kaunseling WHERE DATE(tarikh_tempahan) = CURDATE()")->fetchColumn();
                    echo $today;
                    ?>
                </b>
            </div>
        </div>

        <!-- FILTER -->
        <div class="filter">
            <a href="?" class="<?= !$filter ? 'active' : '' ?>">Semua Tempahan</a>
            <a href="?tahap=SVM" class="<?= $filter=='SVM' ? 'active' : '' ?>">SVM Sahaja</a>
            <a href="?tahap=DVM" class="<?= $filter=='DVM' ? 'active' : '' ?>">DVM Sahaja</a>
        </div>

        <!-- TABLE -->
        <?php if(count($data) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tahap</th>
                    <th>Nama Pelajar</th>
                    <th>Program / Sem</th>
                    <th>Tarikh & Masa</th>
                    <th>Jenis Sesi</th>
                    <th>Kaunselor</th>
                    <th>Sebab Ringkas</th>
                    <th>Status</th>
                    <th>Ditempah Pada</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data as $i => $r): ?>
                <tr>
                    <td><?= $i+1 ?></td>
                    <td><span class="badge <?= $r['tahap'] ?>"><?= $r['tahap'] ?></span></td>
                    <td>
                        <b><?= htmlspecialchars($r['nama']) ?></b><br>
                        <small style="color:#666">
                            <?= $r['telefon'] ?> | <?= $r['jantina'] ?> | <?= $r['kaum'] ?>
                        </small>
                    </td>
                    <td><?= htmlspecialchars($r['program']) ?><br><b><?= $r['semester'] ?></b></td>
                    <td><b><?= date('d/m/Y', strtotime($r['tarikh_masa'])) ?><br><?= date('h:i A', strtotime($r['tarikh_masa'])) ?></b></td>
                    <td><?= $r['jenis_sesi'] ?><br><small><?= $r['jenis_kaunseling'] ?></small></td>
                    <td><b><?= htmlspecialchars($r['kaunselor']) ?></b></td>
                    <td style="max-width:200px;white-space:normal;">
                        <?= strlen($r['sebab']) > 60 ? substr(htmlspecialchars($r['sebab']),0,60).'...' : htmlspecialchars($r['sebab']) ?>
                    </td>
                    <td><span class="badge <?= $r['status']=='Baru'?'Baru':$r['status'] ?>"><?= $r['status'] ?></span></td>
                    <td><?= date('d/m/Y H:i', strtotime($r['tarikh_tempahan'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <div class="no-data">
                <i class="fas fa-inbox" style="font-size:60px;color:#ccc;margin-bottom:20px"></i>
                <p>Tiada tempahan ditemui untuk filter ini.</p>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>