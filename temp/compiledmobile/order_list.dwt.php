<!DOCTYPE html>
<html>
<head>
<meta name="Generator" content="ECSHOP v2.7.3" />
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, minimum-scale=1, user-scalable=no, minimal-ui" />
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <title><?php echo $this->_var['page_title']; ?></title>
  <link href="themes/default/styles/master.css" rel="stylesheet" type="text/css" />
  <script type="text/javascript" src="themes/default/scripts/jquery1.8.min.js"></script>
  <script type="text/javascript" src="themes/default/scripts/framework7.min.js"></script>
  <script type="text/javascript" src="themes/default/scripts/frontend.js"></script>
<?php echo $this->smarty_insert_scripts(array('files'=>'utils.js')); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'transport.js,common.js,user.js')); ?>
<script type="text/javascript">
<?php $_from = $this->_var['lang']['merge_order_js']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
  var <?php echo $this->_var['key']; ?> = "<?php echo $this->_var['item']; ?>";
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
</script>
</head>
<body>

<div class="views">
  
  <div class="view view-main">
    
    <div class="navbar">
      <div class="navbar-inner">
        <div class="left">
          <a href="user.php" class="back link">
            <i class="icon icon-back"></i>
            <h1 class="left navbar-tit">我的订单</h1>
          </a>
        </div>
        <div class="right">
        
          <a href="flow.php" class="link icon-only"><i class="icon icon-shoppingcart"></i></a>
        </div>
      </div>
    </div>
    
    
    <div class="pages navbar-through toolbar-through">
      <div data-page="member-orders" class="page">
        
        <div class="page-content">
          
          <div class="section">
            
            <div class="list-block media-list">
              <ul id="order_list">
			   <?php $_from = $this->_var['orders']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['item']):
?>
                <li>
                  <a href="user.php?act=order_detail&order_id=<?php echo $this->_var['item']['order_id']; ?>" class="item-link item-content">
                    <div class="item-media">
                      <img src="/<?php echo $this->_var['item']['goods_thumb']; ?>" width="60" height="60" />
                    </div>
                    <div class="item-inner">
                      <div class="item-title">订单号：<?php echo $this->_var['item']['order_sn']; ?></div>
                      <div class="item-date">下单日期：<?php echo $this->_var['item']['order_time']; ?></div>
                      <div class="item-other">
                        总价：<strong class="orange"><?php echo $this->_var['item']['total_fee']; ?></strong> <br />
						状态：<strong class="orange"><?php echo $this->_var['item']['order_status']; ?></strong>
                      </div>
                    </div>
                  </a>
                </li>
			   <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
			   
              </ul>
            </div>          
            
          </div>
          
        </div>
        
      </div>
    </div>
    
  </div>
  
</div>

<input type="hidden" value="<?php echo $this->_var['pager']['page_next']; ?>" id="pager_url"/>  
<script>
var page = 1;
$(document).ready(function(){
  $(document).scroll(function(){
	var scrolltop = document.documentElement.scrollTop || document.body.scrollTop;
	var tops = $(document).scrollTop();
	var sctop = $(document).height() - $(window).height();
	if (tops >= sctop){
		var Url = $('#pager_url').val();
		Url = Url.replace(/&page(.*?)&/,"&");
		if(page < '<?php echo $this->_var['pager']['page_count']; ?>'*1){
			page = ++page;
			Url += '&json=1&page='+page;
			$.get(Url, function (data) {
				$("#order_list").append(data);
			});
		}
	}
  });
});
</script >
</body>
</html>