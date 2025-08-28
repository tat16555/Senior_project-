<?php
session_start();

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบหรือไม่
if (!isset($_SESSION['login'])) {
    echo "กรุณาเข้าสู่ระบบก่อน";
    exit;
}

// เชื่อมต่อกับฐานข้อมูล
require '../config/database.php';
$db = new DBController();
$conn = $db->getConn();

// รับค่า certificate_id จาก URL
$certificate_id = isset($_GET['certificate_id']) ? $_GET['certificate_id'] : null;

if ($certificate_id === null) {
    echo "ไม่พบข้อมูลใบ Certificate";
    exit;
}

// ดึงข้อมูลใบ Certificate จากฐานข้อมูล
$sql = "SELECT * FROM certificates WHERE certificate_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $certificate_id);
$stmt->execute();
$result = $stmt->get_result();

// ตรวจสอบว่าใบ Certificate นี้มีอยู่หรือไม่
if ($result->num_rows == 0) {
    echo "ไม่พบใบ Certificate นี้";
    exit;
}

$certificate = $result->fetch_assoc();
$certificate_title = $certificate['certificate_title'];
$status = $certificate['status'];

// ตัวอย่างไฟล์รูปที่ใช้เป็นใบ Certificate
$certificate_image = 'path_to_certificate_image/' . $certificate_title . '.jpg'; // หรือ PDF หากต้องการให้เป็น PDF

// ตรวจสอบว่าไฟล์มีอยู่หรือไม่
if (!file_exists($certificate_image)) {
    echo "ไฟล์ใบ Certificate ไม่พบ";
    exit;
}

// ตั้งค่าให้เบราว์เซอร์ดาวน์โหลดไฟล์
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($certificate_image) . '"');
header('Content-Length: ' . filesize($certificate_image));
readfile($certificate_image);

// ปิดการเชื่อมต่อ
$conn->close();
exit;
?>
