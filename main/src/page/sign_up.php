<?php
session_start();
// require 'config/app.php';
require_once('../config/app.php');
require_once('../config/database.php');
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
                <h6 class="section-title bg-white text-center text-primary px-3">Sign Up</h6>
                <h1 class="mb-5">Sign Up Form</h1>
                <form action="../system/sign_up_system.php" id="passwordForm" method="POST">
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1">Username</span>
                        <input type="text" class="form-control" placeholder="Username" aria-label="nickname" aria-describedby="basic-addon1" id="username" name="username" required>
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1">E-mail</span>
                        <input type="email" class="form-control" placeholder="Username" aria-label="email" aria-describedby="basic-addon1" id="email" name="email" required>
                    </div>

                    <div class="input-group mb-3">
                        <span class="input-group-text">First name</span>
                        <input type="text" class="form-control" placeholder="First name" aria-label="first name" id="first_name" name="first_name" required>
                        <span class="input-group-text">Last name</span>
                        <input type="text" class="form-control" placeholder="Last name" aria-label="last name" id="last_name" name="last_name" required>
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text">Birthdate</span>
                        <input type="date" class="form-control" placeholder="birthdate" aria-label="birthdate" id="birthdate" name="birthdate" required>
                    </div>

                    <div class="input-group mb-3">
                        <span class="input-group-text">Password</span>
                        <input type="password" class="form-control" placeholder="password" aria-label="password" id="password" name="password" required>
                        <span class="input-group-text">Confirm password</span>
                        <input type="password" class="form-control" placeholder="Confirm password" aria-label="Confirm password" id="confirmPassword" name="confirmPassword" required>
                        <div id="passwordHelp" class="form-text text-danger d-none">Passwords do not match</div>
                    </div>
                    
                    <input type="submit" class="btn btn-primary" value="Submit">
                </form>
                <br><hr><br>
            <h1> If you already have an account <a href="sign_in.php"> <button type="button" class="btn btn-success">sign in</button></h1></a>
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
        ////////////////////////////////////////////////////////////////////////////
        document.addEventListener("DOMContentLoaded", function () {
            let birthdateInput = document.getElementById("birthdate");
            
            // คำนวณวันที่สูงสุดที่อนุญาต (ต้องมีอายุ 5 ปีขึ้นไป)
            let today = new Date();
            let minDate = new Date(today.getFullYear() - 100, today.getMonth(), today.getDate()); // อายุไม่เกิน 100 ปี
            let maxDate = new Date(today.getFullYear() - 5, today.getMonth(), today.getDate()); // อย่างน้อย 5 ปีขึ้นไป

            // กำหนดค่าขั้นต่ำและค่าสูงสุดใน input
            birthdateInput.setAttribute("min", minDate.toISOString().split("T")[0]);
            birthdateInput.setAttribute("max", maxDate.toISOString().split("T")[0]);

            // ตรวจสอบเมื่อมีการเปลี่ยนค่า
            birthdateInput.addEventListener("change", function () {
                let selectedDate = new Date(this.value);
                let age = today.getFullYear() - selectedDate.getFullYear();

                if (selectedDate > maxDate) {
                    alert("ผู้ใช้ต้องมีอายุอย่างน้อย 5 ปีขึ้นไป");
                    this.value = ""; // รีเซ็ตค่า
                }
            });
        });
    </script>

</body>
</html>