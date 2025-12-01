    <?php
    session_start();
    include("db.php");

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $student_id = strtoupper(trim($_POST['student_id']));
        $password   = trim($_POST['password']);

        // NEW: read action from form
        $action = $_POST['action'] ?? '';

        // ===================== CHECK ADMIN DULU =====================
$admin = $conn->prepare("SELECT id, counselor_id, password FROM caunselor WHERE counselor_id = ?");
$admin->bind_param("s", $student_id);
$admin->execute();
$adminResult = $admin->get_result();

if ($adminRow = $adminResult->fetch_assoc()) {

    // password admin hashed SHA2
    $verifyAdmin = $conn->prepare(
        "SELECT counselor_id FROM caunselor 
         WHERE counselor_id = ? AND password = SHA2(?, 256)"
    );
    $verifyAdmin->bind_param("ss", $student_id, $password);
    $verifyAdmin->execute();
    $verifyResult = $verifyAdmin->get_result();

    if ($verifyResult->num_rows > 0) {

        $_SESSION['admin_id'] = $adminRow['counselor_id'];

        // ROUTE IKUT ID ADMIN
        if ($adminRow['id'] == 1) {
            header("Location: KVK_Admin_CgMuhirman_Utama.php");
            exit;
        }
        elseif ($adminRow['id'] == 2) {
            header("Location: KVK_Admin_CgWhilemina_Utama.php");
            exit;
        }
        elseif ($adminRow['id'] == 3) {
            header("Location: KVK_Admin_CgTanita_Utama.php");
            exit;
        }

    } else {
        $_SESSION['error'] = "Kata laluan salah untuk admin!";
        $_SESSION['error_type'] = 'login';
        header("Location: UltimateLoginPage.php");
        exit;
    }
}


        // ========== DAFTAR ==========
        if ($action === 'register') {  // <--- UPDATED ONLY THIS
            $confirm = trim($_POST['confirm_password']);

            if (empty($student_id) || empty($password) || empty($confirm)) {
                $_SESSION['error'] = "Pastikan semua kotah diisi ya!";
                $_SESSION['error_type'] = 'register';
            }
            elseif ($password !== $confirm) {
                $_SESSION['error'] = "Kata laluan salah, sila cuba lagi!";
                $_SESSION['error_type'] = 'register';
            }
            else {
                $check = $conn->prepare("SELECT student_id FROM students WHERE student_id = ?");
                $check->bind_param("s", $student_id);
                $check->execute();
                if ($check->get_result()->num_rows > 0) {
                    $_SESSION['error'] = "Akaun ini dah wujud";
                    $_SESSION['error_type'] = 'register';
                }
                else {
                    $hashed = password_hash($password, PASSWORD_DEFAULT);
                    $insert = $conn->prepare("INSERT INTO students (student_id, password) VALUES (?, ?)");
                    $insert->bind_param("ss", $student_id, $hashed);
                    if ($insert->execute()) {
                        $_SESSION['student_id'] = $student_id;
                        unset($_SESSION['error'], $_SESSION['error_type']);
                        header("Location: KVK_Registration.php");
                        exit;
                    } else {
                        $_SESSION['error'] = "Ada masalah sikit. Cuba lagi!";
                        $_SESSION['error_type'] = 'register';
                    }
                }
            }
            header("Location: UltimateLoginPage.php");
            exit;
        }

        // ========== LOGIN ==========
        if ($action === 'login') {   // <--- UPDATED ONLY THIS
            if (empty($student_id) || empty($password)) {
                $_SESSION['error'] = "Pastikan semua kotah diisi ya!";
                $_SESSION['error_type'] = 'login';
            }
            else {
                $stmt = $conn->prepare("SELECT student_id, password FROM students WHERE student_id = ?");
                $stmt->bind_param("s", $student_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($row = $result->fetch_assoc()) {
                    if (password_verify($password, $row['password'])) {
                        $_SESSION['student_id'] = $row['student_id'];
                        unset($_SESSION['error'], $_SESSION['error_type']);
                        header("Location: KVK_Registration.php");
                        exit;
                    } else {
                        $_SESSION['error'] = "Kata laluan salah, sila cuba lagi!";
                        $_SESSION['error_type'] = 'login';
                    }
                } else {
                    $_SESSION['error'] = "Akaun tak wujud! Daftar dulu";
                    $_SESSION['error_type'] = 'login';
                }
            }
            header("Location: UltimateLoginPage.php");
            exit;
        }
    }
    ?>