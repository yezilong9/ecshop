<?php
/** 
 * ecmoban By Leah
 * 地图
 */
define('IN_ECS', true);
define('ECS_ADMIN', true);
require(dirname(__FILE__) . '/includes/init.php');

if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}
/* 显示模板 */
assign_template();
$smarty->display('map.dwt');

?>
