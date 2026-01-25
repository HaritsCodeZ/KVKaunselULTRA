<?php
session_start();
include("db.php");
header('Content-Type: application/json'); // PENTING untuk JavaScript baca

$new_code = strtoupper(substr(md5(uniqid()), 0, 6));
$admin = $_SESSION['counselor_short_name'] ?? 'Admin';

$sql = "INSERT INTO invitation_codes (code, admin_name, status) VALUES ('$new_code', '$admin', 'pending')";

if (mysqli_query($conn, $sql)) {
    echo json_encode(['success' => true, 'code' => $new_code]);
} else {
    echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
}
?>