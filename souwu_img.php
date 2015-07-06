<?php
/*
* 临时处理文件，不作优化不作解释 如需使用，使用后请在文件头加上dump('')禁止访问;
* 搜物网商品图片
* add by hg for date 2014-05-26
* 搜物网标识：sou;(用于商品表，记录供货商)
*/
set_time_limit(0);
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . '/' . ADMIN_PATH . '/includes/lib_goods.php');
include_once(ROOT_PATH . '/includes/cls_image.php');
//禁止访问
dump('');
//获取图片
function get_img($img_url = '')
{
	$cls_imageobj = new cls_image();
	$data = file_get_contents($img_url); 
	$dir = date('Ym');
	$filename = cls_image::random_filename(); 
	$imgDir = $cls_imageobj->images_dir . '/' . $dir . '/source_img/'.$filename.'.jpg';
	$dir = ROOT_PATH . $imgDir;
	$fp = @fopen($dir,"w"); 
	@fwrite($fp,$data); 
	fclose($fp);
	return $imgDir;
}

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
				$productdesc_new_img = get_img($productdesc_img_v[0]);
				$desc = preg_replace("|$productdesc_img_v[0]|",$productdesc_new_img,$desc);
			}
		}
	}
	return $desc;
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

$original_img = $db->getAll("select productdesc,gallerylist,id,original_img from ".$ecs->table('souwu_goods')."");
//处理产品图片
foreach($original_img as $key=>$value){
	$goodsArr     = '';
	$original_img = '';
	$str          = '';
	$img_str      = '';
	$imArr        = '';
	$desc         = '';
	$str          = strstr($value['original_img'],'http://');
	//商品图片
	if($str)
	{
		$original_img = get_img($value['original_img']);
	}
	//图片列表
	if($value['gallerylist'])
	{
		$serImg = array();
		$new_img = array();
		$imArr = unserialize($value['gallerylist']);
		foreach($imArr as $imArr_k=>$imArr_v){
			$img_str = strstr($imArr_v['img_url'],'http://');
			if($img_str)
			{
				$new_img[$imArr_k]['img_url'] = get_img($imArr_v['img_url']);
				$new_img[$imArr_k]['thumb_url'] = $new_img[$imArr_k]['img_url'];
			}
		}
		if($new_img)
		{
			$new_img = serialize($new_img);
		}
	}
	//详情图片
	
	$desc = goods_desc($value['productdesc']);
	
	//更新处理的图片
	if($original_img && $new_img)
	{
		$sql = "UPDATE " . $GLOBALS['ecs']->table('souwu_goods') . " SET original_img = '$original_img ',goods_thum = '$original_img',goods_img = '$original_img',gallerylist = '$new_img',productdesc ='$desc' where id = $value[id]";
	}
	else
	{
		if($original_img)
		{
			$sql = "UPDATE " . $GLOBALS['ecs']->table('souwu_goods') . " SET original_img = '$original_img ',goods_thum = '$original_img',goods_img = '$original_img',productdesc ='$desc' where id = $value[id]";
		}
		elseif($new_img)
		{
			$sql = "UPDATE " . $GLOBALS['ecs']->table('souwu_goods') . " SET gallerylist = '$new_img',productdesc ='$desc' where id = $value[id]";
		}
		else
		{
			$sql = "UPDATE " . $GLOBALS['ecs']->table('souwu_goods') . " SET productdesc ='$desc' where id = $value[id]";
		}
	}
	//执行

	$db->query($sql);	

}

echo 'OK';





?>