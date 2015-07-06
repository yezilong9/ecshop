<?php

/**
 *  商品分类管理程序
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www..com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: category.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
$exc = new exchange($ecs->table("category"), $db, 'cat_id', 'cat_name');

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
//-- 商品分类列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    /* 获取分类列表 */
    //$cat_list = cat_list(0, 0, false);
    /* 模板赋值 */
    
    /* 主站可以选择代理商进行搜索 ccx 2015-03-17 start  新建函数 */
    $cat_list = cat_list_ccx(0, 0, false);
    $smarty->assign('agency_list',   agency_list());
    /* ccx 2015-03-17 end  */
    
    $smarty->assign('ur_here',      $_LANG['03_category_list']);
    $smarty->assign('action_link',  array('href' => 'category.php?act=add', 'text' => $_LANG['04_category_add']));
    $smarty->assign('full_page',    1);
	$action_list = if_agency()?'all':'';
	$smarty->assign('all',         $action_list);
    $smarty->assign('cat_info',     $cat_list);

    /* 列表页面 */
    assign_query_info();
    $smarty->display('category_list.htm');
}

/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    //$cat_list = cat_list(0, 0, false);
    
    /* 主站可以选择代理商进行搜索 ccx 2015-03-17 start  新建函数 */
    $cat_list = cat_list_ccx(0, 0, false);
    $admin_agency_id = empty($_REQUEST['admin_agency_id']) ? '' : trim($_REQUEST['admin_agency_id']);
    if(!$admin_agency_id)
    {
        $action_list = if_agency()?'all':'';
	    $smarty->assign('all',         $action_list);
    }
    /* ccx 2015-03-17 end  新建函数 */
    
    $smarty->assign('cat_info',     $cat_list);

    make_json_result($smarty->fetch('category_list.htm'));
}
/*------------------------------------------------------ */
//-- 添加商品分类
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'add')
{
    /* 权限检查 */
    admin_priv('cat_manage');


    $action_list = if_agency()?'all':'';
    $smarty->assign('all',         $action_list);
    /* 模板赋值 */
    $smarty->assign('ur_here',      $_LANG['04_category_add']);
    $smarty->assign('action_link',  array('href' => 'category.php?act=list', 'text' => $_LANG['03_category_list']));

    $smarty->assign('goods_type_list',  goods_type_list(0)); // 取得商品类型
    $smarty->assign('attr_list',        get_attr_list()); // 取得商品属性

    //$smarty->assign('cat_select',   cat_list(0, 0, true));
    
    /*ccx 2015-03-18 代理商显示代理商自己的商品分类 start */ 
    $smarty->assign('cat_select',   cat_list_ccx(0, 0, true));
    /*ccx 2015-03-18 代理商显示代理商自己的商品分类 end */ 
    
    $smarty->assign('form_act',     'insert');
    $smarty->assign('cat_info',     array('is_show' => 1));



    /* 显示页面 */
    assign_query_info();
    $smarty->display('category_info.htm');
}

/*------------------------------------------------------ */
//-- 商品分类添加时的处理
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'insert')
{
    /* 权限检查 */
    admin_priv('cat_manage');

    /* 初始化变量 */
    $cat['cat_id']       = !empty($_POST['cat_id'])       ? intval($_POST['cat_id'])     : 0;
    $cat['parent_id']    = !empty($_POST['parent_id'])    ? intval($_POST['parent_id'])  : 0;
    $cat['sort_order']   = !empty($_POST['sort_order'])   ? intval($_POST['sort_order']) : 0;
    $cat['keywords']     = !empty($_POST['keywords'])     ? trim($_POST['keywords'])     : '';
    $cat['cat_desc']     = !empty($_POST['cat_desc'])     ? $_POST['cat_desc']           : '';
    $cat['measure_unit'] = !empty($_POST['measure_unit']) ? trim($_POST['measure_unit']) : '';
    $cat['cat_name']     = !empty($_POST['cat_name'])     ? trim($_POST['cat_name'])     : '';
    $cat['show_in_nav']  = !empty($_POST['show_in_nav'])  ? intval($_POST['show_in_nav']): 0;
    $cat['style']        = !empty($_POST['style'])        ? trim($_POST['style'])        : '';
    $cat['is_show']      = !empty($_POST['is_show'])      ? intval($_POST['is_show'])    : 0;
	/*by zhou*/
    $cat['is_top_show']      = !empty($_POST['is_top_show'])      ? intval($_POST['is_top_show'])    : 0;	
	$cat['is_top_style']  = !empty($_POST['is_top_style'])  ? intval($_POST['is_top_style']): 0;
    /*by zhou*/   
	/* 需要进行代理和主站区分的字段 by hg begin*/
	$cat['grade']          = !empty($_POST['grade'])        ? intval($_POST['grade'])      : 0;
	$cat['filter_attr']    = !empty($_POST['filter_attr'])  ? implode(',', array_unique(array_diff($_POST['filter_attr'],array(0)))) : 0;
	$cat['cat_recommend']  = !empty($_POST['cat_recommend'])  ? $_POST['cat_recommend'] : array();
	$cat['show_in_nav']    = !empty($_POST['show_in_nav'])  ? intval($_POST['show_in_nav']): 0;
	$cat['is_show']        = !empty($_POST['is_show'])      ? intval($_POST['is_show'])    : 0;
	$agency_attr = array(
				'grade'         => $cat['grade'],
				'filter_attr'   => $cat['filter_attr'],
				'show_in_nav'   => $cat['show_in_nav'],
				'is_show'       => $cat['is_show'],
				'sort_order'    => $cat['sort_order'],
				'measure_unit'  => $cat['measure_unit']
	);
	/* end */
    if($cat['grade'] > 10 || $cat['grade'] < 0)
    {
        /* 价格区间数超过范围 */
       $link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
       sys_msg($_LANG['grade_error'], 0, $link);
    }
	/* 代理商id by hg */
	$admin_agency_id = admin_agency_id();
	if($admin_agency_id)
	{
		$cat['agency_cat'] = ','.$admin_agency_id.',';
		$cat['host_cat'] = 0;
	}
	$obj_cat = new class_category();
	/* 入库的操作 */
	$exist_cat_id = cat_exists($cat['cat_name'], $cat['parent_id']);
	//dump($exist_cat_id);
    if ($exist_cat_id)
    {	
		//区分代理商添加和主站添加 add by hg for date 2014-09-1
		if($obj_cat->exist_add_cat($exist_cat_id,$admin_agency_id,$agency_attr))
		{
			/* 同级别下不能有重复的分类名称 */
			$link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
			sys_msg($_LANG['catname_exist'], 0, $link);	
		}
		else
		{
			insert_cat_recommend($cat['cat_recommend'], $exist_cat_id);
		}
    }
    else
    {
        $cat_id = $obj_cat->add_cat($cat);
		if($cat_id)
		{
			if($cat['show_in_nav'] == 1)
			{
				$vieworder = $db->getOne("SELECT max(vieworder) FROM ". $ecs->table('nav') . " WHERE type = 'middle'");
				$vieworder += 2;
				//显示在自定义导航栏中
				$sql = "INSERT INTO " . $ecs->table('nav') .
					" (name,ctype,cid,ifshow,vieworder,opennew,url,type)".
					" VALUES('" . $cat['cat_name'] . "', 'c', '".$db->insert_id()."','1','$vieworder','0', '" . build_uri('category', array('cid'=> $cat_id), $cat['cat_name']) . "','middle')";
				$db->query($sql);
			}
			insert_cat_recommend($cat['cat_recommend'], $cat_id);
		}

    }
	admin_log($_POST['cat_name'], 'add', 'category');   // 记录管理员操作
	clear_cache_files();    // 清除缓存
	/*添加链接*/
	$link[0]['text'] = $_LANG['continue_add'];
	$link[0]['href'] = 'category.php?act=add';
	$link[1]['text'] = $_LANG['back_list'];
	$link[1]['href'] = 'category.php?act=list';
	sys_msg($_LANG['catadd_succed'], 0, $link);
 }

/*------------------------------------------------------ */
//-- 编辑商品分类信息
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'edit')
{
    admin_priv('cat_manage');   // 权限检查
    $cat_id = intval($_REQUEST['cat_id']);
    $cat_info = get_cat_info($cat_id);  // 查询分类信息数据
    
	$admin_agency_id = admin_agency_id();
    $attr_list = get_attr_list();
    $filter_attr_list = array();
	$action_list = if_agency()?'all':'';
	$smarty->assign('all',         $action_list);
    if ($cat_info['filter_attr'])
    {
        $filter_attr = explode(",", $cat_info['filter_attr']);  //把多个筛选属性放到数组中

        foreach ($filter_attr AS $k => $v)
        {
            $attr_cat_id = $db->getOne("SELECT cat_id FROM " . $ecs->table('attribute') . " WHERE attr_id = '" . intval($v) . "'");
            $filter_attr_list[$k]['goods_type_list'] = goods_type_list($attr_cat_id);  //取得每个属性的商品类型
            $filter_attr_list[$k]['filter_attr'] = $v;
            $attr_option = array();

            foreach ($attr_list[$attr_cat_id] as $val)
            {
                $attr_option[key($val)] = current ($val);
            }

            $filter_attr_list[$k]['option'] = $attr_option;
        }

        $smarty->assign('filter_attr_list', $filter_attr_list);
    }
    else
    {
        $attr_cat_id = 0;
    }

    /* 模板赋值 */
    $smarty->assign('attr_list',        $attr_list); // 取得商品属性
    $smarty->assign('attr_cat_id',      $attr_cat_id);
    $smarty->assign('ur_here',     $_LANG['category_edit']);
    $smarty->assign('action_link', array('text' => $_LANG['03_category_list'], 'href' => 'category.php?act=list'));

    //分类是否存在首页推荐
    $res = $db->getAll("SELECT recommend_type FROM " . $ecs->table("cat_recommend") .
	" WHERE cat_id=$cat_id AND admin_agency_id = $admin_agency_id");
    if (!empty($res))
    {
        $cat_recommend = array();
        foreach($res as $data)
        {
            $cat_recommend[$data['recommend_type']] = 1;
        }
        $smarty->assign('cat_recommend', $cat_recommend);
    }

    $smarty->assign('cat_info',    $cat_info);
    $smarty->assign('form_act',    'update');
    //$smarty->assign('cat_select',  cat_list(0, $cat_info['parent_id'], true));
    
    /*2015-03-18 ccx 代理商显示代理商站点的分类 start */
    $smarty->assign('cat_select',  cat_list_ccx(0, $cat_info['parent_id'], true));
    /*2015-03-18 ccx end */
    
    $smarty->assign('goods_type_list',  goods_type_list(0)); // 取得商品类型

    /* 显示页面 */
    assign_query_info();
    $smarty->display('category_info.htm');
}
elseif($_REQUEST['act'] == 'add_category')
{
    $cat['parent_id'] = empty($_REQUEST['parent_id']) ? 0 : intval($_REQUEST['parent_id']);
    $cat['cat_name'] = empty($_REQUEST['cat']) ? '' : json_str_iconv(trim($_REQUEST['cat']));
	$cat['is_show'] = 1;
	$admin_agency_id = admin_agency_id();
	$exist_cat_id = cat_exists($cat['cat_name'], $cat['parent_id']);
	$obj_cat = new class_category();
	//分类存在
    if($exist_cat_id)
    {
		if($obj_cat->exist_add_cat($exist_cat_id,$admin_agency_id,$cat))
		{
			make_json_error($_LANG['catname_exist']);
		}
    }
    else
    {
		$exist_cat_id = $obj_cat->add_cat($cat);
    }
	$arr = array("parent_id"=>$cat['parent_id'], "id"=>$exist_cat_id, "cat"=>$cat['cat_name']);
	clear_cache_files();    // 清除缓存
	make_json_result($arr);
}
/*------------------------------------------------------ */
//-- 编辑商品分类信息
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'update')
{
    /* 权限检查 */
    admin_priv('cat_manage');
	$admin_agency_id = admin_agency_id();
    /* 初始化变量 */
    $cat_id              = !empty($_POST['cat_id'])       ? intval($_POST['cat_id'])     : 0;
    $old_cat_name        = $_POST['old_cat_name'];
    $cat['parent_id']    = !empty($_POST['parent_id'])    ? intval($_POST['parent_id'])  : 0;
    $cat['sort_order']   = !empty($_POST['sort_order'])   ? intval($_POST['sort_order']) : 0;
    $cat['keywords']     = !empty($_POST['keywords'])     ? trim($_POST['keywords'])     : '';
    $cat['cat_desc']     = !empty($_POST['cat_desc'])     ? $_POST['cat_desc']           : '';
    $cat['measure_unit'] = !empty($_POST['measure_unit']) ? trim($_POST['measure_unit']) : '';
    $cat['cat_name']     = !empty($_POST['cat_name'])     ? trim($_POST['cat_name'])     : '';
    $cat['is_show']      = !empty($_POST['is_show'])      ? intval($_POST['is_show'])    : 0;
	/*by zhou*/
    $cat['is_top_show']   = !empty($_POST['is_top_show'])      ? intval($_POST['is_top_show'])    : 0;	
	$cat['is_top_style']  = !empty($_POST['is_top_style'])  ? intval($_POST['is_top_style']): 0;
	/*by zhou*/
    $cat['show_in_nav']  = !empty($_POST['show_in_nav'])  ? intval($_POST['show_in_nav']): 0;
    $cat['style']        = !empty($_POST['style'])        ? trim($_POST['style'])        : '';
    $cat['grade']        = !empty($_POST['grade'])        ? intval($_POST['grade'])      : 0;
    $cat['filter_attr']  = !empty($_POST['filter_attr'])  ? implode(',', array_unique(array_diff($_POST['filter_attr'],array(0)))) : 0;
    $cat['cat_recommend']  = !empty($_POST['cat_recommend'])  ? $_POST['cat_recommend'] : array();
	$cat['agency_add']     = !empty($_POST['agency_add'])  ? intval($_POST['agency_add']) : '';
	/* 代理商分类属性 by hg */
	$agency_attr = array(
				'grade'         => $cat['grade'],
				'filter_attr'   => $cat['filter_attr'],
				'show_in_nav'   => $cat['show_in_nav'],
				'is_show'       => $cat['is_show'],
				'sort_order'    => $cat['sort_order'],
				'measure_unit'  => $cat['measure_unit']
			);
    /* 判断分类名是否重复 */
    if ($cat['cat_name'] != $old_cat_name)
    {
        if (hg_cat_exists($cat['cat_name'],$cat['parent_id'],$admin_agency_id))
        {
           $link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
           sys_msg($_LANG['catname_exist'], 0, $link);
        }
    }

    /* 判断上级目录是否合法 */
    $children = array_keys(cat_list($cat_id, 0, false));     // 获得当前分类的所有下级分类
    if (in_array($cat['parent_id'], $children))
    {
        /* 选定的父类是当前分类或当前分类的下级分类 */
       $link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
       sys_msg($_LANG["is_leaf_error"], 0, $link);
    }

    if($cat['grade'] > 10 || $cat['grade'] < 0)
    {
        /* 价格区间数超过范围 */
       $link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
       sys_msg($_LANG['grade_error'], 0, $link);
    }
    
    /* ccx 2015-03-19 判断是代理商引用主站的商品分类，不能够对该分类进行修改编辑 start*/
    if($admin_agency_id)
    {
        $sql = "SELECT host_cat FROM " .$GLOBALS['ecs']->table('category').
           " WHERE cat_id = " .$cat_id. " ";
        $host_cat = $GLOBALS['db']->getOne($sql);     
        if($host_cat > 1 ) // 如果该分类是代理商引用的就不能进行编辑修改
        {
            $link[] = array('text' => $_LANG['go_back'], 'href' => 'category.php?act=list');
            sys_msg("该分类是引用主站的,不能够对其进行修改编辑", 0, $link);
        }
    }
    /* ccx 2015-03-19 start*/
    
    $dat = $db->getRow("SELECT agency_cat, cat_name, show_in_nav FROM ". $ecs->table('category') . " WHERE cat_id = '$cat_id'");
	$cat_obj = new class_category();
	/* 如果是代理商编辑分类 add by hg for date 2014-09-01 */
	if($admin_agency_id)
	{
		if($cat['cat_name'] == $old_cat_name)
		{
			/*屏蔽 2015-03-19 以前关于商品分类的修改代码 start 
			* if($cat_obj->agency_exist_add_cat($dat['agency_cat'],$cat_id,$admin_agency_id,$agency_attr))
			* {
			*	$where = "cat_id='$cat_id' AND admin_agency_id = '$admin_agency_id'";
			*	$db->autoExecute($ecs->table('category_attribute'), $agency_attr, 'UPDATE', $where);
			* }
		    end*/
			
			/*新增关于编辑代理商商品分类的相关代码 ccx 2015-03-19 start*/
			
            if($db->autoExecute($ecs->table('category'), $cat, 'UPDATE', "cat_id ='$cat_id'"))
            {
                $where = "cat_id='$cat_id' AND admin_agency_id = '$admin_agency_id'";
                $db->autoExecute($ecs->table('category_attribute'), $agency_attr, 'UPDATE', $where);
            }
			/*end 新增代码结束*/
		}
		else
		{
			#分类改名删除原来分类
			/*2015-03-19 屏蔽该函数代码，并在下面进行重写 start*/
			//$cat_obj->agency_del_cat($dat['agency_cat'],$cat_id,$admin_agency_id);
			//$cat_id = $cat_obj->agency_add_cat($cat,$admin_agency_id);
			/*end */
            
            /*ccx 2015-03-19 start*/  
            if($db->autoExecute($ecs->table('category'), $cat, 'UPDATE', "cat_id ='$cat_id'"))
            {
                $where = "cat_id='$cat_id' AND admin_agency_id = '$admin_agency_id'";
                $db->autoExecute($ecs->table('category_attribute'), $agency_attr, 'UPDATE', $where);
            }
            /*ccx 2015-03-19 end */
		}
	}
	#主站编辑分类
	else 
	{
        if($cat['cat_name'] == $old_cat_name)
        {
        	if($db->autoExecute($ecs->table('category'), $cat, 'UPDATE', "cat_id='$cat_id'"))
        	{
        		if($cat['show_in_nav'] != $dat['show_in_nav'])
        		{
        			//是否显示于导航栏发生了变化
        			if($cat['show_in_nav'] == 1)
        			{
        				//显示
        				$nid = $db->getOne("SELECT id FROM ". $ecs->table('nav') . " WHERE ctype = 'c' AND cid = '" . $cat_id . "' AND type = 'middle'");
        				if(empty($nid))
        				{
        					//不存在
        					$vieworder = $db->getOne("SELECT max(vieworder) FROM ". $ecs->table('nav') . " WHERE type = 'middle'");
        					$vieworder += 2;
        					$uri = build_uri('category', array('cid'=> $cat_id), $cat['cat_name']);
        
        					$sql = "INSERT INTO " . $ecs->table('nav') . " (name,ctype,cid,ifshow,vieworder,opennew,url,type) VALUES('" . $cat['cat_name'] . "', 'c', '$cat_id','1','$vieworder','0', '" . $uri . "','middle')";
        				}
        				else
        				{
        					$sql = "UPDATE " . $ecs->table('nav') . " SET ifshow = 1 WHERE ctype = 'c' AND cid = '" . $cat_id . "' AND type = 'middle'";
        				}
        				$db->query($sql);
        			}
        			else
        			{
        				//去除
        				$db->query("UPDATE " . $ecs->table('nav') . " SET ifshow = 0 WHERE ctype = 'c' AND cid = '" . $cat_id . "' AND type = 'middle'");
        			}
        		}
        	}
        }
        else
        {
        	if($dat['agency_cat'])
        	{
        		$cat_id = $db->getOne("SELECT cat_id FROM ".$ecs->table('category')." WHERE cat_name = '$cat[cat_name]'");
        	}
        	$cat['host'] = 1;
        	if($cat_id)
        	{
        		$db->autoExecute($ecs->table('category'), $cat, 'UPDATE', "cat_id ='$cat_id'");
        	}
        	else
        	{
        		$db->autoExecute($GLOBALS['ecs']->table('category'), $cat);
        		$cat_id = $db->insert_id();
        	}
        }
                
    }
	//更新首页推荐
	insert_cat_recommend($cat['cat_recommend'], $cat_id);
	/* 更新分类信息成功 */
	clear_cache_files(); // 清除缓存
	admin_log($_POST['cat_name'], 'edit', 'category'); // 记录管理员操作

	/* 提示信息 */
	$link[] = array('text' => $_LANG['back_list'], 'href' => 'category.php?act=list');
	sys_msg($_LANG['catedit_succed'], 0, $link);
	
}

/*------------------------------------------------------ */
//-- 批量转移商品分类页面
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'move')
{
    /* 权限检查 */
    admin_priv('cat_drop');

    $cat_id = !empty($_REQUEST['cat_id']) ? intval($_REQUEST['cat_id']) : 0;

    /* 模板赋值 */
    $smarty->assign('ur_here',     $_LANG['move_goods']);
    $smarty->assign('action_link', array('href' => 'category.php?act=list', 'text' => $_LANG['03_category_list']));

    //$smarty->assign('cat_select', cat_list(0, $cat_id, true));
    
    /*ccx 2015-03-18 代理商显示代理商自己的商品分类 start */ 
    $smarty->assign('cat_select',   cat_list_ccx(0, 0, true));
    /*ccx 2015-03-18 代理商显示代理商自己的商品分类 end */ 
    
    $smarty->assign('form_act',   'move_cat');

    /* 显示页面 */
    assign_query_info();
    $smarty->display('category_move.htm');
}

/*------------------------------------------------------ */
//-- 处理批量转移商品分类的处理程序
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'move_cat')
{
    /* 权限检查 */
    admin_priv('cat_drop');

    $cat_id        = !empty($_POST['cat_id'])        ? intval($_POST['cat_id'])        : 0;
    $target_cat_id = !empty($_POST['target_cat_id']) ? intval($_POST['target_cat_id']) : 0;

    /* 商品分类不允许为空 */
    if ($cat_id == 0 || $target_cat_id == 0)
    {
        $link[] = array('text' => $_LANG['go_back'], 'href' => 'category.php?act=move');
        sys_msg($_LANG['cat_move_empty'], 0, $link);
    }

    /* 更新商品分类 */
    $sql = "UPDATE " .$ecs->table('goods'). " SET cat_id = '$target_cat_id' ".
           "WHERE cat_id = '$cat_id'";
    if ($db->query($sql))
    {
        /* 清除缓存 */
        clear_cache_files();

        /* 提示信息 */
        $link[] = array('text' => $_LANG['go_back'], 'href' => 'category.php?act=list');
        sys_msg($_LANG['move_cat_success'], 0, $link);
    }
}

/*------------------------------------------------------ */
//-- 编辑排序序号
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'edit_sort_order')
{
    check_authz_json('cat_manage');

    $id = intval($_POST['id']);
    $val = intval($_POST['val']);

    if (cat_update($id, array('sort_order' => $val)))
    {
        clear_cache_files(); // 清除缓存
        make_json_result($val);
    }
    else
    {
        make_json_error($db->error());
    }
}

/*------------------------------------------------------ */
//-- 编辑数量单位
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'edit_measure_unit')
{
    check_authz_json('cat_manage');

    $id = intval($_POST['id']);
    $val = json_str_iconv($_POST['val']);

    if (cat_update($id, array('measure_unit' => $val)))
    {
        clear_cache_files(); // 清除缓存
        make_json_result($val);
    }
    else
    {
        make_json_error($db->error());
    }
}

/*------------------------------------------------------ */
//-- 编辑排序序号
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'edit_grade')
{
    check_authz_json('cat_manage');

    $id = intval($_POST['id']);
    $val = intval($_POST['val']);

    if($val > 10 || $val < 0)
    {
        /* 价格区间数超过范围 */
        make_json_error($_LANG['grade_error']);
    }

    if (cat_update($id, array('grade' => $val)))
    {
        clear_cache_files(); // 清除缓存
        make_json_result($val);
    }
    else
    {
        make_json_error($db->error());
    }
}

/*------------------------------------------------------ */
//-- 切换是否显示在导航栏
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'toggle_show_in_nav')
{
    check_authz_json('cat_manage');

    $id = intval($_POST['id']);
    $val = intval($_POST['val']);

    if (cat_update($id, array('show_in_nav' => $val)) != false)
    {
        if($val == 1)
        {
            //显示
            $vieworder = $db->getOne("SELECT max(vieworder) FROM ". $ecs->table('nav') . " WHERE type = 'middle'");
            $vieworder += 2;
            $catname = $db->getOne("SELECT cat_name FROM ". $ecs->table('category') . " WHERE cat_id = '$id'");
            //显示在自定义导航栏中
            $_CFG['rewrite'] = 0;
            $uri = build_uri('category', array('cid'=> $id), $catname);

            $nid = $db->getOne("SELECT id FROM ". $ecs->table('nav') . " WHERE ctype = 'c' AND cid = '" . $id . "' AND type = 'middle'");
            if(empty($nid))
            {
                //不存在
                $sql = "INSERT INTO " . $ecs->table('nav') . " (name,ctype,cid,ifshow,vieworder,opennew,url,type) VALUES('" . $catname . "', 'c', '$id','1','$vieworder','0', '" . $uri . "','middle')";
            }
            else
            {
                $sql = "UPDATE " . $ecs->table('nav') . " SET ifshow = 1 WHERE ctype = 'c' AND cid = '" . $id . "' AND type = 'middle'";
            }
            $db->query($sql);
        }
        else
        {
            //去除
            $db->query("UPDATE " . $ecs->table('nav') . "SET ifshow = 0 WHERE ctype = 'c' AND cid = '" . $id . "' AND type = 'middle'");
        }
        clear_cache_files();
        make_json_result($val);
    }
    else
    {
        make_json_error($db->error());
    }
}

/*------------------------------------------------------ */
//-- 切换是否显示
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'toggle_is_show')
{
    check_authz_json('cat_manage');

    $id = intval($_POST['id']);
    $val = intval($_POST['val']);

    if (cat_update($id, array('is_show' => $val)) != false)
    {
        clear_cache_files();
        make_json_result($val);
    }
    else
    {
        make_json_error($db->error());
    }
}

/*------------------------------------------------------ */
//-- 删除商品分类
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'remove')
{
    check_authz_json('cat_manage');

    /* 初始化分类ID并取得分类名称 */
    $cat_id   = intval($_GET['id']);
    $cat_name = $db->getOne('SELECT cat_name FROM ' .$ecs->table('category'). " WHERE cat_id='$cat_id'");

    /* 当前分类下是否有子分类 */
	$admin_agency_id = admin_agency_id();
	if($admin_agency_id){
		$cat_count = $db->getOne('SELECT COUNT(*) FROM ' .$ecs->table('category'). 
		" WHERE parent_id='$cat_id' AND find_in_set('$admin_agency_id',agency_cat)");
	}else{
		$cat_count = $db->getOne('SELECT COUNT(*) FROM ' .$ecs->table('category'). 
		" WHERE parent_id='$cat_id' AND host_cat = 1");
	}

    /* 当前分类下是否存在商品 */
    $goods_count = $db->getOne('SELECT COUNT(*) FROM ' .$ecs->table('goods'). " WHERE cat_id='$cat_id'");

    /* 如果不存在下级子分类和商品，则删除之 */
    if ($cat_count == 0 && $goods_count == 0)
    {
		$obj_cat = new class_category();
		if($obj_cat->del_cat($cat_id))
		{
			/* 删除分类 */
			$sql = 'DELETE FROM ' .$ecs->table('category'). " WHERE cat_id = '$cat_id'";
			if ($db->query($sql))
			{
				$db->query("DELETE FROM " . $ecs->table('nav') . "WHERE ctype = 'c' AND cid = '" . $cat_id . "' AND type = 'middle'");
				/* 删除代理商分类属性 */
				$db->query("DELETE FROM".$GLOBALS['ecs']->table('category').
				" WHERE cat_id=$cat_id");
				$admin_agency_id = admin_agency_id();
				$db->query("DELETE FROM".$GLOBALS['ecs']->table('cat_recommend')." WHERE cat_id=$cat_id AND admin_agency_id=$admin_agency_id");
				admin_log($cat_name, 'remove', 'category');
			}
		}
		
        /*ccx 2015-03-19 start 删除代理商自己分类*/
        if($admin_agency_id)
        {
            $sql = 'DELETE FROM ' .$ecs->table('category'). " WHERE cat_id = '$cat_id'";
            $db->query($sql);
        }
        /*ccx 2015-03-19 end  */
		
		
		clear_cache_files();
    }
    else
    {
        make_json_error($cat_name .' '. $_LANG['cat_isleaf']);
    }

    $url = 'category.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}

/*ccx 2015-03-17 代理商引用主站的商品分类 start*/
if ($_REQUEST['act'] == 'host_category')
{    
    /* 主站可以选择代理商进行搜索 ccx 2015-03-17 start  新建函数 */
    $cat_list = cat_list_host_category(0, 0, false);
    //print_r($cat_list);
    $smarty->assign('agency_list',   agency_list());
    /* ccx 2015-03-17 end  */
    
    $smarty->assign('ur_here',      "引用主站商品分类");
    $smarty->assign('action_link',  array('href' => 'category.php?act=add', 'text' => $_LANG['04_category_add']));
     $smarty->assign('action_link2', array('href' => 'category.php?act=list', 'text' => $_LANG['03_category_list']));
    $smarty->assign('full_page',    1);
	$action_list = if_agency()?'all':'';
	$smarty->assign('all',         $action_list);
    $smarty->assign('cat_info',     $cat_list);

    /* 列表页面 */
    assign_query_info();
    $smarty->display('host_category_list.htm');
}

/*2015-03-23 注释 ccx 采用了host_category_copy方法，该方法注释掉 start
if ($_REQUEST['act'] == 'copy_host_category')
{
    include_once(ROOT_PATH . 'includes/lib_main.php');
    //echo "ceshi";exit;
    
    $admin_agency_id = admin_agency_id();//代理商user_id
    $agency_attr = array();
    $cat_id = empty($_REQUEST['cat_id']) ? 0 : intval($_REQUEST['cat_id']);  
    $cart_number = get_parent_cats($cat_id);

    $cart_number = array_reverse($cart_number);
    
    //ccx 2015-03-19 判断该分类（包括上级分类）是否显示 start
    foreach ($cart_number AS $k => $v)
    {
        $sql = "SELECT is_show FROM " .$GLOBALS['ecs']->table('category').
                " WHERE cat_id = ".$v['cat_id'];
        $is_show = $GLOBALS['db']->getOne($sql);     
        if($is_show == 0 ) // 如果该分类(包括上级分类)，没有显示，该分类不允许引用）
        {
            $is_show = 0;
            break;
        }
    }
    if($is_show == 0)
    {
        // 该分类不允许引用 
        $link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
        sys_msg("请当前分类，或者当前分类的上一级分类存在不显示的状态,所以该分类不能被引用", 0, $link);	
    }
    //ccx 2015-03-19 end 
    
    $host_parent_id = 0; // 默认当前的parent_id=0 顶级分类
    //print_r($cart_number);exit;
    foreach ($cart_number AS $k => $v)
    {
        $sql = "SELECT cat_id FROM " .$GLOBALS['ecs']->table('category').
                " WHERE host_cat = ".$v['cat_id']." AND  agency_cat like '%,$admin_agency_id,%' ";
        $nid = $GLOBALS['db']->getOne($sql);
        
        if($nid) // 如果该分类已存在（已经被引用了）
        {
          //echo "存在";exit;  
          $not_hot_cat = false;
          $host_parent_id = $nid; //获取当前引用分类的最新的上一级的cat_id,如果存在，当前引用的分类的parent_id就是该cat_id了
          continue; 
        }
        else 
        {
            //echo "不存在";exit;
            $sql = "SELECT * FROM " .$GLOBALS['ecs']->table('category'). " WHERE cat_id= ".$v['cat_id']." LIMIT 1";
            $res = $GLOBALS['db']->getRow($sql);
            if($host_parent_id > 0)
            {
               $cat['parent_id'] = $host_parent_id;
            }
            $cat['agency_cat']   = ','.$admin_agency_id.',';
            $cat['host_cat']     = $v['cat_id'];
            $cat['cat_name']     = $v['cat_name'];
            
            $cat['keywords']     = $res['keywords'];
            $cat['cat_desc']     = $res['cat_desc'];
            $cat['sort_order']   = $res['sort_order'];
            $cat['template_file']  = $res['template_file'];
            $cat['measure_unit']  = $res['measure_unit'];
            $cat['show_in_nav']  = $res['show_in_nav'];
            $cat['style']  = $res['style'];
            $cat['is_show']      = $res['is_show'];
            $cat['grade']  = $res['grade'];
            $cat['filter_attr']  = $res['filter_attr'];
            $cat['category_index']  = $res['category_index'];
            $cat['show_in_index']  = $res['show_in_index'];
            $cat['cat_index_rightad']  = $res['cat_index_rightad'];
            $cat['cat_adimg_1']  = $res['cat_adimg_1'];
            $cat['cat_adurl_1']  = $res['cat_adurl_1'];
            $cat['cat_adimg_2']  = $res['cat_adimg_2'];
            $cat['cat_adurl_2']  = $res['cat_adurl_2'];
            $cat['cat_nameimg']  = $res['cat_nameimg'];
            $cat['is_top_style']  = $res['is_top_style'];
            $cat['is_top_show']  = $res['is_top_show'];
            
            $db->autoExecute($GLOBALS['ecs']->table('category'), $cat, 'INSERT');
            $cat['parent_id'] = $db->insert_id();
            $host_parent_id = 0;
            
            $agency_attr['cat_id'] =  $cat['parent_id']; 
            $agency_attr['is_show'] = $res['is_show'];
            $agency_attr['filter_attr'] = $res['filter_attr'];       
            $agency_attr['admin_agency_id'] =  $admin_agency_id; 
            $agency_attr['is_show'] = 1; 
            $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('category_attribute'), $agency_attr, 'INSERT');
            
            $not_hot_cat = true;
            //continue;
            
        }
 
    } 
    if($not_hot_cat == false)
    {
        // 判断该分类是否已经被引用了
        $link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
        sys_msg("该分类已经被代理商站点引用过了", 0, $link);	
    }
    else 
    {
       // 提示信息,引用该分类成功 
        $link[] = array('text' => $_LANG['back_list'], 'href' => 'category.php?act=list');
        sys_msg("该分类引用成功", 0, $link); 
    }

}
2015-03-23 注释 ccx 采用了host_category_copy方法 end */

if ($_REQUEST['act'] == 'host_category_copy')
{
    //echo "123456789";exit;
    //make_json_error("不能引用该分类");
    
    include_once(ROOT_PATH . 'includes/lib_main.php');
    $cat_id = empty($_REQUEST['cat_id']) ? '' : json_str_iconv(trim($_REQUEST['cat_id']));
    $val =    empty($_REQUEST['val']) ? '' : json_str_iconv(trim($_REQUEST['val']));
      
    $admin_agency_id = admin_agency_id();//代理商user_id
    $agency_attr = array();
    $cart_number = get_parent_cats($cat_id);

    $cart_number = array_reverse($cart_number);
    
    /*ccx 2015-03-19 判断该分类（包括上级分类）是否显示 start*/
    foreach ($cart_number AS $k => $v)
    {
        $sql = "SELECT is_show FROM " .$GLOBALS['ecs']->table('category').
                " WHERE cat_id = ".$v['cat_id'];
        $is_show = $GLOBALS['db']->getOne($sql);     
        if($is_show == 0 ) // 如果该分类(包括上级分类)，没有显示，该分类不允许引用）
        {
            $is_show = 0;
            break;
        }
    }
    if($is_show == 0)
    {
        /* 该分类不允许引用 */
        make_json_error("请当前分类，或者当前分类的上一级分类存在不显示的状态,所以该分类不能被引用");
    }
    /*ccx 2015-03-19 end */
    
    $host_parent_id = 0; // 默认当前的parent_id=0 顶级分类
    //print_r($cart_number);exit;
    foreach ($cart_number AS $k => $v)
    {
        $sql = "SELECT cat_id FROM " .$GLOBALS['ecs']->table('category').
                " WHERE host_cat = ".$v['cat_id']." AND  agency_cat like '%,$admin_agency_id,%' ";
        $nid = $GLOBALS['db']->getOne($sql);
        
        if($nid) // 如果该分类已存在（已经被引用了）
        {
          //echo "存在";exit;  
          $not_hot_cat = false;
          $host_parent_id = $nid; //获取当前引用分类的最新的上一级的cat_id,如果存在，当前引用的分类的parent_id就是该cat_id了
          continue; 
        }
        else 
        {
            //echo "不存在";exit;
            $sql = "SELECT * FROM " .$GLOBALS['ecs']->table('category'). " WHERE cat_id= ".$v['cat_id']." LIMIT 1";
            $res = $GLOBALS['db']->getRow($sql);
            if($host_parent_id > 0)
            {
               $cat['parent_id'] = $host_parent_id;
            }
            $cat['agency_cat']   = ','.$admin_agency_id.',';
            $cat['host_cat']     = $v['cat_id'];
            $cat['cat_name']     = $v['cat_name'];
            
            $cat['keywords']     = $res['keywords'];
            $cat['cat_desc']     = $res['cat_desc'];
            $cat['sort_order']   = $res['sort_order'];
            $cat['template_file']  = $res['template_file'];
            $cat['measure_unit']  = $res['measure_unit'];
            $cat['show_in_nav']  = $res['show_in_nav'];
            $cat['style']  = $res['style'];
            $cat['is_show']      = $res['is_show'];
            $cat['grade']  = $res['grade'];
            $cat['filter_attr']  = $res['filter_attr'];
            $cat['category_index']  = $res['category_index'];
            $cat['show_in_index']  = $res['show_in_index'];
            $cat['cat_index_rightad']  = $res['cat_index_rightad'];
            $cat['cat_adimg_1']  = $res['cat_adimg_1'];
            $cat['cat_adurl_1']  = $res['cat_adurl_1'];
            $cat['cat_adimg_2']  = $res['cat_adimg_2'];
            $cat['cat_adurl_2']  = $res['cat_adurl_2'];
            $cat['cat_nameimg']  = $res['cat_nameimg'];
            $cat['is_top_style']  = $res['is_top_style'];
            $cat['is_top_show']  = $res['is_top_show'];
            
            $db->autoExecute($GLOBALS['ecs']->table('category'), $cat, 'INSERT');
            $cat['parent_id'] = $db->insert_id();
            $host_parent_id = 0;
            
            $agency_attr['cat_id'] =  $cat['parent_id']; 
            $agency_attr['is_show'] = $res['is_show'];
            $agency_attr['filter_attr'] = $res['filter_attr'];       
            $agency_attr['admin_agency_id'] =  $admin_agency_id; 
            $agency_attr['is_show'] = 1; 
            $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('category_attribute'), $agency_attr, 'INSERT');
            
            $not_hot_cat = true;
            //continue;
            
        }
 
    } 
    if($not_hot_cat == false)
    {
        /* 判断该分类是否已经被引用了 */
        make_json_error("该分类已经被代理商站点引用过");
    }
    else 
    {
       /* 提示信息,引用该分类成功 */
       make_json_result($val);
    }
    
    
    
    
    //make_json_result($val);
}

/*ccx 2015-03-17 end*/


/*------------------------------------------------------ */
//-- PRIVATE FUNCTIONS
/*------------------------------------------------------ */
//
///**
// * 检查分类是否已经存在
// *
// * @param   string      $cat_name       分类名称
// * @param   integer     $parent_cat     上级分类
// * @param   integer     $exclude        排除的分类ID
// *
// * @return  boolean
// */
//function cat_exists($cat_name, $parent_cat, $exclude = 0)
//{
//    $sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('category').
//           " WHERE parent_id = '$parent_cat' AND cat_name = '$cat_name' AND cat_id<>'$exclude'";
//    return ($GLOBALS['db']->getOne($sql) > 0) ? true : false;
//}

/**
 * 获得商品分类的所有信息
 *
 * @param   integer     $cat_id     指定的分类ID
 *
 * @return  mix
 */
function get_cat_info($cat_id)
{
	$admin_agency_id = admin_agency_id();
    $sql = "SELECT * FROM " .$GLOBALS['ecs']->table('category'). " WHERE cat_id='$cat_id' LIMIT 1";
	$res = $GLOBALS['db']->getRow($sql);
	/* 根据代理商过滤分类信息 */
	if($admin_agency_id)
	{
		$agency_res = $GLOBALS['db']->getRow("SELECT grade,filter_attr,show_in_nav,is_show FROM ".
		$GLOBALS['ecs']->table('category_attribute')." WHERE cat_id = $cat_id AND admin_agency_id = $admin_agency_id");
		if($agency_res)
		{
			$res['grade']		 = $agency_res['grade'];
			$res['filter_attr']  = $agency_res['filter_attr'];
			$res['show_in_nav']  = $agency_res['show_in_nav'];
			$res['is_show'] 	 = $agency_res['is_show'];
			$res['measure_unit'] = $agency_res['measure_unit'];
			//$res['sort_order']   = $agency_res['sort_order'];
		}
		else
		{
			$GLOBALS['smarty']->assign('agency_add','1');
		}
	}
    return $res;
}

/**
 * 添加商品分类
 *
 * @param   integer $cat_id
 * @param   array   $args
 *
 * @return  mix
 */
function cat_update($cat_id, $args)
{
    if (empty($args) || empty($cat_id))
    {
        return false;
    }

    return $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('category'), $args, 'update', "cat_id='$cat_id'");
}


/**
 * 获取属性列表
 *
 * @access  public
 * @param
 *
 * @return void
 */
function get_attr_list()
{
    $sql = "SELECT a.attr_id, a.cat_id, a.attr_name ".
           " FROM " . $GLOBALS['ecs']->table('attribute'). " AS a,  ".
           $GLOBALS['ecs']->table('goods_type') . " AS c ".
           " WHERE  a.cat_id = c.cat_id AND c.enabled = 1 ".
           " ORDER BY a.cat_id , a.sort_order";

    $arr = $GLOBALS['db']->getAll($sql);

    $list = array();

    foreach ($arr as $val)
    {
        $list[$val['cat_id']][] = array($val['attr_id']=>$val['attr_name']);
    }

    return $list;
}

/**
 * 插入首页推荐扩展分类
 *
 * @access  public
 * @param   array   $recommend_type 推荐类型
 * @param   integer $cat_id     分类ID
 *
 * @return void
 */
function insert_cat_recommend($recommend_type, $cat_id)
{
	$admin_agency_id = admin_agency_id();//代理商user_id
    //检查分类是否为首页推荐
    if (!empty($recommend_type))
    {
        //取得之前的分类
        $recommend_res = $GLOBALS['db']->getAll("SELECT recommend_type FROM " . $GLOBALS['ecs']->table("cat_recommend") . " WHERE cat_id=" . $cat_id. " AND admin_agency_id = $admin_agency_id");
        if (empty($recommend_res))
        {
            foreach($recommend_type as $data)
            {
                $data = intval($data);
                $GLOBALS['db']->query("INSERT INTO " . $GLOBALS['ecs']->table("cat_recommend") .
				"(cat_id, recommend_type,admin_agency_id) VALUES ('$cat_id', '$data',$admin_agency_id)");
            }
        }
        else
        {
            $old_data = array();
            foreach($recommend_res as $data)
            {
                $old_data[] = $data['recommend_type'];
            }
            $delete_array = array_diff($old_data, $recommend_type);
            if (!empty($delete_array))
            {
                $GLOBALS['db']->query("DELETE FROM " . $GLOBALS['ecs']->table("cat_recommend") . " WHERE cat_id=$cat_id AND recommend_type " . db_create_in($delete_array) ." AND admin_agency_id = $admin_agency_id");
            }
            $insert_array = array_diff($recommend_type, $old_data);
            if (!empty($insert_array))
            {
                foreach($insert_array as $data)
                {
                    $data = intval($data);
                    $GLOBALS['db']->query("INSERT INTO " . $GLOBALS['ecs']->table("cat_recommend") .
					"(cat_id, recommend_type,admin_agency_id) VALUES ('$cat_id', '$data',$admin_agency_id)");
                }
            }
        }
    }
    else
    {
        $GLOBALS['db']->query("DELETE FROM ". $GLOBALS['ecs']->table("cat_recommend") .
		" WHERE cat_id=" . $cat_id ." AND admin_agency_id = $admin_agency_id");
    }
}









?>