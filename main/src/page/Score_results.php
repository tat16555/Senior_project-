<?php
session_start();

// ตรวจสอบว่ามีข้อมูลคะแนนจาก session หรือไม่
if (!isset($_SESSION['quiz_result'])) {
    echo "ข้อมูลการทำแบบทดสอบไม่ถูกต้อง.";
    exit;
}

$quiz_result = $_SESSION['quiz_result'];

// ดึงข้อมูลจาก session
$quiz_title = $quiz_result['quiz_title'];
$course_code = $quiz_result['course_code'];
$score = $quiz_result['score'];
$score_full = $quiz_result['score_full'];
$status = $quiz_result['status'];


// เชื่อมต่อกับฐานข้อมูล
require '../config/database.php';
$db = new DBController();
$conn = $db->getConn();

// ถ้าผ่านให้แสดงปุ่มขอใบ Certificate
if ($status == 'passed') {
    $show_certificate_button = true;
} else {
    $show_certificate_button = false;
}

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduInsightHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <div class="card">
        <div class="card-header">
            <h3>ผลการทำแบบทดสอบ</h3>
        </div>
        <div class="card-body">
            <h5 class="card-title"><?php echo htmlspecialchars($quiz_title); ?></h5>
            <p class="card-text">
                <strong>คอร์ส: </strong> <?php echo htmlspecialchars($course_code); ?><br>
                <strong>คะแนนเต็ม: </strong> <?php echo $score_full; ?><br>
                <strong>คะแนนที่ได้: </strong> <?php echo number_format($score, 2); ?><br>
                <strong>สถานะ: </strong>
                <?php if ($status == 'passed'): ?>
                    <span class="badge bg-success">ผ่าน</span>
                <?php else: ?>
                    <span class="badge bg-danger">ไม่ผ่าน</span>
                <?php endif; ?>
            </p>
        </div>
        <div class="card-footer">
            <?php if ($show_certificate_button): ?>
                <a href="../system/request_certificate.php" class="btn btn-success">ขอใบ Certificate</a>
            <?php else: ?>
                <a href="courses.php" class="btn btn-primary">กลับ</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
