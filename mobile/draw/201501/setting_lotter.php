<?PHP
//*************************** 抽奖系统配置参数  开始****************************//

//m9 
$lottery_status_conf = array(0 => '未抽奖',
                          	 1  => '中奖',
                          	 2  => '未中奖'
		                        );
define("KA_LOTTERY_STATUS_NULL", 0); //未执行
define("KA_LOTTERY_STATUS_YES", 1);  //中奖
define("KA_LOTTERY_STATUS_NO", 2);   //未中奖

//m13
/*
$lottery_award_conf = array( 0 => '鼓励奖，送您1-5个不等的随机积分',
                        		 1 => '火车票'
                           );
*/
//奖品
define("KA_LOTTERY_AWARD_0", 0); 
define("KA_LOTTERY_AWARD_1", 1); 
define("KA_LOTTERY_AWARD_2", 2); 
define("KA_LOTTERY_AWARD_3", 3); 
define("KA_LOTTERY_AWARD_4", 4); 
define("KA_LOTTERY_AWARD_5", 5); 
define("KA_LOTTERY_AWARD_6", 6); 
define("KA_LOTTERY_AWARD_7", 7);                           
//*************************** 抽奖系统配置参数  结束****************************//