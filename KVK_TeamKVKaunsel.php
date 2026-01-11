<?php
// KVK_TeamKVKaunsel.php
// About Our Team page with FIXED VIDEO background + Content Overlay
?>
<?php include 'KVK_Navbar.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kenali Sistem | KVKaunsel</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --primary: #9d50bb;       /* Vibrant Violet */
            --secondary: #6e48aa;     /* Deep Purple */
            --accent: #c084fc;        /* Soft Lavender accent */
            --dark-bg: #120c18;       /* Deep Obsidian background */
            --card-bg: rgba(255, 255, 255, 0.06); /* Slightly more visible glass */
            --glass-border: rgba(255, 255, 255, 0.1);
            --text-main: #f8f5ff;
            --text-muted: #f1f1fcff;
            --gradient: linear-gradient(135deg, #6e48aa, #9d50bb);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--dark-bg);
            color: var(--text-main);
            overflow-x: hidden;
            line-height: 1.6;
        }

        /* ================= FIXED VIDEO BACKGROUND ================= */
        .hero-video {
            position: fixed; /* STAYS IN PLACE */
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -2; /* Behind everything */
            opacity: 0.8;
        }

        .hero-overlay {
            position: fixed; /* STAYS IN PLACE over video */
            inset: 0;
            /* Control strength here: Adjust the 0.4 and 0.8 to darken/lighten */
            background: linear-gradient(to bottom, rgba(18, 12, 24, 0.4), rgba(18, 12, 24, 0.85));
            z-index: -1;
        }

        /* ================= HERO SECTION (SCROLLABLE) ================= */
        .hero {
            height: 100vh;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            background: transparent; /* Must be transparent to see fixed video */
        }

        .hero-content {
            max-width: 900px;
            padding: 0 30px;
        }

        .hero h1 {
            font-size: 5.5rem;
            font-weight: 800;
            margin-bottom: 20px;
            background: linear-gradient(to bottom, #fff, #c084fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -2px;
        }

        .hero p {
            font-size: 1.3rem;
            color: var(--text-muted);
            max-width: 700px;
            margin: 0 auto 40px;
            text-shadow: 0 4px 10px rgba(0,0,0,0.3);
        }

        /* ================= SCROLL ARROW ================= */
        .scroll-down {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            color: white;
            text-decoration: none;
            animation: bounce 2s infinite;
            opacity: 0.8;
            transition: opacity 0.3s;
        }

        .scroll-down i { font-size: 2.5rem; color: var(--accent); }

        /* ================= TEAM SECTION (SCROLLABLE) ================= */
        .team-section {
            position: relative;
            padding: 120px 5% 150px;
            background: transparent; /* Lets video show through */
            z-index: 1;
        }

        .section-title {
            text-align: center;
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 80px;
            color: white;
            text-shadow: 0 5px 15px rgba(0,0,0,0.5);
        }

        .supervisor-card {
            max-width: 450px;
            margin: 0 auto 100px;
            background: var(--card-bg);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(15px); /* Frosty glass effect */
            border-radius: 30px;
            overflow: hidden;
            transition: transform 0.4s ease;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 40px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .member-card {
            background: var(--card-bg);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(15px);
            border-radius: 25px;
            overflow: hidden;
            transition: all 0.4s ease;
        }

        .member-card:hover, .supervisor-card:hover {
            transform: translateY(-15px);
            border-color: rgba(192, 132, 252, 0.5);
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
        }

        .member-photo {
            height: 400px;
            position: relative;
        }

        /* Fade photo into card background */
        .member-photo::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 40%;
            background: linear-gradient(to top, rgba(18, 12, 24, 0.8), transparent);
        }

        .member-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .member-info {
            padding: 40px 30px;
            text-align: center;
        }

        .member-name {
            font-size: 1.7rem;
            font-weight: 700;
            color: white;
            margin-bottom: 8px;
        }

        .member-role {
            font-size: 1rem;
            color: var(--accent);
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .member-bio {
            font-size: 0.95rem;
            color: var(--text-muted);
            margin-bottom: 30px;
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .social-links a {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            transition: all 0.3s ease;
            border: 1px solid var(--glass-border);
            text-decoration: none;
        }

        .social-links a:hover {
            background: var(--primary);
            color: white;
            transform: scale(1.1);
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateX(-50%) translateY(0); }
            40% { transform: translateX(-50%) translateY(-10px); }
            60% { transform: translateX(-50%) translateY(-5px); }
        }

        @media (max-width: 768px) {
            .hero h1 { font-size: 3.2rem; }
            .hero p { font-size: 1.1rem; }
            .section-title { font-size: 2.5rem; }
        }
    </style>
</head>
<body>

<video class="hero-video" autoplay muted loop playsinline preload="metadata" poster="images/hero-fallback.jpg">
    <source src="VideoGalleries/KVK_TeamLove.mp4" type="video/mp4">
</video>
<div class="hero-overlay"></div>

<section class="hero" id="hero">
    <div class="hero-content">
        <h1>Kenali Sistem</h1>
        <p>
            Sistem KVKaunsel dibangunkan bertujuan untuk memudahkan sistem tempahan sesi kaunseling secara dalam talian atau secara bersemuka. 
            Pelajar dapat membuat tempahan dengan lebih efisien melalui platform digital kami.
        </p>
    </div>

    <a href="#team-section" class="scroll-down">
        <i class="fas fa-chevron-down"></i>
    </a>
</section>

<section class="team-section" id="team-section">
    <h2 class="section-title">Kenali Pembangun Sistem K5</h2>

    <div class="supervisor-card">
        <div class="member-photo">
            <img src="Team/CikguFlorina.jpeg" alt="Cik Florina Binti Junus">
        </div>
        <div class="member-info">
            <div class="member-name">Cik Florina Binti Junus</div>
            <div class="member-role">Penyelia Projek</div>
            <p class="member-bio">
                Membimbing kumpulan sepanjang pembangunan sistem, memastikan visi yang jelas untuk kejayaan KVKaunsel.
            </p>
            
        </div>
    </div>

    <div class="team-grid">


    <div class="member-card">
            <div class="member-photo">
                <img src="Team/cahaya.jpeg" alt="Nur Cahaya Az-Zahra">
            </div>
            <div class="member-info">
                <div class="member-name">Nur Cahaya Az-Zahra Binti Suaidie</div>
                <div class="member-role">Technical Writer</div>
                <p class="member-bio">
                    Menyediakan dokumentasi teknikal yang jelas dan teratur seperti laporan sistem, dan dokumentasi projek bagi memudahkan pemahaman pengguna dan pembangun.
                </p>
                <div class="social-links">
                    <a href="https://wa.me/601121446026" target="_blank"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
        </div>

        <div class="member-card">
            <div class="member-photo">
                <img src="Team/passport.jpg" alt="Muhammad Harits Fikri Bin Hassan">
            </div>
            <div class="member-info">
                <div class="member-name">Muhammad Harits Fikri Bin Hassan</div>
                <div class="member-role">Full Stack Developer (KetuaKumpulan)</div>
                <p class="member-bio">
                    Bertanggungjawab membangunkan dan menyelenggara sistem dari segi front-end dan back-end, termasuk reka bentuk antaramuka, logik sistem, serta pengurusan pangkalan data.
                </p>
                <div class="social-links">
                    <a href="https://wa.me/60198102571" target="_blank"><i class="fab fa-whatsapp"></i></a>     
                </div>
            </div>
        </div>

        

        <div class="member-card">
            <div class="member-photo">
                <img src="team/student2.jpg" alt="SHarfik">
            </div>
            <div class="member-info">
                <div class="member-name">Mohd Sharfik Bin Hamid</div>
                <div class="member-role">Asisstant Technical Writer</div>
                <p class="member-bio">
                    Membantu Technical Writer dalam menyediakan, menyemak, dan mengemaskini dokumentasi teknikal serta memastikan kandungan adalah tepat, konsisten, dan mudah difahami.
                </p>
                <div class="social-links">
                    <a href="https://wa.me/60149303688" target="_blank"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>

</body>
</html>