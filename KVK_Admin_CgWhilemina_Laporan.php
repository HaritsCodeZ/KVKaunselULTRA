<?php
session_start();

// Security: Only Whilemina can access
if (!isset($_SESSION['kaunselor_id']) || $_SESSION['counselor_full_name'] !== 'Whilemina Thimah Gregory Anak Jimbun') {
    header("Location: UltimateLoginPage.php");
    exit;
}

$admin_name = $_SESSION['counselor_short_name'] ?? "Cg. Whilemina";

try {
    $pdo = new PDO("mysql:host=localhost;dbname=kvkaunsel_db", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Sambungan pangkalan data gagal: " . $e->getMessage());
}

$kaunselor = 'Whilemina Thimah Gregory Anak Jimbun';
$accepted_where = "WHERE status = 'Selesai' AND kaunselor = ?";

// Total Accepted Bookings for Whilemina
$stmt_total = $pdo->prepare("SELECT COUNT(*) FROM tempahan_kaunseling $accepted_where");
$stmt_total->execute([$kaunselor]);
$total_accepted = $stmt_total->fetchColumn();

// SVM vs DVM Breakdown for Whilemina
$stmt_svm = $pdo->prepare("SELECT COUNT(*) FROM tempahan_kaunseling $accepted_where AND tahap = 'SVM'");
$stmt_svm->execute([$kaunselor]);
$svm_count = $stmt_svm->fetchColumn();

$stmt_dvm = $pdo->prepare("SELECT COUNT(*) FROM tempahan_kaunseling $accepted_where AND tahap = 'DVM'");
$stmt_dvm->execute([$kaunselor]);
$dvm_count = $stmt_dvm->fetchColumn();

// Monthly Trend (Last 12 months) for Whilemina
$monthly_sql = "
    SELECT DATE_FORMAT(tarikh_masa, '%Y-%m') AS bulan, 
           COUNT(*) AS jumlah
    FROM tempahan_kaunseling
    WHERE kaunselor = ?
      AND status = 'Selesai'
      AND tarikh_masa >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
    GROUP BY bulan
    ORDER BY bulan ASC
";
$stmt_monthly = $pdo->prepare($monthly_sql);
$stmt_monthly->execute([$kaunselor]);
$monthly_data = $stmt_monthly->fetchAll(PDO::FETCH_KEY_PAIR); // 'YYYY-MM' => count

// Prepare labels and data for chart (oldest → newest)
$months = [];
$counts = [];
for ($i = 11; $i >= 0; $i--) {
    $date = date('Y-m', strtotime("-$i months"));
    $months[] = date('M Y', strtotime($date)); // e.g., Jan 2026
    $counts[] = $monthly_data[$date] ?? 0;
}

// Top Jenis Kaunseling for Whilemina
$top_jenis = $pdo->prepare("
    SELECT jenis_kaunseling, COUNT(*) AS jumlah
    FROM tempahan_kaunseling
    WHERE kaunselor = ?
      AND status = 'Selesai'
      AND jenis_kaunseling IS NOT NULL
      AND jenis_kaunseling != ''
    GROUP BY jenis_kaunseling
    ORDER BY jumlah DESC
    LIMIT 6
");
$top_jenis->execute([$kaunselor]);
$top_jenis = $top_jenis->fetchAll(PDO::FETCH_ASSOC);

// Most common jenis percentage
$most_common_percent = 0;
$most_common_jenis = 'Tiada Data';
if ($total_accepted > 0 && !empty($top_jenis)) {
    $highest_count = $top_jenis[0]['jumlah'];
    $most_common_percent = round(($highest_count / $total_accepted) * 100);
    $most_common_jenis = $top_jenis[0]['jenis_kaunseling'];
}

// Recent 10 Accepted Bookings for Whilemina
$recent = $pdo->prepare("
    SELECT nama, tahap, tarikh_masa, kaunselor
    FROM tempahan_kaunseling
    WHERE kaunselor = ? AND status = 'Selesai'
    ORDER BY tarikh_masa DESC
    LIMIT 10
");
$recent->execute([$kaunselor]);
$recent = $recent->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KVKaunsel - Laporan (Cg. Whilemina)</title>
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

        /* SIDEBAR */
        .sidebar {
            width: 280px; 
            background: var(--darkpurple); 
            color: white; 
            height: 100vh;
            padding: 0 24px; 
            position:fixed; 
            overflow-y:auto;
            display: flex;
            flex-direction: column;
        }

        .profile-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 30px 16px 32px;
            cursor: pointer;
            position: relative;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .profile-section:hover { background: rgba(255,255,255,0.08); }

        .profile-avatar {
            width: 96px; height: 96px; border-radius: 50%; overflow: hidden;
            border: 4px solid rgba(255,255,255,0.25);
            margin-bottom: 12px;
            position: relative;
            background: linear-gradient(135deg, var(--purple), var(--pink));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 40px;
        }

        .profile-name { font-size: 19px; font-weight: 700; margin-bottom: 6px; }
        .welcome-text {
            font-size: 14px;
            opacity: 0.85;
            margin-top: 6px;
            font-weight: 500;
            text-align: center;
            line-height: 1.4;
            max-width: 220px;
        }
        .profile-arrow {
            margin-top: 14px;
            font-size: 14px;
            opacity: 0.7;
            transition: transform 0.3s ease;
        }
        .profile-section:hover .profile-arrow { transform: rotate(180deg); }

        .profile-menu {
            position: absolute;
            top: 25%;
            left: 50%;
            transform: translateX(-50%);
            background: var(--darkpurple);
            width: 240px;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
            padding: 12px 0;
            z-index: 100;
            display: none;
            margin-top: 12px;
        }
        .profile-menu::before {
            content: '';
            position: absolute;
            top: -8px;
            left: 50%;
            transform: translateX(-50%);
            border-left: 10px solid transparent;
            border-right: 10px solid transparent;
            border-bottom: 10px solid var(--darkpurple);
        }

        .menu-item-profile {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: white;
            font-weight: 500;
            cursor: pointer;
            transition: 0.2s;
        }
        .menu-item-profile:hover { background: rgba(255,255,255,0.15); }
        .menu-item-profile i { width: 36px; font-size: 17px; text-align: center; }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 16px 20px;
            border-radius: 14px;
            margin-bottom: 10px;
            font-weight: 600;
            color: white;
            text-decoration: none;
            transition: 0.3s;
        }
        .menu-item:hover { background: rgba(255,255,255,0.15); }
        .menu-item.active { background: rgba(255,255,255,0.25); box-shadow: 0 8px 20px rgba(0,0,0,0.2); }
        .menu-item i { width: 40px; font-size: 19px; text-align: center; }
        .menu-item span { margin-left: 16px; font-size: 15.5px; }

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

        /* PASSWORD MODAL STYLES */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.7); backdrop-filter: blur(8px);
            display: none; align-items: center; justify-content: center; z-index: 1000;
        }
        .modal-content-password {
            background: white; width: 90%; max-width: 460px; border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.3); overflow: hidden;
        }
        .modal-header-password {
            padding: 24px 28px 16px; border-bottom: 1px solid #eee; position: relative;
        }
        .modal-header-password h3 { font-size: 22px; color: var(--darkpurple); }
        .close-modal-password {
            position: absolute; top: 24px; right: 28px; font-size: 28px;
            cursor: pointer; color: #aaa;
        }
        .close-modal-password:hover { color: #000; }
        .password-wrapper {
            position: relative;
        }
        .modal-content-password input[type=password],
        .modal-content-password input[type=text].password-input {
            width: 100%; padding: 14px 44px 14px 16px; border: 1px solid #ddd;
            border-radius: 12px; font-size: 16px;
        }
        .eye-icon {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #888;
            font-size: 18px;
        }
        .eye-icon:hover { color: #555; }

        .btn-cancel, .btn-save {
            padding: 12px 24px; border: none; border-radius: 12px;
            font-weight: 600; cursor: pointer; margin-left: 10px;
        }
        .btn-cancel { background: #eee; color: #666; }
        .btn-save { background: var(--purple); color: white; }
        .btn-save:hover { background: #7c4dff; }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="profile-section" id="profileDropdown">
        <div class="profile-avatar">
            <i class="fas fa-user"></i>
        </div>
        <div class="profile-name"><?= htmlspecialchars($admin_name) ?></div>
        <div class="welcome-text">Selamat Datang Ke KVKaunsel Admin</div>
        <i class="fas fa-chevron-down profile-arrow"></i>
    </div>

    <div class="profile-menu" id="profileMenu">
        <div class="menu-item-profile" onclick="location.reload()">
            <i class="fas fa-sync-alt"></i><span>Segarkan Halaman</span>
        </div>
        <div class="menu-item-profile" onclick="openChangePasswordModal()">
            <i class="fas fa-key"></i><span>Tukar Kata Laluan</span>
        </div>
        <hr style="margin:10px 16px; border-color:rgba(255,255,255,0.1);">
        <div class="menu-item-profile" onclick="if(confirm('Log keluar dari sistem?')) location.href='logout.php'">
            <i class="fas fa-sign-out-alt"></i><span>Log Keluar</span>
        </div>
    </div>

    <a href="KVK_Admin_CgWhilemina_Utama.php" class="menu-item <?= basename($_SERVER['PHP_SELF']) == 'KVK_Admin_CgWhilemina_Utama.php' ? 'active' : '' ?>">
        <i class="fas fa-home"></i><span>Laman Utama</span>
    </a>
    <a href="KVK_Admin_CgWhilemina_Tempahan.php" class="menu-item <?= basename($_SERVER['PHP_SELF']) == 'KVK_Admin_CgWhilemina_Tempahan.php' ? 'active' : '' ?>">
        <i class="fas fa-book-open-reader"></i><span>Tempahan Pelajar</span>
    </a>
    <a href="KVK_Admin_CgWhilemina_Temujanji.php" class="menu-item <?= basename($_SERVER['PHP_SELF']) == 'KVK_Admin_CgWhilemina_Temujanji.php' ? 'active' : '' ?>">
        <i class="fas fa-calendar-check"></i><span>Temujanji</span>
    </a>
    <a href="KVK_Admin_CgWhilemina_Laporan.php" class="menu-item active">
        <i class="fas fa-chart-line"></i><span>Laporan</span>
    </a>
</div>

<!-- PASSWORD CHANGE MODAL -->
<div id="passwordModal" class="modal-overlay">
    <div class="modal-content-password">
        <div class="modal-header-password">
            <h3>Tukar Kata Laluan</h3>
            <span class="close-modal-password" onclick="closePasswordModal()">&times;</span>
        </div>
        <form id="changePassForm">
            <input type="hidden" name="kaunselor_id" value="<?= $_SESSION['kaunselor_id'] ?>">
            
            <label>Kata Laluan Lama</label>
            <div class="password-wrapper">
                <input type="password" name="old_password" class="password-input" required placeholder="Masukkan kata laluan lama">
                <i class="fas fa-eye eye-icon" onclick="togglePassword(this, 'old_password')"></i>
            </div>

            <label>Kata Laluan Baru</label>
            <div class="password-wrapper">
                <input type="password" name="new_password" class="password-input" required minlength="6" placeholder="Minimum 6 aksara">
                <i class="fas fa-eye eye-icon" onclick="togglePassword(this, 'new_password')"></i>
            </div>

            <label>Sahkan Kata Laluan Baru</label>
            <div class="password-wrapper">
                <input type="password" name="confirm_password" class="password-input" required placeholder="Ulang kata laluan baru">
                <i class="fas fa-eye eye-icon" onclick="togglePassword(this, 'confirm_password')"></i>
            </div>
            
            <div id="passwordMessage" style="margin: 16px 0; text-align: center; font-weight: 600; min-height: 24px;"></div>
            
            <div style="margin-top:20px; text-align:right;">
                <button type="button" onclick="closePasswordModal()" class="btn-cancel">Batal</button>
                <button type="submit" class="btn-save">Simpan</button>
            </div>
        </form>
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
document.addEventListener('DOMContentLoaded', function() {
    // Profile dropdown and password modal (same as before)
    const profileDropdown = document.getElementById('profileDropdown');
    const profileMenu = document.getElementById('profileMenu');

    if (profileDropdown && profileMenu) {
        profileDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
            profileMenu.style.display = profileMenu.style.display === 'block' ? 'none' : 'block';
        });
    }

    document.addEventListener('click', function(e) {
        if (profileMenu && !profileDropdown.contains(e.target)) {
            profileMenu.style.display = 'none';
        }
    });

    window.openChangePasswordModal = function() {
        document.getElementById('passwordModal').style.display = 'flex';
        document.getElementById('profileMenu').style.display = 'none';
        document.getElementById('passwordMessage').innerHTML = '';
        document.getElementById('changePassForm').reset();
        document.querySelectorAll('.password-input').forEach(input => {
            input.type = 'password';
            const icon = input.parentElement.querySelector('.eye-icon');
            if (icon) icon.classList.replace('fa-eye-slash', 'fa-eye');
        });
    };

    window.closePasswordModal = function() {
        document.getElementById('passwordModal').style.display = 'none';
    };

    window.togglePassword = function(icon, fieldName) {
        const input = document.querySelector(`input[name="${fieldName}"]`);
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    };

    // Monthly Chart
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
});
</script>

</body>
</html>