<?php

/*天下支付通知*/

define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
include_once('includes/modules/payment/txd.php');
include_once('includes/lib_payment.php');

$pay_obj    = new txd();

//异步
if(empty($_REQUEST['skip']))
{
	$notify_data = isset($_REQUEST['notify_data'])?$_REQUEST['notify_data']:'';
	$notify_data = str_replace('\\','',$notify_data);
	$sign = isset($_REQUEST['sign'])?$_REQUEST['sign']:'';
	$pay_obj->verify($notify_data,$sign);
	
}else{
//同步
	$order = $pay_obj->get_verify();
	if($order !== false)
	{
		$smarty->assign('order',$order);
	}
	$smarty->display('txd_inform.dwt');
}

?>