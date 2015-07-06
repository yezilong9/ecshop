var xmlDoc = null;
//var str_page_ini = 'lottery_action.php';
var str_page_ini = '../../lucky_draw.php';
var ajax;
var arr_grid = [];
var obj_pic = {};
var int_lottery_stat,int_lottery_win,int_lottery_result;
var int_round = 0;
var int_radmon_one;
var arr_obj = new Array();
var bln_is_click = false;
var bln_is__win_click = false;

var obj_pic ={
    '1':'images/lucky-draw/jp1.gif',
    '2':'images/lucky-draw/jp2.gif',
    '3':'images/lucky-draw/jp3.gif',
    '4':'images/lucky-draw/jp4.gif',
    '5':'images/lucky-draw/jp5.gif',
    '6':'images/lucky-draw/jp6.gif',
    '7':'images/lucky-draw/jp7.gif',
    '8':'images/lucky-draw/jp8.gif'
};

//此0 [5,8] 意思为： 0 对应 obj_win_name 中 0 后面的文字；5,8 对应 obj_pic 中的序号
var obj_dist ={
    '0':[1],
    '1':[2],
    '2':[3],
    '3':[4],
    '4':[5],
    '5':[6],
    '6':[7],
    '7':[8]
};
var obj_win_name ={
    '0':'恭喜您获得：<br/>5元红包,请登录账号查看。',
    '1':'恭喜您获得：<br/>10元红包,请登录账号查看。',   
    '2':'恭喜您获得：<br/>20元红包,请登录账号查看。',
    '3':'恭喜您获得：<br/>30元红包,请登录账号查看。',
    '4':'恭喜您获得：<br/>50元红包,请登录账号查看。',
    '5':'恭喜您获得：<br/>100元红包,请登录账号查看。',
    '6':'恭喜您获得：<br/>神秘礼品一份',
    '7':'恭喜您获得：<br/>iphone6 plus 一台'
};


var SOUND_WIN = "sound/win.wav";
var SOUND_LOSE = "sound/lost.wav";
var SOUND_READY = "sound/ready.wav";

for(var i = 0; i < 8; i++){
    arr_grid[i] = (i+1);
}

//开始抽奖
function mix_start_lottery(){
    if(!bln_is_click){
        var parm_lotter = $("#parm").val();
        //alert();
        ajaxSend(str_page_ini+'?'+parm_lotter,getReadyCompleted); 
    }
}
var oldColor;

function void_flash(i){
    var args = void_flash.arguments;
    var int_sec = args[1];
    var flashTd = document.getElementById(i);
	  var arrowTd = document.getElementById("core"+i);
	  
//  alert(arrowTd.src);
	  
    oldColor    = flashTd.src;
    flashTd.src = "images/lucky-draw/jp.jpg";    
    arrowTd.src = "images/lucky-draw/yj"+i+".gif";    
//    alert(arrowTd.src);
    
    for(var m = 0; m < 8; m++){
        if(i == m){
           document.getElementById("core"+i).style.display = "block";
        }else{
           document.getElementById("core"+m).style.display = "none";
        }
    }
    
    
    //中奖定位
    if(int_round == 3){
        //alert(int_lottery_stat+','+int_lottery_win+','+int_lottery_result);
        if(!int_radmon_one){
            arr_obj = obj_dist[int_lottery_result];            
            int_radmon_one = Math.round(Math.random()*(arr_obj.length-1));
        }
        if(arr_obj){                   
            if((i) == arr_obj[int_radmon_one]){
                //alert(i);
                //alert(arr_obj[int_radmon_one]);
                //flashTd.src="images/lucky-draw/jp2.jpg";
                flashTd.src = oldColor.substr(0, oldColor.length-4)+'d.gif';
                //alert(flashTd.src);
                
                if(int_lottery_result != 0 ){//中奖
                   //document.getElementById("award").style.display = "";
                   document.getElementById("tips").style.display = "";
                   setMusic(SOUND_WIN);
                }else{
                   setMusic(SOUND_LOSE);
                }

                if(int_lottery_result != 0){// && int_lottery_result != 1
                    bln_is__win_click = true;
                    //$('get_award_id').src = "images/lucky-draw/buttom-02d.gif";
                }
                
                $("#tips").show().html(obj_win_name[int_lottery_result]);				
                int_round = 0;
                arr_obj = new Array();
                int_radmon_one = null;
                
                //金猪.可以获得另一次机会
                //if(int_lottery_result == 1)  bln_is_click = false;
                return;
            }
        }
    }

    //if(i>1)
    setTimeout("void_resetColor("+i+",'"+oldColor+"')",50);
    //转完1周再转1周
    if(i == 8 && int_round < 3){
         int_round++;
         void_flash(1);
     }
    if(i<arr_grid.length){
        i++;
        var int_sec_tmp;
        switch(int_round){
           case 0:
               int_sec_tmp = 100*0.5;
               break;           
           case 1:
               int_sec_tmp = 100*1;
               break;
           case 2:
               int_sec_tmp = 100*2;
               break;           
           default:
               int_sec_tmp = 100*3;
               break;            
        }        
        setTimeout("void_flash("+i+")",int_sec_tmp);
    }
}

function void_get_award(){
    if(bln_is__win_click){
		    $('tips').innerHTML = '奖品将于下个工作日予发放！';
    }else{
        $('tips').innerHTML = '很遗憾,您未中奖,请继续努力!';
    }
}

function void_resetColor(id,color){
    var td = document.getElementById(id);
    td.src = color;
}

//-----------
function parseAjax() {    
	try{
		var myDoc = ajax.response;
		xmlDoc = jQuery.parseJSON(myDoc);
		//xmlDoc = myDoc.parseJSON();
	}catch(e) { 
	  alert('由于网络故障，需要重新进入游戏！'); 
	} //解析错误，请与管理员联系
}

function ajaxSend(ajaxurl,ajaxfunc){

	ajax = new sack();
	ajax.requestFile  = ajaxurl;
	ajax.onLoading    = whenLoading;
	ajax.onCompletion = ajaxfunc;      //whenLoaded
	ajax.runAJAX();	
}
function whenLoading(){
    $('#tips').show().html("读取数据，请稍后...");
}
function getReadyCompleted(){
    parseAjax();
    var current = null; 
    //---获取个人资料
    if(xmlDoc != null)
    {
        if(xmlDoc["int_lottery_stat"] == 2)
        {
            alert(xmlDoc["msg"]);
            if(xmlDoc["source"] == 'no_order')
            {
                window.location.href = "index.php";
            }
            else
            {
                window.close(); 
            }
        }
        else
        {
            bln_is_click = true;
            //setMusic(SOUND_READY);             
            $('#tips').show().html("正在抽奖，请勿刷新...");      
            int_lottery_stat   = xmlDoc["int_lottery_stat"];
            int_lottery_result = xmlDoc["int_lottery_result"];
            int_lottery_win    = xmlDoc["int_lottery_win"];
            void_flash(1);
        } 
    }
    else
    {
        alert("获取数据失败，请稍后重试");
        window.close();
    }  
}

var musicOn = false;
function setMusic(src){
    if(musicOn){
       sound.stop();
       sound.fileName = src;
       sound.Play();
    }
}

function init_page(){
    MM_preloadImages('images/lucky-draw/jp.jpg','images/lucky-draw/jp1.gif','images/lucky-draw/jp2.gif','images/lucky-draw/jp3.gif','images/lucky-draw/jp5.gif','images/lucky-draw/jp6.gif',
    'images/lucky-draw/jp7.gif','images/lucky-draw/jp8.gif','images/lucky-draw/jp1d.gif','images/lucky-draw/jp2d.gif','images/lucky-draw/jp3d.gif','images/lucky-draw/jp5d.gif','images/lucky-draw/jp6d.gif','images/lucky-draw/jp7d.gif','images/lucky-draw/jp8d.gif');
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}