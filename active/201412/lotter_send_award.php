<?php 
/******************************************************************************
Filename       : www.91ka.com/activity/MiaAutumnFestival/lotter_send_award.php
Author         : SouthBear
Email          : SouthBear819@163.com
Date/time      : 2010-9-9 11:56:50
Purpose        : 用户抽奖中奖后提交中奖资料后自动发送卡密奖品
Mantis ID      : 
Description    : 
Revisions      : 
Modify         : 
Inspect        : 
******************************************************************************/
include_once("initFront.inc.php");
include_once("setting.inc.php");
include_once("function.php");
include_once('clsDB.php');


$obj_db = new clsDB();

$str_action = strval(trim($_POST['action']));
if($str_action == 'data'){//用户提交的领奖资料然后发送邮件
   $str_rec_username = strval(trim($_POST['winner_name']));  //中奖人姓名
   $str_rec_email    = strval(trim($_POST['winner_email']));  //中奖人EMAIL
   $bol_chc = CheckUserMail($str_rec_email);
   if(!$bol_chc){
       raiseError("收货人EMAIL，请重新填写正确的EMAIL");
   }
   $str_order_no    = strval(trim($_POST['orderno']));        //订单号码
   
   //查询中奖记录         
   $str_sql = "SELECT a.order_no,a.status,a.ctime,a.is_get_award,a.winner_name,a.winner_email,a.is_pay,a.utime,a.award 
                 FROM lottery a, user_order b 
  		          WHERE a.user_name = b.username 
  		            AND a.order_no  = b.order_no 
  			          AND a.user_name = '".$_SESSION['user_name']."' 
  			          AND a.order_no  = '".$str_order_no."' ";
   $obj_db->query($str_sql);
   $arr_res = $obj_db->fetchArray();	
   if(!is_array($arr_res) || count($arr_res) != 1){
       raiseError("查无该订单".$str_order_no."的抽奖记录");
   }
   if($arr_res[0]['STATUS'] != 1){
       raiseError("查无该订单".$str_order_no."的中奖记录");
   }
   if($arr_res[0]['IS_PAY '] == 1){
       raiseError("该订单".$str_order_no."的奖品已经发送，请查收");
   }
   
   //记录资料并在线发送卡密
   $str_sql = "UPDATE lottery 
                  SET is_pay = 1,is_get_award = 1,pay_time = SYSDATE,award_time = SYSDATE,
                      winner_name = '".$str_rec_username."', winner_email = '".$str_rec_email."' 
                WHERE order_no  = '".$str_order_no."' 
                  AND user_name = '".$_SESSION['user_name']."'";
   $obj_db->query($str_sql);
   
   switch($arr_res[0]['AWARD']){
       case '0': //龙腾世界吉祥金卡
          $int_prod_id    = 481;
          $str_award_name = '龙腾世界吉祥金卡'; 
       break;
       case 1:  //远征OL新手无忧卡
          $int_prod_id = 481;
          $str_award_name = '远征OL新手无忧卡';
       break;
       case 2:  //兽血外传白金新手卡
          $int_prod_id = 481;
          $str_award_name = '兽血外传白金新手卡';
       break;
       case 3:  //十虎英雄传激活码
          $int_prod_id = 481;
          $str_award_name = '十虎英雄传激活码';
       break;
       case 4:  //炼狱新手卡
          $int_prod_id = 481;
          $str_award_name = '炼狱新手卡';
       break;
       case 5:  //幻想封神白金宠物卡
          $int_prod_id = 481;
          $str_award_name = '幻想封神白金宠物卡';
       break;
       case 6:  //梦幻龙族888元新手卡
          $int_prod_id = 481;
          $str_award_name = '梦幻龙族888元新手卡';
       break;
       case 7:  //腾讯Q币30元卡
          $int_prod_id = 481;
          $str_award_name = '腾讯Q币30元卡';
       break;
   }
      
   //在线取卡
   include_once('clsActivity.php');
   $obj_active = new clsActivity();
   $arr_cards = $obj_active->get_send_card($int_prod_id);
   $arr_cards['order_no'] = $str_order_no;
   $arr_cards['prod_id']  = $int_prod_id;   
   $bol_succe = $obj_active->write_order_present_log($arr_cards);
   
   //发送邮件
   $str_subject = '91KA数字点卡商城抽奖中奖奖品送货通知单';    
   $str_body  = '亲爱的会员'.$_SESSION['user_name'].'：<BR>';
   $str_body .= '&nbsp;&nbsp;&nbsp;&nbsp;您好！您于' .$arr_res[0]['UTIME']. '在91KA系统的抽奖活动中赢得奖品'.$str_award_name.'，<BR>';
   $str_body .= '----------------------------------------------------------------<BR>';
   $str_body .= '奖品信息如下：<BR>';
   $str_body .= '<table width="500" border="0" cellpadding="5" cellspacing="0" bgcolor="#CCCCCC">
                  <tr bgcolor="#EAEAEA">
                    <td width="240">卡号</td>
                    <td width="240">密码</td>
                  </tr>
                  <tr bgcolor="#FFFFFF">
                    <td>'.$arr_cards['no'].'</td>
                    <td>'.$arr_cards['pwd'].'</td>
                  </tr>
                </table>   ';
   $str_body .= '----------------------------------------------------------------<BR>';
   $str_body .= '产品使用方式简介：<BR>';
   
   if($int_award_id == '0'){//龙腾世界吉祥金卡
      $str_body .= '官方网站：http://lt.baiyou100.com/<BR>
                    使用方式：请登陆以下网址，选择您所在的游戏区域，输入龙腾世界吉祥金卡的“密码”进行激活:<BR>
                    http://lt.baiyou100.com/ActivityList/A0013/UseRandomCard.aspx';
   }
   if($int_award_id == 1){//远征OL新手无忧卡
      $str_body .= '官方网站：http://yz.szgla.com/main.html<BR>
                    使用方式：请登陆游戏内激活，玩家可以到青龙城青龙广场授赏御史【57,40】处点击“激活卡”进行激活。';
   }
   if($int_award_id == 2){//兽血外传白金新手卡
      $str_body .= '官方网站：http://sx.baiyou100.com/<BR>
                    使用方法：请先在官方网站注册百游通行证，登陆以下网址进行，点击选择所需要激活的大区及服务器激活，每个账号只能激活一次。<BR>
                    http://acc.baiyou100.com/SpeedReg/RegForSxft.aspx?AdID=1071';
   }
   if($int_award_id == 3){//十虎英雄传激活码
      $str_body .= '官方网站：http://10hu.8kdd.com <BR>
                    使用方法：http://10hu.8kdd.com/byh/code/index.html';
   }
   if($int_award_id == 4){//炼狱新手卡
      $str_body .= '官方网站：http://www.lianyu.com <BR>
                    使用方法：<BR>
                    	1、每张新手卡只能一个账号且一个角色在一组服务器内使用，无法重复使用。<BR>
                    	2、“新手卡”使用流程：<BR>
                          （1）登陆官网，注册帐号；（已有帐号的略过此步）<BR>
                          （2）登录游戏，创立角色；（已在游戏内创建角色的略过此步）<BR>
                          （3）在官方网站上登录已申请的帐号；<BR>
                          （4）登录成功后，点击“激活新手大礼包”按钮，进入激活页面。<BR>
                          （5）输入新手卡密码，选择所在服务器和角色名，点确认。<BR>
                          （6）激活成功，登录游戏后玩家可在所激活的角色物品栏里查看使用。<BR>
                          	注：激活新手卡前，玩家务必先在游戏里建立角色。';
    
   }
   if($int_award_id == 5){//幻想封神白金宠物卡
      $str_body .= '官方网站：http://fs.51yx.com/index.html <BR>
                    使用说明 <BR>
                    1.激活本卡后，进入游戏找NPC“礼品使者”对话，领取奖励。<BR>
                    2.白金卡奖励内容根据级别进行发放，级别越高获得的奖励越丰厚，最高送至55级。 <BR>
                    3.本卡没有任何等级限制，每个帐号仅限领取并激活一次，不可重复激活；<BR>
                    4.本卡内道具均为绑定道具，不可交易。 <BR>
                    5.本卡最终解释权归北京神雕展翅科技有限公司所有。 <BR>
                    白金卡详细礼品内容 <BR>
                    	10级  随机限量专属宠物一个（色色牛\麦麦猪\飞飞雕\乖乖熊） <BR>
                      25级 宠物兽栏1个、平乱卡2张 <BR>
                      40级 炼兵仙药3个、40级黄金武器（对应职业） <BR>
                      55级 宠物双倍训练卡3张、增效双倍卡1张、神之生命1个、神之法力1个';
   }   
   if($int_award_id == 6){//梦幻龙族888元新手卡
      $str_body .= '官方网站：http://ml.playcool.com/m/sales/index.html <BR>
                    使用方法：http://ml.playcool.com/xsk/ <BR>
                    每个账号只可领取一次新手卡奖励，且账号首个激活的游戏区中第一个建立的角色才有资格使用，礼包内所赠道具为绑定道具，不可交易。';
   } 
   if($int_award_id == 7){//腾讯Q币30元卡
      $str_body .= '官方网站：http://www.qq.com <BR>
                    使用方式：请登陆 http://pay.qq.com 对QQ号进行充值.';
   }

   $str_body .= '----------------------------------------------------------------<BR>';      
   $str_body .= '<p><font color="#FF0000">本邮件由系统自动发送，请匆回复!</font>'; 

   if(!autoSendEmail($str_subject, $str_body, array($arr_res[0]['WINNER_EMAIL']))){
  	   raiseError('很抱歉，奖品发送失败，请联系我们的客服，告知您的订单号码：'.$str_order_no);
   }else{
  	   raiseMsg('恭喜您，奖品成功发送，请到您提交的邮箱'.$arr_res[0]['WINNER_EMAIL']."进行查收!",'../../index.php',5);
   }   
}
?>