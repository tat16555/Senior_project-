<?php
require '../config/database.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $id = 4;
    $db = new DBController();
    $conn = $db->getConn();

    if (!$conn) {
        die("Database connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT content FROM Course WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($content);
        if ($stmt->fetch()) {
            echo $content;
        } else {
            echo "Article not found.";
        }
        $stmt->close();
    } else {
        echo "Failed to prepare statement: " . $conn->error;
    }

    $conn->close();
} else {
    echo "No article ID provided.";
}
?>
