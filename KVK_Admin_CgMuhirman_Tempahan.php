<?php
session_start();
$admin_name = "Cikgu Muhirman";

// Database connection
$pdo = new PDO("mysql:host=localhost;dbname=kvkaunsel_db", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Get filter from URL (?tahap=SVM or ?tahap=DVM)
$filter = $_GET['tahap'] ?? '';
$where = $filter && in_array($filter, ['SVM','DVM']) ? "WHERE tahap = ?" : "";
$sql = "SELECT * FROM tempahan_kaunseling $where ORDER BY tarikh_tempahan DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($filter ? [$filter] : []);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);  // ← This gives you all the bookings to display in the table
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin KVKaunsel - Tempahan Pelajar</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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

        /* FILTER BUTTON */
        .filter { margin-bottom:25px; display:flex; gap:12px; flex-wrap:wrap; }
        .filter a {
            padding:12px 24px; background:var(--purple); color:white; text-decoration:none;
            border-radius:12px; font-weight:600; transition:0.3s; box-shadow:0 4px 15px rgba(139,92,246,0.4);
        }
        .filter a:hover { transform:translateY(-3px); box-shadow:0 10px 25px rgba(139,92,246,0.5); }
        .filter a.active { background:#4c1d95; }

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

       /* MODAL - TWO COLUMN LAYOUT */
.modal {
    display: none;
    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0,0,0,0.7); backdrop-filter: blur(10px);
    align-items: center; justify-content: center; z-index: 1000;
    padding: 20px;
}
.modal-content {
    background: white; border-radius: 24px; width: 90%; max-width: 1200px;  /* ← widened from 1000px to 1200px */
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
    height: 850px;                    /* Standard ID card ratio */
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

/* Mobile fallback */
@media (max-width: 900px) {
    .modal-content { flex-direction: column; }
    .modal-left { width: 100%; height: 320px; border-right: none; border-bottom: 1px solid #eee; padding: 30px; }
    .modal-right { padding: 30px; }
    .modal-header { padding-right: 60px; }
}

/* Add these to your existing <style> block */
    @keyframes flashGreen {
        0%, 100% { background-color: transparent; }
        50% { background-color: #dcfce7; }
    }
    .flashing {
        animation: flashGreen 1.5s ease-in-out 2;
    }

    /* Confirmation overlay inside modal */
    .confirm-overlay {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(255,255,255,0.95);
        backdrop-filter: blur(8px);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        z-index: 10;
        border-radius: 24px;
        padding: 40px;
        text-align: center;
    }
    .confirm-overlay h3 {
        font-size: 26px;
        color: #166534;
        margin-bottom: 16px;
    }
    .confirm-overlay p {
        font-size: 18px;
        color: #444;
        margin-bottom: 24px;
    }
    .timer {
        font-size: 48px;
        font-weight: 800;
        color: var(--purple);
        margin-bottom: 20px;
    }
    .btn-confirm {
        background: #10b981;
        color: white;
        padding: 16px 40px;
        font-size: 18px;
        border-radius: 16px;
        border: none;
        cursor: pointer;
        box-shadow: 0 10px 30px rgba(16,185,129,0.3);
        transition: all 0.3s;
    }
    .btn-confirm:hover {
        background: #059669;
        transform: translateY(-4px);
    }

    /* Success notification */
    .success-notification {
        position: fixed;
        top: 30px;
        right: 30px;
        background: linear-gradient(135deg, #10b981, #34d399);
        color: white;
        padding: 24px 32px;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(16,185,129,0.4);
        z-index: 2000;
        display: flex;
        align-items: center;
        gap: 16px;
        font-size: 18px;
        font-weight: 600;
        animation: slideIn 0.5s ease, fadeOut 0.6s ease 3s forwards;
        opacity: 0;
    }
    .success-notification i {
        font-size: 36px;
    }
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes fadeOut {
        to { opacity: 0; transform: translateY(-20px); }
    }

/* Centered Confirmation Modal */
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
    color: #166534;
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
    background: #10b981;
    color: white;
    padding: 16px 48px;
    font-size: 20px;
    font-weight: 600;
    border: none;
    border-radius: 16px;
    cursor: pointer;
    box-shadow: 0 10px 30px rgba(16,185,129,0.4);
    transition: all 0.3s;
}
.btn-confirm-large:hover {
    background: #059669;
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(16,185,129,0.5);
}

@keyframes popIn {
    from { transform: scale(0.8); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}

/* Flash animation for row */
@keyframes flashGreen {
    0%, 100% { background-color: transparent; }
    50% { background-color: #dcfce7; }
}
.flashing {
    animation: flashGreen 1.5s ease-in-out 2;
}

/* Success notification (same as before) */
.success-notification {
    position: fixed;
    top: 30px;
    right: 30px;
    background: linear-gradient(135deg, #10b981, #34d399);
    color: white;
    padding: 24px 32px;
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(16,185,129,0.4);
    z-index: 2000;
    display: flex;
    align-items: center;
    gap: 16px;
    font-size: 18px;
    font-weight: 600;
    animation: slideIn 0.5s ease, fadeOut 0.6s ease 3s forwards;
    opacity: 0;
}
.success-notification i { font-size: 36px; }
@keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
@keyframes fadeOut { to { opacity: 0; transform: translateY(-20px); } }
    </style>
</head>
<body>

    <!-- SIDEBAR -->
    <div class="sidebar">
        <div class="logo"><i class="fas fa-heart-pulse"></i>KVKaunsel</div>
        
        <div class="menu-item" onclick="location.href='KVK_Admin_CgMuhirman_Utama.php'">
            <i class="fas fa-home"></i><span>Laman Utama</span>
        </div>
        
        <div class="menu-item active">
            <i class="fas fa-book-open-reader"></i><span>Tempahan Pelajar</span>
        </div>
        
        <div class="menu-item"><i class="fas fa-envelope"></i><span>Ruang Mesej</span></div>
        <div class="menu-item"><i class="fas fa-calendar-check"></i><span>Temujanji</span></div>
        <div class="menu-item"><i class="fas fa-chart-line"></i><span>Laporan</span></div>
        
        <div class="menu-item" style="margin-top:auto;padding-top:60px;" onclick="if(confirm('Log keluar?')) location.href='logout.php'">
            <i class="fas fa-sign-out-alt"></i><span>Log Keluar</span>
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
                <b>
                    <?php
                    $today = $pdo->query("SELECT COUNT(*) FROM tempahan_kaunseling WHERE DATE(tarikh_tempahan) = CURDATE()")->fetchColumn();
                    echo $today;
                    ?>
                </b>
            </div>
        </div>

        <!-- FILTER -->
        <div class="filter">
            <a href="?" class="<?= !$filter ? 'active' : '' ?>">Semua Tempahan</a>
            <a href="?tahap=SVM" class="<?= $filter=='SVM' ? 'active' : '' ?>">SVM Sahaja</a>
            <a href="?tahap=DVM" class="<?= $filter=='DVM' ? 'active' : '' ?>">DVM Sahaja</a>
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
                    <th>Sebab Ringkas</th>
                    <th>Status</th>
                    <th>Ditempah Pada</th>
                </tr>
            </thead>
            <tbody>
    <?php foreach($data as $i => $r): ?>
    <tr class="booking-row"
    data-id="<?= $r['id'] ?>"
    data-nama="<?= htmlspecialchars($r['nama'], ENT_QUOTES) ?>"
    data-tahap="<?= $r['tahap'] ?>"
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
    style="cursor:pointer">
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
        <td style="max-width:200px;white-space:normal;">
            <?= strlen($r['sebab']) > 60 ? substr(htmlspecialchars($r['sebab']),0,60).'...' : htmlspecialchars($r['sebab']) ?>
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
                <p>Tiada tempahan ditemui untuk filter ini.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- MODAL - TWO COLUMN WITH PASS IMAGE -->
<div id="bookingModal" class="modal">
    <div class="modal-content">
        <!-- LEFT: PASS IMAGE -->
        <div class="modal-left">
            <img id="passImage" src="" alt="Student Pass">
        </div>

        <!-- RIGHT: DETAILS -->
        <div class="modal-right">
            <div class="modal-header">
                <h2 id="modalTitle">Butiran Tempahan</h2>
                <span class="close-modal" onclick="closeModal()">&times;</span>
            </div>

            <div class="modal-body" id="modalBody">
                <!-- Details loaded via JS -->
            </div>

            <div class="modal-footer" id="modalFooter">
                <!-- Buttons loaded via JS -->
            </div>
        </div>
    </div>
</div>

<!-- Centered Confirmation Modal for Accept -->
<div id="confirmModal" class="confirm-modal">
    <div class="confirm-modal-content">
        <h3>Sahkan Penerimaan Tempahan?</h3>
        <p>Anda mempunyai masa untuk mengesahkan tindakan ini. Sekiranya tidak, biarkan masa berlalu pergi</p>
        <div class="timer-big">5</div>
        <button class="btn-confirm-large" id="finalConfirmBtn">
            <i class="fas fa-check"></i> Sahkan Sekali Lagi
        </button>
    </div>
</div>

<!-- Confetti CDN -->
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.2/dist/confetti.browser.min.js"></script>

    <script>
// Global variables
let confirmTimer = null;
let currentAcceptId = null;

// Attach click handlers (original)
document.querySelectorAll('.booking-row').forEach(row => {
    row.addEventListener('click', () => {
        openBookingModal(
            row.dataset.id,
            row.dataset.nama,
            row.dataset.tahap,
            row.dataset.program,
            row.dataset.semester,
            row.dataset.telefon,
            row.dataset.jantina,
            row.dataset.kaum,
            row.dataset.tarikh,
            row.dataset.jenis_sesi,
            row.dataset.jenis_kaunseling,
            row.dataset.kaunselor,
            row.dataset.sebab,
            row.dataset.status
        );
    });
});

// Open modal (your original logic, fully preserved)
function openBookingModal(id, nama, tahap, program, semester, telefon, jantina, kaum, tarikh_masa, jenis_sesi, jenis_kaunseling, kaunselor, sebab, status) {
    const body = document.getElementById('modalBody');
    const footer = document.getElementById('modalFooter');
    const passImg = document.getElementById('passImage');
    const title = document.getElementById('modalTitle');

    title.textContent = `Hi nama saya, ${nama}`;

    // Set correct pass image
    if (tahap === 'SVM') {
        passImg.src = 'ImageGalleries/SVM_PASS_X.jpg?' + new Date().getTime();
    } else if (tahap === 'DVM') {
        passImg.src = 'ImageGalleries/DVM_PASS_X.jpg?' + new Date().getTime();
    } else {
        passImg.src = 'ImageGalleries/default_pass.jpg';
    }

    // Clean detail layout (your original HTML)
    body.innerHTML = `
        <div class="detail-grid">
            <strong>Nama Pelajar</strong>    <div><b>${nama}</b></div>
            <strong>Tahap</strong>           <div><span class="badge ${tahap}">${tahap}</span></div>
            <strong>Program</strong>         <div>${program || '-'} / <b>${semester || '-'}</b></div>
            <strong>Telefon</strong>         <div>${telefon}</div>
            <strong>Jantina / Kaum</strong>   <div>${jantina} / ${kaum}</div>
            <strong>Tarikh & Masa</strong>   <div><b>${tarikh_masa}</b></div>
            <strong>Jenis Sesi</strong>      <div>${jenis_sesi}</div>
            <strong>Jenis Kaunseling</strong><div>${jenis_kaunseling || 'Tiada'}</div>
            <strong>Kaunselor</strong>       <div><b>${kaunselor || 'Belum Ditentukan'}</b></div>
            <strong>Sebab Penuh</strong>
            <div style="grid-column: 1 / -1; background:#f8f9ff; padding:20px; border-radius:12px; border-left:5px solid var(--purple); white-space: pre-wrap;">
                ${sebab || '<em style="color:#888;">Tiada sebab diberikan</em>'}
            </div>
            <strong>Status</strong>          <div><span class="badge ${status==='Baru'?'Baru':status}">${status}</span></div>
        </div>
    `;

    // Buttons (original decline + new accept flow)
    if (status === 'Baru') {
        footer.innerHTML = `
            <button class="btn btn-decline" onclick="updateStatus(${id}, 'Ditolak')">
                <i class="fas fa-times-circle"></i> Tolak
            </button>
            <button class="btn btn-accept" id="initAcceptBtn">
                <i class="fas fa-check-circle"></i> Terima
            </button>
        `;

        // New: Click "Terima" → show centered confirmation
        document.getElementById('initAcceptBtn').onclick = function(e) {
            e.stopPropagation();
            currentAcceptId = id;
            showCenteredConfirm();
        };

    } else {
        footer.innerHTML = `<p style="color:#666; font-size:15px; margin:0;">Status: <strong>${status}</strong> — sudah diproses</p>`;
    }

    document.getElementById('bookingModal').style.display = 'flex';
}

// New: Centered confirmation modal
function showCenteredConfirm() {
    const modal = document.getElementById('confirmModal');
    const timerEl = modal.querySelector('.timer-big');
    const confirmBtn = modal.querySelector('#finalConfirmBtn');

    let seconds = 5;
    timerEl.textContent = seconds;
    modal.classList.add('show');

    if (confirmTimer) clearInterval(confirmTimer);

    confirmTimer = setInterval(() => {
        seconds--;
        timerEl.textContent = seconds;
        if (seconds <= 0) {
            clearInterval(confirmTimer);
            modal.classList.remove('show');
        }
    }, 1000);

    confirmBtn.onclick = function() {
        clearInterval(confirmTimer);
        modal.classList.remove('show');
        if (currentAcceptId) proceedAccept(currentAcceptId);
    };
}

// Close confirmation on background click
document.getElementById('confirmModal').addEventListener('click', function(e) {
    if (e.target === this) {
        clearInterval(confirmTimer);
        this.classList.remove('show');
    }
});

// Original updateStatus (for decline)
function updateStatus(id, newStatus) {
    if (!confirm(`Adakah anda pasti ingin ${newStatus === 'Diterima' ? 'MENERIMA' : 'MENOLAK'} tempahan ini?`)) {
        return;
    }

    fetch('update_status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + id + '&status=' + encodeURIComponent(newStatus)
    })
    .then(response => response.text())
    .then(result => {
        if (result.trim() === 'success') {
            alert('Status berjaya dikemas kini!');
            closeModal();
            location.reload();
        } else {
            alert('Gagal mengemas kini status. Sila cuba lagi.');
        }
    })
    .catch(err => {
        alert('Ralat sambungan. Pastikan update_status.php wujud.');
    });
}

// New: Proceed with accept after confirmation
// New: Proceed with accept after confirmation → use 'Selesai'
function proceedAccept(id) {
    fetch('update_status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + id + '&status=Selesai'  // ← CHANGED TO 'Selesai'
    })
    .then(response => response.text())
    .then(result => {
        console.log('Server response:', result); // DEBUG: Check this in DevTools!
        if (result.trim() === 'success') {
            showSuccessNotification();

            const row = document.querySelector(`.booking-row[data-id="${id}"]`);
            if (row) {
                row.classList.add('flashing');
                // FIXED SELECTOR: Use last .badge in row
                const badge = row.querySelector('.badge:last-of-type');
                if (badge) {
                    badge.textContent = 'Selesai';  // ← Match DB
                    badge.className = 'badge Selesai';
                }
            }

            setTimeout(() => {
                closeModal();
                location.reload();
            }, 1800);
        } else {
            alert('Gagal mengemas kini status. Response: ' + result);
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        alert('Ralat sambungan ke update_status.php');
    });
}

// Also fix decline to use 'Dibatalkan'
function updateStatus(id, newStatus) {
    const dbStatus = newStatus === 'Ditolak' ? 'Dibatalkan' : newStatus;
    if (!confirm(`Adakah anda pasti ingin ${newStatus === 'Ditolak' ? 'MENOLAK' : 'MENERIMA'} tempahan ini?`)) {
        return;
    }

    fetch('update_status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + id + '&status=' + encodeURIComponent(dbStatus)
    })
    .then(response => response.text())
    .then(result => {
        if (result.trim() === 'success') {
            alert('Status berjaya dikemas kini!');
            closeModal();
            location.reload();
        } else {
            alert('Gagal mengemas kini status. Response: ' + result);
        }
    })
    .catch(err => {
        alert('Ralat sambungan. Pastikan update_status.php wujud.');
    });
}

// Success notification with confetti
function showSuccessNotification() {
    const notif = document.createElement('div');
    notif.className = 'success-notification';
    notif.innerHTML = `
        <i class="fas fa-check-circle"></i>
        <div>
            <div>Tempahan Berjaya Diterima! 🎉</div>
            <small>Pelajar akan dimaklumkan. Terima kasih Cikgu!</small>
        </div>
    `;
    document.body.appendChild(notif);

    confetti({ particleCount: 120, spread: 80, origin: { y: 0.6 } });

    // Auto-fade after 7 seconds
    setTimeout(() => {
        notif.classList.add('fade-out');
        setTimeout(() => notif.remove(), 600); // Remove after fade animation finishes
    }, 7000);
}

// Close modal (original + cleanup)
function closeModal() {
    if (confirmTimer) {
        clearInterval(confirmTimer);
        confirmTimer = null;
    }
    document.getElementById('bookingModal').style.display = 'none';
    document.getElementById('confirmModal').classList.remove('show');
}

// Close when clicking outside (original)
window.addEventListener('click', function(e) {
    const modal = document.getElementById('bookingModal');
    if (e.target === modal) {
        closeModal();
    }
});
</script>

</body>
</html>