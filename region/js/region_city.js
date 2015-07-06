/*处理地区选择或搜索的JS  add by zenghd for date 2014-08-20 */ 
$(function(){
	
	/*打开页面时初始化省份下拉列表*/
	handle_ajax('省份',{act:'inquer_region',region:'province',parent:1},'#province');
	/*打开页面时初始化 按拼音首字母选择区*/
	choose_capital();
	
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
	option = '城市';
	data = {act:'select_search',province_id:province_id,city_id:city_id,area_id:area_id};
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
		if(result){
			var msg='';
			switch(parseInt(result.type)){
					case 1:
							msg = '您只查找了省份！';
							break;
					case 2:
							msg = '您只查找了省份和城市！';
							break;
					case 3:
							msg = '您查找了省份，城市和区县！';
							break;
					default:
							msg = '您查找的地方不存在！';
							break;
		   }
			alert(msg);
		}
		
	}
	});
	
});

/*给直接搜索输入框绑定一个keyup事件*/
$('#directly_search').on('keyup',function(e){
			var word = $(this).val();
			if(word != undefined && word != '' && word != '请输入城市中文或拼音'){
			directly_search(word);
			}
			
		
	
});


/*给直接搜索输入框绑定一个focus事件*/
$('#directly_search').on('focus',function(e){
		if(e.keycode == 13){
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
				console.log(result);//[1].length
				var tb_str = '';
					tr_str = '';
				for(var k=0;k<res_len;k++){
					tr_str +="<tr><td style='text-align:left;padding-left:5px;width:40%;'>"
							  +result[k].region_name
							  +"</td><td style='text-align:right;padding-right:5px;width:60%;'>"+result[k].pinyin
							  +"</td></tr>";
					if(k>9){break;}
				}
				tb_str = "<table cellspacing='0' cellpadding='0' style='width:100%;'>"+tr_str+"</table>";
				$('#show_hint').html('').append(tb_str);
				$('#show_hint').show();
				$('body').click(function(){
					$('#show_hint').hide();
				});
			}
			
		}
	});
	
	}

	$('body').click(function(){
		$('#show_hint').hide();
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
			var len = result.length;
			if(len>0){
				console.log(result[0].capital);
				console.log(result[0]);
				console.log(result.length);
				console.log(result);
				var li_A = '',li_B = '',li_C = '',li_D = '',li_E = '',
					li_F = '',li_G = '',li_H = '',li_I = '',li_J = '',
					li_K = '',li_L = '',li_M = '',li_N = '',li_O = '',
					li_P = '',li_Q = '',li_R = '',li_S = '',li_T = '',
					li_W = '',li_X = '',li_Y = '',li_Z = '';
				for(var i=0;i<len;i++){
					switch(result[i].capital){
						case 'A':
								//alert('A');
								li_A += "<a href='#' target='_blank' id='"
										+result[i].region_id+"'>"
										+result[i].region_name
										+"</a>";
								break;
						case 'B':
								//alert('B');
								li_B += "<a href='#' target='_blank' id='"
										+result[i].region_id+"'>"
										+result[i].region_name
										+"</a>";
								break;
						case 'C':
								//alert('C');
								li_C += "<a href='#' target='_blank' id='"
										+result[i].region_id
										+"'>"+result[i].region_name
										+"</a>";
								break;
						case 'D':
								//alert('A');
								li_D += "<a href='#' target='_blank' id='"
										+result[i].region_id+"'>"
										+result[i].region_name
										+"</a>";
								break;
						case 'E':
								//alert('B');
								li_E += "<a href='#' target='_blank' id='"
										+result[i].region_id+"'>"
										+result[i].region_name
										+"</a>";
								break;
						case 'F':
								//alert('A');
								li_F += "<a href='#' target='_blank' id='"
										+result[i].region_id+"'>"
										+result[i].region_name
										+"</a>";
								break;
						case 'G':
								//alert('B');
								li_G += "<a href='#' target='_blank' id='"
										+result[i].region_id+"'>"
										+result[i].region_name
										+"</a>";
								break;
						case 'H':
								li_H += "<a href='#' target='_blank' id='"
										+result[i].region_id+"'>"
										+result[i].region_name
										+"</a>";
								break;
						
						case 'I':
								li_I += "<a href='#' target='_blank' id='"
										+result[i].region_id+"'>"
										+result[i].region_name
										+"</a>";
								break;
						case 'J':
								li_J += "<a href='#' target='_blank' id='"
										+result[i].region_id+"'>"
										+result[i].region_name
										+"</a>";
								break;
						case 'K':
								li_K += "<a href='#' target='_blank' id='"
										+result[i].region_id+"'>"
										+result[i].region_name
										+"</a>";
								break;
						case 'L':
								li_L += "<a href='#' target='_blank' id='"
										+result[i].region_id+"'>"
										+result[i].region_name
										+"</a>";
								break;
						case 'M':
								li_M += "<a href='#' target='_blank' id='"
										+result[i].region_id+"'>"
										+result[i].region_name
										+"</a>";
								break;
						case 'N':
								li_N += "<a href='#' target='_blank' id='"
										+result[i].region_id+"'>"
										+result[i].region_name
										+"</a>";
								break;
						case 'O':
								li_O += "<a href='#' target='_blank' id='"
										+result[i].region_id+"'>"
										+result[i].region_name
										+"</a>";
								break;
						case 'P':
								li_P += "<a href='#' target='_blank' id='"
										+result[i].region_id+"'>"
										+result[i].region_name
										+"</a>";
								break;
						case 'Q':
								li_Q += "<a href='#' target='_blank' id='"
										+result[i].region_id+"'>"
										+result[i].region_name
										+"</a>";
								break;
						case 'R':
								li_R += "<a href='#' target='_blank' id='"
										+result[i].region_id+"'>"
										+result[i].region_name
										+"</a>";
								break;
						case 'S':
								li_S += "<a href='#' target='_blank' id='"
										+result[i].region_id+"'>"
										+result[i].region_name
										+"</a>";
								break;
						case 'T':
								li_T += "<a href='#' target='_blank' id='"
										+result[i].region_id+"'>"
										+result[i].region_name
										+"</a>";
								break;
						case 'W':
								li_W += "<a href='#' target='_blank' id='"
										+result[i].region_id+"'>"
										+result[i].region_name
										+"</a>";
								break;
						case 'X':
								li_X += "<a href='#' target='_blank' id='"
										+result[i].region_id+"'>"
										+result[i].region_name
										+"</a>";
								break;
						case 'Y':
								li_Y += "<a href='#' target='_blank' id='"
										+result[i].region_id+"'>"
										+result[i].region_name
										+"</a>";
								break;
						case 'Z':
								li_Z += "<a href='#' target='_blank' id='"
										+result[i].region_id+"'>"
										+result[i].region_name
										+"</a>";
								break;
						
					}
				}
				$('#show_A').html('').append(li_A);
				$('#show_B').html('').append(li_B);
				$('#show_C').html('').append(li_C);
				$('#show_D').html('').append(li_D);
				$('#show_E').html('').append(li_E);
				$('#show_F').html('').append(li_F);
				$('#show_G').html('').append(li_G);
				$('#show_H').html('').append(li_H);
				$('#show_I').html('').append(li_I);
				$('#show_J').html('').append(li_J);
				$('#show_K').html('').append(li_K);
				$('#show_L').html('').append(li_L);
				$('#show_M').html('').append(li_M);
				$('#show_N').html('').append(li_N);
				$('#show_O').html('').append(li_O);
				$('#show_P').html('').append(li_P);
				$('#show_Q').html('').append(li_Q);
				$('#show_R').html('').append(li_R);
				$('#show_S').html('').append(li_S);
				$('#show_T').html('').append(li_T);
				$('#show_W').html('').append(li_W);
				$('#show_X').html('').append(li_X);
				$('#show_Y').html('').append(li_Y);
				$('#show_Z').html('').append(li_Z);
				
			}
			
		}
	});
}




});






























