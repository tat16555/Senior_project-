<?php
session_start();
if (!isset($_SESSION['login'])) {
    $_SESSION['error'] = "Please Login first.";
    header("Location: sign_in.php");
    exit;
}
require '../config/database.php';
$db = new DBController();
?>
<!DOCTYPE html>
<html lang="en">
<head> 
    <meta charset="utf-8">
    <title>eLEARNING Profile</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="../assets/lib/animate/animate.min.css" rel="stylesheet">
    <link href="../assets/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- Spinner End -->
    <!-- Navbar Start -->
    <?php require_once("views/Navbar.php"); ?>
    <!-- Navbar End -->
    <!-- About Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s" style="min-height: 400px;">
                <div class="position-relative h-100">                   
                    <!-- รูปโปรไฟล์ -->
                    <div class="profile-img-wrapper position-absolute" style="top: 50%; left: 50%; transform: translate(-50%, -50%);">
                        <img class="profile-img img-fluid rounded-circle" src="../img/about2.avif" alt="Profile Picture" style="width: 250px; height: 250px; object-fit: cover;">
                    </div>
                </div>

                </div>
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.3s">
                    <h6 class="section-title bg-white text-center text-primary">personal information</h6>
                    <h1 class="mb-4"><?php echo htmlspecialchars($_SESSION['login']['username']); ?></h1>
                    <p class="mb-4">First Name : <?php echo htmlspecialchars($_SESSION['login']['first_name']); ?> | Last Name : <?php echo htmlspecialchars($_SESSION['login']['last_name']); ?></p>
                    <p class="mb-4">E-mail : <?php echo htmlspecialchars($_SESSION['login']['email']); ?></p>
                </div>
                <?php
                    $user_id = $_SESSION['login']['user_id']; // user_id จาก session

                    // เชื่อมต่อกับฐานข้อมูล
                    $conn = $db->getConn();

                    // ขั้นตอน 1: หาคอร์สที่ผู้ใช้เรียนแล้ว
                    $sql = "SELECT DISTINCT course_code FROM learning_records WHERE user_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    $courses_learned = [];
                    while ($row = $result->fetch_assoc()) {
                        $courses_learned[] = $row['course_code'];
                    }

                    // ขั้นตอน 2: ดึงข้อมูลคอร์สที่เรียนจากตาราง courses
                    $courses_data = [];
                    foreach ($courses_learned as $course_code) {
                        $sql_course = "SELECT course_name, course_code, img FROM courses WHERE course_code = ?";
                        $stmt_course = $conn->prepare($sql_course);
                        $stmt_course->bind_param("s", $course_code);
                        $stmt_course->execute();
                        $result_course = $stmt_course->get_result();

                        if ($row_course = $result_course->fetch_assoc()) {
                            // ขั้นตอน 3: คำนวณจำนวนบทเรียนที่เรียนแล้วในแต่ละคอร์ส
                            $sql_lessons = "SELECT COUNT(lesson_id) AS lessons_completed FROM learning_records WHERE user_id = ? AND course_code = ?";
                            $stmt_lessons = $conn->prepare($sql_lessons);
                            $stmt_lessons->bind_param("is", $user_id, $course_code);
                            $stmt_lessons->execute();
                            $result_lessons = $stmt_lessons->get_result();
                            $lessons_row = $result_lessons->fetch_assoc();
                            $lessons_completed = $lessons_row['lessons_completed'];

                            // คำนวณ % ความสำเร็จ
                            $total_lessons_api_url = "https://true-sadly-bass.ngrok-free.app//api/Calculate.php";
                            $total_lessons_data = json_decode(file_get_contents($total_lessons_api_url), true);
                            $total_lessons = 0;

                            foreach ($total_lessons_data['courses'] as $course) {
                                if ($course['course_code'] === $course_code) {
                                    $total_lessons = $course['total_lessons'];
                                    break;
                                }
                            }

                            $progress_percentage = ($total_lessons > 0) ? ($lessons_completed / $total_lessons) * 100 : 0;

                            // ดึงข้อมูลผลการทำแบบทดสอบที่ดีที่สุด
                            $sql_best_score = "SELECT MAX(score) AS best_score, status FROM quiz_attempts WHERE user_id = ? AND quiz_id IN (SELECT quiz_id FROM quizzes WHERE course_code = ?) GROUP BY status ORDER BY best_score DESC LIMIT 1";
                            $stmt_best_score = $conn->prepare($sql_best_score);
                            $stmt_best_score->bind_param("is", $user_id, $course_code);
                            $stmt_best_score->execute();
                            $result_best_score = $stmt_best_score->get_result();
                            $best_score = $result_best_score->fetch_assoc();

                            // เก็บข้อมูลคอร์สที่เรียน พร้อมกับคะแนนที่ดีที่สุด
                            $courses_data[] = [
                                'course_code' => $course_code,
                                'course_name' => $row_course['course_name'],
                                'img' => $row_course['img'],
                                'lessons_completed' => $lessons_completed,
                                'total_lessons' => $total_lessons,
                                'progress_percentage' => round($progress_percentage, 2),
                                'best_score' => isset($best_score['best_score']) ? $best_score['best_score'] : 'ไม่มีข้อมูล',
                                'status' => isset($best_score['status']) ? $best_score['status'] : 'ไม่มีข้อมูล'
                            ];
                        }
                    }

                    // ขั้นตอน 4: ดึงข้อมูลใบ Certificate ของผู้ใช้
                    $certificates = [];
                    foreach ($courses_learned as $course_code) {
                        $sql_cert = "SELECT img FROM certificates WHERE user_id = ? AND course_code = ?";
                        $stmt_cert = $conn->prepare($sql_cert);
                        $stmt_cert->bind_param("is", $user_id, $course_code);
                        $stmt_cert->execute();
                        $result_cert = $stmt_cert->get_result();
                        
                        while ($row_cert = $result_cert->fetch_assoc()) {
                            $certificates[] = [
                                'course_code' => $course_code,
                                'certificate_img' => $row_cert['img']
                            ];
                        }
                    }

                    // ปิดการเชื่อมต่อ
                    $conn->close();
                ?>
            <!-- HTML แสดงข้อมูล -->
            <div>
                <h1>บันทึกการเรียน</h1>
                <div class="row">
                    <?php foreach ($courses_data as $course): ?>
                        <div class="col-sm-6">
                            <div class="card">
                                <img src="../<?php echo $course['img']; ?>" class="card-img-top" alt="..." style="object-fit: cover; width: 100%; height: 300px;">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $course['course_name']; ?> (<?php echo $course['course_code']; ?>)</h5>
                                    <p class="card-text">เรียนไปแล้ว: <?php echo $course['progress_percentage']; ?>%</p>
                                    <h5>ผลการทำแบบทดสอบ</h5>
                                    <p class="card-text">
                                        <strong>คะแนนดีที่สุด: </strong> <?php echo ($course['best_score'] != 'ไม่มีข้อมูล') ? $course['best_score'] : 'ไม่มีข้อมูล'; ?><br>
                                        <strong>สถานะ: </strong> <?php echo ($course['status'] != 'ไม่มีข้อมูล') ? $course['status'] : 'ไม่มีข้อมูล'; ?>
                                    </p>
                                    <!-- เพิ่มปุ่มดูใบ Certificate -->
                                    <?php
                                    foreach ($certificates as $certificate) {
                                        if ($certificate['course_code'] === $course['course_code']) {
                                            echo '<a href="../' . $certificate['certificate_img'] . '" target="_blank" class="btn btn-primary">ดูใบ Certificate</a>';
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            </div>
        </div>
    </div>
    <!-- About End -->
    <!-- Footer Start -->
    <?php require_once("views/footer.php"); ?>
    <!-- Footer End -->
    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/lib/wow/wow.min.js"></script>
    <script src="../assets/lib/easing/easing.min.js"></script>
    <script src="../assets/lib/waypoints/waypoints.min.js"></script>
    <script src="../assets/lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="../assets/js/main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>