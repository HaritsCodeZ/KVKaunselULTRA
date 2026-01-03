<?php
session_start();

if (!isset($_SESSION['student_id'])) {
    header("Location: UltimateLoginPage.php");
    exit;
}

$student_id = strtoupper(trim($_SESSION['student_id']));

$pdo = new PDO("mysql:host=localhost;dbname=kvkaunsel_db", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$counselors = [
    "Encik Muhirman Bin Mu Alim" => "Cikgu Muhirman",
    "Tanita Anak Numpang" => "Cikgu Tanita",
    "Whilemina Thimah Gregory Anak Jimbun" => "Cikgu Whilemina"
];

$selected_counselor_full = $_GET['counselor'] ?? $_POST['counselor'] ?? array_keys($counselors)[0];
if (!array_key_exists($selected_counselor_full, $counselors)) {
    $selected_counselor_full = array_keys($counselors)[0];
}
$selected_counselor_short = $counselors[$selected_counselor_full];

// Send message - now strictly to the selected counselor only
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $message = trim($_POST['message']);
    if ($message !== '') {
        // Only use booking for THIS specific counselor
        $check = $pdo->prepare("SELECT id FROM tempahan_kaunseling WHERE student_id = ? AND kaunselor = ? AND status = 'Selesai' LIMIT 1");
        $check->execute([$student_id, $selected_counselor_full]);
        $booking_id = $check->fetchColumn() ?: 0;

        $insert = $pdo->prepare("INSERT INTO messages (booking_id, sender, sender_name, message) VALUES (?, 'student', ?, ?)");
        $insert->execute([$booking_id, $student_id, $message]);
    }
    header("Location: KVK_Student_Message.php?counselor=" . urlencode($selected_counselor_full));
    exit;
}

// Clear messages - only for the selected counselor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear_messages'])) {
    $delete = $pdo->prepare("
        DELETE FROM messages 
        WHERE booking_id IN (
            SELECT id FROM tempahan_kaunseling 
            WHERE student_id = ? AND kaunselor = ?
        )
        OR (booking_id = 0 AND sender_name = ? AND message IN (
            SELECT message FROM messages m2
            LEFT JOIN tempahan_kaunseling t2 ON m2.booking_id = t2.id
            WHERE (t2.kaunselor = ? OR m2.booking_id = 0)
        ))
    ");
    $delete->execute([$student_id, $selected_counselor_full, $student_id, $selected_counselor_full]);

    header("Location: KVK_Student_Message.php?counselor=" . urlencode($selected_counselor_full));
    exit;
}

// Fetch messages - ONLY for the currently selected counselor
$messages = [];
$msg_stmt = $pdo->prepare("
    SELECT m.sender, m.sender_name, m.message, m.sent_at
    FROM messages m
    LEFT JOIN tempahan_kaunseling t ON m.booking_id = t.id
    WHERE (t.student_id = ? AND t.kaunselor = ? AND t.status = 'Selesai')
       OR (m.booking_id = 0 AND m.sender_name = ?)
    ORDER BY m.sent_at ASC
");
$msg_stmt->execute([$student_id, $selected_counselor_full, $student_id]);
$messages = $msg_stmt->fetchAll(PDO::FETCH_ASSOC);

$has_conversation = !empty($messages);
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ruang Mesej Kaunseling</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --primary: #8c5690;
            --primary-dark: #af74b1;
            --bubble: #e0f7fa;
            --text: #006064;
            --timestamp: #666;
            --success: #10b981;
            --card-shadow: 0 20px 50px rgba(99,102,241,0.15);
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        html, body { height:100%; font-family:'Poppins',sans-serif;
            background:url('ImageGalleries/KVK_ChatBackground.png') center/cover fixed;
        }
        body { display:flex; }

        /* COUNSELORS PANEL */
        .counselors-panel {
            width:380px; background:rgba(255,255,255,0.92); backdrop-filter:blur(12px);
            border-radius:28px; overflow:hidden; box-shadow:var(--card-shadow);
            display:flex; flex-direction:column; margin:40px 0 40px 40px;
        }
        .counselors-header {
            background:linear-gradient(135deg,var(--primary),var(--primary-dark));
            color:white; padding:45px; text-align:center; font-size:24px; font-weight:700;
            border-radius:28px 28px 0 0;
        }
        .counselors-list { flex:1; overflow-y:auto; }
        .counselor-item {
            padding:28px 24px; cursor:pointer; transition:0.3s;
            display:flex; align-items:center; gap:20px;
            border-bottom:1px solid rgba(241,245,249,0.7);
        }
        .counselor-item:hover { background:rgba(240,245,255,0.6); }
        .counselor-item.active {
            background:rgba(140,86,144,0.1); border-left:6px solid var(--primary);
        }
        .counselor-avatar {
            width:64px; height:64px; background:var(--primary); color:white; border-radius:50%;
            display:flex; align-items:center; justify-content:center; font-size:32px; font-weight:700;
        }
        .counselor-details h3 { font-size:19px; color:var(--primary-dark); font-weight:600; }
        .counselor-details p { color:#64748b; font-size:15px; margin-top:4px; }

        /* MAIN CHAT */
        .main-chat {
            flex:1; display:flex; flex-direction:column; margin:40px 40px 40px 30px;
        }
        .chat-panel {
            flex:1; background:rgba(255,255,255,0.92); backdrop-filter:blur(12px);
            border-radius:28px; overflow:hidden; box-shadow:var(--card-shadow);
            display:flex; flex-direction:column;
        }
        .chat-header {
            background:linear-gradient(135deg,var(--primary-dark),var(--primary));
            color:white; padding:24px 32px; text-align:center; position:relative;
        }
        .chat-header h2 { font-size:28px; font-weight:700; }
        .chat-header p { font-size:17px; opacity:0.9; margin-top:8px; }

        .clear-btn-modern {
            position:absolute; right:32px; top:50%; transform:translateY(-50%);
            background:rgba(255,255,255,0.3); backdrop-filter:blur(8px);
            color:white; border:2px solid rgba(255,255,255,0.5);
            padding:10px 20px; border-radius:30px; font-size:15px; font-weight:600;
            cursor:pointer; transition:0.3s; display:flex; align-items:center; gap:8px;
        }
        .clear-btn-modern:hover {
            background:rgba(255,255,255,0.5); transform:translateY(-50%) scale(1.05);
        }

        .messages-container {
            flex:1; padding:30px; overflow-y:auto; display:flex; flex-direction:column; gap:20px;
            background:transparent;
        }
        .empty-messages {
            flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center;
            color:#64748b; text-align:center;
        }
        .empty-messages i { font-size:80px; margin-bottom:24px; opacity:0.4; color:var(--primary); animation:float 5s infinite; }
        @keyframes float { 0%,100% { transform:translateY(0); } 50% { transform:translateY(-20px); } }

        /* Messages - Light cyan bubbles */
        .message {
            max-width:70%; padding:16px 24px; border-radius:28px;
            box-shadow:0 4px 15px rgba(0,0,0,0.08); position:relative;
        }
        .message.admin {
            align-self:flex-start; background:var(--bubble); color:var(--text);
            border-bottom-left-radius:8px;
        }
        .message.student {
            align-self:flex-end; background:var(--bubble); color:var(--text);
            border-bottom-right-radius:8px;
        }
        .message-content { font-size:16px; margin-bottom:8px; }
        .message-time {
            font-size:12px; color:var(--timestamp); text-align:right;
        }

        .input-section {
            padding:28px; background:rgba(255,255,255,0.95);
        }
        .input-group {
            display:flex; gap:16px; align-items:center;
        }
        .input-group textarea {
            flex:1; padding:18px 24px; border:2px solid var(--primary); border-radius:40px;
            font-size:16px; resize:none; height:60px; background:white;
            transition:all 0.3s;
        }
        .input-group textarea:focus { outline:none; box-shadow:0 0 0 4px rgba(140,86,144,0.2); }
        .input-group button {
            width:60px; height:60px; background:var(--success); color:white;
            border:none; border-radius:50%; font-size:26px; cursor:pointer;
            box-shadow:0 8px 25px rgba(16,185,129,0.3);
        }
        .input-group button:hover { transform:scale(1.1); }

        /* Clear Modal */
        .clear-modal {
            display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); backdrop-filter:blur(14px);
            align-items:center; justify-content:center; z-index:1000;
        }
        .clear-modal-content {
            background:white; border-radius:36px; width:90%; max-width:540px;
            box-shadow:0 40px 100px rgba(0,0,0,0.25); animation:riseUp 0.6s cubic-bezier(0.16,1,0.3,1);
            border:1px solid rgba(140,86,144,0.08); text-align:center; padding:48px 40px;
        }
        @keyframes riseUp {
            from { transform:translateY(60px) scale(0.95); opacity:0; }
            to { transform:translateY(0) scale(1); opacity:1; }
        }
        .clear-modal-icon-circle {
            width:120px; height:120px; background:linear-gradient(135deg,#f3e8ff,#e9d5ff);
            border-radius:50%; display:flex; align-items:center; justify-content:center;
            margin:0 auto 32px; box-shadow:0 16px 40px rgba(140,86,144,0.25); border:6px solid white;
        }
        .clear-modal-icon-circle i { font-size:56px; color:var(--primary); }
        .clear-modal-content h3 { font-size:30px; font-weight:700; color:#1e1b4b; margin-bottom:24px; }
        .warning-text { font-size:18px; color:#374151; line-height:1.7; margin-bottom:24px; font-weight:500; }
        .comfort-text {
            font-size:17px; color:var(--primary); line-height:1.8; font-weight:500;
            background:linear-gradient(135deg,#fdf4ff,#faf5ff); padding:24px;
            border-radius:20px; border-left:5px solid var(--primary);
            box-shadow:0 8px 25px rgba(140,86,144,0.08); margin-bottom:40px;
        }
        .clear-modal-footer {
            display:flex; gap:20px; justify-content:center;
        }
        .btn-soft-cancel {
            flex:1; max-width:200px; padding:18px 32px; background:#f8fafc; color:#64748b;
            border:2px solid #e2e8f0; border-radius:24px; font-size:17px; font-weight:600;
            cursor:pointer; transition:all 0.4s ease; display:flex; align-items:center; justify-content:center; gap:12px;
        }
        .btn-soft-cancel:hover {
            background:#e2e8f0; transform:translateY(-4px); box-shadow:0 12px 30px rgba(0,0,0,0.1);
        }
        .btn-confirm-clear {
            flex:1; max-width:240px; padding:18px 40px;
            background:linear-gradient(135deg,#dc2626,#b91c1c); color:white; border:none;
            border-radius:24px; font-size:17px; font-weight:600; cursor:pointer;
            transition:all 0.4s ease; display:flex; align-items:center; justify-content:center; gap:12px;
            box-shadow:0 12px 35px rgba(220,38,38,0.35);
        }
        .btn-confirm-clear:hover {
            transform:translateY(-5px); box-shadow:0 20px 45px rgba(220,38,38,0.45);
        }

        @media (max-width:1100px) {
            body { flex-direction:column; }
            .counselors-panel { width:100%; margin:40px 40px 20px; border-radius:20px; }
            .main-chat { margin:0 40px 40px; }
        }
    </style>
</head>
<body>

    <!-- COUNSELORS PANEL -->
    <div class="counselors-panel">
        <div class="counselors-header">Pilih Kaunselor</div>
        <div class="counselors-list">
            <?php foreach ($counselors as $full => $short): ?>
            <div class="counselor-item <?= $selected_counselor_full === $full ? 'active' : '' ?>"
                 onclick="location.href='KVK_Student_Message.php?counselor=<?= urlencode($full) ?>'">
                <div class="counselor-avatar"><?= strtoupper(substr($short, 6, 1)) ?></div>
                <div class="counselor-details">
                    <h3><?= htmlspecialchars($short) ?></h3>
                    <p>Sedia membantu anda</p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- CHAT AREA -->
    <div class="main-chat">
        <div class="chat-panel">
            <div class="chat-header">
                <h2>Berbual dengan <?= htmlspecialchars($selected_counselor_short) ?></h2>
                <p>Perbualan sulit dan selamat</p>

                <?php if ($has_conversation): ?>
                <button class="clear-btn-modern" onclick="showClearModal()">
                    <i class="fas fa-broom"></i>
                    <span>Bersihkan Perbualan</span>
                </button>
                <?php endif; ?>
            </div>

            <div class="messages-container" id="messagesContainer">
                <?php if (empty($messages)): ?>
                    <div class="empty-messages">
                        <i class="fas fa-comment-dots"></i>
                        <p>
                            Belum ada mesej lagi.<br><br>
                            Mulakan perbualan dengan menghantar mesej di bawah. <?= htmlspecialchars($selected_counselor_short) ?> akan membalas anda secepat mungkin.
                        </p>
                    </div>
                <?php else: ?>
                    <?php foreach ($messages as $i => $msg): ?>
                        <div class="message <?= $msg['sender'] === 'admin' ? 'admin' : 'student' ?>">
                            <div class="message-content">
                                <?= nl2br(htmlspecialchars($msg['message'])) ?>
                            </div>
                            <div class="message-time">
                                <?= date('d M Y, h:i A', strtotime($msg['sent_at'])) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="input-section">
                <form method="POST" class="input-group">
                    <input type="hidden" name="send_message" value="1">
                    <input type="hidden" name="counselor" value="<?= htmlspecialchars($selected_counselor_full) ?>">
                    <textarea name="message" placeholder="Taip mesej anda kepada <?= htmlspecialchars($selected_counselor_short) ?>..." required autofocus></textarea>
                    <button type="submit"><i class="fas fa-paper-plane"></i></button>
                </form>
            </div>
        </div>

        <!-- Hidden clear form -->
        <form id="clearForm" method="POST" style="display:none;">
            <input type="hidden" name="clear_messages" value="1">
            <input type="hidden" name="counselor" value="<?= htmlspecialchars($selected_counselor_full) ?>">
        </form>
    </div>

    <!-- Clear Confirmation Modal -->
    <div id="clearModal" class="clear-modal">
        <div class="clear-modal-content">
            <div class="clear-modal-icon-circle">
                <i class="fas fa-broom"></i>
            </div>
            <h3>Bersihkan Perbualan?</h3>
            <p class="warning-text">
                Semua mesej dengan <strong><?= htmlspecialchars($selected_counselor_short) ?></strong> akan dihapuskan secara kekal.
            </p>
            <p class="comfort-text">
                <i class="fas fa-heart"></i> Jangan risau — anda sentiasa boleh memulakan perbualan baru bila-bila masa yang anda rasa selesa.<br>
                Kami di sini untuk anda, setiap saat. 💜
            </p>
            <div class="clear-modal-footer">
                <button class="btn-soft-cancel" onclick="hideClearModal()">
                    <i class="fas fa-arrow-left"></i> Kembali
                </button>
                <button class="btn-confirm-clear" onclick="document.getElementById('clearForm').submit()">
                    <i class="fas fa-check-circle"></i> Ya, Bersihkan
                </button>
            </div>
        </div>
    </div>

    <script>
        function showClearModal() {
            document.getElementById('clearModal').style.display = 'flex';
        }
        function hideClearModal() {
            document.getElementById('clearModal').style.display = 'none';
        }
        window.onclick = function(e) {
            if (e.target === document.getElementById('clearModal')) hideClearModal();
        }

        const container = document.getElementById('messagesContainer');
        if (container) container.scrollTop = container.scrollHeight;

        <?php if ($has_conversation): ?>
        setInterval(() => location.reload(), 30000);
        <?php endif; ?>

        const textarea = document.querySelector('textarea');
        if (textarea) {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = this.scrollHeight + 'px';
            });
        }
    </script>

</body>
</html>