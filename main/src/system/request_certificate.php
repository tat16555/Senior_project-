<?php
session_start();

// เชื่อมต่อกับฐานข้อมูล
require '../config/database.php';
$db = new DBController();

// ตรวจสอบว่าผู้ใช้มีการทำแบบทดสอบผ่านหรือไม่
if (!isset($_SESSION['quiz_result'])) {
    echo "ข้อมูลการทำแบบทดสอบไม่ถูกต้อง.";
    exit;
}

$quiz_result = $_SESSION['quiz_result'];

// ดึงข้อมูลจาก session
$quiz_title = $quiz_result['quiz_title'];
$course_code = $quiz_result['course_code'];
$score = $quiz_result['score'];
$score_full = $quiz_result['score_full'];
$user_id = $_SESSION['login']['user_id'];  // user_id จาก session
$status = $quiz_result['status'];

// ตรวจสอบสถานะการผ่านของการทดสอบ
if ($status != 'passed') {
    echo "ไม่ผ่านการทดสอบ ไม่สามารถขอใบ Certificate ได้.";
    exit;
}

// สร้างไฟล์ใบ Certificate โดยใช้ GD Library หรือ ImageMagick
$certificate_image_path = "../uploads/certificates/{$user_id}_{$course_code}.png";

// โหลดภาพพื้นหลังใบ certificate
$certificate_background = imagecreatefrompng('../img/certificate.png');

// กำหนดสีของข้อความ
$text_color = imagecolorallocate($certificate_background, 0, 0, 0);  // สีดำ

// กำหนดฟอนต์ (กรุณาใช้ฟอนต์ TTF ที่สามารถใช้ในระบบได้)
$font_path = '../assets/font/Sarabun-Bold.ttf';  // ต้องแทนที่ด้วยเส้นทางฟอนต์ TTF ที่คุณใช้

$font_size = 80;
$font_angle = 0;
$text_color = imagecolorallocate($certificate_background, 0, 0, 0);  // สีดำ

// ข้อความที่จะแสดง
$user_name = "{$_SESSION['login']['first_name']} {$_SESSION['login']['last_name']}";
$score_text = "คะแนน: {$score} / {$score_full}";

// คำนวณตำแหน่ง x, y สำหรับข้อความ
function getCenteredPosition($text, $font_size, $font_path, $image_width) {
    // หาขนาดของข้อความ
    $bbox = imagettfbbox($font_size, 0, $font_path, $text);
    $text_width = $bbox[2] - $bbox[0];  // ความกว้างของข้อความ
    $x = ($image_width - $text_width) / 2;  // ตำแหน่ง x ที่กึ่งกลาง
    return $x;
}

// คำนวณตำแหน่งกึ่งกลางสำหรับข้อความแต่ละข้อความ
$font_x_title = getCenteredPosition($quiz_title, $font_size, $font_path, imagesx($certificate_background));
$font_x_name = getCenteredPosition($user_name, $font_size, $font_path, imagesx($certificate_background));
$font_x_score = getCenteredPosition($score_text, $font_size, $font_path, imagesx($certificate_background));

// กำหนดตำแหน่ง y สำหรับข้อความ
$font_y = 600;  // ตำแหน่งเริ่มต้นของ y

// เพิ่มข้อความลงในใบ certificate
imagettftext($certificate_background, $font_size, $font_angle, $font_x_title, $font_y, $text_color, $font_path, $quiz_title);

// เพิ่มข้อมูลผู้เรียน
$font_y += 100;  // เพิ่มตำแหน่ง y สำหรับข้อความถัดไป
imagettftext($certificate_background, $font_size, $font_angle, $font_x_name, $font_y, $text_color, $font_path, $user_name);

// เพิ่มคะแนน
$font_y += 100;  // เพิ่มตำแหน่ง y สำหรับข้อความถัดไป
imagettftext($certificate_background, $font_size, $font_angle, $font_x_score, $font_y, $text_color, $font_path, $score_text);

// บันทึกภาพใบ Certificate
imagepng($certificate_background, $certificate_image_path);

// ทำลายการเชื่อมโยงภาพ
imagedestroy($certificate_background);

// บันทึกข้อมูลในฐานข้อมูล
$conn = $db->getConn();
$sql = "INSERT INTO certificates (course_code, user_id, quiz_id, img, certificate_title, status) 
        VALUES (?, ?, ?, ?, ?, 'active')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiiss", $course_code, $user_id, $quiz_result['quiz_id'], $certificate_image_path, $quiz_title);
$stmt->execute();

// ปิดการเชื่อมต่อฐานข้อมูล
$stmt->close();
$conn->close();

// แสดงใบ Certificate ที่สร้างขึ้น
echo "<h2>ใบ Certificate ของคุณ</h2>";
echo "<img src='../uploads/certificates/{$user_id}_{$course_code}.png' alt='Certificate' />";
?>
