<?php 
session_start();
$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Masuk Atau Cipta Akaun KVKaunsel</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Montserrat', sans-serif; }
        body {
            background: url("UltimateLoginPageBg2.jpg") no-repeat center center fixed;
            background-size: cover;
            display: flex; align-items: center; justify-content: center; flex-direction: column;
            height: 100vh;
        }
        .container {
            background-color: #fff;
            border-radius: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.35);
            position: relative; overflow: hidden;
            width: 768px; max-width: 100%; min-height: 480px;
        }
        .container p { font-size: 14px; margin: 20px 0; }
        .container a { color: #333; font-size: 13px; text-decoration: none; margin: 15px 0 10px; }

        .container button {
            background-color: #af74b1;
            color: #fff;
            font-size: 12px;
            padding: 10px 45px;
            border: 1px solid transparent;
            border-radius: 8px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-top: 10px;
            cursor: pointer;
            transition: 0.3s ease;
        }
        .container button:hover { background-color: #8c5690; }

        .container button.hidden {
            background-color: transparent; border-color: #fff; color: #fff; font-weight: 600;
        }
        .container button.hidden:hover {
            background-color: #fff; color: #8c5690; font-weight: 700;
        }

        .container form {
            background-color: #fff;
            display: flex; align-items: center; justify-content: center;
            flex-direction: column;
            padding: 0 40px;
            height: 100%;
        }

        .container input {
            background-color: #eee;
            border: none;
            margin: 8px 0;
            padding: 10px 15px;
            font-size: 13px;
            border-radius: 8px;
            width: 100%;
            outline: none;
        }

        .form-container { position: absolute; top: 0; height: 100%; transition: all 0.6s ease-in-out; }
        .sign-in { left: 0; width: 50%; z-index: 2; }
        .container.active .sign-in { transform: translateX(100%); }

        .sign-up { left: 0; width: 50%; opacity: 0; z-index: 1; }
        .container.active .sign-up { transform: translateX(100%); opacity: 1; z-index: 5; animation: move 0.6s; }

        @keyframes move {
            0%,49.99%{opacity:0;z-index:1;}50%,100%{opacity:1;z-index:5;}
        }

        .toggle-container {
            position: absolute; top: 0; left: 50%;
            width: 50%; height: 100%; overflow: hidden;
            transition: all 0.6s ease-in-out; z-index: 1000;
            border-radius: 150px 0 0 100px;
        }
        .container.active .toggle-container {
            transform: translateX(-100%);
            border-radius: 0 150px 100px 0;
        }

        .toggle {
            background: linear-gradient(to right, #af74b1, #8c5c8d);
            color: #fff; position: relative; left: -100%;
            height: 100%; width: 200%;
            transform: translateX(0);
            transition: all 0.6s ease-in-out;
        }
        .container.active .toggle { transform: translateX(50%); }

        .toggle-panel {
            position: absolute; display: flex; align-items: center; justify-content: center;
            flex-direction: column; padding: 0 30px; text-align: center;
            top: 0; height: 100%; width: 50%;
            transition: all 0.6s ease-in-out;
        }
        .toggle-left { transform: translateX(-200%); }
        .container.active .toggle-left { transform: translateX(0); }
        .toggle-right { right: 0; transform: translateX(0); }
        .container.active .toggle-right { transform: translateX(200%); }

        #bg-video {
            position: fixed; right: 0; bottom: 0;
            min-width: 100%; min-height: 100%;
            z-index: -1; object-fit: cover;
        }

        .message-box {
            margin-top: 12px;
            padding: 10px 15px;
            border-radius: 10px;
            font-size: 13px;
            text-align: center; 
            font-weight: 500;
            animation: fadeIn 0.4s ease;
        }
        .message-error {
            background-color: #ffd6e0;
            color: #b30044;
            border: 1px solid #ffabc0;
        }
        .message-success {
            background-color: #e2ffe7;
            color: #2d7a43;
            border: 1px solid #a2e0b1;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Password Toggle Eye Icon */
.password-wrapper {
    position: relative;
    width: 100%;
}
.password-wrapper input {
    padding-right: 46px; /* ruang untuk icon mata */
}
.toggle-password {
    position: absolute;
    top: 50%;
    right: 14px;
    transform: translateY(-50%);
    cursor: pointer;
    color: #8c5c8d;
    font-size: 18px;
    transition: all 0.3s ease;
    user-select: none;
}
.toggle-password:hover {
    color: #af74b1;
    transform: translateY(-50%) scale(1.15);
}
    </style>
</head>
<body>
<video autoplay muted loop id="bg-video">
    <source src="VideoGalleries/KVK_SecurityBackVideo.mp4" type="video/mp4">
</video>


<div class="container <?php echo $error ? 'active' : ''; ?>" id="container">

   <!-- Sign Up Form -->
<div class="form-container sign-up">
    <form method="POST" action="process_auth.php">
        <img src="ImageGalleries/LOGO_KV.png" width="150">
        <h1>Cipta Akaun</h1>
        <input type="hidden" name="action" value="register">
        
        <input type="text" name="student_id" placeholder="Angka Giliran" required>
        
        <!-- Password + Mata -->
        <div class="password-wrapper">
            <input type="password" name="password" id="reg-password" placeholder="Kata Laluan" required>
            <i class="fas fa-eye toggle-password" onclick="togglePass('reg-password', this)"></i>
        </div>
        
        <!-- Confirm Password + Mata -->
        <div class="password-wrapper">
            <input type="password" name="confirm_password" id="reg-confirm" placeholder="Sahkan Kata Laluan" required>
            <i class="fas fa-eye toggle-password" onclick="togglePass('reg-confirm', this)"></i>
        </div>
        
<?php if($error): ?>
    <div class="message-box message-error">
        <?php echo $error; ?>
    </div>
<?php endif; ?>
        <button type="submit" name="register">Daftar Sekarang</button>
    </form>
</div>

<!-- Sign In Form -->
<div class="form-container sign-in">
   <form method="POST" action="process_auth.php">
        <img src="ImageGalleries/LOGO_KV.png" width="150">
        <h1>Log Masuk</h1>
        <input type="hidden" name="action" value="login">
        
        <input type="text" name="student_id" placeholder="Angka Giliran" required>
        
        <!-- Password + Mata -->
        <div class="password-wrapper">
            <input type="password" name="password" id="login-password" placeholder="Kata Laluan" required>
            <i class="fas fa-eye toggle-password" onclick="togglePass('login-password', this)"></i>
        </div>
        
        <?php if(!empty($error)): ?>
    <div class="message-box message-error">
        <?php echo $error; ?>
    </div>
<?php endif; ?>
        <a href="#">Lupa Kata Laluan?</a>
        
        <button type="submit" name="login">Log Masuk</button>
    </form>
</div>

    <div class="toggle-container">
        <div class="toggle">
            <div class="toggle-panel toggle-left">
                <h1>Selamat Kembali!</h1>
                <p>Jika anda sudah mendaftar, sila Log Masuk untuk mengakses sistem ini.</p>
                <button class="hidden" id="login" type="button">Log Masuk</button>
            </div>
            <div class="toggle-panel toggle-right">
                <h1>Hi Awak!</h1>
                <p>Mulakan pengalaman anda dengan mendaftarkan akaun di sini.</p>
                <button class="hidden" id="register" type="button">Daftar Sekarang</button>
            </div>
        </div>
    </div>
</div>

<script>

    setTimeout(() => {
    const msg = document.getElementById('error-msg');
    if (msg) {
        msg.classList.add('hide');
        setTimeout(() => msg.remove(), 700);
    }
}, 6500);

const container = document.getElementById('container');
document.getElementById('register').addEventListener('click', () => container.classList.add("active"));
document.getElementById('login').addEventListener('click', () => container.classList.remove("active"));

function togglePass(inputId, icon) {
    const input = document.getElementById(inputId);
    if (input.type === "password") {
        input.type = "text";
        icon.classList.replace("fa-eye", "fa-eye-slash");
    } else {
        input.type = "password";
        icon.classList.replace("fa-eye-slash", "fa-eye");
    }
}


</script>
</body>
</html>
