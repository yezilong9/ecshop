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

/*------------------------------------------------------ */
//-- 分成管理页
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    $admin_agency_id = admin_agency_id();
    if (empty($_REQUEST['is_ajax']))
    {
        $smarty->assign('full_page', 1);
    }
    $sql = 'SELECT v2_percent FROM ' .$ecs->table('split_instal'). " WHERE is_line='1'";
    $split_instal = $db->getRow($sql);
    $smarty->assign('ur_here', $_LANG['split_instal']);
    $smarty->assign('split_instal',   $split_instal);
    $smarty->display('split_instal.htm');
}


/*------------------------------------------------------ */
//-- 修改配置
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'updata')
{
    $v2_percent = $_REQUEST['v2_percent'];
    
    $sql_1 = "DELETE FROM " .$ecs->table('split_instal') .
            " WHERE is_line = '1' "; 
    $db->query($sql_1);
    
    $sql = "INSERT INTO " . $ecs->table('split_instal') . " (v2_percent, is_line) " .
                    "VALUES ( '$v2_percent', '1')";
    $db->query($sql, 'SILENT'); 
   
    $links[] = array('text' => $_LANG['affiliate'], 'href' => 'split_instal.php?act=list');
    sys_msg($_LANG['edit_ok'], 0 ,$links);
}
?>