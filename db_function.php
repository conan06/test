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
     * Get data from table
     */
    public function getData($query) {
        
        $mysqli = $this->conn;
        
        if($mysqli->connect_error) {
            die("$mysqli->connect_errno: $mysqli->connect_error");
        }
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
    
    /**
     * Get data from config_ethernet table
     */
    public function getDataFromConfigEthernet() {
        
        $query = "SELECT * FROM config_ethernet";
        return self::getData($query);
    }
    
    /**
     * Get hourly transfer data in this day from monitor_history table
     */
    public function getTodayHourlyTransfer() {
        
        $query = "SELECT HOUR( historyTimeDone ) AS hours, COUNT( HOUR( historyTimeDone ) ) AS sum
                FROM `monitor_history` 
                WHERE DATE( historyTimeDone ) = CURDATE( ) 
                GROUP BY HOUR( historyTimeDone ) ";
        return self::getData($query);
    }
    
    /**
     * Get hourly transfer data all the time from monitor_history table
     */
    public function getAllHourlyTransfer() {
        
        $query = "SELECT HOUR( historyTimeDone ) AS hours, COUNT( HOUR( historyTimeDone ) ) AS sum
                FROM `monitor_history` 
                GROUP BY HOUR( historyTimeDone ) ";
        return self::getData($query);
    }
    
    /**
     * Get last week transfer data from monitor_history table
     */
    public function getLastWeekTransfer() {
        
        $query = "SELECT DATE( historyTimeDone ) AS days, COUNT( DATE( historyTimeDone ) ) AS sum 
                FROM `monitor_history` 
                WHERE DATE_SUB( CURDATE( ) , INTERVAL 7 DAY ) < DATE( historyTimeDone ) 
                GROUP BY DATE( historyTimeDone ) 
                ORDER BY historyTimeDone DESC";
        return self::getData($query);
    }
    
    /**
     * Get each year transfer data from monitor_history table
     */
    public function getEachYearTransfer($arg) {
        
        $query = "SELECT MONTH( historyTimeDone ) AS months, COUNT( MONTH( historyTimeDone ) ) AS sum
                FROM `monitor_history` 
                WHERE YEAR( historyTimeDone ) =  ".$arg."
                GROUP BY MONTH( historyTimeDone ) ";
        return self::getData($query);
    }
    
    /**
     * Get filename extension from monitor_history table
     */
    public function getFilenameExtension() {
        
        $query = "SELECT SUBSTRING_INDEX(historyFileName, '.', -1) AS extension, COUNT( SUBSTRING_INDEX(historyFileName, '.', -1) ) AS sum
                FROM `monitor_history`
                GROUP BY SUBSTRING_INDEX(historyFileName, '.', -1) 
                ORDER BY SUBSTRING_INDEX(historyFileName, '.', -1) ASC";
        return self::getData($query);
    }
}
?>
