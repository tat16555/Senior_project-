<?php 
session_start();
require_once('../config/database.php'); // เชื่อมต่อกับฐานข้อมูล

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_code = $_POST['course_code'];
    $course_name = $_POST['course_name'];
    $course_description = $_POST['course_description'];
    $user_id = $_SESSION['login']['user_id'];
    // $created_at = date('Y-m-d H:i:s'); 
    // $updated_at = date('Y-m-d H:i:s');

    $dbController = new DBController();
    $conn = $dbController->getConn();

    // ตรวจสอบว่า Course Code ซ้ำหรือไม่
    $stmt = $conn->prepare("SELECT * FROM courses WHERE course_code = ?");
    $stmt->bind_param("s", $course_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Course Code already exists.";
        header("Location: ../page/Instructor/add_course.php");
        exit;
    }

    // จัดการอัปโหลดรูปภาพ
    $upload_dir = "../uploads/courses/";
    $course_folder = $upload_dir . $course_code;

    // ตรวจสอบและสร้างโฟลเดอร์หากไม่มี
    if (!is_dir($course_folder)) {
        mkdir($course_folder, 0777, true);
    }

    $img_path = ""; // ค่าเริ่มต้นของ img
    if (!empty($_FILES['course_image']['name'])) {
        $img_name = basename($_FILES["course_image"]["name"]);
        $target_file = $course_folder . "/" . $img_name;

        if (move_uploaded_file($_FILES["course_image"]["tmp_name"], $target_file)) {
            $img_path = "uploads/courses/" . $course_code . "/" . $img_name;
        } else {
            $_SESSION['error'] = "Error uploading image.";
            header("Location: ../page/Instructor/add_course.php");
            exit;
        }
    }

    // เพิ่มข้อมูลหลักสูตรลงในฐานข้อมูล
    $stmt = $conn->prepare("INSERT INTO courses (course_code, course_name, course_description, img, user_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $course_code, $course_name, $course_description, $img_path, $user_id);

    if ($stmt->execute()) {
        // ใช้ course_code ที่เพิ่งเพิ่มสำหรับสร้าง Quiz
        $quiz_title = $course_name;
        $score_full = 100;
        $pass_score = 60;

        $stmt_quiz = $conn->prepare("INSERT INTO quizzes (course_code, quiz_title, score_full, pass_score) VALUES (?, ?, ?, ?)");
        $stmt_quiz->bind_param("ssii", $course_code, $quiz_title, $score_full, $pass_score);

        if ($stmt_quiz->execute()) {
            $_SESSION['success'] = "Course and Quiz added successfully!";
            header("Location: ../page/Instructor/Add_Lesson.php?course_code=" . urlencode($course_code));
            exit;
        } else {
            $_SESSION['error'] = "Error creating quiz: " . $stmt_quiz->error;
            header("Location: ../page/Instructor/add_course.php");
            exit;
        }
        
    } else {
        $_SESSION['error'] = "Error: " . $stmt->error;
        header("Location: ../../page/Instructor/add_course.php");
        exit;
    }

    $stmt->close();
    $conn->close();
}
?>
