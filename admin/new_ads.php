<?php

/**
* 新广告管理程序
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . 'includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);
$exc   = new exchange($ecs->table("ad_new"), $db, 'id', 'ad_name');


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
    $smarty->assign('action_link', array('text' => '添加广告', 'href' => 'new_ads.php?act=add'));
    $smarty->assign('pid',         $pid);
    $smarty->assign('full_page',  1);
	$smarty->assign('agency_list',   agency_list());
	$action_list = if_agency()?'all':'';
	$smarty->assign('all',         $action_list);
    $ads_list = get_newadslist();
    $smarty->assign('ads_list',     $ads_list['ads']);
    $smarty->assign('filter',       $ads_list['filter']);
    $smarty->assign('record_count', $ads_list['record_count']);
    $smarty->assign('page_count',   $ads_list['page_count']);

    assign_query_info();
    $smarty->display('new_ads_list.htm');
}


/*------------------------------------------------------ */
//-- 添加新广告页面
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add')
{
    admin_priv('ad_manage');
	$position_list = new_get_position_list();
	$smarty->assign('position_list',$position_list);
	//代理ID
	$admin_agency_id = isset($_REQUEST['admin_agency_id'])?$_REQUEST['admin_agency_id']:admin_agency_id();
    $smarty->assign('ads',
        array('ad_link' => $ad_link, 'ad_name' => $ad_name, 'start_time' => $start_time,
            'end_time' => $end_time, 'enabled' => 1));

    $smarty->assign('ur_here',       $_LANG['ads_add']);
    $smarty->assign('action_link',   array('href' => 'new_ads.php?act=list', 'text' => $_LANG['ad_list']));
	$smarty->assign('admin_agency_id',$admin_agency_id);
    $smarty->assign('form_act', 'insert');
    $smarty->assign('action',   'add');
    $smarty->assign('cfg_lang', $_CFG['lang']);
	$ads['start'] = 1;
    $smarty->assign('ads', $ads);

    assign_query_info();
    $smarty->display('new_ads_info.htm');
}

/*------------------------------------------------------ */
//-- 新广告的处理
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'insert')
{
    admin_priv('ad_manage');

	$adArr['ad_name'] = isset($_POST['ad_name']) ? trim($_POST['ad_name']) : '';
	$adArr['url']     = isset($_POST['url'])     ? trim($_POST['url']) : '';
	$img     = isset($_FILES['img'])    ? $_FILES['img'] : '';
	$outer_img     	  = isset($_POST['outer_img']) ? trim($_POST['outer_img']) : '';
	$adArr['start']   = isset($_POST['start'])   ? intval($_POST['start']) : '1';
	$adArr['width']   = isset($_POST['width'])   ? intval($_POST['width']) : '';
	$adArr['height']  = isset($_POST['height'])  ? intval($_POST['height']) : '';
	$adArr['particulars']  = isset($_POST['particulars'])  ? trim($_POST['particulars']) : '';
	$adArr['keyword'] = isset($_POST['keyword']) ? trim($_POST['keyword']) : '';
	$adArr['particulars'] = preg_replace("/['`<> ]/",'',$adArr['particulars']);
	$adArr['keyword'] = preg_replace("/['`<> ]/",'',$adArr['keyword']);
	$adArr['file']    = isset($_POST['file']) ? ','.trim($_POST['file']).',' : '';
	$adArr['admin_agency_id']    = !empty($_POST['admin_agency_id']) ? intval($_POST['admin_agency_id']) : admin_agency_id();
	$adArr['position_id'] = isset($_POST['position_id']) ? trim($_POST['position_id']) :'';
	if(empty($adArr['ad_name']))
		$error = '广告名称不能为空';
	elseif(empty($adArr['url']))
		$error = '广告链接不能为空';
	elseif(empty($img))
		$error = '图片不能为空';
	elseif(empty($adArr['file']))
		$error = '使用页面不能为空';
	if(isset($error))
		sys_msg($error, 0, $link);
	
	if($db->getOne("SELECT id FROM ".$ecs->table('ad_new').
	" WHERE ad_name = '$adArr[ad_name]' AND admin_agency_id = $adArr[admin_agency_id]"))
		sys_msg('广告名称已存在', 0, $link);
	if (isset($img['error']) && $img['error']==0)
	{
		$image      = new cls_image($_CFG['bgcolor']);//实例化图片处理函数
		if ($image->check_img_type($img['type']))
			$img_name = $image->upload_image($img, '');
		if(!$img_name)
			sys_msg('上传图片失败', 1);
		$adArr['img'] = $img_name;
	}
	if(!isset($adArr['img']))
	{
		$adArr['img'] = $outer_img;
	}
	$db->autoExecute($ecs->table('ad_new'), $adArr, 'INSERT');
    /* 记录管理员操作 */
    admin_log($_POST['ad_name'].'(新广告)', 'add', 'ads');

    clear_cache_files(); // 清除缓存文件

    /* 提示信息 */
    $link[0]['text'] = '查看广告列表';
    $link[0]['href'] = 'new_ads.php?act=list';
    $link[1]['text'] = '继续添加广告';
    $link[1]['href'] = 'new_ads.php?act=add';
    sys_msg($_LANG['add'] . "&nbsp;" .$_POST['ad_name'] . "&nbsp;" . $_LANG['attradd_succed'],0, $link);

}

/*------------------------------------------------------ */
//-- 广告编辑页面
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit')
{
    admin_priv('ad_manage');

    /* 获取广告数据 */
    $sql = "SELECT id,position_id,keyword,particulars,url,img,width,height,admin_agency_id,file,ad_name,start FROM " .$ecs->table('ad_new'). " WHERE id ='".intval($_REQUEST['id'])."'";
    $ads_arr = $db->getRow($sql);

    $ads_arr['ad_name'] = htmlspecialchars($ads_arr['ad_name']);
	if(strpos($ads_arr['img'],'http://') === false)
		$ads_arr['outer_img'] = 'http://'.agency_url().'/'.$ads_arr['img'];
	else
		$ads_arr['outer_img'] = $ads_arr['img'];
	$ads_arr['file'] = substr($ads_arr['file'],1,-1);
	//广告位
	$position_list = new_get_position_list();
	$smarty->assign('position_list',$position_list);
	$smarty->assign('position_id',$ads_arr['position_id']);
    $smarty->assign('ur_here',       $_LANG['ads_edit']);
    $smarty->assign('action_link',   array('href' => 'new_ads.php?act=list', 'text' => $_LANG['ad_list']));
    $smarty->assign('form_act',      'update');
    $smarty->assign('action',        'edit');
    $smarty->assign('ads',           $ads_arr);

    assign_query_info();
    $smarty->display('new_ads_info.htm');
}

/*------------------------------------------------------ */
//-- 广告编辑的处理
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'update')
{
    admin_priv('ad_manage');
	$id = intval($_POST['id']);
	$adArr['ad_name'] = isset($_POST['ad_name']) ? trim($_POST['ad_name']) : '';
	$adArr['url']     = isset($_POST['url'])     ? trim($_POST['url']) : '';
	$img     = isset($_FILES['img'])    ? $_FILES['img'] : '';
	$outer_img     	  = isset($_POST['outer_img']) ? trim($_POST['outer_img']) : '';
	$adArr['start']   = isset($_POST['start'])   ? intval($_POST['start']) : '1';
	$adArr['width']   = isset($_POST['width'])   ? intval($_POST['width']) : '';
	$adArr['height']  = isset($_POST['height'])  ? intval($_POST['height']) : '';
	$adArr['particulars']  = isset($_POST['particulars'])  ? trim($_POST['particulars']) : '';
	$adArr['keyword'] = isset($_POST['keyword']) ? trim($_POST['keyword']) : '';
	$adArr['particulars'] = preg_replace("/['`<> ]/",'',$adArr['particulars']);
	$adArr['keyword'] = preg_replace("/['`<> ]/",'',$adArr['keyword']);
	$adArr['file']    = isset($_POST['file']) ? ','.trim($_POST['file']).',' : '';
	$adArr['admin_agency_id']    = !empty($_POST['admin_agency_id']) ? intval($_POST['admin_agency_id']) : admin_agency_id();
	$adArr['position_id'] = isset($_POST['position_id']) ? trim($_POST['position_id']) :'';
	if(empty($adArr['ad_name']))
		$error = '广告名称不能为空';
	elseif(empty($adArr['url']))
		$error = '广告链接不能为空';
	elseif(empty($img))
		$error = '图片不能为空';
	elseif(empty($adArr['file']))
		$error = '使用页面不能为空';
	if(isset($error))
		sys_msg($error, 0, $link);
	$old_img = $db->getOne("SELECT img FROM ".$ecs->table('ad_new')." WHERE ad_name = '$adArr[ad_name]'");
	if($db->getOne("SELECT id FROM ".$ecs->table('ad_new')." WHERE ad_name = '$adArr[ad_name]' AND id <> $id AND admin_agency_id = $adArr[admin_agency_id]"))
		sys_msg('广告名称已存在', 0, $link);
	
	if (isset($img['error']) && $img['error']==0)
	{
		$image      = new cls_image($_CFG['bgcolor']);//实例化图片处理函数
		if ($image->check_img_type($img['type']))
			$img_name = $image->upload_image($img, '');
		if(!$img_name)
			sys_msg('上传图片失败', 1);
		$adArr['img'] = $img_name;
		if(!$db->getOne("SELECT id FROM ".$ecs->table('ad_new')." WHERE img ='$old_img' AND id<>$id"))
		@unlink('../'.$old_img);
	}
	if(!isset($adArr['img']))
	{
		$adArr['img'] = $outer_img;
	}
	
	$db->autoExecute($ecs->table('ad_new'), $adArr, 'update',"id = $id");

   /* 记录管理员操作 */
   admin_log($_POST['ad_name'].'(新广告)', 'edit', 'ads');

   clear_cache_files(); // 清除模版缓存

   /* 提示信息 */
   $href[] = array('text' => '广告列表', 'href' =>'new_ads.php?act=list');
   sys_msg($_LANG['edit'] .' '.$_POST['ad_name'].' '. $_LANG['attradd_succed'], 0, $href);

}

/*------------------------------------------------------ */
//-- 编辑广告名称
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_ad_name')
{
    check_authz_json('ad_manage');

    $id      = intval($_POST['id']);
    $ad_name = json_str_iconv(trim($_POST['val']));

    /* 检查广告名称是否重复 */
    if ($exc->num('ad_name', $ad_name, $id) != 0)
    {
        make_json_error(sprintf($_LANG['ad_name_exist'], $ad_name));
    }
    else
    {
        if ($exc->edit("ad_name = '$ad_name'", $id))
        {
            admin_log($ad_name,'edit','ads');
            make_json_result(stripslashes($ad_name));
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
    $img = $exc->get_name($id, 'img');
    $exc->drop($id);

    if (strpos($img, 'http://') === false)
    {
		if(!$db->getOne("SELECT id FROM ".$ecs->table('ad_new')." WHERE img ='$img' AND id<>$id"))
        @unlink('../'.$img);
    }

    admin_log('', 'remove', 'ads');

    $url = 'new_ads.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}

/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{   
	$ads_list = get_newadslist();
	
	$smarty->assign('ads_list',     $ads_list['ads']);
	$smarty->assign('filter',       $ads_list['filter']);
	$smarty->assign('record_count', $ads_list['record_count']);
	$smarty->assign('page_count',   $ads_list['page_count']);
	
	$sort_flag  = sort_flag($ads_list['filter']);
	$smarty->assign($sort_flag['tag'], $sort_flag['img']);
	
	make_json_result($smarty->fetch('new_ads_list.htm'), '',
	array('filter' => $ads_list['filter'], 'page_count' => $ads_list['page_count']));
	
	
}
/*------------------------------------------------------ */
//-- 一键生成代理商广告
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'copy_ad')
{
	$ad_obj = class_ad::new_ad();
	$ad_obj->create_agency_ad();
	$href[] = array('text' => '广告列表', 'href' =>'new_ads.php?act=list');
	sys_msg('生成完毕', 0, $href);
}

/* 获取广告数据列表 */
function get_newadslist()
{
    /* 过滤查询 */
	$ad_name = !empty($_REQUEST['ad_name'])? (string)$_REQUEST['ad_name']: '';
    $filter = array();
	$filter['ad_name']    = $ad_name;
	$filter['admin_agency_id'] = !empty($_REQUEST['admin_agency_id'])? (int)$_REQUEST['admin_agency_id']: '0';
    $filter['sort_by']    = empty($_REQUEST['sort_by']) ? 'ad_name' : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
    $where = 'WHERE 1 ';
	$admin_agency_id = admin_agency_id();
	if($ad_name)
		$where .= ' AND ad_name like \'%'.$ad_name.'%\'';
	if($filter['admin_agency_id'])
		$where .= " AND admin_agency_id = $filter[admin_agency_id] ";
	else
		$where .= " AND admin_agency_id = $admin_agency_id ";
    /* 获得总记录数据 */
    $sql = 'SELECT COUNT(*) FROM ' .$GLOBALS['ecs']->table('ad_new'). ' AS ad ' . $where;
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);
    $filter = page_and_size($filter);
    /* 获得广告数据 */
    $arr = array();
    $sql = 'SELECT ad.id,po.id as position_id,position_name,keyword,particulars,url,img,width,height,admin_agency_id,file,ad_name,start FROM ' .$GLOBALS['ecs']->table('ad_new'). ' AS ad left join '.$GLOBALS['ecs']->table('ad_new_position').' as po on po.id = ad.position_id ' . $where.
            'ORDER by '.$filter['sort_by'].' '.$filter['sort_order'];
    $res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);
    while ($rows = $GLOBALS['db']->fetchRow($res))
    {
		if(strpos($rows['img'],'http://') === false)
			$rows['img'] = 'http://'.agency_url().'/'.$rows['img'];
		if($rows['start'] == 1)
			$rows['start'] = '是';
		else
			$rows['start'] = 'NO';
        $arr[] = $rows;
    }

    return array('ads' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}
/* 获取广告位数据 */
function new_get_position_list()
{
	$position_list = $GLOBALS['db']->getAll("SELECT id,position_name FROM ".$GLOBALS['ecs']->table('ad_new_position'));
	$arr = array();
	/* 数据组装 */
	if($position_list)
	foreach($position_list as $key=>$value){
		$arr[$value['id']] = $value['position_name'];
	}
	return $arr;
	
}
?>