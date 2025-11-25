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
  </head>
  <body>

      <!-- Video Background -->
    <video class="hero__video" autoplay muted loop playsinline poster="ImageGalleries/ImageCounseling1.jpg">
      <source src="VideoGalleries/KVK_BookingVideo.mp4" type="video/mp4">
    </video>


    <!-- ECLIPSE KIRI -->
<a href="KVK_BookingSvm.php" class="lefteclipse-link">
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

<div class="svm-title-new">SVM</div>

<!-- TEXT TENGAH — MUST BE OUTSIDE -->
<div class="center-text">
    <h1>Hai Awak !</h1>
    <p>Mulakan tempahan sesi kaunseling<br>dengan memilih tahap pengajian anda</p>
</div>

<link rel="stylesheet" href="KVK_RegistrationDesign.css">

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

  