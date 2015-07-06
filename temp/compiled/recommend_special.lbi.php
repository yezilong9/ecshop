<?php if ($this->_var['special_goods']): ?>
	<?php if ($this->_var['cat_rec_sign'] != 1): ?>
	<div class="box">
		<div class="centerPadd ">
			<div class="itemTit special tTit_hot" id="itemSpecial">
				<div class="tNm">[特产]</div>
				<div class="tGud">
					<?php if ($this->_var['cat_rec'] [ 4 ]): ?>
	
					<?php $_from = $this->_var['cat_rec']['4']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'rec_data');if (count($_from)):
    foreach ($_from AS $this->_var['rec_data']):
?>
					<h2 class="h2bg">
						<a href="javascript:void(0)" onmouseover="change_tab_style('itemSpecial', 'h2', this);get_cat_recommend(4, <?php echo $this->_var['rec_data']['cat_id']; ?>)"><?php echo $this->_var['rec_data']['cat_name']; ?></a>
					</h2>
					<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
					
					<?php endif; ?>
				</div>
				</div>

		<div id="show_special_area" class="clearfix goodsBox">
		<?php endif; ?>


		<?php $_from = $this->_var['special_goods']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods');if (count($_from)):
    foreach ($_from AS $this->_var['goods']):
?>

		<li style="float: left; width: 232px;" class="goodsItem_kuang goodsItem_kuang2"> 
			<div class="goodsItem_new" onmouseover="this.className='goodsItem_new goodsItem_on'" onmouseout="this.className='goodsItem_new'">
				<div class="fj">
					<a href="<?php echo $this->_var['goods']['url']; ?>" target="_blank" class="fj_a1"><span class="biao pngfix"></span><span>详情介绍</span></a>
					<a href="special_product.php" target="_blank" class="fj_a2"><span class="biao pngfix"></span><span>更多特产</span></a>
				</div>
				<a href="<?php echo $this->_var['goods']['url']; ?>" target="_blank"><img src="<?php echo $this->_var['goods']['thumb']; ?>" alt="<?php echo htmlspecialchars($this->_var['goods']['name']); ?>" class="goodsimg"></a><br>
				<p class="lujin">
					<strong><a class="lj1" href="<?php echo $this->_var['goods']['url']; ?>" target="_blank" title="<?php echo htmlspecialchars($this->_var['goods']['name']); ?>"><?php echo htmlspecialchars($this->_var['goods']['name']); ?></a></strong>  - <a href="<?php echo $this->_var['goods']['url']; ?>" target="_blank" title="<?php echo htmlspecialchars($this->_var['goods']['name']); ?>"><?php echo htmlspecialchars($this->_var['goods']['name']); ?></a>
				</p>
				<div class="clearfix">
				<font class="f1" style="color:#B01330">  
				<?php if ($this->_var['goods']['promote_price'] != ""): ?>
					<?php echo $this->_var['goods']['promote_price']; ?>
				<?php else: ?>
					<?php echo $this->_var['goods']['shop_price']; ?>
				<?php endif; ?>
				</font>
				<font class="marketf1"><?php echo $this->_var['goods']['market_price']; ?></font>
				<!--<a target="_blank" href="<?php echo $this->_var['goods']['url']; ?>" class="pinlun">已售出(<?php echo $this->_var['goods']['sales_volume']; ?>)</a>-->
				</div>
			</div>
		</li>
		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
		<?php if ($this->_var['cat_rec_sign'] != 1): ?>
	</div>
	<div class="more_hot"><a href="special_product.php"  target="_blank">更多特产>></a></div>
	</div>
</div>
<div class="blank5"></div>
		<?php endif; ?>
	<?php endif; ?>