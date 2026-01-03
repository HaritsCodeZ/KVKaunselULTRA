<?php
session_start();
$admin_name = "Cikgu Muhirman";

// Database connection
try {
    $pdo = new PDO("mysql:host=localhost;dbname=kvkaunsel_db", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// ==================== HANDLE ACTIONS ====================

// Delete entire conversation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_conversation') {
    $booking_id = (int)$_POST['booking_id'];

    if ($booking_id > 0) {
        $stmt = $pdo->prepare("DELETE FROM messages WHERE booking_id = ?");
        $stmt->execute([$booking_id]);
    }

    header("Location: KVK_Admin_CgMuhirman_Mesej.php");
    exit;
}

// Send message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'send') {
    $booking_id = (int)$_POST['booking_id'];
    $message = trim($_POST['message'] ?? '');

    if ($booking_id > 0 && $message !== '') {
        $stmt = $pdo->prepare("INSERT INTO messages (booking_id, sender, message) VALUES (?, 'admin', ?)");
        $stmt->execute([$booking_id, $message]);
    }
    header("Location: KVK_Admin_CgMuhirman_Mesej.php?booking_id=$booking_id");
    exit;
}

// Get selected booking ID
$selected_booking_id = (int)($_GET['booking_id'] ?? 0);

// ==================== FETCH BOOKINGS LIST ====================
$sql = "SELECT DISTINCT t.id, t.nama, t.tahap, t.program, t.semester, t.tarikh_masa
        FROM tempahan_kaunseling t
        LEFT JOIN messages m ON t.id = m.booking_id
        WHERE t.status = 'Selesai' OR m.id IS NOT NULL
        ORDER BY t.tarikh_masa DESC";

$bookings = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

// ==================== FETCH CURRENT CONVERSATION ====================
$messages = [];
$student_name = "Pilih Pelajar";
$student_info = "";

if ($selected_booking_id > 0) {
    $stmt = $pdo->prepare("SELECT nama, tahap, program, semester FROM tempahan_kaunseling WHERE id = ? AND status = 'Selesai'");
    $stmt->execute([$selected_booking_id]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($booking) {
        $student_name = $booking['nama'];
        $student_info = $booking['tahap'] . " • " . $booking['program'] . " Sem " . $booking['semester'];
    }

    $stmt = $pdo->prepare("SELECT id, sender, message, sent_at FROM messages WHERE booking_id = ? ORDER BY sent_at ASC");
    $stmt->execute([$selected_booking_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ruang Mesej - KVKaunsel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --purple: #8b5cf6;
            --pink: #ec4899;
            --darkpurple: #6b21a8;
            --light: #f8f9ff;
            --green: #10b981;
            --gray: #f1f5f9;
            --red: #dc2626;
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: 'Inter', sans-serif; background:var(--light); display:flex; min-height:100vh; color:#333; }

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
        .main { margin-left:280px; width:calc(100% - 280px); padding:40px; display:flex; gap:30px; }

        /* Conversations List */
        .conversations {
            width: 360px; background:white; border-radius:20px; box-shadow:0 10px 40px rgba(0,0,0,0.08);
            overflow:hidden; display:flex; flex-direction:column;
        }
        .conv-header {
            background: var(--darkpurple); color:white; padding:20px; text-align:center; font-size:20px; font-weight:700;
        }
        .conv-list { flex:1; overflow-y:auto; }
        .conv-item {
            padding:16px 20px; border-bottom:1px solid #eee; transition:0.3s;
            display: flex; justify-content: space-between; align-items: center;
        }
        .conv-item:hover { background:#f8f9ff; }
        .conv-item.active { background:var(--purple); color:white; }
        .conv-item.active .conv-info b,
        .conv-item.active .conv-info small { color:white; opacity:1; }
        .conv-item.active .delete-btn i { color:white; }
        .conv-info { flex:1; cursor:pointer; }
        .conv-info b { display:block; font-size:16px; font-weight:600; }
        .conv-info small { font-size:13px; opacity:0.8; }
        .delete-btn {
            cursor:pointer; font-size:18px; color:#999; padding:8px; border-radius:8px; transition:0.2s;
        }
        .delete-btn:hover {
            background:#fee2e2; color:var(--red);
        }

        /* Chat Area */
        .chat-area {
            flex:1; background:white; border-radius:20px; box-shadow:0 10px 40px rgba(0,0,0,0.08);
            display:flex; flex-direction:column; overflow:hidden;
        }
        .chat-header {
            background: linear-gradient(135deg, var(--purple), var(--pink));
            color:white; padding:20px; text-align:center;
        }
        .chat-header h2 { font-size:22px; font-weight:700; margin:0; }
        .chat-header small { opacity:0.9; font-size:14px; display:block; margin-top:4px; }

        .messages-area {
            flex:1; padding:20px; overflow-y:auto; background:#fafbff;
            display:flex; flex-direction:column; gap:12px;
        }
        .message {
            max-width:70%; padding:12px 18px; border-radius:18px; line-height:1.5;
            position:relative; word-wrap:break-word;
        }
        .message.admin {
            align-self: flex-end; background:var(--purple); color:white;
            border-bottom-right-radius:4px;
        }
        .message.student {
            align-self: flex-start; background:var(--gray); color:#333;
            border-bottom-left-radius:4px;
        }
        .message time {
            display:block; font-size:11px; opacity:0.7; margin-top:6px; text-align:right;
        }

        .empty-chat {
            flex:1; display:flex; align-items:center; justify-content:center;
            text-align:center; color:#aaa;
        }
        .empty-chat i {
            font-size:100px; opacity:0.3; margin-bottom:20px;
        }
        .empty-chat p {
            font-size:16px;
        }

        .input-area {
            padding:20px; background:#f8f9ff; border-top:1px solid #eee;
        }
        .input-group {
            display:flex; gap:12px;
        }
        .input-group textarea {
            flex:1; padding:14px 18px; border-radius:16px; border:1px solid #ddd;
            font-family:inherit; font-size:15px; resize:none; height:56px;
        }
        .input-group textarea:focus {
            outline:none; border-color:var(--purple); box-shadow:0 0 0 3px rgba(139,92,246,0.2);
        }
        .input-group button {
            width:56px; height:56px; background:var(--green); color:white;
            border:none; border-radius:50%; cursor:pointer; font-size:20px; transition:0.3s;
        }
        .input-group button:hover { background:#059669; transform:scale(1.05); }

        .no-chat {
            flex:1; display:flex; align-items:center; justify-content:center; color:#888;
            flex-direction:column; text-align:center;
        }
        .no-chat i { font-size:80px; margin-bottom:20px; opacity:0.3; }
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
        
        <div class="menu-item active">
            <i class="fas fa-envelope"></i><span>Ruang Mesej</span>
        </div>
        
        <div class="menu-item"><i class="fas fa-calendar-check"></i><span>Temujanji</span></div>
        <div class="menu-item"><i class="fas fa-chart-line"></i><span>Laporan</span></div>
        
        <div class="menu-item" style="margin-top:auto;padding-top:60px;" onclick="if(confirm('Log keluar?')) location.href='logout.php'">
            <i class="fas fa-sign-out-alt"></i><span>Log Keluar</span>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main">
        <!-- Conversations List -->
        <div class="conversations">
            <div class="conv-header">Perbualan Kaunseling</div>
            <div class="conv-list">
                <?php if (!empty($bookings)): ?>
                    <?php foreach($bookings as $b): ?>
                    <div class="conv-item <?= $b['id'] == $selected_booking_id ? 'active' : '' ?>">
                        <div class="conv-info" onclick="location.href='KVK_Admin_CgMuhirman_Mesej.php?booking_id=<?= $b['id'] ?>'">
                            <b><?= htmlspecialchars($b['nama']) ?></b>
                            <small><?= $b['tahap'] ?> • <?= date('d/m/Y h:i A', strtotime($b['tarikh_masa'])) ?></small>
                        </div>
                        <form method="POST" onsubmit="return confirm('Padam keseluruhan perbualan dengan <?= htmlspecialchars($b['nama']) ?>? Semua mesej akan dipadam dan tidak boleh dikembalikan.');" style="margin:0;">
                            <input type="hidden" name="action" value="delete_conversation">
                            <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                            <button type="submit" class="delete-btn" title="Padam Perbualan">
                                <i class="fas fa-trash-can"></i>
                            </button>
                        </form>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="padding:60px 20px; text-align:center; color:#999;">
                        <i class="fas fa-comment-medical" style="font-size:60px; margin-bottom:20px; opacity:0.4;"></i>
                        <p>Tiada perbualan lagi.</p>
                        <small>Terima tempahan pelajar untuk mulakan sesi kaunseling.</small>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Chat Area -->
        <div class="chat-area">
            <?php if ($selected_booking_id > 0 && $booking): ?>
                <div class="chat-header">
                    <h2><?= htmlspecialchars($student_name) ?></h2>
                    <small><?= htmlspecialchars($student_info) ?></small>
                </div>

                <div class="messages-area" id="messagesArea">
                    <?php if (!empty($messages)): ?>
                        <?php foreach($messages as $msg): ?>
                            <div class="message <?= $msg['sender'] ?>">
                                <?= nl2br(htmlspecialchars($msg['message'])) ?>
                                <time><?= date('d/m h:i A', strtotime($msg['sent_at'])) ?></time>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-chat">
                            <i class="fas fa-comment-slash"></i>
                            <p>Tiada mesej lagi dalam perbualan ini.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="input-area">
                    <form method="POST" class="input-group">
                        <input type="hidden" name="action" value="send">
                        <input type="hidden" name="booking_id" value="<?= $selected_booking_id ?>">
                        <textarea name="message" placeholder="Taip mesej anda kepada pelajar..." required autofocus></textarea>
                        <button type="submit" title="Hantar">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <div class="no-chat">
                    <i class="fas fa-comments"></i>
                    <h3>Selamat Datang ke Ruang Mesej</h3>
                    <p>Pilih nama pelajar di sebelah kiri untuk melihat atau memulakan perbualan kaunseling.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Auto-scroll to bottom when there are messages
        const messagesArea = document.getElementById('messagesArea');
        if (messagesArea && !messagesArea.querySelector('.empty-chat')) {
            messagesArea.scrollTop = messagesArea.scrollHeight;
        }

        // Auto-refresh every 30 seconds for new messages
        <?php if ($selected_booking_id > 0): ?>
        setInterval(() => {
            fetch(window.location.href)
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newArea = doc.getElementById('messagesArea');
                    if (newArea && newArea.innerHTML !== messagesArea.innerHTML) {
                        messagesArea.innerHTML = newArea.innerHTML;
                        messagesArea.scrollTop = messagesArea.scrollHeight;
                    }
                });
        }, 30000);
        <?php endif; ?>
    </script>
</body>
</html>