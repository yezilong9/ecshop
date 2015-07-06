<?php

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . '/' . ADMIN_PATH . '/includes/lib_goods.php');
include_once(ROOT_PATH . '/includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);
$exc = new exchange($ecs->table('goods'), $db, 'goods_id', 'goods_name');


if ($_REQUEST['act'] == 'list' || $_REQUEST['act'] == 'trash')
{
    admin_priv('stock_control');
    //print_r($_SESSION);die;
    $cat_id = empty($_REQUEST['cat_id']) ? 0 : intval($_REQUEST['cat_id']);
    $code   = empty($_REQUEST['extension_code']) ? '' : trim($_REQUEST['extension_code']);
    if(!$code)
    {
    //admin_priv('remove_back');
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
    $smarty->display('goods_stock_list_new.htm');
}


/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query' || $_REQUEST['act'] == 'special_query')
{
    $is_delete = empty($_REQUEST['is_delete']) ? 0 : intval($_REQUEST['is_delete']);
    $code = empty($_REQUEST['extension_code']) ? '' : trim($_REQUEST['extension_code']);
    check_authz_json('stock_control');
    if($_REQUEST['act'] == 'query')
    {
        $goods_list = goods_list($is_delete, ($code=='') ? 1 : 0);
        $tpl = $is_delete ? 'goods_trash.htm' : 'goods_stock_list_new.htm';
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



elseif ($_REQUEST['act'] == 'batch')  //批量对商品进行采购处理
{
    admin_priv('stock_control');
    if ($_POST['type'] == 'batch_stock_goods')
    {
        $goods_id_arr = $_POST['checkboxes'];
        $link[] = array('href' => 'goods_stock_control_new.php?act=list', 'text' => $_LANG['batch_stock_goods']);
        if(@!array_filter($goods_id_arr) || !array_filter($goods_id_arr) )
        {
            sys_msg('没有填写价格或者没有勾选产品', 1, $link);
        }
        /*add by ccx  for date 2014-11-27  记录采购订单是代理商添加的还是主站后台添加的 ID */
        $admin_id = $_SESSION['admin_id'];
        $adminRow = $db->getRow("select agency_user_id from " . $ecs->table('admin_user') . "where user_id = $admin_id");
        $admin_agency_id = $adminRow['agency_user_id'];
        $GLOBALS['db']->begin();   //开始事务    
        foreach($goods_id_arr as $key=>$value)
        {
            $id  = $db->getOne("SELECT id FROM ".$ecs->table('stock_goods')." WHERE goods_id = $value and stock_status !=3 limit 1 " );
            if($id > 0)
            {
                $GLOBALS['db']->rollback();
                sys_msg("在采购商品列表当中已经存在您刚刚选择的商品了，请检查再进行添加", 1, $links);
            }
            $goods_name  = $db->getOne("SELECT goods_name FROM ".$ecs->table('goods')." WHERE goods_id = $value");
            $db->query("INSERT INTO ".$ecs->table('stock_goods')." (goods_id,goods_name,admin_agency_id ) VALUES ".
            "($value,'".$goods_name."', '".$admin_agency_id."')");
        }
        $GLOBALS['db']->commit();
    }
    
    else
    {
        sys_msg('请选择批量采购商品选项操作', 1, $link);
    }
    clear_cache_files();   
    $url = 'goods_stock_list.php?act=list';
    ecs_header("Location: $url\n");
}


?>