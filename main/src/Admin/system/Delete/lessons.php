<?php
session_start();
require_once('../../../config/database.php');

// ตรวจสอบว่ามีการส่ง lesson_id มาหรือไม่
if (isset($_GET['lesson_id'])) {
    $lesson_id = intval($_GET['lesson_id']); // แปลงค่าเป็นตัวเลขเพื่อความปลอดภัย

    // สร้างอินสแตนซ์ของ DBController
    $dbController = new DBController();
    $conn = $dbController->getConn();


    $stmt = $conn->prepare("DELETE FROM lessons WHERE lesson_id = ?");
    $stmt->bind_param("i", $lesson_id);

    if ($stmt->execute()) {
        // ลบสำเร็จ
        $_SESSION['success'] = "lesson id : $lesson_id ถูกลบแล้ว!";
        header("Location: ../../pages/tables/courses.php");
    } else {
        $_SESSION['error'] = "ลบไม่สำเร็จ";
        // เกิดข้อผิดพลาด
        header("Location: ../../pages/tables/courses.php");
    }
    $stmt->close();
    $conn->close();
} else {
    header("Location: ../../pages/tables/courses.php");
    exit;
}
?>
