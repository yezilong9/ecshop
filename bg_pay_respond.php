<?php
/**
 * 支付异步响应页面
 * time：2014-04-03
 * by：hg
 *
 *
**/


define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . 'includes/lib_transaction.php');
include_once('includes/modules/payment/payment.php');
require(ROOT_PATH . 'includes/lib_payment.php');
require(ROOT_PATH . 'includes/lib_order.php');

$pay_obj    = new payment();
return $pay_obj->POST_respond();



?>