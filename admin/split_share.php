<?php

/**
 *  程序说明
 * ===========================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www..com；
 * ----------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ==========================================================
 * $Author: liubo $
 * $Id: affiliate_ck.php 17217 2011-01-19 06:29:08Z liubo $
 */

define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');

//admin_priv('affiliate_ck');
$timestamp = time();

$affiliate = unserialize($GLOBALS['_CFG']['affiliate']);
empty($affiliate) && $affiliate = array();
$separate_on = $affiliate['on'];

/*------------------------------------------------------ */
//-- 分成页
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    $smarty->assign('agency_list',   agency_list());
    $action_list = if_agency()?'all':'';
    $smarty->assign('all',         $action_list);
    $logdb = delivery_list();
    //print_r($logdb);
    $smarty->assign('full_page',  1);
    $smarty->assign('ur_here', $_LANG['split_share_order']);
    $smarty->assign('on', $separate_on);
    $smarty->assign('logdb',        $logdb['orders']);
    $smarty->assign('filter',       $logdb['filter']);
    $smarty->assign('record_count', $logdb['record_count']);
    $smarty->assign('page_count',   $logdb['page_count']);
    if (!empty($_GET['auid']))
    {
        $smarty->assign('action_link',  array('text' => $_LANG['back_note'], 'href'=>"users.php?act=edit&id=$_GET[auid]"));
    }
    assign_query_info();
    $smarty->display('split_share_list.htm');
}
/*------------------------------------------------------ */
//-- 分页
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    //$logdb = get_affiliate_ck();
    //$smarty->assign('logdb',        $logdb['logdb']);
    $logdb = delivery_list();
    $smarty->assign('logdb',        $logdb['orders']);
    $smarty->assign('on', $separate_on);
    $smarty->assign('filter',       $logdb['filter']);
    $smarty->assign('record_count', $logdb['record_count']);
    $smarty->assign('page_count',   $logdb['page_count']);

    $sort_flag  = sort_flag($logdb['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('split_share_list.htm'), '', array('filter' => $logdb['filter'], 'page_count' => $logdb['page_count']));
}

/*
**  分润操作相关代码 2015-03-24 ccx 
*/
elseif ($_REQUEST['act'] == 'separate')
{
    admin_priv('split_share');
    include_once(ROOT_PATH . 'includes/lib_order.php');
 
    $oid = (int)$_REQUEST['oid'];
    
     // 分润比例
    $sql = 'SELECT v2_percent FROM ' .$ecs->table('split_instal'). " WHERE is_line='1'";
    $split_instal = $db->getRow($sql);
    
    $sql = "SELECT  o.user_id, o.order_sn FROM " .
            $GLOBALS['ecs']->table('order_info') . " o".
            " LEFT JOIN " . $GLOBALS['ecs']->table('users') . " u ON o.user_id = u.user_id".
            " WHERE order_id = '$oid'" ;
    $row = $db->getRow($sql);
    
    $sql_goods = "SELECT o.goods_id, o.goods_price, o.goods_number, g.split_percent FROM " .
            $GLOBALS['ecs']->table('order_goods') . " o " . 
            " LEFT JOIN " . $GLOBALS['ecs']->table('goods') . " g ON o.goods_id = g.goods_id".
            " WHERE order_id = '$oid'" ;
    $row_goods = $db->getAll($sql_goods);
        
    $user_number = get_user_number($row['user_id']);
    foreach ($user_number AS $k => $v1)
    {
        $user_rank = $v1['user_rank'];
        
        //if($user_rank == 36) // 内网的V2的等级就是36  
        if($user_rank == 40)   // 外网的V2的等级就是40
        {
            $user_id = $v1['user_id'];
            foreach($row_goods as $k=>$v_goods)
            {  
                $goods_prine    = $v_goods['goods_price'];
                $goods_number   = $v_goods['goods_number'];
                $split_percent  = $v_goods['split_percent']/100;
                $goods_id       = $v_goods['goods_id'];
                             
                $money = ($goods_prine * $goods_number) * $split_percent *($split_instal['v2_percent']/100);
                $sql = "INSERT INTO " . $GLOBALS['ecs']->table("earnings_log") . 
                       " (user_id, order_id, goods_id, money, change_time, consumer_id, type, money_status ) VALUES " .
                       " ('$user_id', '$oid', '$goods_id', '$money',  '". gmtime() ."', '". $row['user_id'] ."', '1', '1' )";
                
                $GLOBALS['db']->query($sql);     
                $money_count += $money ;                  
            }
            
            $info = "订单 ". $row['order_sn'] ." 已经给V2进行了分润:根据分润比例,所得的分润金钱有".$money_count."元"; 
            log_account_change($user_id, $money_count, 0, 0, 0, $info);   
            $links[] = array('text' => "该订单已经给V2进行了分润", 'href' => 'split_share.php?act=list');
            sys_msg($_LANG['edit_ok'], 0 ,$links);
        }  
    }
}


/**
 *  获取发货单列表信息
 *
 * @access  public
 * @param
 *
 * @return void
 */
function delivery_list()
{
    $result = get_filter();
    if ($result === false)
    {
        /* 过滤信息 */
        $filter['order_sn'] = empty($_REQUEST['order_sn']) ? '' : trim($_REQUEST['order_sn']);
        if (!empty($_GET['is_ajax']) && $_GET['is_ajax'] == 1)
        {
            $_REQUEST['consignee'] = json_str_iconv($_REQUEST['consignee']);
            //$_REQUEST['address'] = json_str_iconv($_REQUEST['address']);
        }

		/*add by hg for date 2014-04-23 获取代理商id begin*/
		$filter['admin_agency_id'] = (!empty($_REQUEST['admin_agency_id'])) ? $_REQUEST['admin_agency_id'] : 0;
		/*end*/
		/*add by ccx for date 2014-11-12 获取支付类型payment_method*/
		$filter['payment_method'] = empty($_REQUEST['payment_method']) ? '' : trim($_REQUEST['payment_method']);
		/*end*/	
		$filter['start_date'] = empty($_REQUEST['start_date']) ? local_strtotime('-360 days') : $_REQUEST['start_date'];
		$filter['end_date'] = empty($_REQUEST['end_date']) ? local_strtotime('today') : $_REQUEST['end_date'];
		if(strpos($filter['start_date'],'-') !== false)
		{
			$filter['start_date'] = local_strtotime($filter['start_date']);
			$filter['end_date'] = local_strtotime($filter['end_date']);
		}
		//dump(date('Y-m-d H-i-s',$filter['end_date']));
      
        $filter['pay_id'] = empty($_REQUEST['pay_id']) ? 0 : intval($_REQUEST['pay_id']);
        $filter['order_status'] = isset($_REQUEST['order_status']) ? intval($_REQUEST['order_status']) : -1;
        $filter['shipping_status'] = isset($_REQUEST['shipping_status']) ? intval($_REQUEST['shipping_status']) : -1;
        $filter['pay_status'] = isset($_REQUEST['pay_status']) ? intval($_REQUEST['pay_status']) : -1;
        $filter['user_id'] = empty($_REQUEST['user_id']) ? 0 : intval($_REQUEST['user_id']);
        $filter['user_name'] = empty($_REQUEST['user_name']) ? '' : trim($_REQUEST['user_name']);
        $filter['composite_status'] = isset($_REQUEST['composite_status']) ? intval($_REQUEST['composite_status']) : -1;
        $filter['group_buy_id'] = isset($_REQUEST['group_buy_id']) ? intval($_REQUEST['group_buy_id']) : 0;

        $filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'add_time' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

        $filter['start_time'] = empty($_REQUEST['start_time']) ? '' : (strpos($_REQUEST['start_time'], '-') > 0 ?  local_strtotime($_REQUEST['start_time']) : $_REQUEST['start_time']);
        $filter['end_time'] = empty($_REQUEST['end_time']) ? '' : (strpos($_REQUEST['end_time'], '-') > 0 ?  local_strtotime($_REQUEST['end_time']) : $_REQUEST['end_time']);

        $where = 'WHERE 1 ';
		/*add by hg for date 2014-04-22 只显示代理商本身所属订单 begin*/
		$where .= agency_where();
		/*end*/
        if ($filter['order_sn'])
        {
            $where .= " AND o.order_sn LIKE '%" . mysql_like_quote($filter['order_sn']) . "%'";
        }
        if ($filter['consignee'])
        {
            $where .= " AND o.consignee LIKE '%" . mysql_like_quote($filter['consignee']) . "%'";
        }
        if ($filter['email'])
        {
            $where .= " AND o.email LIKE '%" . mysql_like_quote($filter['email']) . "%'";
        }
        if ($filter['address'])
        {
            $where .= " AND o.address LIKE '%" . mysql_like_quote($filter['address']) . "%'";
        }
        if ($filter['zipcode'])
        {
            $where .= " AND o.zipcode LIKE '%" . mysql_like_quote($filter['zipcode']) . "%'";
        }
        if ($filter['tel'])
        {
            $where .= " AND o.tel LIKE '%" . mysql_like_quote($filter['tel']) . "%'";
        }
        if ($filter['mobile'])
        {
            $where .= " AND o.mobile LIKE '%" .mysql_like_quote($filter['mobile']) . "%'";
        }
        if ($filter['country'])
        {
            $where .= " AND o.country = '$filter[country]'";
        }
        if ($filter['province'])
        {
            $where .= " AND o.province = '$filter[province]'";
        }
        if ($filter['city'])
        {
            $where .= " AND o.city = '$filter[city]'";
        }
        if ($filter['district'])
        {
            $where .= " AND o.district = '$filter[district]'";
        }
        if ($filter['shipping_id'])
        {
            $where .= " AND o.shipping_id  = '$filter[shipping_id]'";
        }
        if ($filter['pay_id'])
        {
            $where .= " AND o.pay_id  = '$filter[pay_id]'";
        }
		/* ccx 2014-11-12  增加了对支付类型的搜索功能*/
		if ($filter['payment_method'])
        {
            //$where .= " AND o.pay_id  = '$filter[payment_method]'";
			if($filter['payment_method'] ==3)
			{
				$where .= " AND ( o.pay_id !=1 AND o.pay_id !=2 AND o.pay_id !=0)";
			}
			elseif($filter['payment_method'] ==1)
			{
				$where .= " AND o.pay_id  = 1";
			}
			elseif($filter['payment_method'] ==2) 
			{
				$where .= " AND o.pay_id  = 2";
			}			
        }
        if ($filter['order_status'] != -1)
        {
            $where .= " AND o.order_status  = '$filter[order_status]'";
        }
        if ($filter['shipping_status'] != -1)
        {
            $where .= " AND o.shipping_status = '$filter[shipping_status]'";
        }
        if ($filter['pay_status'] != -1)
        {
            $where .= " AND o.pay_status = '$filter[pay_status]'";
        }
        if ($filter['user_id'])
        {
            $where .= " AND o.user_id = '$filter[user_id]'";
        }
        if ($filter['user_name'])
        {
            $where .= " AND u.user_name LIKE '%" . mysql_like_quote($filter['user_name']) . "%'";
        }
		//对已进行收费确认的订单进行分润处理
		$where .= " AND o.order_status =5 AND o.shipping_status = 2 AND o.pay_status = 2 ";
		
		/*add by hg for date 2014-04-23 根据代理商筛选 begin*/	
		if(if_agency()){
			if (!empty($filter['admin_agency_id']))
			{
				if($filter['admin_agency_id'] != '-')
				{
					$where .= " AND o.admin_agency_id = $filter[admin_agency_id]";
				}else{
					$GLOBALS['smarty']->assign('show_agency',true);//查询整站订单
				}
			}
			else
			{
				$where .= " AND o.admin_agency_id != 0";
			}
		}
		/*end*/

        //综合状态
        switch($filter['composite_status'])
        {
            case CS_AWAIT_PAY :
                $where .= order_query_sql('await_pay');
                break;

            case CS_AWAIT_SHIP :
                $where .= order_query_sql('await_ship');
                break;

            case CS_FINISHED :
                $where .= order_query_sql('finished');
                break;

            case PS_PAYING :
                if ($filter['composite_status'] != -1)
                {
                    $where .= " AND o.pay_status = '$filter[composite_status]' ";
                }
                break;
            case OS_SHIPPED_PART :
                if ($filter['composite_status'] != -1)
                {
                    $where .= " AND o.shipping_status  = '$filter[composite_status]'-2 ";
                }
                break;
            default:
                if ($filter['composite_status'] != -1)
                {
                    $where .= " AND o.order_status = '$filter[composite_status]' ";
                }
        }

        /* 团购订单 */
        if ($filter['group_buy_id'])
        {
            $where .= " AND o.extension_code = 'group_buy' AND o.extension_id = '$filter[group_buy_id]' ";
        }

        /* 如果管理员属于某个办事处，只列出这个办事处管辖的订单 */
        $sql = "SELECT agency_id FROM " . $GLOBALS['ecs']->table('admin_user') . " WHERE user_id = '$_SESSION[admin_id]'";
        $agency_id = $GLOBALS['db']->getOne($sql);
        if ($agency_id > 0)
        {
            $where .= " AND o.agency_id = '$agency_id' ";
        }

        /* 分页大小 */
        $filter['page'] = empty($_REQUEST['page']) || (intval($_REQUEST['page']) <= 0) ? 1 : intval($_REQUEST['page']);

        if (isset($_REQUEST['page_size']) && intval($_REQUEST['page_size']) > 0)
        {
            $filter['page_size'] = intval($_REQUEST['page_size']);
        }
        elseif (isset($_COOKIE['ECSCP']['page_size']) && intval($_COOKIE['ECSCP']['page_size']) > 0)
        {
            $filter['page_size'] = intval($_COOKIE['ECSCP']['page_size']);
        }
        else
        {
            $filter['page_size'] = 15;
        }

        /* 记录总数 */
        if ($filter['user_name'])
        {
            $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('order_info') . " AS o ,".
                   $GLOBALS['ecs']->table('users') . " AS u " . $where;
        }
        else
        {
            $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('order_info') . " AS o ". $where;
        }
        $filter['record_count']   = $GLOBALS['db']->getOne($sql);

        /* 查询 */
        $sql = "SELECT o.order_id, o.order_sn,o.add_time, o.order_status, o.shipping_status, ".
		        "o.order_amount, o.money_paid,IFNULL(a.user_name, '主站') AS admin_user," .
                    "o.pay_status, o.consignee, o.address, o.email, o.tel, o.extension_code, o.extension_id, " .
                    "IFNULL(u.user_name, '" .$GLOBALS['_LANG']['anonymous']. "') AS buyer , o.is_separate, o.user_id , o.admin_agency_id ".
                " FROM " . $GLOBALS['ecs']->table('order_info') . " AS o " .
                " LEFT JOIN " .$GLOBALS['ecs']->table('users'). " AS u ON u.user_id=o.user_id LEFT JOIN ".$GLOBALS['ecs']->table('admin_user')." as a ON a.agency_user_id=o.admin_agency_id ". $where .
                " ORDER BY $filter[sort_by] $filter[sort_order] ".
                " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ",$filter[page_size]";

        foreach (array('order_sn', 'consignee', 'email', 'address', 'zipcode', 'tel', 'user_name') AS $val)
        {
            $filter[$val] = stripslashes($filter[$val]);
        }
        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }

    $row = $GLOBALS['db']->getAll($sql);
    //print_r($row);
    /* 格式话数据 */
    foreach ($row AS $key => $value)
    {
        $user_number = get_user_number($value['user_id']);
        //print_r($user_number); echo "<br>";
        $v2_message = 0;
        foreach ($user_number AS $k => $v1)
        {
            
            $user_rank = $v1['user_rank'];
            $top_rank = $v1['top_rank'];
            //echo $user_rank;echo "ab";echo "<br>";
            if($user_rank == 4 )
            {
                $v1_message = $v1['user_id'];  //判断是否是代理商
            } 
            //if($user_rank == 36 )    //内网的V2等级是36
            if($user_rank == 40 )      //外网的V2等级是40
            {
                $v2_message = 1 ;  //判断是V2  1：表示存在V2，就可以参与分润了
                $sql = "SELECT user_name FROM " . $GLOBALS['ecs']->table('users') . 
                       " WHERE user_id = " . $v1['user_id'];
                $user_name = $GLOBALS['db']->getOne($sql);
            }
            if($top_rank == $value['admin_agency_id'])
            {
                $v1_message = $v1['top_rank'];  //
            }
        }
        
        if($v1_message != $value['admin_agency_id'] || $v2_message != 1 )
        {
           //print_r($row[$key]);echo "<br>";
             unset($row[$key]);
             array_values($row);
             $filter['record_count'] = $filter['record_count'] -1;
             //$filter['page_count']     = $filter['record_count'] > 0 ? ceil($filter['record_count'] / $filter['page_size']) : 1;
        }
        
        else 
        {
            array_values($row);
            $sql = "SELECT el.order_id , el.money, g.goods_name, el.change_time FROM " . $GLOBALS['ecs']->table('earnings_log') . " AS el " .
                " LEFT JOIN " . $GLOBALS['ecs']->table('goods') . " AS g ON el.goods_id = g.goods_id " .
                " WHERE order_id = ".$value['order_id'];
            //$stat = $GLOBALS['db']->getRow($sql);
            $stat = $GLOBALS['db']->getAll($sql);
            $money_message = "";
            $count_money   = "";
            if($stat)
            {
                foreach ($stat AS $ab => $stat_value)
                {
                    $money_message = $money_message . $stat_value['goods_name']."：" . $stat_value['money']."&nbsp;";
                    $count_money   = $count_money + $stat_value['money'];
                    $change_time   = $stat_value['change_time'];
                }
                $row[$key]['stat_fenrun'] = "1";
                //$row[$key]['info'] = "V2(推广商)获得的分润：". $stat['money']."元"; 
                $row[$key]['info'] = "V2(推广商)".$user_name."获得的分润：". $count_money ."<br> (&nbsp; " .$money_message .')';    
                $row[$key]['change_time'] =  local_date('m-d H:i', $change_time);

            }
            else 
            {
                $row[$key]['stat_fenrun'] = "2";
                $row[$key]['info'] = "";       
            }
            
            $row[$key]['formated_order_amount'] = price_format($value['order_amount']);
            $row[$key]['formated_money_paid'] = price_format($value['money_paid']);
            $row[$key]['formated_total_fee'] = price_format($value['total_fee']);
            $row[$key]['short_order_time'] = local_date('m-d H:i', $value['add_time']);
            if ($value['order_status'] == OS_INVALID || $value['order_status'] == OS_CANCELED)
            {
                /* 如果该订单为无效或取消则显示删除链接 */
                $row[$key]['can_remove'] = 1;
            }
            else
            {
                $row[$key]['can_remove'] = 0;
            }
        }
    }
    $filter['page_count']     = $filter['record_count'] > 0 ? ceil($filter['record_count'] / $filter['page_size']) : 1;
	//print_r($row);
	//array_values($row);
    $arr = array('orders' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}

/**
 * 取得供货商列表
 * @return array    二维数组
 */
function get_suppliers_list()
{
    $sql = 'SELECT *
            FROM ' . $GLOBALS['ecs']->table('suppliers') . '
            WHERE is_check = 1
            ORDER BY suppliers_name ASC';
    $res = $GLOBALS['db']->getAll($sql);

    if (!is_array($res))
    {
        $res = array();
    }

    return $res;
}

function get_user_number($user_id)
{
    if ($user_id == 0)
    {
        return array();
    }

    $arr = $GLOBALS['db']->GetAll('SELECT user_id, user_rank, parent_id, top_rank FROM ' . $GLOBALS['ecs']->table('users'));

    if (empty($arr))
    {
        return array();
    }

    $index = 0;
    $cats  = array();
    while (1)
    {
        foreach ($arr AS $row)
        {
            if ($user_id == $row['user_id'])
            {
                $user_id = $row['parent_id'];

                $cats[$index]['user_id']   = $row['user_id'];
                $cats[$index]['user_rank'] = $row['user_rank'];
                $cats[$index]['top_rank']  = $row['top_rank'];
                $index++;
                break;
            }
        }

        if ($index == 0 || $user_id == 0)
        {
            break;
        }
    }

    return $cats;
}

?>