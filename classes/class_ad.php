<?php
/**
* 文件说明:广告相关
* author:hg
* time：2014-09-04
**/

class class_ad{

	private $db;
	private $ecs;
	private static $_instance;
	
	private function __construct()
	{
		$this->db = $GLOBALS['db'];
		$this->ecs = $GLOBALS['ecs'];
	}
	public function __clone(){}
	
	public static function new_ad()
	{
		if(self::$_instance == null){
			self::$_instance = new self;
		}
		return self::$_instance;
	}
	
	/**
	* 获取当前访问文件名
	* return 文件名
	**/
	private function php_file()
	{
		$php_self = $_SERVER['PHP_SELF'];
		if(!$php_self) return false;
		preg_match("!/(.*?).php!ius",$php_self,$fiel_name);
		if(!$fiel_name) return false;
		return $fiel_name[1];
			
	}
	
	/**
	* 获取广告数据
	**/
	public function get_res()
	{
		$fiel_name = $this->php_file();
		if($fiel_name === false) return false;
		$admin_agency_id = agency_id();
		$res = $this->db->getAll("SELECT position_id,id,keyword,particulars,url,img,width,height,admin_agency_id,file,ad_name FROM ".
		$this->ecs->table('ad_new')." WHERE file like '%,$fiel_name,%' AND start = 1 AND admin_agency_id = $admin_agency_id");
		//$res = $this->db->getAll("SELECT position_id,id,keyword,particulars,url,img,width,height,admin_agency_id,file,ad_name FROM ".
		//$this->ecs->table('ad_new')." WHERE file like '%,$fiel_name,%' AND start = 1");
		if(empty($res)) return false;
		$return_arr = array();
		foreach($res as $key=>$value){
			if(strpos($value['img'],'http://') === false)
				$value['img'] = img_url().$value['img'];
			$return_arr[$value['position_id']] = $value;
		}
		
		return $return_arr;
	}
	/**
	* 一键生成代理商广告
	* add by hg for 2014-09-16
	**/
	public function create_agency_ad()
	{
		$sql = "SELECT id,keyword,particulars,url,img,width,height,admin_agency_id,file,ad_name,start,position_id".
		" FROM ".$this->ecs->table('ad_new'). " WHERE admin_agency_id = 0";
		$res = $this->db->getAll($sql);
		//检查代理商广告
		foreach($res as $key=>$value){
			if($value['position_id'])
			foreach(agency_list() as $agency_k=>$agency_v){
				$sql = "SELECT position_id FROM ".$this->ecs->table('ad_new').
				"  WHERE position_id = $value[position_id] AND admin_agency_id =$agency_k";
				$res = $this->db->getOne($sql);
				//代理商没有这个广告
				if(!$res)
				{
					$arr['keyword'] 		= $value['keyword'];
					$arr['particulars'] 	= $value['particulars'];
					$arr['url'] 			= '#';
					$arr['img']			 	= $value['img'];
					$arr['width'] 			= $value['width'];
					$arr['height']			= $value['height'];
					$arr['admin_agency_id'] = $agency_k;
					$arr['file'] 			= $value['file'];
					$arr['ad_name']			= $value['ad_name'];
					$arr['start'] 			= $value['start'];
					$arr['position_id'] 	= $value['position_id'];
					$this->db->autoExecute($this->ecs->table('ad_new'), $arr, 'INSERT');
				}
			}
		}
	}

	/**
	* 替换旧广告功能返回值 
	* $position_name 广告名称
	**/
	public function replace_ad($position_name)
	{
		if($position_name == '食品保健左侧广告')
		{
			$ad_arr = $this->get_res();//为了性能，判断成功再获取广告列表数据
			$url 	= $ad_arr[7]['url'];
			$img 	= $ad_arr[7]['img'];
			$width  = $ad_arr[7]['width'];
			$img 	= $ad_arr[7]['img'];
		}elseif($position_name == '食品保健右侧广告'){
			$ad_arr = $this->get_res();
			$url 	= $ad_arr[8]['url'];
			$img 	= $ad_arr[8]['img'];
			$width  = $ad_arr[8]['width'];
			$height = $ad_arr[8]['height'];
		}elseif($position_name == '居家生活左侧广告'){
			$ad_arr = $this->get_res();
			$url 	= $ad_arr[9]['url'];
			$img 	= $ad_arr[9]['img'];
			$width  = $ad_arr[9]['width'];
			$height = $ad_arr[9]['height'];
		}elseif($position_name == '居家生活右侧广告'){
			$ad_arr = $this->get_res();
			$url 	= $ad_arr[10]['url'];
			$img 	= $ad_arr[10]['img'];
			$width  = $ad_arr[10]['width'];
			$height = $ad_arr[10]['height'];
		}
		if($url)
			return '<a target="_blank" href="'.$url.'"><img width="'.$width.'" height="'.$height.'" border="0" src="'.$img.'"></a>';
		else
			return $position_name;
	}
}















?>