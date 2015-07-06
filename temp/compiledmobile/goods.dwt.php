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
            <h1 class="left navbar-tit">详情</h1>
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
          <button type="reset" id="search_button" class="btn-cancel">取 消</button>
        </form>
      </div>
    </div>
    
    
    <div class="pages navbar-through toolbar-through">
      
      <div data-page="product" class="page">
        
        <div class="page-content">
          
          <div class="section">
            
            <div class="product">
              
              <div class="product-hd">
                
                <div class="product-focus slider-container">
                  
                  <div class="slider-wrapper">
				  
				  <?php $_from = $this->_var['pictures']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['item']):
?>
                    <div class="slider-slide"><img src="<?php echo $this->_var['item']['img_url']; ?>" width="320" height="320" /></div>
				  <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                  </div>
                  
                  <div class="slider-pagination"></div>
                </div> 
                
              </div>
              
              
              <div class="product-bd">
                <div class="product-info">
                  <div class="product-info-main">
                    <div class="product-name"><?php echo $this->_var['goods']['goods_style_name']; ?></div>
                    <strong class="product-price1"><?php echo $this->_var['goods']['shop_price_formated']; ?></strong>
                    <del class="product-price2">市场价格：<?php echo $this->_var['goods']['market_price']; ?></del>
                  </div>
                  <div class="product-info-side"><a class="link-addfavorites" href="javascript:collect(<?php echo $this->_var['goods']['goods_id']; ?>)"><i class="icon icon-star"></i>收藏</a></div>
                </div>
                <div class="row product-inventory">
                   <!--<div>商品库存：<strong><?php echo $this->_var['goods']['goods_number']; ?> 件</strong></div>-->
                   <div>起购数量：<strong><?php echo $this->_var['goods']['start_num']; ?> 件</strong></div>
                </div>
                
                <div class="product-detail">
                  <div class="product-detail-hd">
                    <h2 class="tit">商品详情</h2>
                  </div>
                  
                  <div class="product-detail-bd">
                    <?php echo $this->_var['goods']['goods_desc']; ?>
                  </div>
                  
                </div>
                
              </div>
              
            </div>
            
          </div>
          
        </div>
        
      </div>
      
    </div>
    
    
    <div class="toolbar">
      <div class="toolbar-inner">
        <a class="button button-big button-fill color-orange" href="goods.php?id=<?php echo $this->_var['goods']['goods_id']; ?>&state=add_cart">加入购物车</a>
        <a class="button button-big button-fill color-red" href="goods.php?id=<?php echo $this->_var['goods']['goods_id']; ?>&state=buy">立即购买</a>
      </div>
    </div>
    
  </div>
  
</div>


<script type="text/javascript" src="themes/default/scripts/framework7.min.js"></script>
<script type="text/javascript" src="themes/default/scripts/frontend.js"></script>


</body>
</html>