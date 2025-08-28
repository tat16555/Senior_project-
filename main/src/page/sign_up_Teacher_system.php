<?php
session_start();
require_once('../config/database.php');
$db = new DBController();
if (!isset($_SESSION['login']) || $_SESSION['login']['role'] !== 'user') {
    $_SESSION['error'] = "You must be an user to view course details.";
    header("Location: login.php");
    exit;
}	
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
    <link href="../assets/lib/animate/animate.min.css" rel="stylesheet">
    <link href="../assets/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="../assets/css/style.css" rel="stylesheet">
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
                <h6 class="section-title bg-white text-center text-primary px-3">teachers?</h6>
                <h1>What can teachers do?</h1>
            </div>
            <div class="row g-4">
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="service-item text-center pt-3">
                        <div class="p-4">
                            <i class="fa fa-3x fa-graduation-cap text-primary mb-4"></i>
                            <h5 class="mb-3">สร้างเนื้อ</h5>
                            <p>"สร้าง & ออกแบบ พร้อมทั้งจัดการเนื้อหารายวิชาของคุณ"</p>   
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="service-item text-center pt-3">
                        <div class="p-4">
                            <i class="fa fa-3x fa-globe text-primary mb-4"></i>
                            <h5 class="mb-3">แบ่งปัญองค์ความรู้</h5>
                            <p>แชร์ความรู้ของคุณกับผู้คนทัวโลก</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="service-item text-center pt-3">
                        <div class="p-4">
                            <i class="fa fa-3x fa-home text-primary mb-4"></i>
                            <h5 class="mb-3">ระบบการจัดการใช้งานง่าย</h5>
                            <p>ระบบการจัดการที่ออกแบบมาให้ใช้งานง่ายไม่ซับซ้อน</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.7s">
                    <div class="service-item text-center pt-3">
                        <div class="p-4">
                            <i class="fa fa-3x fa-book-open text-primary mb-4"></i>
                            <h5 class="mb-3">ออกใบ certificate</h5>
                            <p>ออกใบ certificate</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Categories Start -->
    <!-- About Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s" style="min-height: 250px;">
                    <div class="position-relative h-100">
                        <img class="img-fluid position-absolute w-100 h-100" src="../img/Course List.png" alt="" style="object-fit: cover;">
                    </div>
                </div>
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.3s">
                    <h1 class="mb-4">ระบบจัดการบทเรียน</h1>
                    <p class="mb-4">ระบบจัดการบทเรียนเป็นเครื่องมือที่ออกแบบมาเพื่อช่วยผู้ใช้ในการสร้างและจัดการบทเรียนต่างๆ ได้อย่างมีประสิทธิภาพ 
                        โดยการใช้งานจะสะดวกและไม่ซับซ้อน สามารถเพิ่ม แก้ไข หรือลบข้อมูลบทเรียนได้ในไม่กี่คลิก รองรับการตั้งค่าหัวข้อ และเนื้อหาของบทเรียนอย่างละเอียด 
                        ทำให้ผู้ใช้สามารถสร้างบทเรียนที่ตรงกับความต้องการได้ง่ายๆ โดยไม่จำเป็นต้องมีความรู้ด้านเทคนิคมาก่อน นอกจากนี้ยังมีฟังก์ชันที่ช่วยให้การติดตามและตรวจสอบ
                        ความคืบหน้าของบทเรียนเป็นเรื่องง่าย เช่น การดูสถิติการเข้าเรียน หรือการตรวจสอบผลการเรียนของผู้เรียนในแต่ละบทเรียน</p>
                </div>
            </div>
        </div>
    </div>
    <div class="container-xxl py-5">
        <div class="container"> 
            <div class="row g-5">
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s" style="min-height: 250px;">
                    <div class="position-relative h-100">
                        <img class="img-fluid position-absolute w-100 h-100" src="../img/teachers123.png" alt="" style="object-fit: cover;">
                    </div>
                </div>
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.3s">
                    <h1 class="mb-4">สร้างรายวิชาของคุณเองตอนนี้</h1>
                    <p class="mb-4">ตอนนี้คุณสามารถสร้างรายวิชาใหม่ตามความต้องการของคุณได้ง่ายๆ คุณก็สามารถเริ่มต้นได้ทันที 
                        ระบบจะช่วยให้คุณสามารถจัดการเนื้อหาต่างๆ อย่างมีระเบียบ ไม่ว่าจะเป็นการอัปโหลดไฟล์ การสร้างบทเรียน 
                        ได้อย่างสะดวกและรวดเร็ว</p>
                </div>
                <a class="btn btn-primary py-3 px-5 mt-2" href="../../system/sign_up_Teacher_system.php">Become an instructor now Click</a> 
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
    <script>
        document.getElementById('passwordForm').addEventListener('submit', function (event) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const passwordHelp = document.getElementById('passwordHelp');

            // ตรวจสอบว่ารหัสผ่านตรงกันหรือไม่
            if (password !== confirmPassword) {
                event.preventDefault(); // ยกเลิกการส่งฟอร์ม
                passwordHelp.classList.remove('d-none'); // แสดงข้อความเตือน
            } else {
                passwordHelp.classList.add('d-none'); // ซ่อนข้อความเตือน
            }
        });
    </script>

</body>
</html>