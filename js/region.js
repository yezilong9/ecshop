/* $Id : region.js 4865 2007-01-31 14:04:10Z paulgao $ */

var region = new Object();

region.isAdmin = false;

/* *
 * 在选择地方的同时，把对应的地方名称添加到详细地址输入框
 *
 * @region  string  地方名称
 * @type    integer 类型
 * @postion string  目标列表框的名称
 */
region.printAddress = function(region, type, postion)//(this, 2, 'selCities_0')
{
	  var p_arr=postion.split('_');
	  //alert(typeof(p_arr[1]));
	  if(typeof(p_arr[1]) == 'undefined' )return false;
	  var provinces=document.getElementById('selProvinces_'+p_arr[1]);
	  var citys=document.getElementById('selCities_'+p_arr[1]);
	  var address=document.getElementById('address_'+p_arr[1]);
	      //alert(type);
		  if(type==2){
				   address.value='';
				   address.value=region;
		  }
		  if(type==3){
			   province_name=provinces.options[provinces.selectedIndex].text;
			   if(region != '请选择市'){
				   address.value='';
				   address.value=province_name+region;
			   }
		  }
	 	if(type==4){
			   province_name=provinces.options[provinces.selectedIndex].text;
			   city_name=citys.options[citys.selectedIndex].text;
			   if(region != '请选择区'){
				   address.value='';
				   address.value=province_name+city_name+region;
			   }
		  }
}

region.loadRegions = function(parent, type, target)
{
	  Ajax.call(region.getFileName(), 'act=user_address' + '&type=' + type + '&target=' + target + "&parent=" + parent , region.response, "POST", "JSON");
}

/* *
 * 载入指定的国家下所有的省份
 *
 * @country integer     国家的编号
 * @selName string      列表框的名称
 */
region.loadProvinces = function(country, selName)
{
  var objName = (typeof selName == "undefined") ? "selProvinces" : selName;

  region.loadRegions(country, 1, objName);
}

/* *
 * 载入指定的省份下所有的城市
 *
 * @province    integer 省份的编号
 * @selName     string  列表框的名称
 */
region.loadCities = function(province, selName)
{
  var objName = (typeof selName == "undefined") ? "selCities" : selName;

  region.loadRegions(province, 2, objName);
}

/* *
 * 载入指定的城市下的区 / 县
 *
 * @city    integer     城市的编号
 * @selName string      列表框的名称
 */
region.loadDistricts = function(city, selName)
{
  var objName = (typeof selName == "undefined") ? "selDistricts" : selName;
   
  region.loadRegions(city, 3, objName);
}

/* *
 * 处理下拉列表改变的函数
 *
 * @obj     object  下拉列表
 * @type    integer 类型
 * @selName string  目标列表框的名称
 */
region.changed = function(obj, type, selName)//(this, 2, 'selCities_0')
{
  var parent = obj.options[obj.selectedIndex].value;
  var objName = obj.options[obj.selectedIndex].text;//add by zenghd 2014-9-16 20:16
  
  if(type<4){region.loadRegions(parent, type, selName);}
  region.printAddress(objName,type, selName);//add by zenghd 2014-9-16 20:16
  
}

region.response = function(result, text_result)
{
  var sel = document.getElementById(result.target);

  sel.length = 1;
  sel.selectedIndex = 0;
  sel.style.display = (result.regions.length == 0 && ! region.isAdmin && result.type + 0 == 3) ? "none" : '';

  if (document.all)
  {
    sel.fireEvent("onchange");
  }
  else
  {
    var evt = document.createEvent("HTMLEvents");
    evt.initEvent('change', true, true);
    sel.dispatchEvent(evt);
  }

  if (result.regions)
  { //console.log(result);
    for (i = 0; i < result.regions.length; i ++ )
    {
      var opt = document.createElement("OPTION");
      opt.value = result.regions[i].region_id;
      opt.text  = result.regions[i].region_name;

      sel.options.add(opt);
    }
  }
}

region.getFileName = function()
{
  if (region.isAdmin)
  {
    return "../region.php";
  }
  else
  {
    return "region.php";
  }
}
