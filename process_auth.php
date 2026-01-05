<?php
session_start();
include("db.php"); // mysqli connection

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: UltimateLoginPage.php");
    exit;
}

$username = strtoupper(trim($_POST['username'] ?? $_POST['student_id'] ?? ''));
$password = $_POST['password'] ?? '';
$action   = $_POST['action'] ?? 'login';

unset($_SESSION['error'], $_SESSION['error_type']);

$admin_found = false; // ← ADD THIS: Fix undefined variable

// ====================== 1. CEK ADMIN / KAUNSELOR DULU ======================
if ($username && $password) {
    // Cek sama ada username wujud dalam table caunselor
    $stmt = $conn->prepare("SELECT id, counselor_id FROM caunselor WHERE counselor_id = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Username wujud → check password guna SHA2 MySQL
        $check_pass = $conn->prepare("SELECT id FROM caunselor WHERE counselor_id = ? AND password = SHA2(?, 256)");
        $check_pass->bind_param("ss", $username, $password);
        $check_pass->execute();
        $pass_result = $check_pass->get_result();

        if ($pass_result->num_rows === 1) {
            // LOGIN KAUNSELOR BERJAYA
            $admin_found = true; // ← Mark that admin was found and logged in

            // Ambil nama penuh dan nama panggilan dari table caunselor
            $detail_stmt = $conn->prepare("SELECT nama_penuh, nama_panggilan FROM caunselor WHERE id = ?");
            $detail_stmt->bind_param("i", $row['id']);
            $detail_stmt->execute();
            $detail_result = $detail_stmt->get_result();
            $detail = $detail_result->fetch_assoc();
            $detail_stmt->close();

            // Simpan semua maklumat penting dalam session
            $_SESSION['kaunselor_id']         = $row['id'];
            $_SESSION['kaunselor_nama']       = $row['counselor_id'];
            $_SESSION['counselor_full_name']  = $detail['nama_penuh'];     // Important!
            $_SESSION['counselor_short_name'] = $detail['nama_panggilan']; // For display

            // Redirect ke dashboard masing-masing
            switch ($row['id']) {
                case 1: header("Location: KVK_Admin_CgMuhirman_Utama.php"); exit;
                case 2: header("Location: KVK_Admin_CgTanita_Utama.php"); exit;
                case 3: header("Location: KVK_Admin_CgWhilemina_Utama.php"); exit;
                default: header("Location: KVK_Admin_CgMuhirman_Utama.php"); exit;
            }
            // No need to continue after redirect
        } else {
            // Password salah untuk kaunselor
            $_SESSION['error'] = "Kata laluan salah untuk ID Kaunselor!";
        }
        $check_pass->close();
    }
    $stmt->close();
}

// ====================== 2. KALAU BUKAN ADMIN → BARU CEK PELAJAR ======================
if (!$admin_found) { // ← Now this works correctly
    if ($action === 'register') {
        // DAFTAR PELAJAR
        $student_id = strtoupper(trim($_POST['student_id']));
        $confirm    = $_POST['confirm_password'] ?? '';

        if (empty($student_id) || empty($password) || empty($confirm)) {
            $_SESSION['error'] = "Sila isi semua ruangan!";
        } elseif ($password !== $confirm) {
            $_SESSION['error'] = "Kata laluan tidak sama!";
        } else {
            $check = $conn->prepare("SELECT student_id FROM students WHERE student_id = ?");
            $check->bind_param("s", $student_id);
            $check->execute();
            if ($check->get_result()->num_rows > 0) {
                $_SESSION['error'] = "Angka giliran sudah didaftarkan!";
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $insert = $conn->prepare("INSERT INTO students (student_id, password) VALUES (?, ?)");
                $insert->bind_param("ss", $student_id, $hashed);
                if ($insert->execute()) {
                    $_SESSION['student_id'] = $student_id;
                    header("Location: KVK_Registration.php");
                    exit;
                } else {
                    $_SESSION['error'] = "Daftar gagal. Cuba lagi.";
                }
            }
            $check->close();
        }
        $_SESSION['error_type'] = 'register';
        header("Location: UltimateLoginPage.php");
        exit;
    }

    // LOGIN PELAJAR
    if (empty($username) || empty($password)) {
        $_SESSION['error'] = "Sila isi semua ruangan!";
    } else {
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
                $_SESSION['error'] = "Kata laluan salah!";
            }
        } else {
            $_SESSION['error'] = "Angka giliran tidak wujud!";
        }
        $stmt->close();
    }
    $_SESSION['error_type'] = 'login';
}

// Final fallback redirect (only reached on error)
header("Location: UltimateLoginPage.php");
exit;
?>