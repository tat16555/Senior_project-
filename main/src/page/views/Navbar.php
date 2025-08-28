    <!-- Navbar Start -->
    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
        <a href="../index.php" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
            <h2 class="m-0 text-primary"><i class="fa fa-book me-3"></i>EduInsightHub</h2>
        </a>
        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="../index.php" class="nav-item nav-link active">Home</a>
                <a href="about.php" class="nav-item nav-link">About</a>
                <a href="courses.php" class="nav-item nav-link">Courses</a>
                <a href="contact.php" class="nav-item nav-link">Contact</a>

                <?php if (isset($_SESSION['login']) && $_SESSION['login']['role'] === 'Instructor'): ?>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">my courses</a>
                        <div class="dropdown-menu fade-down m-0">
                            <a href="Instructor/Add_Course.php" class="dropdown-item">Add Course</a>
                            <a href="Instructor/course_list.php" class="dropdown-item">my courses list</a>
                        </div>
                    </div>
                <?php elseif (isset($_SESSION['login'])): ?>
                        <a href="sign_up_Teacher_system.php" class="nav-item nav-link">Create my course</a>          
                <?php endif; ?>
                <?php if (isset($_SESSION['login'])): ?>
                    <a href="profile.php"><span class="nav-item nav-link"><i class="fa fa-user me-2" ><?php echo htmlspecialchars($_SESSION['login']['username']); ?></span></i></a>
                    <a href="../system/logout.php" class="btn btn-primary py-4 px-lg-5 d-block d-lg-block">Logout<i class="fa fa-arrow-right ms-3"></i></a>
            <?php else: ?>
                <a href="sign_up.php" class="btn btn-primary py-4 px-lg-5 d-block d-lg-block">Join Now<i class="fa fa-arrow-right ms-3"></i></a>
            <?php endif; ?>  
            </div>
        </div>
    </nav>
    <!-- Navbar End -->