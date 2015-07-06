<?php

/**
 * ECSHOP 专题前台
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * @author:     webboy <laupeng@163.com>
 * @version:    v2.1
 * ---------------------------------------------
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}
$topic_id  = empty($_REQUEST['topic_id']) ? 0 : intval($_REQUEST['topic_id']);

$sql = "SELECT * FROM " . $ecs->table('topic') .
        "WHERE " . gmtime() . " >= start_time and " . gmtime() . "<= end_time";

$topic = $db->getAll($sql);
if(empty($topic))
{
    $smarty->assign('false','1');
}
$smarty->assign('page_title', '活动页面'); // 页面标题
$smarty->assign('topic',$topic);
/* 显示模板 */
$smarty->display('topic.dwt');

?>