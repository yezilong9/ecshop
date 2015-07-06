<?php
/**
* 天猫一期商品接口
* author：hg
**/

set_time_limit(0);
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');

$obj = new tmOne();
$obj->oneGoods('手表');//作用只是赋值给 tmOne::$ca

foreach(tmOne::$ca as $k=>$v){
	$res = '';
	$i = 1;
	do{
		$obj->page = $i;//页数
		$res = $obj->oneGoods($v);
		
		if($res)
		foreach($res as $res_k=>$res_v)
		{
			$supply_sign_id = '';
			$tm_id = $db->getRow("select id,price,product_price from " .$ecs->table('tm_goods'). " where tmall_product_id = $res_v[tmall_product_id]");
			if($tm_id['id'])
			{
				if(($tm_id['price'] != $res_v['price']) || ($tm_id['product_price'] != $res_v['product_price']))
				{
					$supply_sign_id = 'tm_'.$tm_id['id'];
					$tmGoodRes = $db->getRow("select goods_id from ".$ecs->table('goods')." where supply_sign_id = '$supply_sign_id'");
					if($tmGoodRes['goods_id']){
						$agencyGoodsid = $db->getAll("select goods_id from ".$ecs->table('goods')." where host_goods_id = $tmGoodRes[goods_id]");
						$agencyGoodsid[] = $tmGoodRes;
						//修改价格
						foreach($agencyGoodsid as $key=>$value){
							$db->query("update ".$ecs->table('goods')." set market_price = '$res_v[product_price]',shop_price = '$res_v[price]' 
							where goods_id = $value[goods_id]");
						}
					}
				}
				continue;//进入下一次循环
			}
			$goods = array();
			$img   = array();
			$goods['tmall_product_id'] = $res_v['tmall_product_id'];
			$goods['product_id']       = $res_v['product_id'];
			$goods['category']         = $v;
			$goods['product_title']    = $res_v['product_title'];
			$goods['product_price']    = $res_v['product_price'];
			$goods['price']            = $res_v['price'];
			$goods['product_code']     = $res_v['product_code'];
			$goods['tmall_category_id'] = $res_v['tmall_category_id'];
			$goods['parameter']        = $res_v['parameter']?serialize($res_v['parameter']):'';
			$goods['colour_sort']      = $res_v['colour_sort']?serialize($res_v['colour_sort']):'';
			$img[0] = $res_v['big_img_urls'];
			$img[1] = $res_v['small_img_urls'];
			$goods['image']      	   = $img?serialize($img):'';
			$goods['detail_imgs']      = $res_v['detail_imgs']?serialize($res_v['detail_imgs']):'';
			$db->autoExecute($ecs->table('tm_goods'), $goods, 'INSERT');
		}
		$i++;
	}while($res);
}
dump('OK');





class tmOne{
	
	public $gotogame_reg_url = 'http://gz.gotogame.com.cn/cybercafe/interface/api_promote.php';
	
	public $gotogame_key = 'Bn60skUe_vXN_qJiubL7';
	
	public $search       = '';
	
	public $page         = 1;
	
	public $rows_page    = 12;
	
	public static $ca = array();
	
	function oneGoods($category)
	{
		$data = array('category' => $category, 'search' => $this->search, 'page' => $this->page, 'rows_page' => $this->rows_page);
		$arr_return = $this->get_tmall_data($data);
		if(!self::$ca)
		{
			self::$ca = $this->category($arr_return['rows']['category']);
		}
		return $arr_return['rows']['product']?$arr_return['rows']['product']:'';
	}
	
	/*组装分类数据*/
	function category($ca)
	{
		$arr = array();
		foreach($ca as $key=>$value){
			foreach($value as $k=>$v)
			{
				$arr[] = $v;
			}
		}
		return $arr;
	}
    /**
     *  获取加油站天猫第一期的数据
     *  param   $data   数组
     */ 
    function get_tmall_data($data)
    {       
        $post_data = array(
            'action' => 'get_tmall_data',
            'category' => urlencode($data['category']),
            'search' => urlencode($data['search']),
            'page' => $data['page'],
            'rows_page' => $data['rows_page'],
            'time' => time()
        );
        
        $post_data['sign'] = md5($post_data['action'].$post_data['category'].$post_data['search'].$post_data['page'].$post_data['rows_page'].$post_data['time'].$this->gotogame_key);
        $str_ret = $this->curl_access($this->gotogame_reg_url, http_build_query($post_data), 'get');
        $arr_ret = json_decode($str_ret, true);
        return $arr_ret;
    }
	
    /**
     *  curl请求
     *  param   $url            请求地址地址
     *  param   $str_query      请求的参数
     *  param   $method         请求的方式
     *  param   $str_referer    伪造请求来源地址
     *  param   $cookie_file    请求cookie信息
     */
    function curl_access($str_url, $str_query = '', $method = '', $str_referer = '', $cookie_file = '')
    {
        $obj_ch = curl_init();
        curl_setopt($obj_ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($obj_ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:20.0) Gecko/20100101 Firefox/20.0');

        if ($cookie_file != '')
        {
            if(file_exists($cookie_file))
            {
                curl_setopt($obj_ch, CURLOPT_COOKIEFILE, $cookie_file);
            }
            curl_setopt($obj_ch, CURLOPT_COOKIEJAR, $cookie_file);
        }

        if ($str_referer != '')
        {
            curl_setopt($obj_ch, CURLOPT_REFERER, $str_referer);
        }

        if ($method == 'post')
        {
            curl_setopt($obj_ch, CURLOPT_URL, $str_url);
            curl_setopt($obj_ch, CURLOPT_POST, 1);
            curl_setopt($obj_ch, CURLOPT_POSTFIELDS, $str_query);
        }
        else
        {
            curl_setopt($obj_ch, CURLOPT_URL, $str_url.($str_query?'?'.$str_query:''));
            curl_setopt($obj_ch, CURLOPT_HTTPGET, 1);
        }

        if (strpos($str_url, 'https') !== false)
        {
            curl_setopt($obj_ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($obj_ch, CURLOPT_SSL_VERIFYHOST, 1);
            curl_setopt($obj_ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        }

        curl_setopt($obj_ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($obj_ch, CURLOPT_RETURNTRANSFER, 1);
        $str = curl_exec($obj_ch);
        curl_close($obj_ch);

        return trim($str);
    }
}

?>