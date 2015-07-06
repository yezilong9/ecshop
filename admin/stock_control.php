<?php

/**
 *  管理中心商品库存处理程序文件
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
$exc   = new exchange($ecs->table("stock_control"), $db, 'id', 'goods_id');
//$image = new cls_image();

/* 
/*------------------------------------------------------ */
//-- 商品库存管理的列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'stock_control_list')
{
    admin_priv('stock_control_list');
    /* 取得过滤条件 */
    $filter = array();
    $smarty->assign('ur_here',      $_LANG['goods_stock_control_list']);
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
    $stock_control_list = get_stock_control_list();
    	
    $smarty->assign('stock_control_list',    $stock_control_list['arr']);
    $smarty->assign('filter',          $stock_control_list['filter']);
    $smarty->assign('record_count',    $stock_control_list['record_count']);
    $smarty->assign('page_count',      $stock_control_list['page_count']);

    $sort_flag  = sort_flag($stock_control_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    assign_query_info();
    $smarty->display('stock_control_list.htm');
}

/*------------------------------------------------------ */
//-- 翻页，排序
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    check_authz_json('stock_control_list');

    $stock_control_list = get_stock_control_list();
	
	
    $smarty->assign('stock_control_list',    $stock_control_list['arr']);
    $smarty->assign('filter',          $stock_control_list['filter']);
    $smarty->assign('record_count',    $stock_control_list['record_count']);
    $smarty->assign('page_count',      $stock_control_list['page_count']);

    $sort_flag  = sort_flag($stock_control_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('stock_control_list.htm'), '',
        array('filter' => $stock_control_list['filter'], 'page_count' => $stock_control_list['page_count']));
}

/*------------------------------------------------------ */
//-- ccx 2015-01-05 出库操作处理页面  start 开始
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_stock')
{
    /* 权限判断 */
    admin_priv('stock_control_list');
    
    /* 取商品库存数据 */
    $sql = "SELECT * FROM " .$ecs->table('stock_control'). " WHERE id='$_REQUEST[id]'";
    $stock_list = $db->GetRow($sql);
    
    $smarty->assign('stock_list', $stock_list);
    $smarty->assign('form_action', 'update_stock');
    assign_query_info();
    $smarty->assign('ur_here',     "出库作废操作");
    $smarty->assign('action_link', array('text' => "库存列表", 'href' => 'stock_control.php?act=stock_control_list'));
    
    $smarty->display('edit_stock.htm');
}
    
elseif ($_REQUEST['act'] == 'update_stock')
{
    /* 权限判断 */
    admin_priv('stock_control_list');
    $id              = $_POST['id'];
    $cancel_number   = $_POST['cancel_number'];    
    if($cancel_number == 0)
    {
        sys_msg("请输入该商品的作废数量");
    }
    
    /*add by ccx  for date 2015-01-05 获取用户的 ID  start */
    $admin_id = $_SESSION['admin_id'];
    $adminRow = $db->getRow("select agency_user_id from " . $ecs->table('admin_user') . "where user_id = $admin_id");
    $admin_agency_id = $adminRow['agency_user_id'];
    /*  end */
    if($admin_agency_id == '')
    {
        $admin_agency_id = 0;
    }
    
    $sql = "SELECT * FROM " .$ecs->table('stock_control'). " WHERE id= $id ";
    $stock_list = $db->GetRow($sql);
    $goods_number = $stock_list['goods_number'] - $cancel_number;
    if($goods_number < 0 )
    {
        sys_msg("商品的作废数量不能超过商品的库存数量");
    }
    if($admin_agency_id != $stock_list['admin_agency_id'])
    {
        sys_msg("不能对代理商的库存进行作废处理");
    }
    
    //商品表的商品库存数量
    $sql_goods_count = "select goods_number from " . $ecs->table('goods') .
                       " where goods_id = ".$stock_list['goods_id'];
    $sum_goods = $GLOBALS['db']->getOne($sql_goods_count);
    
    //库存表中的商品的数量
    $sql_stock_count = "select sum(goods_number)  from " . $ecs->table('stock_control') .
                       " where goods_id = ".$stock_list['goods_id'];
    $sum_stock = $GLOBALS['db']->getOne($sql_stock_count);
    
    
    if($sum_stock != $sum_goods)  //判断库存数量是否相等，不相等就对库存进行作废操作
    {
        sys_msg("商品库存表的库存数量跟商品表的库存数量不相等,请认真检查,再对该商品进行库存作废操作");
    }
    
    $GLOBALS['db']->begin();   //开始事务
    
    $sql_up = " UPDATE " . $ecs->table('stock_control').
              " SET goods_number = ".$goods_number.
              " WHERE id =".$id ;
    //echo $sql_up;exit;
    $result_up = $GLOBALS['db']->query($sql_up);
    if(!$result_up || $GLOBALS['db']->affected_rows() != 1)
    {
        $GLOBALS['db']->rollback();  //事务中断
        sys_msg("该操作出现异常,请再次对该商品进行作废处理操作");  
    }
    
    $minus_stock_sql = "UPDATE " . $GLOBALS['ecs']->table('goods') . "
                        SET goods_number = goods_number - " . $cancel_number . "
                        WHERE goods_id = " . $stock_list['goods_id'];
    $result_up_2 = $GLOBALS['db']->query($minus_stock_sql, 'SILENT');
    if(!$result_up_2 || $GLOBALS['db']->affected_rows() != 1)
    {
        $GLOBALS['db']->rollback();  //事务中断
        sys_msg("该操作出现异常,请再次对该商品进行作废处理操作");  
    }
    
    
    
    $stock_type = -1 ;    //商品入库处理， 默认为 1（增加）  -1（减少）
    $stock_status = 10;   //0:默认; 1：添加入库产品，-1：发货时候库存减少状态，3：库存不够的时候，4：取消发货的时候库存会增加状态, 5：表示取消发货的状态，6：退货的时候库存会增加的状态， 7：表示该订单时退货的状态 , 8：表示从已发货设置成未发货的时候，库存增加； 9：表示该日志记录修改成未发货的状态; 10 表示库存的作废状态(后台出库处理)
    $delete_stock_info_sn = 'deletetxdpc'.date('Ymdhi',time());  //生成作废的单号   
    
    $sql_log = "INSERT INTO " . $GLOBALS['ecs']->table('stock_control_log') . 
               " (stock_id, goods_name, log_time, goods_number, stock_type, costing_price, stock_number, stock_status, stock_note, ip_address, admin_agency_id )  VALUES ('" . $id . "', '".$stock_list['goods_name']."', '".gmtime()."', '". $cancel_number ."', '". $stock_type ."', '" . $stock_list['costing_price'] . "' , '".$delete_stock_info_sn."' , '".$stock_status."', '".real_ip()."','". real_ip() ."', '".$admin_agency_id."')";
    $result_ok = $GLOBALS['db']->query($sql_log);
    if(!$result_ok)
    {
        $GLOBALS['db']->rollback();  //事务中断
        sys_msg("因为网络原因,写入日志出现异常,请再次对该商品进行作废处理操作");  
    }
    //$GLOBALS['db']->rollback();
    $GLOBALS['db']->commit();
    clear_cache_files(); // 清除相关的缓存文件
    $link[0]['text'] = "返回库存列表页";
    $link[0]['href'] = 'stock_control.php?act=stock_control_list';
    sys_msg("该商品的出库作废操作成功",0, $link);
    
}

/*ccx 2015-01-06 批量对商品进行库存选择处理*/
elseif ($_REQUEST['act'] == 'batch') 
{
    //admin_priv('stock_control');
    
    if ($_POST['type'] == 'batch_stock_goods')
    {
        $goods_id_arr = $_POST['checkboxes'];
        $link[] = array('href' => 'stock_control.php?act=stock_control_list', 'text' => $_LANG['batch_stock_goods']);
        if(@!array_filter($goods_id_arr) || !array_filter($goods_id_arr) )
        {
            sys_msg('没有勾选产品', 1, $link);
        }
        /*add by ccx  for date 2015-01-06  记录采购订单是代理商添加的还是主站后台添加的 ID */
        $admin_id = $_SESSION['admin_id'];
        $adminRow = $db->getRow("select agency_user_id from " . $ecs->table('admin_user') . "where user_id = $admin_id");
        $admin_agency_id = $adminRow['agency_user_id'] ? $adminRow['agency_user_id']:0;
        $GLOBALS['db']->begin();   //开始事务   
         
        foreach($goods_id_arr as $key=>$value)
        {
            $sql_1 = "SELECT id FROM ".$ecs->table('stock_goods_out').
                     " WHERE stock_id = $value and stock_status !=3 and stock_info_sn = 0 and stock_info_id = 0  limit 1 " ;
            
            $id  = $db->getOne($sql_1);
            if($id > 0)
            {
                $GLOBALS['db']->rollback();
                sys_msg("在作废商品列表当中已经存在您刚刚选择的商品了，请检查再进行添加", 1, $links);
            }
            $sql_goods = "SELECT goods_name, goods_id, costing_price, goods_number, admin_agency_id FROM ".$ecs->table('stock_control').
                         " WHERE id = $value";
            $goodsRow  = $db->getRow($sql_goods);
            if($goodsRow['admin_agency_id'] != $admin_agency_id)
            {
                $GLOBALS['db']->rollback();  //事务中断
                $links[] = array('text' => "返回库存列表页", 'href' => 'stock_control.php?act=stock_control_list');
                sys_msg("操作失败--不能对代理商的商品进行作废操作", 1, $links);
            }
            
            $sql_goodsout = "INSERT INTO ".$ecs->table('stock_goods_out').
                            " (stock_id,goods_id,goods_name,admin_agency_id,costing_price,goods_number ) ".
                            " VALUES  ($value,'".$goodsRow['goods_id']."' , '".$goodsRow['goods_name']."', '".$goodsRow['admin_agency_id']."', '".
                                       $goodsRow['costing_price']."', '".$goodsRow['goods_number']."' )";
            $GLOBALS['db']->query($sql_goodsout);
        }
        $GLOBALS['db']->commit();
    }
    
    else
    {
        sys_msg('请选择批量作废的商品选项操作', 1, $link);
    }
    clear_cache_files();   
    $url = 'goods_stock_list_out.php?act=list';
    ecs_header("Location: $url\n");
}


/* 获得库存管理相关列表 */
function get_stock_control_list()
{
    $result = get_filter();
    if ($result === false)
    {
        $filter = array();
        $filter['keyword']    = empty($_REQUEST['keyword']) ? '' : trim($_REQUEST['keyword']);
        $filter['admin_agency_id']    = empty($_REQUEST['admin_agency_id']) ? '' : trim($_REQUEST['admin_agency_id']);
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
            $where .= " AND a.goods_name LIKE '%" . mysql_like_quote($filter['keyword']) . "%'";
        }
        if ($filter['start_date'])
        {
            $where = $where. " AND a.log_time >= '$filter[start_date]'";
        }
        if ($filter['end_date'])
        {
            $where = $where. " AND a.log_time <= '".($filter['end_date'] + 86400)."'";
        }
        
        $sql = 'SELECT COUNT(*) FROM ' .$GLOBALS['ecs']->table('stock_control'). ' AS a '.
               'WHERE 1 ' .$where;
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        $filter = page_and_size($filter);

        /* 获取商品库存的相关数据 */
        $sql = 'SELECT a.*  '.
               'FROM ' .$GLOBALS['ecs']->table('stock_control'). ' AS a '.
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

    //显示采购订单的相关信息：包括库存数量，以及采购总价，当页的采购价格等等
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