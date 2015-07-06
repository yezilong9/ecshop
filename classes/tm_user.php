<?php
/**
* 处理天猫代购过来的会员
* 
**/

class tm_user{


	public $pwd_url   = 'http://portal.txd168.com/auth/mdpwd_o2o';//修改密码url
	
	public $check_url = 'http://portal.txd168.com/auth/login_o2o';//验证会员信息url
	/**
	* 查询代理商域名
	* @$agency_id 代理商userid
	**/
	public function tm_agency_url($agency_id='')
	{
		if(!$agency_id)
			return false;
		$agency_url = $GLOBALS['db']->getOne("select agency_url from ".$GLOBALS['ecs']->table('agency_url')." where agency_user_id = $agency_id");
		return $agency_url?$agency_url:'';
	}
	
	/**
	* 插入代理商信息
	* @$agencyName 代理商名
	* @$password   代理商密码
	* @$email      代理商邮箱
	* @$state      是否是单独过来 0是单独过来，1是和四五级一起过来
	* @$md         0是需要md5,1是已经md5
	**/
	public function add_agency($agencyName, $password, $email,$state='0',$md='0')
	{
		$user_id = $GLOBALS['db']->getOne("SELECT user_id from ".$GLOBALS['ecs']->table('users').
		" WHERE user_name = '$agencyName'");
		if($user_id)
		{
			return $user_id;
		}
		//插入会员信息
		if($md=='0')
		{
			$GLOBALS['user']->add_user($agencyName, $password,$email);
			$MDpassword = MD5($password);
		}else{
			$GLOBALS['user']->add_user($agencyName, $password,$email,-1,0,0,$password);
			$MDpassword = $password;
		}
		$user_id = $GLOBALS['db']->insert_id();
		$add_time = gmtime();
		//获取代理商角色
		$action_list = $GLOBALS['db']->getRow("select role_id,action_list from " .$GLOBALS['ecs']->table('role'). " where role_name = '代理商'");
		$sql = "SELECT nav_list FROM " . $GLOBALS['ecs']->table('admin_user') . " WHERE action_list = 'all'";
        $row = $GLOBALS['db']->getRow($sql);
		//插入代理商信息
        $sql = "INSERT INTO ".$GLOBALS['ecs']->table('admin_user')." (user_name, email, password, add_time, nav_list,agency_user_id,action_list,role_id) ".
           "VALUES ('".$agencyName."', '".$email."', '$MDpassword', '$add_time', '$row[nav_list]','$user_id','$action_list[action_list]',$action_list[role_id])";
		$GLOBALS['db']->query($sql);
		
		/*add by hg for date 2014-05-04 生成代理商商店设置 begin*/
		$shop_res = $GLOBALS['db']->getAll("select parent_id,code,type,store_range,store_dir,value,sort_order from ".$GLOBALS['ecs']->table('shop_config')." where parent_id = 1");
		foreach($shop_res as $shop_k=>$shop_v){
			$GLOBALS['db']->query("INSERT INTO ".$GLOBALS['ecs']->table('agency_shop_config')." (parent_id, code, type, store_range, store_dir,value,sort_order,admin_agency_id) ".
			   "VALUES ('$shop_v[parent_id]', '$shop_v[code]', '$shop_v[type]', '$shop_v[store_range]', '$shop_v[store_dir]','$shop_v[value]','$shop_v[sort_order]','$user_id')");
		}
		$update_data['reg_time']  = local_strtotime(local_date('Y-m-d H:i:s'));
		$update_data['user_rank'] = '4';
		$update_data['tm_mark']   = '1';
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('users'), $update_data, 'UPDATE', 'user_id = ' . $user_id);
		/*发送通知邮件*/
		if(empty($state))
		{	
			//注释发送邮件
			//send_mail($agencyName, $email, '欢迎登陆天下店平台', "<p>尊敬的天下店用户：</p><p>您好！</p><p>你的账号是：<span style='color:red'>$agencyName</span></p><p>初始密码是：<span style='color:red'>$password</span></p><p>温馨提示：为了你的账号安全请尽快修改密码！</p><p>http://o2o.txd168.com/</p>", 1);
		}
		return $user_id;
	}
	
	/**
	* 处理天猫代购过来的会员登陆动作
	**/
	public function tm_login($username)
	{
		$GLOBALS['user']->set_session($username);
        $GLOBALS['user']->set_cookie($username);
	}
	
	/**
	* 插入会员信息
	* 
	**/
	public function add_user($username,$password,$email,$agencyNameId,$md='0')
	{
		if($md=='0')
		{
			$GLOBALS['user']->add_user($username, $password,$email);
		}else{
			$GLOBALS['user']->add_user($username, $password,$email,-1,0,0,$password);
		}
		$user_id = $GLOBALS['db']->insert_id();
		$update_data['reg_time'] = local_strtotime(local_date('Y-m-d H:i:s'));
		$update_data['top_rank'] = $agencyNameId;
		$update_data['tm_mark'] = '1';
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('users'), $update_data, 'UPDATE', 'user_id = ' . $user_id);
		return $user_id;
		//注释发送邮件
		//send_mail($username, $email, '欢迎登陆天下店平台', "<p>尊敬的天下店用户：</p><p>您好！</p><p>你的账号是：<span style='color:red'>$username</span></p><p>初始密码是：<span style='color:red'>$password</span></p><p>温馨提示：为了你的账号安全请尽快修改密码！</p><p>http://o2o.txd168.com/</p>", 1);
	}
	/**
	* 检查会员信息
	**/
	public function check_user($username)
	{
		return $user_id = $GLOBALS['db']->getOne("select user_id from ".$GLOBALS['ecs']->table('users')." where user_name = '$username'");
	}
	
	/**
	* tm用户直接在o2o登陆
	* @$username 用户名
	* @$password 密码
	**/
	public function post_tm_user($username,$password)
	{
		if(!$username || !$password)return 0;
		//发送数据
		$lu_compile = new lu_compile();
		$data = $lu_compile->encrypt('username='.$username.'&password='.md5($password).'');
		//dump('username='.$username.'&password='.$password.'');
		$returnData = $this->post($data,$this->check_url);
		//dump($returnData);
		if(strlen($returnData) > 1)
			$returnData = $lu_compile->decrypt(urldecode($returnData));
		//dump($returnData);
		//判断并插入数据
		if($returnData == '1')
		{
			return $this->add_agency(TMUSER.$username, $password, $username.'@163.com','0');
		}elseif($returnData == '0')
		{
			return 0;
		}else
		{
			parse_str($returnData,$arr);
			$agencyNameId = $this->add_agency(TMUSER.$arr['username'], $arr['password'], $arr['username'].'@163.com','0','1');
			return $this->add_user(TMUSER.$username,$password,$username.'@163.com',$agencyNameId);
		}
	}
	/**
	* 修改密码同步到O2O平台
	* @$username 用户名
	* @$password 修改后密码
	**/
	public function update_pwd($username,$password)
	{
		$lu_compile = new lu_compile();
		if(!$username || !$password)return 0;
		$tmUsername = TMUSER.$username;
		$password_old = $GLOBALS['db']->getOne("select password from ".$GLOBALS['ecs']->table('users')." where user_name = '$tmUsername'");
		$data = $lu_compile->encrypt('username='.$username.'&password_old='.$password_old.'&password_new='.$password.'');
		$returnData = $this->post($data,$this->pwd_url);
		print_r($returnData);
		//dump('username='.$username.'&password_old='.$password_old.'&password_new='.$password.'');
		return (int) ($returnData);
	}
	
	/**
	* post数据
	* @$data 发送的数据 
	* @$url  发送地址 
	**/
	public function post($data,$url)
	{
		$ch	= curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, 'data='.$data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, "Jimmy's CURL Example beta");
		$returnData = curl_exec($ch);
		curl_close($ch);
		return $returnData;
	}
	
	/**
	* 检查是否是TM过来的用户
	**/
	public function check_tm_user($user_id)
	{
		return $user_id?$GLOBALS['db']->getOne("select tm_mark from ".$GLOBALS['ecs']->table('users')." where user_id = $user_id"):'0';
	}
}
?>