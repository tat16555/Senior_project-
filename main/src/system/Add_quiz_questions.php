<?php
session_start();
require_once('../config/database.php'); // เชื่อมต่อกับฐานข้อมูล
$dbController = new DBController();
$conn = $dbController->getConn();

// ตรวจสอบการส่งข้อมูลจากฟอร์ม
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $quiz_id = $_POST['quiz_id'];
    $question_text = $_POST['question_text'];
    $option_a = $_POST['option_a'];
    $option_b = $_POST['option_b'];
    $option_c = $_POST['option_c'];
    $option_d = $_POST['option_d'];
    $correct_answer = $_POST['correct_answer'];

    $stmt = $conn->prepare("INSERT INTO quiz_questions (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_answer) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssss", $quiz_id, $question_text, $option_a, $option_b, $option_c, $option_d, $correct_answer);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Question added successfully!";
    } else {
        $_SESSION['error'] = "Error adding question: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

    // Redirect ไปที่หน้าที่ต้องการ
    header("Location: ../../page/Instructor/course_list.php");
    exit();
}

// เรียกใช้ฟอร์ม HTML
require_once("../../page/Instructor/course_list.php");
