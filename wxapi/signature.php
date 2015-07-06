<?php
/******************************************************************************
Filename       : signature.php
Author         : SouthBear
Email          : SouthBear819@163.com
Date/time      : 2014-07-11 11:14:10 
Purpose        : 微信开发者验证
Description    : 
Modify         : 
******************************************************************************/
//include_once("function.php");

$str_echostr = strval(trim($_REQUEST['echostr'])); //随机字符串
$str_nonce = strval(trim($_REQUEST['nonce'])); //随机数
$str_timestamp = strval(trim($_REQUEST['timestamp'])); //时间戳
$str_signature = strval(trim($_REQUEST['signature'])); //签名字串
/*
$user_mail = array('xiongbo@mail.untx.cn'); 
$subject   = '【微信测试】测试地址';                 
$mailBody  = '系统在' . date("Y-m-d H:i:s") . '收到数据，内容如下：<BR>';
$mailBody .= '[if/weixin/signature.php] 收到发来的参数：'.print_r($_REQUEST,true);
autoSendEmail($subject, $mailBody, $user_mail); 
*/

if (empty($str_echostr)) {
	die('随机字符串为空。NO-01');
}
if (empty($str_nonce)) {
	die('随机数为空。NO-02');
}
if (empty($str_timestamp)) {
	die('时间戳为空。NO-03');
}
if (empty($str_signature)) {
	die('签名字串为空。NO-04');
}

define('TOKEN','untxO2Otxd');

$arr_data = array();
$arr_data['token'] = TOKEN;
$arr_data['timestamp'] = $str_timestamp;
$arr_data['nonce'] = $str_nonce;
$arr_data['signature'] = $str_signature;

$obj_weixin_check = new weixin_check;
$bol_sign = $obj_weixin_check->check_signature($arr_data);
if ($bol_sign) {//验证签名OK
	echo $str_nonce.'<br/>';
/*
$user_mail = array('xiongbo@mail.untx.cn'); 
$subject   = '【微信测试】测试地址';              
$mailBody  = 'System Is' . date("Y-m-d H:i:s") . '[if/weixin/signature.php]  check Sign is Ok,echo is ：'.$str_nonce;
autoSendEmail($subject, $mailBody, $user_mail); 
*/
} else {
	echo 'Error'.'<br/>';
}

class weixin_check {
	
	/**
	 * 验证签名
	 * @paras
	 * return bollean
	 */
	function check_signature ($paras) {
		if (empty($paras)) {
			return false;
		}
		$arr_data = array();
		$arr_data['token'] = TOKEN;
		$arr_data['timestamp'] = $paras['timestamp'];
		$arr_data['nonce'] = $paras['nonce'];
		sort($arr_data, SORT_STRING);
		
		$str_clear_string = implode($arr_data);
		$str_encypt_string = sha1($str_clear_string);

		if ($str_encypt_string == $paras['signature']) {
	    	return true;
	    } else {
			return false;
		}
	}

	/**
	 * 返回数据
	 * @paras
	 * return 
	 */
    function response_msg() {
		//get post data, May be due to the different environments
		$str_postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

      	//extract post data
		if (!empty($str_postStr)){                
			$postObj = simplexml_load_string($str_postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $keyword = trim($postObj->Content);
            $time = time();
			$textTpl = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[%s]]></MsgType>
						<Content><![CDATA[%s]]></Content>
						<FuncFlag>0</FuncFlag>
						</xml>";             
			if(!empty( $keyword )){
          		$msgType = "text";
            	$contentStr = "Welcome to wechat world!";
            	$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
            	echo $resultStr;
			} else {
				echo "Input something...";
			}
        } else {
        	echo "Error";
        	exit;
        }
    } 

}