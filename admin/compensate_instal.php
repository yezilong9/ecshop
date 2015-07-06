<?php

/**
 *  程序说明
 * ===========================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www..com；
 * ----------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ==========================================================
 * $Author: liubo $
 * $Id: affiliate.php 17217 2011-01-19 06:29:08Z liubo $
 */

define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
admin_priv('affiliate');
$config = get_affiliate();

/*------------------------------------------------------ */
//-- 分成管理页
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    assign_query_info();
    if (empty($_REQUEST['is_ajax']))
    {
        $smarty->assign('full_page', 1);
    }
    $sql = "SELECT * "." FROM ".$GLOBALS['ecs']->table('split_instal'). 
            " WHERE is_line = '2' ";
               
    $split_instal = $GLOBALS['db']->getAll($sql);
    //print_r($split_instal);
    $smarty->assign('ur_here', $_LANG['compensate_instal']);
    $smarty->assign('config', $config);
    $smarty->assign('split_instal',   $split_instal);
    $smarty->display('compensate_instal.htm');
}

/*------------------------------------------------------ */
//-- ccx 2015-03-26 增加消费补偿比例
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'insert')
{
    // 交差购物 消费补偿，
    $on = $_POST['on'];
    if($on ==1)  // 1：V3或者V2购物，需要补偿给V2,V3的一个消费补偿比例；     2：V1购物，只补偿给V1的补偿比例
    {
        $sql_1 = "DELETE FROM " .$ecs->table('split_instal') .
            " WHERE is_line = '2' AND user_rank = 1 "; 
        $db->query($sql_1);
        
        $v1_percent = $_REQUEST['rank_a_1'];
        $v2_percent = $_REQUEST['rank_a_2'];  
        $sql = "INSERT INTO " . $ecs->table('split_instal') . " (v1_percent, v2_percent, is_line, user_rank ) " .
                    "VALUES ( '$v1_percent', '$v2_percent', '2', '1')";
        $db->query($sql, 'SILENT'); 
    }
    else
    {
        $sql_1 = "DELETE FROM " .$ecs->table('split_instal') .
            " WHERE is_line = '2' AND user_rank = 2 "; 
        $db->query($sql_1);
        
        $v1_percent = $_REQUEST['rank_b_1'];
        $sql = "INSERT INTO " . $ecs->table('split_instal') . " (v1_percent, is_line, user_rank ) " .
                    "VALUES ( '$v1_percent', '2', '2')";
        $db->query($sql, 'SILENT'); 
    }
     $links[] = array('text' => $_LANG['affiliate'], 'href' => 'compensate_instal.php?act=list');
     sys_msg("设置操作成功", 0 ,$links);
    
}

/*------------------------------------------------------ */
//-- 修改配置
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'updata')
{

    $separate_by = (intval($_POST['separate_by']) == 1) ? 1 : 0;

    $_POST['level_money_all'] = (float)$_POST['level_money_all'];
    //$_POST['level_money_all'] > 100 && $_POST['level_money_all'] = 100;
    
    if (!empty($_POST['level_money_all']) && strpos($_POST['level_money_all'],'%') === false)
    {
        $_POST['level_money_all'] .= '%';
    }
    $temp = $_POST['level_money_all'];
    //print_r($temp);exit;
    put_affiliate($temp);
    $links[] = array('text' => $_LANG['affiliate'], 'href' => 'compensate_instal.php?act=list');
    sys_msg("设置操作成功", 0 ,$links);
}



function get_affiliate()
{
    $sql = "SELECT value FROM ". $GLOBALS['ecs']->table('shop_config') . 
            " WHERE code = 'compensate_instal' ";
    $_value1 = $GLOBALS['db']->getOne($sql);
    return $_value1;
}

function put_affiliate($temp)
{
    $sql = "UPDATE " . $GLOBALS['ecs']->table('shop_config') .
           "SET  value = '$temp'" .
           "WHERE code = 'compensate_instal'";
    $GLOBALS['db']->query($sql);
    clear_all_files();
}
?>