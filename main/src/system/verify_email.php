<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ยืนยันอีเมลสำเร็จ</title>
    <!-- เพิ่มการเชื่อมต่อ Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <?php
    require '../config/database.php';  // ไฟล์เชื่อมต่อฐานข้อมูล
    $db = new DBController();
    $conn = $db->getConn();

    // ตรวจสอบการเชื่อมต่อฐานข้อมูล
    if ($conn->connect_error) {
        echo "<div class='alert alert-danger'>❌ การเชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error . "</div>";
        exit;
    }

    if (isset($_GET['code']) && isset($_GET['user_id'])) {
        $verification_code = $_GET['code'];
        $user_id = $_GET['user_id'];

        // ดึงข้อมูลผู้ใช้จากฐานข้อมูล
        $sql = "SELECT * FROM Users WHERE user_id = ? AND verification_code = ? ";
        
        // เตรียมคำสั่ง SQL และตรวจสอบว่าเตรียมได้สำเร็จหรือไม่
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("is", $user_id, $verification_code);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // อัปเดตให้บัญชีเป็น "ยืนยันแล้ว"
                $update_sql = "UPDATE Users SET email_verified = 1 WHERE user_id = ?";
                if ($update_stmt = $conn->prepare($update_sql)) {
                    $update_stmt->bind_param("i", $user_id);
                    $update_stmt->execute();

                    // ตรวจสอบผลการอัปเดต
                    if ($update_stmt->affected_rows > 0) {
                        echo "<div class='alert alert-success'>✅ ยืนยันอีเมลสำเร็จ! คุณสามารถเข้าสู่ระบบได้แล้ว <a href='../page/sign_in.php'>sign in<a></div>";
                    } else {
                        echo "<div class='alert alert-warning'>❌ การอัปเดตข้อมูลผู้ใช้ไม่สำเร็จ หรือไม่มีการเปลี่ยนแปลงใดๆ</div>";
                    }
                } else {
                    echo "<div class='alert alert-danger'>❌ เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL สำหรับการอัปเดตข้อมูล: " . $conn->error . "</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>❌ ลิงก์นี้ไม่ถูกต้อง หรืออีเมลได้รับการยืนยันไปแล้ว</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>❌ เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL: " . $conn->error . "</div>";
        }
    } else {
        echo "<div class='alert alert-warning'>❌ ข้อมูลไม่ครบถ้วน!</div>";
    }
    ?>
</div>

<!-- เพิ่มการเชื่อมต่อ Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
