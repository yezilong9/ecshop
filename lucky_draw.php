<?php
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
include_once("active/201412/setting_lotter.php");
$parm_lotter = date('Y-m-d H-i-s').'抽奖参数'.print_r($_REQUEST,true).'\n';
$file_name = "draw.txt";
error_log($parm_lotter,3,$file_name);
$bln_is_error = false;
$bln_switch   = true;
$ak = array
(
    'int_lottery_result'=> 1 //默认10元
    ,'int_lottery_stat'	=> 2 //默认为未中奖
    ,'int_lottery_win'   => KA_LOTTERY_STATUS_NO
    ,'award' => ''
    ,'source' => ''
    , 'msg' => '抽奖暂未开始请联系我们工作人员!'
); 

$order_id   = $_REQUEST['order_id'];
$payprice   = $_REQUEST['payprice'];
$datetime   = $_REQUEST['datetime'];
$ip_addr    = $_REQUEST['ip_addr'];
$email      = $_REQUEST['email'];
$user_id    = $_REQUEST['user_id'];

$sign_message_md     = $_REQUEST['sign_message_md'];
$key_value = 'untx';
$sign_message = 'order_id='.$order_id.'&payprice='.$payprice.'&email='.$email.'&user_id='.
               $user_id.'&datetime='.$datetime.'&ip_addr='.$ip_addr;
$sign_message_md_new = md5($sign_message.$key_value);
if($sign_message_md_new == $sign_message_md)
{
    if($order_id =='')
    {
        $ak['msg'] = '订单不存在，不能再进行抽奖了，谢谢合作!'; 
        echo $value = json_encode($ak);
        //echo '订单不存在，不能再进行抽奖了，谢谢合作';
        exit; 
    }
    /*ccx 2014-12-13  获取订单号 开始*/
    $sql_order_sn = "SELECT order_sn FROM " .$ecs->table('order_info').
                      " WHERE order_id = ".$order_id;
    $order_sn = $db->getOne($sql_order_sn);
    /*ccx 2014-12-13  获取订单号 结束*/
    if($order_sn == '')
    {
        $ak['msg'] = '订单号不存在，不能再进行抽奖了，谢谢合作!'; 
        echo $value = json_encode($ak);
        //echo iconv("GB2312","UTF-8",'订单号不存在，不能再进行抽奖了，谢谢合作');
        exit; 
    }
    
    /* ccx 2014-12-15 获取订单活动代码 开始 */
    $sql_activity_code = "SELECT activity_code, order_sn,user_id FROM " .$GLOBALS['ecs']->table('order_info'). " WHERE order_id=$order_id";
    $activity_code  = $GLOBALS['db']->getRow($sql_activity_code);
    if(empty($activity_code['activity_code']))
    {
        $ak['msg'] = '没有获取到订单当中的活动代码，所以该订单不能参与抽奖活动!'; 
        echo $value = json_encode($ak);
        //show_message("没有获取到订单当中的活动代码，所以该订单不能参与抽奖活动", "", 'flow.php?step=done', 'error');
        //echo iconv("GB2312","UTF-8",'没有获取到订单当中的活动代码，所以该订单不能参与抽奖活动');
       exit; 
    }
    if($user_id != $activity_code['user_id'])
    {
        $ak['msg'] = '用户账号错误'; 
        echo $value = json_encode($ak);
        //echo iconv("GB2312","UTF-8",'订单号不存在，不能再进行抽奖了，谢谢合作');
        exit;
    }
    /* ccx 2014-12-15 获取订单活动代码 结束 */
    

    /*ccx 2014-12-13 判断这个订单是否已经参与过抽奖了，如果参与过抽奖的订单，就不允许再次参与抽奖了 开始*/
    $luck_order_sn = "SELECT COUNT(*) FROM " .$ecs->table('lucky_draw').
                      " WHERE order_sn = '".$order_sn."' AND activity_code ='".$activity_code['activity_code']."'";
    $luck_order_sn_count = $db->getOne($luck_order_sn);
    if($luck_order_sn_count > 0)
    {
        $ak['msg'] = '您的订单已经抽过奖了，不能再进行抽奖了，谢谢合作!'; 
        echo $value = json_encode($ak);
        //echo iconv("GB2312","UTF-8",'您的订单已经抽过奖了，不能再进行抽奖了，谢谢合作');
        exit;
    }
    /*ccx 2014-12-13 判断这个订单是否已经参与过抽奖了，如果参与过抽奖的订单，就不允许再次参与抽奖了 结束*/
    
    $record_count = $db->getOne("SELECT COUNT(*) FROM " .$ecs->table('lucky_draw').
                      " WHERE activity_code ='".$activity_code['activity_code']."'");
    $record_number = (($record_count + 1)%100) + 0;
    $arr_return_info = lucky_draw_mesaage($record_number, $order_id, $user_id, $payprice);
    if(!$arr_return_info['success'] || !in_array($arr_return_info['type_money'],array(10,20,30,50)))
    {
        $ak['msg'] = $arr_return_info['message']; 
        echo $value = json_encode($ak);
        exit;
    }
    $ak = array
    (
        'int_lottery_result'=> KA_LOTTERY_AWARD_1 //默认10元
        ,'int_lottery_stat'	=> KA_LOTTERY_STATUS_YES //默认为未中奖
        ,'int_lottery_win'   => KA_LOTTERY_STATUS_YES
        ,'award' => ''
        , 'msg' => $arr_return_info['message']
    ); 

    if($arr_return_info['type_money'] == 10)
    {
         $ak['int_lottery_result'] = KA_LOTTERY_AWARD_1;
    }
    elseif($arr_return_info['type_money'] == 20)
    {
        $ak['int_lottery_result'] = KA_LOTTERY_AWARD_2;
    }
    elseif ($arr_return_info['type_money'] == 30)
    {
        $ak['int_lottery_result'] = KA_LOTTERY_AWARD_3;
    }
    elseif ($arr_return_info['type_money'] == 50)
    {
        $ak['int_lottery_result'] = KA_LOTTERY_AWARD_4;
    }
    
    echo $value = json_encode($ak);
    error_log($value,3,$file_name);
    exit;
}
else 
{
    //你还在犹豫什么赶紧下单购买赢抽奖!
     $ak['msg'] = '活动期间，注册即送红包。下单购买还可以抽奖，100%中奖，最高大奖还有Iphone6 Plus!  GO....'; 
     $ak['source'] = 'no_order';
     echo $value = json_encode($ak);
     exit;
}

function lucky_draw_mesaage($record_number, $order_id, $user_id, $payprice)
{
    $GLOBALS['db']->rollback();  //事务中断
    $GLOBALS['db']->begin();   //开始事务
    $arr_return_draw = array();
    $arr_return_draw['success'] = false;
    $arr_return_draw['type_money'] = 0;
    $arr_return_draw['message'] = '抽奖未开时,请联系工作人员.';
    if($record_number >= 1 && $record_number <= 10 )
    {
        $type_money = 10 ; 
    }
    elseif($record_number >= 11 && $record_number <= 60)
    {
        $type_money = 20 ; 
    }
    elseif ($record_number >= 61 && $record_number <= 98)
    {
        $type_money = 30 ; 
    }
    elseif ($record_number == 99 || $record_number == 0 )
    {
        $type_money = 50 ; 
    }
    else
    {
        $arr_return_draw['message'] = '订单金额错误';
        return $arr_return_draw;
        exit;
    }    
    $sql_activity_code = "SELECT activity_code, order_sn FROM " .$GLOBALS['ecs']->table('order_info'). " WHERE order_id=$order_id";
    $activity_code  = $GLOBALS['db']->getRow($sql_activity_code);
    
    /*ccx 2014-12-13 获取红包类型 开始*/
    $sql_hongbao = 'SELECT type_id, type_money FROM ' . $GLOBALS['ecs']->table("bonus_type").
                   ' WHERE send_type=2 AND type_money ='.$type_money. ' AND (send_start_date <'.gmtime().' AND send_end_date >'.gmtime().')';  
    $reg_bonus = $GLOBALS['db']->getRow($sql_hongbao);
    if(empty($reg_bonus))
    {
        $arr_return_draw['message'] = '没有获取相应的红包价值，该次抽奖没有成功';
        return $arr_return_draw;
        exit;
    }
    $sql_lucky_draw = "INSERT INTO ".$GLOBALS['ecs']->table('lucky_draw').
                         " ( order_sn, goods_amount, user_id, email, ip_addr, draw_time,bonus_type_id,password,type_money,activity_code )".
                      "VALUES('".$activity_code['order_sn']."', $payprice, ".$user_id.", '$email', '".real_ip()."', '".gmtime()."' , ".
                               $reg_bonus['type_id'].",'', '".$type_money."', '".$activity_code['activity_code']."' )";  
    if ($GLOBALS['db']->query($sql_lucky_draw) === false)
    {
        $GLOBALS['db']->rollback();  //事务中断
        $arr_return_draw['message'] = '记录抽奖活动数据失败,请联系商家客服';
        return $arr_return_draw;
        exit;
    }
    
    $sql = "INSERT INTO ".$GLOBALS['ecs']->table('user_bonus').
                            " ( bonus_type_id, bonus_sn, user_id, used_time, order_id, emailed)".
                          "VALUES(".$reg_bonus['type_id'].", 0, ".$user_id.", 0, 0, 0)";        
    if ($GLOBALS['db']->query($sql) == false || $GLOBALS['db']->affected_rows() != 1)
    {
        $GLOBALS['db']->rollback();  //事务中断
        $arr_return_draw['message'] = '红包抽奖活动失败,请联系商家客服';
        return $arr_return_draw;
        exit;
    }
    
    $arr_return_draw['success'] = true;
    $arr_return_draw['type_money'] = $type_money;
    $arr_return_draw['message'] = "恭喜你,这次抽奖活动你获得了".$type_money."元的红包";
    $GLOBALS['db']->commit();
    return $arr_return_draw;
    /*ccx 2014-12-13 获取红包类型 结束*/
    
}

?>