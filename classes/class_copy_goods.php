<?php
/**
* 说明:复制商品,先用于代理商添加商品
* time:2014-08-27
* author:hg
**/

class class_copy_goods{
	
	private $goods_id = '';//商品ID
	
	private $admin_agency_id = '';//代理商id
	
	private $db = '';
	
	private $shop_price = '';//售价
	
	private $new_goods_id ='';
	
	private $new_goods_sn ='';
	
	function __construct($goods_id=0,$admin_agency_id=0,$shop_price='')
	{
		$this->goods_id        = $goods_id;
		$this->admin_agency_id = $admin_agency_id;
		$this->shop_price      = $shop_price;
		$this->db = $GLOBALS['db'];
		$this->ecs = $GLOBALS['ecs'];
	}
	/**
	* 使用复制方法
	**/
	public function copy_go($new_goods_id='',$copy_goods_gallery=true,$copy_goods_attr=true)
	{
		$this->new_goods_id = $new_goods_id?$new_goods_id:$this->copy_goods();
		if($copy_goods_gallery) $this->copy_goods_gallery();
		if($copy_goods_attr)    $this->copy_goods_attr();
	}
	/**
	* 复制商品表
	**/
	private function copy_goods()
	{
		$goods_res = $this->db->getRow("SELECT cat_id,goods_sn,goods_name,goods_number,".
		"market_price,shop_price,keywords,goods_desc,goods_thumb,goods_img,".
		"original_img,goods_type,wholesale_price,costing_price,supply_sign_id,is_on_sale,admin_agency_id FROM ".
		$this->ecs->table('goods')." WHERE goods_id = $this->goods_id");
		#代理商添加商品
		if($this->admin_agency_id && $this->shop_price)
		{
			$shop_price    = $this->shop_price;
			$market_price  = $goods_res['shop_price']*$GLOBALS['_CFG']['market_price_rate'];
			$costing_price = $goods_res['shop_price'];
			$host_goods_id = $this->goods_id;
		}
		else
		{
			$shop_price    = $this->shop_price;
			$market_price  = $goods_res['market_price'];
			$costing_price = $goods_res['costing_price'];
			$host_goods_id = 0;
		}
		$Arr = array(
			'cat_id'       => $goods_res['cat_id'],
			'goods_sn'     => generate_goods_sn($this->db->getOne("SELECT MAX(goods_id) + 1 FROM ".$this->ecs->table('goods'))),
			'goods_name'   => preg_replace("/['`<> ]/",'',$goods_res['goods_name']),
			'goods_number' => $goods_res['goods_number'],
			'market_price' => $market_price,
			'shop_price'   => $shop_price,
			'keywords'     => $goods_res['keywords'],
			'goods_desc'   => $goods_res['goods_desc'],
			'goods_thumb'  => $goods_res['goods_thumb'],
			'goods_img'    => $goods_res['goods_img'],
			'original_img' => $goods_res['original_img'],
			'add_time'     => time(),
			'last_update'  => time(),
			'goods_type'   => $goods_res['goods_type'],
			'wholesale_price'   => $goods_res['wholesale_price'],
			'costing_price'     => $costing_price,
			'supply_sign_id'    => (int)$goods_res['supply_sign_id'],
			'is_on_sale'        => $goods_res['is_on_sale'],
			'admin_agency_id'   => $this->admin_agency_id,
			'host_goods_id'     => $host_goods_id,
		);
		$this->db->autoExecute($this->ecs->table('goods'), $Arr, 'INSERT');
		return $this->db->insert_id();
	}
	
	/**
	* 复制商品图片表
	**/
	private function copy_goods_gallery()
	{
		$res = $this->db->getAll("SELECT img_id,img_url,img_desc,thumb_url,img_original FROM ".
		$this->ecs->table('goods_gallery')." WHERE goods_id = $this->goods_id ORDER BY img_id ASC");
		$state = array_filter($res);
		if(empty($state) || empty($this->new_goods_id)) return false;
		
		foreach($res as $key=>$value){
			$arr_img = array(
						'goods_id' 		=> $this->new_goods_id,
						'img_url' 		=> $value['img_url'],
						'img_desc' 		=> $value['img_desc'],
						'thumb_url' 	=> $value['thumb_url'],
						'img_original'  => $value['img_original']
					);
			$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('goods_gallery'), $arr_img, 'INSERT');
		}
	}
	
	/**
	* 复制属性
	**/
	private function copy_goods_attr()
	{
		$res = $this->db->getAll("SELECT goods_attr_id,attr_id,attr_value,attr_price FROM ".
		$this->ecs->table('goods_attr')." where goods_id = $this->goods_id");
		$state = array_filter($res);
		if(!empty($state))
		foreach($res as $key=>$value)
		{
			$arr['goods_id']    = $this->new_goods_id;
			$arr['attr_id']    = $value['attr_id'];
			$arr['attr_value'] = $value['attr_value'];
			$arr['attr_price'] = $value['attr_price'];
			$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('goods_attr'), $arr, 'INSERT');
			if($arr['attr_price'])
			{
				$new_goods_attr_id = $this->db->insert_id();
				$this->copy_products($res['goods_attr_id'],$new_goods_attr_id);
			}
		}
	}
	
	/**
	* 复制货物
	* @$goods_attr 属性ID
	* @$new_goods_attr_id 新插入商品属性ID
	**/
	private function copy_products($goods_attr,$new_goods_attr_id)
	{
		if(empty($goods_attr) || empty($new_goods_attr_id)) return false;
		$product_number = $this->db->getOne("SELECT product_number FROM ".
		$this->ecs->table('products')." where goods_id = $this->goods_id ADN goods_attr = $goods_attr");
		if($product_number){
			$pro['goods_id']       = $this->new_goods_id;
			$pro['goods_attr']     = $new_goods_attr_id;
			$pro['product_number'] = $product_number;
			$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('products'), $pro, 'INSERT');
			$new_pro_id = $this->db->insert_id;
			$product_sn = $this->get_goods_sn().'g_p'.$new_pro_id;
			$this->db->query("UPDATE ".$this->ecs->table('products').
			" SET product_sn = '$product_sn' WHERE product_id = $new_pro_id");
		}
	}
	
	/**
	* 获取新插入商品货号
	**/
	private function get_goods_sn()
	{
		if($this->new_goods_sn)
			return $this->new_goods_sn;
		else
			return $this->new_goods_sn = $this->db->getOne("SELECT goods_sn FROM ".$this->ecs->table('goods').
			" WHERE goods_id = $this->new_goods_attr_id"); 
	}
	
}
?>