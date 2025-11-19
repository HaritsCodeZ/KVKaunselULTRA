<?php
session_start();
if (isset($_SESSION['student_id'])) {
    header("Location: THEHOMEPAGE.php");
    exit;
}

?>


<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>KaunselKV - Navbar</title>
  <link rel="stylesheet" href="THEHOMEPAGECSS.css" />
  <script defer src="THEHOMEPAGEJS.JS"></script>
</head>
<body>

<!-- Navigation bar on top -->
<nav class="navbar">
  <div class="navbar-container">
    <div class="logo">KVKaunsel</div>
    <ul class="nav-links" id="navLinks">
<li class="dropdown">
  <a href="#">PANEL KAUNSELOR ▾</a>
  <ul class="dropdown-menu">
    <li><a href="gurukaunsel1.php">MUHIRMAN BIN MU-ALIM</a></li>
    <li><a href="kaunselor2.php">TANITA ANAK NUMPANG</a></li> <!-- ✅ direct ke laman khas -->
    <li><a href="#">WHILEMINA THIMAH ANAK GREGORY JIMBUN</a></li>
  </ul>
</li>

      <li><a href="#">TENTANG KAMI</a></li>
      <li class="dropdown">
        <a href="#">PERKHIDMATAN ▾</a>
        <ul class="dropdown-menu">
          <li><a href="#">Kaunseling Individu</a></li>
          <li><a href="#">Psikometrik</a></li>
        </ul>
      </li>
      <li><a href="#">HUBUNGI</a></li>
    </ul>
    <div class="hamburger" id="hamburger">&#9776;</div>

    <!-- ✅ Profile icon added here -->
<div class="profile-icon" id="profileIcon">
  <a href="#">
    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="white" style="background-color: rgba(255,255,255,0.1); padding: 5px; border-radius: 50%;">
      <path d="M12 12c2.7 0 5-2.3 5-5s-2.3-5-5-5-5 2.3-5 5 2.3 5 5 5zm0 2c-3.3 0-10 1.7-10 5v3h20v-3c0-3.3-6.7-5-10-5z"/>
    </svg>
  </a>
</div>

    
  </div>
</nav>

<!-- Video -->
<div class="video-wrapper">
  <video autoplay loop muted playsinline>
    <source src="VIDEOBACKDROPMAINPAGE.mp4" type="video/mp4">
  </video>
  
  <div class="overlay-content">
    <h1 id="greeting-text">Selamat Datang ke KVKaunsel</h1>
    <p>Anda Inginkan Sesi Kaunseling?</p>
    <br>
    <a href="UltimateLoginPage.php" class="animated-button">Tempah Sekarang !</a>
  </div>
</div>

<br>  

</body>
</html>
