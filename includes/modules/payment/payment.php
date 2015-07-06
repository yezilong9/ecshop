<?php
/**
 *新泛联商户通知接口
 *time：2014-04-02
 *name：hg
**/

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

class payment{

	
	private $payment_config = array();
	
	/*配置赋值*/
	function config($payment_config = array())
    {
		
		$this->payment_config = $payment_config;
    }
	

	
	
	/*生成支付代码*/
	function get_code($order)
	{
		//支付方式失效

		if(isset($order['surplus_amount']) && !empty($order['surplus_amount']))
		{
			$this->ext1 = 'rechargeable';//充值
		}
		
		/*支付form参数 */
		$order['order_sn']     = $order['order_sn'];//订单
		$order['order_amount'] = $order['order_amount'];//金额
		$order['pay_num']       = $order['pay_num'];//银行代码
		$order['ext1']       = $this->ext1;//扩展字段1
		$order['ext2']       = $this->ext2;//扩展字段1
		
		
		//载入接口文件
		$pay_file = strtolower('pay_'.$this->payment_config['pay_code']);
		include_once($pay_file.'.php');
		$payObj = new $pay_file();
		return $payObj->pay_from($order,$this->payment_config);
	}

	/*同步响应操作*/
	function respond()
    {
		if(isset($_GET['code']) && !empty($_GET['code']))
		{
			$pay_code = $_GET['code'];
		}
		elseif(isset($_POST['out_trade_no']) && !empty($_POST['out_trade_no']))
		{	
			$pay_code = 'alipay';
		}
		//载入接口文件
		$pay_file = strtolower('pay_'.$pay_code);
		include_once($pay_file.'.php');
		$payObj = new $pay_file();
		return $payObj->pay_verify();
		
   }
   /*异步响应操作*/
   function POST_respond()
   {
		if(isset($_GET['code']) && !empty($_GET['code']))
		{
			$pay_code = $_GET['code'];
		}
		elseif(isset($_POST['out_trade_no']) && !empty($_POST['out_trade_no']))
		{	
			$pay_code = 'alipay';
		}
		
		if(!$pay_code)
			return false;

		//载入接口文件
		$pay_file = strtolower('pay_'.$pay_code);
		
		include_once($pay_file.'.php');
		$payObj = new $pay_file();		
		return $payObj->POST_pay_verify();
   }
	/*通过订单号拿到银行本地代码*/
	function order_num($order_sn)
	{
		$pay_num = $GLOBALS['db']->getOne("select pay_num from ".$GLOBALS['ecs']->table('order_info')." where order_sn=$order_sn");
		if(empty($pay_num))
		{
			$pay_num = $GLOBALS['db']->getOne("select pay_num from ".$GLOBALS['ecs']->table('user_account')." where id=$order_sn");
		}
		
		return $pay_num?current(explode('-',$pay_num)):'';
	}
	
	
	/**
	* 根据设定条件获取支付渠道
	* @$order_id: 订单id
	* @$bank_code: 为页面银行代码
	* @$order_amount: 订单金额
	* @$num: 订单类型 默认为87购物
	*/
	function pay_set($order_id,$bank_code,$order_amount,$num='87')
	{
		$pay_payment = $GLOBALS['db']->getOne("select payment from ".$GLOBALS['ecs']->table('show_bank')." where bank_code='$bank_code'");
		if(empty($pay_payment))
		{
			//订单类型
			$pay_num_id = $this->order_pay($bank_code,$num);
			//金额
			$pay_amount_id = $this->order_amount($bank_code,$order_amount);
			//银行
			$pay_code_id = $this->set_pay($bank_code);

			if(!empty($pay_code_id))
			{
				$pay_id = $pay_code_id;
			}
			elseif(!empty($pay_amount_id))
			{
				$pay_id = $pay_amount_id;
			}
			elseif(!empty($pay_num_id))
			{
				$pay_id = $pay_num_id;
			}
			//获取支付商配置信息
			if(!isset($pay_id))
			{
				show_message('支付方式不存在');
			}
			$where = "pay_id=$pay_id";
		}
		else
		{
			$where = "pay_code='$pay_payment'";
		}		
		$payment = $GLOBALS['db']->getRow("select pay_config,pay_code from ".$GLOBALS['ecs']->table('payment')." where $where and enabled = 1");
		$pay_config = unserialize($payment['pay_config']);
		$pay_config['pay_code'] = $payment['pay_code'];
		$this->config($pay_config);
		//重写订单本地代码
		$pay_num = $payment['pay_code'].'--'.$bank_code;
		
		$bank_name =$GLOBALS['db']->getOne("select bank_name from ".$GLOBALS['ecs']->table('show_bank'). " where bank_code = '$bank_code'");
		
		if($num == '87')
		{
			$GLOBALS['db']->query("update ".$GLOBALS['ecs']->table('order_info')." set pay_num='$pay_num',pay_name='$bank_name' where order_id=$order_id");
		}
		else
		{
			$GLOBALS['db']->query("update ".$GLOBALS['ecs']->table('user_account')." set pay_num='$pay_num' where id=$order_id");
		}
		return $pay_payment?$bank_code:$this->order_pay_num($pay_num);
	}
	
	/*从订单中的银行本地代码获取支付渠道*/
	function order_set($pay_num)
	{
		
		$payment = $this->order_set_and($pay_num);
		$this->config($payment);
	}
	/*获得支付商配置*/
	function order_set_and($pay_num)
	{
		$pay_id = $GLOBALS['db']->getOne("select pay_id,bank_name from ".$GLOBALS['ecs']->table('pay_bank')." where local_bank_code='$pay_num' and enabled = 1" );
		//不是银行支付
		if(empty($pay_id))
		{
			$pay_code = current(explode('--',$pay_num));
			$where = "pay_code='$pay_code'";
		}
		else
		{
			$where = "pay_id=$pay_id";
		}
		$res = $GLOBALS['db']->getRow("select pay_config,pay_code from ".$GLOBALS['ecs']->table('payment')." where $where and enabled = 1");
		if($res['pay_config'])
		{
			$payment = unserialize($res['pay_config']);
			$payment['pay_code'] = $res['pay_code'];
			return $payment;
		}
		
	}

	
	
	
	/*
	* 类型3，直接通过银行代码拿到支付渠道ID
	* 返回支付商ID
	* @$pay_num：银行代码
	*/
	function set_pay($pay_num)
	{
		$pay_set_res = array();
		$pay_set_res = $GLOBALS['db']->getAll("select content from ".$GLOBALS['ecs']->table('pay_set')." where value='$pay_num' and enabled=1");
		
		foreach($pay_set_res as $key=>$value){
			$value['content'];
			$pay_bank = $GLOBALS['db']->getRow("select pay_id from ".$GLOBALS['ecs']->table('pay_bank')." where paybank_id=$value[content] and enabled=1");
			if($pay_bank['pay_id'])
			{
				$pay_id = $pay_bank['pay_id'];
				break;
			}
		}
		if(!empty($pay_id))
		{
			return $pay_id;
		}
		else
		{
			return '';
		}
	}
	
	/**
	* 类型2，通过订单类型拿到支付渠道ID
	* 返回支付商ID
	* 暂时统一87，购物
	* @$pay_numm：银行代码
	* @$num：订单类型 87为购物，88为充值
	**/
	function order_pay($pay_numm,$num)
	{
		//获取支付商ID	
		$pay_set_res = $GLOBALS['db']->getRow("select content from ".$GLOBALS['ecs']->table('pay_set')." where value=$num and enabled=1");
		//dump($pay_set_res);
		if(!empty($pay_set_res['content']))
		{
			$pay_bank_res = $GLOBALS['db']->getRow("select pay_id from ".$GLOBALS['ecs']->table('pay_bank')." where bank_code='$pay_numm' and pay_id=$pay_set_res[content] and enabled=1");
			if($pay_bank_res['pay_id'])
			{
				$pay_id = $pay_bank_res['pay_id'];
			}
			else
			{
				$pay_id ='';
			}
		}
		else
		{
			$pay_id ='';
		}
		return $pay_id;
	}
	
	/**
	* 类型1，通过金额类型拿到支付渠道ID
	* 返回支付商ID
	* @$order_amount : 金额
	* @$pay_numm : 银行代码
	**/
	function order_amount($pay_numm,$order_amount)
	{
		//支付商ID
		$pay_set_res = $GLOBALS['db']->getRow("select content,value from ".$GLOBALS['ecs']->table('pay_set')." where type=1 and enabled=1");
		if(!$pay_set_res)
		{
			return '';
		}
		$static_num =  $order_amount.$pay_set_res['value'];	
		eval("\$str = "."$static_num".";");
		if($str)
		{
			$pay_bank_res = $GLOBALS['db']->getRow("select pay_id from ".$GLOBALS['ecs']->table('pay_bank')." where bank_code='$pay_numm' and pay_id=$pay_set_res[content] and enabled=1");
			if($pay_bank_res['pay_id'])
			{
				$pay_id = $pay_bank_res['pay_id'];
			}
			else
			{
				$pay_id ='';
			}
		}
		else
		{
			$pay_id ='';
		}
		return $pay_id;
	}
	
	/*通过订单中的标示，拿到实际的银行代码*/
	function order_pay_num($order_pay_num = '0')
	{
		$bank_pay_num = $GLOBALS['db']->getOne("select bank_pay_num from ".$GLOBALS['ecs']->table('pay_bank')." where Local_bank_code = '$order_pay_num'");
		$arr_order_pay_num = explode('--',$order_pay_num);
		return $bank_pay_num?$bank_pay_num:$arr_order_pay_num[1];
	}
	
	/**/
}
?>