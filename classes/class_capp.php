<?php
/*********************************
* 说明:APP信息接口
* author:hg
* time：2014-09-22
*********************************/
class class_capp{

	private $app_key  = 'kJKSkS8lzAmq';//效验KEY

	private $post_arr = '';//获取到的数据

	private $db		  = '';
	
	private $ecs	  = '';
	
	private $code     = '';//状态标记
	
	private $msg     = '';//c错误信息
	
	private $user_id  = '';//用户ID
	
	private $present_version  = '';//当前版本号
	
	private $token_id = '';
	
	public function __construct()
	{
		$this->db  = $GLOBALS['db'];
		$this->ecs = $GLOBALS['ecs'];
	}
	
	/**
	* 效验数据
	* $post_arr array() 接收到的数据
	* return 效验结果 false or true
	**/
	public function verify($post_arr)
	{
		array_filter($post_arr);
		#信息有误
		if(!is_array($post_arr) || empty($post_arr)) return false;
		# 写session
		
		$verify_sign = $this->signature($post_arr);
		
		$this->token_id = $post_arr['token_id'];
		//dump($verify_sign);
		if($verify_sign == $post_arr['sign'])
		{
			#版本号验证
			if($this->check_version($post_arr['app_version']) !== true) $this->code = '2';
			#验证用户信息
			if(!empty($post_arr['user_info']) && ($post_arr['user_info'] != 'null')){
				if(($this->check_user($post_arr['user_info']) === false) && !$this->code) $this->code = '3';
			}
			#插入数据库
			$this->insert_app($post_arr);

			#信息正无误
			if(!$this->code){
				 $this->code = '1';
			}
		}
		else
		{
			#效验错误
			$this->code = '4';
			$this->msg  = '效验错误';
		}
	}
	
	/**
	* 数据签名
	* $arr 要签名的数据
	* return sting 返回签名结果
	**/
	public function signature($arr)
	{
		if(!is_array($arr) || empty($arr)) return false;
		unset($arr['sign']);
		ksort($arr);
		$sgin = '';
		foreach($arr as $key=>$value){
			$sgin .= $value;
		}
		return MD5($sgin .= $this->app_key);
	}
	
	/**
	* 把获取到的信息写进数据库
	* return 插入失败 false OR 插入成功 true 
	**/
	private function insert_app($post_arr)
	{
		$post_arr['insert_time'] = time();
		if($this->user_id) $post_arr['user_id'] = $this->user_id;
		return $this->db->autoExecute($this->ecs->table('connector_app'), $post_arr, 'INSERT');
	}
	
	/**
	* 验证版本号
	* $version 版本号
	* return true 
	**/
	private function check_version($version)
	{
		#...取得数据库当前最新的版本号
		#...对比系统当前最新版本号
		$present_version = $this->db->getOne("SELECT version FROM ".$this->ecs->table('version')." ORDER BY id desc");
		//dump($present_version);
		//dump($version);
		if($present_version == $version)
			return true;
		else
			$this->present_version = $present_version; //...当前最新版本号;
	}
	
	/**
	* 验证用户信息
	* $user_info 加密后的用户信息
	* return 失败 false
	**/
	private function check_user($user_info)
	{
		$obj = new lu_compile();
		$user = $obj->decrypt($user_info);
		$user_arr = explode('-',$user);
		if(!$user_arr) return false;
		$user_id = $this->db->getOne("SELECT user_id FROM ".$this->ecs->table('users').
		" WHERE user_id = $user_arr[0] AND user_name = '$user_arr[1]'");
		#进行数据库查询对比
		if($user_id)
		{
			$GLOBALS['user']->set_session($user_arr[1]);
			$this->user_id = $user_id;//成功
		}
		else
		{
			return false;//失败
		}
	}
	/**
	* 返回数据
	**/
	public function return_data()
	{
		if(!$this->code) return false;
		#返回数据
		$arr['code'] 	 = $this->code;
		$arr['msg'] 	 = 'null';
		$arr['data']     = 'null';
		$arr['token_id'] = $this->token_id;

		//成功
		if($this->code == '1' && $this->user_id)
		{
			$row = $this->db->getRow("SELECT user_id,user_name,email FROM ".$this->ecs->table('users').
			" WHERE user_id = $this->user_id");
			$obj = new lu_compile();
			$encrypt = $obj->encrypt($row['user_id'].'-'.$row['user_name']);
			$arr['data'] = $encrypt;
		}//版本需要强制升级
		elseif($this->code == '2')
		{
			$arr['msg']  = '版本需要强制升级';
			$arr['data'] = json_encode(array('present_version'=>$this->present_version));
		}
		elseif($this->code == '3')
		{
			$arr['msg']  = '用户未登录';
		}
		elseif($this->code == '4')
		{
			$arr['msg'] = $this->msg;
		}
		$sign = $this->signature($arr);
		if($arr !== false){
			$arr['sign'] = $sign;
			return json_encode($arr);
		}
		return false;
		
	}

}
?>