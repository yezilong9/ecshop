<?php
/*
* 支付管理模块
* add by hg for date 2014-03-18
*	
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . '/includes/cls_image.php');

//列表
if($_REQUEST['act'] =='payment')
{
	$sql = "SELECT pay_id,pay_code,pay_name,Is_fix_rate,fix_rate,change_rate,upper_limit,pay_desc,rate_desc,pay_order,pay_config,enabled,is_online,Is_delete FROM " . $ecs->table('payment').' where Is_delete = 0 order by pay_order desc,pay_id asc';
	//print_r($sql);die;
	$payment_list = $db->getAll($sql);
	foreach($payment_list as $key=>$value){
		$payment_list[$key]['pay_config'] = unserialize($value['pay_config']);
	}
	$smarty->assign('payment_list',$payment_list);
	$smarty->display('pay_payment.htm');
}

//添加
if(!empty($_POST['act']) && $_POST['act'] =='payment_add')
{
	
	$pay_way_name = $_POST['pay_way_name'];//支付商名称
	$pay_way_code = $_POST['pay_way_code'];//支付商代码
	$status = $_POST['status'];//商户状态
	$is_fix_rate = $_POST['is_fix_rate'];//固定或浮动费率
	$fix_rate = $_POST['fix_rate'];//固定费率
	
	$change_rate = $_POST['change_rate'];//浮动费率
	$change_rate_upper_limit = $_POST['change_rate_upper_limit'];//上限
	
	$rate_desc = $_POST['rate_desc'];//费率中文描述
	$pay_order = $_POST['pay_order'];//排序
	
	$config = array('cfg_value'=>$_POST['cfg_value'],
					'cfg_key'=>$_POST['cfg_key'],
					'cfg_id'=>$_POST['cfg_id'],
					'app_id'=>$_POST['app_id'],
					'app_key'=>$_POST['app_key'],
					'cfg_gate'=>$_POST['cfg_gate']
	);
	$pay_config = serialize($config);
	if($is_fix_rate == '1')//固定费率
	{
		$sql = "INSERT INTO " .$ecs->table('payment'). " (pay_code, pay_name, Is_fix_rate, fix_rate,rate_desc,pay_order,pay_config,enabled)".
                            "VALUES ('$pay_way_code', '$pay_way_name', '$is_fix_rate','$fix_rate','$rate_desc','$pay_order','$pay_config','$status')";
	}else if($is_fix_rate == '2'){
		$sql = "INSERT INTO " .$ecs->table('payment'). " (pay_code, pay_name, Is_fix_rate,change_rate,upper_limit,rate_desc,pay_order,pay_config,enabled)".
                            "VALUES ('$pay_way_code', '$pay_way_name', '$is_fix_rate','$change_rate','$change_rate_upper_limit','$rate_desc','$pay_order','$pay_config','$status')";
	}
	//print_r($sql);die;
	$db->query($sql);
	header('location:pay.php?act=payment');
}
//删除
if($_REQUEST['act'] =='payment_del')
{
	$sql = "DELETE FROM " . $ecs->table('payment') . " WHERE pay_id = '$_REQUEST[pay_id]'";
    $db->query($sql);
	header('location:pay.php?act=payment');
}
//修改显示信息
if($_REQUEST['act'] =='payment_edit')
{
	$sql = "SELECT pay_id,pay_code,pay_name,Is_fix_rate,fix_rate,change_rate,upper_limit,pay_desc,rate_desc,pay_order,pay_config,enabled,is_online,Is_delete " ."FROM " . $ecs->table('payment').'where pay_id ='.$_REQUEST['pay_id'];
    $payment_row = $db->getRow($sql);
	$pay_config = unserialize($payment_row['pay_config']);
	$smarty->assign('pay_config',$pay_config);
	$smarty->assign('payment',$payment_row);
	$smarty->display('pay_payment_edit.htm');
}
//修改内容
if(!empty($_POST['act']) && $_POST['act'] =='payment_edit')
{
	$pay_id = $_POST['pay_id'];//id
	$pay_way_name = $_POST['pay_way_name'];//支付商名称
	$pay_way_code = $_POST['pay_way_code'];//支付商代码
	$status = $_POST['status'];//商户状态
	$is_fix_rate = $_POST['is_fix_rate'];//固定或浮动费率
	$fix_rate = $_POST['fix_rate'];//固定费率
	$change_rate = $_POST['change_rate'];//浮动费率
	$change_rate_upper_limit = $_POST['change_rate_upper_limit'];//上限
	$rate_desc = $_POST['rate_desc'];//费率中文描述
	$pay_order = $_POST['pay_order'];//排序
	//配置信息
	$config = array('cfg_value'=>$_POST['cfg_value'],
					'cfg_key'=>$_POST['cfg_key'],
					'cfg_id'=>$_POST['cfg_id'],
					'cfg_gate'=>$_POST['cfg_gate'],
					'app_id'=>$_POST['app_id'],
					'app_key'=>$_POST['app_key'],
	);
	
	$pay_config = serialize($config);
	
	if($is_fix_rate == '1')//固定费率
	{
		$sql = "UPDATE " . $ecs->table('payment') . "SET pay_code = '$pay_way_code',pay_name = '$pay_way_name',Is_fix_rate = '$is_fix_rate',fix_rate = $fix_rate,rate_desc = '$rate_desc',pay_order ='$pay_order',pay_config ='$pay_config',enabled = '$status',change_rate = '0',upper_limit = '0' WHERE pay_id = $pay_id";
	}else if($is_fix_rate == '2'){
		$sql = "UPDATE " . $ecs->table('payment') . "SET pay_code = '$pay_way_code',pay_name = '$pay_way_name',Is_fix_rate = '$is_fix_rate',fix_rate = '0',rate_desc = '$rate_desc',pay_order ='$pay_order',pay_config ='$pay_config',enabled = '$status',change_rate = $change_rate,upper_limit = $change_rate_upper_limit WHERE pay_id = $pay_id";
	}
	//print_r($sql);die;
	$db->query($sql);
	header('location:pay.php?act=payment');
}

//支付方式列表
if($_REQUEST['act'] =='pay_type')
{
	$sql = "SELECT paytype_id,type_name,enabled,Is_delete FROM " . $ecs->table('pay_type').'where Is_delete = 0 order by paytype_id desc';
	$type_list = $db->getAll($sql);
	//print_r($type_list);die;
	$smarty->assign('type_list',$type_list);
	$smarty->display('pay_type.htm');

}

//支付方式添加
if(!empty($_POST['act']) && $_POST['act'] =='type_add')
{
	$type_name = $_POST['pay_mode_name'];//支付方式name
	$enabled = $_POST['status'];//是否开启
	$sql = "INSERT INTO " .$ecs->table('pay_type'). " (type_name,enabled) VALUES ('$type_name','$enabled')";
	//print_r($sql);die;
	$db->query($sql);
	header('location:pay.php?act=pay_type');
}

//支付方式删除
if($_REQUEST['act'] =='pay_type_del')
{
	$sql = "DELETE FROM " . $ecs->table('pay_type') . " WHERE paytype_id = '$_REQUEST[paytype_id]'";
    $db->query($sql);
	header('location:pay.php?act=pay_type');
}


//支付方式修改显示信息
if($_REQUEST['act'] =='type_edit')
{
	$sql = "SELECT paytype_id,type_name,enabled,Is_delete " ."FROM " . $ecs->table('pay_type').'where paytype_id ='.$_REQUEST['paytype_id'];
    $type_row = $db->getRow($sql);
	$smarty->assign('type',$type_row);
	$smarty->display('pay_type_edit.htm');
}

//支付方式修改
if(!empty($_POST['act']) && $_POST['act'] =='type_edit_and')
{
	$type_name = $_POST['pay_mode_name'];//支付方式name
	$enabled = $_POST['status'];//是否开启
	$paytype_id = $_POST['paytype_id'];//id
	$sql = "UPDATE " . $ecs->table('pay_type') . "SET type_name = '$type_name',enabled='$enabled' WHERE paytype_id = $paytype_id";
	//print_r($sql);die;
	$db->query($sql);
	header('location:pay.php?act=pay_type');
}
//支付商银行列表
if($_REQUEST['act'] =='bank')
{

	$bank_list = get_pzd_list();
	$smarty->assign('bank_list',  $bank_list['pzd_list']);
	$smarty->assign('filter',       $bank_list['filter']);
	$smarty->assign('record_count', $bank_list['record_count']);
	$smarty->assign('page_count',   $bank_list['page_count']);
	$smarty->assign('full_page',    1);
	assign_query_info();
	

				
	//print_r($bank_list);die;
	//支付商
	$payment_sql = "SELECT `pay_id`,`pay_name` FROM " . $ecs->table('payment').'where Is_delete =0';
	$payment_list = $db->getAll($payment_sql);
	$smarty->assign('payment_list',$payment_list);
	
	//支付方式
	$typ_sql = "SELECT `paytype_id`,`type_name` FROM " . $ecs->table('pay_type').' where Is_delete =0';
	$typ_list = $db->getAll($typ_sql);
	$smarty->assign('typ_list',$typ_list); 
	
	$smarty->display('pay_bank.htm');
	
}elseif($_REQUEST['act'] =='query'){
	//获取信息列表
	$bank_list = get_pzd_list();
	$smarty->assign('bank_list',  $bank_list['pzd_list']);
	$smarty->assign('filter',       $bank_list['filter']);
	$smarty->assign('record_count', $bank_list['record_count']);
	$smarty->assign('page_count',   $bank_list['page_count']);
	//支付商
	$payment_sql = "SELECT `pay_id`,`pay_name` FROM " . $ecs->table('payment').'where Is_delete =0';
	$payment_list = $db->getAll($payment_sql);
	$smarty->assign('payment_list',$payment_list);
	
	//支付方式
	$typ_sql = "SELECT `paytype_id`,`type_name` FROM " . $ecs->table('pay_type').' where Is_delete =0';
	$typ_list = $db->getAll($typ_sql);
	$smarty->assign('typ_list',$typ_list);
	//跳转页面
	make_json_result($smarty->fetch('pay_bank.htm'),'',
	array('filter' => $bank_list['filter'], 'page_count' => $bank_list['page_count']));
}

//支付商银行添加
if(!empty($_POST['act']) && $_POST['act'] =='bank_add')
{
	
	$pay_id = $_POST['pay_way_id'];//支付商
	$bank_name = $_POST['pay_bank_name'];//银行名称
	$bank_code = $_POST['pay_num'];//本地代码
	$bank_pay_num = $_POST['bank_pay_num'];//银行代码，支付商的银行代码
	
	$pay_pres = $db->getRow("select pay_code from ".$ecs->table('payment'). "where pay_id=$pay_id");
	$Local_bank_code = $pay_pres['pay_code'].'--'.$bank_code;//识别代码
	$pay_type = $_POST['pay_mode'];//支付方式
	$enabled = $_POST['status'];//状态 开启/关闭
	$sql = "INSERT INTO " .$ecs->table('pay_bank'). " (pay_id, bank_name,bank_code,Local_bank_code,pay_type,enabled,bank_pay_num) VALUES ('$pay_id','$bank_name','$bank_code','$Local_bank_code','$pay_type','$enabled',$bank_pay_num)";
	//print_r($sql);die;
	$db->query($sql);
	header('location:pay.php?act=bank');
}

//支付商银行修改信息显示
if($_REQUEST['act'] =='bank_edit')
{
 	//支付商银行
	$sql = "SELECT b.bank_pay_num,b.Is_delete,b.enabled,b.pay_type,b.Local_bank_code,b.bank_name,b.bank_code,b.pay_id,b.paybank_id,p.pay_name,t.type_name FROM " . $ecs->table('pay_bank'). " AS b left join " .$ecs->table('payment'). " As p on b.pay_id = p.pay_id left join ".$ecs->table('pay_type') ."As t on b.pay_type = t.paytype_id where b.paybank_id =$_REQUEST[paybank_id] and b.Is_delete =0";
	$bank_row = $db->getRow($sql);
	$smarty->assign('bank',$bank_row);
	
	//支付商
	$payment_sql = "SELECT `pay_id`,`pay_name` FROM " . $ecs->table('payment').'where Is_delete =0';
	$payment_list = $db->getAll($payment_sql);
	$smarty->assign('payment_list',$payment_list);
	
	//支付方式
	$typ_sql = "SELECT `paytype_id`,`type_name` FROM " . $ecs->table('pay_type').' where Is_delete =0';
	$typ_list = $db->getAll($typ_sql);
	$smarty->assign('typ_list',$typ_list); 
	$smarty->display('pay_bank_edit.htm');
}
//支付商银行修改过程
if(!empty($_POST['act']) && $_POST['act'] =='bank_edit_and')
{
	$paybank_id = $_POST['paybank_id'];//id
	$pay_id = $_POST['pay_way_id'];//支付商
	$bank_name = $_POST['pay_bank_name'];//银行名称
	$bank_code = $_POST['pay_num'];//银行代码，支付商的银行代码
	$bank_pay_num = $_POST['bank_pay_num'];//银行代码，支付商的银行代码
	
	$pay_pres = $db->getRow("select pay_code from ".$ecs->table('payment'). "where pay_id=$pay_id");
	$Local_bank_code = $pay_pres['pay_code'].'--'.$bank_code;//银行本地代码
	
	$pay_type = $_POST['pay_mode'];//支付方式
	$enabled = $_POST['status'];//状态 开启/关闭
	$sql = "UPDATE " . $ecs->table('pay_bank') . "SET pay_id = '$pay_id',bank_name = '$bank_name',bank_code = '$bank_code',Local_bank_code = '$Local_bank_code',pay_type = '$pay_type',enabled = '$enabled',bank_pay_num= '$bank_pay_num' WHERE paybank_id = $paybank_id";
	$db->query($sql);
	header('location:pay.php?act=bank');
}

//支付商银行删除
if($_REQUEST['act'] =='bank_del')
{
	$sql = "DELETE FROM " . $ecs->table('pay_bank') . " WHERE paybank_id = '$_REQUEST[paybank_id]'";
    $db->query($sql);
	header('location:pay.php?act=bank');	
}

//支付规则列表
if($_REQUEST['act'] =='pay_set')
{
	//支付列表  pay_set表
	$sql = "SELECT enabled,content,value,name,type,paytset_id FROM " . $ecs->table('pay_set'). " where Is_delete =0 order by paytset_id desc";
	
	$set_list = $db->getAll($sql);
	
	foreach($set_list as $k=>$v){
		if($v['content'])
		{
			if($v['type'] == 1 || $v['type'] == 2){
				$payment = $db->getRow("select pay_name from ".$ecs->table('payment')." where pay_id=$v[content]");
				$set_list[$k]['pay_name'] = $payment['pay_name'];
			}
			elseif($v['type'] == 3)
			{
				$pay_bank = $db->getRow("select b.bank_name,p.pay_name from ".$ecs->table('pay_bank')." as b left join " .$ecs->table('payment'). " as p on b.pay_id = p.pay_id where paybank_id=$v[content]");
				$set_list[$k]['pay_name'] = $pay_bank['pay_name'].'/'.$pay_bank['bank_name'];
			}
		}
	}
	//dump($set_list);
	$smarty->assign('set_list',$set_list);
	
	//支付商
	$payment_sql = "SELECT `pay_id`,`pay_name` FROM " . $ecs->table('payment').'where Is_delete =0 and pay_id in (select pay_id from '. $ecs->table('pay_bank').' where Is_delete =0)';
	$payment_list = $db->getAll($payment_sql);
	$smarty->assign('payment_list',$payment_list);
	
	$smarty->display('pay_set.htm');
}

//支付规则添加
if(!empty($_POST['act']) && $_POST['act'] =='set_add')
{
	$type = $_POST['p_way_set_type'];//类型
	$name = $_POST['p_way_set_name'];//名称
	$value = $_POST['p_way_set_value'];//对应type的数值
	$content = $_POST['pay_id'];//支付商ID
	if($type == 1 || $type == 2)
	{
		$content = $_POST['pay_way_id'];
	}
	$enabled  = $_POST['status'];//状态 开启（1）/关闭（0）
	$sql = "INSERT INTO " .$ecs->table('pay_set'). " (type, name,value,content,enabled) VALUES ('$type','$name','$value','$content','$enabled')";
	//print_r($sql);die;
	$db->query($sql);
	header('location:pay.php?act=pay_set');
}

//支付规则修改
if($_REQUEST['act'] =='set_edit')
{
	//支付列表
	$sql = "SELECT enabled,content,value,name,type,paytset_id FROM " . $ecs->table('pay_set'). " where Is_delete =0 and paytset_id = $_REQUEST[paytset_id] order by paytset_id desc";
	
	$set_list = $db->getRow($sql);
	
	if(!$set_list['content']){
		$set_list['content'] = '0';
	}
	if($set_list['type'] == 1 || $set_list['type'] == 2)
	{
		$pay = $db->getRow("select pay_name from ".$ecs->table('payment')." where pay_id=$set_list[content]");
		$set_list['pay_name'] = $pay['pay_name'];
	}
	elseif($set_list['type'] == 3)
	{
		$pay = $db->getRow("select b.bank_name,p.pay_name from ".$ecs->table('pay_bank')." as b left join " .$ecs->table('payment'). " as p on b.pay_id = p.pay_id where paybank_id=$set_list[content]");
		$set_list['pay_name'] = $pay['pay_name'].'/'.$pay['bank_name'];
	}
	$smarty->assign('set',$set_list);
	$smarty->assign('set_name',$pay);
	//支付商
	$payment_sql = "SELECT `pay_id`,`pay_name` FROM " . $ecs->table('payment').'where Is_delete =0 and pay_id in (select pay_id from '. $ecs->table('pay_bank').' where Is_delete =0)';
	
	$payment_list = $db->getAll($payment_sql);
	$smarty->assign('payment_list',$payment_list);
	//支付银行
	//dump($set_list);
	if($set_list['content'])
	{
		$bank_sql = "SELECT `paybank_id`,`bank_name` FROM " . $ecs->table('pay_bank').'where paybank_id ='.$set_list['content'];
		$bank_list = $db->getAll($bank_sql);
		$smarty->assign('bank_list',$bank_list);
	}
	//dump($bank_list);
	$smarty->display('pay_set_edit.htm');
}

//支付规则过程
if(!empty($_POST['act']) && $_POST['act'] =='set_edit_and')
{
	$type = $_POST['p_way_set_type'];//类型
	$name = $_POST['p_way_set_name'];//名称
	$value = $_POST['p_way_set_value'];//对应type的数值
	$content = $_POST['pay_id'];//支付商ID
	//print_r($type.'<br/>');print_r($name.'<br/>');print_r($value.'<br/>');print_r($content);die;
	if($type == 1 || $type == 2)
	{
		$content = $_POST['pay_way_id'];
	}
	$enabled  = $_POST['status'];//状态 开启（1）/关闭（0）
	$paytset_id  = $_POST['paytset_id'];//id
	$sql = "UPDATE " . $ecs->table('pay_set') . "SET type = '$type',name = '$name',value='$value',content = '$content',enabled = '$enabled' WHERE paytset_id = $paytset_id";
	$db->query($sql);
	header('location:pay.php?act=pay_set');
}
//支付规则删除
if($_REQUEST['act'] =='set_del')
{
	$sql = "DELETE FROM " . $ecs->table('pay_set') . " WHERE paytset_id = '$_REQUEST[paytset_id]'";
    $db->query($sql);
	header('location:pay.php?act=pay_set');	
}

//异步获取支付商银行信息
if(!empty($_REQUEST['pay_rul']) &&  $_REQUEST['pay_rul']=='pay_bank_name')
{
	$sql = "select paybank_id,bank_name from " . $ecs->table('pay_bank') . " WHERE pay_id = '$_REQUEST[pay_id]'";
	$res = $db->getAll($sql);
	echo json_encode($res);die;
}
//设置银行
if($_REQUEST['act'] =='show_bank')
{
	$show_bank_list = $db->getAll("select * from ".$ecs->table('show_bank'). " order by id desc");
	$smarty->assign('show_bank_list',$show_bank_list);
	$smarty->display('pay_show_bank.htm');
}
//添加显示银行
if($_REQUEST['act'] =='add_show_bank')
{
	$image      = new cls_image($_CFG['bgcolor']);//实例化图片处理函数
	$bank_name  = isset($_POST['bank_name'])?$_POST['bank_name']:'';
	$bank_code  = isset($_POST['bank_code'])?$_POST['bank_code']:'';
	$bank_image = isset($_FILES['bank_image'])?$_FILES['bank_image']:'';
	$payment    = isset($_POST['payment'])?$_POST['payment']:'';
	$status     = isset($_POST['status'])?$_POST['status']:'0';
	if (isset($bank_image['error']))
	{
		if ($image->check_img_type($bank_image['type']))
			$bank_img_name = $image->upload_image($bank_image, '');
	}else{
		sys_msg('上传图片失败', 1);
	}
	if(!$bank_img_name)
		sys_msg('添加失败', 1);
	$AddArray = array(
			'bank_name' => $bank_name,
			'bank_code' => $bank_code,
			'bank_img'  => $bank_img_name,
			'state'     => $status,
			'payment'     => $payment,
	);
	if($db->autoExecute($ecs->table('show_bank'), $AddArray, 'INSERT'))
	{
		$link = 'pay.php?act=show_bank';
		sys_msg('添加成功', 0,array(array('href'=>$link,'text'=>'返回')));
	}
}
//删除银行
if($_REQUEST['act'] =='del_show_bank')
{
	$id = isset($_REQUEST['id'])?intval($_REQUEST['id']):'';
	if(!$id) sys_msg('参数错误', 1);
	$bank_img = $db->getOne("select bank_img from".$ecs->table('show_bank')." where id=$id");
	$db->query("delete from ".$ecs->table('show_bank')." where id=$id");
	unlink('../'.$bank_img);
	$link = 'pay.php?act=show_bank';
	sys_msg('删除成功', 0,array(array('href'=>$link,'text'=>'返回')));
}
//修改银行
if($_REQUEST['act'] =='edit_show_bank')
{
	
	$id = isset($_REQUEST['id'])?intval($_REQUEST['id']):'';
	$link = 'pay.php?act=edit_show_bank&id='.$id;
	if(!$id) sys_msg('参数错误', 1);
	if(!empty($_POST))
	{
		$image      = new cls_image($_CFG['bgcolor']);//实例化图片处理函数
		$bank_name  = isset($_POST['bank_name'])?$_POST['bank_name']:'';
		$bank_code  = isset($_POST['bank_code'])?$_POST['bank_code']:'';
		$bank_image = isset($_FILES['bank_image'])?$_FILES['bank_image']:'';
		$payment    = isset($_POST['payment'])?$_POST['payment']:'';
		$status     = isset($_POST['status'])?$_POST['status']:'0';
		$bank_img_name = '';
		
		if (isset($bank_image['error']) && $bank_image['error']==0)
		{
			if ($image->check_img_type($bank_image['type']))
				$bank_img_name = $image->upload_image($bank_image, '');
			if(!$bank_img_name)
				sys_msg('上传图片失败', 1);
		}
		if($bank_img_name) $bank_img = ',bank_img = "'.$bank_img_name.'"';
		$res = $db->query("update ".$ecs->table('show_bank')." set bank_name = '$bank_name', bank_code = '$bank_code',state='$status',payment='$payment' $bank_img where id = $id");
		if($res)
			sys_msg('修改成功', 0,array(array('href'=>'pay.php?act=show_bank','text'=>'返回')));
		else
			sys_msg('修改失败', 1,array(array('href'=>$link,'text'=>'返回')));
	}
	else
	{
		$show_bank_list = $db->getRow("select * from ".$ecs->table('show_bank'). "where id=$id");
		$smarty->assign('bank',$show_bank_list);
	}
	$smarty->display('pay_show_bank_edit.htm');

}
//异步修改开启状态
if($_REQUEST['act'] =='edit_state')
{
	$id = isset($_REQUEST['id'])?intval($_REQUEST['id']):'';
	$state = isset($_REQUEST['state'])?intval($_REQUEST['state']):'';
	if($id && ($state==1 || $state==0))
	{
		$res = $db->query("update ".$ecs->table('show_bank')." set state='$state' where id = $id");
		$json = array('msg'=>$res,'state'=>$state);
	}
	else
	{
		$json = array('msg'=>0);
	}
	echo json_encode($json);die;
}
function get_pzd_list()
{
	$sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('pay_bank');
	$filter['record_count'] = $GLOBALS['db']->getOne($sql);
	$filter = page_and_size($filter);
	/* 获活动数据 */
	$sql = "SELECT b.bank_pay_num,b.Is_delete,b.enabled,b.pay_type,b.Local_bank_code,b.bank_name,b.bank_code,b.pay_id,b.paybank_id,p.pay_name,t.type_name FROM " . $GLOBALS['ecs']->table('pay_bank'). " AS b left join " .$GLOBALS['ecs']->table('payment'). " As p on b.pay_id = p.pay_id left join ".$GLOBALS['ecs']->table('pay_type') ."As t on b.pay_type = t.paytype_id where b.Is_delete =0 order by b.paybank_id desc LIMIT ". $filter['start'] .", " . $filter['page_size'];
	$filter['keywords'] = stripslashes($filter['keywords']);
	set_filter($filter, $sql);
	$row = $GLOBALS['db']->getAll($sql);
	$arr = array('pzd_list' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
	return $arr;
}



?>