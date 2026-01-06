<?php
session_start();
if (!isset($_SESSION['kaunselor_id'])) {
    echo 'unauthorized';
    exit;
}

$pdo = new PDO("mysql:host=localhost;dbname=kvkaunsel_db", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_POST['id']) {
    $stmt = $pdo->prepare("DELETE FROM tempahan_kaunseling WHERE id = ?");
    $stmt->execute([$_POST['id']]);
    echo 'success';
} else {
    echo 'invalid';
}
?>