<div class="slideTxtBox100 NewsSet">
  <div class="hd newsTit" >
    <ul>
	  <?php $_from = $this->_var['info_cats']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
			<li><?php echo $this->_var['item']; ?></li>
	  <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    </ul>
  </div>

  <div class="bd tBdy NewsSetWall ">
  
<?php $_from = $this->_var['info_lists']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'info');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['info']):
?>  
	<ul>
      <div style="display: block;">
        <div class="c1 mr10">
          <div class="r1"> 
          <a href="<?php echo $this->_var['info']['475x340']['link_url']; ?>" target="_blank"><img src="<?php echo $this->_var['info']['475x340']['img_file']; ?>" width="475" height="340" border="0"></a>
		  
          <div class="tInfo1 f24"><a href="<?php echo $this->_var['info']['475x340']['link_url']; ?>"  target="_blank"><?php echo htmlspecialchars($this->_var['info']['475x340']['title_describe']); ?></a></div>
          <div class="tInfo2"><a href="<?php echo $this->_var['info']['475x340']['link_url']; ?>"  target="_blank"><?php echo htmlspecialchars($this->_var['info']['475x340']['content_describe']); ?></a></div>
          </div>
          
          </div>
        <div class="c2 mr10">
          <div class="c2A mr10"> 
             <div class="r1 mb10">
                <a href="<?php echo $this->_var['info']['240x160']['0']['link_url']; ?>" target="_blank"><img src="<?php echo $this->_var['info']['240x160']['0']['img_file']; ?>" width="240" height="160" border="0"></a>
                <div class="tInfo1"><a href="<?php echo $this->_var['info']['240x160']['0']['link_url']; ?>" target="_blank"><?php echo $this->_var['info']['240x160']['0']['title_describe']; ?></a></div>
              </div>
              <div class="r2">
                 <a href="<?php echo $this->_var['info']['240x160']['1']['link_url']; ?>" target="_blank"><img src="<?php echo $this->_var['info']['240x160']['1']['img_file']; ?>" width="240" height="160" border="0"></a>
                <div class="tInfo1"> <a href="<?php echo $this->_var['info']['240x160']['1']['link_url']; ?>" target="_blank"><?php echo htmlspecialchars($this->_var['info']['240x160']['1']['title_describe']); ?></a></div>
                </div>
            </div>
            
          <div class="c2B">
           <div class="r1">
            <div class="tInfo1"> <a href="<?php echo $this->_var['info']['240x330']['link_url']; ?>" target="_blank"><?php echo htmlspecialchars($this->_var['info']['240x330']['title_describe']); ?></a></div>
            <a href="<?php echo $this->_var['info']['240x330']['link_url']; ?>" target="_blank"><img src="<?php echo $this->_var['info']['240x330']['img_file']; ?>" width="240" height="330" border="0"></a>
			<div class="tInfo2"><?php echo htmlspecialchars($this->_var['info']['240x330']['content_describe']); ?></div>
            </div></div>
          <div class="c2C mt10">
            <ul>
               <li><?php 
$k = array (
  'name' => 'article',
  'num' => '5',
  'article' => $this->_var['info']['info_article'],
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?></li>
            </ul>
          </div>
        </div>
        <div class="c3"> <div class="r1">
          <div class="tInfo1"><a href="<?php echo $this->_var['info']['240x320']['link_url']; ?>" target="_blank"><?php echo htmlspecialchars($this->_var['info']['240x320']['title_describe']); ?></a></div>
		  <a href="<?php echo $this->_var['info']['240x320']['link_url']; ?>" target="_blank"><img src="<?php echo $this->_var['info']['240x320']['img_file']; ?>" width="240" height="320" border="0"></a>
          <div class="tInfo2"><a href="<?php echo $this->_var['info']['240x320']['link_url']; ?>" target="_blank"><?php echo htmlspecialchars($this->_var['info']['240x320']['content_describe']); ?></a></div>         
           </div></div>
      </div>
    </ul>
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
   
    
  </div>
</div>
<script type="text/javascript">
jQuery(".slideTxtBox100").slide({autoPlay:true});

</script>
