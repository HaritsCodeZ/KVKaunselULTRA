<?php
// Prevent this navbar from loading on the login page itself
if (basename($_SERVER['PHP_SELF']) === 'UltimateLoginPage.php') {
    return;
}
?>

<style>
    :root { 
        --accent: #8C5C8D; 
        --ease: cubic-bezier(0.4, 0, 0.2, 1); 
    }

    /* NAVBAR UTAMA */
    .navbar{
        position:fixed;
        top:20px;
        left:50%;
        transform:translateX(-50%);
        width:calc(100% - 80px);
        max-width:1000px;
        z-index:9999;
        padding:14px 32px;
        border-radius:50px;
        display:flex;
        justify-content:space-between;
        align-items:center;
        background:linear-gradient(180deg,rgba(255,255,255,0.92),rgba(255,255,255,0.86));
        box-shadow:0 12px 40px rgba(7,6,12,0.22);
        backdrop-filter:blur(12px) saturate(140%);
        transition:all 300ms var(--ease);
        font-family:'Inter',sans-serif;
    }
    .navbar.scrolled{
        top:12px;
        background:rgba(255,255,255,0.96);
        box-shadow:0 8px 28px rgba(7,6,12,0.18);
    }

    /* KIRI - LOGO + TEXT */
    .nav-left{
        cursor:pointer;
        display:flex;
        align-items:center;
        gap:12px;
    }
    .nav-left img{
        height:46px;
        border-radius:8px;
    }
    .logo-text{
        font-weight:800;
        font-size:24px;
        color:var(--accent);
        letter-spacing:-0.5px;
    }

    /* KANAN - MENU */
    .nav-right{
        display:flex;
        gap:16px;
        align-items:center;
    }

    /* CRUCIAL: For dropdown positioning */
    .nav-item {
        position: relative;
    }

    /* BUTTON DROPDOWN */
    .nav-btn{
        padding:10px 20px;
        border-radius:999px;
        font-weight:600;
        font-size:14.5px;
        color:var(--accent);
        background:white;
        border:1px solid rgba(140,92,141,0.08);
        box-shadow:0 4px 14px rgba(12,8,15,0.06);
        transition:all 200ms var(--ease);
        cursor:pointer;
        display:flex;
        align-items:center;
        gap:8px;
    }
    .nav-btn:hover{
        background:var(--accent);
        color:white;
        transform:translateY(-2px);
    }
    .chev{
        font-size:11px;
        transition:transform 200ms var(--ease);
        margin-left:4px;
    }
    .nav-item.open .chev{
        transform:rotate(180deg);
    }

    /* ICON BULAT (message & profile) */
    .icon-btn{
        position:relative;
        width:48px;
        height:48px;
        border-radius:50%;
        background:var(--accent);
        display:flex;
        align-items:center;
        justify-content:center;
        cursor:pointer;
        box-shadow:0 4px 16px rgba(140,92,141,0.35);
        transition:all 250ms var(--ease);
    }
    .icon-btn:hover{
        transform:scale(1.12);
        box-shadow:0 10px 30px rgba(140,92,141,0.5);
    }
    .icon-btn svg{
        width:26px;
        height:26px;
        stroke:white;
        stroke-width:2.3;
        fill:none;
    }

    .nav-item {
    position: relative;
}

    /* NOTIFICATION BADGE */
    .notification-badge{
        position:absolute;
        top:6px;
        right:6px;
        background:#ef4444;
        color:white;
        font-size:11px;
        font-weight:700;
        min-width:18px;
        height:18px;
        border-radius:50%;
        display:flex;
        align-items:center;
        justify-content:center;
        padding:0 5px;
        border:2px solid white;
        box-shadow:0 2px 8px rgba(0,0,0,0.2);
    }

    /* DROPDOWN - CENTERED DIRECTLY BELOW THE BUTTON */
    /* DROPDOWN - NOW PERFECTLY CENTERED BELOW THE BUTTON */
 .dropdown{
        position:absolute;top:56px;right:0;min-width:220px;background:white;border-radius:16px;
        box-shadow:0 16px 48px rgba(8,6,12,0.18);overflow:hidden;display:none;padding:8px 0;
        animation:pop 0.22s var(--ease) both;z-index:9999;
    }

.icon-btn ~ .dropdown {
    min-width: 200px;
}

    .dropdown a{
        display:block;
        padding:12px 20px;
        color:var(--accent);
        text-decoration:none;
        font-weight:600;
        transition:background 160ms var(--ease);
    }
    .dropdown a:hover{
        background:rgba(140,92,141,0.08);
    }
    .dropdown .gilran{
        padding:16px 20px;
        font-weight:800;
        font-size:15px;
        color:var(--accent);
        background:rgba(140,92,141,0.08);
        border-bottom:1px solid rgba(140,92,141,0.2);
    }
    .nav-item.open .dropdown{
        display:block;
    }

    @keyframes pop{
        from{opacity:0;transform:translateY(-8px) scale(0.96)}
        to{opacity:1;transform:none}
    }

    /* MOBILE RESPONSIVENESS */
    @media (max-width: 768px) {
        .navbar {
            width: calc(100% - 40px);
            padding: 12px 20px;
            gap: 10px;
        }
        .nav-right {
            gap: 12px;
        }
        .nav-btn {
            padding: 8px 16px;
            font-size: 14px;
        }
        .dropdown {
            left: 0;
            right: 0;
            transform: none;
            width: 100%;
            min-width: auto;
            top: 58px;
            border-radius: 0 0 16px 16px;
        }
    }
</style>

<header class="navbar" id="navbar">
    <div class="nav-left" onclick="location.href='KVK_Homepage.php'">
        <img src="ImageGalleries/KVKaunsel_Logo-New.png" alt="KVKaunsel Logo">
        <img src="ImageGalleries/LOGO_PRS_RELOADED.png" alt="PRS Logo">
        <div class="logo-text">KVKaunsel</div>
    </div>

    <div class="nav-right">
        <div class="nav-item">
            <div class="nav-btn" onclick="toggleDropdown(this)">Perkhidmatan Kami <span class="chev">▼</span></div>
            <div class="dropdown">
                <a href="#">Tempahan Sesi</a>
                <a href="#">Aktiviti Bulanan</a>
                <a href="#">TribbieAI - AI Kaunseling</a>
            </div>
        </div>
        
        <div class="nav-item">
            <div class="nav-btn" onclick="toggleDropdown(this)">Profil Kaunselor & PRS <span class="chev">▼</span></div>
            <div class="dropdown">
                <a href="KVK_ProfilKaunselor.php">Panel Kaunselor</a>
                <a href="#">Ahli Pembimbing Rakan Sebaya</a>
            </div>
        </div>

        <?php if(isset($_SESSION['student_id']) && !empty($_SESSION['student_id'])): 
            $sid = strtoupper($_SESSION['student_id']);
        ?>
            <!-- MESSAGE ICON - Direct link to messaging -->
            <a href="KVK_Student_Message.php" class="icon-btn" title="Ruang Mesej">
                <svg viewBox="0 0 24 24">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                    <polyline points="22,6 12,13 2,6"/>
                </svg>
                <!-- Optional badge - uncomment when you add unread count -->
                <!-- <span class="notification-badge">3</span> -->
            </a>

            <!-- PROFILE DROPDOWN -->
            <div class="nav-item">
                <div class="icon-btn" onclick="toggleDropdown(this)">
                    <svg viewBox="0 0 24 24">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                </div>
                <div class="dropdown">
                    <div class="gilran">Selamat Datang!<br><?= htmlspecialchars($sid) ?></div>
                    <hr style="margin:10px 0;border:none;border-top:1px solid #eee">
                    <a href="logout.php" style="color:#e74c3c">Log Keluar</a>
                </div>
            </div>

        <?php else: ?>
            <a class="nav-btn" href="UltimateLoginPage.php">Log Masuk / Daftar</a>
        <?php endif; ?>
    </div>
</header>

<script>
    function toggleDropdown(el) {
        const item = el.closest('.nav-item');
        const isOpen = item.classList.contains('open');
        
        // Close all other dropdowns
        document.querySelectorAll('.nav-item.open').forEach(openItem => {
            if (openItem !== item) openItem.classList.remove('open');
        });
        
        // Toggle current
        item.classList.toggle('open', !isOpen);
    }

    // Close when clicking outside
    document.addEventListener('click', e => {
        if (!e.target.closest('.nav-item')) {
            document.querySelectorAll('.nav-item.open').forEach(i => i.classList.remove('open'));
        }
    });

    // Scroll effect
    window.addEventListener('scroll', () => {
        const navbar = document.getElementById('navbar');
        if (navbar) {
            navbar.classList.toggle('scrolled', window.scrollY > 50);
        }
    });
</script>