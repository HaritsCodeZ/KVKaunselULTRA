<?php
session_start();
include("db.php");
header('Content-Type: application/json');

if (!isset($_SESSION['kaunselor_id'])) {
    echo json_encode(['success' => false, 'message' => 'Sesi tamat']);
    exit;
}

$new_code = strtoupper(substr(md5(uniqid()), 0, 6));
$admin = $_SESSION['counselor_short_name'] ?? 'Cikgu Muhirman';

// Set masa luput: Waktu sekarang + 3 minit
$sql = "INSERT INTO invitation_codes (code, admin_name, status, expires_at) 
        VALUES ('$new_code', '$admin', 'pending', DATE_ADD(NOW(), INTERVAL 3 MINUTE))";

if (mysqli_query($conn, $sql)) {
    echo json_encode(['success' => true, 'code' => $new_code]);
} else {
    echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
}
?>