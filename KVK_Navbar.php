<?php
if (basename($_SERVER['PHP_SELF']) === 'UltimateLoginPage.php') {
    return;
}
?>

<style>
    :root { --accent: #8C5C8D; --ease: cubic-bezier(0.4, 0, 0.2, 1); }

    .navbar{
        position:fixed;top:20px;left:50%;transform:translateX(-50%);
        width:calc(100% - 80px);max-width:900px;z-index:9999;
        padding:14px 32px;border-radius:50px;display:flex;justify-content:space-between;align-items:center;
        background:linear-gradient(180deg,rgba(255,255,255,0.92),rgba(255,255,255,0.86));
        box-shadow:0 12px 40px rgba(7,6,12,0.22);backdrop-filter:blur(12px) saturate(140%);
        transition:all 300ms var(--ease);font-family:'Poppins',sans-serif;
    }
    .navbar.scrolled{top:12px;background:rgba(255,255,255,0.96);box-shadow:0 8px 28px rgba(7,6,12,0.18)}

    .nav-left{cursor:pointer;display:flex;align-items:center;gap:12px}
    .nav-left img{height:46px;border-radius:8px}
    .logo-text{font-weight:800;font-size:24px;color:var(--accent);letter-spacing:-0.5px}

    .nav-right{display:flex;gap:16px;align-items:center}
    .nav-item{position:relative} /* ← WAJIB ADA NI! */

    .icon-btn{
        width:48px;height:48px;border-radius:50%;background:var(--accent);
        display:flex;align-items:center;justify-content:center;cursor:pointer;
        box-shadow:0 4px 16px rgba(140,92,141,0.35);transition:all 250ms var(--ease);
    }
    .icon-btn:hover{transform:scale(1.12);box-shadow:0 10px 30px rgba(140,92,141,0.5);}
    .icon-btn svg{width:26px;height:26px;stroke:white;stroke-width:2.3;fill:none;}

    /* DROPDOWN */
    .dropdown{
        position:absolute;top:62px;right:0;min-width:280px;background:white;border-radius:18px;
        box-shadow:0 20px 50px rgba(8,6,12,0.25);overflow:hidden;display:none;
        animation:pop 0.25s var(--ease) both;z-index:9999;
    }
    .dropdown a{display:block;padding:14px 24px;color:var(--accent);text-decoration:none;font-weight:600;transition:0.2s}
    .dropdown a:hover{background:rgba(140,92,141,0.1);}
    .dropdown .gilran{padding:16px 24px;font-weight:800;font-size:16px;color:var(--accent);
        background:rgba(140,92,141,0.08);border-bottom:1px solid rgba(140,92,141,0.2);}
    .nav-item.open .dropdown{display:block;}
    @keyframes pop{from{opacity:0;transform:translateY(-10px) scale(0.95)}to{opacity:1;transform:none}}
</style>

<header class="navbar" id="navbar">
    <div class="nav-left" onclick="location.href='KVK_Homepage.php'">
        <img src="ImageGalleries/KVKaunsel_Logo-New.png" alt="KVKaunsel">
        <div class="logo-text">KVKaunsel</div>
    </div>

    <div class="nav-right">
        <?php if(isset($_SESSION['student_id']) && !empty($_SESSION['student_id'])): 
            $sid = strtoupper($_SESSION['student_id']);
        ?>
            <!-- MESSAGE ICON -->
            <a href="messages.php" class="icon-btn" title="Mesej">
                <svg viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            </a>

            <!-- USER ICON + DROPDOWN (YANG INI KAU KENA KLIK!) -->
            <div class="nav-item"> <!-- ← WAJIB ADA CLASS nav-item DI SINI -->
                <div class="icon-btn" onclick="toggleDropdown(this)">
                    <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <div class="dropdown">
                    <div class="gilran">Selamat Datang!<br><?=$sid?></div>
                    <hr style="margin:10px 0">
                    <a href="logout.php" style="color:#e74c3c">Log Keluar</a>
                </div>
            </div>

        <?php else: ?>
            <!-- GUEST -->
            <div class="nav-item">
                <div class="nav-btn dropdown-toggle" onclick="toggleDropdown(this)">Perkhidmatan Kami Down Arrow</div>
                <div class="dropdown">
                    <a href="booking.php">Tempahan Sesi</a>
                    <a href="#">Program Kaunseling</a>
                    <a href="#">Bantuan Kecemasan</a>
                </div>
            </div>
            <div class="nav-item">
                <div class="nav-btn dropdown-toggle" onclick="toggleDropdown(this)">Profil Kaunselor & PRS Down Arrow</div>
                <div class="dropdown">
                    <a href="#">Panel Kaunselor</a>
                    <a href="#">Ahli Pembimbing Rakan Sebaya</a>
                </div>
            </div>
            <a class="nav-btn" href="UltimateLoginPage.php">Log Masuk / Daftar</a>
        <?php endif; ?>
    </div>
</header>

<script>
    function toggleDropdown(el) {
        const item = el.closest('.nav-item');
        const isOpen = item.classList.contains('open');
        // tutup semua dulu
        document.querySelectorAll('.nav-item.open').forEach(i => i.classList.remove('open'));
        // kalau belum open, buka
        if (!isOpen) item.classList.add('open');
    }

    // klik luar → tutup dropdown
    document.addEventListener('click', e => {
        if (!e.target.closest('.nav-item')) {
            document.querySelectorAll('.nav-item.open').forEach(i => i.classList.remove('open'));
        }
    });

    // scroll effect
    window.addEventListener('scroll', () => {
        document.getElementById('navbar')?.classList.toggle('scrolled', window.scrollY > 50);
    });
</script>