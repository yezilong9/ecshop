<?php

/**
 *  特产专区文件，与首页更多特产链接
 * ============================================================================
 * * 版权所有 2005-2014 广州新泛联数码有限公司，并保留所有权利。
 * 网站地址: http://www..com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: auction.php 17217 2011-01-19 06:29:08Z liubo $
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
include(dirname(__FILE__) . '/includes/cls_json.php');
    $json = new JSON;
$smarty->assign('categories_pro',  get_categories_tree_pro()); // 分类树加强版/* 周改 */
$smarty->assign('navigator_list',get_navigator($ctype, $catlist));  //自定义导航栏
$smarty->assign('helps', get_shop_help());       // 网店帮助

$act = isset($_REQUEST['act']) ? trim($_REQUEST['act']):'';
//echo 'bbb'.$act;
//echo ROOT_PATH;

/*------------------------------------------------------ */
//-- 初始化special_product.dwt页面
/*------------------------------------------------------ */
$page_items = 3;//每页需要显示的数据题目数量

if ($act == '')
{
   //get_special_goods(6);
   $province = get_province();//获取用户所在的省份
   $smarty->assign('province',$province);
   
   $total_items=get_total_items($province['province_id']);//获取用户所在省份的特产商品总数
  
   $page_str = $total_items > $page_items ? get_page_str($province['province_id'],$total_items,$page_items,1) : '';
   $smarty->assign('page_str',$page_str);//获取特产商品列表
   
   $condition = array();//获取特产商品的条件
   $condition = array(
   						'province_id'=>$province['province_id'],
						'begin_item'=>0,
						'end_item'=>$page_items
						);
   $smarty->assign('special_goods',get_special_goods($condition));//获取特产商品列表
   
   $ads_list = get_special_ads();//获取广告列表
   //dump($ads_list);
   //echo $ads_num = count($ads_list)-1;
   $smarty->assign('ads_list',$ads_list);
   //$smarty->assign('ads_num',$ads_num);
   $smarty->assign('provinces_list',get_regions(1,1));//获取省份列表
   $smarty->assign('root_path',ROOT_PATH);
   assign_template();
   $smarty->display('special_product.dwt');
}

/*------------------------------------------------------ */
//-- 某一省份的商品列表
/*------------------------------------------------------ */
elseif ($act == 'province_special')
{//echo $act;
    $province_id = isset($_REQUEST['province_id']) ? intval($_REQUEST['province_id']):0;
	$current_pages = isset($_REQUEST['page']) ? intval($_REQUEST['page']):1;
	
	
	if($province_id){
		$result=array();
		
		$sql_province_name = "SELECT region_name FROM ". $GLOBALS['ecs']->table('region') ." WHERE region_type=1  AND  region_id=$province_id ";
		$province_name = $GLOBALS['db']->getOne($sql_province_name);
		$result['province']=array('province_id'=>$province_id,'province_name'=>$province_name);
		
		$total_items=get_total_items($province_id);//获取用户所在省份的特产商品总数
		$page_str = $total_items > $page_items ? get_page_str($province_id,$total_items,$page_items,$current_pages) : '';
		
		$result['page_str'] = $page_str;//获取分页列表
		
		$begin_item = ($current_pages - 1)*$page_items;
		$condition = array();//获取特产商品的条件
		$condition = array(
							'province_id'=>$province_id,
							'begin_item'=>$begin_item,
							'end_item'=>$page_items
							);
	
		$result['special_goods'] = get_special_goods($condition);//获取特产商品列表
		
		
		//$result[''] = get_special_goods($province_id);
		
		//dump($result);
		echo $json->encode($result);
	
	}else{
		echo "<script>alert('你所所选择的省份不存在！')</script>";
		exit;
	}
	
	
	
}


/**
 * 获取特产专区页面中部的广告
 * @return  array
 */
function get_special_ads()
{
	/*广告位名称，必须与管理后台->广告管理->新广告位置中添加的 广告位名称 相同 */
	$ad_position = '特产专区页面中部轮播广告[1224x418]';
	/*获取广告位名称id*/
	$sql_ad_position_id = "SELECT id FROM ".$GLOBALS['ecs']->table('ad_new_position')." WHERE position_name = '$ad_position' ";
	$ad_position_id = $GLOBALS['db']->getOne($sql_ad_position_id);
	
	$sql_ad = "SELECT url,img FROM ".$GLOBALS['ecs']->table('ad_new')." WHERE position_id = $ad_position_id AND start=1 ";
	return $GLOBALS['db']->getAll($sql_ad);
	
}

/**
 * 获取客户所在省份
 * @return  array
 */
function get_province(){
	$ip_province=ipCity();
	$ip_province_name = $ip_province['region'];
	if( $ip_province_name == '黑龙江省' || $ip_province_name == '内蒙古自治区'){
		$province_name = mb_substr($ip_province_name,0,3,'utf-8');
	}else{
		$province_name = mb_substr($ip_province_name,0,2,'utf-8');
	}
	
	$sql_province_id = "SELECT region_id FROM ". $GLOBALS['ecs']->table('region') ." WHERE region_type=1  AND  region_name='$province_name' ";
	$province_id = $GLOBALS['db']->getOne($sql_province_id);
	$p = array();
	$p = array('province_id'=>$province_id,'province_name'=>$province_name);
	return $p;
}

/**
 * 获取某一个省份的特产商品
 * @return  array
 */
function get_special_goods($condition){
	if($_SESSION['user_rank']){
		$time = gmtime();
		$sql_special_goods = 'SELECT g.goods_id, g.goods_name,g.sort_order, g.last_update,g.is_special,g.market_price, g.shop_price AS org_price, g.promote_price, g.province_id, g.province_name, g.city_id, g.city_name, g.area_id, g.area_name,  ' .
                "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, ".
                "g.promote_start_date, g.promote_end_date, g.goods_brief, g.goods_thumb, g.goods_img, RAND() AS rnd " .
                'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
                "LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp ".
                " ON  mp.goods_id = g.goods_id WHERE mp.user_rank = '$_SESSION[user_rank]' AND g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 AND g.is_special = 1 AND g.province_id = '{$condition[province_id]}'  ORDER BY g.sort_order, g.last_update DESC LIMIT {$condition['begin_item']},{$condition['end_item']} ";
	}else{
		$sql_special_goods =" SELECT goods_id, goods_name,sort_order, last_update,is_special,market_price, shop_price,promote_price,promote_start_date, promote_end_date,goods_thumb, province_id, province_name, city_id, city_name, area_id, area_name,goods_img FROM ". $GLOBALS['ecs']->table('goods') ." WHERE is_on_sale = 1 AND is_alone_sale = 1 AND is_delete = 0 AND is_special = 1 AND province_id = '{$condition[province_id]}' ORDER BY sort_order, last_update DESC LIMIT {$condition['begin_item']},{$condition['end_item']} ";
	
	}
	
	$special_goods = $GLOBALS['db']->getAll($sql_special_goods);
	foreach($special_goods as $k=>$v){
		$special_goods[$k]['short_name']=mb_substr($v['goods_name'],0,5,'utf-8');
		if ($v['promote_price'] > 0)
            {
                $promote_price = bargain_price($v['promote_price'], $v['promote_start_date'], $v['promote_end_date']);
                $special_goods[$k]['promote_price'] = $promote_price > 0 ? number_format($promote_price, 2, '.', '') : '';
            }
	}
	
	
	//dump($special_goods);
	return $special_goods;
	
}



function get_total_items($province_id){
	if($_SESSION['user_rank']){
		
		$sql_special_goods = 'SELECT COUNT(*) FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
                "LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp ".
                " ON  mp.goods_id = g.goods_id WHERE mp.user_rank = '$_SESSION[user_rank]' AND g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 AND g.is_special = 1 AND g.province_id = '$province_id'  ORDER BY g.sort_order, g.last_update DESC ";
	}else{
		$sql_special_goods =" SELECT COUNT(*) FROM ". $GLOBALS['ecs']->table('goods') ." WHERE is_on_sale = 1 AND is_alone_sale = 1 AND is_delete = 0 AND is_special = 1 AND province_id = '$province_id' ORDER BY sort_order, last_update DESC ";
	
	}
	
	$special_goods_items = $GLOBALS['db']->getOne($sql_special_goods);
	
	return $special_goods_items;
	
}


	
function get_page_str($province_id,$total_items,$page_items,$current_pages){
	
	$page_flag = 10;//在页面上最多显示10个页码
	$total_pages = ceil($total_items/$page_items);
	$previous_page = ($current_pages - 1) ? ($current_pages - 1) : 1;
	$next_page = ($current_pages + 1) < $total_pages ? ($current_pages + 1) : $total_pages;
	
	$page_str = '';
	$page_str .= "<ul><li class='page_li' onmouseover='change_style(this)' onclick='get_province_special(".$province_id.",".$previous_page.");'  style='width:auto;'>&lt;&lt;上一页</li>";
	
	for($i=0;$i<$total_pages;$i++){
		if($i == $page_flag ){
			break;
		}elseif($i == ($current_pages-1)){
			$page_str .= "<li class='page_li on_visited'  onmouseover='change_style(this)'  onclick='get_province_special(".$province_id.",".($i+1).");'>".($i+1)."</li>";
		}else{
			$page_str .= "<li class='page_li'  onmouseover='change_style(this)' onclick='get_province_special(".$province_id.",".($i+1).");'>".($i+1)."</li>";
		}
	}
	
	$page_str .= "<li class='page_li'  onmouseover='change_style(this)' onclick='get_province_special(".$province_id.",".$next_page.");'  style='width:auto;'>下一页&gt;&gt;</li></ul>";
	
	return $page_str;
	
}




?>