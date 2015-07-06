<?php
/******************************************************************************
Filename       : api.php
Author         : SouthBear
Email          : SouthBear819@163.com
Date/time      : 2014-07-11 11:14:10 
Purpose        : 微信开发者API汇集
Description    : 
Modify         : 
******************************************************************************/
include('weixin.class.php');

$str_action = 'access_token'; //send_sevice_msg

$obj_weixin = new weixin();

/**
 * 开发者验证
 */
if ($str_action == 'check_dev') {
	$str_echostr = strval(trim($_REQUEST['echostr'])); //随机字符串
	$str_nonce = strval(trim($_REQUEST['nonce'])); //随机数
	$str_timestamp = strval(trim($_REQUEST['timestamp'])); //时间戳
	$str_signature = strval(trim($_REQUEST['signature'])); //签名字串
/*
$str_echostr = '3458335650061737471';
$str_nonce = '627010994';
$str_timestamp = '1405049982';
$str_signature = 'f5d9d92c47b52da4215ff632aa6e25424fe2ebbf';
*/
	$arr_data = array();
	$arr_data['action'] = 'check_dev';
	$arr_data['timestamp'] = $str_timestamp;
	$arr_data['nonce'] = $str_nonce;
	$arr_data['signature'] = $str_signature;
	//$arr_data['echostr'] = $str_echostr;

	$bol_result = $obj_weixin->weixin($arr_data);
	if ($bol_result) {
		die($str_echostr);
	} else {
		die('Error');
	}
}

/**
 * 获取access_token
 */
if ($str_action == 'access_token') {
	$arr_data = array();
	$arr_data['action'] = 'access_token';
	$arr_result = $obj_weixin->weixin($arr_data);
echo '<pre>';
print_r($arr_result);
echo '</pre>';
}

/**
 * 上传多媒体文件
 * type 类型  图片（image）、语音（voice）、视频（video）和缩略图（thumb）
 * media 类型 form-data中媒体文件标识，有filename、filelength、content-type等信息
 */
if ($str_action == 'upload_midea') {
	$arr_data = array();
	$arr_data['action'] = 'access_token';
	$arr_result = $obj_weixin->weixin($arr_data);
	$str_access_token = $arr_result['access_token'];
	
	$arr_data = array();
	$arr_data['action'] = 'midea';
	$arr_data['access_token'] = $str_access_token;
	$arr_data['type'] = 'image';
	$arr_data['media'] = $paras['media'];	
	$arr_result = $obj_weixin->weixin($arr_data);
echo '<pre>';
print_r($arr_result);
echo '</pre>';
}

/**
 * 下载多媒体文件
 * type 类型  图片（image）、语音（voice）、视频（video）和缩略图（thumb）
 * media 类型 form-data中媒体文件标识，有filename、filelength、content-type等信息
 */
if ($str_action == 'down_midea') {
	$arr_data = array();
	$arr_data['action'] = 'access_token';
	$arr_result = $obj_weixin->weixin($arr_data);
	$str_access_token = $arr_result['access_token'];

	$arr_data = array();
	$arr_data['action'] = 'down_midea';
	$arr_data['access_token'] = $str_access_token;
	$arr_data['media_id'] = $paras['media'];
	$arr_result = $obj_weixin->weixin($arr_data);
echo '<pre>';
print_r($arr_result);
echo '</pre>';
}

/**
 * 接收普通消息
 */
if ($str_action == 'receive_msg') {
//文本消息
/*
	$xml_file = '<xml>
 <ToUserName><![CDATA[toUser]]></ToUserName>
 <FromUserName><![CDATA[fromUser]]></FromUserName> 
 <CreateTime>1348831866</CreateTime>
 <MsgType><![CDATA[text]]></MsgType>
 <Content><![CDATA[this is a test]]></Content>
 <MsgId>1234567890123456</MsgId>
 </xml>';
*/
	$xml_file = '<xml>
 <ToUserName><![CDATA[EricPolly]]></ToUserName>
 <FromUserName><![CDATA[o2o-txd168]]></FromUserName> 
 <CreateTime>1348831866</CreateTime>
 <MsgType><![CDATA[text]]></MsgType>
 <Content><![CDATA[this is a test]]></Content>
 <MsgId>1234567890123456</MsgId>
 </xml>';

 
//图片消息
/*
	$xml_file = ' <xml>
 <ToUserName><![CDATA[toUser]]></ToUserName>
 <FromUserName><![CDATA[fromUser]]></FromUserName>
 <CreateTime>1348831860</CreateTime>
 <MsgType><![CDATA[image]]></MsgType>
 <PicUrl><![CDATA[this is a url]]></PicUrl>
 <MediaId><![CDATA[media_id]]></MediaId>
 <MsgId>1234567890123456</MsgId>
 </xml>';
*/

//语音消息
/*
	$xml_file = '<xml>
	<ToUserName><![CDATA[toUser]]></ToUserName>
	<FromUserName><![CDATA[fromUser]]></FromUserName>
	<CreateTime>1357290913</CreateTime>
	<MsgType><![CDATA[voice]]></MsgType>
	<MediaId><![CDATA[media_id]]></MediaId>
	<Format><![CDATA[Format]]></Format>
	<MsgId>1234567890123456</MsgId>
	</xml>';
*/

//视频消息
/*
	$xml_file = '<xml>
<ToUserName><![CDATA[toUser]]></ToUserName>
<FromUserName><![CDATA[fromUser]]></FromUserName>
<CreateTime>1357290913</CreateTime>
<MsgType><![CDATA[video]]></MsgType>
<MediaId><![CDATA[media_id]]></MediaId>
<ThumbMediaId><![CDATA[thumb_media_id]]></ThumbMediaId>
<MsgId>1234567890123456</MsgId>
</xml>';
*/

//地理位置消息
/*
	$xml_file = '<xml>
<ToUserName><![CDATA[toUser]]></ToUserName>
<FromUserName><![CDATA[fromUser]]></FromUserName>
<CreateTime>1351776360</CreateTime>
<MsgType><![CDATA[location]]></MsgType>
<Location_X>23.134521</Location_X>
<Location_Y>113.358803</Location_Y>
<Scale>20</Scale>
<Label><![CDATA[Laction]]></Label>
<MsgId>1234567890123456</MsgId>
</xml>';
*/

//链接消息
/*
	$xml_file = '<xml>
<ToUserName><![CDATA[toUser]]></ToUserName>
<FromUserName><![CDATA[fromUser]]></FromUserName>
<CreateTime>1351776360</CreateTime>
<MsgType><![CDATA[link]]></MsgType>
<Title><![CDATA[公众平台官网链接]]></Title>
<Description><![CDATA[公众平台官网链接]]></Description>
<Url><![CDATA[url]]></Url>
<MsgId>1234567890123456</MsgId>
</xml>';
*/
  
	$arr_data = array();
	$arr_data['action'] = 'receive_msg';
	$arr_data['msg'] = $obj_weixin->iconv_charset($xml_file,"GBK",'UTF-8');
	//$arr_data['msg'] = $xml_file;
	//$arr_data['msg'] = parse_str(file_get_contents('php://input'), $_POST);
	$arr_result = $obj_weixin->weixin($arr_data);
echo '<pre>';
print_r($arr_result);
echo '</pre>';
//exit;
	if ($arr_result['MsgType'] == 'text') {//发送被动响应消息
		$arr_data = array();
		$arr_data['action'] = 'send_msg';
		$arr_data['ToUserName'] = $arr_result['ToUserName'];
		$arr_data['FromUserName'] = $arr_result['FromUserName']; 
		$arr_data['CreateTime'] = $arr_result['CreateTime'];
		$arr_data['MsgType'] = $arr_result['MsgType']; 
		$arr_data['Content'] = $arr_result['Content'];	
		
		$arr_result = $obj_weixin->weixin($arr_data);
echo '<pre>';
print_r($arr_result);
echo '</pre>';
		
	}
}

/**
 * 接收事件推送
 */
if ($str_action == 'receive_event') {
//关注/取消关注事件
/*
	$xml_file = '<xml>
<ToUserName><![CDATA[toUser]]></ToUserName>
<FromUserName><![CDATA[FromUser]]></FromUserName>
<CreateTime>123456789</CreateTime>
<MsgType><![CDATA[event]]></MsgType>
<Event><![CDATA[subscribe]]></Event>
</xml>';
*/

//扫描带参数二维码事件
/*
	$xml_file = '<xml>
<ToUserName><![CDATA[toUser]]></ToUserName>
<FromUserName><![CDATA[FromUser]]></FromUserName>
<CreateTime>123456789</CreateTime>
<MsgType><![CDATA[event]]></MsgType>
<Event><![CDATA[subscribe]]></Event>
<EventKey><![CDATA[qrscene_123123]]></EventKey>
<Ticket><![CDATA[TICKET]]></Ticket>
</xml>';
*/

//用户已关注时的事件推送
/*
	$xml_file = '<xml>
<ToUserName><![CDATA[toUser]]></ToUserName>
<FromUserName><![CDATA[FromUser]]></FromUserName>
<CreateTime>123456789</CreateTime>
<MsgType><![CDATA[event]]></MsgType>
<Event><![CDATA[SCAN]]></Event>
<EventKey><![CDATA[SCENE_VALUE]]></EventKey>
<Ticket><![CDATA[TICKET]]></Ticket>
</xml>';
*/


//上报地理位置事件
/*
	$xml_file = '<xml>
<ToUserName><![CDATA[toUser]]></ToUserName>
<FromUserName><![CDATA[fromUser]]></FromUserName>
<CreateTime>123456789</CreateTime>
<MsgType><![CDATA[event]]></MsgType>
<Event><![CDATA[LOCATION]]></Event>
<Latitude>23.137466</Latitude>
<Longitude>113.352425</Longitude>
<Precision>119.385040</Precision>
</xml>';
*/

//自定义菜单事件
/*
	$xml_file = '<xml>
<ToUserName><![CDATA[toUser]]></ToUserName>
<FromUserName><![CDATA[FromUser]]></FromUserName>
<CreateTime>123456789</CreateTime>
<MsgType><![CDATA[event]]></MsgType>
<Event><![CDATA[CLICK]]></Event>
<EventKey><![CDATA[EVENTKEY]]></EventKey>
</xml>';
*/


//点击菜单跳转链接时的事件推送
/**/
	$xml_file = '<xml>
<ToUserName><![CDATA[toUser]]></ToUserName>
<FromUserName><![CDATA[FromUser]]></FromUserName>
<CreateTime>123456789</CreateTime>
<MsgType><![CDATA[event]]></MsgType>
<Event><![CDATA[VIEW]]></Event>
<EventKey><![CDATA[www.qq.com]]></EventKey>
</xml>';



	$arr_data = array();
	$arr_data['action'] = 'receive_event';
	$arr_data['msg'] = $obj_weixin->iconv_charset($xml_file,"GBK",'UTF-8');
	$arr_result = $obj_weixin->weixin($arr_data);
echo '<pre>';
print_r($arr_result);
echo '</pre>';
}

/**
 * 接收语音识别结果
 */
if ($str_action == 'receive_voice') {
//接收语音识别结果
/**/
	$xml_file = '<xml>
<ToUserName><![CDATA[toUser]]></ToUserName>
<FromUserName><![CDATA[fromUser]]></FromUserName>
<CreateTime>1357290913</CreateTime>
<MsgType><![CDATA[voice]]></MsgType>
<MediaId><![CDATA[media_id]]></MediaId>
<Format><![CDATA[Format]]></Format>
<Recognition><![CDATA[腾讯微信团队]]></Recognition>
<MsgId>1234567890123456</MsgId>
</xml>';

	$arr_data = array();
	$arr_data['action'] = 'receive_voice';
	$arr_data['msg'] = $obj_weixin->iconv_charset($xml_file,"GBK",'UTF-8');
	$arr_result = $obj_weixin->weixin($arr_data);
echo '<pre>';
print_r($arr_result);
echo '</pre>';
}

/**
 * 发送被动响应消息
 */
if ($str_action == 'send_msg') {
	$arr_data = array();
	$arr_data['action'] = 'send_msg';
	$arr_result = $obj_weixin->weixin($arr_data);
echo '<pre>';
print_r($arr_result);
echo '</pre>';
}

/**
 * 发送客服消息
 */
if ($str_action == 'send_sevice_msg') {
	//获取基础access_token
//	$arr_data = array();
//	$arr_data['action'] = 'access_token';
//	$arr_result = $obj_weixin->weixin($arr_data);
//	echo '<pre>';
//	print_r($arr_result);
//	echo '</pre>';
//	exit;
//
/**/
	$arr_result = array();
	$arr_result['access_token'] = 'BiFMOBlMWTEnrsiHm-E_ckFUZaGL5l-344UnRE6XEWQzOlKfm7MK_iVUoZV7LjbG2QA2sCIyXMW12298SgUf3w';
	$arr_result['expires_in'] = '7200';
	$arr_result['openid'] = 'oIQ5Vtxp0W4Cb-MIJP2lho-cH82c';

	if ($arr_result) {
		//发送消息文字消息 HELLO WROLD
		$arr_data = array();
		$arr_data['action'] = 'send_sevice_msg';
		$arr_data['access_token'] = $arr_result['access_token'];
		$arr_data['touser'] = $arr_result['openid'];
		//$arr_data['msgtype'] = 'text'; //消息类型 TEXT 文本类型
		//$arr_data['content'] = 'Hello,WelComeToO2O-txd168';
		
		//发送图文消息
		$arr_data['msgtype'] = 'news'; //消息类型，text news
		//内容说明：
		//第一个数组是大图文消息
		//后续的数组内容是小图文消息
		$arr_articles = array();
		$arr_articles[0]['title'] = '测试图文消息';
		$arr_articles[0]['description'] = '测试图文消息 描述';
		$arr_articles[0]['url'] = 'http://money.163.com/14/0806/02/A2U9MJNG00253B0H.html?from=news';
		$arr_articles[0]['picurl'] = 'http://img3.cache.netease.com/photo/0005/2014-08-05/900x600_A2SOEHCF4FFC0005.jpg';
		
		$arr_articles[1]['title'] = '官兵救灾';
		$arr_articles[1]['description'] = '官兵救灾 官兵救灾 描述';
		$arr_articles[1]['url'] = 'http://news.163.com/14/0806/03/A2UEBBBM000146BE.html';
		$arr_articles[1]['picurl'] = 'http://img5.cache.netease.com/photo/0005/2014-08-05/900x600_A2SOE5N04FFC0005.jpg';
		
		$arr_articles[2]['title'] = '汤唯';
		$arr_articles[2]['description'] = '汤唯拄拐杖抵港宣传新戏 获十余人护送 2 描述2';
		$arr_articles[2]['url'] = 'http://news.163.com/14/0806/02/A2UBORB000014AED.html';
		$arr_articles[2]['picurl'] = 'http://img4.cache.netease.com/photo/0003/2014-08-06/900x600_A2U9541P00AJ0003.jpg';
	
		$arr_data['articles'] = $arr_articles; 	

		$arr_result_b = $obj_weixin->weixin($arr_data);
		echo '--send_sevice_msg--'.'<br/>';
		echo '<pre>';
		print_r($arr_result_b);
		echo '</pre>';	
		exit;
	}
}

/**
 * 高级群发消息
 */
if ($str_action == 'send_qunfa_msg') {
	$arr_data = array();
	$arr_data['action'] = 'send_qunfa_msg';
	$arr_result = $obj_weixin->weixin($arr_data);
echo '<pre>';
print_r($arr_result);
echo '</pre>';
}

/**
 * 分组管理接口
 */

/**
 * 获取用户基本信息
 */
 
/**
 * 获取关注者列表
 */ 

/**
 * 获取用户地理位置
 */

/**
 * 网页授权获取用户基本信息
 */
if ($str_action == 'oauth') {
	$arr_data = array();
	$arr_data['action'] = 'oauth';
	$arr_result = $obj_weixin->weixin($arr_data);
echo '<pre>';
print_r($arr_result);
echo '</pre>';
}


/**
 * 网页获取用户网络状态（JS接口）
 */


