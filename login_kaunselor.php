<?php session_start(); 
$error = '';
if ($_POST) {
    $pdo = new PDO("mysql:host=localhost;dbname=kvkaunsel_db", "root", "");
    $stmt = $pdo->prepare("SELECT * FROM kaunselor WHERE counselor_id = ?");
    $stmt->execute([$_POST['username']]);
    $user = $stmt->fetch();

    if ($user && password_verify($_POST['password'], $user['password'])) {
        $_SESSION['kaunselor_id'] = $user['id'];
        $_SESSION['kaunselor_nama'] = $user['nama'] ?? 'Kaunselor';
        header("Location: KVK_Admin_CgMuhirman_Tempahan.php");
        exit;
    } else {
        $error = "Username atau password salah";
    }
}
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Kaunselor</title>
    <style>
        body{background:#f0f2f5;font-family:Arial;display:flex;justify-content:center;align-items:center;height:100vh;margin:0}
        .login-box{background:white;padding:40px 50px;border-radius:16px;box-shadow:0 15px 40px rgba(0,0,0,0.1);width:400px}
        h2{text-align:center;color:#8b5cf6;margin-bottom:30px;font-size:28px}
        input{width:100%;padding:14px;margin:10px 0;border:2px solid #ddd;border-radius:10px;font-size:16px}
        input:focus{border-color:#8b5cf6;outline:none}
        button{width:100%;padding:14px;background:#8b5cf6;color:white;border:none;border-radius:10px;font-size:18px;cursor:pointer}
        button:hover{background:#6b21a8}
        .error{color:red;text-align:center;margin-top:10px}
    </style>
</head>
<body>
<div class="login-box">
    <h2>Login Kaunselor</h2>
    <form method="post">
        <input type="text" name="username" placeholder="Username (contoh: AdminKVK01)" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Log Masuk</button>
    </form>
    <?php if($error) echo "<p class='error'>$error</p>"; ?>
    <p style="text-align:center;margin-top:20px;font-size:14px;color:#666">
        AdminKVK01 → Encik Muhirman<br>
        AdminKVK02 → Tanita<br>
        AdminKVK03 → Whilemina<br>
        Password semua: <b>123</b>
    </p>
</div>
</body>
</html>