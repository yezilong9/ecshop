<?php
/******************************************************************************
Filename       : weixin.class.php
Author         : SouthBear
Email          : SouthBear819@163.com
Date/time      : 2014-07-11 11:14:10 
Purpose        : 微信公众号开发接口类库
Description    : 
Modify         : 
******************************************************************************/

class weixin {
    function __construct() {       
    }
    
    /**
     * 微信公众号相关配置信息
     * @paras
     * return 
     */
	private function weixin_config () {
 		$arr_config = array();
 		$arr_config = array(
 			'token' => 'untxO2Otxd' //untxO2Otxdweixin untxO2Otxd
 			,'appid' => 'wxe7f55d599239f618'
 			,'secret' => '6b4f9818f9e895759af9d3ccaff43f91'
 			,'grant_type_token' => 'client_credential'
 			,'grant_type_auth' => 'authorization_code'
 			,'grant_type_refresh' => 'refresh_token'
 			,'lang' => 'zh_CN'

 			,'token_url' => 'https://api.weixin.qq.com/cgi-bin/token' //获取access_token
 			,'upload_midea_url' => 'http://file.api.weixin.qq.com/cgi-bin/media/upload' //上传多媒体文件
 			,'down_media_url' => 'http://file.api.weixin.qq.com/cgi-bin/media/get' //下载多媒体文件
 			,'send_service_msg_url' => 'https://api.weixin.qq.com/cgi-bin/message/custom/send' //发送客服消息
 			,'send_qunfa_image_url' => 'https://api.weixin.qq.com/cgi-bin/media/uploadnews' //上传图文消息素材
 			,'send_qunfa_team_url' => 'https://api.weixin.qq.com/cgi-bin/message/mass/sendall' //根据分组进行群发
 			,'send_qunfa_openId_url' => 'https://api.weixin.qq.com/cgi-bin/message/mass/send' //根据OpenID列表群发
 			,'send_qunfa_del_url' => 'https://api.weixin.qq.com//cgi-bin/message/mass/delete' //删除群发
 			
 			,'get_auth_url' => 'https://open.weixin.qq.com/connect/oauth2/authorize' //获取授权
 			,'get_auth_tocken_url' => 'https://api.weixin.qq.com/sns/oauth2/access_token' //获取授权后的token
 			,'auth_redirect_url' => 'http://www.91ka.com/if/weixin/auth2.php' //授权后页面重定向地址
 			,'refresh_access_tocken' => 'https://api.weixin.qq.com/sns/oauth2/refresh_token' //刷新access_token
 			,'get_user_info_url' => 'https://api.weixin.qq.com/sns/userinfo' //获取用户信息
 			,'check_access_tocken' => 'https://api.weixin.qq.com/sns/auth' //检查授权凭证（access_token）是否有效
 		);
 		return $arr_config;	
 	}
 	
 	/**
 	 * 微信类库入口
 	 * @paras
 	 * return 
 	 */
 	public function weixin ($paras) {
 		if (empty($paras)) {
 			return false;
 		}
 		$this->config = $this->weixin_config();
 		//开发者验证
 		if ($paras['action'] == 'check_dev') {
			$arr_data = array();
			$arr_data['token'] = $this->config['token'];
			$arr_data['timestamp'] = $paras['timestamp'];
			$arr_data['nonce'] = $paras['nonce'];
			$arr_data['signature'] = $paras['signature'];	
		
			$bol_result = $this->_check_signature($arr_data);
			if ($bol_result){
				return true;
			} else {
				return false;
			}				
			
 		}
 		//获取acccess_token
 		if ($paras['action'] == 'access_token') {
			$arr_data = array();
			$arr_data['grant_type'] = $this->config['grant_type_token'];
			$arr_data['appid'] = $this->config['appid'];
			$arr_data['secret'] = $this->config['secret'];
			
			$arr_result = $this->_get_access_token($arr_data);
			if ($arr_result){
				return $arr_result;
			} else {
				return false;
			}		 			
 		}
 		//上传多媒体文件
 		if ($paras['action'] == 'upload_midea') {
			/**
			 * type 类型  图片（image）、语音（voice）、视频（video）和缩略图（thumb）
			 * media 类型 form-data中媒体文件标识，有filename、filelength、content-type等信息
			 */
			$arr_data = array();
			$arr_data['access_token'] = $paras['access_token'];
			$arr_data['type'] = $paras['type'];
			$arr_data['media'] = $paras['media'];
			
			$arr_result = $this->_upload_media($arr_data);
			if ($arr_result){
				return $arr_result;
			} else {
				return false;
			}	

 		
 		}
 		//下载多媒体文件
 		if ($paras['action'] == 'down_midea') {
			$arr_data = array();
			$arr_data['access_token'] = $paras['access_token'];
			$arr_data['media_id'] = $paras['media_id'];
			
			$arr_result = $this->_down_media($arr_data);
			if ($arr_result){
				return $arr_result;
			} else {
				return false;
			}	 		
 		}
 		//接收消息
 		if ($paras['action'] == 'receive_msg') {
 			$arr_data = array();
 			$arr_data['msg'] = $paras['msg'];		
			$arr_result = $this->_parse_xml($arr_data);
			if ($arr_result){
				return $arr_result;
			} else {
				return false;
			}		
 		}
 		//接收事件
 		if ($paras['action'] == 'receive_event') {
 			$arr_data = array();
 			$arr_data['msg'] = $paras['msg'];		
			$arr_result = $this->_parse_xml($arr_data);
			if ($arr_result){
				return $arr_result;
			} else {
				return false;
			}	
 		} 
 		//接收语音
 		if ($paras['action'] == 'receive_voice') {
 			$arr_data = array();
 			$arr_data['msg'] = $paras['msg'];		
			$arr_result = $this->_parse_xml($arr_data);
			if ($arr_result){
				return $arr_result;
			} else {
				return false;
			}		
 		} 
		//发送被动响应消息
 		if ($paras['action'] == 'send_msg') {
 			$arr_data = array();
			$arr_data['ToUserName'] = $paras['ToUserName'];	//接收方帐号（收到的OpenID）
			$arr_data['FromUserName'] = $paras['FromUserName']; //开发者微信号
			$arr_data['CreateTime'] = $paras['CreateTime']; //消息创建时间 （整型）
			$arr_data['MsgType'] = $paras['MsgType']; //消息类型 text voice video music	news	
			$arr_data['Content'] = $paras['Content']; //回复的消息内容（换行：在content中能够换行，微信客户端就支持换行显示）
			$arr_data['MediaId'] = $paras['MediaId']; //通过上传多媒体文件，得到的id
			$arr_data['Title'] = $paras['Title']; //视频、音乐、图文消息标题
			$arr_data['Description'] = $paras['Description']; //视频、音乐、图文消息描述
			$arr_data['MusicURL'] = $paras['MusicURL']; //音乐链接
			$arr_data['HQMusicUrl'] = $paras['HQMusicUrl']; //高质量音乐链接，WIFI环境优先使用该链接播放音乐
			$arr_data['ThumbMediaId'] = $paras['ThumbMediaId']; //缩略图的媒体id，通过上传多媒体文件，得到的id
			$arr_data['ArticleCount'] = $paras['ArticleCount']; //图文消息个数，限制为10条以内
			$arr_data['Articles'] = $paras['Articles']; //多条图文消息信息，默认第一个item为大图,注意，如果图文数超过10，则将会无响应
			$arr_data['PicUrl'] = $paras['PicUrl'];	//图片链接，支持JPG、PNG格式，较好的效果为大图360*200，小图200*200
			$arr_data['Url'] = $paras['Url'];	//点击图文消息跳转链接
//echo '<pre>';
//print_r($arr_data);
//echo '</pre>';
//exit;	
			$arr_result = $this->_send_msg($arr_data);
			if ($arr_result){
				return $arr_result;
			} else {
				return false;
			}		
 		} 		
		//发送消息
 		if ($paras['action'] == 'send_sevice_msg') {
 			$arr_data = array();
			$arr_data['access_token'] = $paras['access_token'];	//调用接口凭证
			$arr_data['touser'] = $paras['touser']; //普通用户openid
			$arr_data['msgtype'] = $paras['msgtype']; //消息类型 text voice video music	news	
			switch ($paras['msgtype']) {
				case 'text':
					$arr_data['content'] = $paras['content']; //回复的消息内容（换行：在content中能够换行，微信客户端就支持换行显示）
				break;
				case 'news':
					$arr_data['articles'] = $paras['articles'];
				break;
			}
//echo '<pre>';
//print_r($arr_data);
//echo '</pre>';	
//exit;
			$arr_result = $this->_send_sevice_msg($arr_data);
			if ($arr_result){
				return $arr_result;
			} else {
				return false;
			}		
 		}
 		
 		
 		
 		
 		//获取用户授权
 		if ($paras['action'] == 'oauth') {
			$arr_data = array();
			$arr_data['appid'] = $this->config['appid'];
			$arr_data['redirect_uri'] = $this->config['auth_redirect_url'];
			$arr_data['response_type'] = 'code';
			//snsapi_base 不弹出授权页面，直接跳转，只能获取用户openid
			//snsapi_userinfo （弹出授权页面，可通过openid拿到昵称、性别、所在地。并且，即使在未关注的情况下，只要用户授权，也能获取其信息）
			$arr_data['scope'] = 'snsapi_base';
			$arr_data['state'] = 'test'; //可以填写a-zA-Z0-9的参数值
			
			$arr_result = $this->_get_auth($arr_data);
			if ($arr_result){
				return $arr_result;
			} else {
				return false;
			}
 		}
 		//获取用户授权后的access_token
 		if ($paras['action'] == 'oauth_access_token') {
			$arr_data = array();
			$arr_data['appid'] = $this->config['appid'];
			$arr_data['secret'] = $this->config['secret'];
			$arr_data['code'] = $paras['code'];
			$arr_data['grant_type'] = $this->config['grant_type_auth'];
			
			$arr_result = $this->_get_auth_access_token($arr_data);
			if ($arr_result){
				return $arr_result;
			} else {
				return false;
			}
 		}
 		//刷新授权后的access_token
 		if ($paras['action'] == 'refresh_access_token') {
			$arr_data = array();
			$arr_data['appid'] = $this->config['appid'];
			$arr_data['grant_type'] = $this->config['grant_type_refresh'];
			$arr_data['refresh_token'] = $paras['refresh_token'];
			
			$arr_result = $this->_get_refresh_auth_access_token($arr_data);
			if ($arr_result){
				return $arr_result;
			} else {
				return false;
			}
 		}
 		//获取用户信息scope为 snsapi_userinfo
 		if ($paras['action'] == 'get_user_info') {
 			$arr_data = array();
			$arr_data['access_token'] = $paras['access_token'];
			$arr_data['openid'] = $paras['openid'];
			$arr_data['lang'] = $this->config['lang'];
			
			$arr_result = $this->_get_user_info($arr_data);
			if ($arr_result){
				return $arr_result;
			} else {
				return false;
			}		
 		}
 		//检查授权凭证（access_token）是否有效
 		if ($paras['action'] == 'check_access_tocken') {
 			$arr_data = array();
			$arr_data['access_token'] = $paras['access_token'];
			$arr_data['openid'] = $paras['openid'];
			
			$arr_result = $this->_check_access_tocken($arr_data);
			if ($arr_result){
				return $arr_result;
			} else {
				return false;
			}	 		
 		} 		
 		
 	} 

	/**
	 * 验证签名
	 * @paras
	 * return bollean
	 */
	protected function _check_signature ($paras) {
		if (empty($paras)) {
			return false;
		}
		$arr_data = array();
		$arr_data['token'] = $paras['token'];
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
    protected function _response_msg() {
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
        } else {//返回空串
        	echo " ";
        	exit;
        }
    } 

	/**
	 * 获取access_token
	 * @paras
	 * return 
	 * 接口使用频率说明URL http://mp.weixin.qq.com/wiki/index.php?title=接口频率限制说明
	 */
	protected function _get_access_token ($paras) {
		if (empty($paras)) {
			return false;
		}
		$arr_data = array();
		$arr_data['grant_type'] = $paras['grant_type'];
		$arr_data['appid'] = $paras['appid'];;
		$arr_data['secret'] = $paras['secret'];

		//提交数据
		$str_qstring = $this->_encode_data($arr_data);
		$str_url     = $this->config['token_url'];
		//echo $str_url.'?'.$str_qstring.'<br/>';
		//exit;
		$str_method  = 'get';
		$str_rtn_msg = $this->curl_request($str_method,$str_url,$str_qstring);
		//$str_rtn_msg = $this->iconv_charset($str_rtn_msg,"GBK",'UTF-8');
/*
		$str_rtn_msg = '
{"access_token":"AredN4gAetQRm5TByGi73yYvlTBEu6GuAVJnwPNSiJFbI4Hn3-r4-Gr9Dz6e4unSo8bllKU3176bnGQ_CwA9tw","expires_in":7200}';	
*/		
		/**
		 * 返回数据说明
		 * access_token  获取到的凭证
		 * expires_in 凭证有效时间，单位：秒
		 */	
		$obj_result = json_decode($str_rtn_msg);
		$arr_result = $this->object_to_array($obj_result);

		if (is_array($arr_result) && count($arr_result) > 0) {
			if ($arr_result['errcode'] && $arr_result['errcode'] != '0') {
				$arr_result['err_msg'] = $this->_global_result_code($arr_result['errcode']);
			} 
			return $arr_result;
		} else {
			return false;
		}
	
	} 

	/**
	 * 上传媒体文件
	 * @paras
	 * return 
	 */
	protected function _upload_media ($paras) {
		if (empty($paras)) {
			return false;
		}
		$arr_data = array();
		$arr_data['access_token'] = $paras['access_token'];
		$arr_data['type'] = $paras['type'];
		$arr_data['media'] = $paras['media'];
		
		//提交数据
		$str_qstring = $this->_encode_data($arr_data);
		$str_url     = $this->config['upload_midea_url'];

		//echo $str_url.'?'.$str_qstring.'<br/>';
		//exit;
		$str_method  = 'POST';
		//$str_rtn_msg = $this->curl_request($str_method,$str_url,$str_qstring);

		/**
		 * 返回数据说明
		 * type  媒体文件类型，分别有图片（image）、语音（voice）、视频（video）和缩略图（thumb，主要用于视频与音乐格式的缩略图）
		 * media_id 媒体文件上传后，获取时的唯一标识
		 * created_at 媒体文件上传时间戳
		 */	
		
		/**	注意事项			
		 * 	上传的多媒体文件有格式和大小限制，如下：			
		 *	图片（image）: 1M，支持JPG格式
		 *	语音（voice）：2M，播放长度不超过60s，支持AMR\MP3格式
		 *	视频（video）：10MB，支持MP4格式
		 *	缩略图（thumb）：64KB，支持JPG格式
		 * 	媒体文件在后台保存时间为3天，即3天后media_id失效。
		 */
		$obj_result = json_decode($str_rtn_msg);
		$arr_result = $this->object_to_array($obj_result);
//echo '<pre>';
//print_r($arr_result);
//echo '</pre>';
		
	}

	/**
	 * 下载多媒体文件
	 * 可调用本接口来获取多媒体文件。请注意，视频文件不支持下载
	 * @paras
	 * return 
	 */
	protected function _down_media ($paras) {
		if (empty($paras)) {
			return false;
		}
		$arr_data = array();
		$arr_data['access_token'] = $paras['access_token'];
		$arr_data['media_id'] = $paras['media_id'];
		
		//提交数据
		$str_qstring = $this->_encode_data($arr_data);
		$str_url     = $this->config['down_midea_url'];

		//echo $str_url.'?'.$str_qstring.'<br/>';
		//exit;
		$str_method  = 'POST';
		//$str_rtn_msg = $this->curl_request($str_method,$str_url,$str_qstring);
echo '<pre>';
print_r($str_rtn_msg);
echo '</pre>';
		$obj_result = json_decode($str_rtn_msg);
		$arr_result = $this->object_to_array($obj_result);
//echo '<pre>';
//print_r($arr_result);
//echo '</pre>';
		
	} 

	/**
	 * 对接收各种(文本消息/图片消息/语音消息/视频消息/地理位置消息/链接消息)消息的处理
	 * @paras
	 * return 
	 * NOTICE simplexml_load_string 只能正确解析UTF-8的中文字符
	 */
	protected function _parse_xml ($paras) {
		if (empty($paras)) {
			return false;
		}
		$xml_file = $paras['msg'];
		$obj_xml = simplexml_load_string($xml_file);
		$arr_xml = $this->object_to_array($obj_xml);
//echo '<pre>';
//print_r($arr_xml);
//echo '</pre>';
		$arr_xml = $this->iconv_charset($arr_xml,"UTF-8",'GBK');
		if (is_array($arr_xml)) {
			return $arr_xml;
		} else {
			return false;
		}				
	}

	/**
	 * 回复图文多媒体消息
	 * @paras
	 * return 
	 */
	protected function _send_msg ($paras) {
		if (empty($paras)) {
			return false;
		}
		$arr_data = array();
		$arr_data['ToUserName'] = $paras['ToUserName'];	//接收方帐号（收到的OpenID）
		$arr_data['FromUserName'] = $paras['FromUserName']; //开发者微信号
		$arr_data['CreateTime'] = $paras['CreateTime']; //消息创建时间 （整型）
		$arr_data['MsgType'] = $paras['MsgType']; //消息类型 text voice video music	news	
		$arr_data['Content'] = $paras['Content']; //回复的消息内容（换行：在content中能够换行，微信客户端就支持换行显示）
		$arr_data['MediaId'] = $paras['MediaId']; //通过上传多媒体文件，得到的id
		$arr_data['Title'] = $paras['Title']; //视频、音乐、图文消息标题
		$arr_data['Description'] = $paras['Description']; //视频、音乐、图文消息描述
		$arr_data['MusicURL'] = $paras['MusicURL']; //音乐链接
		$arr_data['HQMusicUrl'] = $paras['HQMusicUrl']; //高质量音乐链接，WIFI环境优先使用该链接播放音乐
		$arr_data['ThumbMediaId'] = $paras['ThumbMediaId']; //缩略图的媒体id，通过上传多媒体文件，得到的id
		$arr_data['ArticleCount'] = $paras['ArticleCount']; //图文消息个数，限制为10条以内
		$arr_data['Articles'] = $paras['Articles']; //多条图文消息信息，默认第一个item为大图,注意，如果图文数超过10，则将会无响应
		$arr_data['PicUrl'] = $paras['PicUrl'];	//图片链接，支持JPG、PNG格式，较好的效果为大图360*200，小图200*200
		$arr_data['Url'] = $paras['Url'];	//点击图文消息跳转链接
	
		$xml_file = '<xml>';
		$xml_file .= '<ToUserName>'.$arr_data['ToUserName'].'</ToUserName>';
		$xml_file .= '<FromUserName>'.$arr_data['FromUserName'].'</FromUserName>';
		$xml_file .= '<CreateTime>'.$arr_data['CreateTime'].'</CreateTime>';
		$xml_file .= '<MsgType>'.$arr_data['MsgType'].'</MsgType>';
		
		if ($arr_data['MsgType'] == 'text') {//文本文件
			$xml_file .= '<Content>'.$arr_data['Content'].'</Content>';
		}
		if (in_array($arr_data['MsgType'],array('image','voice'))) {//image 图片消息 voice 语音消息
			$xml_file .= '<MediaId>'.$arr_data['MediaId'].'</MediaId>';
		}		
		if ($arr_data['MsgType'] == 'video') {//视频消息
			$xml_file .= '<MediaId>'.$arr_data['MediaId'].'</MediaId>';
			$xml_file .= '<Title>'.$arr_data['Title'].'</Title>';
			$xml_file .= '<Description>'.$arr_data['Description'].'</Description>';
		}		
		if ($arr_data['MsgType'] == 'music') {//音乐消息
			$xml_file .= '<Title>'.$arr_data['Title'].'</Title>';
			$xml_file .= '<Description>'.$arr_data['Description'].'</Description>';
			$xml_file .= '<MusicURL>'.$arr_data['MusicURL'].'</MusicURL>';
			$xml_file .= '<HQMusicUrl>'.$arr_data['HQMusicUrl'].'</HQMusicUrl>';
			$xml_file .= '<ThumbMediaId>'.$arr_data['ThumbMediaId'].'</ThumbMediaId>';
		}			
		if ($arr_data['MsgType'] == 'news') {//图文消息
			$xml_file .= '<ArticleCount>'.$arr_data['ArticleCount'].'</ArticleCount>';
			$xml_file .= '<Articles>'.$arr_data['Articles'].'</Articles>';
			$xml_file .= '<Title>'.$arr_data['Title'].'</Title>';
			$xml_file .= '<Description>'.$arr_data['Description'].'</Description>';
			$xml_file .= '<PicUrl>'.$arr_data['PicUrl'].'</PicUrl>';
			$xml_file .= '<Url>'.$arr_data['Url'].'</Url>';
		}
		$xml_file .= '</xml>';
		
		if ($xml_file) {
			return $xml_file;
		} else {
			return false;
		}
	}

	/**
	 * 发送客服消息
	 * @paras
	 * return 
	 */
	protected function _send_sevice_msg ($paras) {
//	echo '--msg data--'.'<br/>';
//	echo '<pre>';
//	print_r($paras);
//	echo '</pre>';
		if (empty($paras)) {
			return false;
		}
		$arr_data = array();
		$arr_data['touser'] = $paras['touser']; //普通用户openid
		$arr_data['msgtype'] = $paras['msgtype']; //消息类型，text
		//文字
		if ($paras['msgtype'] == 'text') {			
			$arr_data['text']['content'] = $paras['content']; //文本消息内容
		}
		//图片，语音
		if (in_array($paras['msgtype'],array('image','voice'))) {
			$arr_data[$paras['msgtype']]['media_id'] = $paras['media_id']; //发送的图片的媒体ID 语音的媒体ID
		}
		//视频
		if (in_array($paras['msgtype'],array('video'))) {
			$arr_data[$paras['msgtype']]['media_id'] = $paras['media_id']; //发送的视频的媒体ID
		    $arr_data[$paras['msgtype']]['thumb_media_id'] = $paras['thumb_media_id']; //缩略图的媒体ID
			$arr_data[$paras['msgtype']]['title'] = $paras['title']; //消息的标题
			$arr_data[$paras['msgtype']]['description'] = $paras['description']; //消息的描述
		}
		//音乐
		if (in_array($paras['msgtype'],array('music'))) {
			$arr_data[$paras['msgtype']]['title'] = $paras['title']; //消息的标题
			$arr_data[$paras['msgtype']]['description'] = $paras['description']; //消息的描述
			$arr_data[$paras['msgtype']]['musicurl'] = $paras['musicurl']; //音乐链接
			$arr_data[$paras['msgtype']]['hqmusicurl'] = $paras['hqmusicurl']; //高品质音乐链接，wifi环境优先使用该链接播放音乐
		    $arr_data[$paras['msgtype']]['thumb_media_id'] = $paras['thumb_media_id']; //缩略图的媒体ID
		}
		//图文消息
		if ($paras['msgtype'] == 'news') {		
			$arr_articles = array();
			$int_num = count($paras['articles']);
			for ($i = 0; $i < $int_num; $i++) {
				$arr_articles[$i]['title'] = $this->iconv_charset($paras['articles'][$i]['title'],"GBK",'UTF-8');
				$arr_articles[$i]['description'] = $this->iconv_charset($paras['articles'][$i]['description'],"GBK",'UTF-8');
				$arr_articles[$i]['url'] = $paras['articles'][$i]['url'];
				$arr_articles[$i]['picurl'] = $paras['articles'][$i]['picurl'];			
			}			
			$arr_data[$paras['msgtype']]['articles'] = $arr_articles; 
		}
//echo '<pre>';
//print_r($arr_data);
//echo '</pre>';

		//提交数据
		$str_qstring = $this->JSON($arr_data);
//echo '--json data--'.'<br/>';
//echo '<pre>';
//print_r($str_qstring);
//echo '</pre>';
//exit;
		$str_url = $this->config['send_service_msg_url'];
		$str_url .= '?'.'access_token='.$paras['access_token'];
		//echo $str_url.'<br/>'; //.'?'.$str_qstring

		$str_method  = 'POST';
		$str_rtn_msg = $this->curl_request($str_method,$str_url,$str_qstring);

		$obj_result = json_decode($str_rtn_msg);
		$arr_result = $this->object_to_array($obj_result);
	
		if (is_array($arr_result)) {
			if ($arr_result['errcode'] != '0') {
				$arr_result['err_msg'] = $this->_global_result_code($arr_result['errcode']);
			} 
//echo '<pre>';
//print_r($arr_result);
//echo '</pre>';
			return $arr_result;			
		} else {
			return false;
		}
	}

	/**
	 * 高级群发消息
	 * @paras
	 * return 
	 */
	protected function _send_qunfa_msg ($paras) {
		if (empty($paras)) {
			return false;
		}
		
		$arr_data = array();
		/**
		 * type 群发数据类型
		 * 1 上传图文消息素材
		 * 2 根据分组进行群发
		 * 3 根据OpenID列表群发
		 * 4 删除群发
		 * 5 事件推送群发结果
		 */
		if ($paras == 'images') {//1 图文消息

		}
		if ($paras == 'images') {//2 根据分组进行群发

		}		
		if ($paras == 'images') {//3 根据OpenID列表群发

		}		
		if ($paras == 'images') {//4 删除群发

		}
		if ($paras == 'images') {//5 事件推送群发结果

		}
				
		//提交数据
		$str_qstring = $this->_encode_data($arr_data);
		$str_url     = $this->config[''];

		//echo $str_url.'?'.$str_qstring.'<br/>';
		//exit;
		$str_method  = 'POST';
		$str_rtn_msg = $this->curl_request($str_method,$str_url,$str_qstring);

		
	}

	/**
	 * 群发图文信息
	 * @paras
	 * return 
	 */
	protected function _qunfa_image_msg ($paras) {
		if (empty($paras)) {
			return false;
		}
		
		$arr_data = array();
		$arr_data['Articles'] = $paras['Articles']; //图文消息，一个图文消息支持1到10条图文
		$arr_data['thumb_media_id'] = $paras['thumb_media_id']; //图文消息缩略图的media_id，可以在基础支持-上传多媒体文件接口中获得
		$arr_data['author'] = $paras['author']; //图文消息的作者 
		$arr_data['title'] = $paras['title']; //图文消息的标题
		$arr_data['content_source_url'] = $paras['content_source_url']; //在图文消息页面点击“阅读原文”后的页面
		$arr_data['content'] = $paras['content']; //图文消息页面的内容，支持HTML标签
		$arr_data['digest'] = $paras['digest']; //图文消息的描述
		$arr_data['show_cover_pic'] = $paras['show_cover_pic']; //是否显示封面，1为显示，0为不显示
		$arr_data['touser'] = $paras['touser']; 

		//提交数据
		$str_qstring = json_encode($this->_encode_data($arr_data));
		$str_url     = $this->config['send_qunfa_image_url'];

		//echo $str_url.'?'.$str_qstring.'<br/>';
		//exit;
		$str_method  = 'POST';
		$str_rtn_msg = $this->curl_request($str_method,$str_url,$str_qstring);
//echo '<pre>';
//print_r($res);
//echo '</pre>';	
		/**
		 * 返回的数据
		 * type	 媒体文件类型，分别有图片（image）、语音（voice）、视频（video）和缩略图（thumb），
		 *       次数为news，即图文消息
		 * media_id	 媒体文件/图文消息上传后获取的唯一标识
		 * created_at	 媒体文件上传时间
		 */
		$obj_result = json_decode($str_rtn_msg);
		$arr_result = $this->object_to_array($obj_result);	
			
	}

	/**
	 * 群发分组信息
	 * @paras
	 * return 
	 */
	protected function _qunfa_team_msg ($paras) {
		if (empty($paras)) {
			return false;
		}
		
		$arr_data = array();
		$arr_data['filter'] = $paras['filter']; //用于设定图文消息的接收者
		$arr_data['group_id'] = $paras['group_id']; //群发到的分组的group_id
		$arr_data['mpnews'] = $paras['mpnews']; //用于设定即将发送的图文消息 
		$arr_data['media_id'] = $paras['media_id']; //用于群发的消息的media_id
		$arr_data['msgtype'] = $paras['msgtype']; //群发的消息类型，图文消息为mpnews，文本消息为text，语音为voice，音乐为music，图片为image，视频为video
		$arr_data['title'] = $paras['title']; //消息的标题
		$arr_data['description'] = $paras['description']; //消息的描述
		$arr_data['thumb_media_id'] = $paras['thumb_media_id']; //视频缩略图的媒体ID

		//提交数据
		$str_qstring = json_encode($this->_encode_data($arr_data));
		$str_url     = $this->config['send_qunfa_team_url'];

		//echo $str_url.'?'.$str_qstring.'<br/>';
		//exit;
		$str_method  = 'POST';
		$str_rtn_msg = $this->curl_request($str_method,$str_url,$str_qstring);
//echo '<pre>';
//print_r($res);
//echo '</pre>';	
		/**
		 * 返回的数据
		 * type	 媒体文件类型，分别有图片（image）、语音（voice）、视频（video）和缩略图（thumb），
		 *       次数为news，即图文消息
		 * errcode	 错误码
		 * errmsg    错误信息
		 * msg_id	 消息ID
		 * 在返回成功时，意味着群发任务提交成功，并不意味着此时群发已经结束
		 */
		$obj_result = json_decode($str_rtn_msg);
		$arr_result = $this->object_to_array($obj_result);	
			
	}

	/**
	 * 群发分组信息
	 * @paras
	 * return 
	 */
	protected function _qunfa_openId_msg ($paras) {
		if (empty($paras)) {
			return false;
		}
		
		$arr_data = array();
		$arr_data['touser'] = $paras['touser']; //填写图文消息的接收者，一串OpenID列表，OpenID最少个，最多10000个
		$arr_data['mpnews'] = $paras['mpnews']; //用于设定即将发送的图文消息
		$arr_data['media_id'] = $paras['media_id']; //用于群发的图文消息的media_id 
		$arr_data['msgtype'] = $paras['msgtype']; //群发的消息类型，图文消息为mpnews，文本消息为text，语音为voice，音乐为music，图片为image，视频为video
		$arr_data['title'] = $paras['title']; //消息的标题
		$arr_data['description'] = $paras['description']; //消息的描述
		$arr_data['thumb_media_id'] = $paras['thumb_media_id']; //视频缩略图的媒体ID

		//提交数据
		$str_qstring = json_encode($this->_encode_data($arr_data));
		$str_url     = $this->config['send_qunfa_openId_url'];

		//echo $str_url.'?'.$str_qstring.'<br/>';
		//exit;
		$str_method  = 'POST';
		$str_rtn_msg = $this->curl_request($str_method,$str_url,$str_qstring);
//echo '<pre>';
//print_r($res);
//echo '</pre>';	
		/**
		 * 返回的数据
		 * type	 媒体文件类型，分别有图片（image）、语音（voice）、视频（video）和缩略图（thumb），
		 *       次数为news，即图文消息
		 * errcode	 错误码
		 * errmsg    错误信息
		 * msg_id	 消息ID
		 * 在返回成功时，意味着群发任务提交成功，并不意味着此时群发已经结束
		 */
		$obj_result = json_decode($str_rtn_msg);
		$arr_result = $this->object_to_array($obj_result);	
			
	}

	/**
	 * 删除群发信息
	 * @paras
	 * return 
	 */
	protected function _qunfa_del_msg ($paras) {
		if (empty($paras)) {
			return false;
		}
		
		$arr_data = array();
		$arr_data['msg_id'] = $paras['msg_id']; //发送出去的消息ID
		
		/**
		 * 请注意，只有已经发送成功的消息才能删除删除消息只是将消息的图文详情页失效，
		 * 已经收到的用户，还是能在其本地看到消息卡片。 另外，删除群发消息只能删除
		 * 图文消息和视频消息，其他类型的消息一经发送，无法删除。
		 */

		//提交数据
		$str_qstring = json_encode($this->_encode_data($arr_data));
		$str_url     = $this->config['send_qunfa_del_url'];

		//echo $str_url.'?'.$str_qstring.'<br/>';
		//exit;
		$str_method  = 'POST';
		$str_rtn_msg = $this->curl_request($str_method,$str_url,$str_qstring);
//echo '<pre>';
//print_r($res);
//echo '</pre>';	
		/**
		 * 返回的数据
		 * errcode	 错误码
		 * errmsg    错误信息
		 */
		$obj_result = json_decode($str_rtn_msg);
		$arr_result = $this->object_to_array($obj_result);	
			
	}

	/**
	 * 事件推送群发结果
	 * @paras
	 * return 
	 */
	protected function _qunfa_tuisong_result ($paras) {
		if (empty($paras)) {
			return false;
		}
		
		$arr_data = array();
		$arr_data['ToUserName'] = $paras['ToUserName']; //公众号的微信号
		$arr_data['FromUserName'] = $paras['FromUserName']; //公众号群发助手的微信号，为mphelper
		$arr_data['CreateTime'] = $paras['CreateTime']; //创建时间的时间戳
		$arr_data['MsgType'] = $paras['MsgType']; //消息类型，此处为event
		$arr_data['Event'] = $paras['Event']; //事件信息，此处为MASSSENDJOBFINISH
		$arr_data['MsgID'] = $paras['MsgID']; //群发的消息ID
		$arr_data['Status'] = $paras['Status']; //群发的结构，为“send success”或“send fail”或“err(num)”
		$arr_data['TotalCount'] = $paras['TotalCount']; //group_id下粉丝数；或者openid_list中的粉丝数
		$arr_data['FilterCount'] = $paras['FilterCount']; //过滤（过滤是指特定地区、性别的过滤、用户设置拒收的过滤，用户接收已超4条的过滤）后，准备发送的粉丝数，原则上，FilterCount = SentCount + ErrorCount
		$arr_data['SentCount'] = $paras['SentCount']; //发送成功的粉丝数
		$arr_data['ErrorCount'] = $paras['ErrorCount']; //发送失败的粉丝数

//echo '<pre>';
//print_r($res);
//echo '</pre>';	

		$obj_result = json_decode($str_rtn_msg);
		$arr_result = $this->object_to_array($obj_result);	
			
	}

	/**
	 * 获取网页授权
	 * @paras
	 * return 
	 */
	protected function _get_auth ($paras) {
		if (empty($paras)) {
			return false;
		}
		
		$arr_data = array();
		$arr_data['appid'] = $paras['appid'];
		$arr_data['redirect_uri'] = urlencode($paras['redirect_uri']);
		$arr_data['response_type'] = $paras['response_type'];
		$arr_data['scope'] = $paras['scope'];
		$arr_data['state'] = $paras['state'];
		
		//提交数据
		$str_qstring = $this->_encode_data($arr_data);
		$str_qstring .= '#wechat_redirect';
		$str_url     = $this->config['get_auth_url'];

		//echo $str_url.'?'.$str_qstring.'<br/>';
		//exit;
		$str_method  = 'POST';
		$str_rtn_msg = $this->curl_request($str_method,$str_url,$str_qstring);
//echo '<pre>';
//print_r($str_rtn_msg);
//echo '</pre>';		
//echo '------_get_auth--------'.'<br/>';
//exit;	
		if ($str_rtn_msg) {
			return $str_rtn_msg;
		} else {
			return false;
		}		
	}

	/**
	 * 获取授权后的access_token
	 * @paras
	 * return 
	 */
	protected function _get_auth_access_token ($paras) {
		if (empty($paras)) {
			return false;
		}
		$arr_data = array();
		$arr_data['appid'] = $paras['appid'];
		$arr_data['secret'] = $paras['secret'];
		$arr_data['code'] = $paras['code'];
		$arr_data['grant_type'] = $paras['grant_type'];

		//提交数据
		$str_qstring = $this->_encode_data($arr_data);
		$str_url     = $this->config['get_auth_tocken_url'];
		//echo '_get_auth_access_token'.'<br/>';
		//echo $str_url.'?'.$str_qstring.'<br/>';
		//exit;
		$str_method  = 'get';
		$str_rtn_msg = $this->curl_request($str_method,$str_url,$str_qstring);
		$str_rtn_msg = $this->iconv_charset($str_rtn_msg,"GBK",'UTF-8');
//echo '<pre>';
//print_r($str_rtn_msg);
//echo '</pre>';
/*
		$str_rtn_msg = '
{"access_token":"OezXcEiiBSKSxW0eoylIePVeTYBNndr3I0NlzF49udAiHLaGPkZ4CHwntPexYuvepTRJibh-c574g28ADiVEeAy31FMYnkGNASSeWHUCl5y3Q7OlZ47XBZJQ_FVbUmddsyu4Ekn7PLxRY3yJbFuU-A","expires_in":7200,"refresh_token":"OezXcEiiBSKSxW0eoylIePVeTYBNndr3I0NlzF49udAiHLaGPkZ4CHwntPexYuvehSiBmcbDc6kaIHU8pRTPZlXbLWjJN7LtkIPO-SWP8qNN0cMGbE0O4GknrylNUjzMnJjaeBGf1KBvMzwShGHk9Q","openid":"oIQ5Vtxp0W4Cb-MIJP2lho-cH82c","scope":"snsapi_base"}';

Array
(
    [access_token] => OezXcEiiBSKSxW0eoylIePVeTYBNndr3I0NlzF49udAiHLaGPkZ4CHwntPexYuvepTRJibh-c574g28ADiVEeAy31FMYnkGNASSeWHUCl5y3Q7OlZ47XBZJQ_FVbUmddsyu4Ekn7PLxRY3yJbFuU-A
    [expires_in] => 7200
    [refresh_token] => OezXcEiiBSKSxW0eoylIePVeTYBNndr3I0NlzF49udAiHLaGPkZ4CHwntPexYuvehSiBmcbDc6kaIHU8pRTPZlXbLWjJN7LtkIPO-SWP8qNN0cMGbE0O4GknrylNUjzMnJjaeBGf1KBvMzwShGHk9Q
    [openid] => oIQ5Vtxp0W4Cb-MIJP2lho-cH82c
    [scope] => snsapi_base
)	
*/
		/**
		 * 返回数据说明
		 * access_token  获取到的凭证
		 * expires_in 凭证有效时间，单位：秒
		 */	
		$obj_result = json_decode($str_rtn_msg);
		$arr_result = $this->object_to_array($obj_result);

		if (is_array($arr_result) && count($arr_result) > 0) {
			if ($arr_result['errcode'] && $arr_result['errcode'] != '0') {
				$arr_result['err_msg'] = $this->_global_result_code($arr_result['errcode']);
			} 
			return $arr_result;
		} else {
			return false;
		}	
	}

	/**
	 * 刷新获取授权后的access_token
	 * @paras
	 * return 
	 */
	protected function _get_refresh_auth_access_token ($paras) {
		if (empty($paras)) {
			return false;
		}
		$arr_data = array();
		$arr_data['appid'] = $paras['appid'];
		$arr_data['grant_type'] = $paras['grant_type'];
		$arr_data['refresh_token'] = $paras['refresh_token'];

		//提交数据
		$str_qstring = $this->_encode_data($arr_data);
		$str_url     = $this->config['refresh_access_tocken'];

		$str_method  = 'get';
		$str_rtn_msg = $this->curl_request($str_method,$str_url,$str_qstring);
		$str_rtn_msg = $this->iconv_charset($str_rtn_msg,"GBK",'UTF-8');

		/**
		 * 返回数据说明
		 * access_token  获取到的凭证
		 * expires_in 凭证有效时间，单位：秒
		 */	
		$obj_result = json_decode($str_rtn_msg);
		$arr_result = $this->object_to_array($obj_result);
	
		if (is_array($arr_result) && count($arr_result) > 0) {
			if ($arr_result['errcode'] && $arr_result['errcode'] != '0') {
				$arr_result['err_msg'] = $this->_global_result_code($arr_result['errcode']);
			} 
//echo '--refresh_access_tocken--'.'<br/>';
//echo '<pre>';
//print_r($arr_result);
//echo '</pre>';
//exit;
			return $arr_result;
		} else {
			return false;
		}	
	}

	/**
	 * 检查授权凭证（access_token）是否有效
	 * @paras
	 * return 
	 */
	protected function _check_access_tocken ($paras) {
		if (empty($paras)) {
			return false;
		}

		$arr_data = array();
		$arr_data['access_token'] = $paras['access_token'];
		$arr_data['openid'] = $paras['openid'];
			
		//提交数据
		$str_qstring = $this->_encode_data($arr_data);
		$str_url     = $this->config['check_access_tocken'];

		$str_method  = 'get';
		$str_rtn_msg = $this->curl_request($str_method,$str_url,$str_qstring);
		$str_rtn_msg = $this->iconv_charset($str_rtn_msg,"GBK",'UTF-8');

		/**
		 * 返回数据说明
		 * access_token  获取到的凭证
		 * expires_in 凭证有效时间，单位：秒
		 */	
		$obj_result = json_decode($str_rtn_msg);
		$arr_result = $this->object_to_array($obj_result);

		if (is_array($arr_result) && count($arr_result) > 0) {
			if ($arr_result['errcode'] && $arr_result['errcode'] != '0') {
				$arr_result['err_msg'] = $this->_global_result_code($arr_result['errcode']);
			} 
//echo '<pre>';
//print_r($arr_result);
//echo '</pre>';	
//exit;
			return $arr_result;
		} else {
			return false;
		}	
	}



	/**
	 * 微信接口全局错误码
	 * @paras
	 * return 
	 */
	protected function _global_result_code ($paras) {
		if (empty($paras)) {
			return false;
		}
		$str_rtn_msg = '';
		switch ($paras) {
			case '-1':
				$str_rtn_msg = '系统繁忙';
			break;
			case '0':
				$str_rtn_msg = '请求成功';
			break;			
			case '40001':
				$str_rtn_msg = '获取access_token时AppSecret错误，或者access_token无效';
			break;
			case '40002':
				$str_rtn_msg = '不合法的凭证类型';
			break;	
			case '40003':
				$str_rtn_msg = '不合法的OpenID';
			break;
			case '40004':
				$str_rtn_msg = '不合法的媒体文件类型';
			break;				
			case '40005':
				$str_rtn_msg = '不合法的文件类型';
			break;
			case '40006':
				$str_rtn_msg = '不合法的文件大小';
			break;			
			case '40007':
				$str_rtn_msg = '不合法的媒体文件id';
			break;
			case '40008':
				$str_rtn_msg = '不合法的消息类型';
			break;	
			case '40009':
				$str_rtn_msg = '不合法的图片文件大小';
			break;
			case '40010':
				$str_rtn_msg = '不合法的语音文件大小';
			break;
			case '40011':
				$str_rtn_msg = '不合法的视频文件大小';
			break;
			case '40012':
				$str_rtn_msg = '不合法的缩略图文件大小';
			break;			
			case '40013':
				$str_rtn_msg = '不合法的APPID';
			break;
			case '40014':
				$str_rtn_msg = '不合法的access_token';
			break;	
			case '40015':
				$str_rtn_msg = '不合法的菜单类型';
			break;
			case '40016':
				$str_rtn_msg = '不合法的按钮个数';
			break;				
			case '40017':
				$str_rtn_msg = '不合法的按钮个数';
			break;
			case '40018':
				$str_rtn_msg = '不合法的按钮名字长度';
			break;			
			case '40019':
				$str_rtn_msg = '不合法的按钮KEY长度';
			break;
			case '40020':
				$str_rtn_msg = '不合法的按钮URL长度';
			break;	
			case '40021':
				$str_rtn_msg = '不合法的菜单版本号';
			break;
			case '40022':
				$str_rtn_msg = '不合法的子菜单级数';
			break;
			case '40023':
				$str_rtn_msg = '不合法的子菜单按钮个数';
			break;
			case '40024':
				$str_rtn_msg = '不合法的子菜单按钮类型';
			break;			
			case '40025':
				$str_rtn_msg = '不合法的子菜单按钮名字长度';
			break;
			case '40026':
				$str_rtn_msg = '不合法的子菜单按钮KEY长度';
			break;	
			case '40027':
				$str_rtn_msg = '不合法的子菜单按钮URL长度';
			break;
			case '40028':
				$str_rtn_msg = '不合法的自定义菜单使用用户';
			break;				
			case '40029':
				$str_rtn_msg = '不合法的oauth_code';
			break;
			case '40030':
				$str_rtn_msg = '不合法的refresh_token';
			break;			
			case '40031':
				$str_rtn_msg = '不合法的openid列表';
			break;
			case '40032':
				$str_rtn_msg = '不合法的openid列表长度';
			break;	
			case '40033':
				$str_rtn_msg = '不合法的请求字符，不能包含\uxxxx格式的字符';
			break;
			case '40035':
				$str_rtn_msg = '不合法的参数';
			break;
			case '40038':
				$str_rtn_msg = '不合法的请求格式';
			break;
			case '40039':
				$str_rtn_msg = '不合法的URL长度';
			break;			
			case '40050':
				$str_rtn_msg = '不合法的分组id';
			break;
			case '40051':
				$str_rtn_msg = '分组名字不合法';
			break;	
			case '41001':
				$str_rtn_msg = '缺少access_token参数';
			break;
			case '41002':
				$str_rtn_msg = '缺少appid参数';
			break;				
			case '41003':
				$str_rtn_msg = '缺少refresh_token参数';
			break;
			case '41004':
				$str_rtn_msg = '缺少secret参数';
			break;			
			case '41005':
				$str_rtn_msg = '缺少多媒体文件数据';
			break;
			case '41006':
				$str_rtn_msg = '缺少media_id参数';
			break;	
			case '41007':
				$str_rtn_msg = '缺少子菜单数据';
			break;
			case '41008':
				$str_rtn_msg = '缺少oauth code';
			break;
			case '41009':
				$str_rtn_msg = '缺少openid';
			break;			
			case '42001':
				$str_rtn_msg = 'access_token超时';
			break;
			case '42002':
				$str_rtn_msg = 'refresh_token超时';
			break;	
			case '42003':
				$str_rtn_msg = 'oauth_code超时';
			break;
			case '43001':
				$str_rtn_msg = '需要GET请求';
			break;
			case '43002':
				$str_rtn_msg = '需要POST请求';
			break;			
			case '43003':
				$str_rtn_msg = '需要HTTPS请求';
			break;
			case '43004':
				$str_rtn_msg = '需要接收者关注';
			break;	
			case '43005':
				$str_rtn_msg = '需要好友关系';
			break;
			case '44001':
				$str_rtn_msg = '多媒体文件为空';
			break;				 
			case '44002':
				$str_rtn_msg = 'POST的数据包为空';
			break;			
			case '44003':
				$str_rtn_msg = '图文消息内容为空';
			break;
			case '44004':
				$str_rtn_msg = '文本消息内容为空';
			break;	
			case '45001':
				$str_rtn_msg = '多媒体文件大小超过限制';
			break;
			case '45002':
				$str_rtn_msg = '消息内容超过限制';
			break;				 
			case '45003':
				$str_rtn_msg = '标题字段超过限制';
			break;			
			case '45004':
				$str_rtn_msg = '描述字段超过限制';
			break;
			case '45005':
				$str_rtn_msg = '链接字段超过限制';
			break;	
			case '45006':
				$str_rtn_msg = '图片链接字段超过限制';
			break;
			case '45007':
				$str_rtn_msg = '语音播放时间超过限制';
			break;		
			case '45008':
				$str_rtn_msg = '图文消息超过限制';
			break;			
			case '45009':
				$str_rtn_msg = '接口调用超过限制';
			break;
			case '45010':
				$str_rtn_msg = '创建菜单个数超过限制';
			break;	
			case '45015':
				$str_rtn_msg = '回复时间超过限制';
			break;
			case '45016':
				$str_rtn_msg = '系统分组，不允许修改';
			break;
			case '45017':
				$str_rtn_msg = '分组名字过长';
			break;
			case '45018':
				$str_rtn_msg = '分组数量超过上限';
			break;	
			case '46001':
				$str_rtn_msg = '不存在媒体数据';
			break;
			case '46002':
				$str_rtn_msg = '不存在的菜单版本';
			break;
			case '46003':
				$str_rtn_msg = '不存在的菜单数据';
			break;
			case '46004':
				$str_rtn_msg = '不存在的用户';
			break;
			case '47001':
				$str_rtn_msg = '解析JSON/XML内容错误';
			break;
			case '48001':
				$str_rtn_msg = 'api功能未授权';
			break;			
			case '50001':
				$str_rtn_msg = '用户未授权该api';
			break;	
		}
		return $str_rtn_msg;
	} 

	/**
	 * 整理字串
	 */
	protected function _encode_data ($paras) {
		foreach($paras as $key => $val) {
			//$val = $this->iconv_charset($val,"UTF-8",'GBK');
			//if ($key == 'province') {
			//	$val = urlencode(base64_encode($val));
			//} 
			$content .= $key.'='.$val.'&';
		}	
		$string = $content;
		$string = substr($string,0,-1);
		return $string;
	}

	/** 
	 * $method       提交方法：post get
	 * $str_bgUrl    提交地址
	 * $str_qstring  请求数据
	 */
	public function curl_request($method,$str_bgUrl,$str_qstring){
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
	     if (curl_errno($ch) != 0){
	         $int_err_code = '9999';
	         $str_err_msg = '接口通知失败:网络错误.'.curl_error($ch);
	     }
	     curl_close($ch);
	     if($int_err_code) return false;
	     return $data;
	}

	/** 
	 * 自动转换字符集 支持数组转换
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
	            return mb_convert_encoding($fContents, $to, $from);
	        } elseif (function_exists('iconv')) {
	            return iconv($from, $to.'//IGNORE', $fContents);
	        } else {
	            return $fContents;
	        }
	    } elseif (is_array($fContents)) {
	        foreach ($fContents as $key => $val) {
	            $_key = $this->iconv_charset($key, $from, $to);
	            $fContents[$_key] = $this->iconv_charset($val, $from, $to);
	            if ($key != $_key)
	                unset($fContents[$key]);
	        }
	        return $fContents;
	    }  else {
	        return $fContents;
	    }
	} 	 

	/**
	 * 对象转换为数组
	 */	
	public function object_to_array($obj){ 
		if( count($obj) == 0 )  return trim((string)$obj);
	    $_arr = is_object($obj) ? get_object_vars($obj) : $obj; 
	    foreach ($_arr as $key => $val) 
	    { 
	        $val = (is_array($val) || is_object($val)) ? $this->object_to_array($val) : $val; 
	        $arr[$key] = $val; 
	    } 
	    return $arr; 
	}

	/**************************************************************
	 * 使用特定function对数组中所有元素做处理
	 * @param  string  &$array     要处理的字符串
	 * @param  string  $function   要执行的函数
	 * @return boolean $apply_to_keys_also     是否也应用到key上
	 * @access public
     *************************************************************/
    public function arrayRecursive(&$array, $function, $apply_to_keys_also = false) {
        static $recursive_counter = 0;
        if (++$recursive_counter > 1000) {
            die('possible deep recursion attack');
        }
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $this->arrayRecursive($array[$key], $function, $apply_to_keys_also);
            } else {
                $array[$key] = $function($value);
            }
     
            if ($apply_to_keys_also && is_string($key)) {
                $new_key = $function($key);
                if ($new_key != $key) {
                    $array[$new_key] = $array[$key];
                    unset($array[$key]);
                }
            }
        }
        $recursive_counter--;
    }
     
    /**************************************************************
     *
     *    将数组转换为JSON字符串（兼容中文）
     *    @param  array   $array      要转换的数组
     *    @return string      转换得到的json字符串
     *    @access public
     *
     *************************************************************/
    public function JSON ($array) {
        $this->arrayRecursive($array, 'urlencode', true);
        $json = json_encode($array);
        return urldecode($json);
    }


}