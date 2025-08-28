<?php
session_start();
// require 'config/app.php';
require_once('../../config/app.php');
if (!isset($_SESSION['login']) || $_SESSION['login']['role'] !== 'Instructor') {
    $_SESSION['error'] = "You must be an Instructor to view course details.";
    header("Location: login.php");
    exit;
}
// require 'controllers/ExampleController.php';

// $controller = new ExampleController();
// $controller->index();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Course</title>
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
                <h6 class="section-title bg-white text-center text-primary px-3">Add Course</h6>
                <h1 class="mb-5">Add New Course</h1>
                <div class="container mt-5">
                <form action="../../system/add_course_sy.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="course_code" class="form-label">Course Code</label>
                        <input type="text" name="course_code" id="course_code" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="course_name" class="form-label">Course Name</label>
                        <input type="text" name="course_name" id="course_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="course_description" class="form-label">Course Description</label>
                        <textarea name="course_description" id="course_description" class="form-control"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="course_image" class="form-label">Course Image</label>
                        <input type="file" name="course_image" id="course_image" class="form-control" accept="image/*" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Course</button>
                </form>

                </div>
            </div>
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
    <script src="../../assets/lib/wow/wow.min.js"></script>
    <script src="../../assets/ib/easing/easing.min.js"></script>
    <script src="../../assets/lib/waypoints/waypoints.min.js"></script>
    <script src="../../assets/lib/owlcarousel/owl.carousel.min.js"></script>
    <!-- Template Javascript -->
    <script src="assets/js/main.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
</body>
</html>
