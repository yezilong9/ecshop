<?php
/*********************************
* 说明：处理代理商分类相关
* date:2014-8-8
* author:hg
* 
*
*********************************/

class class_category
{
    
    /**
    * 说明:分类已存在的情况下添加分类
    * @$exist_cat_id 分类ID
    * @$admin_agency_id 代理商ID
    * @$agency_attr    需要区分代理和主站的属性
    **/
    public function exist_add_cat($exist_cat_id,$admin_agency_id,$agency_attr)
    {
        $res = $GLOBALS['db']->getRow("SELECT agency_cat,host_cat FROM ".$GLOBALS['ecs']->table('category').
        " WHERE cat_id = $exist_cat_id");
        #主站添加分类
        if(!$admin_agency_id)
        {
            if($res['host_cat']) return true;//已经添加
            $set = ',';
            foreach($agency_attr as $key=>$value){
                $set .= $key.'='."'$value',";
            }
            $set = substr($set,0,-1);
            $sql = "UPDATE ".$GLOBALS['ecs']->table('category').
            " SET host_cat = 1 $set WHERE cat_id = $exist_cat_id";
            if($GLOBALS['db']->query($sql)) return false;//添加成功
        }
        else
        {
            #代理商添加
            $return = $this->agency_exist_add_cat($res['agency_cat'],$exist_cat_id,$admin_agency_id,$agency_attr);
			return $return == true?true:false;
        }
        return false;
    }
	/**
	* 说明：分类已存在的情况下，代理商添加分类
	* add by hg for date 2014-09-02
	**/
	public function agency_exist_add_cat($agency_cat,$exist_cat_id,$admin_agency_id,$agency_attr)
	{
		if(strpos($agency_cat,','.$admin_agency_id.',') !== false) return true;//已存在
		if(empty($agency_cat)) $agency_cat = ',';
		$agency_cat .= $admin_agency_id.',';
		$GLOBALS['db']->query("UPDATE ".$GLOBALS['ecs']->table('category').
		" SET agency_cat = '$agency_cat' WHERE cat_id = $exist_cat_id");
		$agency_attr['admin_agency_id'] = $admin_agency_id;
		$agency_attr['cat_id'] = $exist_cat_id;
		if($GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('category_attribute'), $agency_attr, 'INSERT')) return false;
	}
	
    /**
    * 说明：插入新的一条分类数据
    * @$cat array 分类数据
    **/
    public function add_cat($cat)
    {
        $admin_agency_id = admin_agency_id();
        if($admin_agency_id)
        {
			if(empty($cat['agency_cat'])) $cat['agency_cat'] = ','.$admin_agency_id.',';
			if(!isset($cat['host_cat']))  $cat['host_cat'] = 0;
			$cat_id = $this->agency_add_cat($cat,$admin_agency_id);
			
        }
        else
        {
            $state = $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('category'), $cat);
            $cat_id = $GLOBALS['db']->insert_id();
        }
        return $cat_id;
    }
	
	/**
	* 说明:代理商添加分类
	* add by hg for date 2014-09-01
	**/
	public function agency_add_cat($cat,$admin_agency_id)
	{
		$cat['grade'] = '0';
		$cat['filter_attr'] = '';
		$cat['show_in_nav'] = '0';
		//$cat['is_show'] = '0';  //注释2015-03-20 代理商添加的分类要根据他选择(是否显示)实际，不能默认为0
		$state = $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('category'), $cat);
		$cat_id = $GLOBALS['db']->insert_id();
		$agency_attr = array(
					'grade'       => $cat['grade'],
					'filter_attr' => $cat['filter_attr'],
					'show_in_nav' => $cat['show_in_nav'],
					'is_show'     => $cat['is_show'],
					'admin_agency_id'     => $admin_agency_id,
					'cat_id'      => $cat_id,
					'sort_order'    => $cat['sort_order'],
					'measure_unit'  => $cat['measure_unit']
				);
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('category_attribute'), $agency_attr, 'INSERT');
		return $cat_id;
	}
    /** 
    * 说明:删除分类
    * @$cat_id 分类ID;
    *
    **/
    public function del_cat($cat_id)
    {
        $admin_agency_id = admin_agency_id();
        $cat_res = $GLOBALS['db']->getRow("SELECT agency_cat,host_cat FROM ".$GLOBALS['ecs']->table('category').
        " WHERE cat_id = $cat_id");
        if($admin_agency_id)//代理商删除分类
        {
            if(($cat_res['agency_cat'] == ','.$admin_agency_id.',') && empty($cat_res['host_cat']))
            {
                //删除代理商分类属性
                $GLOBALS['db']->query("DELETE FROM".$GLOBALS['ecs']->table('category_attribute').
                " WHERE cat_id=$cat_id AND admin_agency_id=$admin_agency_id");
                return true;
            }
            else
            {
                //删除代理商分类标识和分类属性
                $this->agency_del_cat($cat_res['agency_cat'],$cat_id,$admin_agency_id);
                return false;
            }
        }
        else
        {
            if(empty($cat_res['agency_cat']))
            {
                return true;
            }
            else
            {
                $GLOBALS['db']->query("UPDATE ".$GLOBALS['ecs']->table('category').
                " SET host_cat = 0 WHERE cat_id = $cat_id ");
                return false;
            }
        }
    }
    /**
    * 代理商删除分类
    * $agency_cat 分类标记属于哪个代理商
    * $cat_id       分类ID
    * $admin_agency_id 代理商ID
    * add by hg for date 2014-09-01
    **/
    public function agency_del_cat($agency_cat,$cat_id,$admin_agency_id)
    {
        $new_agency_cat = preg_replace("|,$admin_agency_id,|",',',$agency_cat);
        if($new_agency_cat == ',') $new_agency_cat = null;
        $GLOBALS['db']->query("UPDATE ".$GLOBALS['ecs']->table('category').
        " SET agency_cat = '$new_agency_cat' WHERE cat_id = $cat_id ");
        //删除代理商分类属性
        $GLOBALS['db']->query("DELETE FROM".$GLOBALS['ecs']->table('category_attribute').
        " WHERE cat_id=$cat_id AND admin_agency_id=$admin_agency_id");
        return true;
    }
	/**
	* 删除分类推荐
	* 
	**/
	public function del_cat_recommend($cat_id ,$admin_agency_id)
	{
		
	}
}





?>