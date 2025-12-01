<?php session_start(); ?>
<!doctype html>
<html lang="ms">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>KVKaunsel — Dapatkan Sokongan Kaunseling</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800;900&display=swap" rel="stylesheet">

<style>
  :root{
    --accent:#8c5c8d;
    --accent-2:#a26aa7;
    --purple-glow:#c89bc9;
    --text:#ffffff;
    --muted:rgba(255,255,255,0.92);
    --ease:cubic-bezier(.2,.9,.25,1);
  }
  *{box-sizing:border-box;margin:0;padding:0}
  html,body{height:100%;font-family:"Poppins",sans-serif;color:var(--text);overflow-x:hidden}
  
  /* Video Background */
  .hero__video{position:fixed;top:0;left:0;width:100%;height:100%;object-fit:cover;z-index:-2;filter:brightness(1.4) saturate(1.1)}
  .content-overlay{position:fixed;inset:0;background:linear-gradient(135deg,rgba(60,20,90,0.68)0%,rgba(100,40,120,0.52)60%,rgba(80,30,100,0.45)100%);z-index:-1}

  /* Navbar */
  .navbar{
    position:fixed;top:20px;left:50%;transform:translateX(-50%);
    width:calc(100% - 80px);max-width:1280px;z-index:60;
    padding:14px 24px;border-radius:50px;display:flex;justify-content:space-between;align-items:center;gap:16px;
    background:linear-gradient(180deg,rgba(255,255,255,0.92),rgba(255,255,255,0.86));
    box-shadow:0 12px 40px rgba(7,6,12,0.22);backdrop-filter:blur(12px) saturate(140%);
    transition:all 300ms var(--ease);
  }
  .navbar.scrolled{top:12px;background:rgba(255,255,255,0.96);box-shadow:0 8px 28px rgba(7,6,12,0.18)}
  .nav-left{cursor:pointer;display:flex;align-items:center;gap:10px}
  .nav-left img{height:48px}
  .logo-text{font-weight:800;font-size:22px;color:var(--accent);letter-spacing:-0.3px}

  .nav-right{display:flex;gap:12px;align-items:center}
  .nav-item{position:relative}
  .nav-btn{
    padding:10px 20px;border-radius:999px;font-weight:600;font-size:14.5px;
    color:var(--accent);background:white;border:1px solid rgba(140,92,141,0.08);
    box-shadow:0 4px 14px rgba(12,8,15,0.06);transition:all 200ms var(--ease);cursor:pointer;
    display:flex;align-items:center;gap:8px;
  }
  .nav-btn:hover{background:var(--accent);color:white;transform:translateY(-2px)}
  .chev{font-size:11px;transition:transform 200ms var(--ease)}
  .nav-item.open .chev{transform:rotate(180deg)}

  .dropdown{
    position:absolute;top:52px;right:0;min-width:220px;background:white;border-radius:16px;
    box-shadow:0 16px 48px rgba(8,6,12,0.18);overflow:hidden;display:none;padding:8px 0;
    animation:pop 0.22s var(--ease) both;
  }
  .dropdown a{
    display:block;padding:12px 20px;color:var(--accent);text-decoration:none;font-weight:600;
    transition:background 160ms var(--ease);
  }
  .dropdown a:hover{background:rgba(140,92,141,0.08)}
  .nav-item.open .dropdown{display:block}

  @keyframes pop{from{opacity:0;transform:translateY(-8px) scale(0.96)}to{opacity:1;transform:none}}

  /* Hero */
  .hero{min-height:100vh;display:flex;align-items:center;padding:0 40px;position:relative}
  .container{max-width:1400px;margin:0 auto;display:grid;grid-template-columns:1fr 1.1fr;gap:6rem;align-items:center}

  .hero-left h1{font-size:4.5rem;line-height:1.02;margin-bottom:1.4rem;letter-spacing:-2.2px;font-weight:900}
  .hero-left h1 span{
    display:block;font-size:7.2rem;
    background:linear-gradient(90deg,#ffffff,#e9d5ff,#ffffff);
    -webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;
  }
  .kicker{
    display:inline-block;background:rgba(255,255,255,0.15);padding:8px 16px;border-radius:999px;
    font-weight:700;font-size:14px;margin-bottom:20px;backdrop-filter:blur(4px);
  }
  .lead{font-size:1.38rem;line-height:1.7;margin-bottom:2.8rem;opacity:0.96;max-width:660px}
  .cta-row{display:flex;gap:1.8rem;flex-wrap:wrap}
  .btn-primary {
  position: relative;
  padding: 1.4rem 3.2rem;
  font-size: 1.25rem;
  font-weight: 800;
  border-radius: 60px;
  background: linear-gradient(135deg, #7f3ca8, #b84592, #ff6b6b);
  background-size: 200% 200%;
  color: white;
  border: none;
  cursor: pointer;
  overflow: hidden;
  z-index: 1;
  transition: all 0.4s ease;
  text-transform: uppercase;
  letter-spacing: 1px;
}

/* Gradient wave yang gerak perlahan (WOW FACTOR) */
.btn-primary::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  background: linear-gradient(90deg, 
    transparent, 
    rgba(255,255,255,0.3), 
    transparent
  );
  background-size: 200% 100%;
  animation: wave 4s linear infinite;
  z-index: -1;
}

/* Hover effect gila */
.btn-primary:hover {
  transform: translateY(-8px);
  box-shadow: 0 25px 60px rgba(183, 69, 146, 0.5);
}

.btn-primary:active {
  transform: translateY(-4px);
}

  .btn-secondary{
    padding:1.1rem 2.4rem;font-size:1.15rem;background:transparent;
    border:2.5px solid rgba(255,255,255,0.7);color:white;border-radius:60px;font-weight:600;
    transition:all 300ms var(--ease);
  }
  .btn-secondary:hover{background:rgba(255,255,255,0.14)}

  .trust{margin-top:3.5rem;font-size:1.2rem;opacity:0.92}
  .trust strong{color:#e9d5ff;font-weight:800}

  .image-card{
  border-radius:32px;
  overflow:hidden;
  box-shadow:0 30px 80px rgba(0,0,0,0.55);
  transition:all 0.6s var(--ease);

  /* NI YANG KAU UBAH — tambah lebar */
  width: 680px;          /* ← ubah nilai ni je (contoh: 680px, 720px, 750px) */
  max-width: 95%;        /* biar tak melimpah kat phone */
}
  .image-card img{
  width:100%;
  height:479px;           /* ubah nilai ni je */
  object-fit:cover;       /* penting! biar tak stretch */
  display:block;
}
.hero-right {
  transform: translateY(-40px);     /* naik = nilai negatif */
  /* atau */
  /* transform: translateY(40px); */   /* turun = nilai positif */
}
  .college-name{
  position:absolute;
  transform: translateY(559px);     /* naik = nilai negatif */
  right:275px;
  background:rgba(0,0,0,0.4);
  backdrop-filter:blur(10px);
  padding:10px 20px;
  border-radius:50px;
  font-weight:700;
  font-size:15px;
  color:white;
  z-index:10;
  box-shadow:0 8px 32px rgba(0,0,0,0.3);
}
@media (max-width:768px){
  .college-name{
    position:relative;
    bottom:auto; right:auto;
    margin-top:30px;
    text-align:center;
    width:fit-content;
    margin-left:auto;
    margin-right:auto;
  }
}
  /* Responsive */
  @media (max-width:992px){
    .container{grid-template-columns:1fr;text-align:center;gap:4rem}
    .hero-left h1{font-size:4.2rem}
    .hero-left h1 span{font-size:5.4rem}
    .cta-row{justify-content:center}
  }
  @media (max-width:600px){
    .hero-left h1{font-size:3.4rem}
    .hero-left h1 span{font-size:4.4rem}
    .hero{padding:0 20px}
    .navbar{width:calc(100% - 32px);padding:12px 16px}
  }

  /* Wave animation */
@keyframes wave {
  0%   { background-position: -200% 0; }
  100% { background-position: 200% 0; }
}
</style>
</head>
<body>

  <!-- Video Background -->
  <video class="hero__video" autoplay muted loop playsinline poster="ImageGalleries/ImageCounseling1.jpg">
    <source src="VideoGalleries/KVKVideoHomePage.mp4" type="video/mp4">
  </video>
  <div class="content-overlay"></div>

  <!-- Navbar -->
  <header class="navbar">
    <div class="nav-left" onclick="location.href='KVK_Homepage.php'">
      <img src="ImageGalleries/KVKaunsel_Logo-New.png" alt="KVKaunsel logo">
      <div class="logo-text">KVKaunsel</div>
    </div>

    <nav class="nav-right">
      <div class="nav-item" id="services-item">
        <div class="nav-btn dropdown-toggle">Perkhidmatan Kami <span class="chev">▼</span></div>
        <div class="dropdown">
          <a href="#">Tempahan Sesi</a>
          <a href="#">Aktiviti Bulanan</a>
          <a href="#">TribbieAI - AI Kaunseling</a>
        </div>
      </div>

      <div class="nav-item" id="panel-item">
        <div class="nav-btn dropdown-toggle">Profil Kaunselor & PRS <span class="chev">▼</span></div>
        <div class="dropdown">
          <a href="KVK_ProfilKaunselor.php">Panel Kaunselor</a>
          <a href="#">Ahli Pembimbing Rakan Sebaya</a>
        </div>
      </div>

      <a class="nav-btn" href="UltimateLoginPage.php">Log Masuk / Daftar</a>
    </nav>
  </header>

  <!-- Hero -->
  <main class="hero">
    <div class="container">
      <section class="hero-left">
        <div class="college-name">
            Bilik Kaunseling Kolej Vokasional Betong
        </div>
        <br><br>
        <br><br>  
        <h1>Sistem<br>Kaunseling<br>Pelajar KV<br><span>KVKAUNSEL!</span></h1>
        <p class="lead">
          Tempah sesi kaunseling dengan cepat, mudah dan selamat. Kami faham cabaran anda dan disitulah KVKaunsel sedia membantu anda!
        </p>

        <div class="cta-row">
          <a href="UltimateLoginPage.php">
    <button class="btn-primary">Tempah Sesi Sekarang</button>
            </a>
          <button class="btn-secondary">Kenali Ahli Kumpulan</button>
        </div>
        <div class="trust">
          UNIT PSIKOLOGI DAN KERJAYA<br>
          <strong>— Dihasilkan oleh Kumpulan 5</strong> • Diploma Teknologi Maklumat
        </div>
      </section>

      <div class="hero-right">
        <div class="image-card">
          <img src="ImageGalleries/ImageCounseling1.jpg" alt="Sesi kaunseling penuh empati">
        </div>
      </div>
    </div>
  </main>

  <!-- Full working dropdown + scroll JS -->
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const navbar = document.querySelector('.navbar');
      window.addEventListener('scroll', () => {
        navbar.classList.toggle('scrolled', window.scrollY > 30);
      });

      document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
        toggle.addEventListener('click', (e) => {
          e.stopPropagation();
          const item = toggle.parentElement;
          const isOpen = item.classList.toggle('open');
          document.querySelectorAll('.nav-item').forEach(i => {
            if (i !== item) i.classList.remove('open');
          });
        });
      });

      document.addEventListener('click', () => {
        document.querySelectorAll('.nav-item').forEach(item => item.classList.remove('open'));
      });
    });
  </script>
</body>
</html>