<?php
/******************************************
* 说明：提供天猫查询用户余额和提供一个转账功能呢
* author:hg
* date :2014-08-12;
*
*******************************************/


class money
{
	private $key     = 'VGsPCiN8Qy9FbJSvJpvhA8UuR';//效验KEY
	
	private $purpose = 'purpose';//1余额，2转出，3转入
	
	private $from 	 = 'from';//purpose为1时此参数可空 ，1是tm转账，2加油站转账
	
	private $account = 'account';//用户名
	
	private $money   = 'money';//purpose为1时此参数可空,purpose为2时代表（转出），purpose为3时代表（转入）
	
	private $sign    = 'sign';//效验
	
	private $user_name    = '';//O2O平台的用户名
	
	private $state    = '';//状态
	
	private $user_money    = 0;//查询得到的用户金额
	
	private $log_id = '';//流水账ID
	/**
	* 说明:效验数据
	* @arr array 接收数组
	*
	**/
	public function check_sign($arr)
	{
		$this->purpose = $arr[$this->purpose];
		$this->from    = $arr[$this->from];
		$this->account = $arr[$this->account];
		$this->money   = $arr[$this->money];
		$this->sign    = $arr[$this->sign];
		#返回数组#
		if(($this->purpose ==1) && ($this->purpose ==2) && ($this->purpose ==3))
			$error = '1';
		elseif(empty($this->from))
			$error = '2';
		elseif(empty($this->account))
			$error = '3';
		elseif(($this->purpose !=1) && empty($this->money))
			$error = '4';
		elseif(empty($this->sign))
			$error = '5';
		#返回错误信息#
		if(isset($error)) return $this->state = 1001;
		$md_sign = MD5($this->purpose.$this->from.$this->account.$this->money.$this->key);//效验
		if($md_sign != $this->sign)
			return $this->state = 1001;
		else
			return true;
	}
	
	/**
	* 说明：选择处理方式
	**/
	public function dispose()
	{
		#获取o2o这边的用户名#
		$this->user_name = TMUSER.$this->account;
		$user_id = $GLOBALS['db']->getOne("SELECT user_id from ".$GLOBALS['ecs']->table('users').
		" WHERE user_name = '$this->user_name'");
		if(!$user_id) return $this->state = 1002;
		#查询，转出，转入#
		if($this->purpose == 1)
			$this->check_user_balance($user_id);
		elseif($this->purpose == 2)
			$this->roll_out($user_id);
		elseif($this->purpose == 3)
			$this->shift_to($user_id);
	}
	
	/**
	* 说明：查询用户余额
	**/
	private function check_user_balance($user_id)
	{
		$this->state = 1;
		return $this->user_money = $GLOBALS['db']->getOne("SELECT user_money from ".$GLOBALS['ecs']->table('users').
		" WHERE user_id = $user_id");
		
	}
	
	/**
	* 说明：账户金额转出
	**/
	private function roll_out($user_id)
	{
		# 查询余额是否足够 #
		$old_money = $this->check_user_balance($user_id);
		unset($this->user_money);
		if($this->money > $old_money)
		{
			$this->state = 1003;
			return false;
		}
		$change_desc = $this->log($user_id);
		$log_id = log_account_change($user_id, -1*$this->money, 0, 0, 0, $change_desc, 99);
		if($log_id !== false)
		{
			$this->log_id = $log_id;
			$this->state = 1;
		}
		else
			$this->state = 1004;
	}
	
	/**
	* 说明：账户金额转入
	**/
	private function shift_to($user_id)
	{
		$change_desc = $this->log($user_id);
		$log_id = log_account_change($user_id, $this->money,0, 0, 0, $change_desc, 99);
		if($log_id !== false)
		{
			$this->log_id = $log_id;
			$this->state = 1;
		}
		else
			$this->state = 1005;
	}

	/**
	* 说明:日志
	**/
	private function log($user_id)
	{
		
		if($this->from == 1)
			$site = 'TM';
		elseif($this->from == 2)
			$site = '加油站';
		$arr = array(
				'time'	 => time(),
				'user_id'=> $user_id,
				'money'=> $this->money,
				//'return'=> json_encode($this->return_msg())
		);
		#转出，转入 日志#
		if($this->purpose == 2)
		{
			$arr['motion'] = 1;
			$arr['log'] = '用户：'.$this->user_name.'于'.date('Y-m-d H:i:s',time()).'转出金额:'.$this->money.'元到'.$site.'平台';
		}
		elseif($this->purpose == 3)
		{
			$arr['motion'] = 2;
			$arr['log'] = '用户：'.$this->user_name.'于'.date('Y-m-d H:i:s',time()).'从'.$site.'平台转入金额:'.$this->money.'元';
		}
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('motion_money_log'), $arr, 'INSERT');
		return $arr['log'];
	}
	
	/**
	* 说明:返回数组
	*
	**/
	public function return_msg()
	{
		$return_msg = array();
		$return_msg['purpose'] = $this->purpose;
		if($this->from)    $return_msg['from'] = $this->from;
		$return_msg['account'] = $this->account;
		if($this->money)   $return_msg['money'] = $this->money;
		$return_msg['sign'] = $this->sign;
		$return_msg['state'] = $this->state;
		if($this->user_money)    $return_msg['user_money'] = $this->user_money;
		if($this->log_id)   $return_msg['log_id'] = $this->log_id;
		return $return_msg;
	}
}



?>