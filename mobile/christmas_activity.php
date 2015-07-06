<?php
    define('IN_ECS', true);
    require(dirname(__FILE__) . '/includes/init.php');
    require(ROOT_PATH . 'includes/lib_order.php');
    
    //echo $user_id;
    $action = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : '';
    if($action =='act_login')
    {
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        $password = isset($_POST['password']) ? trim($_POST['password']) : '';
        $back_act = isset($_POST['back_act']) ? trim($_POST['back_act']) : '';
        
        $captcha = intval($_CFG['captcha']);

        if (($captcha & CAPTCHA_LOGIN) && (!($captcha & CAPTCHA_LOGIN_FAIL) || (($captcha & CAPTCHA_LOGIN_FAIL) && $_SESSION['login_fail'] > 2)) && gd_version() > 0)
        {
            if (empty($_POST['captcha']))
            {
                show_message($_LANG['invalid_captcha'], $_LANG['relogin_lnk'], 'user.php', 'error');
            }
    
            /* 检查验证码 */
            include_once('includes/cls_captcha.php');
    
            $validator = new captcha();
            $validator->session_word = 'captcha_login';
            if (!$validator->check_word($_POST['captcha']))
            {
                show_message($_LANG['invalid_captcha'], $_LANG['relogin_lnk'], 'user.php', 'error');
            }
        }

        if ($user->login($username, $password,isset($_POST['remember'])))
        {
            	
            update_user_info();
            recalculate_price();
    
            $ucdata = isset($user->ucdata)? $user->ucdata : '';
            //show_message($_LANG['login_success'] . $ucdata , array($_LANG['back_up_page'], $_LANG['profile_lnk']), array($back_act,'user.php'), 'info');
            $user_id = $_SESSION['user_id'];
            $smarty->assign('user_id', $user_id);
            $smarty->assign('css_path', 'themes/default/christmas_activity/');
            $smarty->display('/christmas_activity/christmas_activity.dwt');
            echo "<script>alert('登录成功');</script>";
        }
        else
        {
            /*ccx 2014-12-20 判断手机号码是否已经存在*/
            $sql_username = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('users') .
                            " WHERE user_name = '$username'";             
            $res = $GLOBALS['db']->getOne($sql_username);
            if ($res == 0)
            {
                $smarty->assign('css_path', 'themes/default/christmas_activity/');
                $smarty->display('/christmas_activity/christmas_activity.dwt'); 
                echo "<script>if(confirm('该用户不存在,请去注册页面注册账号再进行购物,谢谢合作')){window.location.href='user.php?act=register&activity=christmas_activity'}</script>";
                exit;
            }
            
            /*ccx 2014-12-20 */
            
            $user_id = $_SESSION['user_id'];
            $smarty->assign('user_id', $user_id);
            $smarty->assign('css_path', 'themes/default/christmas_activity/');
            $smarty->display('/christmas_activity/christmas_activity.dwt');
            $_SESSION['login_fail'] ++ ;
            //show_message($_LANG['login_failure'], $_LANG['relogin_lnk'], 'user.php', 'error');
            echo "<script>alert('登录失败,请输入正确的用户名跟密码');</script>";
            
            
        }
    }
    elseif($action =='select_goods')
    {
         $goods_id = trim($_GET['goods_id']);
         $goods = get_goods_info($goods_id);
         //print_r($goods);
         $shop_price = $goods['shop_price'];
         echo $shop_price;
    }
    elseif($action =='addtocar')
    {
        include_once(ROOT_PATH . 'includes/lib_transaction.php');
        $goods_id = trim($_GET['goods_id']);
        $addr_radio = trim($_GET['addr_radio']);
        
        //echo $goods_id;  echo $addr_radio;
        $result = array();
        // 更新：添加到购物车
        if (addto_cart($goods_id, 1, $result, ""))
        {
            //echo "成功";
            if ($_CFG['cart_confirm'] > 2)
            {
                $result['message'] = '';
            }
            else
            {
                $result['message'] = $_CFG['cart_confirm'] == 1 ? $_LANG['addto_cart_success_1'] : $_LANG['addto_cart_success_2'];
            }

            $result['content'] = insert_cart_info();
            $result['one_step_buy'] = $_CFG['one_step_buy'];
        }
        else 
        {
            echo "false";exit;
        }
        //ccx 2014-12-17 获取会员表当中的用户的用户名和电话
        $sql = "SELECT user_name, mobile_phone FROM " . $ecs->table('users') . " WHERE user_id = ".$_SESSION['user_id'];
        $user_message = $db->getRow($sql); 
        $user_name = $user_message['user_name'];
        if($user_message['mobile_phone'] =='')
        {
           $phone = "02010086"; 
        }
        else 
        {
            $phone = $user_message['mobile_phone'];
        }
         
        /*ccx 2014-12-17 开始 如果有收获人的姓名,地址电话, 并且会员当中的收货地址不存在的情况,那么就往会员收货地址写入数据 */
        if($addr_radio ==7)
        {
            /*ccx 如果不是自提的, 需要获取相应的收货人的姓名,手机,地址 开始*/
            $address     = trim($_GET['address']);
            $user_name   = trim($_GET['user_name']);
            $phone       = trim($_GET['phone']);
            /*ccx 结束*/
        }
        elseif($addr_radio == 1 )
        {
            $address    = "大学城广大菊苑饭堂斜对面（报亭隔壁），12月22-24日每日16:00-18:00";
        }
        elseif($addr_radio == 2)
        {
            $address  = "大学城广美天桥，12月22-24日每日17:00-18:00";
        }
        elseif($addr_radio == 3)
        {
            $address  = "大学城中大3饭广场，12月22-23日每日20:00-21:00";
        }
        elseif($addr_radio == 4)
        {
            $address  = "大学城广外学生活动中心一楼(自助圈存机旁), 12月22-23日每日20:00-21:00";
        }
        elseif($addr_radio == 5)
        {
            $address  = "大学城广东药学院2饭, 12月22-24日每日17:00-18:00";
        }
        elseif($addr_radio == 6)
        {
            $address  = "大学城广工3饭保安亭，12月23日20:00-21:00";
        }
        elseif($addr_radio == 8)
        {
            $address  = "大学城（华师+星海）地点星海宿舍C栋，12月22-23日18:00-20:00,24日15:00-17:00";
        }
        elseif($addr_radio == 9)
        {
            $address  = "大学城广工3饭堂保安亭，12月22日12月24日18:00-19:30,23日包送货";
        }
        
        $address_number = "SELECT count(*) FROM ".$ecs->table('user_address').
                      " WHERE address='".$address."' AND user_id=".$_SESSION['user_id']; 
        $user_address_number = $db->getOne($address_number); 
        if($user_address_number == 0) //收货地址表那里不存在这个地址,就写入到数据库当中
        {
           $consignee = array(
                'address_id'    => empty($_POST['address_id']) ? 0  :   intval($_POST['address_id']),
                'consignee'     => $user_name,
                'country'       => 1,
                'province'      => 6,
                'city'          => 76,
                'district'      => 700,
                'email'         => "daxuecheng@qq.com",
                'address'       => $address,
                'zipcode'       => 510006,
                'tel'           => $phone,
                'mobile'        => '',
                'sign_building' => '',
                'best_time'     => '',
            );
            
            /* 如果用户已经登录，则保存收货人信息 */
            $consignee['user_id'] = $_SESSION['user_id'];
            save_consignee($consignee, true);
            /*ccx 2014-12-17 保存收货地址 */
        }
        /*ccx 2014-12-17 结束  */
        
        $message_url = trim($_GET['message_url']);
        if($goods_id == 10721 || $goods_id == 10783 || $goods_id == 10784 || $goods_id == 10785 )
        {
            if($message_url != "")
            {
                echo $message_url;exit;
            } 
        }
       
        exit;
    }
    
    elseif($action =='nv')
    {
        $smarty->assign('css_path', 'themes/default/christmas_activity/');
        $value_send = $_REQUEST['message'];
        if($value_send == 'nvshen')
        {
            $smarty->display('/christmas_activity/gift1_nvshen.dwt');  
        }
        elseif($value_send == 'jiyou')
        {
            $smarty->display('/christmas_activity/gift1_jiyou.dwt'); 
        }
        elseif($value_send == 'guimi')
        {
            $smarty->display('/christmas_activity/gift1_guimi.dwt'); 
        }
        elseif($value_send == 'nanshen')
        {
            $smarty->display('/christmas_activity/gift1_nanshen.dwt'); 
        }
        elseif($value_send == 'laoshi')
        {
            $smarty->display('/christmas_activity/gift1_laoshi.dwt'); 
        }
        elseif($value_send == 'xuezha')
        {
            $smarty->display('/christmas_activity/gift1_xuezha.dwt'); 
        }
        else 
        {
            $smarty->display('/christmas_activity/christmas_activity.dwt'); 
        }
        
    }
    
    
    else 
    {
        $user_id = $_SESSION['user_id'];
        $smarty->assign('user_id', $user_id);
        $smarty->assign('css_path', 'themes/default/christmas_activity/');
        $smarty->assign('url_request', 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
        $smarty->display('/christmas_activity/christmas_activity.dwt');
    
    
    }
   
?>