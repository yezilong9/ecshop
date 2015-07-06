<?php
/*
* 搜物网商品模块
* add by hg for date 2014-05-19
* 搜物网标识：sou;(用于商品表，记录供货商)
*/
set_time_limit(0);
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . '/' . ADMIN_PATH . '/includes/lib_goods.php');
include_once(ROOT_PATH . '/includes/cls_image.php');

if($_REQUEST['act'] == 'list')
{
	admin_priv('souwu');
	//vdump(cat_list(0, $cat_id));

	
	$smarty->assign('cat_list',     cat_list(0, $cat_id));
	//dump();
	$smarty->assign('ur_here',      $_LANG['souwu']);
	$souwu_goods_list = souwu_goods_list();
	$smarty->assign('filter',       $souwu_goods_list['filter']);
	$smarty->assign('record_count', $souwu_goods_list['record_count']);
	$smarty->assign('page_count',   $souwu_goods_list['page_count']);
	$smarty->assign('full_page',    1);
	assign_query_info();
	$smarty->assign('goods_res',$souwu_goods_list['goods_res']);
	$smarty->display('souwu_goods_list.htm');
}
elseif($_REQUEST['act'] == 'query')
{
	$souwu_goods_list = souwu_goods_list();
	$smarty->assign('goods_res',  $souwu_goods_list['goods_res']);
	$smarty->assign('filter',       $souwu_goods_list['filter']);
	$smarty->assign('record_count', $souwu_goods_list['record_count']);
	$smarty->assign('page_count',   $souwu_goods_list['page_count']);
    /* 排序标记 */
    $sort_flag  = sort_flag($souwu_goods_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);
	make_json_result($smarty->fetch('souwu_goods_list.htm'),'',
	array('filter' => $souwu_goods_list['filter'], 'page_count' => $souwu_goods_list['page_count']));
}
elseif($_REQUEST['act'] == 'batch')
{
	
	if(!empty($_POST['checkboxes']) && !empty($_POST['type']))
	{
		foreach($_POST['checkboxes'] as $k=>$v){
			$Arr = array();
			$ArrImg = array();
			$souwu_res = $db->getRow("select producttitle,markprice,costprice,productnum,goods_thum,goods_img,original_img,productdesc,skulist,gallerylist,props from "
			 .$ecs->table('souwu_goods'). "where id = $v AND and_add = 0");
			if(!$souwu_res)
			{
				continue;
			}
			//处理图片
			//dump($souwu_res['original_img']);
			$imgArr = get_img($souwu_res['original_img']);
		
			
			 //商品表
			$Arr['cat_id'] = $_POST['type'];
			$goods_sn               = generate_goods_sn($db->getOne("SELECT MAX(goods_id) + 1 FROM ".$ecs->table('goods')));
			$Arr['goods_sn']        =  $goods_sn;
			$Arr['goods_name']      =  $souwu_res['producttitle'];
			$Arr['goods_number']    =  $souwu_res['productnum'];
			$Arr['market_price']    =  $souwu_res['markprice'];
			$Arr['shop_price']      =  $souwu_res['costprice'];
			$Arr['keywords']        =  $souwu_res['producttitle'];
			//$Arr['goods_desc']      =  goods_desc($souwu_res['productdesc']);
			$Arr['goods_desc']      =  $souwu_res['productdesc'];
			$Arr['goods_thumb']     =  $imgArr['goods_thumb'];
			$Arr['goods_img']       =  $imgArr['goods_img'];
			$Arr['original_img']    =  $imgArr['original_img'];
			$Arr['add_time']        =  time();
			$Arr['last_update']     =  time();
			$Arr['goods_type']      =  '12';
			$Arr['wholesale_price'] =  $souwu_res['costprice'];
			$Arr['costing_price']   =  $souwu_res['costprice'];
			$Arr['supply_sign_id']   =  'sou_'.$v;
			$db->autoExecute($ecs->table('goods'), $Arr, 'INSERT');
			//修改供应产品状态
			$goods_id = $db->insert_id();
			$db->query("UPDATE " . $GLOBALS['ecs']->table('souwu_goods') . " SET and_add = 1 WHERE id = $v");

			//属性表
			$attribute['cat_id'] = '12';
			if($souwu_res['props'])
			{
				$props = unserialize($souwu_res['props']);
				foreach($props as $props_k=>$props_v){
					$attr_id = '';
					$attr_id = $db->getOne("select attr_id from " .$ecs->table('attribute'). "where attr_name = '$props_v[name]'");
					if($attr_id)
					{
						$goods_attr['goods_id'] = $goods_id;
						$goods_attr['attr_id'] = $attr_id;
						$goods_attr['attr_value'] = $props_v['value'];
						$db->autoExecute($ecs->table('goods_attr'), $goods_attr, 'INSERT');
					}else{
						$attribute['attr_name'] = $props_v['name'];
						$attribute['attr_input_type'] = '0';
						$attribute['attr_type'] = '0';
						$db->autoExecute($ecs->table('attribute'), $attribute, 'INSERT');
						$attr_id = $db->insert_id();
						$goods_attr['goods_id'] = $goods_id;
						$goods_attr['attr_id'] = $attr_id;
						$goods_attr['attr_value'] = $props_v['value'];
						$db->autoExecute($ecs->table('goods_attr'), $goods_attr, 'INSERT');
					}
					//处理品牌
					if($props_v['name'] == '品牌')
					{
						$brand_id = '';
						$brand_id = $db->getOne("select brand_id from " .$ecs->table('brand'). "where brand_name = '$props_v[value]'");
						if($brand_id)
						{
							$db->query("UPDATE " . $GLOBALS['ecs']->table('goods') . " SET brand_id = $brand_id WHERE goods_id = $goods_id");
						}else{
							$brandArr['brand_name'] = $props_v['value'];
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
			$ArrImg['goods_id']     = $goods_id;
			$ArrImg['img_url']      = $imgArr['original_img'];
			$ArrImg['thumb_url']    = $imgArr['goods_thumb'];
			$ArrImg['img_original'] = $imgArr['original_img'];
			$db->autoExecute($ecs->table('goods_gallery'), $ArrImg, 'INSERT');
			if($souwu_res['gallerylist'])
			{
				$img = unserialize($souwu_res['gallerylist']);
				foreach($img as $img_k=>$img_v){
					$ArrImg = array();
					//处理图片
					$goods_gallery_Arr = get_img($img_v['img_url']);
					$ArrImg['goods_id']     = $goods_id;
					$ArrImg['img_url']      = $goods_gallery_Arr['original_img'];
					$ArrImg['thumb_url']    =  $goods_gallery_Arr['goods_thumb'];
					$ArrImg['img_original'] = $goods_gallery_Arr['original_img'];
					$db->autoExecute($ecs->table('goods_gallery'), $ArrImg, 'INSERT');
				}
			}
			
			//货物表
			if($souwu_res['skulist'])
			{
				$sku = unserialize($souwu_res['skulist']);
				//dump($sku);
				foreach($sku as $sku_k=>$sku_v){
					$attrArr = array();
					if($sku_v['skuquantity'] > 0 && $sku_v['Isdelisting'] == 'true')
					{
						
						//goods_attr表
						$attrArr['goods_id']   = $goods_id;
						$attrArr['attr_id']    = '212';
						$attrArr['attr_value'] = $sku_v['skuattrname'];
						$db->autoExecute($ecs->table('goods_attr'), $attrArr, 'INSERT');
						
						//货物表
						$attr_id = $db->insert_id();
						$proArr['goods_id'] = $goods_id;
						$proArr['goods_attr'] = $attr_id;
						$proArr['product_number'] = $sku_v['skuquantity'];
						
						$db->autoExecute($ecs->table('products'), $proArr, 'INSERT');
						
						$sql = "UPDATE " . $ecs->table('products') . "
						SET product_sn = '" . $goods_sn . "g_p" . $db->insert_id() . "'
						WHERE product_id = '" . $db->insert_id() . "'";
						$db->query($sql);
					}
				}
			}
		}
		$link[] = array('href' => 'souwu.php?act=list', 'text' => '供货列表');
		sys_msg('操作成功', 0, $link);
	}
	else
	{
		header('location:souwu.php?act=list');
	}
}
function souwu_goods_list()
{
	$GLOBALS['db']->query('set names utf8');
    $filter['productcatename']          = empty($_REQUEST['productcatename']) ? '' : trim($_REQUEST['productcatename']);
    $filter['keyword']          = empty($_REQUEST['keyword']) ? '' : trim($_REQUEST['keyword']);
	$filter['sort_by']          = empty($_REQUEST['sort_by']) ? 'id' : trim($_REQUEST['sort_by']);
	$filter['sort_order']       = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);


	/* 关键字 */
	if (!empty($filter['keyword']))
	{
		$where .= " AND (producttitle LIKE '%" . mysql_like_quote($filter['keyword']) . "%' OR productcatenamestr LIKE '%" . mysql_like_quote($filter['keyword']) . "%')";
	}
	if (!empty($filter['productcatename']))
	{
		$where .= " AND productcatename = $filter[productcatename]";
	}
	$sql = "SELECT  COUNT(*) FROM  " . $GLOBALS['ecs']->table('souwu_goods') . "WHERE showtype = 1 and productnum > 0 $where order by $filter[sort_by] $filter[sort_order]";
	$filter['record_count'] = $GLOBALS['db']->getOne($sql);
	$filter = page_and_size($filter);
	
	$goods_res_sql = "SELECT productcatename,and_add,id,producttitle, markprice,costprice,productnum,productcatenamestr,classPath FROM  " . $GLOBALS['ecs']->table('souwu_goods') . " WHERE showtype = 1 and productnum > 0 $where order by $filter[sort_by] $filter[sort_order]  LIMIT ". $filter['start'] .", " . $filter['page_size'] ."";
	$filter['keywords'] = stripslashes($filter['keywords']);
	set_filter($filter, $goods_res_sql);
	$goods_res = $GLOBALS['db']->getAll($goods_res_sql);
	cate($filter['productcatename']);
	$arr = array('goods_res' => $goods_res, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
	return $arr;
}
/**
* 处理url图片
* @$img_url 图片地址
* @$mark  	是否处理缩略图 1不处理
**/
function get_img($img_url = '',$mark='0')
{

	$cls_imageobj = new cls_image();
	if(strstr($img_url,'http://'))
	{
		$data = file_get_contents($img_url); 
		$dir = date('Ym');
		$filename = cls_image::random_filename(); 
		$imgDir = $cls_imageobj->images_dir . '/' . $dir . '/source_img/'.$filename.'.jpg';
		$dir = ROOT_PATH . $imgDir;
		
		$fp = @fopen($dir,"w"); 
		@fwrite($fp,$data); 
		fclose($fp);
	}else{
		$imgDir = $img_url;
	}
	if($mark=='0')
	{
		//处理缩略图
		$goods_thumb = '';
		$goods_img = '';
		$goods_thumb  = $cls_imageobj->make_thumb('http://o2o.txd168.com/'.$imgDir,"170",'170');
		$goods_img    = $cls_imageobj->make_thumb('http://o2o.txd168.com/'.$imgDir,"300",'300');
		return array('original_img'=>$imgDir,'goods_thumb'=>$goods_thumb,'goods_img'=>$goods_img);
	}
	elseif($mark=='1')
	{
		return $imgDir;
	}
}
/**
* 处理商品详情图片
* @$desc 商品详情
*/
function goods_desc($desc)
{
	$desc = geshiimg('jpg',$desc);
	$desc = geshiimg('gif',$desc);
	$desc = geshiimg('png',$desc);
	return $desc;
}
/*
* 根据图片各市匹配
* @$geshi 图片格式，如.jpg
* @$desc  商品详情
*/
function geshiimg($geshi,$desc)
{
	preg_match_all('|http:(.*?).'.$geshi.'|',$desc,$productdesc_img, PREG_SET_ORDER);
	
	if($productdesc_img)
	{
		foreach($productdesc_img as $productdesc_img_k=>$productdesc_img_v)
		{
			$img_str = strstr($productdesc_img_v[0],'http://');
			if($img_str)
			{
				$productdesc_new_img = get_img($productdesc_img_v[0],'1');
				//dump($productdesc_new_img);
				$desc = preg_replace("|$productdesc_img_v[0]|",$productdesc_new_img,$desc);
			}
		}
	}
	return $desc;
}

function cate($productcatename)
{
	$goods_res_sql = "SELECT productcatename,productcatenamestr FROM  " . $GLOBALS['ecs']->table('souwu_goods') . " WHERE showtype = 1 and productnum > 0 and and_add=0";
	$cate = array();
	$goods_res = $GLOBALS['db']->getAll($goods_res_sql);
	foreach($goods_res as $k=>$v){
		$cate[$v['productcatename']] = $v['productcatenamestr'];
	}
	$GLOBALS['smarty']->assign('category',   $cate);
	$GLOBALS['smarty']->assign('productcatename',   $productcatename);
}
?>