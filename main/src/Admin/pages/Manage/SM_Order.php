<?php
session_start();
require_once('../../../dbcontroller.php');
$db_handle = new DBController();

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // ตรวจสอบว่ามีค่า status1 และ order_id ที่รับเข้ามาหรือไม่
    if (isset($_GET['status1']) && isset($_GET['order_id'])) {
        // รับค่า status1 และ order_id จาก URL
        $Parcel_number = isset($_GET['Parcel_number']) ? $_GET['Parcel_number'] : '';
        $status1 = $_GET['status1'];
        $order_id = $_GET['order_id'];

        // Update user information in the database
        $query = "UPDATE Orders SET status = '$status1', Parcel_number = '$Parcel_number'  WHERE order_id = '$order_id'";

        // รันคำสั่ง SQL ด้วย mysqli_query() โดยใช้ getConn() จาก DBController
        $conn = $db_handle->getConn();
        $result = mysqli_query($conn, $query);

        // ตรวจสอบว่าอัปเดตข้อมูลสำเร็จหรือไม่
        if ($result) {
            // หากสำเร็จให้ redirect ไปยังหน้า Manage_Order.php
            header('Location: Manage_Order.php');
            exit;
        } else {
            // หากไม่สำเร็จให้แสดงข้อความผิดพลาด
            echo "Error updating user information: " . mysqli_error($conn);
        }
    } else {
        echo "Missing status1 or order_id parameter.";
    }
} else {
    echo "Invalid request method.";
}
?>
