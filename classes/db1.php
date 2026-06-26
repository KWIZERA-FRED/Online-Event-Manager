<?php

class Connection {

    public $conn;

    public function __construct() {

        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "umuco_db";

        // Create connection
        $this->conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }

    }
}

?>