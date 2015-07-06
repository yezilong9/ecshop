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

//if($sign_message_md_new == $sign_message_md)
$user_id = $_SESSION['user_id'];
if ($user_id > 0)
{
	
	/* ccx 2014-12-26 查询会员表当中该用户是否是通过短信验证的用户 开始 */
    $sql_phone_validate = "SELECT phone_validate FROM " .$GLOBALS['ecs']->table('users'). " WHERE user_id = $user_id";
    $phone_validate  = $GLOBALS['db']->getRow($sql_phone_validate);
    if($phone_validate['phone_validate'] == 0)
    {
        $ak['msg'] = '该用户不是通过手机验证的用户,所以暂时不能参与这次的红包抽奖活动!'; 
        echo $value = json_encode($ak);
        exit; 
    }
    /*ccx 2014-12-26 */
    
    // 判断是否已经抽奖过了的  本次登陆抽奖活动的固定设置为 2015010002
    /*ccx 2014-12-26 判断该用户是否已经抽奖过 开始*/
    $luck_count_sql = "SELECT COUNT(*) FROM " .$ecs->table('lucky_draw').
                      " WHERE user_id = $user_id AND activity_code = '2015010002' ";
    $luck_count = $db->getOne($luck_count_sql);
    if($luck_count > 0)
    {
        $ak['msg'] = '您这个账号已经参与过本次抽奖活动了，不能再进行抽奖了，谢谢合作!'; 
        echo $value = json_encode($ak);
        exit;
    }
    /*ccx 2014-12-26 是否抽奖 结束*/
    
    /* ccx 2014-12-26 统计该活动参与抽奖的人数 */
    $record_count = $db->getOne("SELECT COUNT(*) FROM " .$ecs->table('lucky_draw').
                      " WHERE activity_code ='2015010002'");
    $record_number = (($record_count + 1)%100) + 0;
  
    $arr_return_info = lucky_draw_mesaage($record_number, $user_id );
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
     $ak['msg'] = '活动期间，注册即送红包,送完红包还能够进行抽奖, 赶紧去注册进行登录吧! GO!!!'; 
     $ak['source'] = 'no_order';
     echo $value = json_encode($ak);
     exit;
}

function lucky_draw_mesaage($record_number, $user_id )
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
        $arr_return_draw['message'] = '网络故障,该用户抽奖出现问题,请稍后再参与抽奖';
        return $arr_return_draw;
        exit;
    }    
    
    /*ccx 2014-12-13 获取红包类型 开始*/
    $sql_hongbao = 'SELECT type_id, type_money FROM ' . $GLOBALS['ecs']->table("bonus_type").
                   ' WHERE send_type=5 AND type_money ='.$type_money. ' AND (send_start_date <'.gmtime().' AND send_end_date >'.gmtime().')';  
    $reg_bonus = $GLOBALS['db']->getRow($sql_hongbao);
    if(empty($reg_bonus))
    {
        $arr_return_draw['message'] = '没有获取相应的红包价值，该次抽奖没有成功';
        return $arr_return_draw;
        exit;
    }
    $sql_lucky_draw = "INSERT INTO ".$GLOBALS['ecs']->table('lucky_draw').
                         " ( order_sn, goods_amount, user_id, email, ip_addr, draw_time,bonus_type_id,password,type_money,activity_code )".
                      "VALUES('新用户登录抽奖', 0, ".$user_id.", '$email', '".real_ip()."', '".gmtime()."' , ".
                               $reg_bonus['type_id'].",'', '".$type_money."', '2015010002' )";  
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