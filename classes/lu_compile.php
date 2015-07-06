<?php
/*	-----------------------------------------------------------------------
* 	说明：加密解密编译类classname: Lu_compile
* 	-----------------------------------------------------------------------
*   构造函数参数说明
*	@ $is_serialize =false  //是否需要序列化,默认否
*	@ $interference = 1         //如果参数是数字的话，默认加上扰乱码,有默认值
* 	-----------------------------------------------------------------------
*
* 	$ser = new Lu_compile(bool $is_serialize=false ,string $interference= 0);

* 	加密，返回字符串
* 	string $ser->encrypt(string $txt [,string $key]);

* 	解密，返回字符串
* 	string $ser->decrypt(string $txt [,string $key]);
* 	-----------------------------------------------------------------------
* 	-----------------------------------------------------------------------
*/
/* $ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "172.16.67.183/demo.php");
curl_setopt($ch, CURLOPT_POSTFIELDS, 'username=234&password=345');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, "Jimmy's CURL Example beta");
$data = curl_exec($ch);
curl_close($ch);
print_r($data); */
//echo md5('000000');

#-------------------------------------------------------------------------
#           CLASS START            
#-------------------------------------------------------------------------
class lu_compile
{
	private $key 		  = 'V2pbOQc9BT0BNVYzWGgOaQUkUygGLwZhU2FXdVQuV2UHPVA5',
	        $txt 		  = '',
	        $interference = '[`(--hemengcity.net.con--)`]',
	        $pattern 	  = '/^\d+$/',
	        $is_serialize = false,
	        $_is_int 	  = false;
			
	private	$sign_key     = 'N7RB';
#   ----------------------------------------------------------------------
#    构造函数 
#	@ $is_serialize = false  //是否需要序列化,默认否
#	@ $interference = ''     //如果参数是数字的话，默认加上扰乱码,有默认值           
#   ----------------------------------------------------------------------
	function Lu_compile( $is_serialize = false  ,$interference = 0)
	{
		!empty($is_serialize) && $this->is_serialize = $is_serialize ;		
		!empty($interference) && $this->interference = $interference ;
	}

	
	
#   ----------------------------------------------------------------------
#    加密文本           
#   ----------------------------------------------------------------------
	function encrypt($txt,$key='')
	{
		if(empty($txt)) return '';		
		!empty($key) && $this->key = $key ;
		!empty($txt) && $this->txt = $txt ;		
		if(is_int( $this->txt ) or preg_match($this->pattern,$this->txt))
		{
			$this->txt .= $this->interference;
			$this->_is_int = true;
		}
        $this->is_serialize && $this->txt = serialize($this->txt);		
		return $this->passport_encrypt($this->txt, $this->key);
	}
	
	
#   ----------------------------------------------------------------------
#    解密文本           
#   ----------------------------------------------------------------------
	function decrypt($txt,$key='')
	{	
		if(empty($txt)) return '';
		!empty($txt) && $this->txt = $txt ;
		!empty($key) && $this->key = $key ;		
		$result = $this->passport_decrypt($this->txt, $this->key);
		$this->is_serialize && $result = unserialize($result);			
		($this->_is_int or strpos($result,$this->interference) >= 0) && $result = str_replace($this->interference,'',$result);		
		return $result;	
	}
#   ----------------------------------------------------------------------
#   加密           
#   ----------------------------------------------------------------------
	private function passport_encrypt($txt, $key) 
	{
		srand((double)microtime() * 1000000);
		$encrypt_key = md5(rand(0, 32000));
		$ctr = 0;
		$tmp = '';
		for($i = 0;$i < strlen($txt); $i++) 
		{
			$ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
			$tmp .= $encrypt_key[$ctr].($txt[$i] ^ $encrypt_key[$ctr++]);
		}
		return base64_encode($this->passport_key($tmp, $key));
	}	
#   ----------------------------------------------------------------------
#   解密           
#   ----------------------------------------------------------------------
	private function passport_decrypt($txt, $key) 
	{
		$txt = $this->passport_key(base64_decode($txt), $key);
		$tmp = '';
		for($i = 0;$i < strlen($txt); $i++) 
		{
			$md5 = $txt[$i];
			$tmp .= $txt[++$i] ^ $md5;
		}
		return $tmp;
	}
	private function passport_key($txt, $encrypt_key) 
	{
		$encrypt_key = md5($encrypt_key);
		$ctr = 0;
		$tmp = '';
		for($i = 0; $i < strlen($txt); $i++) {
		$ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
		$tmp .= $txt[$i] ^ $encrypt_key[$ctr++];
		}
		return $tmp;
	}
	
	//转字符串
	function turn_string($arr)
	{
		$arr['key'] = $this->sign_key;
		return http_build_query($arr);
	}
	
	//转回数组
	function turn_arr($user='')
	{
		parse_str($user,$arr);
		if($arr['key'] == $this->sign_key)
		{
			unset($arr['key']);
			return $arr;
		}
		else
		{
			return false;
		}
	}
}
#-------------------------------------------------------------------------
#  			CLASS END    
#-------------------------------------------------------------------------
?> 