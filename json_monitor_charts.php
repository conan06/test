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

if (isset($input['type']) && $input['type'] != '') {
    
    // 获取参数
    $type = $input['type'];
    
    switch($type){
    
    case 'todayhrs':                                            // 当天每时段传输数据
        
        $result = $db->getTodayHourlyTransfer();
        $todayHourly = getHourlyData("today", $result);
        
        $db = new DB_Functions();                               // 再次初始化
        $result = $db->getAllHourlyTransfer();
        $allHourly = getHourlyData("all", $result);

        $response = array_merge($todayHourly, $allHourly);      // 合并两个数组

        echo jsonRemoveUnicodeSequences($response);
        
        break;

    case 'lastweek':
        
        $concrete = array();
        $average = array();
        $result = $db->getLastWeekTransfer();
        
        if ($result) {                                              // 读取成功
            
            $today = date('Y-m-d');
            $data = array();
            $totality = 0;
            $row = $result->fetch_array(MYSQLI_NUM);                // 获取第一行数据
            
            for ($i = 0; $i <= 6; $i++) {
                
                $today = date('Y-m-d', strtotime('-'.$i.' day'));   // 在today的基础上减一天
                $data[0] = strtotime($today) * 1000;
                
                // if (!strcmp($row[0],$today)) {                   // 字符串匹配
                if (strtotime($row[0]) * 1000 == $data[0]) {        // 时间戳匹配
                    
                    $data[1] = $row[1] * !is_null($row);            // 如果这行数据中有该日期，则赋值；若为空，则置0
                    $totality += $row[1] * !is_null($row);          // 累加总数；若为空，则置0
                    $row = $result->fetch_array(MYSQLI_NUM);        // 读取下一行数据
                    
                } else {
                    
                    $data[1] = 0;
                    
                }
                
                array_unshift($concrete, $data);                    // 将数据由头插入concrete数组，使时间按递增顺序

            }
            
            $totality /= 7;                                         // 计算平均值
                
            for ($i = 0; $i < 2; $i++) {
                
                $data[0] = strtotime($today) * 1000;
                $data[1] = round($totality, 2);
                $today = date('Y-m-d');
                $average[] = $data;                                 // 尾插法将均值插入数组average中
                
            }
            
            $response["concrete"] = $concrete;
            $response["average"] = $average;

        } else {                                                    // 读取失败
            
            $response["error"] = TRUE;
            $response["error_msg"] = "数据读取失败";
            
        }
        echo jsonRemoveUnicodeSequences($response);
        
        break;
        
    case 'thisyear':
        
        $year = date('Y');                                      // 获取年份
        $result = $db->getEachYearTransfer($year);
        $thisYear = getMonthlyData($result);
        
        $db = new DB_Functions();                               // 再次初始化
        $result = $db->getEachYearTransfer($year - 1);
        $lastYear = getMonthlyData($result);

        $response["thisyear"] = $thisYear;
        $response["lastyear"] = $lastYear;

        echo jsonRemoveUnicodeSequences($response);
        
        break;
        
    case 'test':
        
        $result = $db->getThisYearTransfer();
        
        if ($result) {                                              // 读取成功
            
            while ($row = $result->fetch_array(MYSQLI_NUM)) {
                
                $response[] = $row;
            }
            echo jsonRemoveUnicodeSequences($response);
        } else {                                                    // 读取失败
            
            $response["error"] = TRUE;
            $response["error_msg"] = "数据读取失败";
            
            echo jsonRemoveUnicodeSequences($response);
        }
        
        break;
    
    default:
        
        $response["error"] = TRUE;
        $response["error_msg"] = "不存在该参数";
        echo jsonRemoveUnicodeSequences($response);
        
        break;
    }
} else {
        $response["error"] = TRUE;
        $response["error_msg"] = "缺少数据参数";
        echo jsonRemoveUnicodeSequences($response);
}

/**
 * Get hourly data
 */
function getHourlyData($str, $result) {
    
    if ($result) {                                          // 读取成功
            
        $data = array();
        $hourly = array();
        $concrete = array();
        $average = array();
        $totality = 0;
        $row = $result->fetch_array(MYSQLI_NUM);
        
        for ($i = 0; $i <= 24; $i++) {
            
            $data[0] = $i * 60 * 60 * 1000;
            
            // if (!strcmp($row[0],$i)) {                   // 字符串匹配
            if ($row[0] * 60 * 60 * 1000 == $data[0]) {     // 时间戳匹配
                
                $data[1] = $row[1] * !is_null($row);        // 如果这行数据中有该日期，则赋值；若为空，则置0
                $totality += $row[1] * !is_null($row);      // 累加总数；若为空，则置0
                $row = $result->fetch_array(MYSQLI_NUM);    // 读取下一行数据
                
            } else {
                
                $data[1] = 0;
                
            }
            
            $concrete[] = $data;                            // 将数据由头插入数组，使时间按递增顺序
        }
       
        $concrete[24][1] = $concrete[0][1];
        
        $totality /= 24;                                    // 计算平均值
                
        for ($i = 0; $i <= 24; $i+=24) {
                
            $data[0] = $i * 60 * 60 * 1000;
            $data[1] = round($totality, 2);
            $average[] = $data;                             // 尾插法将均值插入数组average中
                
        }
        
        $hourly[$str] = $concrete;
        $hourly[''.$str.'_average'] = $average;
        return $hourly;
            
    } else {
        // 读取失败
        $response["error"] = TRUE;
        $response["error_msg"] = "数据读取失败";
            
        echo jsonRemoveUnicodeSequences($response);
    }
}

/**
 * Get hourly data
 */
function getMonthlyData($result) {
    
    if ($result) {                                                  // 读取成功
            
            $data = array();
            $row = $result->fetch_array(MYSQLI_NUM);                    // 获取第一行数据
            
            for ($i = 1; $i <= 12; $i++) {
                
                $data[0] = $i;
                
                if (!strcmp($row[0],$data[0])) {                        // 字符串（月份）匹配
                    
                    $data[1] = $row[1] * !is_null($row);                // 如果这行数据中有该日期，则赋值；若为空，则置0
                    $row = $result->fetch_array(MYSQLI_NUM);            // 读取下一行数据
                    
                } else {
                    
                    $data[1] = 0;
                    
                }
                
                $response[] = $data;

            }
            
            return $response;
            
        } else {                                                        // 读取失败
            
            $response["error"] = TRUE;
            $response["error_msg"] = "数据读取失败";
            
            echo jsonRemoveUnicodeSequences($response);
        }
}
?>