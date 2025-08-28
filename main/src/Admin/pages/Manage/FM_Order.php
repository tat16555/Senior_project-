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
  <title>AdminLTE 3 | DataTables</title>

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
              <div class="card-header">
                    <?php
                      // ดึงรายการสั่งซื้อของผู้ใช้จากฐานข้อมูล
                      $order_id = $_GET['order_id'];

                      // ดึงข้อมูล order
                      $orderQuery = "SELECT * FROM Orders WHERE order_id = ?";
                      $orders = $db_handle->runQuery($orderQuery, 'i', [$order_id]);
                      
                      // ถ้าพบ order ที่ตรงกับ order_id ที่ระบุ
                    if (!empty($orders)) {
                          foreach ($orders as $order) {
                              $order_id = $order['order_id'];
                              $user_id = $order['user_id'];
                              $order_date = $order['order_date'];
                              $status = $order['status'];
                              $shipping_method = $order['shipping_method'];
                              $payment_method = $order['payment_method'];
                              $Parcel_number = $order['Parcel_number'];
                              $total_amount = $order['total_amount'];
                          }
                      
                          // ดึงข้อมูล user จากตาราง users
                          $orderUsersQuery = "SELECT * FROM users WHERE id = ?";
                          $users = $db_handle->runQuery($orderUsersQuery, 'i', [$user_id]);
                      
                          // ถ้าพบข้อมูล user ที่ตรงกับ user_id ที่ระบุ
                              foreach ($users as $user) {
                                  $name = $user['name'];
                                  $email = $user['email'];
                                  $phone = $user['phone'];
                                  $real_name = $user['real_name'];
                                  $last_name = $user['last_name'];
                                  $address = $user['address'];
                                  // ทำสิ่งที่ต้องการกับข้อมูลผู้ใช้ตามที่คุณต้องการ

                              }

                      }                                                             
                    ?>                  
                <!-- Main content -->
                <div class="invoice p-3 mb-3">
                  <!-- title row -->
                  <div class="row">
                    <div class="col-12">
                      <h4>
                        <i class="fas fa-globe"></i> Details
                        <small class="float-right"><?php echo $order_date ?></small>
                      </h4>
                    </div>
                    <!-- /.col -->
                  </div>
                  <!-- info row -->
                  <div class="row invoice-info">
                    <div class="col-sm-4 invoice-col">
                      From
                      <address>
                        <strong>Thepsadej Caffeinet.</strong><br>
                        795 Folsom Ave, Suite 600<br>
                        San Francisco, CA 94107<br>
                        Phone: (+66) 0622951263<br>
                        Email: pannatat.b@gmail.com
                      </address>
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-4 invoice-col">
                      To
                      <address>
                        <strong><?php echo $real_name?> <?php echo $last_name?></strong><br>
                        <?php echo $address ?><br>
                        Phone: (+66) <?php echo $phone ?><br>
                        Email: <?php echo $email ?>
                      </address>
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-4 invoice-col">
                      <b>Invoice #<?php echo $order_id ?></b><br>
                      <br>
                      <b>Order ID:</b> <?php echo $order_id ?><br>
                      <b>payment method :</b> <?php echo $payment_method ?><br><br>

                      <b>status :</b>  <a class="btn btn-primary"><?php echo $status ?></a><br>
                    </div>
                    <!-- /.col -->
                  </div>
                  <!-- /.row -->

                  <!-- Table row -->
                  <div class="row">
                    <div class="col-12 table-responsive">
                      <table class="table table-striped">
                        <thead>
                        <tr>
                          <th>product code</th>
                          <th>product_name</th>
                          <th>quantity</th>
                          <th>price</th>
                          <th>pick_option</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        // ดึงรายการสินค้าที่ถูกสั่งซื้อในแต่ละรายการ
                              $orderItemsQuery = "SELECT * FROM OrderItems WHERE order_id = ?";
                              $orderItems = $db_handle->runQuery($orderItemsQuery, 'i', [$order_id]);
                              foreach ($orderItems as $item){
                              
                        ?>                      
                        <tr>
                          <td><?php echo $item['product_code'];?></td>
                          <td><?php echo $item['product_name'];?></td>
                          <td><?php echo $item['quantity'];?></td>
                          <td><?php echo $item['price'];?></td>
                          <td><?php echo $item['pick_option'];?></td>
                        </tr>
                        <?php
                        }
                        ?>
                        </tbody>
                      </table>
                    </div>
                    <!-- /.col -->
                  </div>
                  <!-- /.row -->

                  <div class="row">
                    <!-- /.col -->
                    <div class="col-6">

                      <div class="table-responsive">
                        <table class="table">
                          <tr>
                            <th>Total:</th>
                            <td>$<?php echo $total_amount ?></td>
                          </tr>
                        </table>
                      </div>
                    </div>
                    <!-- /.col -->
                  </div>
                  <!-- /.row -->

                  <!-- this row will not appear when printing -->
                  <div class="row no-print">
                  <div class="row no-print">
                      <div class="col-12">
                          <?php
                          if ($status == "Pending") {
                              $status1 = "Being shipped";
                          } elseif ($status == "Being shipped") {
                              $status1 = "To receive";
                          } elseif ($status == "To receive") {
                              $status1 = "Succeed";
                          } elseif ($status == "Succeed") {
                              $status1 = "Succeed";
                          }
                          ?>                  
                          <form class="row g-3" action="SM_Order.php" method="get" onsubmit="return validateForm()">
                              <input type="hidden" name="order_id" value="<?php echo $order_id ?>">
                              <input type="hidden" name="status1" value="<?php echo $status1 ?>">
                              <?php if ($status == "Being shipped") { ?>
                                  <div class="col-auto">
                                    <label for="Parcel_number" class="visually-hidden">Tracking Number: </label>
                                    <input type="text" class="form-control" id="Parcel_number" name="Parcel_number" placeholder="Tracking Number">
                                  </div>
                              <?php } ?>
                              <div class="col-auto">
                                <button type="submit" class="btn btn-primary mb-3"><h1><i class="far fa-credit-card"> <?php echo $status1 ?></i></h1></button>
                              </div>
                          </form>
                      </div>
                  </div>
                  </div>
                </div>
                <!-- /.invoice -->
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
<script>
function validateForm() {
    <?php if ($status == "Being shipped") { ?>
        var trackingNumber = document.getElementById('tracking_number').value;
        if (trackingNumber == "") {
            alert("Please enter the tracking number.");
            return false;
        }
    <?php } ?>
    return true;
}
</script>
</body>
</html>
