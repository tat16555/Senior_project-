<?php
session_start();

// เชื่อมต่อกับฐานข้อมูล
require '../config/database.php';
$db = new DBController();
$conn = $db->getConn();

// ตรวจสอบว่ามีข้อมูลจาก session หรือไม่
if (!isset($_SESSION['quiz_result'])) {
    echo "ข้อมูลไม่ถูกต้อง.";
    exit;
}

$quiz_result = $_SESSION['quiz_result'];
$user_id = $_SESSION['login']['user_id']; // จาก session ผู้ใช้
$quiz_id = $quiz_result['quiz_id']; // quiz_id ที่เกี่ยวข้องกับผลการทดสอบ
$certificate_title = "Certificate of Completion for " . $quiz_result['quiz_title'];
$status = "active"; // ตั้งสถานะเริ่มต้นเป็น active

// บันทึกข้อมูลใบ Certificate ในฐานข้อมูล
$sql = "INSERT INTO certificates (user_id, quiz_id, certificate_title, status) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiss", $user_id, $quiz_id, $certificate_title, $status);
$stmt->execute();

// ตรวจสอบผลลัพธ์การบันทึก
if ($stmt->affected_rows > 0) {
    echo "<div class='container mt-5'>";
    echo "<div class='alert alert-success'>ใบ Certificate ของคุณถูกสร้างเรียบร้อยแล้ว!</div>";
    echo "<a href='view_certificate.php' class='btn btn-primary'>ดูใบ Certificate</a>";
    echo "</div>";
} else {
    echo "<div class='container mt-5'>";
    echo "<div class='alert alert-danger'>เกิดข้อผิดพลาดในการสร้างใบ Certificate</div>";
    echo "<a href='courses.php' class='btn btn-primary'>กลับ</a>";
    echo "</div>";
}

// ปิดการเชื่อมต่อ
$conn->close();
// ลบข้อมูลคะแนนจาก session หลังจากที่แสดงผล
// unset($_SESSION['quiz_result']);
?>
