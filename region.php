<?php
/**
 *  地域处理
 * ============================================================================
 * * 版权所有 2005-2012 广州新泛联数码有限公司，并保留所有权利。
 * 网站地址: http://www..com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: zenghd $
 * $Id: region.php 17217 2014-08-20 11:08:08Z zenghd $
*/

define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
include(dirname(__FILE__) . '/includes/cls_json.php');
    $json = new JSON;
	
	

$smarty->assign('categories_pro',  get_categories_tree_pro()); // 分类树加强版/* 周改 */
$smarty->assign('navigator_list',get_navigator($ctype, $catlist));  //自定义导航栏
$smarty->assign('helps', get_shop_help());       // 网店帮助

if($_POST['act'] == ''){
    $region_table = $ecs->table('region');
	$table_arr = explode('.',$region_table);
	$table = substr($table_arr[1],1,-1);
	
	$sql_region = "SHOW TABLES LIKE '{$table}'";
	$tb_name = $db->getOne($sql_region);

	if($tb_name == $table){
	    //echo 11;
		$sql_capital = "SHOW COLUMNS FROM {$region_table} LIKE 'capital' ";
		$col_capital_name = $db->getOne($sql_capital);
		
		$sql_pinyin = "SHOW COLUMNS FROM {$region_table} LIKE 'pinyin' ";
		$col_pinyin_name = $db->getOne($sql_pinyin);
		if(empty($col_capital_name)){
			$sql_add_capital = "ALTER TABLE {$region_table} ADD `capital` char(1) not null DEFAULT 'A' ";
			$db->query($sql_add_capital);
		}
		if(empty($col_pinyin_name)){
			$sql_add_capital = "ALTER TABLE {$region_table} ADD `pinyin` varchar(50) not null DEFAULT 'abcd' ";
			$db->query($sql_add_capital);
		}
		
		if(empty($col_capital_name) || empty($col_pinyin_name)){
			$sql = "SELECT region_id,region_name FROM {$region_table}";
			$rows=array();
			$rows = $db->getAll($sql);
			//dump($rows);
			foreach($rows as $row){
				//echo $row['region_name'];
				$spell = Pinyin($row['region_name'],'utf-8');
				if($row['region_name'] == '亳州'){
					$spell = 'bozhou';
				}
				if($row['region_name'] == '儋州'){
					$spell = 'danzhou';
				}
				if($row['region_name'] == '衢州'){
					$spell = 'quzhou';
				}
				if($row['region_name'] == '重庆'){
					$spell = 'chongqing';
				}
				$capital=strtoupper(substr($spell, 0, 1));
				$sql_update="UPDATE {$region_table} SET capital = '$capital',pinyin = '$spell' WHERE region_id = '".$row['region_id']."'";
				$db->query($sql_update);
			}	
				
		}
		
	}else{
		echo "<script type='text/javascript'>alert('您的region数据表不存在,无法进行数据的读取！')</script>";
	}
	
	
	$smarty->assign('reset_css_path', 'themes/' . $_CFG['template'] . '/reset.css');
	$smarty->assign('choosecities_css_path', 'themes/' . $_CFG['template'] . '/choosecities.css');
	assign_template();
	$smarty->display('region.dwt');
	
}

if($_REQUEST['act'] == 'change_city'){
     $city_id = intval($_REQUEST['city_id']);
	 $sql_url = "SELECT agency_url FROM " . $ecs->table('agency_url').'WHERE region_id ='.$city_id;
	 $agency_url = $db->getOne($sql_url);
	 if($agency_url){
	 	header("Location:http://".$agency_url);
	 }else{
	 	echo "<script type='text/javascript'>alert('您所选择的城市还没有代理商哦！')</script>";
		exit();
	 }
	 
	 
}elseif($_REQUEST['act'] == 'user_address'){
    $type   = !empty($_REQUEST['type'])   ? intval($_REQUEST['type'])   : 0;
	$parent = !empty($_REQUEST['parent']) ? intval($_REQUEST['parent']) : 0;
	
	$address['regions'] = get_regions($type, $parent);
	$address['type']    = $type;
	$address['target']  = !empty($_REQUEST['target']) ? stripslashes(trim($_REQUEST['target'])) : '';
	$address['target']  = htmlspecialchars($address['target']);
	die($json->encode($address));

}elseif($_POST['act'] == 'inquer_region'){
	$region = $_POST['region'] ? mysql_real_escape_string($_POST['region']) : '';
	$parent = $_POST['parent'] ? intval($_POST['parent']) : 0;
	switch($region){
		case 'province':
				$region_type = 1;
				break;
		case 'city':
				$region_type = 2;
				break;
		case 'area':
				$region_type = 3;
				break;
	
	}
	$parent_id = $parent;
	$sql = "SELECT region_id,region_name FROM " . $ecs->table('region') .
					" WHERE parent_id = $parent_id AND region_type = $region_type ";
    $result = $db->getAll($sql);
	//dump($result);
	//$json->encode($result);
	die($json->encode($result));
	
}elseif($_POST['act'] == 'select_search'){	
	$city_id = $_POST['city_id'] ? intval($_POST['city_id']) : 0;
	$sql_url = "SELECT agency_url FROM " . $ecs->table('agency_url').'WHERE region_id ='.$city_id;
	$agency_url = $db->getOne($sql_url);
	//header("Location:http://".$agency_url);
	$result_url = array();
	$result_url['url'] = $agency_url;
	die($json->encode($result_url));
	
	
	
}elseif($_POST['act'] == 'word_search'){
	$keyword = !empty($_POST['keyword']) ? $_POST['keyword'] : '';
	$sql = "SELECT region_id,region_name,pinyin FROM " . $ecs->table('region') ." WHERE region_name LIKE '$keyword%' OR pinyin LIKE '$keyword%'";
	$rows = $db->getAll($sql);
	//dump($rows);
	die($json->encode($rows));
	

}elseif($_POST['act'] == 'capital_search'){

	#读取缓存
	$rows = read_static_cache('region');
	if($rows === false)
	{
		$sql = "SELECT region_id,region_name,capital FROM " . $ecs->table('region') . " WHERE region_type = 2 ORDER BY capital ASC";
		$rows = array();
		$rows = $db->getAll($sql);
		#写入缓存
		write_static_cache('region', $rows);
	}

	/* 对数据进行组装 start */
	$arr = array();
	foreach($rows as $key=>$value){
		$arr[$value['capital']][] = $value;
	}
	$html = '';
	foreach($arr as $k=>$v){
		$city = '';
		$html .= "<li id='city-A' class=''><div class='cf fn-clear'><div class='label'><strong>".$k.
				"</strong></div><div class='label-city fn-clear'>";
		foreach($v as $city_k=>$city_v){
			
											
			$city .="<div class='city-content'><a class='link' onclick='jq_cookie($city_v[region_id])' value='$city_v[region_id]' ".
			" style='font-size:14px;' target='_self' href='$url'>$city_v[region_name]</a>".
			'<div class="city-county">';
			$res = array();
			$sql = "SELECT region_id,region_name,capital FROM " . $ecs->table('region') .
			" WHERE parent_id = $city_v[region_id] ORDER BY capital ASC";
			$res = $db->getAll($sql);
			foreach($res as $k=>$v){
				$x_url = "region.php?act=change_city&city_name=$v[region_name]&city_id=$v[region_id]";
				$city .= " <a href='$x_url' onclick='jq_cookie($v[region_id])'>$v[region_name]</a>";
			}
			
			$city .=' </div></div>';
		
		}
		$html .= $city;
		$html .= "</div></li>";
	}
		$html .= '<script type="text/javascript">
			$(".label-city .city-content").hover(function(){
					var xuliehao = $(".label-city .city-content").index($(this));
					$(".city-content").removeClass("active");
					$(this).addClass("active");
				  }
				);
				$(".city-content").hover(function(){},function(){
					$(this).removeClass("active");
				});
			</script>';


	/* 数据组装 and*/
	die($json->encode(array($html,$rows)));
		
}elseif($_POST['act'] == 'query_lately_accessing'){
	$lately_accessing_id = $_POST['lately_accessing_id'];
	//print_r($lately_accessing_id);
	if(!empty($lately_accessing_id)){
		$lately_accessing_rows = array();
		foreach($lately_accessing_id as $k=>$id_value){
			if($id_value>0){
				$region_id = intval($id_value);
				$sql = "SELECT region_id,region_name FROM " . $ecs->table('region') . " WHERE region_id = {$region_id} ";
				$lately_accessing_rows[] = $db->getRow($sql);
			}
		}
		die($json->encode($lately_accessing_rows));
	}
}



function Pinyin($_String, $_Code='gb2312')
{
	$_DataKey = "a|ai|an|ang|ao|ba|bai|ban|bang|bao|bei|ben|beng|bi|bian|biao|bie|bin|bing|bo|bu|ca|cai|can|cang|cao|ce|ceng|cha".
"|chai|chan|chang|chao|che|chen|cheng|chi|chong|chou|chu|chuai|chuan|chuang|chui|chun|chuo|ci|cong|cou|cu|".
"cuan|cui|cun|cuo|da|dai|dan|dang|dao|de|deng|di|dian|diao|die|ding|diu|dong|dou|du|duan|dui|dun|duo|e|en|er".
"|fa|fan|fang|fei|fen|feng|fo|fou|fu|ga|gai|gan|gang|gao|ge|gei|gen|geng|gong|gou|gu|gua|guai|guan|guang|gui".
"|gun|guo|ha|hai|han|hang|hao|he|hei|hen|heng|hong|hou|hu|hua|huai|huan|huang|hui|hun|huo|ji|jia|jian|jiang".
"|jiao|jie|jin|jing|jiong|jiu|ju|juan|jue|jun|ka|kai|kan|kang|kao|ke|ken|keng|kong|kou|ku|kua|kuai|kuan|kuang".
"|kui|kun|kuo|la|lai|lan|lang|lao|le|lei|leng|li|lia|lian|liang|liao|lie|lin|ling|liu|long|lou|lu|lv|luan|lue".
"|lun|luo|ma|mai|man|mang|mao|me|mei|men|meng|mi|mian|miao|mie|min|ming|miu|mo|mou|mu|na|nai|nan|nang|nao|ne".
"|nei|nen|neng|ni|nian|niang|niao|nie|nin|ning|niu|nong|nu|nv|nuan|nue|nuo|o|ou|pa|pai|pan|pang|pao|pei|pen".
"|peng|pi|pian|piao|pie|pin|ping|po|pu|qi|qia|qian|qiang|qiao|qie|qin|qing|qiong|qiu|qu|quan|que|qun|ran|rang".
"|rao|re|ren|reng|ri|rong|rou|ru|ruan|rui|run|ruo|sa|sai|san|sang|sao|se|sen|seng|sha|shai|shan|shang|shao|".
"she|shen|sheng|shi|shou|shu|shua|shuai|shuan|shuang|shui|shun|shuo|si|song|sou|su|suan|sui|sun|suo|ta|tai|".
"tan|tang|tao|te|teng|ti|tian|tiao|tie|ting|tong|tou|tu|tuan|tui|tun|tuo|wa|wai|wan|wang|wei|wen|weng|wo|wu".
"|xi|xia|xian|xiang|xiao|xie|xin|xing|xiong|xiu|xu|xuan|xue|xun|ya|yan|yang|yao|ye|yi|yin|ying|yo|yong|you".
"|yu|yuan|yue|yun|za|zai|zan|zang|zao|ze|zei|zen|zeng|zha|zhai|zhan|zhang|zhao|zhe|zhen|zheng|zhi|zhong|".
"zhou|zhu|zhua|zhuai|zhuan|zhuang|zhui|zhun|zhuo|zi|zong|zou|zu|zuan|zui|zun|zuo";
$_DataValue = "-20319|-20317|-20304|-20295|-20292|-20283|-20265|-20257|-20242|-20230|-20051|-20036|-20032|-20026|-20002|-19990".
"|-19986|-19982|-19976|-19805|-19784|-19775|-19774|-19763|-19756|-19751|-19746|-19741|-19739|-19728|-19725".
"|-19715|-19540|-19531|-19525|-19515|-19500|-19484|-19479|-19467|-19289|-19288|-19281|-19275|-19270|-19263".
"|-19261|-19249|-19243|-19242|-19238|-19235|-19227|-19224|-19218|-19212|-19038|-19023|-19018|-19006|-19003".
"|-18996|-18977|-18961|-18952|-18783|-18774|-18773|-18763|-18756|-18741|-18735|-18731|-18722|-18710|-18697".
"|-18696|-18526|-18518|-18501|-18490|-18478|-18463|-18448|-18447|-18446|-18239|-18237|-18231|-18220|-18211".
"|-18201|-18184|-18183|-18181|-18012|-17997|-17988|-17970|-17964|-17961|-17950|-17947|-17931|-17928|-17922".
"|-17759|-17752|-17733|-17730|-17721|-17703|-17701|-17697|-17692|-17683|-17676|-17496|-17487|-17482|-17468".
"|-17454|-17433|-17427|-17417|-17202|-17185|-16983|-16970|-16942|-16915|-16733|-16708|-16706|-16689|-16664".
"|-16657|-16647|-16474|-16470|-16465|-16459|-16452|-16448|-16433|-16429|-16427|-16423|-16419|-16412|-16407".
"|-16403|-16401|-16393|-16220|-16216|-16212|-16205|-16202|-16187|-16180|-16171|-16169|-16158|-16155|-15959".
"|-15958|-15944|-15933|-15920|-15915|-15903|-15889|-15878|-15707|-15701|-15681|-15667|-15661|-15659|-15652".
"|-15640|-15631|-15625|-15454|-15448|-15436|-15435|-15419|-15416|-15408|-15394|-15385|-15377|-15375|-15369".
"|-15363|-15362|-15183|-15180|-15165|-15158|-15153|-15150|-15149|-15144|-15143|-15141|-15140|-15139|-15128".
"|-15121|-15119|-15117|-15110|-15109|-14941|-14937|-14933|-14930|-14929|-14928|-14926|-14922|-14921|-14914".
"|-14908|-14902|-14894|-14889|-14882|-14873|-14871|-14857|-14678|-14674|-14670|-14668|-14663|-14654|-14645".
"|-14630|-14594|-14429|-14407|-14399|-14384|-14379|-14368|-14355|-14353|-14345|-14170|-14159|-14151|-14149".
"|-14145|-14140|-14137|-14135|-14125|-14123|-14122|-14112|-14109|-14099|-14097|-14094|-14092|-14090|-14087".
"|-14083|-13917|-13914|-13910|-13907|-13906|-13905|-13896|-13894|-13878|-13870|-13859|-13847|-13831|-13658".
"|-13611|-13601|-13406|-13404|-13400|-13398|-13395|-13391|-13387|-13383|-13367|-13359|-13356|-13343|-13340".
"|-13329|-13326|-13318|-13147|-13138|-13120|-13107|-13096|-13095|-13091|-13076|-13068|-13063|-13060|-12888".
"|-12875|-12871|-12860|-12858|-12852|-12849|-12838|-12831|-12829|-12812|-12802|-12607|-12597|-12594|-12585".
"|-12556|-12359|-12346|-12320|-12300|-12120|-12099|-12089|-12074|-12067|-12058|-12039|-11867|-11861|-11847".
"|-11831|-11798|-11781|-11604|-11589|-11536|-11358|-11340|-11339|-11324|-11303|-11097|-11077|-11067|-11055".
"|-11052|-11045|-11041|-11038|-11024|-11020|-11019|-11018|-11014|-10838|-10832|-10815|-10800|-10790|-10780".
"|-10764|-10587|-10544|-10533|-10519|-10331|-10329|-10328|-10322|-10315|-10309|-10307|-10296|-10281|-10274".
"|-10270|-10262|-10260|-10256|-10254";
	$_TDataKey = explode('|', $_DataKey);
	$_TDataValue = explode('|', $_DataValue);
	$_Data = (PHP_VERSION>='5.0') ? array_combine($_TDataKey, $_TDataValue) : 	_Array_Combine($_TDataKey, $_TDataValue);
	arsort($_Data);
	reset($_Data);
	if($_Code != 'gb2312') $_String = _U2_Utf8_Gb($_String);
	$_Res = '';
	for($i=0; $i<strlen($_String); $i++)
	{
		$_P = ord(substr($_String, $i, 1));
		if($_P>160) { 
			$_Q = ord(substr($_String, ++$i, 1));
			$_P = $_P*256 + $_Q - 65536; 
		}
		$_Res .= _Pinyin($_P, $_Data);
	}
	return preg_replace("/[^a-z0-9]*/", '', $_Res);
}

function _Pinyin($_Num, $_Data)
{
	if ($_Num>0 && $_Num<160 ) return chr($_Num);
	elseif($_Num<-20319 || $_Num>-10247) return '';
	else {
		foreach($_Data as $k=>$v){ 
			if($v<=$_Num) break; 
		}
	return $k;
}
}
function _U2_Utf8_Gb($_C)
{
	$_String = '';
	if($_C < 0x80)
	{
		$_String .= $_C;
	}
	elseif($_C < 0x800)
	{
		$_String .= chr(0xC0 | $_C>>6);
		$_String .= chr(0x80 | $_C & 0x3F);
	}elseif($_C < 0x10000){
		$_String .= chr(0xE0 | $_C>>12);
		$_String .= chr(0x80 | $_C>>6 & 0x3F);
		$_String .= chr(0x80 | $_C & 0x3F);
	} elseif($_C < 0x200000) {
		$_String .= chr(0xF0 | $_C>>18);
		$_String .= chr(0x80 | $_C>>12 & 0x3F);
		$_String .= chr(0x80 | $_C>>6 & 0x3F);
		$_String .= chr(0x80 | $_C & 0x3F);
	}
	return iconv('UTF-8', 'GB2312//IGNORE', $_String);
}
function _Array_Combine($_Arr1, $_Arr2)
{
	for($i=0; $i<count($_Arr1); $i++) $_Res[$_Arr1[$i]] = $_Arr2[$i];
	return $_Res;
}

?>