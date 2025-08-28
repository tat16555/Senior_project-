<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login']['role'] !== 'admin') {
  $_SESSION['error'] = "You must be an admin to view course details.";
  header("Location: ../login.php");
  exit;
}
require_once('../../../config/database.php');
// Connect to the database
$db_handle = new DBController();
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
            <div class="card">
              <!-- /.card-header -->
              <div class="card-body">
                <table id="example1" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>lesson id</th>
                    <th>course id</th>
                    <th>number</th>
                    <th>lesson title</th>
                    <th>lesson description</th>
                    <th>created at</th>
                    <th>updated at</th>
                    <th>Management Data</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php
                  $quiz_id = intval($_GET['quiz_id']);

                  // Create a new instance of the DBController
                  $dbController = new DBController();
                  $conn = $dbController->getConn();

                  // Prepare the SQL statement
                  $query = "SELECT question_id, quiz_id, question_text, option_a, option_b, option_c, option_d, correct_answer,created_at, updated_at FROM quiz_attempts WHERE quiz_id = ?";
                  $stmt = $conn->prepare($query);

                  // Check if the preparation was successful
                  if ($stmt === false) {
                      die("Error preparing the statement: " . $conn->error);
                  }

                  // Bind the parameter
                  $stmt->bind_param("i", $course_id);

                  // Execute the statement
                  $stmt->execute();

                  // Get the result
                  $result = $stmt->get_result();

                  // Check for query success
                  if ($result === false) {
                      die("Error fetching data: " . $stmt->error);
                  }

                  // Main content
                  ?>
                  <?php while ($lessons = $result->fetch_assoc()): ?>
                  <tr>
                      <td><?php echo $lessons['question_id']; ?></td>
                      <td><?php echo $lessons['quiz_id']; ?></td>
                      <td><?php echo $lessons['question_text']; ?></td>
                      <td><?php echo $lessons['option_a']; ?></td>
                      <td><?php echo $lessons['option_b']; ?></td>
                      <td><?php echo $lessons['option_c']; ?></td>
                      <td><?php echo $lessons['option_d']; ?></td>
                      <td><?php echo $lessons['correct_answer']; ?></td>
                      <td><?php echo $lessons['created_at']; ?></td>
                      <td><?php echo $lessons['updated_at']; ?></td>
                      <td>
                        <button class="btn btn-warning">Edit</button> 
                        <button class="btn btn-danger">Delete</button>
                        
                        <a href="../../forms/Edit/quiz_questions.php?question_id=<?= $lessons['question_id']; ?>" class="btn btn-warning">Edit</a>
                        <a href="../../system/Delete/quiz_questions.php?question_id=<?= $lessons['question_id']; ?>" class="btn btn-danger">Delete</a>
                      </td>                      
                  </tr>
                  <?php endwhile; ?>
                  </tbody>
                  <tfoot>
                  <tr>
                    <th>lesson id</th>
                    <th>course id</th>
                    <th>number</th>
                    <th>lesson title</th>
                    <th>lesson description</th>
                    <th>created at</th>
                    <th>updated at</th>
                    <th>Management Data</th>
                  </tr>
                  </tfoot>
                </table>
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
<script>
  $(function () {
    $("#example1").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });
  });
</script>
</body>
</html>
