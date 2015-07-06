<?php

/******************************************************************************
Filename       : www.91ka.com/include/clsMobileNotice.php
Author         : SouthBear
Email          : SouthBear819@163.com
Date/time      : 短信通知类库
Purpose        : 
Mantis ID      : 
Description    : 
Revisions      : 
Modify         : 
Inspect        : 
******************************************************************************/

//短信发送类
class class_sms {
    private $user_name = '106';  //账号
    private $password = '5d3ptbpvrucrn8a4vd7ue4vf';  //密码
    private $url ='http://smsapi.gotogame.com.cn/send/submit'; //发送地址
    private $to = '';
    private $content = '';
    private $sub_id = '';
     
    public function __construct($to,$content,$sub_id = '') {
        $this->to = $to;
        $this->content = $content;
        $this->sub_id = $sub_id;
    }
     
    //发送短信
    public function send_sms() {
        $method = 'post';

        $str_qstring = $str_string = $sign = '';
        $str_qstring = 'project_id='.$this->user_name.'&phone='.$this->to.'&content='.$this->content;
        $str_string = $this->content.$this->to.$this->user_name;
        $sign = md5($str_string.$this->password);
        $str_qstring = $str_qstring.'&sign='.$sign;
        return $this->sms_sendRequest($method,$this->url,$str_qstring);
		
       /* switch($result['code']){
            case 0000: $return = '000'; break;//表示发送成功
            case 1000: $return = '001'; break;
            case 1001: $return = '002'; break;
            case 1002: $return = '003'; break;
            case 1003: $return = '004'; break;
            case 1004: $return = '005'; break;
            case 1005: $return = '006'; break;
        }
        return $return;    */
		
    }
    
// 自动转换字符集 支持数组转换
/*************************************************************
 * 参数说明
 * fContents：需要转换编码的数据源
 * from：数据源内容编码
 * to:   转换后的数据内容编码
 *************************************************************/
	public function iconv_charset ($fContents, $from, $to) {
		$from = strtoupper($from);
		$to   = strtoupper($to);
		if (strtoupper($from) === strtoupper($to) || empty($fContents) || (is_scalar($fContents) && !is_string($fContents))) {
			//如果编码相同或者非字符串标量则不转换
			return $fContents;
		}
		if (is_string($fContents)) {
			if (function_exists('mb_convert_encoding')) {
				return @mb_convert_encoding($fContents, $to, $from);
			} elseif (function_exists('iconv')) {
				return iconv($from, $to, $fContents);
			} else {
				return $fContents;
			}
		} elseif (is_array($fContents)) {
			foreach ($fContents as $key => $val) {
				$_key = iconv_charset($key, $from, $to);
				$fContents[$_key] = iconv_charset($val, $from, $to);
				if ($key != $_key)
					unset($fContents[$key]);
			}
			return $fContents;
		}  else {
			return $fContents;
		}
	} 
 	
	
	public function sms_sendRequest($method,$str_bgUrl,$str_qstring){
		 $ch = curl_init();
		 if(strtolower($method) == 'get'){
			curl_setopt($ch, CURLOPT_URL, $str_bgUrl.'?'.$str_qstring);
		 }else{
			curl_setopt($ch, CURLOPT_URL, $str_bgUrl);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $str_qstring);
		 }
		 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		 curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);       
		 curl_setopt($ch, CURLOPT_HEADER, 0);          
		 $data = curl_exec($ch);
		 curl_errno($ch);
		 if(curl_errno($ch) != 0){
			$int_err_code = '9999';
			$str_err_msg = '接口通知失败:网络错误.'.curl_error($ch);
		 }
		 curl_close($ch);
		 if($int_err_code) return false;
		 return $data;
	}
    
}

