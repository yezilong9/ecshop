<!DOCTYPE html>
<html>
<head>
<meta name="Generator" content="ECSHOP v2.7.3" />
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, minimum-scale=1, user-scalable=no, minimal-ui" />
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <meta name="renderer" content="webkit" />
  <title><?php echo $this->_var['page_title']; ?></title>
  <link href="themes/default/styles/master.css" rel="stylesheet" type="text/css" />
  <script type="text/javascript" src="themes/default/scripts/jquery1.8.min.js"></script>
  <?php echo $this->smarty_insert_scripts(array('files'=>'common.js')); ?>
  <?php echo $this->smarty_insert_scripts(array('files'=>'transport.js,utils.js')); ?>
</head>
<body>

<div class="views">
  
  <div class="view view-main">
    
    <div class="navbar">
      <div class="navbar-inner">
        <div class="left">
          <a href="javascript:history.go(-1);" class="back link">
            <i class="icon icon-back"></i>
            <h1 class="left navbar-tit">返回首页</h1>
          </a>
        </div>
        <div class="right">
          <a href="javascript:void(0);" class="link link-search  icon-only"><i class="icon icon-magnifier"></i></a>
          <a href="flow.php" class="link icon-only"><i class="icon icon-shoppingcart"></i></a>
        </div>
	    <form class="searchbar none" id="search_exit" data-search-list=".list-block-search" data-search-in=".item-title" data-searchbar-found=".searchbar-found" data-searchbar-not-found=".searchbar-not-found" action="search.php">
          <div class="searchbar-input">
            <input type="search" name="keywords" placeholder="品牌/商品名" />
            <a href="#" class="searchbar-clear"></a>
          </div>
          <button type="reset"id="search_button"class="btn-cancel">取 消</button>
        </form>
      </div>
    </div>
    
    
    <div class="pages navbar-through toolbar-through">
      
      <div data-page="products-template" class="page">
        
        <div class="page-content">
          
          <div class="products-sorting">

          </div>
          
          
          <div class="section">
            
            <div class="products">
              
              <div class="products-bd">
                <div class="items" id="goods_list">
				<?php if ($this->_var['goods_list']): ?>
				<?php $_from = $this->_var['goods_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods');$this->_foreach['goods_list'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['goods_list']['total'] > 0):
    foreach ($_from AS $this->_var['goods']):
        $this->_foreach['goods_list']['iteration']++;
?>
                  <div class="item">
                    <a href="<?php echo $this->_var['goods']['url']; ?>">
                      <div class="pic"><img src="<?php echo $this->_var['goods']['goods_thumb']; ?>" width="145" /></div>
                      <p class="item-info">
                        <span class="item-name"><?php echo $this->_var['goods']['goods_name']; ?></span>
                        <strong class="item-price"><?php echo $this->_var['goods']['shop_price']; ?></strong>
                      </p>
                    </a>
                  </div>
				<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
				<?php else: ?>
				没有结果
				<?php endif; ?>
                  </div>
              </div>
              
            </div>
            
          </div>
          
        </div>
        
      </div>
      
    </div>
    
  </div>
  
<input type="hidden" value="<?php echo $this->_var['pager']['page_next']; ?>" id="pager_url"/>  
</div>

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
				$("#goods_list").append(data);
			});
		}
	}
  });
});
</script >
<script type="text/javascript" src="themes/default/scripts/framework7.min.js"></script>
<script type="text/javascript" src="themes/default/scripts/frontend.js"></script>


</body>
</html>