<?php
session_start();
if (!isset($_SESSION['kaunselor_id'])) exit('unauthorized');

$pdo = new PDO("mysql:host=localhost;dbname=kvkaunsel_db", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_POST['id']) {
    $stmt = $pdo->prepare("UPDATE tempahan_kaunseling SET archived = 1 WHERE id = ?");
    $stmt->execute([$_POST['id']]);
    echo 'success';
}
?>