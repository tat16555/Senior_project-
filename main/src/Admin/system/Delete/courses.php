<?php
session_start();
require_once('../../../config/database.php');

// ตรวจสอบว่ามีการส่ง course_code มาหรือไม่ 
if (isset($_GET['course_code'])) {
    $course_code = $_GET['course_code']; // ใช้ course_code ในการลบ

    // สร้างอินสแตนซ์ของ DBController
    $dbController = new DBController();
    $conn = $dbController->getConn();

    // เริ่มทำการลบข้อมูลทีละขั้นตอน
    $conn->begin_transaction(); // เริ่มต้นการทำงานในรูปแบบ Transaction

    try {
        // ตรวจสอบคอลัมน์ 'course_code' ในตาราง quiz_questions
        $result_questions = $conn->query("SHOW COLUMNS FROM quiz_questions LIKE 'course_code'");
        if ($result_questions->num_rows > 0) {
            // ถ้ามีคอลัมน์ course_code
            $stmt_questions = $conn->prepare("DELETE FROM quiz_questions WHERE course_code = ?");
            if (!$stmt_questions) {
                throw new Exception("Failed to prepare statement for quiz_questions: " . $conn->error);
            }
            $stmt_questions->bind_param("s", $course_code);
            $stmt_questions->execute();
        }

        // ตรวจสอบคอลัมน์ 'course_code' ในตาราง quizzes
        $result_quiz = $conn->query("SHOW COLUMNS FROM quizzes LIKE 'course_code'");
        if ($result_quiz->num_rows > 0) {
            // ถ้ามีคอลัมน์ course_code
            $stmt_quiz = $conn->prepare("DELETE FROM quizzes WHERE course_code = ?");
            if (!$stmt_quiz) {
                throw new Exception("Failed to prepare statement for quizzes: " . $conn->error);
            }
            $stmt_quiz->bind_param("s", $course_code);
            $stmt_quiz->execute();
        }

        // ตรวจสอบคอลัมน์ 'course_code' ในตาราง lessons
        $result_lessons = $conn->query("SHOW COLUMNS FROM lessons LIKE 'course_code'");
        if ($result_lessons->num_rows > 0) {
            // ถ้ามีคอลัมน์ course_code
            $stmt_lessons = $conn->prepare("DELETE FROM lessons WHERE course_code = ?");
            if (!$stmt_lessons) {
                throw new Exception("Failed to prepare statement for lessons: " . $conn->error);
            }
            $stmt_lessons->bind_param("s", $course_code);
            $stmt_lessons->execute();
        }

        // ลบข้อมูลคอร์สจากตาราง courses
        $stmt_course = $conn->prepare("DELETE FROM courses WHERE course_code = ?");
        if (!$stmt_course) {
            throw new Exception("Failed to prepare statement for courses: " . $conn->error);
        }
        $stmt_course->bind_param("s", $course_code);
        $stmt_course->execute();

        // ถ้าทุกขั้นตอนสำเร็จ commit transaction
        $conn->commit();

        // แจ้งผลการลบ
        $_SESSION['success'] = "Course with code $course_code has been deleted successfully!";
        header("Location: ../../pages/tables/courses.php");
        exit;
    } catch (Exception $e) {
        // หากเกิดข้อผิดพลาด ย้อนกลับการทำงานทั้งหมด
        $conn->rollback();
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: ../../pages/tables/courses.php");
        exit;
    }
} else {
    // หากไม่มี course_code ให้กลับไปหน้าจัดการผู้ใช้
    header("Location: ../../pages/tables/courses.php");
    exit;
}
?>
