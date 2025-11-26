<?php session_start(); ?>
<?php include 'KVK_Navbar.php'; ?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KVKaunsel - Tempah Sesi Kaunseling</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        html, body { height:100%; overflow:hidden; font-family:'Inter',sans-serif; }

        .hero__video {
            position:fixed; top:0; left:0; width:100%; height:100%; object-fit:cover;
            z-index:-1; filter:brightness(1.08);
        }

        /* ECLIPSE — LEBIH SOLID & CANTIK */
        .eclipse-left, .eclipse-right {
            position:absolute;
            width:900px; height:1300px;
            top:50%; transform:translateY(-50%);
            background:rgba(175,116,177,0.66);   /* ← kurang transparent, lebih padu */
            border-radius:50%;
            display:flex; align-items:center;
            cursor:pointer;
            transition:background .65s cubic-bezier(.4,0,.2,1), transform .65s cubic-bezier(.4,0,.2,1);
            z-index:2;
        }
        .eclipse-left  { left:-400px;  justify-content:flex-end; padding-right:130px; }
        .eclipse-right { right:-400px; justify-content:flex-start; padding-left:130px; }

        /* HOVER: eclipse scale sikit je, text je gerak masuk */
        .eclipse-left:hover,  .eclipse-right:hover { background:#B87AB8; transform:translateY(-50%) scale(1.02); }

        .eclipse-text {
            color:white; font-size:120px; font-weight:900; letter-spacing:-6px;
            display:flex; align-items:center; gap:36px; user-select:none;
            transition:transform .7s cubic-bezier(.34,1.56,.64,1);   /* bouncy gerak text */
        }
        .eclipse-text .arrow { font-weight:300; font-size:136px; }

        /* Text gerak masuk bila hover — eclipse tak gerak posisi */
        .eclipse-left:hover  .eclipse-text { transform:translateX(120px); }
        .eclipse-right:hover .eclipse-text { transform:translateX(-120px); }

        .center-text {
            position:absolute; top:49%; left:50%; transform:translate(-50%,-50%);
            text-align:center; z-index:10; pointer-events:none; width:100%; padding:0 180px;
        }
        .center-text h1 { font-size:128px; font-weight:900; letter-spacing:-7px; color:#AF74B1; margin-bottom:18px; }
        .center-text p  { font-size:42px; font-weight:600; color:#A070A0; line-height:1.4; opacity:.95; }
        .eclipse-left .eclipse-text  { transform: translateX(120px) ; }   /* SVM: kanan 30px, naik 40px */
        .eclipse-right .eclipse-text { transform: translateX(-120px) ; }  /* DVM: kiri 40px, naik 20px */

    </style>
</head>
<body>

    <video class="hero__video" autoplay muted loop playsinline poster="ImageGalleries/ImageCounseling1.jpg">
        <source src="VideoGalleries/KVK_BookingVideo.mp4" type="video/mp4">
    </video>

    <a href="KVK_BookingSvm.php" style="text-decoration:none;">
        <div class="eclipse-left">
            <div class="eclipse-text">SVM <span class="arrow">→</span></div>
        </div>
    </a>

    <a href="KVK_BookingDvm.php" style="text-decoration:none;">
        <div class="eclipse-right">
            <div class="eclipse-text"><span class="arrow">←</span> DVM</div>
        </div>
    </a>

    <div class="center-text">
        <h1>Hi Awak !</h1>
        <p>Mulakan tempahan sesi kaunseling<br>dengan memilih tahap pengajian anda</p>
    </div>

</body>
</html>