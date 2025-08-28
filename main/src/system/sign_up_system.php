<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once('../config/database.php');

require '../vendor/autoload.php';  // ใช้ autoload ของ Composer
session_start();

// ใช้ ob_start() เพื่อบัฟเฟอร์การแสดงผล
ob_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับข้อมูลจากฟอร์ม
    $username = $_POST['username'];
    $email = $_POST['email'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $birthdate = $_POST['birthdate'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $dbController = new DBController();
    $conn = $dbController->getConn();

    // ตรวจสอบอีเมลหรือชื่อผู้ใช้ที่ซ้ำ
    $stmt = $conn->prepare("SELECT * FROM Users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['error'] = "ชื่อผู้ใช้หรืออีเมลนี้มีอยู่ในระบบแล้ว";
        header("Location: ../../page/sign_up.php");
        exit();
    } else {
        // เพิ่มข้อมูลผู้ใช้ใหม่
        $stmt = $conn->prepare("INSERT INTO Users (username, email, first_name, last_name, birthdate, password, email_verified) VALUES (?, ?, ?, ?, ?, ?, 0)");
        $stmt->bind_param("ssssss", $username, $email, $first_name, $last_name, $birthdate, $password);

        if ($stmt->execute()) {
            // ดึงข้อมูล user_id
            $user_id = $stmt->insert_id;
            $verification_code = md5($email . time()); // สร้างโค้ดยืนยัน

            // ส่งอีเมลยืนยัน
            sendVerificationEmail($email, $user_id, $verification_code);

            $_SESSION['success'] = "สมัครสมาชิกเรียบร้อยแล้ว! กรุณายืนยันอีเมล";
            header("Location: ../../page/sign_in.php");
            exit();
        } else {
            $_SESSION['error'] = "เกิดข้อผิดพลาด: " . $stmt->error;
            header("Location: ../../page/sign_up.php");
            exit();
        }
    }

    $stmt->close();
    $conn->close();
}

// ฟังก์ชันการส่งอีเมลยืนยัน
function sendVerificationEmail($email, $user_id, $verification_code) {
    $verification_link = "https://true-sadly-bass.ngrok-free.app/system/verify_email.php?code=" . $verification_code . "&user_id=" . $user_id;
    $subject = "Verify Your Email Address";
    $message = "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Email Verification</title>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
        <link href='verify_em_sty.css' rel='stylesheet'>
    </head>
    <body class='bg-light'>
    <div style='display: none; font-size: 1px; color: #fefefe; line-height: 1px; font-family: \"Lato\", Helvetica, Arial, sans-serif; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden;'> We're thrilled to have you here! Get ready to dive into your new account. </div>
    <table border='0' cellpadding='0' cellspacing='0' width='100%'>
        <!-- LOGO -->
        <tr>
            <td bgcolor='#FFA73B' align='center'>
                <table border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px;'>
                    <tr>
                        <td align='center' valign='top' style='padding: 40px 10px 40px 10px;'> </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td bgcolor='#FFA73B' align='center' style='padding: 0px 10px 0px 10px;'>
                <table border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px;'>
                    <tr>
                        <td bgcolor='#ffffff' align='center' valign='top' style='padding: 40px 20px 20px 20px; border-radius: 4px 4px 0px 0px; color: #111111; font-family: \"Lato\", Helvetica, Arial, sans-serif; font-size: 48px; font-weight: 400; letter-spacing: 4px; line-height: 48px;'>
                            <h1 style='font-size: 48px; font-weight: 400; margin: 2;'>Welcome!</h1> 
                            <img src='https://img.icons8.com/clouds/100/000000/handshake.png' width='125' height='120' style='display: block; border: 0px;' />
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td bgcolor='#f4f4f4' align='center' style='padding: 0px 10px 0px 10px;'>
                <table border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px;'>
                    <tr>
                        <td bgcolor='#ffffff' align='left' style='padding: 20px 30px 40px 30px; color: #666666; font-family: \"Lato\", Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;'>
                            <p style='margin: 0;'>We're excited to have you get started. First, you need to confirm your account. Just press the button below.</p>
                        </td>
                    </tr>
                    <tr>
                        <td bgcolor='#ffffff' align='left'>
                            <table width='100%' border='0' cellspacing='0' cellpadding='0'>
                                <tr>
                                    <td bgcolor='#ffffff' align='center' style='padding: 20px 30px 60px 30px;'>
                                        <table border='0' cellspacing='0' cellpadding='0'>
                                            <tr>
                                                <td align='center' style='border-radius: 3px;' bgcolor='#FFA73B'>
                                                    <a href='" . $verification_link . "' target='_blank' style='font-size: 20px; font-family: Helvetica, Arial, sans-serif; color: #ffffff; text-decoration: none; padding: 15px 25px; border-radius: 2px; border: 1px solid #FFA73B; display: inline-block;'>Confirm Account</a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr> <!-- COPY -->
                </table>
            </td>
        </tr>
        <tr>
            <td bgcolor='#f4f4f4' align='center' style='padding: 0px 10px 0px 10px;'>
                <table border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px;'>
                    <tr>
                        <td bgcolor='#f4f4f4' align='left' style='padding: 0px 30px 30px 30px; color: #666666; font-family: \"Lato\", Helvetica, Arial, sans-serif; font-size: 14px; font-weight: 400; line-height: 18px;'>
                            <p style='margin: 0;'>If these emails get annoying, please feel free to <a href='#' target='_blank' style='color: #111111; font-weight: 700;'>unsubscribe</a>.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js'></script>
    </body>
    </html>";    

    // ใช้ PHPMailer แทน mail() เพื่อส่งอีเมล
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'pannatat.b@gmail.com';  // ใช้แอพพาสเวิร์ด
        $mail->Password = 'pqok dgoq ylbq jcht';  // รหัสผ่านแอพจาก Google
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('no-reply@yourwebsite.com', 'EduInsightHub');
        $mail->addAddress($email);
        $mail->Subject = $subject;
        $mail->Body = $message;  // กำหนดเนื้อหาของอีเมลเป็น HTML
        $mail->isHTML(true);     // ระบุว่าเนื้อหาคือ HTML

        $mail->send();

        $dbController = new DBController();
        $conn = $dbController->getConn();
        $stmt = $conn->prepare("UPDATE Users SET verification_code = ? WHERE user_id = ?");
        $stmt->bind_param("si", $verification_code, $user_id);
        $stmt->execute();
        $stmt->close();

        echo '✅ อีเมลยืนยันถูกส่งไปยัง ' . $email;
    } catch (Exception $e) {
        echo "❌ ไม่สามารถส่งอีเมลได้. Error: {$mail->ErrorInfo}";
    }
}

// ปิดการบัฟเฟอร์หลังจากที่ใช้ header()
ob_end_flush();
?>
