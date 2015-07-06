<?php
/**
* 代理商下级购物时扣除代理商余额
* time：2014-05-22
* author：hg
*
**/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/lib_order.php');
require_once(ROOT_PATH . 'includes/lib_goods.php');
require_once(ROOT_PATH . '/' . ADMIN_PATH . '/includes/lib_goods.php');

if ($_REQUEST['act'] == 'list')
{
	admin_priv('agency_pay_log');

	$action_list = if_agency()?'all':'';
	$smarty->assign('all',         $action_list);
	
	$smarty->assign('agency_list',   agency_list());
	$smarty->assign('ur_here',      $_LANG['souwu']);
	$agency_pay_log_list = agency_pay_log_list();
	$smarty->assign('filter',       $agency_pay_log_list['filter']);
	$smarty->assign('record_count', $agency_pay_log_list['record_count']);
	$smarty->assign('page_count',   $agency_pay_log_list['page_count']);
	$smarty->assign('full_page',    1);
	assign_query_info();
	//dump($agency_pay_log_list['res']);
	$smarty->assign('goods_res',$agency_pay_log_list['res']);
	$smarty->display('agency_pay_log_list.htm');
}
elseif($_REQUEST['act'] == 'query')
{
	$agency_pay_log_list = agency_pay_log_list();
	$smarty->assign('goods_res',  $agency_pay_log_list['res']);
	$smarty->assign('filter',       $agency_pay_log_list['filter']);
	$smarty->assign('record_count', $agency_pay_log_list['record_count']);
	$smarty->assign('page_count',   $agency_pay_log_list['page_count']);
    /* 排序标记 */
    $sort_flag  = sort_flag($agency_pay_log_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);
	make_json_result($smarty->fetch('agency_pay_log_list.htm'),'',
	array('filter' => $agency_pay_log_list['filter'], 'page_count' => $agency_pay_log_list['page_count']));
}

function agency_pay_log_list()
{
    $filter['order_sn']          = empty($_REQUEST['order_sn']) ? '' : trim($_REQUEST['order_sn']);
    $filter['status']            = empty($_REQUEST['status']) ? '' : trim($_REQUEST['status']);
    $filter['admin_agency_id']   = empty($_REQUEST['admin_agency_id']) ? '' : trim($_REQUEST['admin_agency_id']);
	
	$filter['sort_by']          = empty($_REQUEST['sort_by']) ? 'id' : trim($_REQUEST['sort_by']);
	$filter['sort_order']       = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
	
	$where = '';
	$where .= agency_where();
	$where = preg_replace('|admin_agency_id|','ag.agency_id',$where);
	//dump($where);
	/* 订单号 */
	if (!empty($filter['order_sn']))
	{
		$where .= " AND oi.order_sn = $filter[order_sn]";
	}
	/*状态*/
	if(!empty($filter['status']))
	{
		$where .= " AND ag.status = $filter[status]";
	}
	/*代理商*/
	if(!empty($filter['admin_agency_id']))
	{
		$where .= " AND ag.agency_id = $filter[admin_agency_id]";
	}
	
	$sql = "SELECT COUNT(*) FROM  " . $GLOBALS['ecs']->table('agency_pay_log') . "as ag left join " .$GLOBALS['ecs']->table('users') . " as u on u.user_id = ag.user_id left join " .$GLOBALS['ecs']->table('admin_user'). " as au on au.agency_user_id = ag.agency_id left join " .$GLOBALS['ecs']->table('order_info'). " as oi on oi.order_id = ag.order_id WHERE 1 $where order by $filter[sort_by] $filter[sort_order] ";
	
	$filter['record_count'] = $GLOBALS['db']->getOne($sql);
	$filter = page_and_size($filter);


	$sql = "SELECT ag.id,oi.order_sn,ag.order_id,au.user_name as admin_name,u.user_name,ag.order_amount,ag.time,ag.status FROM  " . $GLOBALS['ecs']->table('agency_pay_log') . "as ag left join " .$GLOBALS['ecs']->table('users') . " as u on u.user_id = ag.user_id left join " .$GLOBALS['ecs']->table('admin_user'). " as au on au.agency_user_id = ag.agency_id left join " .$GLOBALS['ecs']->table('order_info'). " as oi on oi.order_id = ag.order_id WHERE 1 $where order by $filter[sort_by] $filter[sort_order]  LIMIT ". $filter['start'] .", " . $filter['page_size'] ."";
	$filter['keywords'] = stripslashes($filter['keywords']);
	set_filter($filter, $sql);
	$res = $GLOBALS['db']->getAll($sql);
	//dump($res);
	$arr = array('res' => $res, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
	return $arr;
}
?>