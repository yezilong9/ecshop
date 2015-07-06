<?php
/******************
* 查询OR转账文件
* by hg
******************/

define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');

$array = array();
$array['purpose'] = isset($_REQUEST['purpose'])?intval($_REQUEST['purpose']):'';
$array['from']    = isset($_REQUEST['from'])?intval($_REQUEST['from']):'';
$array['account'] = isset($_REQUEST['account'])?$_REQUEST['account']:'';
$array['money']   = isset($_REQUEST['money'])?$_REQUEST['money']:'';
$array['sign']    = isset($_REQUEST['sign'])?$_REQUEST['sign']:'';

if(empty($array)) return false;
$obj_money = new money();
$return_msg = $obj_money->check_sign($array);//效验数据
if($return_msg !== true)
{
	echo json_encode($obj_money->return_msg());die;
}
$obj_money->dispose();
echo json_encode($obj_money->return_msg());
?>