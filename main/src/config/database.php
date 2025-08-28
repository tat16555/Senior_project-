<?php
// Database configuration
class DBController {
    private $host = "db";
    private $user = "MYSQL_USER";
    private $password = "MYSQL_PASSWORD";
    private $db = "MYSQL_DATABASE";
    private $conn;

    function __construct() {
        $this->conn = $this->connectDB();
    }

    function connectDB() {
        $conn = new mysqli($this->host, $this->user, $this->password, $this->db);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        return $conn;
    }
    public function getConn() {
        return $this->conn;
    }
}

?>
