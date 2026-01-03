<?php
// update_status.php
session_start();
header('Content-Type: text/plain');

if (!isset($_POST['id']) || !isset($_POST['status'])) {
    echo 'error: missing data';
    exit;
}

$id = (int)$_POST['id'];
$status = $_POST['status']; // Expected: 'Selesai' or 'Dibatalkan'

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

    echo $stmt->rowCount() > 0 ? 'success' : 'error: no change';
} catch (Exception $e) {
    echo 'error: ' . $e->getMessage();
}
?>