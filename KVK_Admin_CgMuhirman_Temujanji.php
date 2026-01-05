<?php
session_start();
$admin_name = "Cikgu Muhirman";

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
$colors = ['#ff9ff3', '#feca57', '#48dbfb', '#1dd1a1', '#54a0ff', '#ff6b6b', '#c8d6e5'];
$color_index = 0;

foreach ($bookings as $booking) {
    $start = $booking['tarikh_masa'];
    $end = date('Y-m-d H:i:s', strtotime($start . ' +1 hour'));
    $timeDisplay = date('h:i A', strtotime($start));

    $events[] = [
        'id' => $booking['id'],
        'title' => '',  // ← No text → shows only colored dots
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
    <title>Admin KVKaunsel - Temujanji</title>
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

        /* CLEAN DOTS - like normal calendars */
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

        /* Pulsing glow on date cells with appointments */
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
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="logo"><i class="fas fa-heart-pulse"></i>KVKaunsel</div>
        
        <div class="menu-item" onclick="location.href='KVK_Admin_CgMuhirman_Utama.php'">
            <i class="fas fa-home"></i><span>Laman Utama</span>
        </div>
        
        <div class="menu-item" onclick="location.href='KVK_Admin_CgMuhirman_Tempahan.php'">
            <i class="fas fa-book-open-reader"></i><span>Tempahan Pelajar</span>
        </div>
        
        <div class="menu-item active">
            <i class="fas fa-calendar-check"></i><span>Temujanji</span>
        </div>
        
          <div class="menu-item" onclick="location.href='KVK_Admin_CgMuhirman_Laporan.php'">
            <i class="fas fa-chart-bar"></i><span>Laporan</span>
        </div>
        
        <div class="menu-item" style="margin-top:auto;padding-top:60px;" onclick="if(confirm('Log keluar?')) location.href='logout.php'">
            <i class="fas fa-sign-out-alt"></i><span>Log Keluar</span>
        </div>
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

        <?php if (empty($events)): ?>
            <div style="text-align:center; padding:40px; color:#666; font-size:18px;">
                Tiada temujanji yang diluluskan lagi.<br>
                Selepas anda meluluskan tempahan pelajar, ia akan muncul di sini secara automatik.
            </div>
        <?php endif; ?>

        <div id="calendar"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/ms.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
                    // Hover tooltip: full details
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