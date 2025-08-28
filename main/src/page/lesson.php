<?php 
session_start();
require_once('../config/database.php'); // เชื่อมต่อกับฐานข้อมูล

if (!isset($_GET['lesson_id']) || !is_numeric($_GET['lesson_id'])) {
    $_SESSION['error'] = "Invalid lesson ID.";
    header("Location: courses.php"); // ไปหน้าหลักหรือหน้าอื่นๆ
    exit;
}

$lesson_id = $_GET['lesson_id'];

$dbController = new DBController();
$conn = $dbController->getConn();

// ดึงข้อมูลบทเรียนจากฐานข้อมูล
$stmt = $conn->prepare("SELECT * FROM lessons WHERE lesson_id = ?");
$stmt->bind_param("i", $lesson_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $lesson = $result->fetch_assoc();
    $lesson_title = htmlspecialchars($lesson['lesson_title']);
    $lesson_description = nl2br(htmlspecialchars($lesson['lesson_description']));
    $lesson_content = $lesson['content']; // ไฟล์ PDF ในรูปแบบ binary (longblob)
    $vdo_url = htmlspecialchars($lesson['vdo_url']);
    $course_code = $lesson['course_code'];
    $lesson_number = $lesson['lesson_number'];
} else {
    $_SESSION['error'] = "Lesson not found.";
    header("Location: courses.php"); // ไปหน้าหลักหรือหน้าอื่นๆ
    exit;
}

// ค้นหาบทเรียนก่อนหน้าและถัดไปใน course เดียวกัน
$prev_stmt = $conn->prepare("SELECT * FROM lessons WHERE course_code = ? AND lesson_number < ? ORDER BY lesson_number DESC LIMIT 1");
$prev_stmt->bind_param("ii", $course_code, $lesson_number);
$prev_stmt->execute();
$prev_result = $prev_stmt->get_result();
$prev_lesson = $prev_result->fetch_assoc();

$next_stmt = $conn->prepare("SELECT * FROM lessons WHERE course_code = ? AND lesson_number > ? ORDER BY lesson_number ASC LIMIT 1");
$next_stmt->bind_param("ii", $course_code, $lesson_number);
$next_stmt->execute();
$next_result = $next_stmt->get_result();
$next_lesson = $next_result->fetch_assoc();

// เช็คว่ามีการบันทึกการเรียนสำหรับ user_id และ lesson_id นี้หรือยัง
if (isset($_SESSION['login'])) {
$user_id = $_SESSION['login']['user_id']; // ดึง user_id จาก session

$check_stmt = $conn->prepare("SELECT * FROM learning_records WHERE user_id = ? AND lesson_id = ?");
$check_stmt->bind_param("ii", $user_id, $lesson_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows == 0) {
    // ถ้าไม่มีการบันทึกให้บันทึกประวัติความคืบหน้า
    $completion_date = date("Y-m-d H:i:s"); // วันที่ที่เรียน

    $insert_stmt = $conn->prepare("INSERT INTO learning_records (user_id, course_code, lesson_id, completion_date) VALUES (?, ?, ?, ?)");
    $insert_stmt->bind_param("isis", $user_id, $course_code, $lesson_id, $completion_date);
    $insert_stmt->execute();
}
$stmt->close();
$prev_stmt->close();
$next_stmt->close();
$check_stmt->close();
$conn->close();
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

    <!-- แสดงข้อมูลบทเรียน -->
    <div class="container">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h6 class="section-title bg-white text-center text-primary px-3"><?=$lesson_number; ?> <?=$lesson_title; ?> </h6>
            <div class="container mt-5">

                <h1 class="text-center"></h1>
                <p><?php echo $lesson_description; ?></p>
                <?php if (!empty($lesson['vdo_url'])): ?>
                    <?php
                    function extractYouTubeID($url) {
                        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $url, $matches);
                        return $matches[1] ?? null;
                    }

                    $videoID = extractYouTubeID($lesson['vdo_url']);
                    ?>

                    <?php if ($videoID): ?>
                        <div class="video-container">
                            <iframe width="1080" height="700" src="https://www.youtube.com/embed/<?php echo htmlspecialchars($videoID); ?>" frameborder="0" allowfullscreen></iframe>
                        </div>
                    <?php else: ?>
                        <p>Invalid video URL.</p>
                    <?php endif; ?>
                <?php else: ?>
                    <p>No video available for this lesson.</p>
                <?php endif; ?>
                <!-- แสดง PDF ด้วย PDF.js -->
                <div id="pdf-container"></div>
                <div class="d-flex justify-content-between">
                    <button id="prev" class="btn btn-primary">Previous</button>
                    <button id="next" class="btn btn-primary">Next</button>
                </div>

                <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.min.js"></script>
                <script>
                    var pdfData = atob("<?php echo base64_encode($lesson_content); ?>");
                    var loadingTask = pdfjsLib.getDocument({data: pdfData});
                    var pdfDoc = null;
                    var currentPage = 1;
                    var totalPages = 0;

                    loadingTask.promise.then(function(pdf) {
                        pdfDoc = pdf;
                        totalPages = pdf.numPages;
                        renderPage(currentPage);
                    });

                    // ฟังก์ชันการแสดงหน้า PDF
                    function renderPage(pageNum) {
                        pdfDoc.getPage(pageNum).then(function(page) {
                            var scale = 1.5;
                            var viewport = page.getViewport({ scale: scale });

                            var canvas = document.createElement("canvas");
                            var context = canvas.getContext("2d");
                            canvas.height = viewport.height;
                            canvas.width = viewport.width;

                            var pdfContainer = document.getElementById('pdf-container');
                            pdfContainer.innerHTML = ''; // เคลียร์หน้าก่อนแสดงหน้าใหม่
                            pdfContainer.appendChild(canvas);

                            page.render({
                                canvasContext: context,
                                viewport: viewport
                            });
                        });
                    }

                    // ฟังก์ชันไปหน้าถัดไป
                    document.getElementById('next').addEventListener('click', function() {
                        if (currentPage < totalPages) {
                            currentPage++;
                            renderPage(currentPage);
                        }
                    });

                    // ฟังก์ชันไปหน้าก่อนหน้า
                    document.getElementById('prev').addEventListener('click', function() {
                        if (currentPage > 1) {
                            currentPage--;
                            renderPage(currentPage);
                        }
                    });
                </script>

                <!-- ปุ่มไปบทถัดไปและย้อนกลับ -->
            <div class="mt-4 d-flex justify-content-between">
                <?php if ($prev_lesson): ?>
                    <a href="lesson.php?lesson_id=<?php echo $prev_lesson['lesson_id']; ?>" class="btn btn-primary">past chapter</a>
                <?php endif; ?>

                <?php if ($next_lesson): ?>
                    <a href="lesson.php?lesson_id=<?php echo $next_lesson['lesson_id']; ?>" class="btn btn-primary">next chapter</a>
                <?php else: ?>
                    <a href="quizzes.php?course_code=<?php echo $course_code; ?>" class="btn btn-success">Go to Quiz</a>
                <?php endif; ?>
            </div>
   
            </div>
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

