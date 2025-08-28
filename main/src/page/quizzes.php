<?php
session_start();
if (!isset($_SESSION['login'])) {
    $_SESSION['error'] = "You must Login to take the test.";
    header("Location: sign_in.php");
    exit;
}
require '../config/database.php';

$user_id = $_SESSION['login']['user_id']; // user_id จาก session
$course_code = $_GET['course_code']; // รับค่า course_code

// สร้าง DBController
$db = new DBController();
$conn = $db->getConn(); // ใช้ฟังก์ชัน getConn() เพื่อดึงการเชื่อมต่อฐานข้อมูล
// ขั้นตอนที่ 1: ดึงข้อมูล quizzes จาก course_code
$query_quiz = "SELECT * FROM quizzes WHERE course_code = ?";
$stmt = $conn->prepare($query_quiz);
$stmt->bind_param("s", $course_code);
$stmt->execute();
$quiz_result = $stmt->get_result();
$quiz = $quiz_result->fetch_assoc();

if (!$quiz) {
    echo "ไม่พบแบบทดสอบสำหรับคอร์สนี้";
    exit;
}

// แสดงข้อมูล quiz_title และคะแนนเต็ม
$quiz_title = $quiz['quiz_title'];
$score_full = $quiz['score_full'];
$pass_score = $quiz['pass_score'];
$quiz_id = $quiz['quiz_id'];

// ขั้นตอนที่ 2: ดึงข้อมูลคำถามจาก quiz_questions แบบสุ่มสูงสุด 20 ข้อ
$query_questions = "SELECT * FROM quiz_questions WHERE quiz_id = ? ORDER BY RAND() LIMIT 20";
$stmt = $conn->prepare($query_questions);
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$questions_result = $stmt->get_result();
$questions = $questions_result->fetch_all(MYSQLI_ASSOC);

// ขั้นตอนที่ 3: แสดงแบบทดสอบและตรวจสอบคำตอบหลังส่ง
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correct_answers = 0;
    $total_questions = count($questions);

    // ตรวจสอบคำตอบที่ผู้ใช้เลือก
    foreach ($questions as $question) {
        $user_answer = $_POST['answer_' . $question['question_id']] ?? ''; // ตรวจสอบคำตอบที่เลือก
        if ($user_answer == $question['correct_answer']) {
            $correct_answers++;
        }
    }

    // คำนวณคะแนน
    $score = ($correct_answers / $total_questions) * $score_full;

    // บันทึกผลการทำแบบทดสอบใน quiz_attempts
    $query_insert_attempt = "INSERT INTO quiz_attempts (quiz_id, user_id, score, total_questions, correct_answers, status) VALUES (?, ?, ?, ?, ?, ?)";
    $status = ($score >= $pass_score) ? 'passed' : 'failed';
    $stmt = $conn->prepare($query_insert_attempt);
    $stmt->bind_param("iiiiis", $quiz_id, $user_id, $score, $total_questions, $correct_answers, $status);

    $stmt->execute();

    // เก็บข้อมูลคะแนนใน session เพื่อแสดงในหน้า profile.php
    $_SESSION['quiz_result'] = [
        'quiz_id' => $quiz_id,
        'quiz_title' => $quiz_title,
        'course_code' => $course_code,
        'score' => $score,
        'score_full' => $score_full,
        'status' => $status
    ];
    header('Location: Score_results.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head> 
    <meta charset="utf-8">
    <title>EduInsightHub</title>
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

    <div class="container">
        <h1 class="text-center"><?php echo $quiz_title; ?></h1>
        <p>คะแนนเต็ม: <?php echo $score_full; ?> คะแนน | คะแนนที่ต้องการ: <?php echo $pass_score; ?> คะแนน</p>
        <form method="POST">
            <?php foreach ($questions as $question): ?>
                <div class="mb-3">
                    <h5><?php echo $question['question_text']; ?></h5>
                    <div>
                        <input type="radio" name="answer_<?php echo $question['question_id']; ?>" value="a"> <?php echo $question['option_a']; ?>
                    </div>
                    <div>
                        <input type="radio" name="answer_<?php echo $question['question_id']; ?>" value="b"> <?php echo $question['option_b']; ?>
                    </div>
                    <div>
                        <input type="radio" name="answer_<?php echo $question['question_id']; ?>" value="c"> <?php echo $question['option_c']; ?>
                    </div>
                    <?php if ($question['option_d']): ?>
                        <div>
                            <input type="radio" name="answer_<?php echo $question['question_id']; ?>" value="d"> <?php echo $question['option_d']; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            <button type="submit" class="btn btn-primary">ส่งคำตอบ</button>
        </form>
    </div>



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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Template Javascript -->
    <script src="../assets/js/main.js"></script>
</body>

</html>