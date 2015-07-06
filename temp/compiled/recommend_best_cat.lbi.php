<div class="tTit w1225" id="itemBest">
	<span class="tNm">[精品]</span>
    <ul class="tGud" id="best_clsiTit">
    
    <?php if ($this->_var['link'] != 345): ?>
	 <?php if ($this->_var['best_cat_name']): ?>
	  <?php $_from = $this->_var['best_cat_name']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'best');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['best']):
?>
	  
	  <li class="curr h2bg"><a onmouseover="change_tab_style('itemBest', 'li', this);change_show_cat(this,'best_classicSet');"><?php echo $this->_var['best']['cat_name']; ?></a></li>
      
	  <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	  
      <?php endif; ?>
	<?php else: ?>
     <li class="curr h2bg"><a href="http://dxc.txd168.com/category.php?id=728">男士专用</a></li>
    <?php endif; ?>  
    </ul>
  </div>
