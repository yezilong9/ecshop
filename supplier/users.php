<?php

/**
 *  会员管理程序
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www..com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: users.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . '/includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);
/*------------------------------------------------------ */
//-- 用户帐号列表
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'list')
{
    /* 检查权限 */
	
    admin_priv('users_manage');
	/* 代理商条件 */
	$agency_where = agency_where();
	if(!$agency_where)
	{
		$agency_where = "AND admin_agency_id = 0";
	}
    $sql = "SELECT rank_id, rank_name, min_points FROM ".$ecs->table('user_rank')." WHERE 1 $agency_where ORDER BY min_points ASC ";
    $rs = $db->query($sql);

    $ranks = array();
    while ($row = $db->FetchRow($rs))
    {
        $ranks[$row['rank_id']] = $row['rank_name'];
    }
	/*add by hg*/
	$action_list = if_agency()?'all':'';
	$GLOBALS['smarty']->assign('all',        $action_list);
	$smarty->assign('agency_list',   agency_list());
	/*end*/
    $smarty->assign('user_ranks',   $ranks);
    $smarty->assign('ur_here',      $_LANG['03_users_list']);
    $smarty->assign('action_link',  array('text' => $_LANG['04_users_add'], 'href'=>'users.php?act=add'));

    $user_list = user_list();

    $smarty->assign('user_list',    $user_list['user_list']);
    $smarty->assign('filter',       $user_list['filter']);
    $smarty->assign('record_count', $user_list['record_count']);
    $smarty->assign('page_count',   $user_list['page_count']);
    $smarty->assign('full_page',    1);
    $smarty->assign('sort_user_id', '<img src="images/sort_desc.gif">');

    assign_query_info();
    $smarty->display('users_list.htm');
}

/*------------------------------------------------------ */
//-- ajax返回用户列表
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    $user_list = user_list();

    $smarty->assign('user_list',    $user_list['user_list']);
    $smarty->assign('filter',       $user_list['filter']);
    $smarty->assign('record_count', $user_list['record_count']);
    $smarty->assign('page_count',   $user_list['page_count']);

    $sort_flag  = sort_flag($user_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('users_list.htm'), '', array('filter' => $user_list['filter'], 'page_count' => $user_list['page_count']));
}

/*------------------------------------------------------ */
//-- 添加会员帐号
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add')
{
    /* 检查权限 */
    admin_priv('users_manage');

    $user = array(  'rank_points'   => $_CFG['register_points'],
                    'pay_points'    => $_CFG['register_points'],
                    'sex'           => 0,
                    'credit_line'   => 0
                    );
    /* 取出注册扩展字段 */
    $sql = 'SELECT * FROM ' . $ecs->table('reg_fields') . ' WHERE type < 2 AND display = 1 AND id != 6 ORDER BY dis_order, id';
    $extend_info_list = $db->getAll($sql);
    $smarty->assign('extend_info_list', $extend_info_list);
    $smarty->assign('ur_here',          $_LANG['04_users_add']);
    $smarty->assign('action_link',      array('text' => $_LANG['03_users_list'], 'href'=>'users.php?act=list'));
    $smarty->assign('form_action',      'insert');
    $smarty->assign('user',             $user);
	//print_r(get_rank_list(true));die;
    $smarty->assign('special_ranks',    get_rank_list(true));
	
	/*add by hg for date 2014-003-25 查找代理商与判断是否是最高管理员,是否为增加页 begin*/
	$rank_sql = "select user_id,user_name from ". $ecs->table('users') ."where user_rank = 4";
	$rankRow = $db->getAll($rank_sql);
	$smarty->assign('rankRow',      $rankRow);
	$action_list = if_agency()?'all':'';
	$smarty->assign('action_list',        $action_list);
	$smarty->assign('add',      $_REQUEST['act']);
	
	//地址
	$areaArr = $db->getAll("SELECT region_id,parent_id,region_name FROM " . $ecs->table('region'). " WHERE parent_id = 1"); 
	$smarty->assign('areaArr',      $areaArr);	
	/*end*/
    assign_query_info();
    $smarty->display('user_info.htm');
}

/*------------------------------------------------------ */
//-- 添加会员帐号
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'insert')
{
    /* 检查权限 */
    admin_priv('users_manage');
    $username = empty($_POST['username']) ? '' : trim($_POST['username']);
    $password = empty($_POST['password']) ? '' : trim($_POST['password']);
    $email = empty($_POST['email']) ? '' : trim($_POST['email']);
    $sex = empty($_POST['sex']) ? 0 : intval($_POST['sex']);
    $sex = in_array($sex, array(0, 1, 2)) ? $sex : 0;
    $birthday = $_POST['birthdayYear'] . '-' .  $_POST['birthdayMonth'] . '-' . $_POST['birthdayDay'];
    $rank = empty($_POST['user_rank']) ? 0 : intval($_POST['user_rank']);
    $rankRow = empty($_POST['rankRow']) ? 0 : intval($_POST['rankRow']);
    $credit_line = empty($_POST['credit_line']) ? 0 : floatval($_POST['credit_line']);

    $users =& init_users();

    if (!$users->add_user($username, $password, $email))
    {
        /* 插入会员数据失败 */
        if ($users->error == ERR_INVALID_USERNAME)
        {
            $msg = $_LANG['username_invalid'];
        }
        elseif ($users->error == ERR_USERNAME_NOT_ALLOW)
        {
            $msg = $_LANG['username_not_allow'];
        }
        elseif ($users->error == ERR_USERNAME_EXISTS)
        {
            $msg = $_LANG['username_exists'];
        }
        elseif ($users->error == ERR_INVALID_EMAIL)
        {
            $msg = $_LANG['email_invalid'];
        }
        elseif ($users->error == ERR_EMAIL_NOT_ALLOW)
        {
            $msg = $_LANG['email_not_allow'];
        }
        elseif ($users->error == ERR_EMAIL_EXISTS)
        {
            $msg = $_LANG['email_exists'];
        }
        else
        {
            //die('Error:'.$users->error_msg());
        }
        sys_msg($msg, 1);
    }

    /* 注册送积分 */
    if (!empty($GLOBALS['_CFG']['register_points']))
    {
        log_account_change($_SESSION['user_id'], 0, 0, $GLOBALS['_CFG']['register_points'], $GLOBALS['_CFG']['register_points'], $_LANG['register_points']);
    }

    /*把新注册用户的扩展信息插入数据库*/
    $sql = 'SELECT id FROM ' . $ecs->table('reg_fields') . ' WHERE type = 0 AND display = 1 ORDER BY dis_order, id';   //读出所有扩展字段的id
    $fields_arr = $db->getAll($sql);

    $extend_field_str = '';    //生成扩展字段的内容字符串
	
    $user_id_arr = $users->get_profile_by_name($username);
    foreach ($fields_arr AS $val)
    {
        $extend_field_index = 'extend_field' . $val['id'];
        if(!empty($_POST[$extend_field_index]))
        {
            $temp_field_content = strlen($_POST[$extend_field_index]) > 100 ? mb_substr($_POST[$extend_field_index], 0, 99) : $_POST[$extend_field_index];
            $extend_field_str .= " ('" . $user_id_arr['user_id'] . "', '" . $val['id'] . "', '" . $temp_field_content . "'),";
        }
		
		/* add by hg for date 2014-03-28 拼接地址*/
		if(!empty($_POST['agency_dz_shi']) && $val['id'] == 101 && $rank == 4)
		{
			$agency_dz_sheng = $db->getRow("SELECT region_name FROM " . $ecs->table('region'). " WHERE region_id =$_POST[agency_dz_sheng]"); 
			$agency_dz_shi = $db->getRow("SELECT region_name FROM " . $ecs->table('region'). " WHERE region_id = $_POST[agency_dz_shi]");
			$static_dz_sheng = strstr($extend_field_str,$agency_dz_sheng['region_name']);
			if(empty($static_dz_sheng))
			{
				$extend_field_str .= " ('" . $user_id_arr['user_id'] . "', '" . $val['id'] . "', '" . $agency_dz_sheng['region_name'].','.	$agency_dz_shi['region_name'] . "'),";
			}

		}
		/*end*/
		
		/*add by hg for date 2014-03-24  拼接img   begin*/
		if(!empty($_FILES[$extend_field_index]))
		{
			$img_name;
			$data = $_FILES[$extend_field_index];
			$imgArr = img_file($data);
			for($i =0;$i<=count($imgArr);$i++){
				$img_name .= $image->upload_image($imgArr[$i]).';';
			}
			$sub_img_name = substr($img_name,0,-2);
			if($sub_img_name != ';;'){
				$extend_field_str .= " ('" . $user_id_arr['user_id'] . "', '" . $val['id'] . "', '" . $sub_img_name . "'),";
				
			}
		}
		/*end*/
    }
    $extend_field_str = substr($extend_field_str, 0, -1);
    if ($extend_field_str)      //插入注册扩展数据
    {
        $sql = 'INSERT INTO '. $ecs->table('reg_extend_info') . ' (`user_id`, `reg_field_id`, `content`) VALUES' . $extend_field_str;
        $db->query($sql);
    }

    /* 更新会员的其它信息 */
    $other =  array();
    $other['credit_line'] = $credit_line;
    $other['user_rank']  = $rank;
    $other['top_rank']  = $rankRow;
	
    $other['sex']        = $sex;
    $other['birthday']   = $birthday;
    $other['reg_time'] = local_strtotime(local_date('Y-m-d H:i:s'));

    $other['msn'] = isset($_POST['extend_field1']) ? htmlspecialchars(trim($_POST['extend_field1'])) : '';
    $other['qq'] = isset($_POST['extend_field2']) ? htmlspecialchars(trim($_POST['extend_field2'])) : '';
    $other['office_phone'] = isset($_POST['extend_field3']) ? htmlspecialchars(trim($_POST['extend_field3'])) : '';
    $other['home_phone'] = isset($_POST['extend_field4']) ? htmlspecialchars(trim($_POST['extend_field4'])) : '';
    $other['mobile_phone'] = isset($_POST['extend_field5']) ? htmlspecialchars(trim($_POST['extend_field5'])) : '';
	$res = $db->getRow("select * from ".$ecs->table('users')." where user_name = '$username'");
    $db->autoExecute($ecs->table('users'), $other, 'UPDATE', "user_name = '$username'");
	
	/*add by hg for date 2014-03-25 为代理商的时候插入管理员表与更新上级代理商*/
	if($rank == 4 && empty($rankRow))
	{
		$user_row = $db->getRow('select user_id from '. $ecs->table('users') ." where user_name = '$username'");
		
		$password = MD5($password);
		$add_time = gmtime();
		//获取代理商角色
		$action_list = $db->getRow("select role_id,action_list from " .$ecs->table('role'). " where role_name = '代理商'");
		
		$sql = "SELECT nav_list FROM " . $ecs->table('admin_user') . " WHERE action_list = 'all'";
        $row = $db->getRow($sql);
        $sql = "INSERT INTO ".$ecs->table('admin_user')." (user_name, email, password, add_time, nav_list,agency_user_id,action_list,role_id) ".
           "VALUES ('".$username."', '".$email."', '$password', '$add_time', '$row[nav_list]','$user_row[user_id]','$action_list[action_list]',$action_list[role_id])";
		$db->query($sql);
		
		/*add by hg for date 2014-05-04 生成代理商商店设置 begin*/
		$shop_res = $db->getAll("select parent_id,code,type,store_range,store_dir,value,sort_order from ".$ecs->table('shop_config')." where parent_id = 1");
		foreach($shop_res as $shop_k=>$shop_v){
			$db->query("INSERT INTO ".$ecs->table('agency_shop_config')." (parent_id, code, type, store_range, store_dir,value,sort_order,admin_agency_id) ".
			   "VALUES ('$shop_v[parent_id]', '$shop_v[code]', '$shop_v[type]', '$shop_v[store_range]', '$shop_v[store_dir]','$shop_v[value]','$shop_v[sort_order]','$user_row[user_id]')");
		}
		
	}
	
	if(!if_agency()){
		//dump($rankRow);
		$admin_id = isset($_SESSION['admin_id'])?$_SESSION['admin_id']:0;
		$admin_user_id = $GLOBALS['db']->getOne("select agency_user_id from ".$ecs->table('admin_user')."where user_id = $admin_id" );
		$sql = "UPDATE " . $ecs->table('users') . "SET top_rank = '$admin_user_id' WHERE user_name = '$username'";
		$db->query($sql);
	}
	/*end*/
	
    /* 记录管理员操作 */
    admin_log($_POST['username'], 'add', 'users');
	admin_log($_POST['username'], 'add', 'admin_user');
    /* 提示信息 */
    $link[] = array('text' => $_LANG['go_back'], 'href'=>'users.php?act=list');
    sys_msg(sprintf($_LANG['add_success'], htmlspecialchars(stripslashes($_POST['username']))), 0, $link);

}

/*------------------------------------------------------ */
//-- 编辑用户帐号
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'edit')
{	//print_r($_SESSION);die;
    /* 检查权限 */
    admin_priv('users_manage');

    $sql = "SELECT u.user_name, u.sex, u.birthday, u.pay_points, u.rank_points, u.user_rank , u.user_money, u.frozen_money, u.credit_line, u.parent_id, u2.user_name as parent_username, u.qq, u.msn, u.office_phone, u.home_phone, u.mobile_phone".
        " FROM " .$ecs->table('users'). " u LEFT JOIN " . $ecs->table('users') . " u2 ON u.parent_id = u2.user_id WHERE u.user_id='$_GET[id]'";

    $row = $db->GetRow($sql);
    $row['user_name'] = addslashes($row['user_name']);
    $users  =& init_users();
    $user   = $users->get_user_info($row['user_name']);

    $sql = "SELECT u.user_id, u.sex, u.birthday, u.pay_points, u.rank_points, u.user_rank , u.user_money, u.frozen_money, u.credit_line, u.parent_id, u2.user_name as parent_username, u.qq, u.msn,u.top_rank,
    u.office_phone, u.home_phone, u.mobile_phone".
        " FROM " .$ecs->table('users'). " u LEFT JOIN " . $ecs->table('users') . " u2 ON u.parent_id = u2.user_id WHERE u.user_id='$_GET[id]'";

    $row = $db->GetRow($sql);
	$smarty->assign('op_user_rank',$row['user_rank']);

	
    if ($row)
    {
        $user['user_id']        = $row['user_id'];
        $user['sex']            = $row['sex'];
        $user['birthday']       = date($row['birthday']);
        $user['pay_points']     = $row['pay_points'];
        $user['rank_points']    = $row['rank_points'];
        $user['user_rank']      = $row['user_rank'];
        $user['user_money']     = $row['user_money'];
        $user['frozen_money']   = $row['frozen_money'];
        $user['credit_line']    = $row['credit_line'];
        $user['formated_user_money'] = price_format($row['user_money']);
        $user['formated_frozen_money'] = price_format($row['frozen_money']);
        $user['parent_id']      = $row['parent_id'];
        $user['parent_username']= $row['parent_username'];
        $user['qq']             = $row['qq'];
        $user['msn']            = $row['msn'];
        $user['office_phone']   = $row['office_phone'];
        $user['home_phone']     = $row['home_phone'];
        $user['mobile_phone']   = $row['mobile_phone'];
    }
    else
    {
          $link[] = array('text' => $_LANG['go_back'], 'href'=>'users.php?act=list');
          sys_msg($_LANG['username_invalid'], 0, $links);
//        $user['sex']            = 0;
//        $user['pay_points']     = 0;
//        $user['rank_points']    = 0;
//        $user['user_money']     = 0;
//        $user['frozen_money']   = 0;
//        $user['credit_line']    = 0;
//        $user['formated_user_money'] = price_format(0);
//        $user['formated_frozen_money'] = price_format(0);
     }

    /* 取出注册扩展字段 */
    $sql = 'SELECT * FROM ' . $ecs->table('reg_fields') . ' WHERE type < 2 AND display = 1 AND id != 6 ORDER BY dis_order, id';
    $extend_info_list = $db->getAll($sql);

    $sql = 'SELECT reg_field_id, content ' .
           'FROM ' . $ecs->table('reg_extend_info') .
           " WHERE user_id = $user[user_id]";
    $extend_info_arr = $db->getAll($sql);

    $temp_arr = array();
    foreach ($extend_info_arr AS $val)
    {
        $temp_arr[$val['reg_field_id']] = $val['content'];
    }

    foreach ($extend_info_list AS $key => $val)
    {
        switch ($val['id'])
        {
            case 1:     $extend_info_list[$key]['content'] = $user['msn']; break;
            case 2:     $extend_info_list[$key]['content'] = $user['qq']; break;
            case 3:     $extend_info_list[$key]['content'] = $user['office_phone']; break;
            case 4:     $extend_info_list[$key]['content'] = $user['home_phone']; break;
            case 5:     $extend_info_list[$key]['content'] = $user['mobile_phone']; break;
            default:    $extend_info_list[$key]['content'] = empty($temp_arr[$val['id']]) ? '' : $temp_arr[$val['id']] ;
        }
    }
	//print_r($extend_info_list);die;
	/*add by hg for date 2014-03-24 拆分img begin 判断是否是代理商*/
	foreach($extend_info_list as $key=>$value){
		if($value['upload'] == 1)
		{
			$img_name = explode(';',$value['content']);
			$extend_info_list[$key]['content'] = $img_name;
		}
		$strnum = strstr($value['content'],',');
		if(!empty($strnum))
		{
			$content_site = explode(',',$value['content']);
			$extend_info_list[$key]['sheng'] = $content_site[0];
			$siteRow = $db->getRow("SELECT region_id FROM " . $ecs->table('region'). " WHERE region_name = '$content_site[0]'");
			$extend_info_list[$key]['shi'] = $content_site[1];
		}
	}
	//地址
	$areaArr = $db->getAll("SELECT region_id,parent_id,region_name FROM " . $ecs->table('region'). " WHERE parent_id = 1"); 
	if($siteRow['region_id'])
	{
		$siteArr = $db->getAll("SELECT region_id,parent_id,region_name FROM " . $ecs->table('region'). " WHERE parent_id = $siteRow[region_id]");
	}
	$smarty->assign('areaArr',      $areaArr);	
	$smarty->assign('siteArr',      $siteArr);	
	/**/		   
	
    $smarty->assign('extend_info_list', $extend_info_list);

	$top_rank = $db->getRow("select agency_user_id from " .$ecs->table('admin_user'). "where agency_user_id = $_GET[id]");
	$smarty->assign('top_rank',$top_rank['agency_user_id']);
	/*end*/
	
    /* 当前会员推荐信息 */
    $affiliate = unserialize($GLOBALS['_CFG']['affiliate']);
    $smarty->assign('affiliate', $affiliate);

    empty($affiliate) && $affiliate = array();

    if(empty($affiliate['config']['separate_by']))
    {
        //推荐注册分成
        $affdb = array();
        $num = count($affiliate['item']);
        $up_uid = "'$_GET[id]'";
        for ($i = 1 ; $i <=$num ;$i++)
        {
            $count = 0;
            if ($up_uid)
            {
                $sql = "SELECT user_id FROM " . $ecs->table('users') . " WHERE parent_id IN($up_uid)";
                $query = $db->query($sql);
                $up_uid = '';
                while ($rt = $db->fetch_array($query))
                {
                    $up_uid .= $up_uid ? ",'$rt[user_id]'" : "'$rt[user_id]'";
                    $count++;
                }
            }
            $affdb[$i]['num'] = $count;
        }
        if ($affdb[1]['num'] > 0)
        {
            $smarty->assign('affdb', $affdb);
        }
    }

    assign_query_info();
    $smarty->assign('ur_here',          $_LANG['users_edit']);
    $smarty->assign('action_link',      array('text' => $_LANG['03_users_list'], 'href'=>'users.php?act=list&' . list_link_postfix()));
    $smarty->assign('user',             $user);
    $smarty->assign('form_action',      'update');
    $smarty->assign('special_ranks',    get_rank_list(true));
	/*add by hg for date 2014-03-25*/
	$rank_sql = "select user_id,user_name from ". $ecs->table('users') ."where user_rank = 4";
	$rankRow = $db->getAll($rank_sql);
	$smarty->assign('rankRow',      $rankRow);
	$rank_sql = "select user_name from ". $ecs->table('users') ."where user_id = $row[top_rank]";
	$top_rank_name = $db->getRow($rank_sql);
	//print_r($top_rank_name);die;
	if($_SESSION['admin_name'] != $top_rank_name['user_name'] && if_agency())
	{
		$smarty->assign('top_rank_name',      $top_rank_name);
	} 
	$action_list = if_agency()?'all':'';
	$smarty->assign('action_list',      $action_list);
	/*end*/
	//print_r(get_rank_list(true));die;
    $smarty->display('user_info.htm');
}

/*------------------------------------------------------ */
//-- 更新用户帐号
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'update')
{
    /* 检查权限 */
    admin_priv('users_manage');
    $username = empty($_POST['username']) ? '' : trim($_POST['username']);
    $password = empty($_POST['password']) ? '' : trim($_POST['password']);
    $email = empty($_POST['email']) ? '' : trim($_POST['email']);
    $sex = empty($_POST['sex']) ? 0 : intval($_POST['sex']);
    $sex = in_array($sex, array(0, 1, 2)) ? $sex : 0;
    $birthday = $_POST['birthdayYear'] . '-' .  $_POST['birthdayMonth'] . '-' . $_POST['birthdayDay'];
    $rank = empty($_POST['user_rank']) ? 0 : intval($_POST['user_rank']);
	$rankRow = empty($_POST['rankRow']) ? 0 : intval($_POST['rankRow']);
    $credit_line = empty($_POST['credit_line']) ? 0 : floatval($_POST['credit_line']);
	//print_r($rank);die;
    $users  =& init_users();
	/*通知TM平台修改密码信息*/
	$tm_mark = $db->getOne("select tm_mark from ".$ecs->table('users')." where user_name = '$username'");
	if($tm_mark == '1')
	{
		$tm_user = new tm_user();
		$state    = $tm_user->update_pwd(substr($username,3),$password);
		if($state == '0')
		{
			sys_msg('修改密码失败', 0, $links);
		}
	}
	/*end*/
	
    if (!$users->edit_user(array('username'=>$username, 'password'=>$password, 'email'=>$email, 'gender'=>$sex, 'bday'=>$birthday ), 1))
    {
        if ($users->error == ERR_EMAIL_EXISTS)
        {
            $msg = $_LANG['email_exists'];
        }
        else
        {
            $msg = $_LANG['edit_user_failed'];
        }
        sys_msg($msg, 1);
    }
    if(!empty($password))
    {
			$sql="UPDATE ".$ecs->table('users'). "SET `ec_salt`='0' WHERE user_name= '".$username."'";
			$db->query($sql);
	}
    /* 更新用户扩展字段的数据 */
    $sql = 'SELECT id FROM ' . $ecs->table('reg_fields') . ' WHERE type = 0 AND display = 1 ORDER BY dis_order, id';   //读出所有扩展字段的id
    $fields_arr = $db->getAll($sql);
    $user_id_arr = $users->get_profile_by_name($username);
    $user_id = $user_id_arr['user_id'];

    foreach ($fields_arr AS $val)       //循环更新扩展用户信息
    {
        $extend_field_index = 'extend_field' . $val['id'];
		 $sql = 'SELECT id,content FROM ' . $ecs->table('reg_extend_info') . "  WHERE reg_field_id = '$val[id]' AND user_id = '$user_id'";	 
		 $static = $db->getRow($sql);
        if(isset($_POST[$extend_field_index]))
        {
            $temp_field_content = strlen($_POST[$extend_field_index]) > 100 ? mb_substr($_POST[$extend_field_index], 0, 99) : $_POST[$extend_field_index];
			if(!empty($temp_field_content)){
				if ($static)      //如果之前没有记录，则插入
				{
					$sql = 'UPDATE ' . $ecs->table('reg_extend_info') . " SET content = '$temp_field_content' WHERE reg_field_id = '$val[id]' AND user_id = '$user_id'";
				}
				else
				{
					$sql = 'INSERT INTO '. $ecs->table('reg_extend_info') . " (`user_id`, `reg_field_id`, `content`) VALUES ('$user_id', '$val[id]', '$temp_field_content')";
				}
				$db->query($sql);
			}
        } 
		/*add by hg for date 2014-03-24 更新证照信息 begin*/
		if(isset($_FILES[$extend_field_index]))
		{
			$img_name;
			$data = $_FILES[$extend_field_index];
			
			$imgArr = img_file($data);
			for($i =0;$i<=count($imgArr)-1;$i++){
				$imgName = $image->upload_image($imgArr[$i]).';';
				$img_name .= $imgName;
			}
			$img_name = substr($img_name,0,-1);
			$new_img = explode(';',$img_name);
			$my_img = explode(';',$static['content']);
			//print_r($my_img);die;
			for($i=0;$i<=count($new_img)-1;$i++){
				$new_img[$i] = str_replace(' ','',$new_img[$i]);
				if(!empty($new_img[$i])){
					@unlink("../$my_img[$i]");
					$my_img[$i] = $new_img[$i];
				}
			}
			$content;
			foreach($my_img as $k=>$v){
				$content .= $v.';';
			}
			$content = substr($content,0,-1);
			//print_r($content);die;
			if(!empty($content)){
				if ($static)      //如果之前没有记录，则插入
				{
					$img_name = explode(';',$static['content']);
					
					$sql = 'UPDATE ' . $ecs->table('reg_extend_info') . " SET content = '$content' WHERE reg_field_id = '$val[id]' AND user_id = '$user_id'";
				}
				else
				{
					$sql = 'INSERT INTO '. $ecs->table('reg_extend_info') . " (`user_id`, `reg_field_id`, `content`) VALUES ('$user_id', '$val[id]', '$content')";
				}
				$db->query($sql);
			}
		}
		/*end*/
    }


    /* 更新会员的其它信息 */
    $other =  array();
    $other['credit_line'] = $credit_line;
    $other['user_rank'] = $rank;
	$other['top_rank'] = $rankRow;
    $other['msn'] = isset($_POST['extend_field1']) ? htmlspecialchars(trim($_POST['extend_field1'])) : '';
    $other['qq'] = isset($_POST['extend_field2']) ? htmlspecialchars(trim($_POST['extend_field2'])) : '';
    $other['office_phone'] = isset($_POST['extend_field3']) ? htmlspecialchars(trim($_POST['extend_field3'])) : '';
    $other['home_phone'] = isset($_POST['extend_field4']) ? htmlspecialchars(trim($_POST['extend_field4'])) : '';
    $other['mobile_phone'] = isset($_POST['extend_field5']) ? htmlspecialchars(trim($_POST['extend_field5'])) : '';

    $db->autoExecute($ecs->table('users'), $other, 'UPDATE', "user_name = '$username'");
	
	/*add by hg for date 2014-03-25 为代理商的时候插入管理员表与更新上级代理商*/
	if($rank == 4)
	{
		$user_row = $db->getRow('select user_id from '. $ecs->table('users') ." where user_name = '$username'");
		
		$password = MD5($password);
		$add_time = gmtime();
		
		$sql = "SELECT nav_list FROM " . $ecs->table('admin_user') . " WHERE action_list = 'all'";
        $row = $db->getRow($sql);
	    $sql = "INSERT INTO ".$ecs->table('admin_user')." (user_name, email, password, add_time, nav_list,agency_user_id) ".
           "VALUES ('".$username."', '".$email."', '$password', '$add_time', '$row[nav_list]','$user_row[user_id]')";
		$db->query($sql);
	}
	if(!if_agency()){
		$admin_id = isset($_SESSION['admin_id'])?$_SESSION['admin_id']:0;
		$admin_user_id = $GLOBALS['db']->getRow("select agency_user_id from ".$ecs->table('admin_user')."where user_id = $admin_id" );
		$sql = "UPDATE " . $ecs->table('users') . "SET top_rank = '$admin_user_id[agency_user_id]' WHERE user_name = '$username'";
		$db->query($sql);
	}
	/*end*/
	
	
    /* 记录管理员操作 */
    admin_log($username, 'edit', 'users');

    /* 提示信息 */
    $links[0]['text']    = $_LANG['goto_list'];
    $links[0]['href']    = 'users.php?act=list&' . list_link_postfix();
    $links[1]['text']    = $_LANG['go_back'];
    $links[1]['href']    = 'javascript:history.back()';

    sys_msg($_LANG['update_success'], 0, $links);

}

/*------------------------------------------------------ */
//-- 批量删除会员帐号
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'batch_remove')
{
    /* 检查权限 */
    admin_priv('users_drop');

    if (isset($_POST['checkboxes']))
    {
        $sql = "SELECT user_name FROM " . $ecs->table('users') . " WHERE user_id " . db_create_in($_POST['checkboxes']);
        $col = $db->getCol($sql);
        $usernames = implode(',',addslashes_deep($col));
        $count = count($col);
        /* 通过插件来删除用户 */
        $users =& init_users();
        $users->remove_user($col);

        admin_log($usernames, 'batch_remove', 'users');

        $lnk[] = array('text' => $_LANG['go_back'], 'href'=>'users.php?act=list');
        sys_msg(sprintf($_LANG['batch_remove_success'], $count), 0, $lnk);
    }
    else
    {
        $lnk[] = array('text' => $_LANG['go_back'], 'href'=>'users.php?act=list');
        sys_msg($_LANG['no_select_user'], 0, $lnk);
    }
}

/* 编辑用户名 */
elseif ($_REQUEST['act'] == 'edit_username')
{
    /* 检查权限 */
    check_authz_json('users_manage');

    $username = empty($_REQUEST['val']) ? '' : json_str_iconv(trim($_REQUEST['val']));
    $id = empty($_REQUEST['id']) ? 0 : intval($_REQUEST['id']);

    if ($id == 0)
    {
        make_json_error('NO USER ID');
        return;
    }

    if ($username == '')
    {
        make_json_error($GLOBALS['_LANG']['username_empty']);
        return;
    }

    $users =& init_users();

    if ($users->edit_user($id, $username))
    {
        if ($_CFG['integrate_code'] != '')
        {
            /* 更新商城会员表 */
            $db->query('UPDATE ' .$ecs->table('users'). " SET user_name = '$username' WHERE user_id = '$id'");
        }

        admin_log(addslashes($username), 'edit', 'users');
        make_json_result(stripcslashes($username));
    }
    else
    {
        $msg = ($users->error == ERR_USERNAME_EXISTS) ? $GLOBALS['_LANG']['username_exists'] : $GLOBALS['_LANG']['edit_user_failed'];
        make_json_error($msg);
    }
}

/*------------------------------------------------------ */
//-- 编辑email
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_email')
{
    /* 检查权限 */
    check_authz_json('users_manage');

    $id = empty($_REQUEST['id']) ? 0 : intval($_REQUEST['id']);
    $email = empty($_REQUEST['val']) ? '' : json_str_iconv(trim($_REQUEST['val']));

    $users =& init_users();

    $sql = "SELECT user_name FROM " . $ecs->table('users') . " WHERE user_id = '$id'";
    $username = $db->getOne($sql);


    if (is_email($email))
    {
        if ($users->edit_user(array('username'=>$username, 'email'=>$email)))
        {
            admin_log(addslashes($username), 'edit', 'users');

            make_json_result(stripcslashes($email));
        }
        else
        {
            $msg = ($users->error == ERR_EMAIL_EXISTS) ? $GLOBALS['_LANG']['email_exists'] : $GLOBALS['_LANG']['edit_user_failed'];
            make_json_error($msg);
        }
    }
    else
    {
        make_json_error($GLOBALS['_LANG']['invalid_email']);
    }
}

/*------------------------------------------------------ */
//-- 删除会员帐号
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'remove')
{
    /* 检查权限 */
    admin_priv('users_drop');

    $sql = "SELECT user_name FROM " . $ecs->table('users') . " WHERE user_id = '" . $_GET['id'] . "'";
    $username = $db->getOne($sql);
    /* 通过插件来删除用户 */
    $users =& init_users();
    $users->remove_user($username); //已经删除用户所有数据

    /* 记录管理员操作 */
    admin_log(addslashes($username), 'remove', 'users');

    /* 提示信息 */
    $link[] = array('text' => $_LANG['go_back'], 'href'=>'users.php?act=list');
    sys_msg(sprintf($_LANG['remove_success'], $username), 0, $link);
}

/*------------------------------------------------------ */
//--  收货地址查看
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'address_list')
{
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $sql = "SELECT a.*, c.region_name AS country_name, p.region_name AS province, ct.region_name AS city_name, d.region_name AS district_name ".
           " FROM " .$ecs->table('user_address'). " as a ".
           " LEFT JOIN " . $ecs->table('region') . " AS c ON c.region_id = a.country " .
           " LEFT JOIN " . $ecs->table('region') . " AS p ON p.region_id = a.province " .
           " LEFT JOIN " . $ecs->table('region') . " AS ct ON ct.region_id = a.city " .
           " LEFT JOIN " . $ecs->table('region') . " AS d ON d.region_id = a.district " .
           " WHERE user_id='$id'";
    $address = $db->getAll($sql);
    $smarty->assign('address',          $address);
    assign_query_info();
    $smarty->assign('ur_here',          $_LANG['address_list']);
    $smarty->assign('action_link',      array('text' => $_LANG['03_users_list'], 'href'=>'users.php?act=list&' . list_link_postfix()));
    $smarty->display('user_address_list.htm');
}

/*------------------------------------------------------ */
//-- 脱离推荐关系
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'remove_parent')
{
    /* 检查权限 */
    admin_priv('users_manage');

    $sql = "UPDATE " . $ecs->table('users') . " SET parent_id = 0 WHERE user_id = '" . $_GET['id'] . "'";
    $db->query($sql);

    /* 记录管理员操作 */
    $sql = "SELECT user_name FROM " . $ecs->table('users') . " WHERE user_id = '" . $_GET['id'] . "'";
    $username = $db->getOne($sql);
    admin_log(addslashes($username), 'edit', 'users');

    /* 提示信息 */
    $link[] = array('text' => $_LANG['go_back'], 'href'=>'users.php?act=list');
    sys_msg(sprintf($_LANG['update_success'], $username), 0, $link);
}

/*------------------------------------------------------ */
//-- 查看用户推荐会员列表
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'aff_list')
{
    /* 检查权限 */
    admin_priv('users_manage');
    $smarty->assign('ur_here',      $_LANG['03_users_list']);

    $auid = $_GET['auid'];
    $user_list['user_list'] = array();

    $affiliate = unserialize($GLOBALS['_CFG']['affiliate']);
    $smarty->assign('affiliate', $affiliate);

    empty($affiliate) && $affiliate = array();

    $num = count($affiliate['item']);
    $up_uid = "'$auid'";
    $all_count = 0;
    for ($i = 1; $i<=$num; $i++)
    {
        $count = 0;
        if ($up_uid)
        {
            $sql = "SELECT user_id FROM " . $ecs->table('users') . " WHERE parent_id IN($up_uid)";
            $query = $db->query($sql);
            $up_uid = '';
            while ($rt = $db->fetch_array($query))
            {
                $up_uid .= $up_uid ? ",'$rt[user_id]'" : "'$rt[user_id]'";
                $count++;
            }
        }
        $all_count += $count;

        if ($count)
        {
            $sql = "SELECT user_id, user_name, '$i' AS level, email, is_validated, user_money, frozen_money, rank_points, pay_points, reg_time ".
                    " FROM " . $GLOBALS['ecs']->table('users') . " WHERE user_id IN($up_uid)" .
                    " ORDER by level, user_id";
            $user_list['user_list'] = array_merge($user_list['user_list'], $db->getAll($sql));
        }
    }

    $temp_count = count($user_list['user_list']);
    for ($i=0; $i<$temp_count; $i++)
    {
        $user_list['user_list'][$i]['reg_time'] = local_date($_CFG['date_format'], $user_list['user_list'][$i]['reg_time']);
    }

    $user_list['record_count'] = $all_count;

    $smarty->assign('user_list',    $user_list['user_list']);
    $smarty->assign('record_count', $user_list['record_count']);
    $smarty->assign('full_page',    1);
    $smarty->assign('action_link',  array('text' => $_LANG['back_note'], 'href'=>"users.php?act=edit&id=$auid"));

    assign_query_info();
    $smarty->display('affiliate_list.htm');
	
//异步拿下级地址
}elseif ($_REQUEST['act'] == 'dz'){
	if(empty($_GET['agency_dz_sheng']))
	{
		echo json_encode('0');
	}
	$areaArr = $db->getAll("SELECT region_id,parent_id,region_name FROM " . $ecs->table('region'). " WHERE parent_id = $_GET[agency_dz_sheng]"); 
	if($areaArr)
	{
		echo json_encode($areaArr);
	}
}

/**
 *  返回用户列表数据
 *
 * @access  public
 * @param
 *
 * @return void
 */
function user_list()
{
    $result = get_filter();
    if ($result === false)
    {
		/*add by hg for date 2014-03-25 bagein*/
		$admin_id = isset($_SESSION['admin_id'])?$_SESSION['admin_id']:0;
		$admin_user_id = $GLOBALS['db']->getRow("select agency_user_id from ".$GLOBALS['ecs']->table('admin_user')."where user_id = $admin_id" );
		/*end*/
		
		/*add by hg for date 2014-04-23 获取代理商信息 begin*/
		$filter['admin_agency_id'] = (!empty($_REQUEST['admin_agency_id'])) ? trim($_REQUEST['admin_agency_id']) : 0;
		$GLOBALS['smarty']->assign('agency_list',   agency_list());
		$GLOBALS['smarty']->assign('admin_agency_id',         $filter['admin_agency_id']);
		$action_list = if_agency()?'all':'';
		$GLOBALS['smarty']->assign('all',         $action_list);
	/*end*/
	
        /* 过滤条件 */
        $filter['keywords'] = empty($_REQUEST['keywords']) ? '' : trim($_REQUEST['keywords']);
        if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
        {
            $filter['keywords'] = json_str_iconv($filter['keywords']);
        }
        $filter['rank'] = empty($_REQUEST['rank']) ? 0 : intval($_REQUEST['rank']);
        $filter['pay_points_gt'] = empty($_REQUEST['pay_points_gt']) ? 0 : intval($_REQUEST['pay_points_gt']);
        $filter['pay_points_lt'] = empty($_REQUEST['pay_points_lt']) ? 0 : intval($_REQUEST['pay_points_lt']);

        $filter['sort_by']    = empty($_REQUEST['sort_by'])    ? 'user_id' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC'     : trim($_REQUEST['sort_order']);
		/*add by hg for date 2014-03-25 bagein*/
		if($admin_user_id['agency_user_id'] != null)
		{
			$ex_where = ' WHERE top_rank = '.$admin_user_id['agency_user_id'].'';
		}else{
			$ex_where = ' WHERE 1 = 1';
		}
		/*end*/
        if ($filter['keywords'])
        {
            $ex_where .= " AND user_name LIKE '%" . mysql_like_quote($filter['keywords']) ."%'";
        }
        if ($filter['rank'])
        {
            $sql = "SELECT min_points, max_points, special_rank FROM ".$GLOBALS['ecs']->table('user_rank')." WHERE rank_id = '$filter[rank]'";
            $row = $GLOBALS['db']->getRow($sql);
            if ($row['special_rank'] > 0)
            {
                /* 特殊等级 */
                $ex_where .= " AND user_rank = '$filter[rank]' ";
            }
            else
            {
                $ex_where .= " AND rank_points >= " . intval($row['min_points']) . " AND rank_points < " . intval($row['max_points']);
            }
        }
        if ($filter['pay_points_gt'])
        {
             $ex_where .=" AND pay_points >= '$filter[pay_points_gt]' ";
        }
        if ($filter['pay_points_lt'])
        {
            $ex_where .=" AND pay_points < '$filter[pay_points_lt]' ";
        }
		
		/*add by hg for date 2014-04-23 根据代理商筛选 begin*/
		if (!empty($filter['admin_agency_id']) && if_agency())
		{
			if($filter['admin_agency_id'] == 'top')
			{
				$ex_where .= ' AND user_id IN ('.implode(',',agency_list('1')).')';
			}
			else
			{
				$ex_where .= " AND top_rank = '$filter[admin_agency_id]'";
			}
			
		}elseif(if_agency()){
		
			$ex_where .= ' AND user_id not in ('.implode(',',agency_list('1')).') AND top_rank = 0';
		}
		/*end*/
		//dump($ex_where);
        $filter['record_count'] = $GLOBALS['db']->getOne("SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('users') . $ex_where);

        /* 分页大小 */
        $filter = page_and_size($filter);
        $sql = "SELECT user_id, user_name, email, is_validated, user_money, frozen_money, rank_points, pay_points, reg_time ".
                " FROM " . $GLOBALS['ecs']->table('users') . $ex_where .
                " ORDER by " . $filter['sort_by'] . ' ' . $filter['sort_order'] .
                " LIMIT " . $filter['start'] . ',' . $filter['page_size'];
		//print_r($sql);die;
        $filter['keywords'] = stripslashes($filter['keywords']);
        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }
	
    $user_list = $GLOBALS['db']->getAll($sql);
//print_r($sql);
//dump($user_list);
    $count = count($user_list);
    for ($i=0; $i<$count; $i++)
    {
        $user_list[$i]['reg_time'] = local_date($GLOBALS['_CFG']['date_format'], $user_list[$i]['reg_time']);
    }

    $arr = array('user_list' => $user_list, 'filter' => $filter,
        'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    return $arr;
}
/*add by hg for date 2014-03-24  begin*/
	function img_file($data)
	{	
		if(!is_array($data) || empty($data))
		{
			return false;exit;
		}
		$res_arr = Array();
		foreach($data as $key=>$value){
		$i = 0;
			foreach($value as $k=>$v){
				$res_arr[$i][$key] = $v; 
				$i++;
			}
		}
		return $res_arr;
	}
/*end*/
?>