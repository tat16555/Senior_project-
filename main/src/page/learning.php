<?php
session_start();

require '../config/database.php';
$db = new DBController();
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
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- Spinner End -->

    <?php
    if (isset($_GET['course_code'])) {
        $course_code = $_GET['course_code'];
        // เชื่อมต่อกับฐานข้อมูล
        $db = new DBController();
        $conn = $db->getConn();

        // ค้นหาหลักสูตรในฐานข้อมูล
        $stmt_course = $conn->prepare("SELECT * FROM courses WHERE course_code = ?");
        $stmt_course->bind_param("i", $course_code);
        $stmt_course->execute();
        $course_result = $stmt_course->get_result();
        
        if ($course_result->num_rows > 0) {
            $course_d = $course_result->fetch_assoc();
            $course_description = $course_d['course_description'];
            $course_name = $course_d['course_name'];

            // เพิ่มจำนวน view
            $new_views = $course_d['view'] + 1;  // เพิ่ม view ไปอีก 1
            $stmt_update = $conn->prepare("UPDATE courses SET view = ? WHERE course_code = ?");
            $stmt_update->bind_param("ii", $new_views, $course_code);
            $stmt_update->execute();
        } else {
            $course_description = "Instructor not found";
        }
    ?>
    <!-- Navbar Start -->
    <?php require_once("views/Navbar.php"); ?>
    <!-- Navbar End -->
    <!-- Header Start -->
    <div class="container-fluid bg-primary py-1 mb-5 page-header">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="display-3 text-white animated slideInDown"><?php echo $course_name; ?></h1>
                    <nav aria-label="breadcrumb">
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->
     
    <div class="sidebar">
    <div class="container py-5">
        <h1 class="mb-4">สารบัญ</h1>
        <p><?php echo $course_description ; ?></p>
        <?php
            // เตรียมคำสั่ง SQL เพื่อดึงข้อมูลตามรหัสที่ส่งมา
            $stmt = $conn->prepare("SELECT * FROM lessons WHERE course_code = ?");
            $stmt->bind_param("s", $course_code);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $hasChapters = false;  // Flag to check if any chapters are found
                while ($row = $result->fetch_assoc()) {
                    $hasChapters = true;
        ?>
        <div class="row">
            <div class="col-md">
                <div class="list-group" id="chapter-list" role="tablist">
                    <a class="list-group-item list-group-item-action" href="lesson.php?course_code=<?=$course_code?>&lesson_id=<?=$row['lesson_id']?>" role="tab">บทที่ <?php echo $row['lesson_number']; ?> <?php echo $row['lesson_title']; ?></a>
                </div>
            </div>
        </div>
        <?php
                }
                if (!$hasChapters) {
                    echo "<p>ตอนนี้ยังไม่มีบทเรียน</p>";
                }
            } else {
                echo "<p>No course found with code $code.</p>";
            }
            $stmt->close();
            $conn->close();
        } else {
            echo "<p>No course code specified.</p>";
        }
        ?>
    </div>

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
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <!-- Template Javascript -->
    <script src="../assets/js/main.js"></script>
    
</body>

</html>