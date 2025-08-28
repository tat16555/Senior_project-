<?php
    session_start();
    if (!isset($_SESSION['login']) || $_SESSION['login']['role'] !== 'admin') {
        $_SESSION['error'] = "You must be an admin to view course details.";
        header("Location: ../../../page/login.php");
        exit;
        }
    $course_code = intval($_GET['course_code']);
    $lesson_id = intval($_GET['lesson_id']); 
    
    // Connect to the database
    require_once('../../../config/app.php');
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
        header("Location: Location: edit_lesson.php?course_code=$course_code&lesson_id=$lesson_id");
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
    
        header("Location: ../tables/lessons.php?course_code=$course_code&lesson_id=$lesson_id");
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
