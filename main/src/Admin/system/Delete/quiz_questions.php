<?php
session_start();
require_once('../../../config/database.php');

// ตรวจสอบว่ามีการส่ง question_id มาหรือไม่
if (isset($_GET['question_id']) && is_numeric($_GET['question_id'])) {
    $question_id = intval($_GET['question_id']); // แปลงค่าเป็นตัวเลขเพื่อความปลอดภัย

    // สร้างอินสแตนซ์ของ DBController
    $dbController = new DBController();
    $conn = $dbController->getConn();

    // ตรวจสอบการเชื่อมต่อฐานข้อมูล
    if ($conn === false) {
        die('Error connecting to the database: ' . $conn->connect_error);
    }

    // เตรียมคำสั่ง SQL เพื่อลบผู้ใช้
    $stmt = $conn->prepare("DELETE FROM quiz_questions WHERE question_id = ?");
    if ($stmt === false) {
        die('Error preparing query: ' . $conn->error);
    }

    $stmt->bind_param("i", $question_id);

    if ($stmt->execute()) {
        // ลบสำเร็จ 
        $_SESSION['success'] = "question id : $question_id ถูกลบแล้ว!";
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