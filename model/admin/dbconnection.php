<?php
    class dbcon {
        private $servername = "localhost";
        private $username = "root";
        private $password = "";
        private $dbname = "barangay_db";
        protected $conn;

        // Constructor to initialize the database connection
        public function __construct($servername = null, $username = null, $password = null, $dbname = null) {
            // Allow optional parameters to override the default values
            $this->servername = $servername ?? $this->servername;
            $this->username = $username ?? $this->username;
            $this->password = $password ?? $this->password;
            $this->dbname = $dbname ?? $this->dbname;

            // Establish the connection
            $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);

            // Check for connection errors
            if ($this->conn->connect_error) {
                die("Connection failed: " . $this->conn->connect_error);
            }
        }

        // Function to get the connection
        public function getConnection() {
            return $this->conn;
        }

    
    }
?>
