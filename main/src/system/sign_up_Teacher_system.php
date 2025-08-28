<?php
require_once('../config/database.php');
session_start();

if (!isset($_SESSION['login']) || $_SESSION['login']['role'] !== 'user') {
    $_SESSION['error'] = "You must be a user to update your role.";
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['login']['user_id'];

$dbController = new DBController();
$conn = $dbController->getConn();

// ตรวจสอบอายุผู้ใช้จาก birthdate
$sql_age_check = "SELECT birthdate FROM Users WHERE user_id = ?";
$stmt_age_check = $conn->prepare($sql_age_check);
if ($stmt_age_check === false) {
    $_SESSION['error'] = "Failed to prepare age check SQL: " . $conn->error;
    header("Location: index.php");
    exit;
}

$stmt_age_check->bind_param('i', $user_id);
$stmt_age_check->execute();
$stmt_age_check->store_result();
$stmt_age_check->bind_result($birthdate);
$stmt_age_check->fetch();

// คำนวณอายุจาก birthdate
$birthdate = new DateTime($birthdate);
$now = new DateTime();
$age = $now->diff($birthdate)->y;

if ($age < 20) {
    // ถ้าอายุไม่ถึง 20 ปี ไม่อัพเดท session และไปหน้าแรก
    $_SESSION['error'] = "You must be at least 20 years old to update your role.";
    header("Location: ../index.php");
    exit;
}

// คำนวณอายุจากวันที่เกิด และตรวจสอบว่าอายุไม่น้อยกว่า 20 ปี
$sql = "UPDATE Users SET role = 'Instructor' WHERE user_id = ? AND birthdate <= CURDATE() - INTERVAL 20 YEAR";

// เตรียมคำสั่ง SQL
$stmt = $conn->prepare($sql);

// เช็คว่าเตรียมคำสั่งสำเร็จหรือไม่
if ($stmt === false) {
    $_SESSION['error'] = "Failed to prepare the SQL statement: " . $conn->error;
    header("Location: ../index.php");
    exit;
}

// เชื่อมค่าของ ? กับ user_id
$stmt->bind_param('i', $user_id);

// ตรวจสอบว่า execute สำเร็จหรือไม่
if ($stmt->execute()) {
    // อัพเดท role ใน session ให้เป็น 'Instructor'
    $_SESSION['login']['role'] = 'Instructor';
    $_SESSION['success'] = "Your role has been updated to Instructor.";
} else {
    $_SESSION['error'] = "Failed to update your role.";
}

header("../page/Instructor/course_list.php");
exit;
?>
