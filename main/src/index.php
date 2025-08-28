<?php
session_start();
// require 'config/app.php';
require_once('config/app.php');
require_once('config/database.php');
$db = new DBController();
// require 'controllers/ExampleController.php';

// $controller = new ExampleController();
// $controller->index();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduInsightHub</title>
        <!-- Google Web Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="assets/lib/animate/animate.min.css" rel="stylesheet">
    <link href="assets/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="assets/css/style.css" rel="stylesheet">
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
<!-- Carousel Start -->
<div class="container-fluid p-0 mb-5">
        <div class="owl-carousel header-carousel position-relative">
            <div class="owl-carousel-item position-relative">
                <img class="img-fluid" src="img/carousel01.avif" alt="">
                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center" style="background: rgba(24, 29, 56, .7);">
                    <div class="container">
                        <div class="row justify-content-start">
                            <div class="col-sm-10 col-lg-8">
                            <h5 class="text-primary text-uppercase mb-3 animated slideInDown">หลักสูตรออนไลน์ที่ดีที่สุด</h5>
                                <h1 class="display-3 text-white animated slideInDown">ศึกษาทางออนไลน์จากทุกที่ที่คุณต้องการ</h1>
                                <p class="fs-5 text-white mb-4 pb-2">ค้นพบความสะดวกสบายในการเรียนรู้จากที่บ้านหรือทุกที่ที่คุณต้องการ ด้วยหลักสูตรออนไลน์ที่ออกแบบมาเพื่อเพิ่มพูนความรู้และทักษะของคุณอย่างมีประสิทธิภาพ</p>
                                <a href="" class="btn btn-primary py-md-3 px-md-5 me-3 animated slideInLeft">Read More</a>
                                <a href="" class="btn btn-light py-md-3 px-md-5 animated slideInRight">Join Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="owl-carousel-item position-relative">
                <img class="img-fluid" src="img/carousel02.avif" alt="">
                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center" style="background: rgba(24, 29, 56, .7);">
                    <div class="container">
                        <div class="row justify-content-start">
                            <div class="col-sm-10 col-lg-8">
                                <h5 class="text-primary text-uppercase mb-3 animated slideInDown">หลักสูตรออนไลน์ที่ดีที่สุด</h5>
                                <h1 class="display-3 text-white animated slideInDown">ศึกษาทางออนไลน์จากทุกที่ที่คุณต้องการ</h1>
                                <p class="fs-5 text-white mb-4 pb-2">ค้นพบความสะดวกสบายในการเรียนรู้จากที่บ้านหรือทุกที่ที่คุณต้องการ ด้วยหลักสูตรออนไลน์ที่ออกแบบมาเพื่อเพิ่มพูนความรู้และทักษะของคุณอย่างมีประสิทธิภาพ</p>
                                <a href="page/courses.php" class="btn btn-primary py-md-3 px-md-5 me-3 animated slideInLeft">Read More</a>
                                <a href="page/courses.php" class="btn btn-light py-md-3 px-md-5 animated slideInRight">Join Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Carousel End -->


    <!-- Service Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="service-item text-center pt-3">
                        <div class="p-4">
                            <i class="fa fa-3x fa-graduation-cap text-primary mb-4"></i>
                            <h5 class="mb-3">เนื้อหาการเรียน</h5>
                            <p>"เนื้อหาการเรียนของเราถูกออกแบบโดยมืออาชีพเพื่อคุณโดยเฉพาะ!"</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="service-item text-center pt-3">
                        <div class="p-4">
                            <i class="fa fa-3x fa-globe text-primary mb-4"></i>
                            <h5 class="mb-3">เนื้อหามาตรฐานระดับโลก</h5>
                            <p>หลักสูตรได้รับการรับรองและยอมรับจากผู้เชี่ยวชาญทั่วโลกว่าเป็นไปตามมาตรฐานสากล ซึ่งสะท้อนถึงความเชื่อมั่นในความถูกต้องและความน่าเชื่อถือ</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="service-item text-center pt-3">
                        <div class="p-4">
                            <i class="fa fa-3x fa-home text-primary mb-4"></i>
                            <h5 class="mb-3">เรียนรู้ได้ตลอดเวลา</h5>
                            <p>เข้าถึงการเรียนรู้จากทุกที่และทุกเวลาได้อย่างสะดวก</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.7s">
                    <div class="service-item text-center pt-3">
                        <div class="p-4">
                            <i class="fa fa-3x fa-book-open text-primary mb-4"></i>
                            <h5 class="mb-3">ออกใบ certificate</h5>
                            <p>ใบประกาศนียบัตรของเราได้รับการรับรองจากองค์กรชั้นนำ เพื่อการันตีความน่าของคุณ</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Service End -->
    <!-- About Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s" style="min-height: 400px;">
                    <div class="position-relative h-100">
                        <img class="img-fluid position-absolute w-100 h-100" src="img/about2.avif" alt="" style="object-fit: cover;">
                    </div>
                </div>
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.3s">
                    <h6 class="section-title bg-white text-start text-primary pe-3">About Us</h6>
                    <h1 class="mb-4">ยินดีต้อนรับสู่เว็บไซต์การเรียนรู้ของเรา</h1>
                    <p class="mb-4">ยินดีต้อนรับสู่เว็บไซต์ของเรา ที่จะพาคุณไปสู่ประสบการณ์การเรียนรู้ที่ไม่มีที่สิ้นสุด เราให้บริการหลักสูตรที่หลากหลายและมีคุณภาพ เพื่อเสริมสร้างความรู้และทักษะของคุณให้ดียิ่งขึ้น</p>
                    <p class="mb-4">ค้นพบความสะดวกในการเรียนรู้ที่บ้านหรือทุกที่ที่คุณต้องการ ด้วยหลักสูตรที่ออกแบบมาอย่างพิถีพิถันเพื่อตอบโจทย์ความต้องการของคุณ เรามุ่งมั่นที่จะทำให้การเรียนรู้เป็นเรื่องที่ง่ายและมีประสิทธิภาพ เพื่อให้คุณเติบโตอย่างเต็มศักยภาพ</p>
                    <div class="row gy-2 gx-4 mb-4">
                        <div class="col-sm-6">
                            <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>ออกแบบเนื้อหาโดยมืออาชีพ</p>
                        </div>

                        <div class="col-sm-6">
                            <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>ใบ Certificate ใยระดับสากล</p>
                        </div>
                    </div>
                    <a class="btn btn-primary py-3 px-5 mt-2" href="">Read More</a>
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
    <script src="assets/lib/wow/wow.min.js"></script>
    <script src="assets/ib/easing/easing.min.js"></script>
    <script src="assets/lib/waypoints/waypoints.min.js"></script>
    <script src="assets/lib/owlcarousel/owl.carousel.min.js"></script>
    <!-- Template Javascript -->
    <script src="assets/js/main.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

</body>
</html>