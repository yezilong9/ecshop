<?php
/********************************************
快讯打印页面

time:2014-07-07

********************************************/
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
$class_print = new class_print();
$smarty->assign('helps',      get_shop_help());       // 网店帮助
$smarty->assign('print',      'print');
assign_template('', '');//页面底部信息
if($_REQUEST['act'] == 'index')
{
	print_user();
	$smarty->assign('act','index');
	$smarty->assign('user_id',$_SESSION['user_id']);
}
elseif($_REQUEST['act'] == 'var_normal')//普通版
{
	  print_user();
	$smarty->assign('act','var_normal');
}
elseif($_REQUEST['act'] == 'var_simple')//A4版
{
	  print_user();
	$smarty->assign('act','var_simple');
}
elseif($_REQUEST['act'] == 'old')//选择产品
{
	print_user();
	if(isset($_REQUEST['del']) && $_REQUEST['del'] == '1')
		unset($_SESSION[$_REQUEST['p']]);
	$smarty->assign('m',$_REQUEST['m']?$_REQUEST['m']:'var_'.substr($_REQUEST['p'],0,6));
	$smarty->assign('act',$_REQUEST['act']);
	$smarty->assign('panel',$_REQUEST['p']);
}
elseif($_REQUEST['act'] == 'choose')//显示选择普通版的正面产品
{
	  print_user();
	//unset($_SESSION);
	$search     = isset($_REQUEST['search'])?$_REQUEST['search']:'';
	$page       = isset($_REQUEST['page'])?$_REQUEST['page']:'';
	$cat_id     = isset($_REQUEST['cat_id'])?$_REQUEST['cat_id']:(!$search?'1':'');//默认童装
	$panel      = isset($_REQUEST['panel'])?$_REQUEST['panel']:'';
	$url        = '?'.urldecode($_SERVER['QUERY_STRING']);
	$goods_message = $class_print->category_goods($cat_id,$_SESSION[$panel],$search,$url,$page);
	$html = $class_print->print_goods_html($_SESSION[$panel],$panel);
	$smarty->assign('search',$search);
	$smarty->assign('html',$html);
	$smarty->assign('category',get_categories_tree(1));
	$smarty->assign('panel',$panel);
	$smarty->assign('page_html',$goods_message['page_html']);
	$smarty->assign('goods_list',$goods_message['goods_list']);
	$smarty->assign('cat_id',$cat_id);
	$smarty->assign('act',$_REQUEST['act']);
}
elseif($_REQUEST['act'] == 'choose_add')//选择并添加商品
{
	  print_user();
	$goods_id = isset($_REQUEST['goods_id'])?(int) ($_REQUEST['goods_id']):'0';
	$panel    = isset($_REQUEST['panel'])?$_REQUEST['panel']:'';
	//unset($_SESSION[$panel]);
	$html = '';
	if(!$goods_id || !$panel){
		$msg = '0';
	}else{
		if(in_array($goods_id,$_SESSION[$panel]))//已添加
		{
			$msg = '1';
			unset($_SESSION[$panel][array_search($goods_id,$_SESSION[$panel])]);
			$html = $class_print->print_goods_html($_SESSION[$panel],$panel);
		}
		elseif(count($_SESSION[$panel]) == $class_print->count_print_goods($panel))//数量达上限
		{
			$msg = '2';
		}
		else//添加成功
		{
			$_SESSION[$panel][] = $goods_id;
			$html = $class_print->print_goods_html($_SESSION[$panel],$panel);
			$msg = '3';
		}
	}
	echo json_encode(array('msg'=>$msg,'html'=>$html));exit;
}
elseif($_REQUEST['act'] == 'preview')//预览
{
	print_user();
	$panel = isset($_REQUEST['p'])?$_REQUEST['p']:'';
	$preview_html = $class_print->panel_html($panel,$_SESSION[$panel]);
	$smarty->assign('preview_html',$preview_html);
	$smarty->assign('panel',$panel);
	$smarty->assign('act',$_REQUEST['act']);
}
elseif($_REQUEST['act'] == 'peint_sort')//商品排序
{
	print_user();
	$panel   = isset($_REQUEST['panel'])?$_REQUEST['panel']:'';
	$from_id = isset($_REQUEST['from_id'])?$_REQUEST['from_id']-1:'';
	$to_id   = isset($_REQUEST['to_id'])?$_REQUEST['to_id']-1:'';
	if(empty($panel))
	{
		$msg = 0;
	}
	else
	{
		$print_to = $_SESSION[$panel][$to_id];
		$print_from = $_SESSION[$panel][$from_id];
		if(!$print_to || !$print_from)
		{
			$msg = 0;
		}
		else
		{
			$_SESSION[$panel][$from_id] = $print_to;
			$_SESSION[$panel][$to_id]   = $print_from;
			$msg = 1;
		}
	}
	echo json_encode(array('msg'=>$msg));exit;
}
elseif($_REQUEST['act'] == 'view_full')//预览大图
{
	
	$panel     = isset($_REQUEST['panel'])?$_REQUEST['panel']:'';
	$print_id  = isset($_REQUEST['print_id'])?$_REQUEST['print_id']:'';
	if($print_id)
	{
		$res = $db->getRow("select * from ".$ecs->table('print_log')." where id = $print_id");
		$address   = $res['address'];
		$phone     = $res['phone'];
		$goods_id_arr = json_decode($res['conten']);
	}else{
		$address   = $_SESSION['address'];
		$phone     = $_SESSION['phone'];
		$goods_id_arr = $_SESSION[$panel];
	}

	$big_html  = $class_print->big_html($panel,$goods_id_arr,$address,$phone);
	$smarty->assign('big_html',$big_html);
	$smarty->assign('panel',$panel);
	$smarty->assign('act',$_REQUEST['act']);
}
elseif($_REQUEST['act'] == 'shop_message')
{
	print_user();
	$address   = isset($_REQUEST['address'])?$_REQUEST['address']:'';
	$phone     = isset($_REQUEST['phone'])?$_REQUEST['phone']:'';
	$_SESSION['address'] = $address;
	$_SESSION['phone'] = $phone;exit;
}
elseif($_REQUEST['act'] == 'print_save')
{
	print_user();
	$panel     = isset($_REQUEST['panel'])?$_REQUEST['panel']:'';
	$pic_path = date('Ymdhis',time());
	$file_name = $pic_path.$panel;
	$print_log = array(
		'user_id'  => $_SESSION['user_id'],
		'edit_var' => $panel,
		'address'  => $_SESSION['address'],
		'phone'    => $_SESSION['phone'],
		'conten'   => json_encode($_SESSION[$panel]),
		'log_time' => time(),
		'pic_path' => '/print/'.$file_name.'.zip',
	);
	$db->autoExecute($ecs->table('print_log'), $print_log, 'INSERT');
	$print_id = $db->insert_id();
	$host_url = 'http://'.(trim($_SERVER['HTTP_HOST'])?trim($_SERVER['HTTP_HOST']):trim($_SERVER['SERVER_NAME']));
	$ab_path  = '/home/webadm/wwwroot/o2o_txd168';
	$pic_path_org = $ab_path.'/print/'.$file_name.'.bmp';
	$url = $host_url.'/print.php?act=view_full&panel='.$panel.'&print_id='.$print_id;
    $exec_result = shell_exec('/usr/local/wkhtmltox/bin/wkhtmltoimage --quality 100 --format bmp "'.$url.'" '.$pic_path_org);
	//dump('/usr/local/wkhtmltox/bin/wkhtmltoimage --quality 100 --format bmp "'.$url.'" '.$pic_path_org);
	// 压缩图片
	$zip_name = $ab_path.'/print/'.$file_name.'.zip';
	$zip = new ZipArchive();
	if($zip->open($zip_name, ZIPARCHIVE::CREATE) !== TRUE)
	{
		$json['msg'] = '0';
		echo json_encode($json);
	}else{
		$zip->addFile($pic_path_org, $file_name.'.bmp');
		$zip->close();
		@unlink($pic_path_org); // 删除图片
		$download_name = $url.$file_name.'.zip';
		$json['msg'] = '1';
		$json['url'] = $file_name.'.zip';
		echo json_encode($json);
		//dump($pic_path_org);
	}
	exit;
}
elseif($_REQUEST['act'] == 'print_ok')
{
	  print_user();
	$zip     = isset($_REQUEST['xia'])?$_REQUEST['xia']:'';
	if(strstr($zip,'normal_a'))
	{
		$name = '普通版，正面';
	}
	elseif(strstr($zip,'normal_b'))
	{
		$name = '普通版，背面';
	}
	elseif(strstr($zip,'simple_a'))
	{
		$name = '简版，正面';
	}
	elseif(strstr($zip,'simple_b'))
	{
		$name = '简版，背面';
	}
	$smarty->assign('name',$name);
	$smarty->assign('zip',$zip);
	$smarty->assign('act',$_REQUEST['act']);
}
$smarty->display('print.dwt');

function  print_user()
{
	if(empty($_SESSION['user_id']))
	{
		show_message('请登录后访问此页面...');
	}
}


?>