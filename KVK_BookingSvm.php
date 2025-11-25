<?php session_start();?>

<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tempahan Kaunseling SVM</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">

<!-- External CSS -->
<link rel="stylesheet" href="KVK_BookingSvm.css">

</head>

<body>

<!-- ===== VIDEO BACKGROUND ===== -->
<video autoplay muted loop class="bg-video">
  <source src="VideoGalleries/KVK_BookingVideo.mp4" type="video/mp4">
</video>

<!-- ===== RIGHT ECLIPSE (BEHIND FORM) ===== -->
<div class="righteclipse">
    <div class="text-wrapper">
        <h1>SVM</h1>
    </div>
</div>

<!-- ===== MAIN CONTENT (FRONT) ===== -->
<div class="wrapper">

  <div class="left-section">
  <!-- LOGO ROW -->
  <div class="logo-row">
    <img src="logo1.png" alt="">
    <img src="logo2.png" alt="">
    <!-- logo kau -->
  </div>

  <!-- INI YANG BARU – GANTI YANG LAMA -->
  <div class="greeting-text">
    <h1 class="hai-text">Hai !</h1>
    <p class="intro-text">Sebelum tempahan,<br>Mari kita berkenalan dahulu</p>
  </div>    

    <form action="booking-step2.php" method="POST">

      <label>Nama Penuh Anda</label><br>
      <input type="text" name="nama" required>
<br><br>
      <label>Program Anda</label><br>
      <select name="program" required>
        <option value="">— Pilih Program —</option>
        <option value="SVM">Sijil Vokasional Malaysia (SVM)</option>
        <option value="DKM">Diploma Kemahiran Malaysia (DKM)</option>
      </select>
<br><br>
      <label>Semester Anda</label><br>
      <select name="semester" required>
        <option value="">— Pilih Semester —</option>
        <option value="1">Semester 1</option>
        <option value="2">Semester 2</option>
        <option value="3">Semester 3</option>
        <option value="4">Semester 4</option>
      </select>

      <button class="button">Teruskan</button>

    </form>
  </div>

</div>

</body>
</html>
