<?php
/**
* 新泛联支付接口
* 
* time ：2014-05-09
**/
if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}


class pay_untx{

	private $version = '2.0.1';//版本号
	/**
	* 生成提交表单
	* @$pay_arr 表单参数
	**/
	function pay_from($pay_arr,$payment_config)
	{
		$mdkey  = $this->md5_encode($pay_arr,$payment_config);
		
		$def_url = "<div style='text-align:center'><form name='cp_frm' method='POST' action='".$payment_config['cfg_gate']."' target='_blank' onsubmit='subpay()'>";
		$def_url .="<input type='hidden' name='origin' value='".$payment_config['cfg_value']."'>";								 //商户名
		$def_url .="<input type='hidden' name='act' value='culr'>";								 				
		$def_url .="<input type='hidden' name='version' value='".$this->version."'>";							//版本号
		$def_url .="<input type='hidden' name='validate' value='".$mdkey."'>";									//md5效验码
		$def_url .="<input type='hidden' name='orderid' size='20' maxlength='30' value='".$pay_arr['order_sn']."'>";//订单号
		$def_url .="<input type='hidden' name='chargemoney' value='".$pay_arr['order_amount']."'>";				//订单金额
		$def_url .="<input type='hidden' name='channelid' value='1'>";											//网银支付
		$def_url .="<input type='hidden' name='fronturl' value='".return_url(basename(__FILE__, '.php'))."'>";//后台地址
		$def_url .="<input type='hidden' name='bgurl' value='".BG_return_url(basename(__FILE__, '.php'))."'>";//前台地址
		$def_url .="<input type='hidden' name='paytype' value='1'>";										//储值卡
		$def_url .="<input type='hidden' name='cardno' value=''>";											//充值卡卡号
		$def_url .="<input type='hidden' name='cardpwd' value=''>";											//充值卡密码
		$def_url .="<input type='hidden' name='cardamount' value=''>";										//卡面值
		$def_url .="<input type='hidden' name='bankcode' value='".$pay_arr['pay_num']."'>";						//银行代码
		$def_url .="<input type='hidden' name='ext1' value='".$pay_arr['ext1']."'>";									//扩展字段一
		$def_url .="<input type='hidden' name='ext2' value='".$pay_arr['ext2']."'>";									//扩展字段二
		$def_url .= "<input type='submit' name='submit' value='前去支付' id='mitpay'/>";
		$def_url .="</form></div></br>";
		
		return $def_url;
	}
	
	/**
	* 同步验证返回信息
	*
	**/
	function pay_verify()
	{
		$respondArr = array();
        $respondArr['orderid'] 	 =$_POST['orderid'];		//订单号
        $respondArr['channelid'] = $_POST['channelid']; 	//支付渠道 ，（网银）
		$respondArr['systemno']  = $_POST['systemno'];		//新泛联订单号
		$respondArr['payprice']  = $_POST['payprice'];		//充值金额
		$respondArr['status']    = $_POST['status'];			//支付状态
		$respondArr['ext1']      = $_POST['ext1'];				//扩充字段1,原样返回
		$respondArr['ext2']      = $_POST['ext2'];				//扩充字段2,原样返回
		$respondArr['validate']  = $_POST['validate'];		//MD5密码
		
		$respondArr['key'] = $this->order_num($respondArr['orderid']);
		$this->untx_pay_log($respondArr,1);
		$md5_respond = $this->md5_respond($respondArr);
		//dump($respondArr['orderid']);
		if($_SESSION['pay_sn'] == $respondArr['orderid'])
		{
			header('location:flow.php?step=done');exit;
		}
		//dump($_SESSION['pay_sn']);
		if($respondArr['validate'] == $md5_respond)
		{
			
			if($respondArr['status'] == '1')
			{
				if($respondArr['ext1'] == 'rechargeable')
				{
					$v_oid = get_order_id_by_sn($respondArr['orderid'],'true');
				}
				else
				{
					$v_oid = get_order_id_by_sn($respondArr['orderid']);
				}
				order_paid($v_oid);
				return $respondArr;
			}
			
		}else{
			return false;
		}
	}

	/*异步验证返回信息*/
	function POST_pay_verify()
	{
		if(!isset($_POST) || empty($_POST))
			return false;
		$arr = array();
		$arr['orderid']    = $_POST['orderid'];  //订单号
		$arr['chargemoney'] = $_POST['chargemoney']; //金额
		$arr['systemno']    = $_POST['systemno']; //新泛联订单号
		$arr['channelid']   = $_POST['channelid']; //支付渠道
		$arr['status']      = $_POST['status']; //支付状态
		$arr['ext1']        = $_POST['ext1']; 
		$arr['ext2']        = $_POST['ext2']; 
		$arr['validate']        = $_POST['validate']; //md5 值
		$arr['key'] = $this->order_num($arr['orderid']);
		$this->untx_pay_log($arr,2);
		$md5_respond = $this->POST_md5_respond($arr);
		//dump($md5_respond);
		if($arr['validate'] == $md5_respond)
		{
			if($arr['status'] == '1')
			{
				if($arr['ext1'] == 'rechargeable')
				{
					$v_oid = get_order_id_by_sn($arr['orderid'],'true');
				}
				else
				{
					$v_oid = get_order_id_by_sn($arr['orderid']);
				}
				//检查金额
				if (!check_money($v_oid, $_POST['chargemoney']))
				{
					return '0';exit;
				}
				//返回成功信息
				order_paid($v_oid);
				return '1';
			}	
		}else{
			return '0';
		}
	}
	/*生成md5效验码*/
	function md5_encode($mdOeder,$payment_config)
	{
      	$sign = 'orderid='.$mdOeder['order_sn'].'&origin='.$payment_config['cfg_value'].'&chargemoney='.$mdOeder['order_amount'].'&channelid=1&paytype=1&bankcode='.$mdOeder['pay_num'].'&cardno=&cardpwd=&cardamount=&fronturl='.return_url(basename(__FILE__, '.php')).'&bgurl='.BG_return_url(basename(__FILE__, '.php')).'&ext1='.$mdOeder['ext1'].'&ext2='.$mdOeder['ext2'];
		
        if($payment_config['cfg_key']){
           $sign = substr(md5($sign.$payment_config['cfg_key']),8,16); //md5 16加密
        }else{
           $sign = md5($sign);
        }
		
    	return $sign;
	}
	
	
	/*根据订单拿支付商key*/
	function order_num($order_sn)
	{
		$pay_num = $GLOBALS['db']->getOne("select pay_num from ".$GLOBALS['ecs']->table('order_info')." where order_sn='$order_sn'");
		if(empty($pay_num))
		{
			$pay_num = $GLOBALS['db']->getOne("select pay_num from ".$GLOBALS['ecs']->table('user_account')." where id='$order_sn'");
		}
		/*获取支付商key*/
		if($pay_num)
		{
			$pay_code = current(explode('-',$pay_num));
			
			$payment = $GLOBALS['db']->getOne("select pay_config from ".$GLOBALS['ecs']->table('payment')." where pay_code ='$pay_code'");
			
			$payment = unserialize($payment);
			
			return  $payment['cfg_key'];
		}
	}
	
	
	/*同步响应操作生成MD5*/
	function md5_respond($arr=array())
	{
      	$sign = 'orderid='.$arr['orderid'].'&channelid='.$arr['channelid'].'&systemno='.$arr['systemno'].'&payprice='.$arr['payprice'].'&status='.$arr['status'].'&ext1='.$arr['ext1'].'&ext2='.$arr['ext2'];
        if($arr['key'])
           return $sign = substr(md5($sign.$arr['key']),8,16); //md5 16加密
        else
           return $sign = md5($sign);
	}
	
	function POST_md5_respond($arr=array())
	{
		$sign = 'orderid='.$arr['orderid'].'&chargemoney='.$arr['chargemoney'].'&systemno='.$arr['systemno'].'&channelid=1&status='.$arr['status'].'&ext1='.$arr['ext1'].'&ext2='.$arr['ext2'];
		
        if($arr['key'])
           return $sign = substr(md5($sign.$arr['key']),8,16); //md5 16加密
        else
          return  $sign = md5($sign);
	}
	function untx_pay_log($arr,$from)
	{
		$Arr['log'] = json_encode($arr);
		$Arr['time'] = time();
		$Arr['pay_from'] = $from;
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('untx_pay_log'), $Arr, 'INSERT');
	}
}
?>