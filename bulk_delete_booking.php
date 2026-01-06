<?php
session_start();
if (!isset($_SESSION['kaunselor_id'])) {
    echo 'unauthorized';
    exit;
}

$pdo = new PDO("mysql:host=localhost;dbname=kvkaunsel_db", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$type = $_POST['type'] ?? '';

if ($type === 'selesai') {
    $stmt = $pdo->prepare("DELETE FROM tempahan_kaunseling WHERE status = 'Selesai'");
} elseif ($type === 'ditolak') {
    $stmt = $pdo->prepare("DELETE FROM tempahan_kaunseling WHERE status = 'Dibatalkan'");
} elseif ($type === 'both') {
    $stmt = $pdo->prepare("DELETE FROM tempahan_kaunseling WHERE status IN ('Selesai', 'Dibatalkan')");
} else {
    echo 'invalid';
    exit;
}

$stmt->execute();
echo 'success';
?>