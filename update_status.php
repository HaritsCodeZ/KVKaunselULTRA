<?php
// update_status.php
session_start();
header('Content-Type: text/plain');

if (!isset($_POST['id']) || !isset($_POST['status'])) {
    echo 'error: missing data';
    exit;
}

$id = (int)$_POST['id'];
$status = $_POST['status']; // 'Selesai' or 'Dibatalkan'

// Validate status against ENUM
$validStatuses = ['Baru', 'Dalam Proses', 'Selesai', 'Dibatalkan'];
if (!in_array($status, $validStatuses)) {
    echo 'error: invalid status';
    exit;
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=kvkaunsel_db", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("UPDATE tempahan_kaunseling SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);

    if ($stmt->rowCount() > 0) {
        echo 'success';
    } else {
        echo 'error: no row updated (ID not found or same status)';
    }
} catch (Exception $e) {
    echo 'error: ' . $e->getMessage();
}
?>