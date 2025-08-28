<?php
// เริ่มเซสชัน
session_start();

// นำเข้าไฟล์เชื่อมต่อฐานข้อมูล
require_once('../../../config/database.php');

// ตรวจสอบว่ามีการส่งข้อมูลจากฟอร์มหรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // รับค่าจากฟอร์ม
    $permission_id = intval($_POST['permission_id']);
    $description = trim($_POST['description']);
    $updated_at = date('Y-m-d H:i:s'); // ใช้วันที่ปัจจุบัน

    try {
        // สร้างอินสแตนซ์ของ DBController
        $dbController = new DBController();
        $conn = $dbController->getConn();

        // เตรียมคำสั่ง SQL สำหรับอัปเดตข้อมูล
        $query = "UPDATE Role SET 
                    description = ?, 
                    updated_at = ? 
                  WHERE permission_id = ?";

        $stmt = $conn->prepare($query);
        if ($stmt === false) {
            throw new Exception("Error preparing the statement: " . $conn->error);
        }

        // ผูกค่ากับคำสั่ง SQL
        $stmt->bind_param(
            "ssi", // ระบุประเภทข้อมูล: s = string, i = integer
            $description,
            $updated_at,
            $permission_id
        );

        // ดำเนินการอัปเดตข้อมูล
        if ($stmt->execute() === false) {
            throw new Exception("Error executing the statement: " . $stmt->error);
        }

        // ตรวจสอบว่ามีการอัปเดตข้อมูลสำเร็จหรือไม่
        if ($stmt->affected_rows > 0) {
            $_SESSION['success'] = "Permission ID $permission_id updated successfully!";
        } else {
            $_SESSION['info'] = "No changes made to Permission ID $permission_id.";
        }

        // ปิด statement
        $stmt->close();

        // เปลี่ยนเส้นทางกลับไปยังหน้า Role
        header("Location: ../../pages/tables/Role.php");
        exit;
    } catch (Exception $e) {
        // กรณีเกิดข้อผิดพลาด
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: ../../pages/tables/Role.php");
        exit;
    }
} else {
    // กรณีไม่มีการส่งข้อมูลจากฟอร์ม
    $_SESSION['error'] = "Invalid request method.";
    header("Location: ../../pages/tables/Role.php");
    exit;
}
?>
