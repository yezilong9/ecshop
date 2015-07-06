<!DOCTYPE html>
<html class="android">
<head>
<meta name="Generator" content="ECSHOP v2.7.3" />
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, minimum-scale=1, user-scalable=no, minimal-ui" />
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <meta name="renderer" content="webkit" />
  <title><?php echo $this->_var['page_title']; ?></title>
  <link href="themes/default/styles/master.css" rel="stylesheet" type="text/css" />
</head>
<body>

<div class="views">
  
  <div class="view view-main">
    
		
		<?php echo $this->fetch('library/header.lbi'); ?>

    
    
    <div class="pages navbar-through toolbar-through">
      <div data-page="home" class="page">
        
        <div class="page-content">
          
			
			<?php echo $this->fetch('library/meun.lbi'); ?>
          
          
          <div class="section">
            
            <div class="home-focus slider-container">
              
              <div class="slider-wrapper">
			  

				<?php $_from = $this->_var['xml']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'ads');if (count($_from)):
    foreach ($_from AS $this->_var['ads']):
?>
				<div class="slider-slide">
					<a href="<?php echo $this->_var['ads']['url']; ?>"><img src="<?php echo $this->_var['ads']['src']; ?>" width="320" /></a>
				</div>
				<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
              </div>
              
              <div class="slider-pagination"></div>
            </div> 
            
            
            <div class="products">
              
              <div class="products-hd">
                <h2 class="tit">[新品上市]</h2>
                <a class="link-more" href="search.php?intro=new">更多&nbsp;&raquo;</a>
              </div>
              
              
              <div class="products-bd">
                <div class="items clearfix">
				<?php $_from = $this->_var['new_goods']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'n_goods');$this->_foreach['new_goods'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['new_goods']['total'] > 0):
    foreach ($_from AS $this->_var['n_goods']):
        $this->_foreach['new_goods']['iteration']++;
?>
					<?php if (($this->_foreach['new_goods']['iteration'] - 1) < 6): ?>
                  <div class="item">
                    <a href="<?php echo $this->_var['n_goods']['url']; ?>">
                      <div class="item-pic"><img src="<?php echo $this->_var['n_goods']['thumb']; ?>" width="145" /></div>
                      <p class="item-info">
                        <span class="item-name"><?php echo $this->_var['n_goods']['short_style_name']; ?></span>
                        <strong class="item-price"><?php echo $this->_var['n_goods']['shop_price']; ?></strong>
                      </p>
                    </a>
                  </div>
					<?php endif; ?>
				<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                </div>
              </div>
              
            </div>
            
            
            <div class="products">
              
              <div class="products-hd">
                <h2 class="tit">[火爆热销]</h2>
                <a class="link link-more" href="search.php?intro=hot">更多&nbsp;&raquo;</a>
              </div>
              
              
              <div class="products-bd">
                <div class="items clearfix">
				<?php $_from = $this->_var['hot_goods']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'h_goods');$this->_foreach['hot_goods'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['hot_goods']['total'] > 0):
    foreach ($_from AS $this->_var['h_goods']):
        $this->_foreach['hot_goods']['iteration']++;
?>
					<?php if (($this->_foreach['hot_goods']['iteration'] - 1) < 6): ?>
                  <div class="item">
                    <a href="<?php echo $this->_var['h_goods']['url']; ?>">
                      <div class="item-pic"><img src="<?php echo $this->_var['h_goods']['thumb']; ?>" width="145" /></div>
                      <p class="item-info">
                        <span class="item-name"><?php echo $this->_var['h_goods']['short_style_name']; ?></span>
                        <strong class="item-price"><?php echo $this->_var['h_goods']['shop_price']; ?></strong>
                      </p>
                    </a>
                  </div>
					<?php endif; ?>
				<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                </div>
              </div>
              
            </div>
            
            
            <div class="products">
              
              <div class="products-hd">
                <h2 class="tit">[精品推荐]</h2>
                <a class="link-more" href="search.php?intro=best">更多&nbsp;&raquo;</a>
              </div>
              
              
              <div class="products-bd">
                <div class="items clearfix">
				<?php $_from = $this->_var['best_goods']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'b_goods');$this->_foreach['best_goods'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['best_goods']['total'] > 0):
    foreach ($_from AS $this->_var['b_goods']):
        $this->_foreach['best_goods']['iteration']++;
?>
					<?php if (($this->_foreach['best_goods']['iteration'] - 1) < 6): ?>
                  <div class="item">
                    <a href="<?php echo $this->_var['b_goods']['url']; ?>">
                      <div class="item-pic"><img src="<?php echo $this->_var['b_goods']['thumb']; ?>" width="145" /></div>
                      <p class="item-info">
                        <span class="item-name"><?php echo $this->_var['b_goods']['short_style_name']; ?></span>
                        <strong class="item-price"><?php echo $this->_var['b_goods']['shop_price']; ?></strong>
                      </p>
                    </a>
                  </div>
					<?php endif; ?>
				<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                </div>
              </div>
              
            </div>
            
          </div>
          
        </div>
        
      </div>
    </div>
    
  </div>
           
</div>

<script type="text/javascript" src="themes/default/scripts/framework7.min.js"></script>
<script type="text/javascript" src="themes/default/scripts/frontend.js"></script>



</body>
</html>
