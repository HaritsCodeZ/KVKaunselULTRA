<?php
session_start();
$admin_name = "Cikgu Muhirman";
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
  :root {
    --purple: #8b5cf6;
    --pink: #ec4899;
    --darkpurple: #6b21a8;
  }
  * { margin:0; padding:0; box-sizing:border-box; }
  body { font-family: 'Inter', sans-serif; background:#f9f5ff; display:flex; color:#333; }

  /* SIDEBAR */
  .sidebar {
    width: 280px; background: var(--darkpurple); color: white; height: 100vh;
    padding: 30px 24px; position:fixed; font_weight:bold;
  }
  .logo { display:flex; align-items:center; margin-bottom:50px; font-size:22px; font-weight:700; }
  .logo i { font-size:28px; margin-right:12px; background:var(--purple); width:48px; height:48px;
    border-radius:14px; display:flex; align-items:center; justify-content:center; }
  .menu-item { display:flex; align-items:center; padding:14px 18px; border-radius:12px;
    margin-bottom:8px; cursor:pointer; transition:0.3s; }
  .menu-item:hover, .menu-item.active { background:rgba(255, 255, 255, 0.3); }
  .menu-item i { width:40px; font-size:18px; }
  .menu-item span { margin-left:16px; font-size:15px; }

  /* MAIN */
  .main { margin-left:280px; width:calc(100% - 280px); padding:30px; }

  /* HEADER */
  .header {
    background: var(--purple); color:white; padding:20px 30px; border-radius:16px;
    display:flex; justify-content:space-between; align-items:center; margin-bottom:30px;
  }
  .header h1 { font-size:24px; font-weight:600; }
  .header .info { text-align:right; font-size:14px; }
  .header .info b { font-size:16px; display:block; margin-top:4px; }


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
    box-shadow:0 10px 30px rgba(139,92,246,0.1);margin-right: -115px;
  }
  .left-card h3 { font-size:18px; margin-bottom:20px; font-weight:600; }

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
  <div class="menu-item active"><i class="fas fa-home"></i><span>Utama</span></div>
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

  <!-- BOTTOM SECTION - LEBAR & RAPAT KANAN -->
  <div class="bottom-grid">
    <!-- KIRI: LEBAR -->
    <div class="left-card">
      <h3>Pelajar Terkini</h3>
      <table>
        <tr><th>Bil</th><th>Nama Pelajar</th><th>Tarikh</th><th>Jenis Kes</th><th>Status</th></tr>
        <tr><td>1</td><td>Ahmad Daniel</td><td>29/08/2025</td><td>Akademik</td><td><span class="status processing">Sedang Diproses</span></td></tr>
        <tr><td>2</td><td>Nur Aina</td><td>28/08/2025</td><td>Emosi</td><td><span class="status pending">Menunggu</span></td></tr>
        <tr><td>3</td><td>Amirul Hakeem</td><td>28/08/2025</td><td>Disiplin</td><td><span class="status processing">Sedang Diproses</span></td></tr>
      </table>

      <h3>Mengikut Jenis Kes</h3>
      <div class="bar-item"><div>Akademik</div><div class="bar-outer"><div class="bar-inner" style="width:68%"></div></div><b>68%</b></div>
      <div class="bar-item"><div>Emosi</div><div class="bar-outer"><div class="bar-inner" style="width:45%"></div></div><b>45%</b></div>
    </div>

    <!-- KANAN: RAPAT KE KANAN -->
    <div class="right-column">
      <div class="chart-card">
        <h3>Sasaran Bulanan</h3>
        <div class="donut"><div class="donut-inner">82%</div></div>
        <p style="color:#666;margin-top:8px;">258 / 315 pelajar</p>
      </div>

      <div class="chart-card">
        <h3>Mengikut Tingkatan</h3>
        <div class="bar-item"><div>Tingkatan 5</div><div class="bar-outer"><div class="bar-inner" style="width:48%"></div></div><b>48%</b></div>
        <div class="bar-item"><div>Tingkatan 4</div><div class="bar-outer"><div class="bar-inner" style="width:35%"></div></div><b>35%</b></div>
      </div>
    </div>
  </div>

</div>
</body>
</html>