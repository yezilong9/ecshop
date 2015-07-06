<?php

/**
 *  前台公用文件
 * ============================================================================
 * * 版权所有 2005-2012 广州新泛联数码有限公司，并保留所有权利。
 * 网站地址: http://www..com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: init.php 17217 2011-01-19 06:29:08Z liubo $
*/

//各域名共用一个 weichen 2014/12/24 10:02:07
ini_set('session.cookie_domain', 'ec.com');
if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));

if (__FILE__ == '')
{
    die('Fatal error code: 0');
}

/* 取得当前所在的根目录 */
define('ROOT_PATH', str_replace('includes/init.php', '', str_replace('\\', '/', __FILE__)));
//echo ROOT_PATH;
if (!file_exists(ROOT_PATH . 'data/install.lock') && !file_exists(ROOT_PATH . 'includes/install.lock')
    && !defined('NO_CHECK_INSTALL'))
{
    header("Location: ./install/index.php\n");

    exit;
}

/* 初始化设置 */
@ini_set('memory_limit',          '64M');
@ini_set('session.cache_expire',  180);
@ini_set('session.use_trans_sid', 0);
@ini_set('session.use_cookies',   1);
@ini_set('session.auto_start',    0);
@ini_set('display_errors',        1);


if (DIRECTORY_SEPARATOR == '\\')
{
    @ini_set('include_path', '.;' . ROOT_PATH);
}
else
{
    @ini_set('include_path', '.:' . ROOT_PATH);
}

require(ROOT_PATH . 'data/config.php');

if (defined('DEBUG_MODE') == false)
{
    define('DEBUG_MODE', 0);
}

if (PHP_VERSION >= '5.1' && !empty($timezone))
{
    date_default_timezone_set($timezone);
}

$php_self = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
if ('/' == substr($php_self, -1))
{
    $php_self .= 'index.php';
}
define('PHP_SELF', $php_self);

require(ROOT_PATH . 'includes/inc_constant.php');
require(ROOT_PATH . 'includes/cls_ecshop.php');
require(ROOT_PATH . 'includes/cls_error.php');
require(ROOT_PATH . 'includes/lib_time.php');
require(ROOT_PATH . 'includes/lib_base.php');
require(ROOT_PATH . 'includes/lib_common.php');
require(ROOT_PATH . 'includes/lib_main.php');
require(ROOT_PATH . 'includes/lib_insert.php');
require(ROOT_PATH . 'includes/lib_goods.php');
require(ROOT_PATH . 'includes/lib_article.php');
require(ROOT_PATH . 'includes/lib_ecmoban.php');

/* 对用户传入的变量进行转义操作。*/
if (!get_magic_quotes_gpc())
{
    if (!empty($_GET))
    {
        $_GET  = addslashes_deep($_GET);
    }
    if (!empty($_POST))
    {
        $_POST = addslashes_deep($_POST);
    }

    $_COOKIE   = addslashes_deep($_COOKIE);
    $_REQUEST  = addslashes_deep($_REQUEST);
}

/* 创建  对象 */
$ecs = new ECS($db_name, $prefix);
define('DATA_DIR', $ecs->data_dir());
define('IMAGE_DIR', $ecs->image_dir());

/* 初始化数据库类 */
require(ROOT_PATH . 'includes/cls_mysql.php');
$db = new cls_mysql($db_host, $db_user, $db_pass, $db_name);
$db->set_disable_cache_tables(array($ecs->table('sessions'), $ecs->table('sessions_data'), $ecs->table('cart')));
$db_host = $db_user = $db_pass = $db_name = NULL;

/* 创建错误处理对象 */
$err = new ecs_error('message.dwt');

/* 载入系统参数 */
$_CFG = load_config();

/* 当前网站地址 */
$present_url = agency_url();

#####   配置信息（放至所有配置网站一些信息）   #####################
$_CFG['o2o_img_url'] = 'http://'.$present_url;//.'/o2o/'

define('TMUSER','o2o');
/*配置天猫过来的用户名前缀*/

/* 网站图片地址 */
$img_arr = array(
				'http://img1.txd168.com',
				'http://img2.txd168.com',
				'http://img3.txd168.com'
				);
##### end ####################################

/* 判断是够是外网 */
if(strpos($present_url,'txd168.com') !== false)
	$_CFG['o2o_img_url'] = $img_arr;

/* add by hg for date 2014-05-04 重写$_CFG*/
agency_shop_config();

/* 载入语言文件 */
require(ROOT_PATH . 'languages/' . $_CFG['lang'] . '/common.php');

if ($_CFG['shop_closed'] == 1)
{
    /* 商店关闭了，输出关闭的消息 */
    header('Content-type: text/html; charset='.EC_CHARSET);

    die('<div style="margin: 150px; text-align: center; font-size: 14px"><p>' . $_LANG['shop_closed'] . '</p><p>' . $_CFG['close_comment'] . '</p></div>');
}

if (is_spider())
{
    /* 如果是蜘蛛的访问，那么默认为访客方式，并且不记录到日志中 */
    if (!defined('INIT_NO_USERS'))
    {
        define('INIT_NO_USERS', true);
        /* 整合UC后，如果是蜘蛛访问，初始化UC需要的常量 */
        if($_CFG['integrate_code'] == 'ucenter')
        {
             $user = & init_users();
        }
    }
    $_SESSION = array();
    $_SESSION['user_id']     = 0;
    $_SESSION['user_name']   = '';
    $_SESSION['email']       = '';
    $_SESSION['user_rank']   = 0;
    $_SESSION['discount']    = 1.00;
}

if (!defined('INIT_NO_USERS'))
{
    /* 初始化session */
    include(ROOT_PATH . 'includes/cls_session.php');

    $sess = new cls_session($db, $ecs->table('sessions'), $ecs->table('sessions_data'));

    define('SESS_ID', $sess->get_session_id());
}
if(isset($_SERVER['PHP_SELF']))
{
    $_SERVER['PHP_SELF']=htmlspecialchars($_SERVER['PHP_SELF']);
}
if (!defined('INIT_NO_SMARTY'))
{
    header('Cache-control: private');
    header('Content-type: text/html; charset='.EC_CHARSET);

    /* 创建 Smarty 对象。*/
    require(ROOT_PATH . 'includes/cls_template.php');
    $smarty = new cls_template;
	
	/*获取代理商关联user_id add by hg for date 2014-04-01*/
	$agency_where = agency_goods();
	
	$agency_user_id_arr = explode(' ',$agency_where);
	$agency_user_id = $agency_user_id_arr[2];
	if($agency_user_id)
	{
		$user_tpl = $db->getRow("select agency_template from " .$ecs->table('admin_user'). " where agency_user_id = $agency_user_id");
	}else{
		$user_tpl = $db->getRow("select agency_template from " .$ecs->table('admin_user'). " where agency_user_id is null or action_list = 'all'");
	}
	
	
	if(!empty($user_tpl['agency_template']))
	{
		//反序列化
		$user_tpl = unserialize($user_tpl['agency_template']);
		$_CFG['template'] = $user_tpl['tpl_name'];
		$_CFG['stylename'] = $user_tpl['tpl_fg'];
	}else{
		
	}
	//清理模板
	clear_all_files();
	/*end*/
	
    $smarty->cache_lifetime = $_CFG['cache_time'];
    $smarty->template_dir   = ROOT_PATH . 'themes/' . $_CFG['template'];
    $smarty->cache_dir      = ROOT_PATH . 'temp/caches';
    $smarty->compile_dir    = ROOT_PATH . 'temp/compiled';

    if ((DEBUG_MODE & 2) == 2)
    {
        $smarty->direct_output = true;
        $smarty->force_compile = true;
    }
    else
    {
        $smarty->direct_output = false;
        $smarty->force_compile = false;
    }

    $smarty->assign('lang', $_LANG);
    $smarty->assign('ecs_charset', EC_CHARSET);
    if (!empty($_CFG['stylename']))
    {
        $smarty->assign('ecs_css_path', 'themes/' . $_CFG['template'] . '/style_' . $_CFG['stylename'] . '.css');
    }
    else
    {
        $smarty->assign('ecs_css_path', 'themes/' . $_CFG['template'] . '/style.css');
    }
	$smarty->assign('css_path', 'themes/'. $_CFG['template'] . '/');
}

if (isset($smarty))
{	//print_r($_SESSION);
	$GLOBALS['smarty']->assign('loogo',$_CFG['shop_logo']);
	$GLOBALS['smarty']->assign('user',$_SESSION?$_SESSION:'0');
	$GLOBALS['smarty']->assign('is_store_user',$_SESSION['user_rank']?$_SESSION['user_rank']:'0');
	if($_SESSION['user_rank']){
		$sql_rank_name = 'SELECT rank_name FROM ' .$ecs->table('user_rank') .
                " WHERE rank_id = " . $_SESSION['user_rank'];
        $rank_name = $db->getOne($sql_rank_name);
	}
	$GLOBALS['smarty']->assign('rank_name',$rank_name);
}

if (!defined('INIT_NO_USERS'))
{
    /* 会员信息 */
    $user =& init_users();
    //dump($user);
    if (!isset($_SESSION['user_id']))
    {
        /* 获取投放站点的名称 */
        $site_name = isset($_GET['from'])   ? htmlspecialchars($_GET['from']) : addslashes($_LANG['self_site']);
        $from_ad   = !empty($_GET['ad_id']) ? intval($_GET['ad_id']) : 0;

        $_SESSION['from_ad'] = $from_ad; // 用户点击的广告ID
        $_SESSION['referer'] = stripslashes($site_name); // 用户来源

        unset($site_name);

        if (!defined('INGORE_VISIT_STATS'))
        {
            visit_stats();
        }
    }

    if (empty($_SESSION['user_id']))
    {
        if ($user->get_cookie())
        {
            /* 如果会员已经登录并且还没有获得会员的帐户余额、积分以及优惠券 */
            if ($_SESSION['user_id'] > 0)
            {
                update_user_info();
            }
        }
        else
        {
            $_SESSION['user_id']     = 0;
            $_SESSION['user_name']   = '';
            $_SESSION['email']       = '';
            $_SESSION['user_rank']   = 0;
            $_SESSION['discount']    = 1.00;
            if (!isset($_SESSION['login_fail']))
            {
                $_SESSION['login_fail'] = 0;
            }
        }
    }

    /* 设置推荐会员 */
    if (isset($_GET['u']))
    {
        set_affiliate();
    }

    /* session 不存在，检查cookie */
    if (!empty($_COOKIE['ECS']['user_id']) && !empty($_COOKIE['ECS']['password']))
    {
        // 找到了cookie, 验证cookie信息
        $sql = 'SELECT user_id, user_name, password ' .
                ' FROM ' .$ecs->table('users') .
                " WHERE user_id = '" . intval($_COOKIE['ECS']['user_id']) . "' AND password = '" .$_COOKIE['ECS']['password']. "'";

        $row = $db->GetRow($sql);

        if (!$row)
        {
            // 没有找到这个记录
           $time = time() - 3600;
           setcookie("ECS[user_id]",  '', $time, '/');
           setcookie("ECS[password]", '', $time, '/');
        }
        else
        {
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['user_name'] = $row['user_name'];
            update_user_info();
        }
    }

    if (isset($smarty))
    {
        $smarty->assign('ecs_session', $_SESSION);
    }
}

/*初始化获取用户IP地址，得到用户所在的城市名称*/
if (!defined('INIT_NO_SMARTY'))
{
    if($_REQUEST['city_id']){
		$city_name=$_REQUEST['city_name'];
		$city_id=intval($_REQUEST['city_id']);
	}else{
		$ip_city=ipCity();
		$ip_city_name = $ip_city['city'];
		$str_len=(strlen($ip_city_name)-3)/3;
		$city_name = mb_substr($ip_city_name,0,$str_len,'utf-8');
		$sql_city_id = "SELECT region_id FROM ". $ecs->table('region') ." WHERE region_type=2  AND  region_name='$city_name' ";
		$city_id = $db->getOne($sql_city_id);
	}
	/* 根据域名显示地址 */
	$area_name = $db->getOne("SELECT area_name FROM ". $ecs->table('agency_url') ." WHERE agency_url = '$present_url'");
	$city_name = $area_name?$area_name:$city_name;
	$city_name_url = "region.php?act=change_city&city_name={$city_name}&city_id={$city_id}";
	$smarty->assign('city_name_url', $city_name_url);
	$smarty->assign('city_id', $city_id);
	$smarty->assign('city_name', $city_name);
}

if ((DEBUG_MODE & 1) == 1)
{
    error_reporting(E_ALL);
}
else
{
    error_reporting(E_ALL ^ (E_NOTICE | E_WARNING)); 
}
if ((DEBUG_MODE & 4) == 4)
{
    include(ROOT_PATH . 'includes/lib.debug.php');
}

/* 判断是否支持 Gzip 模式 */
if (!defined('INIT_NO_SMARTY') && gzip_enabled())
{
    ob_start('ob_gzhandler');
}
else
{
    ob_start();
}
/*头部二维码 add by hg 2014-07-22*/
$code_url = $present_url;
//goods_id其实是二维码图片名
$code_url = 'http://'.$code_url.'/codeImg.php?url=http://'.$code_url.'&goods_id='.$code_url;
if (isset($smarty))
{
	$smarty->assign('code_url',$code_url);
}

/* 广告赋值 */
if (isset($smarty))
{
	$obj_ad = class_ad::new_ad();
	$show_ad = $obj_ad->get_res();
	//dump($show_ad);
	$smarty->assign('show_ad',$show_ad);
	
}


/* 第三方登录显示 */
if(isset($smarty)){
	$login_domain = array();
	$login_domain = array('localhost','www.txd168.com','tg01.txd168.com','lc.txd168.com');
	$oath_login = in_array($_SERVER['HTTP_HOST'],$login_domain)?1:0;
	$smarty->assign('oath_login',$oath_login);
}



?>