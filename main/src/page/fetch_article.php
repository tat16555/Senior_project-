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
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <!-- Navbar Start -->
    <?php require_once("views/Navbar.php"); ?>
    <!-- Navbar End -->
    <div class="sidebar">
    <div class="container py-5">
    <?php
require '../config/database.php';

if (isset($_GET['code']) && isset($_GET['chapter'])) {
    $code = $_GET['code'];
    $chapter = $_GET['chapter'];

    $db = new DBController();
    $conn = $db->getConn();

    if (!$conn) {
        die("Database connection failed: " . $conn->connect_error);
    }

    // Use placeholders for both variables
    $stmt = $conn->prepare("SELECT content FROM Course WHERE code = ? AND chapter = ?");
    if ($stmt) {
        // Adjust bind_param types according to your database schema
        $stmt->bind_param("si", $code, $chapter);
        $stmt->execute();
        $stmt->bind_result($content);
        if ($stmt->fetch()) {
            // Convert Markdown to HTML
            $apiUrl = 'https://api.github.com/markdown';
            $postData = json_encode(array('text' => $content, 'mode' => 'gfm'));
            $ch = curl_init($apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'User-Agent: PHP'
            ));
            $htmlContent = curl_exec($ch);

            if (curl_errno($ch)) {
                echo 'Curl error: ' . curl_error($ch);
            } else {
                // Display HTML content
                echo $htmlContent;
            }

            curl_close($ch);
        } else {
            echo "Article not found.";
        }
        $stmt->close();
    } else {
        echo "Failed to prepare statement: " . $conn->error;
    }

    $conn->close();
} else {
    echo "No code or chapter specified.";
}
?>

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
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <!-- Template Javascript -->
    <script src="../assets/js/main.js"></script>
    
</body>

</html>