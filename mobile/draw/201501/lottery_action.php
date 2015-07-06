<?php
include_once("setting_lotter.php");
$parm_lotter = date('Y-m-d H-i-s').'抽奖参数'.print_r($_REQUEST,true).'\n';
$file_name = "draw.txt";
error_log($parm_lotter,3,$file_name);
$bln_is_error = false;
$bln_switch   = true;
$ak = array
(
    'int_lottery_result'=> KA_LOTTERY_AWARD_3
    ,'int_lottery_stat'	=> 1
    ,'int_lottery_win'   => KA_LOTTERY_STATUS_YES
    ,'award' => ''
    , 'msg' => '差一点点运气,欢迎下次再来!'
); 

echo $value = json_encode($ak);
exit;
//echo "<pre>";
//print_r($arr_pract_rs);
//echo "</pre>";
//是否中奖
//$bln_is_win =  rand( [int min, int max] );
?>