<?php
/********************************************
天猫产品数据导入
time:2014-07-12

********************************************/

define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . '/' . ADMIN_PATH . '/includes/lib_goods.php');
include_once(ROOT_PATH . '/includes/cls_image.php');
$obj_tm_godos = new class_tm_user_goods();
$xi = isset($_REQUEST['xi'])?$_REQUEST['xi']:'';
if(!$xi) return false;
//$obj = new lu_compile();
//$res = $obj->decrypt(urldecode($xi));
$obj_tm_godos->tm_log('完成接收数据');
parse_str($xi,$res);
//dump($res);
//$obj_tm_godos->tm_log($xi);
$user_name = TMUSER.$res['account'];
$user_id = $db->getOne("select agency_user_id from ".$ecs->table('admin_user')." where user_name='$user_name'");

/* 特殊处理账号 */
if(!$user_id && ($res['account'] != 'liaosl'))
{
	$obj_tm_godos->tm_log('会员不存在');
	echo 0;die;
}
if(!$user_id) $user_id = 0;
if(is_array($res['print_list'])){
	foreach($res['print_list'] as $key=>$value){
	//dump(implode('',unserialize(urldecode($value['detail_img_url']))));
		//检测是否重复提交
		$supply_sign_id = 'tm_'.$value['item_id'];
		$old_goods_id = $db->getOne("select goods_id from ".$ecs->table('goods')." where admin_agency_id = $user_id and supply_sign_id = '$supply_sign_id'");
		if(!$old_goods_id){
			/*商品类型 */
			$goods_type_id = $obj_tm_godos->di_goods_type($value['category']);
			/*商品分类*/
			$cat_id = $db->getOne("select cat_id from ".$ecs->table('category')." where cat_name = '$value[category]'");
			$goods_sn               = generate_goods_sn($db->getOne("SELECT MAX(goods_id) + 1 FROM ".$ecs->table('goods')));
			$is_on_sale = $cat_id?'1':'0';
			$Arr = array(
				'cat_id'       => $cat_id?$cat_id:'0',
				'goods_sn'     => $goods_sn,
				'goods_name'   => $value['name'],
				'goods_number' => '0',
				'market_price' => $value['price']*1.2,
				'shop_price'   => $value['price_show'],
				'keywords'     => $value['name'],
				'goods_desc'   => $obj_tm_godos->di_goods_desc(unserialize(urldecode($value['detail_img_url']))),
				'goods_thumb'  => $value['pic_url'],
				'goods_img'    => $value['pic_url_big'],
				'original_img' => $value['pic_url_big'],
				'add_time'     => time(),
				'last_update'  => time(),
				'suppliers_id' => '3',
				'goods_type'   => $goods_type_id,
				'wholesale_price'   => $value['price'],
				'costing_price'     => $value['price'],
				'supply_sign_id'    => $supply_sign_id,
				'is_on_sale'        => $is_on_sale,
				'admin_agency_id'    => $user_id,
			);
			$db->autoExecute($ecs->table('goods'), $Arr, 'INSERT');
			$goods_id = $db->insert_id();
			//图片
			$ArrImg['goods_id']     = $goods_id;
			$ArrImg['img_url']      = $value['pic_url_big'];
			$ArrImg['thumb_url']    = $value['pic_url'];
			$ArrImg['img_original'] = $value['pic_url_big'];
			$db->autoExecute($ecs->table('goods_gallery'), $ArrImg, 'INSERT');
			$obj_tm_godos->tm_log('插入商品成功',$value['item_id']);
			/* 商品可选属性,就是货物了 */
			$obj_tm_godos->di_goods_pro($goods_sn,$goods_id,$goods_type_id,unserialize(urldecode($value['colour_sort'])));
			$obj_tm_godos->tm_log('插入货物成功',$value['item_id']);
			/* 商品属性 */
			$obj_tm_godos->di_goods_attr($goods_sn,$goods_id,$goods_type_id,unserialize(urldecode($value['parameter'])));
			$obj_tm_godos->tm_log('插入商品属性成功',$value['item_id']);
			/* 商品图片 */
			$obj_tm_godos->di_goods_gallery($goods_id,unserialize(urldecode($value['img_url'])));
			$obj_tm_godos->tm_log('插入商品图片成功',$value['item_id']);
		}else{
			$market_price = $value['price']*1.2;
			$db->query("update ".$ecs->table('goods')." set market_price='$market_price',shop_price='$value[price]' where goods_id=$old_goods_id");
			$db->autoExecute($ecs->table('goods_gallery'), $ArrImg, 'INSERT');
			$obj_tm_godos->tm_log('商品已存在',$value['item_id']);
		}
	}
	echo 1;die;
}
echo 0;

?>