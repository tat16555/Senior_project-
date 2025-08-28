<?php
session_start();
require_once('../../../config/database.php');

// ตรวจสอบว่ามีการส่ง user_id มาหรือไม่
if (isset($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']); // แปลงค่าเป็นตัวเลขเพื่อความปลอดภัย

    // สร้างอินสแตนซ์ของ DBController
    $dbController = new DBController();
    $conn = $dbController->getConn();

    // เตรียมคำสั่ง SQL เพื่อลบผู้ใช้
    $stmt = $conn->prepare("DELETE FROM Users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        // ลบสำเร็จ
        $_SESSION['success'] = "user id : $user_id ถูกลบแล้ว!";
        header("Location: ../../pages/tables/users.php");
    } else {
        $_SESSION['error'] = "ลบไม่สำเร็จ";
        // เกิดข้อผิดพลาด
        header("Location: ../../pages/tables/users.php");
    }

    $stmt->close();
    $conn->close();
} else {
    // หากไม่มี user_id ให้กลับไปหน้าจัดการผู้ใช้
    header("Location: ../../pages/tables/users.php");
    exit;
}
?>