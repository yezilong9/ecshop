<?php echo $this->smarty_insert_scripts(array('files'=>'JCookie.js')); ?>
<script type="text/javascript">
var process_request = "<?php echo $this->_var['lang']['process_request']; ?>";
</script>
<script type="text/javascript">
//收藏本站 bbs.ecmoban.com
function AddFavorite(title, url) {
  try {
      window.external.addFavorite(url, title);
  }
catch (e) {
     try {
       window.sidebar.addPanel(title, url, "");
    }
     catch (e) {
         alert("抱歉，您所使用的浏览器无法完成此操作。\n\n加入收藏失败，请使用Ctrl+D进行添加");
     }
  }
}

</script>
<style type="text/css">
.third_login{background: url(themes/wanbiao/images/l__.gif) no-repeat top left; padding-left:18px;}
.alipay {background-position: 0 -126px;}
.taobao {background-position: 0 -358px;}
.qq {background-position: 0 -258px;}
.sina {background-position: 0 -391px;}
.weixin {background-position: 0 -289px;}
</style>

<div class="header">
  <?php echo $this->smarty_insert_scripts(array('files'=>'jquery-1.9.1.min.js,jquery.json.js,jquery.SuperSlide.js')); ?>
          <?php echo $this->smarty_insert_scripts(array('files'=>'transport.js,utils.js,ecmoban_common.js')); ?>

   <div class="r1 w1225 " style="position:relative;  z-index:999999999;">
   <div class="le" style="width:500px;">
	 <?php if (! $this->_var['user']['user_id']): ?>
      <span class="tFav">
      <a href="user.php?act=login"  class="f12 c666">登录</a>
      </span>
      <span class="sLine"></span>
      <span class="tFav">
      <a href="user.php?act=register"  class="f12 c666">注册</a>
      </span>
	  
	  <?php if ($this->_var['oath_login'] == 1): ?>
	  <span class="sLine"></span>
      <span class="tFav">
      <a class="third_login qq f12" title="用QQ帐号登录" href="user.php?act=oath&type=qq" target="_top">QQ登录</a>
      </span>
	  
	  <?php endif; ?>
	  
	  <span class="sLine"></span>
	  <span class="tFav">
      <a class="third_login sina f12" title="用新浪微博帐号登录" href="user.php?act=oath&type=weibo">新浪微博</a>
      </span>
	  
	  
	  <!--<span class="sLine"></span>
	  <span class="tFav">
      <a class="third_login alipay f12" title="用支付宝帐号登录" href="user.php?act=oath&type=alipay">支付宝</a>
      </span>
	  <span class="sLine"></span>
	  <span class="tFav">
      <a class="third_login taobao f12" title="用淘宝帐号登录" href="user.php?act=oath&type=taobao">淘宝</a>
      </span>
	  <span class="sLine"></span>
	  <span class="tFav">
      <a class="third_login weixin f12" title="用微信帐号登录" href="user.php?act=oath&type=weixin">微信</a>
      </span>
	<span class="sLine"></span>-->
	
	 <?php else: ?>
      <span class="tFav">
      <a href="javascript:void(0)"  class="f12 c666">欢迎您,</a>
      </span>
      <span class="tFav">
      <a href="user.php"  class="f12 c666"><?php echo $this->_var['user']['user_name']; ?></a>
      </span>
      </a>
     <span class="sLine"></span>
      <span class="tFav">
      <a href="user.php?act=logout"  class="f12 c666">退出</a>
      </span>
	 <?php endif; ?>
    </div>
    
    <div class="ri"  style="position:relative;width:395px;">
      <span class="ico c__phone"></span>
      <span class="tTel">020-87383888转8</span>
      <span class="sLine"></span>
		 <a href="user.php" class="f12 c666" style="display:block;width:60px;height:30px;float:right;margin-top:4px;position:absolute;right:208px;">会员中心</a>
		<div class="shop_txt_out" id="ECS_CARTINFO"  style="display:inline; position:absolute;right:130px; float:left;*width:80px; z-index:999999"><?php 
$k = array (
  'name' => 'cart_info',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?></div>
	  <a  class="wei" onmouseover="this.className='wei wei_on'" onmouseout="this.className='wei'" style="float:right;position:absolute;right:110px;margin-top:5px;">
		<img src="<?php echo $this->_var['code_url']; ?>" style="width:111px;height:111px;">
	  </a>
		<a href="http://portal.txd168.com" class="f12 c666" style="display:block;width:60px;height:30px;float:right;margin-top:4px;position:absolute;right:40px;color:#B01330;">招商加盟</a>
		 <a href="javascript:void(0);" onclick="AddFavorite('我的网站',location.href)" class="f12 c666" style="display:block;width:30px;height:30px;float:right;margin-top:4px;">收藏</a>
     </div>
   </div>
<style>
.logo .c_logo{border:0px solid red;display:block;float:left;width:190px;}
.city_show{border:0px solid black; float:left; width:100px; padding-top:15px;}
.city_show h2{padding-bottom:5px; padding-left:5px;}
.city_info{font-size:16px;font-weight:bold;color:#000;}
.toggle_city{border: 1px solid #EEE;padding: 2px 5px;line-height: 20px;font-size: 14px;color: #999;background: #FFF; margin-top:5px;
}

</style>


<div class="headBody">
    <div class="r2 w1225">
    <div class="headBox" >
      <div class="logo"  style="border:0px solid green;">
		  <a href="index.php" name="top" class="c_logo"><img src="<?php echo $this->_var['logo_pic']; ?>"  style="display:block;"/></a>
		  <!--<div class="city_show">
			  <h2><a class="city_info"  href="javascript:void(0);"><?php echo $this->_var['city_name']; ?></a></h2>
			  <a class="toggle_city" href="region.php">切换城市</a>
		  </div>-->
	  </div>
     
      <div class="wbPt ">
          <div id="slideBox_h" class="slideBox_h">
			<div class="hd">
				<ul><!--<li></li><li></li><li></li><li></li><li></li>--></ul>
			</div>
			<div class="bd_h">
				<ul>
					<li><a href="javascript:void(0)">省钱又方便  就来天下店</a></li>
					<!--<li><a href="#" target="_blank">省钱又方便  就来天下店</a></li>
					<li><a href="#" target="_blank">省钱又方便  就来天下店</a></li>
                    <li><a href="#" target="_blank">省钱又方便  就来天下店</a></li>
                    <li><a href="#" target="_blank">省钱又方便  就来天下店</a></li>-->
				</ul>
			</div>
		</div>
          <script type="text/javascript">
		jQuery(".slideBox_h").slide({mainCell:".bd_h ul",autoPlay:true});
		</script>
      </div>
	   
      <div id="search"  class="headSearch">
        <form id="searchForm" name="searchForm" method="get" action="search.php" onSubmit="return checkSearchForm()"  >
          <div class="headSearch_input">
            <input style="color:#140002;" name="keywords" type="text" id="keyword" value="<?php echo $this->_var['search_keywords']; ?>" placeholder="搜索 品牌/商品名" />
          </div>
          <div class="headSearch_btn">
            <input name="imageField" type="submit" value=""  style="cursor:pointer;" />
          </div>
        </form>
      </div>
      
    </div>
    </div>  
</div>

   
  <div class="headNav" style=" width:1225px; position:relative; z-index:9992 ">
      <div style=" position:relative;  float:left;">
      <div class="classNav"> <a class="classNav_a" href="allcate.php"><span class="c__category"></span>产品分类</a> </div>
     <div class="left_nav" style="position:absolute; margin-top:37px; left:0px; z-index:9993">
        <div class="leftNav" id="J_mainCata">
          <ul>
            <?php $_from = $this->_var['categories_pro']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'cat');$this->_foreach['categories_pro'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['categories_pro']['total'] > 0):
    foreach ($_from AS $this->_var['cat']):
        $this->_foreach['categories_pro']['iteration']++;
?>
            <li>
              <p class="leftNav_p0<?php echo $this->_foreach['categories_pro']['iteration']; ?>"><a class="a1" href="<?php echo $this->_var['cat']['url']; ?>" title="<?php echo htmlspecialchars($this->_var['cat']['name']); ?>"><?php echo htmlspecialchars($this->_var['cat']['name']); ?></a></p>
              <div class="childer_hide" >
                <?php $_from = $this->_var['cat']['cat_id']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'child');if (count($_from)):
    foreach ($_from AS $this->_var['child']):
?>
                <?php $_from = $this->_var['child']['cat_id']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'childer');if (count($_from)):
    foreach ($_from AS $this->_var['childer']):
?>
                <a href="<?php echo $this->_var['childer']['url']; ?>" target="_blank"><?php echo htmlspecialchars($this->_var['childer']['name']); ?></a>
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
              </div>
			  <div class="J_arrowBtn" style="top: 19px; display: block;"></div>
			  <div class="leftSubNav" id="J_subCata" style="opacity: 1; left: 210px; display: block; top: 0px;width:450px;">
        

          <div class="leftSubNav_list"  style="width:450px;">
            <div class="leftSubNav_left">
			          <?php $_from = $this->_var['cat']['cat_id']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'child');if (count($_from)):
    foreach ($_from AS $this->_var['child']):
?>
              <div class="leftSubNav_left_txt">
                <p class="p1" style=" background:none;"><a href="<?php echo $this->_var['child']['url']; ?>" target="_blank"><?php echo htmlspecialchars($this->_var['child']['name']); ?></a></p>
                <dl>
                  <?php $_from = $this->_var['child']['cat_id']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'childer');if (count($_from)):
    foreach ($_from AS $this->_var['childer']):
?>
                  <dd><a href="<?php echo $this->_var['childer']['url']; ?>" target="_blank"><?php echo htmlspecialchars($this->_var['childer']['name']); ?></a></dd>
                  <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
				  <div class="blank"></div>
                </dl>
              </div>
			     <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            </div>
			
            <div class="leftSubNav_list_right">
              <dl>
                <?php $_from = $this->_var['cat']['brands']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'brand');if (count($_from)):
    foreach ($_from AS $this->_var['brand']):
?>
                <dd class="pin" onmouseover="this.className='pin pin_on'" onmouseout="this.className='pin'">
                  <?php if ($this->_var['brand']['brand_logo']): ?>
                   <p class="pin_m"><a href="<?php echo $this->_var['brand']['url']; ?>" target="_blank" class="pin_img" ><img border="0" width="78" height="38" src="data/brandlogo/<?php echo $this->_var['brand']['brand_logo']; ?>" alt="<?php echo htmlspecialchars($this->_var['brand']['brand_name']); ?> (<?php echo $this->_var['brand']['goods_num']; ?>)" /></a>
                   <span class="red_y"><?php echo htmlspecialchars($this->_var['brand']['brand_name']); ?></span></p>
                  <?php endif; ?>
                </dd>
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
              </dl>
              
            </div>
          </div>
       
         
        </div>
		<div class="blank"></div>
            </li>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
          </ul>
        </div>
        
        
      </div>
    </div>
      
      <div class="subNav">
        <ul>
              <li> <a href="index.php"><?php echo $this->_var['lang']['home']; ?></a></li>
              <?php $_from = $this->_var['navigator_list']['middle']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'nav');$this->_foreach['nav_middle_list'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['nav_middle_list']['total'] > 0):
    foreach ($_from AS $this->_var['nav']):
        $this->_foreach['nav_middle_list']['iteration']++;
?>
              <li ><a href="<?php echo $this->_var['nav']['url']; ?>" ><?php echo $this->_var['nav']['name']; ?></a></li>
              <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
      
		  <!--<?php if ($this->_var['user']['user_id']): ?>
		  <li><a href="print.php?act=index" target="_blank"  >快讯打印</a> </li>
		  <?php endif; ?>
		  <li><a href="http://tm.gotogame.com.cn" target="_blank"  >天猫代购</a> </li>-->
        </ul>
      </div>
      <div class="wbPt2">
     <!-- <i class="c__corner"></i>
      <ul class="p1 c__fun">
        <li><a href="#" target="_blank" rel=" nofollow">品牌授权</a></li>
        <li><a href="#" target="_blank" rel=" nofollow">全球联保</a></li>
        <li><a href="#" target="_blank" rel=" nofollow">银行分期</a></li>
      </ul>-->
    </div>
       
      
  </div>

  
  </div>
  
  
  
  
  
  
  
  
  