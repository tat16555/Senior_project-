<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login']['role'] !== 'admin') {
    $_SESSION['error'] = "You must be an admin to view course details.";
    header("Location: ../../../page/login.php");
    exit;
    }
require_once('../../../config/app.php');
$dbController = new DBController();
$conn = $dbController->getConn();
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
        $upload_dir = "../../../uploads/courses/";
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
                $img_path = "../../../uploads/courses/" . $course_code . "/" . $img_name;
            } else {
                $_SESSION['error'] = "Error uploading image.";
                header("Location: ../tables/courses.php?course_code=$course_code");
                exit;
            }
        }

        // Update the course details in the database
        $update_sql = "UPDATE courses SET course_name = ?, course_description = ?, img = ? WHERE course_code = ?";
        $stmt_update = $conn->prepare($update_sql);
        $stmt_update->bind_param("ssss", $course_name, $course_description, $img_path, $course_code);

        if ($stmt_update->execute()) {
            $_SESSION['success'] = "Course updated successfully!";
            header("Location: ../tables/courses.php");
            exit;
        } else {
            $_SESSION['error'] = "Failed to update course.";
        }
    }

    // Close the connection
    $conn->close();
} else {
    $_SESSION['error'] = "Course code not provided.";
    header("Location: ../tables/courses.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AdminEIH | DataTables</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="../../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="../../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="../../plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../../dist/css/adminlte.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <!-- Navbar -->
  <?php require_once("../nav/nav1.php"); ?>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="../../index3.html" class="brand-link">
      <img src="../../dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">AdminEIH</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
        <img src="../../../img/about2.avif" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block"><?php echo htmlspecialchars($_SESSION['login']['username']); ?></a>
        </div>
      </div>

      <!-- SidebarSearch Form -->
      <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <?php require_once("../nav/nav2.php"); ?>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>DataTables</h1>
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

          </div>
          
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">DataTables</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->
      <section class="content">
      <div class="container-fluid">
          <div class="row">
              <div class="col-12">
                  <div class="card">
                      <div class="card-body">
                      <div class="container mt-5">
                      <h1>Edit Course</h1>
                        <form action="edit_course.php?course_code=<?php echo $course_code; ?>" method="POST" enctype="multipart/form-data">
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
                      </div>
                      <!-- /.card-body -->
                  </div>
                  <!-- /.card -->
              </div>
              <!-- /.col -->
          </div>
          <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
  </section>
  <!-- /.content -->
  </div>
  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="../../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- DataTables  & Plugins -->
<script src="../../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="../../plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="../../plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="../../plugins/jszip/jszip.min.js"></script>
<script src="../../plugins/pdfmake/pdfmake.min.js"></script>
<script src="../../plugins/pdfmake/vfs_fonts.js"></script>
<script src="../../plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="../../plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="../../plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<!-- AdminLTE App -->
<script src="../../dist/js/adminlte.min.js"></script>
<!-- Page specific script -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
