/*处理代理商域名设置时选择代理商区域的JS  add by zenghd for date 2014-08-27 */ 
$(function(){
	
	/*打开页面时初始化省份下拉列表*/
	handle_ajax('省份',{act:'inquer_region',region:'province',parent:1},'#province');
	


/*给省份下拉列表框绑定一个change事件*/
$('#province').on('change',function(){
	var parent_id = $(this).val();
	option = '城市';
	data = {act:'inquer_region',region:'city',parent:parent_id};
	handle_ajax(option,data,'#city');
	$('#area').html('').append("<option  value='0'>--选择区县--</option>");
});

/*给城市下拉列表框绑定一个change事件*/
$('#city').on('change',function(){
	var parent_id = $(this).val();
	option = '区县';
	data = {act:'inquer_region',region:'area',parent:parent_id};
	handle_ajax(option,data,'#area');
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
		url:'agency_url_config.php',
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
	

	

});

