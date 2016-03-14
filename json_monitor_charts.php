<?php

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
	
	if ($type == 'ethernet') {

		// 从 config_ethernet 表中获取数据
		$result = $db->getDataFromConfigEthernet();
		
		if ($result) {
            // 读取成功
            while ($row = $result->fetch_array(MYSQLI_NUM)) {
                $response[] = $row;
            }
			echo jsonRemoveUnicodeSequences($response);
		} else {
			// 读取失败
			$response["error"] = TRUE;
			$response["error_msg"] = "数据读取失败";
            
			echo jsonRemoveUnicodeSequences($response);
		}
	} else if ($type == 'hours') {
		// 从 xx 表中获取数据
        echo "[[0, 3.0], [3, 3.9], [6, 2.0], [9, 1.2], [12, 1.3], [15, 2.5], [18, 2.0], [21, 3.1]]";
	}
} else {
		$response["error"] = TRUE;
		$response["error_msg"] = "缺少参数";
		echo jsonRemoveUnicodeSequences($response);
}
?>