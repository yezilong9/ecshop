<?php

/**
 *  管理中心库存日志管理处理程序文件
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www..com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: article.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/*初始化数据交换对象 */
$exc   = new exchange($ecs->table("stock_control_log"), $db, 'id', 'goods_id');


/*------------------------------------------------------ */
//-- 库存日志管理列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    admin_priv('stock_control_ruku');
    /* 取得过滤条件 */
    $filter = array();
    $smarty->assign('ur_here',      $_LANG['goods_stock_control_ruku']);
    $smarty->assign('full_page',    1);
    $smarty->assign('filter',       $filter);

    /* 代理商列表 */
    $arr_res = agency_list();
    $GLOBALS['smarty']->assign('agency_list',   $arr_res);
    /*判断代理商或管理员*/
    if(if_agency())
    {
        $smarty->assign('if_agency',       if_agency());
    }
    $stock_control_log = get_stock_control_log();

    $smarty->assign('stock_control_log',    $stock_control_log['arr']);
    $smarty->assign('filter',          $stock_control_log['filter']);
    $smarty->assign('record_count',    $stock_control_log['record_count']);
    $smarty->assign('page_count',      $stock_control_log['page_count']);

    $sort_flag  = sort_flag($stock_control_log['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    assign_query_info();
    $smarty->display('stock_control_ruku.htm');
}

/*------------------------------------------------------ */
//-- 翻页，排序
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    check_authz_json('stock_control_ruku');

    $stock_control_log = get_stock_control_log();
	
	
    $smarty->assign('stock_control_log',    $stock_control_log['arr']);
    $smarty->assign('filter',          $stock_control_log['filter']);
    $smarty->assign('record_count',    $stock_control_log['record_count']);
    $smarty->assign('page_count',      $stock_control_log['page_count']);

    $sort_flag  = sort_flag($stock_control_log['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('stock_control_ruku.htm'), '',
    array('filter' => $stock_control_log['filter'], 'page_count' => $stock_control_log['page_count']));
}




/* 获取库存日志管理列表 */
function get_stock_control_log()
{
    $result = get_filter();
    if ($result === false)
    {
        $filter = array();
        $filter['admin_agency_id']    = empty($_REQUEST['admin_agency_id']) ? '' : trim($_REQUEST['admin_agency_id']);
        $filter['keyword']    = empty($_REQUEST['keyword']) ? '' : trim($_REQUEST['keyword']);
        if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
        {
            $filter['keyword'] = json_str_iconv($filter['keyword']);
        }
        $filter['sort_by']    = empty($_REQUEST['sort_by']) ? 'a.id' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
        
        $filter['start_date'] = empty($_REQUEST['start_date']) ? local_strtotime('-30 days') : $_REQUEST['start_date'];
        $filter['end_date'] = empty($_REQUEST['end_date']) ? local_strtotime('today') : $_REQUEST['end_date'];  
        
        if(strpos($filter['start_date'],'-') !== false)
        {
        $filter['start_date'] = local_strtotime($filter['start_date']);
        $filter['end_date'] = local_strtotime($filter['end_date']);
        }

        $where = ' AND stock_status = 1 ';
        $where .= agency_where();
        /*add by ccx for date 2014-11-28 商品入库列表*/
        if(if_agency()){
            if (!empty($filter['admin_agency_id']))
            {
                $where .= " AND (a.admin_agency_id = $filter[admin_agency_id] ) ";
            }
            else
            {
                $where .= " AND (a.admin_agency_id = 0 ) ";
            }
        }
        if (!empty($filter['keyword']))
        {
            $where =$where.  " AND (a.goods_name LIKE '%" . mysql_like_quote($filter['keyword']) . "%'  or a.stock_number LIKE '%" . mysql_like_quote($filter['keyword']) . "%' )";
        }
        if ($filter['start_date'])
        {
            $where = $where. " AND a.log_time >= '$filter[start_date]'";
        }
        if ($filter['end_date'])
        {
            $where = $where. " AND a.log_time <= '".($filter['end_date'] + 86400)."'";
        }
        

        $sql = 'SELECT COUNT(*) FROM ' .$GLOBALS['ecs']->table('stock_control_log'). ' AS a '.
               'WHERE 1 ' .$where;
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        $filter = page_and_size($filter);

        /* 获取库存日志管理数据 */
        
        $sql = 'SELECT a.*  '.
               'FROM ' .$GLOBALS['ecs']->table('stock_control_log'). ' AS a '.
               'WHERE 1 ' .$where. ' ORDER by '.$filter['sort_by'].' '.$filter['sort_order'];

        $filter['keyword'] = stripslashes($filter['keyword']);
        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }
    $arr = array();
    $res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);

    while ($rows = $GLOBALS['db']->fetchRow($res))
    {
        $rows['date'] = local_date($GLOBALS['_CFG']['time_format'], $rows['log_time']);

        $arr[] = $rows;
    }
    
    $GLOBALS['smarty']->assign('start_date',       local_date('Y-m-d', $filter['start_date']));
    $GLOBALS['smarty']->assign('end_date',         local_date('Y-m-d', $filter['end_date']));
    
   //显示商品的入库成本入库数量相关信息
    $sql_limit = $sql. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ",$filter[page_size]";
    $overall_order_amount = overall_order_amount($sql);  //总的计算
    $current_order_amount = current_order_amount($sql_limit); // 当前页的计算
    $GLOBALS['smarty']->assign('costing_price_amount',price_format($overall_order_amount['costing_price_amount']));
    $GLOBALS['smarty']->assign('goods_number_amount',$overall_order_amount['goods_number_amount']);
    $GLOBALS['smarty']->assign('current_costing_price_amount',price_format($current_order_amount['current_costing_price_amount']));
    $GLOBALS['smarty']->assign('current_goods_number_amount',$current_order_amount['current_goods_number_amount']);
    
    $filter['start_date'] = local_date('Y-m-d', $filter['start_date']);
    $filter['end_date'] = local_date('Y-m-d', $filter['end_date']);
    return array('arr' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}


function overall_order_amount($sql)
{
    $row = $GLOBALS['db']->getAll($sql);
    $arr = array('costing_price_amount'=>'','goods_number_amount'=>'');
    if(!empty($row))
    foreach($row as $k=>$v)
    {
        $arr['costing_price_amount'] += $v['costing_price'] * $v['goods_number'];
        $arr['goods_number_amount'] += $v['goods_number'];
    }
    return $arr;
}
function current_order_amount($sql_limit)
{
    $row = $GLOBALS['db']->getAll($sql_limit);
    $arr = array('current_costing_price_amount'=>'','current_goods_number_amount'=>'');
    if(!empty($row))
    foreach($row as $k=>$v)
    {
        $arr['current_costing_price_amount'] += $v['costing_price'] * $v['goods_number'];
        $arr['current_goods_number_amount'] += $v['goods_number'];
    }
    return $arr;
}
?>