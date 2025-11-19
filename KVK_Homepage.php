<?php session_start(); ?>
<!doctype html>
<html lang="ms">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>KVKaunsel — Dapatkan Sokongan Kaunseling</title>

<!-- Calm Premium styles (all-in-one for easy paste) -->
<style>
  :root{
    --accent:#8c5c8d;
    --accent-2:#a26aa7;
    --bg-gradient: linear-gradient(180deg, rgba(140,92,141,0.16) 0%, rgba(48,29,41,0.5) 100%);
    --glass: rgba(255,255,255,0.7);
    --glass-2: rgba(255,255,255,0.06);
    --text:#0b0b0b;
    --muted: rgba(255,255,255,0.85);
    --radius-lg: 20px;
    --radius-xl: 28px;
    --ease: cubic-bezier(.2,.9,.25,1);
  }

  /* Reset & base */
  *{box-sizing:border-box}
  html,body{height:100%}
  body{
    margin:0;
    font-family:"Poppins", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
    -webkit-font-smoothing:antialiased;
    -moz-osx-font-smoothing:grayscale;
    color:var(--muted);
    overflow-x:hidden;
    background:var(--bg-gradient);
  }

  /* Floating navbar with glassmorphism */
  .navbar {
    position:fixed;
    top:20px;
    left:50%;
    transform:translateX(-50%);
    width:calc(100% - 80px);
    max-width:1280px;
    z-index:60;
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:12px;
    padding:12px 18px;
    border-radius:40px;
    background:linear-gradient(180deg, rgba(255,255,255,0.88), rgba(255,255,255,0.82));
    box-shadow:0 10px 30px rgba(7,6,12,0.18);
    transition:all 300ms var(--ease);
    backdrop-filter: blur(8px) saturate(120%);
  }

  .navbar.scrolled {
    transform: translateX(-50%) translateY(-6px) scale(0.995);
    box-shadow:0 6px 20px rgba(7,6,12,0.16);
    background: rgba(255,255,255,0.94);
  }

  .nav-left{display:flex;align-items:center;gap:12px;cursor:pointer}
  .nav-left img{height:44px;width:auto;display:block;transition:transform .25s var(--ease)}
  .nav-left .logo-text{font-weight:700;color:var(--accent);font-size:20px;letter-spacing:0.2px}

  /* Nav actions */
  .nav-right{display:flex;align-items:center;gap:12px}
  .nav-item{
    position:relative;
    display:inline-flex;
    align-items:center;
    gap:8px;
  }

  .nav-btn{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding:8px 16px;
    border-radius:999px;
    font-weight:600;
    font-size:14px;
    color:var(--accent);
    background:#fff;
    text-decoration:none;
    border:1px solid rgba(140,92,141,0.06);
    box-shadow:0 4px 12px rgba(12,8,15,0.04);
    transition:all 180ms var(--ease);
  }
  .nav-btn:hover{
    background:var(--accent);
    color:white;
    transform:translateY(-2px);
  }

  /* accessible dropdown */
  .dropdown-toggle{
    cursor:pointer;
    display:inline-flex;
    align-items:center;
    gap:8px;
  }

  .chev {
    font-size:11px;
    transform:translateY(0);
    transition:transform 180ms var(--ease);
    color:var(--accent);
  }

  /* dropdown menu */
  .dropdown {
    position:absolute;
    top:48px;
    right:0;
    min-width:220px;
    background:linear-gradient(180deg, #fff, #fbf8fb);
    border-radius:12px;
    box-shadow:0 14px 40px rgba(8,6,12,0.12);
    overflow:hidden;
    display:none;
    flex-direction:column;
    padding:6px;
    z-index:80;
  }

  .dropdown a{
    display:block;
    padding:11px 14px;
    color:var(--accent);
    text-decoration:none;
    font-weight:600;
    border-radius:10px;
    transition:background 160ms var(--ease);
    font-size:14px;
  }
  .dropdown a:hover, .dropdown a:focus{
    background:linear-gradient(90deg, rgba(140,92,141,0.06), rgba(162,106,167,0.06));
    outline:none;
  }

  /* show on hover / keyboard toggle - JS will also add .open */
  .nav-item.open .dropdown{ display:flex; animation:pop .18s var(--ease) both }
  .nav-item.open .chev{ transform: rotate(180deg) }

  @keyframes pop {
    from {opacity:0; transform: translateY(-6px) scale(.98)}
    to {opacity:1; transform: translateY(0) scale(1)}
  }

  /* HERO layout */
  .hero {
    width:100%;
    height:100vh;
    position:relative;
    display:flex;
    align-items:center;
    justify-content:center;
    z-index:1;
    margin-top:0;
  }

  /* background video */
  .hero__video {
    position:absolute;
    inset:0;
    width:100%;
    height:100%;
    object-fit:cover;
    filter:brightness(.78) saturate(.95);
    z-index:0;
  }

  /* subtle overlay gradient for depth */
  .hero__overlay {
    position:absolute;
    inset:0;
    background: linear-gradient(90deg, rgba(12,6,10,0.12) 0%, rgba(12,6,10,0.22) 70%);
    z-index:1;
    pointer-events:none;
  }

  /* content wrapper centered with max width */
  .container {
    position:relative;
    z-index:2;
    max-width:1280px;
    width:100%;
    padding: 64px 48px;
    display:flex;
    gap:40px;
    align-items:center;
  }

  /* left column (text) */
  .hero-left {
    width:55%;
    min-width:300px;
    color:var(--muted);
  }
  .kicker {
    display:inline-block;
    background: rgba(255,255,255,0.06);
    color:rgba(255,255,255,0.85);
    padding:6px 12px;
    border-radius:999px;
    margin-bottom:18px;
    font-weight:600;
    font-size:13px;
    box-shadow: inset 0 -1px 0 rgba(255,255,255,0.02);
  }

  h1.hero-title{
    margin:0 0 18px 0;
    font-size:60px;
    line-height:1.02;
    color:white;
    font-weight:800;
    text-shadow: 0 6px 24px rgba(8,6,12,0.35);
    letter-spacing:-0.6px;
  }
  p.lead{
    margin:0 0 28px 0;
    color:rgba(255,255,255,0.9);
    font-size:18px;
    max-width:680px;
    opacity:0.95;
  }

  .cta-row { display:flex; gap:16px; align-items:center; flex-wrap:wrap; }

  .btn-primary {
    padding:14px 22px;
    font-weight:700;
    border-radius:12px;
    background: linear-gradient(180deg, var(--accent), var(--accent-2));
    color:white;
    border: none;
    cursor:pointer;
    font-size:16px;
    box-shadow: 0 10px 30px rgba(140,92,141,0.18);
    transition: transform 220ms var(--ease), box-shadow 220ms var(--ease);
  }
  .btn-primary:hover { transform: translateY(-3px); box-shadow: 0 18px 42px rgba(140,92,141,0.22); }

  .btn-secondary {
    padding:12px 18px;
    background: transparent;
    border:1px solid rgba(255,255,255,0.12);
    color:rgba(255,255,255,0.92);
    border-radius:12px;
    font-weight:600;
    cursor:pointer;
  }

  /* right column image card */
  .hero-right {
    width:60%;
    display:flex;
    justify-content:flex-end;
    align-items:center;
    pointer-events:none;
  }

  .image-card {
    width:640px;
    max-width:95%;
    height:auto;
    border-radius:20px;
    overflow:hidden;
    box-shadow: 0 20px 50px rgba(8,6,12,0.45);
    transform:translateY(8px);
    background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));
    border: 1px solid rgba(255,255,255,0.04);
    pointer-events:auto;
    transition: transform 420ms var(--ease), box-shadow 420ms var(--ease);
  }

  .image-card img {
    width:100%;
    height:100%;
    object-fit:cover;
    display:block;
  }

  .image-card:hover { transform: translateY(-6px); box-shadow: 0 28px 70px rgba(8,6,12,0.5); }

  /* small responsive tweaks */
  @media (max-width:1100px){
    .container { padding:40px 24px; gap:28px }
    h1.hero-title { font-size:44px }
    .hero-left { width:58% }
    .hero-right { width:42% }
  }

  @media (max-width:860px){
    .navbar { width:calc(100% - 36px); padding:10px 12px }
    .container { flex-direction:column; align-items:flex-start; padding:36px 20px; gap:28px }
    .hero-left { width:100% }
    .hero-right { width:100%; justify-content:center }
    .image-card { max-width:540px; width:100%; border-radius:18px }
    h1.hero-title { font-size:34px }
    .kicker { display:none }
    .nav-right{gap:8px}
    .nav-btn{padding:8px 12px}
  }

  @media (max-width:520px){
    body{font-size:14px}
    .container{padding:28px 16px}
    h1.hero-title{font-size:28px}
    .image-card{border-radius:14px}
  }

  /* --- Tambahan untuk Animasi Butang Utama --- */
  @keyframes pulse-glow {
    0% {
      box-shadow: 0 10px 30px rgba(140,92,141,0.18), 0 0 0 0 rgba(140,92,141,0.4);
      transform: translateY(0);
    }
    50% {
      box-shadow: 0 10px 30px rgba(140,92,141,0.35), 0 0 0 10px rgba(140,92,141,0); /* Glow effect */
      transform: translateY(-1px);
    }
    100% {
      box-shadow: 0 10px 30px rgba(140,92,141,0.18), 0 0 0 0 rgba(140,92,141,0.4);
      transform: translateY(0);
    }
  }

  .btn-primary {
    /* ... (style yang sedia ada) */
    animation: pulse-glow 2.5s infinite ease-in-out; /* Tambah animasi denyutan */
  }

  .btn-primary:hover {
    /* ... (style yang sedia ada) */
    animation: none; /* Hentikan denyutan semasa hover untuk kesan yang lebih baik */
    transform: translateY(-3px); 
    box-shadow: 0 18px 42px rgba(140,92,141,0.22); 
  }
  /* ------------------------------------------- */
  /* focus styles for accessibility */
  a:focus, button:focus { outline: 3px solid rgba(140,92,141,0.16); outline-offset:3px; border-radius:8px; }

  /* small helper */
  .sr-only { position:absolute !important; width:1px; height:1px; padding:0; margin:-1px; overflow:hidden; clip:rect(0,0,0,0); white-space:nowrap; border:0; }
</style>
</head>
<body>

<!-- NAVBAR -->
<header class="navbar" role="navigation" aria-label="Primary navigation">
  <div class="nav-left" onclick="location.href='KVK_Homepage.php'" tabindex="0" role="link" aria-label="Home — KVKaunsel">
    <img src="ImageGalleries/KVKaunsel_Logo-New.png" alt="KVKaunsel logo">
    <div class="logo-text">KVKaunsel</div>
  </div>

  <nav class="nav-right" aria-label="Top menu">
    <!-- Perkhidmatan Kami -->
    <div class="nav-item" id="services-item">
      <div class="nav-btn dropdown-toggle" role="button" aria-haspopup="true" aria-expanded="false" tabindex="0">
        Perkhidmatan Kami <span class="chev">▾</span>
      </div>
      <div class="dropdown" role="menu" aria-label="Perkhidmatan Kami menu">
        <a href="#" role="menuitem">Tempahan Sesi</a>
        <a href="#" role="menuitem">Program Kaunseling</a>
        <a href="#" role="menuitem">Bantuan Kecemasan</a>
      </div>
    </div>

    <!-- Panel Kaunselor -->
    <div class="nav-item" id="panel-item">
      <div class="nav-btn dropdown-toggle" role="button" aria-haspopup="true" aria-expanded="false" tabindex="0">
        Panel Kaunselor <span class="chev">▾</span>
      </div>
      <div class="dropdown" role="menu" aria-label="Panel Kaunselor menu">
        <a href="#" role="menuitem">Senarai Kaunselor</a>
        <a href="#" role="menuitem">Bidang Kepakaran</a>
        <a href="#" role="menuitem">Temu Janji</a>
      </div>
    </div>

    <!-- Login -->
    <a class="nav-btn" href="UltimateLoginPage.php" role="button">Log Masuk / Daftar</a>
  </nav>
</header>

<!-- HERO -->
<main class="hero" role="main" aria-label="Hero">
  <!-- background video -->
  <video class="hero__video" autoplay muted loop playsinline poster="ImageGalleries/ImageCounseling1.jpg">
    <source src="VideoGalleries/KVKVideoHomePage.mp4" type="video/mp4">
    <!-- fallback: a static background if video fails -->
  </video>
  <div class="hero__overlay" aria-hidden="true"></div>

  <div class="container">
    <!-- Left: text & CTA -->
    <section class="hero-left" aria-labelledby="hero-heading">
      <div class="kicker">Kolej Vokasional Betong</div>
      <h1 id="hero-heading" class="hero-title">Sokongan Kaunseling Pelajar KV.<br>KVKAUNSEL!</h1>
      <p class="lead">Sistem tempahan sesi kaunseling untuk pelajar KV — cepat, selamat & profesional. Kami memahami; kami sedia membantu.</p>

      <div class="cta-row" role="region" aria-label="Actions">
        <button class="btn-primary" id="primary-cta">Tempah Sesi Kaunseling</button>
        <button class="btn-secondary" id="learn-more">Ketahui Lebih Lanjut</button>
      </div>
    </section>

    <!-- Right: professional image card -->
    <aside class="hero-right" aria-hidden="true">
      <div class="image-card" aria-hidden="true">
        <img src="ImageGalleries/ImageCounseling1.jpg" alt="Ilustrasi sesi kaunseling - seorang kaunselor berbincang dengan klien">
      </div>
    </aside>
  </div>
</main>

<!-- Minimal JS for interactions (dropdowns, nav scroll, keyboard accessible) -->
<script>
  (function(){
    // Navbar scroll effect
    const navbar = document.querySelector('.navbar');
    let lastScroll = 0;
    window.addEventListener('scroll', ()=>{
      const sc = window.scrollY;
      if (sc > 24) navbar.classList.add('scrolled'); else navbar.classList.remove('scrolled');
      lastScroll = sc;
    }, {passive:true});

    // Dropdown behaviour (toggle on click + keyboard accessible)
    const navItems = Array.from(document.querySelectorAll('.nav-item'));
    navItems.forEach(item=>{
      const toggle = item.querySelector('.dropdown-toggle');
      const menu = item.querySelector('.dropdown');

      // helper to close
      const closeMenu = ()=>{
        item.classList.remove('open');
        toggle.setAttribute('aria-expanded','false');
      };
      const openMenu = ()=>{
        item.classList.add('open');
        toggle.setAttribute('aria-expanded','true');
      };

      // click toggles
      toggle.addEventListener('click', (e)=>{
        e.stopPropagation();
        item.classList.contains('open') ? closeMenu() : openMenu();
      });

      // keyboard support on toggle (Enter/Space/ArrowDown)
      toggle.addEventListener('keydown', (e)=>{
        if (['Enter',' '].includes(e.key)) { e.preventDefault(); toggle.click(); }
        if (e.key === 'ArrowDown') { e.preventDefault(); openMenu(); menu.querySelector('a')?.focus(); }
      });

      // close when clicking outside
      document.addEventListener('click', (ev)=>{
        if (!item.contains(ev.target)) closeMenu();
      });

      // close on escape when menu open
      document.addEventListener('keydown', (ev)=>{
        if (ev.key === 'Escape') closeMenu();
      });
    });

    // CTA — example action (smooth scroll or open modal)
    document.getElementById('primary-cta').addEventListener('click', ()=>{
      // scroll to booking section (if exists) or show UX hint
      const booking = document.querySelector('#booking') || null;
      if (booking) booking.scrollIntoView({behavior:'smooth'});
      else {
        // small visual feedback
        const btn = document.getElementById('primary-cta');
        btn.animate([{transform:'translateY(0)'},{transform:'translateY(-4px)'},{transform:'translateY(0)'}], {duration:360, easing:'cubic-bezier(.2,.9,.25,1)'});
      }
    });
  })();
</script>

</body>
</html>
