<?php
/**
 * 新支付响应页面
 * time：2014-04-03
 * by：hg
 *
 *
**/


define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

if($_GET['act'] == 'message')
{
	if(isset($_SESSION['message']) && isset($_SESSION['message_sn'])){
		if($_SESSION['message'] > 0 && $_SESSION['message_sn']>0)
		{
			$order_id = $_SESSION['message'];
			$order['order_sn'] = $_SESSION['message_sn'];
			
			$virtual_goods = get_virtual_goods($order_id, true);
			
			if($virtual_goods){
				$virtual_card = array();
				foreach ($virtual_goods AS $code => $goods_list)
				{
					/* 只处理虚拟卡 */
					if ($code == 'virtual_card')
					{
						foreach ($goods_list as $goods)
						{
							if ($info = virtual_card_result($order['order_sn'], $goods))
							{
								$virtual_card[] = array('goods_id'=>$goods['goods_id'], 'goods_name'=>$goods['goods_name'], 'info'=>$info);
							}
						}
					}
				}
				$smarty->assign('virtual_card', $virtual_card);
			}
		}
		$payArr['order_id'] = $_GET['order_id'];
		$payArr['status']   = $_GET['status'];
		$payArr['payprice'] = $_GET['money'];
		/* ccx 2014-12-13 获取抽奖活动地址链接相关信息 开始*/
        $payArr['email']    = $_GET['email'];
        $payArr['user_id']  = $_GET['user_id'];
        $payArr['datetime'] = gmtime();
        $payArr['ip_addr']  = real_ip();
        $sign_message = 'order_id='.$payArr['order_id'].'&payprice='.$payArr['payprice'].'&email='.$payArr['email'].'&user_id='.
                        $payArr['user_id'].'&datetime='.$payArr['datetime'].'&ip_addr='.$payArr['ip_addr'];
        $key_value = 'untx';   
        $sign_message_md = md5($sign_message.$key_value); 
        $smarty->assign('sign_message',$sign_message);    // 地址栏相关参数链接
        $smarty->assign('sign_message_md',$sign_message_md);   // 地址栏md5加密之后传递的参数
        /* ccx 2014-12-13 抽奖活动地址链接 结束*/
        
		$smarty->assign('message',$_GET['message']);
		//print_r($payArr);die;
		$smarty->assign('payArr',$payArr);
		assign_template();
		$_SESSION['pay_sn'] = $order['order_sn'];
		unset($_SESSION['message']);
		unset($_SESSION['message_sn']);
		$smarty->display('pay_respond.dwt');
	}else{
		header('location:flow.php?step=done');
	}
}
else
{
	include_once(ROOT_PATH . 'includes/lib_transaction.php');
	include_once('includes/modules/payment/payment.php');
	require(ROOT_PATH . 'includes/lib_payment.php');
	require(ROOT_PATH . 'includes/lib_order.php');
	$pay_obj    = new payment();

	$payArr = $pay_obj->respond();

	if($payArr != false && $payArr['status']==1)
	{
		$message = '支付成功';
		//$order_id = $db->getRow('select order_id from '.$ecs->table('order_info').' where order_sn='.$payArr['orderid'].'');
		$order_id = $db->getRow("select user_id, email, order_id from " .$ecs->table('order_info')." where order_sn='".$payArr['orderid']."'");
		$payArr['order_id'] = $order_id['order_id'];
		$payArr['user_id'] = $order_id['user_id'];
		$payArr['email'] = $order_id['email'];
		//$url = '&message='.$message.'&order_id='.$payArr['order_id'].'&status='.$payArr['status'].'&money='.$payArr['payprice'];
        $url = '&message='.$message.'&order_id='.$payArr['order_id'].'&status='.$payArr['status'].'&money='.$payArr['payprice'].'&email='.$payArr['email'].'&user_id='.$payArr['user_id'];

	}
	else
	{
		$message ="支付失败";
		$url = '&message='.$message;
	}
	$_SESSION['message_sn'] = $payArr['orderid']?$payArr['orderid']:0;
	$_SESSION['message'] = $order_id['order_id']?$order_id['order_id']:0;
	header('location:pay_respond.php?act=message'.$url.'');
}



?>