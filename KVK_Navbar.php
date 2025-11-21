<?php 
// Kalau tengah buka UltimateLoginPage.php, jangan tunjuk navbar langsung
if (basename($_SERVER['PHP_SELF']) === 'UltimateLoginPage.php') {
    return;
}
?>

<header class="navbar">
  <div class="nav-left" onclick="location.href='KVK_Homepage.php'">
    <img src="ImageGalleries/KVKaunsel_Logo-New.png" alt="KVKaunsel logo">
    <div class="logo-text">KVKaunsel</div>
  </div>

  <div class="nav-right">
    <?php if(isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) { ?>
      <!-- USER DAH LOGIN -->
      <a href="messages.php" class="icon-btn" title="Mesej">
        Message
      </a>

      <div class="nav-item" style="position:relative;">
        <div class="icon-btn dropdown-toggle" style="cursor:pointer;" title="Akaun">
          User <?php echo ucfirst(strtolower(htmlspecialchars($_SESSION['nama']))); ?>
        </div>
        <div class="dropdown">
          <a href="profile.php">Profil Saya</a>
          <a href="my-bookings.php">Tempahan Saya</a>
          <hr>
          <a href="logout.php" style="color:#e74c3c;">Log Keluar</a>
        </div>
      </div>

    <?php } else { ?>
      <!-- GUEST -->
      <div class="nav-item">
        <div class="nav-btn dropdown-toggle">Perkhidmatan Kami Down Arrow</div>
        <div class="dropdown">
          <a href="booking.php">Tempahan Sesi</a>
          <a href="#">Program Kaunseling</a>
          <a href="#">Bantuan Kecemasan</a>
        </div>
      </div>

      <div class="nav-item">
        <div class="nav-btn dropdown-toggle">Profil Kaunselor & PRS Down Arrow</div>
        <div class="dropdown">
          <a href="#">Panel Kaunselor</a>
          <a href="#">Ahli Pembimbing Rakan Sebaya</a>
        </div>
      </div>

      <a class="nav-btn" href="UltimateLoginPage.php">Log Masuk / Daftar</a>
    <?php } ?>
  </div>
</header>