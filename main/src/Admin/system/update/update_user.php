<?php
// เริ่มเซสชัน
session_start();

// นำเข้าไฟล์เชื่อมต่อฐานข้อมูล
require_once('../../../config/database.php');

// ตรวจสอบว่ามีการส่งข้อมูลจากฟอร์มหรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // รับค่าจากฟอร์ม
    $user_id = intval($_POST['user_id']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $birthdate = $_POST['birthdate'];
    $role = trim($_POST['role']);
    $status = trim($_POST['status']);

    try {
        // สร้างอินสแตนซ์ของ DBController
        $dbController = new DBController();
        $conn = $dbController->getConn();

        // ตรวจสอบความถูกต้องของข้อมูล
        if (empty($username) || empty($email) || empty($first_name) || empty($last_name) || empty($birthdate) || empty($role) || empty($status)) {
            throw new Exception("All fields are required.");
        }

        // ตรวจสอบรูปแบบอีเมล
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format.");
        }

        // เตรียมคำสั่ง SQL สำหรับอัปเดตข้อมูล
        $query = "UPDATE Users SET 
                    username = ?, 
                    email = ?, 
                    first_name = ?, 
                    last_name = ?, 
                    birthdate = ?, 
                    role = ?, 
                    status = ? 
                  WHERE user_id = ?";

        $stmt = $conn->prepare($query);
        if ($stmt === false) {
            throw new Exception("Error preparing the statement: " . $conn->error);
        }

        // ผูกค่ากับคำสั่ง SQL
        $stmt->bind_param(
            "sssssssi",
            $username,
            $email,
            $first_name,
            $last_name,
            $birthdate,
            $role,
            $status,
            $user_id
        );

        // ดำเนินการอัปเดตข้อมูล
        if ($stmt->execute() === false) {
            throw new Exception("Error executing the statement: " . $stmt->error);
        }

        // ตรวจสอบว่ามีการอัปเดตข้อมูลสำเร็จหรือไม่
        if ($stmt->affected_rows > 0) {
            $_SESSION['success'] = "User with ID $user_id has been updated successfully!";
        } else {
            $_SESSION['info'] = "No changes were made to the user.";
        }

        // ปิด statement
        $stmt->close();

        // เปลี่ยนเส้นทางกลับไปยังหน้ารายชื่อผู้ใช้
        header("Location: ../../pages/tables/users.php");
        exit;
    } catch (Exception $e) {
        // กรณีเกิดข้อผิดพลาด
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: ../../pages/tables/users.php");
        exit;
    }
} else {
    // กรณีไม่มีการส่งข้อมูลจากฟอร์ม
    $_SESSION['error'] = "Invalid request method.";
    header("Location: ../../pages/tables/users.php");
    exit;
}
?>
