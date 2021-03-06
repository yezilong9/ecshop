<?Php
$str_request = $_SERVER['QUERY_STRING'];
//echo "<Pre>";
//print_r($_SERVER);
//echo "<Pre>";
//die;
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <meta name="renderer" content="webkit" />
  <meta name="force-rendering" content="webkit" />
  <title>平安之夜 会说话的苹果|天下店圣诞节活动</title>
  <meta name="Keywords" content="平安之夜,苹果6相伴,天下店圣诞节活动" />
  <meta name="Description" content="平安夜送苹果，表达着人们希望亲朋好友能平平安安，吉祥如意的美好祝愿。对于在外求学的学子来说，在平安夜收到一份简单的祝福是件温馨的事。活动期间，在天下店预定苹果一份，就可以把您的祝福送到您的好基友手上。" />
  <link href="styles/master.css" rel="stylesheet" />
  <!--[if lte IE 8]>
  <link href="styles/lteie8.css" rel="stylesheet" />
  <![endif]-->

</head>
<body>
<!--页首(start)-->
<div class="header">
  <!--模块主体(start)-->
  <div class="header__bd">
    <h1><a title="官网首页" class="txtoverflow header__logo" href="http://www.txd168.com" target="_blank">天下店</a></h1>
    <div class="header-nav">
      <ul>
        <li><a href="http://dxc.txd168.com" target="_blank">首页（大学城站）</a></li>
        <li><a href="http://dxc.txd168.com/category.php?id=726" target="_blank">圣诞专区</a></li>
      </ul>
    </div>
  </div>
  <!--模块主体(end)-->
</div>
<!--页首(end)-->
<div class="container">
  <!--中间部份(start)-->
  <div class="section">
    <!--活动描述(start)-->
    <div class="mod mod1">
      <pre>
不是所有苹果都叫红富士，
不是所有苹果都会说话。
我们的苹果内外兼修。
——来自 零夏十一度，约？
      </pre>
    </div>
    <!--活动描述(end)-->
    <!--活动导航(start)-->
    <div class="mod mod2">
      <ul>
        <li><a id="go_gift1" title="送女神" href="../../christmas_activity.php">送女神</a></li>
        <li><a title="送基友" href="../../christmas_activity.php">送基友</a></li>
        <li><a title="送闺蜜" href="../../christmas_activity.php">送闺蜜</a></li>
        <li><a title="送男神" href="../../christmas_activity.php">送男神</a></li>
        <li><a title="送老师" href="../../christmas_activity.php">送老师</a></li>
        <li><a title="送学渣" href="../../christmas_activity.php">送学渣</a></li>
      </ul>
    </div>
    <!--活动导航(end)-->
    <!--活动流程(start)-->
    <div class="mod mod3">
      <pre>
1、点击按钮 → 选择你要送的对象 → 选择祝福语
（音频、文字、自定义音视频、图片等） →
祝福语被生成个性化的二维码，随礼品一起送出。
      </pre>
      <pre>
2、活动期间，注册即送红包。下单购买还可以抽奖，
100%中奖，最高大奖还有Iphone6 Plus。
      </pre>
      <pre>
3、红包可以在 <a href="http://dxc.txd168.com/category.php?id=726" target="_blank"><strong>圣诞专区</strong></a> 使用，也可以用于本活动
部分祝福套餐。
      </pre>
    </div>
    <!--活动流程(end)-->
    <!--活动规则(start)-->
    <div class="mod mod4">
      <pre>
1、活动期间，送货地址只限大学城范围，
想送远方好友的请期待下次吧。

2、祝福套餐将会在2-3天送出。

3、本活动最终解释权归天下店所有。
      </pre>
    </div>
    <!--活动规则(end)-->
  </div>
  <!--中间部份(end)-->
  <!--页尾(start)-->
  <div class="footer">
    <div class="footer__bd">
      <p>
        &copy; 2014-2015 天下店 版权所有，并保留所有权利。<br />
        ICP备案证书号:粤ICP备08036645号-11<br />
        广州市新泛联数码科技有限公司
      </p>
    </div>
  </div>
  <!--页尾(end)-->
</div>
<div class="container-bg">
  <!--下雪(start)-->
  <canvas id="snow" class="snow"></canvas>
  <canvas id="flake" class="flake"></canvas>
  <!--下雪(end)-->
  <div class="container-bg-01"></div>
  <div class="container-bg-02"></div>
  <div class="container-bg-03"></div>
  <div class="container-bg-04"></div>
  <div class="container-bg-05"></div>
</div>
<div id="area_overlay" class="area-overlay none">
  <!--[if ie 6]>
  <div style="height:100%" ie6debug="用iframe來挡住ie6的下拉框，但这样做 area_overlay 无法接收点击事件，在iframe前面增加一个元素以解决此问题。"></div>
  <iframe id="ie6_iframe"></iframe>
  <![endif]-->
</div>
<!--弹出框：登录(start)-->
<div id="popup_login" class="popup-wrap popup-login-wrap">
  <div class="popup popup-login">
    <!--模块首(start)-->
    <div class="hd popup__hd">
      <h1 class="tit">登录</h1>
      <div class="handle popup__handle">
        <a title="关闭" class="txtoverflow close popup__close" href="#">关闭</a>
      </div>
    </div>
    <!--模块首(start)-->
    <!--模块主体(start)-->
    <div class="bd popup__bd">
      <form id="popup_login_submit" name="popup_login_submit" class="fm">
        <div class="form-item">
          <label class="lab">手机号码：</label>
          <input name="account" id="account" type="text" class="inp"/><br />
        </div>
        <div class="form-item">
          <label class="lab">输入密码：</label>
          <input name="password" id="password" type="password" class="inp"/>
        </div>
        <div class="form-apply">
          <button title="登录" type="submit" class="txtoverflow btn-submit">登 录</button>
        </div>
      </form>
    </div>
    <!--模块主体(end)-->
  </div>
</div>
<!--弹出框：登录(end)-->
<!--弹出框：抽奖(start)-->
<div id="popup_lucky_draw" class="popup-wrap popup-lucky-draw-wrap">
  <div class="popup popup-lucky-draw">
    <!--模块首(start)-->
    <div class="hd popup__hd">
      <h1>抽奖</h1>
      <div class="handle popup__handle">
        <a title="关闭" class="txtoverflow close popup__close" href="#">关闭</a>
      </div>
    </div>
    <!--模块首(start)-->
    <!--模块主体(start)-->
    <div class="bd popup__b">
      <!--轮盘(start)-->
      <table class="roulette">
        <tr>
          <td>&nbsp;</td>
          <td width="68"><img id="1" src="images/lucky-draw/jp1.gif" width="80" height="80" /></td>
          <td width="50">&nbsp;</td>
          <td width="71"><img id="2" src="images/lucky-draw/jp2.gif" width="80" height="80" /></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td><img id="8" src="images/lucky-draw/jp8.gif" width="80" height="80" /></td>
          <td height="210" colspan="3" rowspan="3" align="center" valign="middle">
            <table border="0">
              <tr>
                <td>
                    <input type="hidden" id="parm" value="<?=$str_request?>">
                  <!--
                  <img src="images/lucky-draw/yj0.gif" width="189" height="189" border="0" onclick="mix_start_lottery()"/>
                  -->
                  <img id="core0" style="display:block;" src="images/lucky-draw/yj0.gif" width="189" height="189" border="0" onclick="mix_start_lottery()"/>
                  <img id="core1" style="display:none;" src="images/lucky-draw/yj1.gif" width="189" height="189" border="0" />
                  <img id="core2" style="display:none;" src="images/lucky-draw/yj2.gif" width="189" height="189" border="0" />
                  <img id="core3" style="display:none;" src="images/lucky-draw/yj3.gif" width="189" height="189" border="0" />
                  <img id="core4" style="display:none;" src="images/lucky-draw/yj4.gif" width="189" height="189" border="0" />
                  <img id="core5" style="display:none;" src="images/lucky-draw/yj5.gif" width="189" height="189" border="0" />
                  <img id="core6" style="display:none;" src="images/lucky-draw/yj6.gif" width="189" height="189" border="0" />
                  <img id="core7" style="display:none;" src="images/lucky-draw/yj7.gif" width="189" height="189" border="0" />
                  <img id="core8" style="display:none;" src="images/lucky-draw/yj8.gif" width="189" height="189" border="0" />
                </td>
              </tr>
            </table>
          </td>
          <td align="center" valign="middle"><img id="3" src="images/lucky-draw/jp3.gif" width="80" height="80" /></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td height="70"><img id="7" src="images/lucky-draw/jp7.gif" width="80" height="80" /></td>
          <td align="center"><img id="4" src="images/lucky-draw/jp4.gif" width="80" height="80" /></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td align="center"><img id="6" src="images/lucky-draw/jp6.gif" width="80" height="80" /></td>
          <td>&nbsp;</td>
          <td align="center"><img id="5" src="images/lucky-draw/jp5.gif" width="80" height="80" /></td>
          <td>&nbsp;</td>
        </tr>
      </table>
      <!--轮盘(end)-->
      <div id="tips" class="popup-prize">恭喜您获得<br />抽奖中</div>
    </div>
    <!--模块主体(end)-->
  </div>
</div>
<!--弹出框：抽奖(end)-->
<!--弹出框：送女神(start)-->
<div id="popup_gift1" class="popup-wrap popup-gift-wrap">
  <div class="popup popup-gift">
    <!--模块首(start)-->
    <div class="hd popup__hd">
      <h1>送女神</h1>
      <div class="handle popup__handle">
        <a title="关闭" class="txtoverflow close popup__close" href="#">关闭</a>
      </div>
    </div>
    <!--模块首(start)-->
    <!--模块主体(start)-->
    <div class="bd popup__bd">
      <form id="popup_gift1_submit" name="popup_gift1_submit" class="fm">
        <div class="form-item">
          <h2>这个平安果是要送给你女神的。对于你的女神，你想要说点什么呢？</h2>
          <ul>
            <li>
              <label>
                <input name="radio_group1" type="radio" /> 平安果一个+音频：汪峰 - 当我想你的时候
              </label>
              <a href="http://y.qq.com/#type=song&mid=002Fei9x43B7Fq&from=smartbox" target="_blank">试听</a>
            </li>
            <li>
              <label>
                <input name="radio_group1" type="radio" /> 平安果一个+音频：五月天 - 知足
              </label>
              <a href="http://y.qq.com/#type=song&mid=0033P66R0qEtlT&from=smartbox" target="_blank">试听</a>
            </li>
            <li>
              <label>
                <input name="radio_group1" type="radio" /> 平安果一个+文字：“难以忘记，初次相遇，你那迷人的眼睛”
              </label>
            </li>
            <li>
              <label>
                <input name="radio_group1" type="radio" /> 平安果一个+文字：“青梅已枯，竹马老去，从此我爱的人都像你。”
              </label>
            </li>
            <li>
              <label>
                <input name="radio_group1" type="radio" /> 平安果一个+其他自定义视频、音频、图片
              </label>
            </li>
            <li>
              <label>
                <input name="radio_group1" type="radio" /> 平安果一个+<a href="#">鲜花一束</a>+其他自定义视频、音频、图片，可使用优惠券抵扣
              </label>
            </li>
            <li>
              <label>
                <input name="radio_group1" type="radio" /> 平安果一个+<a href="#">朱古力99粒</a>+其他自定义视频、音频、图片，可使用优惠券抵扣
              </label>
            </li>
            <li>
              <label>
                <input name="radio_group1" type="radio" />平安果一个+<a href="#">Hello kitty</a>+其他自定义视频、音频、图片，可使用优惠券抵扣
              </label>
              <input type="text" class="inp inp-custom" placeholder="请把链接复制到此处" />
              <div class="tips">
友情提示：自定义的视频、音频请自行录制，然后上传到优酷，然后复制链接黏贴到上方即可；图片同理，上传到某个图库并获取链接。<br />
不懂如何操作请点击 <a class="service-online" href=#"><img src="images/qq.gif" alt="qq在线" /></a> 联系客服。
              </div>
            </li>
          </ul>
        </div>
        <div class="form-item">
          <h2>是自己亲手送，还是假手于人，选择吧……</h2>
          <h3>亲手送，请于12月20日下午2点到5点到以下地址自提：</h3>
          <ul>
            <li>
              <label>
                <input name="radio_group2" type="radio" /> 地址1：大学城广大，菊苑饭堂斜对面（报亭隔壁）
              </label>
            </li>
            <li>
              <label>
                <input name="radio_group2" type="radio" /> 地址2：大学城广美，广美天桥上
              </label>
            </li>
            <li>
              <label>
                <input name="radio_group2" type="radio" /> 地址3：大学城广东药学院，2饭
              </label>
            </li>
            <li>
              <label>
                <input name="radio_group2" type="radio" /> 地址4：大学城中大，三饭广场
              </label>
            </li>
            <li>
              <label>
                <input name="radio_group2" type="radio" /> 还是让其他人送，那就留下你女神的联系方式：<span class="red">（苹果统一在12月23日送出）</span>
              </label>
              <div class="logistics">
                <ul>
                  <li><label>女神姓名：</label><input class="inp" type="text" /></li>
                  <li><label>手机号码：</label><input class="inp" type="text" /></li>
                  <li><label>具体地址：</label><input class="inp" type="text" /></li>
                </ul>
              </div>
            </li>
          </ul>
        </div>
        <div class="form-item form-item-count">
          <p class="count">
          苹果费用：<strong>￥10.00</strong>　　
          自定义祝福费用：<strong>￥5.00</strong>
          </p>
          <p class="total">合计：￥18.00 </p>
        </div>
        <div class="form-apply">
          <button title="提交" type="submit" class="txtoverflow btn-submit">提交</button>
          <span class="red">支付完成请不要关闭页面，还有100%得奖的抽奖活动哟~</span>
        </div>
      </form>
    </div>
    <!--模块主体(end)-->
  </div>
</div>
<!--弹出框：送女神(end)-->
<!--弹出框：系统消息(start)-->
<div id="popup1" class="popup-wrap">
  <div class="popup">
    <!--模块首(start)-->
    <div class="hd popup__hd">
      <h1 class="popup__h1">系统消息</h1>
      <div class="handle popup__handle">
        <a title="关闭" class="txtoverflow close popup__close" href="#">关闭</a>
      </div>
    </div>
    <!--模块首(start)-->
    <!--模块主体(start)-->
    <div class="bd popup__bd">
        <p>
      基础模块，<strong>文字可自行修改</strong>，预留给后台调用。
        </p>
        <p>
      [例]<br />
      卡号：XXXXXXXX<br />
      密码：●●●●●●●●●●<br />
      <br /><br />
      注：<em class="red">沒有</em>宽度限制。<br />
        </p>
    </div>
    <!--模块主体(end)-->
  </div>
</div>
<!--弹出框：系统消息(end)-->

<script type="text/javascript" src="scripts/jquery1.8.min.js"></script>
<script language="JavaScript" src="lottery.js"></script>
<!--SCRIPT language="JavaScript" src="scripts/prototype.js"--></SCRIPT>
<!--script language="JavaScript" src="scripts/json.js" ></script-->
<script language="JavaScript" src="scripts/tw-sack.js"></script>
<script type="text/javascript">
//<![CDATA[
var $window = $(window);
//提示IE8以下的浏览器，升级
if($.browser.msie && $.browser.version < 8){
  var html_code =
  '<div class="browser-upgrader">' +
  '  阁下正在使用过旧的浏览器，请更换浏览器以获得更好的用户体现。建议安装'+
  '  <a href="https://www.mozilla.org" target="_blank">Firefox浏览器</a>，'+
  '  <a href="http://www.google.cn/intl/zh-CN/chrome/browser/desktop/index.html" target="_blank">Chrome浏览器</a>，或'+
  '  <a href="http://www.microsoft.com/zh-cn/download/details.aspx?spm=1.7274553.0.0.XI6QyK&id=43" target="_blank">升级你的IE</a>' +
  '  <a title="关闭" class="close" href="javascript:;">x</a>'+
  '</div>';
  $('body').prepend(html_code);
  $('.browser-upgrader .close').click(function(){
    $('.browser-upgrader').remove();
  })
}

//打开“覆盖层”
var area_overlay = $('#area_overlay');
var open_area_overlay = function(){
  area_overlay.removeClass('none');
  //隐藏滚动条
  //$('html').css('overflow','hidden');

  //ie6
  if($.browser.msie && $.browser.version==6){
    area_overlay.css('height',$('body').height());
    $window.bind('resize',function(){
      area_overlay.css('height',$('body').height());
    })
  }
}
//关闭“覆盖层”
var close_area_overlay = function(){
  area_overlay.addClass('none');
   $('html').removeAttr('style');

  //ie6
  if($.browser.msie && $.browser.version==6){
   $window.unbind('resize');
   $('body').removeAttr('style');
  }
}

/////////////////////////////////////////////////////////弹出框(start)
popup = function(obj){
  //打开“覆盖层”
  open_area_overlay();
  //根据弹出框的宽高，设置其左右位置
  obj.css({
   'width':obj.width()+'px',
   'margin-left':'-'+obj.width()/2+'px'
  });

  //ie6
  if($.browser.msie && $.browser.version==6){
    obj.css({
     'top':$(document).scrollTop() + 150
    });
  }
  //显示
  obj.fadeIn();

  //关闭
  obj.find('.popup__close').add(area_overlay).click(function(e){
    obj.css('display','').removeAttr('style');
    close_area_overlay();

    e.preventDefault();
  })
}

//循环点击，显示弹出框
$('.go-popup').each(function(i){
  $(this).click(function(e){
    //alert($(this).index());
    popup($('#popup'+(i+1))); //索引号从0计起
    e.preventDefault();
  })
});

//弹出框：登录
$('#go_login').click(function(e){
  popup($('#popup_login'));
  e.preventDefault();
});

//弹出框：送女神
/*
$('#go_gift1').click(function(e){
  popup($('#popup_gift1'));
});
*/
popup($('#popup_lucky_draw'));
//弹出框：抽奖
$('#go_lucky_draw').click(function(e){
  popup($('#popup_lucky_draw'));
  e.preventDefault();
});

/////////////////////////////////////////////////////////弹出框(end)

//]]>
</script>
</body>
</html>