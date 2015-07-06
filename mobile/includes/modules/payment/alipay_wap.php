<?php

/**
 * ECSHOP 手机支付宝插件
 * $Author: phplife  qq:40499756 email:admin@topit.cn $
 * $Id: alipay.php 17063 2010-03-25 06:35:46Z liuhui $
 *http://club.alipay.com/thread.php?fid=747
 *https://ms.alipay.com/index.htm

 */

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

$payment_lang = ROOT_PATH . 'languages/' .$GLOBALS['_CFG']['lang']. '/payment/alipay_wap.php';

if (file_exists($payment_lang))
{
    global $_LANG;

    include_once($payment_lang);
}

/* 模块的基本信息 */
if (isset($set_modules) && $set_modules == TRUE)
{
    $i = isset($modules) ? count($modules) : 0;

    /* 代码 */
    $modules[$i]['code']    = basename(__FILE__, '.php');

    /* 描述对应的语言项 */
    $modules[$i]['desc']    = 'alipay_wap_desc';

    /* 是否支持货到付款 */
    $modules[$i]['is_cod']  = '0';

    /* 是否支持在线支付 */
    $modules[$i]['is_online']  = '1';

    /* 作者 */
    $modules[$i]['author']  = 'topit';

    /* 网址 */
    $modules[$i]['website'] = 'http://www.XXXX.com';

    /* 版本号 */
    $modules[$i]['version'] = '1.0.0';

    /* 配置信息 */
    $modules[$i]['config']  = array(
        array('name' => 'alipay_key',               'type' => 'text',   'value' => ''),
        array('name' => 'alipay_partner',           'type' => 'text',   'value' => ''),
        array('name' => 'seller_email',             'type' => 'text', 'value' => '0')
    );

    return;
}

/**
 * 类
 */
class alipay_wap
{

    /**
     * 构造函数
     *
     * @access  public
     * @param
     *
     * @return void
     */
    function alipay_wap()
    {

    }

    function __construct()
    {
        $this->alipay_wap();
    }

    /**
     * 生成支付代码
     * @param   array   $order      订单信息
     * @param   array   $payment    支付方式信息
     */
    function get_code($order, $payment)
    {
        $gateway = 'http://wappaygw.alipay.com/service/rest.htm?';

		if (!defined('EC_CHARSET'))
        {
            $charset = 'utf-8';
        }
        else
        {
            $charset = EC_CHARSET;
        }
		$req_id = date('Ymdhis');


		$req_data  = '<direct_trade_create_req>';
		$req_data  .= '<subject>' . $order['order_sn'] . '</subject>';
		$req_data  .= '<out_trade_no>' . $order['order_sn'] . $order['log_id'] . '</out_trade_no>';
		$req_data  .= '<total_fee>' . $order['order_amount'] . '</total_fee>';
		$req_data  .= '<seller_account_name>' . $payment['seller_email'] . '</seller_account_name>';
		$req_data  .= '<notify_url>' .basename(__FILE__, '.php') . '</notify_url>';
		$req_data  .= '<out_user>' . $order['consignee'] . '</out_user>';
		$req_data  .= '<merchant_url>' . $GLOBALS['ecs']->url() . '</merchant_url>';
		$req_data  .= '<call_back_url>' . basename(__FILE__, '.php'). '</call_back_url>';
		$req_data  .= '</direct_trade_create_req>';

        $parameter = array (
				'req_data' => $req_data,
			    'service' => 'alipay.wap.trade.create.direct',
				'sec_id' => 'MD5',
				'partner' => $payment['alipay_partner'],
				'req_id' => date('Ymdhms'),
				'format' =>'xml',
				'v' =>'2.0'
		);

		ksort($parameter);
        reset($parameter);
        $param ='';
		$sign ='';
		//var_dump($parameter);
		foreach ($parameter AS $key => $val)
        {
            if($key == "sign" || $key == "sign_type" || $val == "") continue;
			$param .= "$key=" .urlencode($val). "&";
            $sign  .= "$key=$val&";
        }
		$md5_sign  = md5(substr($sign, 0, -1). $payment['alipay_key']);
		$param = substr($param, 0, -1). '&sign=' . urlencode ( $md5_sign);
		//echo('<br/>'.urlencode($param) .'<br/>');
		$result = $this->post($gateway, $param);
		//echo("<br/>=========================<br/>");
		//var_dump($result);
		//echo("<br/>=========================<br/>");

		$result = urldecode ( $result ); // URL转码
		//var_dump($result);
		$arr = explode ( '&', $result ); // 根据 & 符号拆分
		//var_dump($arr);

		$temp = array (); // 临时存放拆分的数组
		$myarray = array (); // 待签名的数组
		                    // 循环构造key、value数组
		for($i = 0; $i < count ( $arr ); $i ++) {
			$temp = explode ( '=', $arr [$i], 2 );
			$myarray[$temp [0]] = $temp [1];
		}
		$ret_sign = $myarray ['sign'];
		ksort($myarray);
        reset($myarray);
		$new_param ='';
		$new_sign ='';
		foreach ($myarray AS $key => $val)
        {
            if($key == "sign" || $key == "sign_type" || $val == "") continue;
			$new_param .= "$key=" .urlencode($val). "&";
            $new_sign  .= "$key=$val&";
        }

		$sign = md5(substr($new_sign, 0, -1). $payment['alipay_key']);
		$token ='';
		//var_dump($ret_sign);
        //var_dump($sign);
		//die();
		if ($ret_sign == $sign) 		// 判断签名是否正确
		{
			$xml = simplexml_load_string($myarray['res_data']);
			//var_dump($xml);
			$token = $xml->request_token;
		} else {
			die('签名不正确');
		}
		if(empty($token))
		{
		   die('token为空');
		}


		$req_data  = '<auth_and_execute_req>';
		$req_data  .= '<request_token>' . $token . '</request_token>';
		$req_data  .= '</auth_and_execute_req>';

		$parameter = array (
			    'service' => 'alipay.wap.auth.authAndExecute',
			    'partner' => $payment['alipay_partner'],
				'sec_id' => 'MD5',
				'format' =>'xml',
				'v' =>'2.0',
				'req_data' => $req_data,
				"_input_charset"	=> $charset,
				'call_back_url' => return_url(basename(__FILE__, '.php')),

		);

		ksort($parameter);
        reset($parameter);
        $sign ='';
		$param='';
		foreach ($parameter AS $key => $val)
        {
            if($key == "sign" || $key == "sign_type" || $val == "") continue;
			$param .= "$key=" .urlencode($val). "&";
            $sign  .= "$key=$val&";
        }
		$md5_sign  = md5(substr($sign, 0, -1). $payment['alipay_key']);
		$url = $gateway . substr($param, 0, -1). '&sign=' . urlencode($md5_sign);
        $button = '<div style="text-align:center"><input type="button" onclick="window.open(\''.$url. '\')" value="' .$GLOBALS['_LANG']['pay_button']. '" class="fc_btn fc_btn_o"/></div>';

        return $button;
    }

    /**
     * 响应操作
     */
    function respond()
    {
        @ini_set('display_errors',        0);
		if (!empty($_POST))
        {
            foreach($_POST as $key => $data)
            {
                $_GET[$key] = $data;
            }
        }
		$payment  = get_payment($_GET['code']);


		/* 检查数字签名是否正确 */
        ksort($_GET);
        reset($_GET);

        $sign = '';
        foreach ($_GET AS $key=>$val)
        {
            if ($key != 'sign' && $key != 'sign_type' && $key != 'code')
            {
                $sign .= "$key=$val&";
            }
        }

		$sign = substr($sign, 0, -1) . $payment['alipay_key'];

		if (md5($sign) != $_GET['sign'])
        {
            return false;
        }

        $seller_email = rawurldecode($_GET['seller_email']);
        $order_sn = str_replace($_GET['subject'], '', $_GET['out_trade_no']);
        $order_sn = trim($order_sn);

        /* 检查支付的金额是否相符 */
        if (!check_money($order_sn, $_GET['total_fee']))
        {
            return false;
        }

        if ($_GET['result'] == 'success')
        {
			$order_sn = strlen($order_sn)>13?substr($order_sn,13):$order_sn; //by Leah
            /* 改变订单状态 */
            order_paid($order_sn , 2);

            return true;
        }
        else
        {
            return false;
        }
    }



	/**日志消息,把支付宝返回的参数记录下来
    * 请注意服务器是否开通fopen配置
    */
	function  log_result($word) {
		$fp = fopen("log.txt","a");
		flock($fp, LOCK_EX) ;
		fwrite($fp,"执行日期：".strftime("%Y%m%d%H%M%S",time())."\n".$word."\n");
		flock($fp, LOCK_UN);
		fclose($fp);
	}

	/**
	 * PHP Crul库 模拟Post提交至支付宝网关
	 * 如果使用Crul 你需要改一改你的php.ini文件的设置，找到php_curl.dll去掉前面的";"就行了
	 * 返回 $data
	 */
	function post($url, $param)
	{
		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $url ); // 配置网关地址
		curl_setopt ($ch, CURLOPT_HEADER, 0 ); // 过滤HTTP头
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ($ch, CURLOPT_POST, 1 ); // 设置post提交
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $param); // post传输数据
		$data = curl_exec ( $ch );
		curl_close ( $ch );
		return $data;
	}
}

?>