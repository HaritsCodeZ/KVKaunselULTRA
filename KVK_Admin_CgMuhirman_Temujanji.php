<?php
session_start();
// 1. Tetapkan timezone supaya PHP dan Database selari
date_default_timezone_set('Asia/Kuala_Lumpur');

// Redirect if not logged in
if (!isset($_SESSION['kaunselor_id'])) {
    header("Location: UltimateLoginPage.php");
    exit;
}

$admin_name = $_SESSION['counselor_short_name'] ?? $_SESSION['counselor_full_name'] ?? "Cikgu Muhirman";

try {
    $pdo = new PDO("mysql:host=localhost;dbname=kvkaunsel_db", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Sambungan pangkalan data gagal: " . $e->getMessage());
}

$approved_status = 'Selesai';
$counselor_full  = 'Encik Muhirman Bin Mu Alim';

$stmt = $pdo->prepare("
    SELECT id, nama, tarikh_masa, jenis_kaunseling, program, semester 
    FROM tempahan_kaunseling 
    WHERE kaunselor = ? AND status = ?
    ORDER BY tarikh_masa
");
$stmt->execute([$counselor_full, $approved_status]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

$today_stmt = $pdo->prepare("
    SELECT COUNT(*) FROM tempahan_kaunseling 
    WHERE DATE(tarikh_masa) = CURDATE() 
      AND kaunselor = ? AND status = ?
");
$today_stmt->execute([$counselor_full, $approved_status]);
$today_count = $today_stmt->fetchColumn();

$events = [];
foreach ($bookings as $booking) {
    $startDate = date('Y-m-d', strtotime($booking['tarikh_masa'])); 
    $timeDisplay = date('h:i A', strtotime($booking['tarikh_masa']));

    $events[] = [
        'id' => $booking['id'],
        'title' => '<i class="fas fa-bell"></i>', 
        'start' => $startDate,
        'allDay' => true,
        'backgroundColor' => 'transparent',
        'borderColor' => 'transparent',
        'extendedProps' => [
            'nama' => $booking['nama'],
            'waktu' => $timeDisplay,
            'jenis' => $booking['jenis_kaunseling'] ?: 'Umum',
            'program_sem' => $booking['program'] . ' Sem ' . $booking['semester']
        ]
    ];
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KVKaunsel Admin_1_Temujanji</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
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
            width: 280px; 
            background: var(--darkpurple); 
            color: white; 
            height: 100vh;
            padding: 0 24px; 
            position:fixed; 
            overflow-y:auto;
            display: flex;
            flex-direction: column;
            z-index: 100;
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
            z-index: 110;
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
            cursor: pointer;
        }
        .menu-item:hover { background: rgba(255,255,255,0.15); }
        .menu-item.active { background: rgba(255,255,255,0.25); box-shadow: 0 8px 20px rgba(0,0,0,0.2); }
        .menu-item i { width: 40px; font-size: 19px; text-align: center; }
        .menu-item span { margin-left: 16px; font-size: 15.5px; }

        .main { margin-left:280px; width:calc(100% - 280px); padding:40px; }

        .header {
            background: linear-gradient(135deg, var(--purple), var(--pink));
            color:white; padding:25px 35px; border-radius:18px;
            display:flex; justify-content:space-between; align-items:center; margin-bottom:40px;
            box-shadow:0 15px 40px rgba(139,92,246,0.3);
        }
        .header h1 { font-size:28px; font-weight:700; }
        .header p { font-size:16px; opacity:0.9; margin-top:6px; }
        .header .info { text-align:right; background:rgba(255,255,255,0.15); padding:12px 20px; border-radius:12px; }
        .header .info div { font-size:14px; }
        .header .info b { font-size:32px; display:block; margin-top:4px; }

        #calendar {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 24px;
            padding: 30px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
        }

        /* CUSTOM CALENDAR HEADER */
        .fc-toolbar { justify-content: center !important; gap: 20px; }
        .fc-toolbar-title { 
            font-size: 28px !important; 
            font-weight: 700; 
            color: var(--darkpurple);
            min-width: 250px;
            text-align: center;
        }
        .fc-button { 
            background: var(--purple) !important; 
            border: none !important; 
            border-radius: 12px !important; 
            padding: 10px 18px !important; 
            transition: all 0.3s ease !important; 
        }
        .fc-button:hover { 
            background: var(--pink) !important; 
            transform: scale(1.1); 
        }

        .fc-daygrid-day { transition: all 0.4s ease; border-radius: 12px; margin: 4px; background: #ffffff; }   
        .fc-daygrid-day:hover { transform: scale(1.05) translateY(-5px); box-shadow: 0 15px 35px rgba(139,92,246,0.2); z-index: 10; }

        .fc-day-today {
            background: linear-gradient(90deg, var(--purple), var(--pink), var(--purple)) !important;
            background-size: 200% 200% !important;
            animation: waveFlow 8s ease infinite !important;
            color: white !important; font-weight: bold;
        }
        @keyframes waveFlow { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }

        /* Styling Ikon Loceng */
        .fc-daygrid-event {
            background: transparent !important;
            border: none !important;
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
        }
        .fc-daygrid-event i { color: #ffe600; font-size: 30px; position: relative; z-index: 2; }

        .fc-daygrid-event-harness {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 45px;
        }
        .fc-daygrid-event-harness::after {
            content: '';
            position: absolute;
            width: 40px; 
            height: 40px;
            background-color: rgba(0, 0, 0, 0.5);
            border-radius: 50%;
            z-index: 1;
            animation: bellPulse 2s infinite ease-out;
        }
        @keyframes bellPulse {
            0% { transform: scale(0.6); opacity: 0.8; }
            100% { transform: scale(1.8); opacity: 0; }
        }

        .fc-col-header-cell {
            background: linear-gradient(135deg, var(--purple), var(--pink));
            color: white; font-weight: 600; padding: 16px; border-radius: 12px 12px 0 0;
        }

        /* PASSWORD MODAL STYLES */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.7); backdrop-filter: blur(8px);
            display: none; align-items: center; justify-content: center; z-index: 1000;
        }
        .modal-content {
            background: white; width: 90%; max-width: 460px; border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.3); overflow: hidden;
        }
        .modal-header { padding: 24px 28px 16px; border-bottom: 1px solid #eee; position: relative; }
        .modal-header h3 { font-size: 22px; color: var(--darkpurple); }
        .close-modal { position: absolute; top: 24px; right: 28px; font-size: 28px; cursor: pointer; color: #aaa; }
        .modal-content form { padding: 24px 28px; }
        .modal-content label { display: block; margin: 16px 0 8px; font-weight: 600; color: #444; }
        .password-wrapper { position: relative; }
        .modal-content input[type=password], .modal-content input[type=text].password-input {
            width: 100%; padding: 14px 44px 14px 16px; border: 1px solid #ddd; border-radius: 12px; font-size: 16px;
        }
        .eye-icon { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #888; font-size: 18px; }
        .btn-cancel, .btn-save { padding: 12px 24px; border: none; border-radius: 12px; font-weight: 600; cursor: pointer; margin-left: 10px; }
        .btn-cancel { background: #eee; color: #666; }
        .btn-save { background: var(--purple); color: white; }

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


        /* Pastikan toolbar disusun secara flex (sebaris) */
.fc-header-toolbar {
    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
    gap: 15px !important; /* Jarak antara butang dan tajuk */
}

/* Hilangkan margin default FullCalendar yang buat dia lari baris */
.fc-toolbar-chunk {
    display: flex !important;
    align-items: center !important;
}

/* Jarakkan sedikit tajuk bulan dari butang kiri/kanan */
.fc-toolbar-title {
    margin: 0 10px !important;
}

    </style>
</head>
<body>

<div class="sidebar">
    <div class="profile-section" id="profileDropdown">
        <div class="profile-avatar"><i class="fas fa-user"></i></div>
        <div class="profile-name"><?= htmlspecialchars($admin_name) ?></div>
        <div class="welcome-text">Selamat Datang Ke KVKaunsel Admin</div>
        <i class="fas fa-chevron-down profile-arrow"></i>
    </div>

    <div class="profile-menu" id="profileMenu">
        <div class="menu-item-profile" onclick="location.reload()"><i class="fas fa-sync-alt"></i><span>Segarkan Halaman</span></div>
        <div class="menu-item-profile" onclick="openChangePasswordModal()"><i class="fas fa-key"></i><span>Tukar Kata Laluan</span></div>
        <hr style="margin:10px 16px; border-color:rgba(255,255,255,0.1);">
        <div class="menu-item-profile" onclick="if(confirm('Log keluar dari sistem?')) location.href='logout.php'"><i class="fas fa-sign-out-alt"></i><span>Log Keluar</span></div>
    </div>

    <a href="KVK_Admin_CgMuhirman_Utama.php" class="menu-item <?= basename($_SERVER['PHP_SELF']) == 'KVK_Admin_CgMuhirman_Utama.php' ? 'active' : '' ?>"><i class="fas fa-home"></i><span>Laman Utama</span></a>
    <a href="KVK_Admin_CgMuhirman_Tempahan.php" class="menu-item <?= basename($_SERVER['PHP_SELF']) == 'KVK_Admin_CgMuhirman_Tempahan.php' ? 'active' : '' ?>"><i class="fas fa-book-open-reader"></i><span>Tempahan Pelajar</span></a>
    <a href="KVK_Admin_CgMuhirman_Temujanji.php" class="menu-item active"><i class="fas fa-calendar-check"></i><span>Temujanji</span></a>
    <a href="KVK_Admin_CgMuhirman_Laporan.php" class="menu-item <?= basename($_SERVER['PHP_SELF']) == 'KVK_Admin_CgMuhirman_Laporan.php' ? 'active' : '' ?>"><i class="fas fa-chart-line"></i><span>Laporan</span></a>
    <div class="pulse-text">Dapatkan Kod Jemputan Di Laman Utama!</div>
</div>

<div class="main">
    <div class="header">
        <div>
            <h1>Temujanji Kaunseling</h1>
            <p>• Menunjukkan jumlah sesi kaunseling anda hari ini!</p>
        </div>
        <div class="info">
            <div>Temujanji Hari Ini</div>
            <b><?= $today_count ?></b>
        </div>
    </div>
    <div id="calendar"></div>
</div>

<div id="passwordModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Tukar Kata Laluan</h3>
            <span class="close-modal" onclick="closePasswordModal()">&times;</span>
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

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/ms.js"></script>

<script>
// Fungsi Modal (Tukar Kata Laluan)
function openChangePasswordModal() {
    document.getElementById('passwordModal').style.display = 'flex';
    document.getElementById('profileMenu').style.display = 'none';
    document.getElementById('passwordMessage').innerHTML = '';
    document.getElementById('changePassForm').reset();
}
function closePasswordModal() { document.getElementById('passwordModal').style.display = 'none'; }
function togglePassword(icon, fieldName) {
    const input = document.querySelector(`input[name="${fieldName}"]`);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Dropdown Profile
    const profileDropdown = document.getElementById('profileDropdown');
    const profileMenu = document.getElementById('profileMenu');
    if (profileDropdown && profileMenu) {
        profileDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
            profileMenu.style.display = profileMenu.style.display === 'block' ? 'none' : 'block';
        });
        document.addEventListener('click', function(e) {
            if (!profileDropdown.contains(e.target)) profileMenu.style.display = 'none';
        });
    }

    // --- INISIALISASI KALENDAR (VERSI BERSIH) ---
    var calendarEl = document.getElementById('calendar');
    if (calendarEl) {
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            height: 'auto',
            locale: 'ms',
            headerToolbar: {
                left: '',
                center: 'prev title next', 
                right: ''
            },
            events: <?php echo json_encode($events); ?>,
            eventContent: function(arg) {
                let bellSpan = document.createElement('span');
                bellSpan.innerHTML = arg.event.title;
                return { domNodes: [bellSpan] };
            },
            eventDidMount: function(info) {
                info.el.title = info.event.extendedProps.nama + ' (' + info.event.extendedProps.waktu + ')';
            },
            eventClick: function(info) {
                alert(
                    '🔔 TEMUJANJI KAUNSELING\n---------------------------\n' +
                    'Nama: ' + info.event.extendedProps.nama + '\n' +
                    'Masa: ' + info.event.extendedProps.waktu + '\n' +
                    'Jenis: ' + info.event.extendedProps.jenis + '\n' +
                    'Program: ' + info.event.extendedProps.program_sem
                );
            }
        });
        calendar.render();
    }
});
</script>
</body>
</html>