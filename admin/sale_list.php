<?php

/**
 *  销售明细列表程序
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www..com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: sale_list.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/lib_order.php');
require_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/admin/statistic.php');
require_once(ROOT_PATH . '/' . ADMIN_PATH . '/includes/lib_goods.php');
$smarty->assign('lang', $_LANG);

if (isset($_REQUEST['act']) && ($_REQUEST['act'] == 'query' ||  $_REQUEST['act'] == 'download'))
{
    /* 检查权限 */
    check_authz_json('sale_order_stats');
    if (strstr($_REQUEST['start_date'], '-') === false)
    {
        $_REQUEST['start_date'] = local_date('Y-m-d', $_REQUEST['start_date']);
        $_REQUEST['end_date'] = local_date('Y-m-d', $_REQUEST['end_date']);
    }
    /*------------------------------------------------------ */
    //--Excel文件下载
    /*------------------------------------------------------ */
    if ($_REQUEST['act'] == 'download')
    {
        $file_name = $_REQUEST['start_date'].'_'.$_REQUEST['end_date'] . '_sale';
        $goods_sales_list = get_sale_list(false);
        header("Content-type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=$file_name.xls");

        /* 文件标题 */
        echo ecs_iconv(EC_CHARSET, 'GB2312', $_REQUEST['start_date']. $_LANG['to'] .$_REQUEST['end_date']. $_LANG['sales_list']) . "\t\n";

        /* 商品名称,订单号,商品数量,销售价格,销售日期 */
        echo ecs_iconv(EC_CHARSET, 'GB2312', $_LANG['goods_name']) . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', $_LANG['order_sn']) . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', $_LANG['amount']) . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', $_LANG['sell_unit_price']) . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', $_LANG['order_total_price']) . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', $_LANG['costing_price']) . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', $_LANG['profit_total']) . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', $_LANG['sell_date']) . "\t\n";

        foreach ($goods_sales_list['sale_list_data'] AS $key => $value)
        {
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['goods_name']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', '[ ' . $value['order_sn'] . ' ]') . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['goods_num']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['unit_sales_price']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['goods_amount']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['order_costing_price']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['profit_total']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['sales_time']) . "\t";
            echo "\n";
        }
		
		echo ecs_iconv(EC_CHARSET, 'GB2312', $_LANG['total_sales_price'].':'.$goods_sales_list['statistics']['sales_price']. '  '.$_LANG['total_costing_price'].':'.$goods_sales_list['statistics']['costing_price']. '  '.$_LANG['total_profit_total'].':'.$goods_sales_list['statistics']['profit_total']) . "\t\n";
        exit;
    }
    $sale_list_data = get_sale_list();

    $smarty->assign('goods_sales_list',    $sale_list_data['sale_list_data']);
    $smarty->assign('filter',       $sale_list_data['filter']);
    $smarty->assign('record_count', $sale_list_data['record_count']);
    $smarty->assign('page_count',   $sale_list_data['page_count']);

    make_json_result($smarty->fetch('sale_list.htm'), '', array('filter' => $sale_list_data['filter'], 'page_count' => $sale_list_data['page_count']));
}
/*------------------------------------------------------ */
//--商品明细列表
/*------------------------------------------------------ */
else
{
    /* 权限判断 */
    admin_priv('sale_order_stats');
    /* 时间参数 */
    if (!isset($_REQUEST['start_date']))
    {
        $start_date = local_strtotime('-7 days');
    }
    if (!isset($_REQUEST['end_date']))
    {
        $end_date = local_strtotime('today');
    }
    
    $sale_list_data = get_sale_list();

    /* 赋值到模板 */
    $smarty->assign('filter',       $sale_list_data['filter']);
    $smarty->assign('record_count', $sale_list_data['record_count']);
    $smarty->assign('page_count',   $sale_list_data['page_count']);
    $smarty->assign('goods_sales_list', $sale_list_data['sale_list_data']);
    $smarty->assign('ur_here',          $_LANG['sell_stats']);
    $smarty->assign('full_page',        1);
    $smarty->assign('start_date',       local_date('Y-m-d', $start_date));
    $smarty->assign('end_date',         local_date('Y-m-d', $end_date));
    $smarty->assign('ur_here',      $_LANG['sale_list']);
    $smarty->assign('cfg_lang',     $_CFG['lang']);
    $smarty->assign('action_link',  array('text' => $_LANG['down_sales'],'href'=>'#download'));

    /* 显示页面 */
    assign_query_info();
    $smarty->display('sale_list.htm');
}
/*------------------------------------------------------ */
//--获取销售明细需要的函数
/*------------------------------------------------------ */
/**
 * 取得销售明细数据信息
 * @param   bool  $is_pagination  是否分页
 * @return  array   销售明细数据
 */
function get_sale_list($is_pagination = true){
    /* 时间参数 */
    $filter['start_date'] = empty($_REQUEST['start_date']) ? local_strtotime('-7 days') : local_strtotime($_REQUEST['start_date']);
    $filter['end_date'] = empty($_REQUEST['end_date']) ? local_strtotime('today') : local_strtotime($_REQUEST['end_date']);
	
	/*add by hg for date 2014-04-23 获取代理商信息 begin*/
	$filter['admin_agency_id'] = (!empty($_REQUEST['admin_agency_id'])) ? $_REQUEST['admin_agency_id'] :'0';
	/*add by ccx for date 2014-11-12 获取支付类型payment_method*/
	$filter['payment_method'] = empty($_REQUEST['payment_method']) ? '' : trim($_REQUEST['payment_method']);
	/*end*/	
	
	$res = agency_list();
	$agency_list = array('-' => '全站');
	foreach($res as $re_k=>$res_v){
		$agency_list[$re_k]= $res_v;
	}
    $GLOBALS['smarty']->assign('agency_list',   $agency_list);
	$GLOBALS['smarty']->assign('admin_agency_id',         $filter['admin_agency_id']);
	$action_list = if_agency() ? 'all' : '';
	$GLOBALS['smarty']->assign('all', $action_list);
	/*end*/
	
	/*add by hg for date 2014-04-22		加入代理商条件*/
	$agency_where = agency_where();
	if(!empty($agency_where))
	{
		$whereArr = explode(' ',$agency_where);
		$sale_where =$whereArr[0].$whereArr[1].' oi.'.$whereArr[2].$whereArr[3].$whereArr[4];
	}
	/*end*/

    /* 查询数据的条件 */
    $where = " WHERE og.order_id = oi.order_id". order_query_sql('finished', 'oi.') .
             " AND oi.add_time >= '".$filter['start_date']."' 
               AND oi.add_time < '" . ($filter['end_date'] + 86400) . "'$sale_where";
			 
	/*add by hg for date 2014-04-23 根据代理商筛选  begin*/
    if (!empty($filter['admin_agency_id']) && if_agency())
    {
		if($filter['admin_agency_id'] != '-')
		{
			$where .= " AND oi.admin_agency_id = ".$filter['admin_agency_id'];
		} 
    } elseif (if_agency()){
		$where .= " AND admin_agency_id = '0' ";
	}

	/*en
	/*end*/
	/*add by ccx for date 2014-11-12  根据选择支付类型(余额支付,货到付款,在线支付三种)筛选  begin*/
	if ($filter['payment_method'])
	{
		//$where .= " AND o.pay_id  = '$filter[payment_method]'";
		if($filter['payment_method'] ==3)
		{
			$where .= " AND ( oi.pay_id !=1 AND oi.pay_id !=2 AND oi.pay_id !=0)";
		}
		elseif($filter['payment_method'] ==1)   
		{
			$where .= " AND oi.pay_id  = 1";
		}
		elseif($filter['payment_method'] ==2)
		{
			$where .= " AND oi.pay_id  = 2";
		}		
	}	
	/*end*/
    $sql = "SELECT COUNT(og.goods_id) FROM " .
           $GLOBALS['ecs']->table('order_info') . ' AS oi,'.
           $GLOBALS['ecs']->table('order_goods') . ' AS og '.
           $where;
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);

    /* 分页大小 */
    $filter = page_and_size($filter);
	
	/* modify by SouthBear 2014-12-04 16:13:04
	* 增加 oi.order_amount, oi.goods_amount order_amount 字段用途不明，TABLE中无数据
	*/
    $sql = 'SELECT og.goods_id, og.costing_price,og.goods_sn, og.goods_name, og.goods_number AS goods_num, 
    		og.goods_price AS sales_price, oi.add_time AS sales_time, oi.order_id, oi.order_sn,oi.order_amount,
    		oi.goods_amount , og.stock_costing_price '.
           "FROM " . $GLOBALS['ecs']->table('order_goods')." AS og, ".$GLOBALS['ecs']->table('order_info')." AS oi ".
           $where. " ORDER BY sales_time DESC, goods_num DESC";
	$statistics_sql = $sql;
    if ($is_pagination)
    {
        $sql .= " LIMIT " . $filter['start'] . ', ' . $filter['page_size'];
    }
    $sale_list_data = $GLOBALS['db']->getAll($sql);

	/* modify by SouthBear 2014-12-04 16:13:04
	* 将订单成本和订单金额重新计算
	*/
	//if (count($sale_list_data) > 0) {
	    foreach ($sale_list_data as $key => $item)
	    {
	        //$sale_list_data[$key]['profit_total'] = price_format(($sale_list_data[$key]['sales_price'] - $sale_list_data[$key]['costing_price'])*$sale_list_data[$key]['goods_num']); //利润
	        /*ccx 2014-12-10 订单商品的利润*/
	        $sale_list_data[$key]['profit_total'] = price_format(($sale_list_data[$key]['sales_price']*$sale_list_data[$key]['goods_num']) - $sale_list_data[$key]['stock_costing_price']) ; //利润
	        $sale_list_data[$key]['unit_sales_price'] = price_format($sale_list_data[$key]['sales_price']); //订单单价
	        //成本
	        $sale_list_data[$key]['unit_costing_price'] = price_format($sale_list_data[$key]['costing_price']); //成本单价
	        //$sale_list_data[$key]['order_costing_price'] = price_format($sale_list_data[$key]['costing_price'] * $sale_list_data[$key]['goods_num']); //订单成本
	        /*ccx 2014-12-10 ccx 读取订单商品表新建的库存商品成本总价*/
	        $sale_list_data[$key]['order_costing_price'] = $sale_list_data[$key]['stock_costing_price']; //订单成本总价
	
	        
	        //订单总金额
	        $sale_list_data[$key]['goods_amount'] = price_format($sale_list_data[$key]['sales_price'] * $sale_list_data[$key]['goods_num']);  
	        $sale_list_data[$key]['sales_time']  = local_date($GLOBALS['_CFG']['time_format'], $sale_list_data[$key]['sales_time']);
	    }
		$statistics = statistics($statistics_sql);	
    	$arr = array('sale_list_data' => $sale_list_data, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count'],'statistics'=>$statistics);
	//} else {
	//	$arr = false;
	//}
    return $arr;
}

/**
* 统计销售金额成本等信息
* @sql sql语句
**/
function statistics($sql)
{
	
    $sale_list_data = $GLOBALS['db']->getAll($sql);
	$statistics = array('profit_total'=>'','sales_price'=>'','costing_price'=>'');
    foreach ($sale_list_data as $key => $item)
    {
		//一定时间段内的总数据
		//$sale_list_data[$key]['profit_total'] = ($sale_list_data[$key]['sales_price'] - $sale_list_data[$key]['costing_price'])*$sale_list_data[$key]['goods_num'];
		/*ccx 2014-12-10 订单利润统计 */
		$sale_list_data[$key]['profit_total'] = $sale_list_data[$key]['sales_price'] *$sale_list_data[$key]['goods_num'] - $sale_list_data[$key]['stock_costing_price'];
		$statistics['profit_total']   += $sale_list_data[$key]['profit_total'];  //利润
		$statistics['sales_price']    += $sale_list_data[$key]['sales_price']*$sale_list_data[$key]['goods_num'];
		//$statistics['costing_price']  += $sale_list_data[$key]['costing_price']*$sale_list_data[$key]['goods_num'];
		/*ccx 2014-12-10 成本的合计  stock_costing_price 字段记录的已经是该商品总的成本(单个*数量)*/
		$statistics['costing_price']  += $sale_list_data[$key]['stock_costing_price'];
    }
	$statistics['profit_total'] = price_format($statistics['profit_total']);
	$statistics['sales_price'] = price_format($statistics['sales_price']);
	$statistics['costing_price'] = price_format($statistics['costing_price']);
	
	$GLOBALS['smarty']->assign('profit_total',   $statistics['profit_total']);
	$GLOBALS['smarty']->assign('sales_price',   $statistics['sales_price']);
	$GLOBALS['smarty']->assign('costing_price',   $statistics['costing_price']);
	
	return $statistics;
}
?>