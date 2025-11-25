<?php
session_start();
session_destroy();
header("Location: KVK_Homepage.php");
exit;
?>
