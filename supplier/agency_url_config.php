<?php
/*
* 支付管理模块
* add by hg for date 2014-03-18
*	
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
include(ROOT_PATH . '/includes/cls_json.php');
    $json = new JSON;

//列表
if($_REQUEST['act'] =='list')
{
	$smarty->assign('agency_list',   agency_list());
	$sql = "SELECT id,agency_url,agency_user_id,region_id,area_name FROM " . $ecs->table('agency_url')." order by id desc";
	
	$agency_url_list = $db->getAll($sql);
	//dump($agency_url_list);
	$smarty->assign('agency_url_list',$agency_url_list);
	$smarty->display('agency_url.htm');
}

if($_POST['act'] == 'inquer_region'){
	$region = $_POST['region'] ? trim($_POST['region']) : '';
	$parent = $_POST['parent'] ? intval($_POST['parent']) : 0;
	switch($region){
		case 'province':
				$region_type = 1;
				break;
		case 'city':
				$region_type = 2;
				break;
		case 'area':
				$region_type = 3;
				break;
	}
	$parent_id = $parent;
	$sql = "SELECT region_id,region_name FROM " . $ecs->table('region') .
					" WHERE parent_id = $parent_id AND region_type = $region_type ";
    $result = $db->getAll($sql);
	die($json->encode($result));
	
}


//添加
if(!empty($_POST['act']) && $_POST['act'] =='url_add')
{
	
	$agency_url = trim($_POST['agency_url']);//代理商域名
	$agency_user_id = intval($_POST['agency_user_id']);//代理商
	$region_id = intval($_POST['area'])?intval($_POST['area']):intval($_POST['city']);//代理商所在地id
	
	$sql_area = "SELECT region_name FROM ".$ecs->table('region')."WHERE region_id = '$region_id'";
	$area_name = $db->getOne($sql_area);
	$sql = "INSERT INTO " .$ecs->table('agency_url'). " (agency_url, agency_user_id,region_id,area_name)".
						"VALUES ('$agency_url', '$agency_user_id',$region_id,'$area_name')";
	$db->query($sql);

	header('location:agency_url_config.php?act=list');
}
//删除
if($_REQUEST['act'] =='url_del')
{
	$sql = "DELETE FROM " . $ecs->table('agency_url') . " WHERE id = '$_REQUEST[url_id]'";
    $db->query($sql);
	header('location:agency_url_config.php?act=list');
}
//修改显示信息
if($_REQUEST['act'] =='url_edit')
{
	$sql = "SELECT id,agency_url,agency_user_id,area_name " ." FROM " . $ecs->table('agency_url').'where id ='.$_REQUEST['url_id'];
    $url = $db->getRow($sql);
	
	$smarty->assign('agency_user_id',$url['agency_user_id']);
	$smarty->assign('agency_list',   agency_list());
	$smarty->assign('agency_url_list',$agency_url_list);
	$smarty->assign('url',$url);
	$smarty->display('agency_url_edit.htm');
	
}
//修改内容
if(!empty($_POST['act']) && $_POST['act'] =='url_edit_info')
{
	$agency_url = $_POST['agency_url'];//代理商域名
	$agency_user_id = $_POST['agency_user_id'];//代理商
	$id = $_POST['url_id'];//代理商
	$region_id = intval($_POST['area'])?intval($_POST['area']):intval($_POST['city']);//代理商所在地id
	if($region_id>0){
		$sql_area = "SELECT region_name FROM ".$ecs->table('region')."WHERE region_id = '$region_id'";
	$area_name = $db->getOne($sql_area);
		$sql = "UPDATE " . $ecs->table('agency_url') . "SET agency_url = '$agency_url',agency_user_id = '$agency_user_id', region_id = '$region_id',area_name = '$area_name' WHERE id = $id";
	}else{
		$sql = "UPDATE " . $ecs->table('agency_url') . "SET agency_url = '$agency_url',agency_user_id = '$agency_user_id' WHERE id = $id";
	}

	$db->query($sql);
	header('location:agency_url_config.php?act=list');
}




?>