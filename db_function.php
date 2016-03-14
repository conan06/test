<?php
class DB_Functions {
    private $conn;
    // constructor
    function __construct() {
        require_once 'db_connect.php';
        // connecting to database
        $db = new Db_Connect();
        $this->conn = $db->connect();
    }
    // destructor
    function __destruct() {
    }

    /**
     * Encrypting password
     * @param password
     * returns encrypted password
     */
    public function hashSSHA($password) {

        $encrypted = md5($password);
        $hash = array("encrypted" => $encrypted);
        return $hash;
    }

    /**
     * Decrypting password
     * @param password
     * returns hash string
     */
    public function checkhashSSHA($password) {

        $hash = md5($password);
        return $hash;
    }
	
	/**
     * Get data from config_ethernet table
     */
    public function getDataFromConfigEthernet() {
        $mysqli = $this->conn;
        if($mysqli->connect_error) {
            die("$mysqli->connect_errno: $mysqli->connect_error");
        }
        $query = "SELECT * FROM config_ethernet";
        $stmt = $mysqli->stmt_init();
        if(!$stmt->prepare($query)) {
            print "Failed to prepare statement\n";
        } else {
            $stmt->execute();
            $result = $stmt->get_result();
		}
        $stmt->close();
        $mysqli->close();
        if ($result) {
            return $result;
        } else {
            return NULL;
        }
    }
}
?>
