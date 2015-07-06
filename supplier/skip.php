<?php
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
$act = $_REQUEST['act'];
if($act=='tm')
{
	$admin_user_id = admin_agency_id();
	$res = $db->getRow("SELECT user_name,password,tm_mark FROM ".$ecs->table('users')." WHERE user_id = $admin_user_id");
	if((int)$res['tm_mark'] == 1)
	{
		$arr = array();
		$arr['username'] = substr($res['user_name'],strlen(TMUSER));
		$arr['password'] = $res['password'];
		$str = http_build_query($arr);
		$obj = new lu_compile();//加密类
		$obj_user = new tm_user();//加密类
		$data = $obj->encrypt($str);
		header("Location:http://taobao.ba.com/auth/login_o2o?data=$data");
	}
	sys_msg('统计信息不存在或请求错误，请稍后再试', 0, $links);
}
?>