<?php 
session_start();  // PENTING! Letak di atas sekali
// include 'config.php'; atau database connection kalau ada
?>

<?php include 'KVK_Navbar.php'; ?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kaunselor Kami • KVKaunsel</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{
            font-family:'Inter',sans-serif;
            background:url('ImageGalleries/KVK_BungaBackground.png') center/cover no-repeat fixed;
            min-height:100vh;
            overflow-x:hidden;
        }

        /* SAKURA PETALS */
        .petal{
            position:fixed;
            pointer-events:none;
            z-index:9999;
            opacity:0.85;
            animation:fall linear infinite;
        }
        @keyframes fall{
            0%{transform:translateY(-100vh) rotate(0deg); opacity:0;}
            10%{opacity:0.85;}
            100%{transform:translateY(110vh) rotate(360deg); opacity:0;}
        }

        .container{padding:110px 20px 100px;max-width:1200px;margin:0 auto;position:relative;z-index:2}
        h1{text-align:center;font-family:'Playfair Display',serif;font-size:88px;color:#9558b2;margin-bottom:50px;text-shadow:0 6px 20px rgba(0,0,0,0.15)}

        .counselors{display:flex;flex-direction:column;gap:160px}
        .profile{
            background:rgba(255,255,255,0.96);
            border-radius:1px solid rgba(255,255,255,0.4);
            border-radius:40px;
            overflow:hidden;
            box-shadow:0 30px 80px rgba(149,88,178,0.3);
            display:flex;align-items:center;gap:60px;
            padding:50px 60px;max-width:1100px;margin:0 auto;
        }
        .profile:nth-child(even){flex-direction:row-reverse}
        .profile:hover img{transform:scale(1.1)}


        
        .profile-img{
            width:380px;height:500px;border-radius:32px;overflow:hidden;
            border:8px solid #fff;box-shadow:0 20px 50px rgba(0,0,0,0.25);flex-shrink:0;
        }
        .profile-img img{width:100%;height:100%;object-fit:cover;transition:transform .8s ease}

        .profile-info h3{font-family:'Playfair Display',serif;font-size:48px;color:#8a4baf;margin-bottom:8px}
        .profile-info .title{font-size:21px;color:#b476d1;margin-bottom:20px;font-weight:600}
        .profile-info p{font-size:18px;line-height:1.9;color:#444;margin-bottom:40px}

        .whatsapp-btn{
            position:relative;background:linear-gradient(135deg,#d4a5e8,#9f6bb1);
            color:white;padding:18px 50px;border-radius:50px;font-weight:700;font-size:19px;
            text-decoration:none;display:inline-flex;align-items:center;gap:12px;
            box-shadow:0 15px 40px rgba(159,107,177,0.6);overflow:hidden;
            animation:pulse 2.5s infinite;
        }
        
        .whatsapp-btn::before{
            content:'';position:absolute;top:0;left:-100%;width:100%;height:100%;
            background:linear-gradient(90deg,transparent,rgba(255,255,255,.3),transparent);
            transition:.7s;
        }
        .whatsapp-btn:hover::before{left:100%}
        .whatsapp-btn:hover{transform:translateY(-6px) scale(1.05);box-shadow:0 25px 60px rgba(159,107,177,0.8)}

        @keyframes pulse{
            0%,100%{box-shadow:0px40px rgba(159,107,177,0.6)}
            50%{box-shadow:0 15px 70px rgba(159,107,177,0.9)}
        }

        @media(max-width:992px){
            .profile,.profile:nth-child(even){flex-direction:column;text-align:center}
            .profile-img{width:340px;height:440px}
            h1{font-size:64px}.counselors{gap:120px}
        }
        @media(max-width:576px){
            h1{font-size:52px;margin-bottom:80px}
            .profile{padding:40px 20px;border-radius:32px}
            .profile-img{width:100%;max-width:320px;height:400px}
            .profile-info h3{font-size:40px}
        }
    </style>
</head>
<body>

<!-- SAKURA PETALS RAINING -->
<div id="sakura"></div>

<div class="container">
    <h1>Kenali Kaunselor Anda!</h1>

    <div class="counselors">
        <div class="profile">
            <div class="profile-img"><img src="Team/CikguMuhirman_jpeg-removebg-preview.png" alt="En. Muhirman"></div>
            <div class="profile-info">
                <h3>En. Muhirman Bin <br>Mu-Alim</h3>
                <div class="title">Ketua Unit Psikologi Dan Kerjaya</div>
                <p>Beliau berpengalaman dalam bidang bimbingan dan kaunseling dengan penekanan terhadap pendekatan terapi seni dan mindfulness. Pendekatan ini memberi ruang kepada individu untuk meneroka emosi, mengurus tekanan serta membina keseimbangan diri melalui kaedah yang kreatif dan berstruktur.</p>
                <a href="https://wa.me/601119861202" class="whatsapp-btn" target="_blank">Hubungi En. Muhirman</a>
            </div>
        </div>

        <div class="profile">
            <div class="profile-img"><img src="Team/CikguTanita.jpeg" alt="Pn. Tanita"></div>
            <div class="profile-info">
                <h3>Pn. Tanita Anak Numpang</h3>
                <div class="title">Kaunselor</div>
                <p>Beliau merupakan kaunselor yang berpengalaman dalam membantu pelajar mengurus isu emosi dan tekanan. Pendekatan kaunseling yang tenang dan berfokus kepada kesejahteraan emosi membantu pelajar berasa selesa untuk berkongsi serta memahami diri dengan lebih baik.</p>
                <a href="https://wa.me/60195429585" class="whatsapp-btn" target="_blank">Hubungi Pn. Tanita</a>
            </div>
        </div>

        <div class="profile">
            <div class="profile-img"><img src="Team/CikguWhilemina.jpg-removebg-preview.png" alt="Pn. Whilemina"></div>
            <div class="profile-info">
                <h3>Pn. Whilemina Anak Gregory Jimbun</h3>
                <div class="title">Kaunselor</div>
                <p>Beliau merupakan kaunselor berpengalaman dalam bidang kaunseling, khususnya dalam kaunseling kerjaya, pengurusan trauma dan pembangunan diri. Kepakaran serta pendekatan yang matang dan berfokus membantu pelajar membuat perancangan masa depan dengan lebih jelas.</p>
                <a href="https://wa.me/601116069009" class="whatsapp-btn" target="_blank">Hubungi Pn. Whilemina</a>
            </div>
        </div>
    </div>
</div>

<script>
    // Sakura petals magic
    function createPetal() {
        const petal = document.createElement('div');
        petal.classList.add('petal');
        
        // Random size & type
        const size = Math.random() * 18 + 12 + 'px';
        petal.style.width = size;
        petal.style.height = size;
        petal.style.left = Math.random() * 100 + 'vw';
        petal.style.animationDuration = Math.random() * 12 + 15 + 's';
        petal.style.animationDelay = Math.random() * 5 + 's';
        petal.style.opacity = Math.random() * 0.4 + 0.6;

        // Random sakura color
        const colors = ['#ffd1f0', '#ffbee3', '#ffb3d9', '#ffc0ea', '#f8dfff'];
        petal.style.background = colors[Math.floor(Math.random() * colors.length)];
        petal.style.borderRadius = '50% 0 50% 0';
        petal.style.transform = `rotate(${Math.random() * 360}deg)`;

        document.getElementById('sakura').appendChild(petal);

        // Remove after animation
        setTimeout(() => {
            petal.remove();
        }, 30000);
    }

    // Buat 40-60 petals sekaligus
    setInterval(createPetal, 300);
    // First batch
    for(let i = 0; i < 50; i++) {
        setTimeout(createPetal, i * 100);
    }
</script>

</body>
</html>