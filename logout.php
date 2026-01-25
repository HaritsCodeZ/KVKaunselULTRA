<?php
session_start();

// Cek jika yang tekan logout ini memang Student
if (isset($_SESSION['student_id'])) {
    
    // Padam semua session yang bermula dengan 'student_'
    unset($_SESSION['student_logged_in']);
    unset($_SESSION['student_id']);
    unset($_SESSION['student_data']);
    
    // Mesej berjaya (opsional)
    $_SESSION['logout_msg'] = "Student berjaya logout.";
}

// Redirect ke homepage
header("Location: KVK_Homepage.php");
exit;
?>