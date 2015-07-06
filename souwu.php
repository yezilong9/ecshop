<?php

set_time_limit(0);
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . '/includes/cls_image.php');

//禁止访问
dump('');

/**
* 获取图片
* @$img_url 图片url地址
**/
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
/**
* 获取图片
* @$geshi 图片格式.jpg
* @$desc  商品详情
**/
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

$id_res = simplexml_load_file("http://www.sowu.com/products/goods_on_sale_api_1.xml");

$ch = curl_init();
$img_url = 'http://img.sowu.com/Files/ProdPic';

for($i=1; $i< $id_res->pagesize+1; $i++){
	// 设置URL和相应的选项
	
	$id_res = simplexml_load_file("http://www.sowu.com/products/goods_on_sale_api_".$i.".xml");
	
	foreach($id_res->ids as $ids_k=>$ids_v){
		
		foreach($ids_v as $k=>$v){
			//商品是否存在
			if($db->getOne("select productid from " .$ecs->table('souwu_goods'). " where productid = $v"))
			continue;
			
			$goods_res = simplexml_load_file("http://www.sowu.com/products/goods_api_".$v.".xml");
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://www.sowu.com/products/goods_api_".$v.".xml");
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$xml= curl_exec($ch);
			preg_match("!<productdesc>(.*?)</productdesc>!ius",$xml,$productdesc);
			preg_match("!<classPath>(.*?)</classPath>!ius",$xml,$classPath);
			
			//属性
			$props = current($goods_res->props);
			//dump($props);
			$propsArr = array();
			foreach($props as $propslist_k=>$propslist_v){
					$propsArr[$propslist_k]['name'] = current($propslist_v->name);
					$propsArr[$propslist_k]['value'] = current($propslist_v->value);
			}
			
			//图片列表
			$gallerylist = current($goods_res->gallerylist);
			$galleryArr = array();
			foreach($gallerylist as $gallerylist_k=>$gallerylist_v){
				if(current($gallerylist_v->img_url)){
				
					$this_img_url = get_img($img_url.current($gallerylist_v->img_url));
					$galleryArr[$gallerylist_k]['img_url'] = $this_img_url;
					$galleryArr[$gallerylist_k]['thumb_url'] = $this_img_url;
				}
			}
			//规格列表
			$skulist = current($goods_res->skulist);
			$skulistArr = array();
			foreach($skulist as $skulist_k=>$skulist_v){
				if(current($skulist_v->skuattrname)){
					$response = simplexml_load_file("http://www.sowu.com/products/productsaleprop_skuid_api_".current($skulist_v->skuid).".xml");
					$skulistArr[$skulist_k]['skuattrname'] = current($skulist_v->skuattrname);
					$skulistArr[$skulist_k]['skuquantity'] = current($skulist_v->skuquantity);
					$skulistArr[$skulist_k]['skuid'] = current($skulist_v->skuid);
					$skulistArr[$skulist_k]['Isdelisting'] = current($response->Isdelisting);
				}
			}
			
			$arr['productid'] = current($goods_res->productid);
			$arr['producttype'] = current($goods_res->producttype);
			$arr['productcatename'] = current($goods_res->productcatename);
			$arr['productcatenamestr'] = current($goods_res->productcatenamestr);
			$arr['producttitle'] = current($goods_res->producttitle);
			$arr['markprice'] = current($goods_res->markprice);
			$arr['costprice'] = current($goods_res->costprice);
			$arr['showtype'] = current($goods_res->showtype);
			$arr['productnum'] = current($goods_res->productnum);
			$arr['updatetime'] = current($goods_res->updatetime);
			$arr['goods_sn'] = current($goods_res->goods_sn);
			//处理商品图片
			
			$arr['goods_thum'] = $img_url.current($goods_res->original_img);
			$arr['goods_img'] = $img_url.current($goods_res->original_img);
			$arr['original_img'] = $img_url.current($goods_res->original_img);
			
			//处理商品详情
			$productdesc = preg_replace('/\"/','',substr($productdesc[1],9,-3));
			$productdesc = preg_replace('/\'/','',$productdesc);
			
			$productdesc = $productdesc;

			
			$arr['productdesc'] = $productdesc;
			$arr['skulist'] = $skulistArr?serialize($skulistArr):'';
			$arr['gallerylist'] = $galleryArr?serialize($galleryArr):'';
			$arr['props'] = $propsArr?serialize($propsArr):'';
			$arr['classPath'] = substr($classPath[1],9,-3);
			
			$db->autoExecute($ecs->table('souwu_goods'), $arr, 'INSERT');
			
		}
	}	

}
curl_close($ch);
echo 'OK';

?>