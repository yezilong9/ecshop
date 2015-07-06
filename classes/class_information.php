<?php
/**
 *  本类用于index.php显示资讯信息
 * ============================================================================
 * * 版权所有 2005-2012 广州新泛联数码有限公司，并保留所有权利。
 * 网站地址: http://www..com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: zenghd $
 * $Id: class_information.php 17217 2014-09-18 20:01:08Z zenghd $
*/
class class_information{
	
	private $db = '';
	
	private $ecs = '';
	
	function __construct($admin_agency_id=0)
	{
		$this->admin_agency_id = $admin_agency_id;
		$this->db = $GLOBALS['db'];
		$this->ecs = $GLOBALS['ecs'];
	}
	
	/**
	* 获取资讯分类名称
	**/
	public function get_info_cats()
	{
		$info_cats = array();
		$sql_info_cats = "SELECT info_cat_id,info_cat_name FROM ".$this->ecs->table('information_category')." WHERE admin_agency_id = $this->admin_agency_id AND is_show = 1 ORDER BY show_order ASC";
		$rows=$this->db->getAll($sql_info_cats);
		foreach($rows as $r_k => $r_v){
			$info_cats[$r_v['info_cat_id']] = $r_v['info_cat_name'];
		}
		return $info_cats;
		
	}
	
	/**
	* 获取资讯信息
	**/
	public function get_infos_list()
	{
		$info_cats = $this->get_info_cats();
		//print_r($info_cats);
		$infos_list = array();
		$i = 0;
		foreach($info_cats as $i_k=>$i_v){
			$sql_infos_list = "SELECT info_cat_id,img_spec,img_file,title_describe,content_describe,link_url,is_start FROM ".$this->ecs->table('information')." WHERE info_cat_id = $i_k  AND is_start = 1 ORDER BY info_id DESC LIMIT 5 ";
			$rows = $this->db->getAll($sql_infos_list);
			//print_r($rows);
			if(!empty($rows)){
				foreach($rows as $r_k => $r_v){
					$infos_list[$i]['info_article']= $i_v;
					if($r_v['img_spec'] == '240x160'){
						$infos_list[$i][$r_v['img_spec']][]= $r_v;
					}else{
						$infos_list[$i][$r_v['img_spec']]= $r_v;
					}
				}
			}else{
				$infos_list[$i]['info_article']= $i_v;
				$infos_list[$i][]= array();
				
			}
			
			$i++;
		
		}
		//dump($infos_list);
		return $infos_list;
	
	}
	
	
	
}
?>