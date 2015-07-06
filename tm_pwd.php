<?php
/**
* 接收天猫修改密码通知信息
* 2014-07-15
*
**/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

$data = isset($_REQUEST['data'])?$_REQUEST['data']:'';
if(!$data) return false;
$obj = new lu_compile();
$data = $obj->decrypt($data);
parse_str($data,$res);
$username = TMUSER.$res[username];
$user_id = $db->getOne("select user_id from ".$ecs->table('users')." where user_name = '$username' and password = '$res[password_old]'");
if(!$user_id)
{
	echo '0';die;
}
else
{
	$password = md5($res['password_new']);
	$start = $db->query("update ".$ecs->table('users')." set password = '$password' where user_id=$user_id");
	echo $start?'1':'0';die;
}
?>