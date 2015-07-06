<?php

/**
 *  管理中心出库类型管理处理程序文件
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
$exc   = new exchange($ecs->table("stock_out_type"), $db, 'id', '');

/*------------------------------------------------------ */
//-- 出库类型管理列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    /* 权限判断 */
    //admin_priv('stock_out_type');
    
    /* 取得过滤条件 */
    $filter = array();
    $smarty->assign('ur_here',      "出库类型管理");
    $smarty->assign('action_link',  array('text' => "添加出库类型", 'href' => 'stock_out_type.php?act=add'));
    $smarty->assign('full_page',    1);
    $smarty->assign('filter',       $filter);
     /* 代理商列表 */
    $arr_res = agency_list();
    $GLOBALS['smarty']->assign('agency_list',   $arr_res);
    $type_list = get_typelist();
    /*判断代理商或管理员*/
    if(if_agency())
    {
        $smarty->assign('if_agency',       if_agency());
    }

    $smarty->assign('type_list',    $type_list['arr']);
    $smarty->assign('filter',          $type_list['filter']);
    $smarty->assign('record_count',    $type_list['record_count']);
    $smarty->assign('page_count',      $type_list['page_count']);

    assign_query_info();
    $smarty->display('stock_out_type_list.htm');
}

/*------------------------------------------------------ */
//-- 添加出库类型
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add')
{
    /* 权限判断 */
    admin_priv('stock_out_type');

    $smarty->assign('ur_here',     "添加出库类型");
    $smarty->assign('action_link', array('text' => "出库类型列表", 'href' => 'stock_out_type.php?act=list'));
    $smarty->assign('form_action', 'insert');
    assign_query_info();
    $smarty->display('stock_out_type_info.htm');
}

/*------------------------------------------------------ */
//-- 添加出库类型
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'insert')
{
    /* 权限判断 */
    admin_priv('stock_out_type');
    
    $remarks         = $_POST['remarks'];
    $add_time        = gmtime();
    $user_id         = $_SESSION[admin_id];
    $admin_agency_id = admin_agency_id();
    $ip_addr         = real_ip();
    $sql = "INSERT INTO ".$ecs->table('stock_out_type').
           "(remarks, add_time, user_id, if_delete, ip_addr, admin_agency_id ) ".
            "VALUES ('$remarks', '$add_time', '$user_id', '0', '$ip_addr', '$admin_agency_id')";
    $db->query($sql);
    clear_cache_files(); // 清除相关的缓存文件
    $link[0]['text'] = "操作成功";
    $link[0]['href'] = 'stock_out_type.php?act=list';
    sys_msg("出库类型添加操作成功",0, $link);
}

/*------------------------------------------------------ */
//-- 翻页，排序
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    check_authz_json('stock_out_type');

    $type_list = get_typelist();
    /*判断代理商或管理员*/
    if(if_agency())
    {
        $smarty->assign('if_agency',       if_agency());
    }
    $smarty->assign('type_list',       $type_list['arr']);
    $smarty->assign('filter',          $type_list['filter']);
    $smarty->assign('record_count',    $type_list['record_count']);
    $smarty->assign('page_count',      $type_list['page_count']);

    make_json_result($smarty->fetch('stock_out_type_list.htm'), '',
                     array('filter' => $type_list['filter'], 'page_count' => $type_list['page_count']));
}

/*------------------------------------------------------ */
//-- 放入回收站
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    check_authz_json('stock_out_type');
    $id = intval($_REQUEST['id']);
        
        /*add by hg for date 2014-03-26 判断代理商是否非法操作商品*/
        //static_goods($_REQUEST['goods_id']);
        /*end*/

    if ($exc->edit("if_delete = 1", $id))
    {
        clear_cache_files();
        //$goods_name = $exc->get_name($goods_id);

        //admin_log(addslashes($goods_name), 'trash', 'goods'); // 记录日志

        $url = 'stock_out_type.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

        ecs_header("Location: $url\n");
        exit;
    }
}

/*------------------------------------------------------ */
//-- 还原回收站中的商品
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'restore_type')
{
    $id = intval($_REQUEST['id']);

    check_authz_json('stock_out_type'); // 检查权限

    $exc->edit("if_delete = 0, add_time = '" . gmtime() . "'", $id);
    clear_cache_files();

    //$goods_name = $exc->get_name($goods_id);

    //admin_log(addslashes($goods_name), 'restore', 'goods'); // 记录日志

    $url = 'stock_out_type.php?act=query&' . str_replace('act=restore_type', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}


/* 获得出库类型列表 */
function get_typelist()
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
        $filter['sort_by']    = empty($_REQUEST['sort_by']) ? 'a.add_time' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

        $where = '';
        if (!empty($filter['keyword']))
        {
            $where = " AND a.remarks LIKE '%" . mysql_like_quote($filter['keyword']) . "%'";
        }
        /*加入代理商条件*/
        if(!$filter['admin_agency_id'])
        {
            $agency_where = agency_where();
            if(!$agency_where)
            {
                $agency_where = "AND a.admin_agency_id = 0";
            }
            else 
            {
                $agency_where = "AND a.admin_agency_id = ".admin_agency_id();
            }
        }
        else
        {
            $agency_where = " AND a.admin_agency_id = $filter[admin_agency_id]";
        }
        
       //dump($agency_where);
        /* 出库类型总数 */
        $sql = 'SELECT COUNT(*) FROM ' .$GLOBALS['ecs']->table('stock_out_type'). ' AS a '.
               'WHERE 1 ' .$where.$agency_where;
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        $filter = page_and_size($filter);

        /* 获取出库类型数据 */
        $sql = 'SELECT a.* , u.user_name '.
               'FROM ' .$GLOBALS['ecs']->table('stock_out_type'). ' AS a '.
               'LEFT JOIN ' .$GLOBALS['ecs']->table('admin_user'). ' AS u ON u.user_id = a.user_id '.
               'WHERE 1 ' .$where.$agency_where. ' ORDER by '.$filter['sort_by'].' '.$filter['sort_order'];

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
        $rows['date'] = local_date($GLOBALS['_CFG']['time_format'], $rows['add_time']);

        $arr[] = $rows;
    }
    return array('arr' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}

?>