<?php
//สร้าง Api ฟังชั่น คำนวนว่า courses มีบทเรียนอยู่กี่บท โดยนำข้อมูลจากตาราง lessons มาดูว่ามีแถวไหนบ้าง
//ที่มี course_code ตรงกับ course_code ของตาราง courses แล้วดูว่ามีกี่อันที่ตรงกันนันคือจำนวนของคอส
session_start();
require '../config/database.php';
$db = new DBController();

// เชื่อมต่อกับฐานข้อมูล
$conn = $db->getConn();

// คำสั่ง SQL เพื่อดึงข้อมูล course_code จากตาราง courses และคำนวณจำนวนบทเรียนที่ตรงกัน
$sql = "SELECT courses.course_code, COUNT(lessons.lesson_id) AS total_lessons
        FROM courses
        LEFT JOIN lessons ON courses.course_code = lessons.course_code
        GROUP BY courses.course_code";

$result = $conn->query($sql);

// ตรวจสอบผลลัพธ์และส่งข้อมูลกลับ
if ($result->num_rows > 0) {
    $courses_lessons = [];
    while ($row = $result->fetch_assoc()) {
        $courses_lessons[] = [
            'course_code' => $row['course_code'],
            'total_lessons' => $row['total_lessons']
        ];
    }
    // ส่งข้อมูลจำนวนบทเรียนของแต่ละคอร์สกลับในรูปแบบ JSON
    echo json_encode([
        'success' => true,
        'courses' => $courses_lessons
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No data found.'
    ]);
}

// ปิดการเชื่อมต่อ
$conn->close();
?>
