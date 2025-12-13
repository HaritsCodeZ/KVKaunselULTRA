<?php
session_start();
$admin_name = "Cikgu Muhirman";

// Database connection
try {
    $pdo = new PDO("mysql:host=localhost;dbname=kvkaunsel_db", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// 1. Get latest 3 bookings
$stmt_recent = $pdo->query("
    SELECT nama, tarikh_masa, jenis_kaunseling, status 
    FROM tempahan_kaunseling 
    ORDER BY tarikh_tempahan DESC 
    LIMIT 5
");
$recent_students = $stmt_recent->fetchAll(PDO::FETCH_ASSOC);

// 2. Get percentage by jenis kaunseling
$stmt_types = $pdo->query("
    SELECT jenis_kaunseling, COUNT(*) as count 
    FROM tempahan_kaunseling 
    GROUP BY jenis_kaunseling
");
$type_data = $stmt_types->fetchAll(PDO::FETCH_ASSOC);

$total_cases = array_sum(array_column($type_data, 'count'));
$percentages = [];
foreach ($type_data as $row) {
    $percent = $total_cases > 0 ? round(($row['count'] / $total_cases) * 100) : 0;
    $percentages[$row['jenis_kaunseling']] = $percent;
}

// Default common types (so bars always show even if 0)
$common_types = ['Akademik' => 0, 'Emosi' => 0, 'Disiplin' => 0, 'Kerjaya' => 0, 'Peribadi' => 0];
foreach ($common_types as $type => $val) {
    $common_types[$type] = $percentages[$type] ?? 0;
}

?>

<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="UTF-8">
<title>Kaunseling KV - Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
  html, body {
    height: 100%;
    overflow: hidden; /* This disables scrolling completely */
}
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



  /* 4 METRIC CARDS */
  .grid { display:grid; grid-template-columns: repeat(4, 1fr); gap:24px; margin-bottom:30px; }
  .metric-card {
    background:white; border-radius:20px; padding:24px; text-align:center;
    box-shadow:0 10px 30px rgba(139,92,246,0.1);
  }
  .metric-card .icon-circle {
    width:80px; height:80px; border-radius:50%;
    background:linear-gradient(135deg, #8b5cf6, #ec4899);
    display:flex; align-items:center; justify-content:center; margin:0 auto 16px;
    font-size:32px; color:white;
  }
  .metric-card h3 { font-size:14px; color:#888; margin-bottom:8px; }
  .metric-card .number { font-size:36px; font-weight:700; margin:8px 0; }
  .metric-card .change { font-size:13px; }
  .positive { color:#10b981; }
  .new-student { background:linear-gradient(135deg, #8b5cf6, #ec4899); color:white; }
  .new-student .number, .new-student h3 { color:white; }

  /* BOTTOM SECTION - OPTIMIZED LAYOUT */
.bottom-grid {
    display: grid;
    grid-template-columns: 2.2fr 1fr;
    gap: 30px;
    align-items: start;
    /* Tambah baris ni kalau nak seluruh ruang bawah lebih "balanced" */
    justify-content: start;   /* pastikan tak stretch sampai hujung kanan */
}

  /* CARD KIRI (Pelajar Terkini + Jenis Kes) */
  .left-card {
    background:white; border-radius:20px; padding:32px;
    box-shadow:0 10px 30px rgba(139,92,246,0.1);margin-right: 390px; min-height: 515px;
  }
  .left-card h3 { font-size:18px; margin-bottom:20px; font-weight:600; }

  .left-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(139,92,246,0.25) !important;
}

.left-card:hover .bar-inner {
    background: linear-gradient(90deg, #a78bfa, #ec4899) !important;
}

.left-card:hover {
    border: 2px solid #d8b4fe;
}

  table { width:100%; border-collapse:collapse; font-size:14.5px; margin-bottom:30px; }
  th { background:#f5f0ff; padding:14px 12px; text-align:left; color:#555; }
  td { padding:14px 12px; border-bottom:1px solid #eee; }
  .status { padding:6px 14px; border-radius:20px; font-size:12px; font-weight:600; }
  .processing { background:#dbeafe; color:#1e40af; }
  .pending { background:#fef3c7; color:#92400e; }

  /* BAR */
  .bar-item {
    display:flex; align-items:center; justify-content:space-between; margin:18px 0; font-size:14.5px;
  }
  .bar-outer { flex:1; height:12px; background:#e2e8f0; border-radius:6px; margin:0 14px; overflow:hidden; }
  .bar-inner { height:100%; background:linear-gradient(90deg, #8b5cf6, #ec4899); border-radius:6px; }

  /* KANAN - Donut + Tingkatan (rapat ke kanan) */
 .right-column {
    display: flex;
    flex-direction: column;
    gap: 30px;
    /* Ini yang buat dia gerak ke kiri sikit dari tepi kanan */
    margin-left: 110px;        /* ← adjust nilai ni ikut suka */
    margin-right: -2px;       /* biar tak terlalu rapat tepi kanan */
}
  .chart-card {
    background:white; border-radius:20px; padding:32px;
    box-shadow:0 10px 30px rgba(139,92,246,0.1); text-align:center;
  }
  .chart-card h3 { font-size:18px; margin-bottom:24px; font-weight:600; }

  .donut {
    width:160px; height:160px; border-radius:50%;
    background:conic-gradient(#8b5cf6 0% 82%, #ec4899 82% 100%);
    display:flex; align-items:center; justify-content:center; margin:0 auto 16px;
  }
  .donut-inner {
    width:110px; height:110px; background:white; border-radius:50%;
    display:flex; align-items:center; justify-content:center;
    font-size:32px; font-weight:700; color:#8b5cf6;
  }

  @media (max-width: 1200px) {
    .grid { grid-template-columns: 1fr 1fr; }
    .bottom-grid { grid-template-columns: 1fr; }
    .right-column { justify-self: stretch; }
  }
</style>
</head>
<body>

<!-- SIDEBAR (sama) -->
<div class="sidebar">
  <div class="logo"><i class="fas fa-heart"></i>KVKaunsel</div>
  <div class="menu-item active"><i class="fas fa-home"></i><span>Laman Utama</span></div>
  <div class="menu-item" onclick="window.location.href='KVK_Admin_CgMuhirman_Tempahan.php'" style="cursor:pointer;">
    <i class="fas fa-book"></i>
    <span>Tempahan Pelajar</span>
</div>
  <div class="menu-item"><i class="fas fa-envelope"></i><span>Ruang Mesej</span></div>
  <div class="menu-item"><i class="fas fa-calendar"></i><span>Temujanji</span></div>
  <div class="menu-item"><i class="fas fa-chart-bar"></i><span>Laporan</span></div>
  <div class="menu-item" style="margin-top:auto;padding-top:50px;"><i class="fas fa-sign-out-alt"></i><span>Log Keluar</span></div>
</div>

<!-- MAIN -->
<div class="main">
  <div class="header">
    <h1>KVKaunsel - Utama</h1>
    <div class="info">
      Selamat Datang!<br>
      <b>Panel Kaunselor: <?= htmlspecialchars($admin_name) ?></b>
    </div>
  </div>

  <!-- 4 METRIC -->
  <div class="grid">
    <div class="metric-card">
      <div class="icon-circle"><i class="fas fa-users"></i></div>
      <h3>Jumlah Kunjungan</h3>
      <div class="number">-</div>
      <div class="change positive">+12% dari bulan lepas</div>
    </div>
    <div class="metric-card">
      <div class="icon-circle"><i class="fas fa-calendar-check"></i></div>
      <h3>Jumlah Sesi</h3>
      <div class="number">-</div>
      <div class="change">Bulan ini</div>
    </div>
    <div class="metric-card">
      <div class="icon-circle"><i class="fas fa-folder-open"></i></div>
      <h3>Kes Aktif</h3>
      <div class="number">-</div>
      <div class="change positive">+8% dari bulan lepas</div>
    </div>
        <div class="metric-card new-student">
      <div class="icon-circle"><i class="fas fa-user-plus"></i></div>
      <h3>Pelajar Baru</h3>
      <div class="number">-</div>
      <div class="change">Pertama kali berjumpa</div>
    </div>
  </div>

<!-- KIRI: LEBAR - CLICKABLE TO BOOKINGS PAGE -->
<div class="left-card" style="cursor: pointer; transition: all 0.3s ease; position: relative;" 
     onclick="window.location.href='KVK_Admin_CgMuhirman_Tempahan.php'">
  
  <!-- Optional overlay for better click area and visual feedback -->
  <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border-radius: 20px; pointer-events: none; 
              box-shadow: 0 0 0 rgba(139,92,246,0); transition: box-shadow 0.3s;"></div>

  <h3>Pelajar Terkini 
    <span style="font-size: 14px; color: #8b5cf6; float: right; font-weight: 500;">
      Lihat Semua →
    </span>
  </h3>

  <?php if(count($recent_students) > 0): ?>
  <table>
    <tr>
      <th>Bil</th>
      <th>Nama Pelajar</th>
      <th>Ditempah Pada</th>
      <th>Jenis Kes</th>
      <th>Status</th>
    </tr>
    <?php foreach($recent_students as $i => $r): ?>
    <tr>
      <td><?= $i + 1 ?></td>
      <td><b><?= htmlspecialchars($r['nama']) ?></b></td>
      <td><?= date('d/m/Y', strtotime($r['tarikh_masa'])) ?></td>
      <td><?= htmlspecialchars($r['jenis_kaunseling'] ?: 'Tiada') ?></td>
      <td>
        <span class="status <?= $r['status'] == 'Selesai' ? 'processing' : 'pending' ?>">
          <?= $r['status'] == 'Baru' ? 'Menunggu' : htmlspecialchars($r['status']) ?>
        </span>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
  <?php else: ?>
  <p style="text-align:center;color:#888;padding:40px 0;">Tiada tempahan terkini</p>
  <?php endif; ?>

  <h3>Mengikut Jenis Kes</h3>
  <?php foreach($common_types as $type => $percent): ?>
    <?php if($percent > 0): ?>
    <div class="bar-item">
      <div><?= htmlspecialchars($type) ?></div>
      <div class="bar-outer">
        <div class="bar-inner" style="width:<?= $percent ?>%"></div>
      </div>
      <b><?= $percent ?>%</b>
    </div>
    <?php endif; ?>
  <?php endforeach; ?>

  <!-- Optional: Small footer hint -->
  <div style="text-align: center; margin-top: 30px; color: #999; font-size: 14px;">
    Klik di mana-mana untuk lihat senarai penuh
  </div>
</div>

  <?php if(array_sum($common_types) == 0): ?>
  <p style="text-align:center;color:#888;margin-top:20px;">Tiada data kes lagi</p>
  <?php endif; ?>
</div>

</div>
</body>
</html>