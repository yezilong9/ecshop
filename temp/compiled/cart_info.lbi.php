<Div class="shop_txt"> <a href="flow.php"> 购物袋 ( <?php echo $this->_var['str']; ?> ) </a> 
  
  <?php if ($this->_var['goods']): ?>
  
  <div class="shopBody" id="shopBody">
    <ul>
      <?php $_from = $this->_var['goods']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods_0_59395800_1435810507');$this->_foreach['goods'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['goods']['total'] > 0):
    foreach ($_from AS $this->_var['goods_0_59395800_1435810507']):
        $this->_foreach['goods']['iteration']++;
?>
      <li class="shopWhite">
        <div class="shopLi_img"> <a href="<?php echo $this->_var['goods_0_59395800_1435810507']['url']; ?>"><img src="<?php echo $this->_var['goods_0_59395800_1435810507']['goods_thumb']; ?>" style="width:43px; height:43px;" alt="<?php echo $this->_var['goods_0_59395800_1435810507']['goods_name']; ?>"></a> </div>
        <div class="shopLi_txt"> <a  href="<?php echo $this->_var['goods_0_59395800_1435810507']['url']; ?>"><?php echo $this->_var['goods_0_59395800_1435810507']['short_name']; ?></a> </div>
        <div class="shopLi_del">
          <p class="shopLi_pink"><span style="color:#CF0000"><?php echo $this->_var['goods_0_59395800_1435810507']['goods_price']; ?></span><span >×<?php echo $this->_var['goods_0_59395800_1435810507']['goods_number']; ?></span></p>
          <a  href="javascript:" onClick="deleteCartGoods(<?php echo $this->_var['goods_0_59395800_1435810507']['rec_id']; ?>)" style="color:#005EA7; text-align:right">删除</a></div>
      </li>
      <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    </ul>
    <div class="shopSet">
      <div class="set_txt">
        <p>共<span style="color:#CF0000; font-weight:bold"><?php echo $this->_var['str']; ?></span>件商品共计<span class="set_gray">¥<?php echo $this->_var['amount']; ?></span></p>
        <a id="payfor" href="flow.php">去购物车结算</a> </div>
    </div>
  </div>
  
  <?php else: ?>
  <div class="shopBody">
    <p class="shopNo">购物袋内还没有商品，赶快选购吧！</p>
  </div>
  <?php endif; ?> 
  
</Div>
<script type="text/javascript">
function deleteCartGoods(rec_id)
{
Ajax.call('delete_cart_goods.php', 'id='+rec_id, deleteCartGoodsResponse, 'POST', 'JSON');
}

/**
 * 接收返回的信息
 */
function deleteCartGoodsResponse(res)
{
  if (res.error)
  {
    alert(res.err_msg);
  }
  else
  {
      document.getElementById('ECS_CARTINFO').innerHTML = res.content;
  }
}

</script> 
