<?php $_from = $this->_var['article']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'articles_0_64027200_1435810507');if (count($_from)):
    foreach ($_from AS $this->_var['articles_0_64027200_1435810507']):
?>
  <li><a href="<?php echo $this->_var['articles_0_64027200_1435810507']['url']; ?>"><?php echo $this->_var['articles_0_64027200_1435810507']['title']; ?></a></li>
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
