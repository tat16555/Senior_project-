<?php
session_start();
require_once('../../../config/database.php'); // เชื่อมต่อกับฐานข้อมูล

// ตรวจสอบว่ามีการส่งข้อมูลมา หรือไม่
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ตรวจสอบค่าจากฟอร์มที่ส่งมา
        // รับข้อมูลจากฟอร์ม
        $question_text = $_POST['question_text'];
        $option_a = $_POST['option_a'];
        $option_b = $_POST['option_b'];
        $option_c = $_POST['option_c'];
        $option_d = $_POST['option_d'];
        $correct_answer = $_POST['correct_answer'];
        $question_id = intval($_POST['question_id']);
        
        // เชื่อมต่อฐานข้อมูล
        $dbController = new DBController();
        $conn = $dbController->getConn();
        
        // เตรียมคำสั่ง SQL เพื่ออัปเดตข้อมูล
        $stmt = $conn->prepare("UPDATE quiz_questions SET question_text = ?, option_a = ?, option_b = ?, option_c = ?, option_d = ?, correct_answer = ? WHERE question_id = ?");
        $stmt->bind_param("ssssssi", $question_text, $option_a, $option_b, $option_c, $option_d, $correct_answer, $question_id);
        
        // ดำเนินการคำสั่ง SQL
        if ($stmt->execute()) {
            // หากอัปเดตสำเร็จ
            $_SESSION['success'] = "Question updated successfully.";
            header("Location: ../../pages/tables/courses.php");
        } else {
            // หากเกิดข้อผิดพลาด
            $_SESSION['error'] = "Failed to update question.";
            header("Location: ../../pages/tables/courses.php"); // เปลี่ยนไปที่หน้า edit_lesson.php และส่ง question_id
        }
        
        // ปิดการเชื่อมต่อ
        $stmt->close();
        $conn->close();
} else {
    $_SESSION['error'] = "Invalid request method.";
    header("Location: ../../pages/tables/courses.php"); // เปลี่ยนไปที่หน้า edit_lesson.php หากไม่ใช่ POST
}
exit;
?>