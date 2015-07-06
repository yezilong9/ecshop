<?php
/**
* 说明:APP版本信息管理
* author：hg
* time:2014-09-23
**/
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
$exc   = new exchange($ecs->table("version"), $db, 'id', 'ad_name');
/*------------------------------------------------------ */
//-- 版本信息列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
	admin_priv('app_version');
    $smarty->assign('ur_here',     '版本管理');
    $smarty->assign('action_link', array('text' => '添加新版本号', 'href' => 'app_version.php?act=add'));
    $smarty->assign('full_page',  1);
    $version_list = version();
    $smarty->assign('version_list', $version_list['version']);
    $smarty->assign('filter',       $version_list['filter']);
    $smarty->assign('record_count', $version_list['record_count']);
    $smarty->assign('page_count',   $version_list['page_count']);

    assign_query_info();
    $smarty->display('app_version.htm');
}
/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{   
	$version_list = version();
	
	$smarty->assign('version_list',     $version_list['version']);
	$smarty->assign('filter',       $version_list['filter']);
	$smarty->assign('record_count', $version_list['record_count']);
	$smarty->assign('page_count',   $version_list['page_count']);
	
	$sort_flag  = sort_flag($version_list['filter']);
	$smarty->assign($sort_flag['tag'], $sort_flag['img']);
	make_json_result($smarty->fetch('app_version.htm'), '',
	array('filter' => $version_list['filter'], 'page_count' => $version_list['page_count']));

}
/*------------------------------------------------------ */
//-- 添加版本号页面
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add')
{
    admin_priv('app_version');
	//代理ID

    $smarty->assign('ur_here',       '添加新版本');
    $smarty->assign('action_link',   array('href' => 'app_version.php?act=list', 'text' => '版本列表'));
    $smarty->assign('form_act', 'insert');
    $smarty->assign('action',   'add');
	$ads['start'] = 1;
    $smarty->assign('ads', $ads);
	$smarty->assign('start_date', local_date('Y-m-d', time()));

    assign_query_info();
    $smarty->display('app_version_info.htm');
}
/*------------------------------------------------------ */
//-- 版本号处理
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'insert')
{
    admin_priv('app_version');

	$adArr['version'] = isset($_POST['version']) ? trim($_POST['version']) : '';
	$adArr['content'] = isset($_POST['content']) ? trim($_POST['content']) : '';
	$adArr['update_time'] = isset($_POST['start_date'])     ? strtotime($_POST['start_date']) : '';

	if(empty($adArr['version']))
		$error = '版本号不能为空';
	elseif(empty($adArr['content']))
		$error = '版本更新说明不能为空';

	if(isset($error))
		sys_msg($error, 0, $link);
	$adArr['time'] = time();
	$db->autoExecute($ecs->table('version'), $adArr, 'INSERT');
    /* 记录管理员操作 */
    admin_log('', '', '','增加APP版本号：'.$adArr['version']);
	// 清除缓存文件
    clear_cache_files(); // 清除缓存文件
    /* 提示信息 */
    $link[0]['text'] = '查看版本列表';
    $link[0]['href'] = 'app_version.php?act=list';
    sys_msg($_LANG['add'] . "&nbsp;" .$_POST['version'] . "&nbsp;" . $_LANG['attradd_succed'],0, $link);
}

/*------------------------------------------------------ */
//-- 版本编辑页面
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit')
{
    admin_priv('app_version');
    /* 获取广告数据 */
    $sql = "SELECT id,version,content,update_time FROM " .$ecs->table('version'). " WHERE id ='".intval($_REQUEST['id'])."'";
    $version_arr = $db->getRow($sql);
	//广告位
	$smarty->assign('version_arr',$version_arr);
	$smarty->assign('start_date',date('Y-m-d',$version_arr['update_time']));
    $smarty->assign('ur_here',       '编辑版本信息');
    $smarty->assign('action_link',   array('href' => 'app_version.php?act=list', 'text' => '版本列表'));
    $smarty->assign('form_act',      'update');
    $smarty->assign('action',        'edit');
    assign_query_info();
    $smarty->display('app_version_info.htm');
}

/*------------------------------------------------------ */
//-- 版本信息编辑的处理
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'update')
{
    admin_priv('app_version');
	$id = intval($_POST['id']);
	$adArr['version'] = isset($_POST['version']) ? trim($_POST['version']) : '';
	$adArr['content'] = isset($_POST['content']) ? trim($_POST['content']) : '';
	$adArr['update_time'] = isset($_POST['start_date'])     ? strtotime($_POST['start_date']) : '';

	if(empty($adArr['version']))
		$error = '版本号不能为空';
	elseif(empty($adArr['content']))
		$error = '版本更新说明不能为空';

	if(isset($error))
		sys_msg($error, 0, $link);
	
	$db->autoExecute($ecs->table('version'), $adArr, 'update',"id = $id");

   /* 记录管理员操作 */
   admin_log('', '', '','编辑APP版本信息：'.$adArr['version']);

   clear_cache_files(); // 清除模版缓存

   /* 提示信息 */
   $href[] = array('text' => '广告列表', 'href' =>'app_version.php?act=list');
   sys_msg($_LANG['edit'] .' '.$_POST['version'].' '. $_LANG['attradd_succed'], 0, $href);
}
/*------------------------------------------------------ */
//-- 删除版本
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    check_authz_json('ad_manage');

    $id = intval($_GET['id']);
    $version = $exc->get_name($id, 'version');
    $exc->drop($id);
    admin_log('', '', '','删除APP版本信息：'.$version);

    $url = 'app_version.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}
/* 获取版本数据列表 */
function version()
{
    $filter = array();
    /* 获得总记录数据 */
    $sql = 'SELECT COUNT(*) FROM ' .$GLOBALS['ecs']->table('version');
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);
    $filter = page_and_size($filter);
	//dump($filter);
    /* 获得广告数据 */
    $arr = array();
    $sql = 'SELECT id,version,content,update_time FROM ' .$GLOBALS['ecs']->table('version').' ORDER BY id DESC';
    $res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);
    while ($rows = $GLOBALS['db']->fetchRow($res))
    {
		$rows['update_time'] = local_date('Y-m-d', $rows['update_time']);
		if(strlen($rows['content'])>40)
		$rows['content'] = mb_substr($rows['content'],0,40,'utf-8').'...';
		
        $arr[] = $rows;
    }
    return array('version' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}
?>