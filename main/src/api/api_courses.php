<?php
require '../config/database.php';

header('Content-Type: application/json');

// สร้างการเชื่อมต่อกับฐานข้อมูล
$db = new DBController();
$conn = $db->getConn();

// คำสั่ง SQL เพื่อดึงข้อมูลจากตาราง courses
$sql = "SELECT * FROM courses";
$result = $conn->query($sql);

// ตรวจสอบผลลัพธ์
if ($result->num_rows > 0) {
    $courses = [];
    while ($row = $result->fetch_assoc()) {
        // ค้นหาชื่อ Instructor
        $user_id = $row['user_id'];
        $stmt_instructor = $conn->prepare("SELECT first_name, last_name FROM Users WHERE user_id = ?");
        $stmt_instructor->bind_param("i", $user_id);
        $stmt_instructor->execute();
        $instructor_result = $stmt_instructor->get_result();

        $instructor_name = "Instructor not found";
        if ($instructor_result->num_rows > 0) {
            $instructor = $instructor_result->fetch_assoc();
            $instructor_name = $instructor['first_name'] . ' ' . $instructor['last_name'];
        }

        // บันทึกข้อมูลลงในอาร์เรย์
        $courses[] = [
            'course_id' => $row['course_id'],
            'course_name' => $row['course_name'],
            'course_code' => $row['course_code'],
            'instructor_name' => $instructor_name,
            'view' => $row['view']
        ];
    }
    echo json_encode(['courses' => $courses]);
} else {
    echo json_encode(['courses' => []]);
}

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
?>
