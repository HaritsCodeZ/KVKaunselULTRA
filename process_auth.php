<?php
session_start();
include("db.php"); // mysqli connection

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: UltimateLoginPage.php");
    exit;
}

// Ambil input dari borang
$username = strtoupper(trim($_POST['username'] ?? $_POST['student_id'] ?? ''));
$password = $_POST['password'] ?? '';
$action   = $_POST['action'] ?? 'login';
$invite_code = strtoupper(trim($_POST['invite_code'] ?? '')); 

unset($_SESSION['error'], $_SESSION['error_type']);

$admin_found = false;

// ====================== 1. CEK ADMIN / KAUNSELOR DULU ======================
if ($username && $password && $action === 'login') {
    $stmt = $conn->prepare("SELECT id, counselor_id FROM caunselor WHERE counselor_id = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Cek password guna SHA2
        $check_pass = $conn->prepare("SELECT id FROM caunselor WHERE counselor_id = ? AND password = SHA2(?, 256)");
        $check_pass->bind_param("ss", $username, $password);
        $check_pass->execute();
        $pass_result = $check_pass->get_result();

        if ($pass_result->num_rows === 1) {
            $admin_found = true;
            
            // Ambil detail nama cikgu
            $detail_stmt = $conn->prepare("SELECT nama_penuh, nama_panggilan FROM caunselor WHERE id = ?");
            $detail_stmt->bind_param("i", $row['id']);
            $detail_stmt->execute();
            $detail = $detail_stmt->get_result()->fetch_assoc();
            $detail_stmt->close();

            // Set session admin
            $_SESSION['kaunselor_id']         = $row['id'];
            $_SESSION['kaunselor_nama']       = $row['counselor_id'];
            $_SESSION['counselor_full_name']  = $detail['nama_penuh'];
            $_SESSION['counselor_short_name'] = $detail['nama_panggilan'];

            // Redirect ikut ID Cikgu
            switch ($row['id']) {
                case 1: header("Location: KVK_Admin_CgMuhirman_Utama.php"); exit;
                case 2: header("Location: KVK_Admin_CgTanita_Utama.php"); exit;
                case 3: header("Location: KVK_Admin_CgWhilemina_Utama.php"); exit;
                default: header("Location: KVK_Admin_CgMuhirman_Utama.php"); exit;
            }
        } else {
            $_SESSION['error'] = "Kata laluan salah untuk ID Kaunselor!";
            $_SESSION['error_type'] = 'login';
        }
        $check_pass->close();
    }
    $stmt->close();
}

// ====================== 2. KALAU BUKAN ADMIN → PROSES PELAJAR ======================
if (!$admin_found) {
    if ($action === 'register') {
        $student_id = strtoupper(trim($_POST['student_id']));
        $confirm    = $_POST['confirm_password'] ?? '';

        if (empty($student_id) || empty($password) || empty($confirm) || empty($invite_code)) {
            $_SESSION['error'] = "Sila isi semua ruangan termasuk Kod Jemputan!";
        } elseif ($password !== $confirm) {
            $_SESSION['error'] = "Kata laluan tidak sama!";
        } else {
            // --- LOGIK KOD JEMPUTAN BARU (START) ---
            $stmt_code = $conn->prepare("SELECT id FROM invitation_codes WHERE code = ? AND expires_at > NOW()");
            $stmt_code->bind_param("s", $invite_code);
            $stmt_code->execute();
            $res_code = $stmt_code->get_result();

            if ($res_code->num_rows > 0) {
                // Kod Sah & Belum tamat tempoh!
                $check = $conn->prepare("SELECT student_id FROM students WHERE student_id = ?");
                $check->bind_param("s", $student_id);
                $check->execute();
                
                if ($check->get_result()->num_rows > 0) {
                    $_SESSION['error'] = "Angka giliran sudah didaftarkan!";
                } else {
                    // Simpan Pelajar Baru
                    $hashed = password_hash($password, PASSWORD_DEFAULT);
                    $insert = $conn->prepare("INSERT INTO students (student_id, password) VALUES (?, ?)");
                    $insert->bind_param("ss", $student_id, $hashed);
                    
                    if ($insert->execute()) {
                        // LOGIK ANDA: JANGAN UPDATE status supaya orang lain boleh guna kod sama
                        $_SESSION['student_id'] = $student_id;
                        header("Location: KVK_Registration.php");
                        exit;
                    } else {
                        $_SESSION['error'] = "Pendaftaran gagal. Sila hubungi Admin.";
                    }
                }
            } else {
                $_SESSION['error'] = "Kod Jemputan tidak sah atau telah tamat tempoh!";
            }
            $stmt_code->close();
        }
        $_SESSION['error_type'] = 'register';
        header("Location: UltimateLoginPage.php");
        exit;

    } else {
        // ====================== LOGIN PELAJAR ======================
        $stmt = $conn->prepare("SELECT student_id, password FROM students WHERE student_id = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row['password'])) {
                $_SESSION['student_id'] = $row['student_id'];
                header("Location: KVK_Registration.php");
                exit;
            } else {
                $_SESSION['error'] = "Kata laluan pelajar salah!";
            }
        } else {
            $_SESSION['error'] = "ID Pelajar tidak dijumpai!";
        }
        $_SESSION['error_type'] = 'login';
        header("Location: UltimateLoginPage.php");
        exit;
    }
}
?>