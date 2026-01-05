<?php
session_start();

// Only allow logged-in counselors
if (!isset($_SESSION['kaunselor_id'])) {
    header("Location: UltimateLoginPage.php");
    exit;
}

// Include your existing MySQLi connection
include("db.php"); // This gives us $conn

header('Content-Type: application/json');

$kaunselor_id = $_SESSION['kaunselor_id'];
$old_password = $_POST['old_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Kaedah tidak sah.';
    echo json_encode($response);
    exit;
}

if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
    $response['message'] = 'Sila isi semua ruangan.';
} elseif (strlen($new_password) < 6) {
    $response['message'] = 'Kata laluan baru mesti sekurang-kurangnya 6 aksara.';
} elseif ($new_password !== $confirm_password) {
    $response['message'] = 'Kata laluan baru tidak sepadan.';
} else {
    // Verify old password using SHA2(256)
    $stmt = $conn->prepare("SELECT id FROM caunselor WHERE id = ? AND password = SHA2(?, 256)");
    $stmt->bind_param("is", $kaunselor_id, $old_password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $response['message'] = 'Kata laluan lama salah.';
    } else {
        // Update password
        $update = $conn->prepare("UPDATE caunselor SET password = SHA2(?, 256) WHERE id = ?");
        $update->bind_param("si", $new_password, $kaunselor_id);
        
        if ($update->execute()) {
            $response['success'] = true;
            $response['message'] = 'Kata laluan berjaya ditukar!';
        } else {
            $response['message'] = 'Ralat semasa menyimpan. Sila cuba lagi.';
        }
        $update->close();
    }
    $stmt->close();
}

echo json_encode($response);
exit;
?>