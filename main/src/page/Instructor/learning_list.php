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

    // เชื่อมต่อฐานข้อมูล
    $dbController = new DBController();
    $conn = $dbController->getConn();

    // ดึงข้อมูล Course
    $stmtCourse = $conn->prepare("SELECT course_name, course_description FROM courses WHERE course_code = ?");
    $stmtCourse->bind_param("i", $course_code);
    $stmtCourse->execute();
    $resultCourse = $stmtCourse->get_result();
    $course = $resultCourse->fetch_assoc();

    // ตรวจสอบว่าพบ Course หรือไม่
    if (!$course) {
        $_SESSION['error'] = "Course not found.";
        header("Location: courses_list.php");
        exit;
    }

    // ดึงข้อมูล Lessons ของ Course นี้
    $stmtLessons = $conn->prepare("SELECT lesson_id, lesson_number, lesson_title, lesson_description, vdo_url, created_at FROM lessons WHERE course_code = ?");
    $stmtLessons->bind_param("i", $course_code);
    $stmtLessons->execute();
    $resultLessons = $stmtLessons->get_result();
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
    <!-- Categories Start -->
    <div class="container-xxl py-5 category">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title bg-white text-center text-primary px-3">Learning List</h6>
                <div class="container mt-5">
                    <h2>Learning List</h2>
                    <h4>Course Name : <?= htmlspecialchars($course['course_name']); ?></h4>
                    <p><?= htmlspecialchars($course['course_description']); ?></p>
                    <a href="quizzes.php?course_code=<?= $course_code; ?>"class="btn btn-primary">view quizzes</a> 
                    <a href="Add_Lesson.php?course_code=<?= $course_code; ?>"class="btn btn-info btn-3">Add Lessons</a>
                    <table class="table table-bordered table-hover mt-3">
                        <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Lesson Title</th>
                            <th>Description</th>
                            <th>Video URL</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if ($resultLessons->num_rows > 0) {
                            $index = 1;
                            while ($lesson = $resultLessons->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $index++; ?></td>
                                    <td><?= htmlspecialchars($lesson['lesson_title']); ?></td>
                                    <td><?= htmlspecialchars($lesson['lesson_description']); ?></td>
                                    <td>
                                        <a href="<?= htmlspecialchars($lesson['vdo_url']); ?>" target="_blank">
                                            Watch Video
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars($lesson['created_at']); ?></td>
                                    <td>
                                        <a href="view_lesson.php?lesson_id=<?= $lesson['lesson_id']; ?>" class="btn btn-info btn-sm">View</a>
                                        <a href="edit_lesson.php?lesson_id=<?= $lesson['lesson_id']; ?>&course_code=<?= $course_code; ?>" class="btn btn-warning btn-sm">Edit</a>
                                        <a href="delete_lesson.php?lesson_id=<?= $lesson['lesson_id']; ?>" 
                                        class="btn btn-danger btn-sm" 
                                        onclick="return confirm('Are you sure you want to delete this lesson?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile;
                        } else { ?>
                            <tr>
                                <td colspan="6" class="text-center">No lessons found for this course.</td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    <a href="course_list.php" class="btn btn-secondary mt-3">Back to Courses</a>
                </div>
                <?php
                $stmtCourse->close();
                $stmtLessons->close();
                $conn->close();
                ?>
            </div>
        </div>
    </div>
    <!-- Categories Start -->

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

