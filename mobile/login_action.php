<?php
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');

$action   = $_REQUEST['act'];
if($action == 'login')
{
    $username = $_REQUEST['username'];
    $password = $_REQUEST['password'];
    
    if ($user->login($username, $password,isset($_POST['remember'])))
    {
        update_user_info();
        recalculate_price();
        $ucdata = isset($user->ucdata)? $user->ucdata : '';
        
        /* 通知APP */
        $obj = new lu_compile();
        $code = $obj->encrypt($_SESSION['user_id'].'-'.$_SESSION['user_name']);
        if(isset($_SESSION['APP']))
        header("Location:txd://userinfo?$code");
        echo json_encode("success");  
    }
    else
    {
        $_SESSION['login_fail'] ++ ;
        
        /*ccx 2014-12-29 start 登陆失败,判断该用户名是否已经被注册了*/
        
        include_once(ROOT_PATH . 'includes/lib_passport.php');
        $username = json_str_iconv($username);    
        if ($user->check_user($username) || admin_registered($username))
        {
            echo json_encode("fail_1");  
        }
        else
        {
            echo json_encode("fail_2");  
        }
        
        /*ccx 2014-12-29 end */
        
        //echo json_encode("fail");
    }
}

?>