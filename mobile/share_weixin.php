 <?php
require_once "jssdk.php";
$jssdk = new JSSDK("wx6754a092969484b1", "91637ec869ea3b8f04080b12904d9127");
$signPackage = $jssdk->GetSignPackage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title></title>
</head>
<body>
  <center><button class="btn btn_primary" id="onMenuShareAppMessage" onclick="share_pengyou()">分享给朋友</button></center>
</body>
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script>
  /*
   * 注意：
   * 1. 所有的JS接口只能在公众号绑定的域名下调用，公众号开发者需要先登录微信公众平台进入“公众号设置”的“功能设置”里填写“JS接口安全域名”。
   * 2. 如果发现在 Android 不能分享自定义内容，请到官网下载最新的包覆盖安装，Android 自定义分享接口需升级至 6.0.2.58 版本及以上。
   * 3. 常见问题及完整 JS-SDK 文档地址：http://mp.weixin.qq.com/wiki/7/aaa137b55fb2e0456bf8dd9148dd613f.html
   *
   * 开发中遇到问题详见文档“附录5-常见错误及解决办法”解决，如仍未能解决可通过以下渠道反馈：
   * 邮箱地址：weixin-open@qq.com
   * 邮件主题：【微信JS-SDK反馈】具体问题
   * 邮件内容说明：用简明的语言描述问题所在，并交代清楚遇到该问题的场景，可附上截屏图片，微信团队会尽快处理你的反馈。
   */
   
  var title = '<?php echo $goods["goods_name"]?>';
  var desc = '<?php echo $goods["goods_name"]?>';
  var link = 'www.txd168.com/mobile/goods.php?id=<?php echo $goods["goods_id"]?>&share=weixin&wb_user_id=<?php echo $_SESSION['user_id']?>';
  var imgUrl = 'http://www.txd168.com/themes/wanbiao/images/logo.png';
  var list = [
        'checkJsApi',
        'onMenuShareAppMessage'
        ];
   
  wx.config({
    debug: true,
    //debug: false,
    appId: '<?php echo $signPackage["appId"];?>',
    timestamp: <?php echo $signPackage["timestamp"];?>,
    nonceStr: '<?php echo $signPackage["nonceStr"];?>',
    signature: '<?php echo $signPackage["signature"];?>',
    jsApiList : list
  });
  wx.ready(function () {
    // 在这里调用 API
    // 1 判断当前版本是否支持指定 JS 接口，支持批量判断
       /*wx.checkJsApi({
          jsApiList: list,
          success: function (res) {
                  var jso = JSON.stringify(res);
                 // alert(jso);
              }
        });
        */ 
        // 1 监听"分享给朋友"，按钮点击、自定义分享内容及分享结果接口
        /*
        wx.onMenuShareAppMessage({
          title: title,
          desc: desc,
          link: link,
          imgUrl: imgUrl,
          trigger: function (res) {
           // alert('用户点击发送给朋友');
          },
          success: function (res) {
            alert('已分享');
          },
          cancel: function (res) {
            alert('已取消分享');
          },
          fail: function (res) {
            //alert(JSON.stringify(res));
            //alert(123456);
          }
        });*/  
  });
 
 
 function share_pengyou()
 {
    var list = [
        'checkJsApi',
        'onMenuShareAppMessage'
        ];
    wx.config({
    debug: true,
    //debug: false,
    appId: '<?php echo $signPackage["appId"];?>',
    timestamp: <?php echo $signPackage["timestamp"];?>,
    nonceStr: '<?php echo $signPackage["nonceStr"];?>',
    signature: '<?php echo $signPackage["signature"];?>',
    jsApiList : list
  });
    //alert("chenchaoxiang");
     wx.ready(function () 
     {
        alert("chenchaoxiang");
        wx.onMenuShareAppMessage({
          title: "14444",
          desc: "2323232",
          link: "baieu.com",
          imgUrl: "http://www.txd168.com/themes/wanbiao/images/logo.png",
          trigger: function (res) {
            alert('用户点击发送给朋友');
          },
          success: function (res) {
            alert('已分享');
          },
          cancel: function (res) {
            alert('已取消分享');
          },
          fail: function (res) {
            //alert(JSON.stringify(res));
            alert(123456);
          }
        });
        alert("end");
     })
 }
</script>
</html>
