<?php
session_start();
if (!isset($_SESSION['kaunselor_id'])) exit('unauthorized');

$pdo = new PDO("mysql:host=localhost;dbname=kvkaunsel_db", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$type = $_POST['type'] ?? '';

if ($type === 'selesai') {
    $stmt = $pdo->prepare("UPDATE tempahan_kaunseling SET archived = 1 WHERE status = 'Selesai'");
} elseif ($type === 'ditolak') {
    $stmt = $pdo->prepare("UPDATE tempahan_kaunseling SET archived = 1 WHERE status = 'Dibatalkan'");
} elseif ($type === 'both') {
    $stmt = $pdo->prepare("UPDATE tempahan_kaunseling SET archived = 1 WHERE status IN ('Selesai', 'Dibatalkan')");
} else {
    echo 'invalid';
    exit;
}

$stmt->execute();
echo 'success';
?>