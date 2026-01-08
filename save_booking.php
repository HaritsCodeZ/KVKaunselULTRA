<?php
// save_booking.php
header('Content-Type: application/json');
session_start();

// Optional debug (remove in production)
// file_put_contents('debug_post.txt', date('Y-m-d H:i:s') . "\n" . print_r($_POST, true) . "\n\n", FILE_APPEND);

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST)) {
    echo json_encode(['success' => false, 'error' => 'Tiada data diterima.']);
    exit;
}

if (!isset($_SESSION['student_id'])) {
    echo json_encode(['success' => false, 'error' => 'Sila log masuk semula.']);
    exit;
}

// Critical: Validate tahap strictly
$tahap = $_POST['tahap'] ?? null;
if (!in_array($tahap, ['SVM', 'DVM'], true)) {
    echo json_encode([
        'success' => false,
        'error' => 'Ralat: Tahap tidak sah. Sila gunakan borang yang betul.'
    ]);
    exit;
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=kvkaunsel_db", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("INSERT INTO tempahan_kaunseling 
        (student_id, tahap, nama, program, semester, jantina, kaum, telefon, tarikh_masa, jenis_sesi, jenis_kaunseling, kaunselor, sebab, tarikh_tempahan, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'Baru')");

    $stmt->execute([
        $_SESSION['student_id'],
        $tahap,
        $_POST['nama'] ?? '',
        $_POST['program'] ?? '',
        $_POST['semester'] ?? '',
        $_POST['jantina'] ?? '',
        $_POST['kaum'] ?? '',
        $_POST['telefon'] ?? '',
        $_POST['tarikh_masa'] ?? '',
        $_POST['jenis_sesi'] ?? '',
        $_POST['jenis_kaunseling'] ?? 'Kaunseling Individu',
        $_POST['kaunselor'] ?? '',
        $_POST['sebab'] ?? ''
    ]);

    echo json_encode([
        'success' => true,
        'id' => $pdo->lastInsertId(),
        'message' => 'Tempahan berjaya dihantar!'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Ralat sistem. Sila cuba lagi.'
    ]);
}
?>