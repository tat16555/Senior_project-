<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login']['role'] !== 'Instructor') {
    $_SESSION['error'] = "You must be an Instructor to view course details.";
    header("Location: login.php");
    exit;
}
$course_code = intval($_GET['course_code']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Lesson</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
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
<div class="container mt-5">
    <h2>Add New Lesson</h2>
    <p><?=$course_code?></p>
    <div class="d-flex justify-content-center align-items-center vh-100">
        <form action="../../system/Add_lesson_sy.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="course_code" value="<?=$course_code?>">
            <div class="mb-3">
                <label for="lesson_title" class="form-label">Lesson Title</label>
                <input type="text" name="lesson_title" id="lesson_title" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="lesson_description" class="form-label">Lesson Description</label>
                <textarea name="lesson_description" id="lesson_description" class="form-control"></textarea>
            </div>
            <div class="mb-3">
                <label for="content" class="form-label">Upload Content (PDF, DOCX, etc.)</label>
                <input type="file" name="content" id="content" class="form-control" accept=".pdf,.doc,.docx" required>
            </div>
            <div class="mb-3">
                <label for="vdo_url" class="form-label">Use the video link with embed code.</label>
                <input type="url" name="vdo_url" id="vdo_url" class="form-control">
            </div>
            <h2>How to?</h2>
            <p>1. Upload VDO on youtube.com</p>
            <p>2. Copy embed code in the VDO you want to use</p>
            <p>3. Paste it in the box</p>
            <img class="img-fluid" src="../../img/Screenshot 2025-02-19 221119.png" alt="" style="object-fit: cover; width: 600px; height: 40%;"> <hr>
            <button type="submit" class="btn btn-primary">Save Lesson</button>
        </form>
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
