<?php
    session_start();
    require '../config/database.php';
    $db = new DBController();
    $conn = $db->getConn();

    // กำหนดค่าหน้าปัจจุบัน (ถ้าไม่มีให้เป็นหน้าแรก)
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 9; // จำกัดจำนวนรายการต่อหน้า
    $offset = ($page - 1) * $limit;

    // ตรวจสอบว่ามีการค้นหาหรือไม่
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $sql = "SELECT * FROM courses";
    $count_sql = "SELECT COUNT(*) AS total FROM courses";

    if (!empty($search)) {
        $sql .= " WHERE course_name LIKE ? OR course_code LIKE ?";
        $count_sql .= " WHERE course_name LIKE ? OR course_code LIKE ?";
    }

    $sql .= " LIMIT ? OFFSET ?";

    // เตรียม statement
    $stmt = $conn->prepare($sql);
    $count_stmt = $conn->prepare($count_sql);

    if (!empty($search)) {
        $search_param = "%{$search}%";
        $stmt->bind_param("ssii", $search_param, $search_param, $limit, $offset);
        $count_stmt->bind_param("ss", $search_param, $search_param);
    } else {
        $stmt->bind_param("ii", $limit, $offset);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $total_items = $count_result->fetch_assoc()['total'];
    $total_pages = ceil($total_items / $limit);

    
?>
<!DOCTYPE html>
<html lang="en">

<head> 
    <meta charset="utf-8">
    <title>EduInsightHub</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

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
    <style>
        @import url('https://fonts.googleapis.com/css?family=Poppins:400,500,600,700&display=swap');

        ::selection {
            color: #fff;
            background: #664AFF;
        }

        .search-box {
            position: relative;
            height: 60px;
            width: 60px;
            border-radius: 50%;
            box-shadow: 5px 5px 30px rgba(0, 0, 0, .2);
            transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        .search-box.active {
            width: 350px;
        }

        .search-box input {
            width: 100%;
            height: 100%;
            border: none;
            border-radius: 50px;
            background: #fff;
            outline: none;
            padding: 0 60px 0 20px;
            font-size: 18px;
            opacity: 0;
            transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        .search-box input.active {
            opacity: 1;
        }

        .search-box input::placeholder {
            color: #a6a6a6;
        }

        .search-box .search-icon {
            position: absolute;
            right: 0px;
            top: 50%;
            transform: translateY(-50%);
            height: 60px;
            width: 60px;
            background: #fff;
            border-radius: 50%;
            text-align: center;
            line-height: 60px;
            font-size: 22px;
            color: #664AFF;
            cursor: pointer;
            z-index: 1;
            transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        .search-box .search-icon.active {
            right: 5px;
            height: 50px;
            line-height: 50px;
            width: 50px;
            font-size: 20px;
            background: #664AFF;
            color: #fff;
            transform: translateY(-50%) rotate(360deg);
        }

        .search-box .cancel-icon {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 25px;
            color: #fff;
            cursor: pointer;
            transition: all 0.5s 0.2s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        .search-box .cancel-icon.active {
            right: -40px;
            transform: translateY(-50%) rotate(360deg);
        }

        .search-box .search-data {
            text-align: center;
            padding-top: 7px;
            color: #fff;
            font-size: 18px;
            word-wrap: break-word;
        }

        .search-box .search-data.active {
            display: none;
        }
    </style>
</head>

<body>
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- Spinner End -->


    <!-- Navbar Start -->
    <?php require_once("views/Navbar.php"); ?>
    <!-- Navbar End -->


    <!-- Header Start -->
    <form method="GET" action="" class="d-flex">
        <div class="container-fluid bg-primary py-1 mb-5 page-header">
            <div class="container py-5">
                <div class="row justify-content-center">
                    <div class="search-box">
                        <input type="text" name="search" placeholder="Type to search.." value="<?= htmlspecialchars($search) ?>">
                        <div class="search-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <div class="cancel-icon">
                            <i class="fas fa-times"></i>
                        </div>
                        <div class="search-data">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <!-- Header End -->
<!-- Courses Section -->
<div class="container-xxl py-5">
    <div class="container">
        <div class="text-center wow fadeInUp" data-wow-delay="0.2s">
            <h1 class="text-center mb-5">Popular Courses</h1>
        </div>
        <div class="row g-4 justify-content-center">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php
                        // Query to get instructor's first name
                        $user_id = $row['user_id'];
                        $instructor_sql = "SELECT first_name FROM Users WHERE user_id = ?";
                        $instructor_stmt = $conn->prepare($instructor_sql);
                        $instructor_stmt->bind_param("i", $user_id);
                        $instructor_stmt->execute();
                        $instructor_result = $instructor_stmt->get_result();
                        $instructor_name = $instructor_result->fetch_assoc()['first_name'];
                    ?>                
                    <div class="col-lg-4 col-md-6">
                        <div class="course-item bg-light">
                            <div class="position-relative overflow-hidden">
                                <img class="img-fluid" src="../../<?= $row['img'] ?>" alt="Course Image" style="object-fit: cover; width: 100%; height: 300px;">
                                <div class="w-100 d-flex justify-content-center position-absolute bottom-0 start-0 mb-4">
                                    <a href="learning.php?course_code=<?= $row['course_code'] ?>" class="flex-shrink-0 btn btn-sm btn-primary px-3 border-end" style="border-radius: 30px 0 0 30px;">Read More</a>
                                    <a href="learning.php?course_code=<?= $row['course_code'] ?>" class="flex-shrink-0 btn btn-sm btn-primary px-3" style="border-radius: 0 30px 30px 0;">Join Now</a>
                                </div>
                            </div>
                            <div class="text-center p-4 pb-0">
                                <h5 class="mb-4"><?= htmlspecialchars($row['course_name']) ?> (<?= $row['course_code'] ?>)</h5>
                            </div>
                            <div class="d-flex border-top">
                                <small class="flex-fill text-center border-end py-2"><i class="fa fa-user-tie text-primary me-2"></i><?php echo $instructor_name ?></small>
                                <small class="flex-fill text-center py-2"><i class="fa fa-user text-primary me-2"></i><?= $row['view'] ?> views</small>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center">ไม่พบคอร์สที่คุณค้นหา</p>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center mt-4">
                <?php if ($page > 1): ?>
                    <li class="page-item"><a class="page-link" href="?search=<?= urlencode($search) ?>&page=<?= $page - 1 ?>">ก่อนหน้า</a></li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?search=<?= urlencode($search) ?>&page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <li class="page-item"><a class="page-link" href="?search=<?= urlencode($search) ?>&page=<?= $page + 1 ?>">ถัดไป</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</div>


    <!-- Footer Start -->
    <?php require_once("views/footer.php"); ?>
    <!-- Footer End -->


    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>


    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/lib/wow/wow.min.js"></script>
    <script src="../assets/lib/easing/easing.min.js"></script>
    <script src="../assets/lib/waypoints/waypoints.min.js"></script>
    <script src="../assets/lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="../assets/js/main.js"></script>
    <script>
        const searchBox = document.querySelector(".search-box");
        const searchBtn = document.querySelector(".search-icon");
        const cancelBtn = document.querySelector(".cancel-icon");
        const searchInput = document.querySelector("input");
        const searchData = document.querySelector(".search-data");

        // เมื่อคลิกปุ่มค้นหา
        searchBtn.onclick = () => {
            searchBox.classList.add("active");
            searchBtn.classList.add("active");
            searchInput.classList.add("active");
            cancelBtn.classList.add("active");
            searchInput.focus();
            if(searchInput.value != "") {
                var values = searchInput.value;
                searchData.classList.remove("active");
                searchData.innerHTML = "You just typed " + "<span style='font-weight: 500;'>" + values + "</span>";
            } else {
                searchData.textContent = "";
            }
        }

        // เมื่อคลิกปุ่มล้าง
        cancelBtn.onclick = () => {
            searchBox.classList.remove("active");
            searchBtn.classList.remove("active");
            searchInput.classList.remove("active");
            cancelBtn.classList.remove("active");
            searchData.classList.toggle("active");
            searchInput.value = "";
        }
    </script>
</body>

</html>