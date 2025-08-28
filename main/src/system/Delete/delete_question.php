<?php
session_start();
require_once('../../config/database.php'); // เชื่อมต่อกับฐานข้อมูล

// ตรวจสอบว่าได้รับ 'question_id' จาก URL หรือไม่
if (isset($_GET['question_id'])) {
    $question_id = $_GET['question_id'];

    // เชื่อมต่อกับฐานข้อมูล
    $dbController = new DBController();
    $conn = $dbController->getConn();

    // เตรียมคำสั่ง SQL เพื่อทำการลบคำถาม
    $stmt = $conn->prepare("DELETE FROM quiz_questions WHERE question_id = ?");
    $stmt->bind_param("i", $question_id);

    // ดำเนินการคำสั่ง SQL
    if ($stmt->execute()) {
        // หากลบสำเร็จ
        $_SESSION['success'] = "Question deleted successfully.";
        header("Location: ../../page/Instructor/course_list.php"); // เปลี่ยนไปที่หน้าที่ต้องการหลังจากลบสำเร็จ
        exit;
    } else {
        // หากเกิดข้อผิดพลาด
        $_SESSION['error'] = "Failed to delete question.";
        header("Location: ../../page/Instructor/course_list.php"); // เปลี่ยนไปที่หน้าที่ต้องการหากเกิดข้อผิดพลาด
        exit;
    }

    // ปิดการเชื่อมต่อ
    $stmt->close();
    $conn->close();
} else {
    // หากไม่มี 'question_id'
    $_SESSION['error'] = "Invalid request.";
    header("Location: ../../page/Instructor/course_list.php"); // เปลี่ยนไปที่หน้าที่ต้องการหากไม่มีข้อมูล
    exit;
}
?>
