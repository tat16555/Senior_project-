<?php
require '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = $_POST['code'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $course_designer = $_POST['Course_designer'];

    // Connect to database
    $db = new DBController();
    $conn = $db->getConn();

    if (!$conn) {
        die("Database connection failed: " . $conn->connect_error);
    }

    // Prepare SQL query
    $stmt = $conn->prepare("INSERT INTO Course_details (code, name, description, Course_designer) VALUES (?, ?, ?, ?)");

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind parameters and execute
    if (!$stmt->bind_param("ssss", $code, $name, $description, $course_designer)) {
        die("Bind failed: " . $stmt->error);
    }

    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }

    echo "Article saved successfully!";

    // Close connections
    $stmt->close();
    $conn->close();
}
?>
