<?php
session_start();
require_once('../../../config/database.php');

// ตรวจสอบว่ามีการส่ง quiz_id มาหรือไม่
if (isset($_GET['quiz_id'])) {
    $quiz_id = intval($_GET['quiz_id']); // แปลงค่าเป็นตัวเลขเพื่อความปลอดภัย

    // สร้างอินสแตนซ์ของ DBController
    $dbController = new DBController();
    $conn = $dbController->getConn();

    // เตรียมคำสั่ง SQL เพื่อลบผู้ใช้
    $stmt = $conn->prepare("DELETE FROM quizzes WHERE quiz_id = ?");
    $stmt->bind_param("i", $quiz_id);

    if ($stmt->execute()) {
        // ลบสำเร็จ
        $_SESSION['success'] = "Quiz id : $quiz_id ถูกลบแล้ว!";
        header("Location: ../../pages/tables/quizzes.php");
    } else {
        $_SESSION['error'] = "ลบไม่สำเร็จ";
        // เกิดข้อผิดพลาด
        header("Location: ../../pages/tables/quizzes.php");
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: ../../pages/tables/quizzes.php");
    exit;
}
?>



<?php
session_start();
require_once('../../../config/database.php');

// ตรวจสอบว่ามีการส่ง quiz_id มาหรือไม่ 
if (isset($_GET['quiz_id'])) {
    $quiz_id = $_GET['quiz_id']; // ใช้ quiz_id ในการลบ

    // สร้างอินสแตนซ์ของ DBController
    $dbController = new DBController();
    $conn = $dbController->getConn();

    // เริ่มทำการลบข้อมูลทีละขั้นตอน
    $conn->begin_transaction(); // เริ่มต้นการทำงานในรูปแบบ Transaction

    try {
        // ลบ quiz_questions ที่เชื่อมโยงกับ quizzes
        $stmt_questions = $conn->prepare("DELETE FROM quiz_questions WHERE quiz_id = ?");
        if (!$stmt_questions) {
            throw new Exception("Failed to prepare statement for quiz_questions: " . $conn->error);
        }
        $stmt_questions->bind_param("i", $quiz_id);
        $stmt_questions->execute();

        // ลบ Quiz ที่เชื่อมโยงกับ course_code
        $stmt_quiz = $conn->prepare("DELETE FROM quizzes WHERE quiz_id = ?");
        if (!$stmt_quiz) {
            throw new Exception("Failed to prepare statement for quizzes: " . $conn->error);
        }
        $stmt_quiz->bind_param("i", $quiz_id);
        $stmt_quiz->execute();
        // ถ้าทุกขั้นตอนสำเร็จ 
        $conn->commit();

        // แจ้งผลการลบ
        $_SESSION['success'] = "Course with code $quiz_id has been deleted successfully!";
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
