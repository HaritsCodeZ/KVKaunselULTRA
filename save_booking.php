<?php
// save_booking.php → MODE DEBUG (gantikan sementara)
header('Content-Type: application/json');

// Simpan semua data yang sampai dalam file txt untuk tengok
file_put_contents('debug_post.txt', date('Y-m-d H:i:s') . "\n" . print_r($_POST, true) . "\n\n", FILE_APPEND);

// Kalau POST kosong, bagitahu
if(empty($_POST)) {
    echo json_encode(['success' => false, 'error' => 'Tiada data POST diterima', 'method' => $_SERVER['REQUEST_METHOD']]);
    exit;
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=kvkaunsel_db", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("INSERT INTO tempahan_kaunseling 
        (student_id,tahap,nama,program,semester,jantina,kaum,telefon,tarikh_masa,jenis_sesi,jenis_kaunseling,kaunselor,sebab) 
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");

    $stmt->execute([
        $_POST['student_id'] ?? '',
        $_POST['tahap'] ?? 'SVM',
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

    echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);

} catch(Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>