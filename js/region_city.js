/*处理地区选择或搜索的JS  add by zenghd for date 2014-08-20 */ 
$(function(){
	
	/*打开页面时初始化省份下拉列表*/
	handle_ajax('省份',{act:'inquer_region',region:'province',parent:1},'#province');
	/*打开页面时初始化 按拼音首字母选择区*/
	choose_capital();
	get_accessed_cookie()

/*给省份下拉列表框绑定一个change事件*/
$('#province').on('change',function(){
	var parent_id = $(this).val();
	//alert(parent_id);
	option = '城市';
	data = {act:'inquer_region',region:'city',parent:parent_id};
	handle_ajax(option,data,'#city');
	$('#area').html('').append("<option  value='0'>--选择区县--</option>");
});

/*给城市下拉列表框绑定一个change事件*/
$('#city').on('change',function(){
	var parent_id = $(this).val();
	//alert(parent_id);
	option = '区县';
	data = {act:'inquer_region',region:'area',parent:parent_id};
	handle_ajax(option,data,'#area');
});
	
/*给按省份选择下拉列表框后面的 确定按钮 绑定一个click事件*/
$('#select_search').on('click',function(){
	var province_id = $('#province').val();
	var city_id = $('#city').val();
	var area_id = $('#area').val();
	//alert(parent_id);
	if(city_id == 0){
		alert('请选择城市！');
		return false;
	}
	data = {act:'select_search',city_id:city_id};//select_search
	console.log(data);
	//handle_ajax(option,data,'#city');
	
	$.ajax({
		url:'region.php',
		type:'POST',
		data:data,
		beforeSend:function(){
			//alert('ready...');
		},
		dataType:'json',
		success:function(result){
			console.log(result);
			if(result.url){
			window.location.href = 'http://'+result.url;
			//window.location.replace('http://'+result.url);
			}else{
				alert('您所选择的城市还没有代理商哦！');
			}
			
		}
	});
	
});

/*给直接搜索输入框绑定一个keyup事件*/
$('#directly_search').on('keyup',function(e){
			var e = e||event;
			var word = $(this).val();
			if(word != undefined && word != '' && word != '请输入城市中文或拼音'){
				directly_search(word);
			}
			
		
	
});


/*给直接搜索输入框绑定一个focus事件*/
$('#directly_search').on('focus',function(e){
		var e = e||event;
		if(e.which == 13){
			var word = $(this).val();
			if(word != undefined && word != '' && word != '请输入城市中文或拼音'){
			directly_search(word);
			}
		}
	
});



/**
 * 本函数处理change事件的ajax
 *
 * @param   option   下拉框选项名称
 * @param   data   要ajax发送的数据
 * @param   id   要处理标签的id
 */
function handle_ajax(option,data,id){
	$.ajax({
		url:'region.php',
		type:'POST',
		data:data,
		beforeSend:function(){
			//alert('ready...');
		},
		dataType:'json',
		success:function(result){
			var option_str = "<option  value='0'>--选择"+option+"--</option>";
			var option_len = result.length
			if(option_len){
				for(var i=0;i<option_len;i++){
					option_str+="<option value='"
							  +result[i].region_id
							  +"'>"
							  +result[i].region_name
							  +"</option>";
				}
			}
			$(id).html('').append(option_str);
		}
	});
}


/**
 * 本函数处理 直接搜索时的ajax
 *
 * @param   word   要搜索的关键字
 */
function directly_search(word){
	$.ajax({
		url:'region.php',
		type:'POST',
		data:{act:'word_search',keyword:word},
		beforeSend:function(){
			//alert('ready...');
		},
		dataType:'json',
		success:function(result){
			var res_len = result.length;
			if(res_len > 0){
				//console.log(result);//[1].length
				var tb_str = '';
					tr_str = '';
				for(var k=0;k<res_len;k++){
					tr_str += "<tr><td style='text-align:left;padding-left:5px;width:40%;font-size:12px;'>"
							 +"<a href='region.php?act=change_city&city_name="
							 +result[k].region_name
							 +"&city_id="
							 +result[k].region_id
							 +"' target='_self' onclick='jq_cookie("
							 +result[k].region_id+")'>"
							 +result[k].region_name
							 +"</a></td><td style='text-align:right;padding-right:5px;width:60%;font-size:12px;'>"
							 +"<a href='region.php?act=change_city&city_name="
							 +result[k].region_name
							 +"&city_id="
							 +result[k].region_id
							 +"' target='_self' onclick='jq_cookie("+result[k].region_id+")'>"
							 +result[k].pinyin+"</a></td></tr>";
					if(k>9){break;}
				}
				tb_str = "<table cellspacing='0' cellpadding='0' style='width:100%;'>"+tr_str+"</table>";
				$('#show_hint').html('').append(tb_str);
				$('#show_hint').show();
				$('body').click(function(){
					$('#show_hint').hide();
				});
			}else{
				$('#show_hint').html('').append('没有相关数据！');
			}
			
		}
	});
	
	}

	$('body').click(function(){
		$('#show_hint').hide();
	});

/*当鼠标放到提示行时改变背景色*/
$('#show_hint table tr').on('click',function(){alert(1122);
	$(this).css('background-color','#99CCFF');
});


/**
 * 本函数处理 按拼音首字母选择区的ajax
 *
 * @param   word   要搜索的关键字
 */
function choose_capital(){
	$.ajax({
		url:'region.php',
		type:'POST',
		data:{act:'capital_search'},
		beforeSend:function(){
			//alert('ready...');
		},
		dataType:'json',
		success:function(result){
			$('#hasallcity').html(result[0]);
			var res_len = result[1].length;
			if(res_len>0){
				common_city_handle(result[1],res_len);
			}
		}	
	});
}

function common_city_handle(result,res_len){
	var common_city=['上海','北京','广州','深圳','武汉','天津','西安','南京','杭州','成都','重庆','佛山','东莞','珠海','惠州','中山','郑州','厦门','海口','南宁'];
	var common_city_str = '';
	var com_len = common_city.length;
	for(var i=0;i<res_len;i++){
		for(var j=0;j<com_len;j++){
			if(result[i].region_name == common_city[j]){
				common_city_str += "<a href='region.php?act=change_city&city_name="
							+result[i].region_name
							+"&city_id="
							+result[i].region_id
							+"' target='_self' onclick='jq_cookie("+result[i].region_id+")'>"
							+result[i].region_name
							+"</a>";
			}
		}
	}
	$('#common_city').html('').append(common_city_str);
	
}



});



