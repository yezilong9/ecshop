<?php
/******************************************************************************
Filename       : auth.php
Author         : SouthBear
Email          : SouthBear819@163.com
Date/time      : 2014-07-11 11:14:10 
Purpose        : 微信开发者API汇集
Description    : 用户授权页面
Modify         : 
******************************************************************************/
include('weixin.class.php');

/*
$arr_data = array();
$arr_data['touser'] = 'oIQ5Vtxp0W4Cb-MIJP2lho-cH82c'; //普通用户openid
$arr_data['msgtype'] = 'news'; //消息类型，text news
//$arr_data['text']['content'] = 'Hello,WelComeToO2O-txd168'; //文本消息内容
//内容说明：
//第一个数组是大图文消息
//后续的数组内容是小图文消息
$arr_articles = array();
$arr_articles[0]['title'] = iconv('GBK','UTF-8'.'//IGNORE', '测试图文消息');
$arr_articles[0]['description'] = iconv('GBK','UTF-8'.'//IGNORE', '测试图文消息 描述');
$arr_articles[0]['url'] = 'http://money.163.com/14/0806/02/A2U9MJNG00253B0H.html?from=news';
$arr_articles[0]['picurl'] = 'http://img3.cache.netease.com/photo/0005/2014-08-05/900x600_A2SOEHCF4FFC0005.jpg';

$arr_articles[1]['title'] = iconv('GBK','UTF-8'.'//IGNORE', '官兵救灾');
$arr_articles[1]['description'] =iconv('GBK','UTF-8'.'//IGNORE', '官兵救灾 官兵救灾 描述');
$arr_articles[1]['url'] = 'http://news.163.com/14/0806/03/A2UEBBBM000146BE.html';
$arr_articles[1]['picurl'] = 'http://img5.cache.netease.com/photo/0005/2014-08-05/900x600_A2SOE5N04FFC0005.jpg';

$arr_articles[2]['title'] = iconv('GBK','UTF-8'.'//IGNORE','汤唯');
$arr_articles[2]['description'] = iconv('GBK','UTF-8'.'//IGNORE','汤唯拄拐杖抵港宣传新戏 获十余人护送 2 描述2');
$arr_articles[2]['url'] = 'http://news.163.com/14/0806/02/A2UBORB000014AED.html';
$arr_articles[2]['picurl'] = 'http://img4.cache.netease.com/photo/0003/2014-08-06/900x600_A2U9541P00AJ0003.jpg';

$arr_data['news']['articles'] = $arr_articles; 


$str_qstring = json_encode($arr_data);
echo '<pre>';
print_r($str_qstring);
echo '</pre>';
exit;
*/

$str_action = 'oauth';
$obj_weixin = new weixin();

/**
 * 网页授权获取用户基本信息
 */
if ($str_action == 'oauth') {
	$arr_data = array();
	$arr_data['action'] = 'oauth';
	$arr_result = $obj_weixin->weixin($arr_data);
//echo '<pre>';
//print_r($arr_result);
//echo '</pre>';
//	if (is_array($arr_result)) {
//		header("Location:https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxe7f55d599239f618&redirect_uri=http%3A%2F%2Fwww.91ka.com%2Fif%2Fweixin%2Fauth2.php&response_type=code&scope=snsapi_base&state=test#wechat_redirect");
//	}
}