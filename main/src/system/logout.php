<?php
session_start();
if (isset($_SESSION['login'])) {
    session_destroy();
    $_SESSION['success'] = "Logout completed!";
    header("location: ../index.php");
}
header("location: ../index.php");
?>