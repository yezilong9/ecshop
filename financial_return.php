<?php
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');

$date = isset($_GET['date'])?$_GET['date']:'';
$sign = isset($_GET['sign'])?$_GET['sign']:'';
$md5_key = 'eEkhIC6jR9QDnsqs27t6Wwrufkt3Q5bE';
if($sign && MD5($date.$md5_key))
{
	$obj = new financial_return();
	echo json_encode($obj->select_data($date));
}

class financial_return{


	public function select_data($time)
	{
		if(!$time)
			return false;
		$timeArr = $this->time_scope($time);
		$res = $GLOBALS['db']->getAll("select * from ".$GLOBALS['ecs']->table('goods_profit')." where data_time > $timeArr[start_time] AND data_time < $timeArr[end_time] AND type = '2'");
		$arr = array();
		foreach($res as $key=>$value)
		{
			$arr[$key]['V_PROD_NAME'] = $value['goods_name'];
			$arr[$key]['V_NUM']       = $value['market_number'];
			$arr[$key]['V_AMOUNT']    = $value['goods_price'];
			$arr[$key]['V_COST']      = $value['costing_price'];
			$arr[$key]['V_PROFIT']    = $value['profit_price'];
		}
		return $arr;
	
	}
	public function time_scope($time)
	{
		$time = strtotime($time);
		$str_time = date('Ymd',$time);
		$start_time = strtotime($str_time.'000000');
		$end_time = strtotime($str_time.'23.59.59');
		return array('start_time'=>$start_time,'end_time'=>$end_time);
	}
}

?>