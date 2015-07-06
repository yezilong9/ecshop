<li>
<a class="weixin-btn" onclick="weixinSendAppMessage()" href="#">
<img src="http://pic.58pic.com/58pic/12/25/04/02k58PICVwf.jpg" width="20px" height="20px">   
</a>
</li>
<script type="text/javascript" src="/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript">
$(document).unbind('WeixinJSBridgeReady').bind('WeixinJSBridgeReady',function(){
	//转发朋友圈
	alert(12);
    WeixinJSBridge.on("menu:share:timeline", function(argv) {alert(2)
        var data = {
            "img_url": "http://pic.58pic.com/58pic/12/25/04/02k58PICVwf.jpg",
            "img_width": "120",
            "img_height": "120",
            "link": "http://center.game.kugou.com/m/ ",
            //desc这个属性要加上，虽然不会显示，但是不加暂时会导致无法转发至朋友圈，
            "desc":"测试",
    	    "title":"测试"
        };
        WeixinJSBridge.invoke("shareTimeline", data, function(res) {
        	alert(3);
        });
    });
    //分享给朋友
    alert(888);
    WeixinJSBridge.on('menu:share:appmessage', function(argv) {
        WeixinJSBridge.invoke("sendAppMessage", {
        	"img_url": "http://www.txd168.com/themes/wanbiao/images/logo.png",
            "img_width": "120",
            "img_height": "120",
            "link": "http://center.game.kugou.com/m/ ",
            //desc这个属性要加上，虽然不会显示，但是不加暂时会导致无法转发至朋友圈，
            "desc":"测试",
    	    "title":"测试"
        }, function(res){
        	
        });
    });
});
</script>