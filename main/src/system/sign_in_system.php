<?php  
session_start(); // เริ่ม session

require_once('../config/database.php'); // เชื่อมต่อกับฐานข้อมูล

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL); // กำจัดช่องว่างและกรองอีเมล
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "กรุณากรอกอีเมลและรหัสผ่าน";
        header("Location: ../page/sign_in.php");
        exit;
    }

    $dbController = new DBController();
    $conn = $dbController->getConn();

    // ตรวจสอบอีเมลในฐานข้อมูล
    $stmt = $conn->prepare("SELECT * FROM Users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // ตรวจสอบสถานะการยืนยันอีเมล
        if ($user['email_verified'] != 1) {
            $_SESSION['error'] = "กรุณายืนยันอีเมลของคุณก่อนเข้าสู่ระบบ";
            header("Location: ../page/sign_in.php");
            exit;
        }

        // ตรวจสอบรหัสผ่าน
        if (password_verify($password, $user['password'])) {
            // รหัสผ่านถูกต้อง

            // อัปเดต last_login
            $stmt_update = $conn->prepare("UPDATE Users SET last_login = NOW() WHERE email = ?");
            $stmt_update->bind_param("s", $email);
            $stmt_update->execute();
            $stmt_update->close();

            // เก็บข้อมูลผู้ใช้ใน session
            $profile = array(
                'user_id' => $user['user_id'],
                'username' => htmlspecialchars($user['username']),
                'email' => htmlspecialchars($user['email']),
                'first_name' => htmlspecialchars($user['first_name']),
                'last_name' => htmlspecialchars($user['last_name']),
                'role' => $user['role']
            );

            $_SESSION['login'] = $profile; // เก็บข้อมูลใน session

            // เปลี่ยนเส้นทางตามบทบาท
            if ($user['role'] === 'admin') {
                $_SESSION['success'] = "เข้าสู่ระบบแล้ว!";
                header("Location: https://true-sadly-bass.ngrok-free.app/Admin/pages/tables/courses.php");
            } else {
                $_SESSION['success'] = "เข้าสู่ระบบแล้ว!";
                header("Location: ../index.php");
            }
            exit;
        } else {
            // รหัสผ่านผิด
            $_SESSION['error'] = "รหัสผ่านไม่ถูกต้อง";
            header("Location: ../page/sign_in.php");
            exit;
        }
    } else {
        // ไม่พบผู้ใช้ที่มีอีเมลนี้
        $_SESSION['error'] = "ไม่พบอีเมลนี้ในระบบ";
        header("Location: ../page/sign_in.php");
        exit;
    }

    $stmt->close();
    $conn->close();
} else {
    // หาก Method ไม่ใช่ POST
    http_response_code(405);
    echo "Method Not Allowed";
    exit;
}
?>
