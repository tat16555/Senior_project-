<?php
session_start();
require_once('../../../config/database.php'); // เชื่อมต่อกับฐานข้อมูล

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_code = $_POST['course_code'];
    $lesson_title = $_POST['lesson_title'];
    $lesson_description = $_POST['lesson_description'];
    $vdo_url = $_POST['vdo_url'];

    $dbController = new DBController();
    $conn = $dbController->getConn();

    // อัปโหลดไฟล์เนื้อหา
    if (isset($_FILES['content']) && $_FILES['content']['error'] == 0) {
        $fileName = $_FILES['content']['name'];
        $fileTmpName = $_FILES['content']['tmp_name'];
        $fileType = $_FILES['content']['type'];

        // อ่านไฟล์เป็น binary เพื่อเก็บในฐานข้อมูล
        $content = file_get_contents($fileTmpName);
    } else {
        $_SESSION['error'] = "Error uploading content file.";
        header("Location: ../../pages/tables/courses.php");
        exit;
    }

    // ดึงหมายเลขบทเรียนล่าสุดในหลักสูตร
    $stmt = $conn->prepare("SELECT MAX(lesson_number) as max_number FROM lessons WHERE course_code = ?");
    $stmt->bind_param("i", $course_code);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $lesson_number = $row['max_number'] ? $row['max_number'] + 1 : 1;

    // เพิ่มข้อมูลบทเรียนในฐานข้อมูล
    $stmt = $conn->prepare("
        INSERT INTO lessons (course_code, lesson_number, lesson_title, lesson_description, content, vdo_url) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("iissss", $course_code, $lesson_number, $lesson_title, $lesson_description, $content, $vdo_url);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Lesson added successfully!";
        header("Location: ../../pages/tables/courses.php");
    } else {
        $_SESSION['error'] = "Error: " . $stmt->error;
        header("Location: ../../pages/tables/courses.php");
    }

    $stmt->close();
    $conn->close();
}
?>
