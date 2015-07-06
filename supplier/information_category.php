<?php

/**
 * ECSHOP 首页资讯信息中的资讯分类名称处理
 * ============================================================================
 * * 版权所有 2005-2012 广州新泛联数码有限公司，并保留所有权利。
 * 网站地址: http://www..comm；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: zenghd $
 * $Id: information_category.php  2014-09-11 10:29:08Z zenghd $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
$exc   = new exchange($ecs->table("information_category"), $db, 'info_cat_id','info_cat_name');
//echo admin_agency_id();exit;
//echo $_REQUEST['act'];

/*------------------------------------------------------ */
//-- 资讯分类名称列表页面
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    $smarty->assign('ur_here',     '资讯分类列表');
    $smarty->assign('action_link', array('text' => '添加资讯分类', 'href' => 'information_category.php?act=add'));
    $smarty->assign('full_page',  1);

    $info_cats_list = get_info_cats_list();

    $smarty->assign('info_cats_list',     $info_cats_list['info_cats']);
    $smarty->assign('filter',       $info_cats_list['filter']);
    $smarty->assign('record_count', $info_cats_list['record_count']);
    $smarty->assign('page_count',   $info_cats_list['page_count']);

    $sort_flag  = sort_flag($info_cats_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);
//dump($sort_flag);
    assign_query_info();
    $smarty->display('information_category_list.htm');
}


/*------------------------------------------------------ */
//-- 添加新资讯类别名称
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add')
{	
	admin_priv('information_category');
	
	$smarty->assign('ur_here',       '添加资讯分类');
    $smarty->assign('action_link',   array('href' => 'information_category.php?act=list', 'text' => '资讯分类列表'));
    //$smarty->assign('position_list', get_position_list());
	$info_cats['is_show'] = 1;
	$smarty->assign('info_cats', $info_cats);
    $smarty->assign('form_act', 'insert');
	$smarty->assign('admin_agency_id',admin_agency_id());
   
    assign_query_info();
    $smarty->display('information_category.htm');
}

/*------------------------------------------------------ */
//-- 新资讯类别名称的处理
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'insert')
{
    admin_priv('information_category');

    /* 初始化变量 */
	$info_cat = array();
	$info_cat['info_cat_name'] = !empty($_POST['info_cat_name']) ? trim($_POST['info_cat_name']) : '';
    $info_cat['show_order'] = !empty($_POST['show_order']) ? intval($_POST['show_order']) : 0;
    $info_cat['is_show'] = isset($_POST['is_show']) ? intval($_POST['is_show'])  : 1;
    $info_cat['admin_agency_id'] = isset($_POST['admin_agency_id']) ? intval($_POST['admin_agency_id']) : admin_agency_id();
	
	if(empty($info_cat['info_cat_name']))
	{
		$error = '资讯分类名称不能为空！';
	}
	elseif(empty($info_cat['show_order']))
	{
		$error = '显示顺序必须为一个整数！';
	}
	
	/* 提示信息 */
	$link[0]['text'] = '管理资讯类别';
    $link[0]['href'] = 'information_category.php?act=list';
    $link[1]['text'] = '继续添加资讯类别';
    $link[1]['href'] = 'information_category.php?act=add';
	if(isset($error))
		sys_msg($error, 0, $link,false);
	
	$sql_article_cat = "SELECT cat_id FROM ".$ecs->table('article_cat')." WHERE cat_name = '".$info_cat['info_cat_name']."' AND admin_agency_id = ".$info_cat['admin_agency_id'];//查询该分类名称是否已经存在于文章分类名称article_cat表中,并且代理商系统
	
	$sql_info_cat = "SELECT info_cat_id FROM ".$ecs->table('information_category')." WHERE info_cat_name = '".$info_cat['info_cat_name']."'" . " AND admin_agency_id = ".$info_cat['admin_agency_id'];//查询该分类名称是否已经存在于资讯分类名称information_category表中,并且代理商系统
	$article_cat_id = $db->getOne($sql_article_cat);
	$info_cat_id = $db->getOne($sql_info_cat);
	
	//如果该名称已经存在article_cat表中和information_category表中
	if(empty($article_cat_id) && empty($info_cat_id)){
		$sql_insert_article_cat = "INSERT INTO " . $ecs->table('article_cat') . " (cat_name, cat_desc,admin_agency_id) VALUES ('$info_cat[info_cat_name]','本文章分类名称是与资讯信息相关的，请不要随便修改或删除！如果要修改或删除该分类名称，请在资讯分类中进行编辑或删除。',$info_cat[admin_agency_id])";
		
		$db->query($sql_insert_article_cat);
		$insert_cat_id = $db->insert_id();
		$info_cat['article_cat_id'] = $insert_cat_id;
		$db->autoExecute($ecs->table('information_category'), $info_cat, 'INSERT');
	}elseif(!empty($article_cat_id) && empty($info_cat_id)){
		$info_cat['article_cat_id'] = $cat_id;
		$db->autoExecute($ecs->table('information_category'), $info_cat, 'INSERT');
	}elseif(!empty($info_cat_id)){
		sys_msg('本资讯类别名称已存在！', 0, $link,false);
		
	}
	
	/* 记录管理员操作 */
    admin_log($_POST['info_cat'], 'add', 'information_category');

    clear_cache_files(); // 清除缓存文件

    /* 提示信息 */
    sys_msg($_LANG['add'] . "&nbsp;" .$info_cat['info_cat_name'] . "&nbsp;" . $_LANG['attradd_succed'],0, $link,false);
}

/*------------------------------------------------------ */
//-- 资讯类别名称编辑页面
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit')
{
   admin_priv('information_category');
    /* 获取广告数据 */
    $sql = "SELECT * FROM " .$ecs->table('information_category'). " WHERE info_cat_id='".intval($_REQUEST['info_cat_id'])."'";
    $info_cats = $db->getRow($sql);

    $info_cats['info_cat_name'] = htmlspecialchars($info_cats['info_cat_name']);
    
	//print_r($info_cats);
    $smarty->assign('ur_here',       '编辑资讯类别');
    $smarty->assign('action_link',   array('href' => 'information_category.php?act=list', 'text' => '资讯类别列表'));
    $smarty->assign('form_act',      'update');
    $smarty->assign('info_cats', $info_cats);
	$smarty->assign('admin_agency_id',$info_cats['admin_agency_id']);

    assign_query_info();
    $smarty->display('information_category.htm');
}

/*------------------------------------------------------ */
//-- 资讯类别名称编辑的处理
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'update')
{
    admin_priv('information_category');

    /* 初始化变量 */
    $info_cat_id = !empty($_POST['info_cat_id']) ? intval($_POST['info_cat_id']) : 0;
    $article_cat_id = !empty($_POST['article_cat_id']) ? intval($_POST['article_cat_id']) : 0;
	$info_cat['info_cat_name'] = !empty($_POST['info_cat_name']) ? trim($_POST['info_cat_name']) : '';
    $info_cat['show_order'] = !empty($_POST['show_order']) ? intval($_POST['show_order']) : 0;
    $info_cat['is_show'] = isset($_POST['is_show']) ? intval($_POST['is_show'])  : 1;
    $info_cat['admin_agency_id'] = isset($_POST['admin_agency_id']) ? intval($_POST['admin_agency_id']) : admin_agency_id();
	if(empty($info_cat['info_cat_name']))
	{
		$error = '资讯分类名称不能为空！';
	}
	elseif(empty($info_cat['show_order']))
	{
		$error = '显示顺序必须为一个整数！';
	}
	
	/* 提示信息 */
	$link[0]['text'] = '管理资讯类别列表';
    $link[0]['href'] = 'information_category.php?act=list';
    $link[1]['text'] = '继续添加资讯类别';
    $link[1]['href'] = 'information_category.php?act=add';
	if(isset($error))
		sys_msg($error, 0, $link,false);
	
	$sql_article_cat = "SELECT cat_id FROM ".$ecs->table('article_cat')." WHERE cat_name = '".$info_cat['info_cat_name']."' AND cat_id <> $article_cat_id AND admin_agency_id = ".$info_cat['admin_agency_id'];//查询该分类名称是否已经存在于文章分类名称article_cat表中,并且代理商系统
	
	$sql_info_cat = "SELECT info_cat_id FROM ".$ecs->table('information_category')." WHERE info_cat_name = '".$info_cat['info_cat_name']."'" . " AND info_cat_id <> $info_cat_id AND admin_agency_id = ".$info_cat['admin_agency_id'];//查询该分类名称是否已经存在于资讯分类名称information_category表中,并且代理商系统
	$get_article_cat_id = $db->getOne($sql_info_cat);
	$get_info_cat_id = $db->getOne($sql_info_cat);
	
	//如果该名称已经存在article_cat表中和information_category表中
	if(empty($get_article_cat_id) && empty($get_info_cat_id)){
		$sql_update_article_cat = "UPDATE ".$ecs->table('article_cat')." SET cat_name = '$info_cat[info_cat_name]' WHERE cat_id = $article_cat_id";//修改article_cat表中该名称对应的分类描述
		$db->query($sql_update_article_cat);
		$db->autoExecute($ecs->table('information_category'), $info_cat,'update',"info_cat_id = $info_cat_id");
	}elseif(!empty($get_article_cat_id) && empty($get_info_cat_id)){
		$sql_update_article_cat = "UPDATE ".$ecs->table('article_cat')." SET cat_desc = '本文章分类名称是与资讯信息相关的，请不要随便修改或删除！如果要修改或删除该分类名称，请在资讯分类中进行编辑或删除。' WHERE cat_id = $article_cat_id ";//修改article_cat表中该名称对应的分类描述
		$info_cat['article_cat_id'] = $article_cat_id;//把information_category表中该行对应的article_cat_id改为article_cat表中cat_id的值
		$db->autoExecute($ecs->table('information_category'), $info_cat, 'update',"info_cat_id = $info_cat_id");
	
	}elseif(!empty($get_info_cat_id)){
		sys_msg('本资讯类别名称已存在！', 0, $link,false);
	}
	
   /* 记录管理员操作 */
   admin_log($_POST['ad_name'], 'edit', 'ads');

   clear_cache_files(); // 清除模版缓存

   /* 提示信息 */
   sys_msg($_LANG['edit'] .' '.$info_cat['info_cat_name'].' '. $_LANG['attradd_succed'], 0, $link);

}

/*------------------------------------------------------ */
//-- 删除资讯类别名称
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'remove')
{
    check_authz_json('information_category');

    $id = intval($_GET['id']);
	$sql_article_cat_id = "SELECT article_cat_id FROM ".$ecs->table('information_category')." WHERE info_cat_id = $id ";
	$article_cat_id = $db->getOne($sql_article_cat_id);
	echo $sql_delete_article_cat = "DELETE FROM ".$ecs->table('article_cat')." WHERE cat_id = $article_cat_id ";
	if($db->query($sql_delete_article_cat))$exc->drop($id);

    admin_log('', 'remove', 'information_category');

    $url = 'information_category.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}

/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{   
	$info_cats_list = get_info_cats_list();
	
	$smarty->assign('info_cats_list',     $info_cats_list['info_cats']);
	$smarty->assign('filter',      $info_cats_list['filter']);
	$smarty->assign('record_count', $info_cats_list['record_count']);
	$smarty->assign('page_count',   $info_cats_list['page_count']);
	
	$sort_flag  = sort_flag($info_cats_list['filter']);
	$smarty->assign($sort_flag['tag'], $sort_flag['img']);
	
	make_json_result($smarty->fetch('information_category_list.htm'), '',
	array('filter' => $info_cats_list['filter'], 'page_count' => $info_cats_list['page_count']));
	
}


/* 获取资讯类别名称数据列表 */
function get_info_cats_list()
{
    /* 过滤查询 */
	$info_cat_word = !empty($_REQUEST['info_cat_word'])? trim($_REQUEST['info_cat_word']): '';

    $filter = array();
	$filter['info_cat_word']    = $info_cat_word;
	$filter['sort_by']    = empty($_REQUEST['sort_by']) ? 'info_cat_id' : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'ASC' : trim($_REQUEST['sort_order']);
	$filter['admin_agency_id'] = isset($_REQUEST['admin_agency_id'])? intval($_REQUEST['admin_agency_id']) : admin_agency_id();
	//dump($filter);
    $where = ' WHERE 1 ';
   
	if(isset($filter['admin_agency_id'])){
		$where .=" AND admin_agency_id = ".$filter['admin_agency_id'];
	}
	
	if($info_cat_word){
		$where .=' AND info_cat_name like \'%'.$info_cat_word.'%\'';
	}
	
    /* 获得总记录数据 */
    $sql = 'SELECT COUNT(*) FROM ' .$GLOBALS['ecs']->table('information_category') . $where;
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);

    $filter = page_and_size($filter);

    /* 获得资讯类别名称数据 */
    $arr = array();
	$sql_info_cat = "SELECT info_cat_id,article_cat_id,info_cat_name,show_order,is_show FROM ".$GLOBALS['ecs']->table('information_category').$where.' ORDER by '.$filter['sort_by'].' '.$filter['sort_order'];
    $res = $GLOBALS['db']->selectLimit($sql_info_cat, $filter['page_size'], $filter['start']);

    while ($row = $GLOBALS['db']->fetchRow($res))
    {  
         $arr[] = $row;
    }
	//print_r($arr);
    return array('info_cats' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}

?>