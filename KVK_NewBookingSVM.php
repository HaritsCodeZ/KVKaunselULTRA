<?php session_start(); ?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tempahan Kaunseling SVM</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        html, body { 
            height:100%; 
            overflow:hidden; 
            font-family:'Inter',sans-serif; 
            background:#000;
        }

        /* Video Background */
        .hero__video {
            position:fixed; 
            top:0; left:0; 
            width:100%; height:100%; 
            object-fit:cover;
            z-index:1;
            filter:brightness(1.05);
        }

        /* Kotak Putih - Sekarang boleh gerak bebas! */
        .white-card {
            position: absolute;
            height: 850px;
            /* Anda boleh ubah nilai top, left, right, bottom sesuka hati */
            top: 50%;           /* ← ubah nilai ni untuk atas/bawah */
            left:57%;
            transform: translate(-50%, -50%);  /* tengah-tengah benda tu sendiri */
            width: 90%;
            max-width: 1250px;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.2);
            padding: 45px 35px;
            z-index: 10;                    /* Di belakang eclipse */
            border: 1px solid #e8e8e8;
        }

        /* Eclipse Ungu + SVM - Layer paling atas */
        .eclipse-left {
            position: absolute;
            width: 1100px;
            height: 1050px;
            top: 49%;
            left: -530px;                   /* anda boleh gerakkan juga kalau nak */
            transform: translateY(-50%);
            background: rgba(175,116,177,0.82);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding-right: 130px;
            cursor: pointer;
            transition: all .6s ease;
            z-index: 20;                    /* PALING ATAS! */
            pointer-events: auto;
        }

        .eclipse-text {
            color: white;
            font-size: 140px;
            font-weight: bold;
            letter-spacing: -10px;
            user-select: none;
        }

        /* === Bahagian dalam kotak putih (sama macam sebelum ni) === */
        .logo-container { text-align:center; margin-bottom:30px; margin-top:-30px; }
        .logo-container img { height:70px; margin:0 15px; }

        .greeting {
        text-align: right;           /* ← align ke kanan */
        font-size: 36px;             /* saya besarkan sikit supaya nampak power */
        font-weight: bold;                /* tebal gila (atau boleh guna bold je) */
        color: #AF74B1;              /* warna ungu yang anda nak */
        margin-bottom: 8px;
        margin-right: 20px;          /* optional: bagi ruang sikit dari tepi kanan */
        letter-spacing: -1px;        /* optional: rapatkan huruf supaya nampak moden */
        }
        .sub-greeting { 
        text-align: right;           /* ← align ke kanan */
         font-size: 36px;           /* saya besarkan sikit supaya nampak power */
        font-weight: bold;            /* tebal gila (atau boleh guna bold je) */
        color: #AF74B1;              /* warna ungu yang anda nak */
        margin-bottom: 8px;
        margin-right: 20px;          /* optional: bagi ruang sikit dari tepi kanan */
        letter-spacing: -1px;  
        }

/* BULATAN KUNING No.1 — PALING ATAS SEKALI & boleh gerak bebas */
.progress-master-circle_1 {
    position: absolute;
    width: 60px;
    height: 60px;
    background: #FFD557;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 30px rgba(255, 214, 10, 0.5);
    z-index: 9999 !important;           /* PALING ATAS — tak ada benda boleh halang! */
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    cursor: default;
    top: 23%;                            /* ubah sini untuk atas/bawah */
    left: 26.2%;                           /* ubah sini untuk kiri/kanan */
    transform: translate(-50%, -50%);
}


.progress-master-circle_1 .progress-number {
    font-size: 42px;
    font-weight: bold;
    color: #000000ff;
    user-select: none;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* BULATAN KUNING No.2 — PALING ATAS SEKALI & boleh gerak bebas */
.progress-master-circle_2 {
    position: absolute;
    width: 60px;
    height: 60px;
    background: #8C5C8D;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 30px rgba(255, 214, 10, 0.5);
    z-index: 9999 !important;           /* PALING ATAS — tak ada benda boleh halang! */
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    cursor: default;
    top: 38%;                            /* ubah sini untuk atas/bawah */
    left: 28.9%;                           /* ubah sini untuk kiri/kanan */
    transform: translate(-50%, -50%);
}


.progress-master-circle_2 .progress-number {
    font-size: 42px;
    font-weight: bold;
    color: #080708ff;
    user-select: none;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
    </style>
</head>
<body>

    <!-- Video Background -->
    <video class="hero__video" autoplay muted loop playsinline poster="ImageGalleries/ImageCounseling1.jpg">
        <source src="VideoGalleries/KVK_BookingVideo.mp4" type="video/mp4">
    </video>

    <!-- Eclipse Ungu + SVM -->
        <div class="eclipse-left">
            <div class="eclipse-text">SVM</div>
        </div>
    </a>

    <!-- Kotak Putih -->
    <div class="white-card">
        <div class="logo-container">
            <img src="ImageGalleries/KVKaunsel_Logo-New.png" alt="Logo 1">
            <img src="ImageGalleries/LOGO_KV.png" alt="Logo 2">
            <img src="ImageGalleries/LOGO_PRS_RELOADED.png" alt="Logo 3">
        </div>

        <h2 class="greeting">Hai !</h2>
        <p class="sub-greeting">Sebelum tempahan,<br>Mari kita berkenalan dahulu</p>
    </div>

    <!-- BULATAN KUNING No.1 — PALING ATAS & boleh gerak sesuka hati -->
    <div class="progress-master-circle_1">
        <span class="progress-number">1</span>
    </div>

    <div class="progress-master-circle_2">
        <span class="progress-number">2</span>
    </div>

</body>
</html>