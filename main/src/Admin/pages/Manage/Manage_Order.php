<?php
session_start();
require_once('../../../dbcontroller.php');
// Connect to the database
$db_handle = new DBController();

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Manage Order</title>

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
      <span class="brand-text font-weight-light">AdminLTE 3</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="../../dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">Alexander Pierce</a>
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
              <div class="card-header">
                <h3 class="card-title">Manage Order</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="example1" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>order id</th>
                    <th>user_id</th>
                    <th>status</th>
                    <th>shipping method</th>
                    <th>payment method</th>
                    <th>total amount</th>
                    <th>Tracking Number</th>
                    <th>order date</th>
                    <td>Manage Order</td>
                  </tr>
                  </thead>
                  <tbody>
                  <?php
                    $product_array = $db_handle->runQuery("SELECT * FROM Orders ORDER BY order_id ASC");
                    if (!empty($product_array)) {
                        foreach ($product_array as $product) {
                  ?>
                  <tr>
                      <td><?php echo $product['order_id']; ?></td>
                      <td><?php echo $product['user_id']; ?></td>
                      <td class="status-cell"><?php echo $product['status']; ?></td>
                      <td><?php echo $product['shipping_method']; ?></td>
                      <td><?php echo $product['payment_method']; ?></td>
                      <td><?php echo $product['total_amount']; ?></td>
                      <td><?php echo $product['Parcel_number']; ?></td>
                      <td><?php echo $product['order_date']; ?></td>
                      <td><a href="FM_Order.php?order_id=<?=$product['order_id']?>" class="btn btn-warning">Manage Order</a></td>
                  </tr>
                  <?php
                        }
                    } else {
                        echo "<tr><td colspan='6'>ไม่พบสินค้าในระบบ</td></tr>";
                    }
                  ?>

                  </tbody>
                  <tfoot>
                  <tr>
                  <th>order id</th>
                    <th>user_id</th>
                    <th>status</th>
                    <th>shipping method</th>
                    <th>payment method</th>
                    <th>total amount</th>
                    <th>Tracking Number</th>
                    <th>order date</th>
                    <td>Manage Order</td>
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
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <div class="float-right d-none d-sm-block">
      <b>Version</b> 3.2.0
    </div>
    <strong>Copyright &copy; 2014-2021 <a href="https://adminlte.io">AdminLTE.io</a>.</strong> All rights reserved.
  </footer>

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
    // เลือกทุก <td> ที่มี class เป็น 'status-cell'
    var statusCells = document.querySelectorAll('.status-cell');

    // วนลูปผ่านทุก <td> ที่มี class เป็น 'status-cell'
    statusCells.forEach(function(statusCell) {
        // ดึงค่าของ status จาก innerText ของ <td>
        var status = statusCell.innerText.trim(); // ลบช่องว่างข้างนอก
        // ตรวจสอบค่า status และเปลี่ยนแปลง class ของ <td> ตามเงื่อนไข
        switch (status) {
            case 'Pending':
                statusCell.innerHTML = '<a class="btn btn-warning">' + status + '</a>';
                break;
            case 'Being shipped':
                statusCell.innerHTML = '<a class="btn btn-info">' + status + '</a>';
                break;
            case 'To receive':
                statusCell.innerHTML = '<a class="btn btn-primary">' + status + '</a>';
                break;
            case 'Succeed':
                statusCell.innerHTML = '<a class="btn btn-success">' + status + '</a>';
                break;
            case 'Cancelled':
                statusCell.innerHTML = '<a class="btn btn-danger">' + status + '</a>';
                break;
            default:
                break;
        }
    });
</script>
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
