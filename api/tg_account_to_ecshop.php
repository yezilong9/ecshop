<?php
/**
 * 导账号计划:
 * (1) ecshop的ecs_users表需要增加的三个字段是
 *    `tm_account` varchar(50) NOT NULL COMMENT '天猫推广系统账号，用户用这个账号也可以登录ecshop',
 *    `tm_level` int(1) unsigned DEFAULT '1' COMMENT '天猫推广商级别',
 *    `tm_path` varchar(255) NOT NULL COMMENT '天猫推广商等级路径，显示账号之间的关系，用现在user_id记录',
 *   
 * (2) 用PHP程序导tg的用户信息到ecshop的ecs_users，需要处理插入和新建记录，tg用户记录在 tg_promoter 和 tg_promoter_staff。
 *    先处理高等级的，再处理低等级的
 * 
 * (3) tg系统新建账号时，通过接口同时通知ecshop系统，功能同(2)的一样调用共同函数
 *
 *  ps. ecshop的svn在svn://172.16.64.53/txd/branch/o2o
 * 
 *  处理过程：ecshop有两个地方可添加账号，
 *            一个是 后台添加账号url路径admin/users.php?act=add，另一个是 前台注册地址user.php?act=register
 *            先看看这个两个地方
 */

// 这个文件是把tg系统的账号导入到ecshop，导账号计划的第二步
// 其实第三步只要新建账号后，触发本文件就可以了
// 这个文件放在 http://www.ec.com/ 系统下，对应157上 /home/webadm/wwwroot/ecshop_svn/api/tg_account_to_ecshop
// 访问地址     http://www.ec.com/api/tg_account_to_ecshop.php

define('IN_ECS', true);
require '../includes/init.php';
echo "<pre>";

//========================第0步：增加ecshop的ecs_users表需要增加的三个字段========================
$table = $GLOBALS['ecs']->table('users');
$db = $GLOBALS['db'];

$new_field = array('tm_account', 'tm_level', 'tm_path');    // 新字段
$old_field = array();                                       // 老字段

$sql = "desc ".$table;
$row = $db->getAll($sql); // getRow 是取一条

for($i = 0; $i < count($row); $i++)
{
    $old_field[] = $row[$i]['Field'];
}

// 新老字段是否有交集
$arr_jiaoji = array_intersect($old_field, $new_field);

if(count($arr_jiaoji) > 0)  // 如果有交集，说明已经插入过
{
    echo "已经有[tm_account][tm_level][tm_path]这三个字段，不用再插入\n\n";
}
else
{
    echo "要插入三个字段...\n";
    $sql = "alter table ".$table." add COLUMN `tm_account` VARCHAR(50) DEFAULT NULL COMMENT '天猫推广系统账号，用户用这个账号也可以登录ecshop'";
    $db->query($sql);
    $sql = "alter table ".$table." add COLUMN `tm_level` INT(1) DEFAULT NULL COMMENT '天猫推广商级别'";
    $db->query($sql);
    $sql = "alter table ".$table." add COLUMN `tm_path` VARCHAR(255) DEFAULT NULL COMMENT '天猫推广商等级路径，显示账号之间的关系，用现在user_id记录'";
    $db->query($sql);
    echo "插入字段完毕\n\n";
}

//========================第1步：CURL取tg的账号信息，组合成========================
// 这里发送请求主要参考 /home/webadm/wwwroot/cybercafe/admin/sysm/WEB-INF/models/tmallmodel.php 中 function getTaobaoSerach
// 还要 http://www.txshop.com/print/api 这里配合增加功能相应功能
 
// $sql = "select * from ".$table;
// $row = $db->getAll($sql);
// print_r($row);
if($_SERVER['SERVER_ADDR'] == '172.16.64.157') // 如果是内网
{
    $tg_account_url = 'http://www.txshop.com/print/api';
}
else
{   
    $tg_account_url = 'http://portal.txd168.com/print/api';
}

echo "取推广系统账号的URL:".$tg_account_url."\n\n";
// 对应的tg的修改文件在53上的 /home/webadm/wwwroot/txshop/WEB-INF/classes/Controller/Print.php 修改 function action_api 

$gotogame_key = '3RnJdEBW_Y8QaekO';

$tg_account = array();
$tg_account['action'] = 'tg_account';
$tg_account['time'] = time();
$tg_account['sign'] = md5($tg_account['action'].
                           $tg_account['time'].
                           $gotogame_key);

echo "提交的参数是:".http_build_query($tg_account)."\n\n";

$str_ret = curl_access($tg_account_url, http_build_query($tg_account), 'get');
$arr_ret = json_decode($str_ret, true);

echo "推广系统返回:";
// print_r($arr_ret);
//die("shit");
if($arr_ret['success'] === true)
{
    echo "有返回\n\n";
    
    //========================第2步：分析各级帐号 ========================
    $arr_account = $arr_ret['arr_account']['rows'];
    //print_r($arr_account);
    
    $level_account = array(); // 保存要处理的账号
    
    for($i = 0; $i < count($arr_account); $i++)
    {
        $level = $arr_account[$i]['level'];
        
        $arr_tmp = array();
        $arr_tmp['level'] = $level;
        $arr_tmp['promoter_id'] = $arr_account[$i]['promoter_id'];
        
        $arr_tmp['path'] =  substr($arr_account[$i]['path'], 2);    // 等级ID层级 去掉前-1层的，
        $arr_tmp['path_account'] = get_path_account($arr_account, $arr_account[$i]['path']);    // 等级账号层级
        $arr_tmp['account'] = $arr_account[$i]['account'];
        $arr_tmp['password'] = $arr_account[$i]['password'];
        $arr_tmp['real_name'] = ($arr_account[$i]['real_name'] == $arr_account[$i]['staff_real_name']) ? 
                $arr_account[$i]['real_name'] : $arr_account[$i]['real_name'].'|'.$arr_account[$i]['staff_real_name'];  // 真实姓名
        $arr_tmp['email'] = $arr_account[$i]['email'];
        
        $level_account[$level][] = $arr_tmp;
    }
    
    // echo "第0层如下:\n";
    // print_r($level_account[0]);
    
    // echo "\n第1层如下:\n";
    // print_r($level_account[1]);
    
    // echo "\n第2层如下:\n";
    // print_r($level_account[2]);
    
    // echo "\n第3层如下:\n";
    // print_r($level_account[3]);
    
    // echo "\n第4层如下:\n";
    // print_r($level_account[4]);
    
    // echo "\n第5层如下:\n";
    // print_r($level_account[5]);
    
    
// 分开各取数组
/*
[56] => Array
(
    [promoter_id] => 10335
    [level] => 2
    [path] => 0,1,10046
    [account] => test_tb1
    [password] => e10adc3949ba59abbe56e057f20f883e
    [real_name] => test_tb1
    [staff_real_name] => test_tb1
    [status] => 1
    [project_id] => 110
)
*/


}
else
{
    echo "没有返回\n";
}


//========================第3步：========================
// copy from /home/webadm/wwwroot/ecshop_svn/admin/users.php 参考用
/*
// 取出注册扩展字段 
$sql = 'SELECT * FROM ' . $ecs->table('reg_fields') . ' WHERE type < 2 AND display = 1 AND id != 6 ORDER BY dis_order, id';
$extend_info_list = $db->getAll($sql);
print_r($extend_info_list);
// add by hg for date 2014-003-25 查找代理商与判断是否是最高管理员,是否为增加页 begin
$rank_sql = "select user_id,user_name from ". $ecs->table('users') ."where user_rank = 4";
$rankRow = $db->getAll($rank_sql);
print_r($rankRow);
*/

// 处理各层账号
print_r($level_account);die;
for($i = 0; $i < count($level_account); $i++)
{
    if($i == 0)
        continue;   // 不处理第0层
     
    $arr_level = $level_account[$i];
    
    if($i == 1) // 测试用，测试只处理第一层
    {
        echo "=================测试用只处理第".$i."层==============\n";
        print_r($arr_level);
        
        for($j = 0; $j < count($arr_level); $j++)
        {
            $account = $arr_level[$j]['account'];
            $level = $arr_level[$j]['level'] + 0;
            $path = $arr_level[$j]['path'];
            $path_account = $arr_level[$j]['path_account'];
            
            
            // 1、查下有没有这个帐号
            $sql =  "select user_id, user_name, tm_account ".
                    "from ". $ecs->table('users') ." ".
                    "where user_name = '".$account."' ".
                        "or user_name = 'o2o".$account."' ".
                        "or tm_account = '".$account."' ".
                        "or tm_account = 'o2o".$account."' ";
            //echo $sql."\n";
            $user_row = $db->getAll($sql);
            //print_r($user_row);
            
            // ⊙⊙⊙⊙⊙⊙⊙⊙⊙⊙⊙⊙⊙⊙ 这个要阿贵检查修改 开始 ⊙⊙⊙⊙⊙⊙⊙⊙⊙⊙⊙⊙⊙⊙
            if(count($user_row) == 0) // 没有这个账号
            {
                echo "(".$j.")没有这个账号【".$account."】，可以插入\n";
                $sql = "INSERT INTO ".$ecs->table('users')." (XXX, XXX, XXX, ..., tm_mark, tm_account, tm_level, tm_path) ".
                        "VALUES (YYY, YYY, YYY, ..., 1, '".$account."', ".$level.", '".$path_account."')"; 
                echo "SQL:".$sql."\n";   
            }
            elseif(count($user_row) == 1)
            {
                echo "(".$j.")这个账号已经存在【".$account."】，没有插入操作，其他字段酌情更新\n";
                $sql =  "UPDATE " .$ecs->table('users'). " SET ".
                        "tm_account = '".$account."', ".
                        "tm_level = ".$level.", ".
                        "tm_path = '".$path_account."' ".
                        "WHERE user_name = '".$account."' ".
                        "or user_name = 'o2o".$account."' ".
                        "or tm_account = '".$account."' ".
                        "or tm_account = 'o2o".$account."' ";
                echo "SQL:".$sql."\n";
            }
            else
            {
                echo "(".$j.")这个账号存在多个【".$account."】，请自己重新检查\n";
            }

            echo "======现在都没有query\n\n";
            // ⊙⊙⊙⊙⊙⊙⊙⊙⊙⊙⊙⊙⊙⊙ 这个要阿贵检查修改 结束 ⊙⊙⊙⊙⊙⊙⊙⊙⊙⊙⊙⊙⊙⊙
            
        }
    }
   
}


//========================第4步：检查========================
//
echo "\nend here";






// 一些用到的函数
// 取账号组成的层级
function get_path_account($arr_account, $id_path)
{
    $arr_return = array();
    $arr_id_path = explode(',', $id_path);
    // print_r($arr_id_path);
    foreach($arr_id_path as $key => $value)
    {
        for($i = 0; $i < count($arr_account); $i++)
        {
            if($arr_account[$i]['promoter_id'] == $value)
            {
                $arr_return[] = $arr_account[$i]['account'];
                break;
            }
        }
    }
    
    $str_return = implode(',', $arr_return);
    
    return $str_return;
}

/**
*   curl请求
*   param   $url  请求地址地址
*   param   $str_query    请求的参数
*   param   $method   请求的方式
*   param   $str_referer   伪造请求来源地址
*   param   $cookie_file   请求cookie信息
*/
function curl_access($str_url, $str_query = '', $method = '', $str_referer = '', $cookie_file = '')
{
    //echo $str_url."?".$str_query;
    $obj_ch = curl_init();
    curl_setopt($obj_ch, CURLOPT_TIMEOUT, 300);
    curl_setopt($obj_ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:20.0) Gecko/20100101 Firefox/20.0');

    if ($cookie_file != '')
    {
        if(file_exists($cookie_file))
        {
            curl_setopt($obj_ch, CURLOPT_COOKIEFILE, $cookie_file);
        }
        curl_setopt($obj_ch, CURLOPT_COOKIEJAR, $cookie_file);
    }

    if ($str_referer != '')
    {
        curl_setopt($obj_ch, CURLOPT_REFERER, $str_referer);
    }

    if ($method == 'post')
    {
        curl_setopt($obj_ch, CURLOPT_URL, $str_url);
        curl_setopt($obj_ch, CURLOPT_POST, 1);
        curl_setopt($obj_ch, CURLOPT_POSTFIELDS, $str_query);
    }
    else
    {
        curl_setopt($obj_ch, CURLOPT_URL, $str_url.($str_query?'?'.$str_query:''));
        curl_setopt($obj_ch, CURLOPT_HTTPGET, 1);
    }

    if (strpos($str_url, 'https') !== false)
    {
        curl_setopt($obj_ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($obj_ch, CURLOPT_SSL_VERIFYHOST, 1);
        curl_setopt($obj_ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    }

    curl_setopt($obj_ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($obj_ch, CURLOPT_RETURNTRANSFER, 1);
    $str = curl_exec($obj_ch);
    curl_close($obj_ch);

    return trim($str);
}







