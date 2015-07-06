<?php
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');


$obj = new financial();
$obj->operation();//会员
$obj->goods_data();//订单
//$obj->return_order();

/**
* 获取财务统计数据
**/
class financial{

	
	/**
	* 获取用户资金
	* @$res 代理商user_id字符串 
	* @$select 查询普通会员为1，查询代理商为2 ,默认查普通会员
	**/
	public function user_money($res,$select='1')
	{
		$not = $select == '1'?'not':'';
		$row = $GLOBALS['db']->getAll("select user_money,frozen_money from ".$GLOBALS['ecs']->table('users')." where user_id $not in(".$res.")");
		$arr['user_money']   = '';
		$arr['frozen_money'] = '';
		foreach($row as $key=>$value){
			$arr['user_money']   += $value['user_money'];
			$arr['frozen_money'] += $value['frozen_money'];
		}
		return $arr;
	}
	
	/**
	* 写入用户资金表
	* @$arr 用户资金 
	* @$type 普通会员为1，代理商随意数字 
	**/
	public function insert_financial($arr,$type='1')
	{
		$type = $type == '1'?'1':'2';
		$arr['type'] = $type;
		$arr['data_time'] =  strtotime("-1 day");
		$arr['add_time']  =  time();
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('financial_money'), $arr, 'INSERT');
	}
	public function operation()
	{
		if($this->check_data('1'))
		{
			return false;exit;
		}
		$res = agency_list('1');
		$res = implode(',',$res);
		$this->insert_financial($this->user_money($res,'2'),'2');
		$this->insert_financial($this->user_money($res,'1'),'1');
	}

	/**
	* 统计每天订单销售数量利润
	* 
	*
	**/
	public function goods_data()
	{
		$time = strtotime("-1 day");
		$str_time = date('Ymd',$time);
		$start_time = strtotime($str_time.'000000');
		$end_time = strtotime($str_time.'23.59.59');
		//dump($start_time);
		$sql = 'SELECT og.goods_id, og.costing_price,og.goods_sn, og.goods_name, og.goods_number AS goods_num, og.goods_price '.
		'AS sales_price, oi.add_time AS sales_time,oi.pay_time, oi.order_id, oi.order_sn '.
		"FROM " . $GLOBALS['ecs']->table('order_goods')." AS og, ".$GLOBALS['ecs']->table('order_info')." AS oi WHERE og.order_id = oi.order_id AND oi.order_status  IN ('1','5')  AND oi.shipping_status <> '4'  AND oi.pay_status  IN ('2','1') AND oi.pay_time > '$start_time' AND oi.pay_time < '$end_time' ORDER BY sales_time DESC, goods_num DESC";
		$data = $GLOBALS['db']->getAll($sql);
		//退货或取消订单
		$return_order = $this->return_order();
		//echo '<pre>';
		//print_r($data);
		//dump($return_order);
		//统计数据
		/*检查是否已经插入数据*/
 		if($this->check_data('2'))
		{
			return false;exit;
		} 
		$goods_data = array();
		foreach($data as $key=>$value){
			$goods_data['market_number']     += $value['goods_num'];
			$goods_data['costing_price']     += $value['costing_price']*$value['goods_num'];
			$goods_data['goods_price']       += $value['sales_price']*$value['goods_num'];
		}
		$goods_data['data_time'] = $time;
		$goods_data['add_time']  = time();
		$goods_data['profit_price']      = $goods_data['goods_price']-$goods_data['costing_price'];
		//减去退货或取消数量
		if(isset($return_order['goods_data']) && !empty($return_order['goods_data']))
		{
			$goods_data['market_number'] = $goods_data['market_number']-$return_order['goods_data']['market_number'];
			$goods_data['costing_price'] = $goods_data['costing_price']-$return_order['goods_data']['costing_price'];
			$goods_data['goods_price']   = $goods_data['goods_price']-$return_order['goods_data']['goods_price'];
			$goods_data['profit_price']  = $goods_data['profit_price']-$return_order['goods_data']['profit_price'];
		}
		$this->insert_data($goods_data,'1');
		
		//每天的数据
		$date_data = array();
		//dump($data);
		foreach($data as $k=>$v){
			$date_data[$v['goods_id']]['market_number'] += $v['goods_num']; 
			$date_data[$v['goods_id']]['costing_price'] += $v['costing_price']*$v['goods_num'];
			$date_data[$v['goods_id']]['goods_price']   += $v['sales_price']*$v['goods_num'];
			$date_data[$v['goods_id']]['profit_price']  += ($v['sales_price']*$v['goods_num'])-($v['costing_price']*$v['goods_num']);
			$date_data[$v['goods_id']]['goods_name']    = $v['goods_name']; 
			$date_data[$v['goods_id']]['data_time']          = $time; 
			$date_data[$v['goods_id']]['add_time']          = time(); 
		}
		//dump($date_data);
		foreach($date_data as $date_k=>$date_v){
			if($return_order['date_data'][$date_k])
			{
				$date_data[$date_k]['market_number'] = $date_data[$date_k]['market_number']+$return_order['date_data'][$date_k]['market_number'];
				$date_data[$date_k]['costing_price'] = $date_data[$date_k]['costing_price']+$return_order['date_data'][$date_k]['costing_price'];
				$date_data[$date_k]['goods_price']   = $date_data[$date_k]['goods_price']+$return_order['date_data'][$date_k]['goods_price'];
				$date_data[$date_k]['profit_price'] = $date_data[$date_k]['profit_price']+$return_order['date_data'][$date_k]['profit_price'];
				unset($return_order['date_data'][$date_k]);
			}
		}
		if($return_order['date_data'])
		{
			$date_data = array_merge($date_data,$return_order['date_data']);
		}
		$this->insert_data($date_data,'2');
	}
	
	/**
	* 插入销售概况表
	* @$data 需要插入的数据
	* @$type 1是每天的统计数据，2是每天新增的数据
	**/
	public function insert_data($data,$type)
	{
		$type = $type == '1'?'1':'2';
		if($type == '1')
		{
			$data['type'] = $type;
			$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('goods_profit'), $data, 'INSERT');
		}
		else
		{
			foreach($data as $key=>$value){
				$value['type'] = $type;
				$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('goods_profit'), $value, 'INSERT');
			}
		}
	}
	
	/**
	* 检查是否已经插入数据
	* @$time 根据时间检查
	* @$type 1是检查会员金额，2是检查订单数据
	**/
	public function check_data($type)
	{
		$table = $type=='1'?'financial_money':'goods_profit';
		$time = date('Ymd',strtotime("-1 day"));
		$data_time = $GLOBALS['db']->getOne("select data_time from ".$GLOBALS['ecs']->table($table)." ORDER BY id desc limit 1");
		if(!$data_time)
		{
			return false;
		}
		$data_time = date('Ymd',$data_time);
		if($time == $data_time)
		{
			return true;
		}else{
			return false;
		}
	}

	/**
	* 获取退货或者取消订单信息
	*
	**/
	public function return_order()
	{
		$time = strtotime("-1 day");
		$str_time = date('Ymd',$time);
		$start_time = strtotime($str_time.'000000');
		$end_time = strtotime($str_time.'23.59.59');
		//操作取消和退货的订单号
		$res = $GLOBALS['db']->getAll("select order_id from ".$GLOBALS['ecs']->table('order_action')." where log_time > '$start_time' AND log_time < '$end_time' AND order_status IN ('2','4')");
		if(!$res)
		{
			return false;
		}
		$order_id_arr = array();
		//是够增加支付过
		foreach($res as $key=>$value){
			$res = '';
			$res = $GLOBALS['db']->getAll("select order_id from ".$GLOBALS['ecs']->table('order_action')." where order_id=$value[order_id] AND pay_status='2' AND log_time < '$start_time'");
			if($res)
			{
				$order_id_arr[] = $value['order_id'];
			}
		}
		$order_id_sting = implode(',',$order_id_arr);
		$sql = 'SELECT og.goods_id, og.costing_price,og.goods_sn, og.goods_name, og.goods_number AS goods_num, og.goods_price '.
		'AS sales_price, oi.add_time AS sales_time,oi.pay_time, oi.order_id, oi.order_sn '.
		"FROM " . $GLOBALS['ecs']->table('order_goods')." AS og, ".$GLOBALS['ecs']->table('order_info')." AS oi WHERE og.order_id = oi.order_id AND oi.order_id in ($order_id_sting)";
		$data = $GLOBALS['db']->getAll($sql);
		if(!$data)
		{
			return false;
		}
		//统计数据
		$goods_data = array();
		foreach($data as $key=>$value){
			$goods_data['market_number']     += $value['goods_num'];
			$goods_data['costing_price']     += $value['costing_price']*$value['goods_num'];
			$goods_data['goods_price']       += $value['sales_price']*$value['goods_num'];
		}
		$goods_data['data_time'] = $time;
		$goods_data['add_time']  = time();
		$goods_data['profit_price']      = $goods_data['goods_price']-$goods_data['costing_price'];
		//每天的数据
		$date_data = array();
		foreach($data as $k=>$v){
			$date_data[$v['goods_id']]['market_number'] += '-'.$v['goods_num']; 
			$date_data[$v['goods_id']]['costing_price'] += '-'.$v['costing_price']*$v['goods_num'];
			$date_data[$v['goods_id']]['goods_price']   += '-'.$v['sales_price']*$v['goods_num'];
			$date_data[$v['goods_id']]['profit_price']  += '-'.(($v['sales_price']*$v['goods_num'])-($v['costing_price']*$v['goods_num']));
			$date_data[$v['goods_id']]['goods_name']    = $v['goods_name']; 
			$date_data[$v['goods_id']]['data_time']          = $time; 
			$date_data[$v['goods_id']]['add_time']          = time(); 
		}
		return array('goods_data'=>$goods_data,'date_data'=>$date_data);
	}

}
?>