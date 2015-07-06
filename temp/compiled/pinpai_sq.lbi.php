
<div id="aboutSet" class="aboutSet" data="">
  <div class="wbPt">
    <div class="r">
      <dl style="top: 0px; ">
        <div class="ptIcon i__auth"></div>
        <dt>天猫授权</dt>
        <dd>天下店良好的品牌形象和广泛的品牌知名度，得到了淘宝天猫的充分肯定。淘宝天猫授权天下店成为广东、福建、海南、广西的区域合作伙伴。现在，天下店已经成为挑选全网性价比最高商品的最佳选择。</dd>
      </dl>
    </div>
    <div class="r">
      <dl style="top: 0px; ">
        <div class="ptIcon i__coop"></div>
        <dt>银行合作</dt>
        <dd>天下店的品质和服务，获得了国内银行的广泛认可，在通过严格的银行资质审核后，天下店先后和中国工商银行、招商银行、中国建设银行等国内大中型银行开展了深入的业务合作，深受客户好评。</dd>
      </dl>
    </div>
    <div class="r">
      <dl style="top: 0px; ">
        <div class="ptIcon i__quality"></div>
        <dt>7天质量退换</dt>
        <dd>天下店秉承行业“7天质量退换货”政策。顾客至上，以诚为本，只要您在天下店购买到的商品被证实为假货，我们一律按“假一赔十”处理。另外，购买商品在7天内出现任何质量问题，一律免费退换货，请您放心购买。</dd>
      </dl>
    </div>
    <div class="r">
      <dl style="top: 0px; ">
        <div class="ptIcon i__first"></div>
        <dt>全国第一</dt>
        <dd>天下店网站每天超过百万的访问量，信任形成口碑！天下店拥有全国3万多个终端点和1200个代理，线下厂商可把商品铺向全国终端进行销售。在每一个县城，天下店将建立1个服务中心， 提供产品体验、促销活动、仓储、 物流配送等服务，对区域内网点作全方位支撑。</dd>
      </dl>
    </div>
    <div class="r">
      <dl style="top: 0px; ">
        <div class="ptIcon i__praise"></div>
        <dt>客户口碑</dt>
        <dd>因为负责，所以信任。截止2014年7月，累计超过1200个代理加盟天下店，超过100万的注册会员，85%的用户会向朋友推荐天下店，您的满意给予我们动力。</dd>
      </dl>
    </div>
    <div class="r">
      <dl style="top: 0px; ">
        <div class="ptIcon i__sale"></div>
        <dt>专业售后</dt>
        <dd>天下店一直秉承“诚信、专业、负责”的原则，和“真心、细心、责任心”的服务态度，竭诚为广大客户提供最放心的产品选购服务体验。</dd>
      </dl>
    </div>
  </div>
  <div class="wbMedia">
    <div class="wbWeibo">
      <div class="tTit"><span class="tNm">天下店微博</span>
        <div class="tLnk"><a href="javascript:void(0)" class="ico i__weixin" title="微信"></a>
        <a href="http://t.qq.com/o2o-txd168"  class="ico i__tqq" title="腾讯"></a>
        <a href="http://weibo.com/u/5237139751" target="_blank" class="ico i__sina"title="新浪"></a></div>
      </div>
      <div class="tBdy">
        <iframe width="100%" height="420" class="share_self"  frameborder="0" scrolling="no" src="http://widget.weibo.com/weiboshow/index.php?language=&width=0&height=420&fansRow=2&ptype=1&speed=0&skin=1&isTitle=1&noborder=1&isWeibo=1&isFans=1&uid=5237139751&verifier=d50dec79&dpc=1"></iframe>


      </div>
      <div class="mLnk"><a href="http://weibo.com/u/5237139751" target="_blank">更多动态</a></div>
    </div>
    
    <div class="feedWB">
      <div class="tTit">订阅最新促销信息</div>
      <div class="feedSet">
       
        <input style="margin-top:1px;" name="email" id="user_email" type="text" class="i__stxt cccc" onfocus="javascript:var t=$(this); if(t.val()==t.attr('title')){t.val('');t.removeClass('cccc');}" onblur="javascript:var t=$(this); if(t.val()==''){t.val(t.attr('title'));t.addClass('cccc');}" value="请输入您的电子邮箱" title="请输入您的电子邮箱" >
        <input type="button" class="i__sbtn" onclick="add_email_list();" value="订阅">
        <script type="text/javascript">
var email = document.getElementById('user_email');
function add_email_list()
{
  if (check_email())
  {
    Ajax.call('user.php?act=email_list&job=add&email=' + email.value, '', rep_add_email_list, 'GET', 'TEXT');
  }
}
function rep_add_email_list(text)
{
  alert(text);
}
function cancel_email_list()
{
  if (check_email())
  {
    Ajax.call('user.php?act=email_list&job=del&email=' + email.value, '', rep_cancel_email_list, 'GET', 'TEXT');
  }
}
function rep_cancel_email_list(text)
{
  alert(text);
}
function check_email()
{
  if (Utils.isEmail(email.value))
  {
    return true;
  }
  else
  {
    alert('<?php echo $this->_var['lang']['email_invalid']; ?>');
    return false;
  }
}
</script>
      </div>
    </div>
    
  </div>
</div>
