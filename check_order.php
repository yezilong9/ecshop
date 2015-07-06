<?php
/***************************
* 查询订单页面
* author:hg
* time：20114-09-18
* 
***************************/
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . 'includes/lib_order.php');
/* 载入语言文件 */
require_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/user.php');

$smarty->assign('categories_pro',  get_categories_tree_pro()); // 分类树加强版
$smarty->assign('navigator_list',get_navigator($ctype, $catlist));  //自定义导航栏
$smarty->assign('helps', get_shop_help());       // 网店帮助
if(!empty($_GET))
{
	$order_key[] = 'consignee';
	$order_key[] = 'order_sn';
	$order_key[] = 'tel';
	$order_key[] = 'email';
	$order[] 	= isset($_GET['consignee'])?$_GET['consignee']:'';
	$order[]  	= isset($_GET['order_sn'])?$_GET['order_sn']:'';
	$order[]	= isset($_GET['tel'])?$_GET['tel']:'';
	$order[]	= isset($_GET['email'])?$_GET['email']:'';
	$obj = new check_order();
	$order_id_arr = $obj->orderid_arr($order,$order_key);
	if($order_id_arr) 
		$order_array = $obj->order_info_res($order_id_arr);
	if($order_array)
		die(json_encode($obj->check_order_html($order_array)));
	echo '1';die;
}
assign_template();
$smarty->display('check_order.dwt');

#处理订单信息
class check_order{

	private $db;
	
	private $ecs;
	
	public function __construct()
	{
		$this->db = $GLOBALS['db'];
		$this->ecs = $GLOBALS['ecs'];
	}
	
	#获取条件组合
	public function orderid_arr($order,$order_key)
	{
		$where = " WHERE 1 ";
		$i = 0;
		foreach($order as $key=>$value){
			if(!empty($value))
			{
				$i++;
				$where .= " AND $order_key[$key] = '$value'";
			}
		}
		$order_id_arr = '';
		if($i >= 2){
			$time = gmtime() - 3600*24*15;
			$sql = 'SELECT order_id FROM '.$this->ecs->table('order_info')." $where ORDER BY order_id desc";
			$order_id_arr = $this->db->getAll($sql);
		}
		//四个条件分别组成不重复的两两组合
		/*$num = count($order);
		for($i=0;$i<=$num;$i++){
			for($ii=$i+1;$ii<=$num-1;$ii++){
				if($order[$i] && $order[$ii]){
					$time = gmtime() - 3600*24*15;
					$where = " WHERE $order_key[$i] = '$order[$i]' AND $order_key[$ii] = '$order[$ii]' AND add_time > '$time'";
					$order_id_arr = $this->db->getAll('SELECT order_id FROM '.$this->ecs->table('order_info')." $where ORDER BY order_id desc");
					if($order_id_arr[0]['order_id']) break;
				}
			}
			if($order_id_arr[0]['order_id']) break;
		}*/
		return $order_id_arr?$order_id_arr:'';
	}
	
	#获取订单信息
	public function order_info_res($order_id_arr)
	{
		$order_array = array();
		foreach($order_id_arr as $key=>$value){
			//获取订单信息
			$order_res = $this->db->getRow("SELECT o.order_sn,o.order_status,o.shipping_status,o.pay_status,d.invoice_no,".
			"o.confirm_time,o.pay_time,o.shipping_time,o.consignee,o.tel,o.email,o.address FROM ".
			$this->ecs->table('order_info')." as o left join ".$this->ecs->table('delivery_order')." as d on o.order_id = d.order_id WHERE ".
			 "o.order_id = $value[order_id]");
			 //订单状态时间
			$order_res = $this->order_status_time($order_res);
			//订单商品
			$goods_list = $this->order_goods($value['order_id']);
			$order_res['goods_list'] = $goods_list;
			$order_array[] = $order_res;
		}
		return $order_array;
	}
	
	#订单状态时间
	public function order_status_time($order_res)
	{
		/* 确认时间 支付时间 发货时间 */
		if ($order_res['confirm_time'] > 0 && ($order_res['order_status'] == OS_CONFIRMED || $order_res['order_status'] == OS_SPLITED || $order_res['order_status'] == OS_SPLITING_PART))
		{
			$order_res['confirm_time'] = sprintf($GLOBALS['_LANG']['confirm_time'], local_date($GLOBALS['_CFG']['time_format'], $order_res['confirm_time']));
		}
		else
		{
			$order_res['confirm_time'] = '';
		}
		if ($order_res['pay_time'] > 0 && $order_res['pay_status'] != PS_UNPAYED)
		{
			$order_res['pay_time'] = sprintf($GLOBALS['_LANG']['pay_time'], local_date($GLOBALS['_CFG']['time_format'], $order_res['pay_time']));
		}
		else
		{
			$order_res['pay_time'] = '';
		}
		if ($order_res['shipping_time'] > 0 && in_array($order_res['shipping_status'], array(SS_SHIPPED, SS_RECEIVED)))
		{
			$order_res['shipping_time'] = sprintf($GLOBALS['_LANG']['shipping_time'], local_date($GLOBALS['_CFG']['time_format'], $order_res['shipping_time']));
		}
		else
		{
			$order_res['shipping_time'] = '';
		}
		$order_res['order_status'] = $GLOBALS['_LANG']['os'][$order_res['order_status']];
		$order_res['pay_status'] = $GLOBALS['_LANG']['ps'][$order_res['pay_status']];
		$order_res['shipping_status'] = $GLOBALS['_LANG']['ss'][$order_res['shipping_status']];
		return $order_res;
	}
	
	#订单商品
	public function order_goods($order_id)
	{
		/* 订单商品 */
		$goods_list = order_goods($order_id);
		foreach ($goods_list AS $key => $value)
		{
			$goods_list[$key]['market_price'] = price_format($value['market_price'], false);
			$goods_list[$key]['goods_price']  = price_format($value['goods_price'], false);
			$goods_list[$key]['subtotal']     = price_format($value['subtotal'], false);
		}
		return $goods_list;
	}
	
	#组装html
	public function check_order_html($res)
	{
		$html = '';
		foreach($res as $key=>$value){
			$html .= '<div class="ch-count">
						<!-- 订单状态 -->
						<div class="ch-state mod">
							<div class="state-hd mod-hd">
									<h2><img src="themes/wanbiao/images/state.png" width="22" height="25"/>订单状态</h2>
							</div>
							<div class="state-bd">
								<ul class="state-list mod-a">
									<li style="color:#AC0E2B;">
										订单号：'.$value['order_sn'].'
									</li>
									<li>
										订单状态：'.$value['order_status'].'   '.$value['confirm_time'].'
									</li>
									<li>
										付款状态：'.$value['pay_status'].'   '.$value['pay_time'].'
									</li>
									<li>
										配送状态：'.$value['shipping_status'].'   '.$value['shipping_time'].'
									</li>
									<li>
										发货单：'.$value['invoice_no'].'
									</li>

								</ul>
							</div>
						</div>
						<!-- 订单状态 end-->
						<!-- 商品以及支付信息 -->
						<div class="ch-zfu mod">
							<div class="zfu-hd mod-hd">
									<h2><img src="themes/wanbiao/images/zfu.png" width="26" height="27"/>商品信息</h2>
							</div>
							<div class="zfu-bd fn-clear">
								<div class="data-name">
									<table cellpadding="0" cellspacing="0" width="100%">
										<tbody>
											<tr>
												<td width="208">名称</td>
												<td>单价</td>
												<td>数量</td>
											</tr>';
			foreach($value['goods_list'] as $k=>$v){
				$html .= '<tr>
							<td width="208">
								<a target="_blank" href=goods.php?id='.$v['goods_id'].' class="zfu-link">'.$v['goods_name'].'</a>
							</td>
							<td>'.$v['goods_price'].' </td>
							<td>'.$v['goods_number'].'</td>
						</tr>';
			}
							
			$html .= '</tbody>
									</table>
								</div>
								<div class="data-price">
								</div>
							</div>
						</div>
						<!-- 商品以及支付信息 end-->
						<!-- 收货人信息 -->
						<div class="ch-people mod">
							<div class="ppl-hd mod-hd">
								<h2><img src="themes/wanbiao/images/people.png" width="27" height="21"/>订单联系方式</h2>
							</div>
							<div class="ppl-bd">
								<ul class="mod-a">
									<li>收货人姓名：'.$value['consignee'].'</li>
									<li>联系电话：'.$value['tel'].'</li>
									<li>E-mail：'.$value['email'].'</li>
									<li>详细地址：'.$value['address'].'</li>
								</ul>
							</div>
						</div>
						<!-- 收货人信息 end-->
						</div>';
		}
		
		return $html;
			
	}




}
?>