<?php

class Connection {

    public PDO $conn;

    public function __construct() {

        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "umuco_db";
        $port = "3307";
        $charset = "utf8mb4";

        $dsn = "mysql:host=$servername;port=$port;dbname=$dbname;charset=$charset";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // throw exceptions instead of silent failures
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,                  // use real prepared statements
        ];

        // Create connection
        try {
            $this->conn = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            error_log("Connection failed: " . $e->getMessage());
            die("Connection failed. Please try again later.");
        }

    }
}

?>