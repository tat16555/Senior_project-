<?php
session_start();
require_once('../../config/app.php');
$dbController = new DBController();
$conn = $dbController->getConn();
// Check if the user is logged in and has the 'Instructor' role
if (!isset($_SESSION['login']) || $_SESSION['login']['role'] !== 'Instructor') {
    $_SESSION['error'] = "You must be an Instructor to view course details.";
    header("Location: login.php");
    exit;
}

if (isset($_GET['course_code'])) {
    $course_code = $_GET['course_code'];

    // Query to fetch the course details by course_code
    $sql = "SELECT * FROM courses WHERE course_code = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $course_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $course = $result->fetch_assoc();
    } else {
        $_SESSION['error'] = "Course not found.";
        header("Location: courses.php");
        exit;
    }

    // If form is submitted to update course
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Sanitize and get updated form data
        $course_name = $_POST['course_name'];
        $course_description = $_POST['course_description'];
        
        // Handling image upload
        $upload_dir = "../../uploads/courses/";
        $course_folder = $upload_dir . $course_code;

        // Create folder if it doesn't exist
        if (!is_dir($course_folder)) {
            mkdir($course_folder, 0777, true);
        }

        $img_path = $course['img']; // Keep the old image if no new image uploaded
        if (!empty($_FILES['course_image']['name'])) {
            // If there is a new image, delete the old one first
            if (!empty($course['img']) && file_exists($course_folder . "/" . basename($course['img']))) {
                unlink($course_folder . "/" . basename($course['img'])); // Delete old image
            }

            // Upload the new image
            $img_name = basename($_FILES["course_image"]["name"]);
            $target_file = $course_folder . "/" . $img_name;

            if (move_uploaded_file($_FILES["course_image"]["tmp_name"], $target_file)) {
                $img_path = "../../uploads/courses/" . $course_code . "/" . $img_name;
            } else {
                $_SESSION['error'] = "Error uploading image.";
                header("Location: F_edit_course.php?course_code=$course_code");
                exit;
            }
        }

        // Update the course details in the database
        $update_sql = "UPDATE courses SET course_name = ?, course_description = ?, img = ? WHERE course_code = ?";
        $stmt_update = $conn->prepare($update_sql);
        $stmt_update->bind_param("ssss", $course_name, $course_description, $img_path, $course_code);

        if ($stmt_update->execute()) {
            $_SESSION['success'] = "Course updated successfully!";
            header("Location: course_list.php");
            exit;
        } else {
            $_SESSION['error'] = "Failed to update course.";
        }
    }

    // Close the connection
    $conn->close();
} else {
    $_SESSION['error'] = "Course code not provided.";
    header("Location: course_list.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Course</title>
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
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success" role="alert">
            <?php echo $_SESSION['success']; ?>
            <?php unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    <!-- Navbar End -->
    <?php require_once("views/Navbar.php"); ?>
    <!-- Navbar End -->
    <div class="container mt-5">
        <h1>Edit Course</h1>
        <form action="F_edit_course.php?course_code=<?php echo $course_code; ?>" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="course_code" class="form-label">Course Code</label>
                <input type="text" name="course_code" id="course_code" class="form-control" value="<?php echo $course['course_code']; ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="course_name" class="form-label">Course Name</label>
                <input type="text" name="course_name" id="course_name" class="form-control" value="<?php echo $course['course_name']; ?>" required>
            </div>

            <div class="mb-3">
                <label for="course_description" class="form-label">Course Description</label>
                <textarea name="course_description" id="course_description" class="form-control"><?php echo $course['course_description']; ?></textarea>
            </div>
            <div class="mb-3">
                <label for="course_image" class="form-label">Course Image</label>
                <input type="file" name="course_image" id="course_image" class="form-control" accept="image/*">
                <?php if ($course['img']): ?>
                    <img src="../<?php echo $course['img']; ?>" alt="Current Image" width="100">
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary">Save Changes</button>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
