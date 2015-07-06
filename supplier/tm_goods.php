<?php
/*
* 搜物网商品模块
* add by hg for date 2014-05-19
* 搜物网标识：tm;(用于商品表，记录供货商)
*/
set_time_limit(0);
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . '/' . ADMIN_PATH . '/includes/lib_goods.php');
include_once(ROOT_PATH . '/includes/cls_image.php');

if($_REQUEST['act'] == 'list')
{
	admin_priv('tm');
	//vdump(cat_list(0, $cat_id));

	
	$smarty->assign('cat_list',     cat_list(0, $cat_id));
	//dump();
	$smarty->assign('ur_here',      $_LANG['souwu']);
	$tm_goods_list = tm_goods_list();
	$smarty->assign('filter',       $tm_goods_list['filter']);
	$smarty->assign('record_count', $tm_goods_list['record_count']);
	$smarty->assign('page_count',   $tm_goods_list['page_count']);
	$smarty->assign('full_page',    1);
	assign_query_info();
	$smarty->assign('goods_res',$tm_goods_list['goods_res']);
	$smarty->display('tm_goods_list.htm');
}
elseif($_REQUEST['act'] == 'query')
{
	$tm_goods_list = tm_goods_list();
	$smarty->assign('goods_res',  $tm_goods_list['goods_res']);
	$smarty->assign('filter',       $tm_goods_list['filter']);
	$smarty->assign('record_count', $tm_goods_list['record_count']);
	$smarty->assign('page_count',   $tm_goods_list['page_count']);
    /* 排序标记 */
    $sort_flag  = sort_flag($tm_goods_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);
	make_json_result($smarty->fetch('tm_goods_list.htm'),'',
	array('filter' => $tm_goods_list['filter'], 'page_count' => $tm_goods_list['page_count']));
}
elseif($_REQUEST['act'] == 'batch')
{
	
	if(!empty($_POST['checkboxes']) && !empty($_POST['type']))
	{
		foreach($_POST['checkboxes'] as $k=>$v){
			
			$Arr = array();
			$ArrImg = array();
			$tm_res = $db->getRow("select product_title,product_price,price,parameter,colour_sort,image,detail_imgs,tmall_product_id from "
			 .$ecs->table('tm_goods'). "where id = $v AND and_add = 0");
			if(!$tm_res)
			{
				continue;
			}

			 //商品表
			$Arr['cat_id'] = $_POST['type'];
			$goods_sn               = generate_goods_sn($db->getOne("SELECT MAX(goods_id) + 1 FROM ".$ecs->table('goods')));
			
			$Arr['goods_sn']        =  $goods_sn;
			$Arr['goods_name']      =  $tm_res['product_title'];
			$Arr['goods_number']    =  '9999';
			$Arr['market_price']    =  $tm_res['product_price'];
			$Arr['shop_price']      =  $tm_res['price'];
			$Arr['keywords']        =  $tm_res['product_title'];
			//商品详情图片
			if($tm_res['detail_imgs'])
			$Arr['goods_desc']      =  tm_goods_desc(unserialize($tm_res['detail_imgs']));
			
			//商品图片
			$new_goods_img = tm_goods_image(unserialize($tm_res['image']));
			$Arr['goods_thumb']     =  $new_goods_img[0]['goods_thumb'];
			$Arr['goods_img']       =  $new_goods_img[0]['goods_img'];
			$Arr['original_img']    =  $new_goods_img[0]['goods_img'];
			$Arr['add_time']        =  time();
			$Arr['last_update']     =  time();
			$Arr['goods_type']      =  '12';
			$Arr['wholesale_price'] =  $tm_res['price'];
			$Arr['costing_price']   =  $tm_res['price'];
			$Arr['supply_sign_id']   =  'tm_'.$v;
			$db->autoExecute($ecs->table('goods'), $Arr, 'INSERT');
			//修改供应产品状态
			$goods_id = $db->insert_id();
			$db->query("UPDATE " . $GLOBALS['ecs']->table('tm_goods') . " SET and_add = 1 WHERE id = $v");
			//属性表
			$attribute['cat_id'] = '12';
			if($tm_res['parameter'])
			{
				$props = unserialize($tm_res['parameter']);
				if(is_array($props))
				foreach($props as $props_k=>$props_v){
					$proArr = explode(':',$props_v);
					
					if(count($proArr) < 2)
					$proArr = explode('：',$props_v);
					$attr_id = '';
					$attr_id = $db->getOne("select attr_id from " .$ecs->table('attribute'). "where attr_name = '$proArr[0]' and attr_type = 0");
					if($attr_id)
					{
						$goods_attr['goods_id'] = $goods_id;
						$goods_attr['attr_id'] = $attr_id;
						$goods_attr['attr_value'] = $proArr[1];
						$db->autoExecute($ecs->table('goods_attr'), $goods_attr, 'INSERT');
					}else{
						$attribute['attr_name'] = $proArr[0];
						$attribute['attr_input_type'] = '0';
						$attribute['attr_type'] = '0';
						$db->autoExecute($ecs->table('attribute'), $attribute, 'INSERT');
						$attr_id = $db->insert_id();
						$goods_attr['goods_id'] = $goods_id;
						$goods_attr['attr_id'] = $attr_id;
						$goods_attr['attr_value'] = $proArr[1];
						$db->autoExecute($ecs->table('goods_attr'), $goods_attr, 'INSERT');
					}
					//处理品牌
					if($proArr[0] == '品牌')
					{
						$brand_id = '';
						$brand_id = $db->getOne("select brand_id from " .$ecs->table('brand'). "where brand_name = '$proArr[1]'");
						if($brand_id)
						{
							$db->query("UPDATE " . $GLOBALS['ecs']->table('goods') . " SET brand_id = $brand_id WHERE goods_id = $goods_id");
						}else{
							$brandArr['brand_name'] = $proArr[1];
							$brandArr['sort_order'] = '50';
							$brandArr['is_show'] = '0';
							$db->autoExecute($ecs->table('brand'), $brandArr, 'INSERT');
							$brand_id = $db->insert_id();
							$db->query("UPDATE " . $GLOBALS['ecs']->table('goods') . " SET brand_id = $brand_id WHERE goods_id = $goods_id");
						}
					}
				}
			}
				
			//商品图片表	
			
			krsort($new_goods_img);
			foreach($new_goods_img as $img_k=>$img_v){
				$ArrImg = array();
				//处理图片
				$ArrImg['goods_id']     = $goods_id;
				$ArrImg['img_url']      = $img_v['goods_img'];
				$ArrImg['thumb_url']    = $img_v['goods_thumb'];
				$ArrImg['img_original'] = $img_v['goods_img'];
				$db->autoExecute($ecs->table('goods_gallery'), $ArrImg, 'INSERT');
			}
			
			//处理可选属性
			if($tm_res['colour_sort']){
				$pro_res = unserialize($tm_res['colour_sort']);
				if(is_array($pro_res))
				foreach($pro_res as $pro_res_k=>$pro_res_v){
					$and_attr_id = $db->getOne("select attr_id from " .$ecs->table('attribute'). "where attr_name = '$pro_res_k' and attr_type = 1");
					
					if($and_attr_id)
					{
						foreach($pro_res_v as $pro_key=>$pro_value){
							$goods_pro_attr = '';
							$new_pro_value = '';
							$sta = '';
							$sta = strstr($pro_value,'http://');
							if($sta)
							{
								$pro_value_arr = explode('|',$pro_value);
								$new_pro_value = $pro_value_arr[0].'|'.'<img src='.$pro_value_arr[1].' />';
							}
							$goods_pro_attr['goods_id']   = $goods_id;
							$goods_pro_attr['attr_id'] 	  = $and_attr_id;
							$goods_pro_attr['attr_value'] = $new_pro_value?$new_pro_value:$pro_value;
							$db->autoExecute($ecs->table('goods_attr'), $goods_pro_attr, 'INSERT');
						}
					}else{
						$pro_attribute['attr_name'] = $pro_res_k;
						$pro_attribute['attr_input_type'] = '0';
						$pro_attribute['attr_type'] = '1';
						$db->autoExecute($ecs->table('attribute'), $pro_attribute, 'INSERT');
						$and_attr_id = $db->insert_id();
						foreach($pro_res_v as $pro_key=>$pro_value){
							$goods_pro_attr = '';
							$new_pro_value = '';
							$sta = '';
							$sta = strstr($pro_value,'http://');
							if($sta)
							{
								$pro_value_arr = explode('|',$pro_value);
								$new_pro_value = $pro_value_arr[0].'|'.'<img src'.$pro_value[1].'>';
							}
							
							$goods_pro_attr['goods_id']   = $goods_id;
							$goods_pro_attr['attr_id']    = $and_attr_id;
							$goods_pro_attr['attr_value'] = $new_pro_value?$new_pro_value:$pro_value;
							$db->autoExecute($ecs->table('goods_attr'), $goods_pro_attr, 'INSERT');
						}
					}
				}
			
			}
			
			
		}
		$link[] = array('href' => 'tm_goods.php?act=list', 'text' => '供货列表');
		sys_msg('操作成功', 0, $link);
	}
	else
	{
		header('location:tm_goods.php?act=list');
	}
}
function tm_goods_list()
{
	
    $filter['category']          = empty($_REQUEST['category']) ? '' : trim($_REQUEST['category']);
    $filter['keyword']          = empty($_REQUEST['keyword']) ? '' : trim($_REQUEST['keyword']);
	$filter['sort_by']          = empty($_REQUEST['sort_by']) ? 'id' : trim($_REQUEST['sort_by']);
	$filter['sort_order']       = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);


	/* 关键字 */
	if (!empty($filter['keyword']))
	{
		$where .= " AND (product_title LIKE '%" . mysql_like_quote($filter['keyword']) . "%' OR category LIKE '%" . mysql_like_quote($filter['keyword']) . "%')";
	}
	if (!empty($filter['category']))
	{
		$where .= " AND category = '$filter[category]'";
	}
	$sql = "SELECT  COUNT(*) FROM  " . $GLOBALS['ecs']->table('tm_goods') . " WHERE 1 $where order by $filter[sort_by] $filter[sort_order]";
	$filter['record_count'] = $GLOBALS['db']->getOne($sql);
	$filter = page_and_size($filter);
	
	$goods_res_sql = "SELECT id,tmall_product_id,product_id,product_title,product_price,price,product_code,tmall_category_id,parameter,colour_sort,image,detail_imgs,category,and_add FROM  " . $GLOBALS['ecs']->table('tm_goods') . " WHERE 1 $where order by $filter[sort_by] $filter[sort_order]  LIMIT ". $filter['start'] .", " . $filter['page_size'] ."";
	$filter['keywords'] = stripslashes($filter['keywords']);
	set_filter($filter, $goods_res_sql);
	$goods_res = $GLOBALS['db']->getAll($goods_res_sql);
	tm_category($filter['category']);
	$arr = array('goods_res' => $goods_res, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
	return $arr;
}

/*
* 商品分类
*/
function tm_category($category)
{
	$goods_res_sql = "SELECT category FROM  " . $GLOBALS['ecs']->table('tm_goods') . "where and_add=0";
	$cate = array();
	$goods_res = $GLOBALS['db']->getAll($goods_res_sql);
	foreach($goods_res as $k=>$v){
		$cate[$v['category']] = $v['category'];
	}
	$GLOBALS['smarty']->assign('category',   $cate);
	$GLOBALS['smarty']->assign('present_category',   $category);
}

/*
* 商品详情
*/
function tm_goods_desc($detail_imgs = array())
{
	$goods_desc = '';
	foreach($detail_imgs as $key=>$value){
		$goods_desc .= '<img src='.$value.'>';
	}
	return $goods_desc?$goods_desc:'';
}

/**
* 商品图片
**/
function tm_goods_image($goods_image = array())
{
	$new_image = array();
	for($i=0;$i<count($goods_image[0]);$i++){
		$new_image[$i]['goods_thumb'] = $goods_image[1][$i];
		$new_image[$i]['goods_img']   = $goods_image[0][$i];
	}
	return $new_image?$new_image:'';
}


?>