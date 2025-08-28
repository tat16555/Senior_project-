<?php
    session_start();
    if (!isset($_SESSION['login']) || $_SESSION['login']['role'] !== 'admin') {
    $_SESSION['error'] = "You must be an admin to view course details.";
    header("Location: ../../page/login.php");
    exit;
    }
    // รับค่า course_code จาก URL
    if (isset($_GET['course_code'])) {
        $course_code = $_GET['course_code'];
    } else {
        // ถ้าไม่มี course_code ให้ส่งกลับไปยังหน้าแรกหรือแสดงข้อความผิดพลาด
        $_SESSION['error'] = "Course code not found!";
        header("Location: ../index.php");
        exit;
    }
    
    require_once('../../../config/database.php');
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
  <?php
    // เชื่อมต่อฐานข้อมูล
    $dbController = new DBController();
    $conn = $dbController->getConn();

    // ดึงข้อมูล quizzes ที่เกี่ยวข้องกับ course_code
    $stmt_quiz = $conn->prepare("SELECT * FROM quizzes WHERE course_code = ?");
    $stmt_quiz->bind_param("i", $course_code);
    $stmt_quiz->execute();
    $result_quiz = $stmt_quiz->get_result();
?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>DataTables</h1>
            <?php while ($quiz = $result_quiz->fetch_assoc()): ?>
                <li>
                <strong><?php echo htmlspecialchars($quiz['quiz_title']); ?></strong><br>
                    Score: <?php echo htmlspecialchars($quiz['score_full']); ?> | Passing Score: <?php echo htmlspecialchars($quiz['pass_score']); ?>
                </li>
                    <a href="../forms/add_question_form.php?quiz_id=<?= $quiz['quiz_id']; ?>" class="btn btn-primary">Add quiz</a>
                <?php $quiz_id = $quiz['quiz_id']?>
            <?php endwhile; ?>            
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
                    <th>question</th>
                    <th>A</th>
                    <th>B</th>
                    <th>C</th>
                    <th>D</th>
                    <th>correct answer</th>
                    <th>created_at</th>
                    <th>manage</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php 
                    $stmt_questions = $conn->prepare("SELECT * FROM quiz_questions WHERE quiz_id = ?");
                    $stmt_questions->bind_param("i", $quiz_id);
                    $stmt_questions->execute();
                    $result_questions = $stmt_questions->get_result();
                ?>
                <?php while ($question = $result_questions->fetch_assoc()): ?>
                  <tr>
                      <td><?php echo htmlspecialchars($question['question_text']); ?></td>
                      <td><?php echo htmlspecialchars($question['option_a']); ?></td>
                      <td><?php echo htmlspecialchars($question['option_b']); ?></td>
                      <td><?php echo htmlspecialchars($question['option_c']); ?></td>
                      <td><?php echo htmlspecialchars($question['option_d']); ?></td>
                      <td><?php echo htmlspecialchars($question['correct_answer']); ?></td>
                      <td><?php echo htmlspecialchars($question['created_at']); ?></td>
                      <td>
                        <a href="../forms/edit_question.php?question_id=<?= $question['question_id']; ?>" class="btn btn-warning">Edit</a>
                        <a href="../../system/Delete/quiz_questions.php?question_id=<?= $question['question_id']; ?>" class="btn btn-danger">Delete</a>
                      </td>                      
                  </tr>
                  <?php endwhile; ?>
                  </tbody>
                  <tfoot>
                  <tr>
                    <th>question</th>
                    <th>A</th>
                    <th>B</th>
                    <th>C</th>
                    <th>D</th>
                    <th>correct answer</th>
                    <th>created_at</th>
                    <th>manage</th>
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
