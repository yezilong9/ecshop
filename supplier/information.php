<?php

/**
 * ECSHOP 首页资讯信息管理程序
 * ============================================================================
 * * 版权所有 2005-2012 广州新泛联数码有限公司，并保留所有权利。
 * 网站地址: http://www..com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: zenghd $
 * $Id: information.php 17217 2014-09-04 09:29:08Z zenghd $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . 'includes/cls_image.php');
$image = new cls_image();
$exc   = new exchange($ecs->table("information"), $db, 'info_id', 'info_cat');
//echo admin_agency_id();


//echo $_REQUEST['act'];

/*------------------------------------------------------ */
//-- 资讯信息列表页面
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    $smarty->assign('ur_here',     '资讯信息列表');
    $smarty->assign('action_link', array('text' => '添加资讯信息', 'href' => 'information.php?act=add'));
    $smarty->assign('full_page',  1);
	$info_list = get_info_list();
	
	//获取资讯分类
	$obj_info = new class_information(admin_agency_id());
	$info_type = $obj_info->get_info_cats();
	$smarty->assign('info_type',$info_type);
	
	$info_specs = array('475x340' => '475x340','240x160' => '240x160','240x330' => '240x330','240x320' => '240x320');
	$smarty->assign('info_specs',$info_specs);
    $smarty->assign('info_lists', $info_list['info']);
    $smarty->assign('filter',       $info_list['filter']);
    $smarty->assign('record_count', $info_list['record_count']);
    $smarty->assign('page_count',   $info_list['page_count']);
    
	assign_query_info();
	$smarty->display('information_list.htm');
}


/*------------------------------------------------------ */
//-- 添加新资讯信息页面
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add')
{	
	admin_priv('information');
    
	//获取资讯分类
	$obj_info = new class_information(admin_agency_id());
	$info_type = $obj_info->get_info_cats();
	$smarty->assign('info_type',$info_type);
	$info_specs = array('475x340' => '475x340','240x160' => '240x160','240x330' => '240x330','240x320' => '240x320');
	$info['is_start'] = 1;
	$smarty->assign('ur_here',     '添加资讯信息');
    $smarty->assign('action_link', array('text' => '资讯信息列表', 'href' => 'information.php?act=list'));
	$smarty->assign('info_specs',$info_specs);
	$smarty->assign('info',$info);
	$smarty->assign('form_act','insert');
	$smarty->assign('admin_agency_id',admin_agency_id());
	assign_query_info();
    $smarty->display('information.htm');
}

/*------------------------------------------------------ */
//-- 新资讯信息的处理
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'insert')
{//dump($_POST);
    admin_priv('information');

	$info['info_cat_id'] = isset($_POST['info_cat']) ? intval($_POST['info_cat']) : '';
	$info['img_spec'] = isset($_POST['img_spec']) ? trim($_POST['img_spec']) : '';
	$img_file = isset($_FILES['img_file']) ? $_FILES['img_file'] : '';
	$info['title_describe'] = isset($_POST['title_describe']) ? trim($_POST['title_describe']) : '';
	$info['content_describe'] = isset($_POST['content_describe']) ? trim($_POST['content_describe']) : '';
	$info['link_url'] = isset($_POST['link_url']) ? trim($_POST['link_url']) : '';
	$info['is_start']  = isset($_POST['is_start']) ? intval($_POST['is_start']) : '';
	$info['admin_agency_id']    = isset($_POST['admin_agency_id']) ? intval($_POST['admin_agency_id']) : admin_agency_id();
	
	if(empty($info['info_cat_id']))
	{
		$error = '资讯类别没有选择！';
	}
	elseif(empty($info['img_spec']))
	{
		$error = '图片规格没有选择！';
	}
	elseif(empty($img_file))
	{
		$error = '没有上传图片！';
	}
	elseif(empty($info['title_describe']))
	{
		$error = '没有填写标题描述！';
	}
	
	$link[0]['text'] = '管理资讯信息';
    $link[0]['href'] = 'information.php?act=list';
    $link[1]['text'] = '继续添加资讯信息';
    $link[1]['href'] = 'information.php?act=add';
	if(isset($error))
		sys_msg($error, 0, $link,false);
		
	if($db->getOne("SELECT info_id FROM ".$ecs->table('information')." WHERE title_describe = '".$info['title_describe']."'"))
		sys_msg('本资讯名称已存在', 0, $link,false);
	if (isset($img_file['error']) && $img_file['error']==0)
	{
		if ($image->check_img_type($img_file['type']))
			$info['img_file'] = $image->upload_image($img_file, '');
		if(!$info['img_file'])
			sys_msg('上传图片失败', 1);
	}
	
	$db->autoExecute($ecs->table('information'), $info, 'INSERT');
    /* 记录管理员操作 */
    admin_log($_POST['info_cat'], 'add', 'information');

    clear_cache_files(); // 清除缓存文件

    /* 提示信息 */
    sys_msg($_LANG['add'] . "&nbsp;" .$info['title_describe'] . "&nbsp;" . $_LANG['attradd_succed'],0, $link,false);
	
}

/*------------------------------------------------------ */
//-- 资讯信息页面
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit')
{
   admin_priv('information');

    /* 获取某条资讯数据 */
    $sql = "SELECT info_id,info_cat_id,img_spec,img_file,title_describe,content_describe,link_url,is_start,admin_agency_id FROM " .$ecs->table('information'). " WHERE info_id =".intval($_REQUEST['info_id']);
    $info_one = $db->getRow($sql);
	$info_one['img_file'] = '../' . $info_one['img_file'];

	
	//获取资讯分类
	$obj_info = new class_information(admin_agency_id());
	$info_type = $obj_info->get_info_cats();
	$smarty->assign('info_type',$info_type);
	$info_specs = array('475x340' => '475x340','240x160' => '240x160','540x175' => '540x175','240x330' => '240x330','240x320' => '240x320');
	$smarty->assign('info_specs',$info_specs);
	
	$smarty->assign('edit',1);
	$smarty->assign('form_act','update');
    $smarty->assign('info',$info_one);
	$smarty->assign('admin_agency_id',$info_one['admin_agency_id']);
	
    assign_query_info();
    $smarty->display('information.htm');
	
}

/*------------------------------------------------------ */
//-- 资讯信息编辑的处理
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'update')
{
    admin_priv('information');
	
	$info_id = intval($_POST['info_id']);
	$info['info_cat_id'] = isset($_POST['info_cat']) ? intval($_POST['info_cat']) : '';
	$info['img_spec'] = isset($_POST['img_spec']) ? trim($_POST['img_spec']) : '';
	$img_file = isset($_FILES['img_file']) ? $_FILES['img_file'] : '';
	$info['title_describe'] = isset($_POST['title_describe']) ? trim($_POST['title_describe']) : '';
	$info['content_describe'] = isset($_POST['content_describe']) ? trim($_POST['content_describe']) : '';
	$info['link_url'] = isset($_POST['link_url']) ? trim($_POST['link_url']) : '';
	$info['is_start']  = isset($_POST['is_start']) ? intval($_POST['is_start']) : '';
	$info['admin_agency_id']    = isset($_POST['admin_agency_id']) ? intval($_POST['admin_agency_id']) : admin_agency_id();
	
	if(empty($info['info_cat_id']))
	{
		$error = '资讯类别没有选择！';
	}
	elseif(empty($info['img_spec']))
	{
		$error = '图片规格没有选择！';
	}
	elseif(empty($img_file))
	{
		$error = '没有上传图片！';
	}
	elseif(empty($info['title_describe']))
	{
		$error = '没有填写标题描述！';
	}

	$link[0]['text'] = '管理资讯';
    $link[0]['href'] = 'information.php?act=list';
    $link[1]['text'] = '添加资讯';
    $link[1]['href'] = 'information.php?act=add';
	if(isset($error))
		sys_msg($error, 0, $link,false);

	if (isset($img_file['error']) && $img_file['error']==0)
	{
		if ($image->check_img_type($img_file['type']))
			$info['img_file'] = $image->upload_image($img_file, '');
		if(!$info['img_file'])
			sys_msg('上传图片失败', 1);
	}
	
	$db->autoExecute($ecs->table('information'), $info, 'update',"info_id = $info_id");

   /* 记录管理员操作 */
   admin_log($_POST['info_cat'], 'edit', 'information');
   clear_cache_files(); // 清除模版缓存

   sys_msg($_LANG['edit'] .' '.$info['title_describe'].' '. $_LANG['attradd_succed'], 0, $link,false);


}

/*------------------------------------------------------ */
//-- 删除资讯信息位置
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'remove')
{
   check_authz_json('information');

    $id = intval($_GET['id']);
    $img_file = $exc->get_name($id, 'img_file');
    $exc->drop($id);

    if (strpos($img_file, 'http://') === false)
    {
        @unlink('../'.$img_file);
    }

    admin_log('', 'remove', 'information_category');

    echo $url = "information.php?act=query&". str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}

/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{   
	
	$info_list = get_info_list();
	$smarty->assign('info_lists',   $info_list['info']);
	$smarty->assign('filter',       $info_list['filter']);
	$smarty->assign('record_count', $info_list['record_count']);
	$smarty->assign('page_count',   $info_list['page_count']);
	make_json_result($smarty->fetch('information_list.htm'),'',array('filter' => $info_list['filter'], 'page_count' => $info_list['page_count']));
	
	
}

/* 获取资讯数据列表 */
function get_info_list(){
	 /* 过滤查询 */
	$info_cat = !empty($_REQUEST['info_cat'])? trim($_REQUEST['info_cat']): '';
    $img_spec = !empty($_REQUEST['img_spec'])? trim($_REQUEST['img_spec']): '';
	$info_words = !empty($_REQUEST['info_words'])? trim($_REQUEST['info_words']): '';
	
	$filter = array();
	$filter['info_cat']    = $info_cat;
	$filter['img_spec']    = $img_spec;
	$filter['info_words']    = $info_words;
	$filter['admin_agency_id'] = isset($_REQUEST['admin_agency_id'])? intval($_REQUEST['admin_agency_id']):admin_agency_id();
    $where = " WHERE 1 ";
	if(isset($filter['admin_agency_id']))
	{
		$where .= " AND admin_agency_id =  ".$filter['admin_agency_id'];
	}
	
	if($info_cat)
	{
		$where .= " AND info_cat_id = '{$info_cat}' ";
	}
	
	if($img_spec)
	{
		$where .= " AND img_spec = '{$img_spec}' ";
	}
	
	if($info_words)
	{
		$where .= " AND title_describe like '%{$info_words}%' OR content_describe like '%{$info_words}%'";
	}
    /* 获得总记录数据 */
    $sql = 'SELECT COUNT(*) FROM ' .$GLOBALS['ecs']->table('information').$where;
	$filter['record_count'] = $GLOBALS['db']->getOne($sql);
    $filter = page_and_size($filter);
    /* 获得广告数据 */
	$info = array();
	$rows = array();
	$sql_info = "SELECT info_id,info_cat_id,img_spec,img_file,title_describe,content_describe,link_url,is_start FROM ".$GLOBALS['ecs']->table('information').$where;
	$rows = $GLOBALS['db']->selectLimit($sql_info, $filter['page_size'], $filter['start']);
	 
	 while ($row = $GLOBALS['db']->fetchRow($rows))
    {  
		$sql_info_cat_name = "SELECT info_cat_name FROM ".$GLOBALS['ecs']->table('information_category')." WHERE info_cat_id = ".$row['info_cat_id'];
		$row['info_cat'] = $GLOBALS['db']->getOne($sql_info_cat_name);
		$info[] = $row;
    }
	
	//dump($info);
	 return array('info' => $info, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}

?>