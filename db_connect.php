<?php
class DB_Connect {
    private $conn;

    // Connecting to database
    public function connect() {
        require_once 'db_config.php';

        // Connecting to mysql database
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
		$this->conn->set_charset("utf8");

        // return database handler
        return $this->conn;
    }
}

?>
