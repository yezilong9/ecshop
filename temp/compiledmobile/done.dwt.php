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
</head>
<body>

<div class="views">
  
  <div class="view view-main">
    
    <div class="navbar">
      <div class="navbar-inner">
        <div class="left">
          <a href="javascript:history.go(-1);" class="back link">
            <i class="icon icon-back"></i>
            <h1 class="left navbar-tit">提交订单成功</h1>
          </a>
        </div>
        <div class="right">
          <a href="index.php" class="link icon-only"><i class="icon icon-home"></i></a>
        </div>
      </div>
    </div>
    
    
    <div class="pages navbar-through toolbar-through">
      
      <div data-page="order-succeed" class="page">
        
        <div class="page-content">
          
          <div class="section">
            
            <div class="order-succeed-info">
              <div class="order-succeed-info-hd">
                <h2 class="tit">提交订单成功</h2>
              </div>
              
              <div class="order-succeed-info-bd">
                <p>
订单号：<br /><strong class="orange"><?php echo $this->_var['order']['order_sn']; ?></strong><br />
订单状态：<?php if ($this->_var['order']['pay_status'] == '2'): ?>已付款<?php else: ?>未付款<?php endif; ?><br />
<?php if ($this->_var['order']['shipping_name']): ?>配送方式: <?php echo $this->_var['order']['shipping_name']; ?><?php endif; ?><br />
支付方式: <?php echo $this->_var['order']['pay_name']; ?><br />
支付金额: <?php echo $this->_var['total']['amount_formated']; ?><br />
<a href="user.php?act=order_detail&order_id=<?php echo $this->_var['order']['order_id']; ?>" style="color:red;">查看订单</a>
 <?php if ($this->_var['lucky_draw']): ?>
<a href="/active/201412/draw.php?<?php echo $this->_var['sign_message']; ?>&sign_message_md=<?php echo $this->_var['sign_message_md']; ?>" target="_blank">
<center><span style="font-size:20px; color:red;">点击抽奖活动(抽奖的订单不能退货退款)</span></center>
</a>
<?php endif; ?>
<?php if ($this->_var['pay_online']): ?><input type="button" value="前往支付" onclick="window.location.href='flow.php?step=txd_pay&order_id=<?php echo $this->_var['order']['order_id']; ?>&payment_method=<?php echo $this->_var['payment_method']; ?>'"/><?php endif; ?>
                </p>
              </div>            
              
            </div>
            
          </div>
          
        </div>
        
      </div>
      
    </div>
      
    </div>
    
  </div>
  
</div>

</body>
</html>