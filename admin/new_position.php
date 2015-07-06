<?php

/**
* 新广告管理程序
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . 'includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);
$exc   = new exchange($ecs->table("ad_new_position"), $db, 'id', 'position_name');


/* act操作项的初始化 */
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'list';
}
else
{
    $_REQUEST['act'] = trim($_REQUEST['act']);
}

/*------------------------------------------------------ */
//-- 广告列表页面
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    $pid = !empty($_REQUEST['pid']) ? intval($_REQUEST['pid']) : 0;
    $smarty->assign('ur_here',     $_LANG['ad_list']);
    $smarty->assign('action_link', array('text' => '添加广告位', 'href' => 'new_position.php?act=add'));
    $smarty->assign('pid',         $pid);
    $smarty->assign('full_page',  1);
    $position_list = get_position();
    $smarty->assign('position_list',    $position_list['ads']);
    $smarty->assign('filter',       	$position_list['filter']);
    $smarty->assign('record_count', 	$position_list['record_count']);
    $smarty->assign('page_count',   	$position_list['page_count']);

    assign_query_info();
    $smarty->display('new_position.htm');
}


/*------------------------------------------------------ */
//-- 添加新广告页面
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add')
{
    admin_priv('ad_manage');
    $smarty->assign('ur_here',       $_LANG['ads_add']);
    $smarty->assign('action_link',   array('href' => 'new_position.php?act=list', 'text' => $_LANG['ad_list']));
    $smarty->assign('form_act', 'insert');
    $smarty->assign('action',   'add');
    $smarty->assign('cfg_lang', $_CFG['lang']);
    $smarty->assign('ads', $ads);
    assign_query_info();
    $smarty->display('new_position_info.htm');
}

/*------------------------------------------------------ */
//-- 新广告的处理
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'insert')
{
    admin_priv('ad_manage');

	$adArr['position_name'] = isset($_POST['position']) ? trim($_POST['position']) : '';

	if(empty($adArr['position_name']))
		$error = '广告位名称不能为空';
	if(isset($error))
		sys_msg($error, 0, $link);
	//检查广告位是否重复
	if($db->getOne("SELECT id FROM ".$ecs->table('ad_new_position')." WHERE position_name = '$adArr[position_name]'"))
		sys_msg('广告位已存在', 0, $link);
		
	$db->autoExecute($ecs->table('ad_new_position'), $adArr, 'INSERT');
    /* 记录管理员操作 */
    admin_log($_POST['ad_name'].'(新广告)', 'add', 'ads_position');

    clear_cache_files(); // 清除缓存文件

    /* 提示信息 */
    $link[0]['text'] = '查看广告位列表';
    $link[0]['href'] = 'new_position.php?act=list';
    $link[1]['text'] = '继续添加广告';
    $link[1]['href'] = 'new_position.php?act=add';
    sys_msg($_LANG['add'] . "&nbsp;" .$_POST['ad_name'] . "&nbsp;" . $_LANG['attradd_succed'],0, $link);

}


/*------------------------------------------------------ */
//-- 编辑广告名称
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_ad_name')
{
    check_authz_json('ad_manage');

    $id      = intval($_POST['id']);
    $position_name = json_str_iconv(trim($_POST['val']));

    /* 检查广告名称是否重复 */
    if ($exc->num('position_name', $position_name, $id) != 0)
    {
        make_json_error(sprintf($_LANG['ad_name_exist'], $position_name));
    }
    else
    {
        if ($exc->edit("position_name = '$position_name'", $id))
        {
            admin_log($position_name,'edit','ads_position');
            make_json_result(stripslashes($position_name));
        }
        else
        {
            make_json_error($db->error());
        }
    }
}

/*------------------------------------------------------ */
//-- 删除广告位置
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    check_authz_json('ad_manage');
    $id = intval($_GET['id']);
    $exc->drop($id);
    admin_log('', 'remove', 'ads_position');
    $url = 'new_position.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);
    ecs_header("Location: $url\n");
    exit;
}

/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{   
	$position_list = get_position();
	
	$smarty->assign('position_list',     $position_list['ads']);
	$smarty->assign('filter',       	 $position_list['filter']);
	$smarty->assign('record_count', 	 $position_list['record_count']);
	$smarty->assign('page_count',   	 $position_list['page_count']);
	
	$sort_flag  = sort_flag($position_list['filter']);
	$smarty->assign($sort_flag['tag'], $sort_flag['img']);
	
	make_json_result($smarty->fetch('new_position.htm'), '',
	array('filter' => $position_list['filter'], 'page_count' => $position_list['page_count']));
	
	
}


/* 获取广告数据列表 */
function get_position()
{
    /* 过滤查询 */
	$position_name = !empty($_REQUEST['position_name'])? trim($_REQUEST['position_name']): '';
    $filter = array();
	$filter['position_name']    = $position_name;
    $filter['sort_by']    = empty($_REQUEST['sort_by']) ? 'position_name' : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
    $where = 'WHERE 1 ';
	if($position_name)
		$where .= ' AND position_name like \'%'.$position_name.'%\'';
    /* 获得总记录数据 */
    $sql = 'SELECT COUNT(*) FROM ' .$GLOBALS['ecs']->table('ad_new_position'). ' AS ad ' . $where;
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);
    $filter = page_and_size($filter);
    /* 获得广告数据 */
    $arr = array();
    $sql = 'SELECT id,position_name FROM ' .$GLOBALS['ecs']->table('ad_new_position'). ' AS ad ' . $where.
            'ORDER by '.$filter['sort_by'].' '.$filter['sort_order'];
    $res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);
    while ($rows = $GLOBALS['db']->fetchRow($res))
    {
        $arr[] = $rows;
    }
    return array('ads' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}

?>