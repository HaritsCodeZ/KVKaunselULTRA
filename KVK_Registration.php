  <?php session_start(); ?>
  <?php include 'KVK_Navbar.php'; ?>

  <!DOCTYPE html>
  <html lang="ms">
  <head>
      <head>
      <link href="https://fonts.googleapis.com/css2?family=Inter:wght@800;900&display=swap" rel="stylesheet">
      </head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>KVKaunsel - Tempah Sesi Kaunseling</title>
      <style>
          html, body {
            height: 100%;
            margin: 0;
            overflow: hidden;       
            touch-action: none;      
          }
          .hero__video{
          position:fixed;
          top:0;
          left:0;
          width:100%;
          height:100%;
          object-fit:cover;
          z-index:-2;
          filter:brightness(1.1)  
          }
          .overlay{
            position:fixed;top:0;left:0;width:100%;height:100%;
            background:linear-gradient(135deg,rgba(140,92,141,0.8),rgba(180,120,200,0.7));
            z-index:-1;
          }
/* ========================================================================================*/
/* PEMBUKA BISMILLAH ECLIPSE KIRI */
          .lefteclipse{
            position:absolute;
            width:1116px;          /* ← BESARKAN/KECILKAN SINI */
            height:1080px;         /* ← SAMA BESAR DENGAN WIDTH */
            background: linear-gradient(90deg, 
                #AF74B1 0%,     /* kiri */
                #8C5C8D 100%    /* kanan */
            );
            backdrop-filter:blur(20px);
            border-radius:50%;
            box-shadow:0 30px 80px rgba(0,0,0,0.4);
            display:flex;align-items:center;justify-content:center;flex-direction:column;
            color:white;text-align:center;
            cursor:pointer;
            transition:all 0.4s ease;
            /* KAU GERAK KIRI/KANAN/ATAS/BAWAH SINI ↓↓↓ */
            left:0%;             /* contoh: 20% = ke kiri, 80% = ke kanan */
            top:50%;              /* atas/bawah */
            transform:translate(-50%,-50%);  /* jangan ubah — buat dia centre betul */
            opacity:0.8;
          }
          .lefteclipse:hover{
          transform:translate(-50%, -50%) scale(1.045);
          filter:brightness(1.1);
          box-shadow:0 60px 140px rgba(0,0,0,0.6);
          }
          .lefteclipse .text-wrapper{
          display:flex;               /* ← INI YANG BUAT SEBELAH-MENYBELAH */
          align-items:center;         /* tengah menegak */
          justify-content:center;    /* tengah mengufuk */
          gap:39px;                   /* ← jarak antara SVM dengan arrow (ubah ikut suka) */
          position:absolute;
          top:48%;
          left:80%;                   /* kau adjust kiri/kanan sini */
          transform:translate(-50%, -50%);
            }
          .lefteclipse h1{
          font-size:128px;    /* besar/kecil huruf SVM */
          font-family:Inter;
          font-weight:bold;
          margin:0 0 20px 0;
          letter-spacing:-4px;
          }
          .lefteclipse .arrow-svg{
          width:90px;         /* besar/kecil arrow */
          height:90px;
          margin-left:30px;   /* jarak arrow dari SVM */
          }
/* PENUTUP BISMILLAH ECLIPSE KIRI */
/* ========================================================================================*/
/* PEMBUKA BISMILLAH TEXT TENGAH */
          .center-text{
          position:absolute;
          top:50%;
          left:51%;                              /* ← kembali ke tengah screen */
          transform:translate(-50%,-50%);        /* ← centre betul */
          text-align:center;
          color:white;
          z-index:20;                            /* ← lebih tinggi dari eclipse */
          pointer-events:none;                   /* ← supaya tak block klik eclipse */
          width:100%;
          padding:0 15%;
          }
          .center-text h1{
          font-size:128px;
          font-family:Inter;
          margin:0 0 20px 0;
          letter-spacing:-3px;
          text-shadow:0 6px 20px rgba(0,0,0,0.2);

          /* GRADIENT KAU NAK — DUA WARNA SAHAJA */
          background: linear-gradient(90deg, #8C5C8D 0%, #AF74B1 1%);
          -webkit-background-clip: text;
          background-clip: text;
          -webkit-text-fill-color: transparent;   /* ← WAJIB ADA 3 BARIS NI SUPAYA GRADIENT MASUK DALAM HURUF */
}
.center-text p{
    font-size:36px;
    font-family:'Inter', sans-serif;
    line-height:1.6;
    opacity:0.95;
    color:#8C5C8D;
    text-shadow:0 6px 20px rgba(0,0,0,0.2);
}
/* PENUTUP BISMILLAH TEXT TENGAH */
/* ========================================================================================*/
/* PEMBUKA BISMILLAH ECLIPSE KANAN */
.righteclipse{
    position:absolute;
    width:1116px;height:1080px;
    background: linear-gradient(90deg, #AF74B1 0%, #8C5C8D 100%);
    backdrop-filter:blur(20px);
    border-radius:50%;
    box-shadow:0 30px 80px rgba(0,0,0,0.4);
    display:flex;align-items:center;justify-content:center;
    color:white;cursor:pointer;
    transition:all 0.4s ease;
    right:0%;top:50%;transform:translate(52%,-50%);
    opacity:0.8;
    z-index:1;
}
.righteclipse:hover{
    transform:translate(52%,-50%) scale(1.045);
    filter:brightness(1.1);
    box-shadow:0 60px 140px rgba(0,0,0,0.6);
}
.righteclipse .text-wrapper{
    display:flex;align-items:center;gap:-90;
    position:absolute;top:47.9%;left:20%;transform:translate(-50%,-50%);
}
.righteclipse h1{
    font-size:128px;font-family:Inter;font-weight:bold;
    margin:0;letter-spacing:-4px;
}
.righteclipse .arrow-svg{
    width:90px;height:90px;order:-1;margin-right:60px;
}
/* PENUTUP BISMILLAH ECLIPSE KANAN */
/* ========================================================================================*/
      </style>
  </head>
  <body>

      <!-- Video Background -->
    <video class="hero__video" autoplay muted loop playsinline poster="ImageGalleries/ImageCounseling1.jpg">
      <source src="VideoGalleries/KVK_BookingVideo.mp4" type="video/mp4">
    </video>

    <!-- ECLIPSE KIRI -->
<div class="lefteclipse">
    <div class="text-wrapper">
        <h1>SVM</h1>

        <!-- SVG ARROW -->
        <svg class="arrow-svg" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"
             stroke-linecap="round" stroke-linejoin="round">
            <path d="M5 12h14"></path>
            <path d="M12 5l7 7-7 7"></path>
        </svg>
    </div>
</div>

<!-- TEXT TENGAH — MUST BE OUTSIDE -->
<div class="center-text">
    <h1>Hai Awak !</h1>
    <p>Mulakan tempahan sesi kaunseling<br>dengan memilih tahap pengajian anda</p>
</div>

<!-- TAMBAH NI JE BAWAH ECLIPSE KIRI KAU -->
<div class="righteclipse">
    <div class="text-wrapper">
        <svg class="arrow-svg" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 12H5"></path>
            <path d="M12 19l-7-7 7-7"></path>
        </svg>
        <h1>DVM</h1>
    </div>
</div>
    
  </body>
  </html>

  