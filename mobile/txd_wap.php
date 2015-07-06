<?php

/*天下支付wap接受天下店通知*/

define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
include_once('includes/modules/payment/txd.php');
include_once('includes/lib_payment.php');

$pay_obj    = new txd();
$k="txd_wap201412.txt";	
$str_order_bak = date('Y-m-d H:i:s')."--天下支付同步或者是异步返回数据：".print_r($_REQUEST,true);
error_log($str_order_bak,3,$k);
//$_REQUEST['notify_data'] = '{\"price\":\"78.000\",\"total_fee\":\"78.000\",\"status\":\"PAY_SUCCESS\",\"id\":\"10332687\",\"app_id\":\"12632\",\"mch_id\":\"10789446\",\"mch_order_no\":\"2014121718161680086\",\"quantity\":\"1\",\"pay_type\":\"114\",\"create_time\":\"2014-12-17 18:16:18\",\"update_time\":\"2014-12-17 18:16:59\"}';

//$_REQUEST['sign'] = '7bfce5567c4914ec4f4d14e7c97e423e';
//异步
if(empty($_REQUEST['order_id']) && empty($_REQUEST['status']))
{
    
	$notify_data = isset($_REQUEST['notify_data']) ? $_REQUEST['notify_data']:'';
	$notify_data = str_replace('\\','',$notify_data);
	$sign = isset($_REQUEST['sign']) ? $_REQUEST['sign']:'';
	$pay_obj->verify($notify_data,$sign);
	
}
elseif(!empty($_REQUEST['order_id']) && !empty($_REQUEST['mch_order_id']))
{
//同步
	$order = $pay_obj->get_verify_wap();
	if($order !== false)
	{
	    //圣诞节活动送红包抽奖活动 2014/12/20 23:24:14 weichen start
        if(date('Ym') == '201412')
        {
            $sign_message = 'order_id='.$order['txd_order_id'].'&payprice='.$order['price'].'&email=0&user_id='.
                            $order['user_id'].'&datetime='.gmtime().'&ip_addr='.real_ip();
            $key_value = 'untx';   
            $sign_message_md = md5($sign_message.$key_value);    
            $smarty->assign('sign_message',$sign_message);    // 地址栏相关参数链接
            $smarty->assign('sign_message_md',$sign_message_md);   // 地址栏md5加密之后传递的参数
            $smarty->assign('lucky_draw', 1);
        }
        //圣诞节活动送红包抽奖活动 2014/12/20 23:24:14 weichen end
		$smarty->assign('order',$order);
	}
	$smarty->display('txd_inform.dwt');
}
else
{
    echo "支付错误";
}

?>