<?php
session_start();
include("db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Determine if this is a login or registration form
    $isLogin = isset($_POST['login']);
    $isRegister = isset($_POST['register']);

    // Common inputs
    $student_id = strtoupper(trim($_POST['student_id']));
    $password = trim($_POST['password']);

    // ---------- SIGN UP / REGISTER ----------
    if ($isRegister) {
        $confirm_password = trim($_POST['confirm_password']);

        // Check if all fields filled
        if (empty($student_id) || empty($password) || empty($confirm_password)) {
            echo "<script>alert('⚠️ Sila isi semua butiran.'); window.history.back();</script>";
            exit;
        }

        // Check password match
        if ($password !== $confirm_password) {
            echo "<script>alert('😿 Kata laluan dan pengesahan tidak sepadan!'); window.history.back();</script>";
            exit;
        }

        // Check duplicate user
        $check = mysqli_query($conn, "SELECT * FROM students WHERE student_id='$student_id'");
        if (mysqli_num_rows($check) > 0) {
            echo "<script>alert('⚠️ Akaun sudah wujud!'); window.history.back();</script>";
            exit;
        }

        // Register safely
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $insert = mysqli_query($conn, "INSERT INTO students (student_id, password) VALUES ('$student_id', '$hashed')");

        if ($insert) {
            echo "<script>alert('✅ Pendaftaran berjaya! Anda kini boleh log masuk.'); window.location='UltimateLoginPage.php';</script>";
        } else {
            echo "<script>alert('❌ Ralat semasa pendaftaran.'); window.history.back();</script>";
        }

        exit;
    }

    // ---------- LOGIN ----------
    if ($isLogin) {
        if (empty($student_id) || empty($password)) {
            echo "<script>alert('⚠️ Sila isi Angka Giliran dan Kata Laluan.'); window.history.back();</script>";
            exit;
        }

        $query = mysqli_query($conn, "SELECT * FROM students WHERE student_id='$student_id'");
        if (mysqli_num_rows($query) === 1) {
            $user = mysqli_fetch_assoc($query);
            if (password_verify($password, $user['password'])) {
                // Correct login — create session
                $_SESSION['student_id'] = $user['student_id'];
                echo "<script>alert('🎉 Selamat Datang Kembali!'); window.location='THEHOMEPAGE.php';</script>";
            } else {
                echo "<script>alert('😿 Kata Laluan Salah. Sila Cuba Lagi.'); window.history.back();</script>";
            }
        } else {
            echo "<script>alert('❌ Akaun tidak dijumpai.'); window.history.back();</script>";
        }
        exit;
    }
}
?>
