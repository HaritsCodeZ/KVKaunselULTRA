<?php
session_start();

// Security: Only Tanita can access
if (!isset($_SESSION['kaunselor_id']) || ($_SESSION['counselor_full_name'] ?? '') !== 'Tanita Anak Numpang') {
    header("Location: UltimateLoginPage.php");
    exit;
}

$admin_name = $_SESSION['counselor_short_name'] ?? "Cg. Tanita";
$counselor_full_name = 'Tanita Anak Numpang'; // Exact DB match

// Database connection
try {
    $pdo = new PDO("mysql:host=localhost;dbname=kvkaunsel_db", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Sambungan pangkalan data gagal: " . $e->getMessage());
}

$approved_status = 'Selesai';

// Fetch approved bookings for Tanita only
$stmt = $pdo->prepare("
    SELECT id, nama, tarikh_masa, jenis_kaunseling, program, semester 
    FROM tempahan_kaunseling 
    WHERE kaunselor = :kaunselor AND status = :status
    ORDER BY tarikh_masa
");
$stmt->execute([':kaunselor' => $counselor_full_name, ':status' => $approved_status]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Today's approved appointments count for Tanita
$today_stmt = $pdo->prepare("
    SELECT COUNT(*) FROM tempahan_kaunseling 
    WHERE DATE(tarikh_masa) = CURDATE() 
      AND kaunselor = :kaunselor AND status = :status
");
$today_stmt->execute([':kaunselor' => $counselor_full_name, ':status' => $approved_status]);
$today_count = $today_stmt->fetchColumn();

// Build FullCalendar events
$events = [];
$colors = ['#ff9ff3', '#feca57', '#48dbfb', '#1dd1a1', '#54a0ff', '#ff6b6b', '#c8d6e5'];
$color_index = 0;

foreach ($bookings as $booking) {
    $start = $booking['tarikh_masa'];
    $end = date('Y-m-d H:i:s', strtotime($start . ' +1 hour'));
    $timeDisplay = date('h:i A', strtotime($start));

    $events[] = [
        'id' => $booking['id'],
        'title' => '',
        'start' => $start,
        'end' => $end,
        'backgroundColor' => $colors[$color_index % count($colors)],
        'borderColor' => $colors[$color_index % count($colors)],
        'textColor' => '#2d3436',
        'extendedProps' => [
            'nama' => $booking['nama'],
            'waktu' => $timeDisplay,
            'jenis' => $booking['jenis_kaunseling'] ?: 'Umum',
            'program_sem' => $booking['program'] . ' Sem ' . $booking['semester']
        ]
    ];
    $color_index++;
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KVKaunsel - Temujanji (Cg. Tanita)</title>
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

        .fc { font-family: 'Inter', sans-serif; }
        .fc-toolbar-title { font-size: 28px !important; font-weight: 700; color: var(--darkpurple); }
        .fc-button { background: var(--purple) !important; border: none !important; border-radius: 12px !important; padding: 10px 20px !important; font-weight: 600 !important; transition: all 0.3s ease !important; box-shadow: 0 4px 15px rgba(139,92,246,0.3) !important; }
        .fc-button:hover { background: var(--pink) !important; transform: translateY(-3px) !important; box-shadow: 0 10px 25px rgba(236,72,153,0.4) !important; }
        .fc-button.fc-today-button { background: #10b981 !important; color: white !important; }

        .fc-daygrid-day { transition: all 0.4s ease; border-radius: 12px; margin: 4px; background: #ffffff; }   
        .fc-daygrid-day:hover { transform: scale(1.05) translateY(-5px); box-shadow: 0 15px 35px rgba(139,92,246,0.2); z-index: 10; background: linear-gradient(135deg, #b43398ff, #ffffff); }

        .fc-day-today {
            background: linear-gradient(90deg, var(--purple), var(--pink), var(--purple)) !important;
            background-size: 200% 200% !important;
            animation: waveFlow 8s ease infinite !important;
            color: white !important; font-weight: bold; text-shadow: 0 2px 6px rgba(0,0,0,0.4);
            position: relative; overflow: hidden;
        }
        .fc-day-today::before {
            content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            animation: shine 8s ease infinite;
        }
        @keyframes waveFlow { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }
        @keyframes shine { 0% { left: -100%; } 50% { left: 100%; } 100% { left: 100%; } }

        .fc-daygrid-event {
            height: 10px !important;
            width: 10px !important;
            border-radius: 50% !important;
            margin: 3px 5px !important;
            padding: 0 !important;
            font-size: 0 !important;
            border: none !important;
        }

        .fc-daygrid-event-harness {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            padding-bottom: 4px;
        }

        .fc-daygrid-day.fc-day:has(.fc-event) {
            animation: pulseGlow 3s ease-in-out infinite;
        }
        @keyframes pulseGlow {
            0% { box-shadow: 0 0 0 0 rgba(139, 92, 246, 0.4); }
            70% { box-shadow: 0 0 0 12px rgba(139, 92, 246, 0); }
            100% { box-shadow: 0 0 0 0 rgba(139, 92, 246, 0); }
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

        .password-wrapper {
            position: relative;
        }
        .modal-content input[type=password],
        .modal-content input[type=text].password-input {
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

    <a href="KVK_Admin_CgTanita_Utama.php" class="menu-item <?= basename($_SERVER['PHP_SELF']) == 'KVK_Admin_CgTanita_Utama.php' ? 'active' : '' ?>">
        <i class="fas fa-home"></i><span>Laman Utama</span>
    </a>
    <a href="KVK_Admin_CgTanita_Tempahan.php" class="menu-item <?= basename($_SERVER['PHP_SELF']) == 'KVK_Admin_CgTanita_Tempahan.php' ? 'active' : '' ?>">
        <i class="fas fa-book-open-reader"></i><span>Tempahan Pelajar</span>
    </a>
    <a href="KVK_Admin_CgTanita_Temujanji.php" class="menu-item active">
        <i class="fas fa-calendar-check"></i><span>Temujanji</span>
    </a>
    <a href="KVK_Admin_CgTanita_Laporan.php" class="menu-item <?= basename($_SERVER['PHP_SELF']) == 'KVK_Admin_CgTanita_Laporan.php' ? 'active' : '' ?>">
        <i class="fas fa-chart-line"></i><span>Laporan</span>
    </a>
</div>

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

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/ms.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Profile dropdown
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

    // Password modal functions
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

    // FullCalendar
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: 'auto',
        locale: 'ms',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        buttonText: {
            today: 'Hari Ini',
            month: 'Bulan',
            week: 'Minggu',
            day: 'Hari'
        },
        events: <?= json_encode($events) ?>,

        eventDidMount: function(info) {
            info.el.title = info.event.extendedProps.nama + ' • ' + 
                            info.event.extendedProps.waktu + '\n' +
                            info.event.extendedProps.jenis + ' (' + 
                            info.event.extendedProps.program_sem + ')';
        },

        eventClick: function(info) {
            alert(
                'Pelajar: ' + info.event.extendedProps.nama + '\n' +
                'Masa: ' + info.event.extendedProps.waktu + '\n' +
                'Jenis: ' + info.event.extendedProps.jenis + '\n' +
                'Program: ' + info.event.extendedProps.program_sem
            );
        },

        dateClick: function(info) {
            var eventsOnDay = calendar.getEvents().filter(function(ev) {
                return ev.start && ev.start.toISOString().slice(0, 10) === info.dateStr;
            });

            if (eventsOnDay.length === 0) {
                alert('Tiada temujanji pada ' + info.dateStr);
                return;
            }

            var message = 'Temujanji pada ' + info.dateStr + ':\n\n';
            eventsOnDay.forEach(function(ev) {
                message += '• ' + ev.extendedProps.nama + '\n';
                message += '  Masa: ' + ev.extendedProps.waktu + '\n\n';
            });
            alert(message);
        }
    });

    calendar.render();
});
</script>

</body>
</html>