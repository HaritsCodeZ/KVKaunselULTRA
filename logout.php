<?php
session_start();
session_destroy();
header("Location: UltimateLoginPage.php");
exit;
?>
