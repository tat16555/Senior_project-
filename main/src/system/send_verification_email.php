<?php 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';  // ใช้ autoload ของ Composer
require '../config/database.php';  // ไฟล์เชื่อมต่อฐานข้อมูล

function sendVerificationEmail($email, $user_id, $verification_code) {
    $verification_link = "https://true-sadly-bass.ngrok-free.app/system/verify_email.php?code=" . urlencode($verification_code) . "&user_id=" . $user_id;

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'pannatat.b@gmail.com';  // ใส่อีเมลของคุณ
        $mail->Password = 'pqok dgoq ylbq jcht';  // ใช้รหัสผ่านแอปจาก Google
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('no-reply@yourwebsite.com', 'Your Website');
        $mail->addAddress($email);
        $mail->addReplyTo('no-reply@yourwebsite.com', 'No-reply');

        $mail->Subject = 'ยืนยันการสมัครสมาชิก';
        $mail->Body = "คลิกที่ลิงก์ด้านล่างเพื่อยืนยันอีเมลของคุณ:\n\n" . $verification_link;

        $mail->send();
        echo '✅ อีเมลยืนยันถูกส่งไปยัง ' . $email;
    } catch (Exception $e) {
        echo "❌ ไม่สามารถส่งอีเมลได้. Error: {$mail->ErrorInfo}";
    }
}

// ตัวอย่างการเรียกใช้ฟังก์ชัน
// $user_id = 1;  
// $email = "user@example.com";
// $verification_code = md5(rand());

sendVerificationEmail($email, $user_id, $verification_code);
?>
