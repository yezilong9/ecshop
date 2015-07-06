<?php
/**
* 作用：处理快讯打印
* 
* author：hg
**/


class class_print
{
	
	public $pege_num   = 24;//一页显示数量
	
	public $pege_show  = 5;//中间显示多少个分页按钮
	
	/**
	* 根据分类ID获取商品列表
	* @$category_id 分类ID
	* @$pege_start，@$pege_end 分页
	* @$panel 版面
	* @$$search 搜索关键字
	**/
	public function category_goods($category_id,$panel,$search,$url,$pege='1')
	{	

		//统计数量
		$goods_count = $GLOBALS['db']->getOne("select count(*) from  ".$GLOBALS['ecs']->table('goods')."where 
		admin_agency_id = 0 AND is_on_sale = 1 AND is_alone_sale = 1 AND is_delete = 0 $where");
		//处理分页
		$pageArr = $this->peint_page($pege,$this->pege_num,$goods_count,$url);
		$pege_start = $pageArr['pege_start'];
		$pege_end   = $pageArr['pege_end'];
		
		//根据分类或关键字
		if(!$search)
		{
			$res = $this->xia_cat_id($category_id);
			$res[] = $category_id;
			$where = "AND cat_id in (".implode(',',$res).")";
		}
		else
		{
			$where = "AND goods_name like '%$search%'";
		}
		$goods_list = $GLOBALS['db']->getAll("select goods_id,goods_name,market_price,shop_price,goods_thumb from  ".
		$GLOBALS['ecs']->table('goods')."where 
		admin_agency_id = 0 AND is_on_sale = 1 AND is_alone_sale = 1 AND is_delete = 0 $where limit $pege_start,$pege_end");
		//j截取名称长度，重写图片路径
		foreach($goods_list as $key=>$value)
		{
			if(strlen($value['goods_name'])>25)
				$goods_list[$key]['goods_name'] = mb_substr($value['goods_name'],0,25,'utf-8').'...';
			if(!empty($panel) && in_array($value['goods_id'],$panel))
				$goods_list[$key]['panel'] = '1';
			if($search)
				$goods_list[$key]['goods_name'] = preg_replace("|$search|",'<span style="color:red;">'.$search.'</span>',$goods_list[$key]['goods_name']);
		}
		return array('goods_list'=>$goods_list,'page_html'=>$pageArr['pegeHtml']);
	}
	
	/**
	* 根据不同版面返回不同商品数量
	* @panel 版面 
	**/
	public function count_print_goods($panel)
	{
		switch($panel)
		{
			case 'normal_a':
				$num = 49;
				break;
			case 'normal_b':
				$num = 60;
				break;
			case 'simple_a':
				$num = 19;
				break;
			case 'simple_b':
				$num = 30;
				break;
		}
		return $num?$num:false;
	}
	
	/**
	* 获取商品信息
	* @$goods_id 商品ID
	**/
	public function goods_message($goods_id)
	{
		if(!$goods_id) return false;
		return $GLOBALS['db']->getAll("select goods_id,goods_name,market_price,shop_price,goods_thumb,goods_img from  ".
		$GLOBALS['ecs']->table('goods')." where goods_id in ($goods_id)");
	}
	
	/**
	* 组织返回一已挑选商品html
	* @$goods_arr_id 商品ID
	* 
	**/
	public function print_goods_html($goods_arr_id,$panel)
	{
		$goods_list = array();
		$goods_list = $this->goods_message(implode(',',$goods_arr_id));
		$goods_count = count($goods_list);
		$forNum = 6-$goods_count%6+count($goods_list);
		$marginLeft = '900px';
		$html = '';
		$ki   = 0;
		for($i=0;$i < $forNum;$i++){
			if(!($i%6))
			{	
				$andMarginLeft = $ki*$marginLeft;
				$html .= '<div class="chanpin_con fn-clear" style="margin-left:'.$andMarginLeft.'px;"><ul class="xuanting1">';
				$ki++;
			};
			if($goods_list[$i]['shop_price'])
			{
				$goods_list[$i]['goods_name'] = strlen($goods_list[$i]['goods_name'])>20?mb_substr($goods_list[$i]['goods_name'],0,20,'utf-8').'...':$goods_list[$i]['goods_name'];
				$state = '1';
				$html .=  '<li>
								<div class="cpitem">
								<a href="#" target="_blank">
									<img src="'.$goods_list[$i]['goods_thumb'].'" style="width:100px;height:89px;" alt=""/>
								</a>
								</div>
								<div class="s_zhekou">'.$goods_list[$i]['shop_price'].'</div>
								<div class="chanpininfo">
									<p class="cpxinxi">'.$goods_list[$i]['goods_name'].'</p>
									<p class="cpbianhao">商品编号：'.$goods_list[$i]['goods_id'].'</p>
								</div>
								<a href="javascript:choose('.$goods_list[$i]['goods_id'].');" title="" class="cpitemclose"></a>
							</li>';
			}else{
				$html .='<li>
							<div class="uncpitem">
								<a href="#">
									<img src="../images/print/chanpinpic05.png" alt=""/>
								</a>
							</div>
						</li>';
			}
			if(!(($i+1)%6) && $ki>0)
			{
				$html .= '</ul></div>';
			}
		}
		$num_limit = $this->count_print_goods($panel);
		$goods_count = isset($state)?$goods_count:0;
		return array('html'=>$html,'andMarginLeft'=>$andMarginLeft,'num'=>$goods_count,'num_surplus'=>$num_limit-$goods_count,'num_limit'=>$num_limit);
	}
	/**
	* 打印页面分页功能
	* @$page 当前页数 
	* @$pege_num 一页显示数量
	* @$goods_count 总量
	**/
	public function peint_page($page,$pege_num,$goods_count,$url)
	{
		$url  = preg_replace("|&page=[0-9]{1,}|",'',$url);//当前URL
		$toal_pege = ceil($goods_count/$pege_num);//总页数
		$page      = $page?$page:1;
		$pege_start = ($page-1)*$pege_num;
		$pege_end  = $pege_num;
		$topPge    = $page > 1?$page-1:1;
		//组装分页html代码
		$topUrl    = $topPge == 1?'javascript:void(0);':$url.'&page='.$topPge;
		$pegeHtml  = '<a href="'.$topUrl.'">上一页</a>';
		$this->pege_show = $toal_pege >= $this->pege_show?$this->pege_show:$toal_pege;
		$start_i = $page-(ceil($this->pege_show/2));
		$if_i    = $page+$this->pege_show;
		$if_i    = $if_i>$toal_pege?$toal_pege:$if_i;
		for($i = $start_i>1?$start_i:1;$i <= $if_i;$i++)
		{	
			if($i == $page)
				$class = 'class="pg_cur"';
			else
				$class = '';
			$pegeHtml .= '<a href="'.$url.'&page='.$i.'" '.$class.' >'.$i.'</a>';
		}
		$nextPge    = $page < $toal_pege?$page+1:$toal_pege;
		
		$nextUrl    = $page >= $toal_pege?'javascript:void(0);':$url.'&page='.$nextPge;
		$pegeHtml .= '<a href="'.$nextUrl.'">下一页</a>';
		$pegeHtml .= '<a href="javascript:void(0)">共'.$toal_pege.'页</a>';
		return array('pegeHtml'=>$pegeHtml,'pege_start'=>$pege_start,'pege_end'=>$pege_end);
	}
	
	/**
	* 预览页面html组装
	* @$panel 版面
	* @goods_arr_id 商品ID数组
	**/
	public function panel_html($panel,$goods_arr_id)
	{
		//dump($goods_arr_id);
		$goods_list = $this->goods_message(implode(',',$goods_arr_id));
		$for_num = count($goods_list);
		foreach($goods_list as $key=>$value)
		{
			$goods_list[$value['goods_id']] = $goods_list[$key];
		}
		$panel_html = '';
		if($panel == 'simple_a')
		{
			$panel_html .= '<li><img src="http://www.txshop.com/assets/images/printshow_02_empty.png" alt="">
			<input type="text" class="ptshowinputTxtC" id="shop_address" name="shop_address" value="" onblur="set_address_phone()" />
			<input type="text" class="ptshowinputTxtD" id="hot_line" name="hot_line" value="" onblur="set_address_phone()" />';
			for($i = 0;$i < $for_num;$i++){
				$panel_html .= '<div class="itemshow_'.($i+1).'_simple_a"><p class="itemshow_cpitem">';
				$panel_html .= '<img src="'.$goods_list[$goods_arr_id[$i]]['goods_thumb'].'" alt="" height="60" width="60" /></p>';
				$panel_html .= '<p class="itemshow_s_zhekou" title="折扣价'.$goods_list[$goods_arr_id[$i]]['shop_price'].'元">'.
								$goods_list[$goods_arr_id[$i]]['shop_price'].'</p><p class="itemshow_chanpininfo">';
				$panel_html .= '<p title="'.$goods_list[$goods_arr_id[$i]]['goods_name'].'" class="itemshow_cpxinxi">';
				$panel_html .= '<nobr>'.$goods_list[$goods_arr_id[$i]]['goods_name'].'</nobr></p>';
				$panel_html .= '<p class="itemshow_cpbianhao" title="商品编号'.$goods_list[$goods_arr_id[$i]]['goods_id'].'">';
				$panel_html .= '商品编号'.$goods_list[$goods_arr_id[$i]]['goods_id'].'</p></p></div>';
			}
			$panel_html .= '</li>';
		}
		elseif($panel == 'normal_a')
		{
			
			$panel_html .= '<li><img src="http://www.txshop.com/assets/images/printshow_01_empty.png" alt="">';
			for($i = 0;$i < $for_num-20;$i++){
				$panel_html .= '<div class="itemshow_'.($i+1).'_normal_a">';
				$panel_html .= '<p class="itemshow_cpitem"><img src="'.$goods_list[$goods_arr_id[$i]]['goods_thumb']
							.'" alt="" height="60" width="60" /></p>';
				$panel_html .= '<p class="itemshow_s_zhekou" title="折扣价'.$goods_list[$goods_arr_id[$i]]['shop_price'].'元">'
							.$goods_list[$goods_arr_id[$i]]['shop_price'].'</p>';
				$panel_html .= '<p class="itemshow_chanpininfo"><p title="'.$goods_list[$goods_arr_id[$i]]['goods_name']
							.'" class="itemshow_cpxinxi"><nobr>'.$goods_list[$goods_arr_id[$i]]['goods_name'].'</nobr></p>';
				$panel_html .= '<p class="itemshow_cpbianhao" title="商品编号'.$goods_list[$goods_arr_id[$i]]['goods_id'].'">商品编号'
							.$goods_list[$goods_arr_id[$i]]['goods_id'].'</p></p></div>';
			}
			$panel_html .= '</li><li><img src="http://www.txshop.com/assets/images/printshow_02_empty.png" alt="">
			<input type="text" class="ptshowinputTxtA" id="shop_address" name="shop_address" value="" onblur="set_address_phone()" />
			<input type="text" class="ptshowinputTxtB" id="hot_line" name="hot_line" value="" onblur="set_address_phone()" />';
			for($ki=$for_num-20;$ki < $for_num;$ki++){
				$panel_html .= '<div class="itemshow_'.($ki+1).'_normal_a">';
				$panel_html .= '<p class="itemshow_cpitem"><img src="'.$goods_list[$goods_arr_id[$ki]]['goods_thumb']
							.'" alt="" height="60" width="60" /></p>';
				$panel_html .= '<p class="itemshow_s_zhekou" title="折扣价'.$goods_list[$goods_arr_id[$ki]]['shop_price'].'元">'
							.$goods_list[$goods_arr_id[$ki]]['shop_price'].'</p>';
				$panel_html .= '<p class="itemshow_chanpininfo"><p title="'.$goods_list[$goods_arr_id[$ki]]['goods_name']
							.'" class="itemshow_cpxinxi"><nobr>'.$goods_list[$goods_arr_id[$ki]]['goods_name'].'</nobr></p>';
				$panel_html .= '<p class="itemshow_cpbianhao" title="商品编号'.$goods_list[$goods_arr_id[$ki]]['goods_id'].'">商品编号'
							.$goods_list[$goods_arr_id[$ki]]['goods_id'].'</p></p></div>';
			}
			$panel_html .= '</li>';
		}
		elseif($panel == 'normal_b')
		{
				$panel_html .= '<li><img src="http://www.txshop.com/assets/images/printshow_01_empty.png" alt="">';
				for($i=0;$i < 30;$i++){
					$panel_html .= '<div class="itemshow_'.($i+1).'_normal_b">';
					$panel_html .= '<p class="itemshow_cpitem"><img src="'.$goods_list[$goods_arr_id[$i]]['goods_thumb'].'" alt="" height="60" width="60" /></p>';
					$panel_html .= '<p class="itemshow_s_zhekou" title="折扣价'.$goods_list[$goods_arr_id[$i]]['shop_price'].'元">'.$goods_list[$goods_arr_id[$i]]['shop_price'].'</p>';
					$panel_html .= '<p class="itemshow_chanpininfo">';
					$panel_html .= '<p title="'.$goods_list[$goods_arr_id[$i]]['goods_name']
							.'" class="itemshow_cpxinxi"><nobr>'.$goods_list[$goods_arr_id[$i]]['goods_name']
							.'</nobr></p>';
					$panel_html .= '<p class="itemshow_cpbianhao" title="商品编号'.$goods_list[$goods_arr_id[$i]]['goods_id'].'">商品编号'.$goods_list[$goods_arr_id[$i]]['goods_id'].'</p></p></div>';
				}
				$panel_html .= '</li><li><img src="http://www.txshop.com/assets/images/printshow_01_empty.png" alt="">';
				for($ki=30;$ki < 60;$ki++){
					$panel_html .= '<div class="itemshow_'.($ki+1).'_normal_b">';
					$panel_html .= '<p class="itemshow_cpitem"><img src="'.$goods_list[$goods_arr_id[$ki]]['goods_thumb'].'" alt="" height="60" width="60" /></p>';
					$panel_html .= '<p class="itemshow_s_zhekou" title="折扣价'.$goods_list[$goods_arr_id[$ki]]['shop_price'].'元">'.$goods_list[$goods_arr_id[$ki]]['shop_price'].'</p>';
					$panel_html .= '<p class="itemshow_chanpininfo">';
					$panel_html .= '<p title="'.$goods_list[$goods_arr_id[$ki]]['goods_name']
							.'" class="itemshow_cpxinxi"><nobr>'.$goods_list[$goods_arr_id[$ki]]['goods_name']
							.'</nobr></p>';
					$panel_html .= '<p class="itemshow_cpbianhao" title="商品编号'.$goods_list[$goods_arr_id[$ki]]['goods_id'].'">'.$goods_list[$goods_arr_id[$ki]]['goods_id'].'</p></p></div>';
				}
				$panel_html .= '</li>';
		}
		elseif($panel == 'simple_b')
		{
			$panel_html .= '<li><img src="http://www.txshop.com/assets/images/printshow_01_empty.png" alt="">';
			for($i = 0;$i < $for_num;$i++){
				$panel_html .= '<div class="itemshow_'.($i+1).'_simple_b">';
				$panel_html .= '<p class="itemshow_cpitem"><img src="'.$goods_list[$goods_arr_id[$i]]['goods_thumb'].'" alt="" height="60" width="60" /></p>';
				$panel_html .= '<p class="itemshow_s_zhekou" title="折扣价'.$goods_list[$goods_arr_id[$i]]['shop_price'].'元">'.$goods_list[$goods_arr_id[$i]]['shop_price'].'</p>';
				$panel_html .= '<p class="itemshow_chanpininfo">';
				$panel_html .= '<p title="'.$goods_list[$goods_arr_id[$i]]['goods_name'].'" class="itemshow_cpxinxi"><nobr>'.$goods_list[$goods_arr_id[$i]]['goods_name'].'</nobr></p>';
				$panel_html .= '<p class="itemshow_cpbianhao" title="商品编号'.$goods_list[$goods_arr_id[$i]]['goods_id'].'">商品编号'.$goods_list[$goods_arr_id[$i]]['goods_id'].'</p></p></div>';
			}
			$panel_html .= '</li>';
		}
		return $panel_html;
	}
	
	public function big_html($panel,$goods_arr_id,$address='',$phone='')
	{
		if(!$goods_arr_id) return false;
		$goods_list = $this->goods_message(implode(',',$goods_arr_id));
		$for_num = count($goods_list);
		foreach($goods_list as $key=>$value)
		{
			$goods_list[$value['goods_id']] = $goods_list[$key];
		}
		$big_html = '';
		if($panel == 'normal_a')
		{
			$big_html .= '<div class="layA"><div class="part_01"><div class="pro_lt"><ul class="fn-clear">';
			for($i=19;$i < $for_num;$i++){
				$big_html .= '<li><div class="cp_pic"> <img width="460" height="460" src="'.$goods_list[$goods_arr_id[$i]]['goods_img'].'"></div>';
				$big_html .= '<div class="zhekou"> <span>'.$goods_list[$goods_arr_id[$i]]['shop_price'].'</span> </div>';
				$big_html .= '<div class="cp_info"><p>'.$goods_list[$goods_arr_id[$i]]['goods_name'].'</p></div>';
				$big_html .= '<div class="cp_bianhao"><p>商品编号：'.$goods_list[$goods_arr_id[$i]]['goods_id'].'</p></div></li>';
			}
			$big_html .= '</ul></div></div></div><div class="pt_rt"><div class="part_02">';
			$big_html .= '<div class="txt"> <span class="inputTxt_01">'.$address.'</span><span class="inputTxt_02">'.$phone.'</span></div>';
			$big_html .= '<div class="tj_box"><div class="tejiapartA"><div class="tj_count">';
			$big_html .= '<div class="tj_hd">7月特价</div><div class="tj_bd"><ul class="tj_list fn-clear">';
			for($ki=0;$ki < 4;$ki++){
				$big_html .= '<li><div class="tj_pic"><img width="460" height="460" src="'.$goods_list[$goods_arr_id[$ki]]['goods_img'].'"></div><div class="tj_info">';
				$big_html .= '<p><span class="tj_price">特价<em>'.$goods_list[$goods_arr_id[$ki]]['shop_price'].'元</em></span>';
				$big_html .= '<span class="tj_bianhao">编号：'.$goods_list[$goods_arr_id[$ki]]['goods_id'].'</span></p>';
				$big_html .= '<p class="tj_clothes">'.$goods_list[$goods_arr_id[$ki]]['goods_name'].'</p></div></li>';
			}
            $big_html .= '</ul></div></div></div></div><div class="pro_listA fn-clear"><ul>';
			for($kii=4;$kii < 19;$kii++){
				$big_html .= '<li><div class="cp_pic"> <img width="460" height="460" src="'.$goods_list[$goods_arr_id[$kii]]['goods_img'].'"> </div>';
				$big_html .= '<div class="zhekou"> <span>'.$goods_list[$goods_arr_id[$kii]]['shop_price'].'</span> </div>';
				$big_html .= '<div class="cp_info"><p>'.$goods_list[$goods_arr_id[$kii]]['goods_name'].'</p></div>';
				$big_html .= '<div class="cp_bianhao"><p>商品编号：'.$goods_list[$goods_arr_id[$kii]]['goods_id'].'</p></div></li>';
			}
			$big_html .= '</ul></div></div></div></div>';
		}
		elseif($panel == 'normal_b')
		{
			$big_html .= '<div class="layB"><div class="pro_con fn-clear"><div class="pro_lt"><ul class="fn-clear">';
			for($i=0;$i < $for_num/2;$i++){
				$big_html .= '<li><div class="cp_pic"><img width="460" height="460" src="'.$goods_list[$goods_arr_id[$i]]['goods_img'].'"></div>';
				$big_html .= '<div class="zhekou"><span>'.$goods_list[$goods_arr_id[$i]]['shop_price'].'</span></div>';
				$big_html .= '<div class="cp_info"><p>'.$goods_list[$goods_arr_id[$i]]['goods_name'].'</p></div>';
				$big_html .= '<div class="cp_bianhao"><p>商品编号：'.$goods_list[$goods_arr_id[$i]]['goods_id'].'</p></div></li>';
			}
            $big_html .= '</ul></div><div class="pro_rt"><ul class="fn-clear">';
			for($ki=$for_num/2;$ki < $for_num;$ki++){
				$big_html .= '<li><div class="cp_pic"><img width="460" height="460" src="'.$goods_list[$goods_arr_id[$ki]]['goods_img'].'"></div>';
				$big_html .= '<div class="zhekou"><span>'.$goods_list[$goods_arr_id[$ki]]['shop_price'].'</span></div>';
				$big_html .= '<div class="cp_info"><p>'.$goods_list[$goods_arr_id[$ki]]['goods_name'].'</p></div>';
				$big_html .= '<div class="cp_bianhao"><p>商品编号：'.$goods_list[$goods_arr_id[$ki]]['goods_id'].'</p></div></li>';
			}
            $big_html .= '</ul></div></div></div>';
		}
		elseif($panel == 'simple_a')
		{
			$big_html .= '<div class="rt_pic"><div class="mc"><div class="txtA">';
			$big_html .= '<span class="inputTxt_01">'.$address.'</span>';
			$big_html .= '<span class="inputTxt_02">'.$phone.'</span></div><div class="tj_box_01"><div class="tejiapart">';
			$big_html .= '<div class="tj_count"> <div class="tj_hd"><?=$sub_title?></div><div class="tj_bd"><ul class="tj_list fn-clear">';
			for($i=0;$i < 4;$i++){
				$big_html .= '<li><div class="tj_pic"><img width="320" height="320" alt="" src="'.$goods_list[$goods_arr_id[$i]]['goods_img'].'"></div>';
				$big_html .= '<div class="tj_info"><p><span class="tj_price">特价<em>'.$goods_list[$goods_arr_id[$i]]['shop_price'].'元</em></span>';
				$big_html .= '<span class="tj_bianhao">编号：'.$goods_list[$goods_arr_id[$i]]['goods_id'].'</span></p>';
				$big_html .= '<p class="tj_clothes">'.$goods_list[$goods_arr_id[$i]]['goods_name'].'</p></div></li>';
			}
			$big_html .= '</ul></div></div></div></div><div class="pro_list fn-clear"><ul>';
			for($ki=4;$ki < $for_num;$ki++){
				$big_html .= '<li><div class="cp_pic"><img width="320" height="320" alt="" src="'.$goods_list[$goods_arr_id[$ki]]['goods_img'].'"></div>';
				$big_html .= '<div class="zhekou"><span>'.$goods_list[$goods_arr_id[$ki]]['shop_price'].'</span></div><div class="cp_info">';
				$big_html .= '<p>'.$goods_list[$goods_arr_id[$ki]]['goods_name'].'</p></div>';
				$big_html .= '<div class="cp_bianhao"><p>商品编号：'.$goods_list[$goods_arr_id[$ki]]['goods_id'].'</p></div></li>';
			}
			$big_html .= '</ul></div></div></div>';
		}
		elseif($panel == 'simple_b')
		{
			$big_html .= '<div class="lt_pic"><div class="mc"><ul class="chanpin_list fn-clear">';
			for($i=0;$i < $for_num;$i++){
				if(!(($i+1)%5)) 
					$class = 'class=cp_last';
				else
					$class = '';
				$big_html .= '<li '.$class.'><div class="cp_pic">';
				$big_html .= '<img width="320" height="320" alt="" src="'.$goods_list[$goods_arr_id[$i]]['goods_img'].'"></div><div class="zhekou">';
				$big_html .= '<span>'.$goods_list[$goods_arr_id[$i]]['shop_price'].'</span></div>';
				$big_html .= '<div class="cp_info"><p>'.$goods_list[$goods_arr_id[$i]]['goods_name'].'</p></div>';
				$big_html .= '<div class="cp_bianhao"><p>商品编号：'.$goods_list[$goods_arr_id[$i]]['goods_id'].'</p></div></li>';
			}
			$big_html .= '</ul></div></div>';
		}
		return $big_html;
	}
	/*
	* 获取当前分类的所有下级分类
	* @$cat_id 分类ID
	*/
	public function xia_cat_id($cat_id,$res='')
	{
		$catAll = $GLOBALS['db']->getAll("select cat_id from ".$GLOBALS['ecs']->table('category')." where parent_id = $cat_id");
		if(current($catAll))
		foreach($catAll as $key=>$value){
			$res[] = $value['cat_id'];
			$res = $this->xia_cat_id($value['cat_id'],$res);
		}
		return $res;
	}
}

													
													
?>