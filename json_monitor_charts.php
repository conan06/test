<?php

date_default_timezone_set('PRC'); // 设置时区为GMT+8

require_once 'db_function.php';
$db = new DB_Functions();

// Input method (use $_GET, $_POST or $_REQUEST)
$input =& $_GET;

// json response array
$response = array();

// 因 json_encode($response) 缺少对中文的支持，因此需要做修改
function jsonRemoveUnicodeSequences($struct) {
	return preg_replace("/\\\\u([a-f0-9]{4})/e", "iconv('UCS-4LE','UTF-8',pack('V', hexdec('U$1')))", json_encode($struct));
}

// 网口配置（ethernet）
if (isset($input['type']) && $input['type'] != '') {
	
	// 获取参数
    $type = $input['type'];
	
	if ($type == 'last24hrs') {
		
        $result = $db->getWithinDayTransfer();
		
		if ($result) {
            // 读取成功
            while ($row = $result->fetch_array(MYSQLI_NUM)) {
                // $data[0] = strtotime($today) * 1000; // 将10位时间戳扩展成兼容JS的13位;
                $response[] = $row;
            }
			echo jsonRemoveUnicodeSequences($response);
		} else {
			// 读取失败
			$response["error"] = TRUE;
			$response["error_msg"] = "数据读取失败";
            
			echo jsonRemoveUnicodeSequences($response);
		}
	} else if ($type == 'lastweek') {

		$result = $db->getLastWeekTransfer();
		
		if ($result) {
            // 读取成功
            $today = date('Y-m-d');
            $data = array();
            $row = $result->fetch_array(MYSQLI_NUM);
            
            for ($i = 0; $i <= 6; $i++) {
                $today = date('Y-m-d', strtotime('-'.$i.' day')); // 在today的基础上减一天
                $data[0] = $today;
                $data[0] = strtotime($today) * 1000;
                
                // if (!strcmp($row[0],$today)) {
                if (strtotime($row[0]) * 1000 == $data[0]) {
                    $data[1] = $row[1];
                    $row = $result->fetch_array(MYSQLI_NUM);
                } else {
                    $data[1] = 0;
                }
                $response[] = $data;
            }
			echo jsonRemoveUnicodeSequences($response);
		} else {
			// 读取失败
			$response["error"] = TRUE;
			$response["error_msg"] = "数据读取失败";
            
			echo jsonRemoveUnicodeSequences($response);
		}
    } else if($type == 'thisyear') {
        
    }
} else {
		$response["error"] = TRUE;
		$response["error_msg"] = "缺少参数";
		echo jsonRemoveUnicodeSequences($response);
}
?>