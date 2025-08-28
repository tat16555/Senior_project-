<?php 
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login']['role'] !== 'Instructor') {
    $_SESSION['error'] = "You must be an Instructor to view course details.";
    header("Location: login.php");
    exit;
}

$course_code = intval($_GET['course_code']);
$lesson_id = intval($_GET['lesson_id']); 

// Connect to the database
require_once('../../config/app.php');
$dbController = new DBController();
$conn = $dbController->getConn();

// Fetch lesson details
$sql = "SELECT * FROM lessons WHERE lesson_id = ? AND course_code = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $lesson_id, $course_code);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $lesson = $result->fetch_assoc();
} else {
    $_SESSION['error'] = "Lesson not found or you don't have permission to edit this lesson.";
    header("Location: courses.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lesson_title = trim($_POST['lesson_title']);
    $lesson_description = trim($_POST['lesson_description']);
    $vdo_url = trim($_POST['vdo_url']);
    $upload_path = $lesson['content']; // Default to existing file

    // Handle file upload
    if (!empty($_FILES['content']['name'])) {
        $target_dir = "../../uploads/";
        $file_name = basename($_FILES['content']['name']);
        $target_file = $target_dir . $file_name;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validate file type
        $allowed_types = ["pdf", "doc", "docx"];
        if (in_array($file_type, $allowed_types)) {
            if (move_uploaded_file($_FILES['content']['tmp_name'], $target_file)) {
                $upload_path = $target_file;
            } else {
                $_SESSION['error'] = "Failed to upload file.";
                header("Location: edit_lesson.php?course_code=$course_code&lesson_id=$lesson_id");
                exit;
            }
        } else {
            $_SESSION['error'] = "Invalid file type. Only PDF, DOC, and DOCX are allowed.";
            header("Location: edit_lesson.php?course_code=$course_code&lesson_id=$lesson_id");
            exit;
        }
    }

    // Update lesson details
    $update_sql = "UPDATE lessons SET lesson_title = ?, lesson_description = ?, content = ?, vdo_url = ? WHERE lesson_id = ? AND course_code = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssssii", $lesson_title, $lesson_description, $upload_path, $vdo_url, $lesson_id, $course_code);

    if ($update_stmt->execute()) {
        $_SESSION['success'] = "Lesson updated successfully.";
    } else {
        $_SESSION['error'] = "Failed to update lesson.";
    }

    header("Location: learning_list.php?course_code=$course_code&lesson_id=$lesson_id");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Lesson</title>
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
<div class="container mt-4">
    <h2>Update Lesson</h2>
    <p>Course Code: <?=$course_code?></p>
    <form action="" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="course_code" value="<?=$course_code?>">
        <input type="hidden" name="lesson_id" value="<?=$lesson_id?>">

        <div class="mb-1">
            <label for="lesson_title" class="form-label">Lesson Title</label>
            <input type="text" name="lesson_title" id="lesson_title" class="form-control" value="<?=$lesson['lesson_title']?>" required>
        </div>
        <div class="mb-1">
            <label for="lesson_description" class="form-label">Lesson Description</label>
            <textarea name="lesson_description" id="lesson_description" class="form-control"><?=$lesson['lesson_description']?></textarea>
        </div>
        <div class="mb-3">
            <label for="content" class="form-label">Upload Content (PDF, DOCX, etc.)</label>
            <input type="file" name="content" id="content" class="form-control" accept=".pdf,.doc,.docx">
        </div>
        <div class="mb-3">
            <label for="vdo_url" class="form-label">Video URL</label>
            <textarea name="vdo_url" id="vdo_url" class="form-control" value="<?=$lesson['vdo_url']?>"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Save Lesson</button>
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
    <script src="../../assets/lib/wow/wow.min.js"></script>
    <script src="../../assets/ib/easing/easing.min.js"></script>
    <script src="../../assets/lib/waypoints/waypoints.min.js"></script>
    <script src="../../assets/lib/owlcarousel/owl.carousel.min.js"></script>
    <!-- Template Javascript -->
    <script src="assets/js/main.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
</body>
</html>
