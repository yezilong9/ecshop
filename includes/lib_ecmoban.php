<?php

/**
 * ECSHOP EC模板堂二次开发函数库
 * ============================================================================
 * * 版权所有 2005-2013 上海商创网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------------------------
 * ============================================================================
 * $Id: lib_ecmoban.php 1.0 2013-10-30 $
*/

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}
 
/**
 * 获得指定分类同级的所有分类以及该分类下的子分类
 *
 * @access  public
 * @param   integer     $cat_id     分类编号
 * @return  array
 */
function get_categories_tree_pro($cat_id = 0)
{
    if ($cat_id > 0)
    {
        $sql = 'SELECT parent_id FROM ' . $GLOBALS['ecs']->table('category') . " WHERE cat_id = '$cat_id'";
        $parent_id = $GLOBALS['db']->getOne($sql);
    }
    else
    {
        $parent_id = 0;
    }
	$admin_agency_id = admin_agency_id()?admin_agency_id():agency_id();
	if($admin_agency_id) 
		// $where = " AND (FIND_IN_SET('$admin_agency_id',agency_cat)  OR host_cat = 1) "; //注释2015-03-19 ccx 
		$where = " AND (FIND_IN_SET('$admin_agency_id',agency_cat)) "; 
    else
		$where = "AND host_cat = 1 ";
    /*
     判断当前分类中全是是否是底级分类，
     如果是取出底级分类上级分类，
     如果不是取当前分类及其下的子分类
    */
    $sql = 'SELECT count(*) FROM ' . $GLOBALS['ecs']->table('category') . " WHERE parent_id = '$parent_id' AND is_show = 1 ";
    if ($GLOBALS['db']->getOne($sql) || $parent_id == 0)
    {
        /* 获取当前分类及其子分类 */
        $sql = 'SELECT cat_id,cat_name ,parent_id,is_show ' .
                'FROM ' . $GLOBALS['ecs']->table('category') .
                "WHERE parent_id = '$parent_id' AND is_show = 1 $where ORDER BY sort_order ASC, cat_id ASC";

        $res = $GLOBALS['db']->getAll($sql);

        foreach ($res AS $row)
        {
			$cat_id = $row['cat_id'];
			$children = get_children($cat_id);
			$cat = $GLOBALS['db']->getRow('SELECT cat_name, keywords, cat_desc, style, grade, filter_attr, parent_id FROM ' . $GLOBALS['ecs']->table('category') .
        " WHERE cat_id = '$cat_id'");

			/* 获取分类下文章 */
			$sql = 'SELECT a.article_id, a.title, ac.cat_name, a.add_time, a.file_url, a.open_type FROM '.$GLOBALS['ecs']->table('article_cat').' AS ac RIGHT JOIN '.$GLOBALS['ecs']->table('article')." AS a ON a.cat_id=ac.cat_id AND a.is_open = 1 WHERE ac.cat_name='$row[cat_name]' ORDER BY a.article_type,a.article_id DESC LIMIT 4 "	;
			
			$articles = $GLOBALS['db']->getAll($sql);
			
			foreach($articles as $key=>$val)
			{
				 $articles[$key]['url']         = $val['open_type'] != 1 ?
          		  build_uri('article', array('aid'=>$val['article_id']), $val['title']) : trim($val['file_url']);
			}
		
			
		

			/* 获取分类下品牌 */
			$sql = "SELECT b.brand_id, b.brand_name,  b.brand_logo, COUNT(*) AS goods_num, IF(b.brand_logo > '', '1', '0') AS tag ".
					"FROM " . $GLOBALS['ecs']->table('brand') . "AS b, ".
						$GLOBALS['ecs']->table('goods') . " AS g LEFT JOIN ". $GLOBALS['ecs']->table('goods_cat') . " AS gc ON g.goods_id = gc.goods_id " .
					"WHERE g.brand_id = b.brand_id AND ($children OR " . 'gc.cat_id ' . db_create_in(array_unique(array_merge(array($cat_id), array_keys(cat_list($cat_id, 0, false))))) . ") AND b.is_show = 1 " .
					" AND g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 ".
					"GROUP BY b.brand_id HAVING goods_num > 0 ORDER BY b.sort_order, b.brand_id ASC";
			//dump($sql);
			$brands = $GLOBALS['db']->getAll($sql);
		
			foreach ($brands AS $key => $val)
			{
	
				$brands[$key]['brand_name'] = $val['brand_name'];
				$brands[$key]['url'] = build_uri('category', array('cid' => $cat_id, 'bid' => $val['brand_id'], 'price_min'=>$price_min, 'price_max'=> $price_max, 'filter_attr'=>$filter_attr_str), $cat['cat_name']);
		
			}

			$cat_arr[$row['cat_id']]['brands'] = $brands;
			$cat_arr[$row['cat_id']]['articles'] = $articles;

            if ($row['is_show'])
            {
                $cat_arr[$row['cat_id']]['id']   = $row['cat_id'];
                $cat_arr[$row['cat_id']]['name'] = $row['cat_name'];
                $cat_arr[$row['cat_id']]['url']  = build_uri('category', array('cid' => $row['cat_id']), $row['cat_name']);

                if (isset($row['cat_id']) != NULL)
                {
                    $cat_arr[$row['cat_id']]['cat_id'] = get_child_tree_pro($row['cat_id']);
                }
            }
        }
    }


    if(isset($cat_arr))
    {
        return $cat_arr;
    }
}

function get_child_tree_pro($tree_id = 0)
{
    $three_arr = array();
    $sql = 'SELECT count(*) FROM ' . $GLOBALS['ecs']->table('category') . " WHERE parent_id = '$tree_id' AND is_show = 1 ";
    if ($GLOBALS['db']->getOne($sql) || $tree_id == 0)
    {
        $child_sql = 'SELECT cat_id, cat_name, parent_id, is_show ' .
                'FROM ' . $GLOBALS['ecs']->table('category') .
                "WHERE parent_id = '$tree_id' AND is_show = 1 ORDER BY sort_order ASC, cat_id ASC";
        $res = $GLOBALS['db']->getAll($child_sql);
        foreach ($res AS $row)
        {
            if ($row['is_show'])

               $three_arr[$row['cat_id']]['id']   = $row['cat_id'];
               $three_arr[$row['cat_id']]['name'] = $row['cat_name'];
               $three_arr[$row['cat_id']]['url']  = build_uri('category', array('cid' => $row['cat_id']), $row['cat_name']);

               if (isset($row['cat_id']) != NULL)
                   {
                       $three_arr[$row['cat_id']]['cat_id'] = get_child_tree($row['cat_id']);

            }
        }
    }
    return $three_arr;
}

//传入goods_id获取折扣和节省
function get_discount($goods_id)
{
		/*$sql = 'SELECT market_price,shop_price,promote_price FROM '.$GLOBALS['ecs']->table('goods')." WHERE goods_id = $goods_id ";
		
		
		$row = $GLOBALS['db']->getRow($sql);
		
		$price=$row['market_price']; //原价 
		if($row['promote_price'] > 0) //如果促销价大于0则现价为促销价
		{
			$nowprice=$row['promote_price']; //现价 
		}
		else //否则为本店价
		{
			$nowprice=$row['shop_price']; //现价 
		}
		$jiesheng=$price-$nowprice; //节省金额 
		
		*/
		
		
		$final_price = get_final_price($goods_id);
		$sql = 'SELECT market_price FROM '.$GLOBALS['ecs']->table('goods')." WHERE goods_id = $goods_id ";
		$market_price = $GLOBALS['db']->getOne($sql);
		
		$jiesheng=$market_price-$final_price; //节省金额 
		
		$arr['jiesheng'] = $jiesheng; 
		
		
		//$discount折扣计算 
		if ( $final_price > 0 ) 
		{ 
			$arr['discount'] = round(10 *($final_price / $market_price), 1); 
		} 
		else 
		{ 
			$arr['discount'] = 0; 
		} 
	
		if ($arr['discount'] <= 0 )
		{
			$arr['discount'] = 0; 
		}
	
		
	return $arr;
	
}


/*评论百分比*/
function comment_percent($goods_id)
{
	$sql = 'SELECT COUNT(*) AS haoping FROM '.$GLOBALS['ecs']->table('comment')." WHERE id_value = '$goods_id' AND comment_type=0 AND status = 1 AND parent_id = 0 AND (comment_rank = 4 OR comment_rank = 5)";
	$haoping_count = $GLOBALS['db']->getOne($sql); 	
	
	$sql = 'SELECT COUNT(*) AS zhongping FROM '.$GLOBALS['ecs']->table('comment')." WHERE id_value = '$goods_id' AND comment_type=0 AND status = 1 AND parent_id = 0 AND (comment_rank = 2 OR comment_rank = 3)";
	$zhongping_count = $GLOBALS['db']->getOne($sql); 
	
	$sql = 'SELECT COUNT(*) AS chaping FROM '.$GLOBALS['ecs']->table('comment')." WHERE id_value = '$goods_id' AND comment_type=0 AND status = 1 AND parent_id = 0 AND comment_rank = 1";
	$chaping_count = $GLOBALS['db']->getOne($sql); 
	
	$sql = 'SELECT COUNT(*) AS comment_count FROM '.$GLOBALS['ecs']->table('comment')." WHERE id_value = '$goods_id' AND comment_type=0 AND status = 1 AND parent_id = 0";
	$comment_count = $GLOBALS['db']->getOne($sql); 
	
	$arr['haoping_percent'] = substr(number_format(($haoping_count/$comment_count)*100, 2, '.', ''), 0, -1);
	$arr['zhongping_percent'] = substr(number_format(($zhongping_count/$comment_count)*100, 2, '.', ''), 0, -1); 
	$arr['chaping_percent'] = substr(number_format(($chaping_count/$comment_count)*100, 2, '.', ''), 0, -1); 
	
	if($comment_count == 0)
	{
		$arr['haoping_percent'] = 100;
	}
	
	foreach($arr as $key => $val)
	{
		if($val == 0.0)
		{
			$arr[$key] = 0;
		}
	}

	return $arr;
	
}

?>