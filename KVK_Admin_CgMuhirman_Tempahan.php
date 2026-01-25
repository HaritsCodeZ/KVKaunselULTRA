<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['kaunselor_id'])) {
    header("Location: UltimateLoginPage.php");
    exit;
}

$admin_name = $_SESSION['counselor_short_name'] ?? $_SESSION['counselor_full_name'] ?? "Cikgu Muhirman";
$counselor_full_name = $_SESSION['counselor_full_name'] ?? "Encik Muhirman Bin Mu Alim"; // Must match exact name in DB

// Database connection
$pdo = new PDO("mysql:host=localhost;dbname=kvkaunsel_db", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Get optional tahap filter from URL
$filter = $_GET['tahap'] ?? '';
$allowed_filters = ['SVM', 'DVM'];

$where_conditions = ["archived = 0", "kaunselor = :kaunselor"];
$params = [':kaunselor' => $counselor_full_name];

if ($filter && in_array($filter, $allowed_filters)) {
    $where_conditions[] = "tahap = :tahap";
    $params[':tahap'] = $filter;
}

$where = "WHERE " . implode(" AND ", $where_conditions);

$sql = "SELECT * FROM tempahan_kaunseling $where ORDER BY tarikh_tempahan DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Today's count — only for this counselor
$today_stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM tempahan_kaunseling 
    WHERE DATE(tarikh_tempahan) = CURDATE() 
      AND archived = 0 
      AND kaunselor = :kaunselor
");
$today_stmt->execute([':kaunselor' => $counselor_full_name]);
$today_count = $today_stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KVKaunsel Admin_1_Tempahan</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --purple: #8b5cf6;
            --pink: #ec4899;
            --darkpurple: #6b21a8;
            --light: #f8f9ff;
            --orange: #f59e0b;
            --orange-dark: #d97706;
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

        /* MENU ITEMS */
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

        /* ACTIONS BAR */
        .actions-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 16px;
        }
        .filter { display:flex; gap:12px; flex-wrap:wrap; }
        .filter a {
            padding:12px 24px; background:var(--purple); color:white; text-decoration:none;
            border-radius:12px; font-weight:600; transition:0.3s; box-shadow:0 4px 15px rgba(139,92,246,0.4);
        }
        .filter a:hover { transform:translateY(-3px); box-shadow:0 10px 25px rgba(139,92,246,0.5); }
        .filter a.active { background:#4c1d95; }

        .bulk-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        .bulk-btn {
            padding: 10px 20px;
            background: var(--orange);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }
        .bulk-btn:hover {
            background: var(--orange-dark);
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(245,158,11,0.4);
        }

        /* TABLE */
        table { width:100%; border-collapse:collapse; background:white; border-radius:16px;
            overflow:hidden; box-shadow:0 10px 40px rgba(0,0,0,0.08); }
        th { background: var(--darkpurple); color:white; padding:18px 20px; text-align:left; font-weight:600; }
        td { padding:18px 20px; border-bottom:1px solid #eee; vertical-align:top; }
        tr:hover { background: #dbb3ebff; }
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

        /* ARCHIVE BUTTON */
        .btn-archive {
            background: var(--orange);
            color: white;
        }
        .btn-archive:hover {
            background: var(--orange-dark);
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(245,158,11,0.4);
        }

        /* MODAL - TWO COLUMN LAYOUT */
        .modal {
            display: none;
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.7); backdrop-filter: blur(10px);
            align-items: center; justify-content: center; z-index: 1000;
            padding: 20px;
        }
        .modal-content {
            background: white; border-radius: 24px; width: 90%; max-width: 1200px;
            max-height: 90vh; overflow: hidden; box-shadow: 0 30px 80px rgba(0,0,0,0.4);
            display: flex; flex-direction: row;
            animation: modalFadeIn 0.4s ease;
        }
        .modal-left {
            width: 420px;
            height: 860px; 
            background: #f8f9ff;
            display: flex;
            align-items: center;
            justify-content: center;
            border-right: 1px solid #eee;
        }

        .modal-left::before {
            content: '';
            position: absolute;
            width: 420px;
            height: 850px;
            border: 3px solid var(--purple);
            border-radius: 20px;
            box-shadow: inset 0 0 20px rgba(139,92,246,0.2);
            pointer-events: none;
        }

        .modal-left img {
            max-width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 30px;
            position: relative;
            z-index: 1;
        }
        .modal-right {
            flex: 1; overflow-y: auto; padding: 40px;
            position: relative;
        }
        .modal-header {
            margin-bottom: 24px; border-bottom: 1px solid #eee; padding-bottom: 16px;
            position: relative;
            padding-right: 50px;
        }
        .modal-header h2 {
            font-size: 28px; color: var(--darkpurple); font-weight: 700;
        }
        .modal-header .close-modal {
            position: absolute; top: 50%; right: 20px;
            transform: translateY(-50%);
            font-size: 36px; cursor: pointer; color: #aaa;
        }
        .modal-header .close-modal:hover { color: #000; }
        .detail-grid {
            display: grid; grid-template-columns: 180px 1fr; gap: 18px 24px;
            font-size: 16px; line-height: 1.7;
        }
        .detail-grid strong { color: #444; font-weight: 600; }
        .modal-footer {
            margin-top: 32px; padding-top: 20px; border-top: 1px solid #eee;
            text-align: right;
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            flex-wrap: wrap;
        }

        .btn {
            padding: 14px 32px;
            border: none;
            border-radius: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 16px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        .btn-decline {
            background: #ef4444;
            color: white;
        }
        .btn-decline:hover {
            background: #dc2626;
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(239,68,68,0.4);
        }
        .btn-accept {
            background: #10b981;
            color: white;
        }
        .btn-accept:hover {
            background: #059669;
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(16,185,129,0.4);
        }

        @keyframes modalFadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        @media (max-width: 900px) {
            .modal-content { flex-direction: column; }
            .modal-left { width: 100%; height: 320px; border-right: none; border-bottom: 1px solid #eee; padding: 30px; }
            .modal-right { padding: 30px; }
            .modal-header { padding-right: 60px; }
            .modal-footer { justify-content: center; }
            .actions-bar { flex-direction: column; align-items: stretch; }
            .bulk-actions { justify-content: center; }
        }

        .confirm-modal {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        .confirm-modal.show {
            opacity: 1;
            visibility: visible;
        }
        .confirm-modal-content {
            background: white;
            padding: 40px 50px;
            border-radius: 24px;
            text-align: center;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            animation: popIn 0.4s ease;
        }
        .confirm-modal-content h3 {
            font-size: 28px;
            color: var(--orange-dark);
            margin-bottom: 16px;
        }
        .confirm-modal-content p {
            font-size: 18px;
            color: #444;
            margin-bottom: 30px;
        }
        .timer-big {
            font-size: 64px;
            font-weight: 800;
            color: var(--purple);
            margin: 20px 0;
            text-shadow: 0 4px 10px rgba(139,92,246,0.2);
        }
        .btn-confirm-large {
            background: var(--orange);
            color: white;
            padding: 16px 48px;
            font-size: 20px;
            font-weight: 600;
            border: none;
            border-radius: 16px;
            cursor: pointer;
            box-shadow: 0 10px 30px rgba(245,158,11,0.4);
            transition: all 0.3s;
        }
        .btn-confirm-large:hover {
            background: var(--orange-dark);
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(245,158,11,0.5);
        }

        @keyframes popIn {
            from { transform: scale(0.8); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .Ditolak {
            background: #fee2e2 !important;
            color: #991b1b !important;
        }

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

        @keyframes whitePulse {
    0% {
        text-shadow: 0 0 5px rgba(255, 255, 255, 0.5);
        opacity: 0.8;
    }
    50% {
        text-shadow: 0 0 20px rgba(255, 255, 255, 1), 0 0 30px rgba(255, 255, 255, 0.6);
        opacity: 1;
        transform: scale(1.02);
    }
    100% {
        text-shadow: 0 0 5px rgba(255, 255, 255, 0.5);
        opacity: 0.8;
    }
}

.pulse-text {
    text-align: center;
    padding: 20px;
    font-size: 14px;
    color: white;
    width: 100%;
    font-weight: bold;
    display: block;
    animation: whitePulse 2s infinite ease-in-out;
    transition: transform 0.3s ease;
}

    </style>
</head>
<body>

<!-- SIDEBAR WITH PROPER <a> LINKS -->
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

    <a href="KVK_Admin_CgMuhirman_Utama.php" class="menu-item <?= basename($_SERVER['PHP_SELF']) == 'KVK_Admin_CgMuhirman_Utama.php' ? 'active' : '' ?>">
        <i class="fas fa-home"></i><span>Laman Utama</span>
    </a>
    <a href="KVK_Admin_CgMuhirman_Tempahan.php" class="menu-item active">
        <i class="fas fa-book-open-reader"></i><span>Tempahan Pelajar</span>
    </a>
    <a href="KVK_Admin_CgMuhirman_Temujanji.php" class="menu-item <?= basename($_SERVER['PHP_SELF']) == 'KVK_Admin_CgMuhirman_Temujanji.php' ? 'active' : '' ?>">
        <i class="fas fa-calendar-check"></i><span>Temujanji</span>
    </a>
    <a href="KVK_Admin_CgMuhirman_Laporan.php" class="menu-item <?= basename($_SERVER['PHP_SELF']) == 'KVK_Admin_CgMuhirman_Laporan.php' ? 'active' : '' ?>">
        <i class="fas fa-chart-line"></i><span>Laporan</span>
    </a>
        <div class="pulse-text">
    Dapatkan Kod Jemputan Di Laman Utama!
</div>
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
    <!-- HEADER -->
    <div class="header">
        <div>
            <h1>Selamat Datang, <?= htmlspecialchars($admin_name) ?></h1>
            <p>Mari Semak Tempahan Pelajar Harini!</p>
        </div>
        <div class="info">
            <div>Jumlah Tempahan Hari Ini</div>
            <b><?= $today_count ?></b>
        </div>
    </div>

    <!-- ACTIONS BAR: FILTER + BULK ARCHIVE -->
    <div class="actions-bar">
        <div class="filter">
            <a href="?" class="<?= !$filter ? 'active' : '' ?>">Semua Tempahan Saya</a>
            <a href="?tahap=SVM" class="<?= $filter=='SVM' ? 'active' : '' ?>">SVM Sahaja</a>
            <a href="?tahap=DVM" class="<?= $filter=='DVM' ? 'active' : '' ?>">DVM Sahaja</a>
        </div>

        <div class="bulk-actions">
            <button class="bulk-btn" onclick="bulkArchive('selesai')">
                <i class="fas fa-archive"></i> Hapus Semua Selesai
            </button>
            <button class="bulk-btn" onclick="bulkArchive('ditolak')">
                <i class="fas fa-archive"></i> Hapus Semua Ditolak
            </button>
            <button class="bulk-btn" onclick="bulkArchive('both')">
                <i class="fas fa-broom"></i> Hapus Semua Selesai & Ditolak
            </button>
        </div>
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
                <th>Tarikh & Masa Yang Diinginkan</th>
                <th>Jenis Sesi</th>
                <th>Kaunselor</th>
                <th>Angka Giliran</th>
                <th>Status</th>
                <th>Ditempah Pada</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($data as $i => $r): ?>
            <tr class="booking-row"
    data-id="<?= $r['id'] ?>"
    data-nama="<?= htmlspecialchars($r['nama'], ENT_QUOTES) ?>"
    data-student_id="<?= htmlspecialchars($r['student_id'], ENT_QUOTES) ?>" data-tahap="<?= $r['tahap'] ?>"
    data-program="<?= htmlspecialchars($r['program'], ENT_QUOTES) ?>"
    data-semester="<?= $r['semester'] ?>"
    data-telefon="<?= $r['telefon'] ?>"
    data-jantina="<?= $r['jantina'] ?>"
    data-kaum="<?= $r['kaum'] ?>"
    data-tarikh="<?= date('d/m/Y h:i A', strtotime($r['tarikh_masa'])) ?>"
    data-jenis_sesi="<?= $r['jenis_sesi'] ?>"
    data-jenis_kaunseling="<?= htmlspecialchars($r['jenis_kaunseling'], ENT_QUOTES) ?>"
    data-kaunselor="<?= htmlspecialchars($r['kaunselor'], ENT_QUOTES) ?>"
    data-sebab="<?= htmlspecialchars($r['sebab'], ENT_QUOTES) ?>"
    data-status="<?= $r['status'] ?>"
    style="cursor:pointer"
    onclick="event.preventDefault(); event.stopPropagation(); openBookingModal(this.dataset);">
                <td><?= $i+1 ?></td>
                <td>
                    <span class="badge <?= 
                        $r['status']=='Baru' ? 'Baru' : 
                        ($r['status']=='Selesai' ? 'Selesai' : 
                        ($r['status']=='Dibatalkan' ? 'Ditolak' : $r['status']))
                    ?>">
                        <?= $r['status']=='Selesai' ? 'Diterima' : ($r['status']=='Dibatalkan' ? 'Ditolak' : $r['status']) ?>
                    </span>
                </td>
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
                <td style="font-weight: bold; color: var(--black);">
                <?= htmlspecialchars($r['student_id']) ?>
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
            <p>Tiada tempahan untuk anda pada masa ini.</p>
        </div>
    <?php endif; ?>
</div>

<!-- BOOKING MODAL -->
<div id="bookingModal" class="modal">
    <div class="modal-content">
        <div class="modal-left">
            <img id="passImage" src="" alt="Student Pass">
        </div>
        <div class="modal-right">
            <div class="modal-header">
                <h2 id="modalTitle">Butiran Tempahan</h2>
                <span class="close-modal" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body" id="modalBody"></div>
            <div class="modal-footer" id="modalFooter"></div>
        </div>
    </div>
</div>

<!-- ACCEPT CONFIRM MODAL -->
<div id="confirmModal" class="confirm-modal">
    <div class="confirm-modal-content">
        <h3 id="confirmTitle">Sahkan Tindakan?</h3>
        <p id="confirmMessage">Anda mempunyai masa untuk mengesahkan tindakan ini.</p>
        <div class="timer-big">5</div>
        <button class="btn-confirm-large" id="finalConfirmBtn">
            <i class="fas fa-check"></i> Sahkan
        </button>
    </div>
</div>

<!-- DECLINE CONFIRM MODAL -->
<div id="declineConfirmModal" class="confirm-modal">
    <div class="confirm-modal-content">
        <h3>Tolak Tempahan Ini?</h3>
        <p>Pelajar akan dimaklumkan bahawa tempahan mereka ditolak.</p>
        <div class="timer-big" style="color:#ef4444;">5</div>
        <button class="btn-confirm-large" id="finalDeclineBtn" style="background:#ef4444;">
            <i class="fas fa-times-circle"></i> Ya, Tolak Tempahan
        </button>
    </div>
</div>



<!-- Confetti CDN -->
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.2/dist/confetti.browser.min.js"></script>

<script>
    let confirmTimer = null;
    let currentAction = null;
    let currentId = null;
    let bulkType = null;

    // Profile dropdown
    document.addEventListener('DOMContentLoaded', function() {
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
    });

    // Password modal
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

    // Booking modal logic
    function openBookingModal(d) {
        const body = document.getElementById('modalBody');
        const footer = document.getElementById('modalFooter');
        const passImg = document.getElementById('passImage');
        const title = document.getElementById('modalTitle');

        title.textContent = `Hi nama saya, ${d.nama}`;

        let tahapClean = (d.tahap || '').trim().toUpperCase();
passImg.src = (tahapClean === 'SVM')
    ? 'ImageGalleries/SVM_PASS_X.jpg?' + new Date().getTime()
    : (tahapClean === 'DVM')
    ? 'ImageGalleries/DVM_PASS_X.jpg?' + new Date().getTime()
    : 'ImageGalleries/default_pass.jpg?' + new Date().getTime();

        body.innerHTML = `
    <div class="detail-grid">
        <strong>Nama Pelajar</strong>      <div><b>${d.nama}</b></div>
        <strong>Angka Giliran</strong>   <div><b>${d.student_id || '-'}</b></div>
        <strong>Tahap</strong>           <div><span class="badge ${d.tahap}">${d.tahap}</span></div>
        <strong>Program / Sem</strong>   <div>${d.program || '-'} / <b>${d.semester || '-'}</b></div>
        <strong>Telefon</strong>         <div>${d.telefon}</div>
        <strong>Jantina / Kaum</strong>   <div>${d.jantina} / ${d.kaum}</div>
        <strong>Tarikh & Masa</strong>   <div><b>${d.tarikh}</b></div>
        
        <strong>Jenis Sesi / Kaunseling</strong> 
        <div>${d.jenis_sesi} / <b>${d.jenis_kaunseling || 'Tiada'}</b></div>
        
        <strong>Kaunselor</strong>        <div><b>${d.kaunselor || 'Belum Ditentukan'}</b></div>
        
        <strong>Sebab Penuh</strong>
        <div style="grid-column: 1 / -1; background:#f8f9ff; padding:20px; border-radius:12px; border-left:5px solid var(--purple); white-space: pre-wrap;">
            ${d.sebab || '<em style="color:#888;">Tiada sebab diberikan</em>'}
        </div>
    </div>
`;

        footer.innerHTML = '';

        if (d.status === 'Baru') {
            footer.innerHTML += `
                <button class="btn btn-decline" id="initDeclineBtn">
                    <i class="fas fa-times-circle"></i> Tolak
                </button>
                <button class="btn btn-accept" id="initAcceptBtn">
                    <i class="fas fa-check-circle"></i> Terima
                </button>
            `;

            // Decline button triggers confirmation
            document.getElementById('initDeclineBtn').onclick = (e) => {
                e.stopPropagation();
                currentAction = 'decline';
                currentId = d.id;
                showDeclineConfirmModal();
            };

            // Accept button triggers confirmation
            document.getElementById('initAcceptBtn').onclick = (e) => {
                e.stopPropagation();
                currentAction = 'accept';
                currentId = d.id;
                showConfirmModal('Sahkan Penerimaan Tempahan?', 'Anda mempunyai masa untuk mengesahkan penerimaan ini.', '#10b981');
            };
        } else if (d.status === 'Selesai' || d.status === 'Dibatalkan') {
            footer.innerHTML += `
                <button class="btn btn-archive" onclick="archiveBooking(${d.id})">
                    <i class="fas fa-archive"></i> Hapus dari Senarai
                </button>
                <p style="color:#666; margin-top:16px; font-size:14px;">
                    Status: <strong>${d.status === 'Selesai' ? 'Diterima' : 'Ditolak'}</strong> — sudah diproses
                </p>
            `;
        } else {
            footer.innerHTML = `<p style="color:#666;">Status: <strong>${d.status}</strong> — sudah diproses</p>`;
        }

        document.getElementById('bookingModal').style.display = 'flex';
    }

    function showConfirmModal(title, message, color) {
        document.getElementById('confirmTitle').textContent = title;
        document.getElementById('confirmMessage').textContent = message;
        document.querySelector('#confirmModal .btn-confirm-large').style.background = color;

        const modal = document.getElementById('confirmModal');
        const timerEl = modal.querySelector('.timer-big');
        let seconds = 5;
        timerEl.textContent = seconds;
        modal.classList.add('show');

        clearInterval(confirmTimer);
        confirmTimer = setInterval(() => {
            seconds--;
            timerEl.textContent = seconds;
            if (seconds <= 0) {
                clearInterval(confirmTimer);
                modal.classList.remove('show');
            }
        }, 1000);

        document.getElementById('finalConfirmBtn').onclick = () => {
            clearInterval(confirmTimer);
            modal.classList.remove('show');

            if (bulkType) {
                performBulkArchive(bulkType);
                bulkType = null;
            } else if (currentAction === 'accept') {
                changeStatus(currentId, 'Selesai', 'Diterima');
            } else if (currentAction === 'archive') {
                archiveBooking(currentId);
            }
        };
    }

    function showDeclineConfirmModal() {
        const modal = document.getElementById('declineConfirmModal');
        const timerEl = modal.querySelector('.timer-big');
        let seconds = 5;
        timerEl.textContent = seconds;
        modal.classList.add('show');

        clearInterval(confirmTimer);
        confirmTimer = setInterval(() => {
            seconds--;
            timerEl.textContent = seconds;
            if (seconds <= 0) {
                clearInterval(confirmTimer);
                modal.classList.remove('show');
            }
        }, 1000);

        document.getElementById('finalDeclineBtn').onclick = () => {
            clearInterval(confirmTimer);
            modal.classList.remove('show');
            changeStatus(currentId, 'Dibatalkan', 'Ditolak');
        };
    }

    function archiveBooking(id) {
        currentAction = 'archive';
        currentId = id;
        showConfirmModal('Hapus Tempahan Ini?', 'Tempahan ini akan dihapuskan dari senarai tetapi kekal dalam laporan.', 'var(--orange)');
    }

    function bulkArchive(type) {
        bulkType = type;
        let title = '';
        let message = '';

        if (type === 'selesai') {
            title = 'Hapus Semua Tempahan Selesai?';
            message = 'Semua tempahan DITERIMA akan dihapuskan dari senarai.';
        } else if (type === 'ditolak') {
            title = 'Hapus Semua Tempahan Ditolak?';
            message = 'Semua tempahan DITOLAK akan dihapuskan dari senarai.';
        } else if (type === 'both') {
            title = 'Hapus Semua Tempahan Selesai & Ditolak?';
            message = 'Semua tempahan yang sudah diproses akan dihapuskan.';
        }

        showConfirmModal(title, message, 'var(--orange)');
    }

    function performBulkArchive(type) {
        fetch('bulk_archive_booking.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `type=${type}`
        })
        .then(r => r.text())
        .then(result => {
            if (result.trim() === 'success') {
                location.reload();
            } else {
                alert('Gagal arkib: ' + result);
            }
        })
        .catch(() => alert('Ralat sambungan.'));
    }

    function changeStatus(id, dbStatus, displayText) {
        fetch('update_status.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${id}&status=${encodeURIComponent(dbStatus)}`
        })
        .then(r => r.text())
        .then(result => {
            if (result.trim() === 'success') {
                location.reload();
            } else {
                alert('Gagal: ' + result);
            }
        })
        .catch(() => alert('Ralat sambungan.'));
    }

    function closeModal() {
        if (confirmTimer) clearInterval(confirmTimer);
        document.getElementById('bookingModal').style.display = 'none';
        document.getElementById('confirmModal').classList.remove('show');
        document.getElementById('declineConfirmModal').classList.remove('show');
    }

    window.addEventListener('click', (e) => {
        if (e.target.classList.contains('modal') || 
            e.target.id === 'confirmModal' || 
            e.target.id === 'declineConfirmModal') {
            closeModal();
        }
    });
    
    // Password form submit
    document.getElementById('changePassForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const messageDiv = document.getElementById('passwordMessage');
        const data = new FormData(this);

        fetch('change_admin_pass.php', {
            method: 'POST',
            body: data,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(result => {
            messageDiv.style.color = result.success ? '#10b981' : '#ef4444';
            messageDiv.innerHTML = result.message;

            if (result.success) {
                setTimeout(closePasswordModal, 2000);
            }
        })
        .catch(() => {
            messageDiv.style.color = '#ef4444';
            messageDiv.innerHTML = 'Ralat sambungan. Sila cuba lagi.';
        });
    });
    // Fungsi untuk membuka modal jemputan
window.openInviteModal = function() {
    document.getElementById('inviteModal').style.display = 'flex';
    document.getElementById('displayArea').style.display = 'none';
    document.getElementById('btnGenerate').style.display = 'block';
};


</script>
</body>
</html>