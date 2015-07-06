<?php
/*
* 天下支付
* time ：2014-10-15
* aothur:hg
*/

class txd{

	private $db;
	
	private $ecs;
	
	private $pay_config;//配置
	
	private $mode = '0';//测试和正式
	
	private $order;//订单

	private $notify_url = 'http://www.txd168.com/mobile/txd_inform.php';//异步地址
	private $wap_from_url = 'http://kaka.txpay.cn/mobile_wap/index';//wap同步跳跳转到天下支付
	private $wap_notify_url = 'http://www.localec.com/mobile/txd_wap.php';//wap接受天下支付地址(异步和同步接受数据)
	
	private $get_verify_key = 'Iq2ddf7dLmrUFH7WESK4NAHbk';//同步key
	
	private $tx_key = 'e37af15f6cd991ed2c';//主动查询KEY
	
	
	public function __construct()
	{
		$this->db  = $GLOBALS['db'];
		$this->ecs = $GLOBALS['ecs'];
	}
	//获取配置与订单信息
	public function config($pay_id,$order)
	{
		$this->pay_config = $this->db->getOne("SELECT pay_config FROM ".$this->ecs->table('payment')." WHERE pay_id = $pay_id");
		$this->pay_config = unserialize($this->pay_config);
		$this->order = $order;
	}
	//获取token_id
	public function get_token()
	{
		$data = array(
			'mode'     => $this->mode,                            //判断环境，测试还是正式环境
			'app_id'   => $this->pay_config['app_id'],             //应用id，商户去天下支付申请的应用获得的
			'mch_id'   => $this->pay_config['cfg_id'],             //商户id，合同签约后，向天下支付商务人员索取
			'order_id' => $this->order['order_sn'],                //订单号
			'price'    => $this->order['order_amount']*100,                          //订单单价(单位：元)
			'quantity' => '1',                               //购买数量(单位：个)
			'total_fee' => $this->order['order_amount']*100,                  //订单总价=订单单价*购买数量(单位：元)
			'subject'  => 'null',                      //购买商品名称,如果有中文请使用urlencode后的值
			'remark'   => 'null',                        //商品描述
			'reserve'  => 'null',                       //预留字段，供商户使用
			'notify_url'  => $this->notify_url                       //预留字段，供商户使用
		);
		
		/*加密*/
		$encrypt_data = 'app_id='.$data['app_id'].'&mch_id='.$data['mch_id'].'&mch_order_no='.$data['order_id'].'&price='.$data['price'].'&quantity='.$data['quantity'].'&total_fee='.$data['total_fee'];
        $encrypt_data .= '&subject='.$data['subject'].'&remark='.$data['remark'].'&reserve='.$data['reserve'].'&notify_url='.$data['notify_url'].'&app_key='.$this->pay_config['app_key'].'&mch_key='.$this->pay_config['cfg_key'];
        $sign = md5($encrypt_data);
        $data['sign'] = $sign;

		//请求日志
		$arr = array(
			'txd_data'   => $encrypt_data,
			'txd_return' => '',
			'txd_or'     => '0',
			'order_sn'   => $this->order['order_sn'],
		);
		$this->get_token_log($arr,'1');
		
		/*curl请求*/
		$message = $this->curl($this->pay_config['cfg_gate'],$data);
		$retuen_message = json_decode($message,true);
		//返回日志
		$arr['txd_return'] = http_build_query($retuen_message);
		$this->get_token_log($arr,'2',"order_sn = '".$this->order['order_sn']."'");
		//处理返回值
		if($retuen_message['code'] == '1')
		{
			$this->db->autoExecute($this->ecs->table('order_info'), array('txd_pay_token_id'=>$retuen_message['token_id']), 'UPDATE','order_id = '.$this->order['order_id']);
		}else{
			show_message('获取支付信息失败');
		}
	}
	
	/*
	* $data 插入或者更新数据
	* $state 1是插入 2是更新
	*/
	private function get_token_log($data,$state,$where='')
	{
		if($state == '1')
		{
			$this->db->autoExecute($this->ecs->table('txd_pay_log'), $data, 'INSERT');
		}elseif($state == '2'){
			$this->db->autoExecute($this->ecs->table('txd_pay_log'), $data, 'UPDATE',$where);
		}
	}
	
	private function curl($url,$data)
	{
		$obj_ch = curl_init();
		curl_setopt($obj_ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($obj_ch, CURLOPT_URL, $url);
		curl_setopt($obj_ch, CURLOPT_POST, 1);
		curl_setopt($obj_ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($obj_ch, CURLOPT_RETURNTRANSFER, 1);
		$str_ret = trim(curl_exec($obj_ch));
		curl_close($obj_ch);
		return $str_ret;
	}
	
	/*
    * 主动查询订单信息 
	* 暂不启用，如果使用，请在M1处修改
	*/
	public function get_order_result()
	{
		$data = array(	
			'mode' => $this->mode,
			'order_id'    => $this->order['order_sn'],
			'app_id'      => $this->pay_config['app_id'],
			'token_id'    => $this->order['token_id'],
		);
		
		$data['mch_id'] = $this->pay_config['cfg_id'];
		//加密
		$mch_sign = "app_id=".$data['app_id']."&mch_id=".$data['mch_id']."&mch_order_no=".$data['order_id']."&app_key=".$this->pay_config['app_key']."&mch_key=".$this->pay_config['cfg_key'];
		$data['mch_sign'] = MD5($mch_sign);
		
		$encrypt_data = '';
		foreach($data as $key => $val)
		{
			$encrypt_data .= "&".urlencode($key)."=".urlencode($val);
		}
		$encrypt_data .= '||tx_key='.$this->tx_key;
		$sign = md5($encrypt_data);
		$data['sign'] = $sign;
		$data['do'] = 'get_order_result_v2';
	
		$http_data = http_build_query($data);
		
		//请求日志
		$arr = array(
			'txd_data'   => $http_data,
			'txd_return' => '--',
			'txd_or'     => '1',
			'order_sn'   => $this->order['order_sn'],
		);
		
		//请求结果
		$message = $this->curl(str_replace('token','order',$this->pay_config['cfg_gate']).'?'.$http_data,$http_data);
		//日志
		$arr['txd_return'] = http_build_query(json_decode($message,true));
		$this->get_token_log($arr,'1');
		return json_decode($message,true);
	}
	
	/* 接收异步通知 */
	public function verify($notify_data,$sign)
	{
		//接收日志
		$arr = array(
			'txd_data'   => '',
			'txd_return' => $notify_data.$sign,
			'txd_or'     => '2',
			'order_sn'   => '0',
		);
		
		
		$this->get_token_log($arr,'1');
		if(!$notify_data || !$sign) return false;
		//配置
		$pay_config = $this->db->getOne("SELECT pay_config FROM ".$this->ecs->table('payment')." WHERE pay_code = 'TXD'");
		$pay_config = unserialize($pay_config);
		//验证
		$this_sign = "notify_data=".$notify_data."||app_key=".$pay_config['app_key']."++mch_key=".$pay_config['cfg_key'];

		$md5_this_sign = md5($this_sign);
		//效验日志
		$arr['txd_return'] = $this_sign.'-'.$md5_this_sign;
		$this->get_token_log($arr,'1');
		
		//判断
		if($sign != $md5_this_sign) return false;
		
		//处理信息
		$notify_data_arr = json_decode($notify_data,true);
		
		//支付状态
		if($notify_data_arr['status'] != 'PAY_SUCCESS') return false;
		

		//检查金额
		$log_id = $this->db->getOne("SELECT p.log_id FROM ".$this->ecs->table('order_info')." AS o LEFT JOIN ".$this->ecs->table('pay_log')." AS p on p.order_id = o.order_id WHERE o.order_sn = '$notify_data_arr[mch_order_no]'");
		//检查金额日志
		$arr['txd_return'] = $log_id.'-'.$notify_data_arr['total_fee'];
		$this->get_token_log($arr,'1');
		if (!check_money($log_id, $notify_data_arr['total_fee'])) return false;

		//日志
		$arr = array(
			'txd_data'   => '',
			'txd_return' => $notify_data.$sign,
			'txd_or'     => '2',
			'order_sn'   => $notify_data_arr['mch_order_no'],
		);
		$this->get_token_log($arr,'1');
		
		//修改订单状态
		$v_oid = get_order_id_by_sn($notify_data_arr['mch_order_no']);
		
		order_paid($v_oid);
		echo 'success';
	}
	
	/* 同步 */
	public function get_verify()
	{
		$arr['order_id']   = $_REQUEST['order_id'];//订单号
		$arr['result']     = $_REQUEST['result'];//结果
		$arr['return_msg'] = $_REQUEST['return_msg'];//说明
		$arr['time']       = $_REQUEST['time'];//请求时间
		$arr['token_id']   = $_REQUEST['token_id'];//token_id
		$sign       = $_REQUEST['sign'];
		
		
		//效验
		if($arr['result'] != '1') return false;
		ksort($arr);
		$md_sign = '';
		foreach($arr as $key=>$value){
			$md_sign .= $value;
		}
		$md_sign = MD5($md_sign .= $this->get_verify_key);

		
		if($sign != $md_sign) return false;
		
		#############主动查询订单信息
		//获取订单信息
		$order = $this->db->getRow("SELECT order_id,order_sn,order_amount,txd_pay_token_id AS token_id,pay_id FROM ".$this->ecs->table('order_info')." WHERE order_sn = '$arr[order_id]' AND txd_pay_token_id = '$arr[token_id]'");
		//配置
		$this->config($order['pay_id'],$order);
		//主动求情结果
		$message = $this->get_order_result();
		
		//请求结果失败
		if($message['code'] != '1') return false;
		#############
		
		$arr['total_fee'] = $this->db->getOne("SELECT p.order_amount FROM ".$this->ecs->table('order_info')." AS o LEFT JOIN ".$this->ecs->table('pay_log')." AS p on p.order_id = o.order_id WHERE o.order_sn = '$arr[order_id]' AND txd_pay_token_id = '$arr[token_id]'");	
		
		
		//日志
		$sql_arr = array(
			'txd_data'   => '',
			'txd_return' => http_build_query($arr).$sign,
			'txd_or'     => '3',
			'order_sn'   => $arr['order_id'],
		);
		$this->get_token_log($sql_arr,'1');
		//修改订单状态
		$v_oid = get_order_id_by_sn($arr['order_id']);
		order_paid($v_oid);
		$arr['ec_order_id'] = $order['order_id'];
		return $arr;
	}
	/* wap支付 weichen 2014/12/15 20:45:58 start */
	/*
	* $data 插入或者更新数据
	* $state 1是插入 2是更新
	*/
	//获取token_id
	public function get_wap_token()
	{
		$data = array(
			'mode'     => $this->mode,                            //判断环境，测试还是正式环境
			'app_id'   => $this->pay_config['app_id'],             //应用id，商户去天下支付申请的应用获得的
			'mch_id'   => $this->pay_config['cfg_id'],             //商户id，合同签约后，向天下支付商务人员索取
			'order_id' => $this->order['order_sn'],                //订单号
			'price'    => $this->order['order_amount']*100,                          //订单单价(单位：元)
			'quantity' => '1',                               //购买数量(单位：个)
			'total_fee' => $this->order['order_amount']*100,                  //订单总价=订单单价*购买数量(单位：元)
			'subject'  => 'null',                      //购买商品名称,如果有中文请使用urlencode后的值
			'remark'   => 'null',                        //商品描述
			'reserve'  => 'null',                       //预留字段，供商户使用
			'notify_url'  => $this->wap_notify_url                       //预留字段，供商户使用
		);
		
		/*加密*/
		$encrypt_data = 'app_id='.$data['app_id'].'&mch_id='.$data['mch_id'].'&mch_order_no='.$data['order_id'].'&price='.$data['price'].'&quantity='.$data['quantity'].'&total_fee='.$data['total_fee'];
        $encrypt_data .= '&subject='.$data['subject'].'&remark='.$data['remark'].'&reserve='.$data['reserve'].'&notify_url='.$data['notify_url'].'&app_key='.$this->pay_config['app_key'].'&mch_key='.$this->pay_config['cfg_key'];
        $sign = md5($encrypt_data);
        $data['sign'] = $sign;

		//请求日志
		$arr = array(
			'txd_data'   => $encrypt_data,
			'txd_return' => '',
			'txd_or'     => '0',
			'order_sn'   => $this->order['order_sn'],
		);
		$this->get_token_log($arr,'1');
		
		/*curl请求*/
		$message = $this->curl($this->pay_config['cfg_gate'],$data);
		$retuen_message = json_decode($message,true);
		//返回日志
		$arr['txd_return'] = http_build_query($retuen_message);
		$this->get_token_log($arr,'2',"order_sn = '".$this->order['order_sn']."'");
		//处理返回值
		if($retuen_message['code'] == '1')
		{
			$this->db->autoExecute($this->ecs->table('order_info'), array('txd_pay_token_id'=>$retuen_message['token_id']), 'UPDATE','order_id = '.$this->order['order_id']);
		}else{
			show_message('获取wap支付信息失败');
		}
	}
	/* 跳转到天下支付wap端 
	*$order_id 订单号
	*/
	public function txd_wap_submit($order_id)
	{
	    if(!$order_id)
	    {
	        show_message('wap支付提交订单号为空');
	    }
	    //获取支付商户和支付代码
	    $sql = "SELECT pay_id,pay_num,txd_pay_token_id
                  FROM ".$this->ecs->table('order_info')." 
                 WHERE order_id = $order_id";
        $order_info_row = $this->db->getRow($sql);
        if (!is_array($order_info_row))
        {
            show_message('wap支付查询订单信息为空');
        }
        $this->config($order_info_row['pay_id'],$order_info_row);
        //$pay_type = $order_info_row['pay_num'];
        //构造支付参数
        //转换成天下店自己支付代码
        $arr_payment = array();
        $arr_payment['pay_id'] = $order_info_row['pay_id'];
        $arr_payment['bank_code'] = $order_info_row['pay_num'];
        $payment = wap_payment_list($arr_payment);
        print_r($payment);
        if(!is_array($payment) || !$payment[0]['bank_pay_num'])
        {
            show_message('wap支付银行代码错误');
        }
        $pay_type = $payment[0]['bank_pay_num'];
        $token_id =  $order_info_row['txd_pay_token_id'];
        $front_url =  $this->wap_notify_url;
        $wap_from = "pay_type=".$pay_type."&token_id=".$token_id."&front_url=".$front_url."&mch_id=".$this->pay_config['cfg_id'];
        $sign = md5($wap_from."&mch_key=".$this->pay_config['cfg_key']);
        $wap_from .= "&sign=".$sign; 
        
        header("Location:".$this->wap_from_url."?".$wap_from);
        show_message('支付中,请稍等');
	}

	/* 同步 */
	public function get_verify_wap()
	{    
		$arr['order_id'] = $_REQUEST['order_id'];//天下支付订单号
		$arr['mch_order_id'] = $_REQUEST['mch_order_id'];//商户订单号
		$arr['price'] = $_REQUEST['price'];//订单单价（元）
		$arr['quantity'] = $_REQUEST['quantity'];//数量
		$arr['total_fee'] = $_REQUEST['total_fee'];//订单总额（元）
		$arr['token_id'] = $_REQUEST['token_id'];//订单唯一凭证
		$arr['status'] = $_REQUEST['status'];//支付状态（success：支付成功，其他：支付失败）
		$sign = $_REQUEST['sign'];
		$md_sign_parm = '';
		foreach($arr as $key=>$value){
			$md_sign_parm .= $key.'='.$value.'&';
		}
		
        $pay_config = $this->db->getOne("SELECT pay_config FROM ".$this->ecs->table('payment')." WHERE pay_code = 'TXD'");
		$pay_config = unserialize($pay_config);
		$md_sign = md5($md_sign_parm.'mch_key='.$pay_config['cfg_key']);
        /*
        echo "<br>";
        echo '本地原始加密:'.$md_sign_parm.'mch_key='.$pay_config['cfg_key']."<br>";
        echo "本地sign:".$md_sign."<br>";
        echo "传递sign:".$sign."<br>";
		echo '121<br>';*/
		if($sign != $md_sign)   return false;
		//效验
		if($arr['status'] != 'success')   return false;
		//echo '12<br>';
		//主动查询订单信息
		//获取订单信息
		$sql = "SELECT order_id,order_sn,order_amount,txd_pay_token_id AS token_id,pay_id,user_id 
		          FROM ".$this->ecs->table('order_info')." 
		         WHERE order_sn = '$arr[mch_order_id]' 
		           AND txd_pay_token_id = '$arr[token_id]'";
		$order = $this->db->getRow($sql);
		//配置
		$this->config($order['pay_id'],$order);
		//主动求情结果 屏蔽自动查询
		//$message = $this->get_order_result();
		//请求结果失败
		//if($message['code'] != '1') return false;


		$sql = "SELECT p.order_amount 
		          FROM ".$this->ecs->table('order_info')." AS o 
		          LEFT JOIN ".$this->ecs->table('pay_log')." AS p on p.order_id = o.order_id 
		         WHERE o.order_sn = '$arr[mch_order_id]' 
		           AND txd_pay_token_id = '$arr[token_id]'";
		$arr['total_fee'] = $this->db->getOne($sql);	
		
		
		//日志
		$sql_arr = array(
			'txd_data'   => '',
			'txd_return' => http_build_query($arr).$sign,
			'txd_or'     => '3',
			'order_sn'   => $arr['mch_order_id'],
		);
		$this->get_token_log($sql_arr,'1');
		//修改订单状态
		$v_oid = get_order_id_by_sn($arr['mch_order_id']);
		order_paid($v_oid);
		$arr['ec_order_id'] = $order['mch_order_id'];
		$arr['txd_order_id'] = $order['order_id'];
		$arr['user_id'] = $order['user_id'];
		return $arr;
	}
	
	/* wap支付 weichen 2014/12/15 20:45:58 end */

}
?>