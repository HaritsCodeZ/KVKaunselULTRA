<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['kaunselor_id'])) {
    header("Location: UltimateLoginPage.php");
    exit;
}

$admin_name = $_SESSION['counselor_short_name'] ?? $_SESSION['counselor_full_name'] ?? "Cikgu Muhirman";

// Database connection
try {
    $pdo = new PDO("mysql:host=localhost;dbname=kvkaunsel_db", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Latest 5 bookings
$recent_students = $pdo->query("
    SELECT nama, tarikh_masa, jenis_kaunseling, status, tarikh_tempahan
    FROM tempahan_kaunseling 
    ORDER BY tarikh_tempahan DESC 
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// Jenis kaunseling percentages
$type_data = $pdo->query("
    SELECT jenis_kaunseling, COUNT(*) as count 
    FROM tempahan_kaunseling 
    WHERE jenis_kaunseling IS NOT NULL AND jenis_kaunseling != ''
    GROUP BY jenis_kaunseling
")->fetchAll(PDO::FETCH_ASSOC);

$total_cases = array_sum(array_column($type_data, 'count'));
$percentages = [];
foreach ($type_data as $row) {
    $percentages[$row['jenis_kaunseling']] = $total_cases > 0 ? round(($row['count'] / $total_cases) * 100) : 0;
}

$common_types = ['Akademik' => 0, 'Emosi' => 0, 'Disiplin' => 0, 'Kerjaya' => 0, 'Peribadi' => 0];
foreach ($common_types as $type => $val) {
    $common_types[$type] = $percentages[$type] ?? 0;
}

// Profile photo path
$counselor_username = strtolower($_SESSION['kaunselor_nama'] ?? 'default');
$profile_photo = "profiles/{$counselor_username}.jpg";
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>KVKaunsel - Laman Utama</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --purple: #8b5cf6;
            --pink: #ec4899;
            --darkpurple: #6b21a8;
            --light: #f8f9ff;
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { 
            font-family: 'Inter', sans-serif; 
            background:var(--light); 
            display:flex; 
            color:#333; 
            min-height:100vh; 
        }

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

        /* PROFILE SECTION */
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
        }
        .profile-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .avatar-placeholder {
            width: 100%; height: 100%;
            background: linear-gradient(135deg, var(--purple), var(--pink));
            color: white; font-size: 36px; font-weight: 800;
            display: flex; align-items: center; justify-content: center;
            display: none;
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
            cursor: pointer;
            transition: 0.3s;
            font-weight: 600;
        }
        .menu-item:hover { background: rgba(255,255,255,0.15); }
        .menu-item.active { background: rgba(255,255,255,0.25); box-shadow: 0 8px 20px rgba(0,0,0,0.2); }
        .menu-item i { width: 40px; font-size: 19px; text-align: center; }
        .menu-item span { margin-left: 16px; font-size: 15.5px; }

        /* MAIN CONTENT */
        .main { 
            margin-left: 280px; 
            width: calc(100% - 280px); 
            padding: 40px; 
            overflow-y: auto;
        }

        .header {
            background: linear-gradient(135deg, var(--purple), var(--pink));
            color: white;
            padding: 25px 35px;
            border-radius: 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 35px;
            box-shadow: 0 10px 30px rgba(139,92,246,0.3);
        }
        .header h1 { font-size: 24px; font-weight: 700; }
        .header .info { text-align: right; }
        .header .info b { font-size: 18px; display: block; margin-top: 6px; }

        .grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); 
            gap: 24px; 
            margin-bottom: 40px; 
        }
        .metric-card {
            background: white;
            border-radius: 20px;
            padding: 24px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(139,92,246,0.1);
        }
        .metric-card .icon-circle {
            width: 80px; height: 80px; border-radius: 50%;
            background: linear-gradient(135deg, #8b5cf6, #ec4899);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 16px;
            font-size: 32px; color: white;
        }
        .metric-card h3 { font-size: 14px; color: #888; margin-bottom: 8px; }
        .metric-card .number { font-size: 36px; font-weight: 700; margin: 8px 0; }
        .metric-card .change { font-size: 13px; }
        .positive { color: #10b981; }
        .new-student { background: linear-gradient(135deg, #8b5cf6, #ec4899); color: white; }
        .new-student .number, .new-student h3 { color: white; }

        .left-card {
            background: white;
            border-radius: 20px;
            padding: 32px;
            box-shadow: 0 10px 30px rgba(139,92,246,0.1);
            cursor: pointer;
            transition: all 0.3s ease;
            min-height: 520px;
        }
        .left-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(139,92,246,0.25);
        }
        .left-card:hover .bar-inner {
            background: linear-gradient(90deg, #a78bfa, #ec4899) !important;
        }

        table { width: 100%; border-collapse: collapse; font-size: 14.5px; margin: 30px 0; }
        th { background: #f5f0ff; padding: 14px 12px; text-align: left; color: #555; }
        td { padding: 14px 12px; border-bottom: 1px solid #eee; }
        .status { padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .processing { background: #dbeafe; color: #1e40af; }
        .pending { background: #fef3c7; color: #92400e; }

        .bar-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 18px 0;
            font-size: 14.5px;
        }
        .bar-outer { flex: 1; height: 12px; background: #e2e8f0; border-radius: 6px; margin: 0 14px; overflow: hidden; }
        .bar-inner { height: 100%; background: linear-gradient(90deg, #8b5cf6, #ec4899); border-radius: 6px; }

        /* MODAL */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.7); backdrop-filter: blur(8px);
            display: none; align-items: center; justify-content: center; z-index: 1000;
        }
        .modal-content {
            background: white; width: 90%; max-width: 460px; border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.3); overflow: hidden;
        }
        .modal-header {
            padding: 24px 28px 16px; border-bottom: 1px solid #eee; position: relative;
        }
        .modal-header h3 { font-size: 22px; color: var(--darkpurple); }
        .close-modal {
            position: absolute; top: 24px; right: 28px; font-size: 28px;
            cursor: pointer; color: #aaa;
        }
        .close-modal:hover { color: #000; }
        .modal-content form { padding: 24px 28px; }
        .modal-content label { display: block; margin: 16px 0 8px; font-weight: 600; color: #444; }
        .modal-content input[type=password] {
            width: 100%; padding: 14px 16px; border: 1px solid #ddd;
            border-radius: 12px; font-size: 16px;
        }
        .btn-cancel, .btn-save {
            padding: 12px 24px; border: none; border-radius: 12px;
            font-weight: 600; cursor: pointer; margin-left: 10px;
        }
        .btn-cancel { background: #eee; color: #666; }
        .btn-save { background: var(--purple); color: white; }
        .btn-save:hover { background: #7c4dff; }

        @media (max-width: 1200px) { .grid { grid-template-columns: 1fr 1fr; } }
        @media (max-width: 768px) {
            .sidebar { width: 100%; height: auto; position: relative; }
            .main { margin-left: 0; width: 100%; }
        }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="profile-section" id="profileDropdown">
        <div class="profile-avatar">
            <img src="<?= $profile_photo ?>?v=<?= time() ?>" alt="Profile Photo"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div class="avatar-placeholder"><?= strtoupper(substr($admin_name, 0, 2)) ?></div>
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
        <div class="menu-item-profile" onclick="document.getElementById('profileUploadInput').click()">
            <i class="fas fa-camera"></i><span>Tukar Gambar Profil</span>
        </div>
        <hr style="margin:10px 16px; border-color:rgba(255,255,255,0.1);">
        <div class="menu-item-profile" onclick="if(confirm('Log keluar dari sistem?')) location.href='logout.php'">
            <i class="fas fa-sign-out-alt"></i><span>Log Keluar</span>
        </div>
    </div>

    <div class="menu-item <?= basename($_SERVER['PHP_SELF']) == 'KVK_Admin_CgMuhirman_Utama.php' ? 'active' : '' ?>">
        <i class="fas fa-home"></i><span>Laman Utama</span>
    </div>
    <div class="menu-item <?= basename($_SERVER['PHP_SELF']) == 'KVK_Admin_CgMuhirman_Tempahan.php' ? 'active' : '' ?>"
         onclick="location.href='KVK_Admin_CgMuhirman_Tempahan.php'">
        <i class="fas fa-book-open-reader"></i><span>Tempahan Pelajar</span>
    </div>
    <div class="menu-item <?= basename($_SERVER['PHP_SELF']) == 'KVK_Admin_CgMuhirman_Temujanji.php' ? 'active' : '' ?>"
         onclick="location.href='KVK_Admin_CgMuhirman_Temujanji.php'">
        <i class="fas fa-calendar-check"></i><span>Temujanji</span>
    </div>
    <div class="menu-item <?= basename($_SERVER['PHP_SELF']) == 'KVK_Admin_CgMuhirman_Laporan.php' ? 'active' : '' ?>"
         onclick="location.href='KVK_Admin_CgMuhirman_Laporan.php'">
        <i class="fas fa-chart-line"></i><span>Laporan</span>
    </div>
</div>

<input type="file" id="profileUploadInput" accept="image/*" style="display:none;" onchange="previewProfilePicture(this)">

<!-- PASSWORD MODAL -->
<div id="passwordModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Tukar Kata Laluan</h3>
            <span class="close-modal" onclick="closePasswordModal()">&times;</span>
        </div>
        <form id="changePassForm">
            <input type="hidden" name="kaunselor_id" value="<?= $_SESSION['kaunselor_id'] ?>">
            <label>Kata Laluan Lama</label>
            <input type="password" name="old_password" required placeholder="Masukkan kata laluan lama">
            <label>Kata Laluan Baru</label>
            <input type="password" name="new_password" required minlength="6" placeholder="Minimum 6 aksara">
            <label>Sahkan Kata Laluan Baru</label>
            <input type="password" name="confirm_password" required placeholder="Ulang kata laluan baru">
            
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
        <h1>KVKaunsel - Utama</h1>
        <div class="info">
            Selamat Datang!<br>
            <b>Panel Kaunselor: <?= htmlspecialchars($admin_name) ?></b>
        </div>
    </div>

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

    <div class="left-card" onclick="window.location.href='KVK_Admin_CgMuhirman_Tempahan.php'">
        <h3>Pelajar Terkini 
            <span style="font-size: 14px; color: #8b5cf6; float: right; font-weight: 500;">Lihat Semua →</span>
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
                <td><?= date('d/m/Y', strtotime($r['tarikh_tempahan'])) ?></td>
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
        <p style="text-align:center;color:#888;padding:60px 0;">Tiada tempahan terkini</p>
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

        <?php if(array_sum($common_types) == 0): ?>
        <p style="text-align:center;color:#888;margin-top:30px;">Tiada data kes lagi</p>
        <?php endif; ?>

        <div style="text-align: center; margin-top: 40px; color: #999; font-size: 14px;">
            Klik di mana-mana untuk lihat senarai penuh
        </div>
    </div>
</div>

<script>
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

    window.previewProfilePicture = function(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.querySelector('.profile-avatar img');
                const placeholder = document.querySelector('.avatar-placeholder');
                img.src = e.target.result;
                img.style.display = 'block';
                placeholder.style.display = 'none';
            };
            reader.readAsDataURL(input.files[0]);
        }
    };

    window.openChangePasswordModal = function() {
        document.getElementById('passwordModal').style.display = 'flex';
        document.getElementById('profileMenu').style.display = 'none';
        document.getElementById('passwordMessage').innerHTML = '';
        document.getElementById('changePassForm').reset();
    };

    window.closePasswordModal = function() {
        document.getElementById('passwordModal').style.display = 'none';
    };

    // AJAX Submit for Password Change
    document.getElementById('changePassForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const messageDiv = document.getElementById('passwordMessage');
        const data = new FormData(this);

        fetch('change_admin_pass.php', {
            method: 'POST',
            body: data,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
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
});
</script>

</body>
</html>