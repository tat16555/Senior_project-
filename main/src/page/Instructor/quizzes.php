<?php
session_start();
require_once('../../config/database.php'); // เชื่อมต่อกับฐานข้อมูล
if (!isset($_SESSION['login']) || $_SESSION['login']['role'] !== 'Instructor') {
    $_SESSION['error'] = "You must be an Instructor to view course details.";
    header("Location: login.php");
    exit;
}

// ตรวจสอบว่ามีการส่ง course_code มาหรือไม่
if (!isset($_GET['course_code']) || empty($_GET['course_code'])) {
    $_SESSION['error'] = "Invalid course ID.";
    header("Location: courses_list.php");
    exit;
}
// รับ course_code จาก URL
$course_code = intval($_GET['course_code']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learning List</title>
    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">
    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Libraries Stylesheet -->
    <link href="../../assets/lib/animate/animate.min.css" rel="stylesheet">
    <link href="../../assets/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <!-- Customized Bootstrap Stylesheet -->
    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- Template Stylesheet -->
    <link href="../../assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .btn {
            width: 100%;
        }

        .table-bordered {
            border: 1px solid #ddd;
        }

    </style>
</head>
<body>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger" role="alert">
        <?php echo $_SESSION['error']; ?>
        <?php unset($_SESSION['error']); // ลบค่าจาก session หลังจากแสดงผลแล้ว ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success" role="alert">
        <?php echo $_SESSION['success']; ?>
        <?php unset($_SESSION['success']); // ลบค่าจาก session หลังจากแสดงผลแล้ว ?>
    </div>
<?php endif; ?>

<!-- Navbar End -->
<?php require_once("views/Navbar.php"); ?>
<!-- Navbar End -->
<?php
    // เชื่อมต่อฐานข้อมูล
    $dbController = new DBController();
    $conn = $dbController->getConn();

    // ดึงข้อมูล quizzes ที่เกี่ยวข้องกับ course_code
    $stmt_quiz = $conn->prepare("SELECT * FROM quizzes WHERE course_code = ?");
    $stmt_quiz->bind_param("i", $course_code);
    $stmt_quiz->execute();
    $result_quiz = $stmt_quiz->get_result();
?>
<!-- Categories Start -->
<div class="container-xxl py-5 category">
    <div class="container">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h6 class="section-title bg-white text-center text-primary px-3">Learning List</h6>
            <div class="container mt-5">
                <h4>Quizzes for Course Code: <?php echo htmlspecialchars($course_code); ?></h4>

                <!-- แสดงรายการ quizzes -->
                <h5>Quizzes:</h5>
                <ul>
                    <?php while ($quiz = $result_quiz->fetch_assoc()): ?>
                        <li>
                            <strong><?php echo htmlspecialchars($quiz['quiz_title']); ?></strong><br>
                            Score: <?php echo htmlspecialchars($quiz['score_full']); ?> | Passing Score: <?php echo htmlspecialchars($quiz['pass_score']); ?>
                        </li>
                        <a href="add_question_form.php?quiz_id=<?= $quiz['quiz_id']; ?>" class="btn btn-primary">Add quiz</a>
                        <?php $quiz_id = $quiz['quiz_id']?>
                    <?php endwhile; ?>
                </ul>
                
                <?php 
                    // ดึงข้อมูล quiz_questions ที่เกี่ยวข้องกับ quiz_id
                    $stmt_questions = $conn->prepare("SELECT * FROM quiz_questions WHERE quiz_id = ?");
                    $stmt_questions->bind_param("i", $quiz_id);
                    $stmt_questions->execute();
                    $result_questions = $stmt_questions->get_result();
                ?>

                    <!-- แสดงรายการ quiz_questions -->
                    <h5>Quiz Questions:</h5>
                    <?php while ($question = $result_questions->fetch_assoc()): ?>
                        <table class="table table-bordered">
                            <thead>
                                <tr>    
                                    <th colspan="4" class="text-center">แบบทดสอบ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="width: 20%; text-align: left;">question</td>
                                    <td style="width: 80%;" colspan="3"><?php echo htmlspecialchars($question['question_text']); ?></td>
                                </tr>
                                <tr>
                                    <td style="width: 20%; text-align: left;">A</td>
                                    <td style="width: 80%;" colspan="3"><?php echo htmlspecialchars($question['option_a']); ?></td>
                                </tr>
                                <tr>
                                    <td style="width: 20%; text-align: left;">B</td>
                                    <td style="width: 80%;" colspan="3"><?php echo htmlspecialchars($question['option_b']); ?></td>
                                </tr>
                                <tr>
                                    <td style="width: 20%; text-align: left;">C</td>
                                    <td style="width: 80%;" colspan="3"><?php echo htmlspecialchars($question['option_c']); ?></td>
                                </tr>
                                <tr>
                                    <td style="width: 20%; text-align: left;">D</td>
                                    <td style="width: 80%;" colspan="3"><?php echo htmlspecialchars($question['option_d']); ?></td>
                                </tr>
                                <tr>
                                    <td style="width: 20%; text-align: left;">คำตอบที่ถูกต้อง คือ </td>
                                    <td style="width: 80%;" colspan="3"><?php echo htmlspecialchars($question['correct_answer']); ?></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="text-center">
                                        <a href="edit_question.php?question_id=<?php echo htmlspecialchars($question['question_id']); ?>" class="btn btn-warning">Edit</a>
                                    </td>
                                    <td colspan="2" class="text-center">
                                        <a href="../../system/Delete/delete_question.php?question_id=<?php echo htmlspecialchars($question['question_id']); ?>" class="btn btn-danger">Delete</a> 
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    <?php endwhile; ?>  
                    <?php
                // ปิดการเชื่อมต่อฐานข้อมูล
                $stmt_quiz->close();
                $stmt_questions->close();
                $conn->close();
                ?>
            </div>
        </div>
    </div>
</div>
<!-- Categories End -->


<!-- Footer Start -->
<?php require_once("views/footer.php"); ?>
<!-- Footer End -->

<!-- Back to Top -->
<a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>

<!-- JavaScript Libraries -->
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../../assets/lib/wow/wow.min.js"></script>
<script src="../../assets/ib/easing/easing.min.js"></script>
<script src="../../assets/lib/waypoints/waypoints.min.js"></script>
<script src="../../assets/lib/owlcarousel/owl.carousel.min.js"></script>
<!-- Template Javascript -->
<script src="assets/js/main.js"></script>

<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
</body>
</html>
