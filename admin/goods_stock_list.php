<?php

/**
 *  管理中心采购商品列表管理处理程序文件
 * ============================================================================
 * * 版权所有 2005-2012 新泛联
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
$exc   = new exchange($ecs->table("stock_goods"), $db, 'id', 'goods_id');


/*------------------------------------------------------ */
//-- 采购商品列表管理列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    admin_priv('goods_stock_list');
    /* 取得过滤条件 */
    $filter = array();
    $smarty->assign('ur_here',      $_LANG['07_goods_stock_list']);
    $smarty->assign('full_page',    1);
    $smarty->assign('filter',       $filter);

    $stock_control_log = get_stock_control_log();
    
    $goods_count  = $GLOBALS['db']->getOne("SELECT id FROM ".$ecs->table('stock_goods')." WHERE stock_info_id = 0 limit 1 " );
    $smarty->assign('goods_count',    $goods_count);

    $smarty->assign('stock_control_log',    $stock_control_log['arr']);
    $smarty->assign('filter',          $stock_control_log['filter']);
    $smarty->assign('record_count',    $stock_control_log['record_count']);
    $smarty->assign('page_count',      $stock_control_log['page_count']);

    $sort_flag  = sort_flag($stock_control_log['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    assign_query_info();
    $smarty->display('goods_stock_list.htm');
}

/*------------------------------------------------------ */
//-- 翻页，排序
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    check_authz_json('goods_stock_list');

    $stock_control_log = get_stock_control_log();
	
	
    $smarty->assign('stock_control_log',    $stock_control_log['arr']);
    $smarty->assign('filter',          $stock_control_log['filter']);
    $smarty->assign('record_count',    $stock_control_log['record_count']);
    $smarty->assign('page_count',      $stock_control_log['page_count']);

    $sort_flag  = sort_flag($stock_control_log['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('goods_stock_list.htm'), '',
    array('filter' => $stock_control_log['filter'], 'page_count' => $stock_control_log['page_count']));
}

elseif ($_REQUEST['act'] == 'remove')
{
    $id = intval($_REQUEST['id']);
    /* 检查权限 */
    //check_authz_json('goods_stock_list');   
    /* 删除商品 */
    if ($exc->drop($id))
    {
        clear_cache_files();
        $url = 'goods_stock_list.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);
        ecs_header("Location: $url\n");
        exit;
    }
}

//ccx 修改商品的成本价格
elseif ($_REQUEST['act'] == 'edit_costing_price')
{
    check_authz_json('goods_stock_list');
    $id              = intval($_POST['id']);
    $costing_price   = floatval($_POST['val']);
    if ($exc->edit("costing_price = '$costing_price' ", $id))
    {
        clear_cache_files();
        make_json_result(number_format($costing_price, 2, '.', ''));
    }
}

//ccx 修改商品的成本价格
elseif ($_REQUEST['act'] == 'edit_goods_number')
{
    check_authz_json('goods_stock_list');
    $id       = intval($_POST['id']);
    $goods_number    = floatval($_POST['val']);
    if ($exc->edit("goods_number = '$goods_number' ", $id))
    {
        clear_cache_files();
        make_json_result(number_format($goods_number, 0, '.', ''));
    }
}

//生成相关的采购订单信息
elseif($_REQUEST['act'] == 'insert')
{
    admin_priv('goods_stock_list');
    /*add by ccx  for date 2014-11-27		只查询相应的后台的还是各个代理商本身的*/
    $where = agency_where();

    /*add by hg for date 2014-04-21 可选商品*/
    if(if_agency()){
        if (!empty($filter['admin_agency_id']))
        {
            $where .= " AND (admin_agency_id = $filter[admin_agency_id] )";
        }
        else
        {
            $where .= " AND (admin_agency_id = 0 )";
        }
    }
    //$sql = "SELECT id FROM ".$ecs->table('stock_goods')." WHERE goods_number = 0 or costing_price =0 $where limit 1 ";
    //echo $sql;exit;
    //$goods_count  = $GLOBALS['db']->getOne("SELECT id FROM ".$ecs->table('stock_goods')." WHERE (goods_number = 0 or costing_price =0) $where limit 1 " );
    $goods_count  = $GLOBALS['db']->getOne("SELECT id FROM ".$ecs->table('stock_goods')." WHERE goods_number = 0 $where limit 1 " );
    if($goods_count > 0)
    {
    $link[] = array('href' => 'goods_stock_list.php?act=list', 'text' => $_LANG['07_goods_stock_list']);
    sys_msg("请按照相应的操作要求,必须填写商品的相关库存数量", 1, $links);
    }
    /*
    $goods_stock_sn  = $GLOBALS['db']->getOne("SELECT id FROM ".$ecs->table('stock_goods')." WHERE stock_info_id = 0 ".$where." limit 1 " );
    if($goods_stock_sn == 0)
    {
    $link[] = array('href' => 'goods_stock_list.php?act=list', 'text' => $_LANG['07_goods_stock_list']);
    sys_msg("改商品已经进行了提交采购订单", 1, $links);
    }
    */
    $goods_amount = 0; 
    
    $sql = "SELECT goods_id , costing_price, goods_number " . " FROM " . $ecs->table('stock_goods') .
          " WHERE stock_status = 0 AND stock_info_id = 0 AND stock_info_sn = 0 ". $where ;

    $stock_ms = $db->getAll($sql);
    foreach ($stock_ms as $key => $stock_val)
    {
        $goods_list_id = $stock_val['goods_id'].','.$goods_list_id;
        $goods_amount = $goods_amount + floatval($stock_val['costing_price'])* $stock_val['goods_number'];
    }
    
    /*add by ccx  for date 2014-11-27  记录采购订单是代理商添加的还是主站后台添加的 ID */
    $admin_id = $_SESSION['admin_id'];
    $adminRow = $db->getRow("select agency_user_id from " . $ecs->table('admin_user') . "where user_id = $admin_id");
    $admin_agency_id = $adminRow['agency_user_id'];
    
    
    $goods_list_id = substr($goods_list_id, 0, -1);
    $stock_info_sn = 'txdpc'.date('Ymdhi',time());  //生成采购订单的订单号
    $sql = "INSERT INTO " . $ecs->table('stock_info') . " (stock_info_sn, goods_list_id, admin_agency_id, goods_amount ) " .
            "VALUES ('".$stock_info_sn."', '$goods_list_id', '".$admin_agency_id."', '".$goods_amount."' )";
    $GLOBALS['db']->query($sql);
    $stock_info_id = $GLOBALS['db']->insert_id();
    
    //把采购的商品表的stock_info_id同时也要更新过里
    //$GLOBALS['db']->query("UPDATE ".$ecs->table('stock_goods')." SET stock_info_sn = '".$stock_info_sn."', stock_info_id = $stock_info_id WHERE stock_status = 0 ");
    $sql_up = "UPDATE ".$ecs->table('stock_goods').
              " SET stock_info_sn = '".$stock_info_sn."', stock_info_id = $stock_info_id ".
              " WHERE stock_status = 0 AND stock_info_id = 0 AND stock_info_sn = 0 ". $where ; 
    $GLOBALS['db']->query($sql_up);
    $url = 'goods_stock_info.php?act=list';
    ecs_header("Location: $url\n");
    exit;
}


/* 获取采购商品列表管理列表 */
function get_stock_control_log()
{
    $result = get_filter();
    if ($result === false)
    {
        $filter = array();
        $filter['keyword']    = empty($_REQUEST['keyword']) ? '' : trim($_REQUEST['keyword']);
        $filter['delete']    = empty($_REQUEST['delete']) ? '' : trim($_REQUEST['delete']);
        if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
        {
            $filter['keyword'] = json_str_iconv($filter['keyword']);
        }
        $filter['sort_by']    = empty($_REQUEST['sort_by']) ? 'a.id' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
        $filter['admin_agency_id']  = empty($_REQUEST['admin_agency_id']) ? '' : trim($_REQUEST['admin_agency_id']);
        
        /*add by ccx  for date 2014-11-27		只显示代理商本身*/
        $where = agency_where();
        /*add by hg for date 2014-04-21 可选商品*/
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
            $where .= " AND (a.goods_name LIKE '%" . mysql_like_quote($filter['keyword']) . "%'  or a.stock_info_sn LIKE '%" . mysql_like_quote($filter['keyword']) . "%' )";
        }
        if (empty($filter['delete']))
        {
           $where .= ' AND stock_status !=3 '; 
        }
        

        $sql = 'SELECT COUNT(*) FROM ' .$GLOBALS['ecs']->table('stock_goods'). ' AS a '.
               'WHERE 1 ' .$where;
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        $filter = page_and_size($filter);

        /* 获取采购商品列表管理数据 */
        
        $sql = 'SELECT a.*  '.
               'FROM ' .$GLOBALS['ecs']->table('stock_goods'). ' AS a '.
               'WHERE 1 ' .$where. ' ORDER by '.$filter['sort_by'].' '.$filter['sort_order'];

        $filter['keyword'] = stripslashes($filter['keyword']);
        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }
    //echo $sql;
    
    $arr = array();
    $res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);

    while ($rows = $GLOBALS['db']->fetchRow($res))
    {
        $arr[] = $rows;
    }
    $overall_order_amount = overall_order_amount($sql);  //总的计算
    $GLOBALS['smarty']->assign('costing_price_amount',price_format($overall_order_amount['costing_price_amount']));
    $GLOBALS['smarty']->assign('goods_number_amount',$overall_order_amount['goods_number_amount']);
    
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

?>