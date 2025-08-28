<?php
require '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = $_POST['code'];
    $subject = $_POST['subject'];
    $chapter = $_POST['chapter'];
    $file = $_FILES['file']['tmp_name'];
    $fileName = $_FILES['file']['name'];
    
    // Validate file type
    if (pathinfo($fileName, PATHINFO_EXTENSION) != 'md') {
        die('Invalid file type. Please upload a .md file.');
    }

    // Read file content
    $content = file_get_contents($file);

    // Connect to database
    $db = new DBController();
    $conn = $db->getConn();

    if (!$conn) {
        die("Database connection failed: " . $conn->connect_error);
    }

    // Prepare SQL query
    $stmt = $conn->prepare("INSERT INTO Course (code, subject, chapter, content) VALUES (?, ?, ?, ?)");

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind parameters and execute
    if (!$stmt->bind_param("ssis", $code, $subject, $chapter, $content)) {
        die("Bind failed: " . $stmt->error);
    }

    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }


    // Save the file locally
    $savePath = "articles/" . basename($fileName);
    if (move_uploaded_file($file, $savePath)) {
        echo "Article saved successfully!";
    } else {
        echo "Failed to save file locally.";
    }

    // Close connections
    $stmt->close();
    $conn->close();
}
?>
