<?php

/**
* 说明:APP信息记录
* author：hg
* time:2014-09-23
**/
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
$exc   = new exchange($ecs->table("version"), $db, 'id', 'ad_name');
/*------------------------------------------------------ */
//-- 记录列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
	admin_priv('request_log');
    $smarty->assign('ur_here',     '版本管理');
    $smarty->assign('action_link', array('text' => '添加新版本号', 'href' => 'app_version.php?act=add'));
    $smarty->assign('full_page',  1);
    $request_log_list = request_log();
    $smarty->assign('request_log_list', $request_log_list['request_log_list']);
    $smarty->assign('filter',       $request_log_list['filter']);
    $smarty->assign('record_count', $request_log_list['record_count']);
    $smarty->assign('page_count',   $request_log_list['page_count']);

    assign_query_info();
    $smarty->display('request_log.htm');
}
/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{   
	$request_log_list = request_log();
	
	$smarty->assign('request_log_list', $request_log_list['request_log_list']);
	$smarty->assign('filter',       $request_log_list['filter']);
	$smarty->assign('record_count', $request_log_list['record_count']);
	$smarty->assign('page_count',   $request_log_list['page_count']);
	
	$sort_flag  = sort_flag($request_log_list['filter']);
	$smarty->assign($sort_flag['tag'], $sort_flag['img']);
	make_json_result($smarty->fetch('request_log.htm'), '',
	array('filter' => $request_log_list['filter'], 'page_count' => $request_log_list['page_count']));

}

/* 获取版本数据列表 */
function request_log()
{
    $filter = array();
    /* 获得总记录数据 */
    $sql = 'SELECT COUNT(*) FROM ' .$GLOBALS['ecs']->table('connector_app');
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);
    $filter = page_and_size($filter);
    /* 获得广告数据 */
    $arr = array();
    $sql = 'SELECT v.id,v.imei,v.phone,v.sim_sn,v.imsi,v.os,v.system_version,v.app_version,v.user_info,v.time,'.
	'v.insert_time,u.user_name FROM ' .$GLOBALS['ecs']->table('connector_app').
	' as v left join '.$GLOBALS['ecs']->table('users').' as u on v.user_id = u.user_id ORDER BY v.id DESC';
    $res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);
    while ($rows = $GLOBALS['db']->fetchRow($res))
    {
		$rows['time'] = date('Y-m-d', $rows['time']);
        $arr[] = $rows;
    }
    return array('request_log_list' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}
?>