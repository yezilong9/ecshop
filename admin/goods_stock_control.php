<?php

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . '/' . ADMIN_PATH . '/includes/lib_goods.php');
include_once(ROOT_PATH . '/includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);
$exc = new exchange($ecs->table('goods'), $db, 'goods_id', 'goods_name');


if ($_REQUEST['act'] == 'list' || $_REQUEST['act'] == 'trash')
{
    
	//print_r($_SESSION);die;
    $cat_id = empty($_REQUEST['cat_id']) ? 0 : intval($_REQUEST['cat_id']);
    $code   = empty($_REQUEST['extension_code']) ? '' : trim($_REQUEST['extension_code']);
    if(!$code)
    {
	admin_priv('remove_back');
    }
    $suppliers_id = isset($_REQUEST['suppliers_id']) ? (empty($_REQUEST['suppliers_id']) ? '' : trim($_REQUEST['suppliers_id'])) : '';
    $is_on_sale = isset($_REQUEST['is_on_sale']) ? ((empty($_REQUEST['is_on_sale']) && $_REQUEST['is_on_sale'] === 0) ? '' : trim($_REQUEST['is_on_sale'])) : '';

    $handler_list = array();
    $handler_list['virtual_card'][] = array('url'=>'virtual_card.php?act=card', 'title'=>$_LANG['card'], 'img'=>'icon_send_bonus.gif');
    $handler_list['virtual_card'][] = array('url'=>'virtual_card.php?act=replenish', 'title'=>$_LANG['replenish'], 'img'=>'icon_add.gif');
    $handler_list['virtual_card'][] = array('url'=>'virtual_card.php?act=batch_card_add', 'title'=>$_LANG['batch_card_add'], 'img'=>'icon_output.gif');

    if ($_REQUEST['act'] == 'list' && isset($handler_list[$code]))
    {
        $smarty->assign('add_handler',      $handler_list[$code]);
    }
    /* 供货商名 */
    $suppliers_list_name = suppliers_list_name();
    $suppliers_exists = 1;
    if (empty($suppliers_list_name))
    {
        $suppliers_exists = 0;
    }
    $smarty->assign('is_on_sale', $is_on_sale);
    $smarty->assign('suppliers_id', $suppliers_id);
    $smarty->assign('suppliers_exists', $suppliers_exists);
    $smarty->assign('suppliers_list_name', $suppliers_list_name);
    unset($suppliers_list_name, $suppliers_exists);

    /* 模板赋值 */
    $goods_ur = array('' => $_LANG['goods_stock_control'], 'virtual_card'=>$_LANG['50_virtual_card_list']);
    $ur_here = ($_REQUEST['act'] == 'list') ? $goods_ur[$code] : $_LANG['11_goods_trash'];
    $smarty->assign('ur_here', $ur_here);
	
    $smarty->assign('action_link',  $action_link);
    $smarty->assign('code',     $code);
    $smarty->assign('cat_list',     cat_list(0, $cat_id));
    $smarty->assign('brand_list',   get_brand_list());
    $smarty->assign('intro_list',   get_intro_list());
    $smarty->assign('lang',         $_LANG);
    $smarty->assign('list_type',    $_REQUEST['act'] == 'list' ? 'goods' : 'trash');
    $smarty->assign('use_storage',  empty($_CFG['use_storage']) ? 0 : 1);

    $suppliers_list = suppliers_list_info(' is_check = 1 ');
    $suppliers_list_count = count($suppliers_list);
    $smarty->assign('suppliers_list', ($suppliers_list_count == 0 ? 0 : $suppliers_list)); // 取供货商列表

    $goods_list = goods_list($_REQUEST['act'] == 'list' ? 0 : 1, ($_REQUEST['act'] == 'list') ? (($code == '') ? 1 : 0) : -1);
	
	//dump($goods_list);
    $smarty->assign('goods_list',   $goods_list['goods']);
    $smarty->assign('filter',       $goods_list['filter']);
    $smarty->assign('record_count', $goods_list['record_count']);
    $smarty->assign('page_count',   $goods_list['page_count']);
    $smarty->assign('full_page',    1);

    /* 排序标记 */
    $sort_flag  = sort_flag($goods_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    /* 获取商品类型存在规格的类型 */
    $specifications = get_goods_type_specifications();
    $smarty->assign('specifications', $specifications);
	
	/*end*/
	/* 重写预览链接 */
	$goods_url = and_agency_url()?'http://'.and_agency_url():'http://'.agency_url();
	$smarty->assign('url',         $goods_url);
    /* 显示商品列表页面 */
    assign_query_info();
    $htm_file = ($_REQUEST['act'] == 'list') ?
        'goods_stock_list.htm' : (($_REQUEST['act'] == 'trash') ? 'goods_trash.htm' : 'group_list.htm');
    $smarty->display($htm_file);
}


/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query' || $_REQUEST['act'] == 'special_query')
{
    $is_delete = empty($_REQUEST['is_delete']) ? 0 : intval($_REQUEST['is_delete']);
    $code = empty($_REQUEST['extension_code']) ? '' : trim($_REQUEST['extension_code']);
    
    if($_REQUEST['act'] == 'query')
    {
		$goods_list = goods_list($is_delete, ($code=='') ? 1 : 0);
		$tpl = $is_delete ? 'goods_trash.htm' : 'goods_stock_list.htm';
    }elseif($_REQUEST['act'] == 'special_query')
    {
		$goods_list = goods_list(0,1,' AND is_special=1 ');
		$tpl = $is_delete ? 'goods_trash.htm' : 'goods_special_list.htm';
    }
	
    $handler_list = array();
    $handler_list['virtual_card'][] = array('url'=>'virtual_card.php?act=card', 'title'=>$_LANG['card'], 'img'=>'icon_send_bonus.gif');
    $handler_list['virtual_card'][] = array('url'=>'virtual_card.php?act=replenish', 'title'=>$_LANG['replenish'], 'img'=>'icon_add.gif');
    $handler_list['virtual_card'][] = array('url'=>'virtual_card.php?act=batch_card_add', 'title'=>$_LANG['batch_card_add'], 'img'=>'icon_output.gif');

    if (isset($handler_list[$code]))
    {
        $smarty->assign('add_handler',      $handler_list[$code]);
    }
    $action_list = if_agency()?'all':'';
    $smarty->assign('all',         $action_list);
    $smarty->assign('code',         $code);
    $smarty->assign('goods_list',   $goods_list['goods']);
    $smarty->assign('filter',       $goods_list['filter']);
    $smarty->assign('record_count', $goods_list['record_count']);
    $smarty->assign('page_count',   $goods_list['page_count']);
    $smarty->assign('list_type',    $is_delete ? 'trash' : 'goods');
    $smarty->assign('use_storage',  empty($_CFG['use_storage']) ? 0 : 1);

    /* 排序标记 */
    $sort_flag  = sort_flag($goods_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    /* 获取商品类型存在规格的类型 */
    $specifications = get_goods_type_specifications();
    $smarty->assign('specifications', $specifications);

    make_json_result($smarty->fetch($tpl), '',
        array('filter' => $goods_list['filter'], 'page_count' => $goods_list['page_count']));
}


/*------------------------------------------------------ */
//-- 库存管理添加操作 add by ccx 2014-11-20
/*------------------------------------------------------ */
elseif ( $_REQUEST['act'] == 'edit_stock' )
{	//echo $_REQUEST['act'];
    $goods_id = $_REQUEST[goods_id]; 
	$smarty->assign('ur_here', $_LANG['goods_stock_control']);	
	$goods_name = $exc->get_name($goods_id);
	
	$sql = "SELECT goods_number, costing_price FROM ".
           $ecs->table('stock_control'). " WHERE goods_id='$goods_id' order by log_time desc ";
    $goods_stock = $db->GetRow($sql);
	$smarty->assign('goods_stock',$goods_stock);
	
    $smarty->assign('goods_name',$goods_name);
	$smarty->assign('goods_id',$goods_id);
	$smarty->assign('form_action',"insert");
    /* 显示商品信息页面 */
    assign_query_info();
    $smarty->display('goods_stock_info.htm');
}

elseif ( $_REQUEST['act'] == 'insert' )
{
	$goods_id        = $_REQUEST['goods_id']; 
	$goods_name      = $_REQUEST['goods_name']; 
	$goods_number    = $_REQUEST['goods_number'];
	$costing_price   = $_REQUEST['costing_price'];
    $stock_number    = $_REQUEST['stock_number'];
	
	
	
	$costing_message = $GLOBALS['db']->getAll("select id, costing_price,goods_number from " . $ecs->table('stock_control') ." where goods_id = $goods_id");
	if(!empty($costing_message[0]))
	{
		foreach($costing_message as $costing_key=>$costing_value){
			if($costing_value['costing_price'] == $costing_price) //判断价格是否有发生过，如果价格没有发生变化，直接修改库存表的库存数量就行了
			{
				$return_id            = $costing_value['id'];
				$return_costing_price = $costing_value['costing_price'];
				$return_goods_number  = $costing_value['goods_number'];
			}
		}
	}	
	
	if($return_id =="" )  //如果数据表没有一个返回的id,那么库存表就没有这条记录，就直接写入
	{
	$sql = "INSERT INTO " . $GLOBALS['ecs']->table('stock_control') . " (goods_id, goods_name, log_time, goods_number, costing_price )  VALUES ('" . $goods_id . "', '".$goods_name."', '". gmtime() ."', '" . $goods_number . "' , '".$costing_price."')";
    $GLOBALS['db']->query($sql);

	$stock_control_id = $GLOBALS['db']->insert_id();  //返回stock_stock_control 所产生的最新的id
	}
	else
	{
	$goods_number_new = $return_goods_number + $goods_number ; 
	$GLOBALS['db']->query("UPDATE " . $ecs->table('stock_control') . " SET log_time = '". gmtime() ."', goods_number = '". $goods_number_new ."' WHERE id='$return_id'");
	$stock_control_id = $return_id; 
	}
	
    $stock_type = 1 ;    //商品入库处理， 默认为 1（增加）  -1（减少）
	$stock_status = 1;   //1:添加入库产品，2：发货时候库存减少状态，3：库存不够的时候，4 退货的时候库存会增加状态）
	//写入相关的入库数据记录
	$sql_log = "INSERT INTO " . $GLOBALS['ecs']->table('stock_control_log') . " (stock_id, goods_name, log_time, goods_number, stock_type, costing_price, stock_number, stock_status, stock_note, ip_address )  VALUES ('" . $stock_control_id . "', '".$goods_name."', '".gmtime()."', '". $goods_number ."', '". $stock_type ."', '" . $costing_price . "' , '".$stock_number."' , '".$stock_status."', '".real_ip()."','". real_ip() ."')";
    $GLOBALS['db']->query($sql_log);
	
	//入库成功之后， 商品的总的库存数量也要相应的增加
	$goods_number_old  = $GLOBALS['db']->getOne("SELECT goods_number FROM ".$GLOBALS['ecs']->table('goods')." WHERE goods_id = $goods_id ");
	$goods_num = $goods_number_old + $goods_number ;
	if (update_goods($goods_id, 'goods_number', $goods_num))
	{
		//记录日志
		//admin_log($goods_id, 'update', 'goods');
	}
    /* 显示商品信息页面 */
    assign_query_info();
    
	$link[0]['text'] = $_LANG['add_success_message'];
    $link[0]['href'] = 'goods_stock_control.php?act=list';
    clear_cache_files();
    sys_msg($goods_name.$_LANG['succed_message'],0, $link);
	
}


?>