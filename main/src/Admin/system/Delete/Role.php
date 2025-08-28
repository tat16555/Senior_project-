<?php
session_start();
require_once('../../../config/database.php');

// ตรวจสอบว่ามีการส่ง permission_id มาหรือไม่
if (isset($_GET['permission_id'])) {
    $permission_id = intval($_GET['permission_id']); // แปลงค่าเป็นตัวเลขเพื่อความปลอดภัย

    // สร้างอินสแตนซ์ของ DBController
    $dbController = new DBController();
    $conn = $dbController->getConn();

    // เตรียมคำสั่ง SQL เพื่อลบผู้ใช้
    $stmt = $conn->prepare("DELETE FROM Role WHERE permission_id = ?");
    $stmt->bind_param("i", $permission_id);

    if ($stmt->execute()) {
        // ลบสำเร็จ
        $_SESSION['success'] = "Role id : $permission_id ได้ถูกลบแล้ว!";
        header("Location: ../../pages/tables/Role.php");
    } else {
        $_SESSION['error'] = "ลบไม่สำเร็จ";
        // เกิดข้อผิดพลาด
        header("Location: ../../pages/tables/Role.php");
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: ../../pages/tables/Role.php");
    exit;
}
?>