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
</head>
<body>

<div class="views">
  
  <div class="view view-main">
    
    <div class="navbar">
      <div class="navbar-inner">
        <div class="left">
          <a href="user.php?act=order_list" class="back link">
            <i class="icon icon-back"></i>
            <h1 class="left navbar-tit">订单详情</h1>
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
          <button type="reset" class="btn-cancel">取 消</button>
        </form>
      </div>
    </div>
    
    
    <div class="pages navbar-through toolbar-through">
      <div data-page="member-order-detail" class="page">
        
        <div class="page-content">
          
          <div class="section spacebetween">
            
            <div class="item">
              <div class="item-hd">
                <h2 class="tit">订单状态</h2>
              </div>
              <div class="item-bd">
                <p>
订单号：<?php echo $this->_var['order']['order_sn']; ?>  
<br />
订单状态：<?php echo $this->_var['order']['order_status']; ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->_var['order']['confirm_time']; ?><br />
付款状态：<?php echo $this->_var['order']['pay_status']; ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php if ($this->_var['order']['order_amount'] > 0): ?><?php echo $this->_var['order']['pay_online']; ?><?php endif; ?><?php echo $this->_var['order']['pay_time']; ?><br />

配送状态：<?php echo $this->_var['order']['shipping_status']; ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->_var['order']['shipping_time']; ?><br />
发货单：<?php if ($this->_var['order']['invoice_no']): ?>$order.invoice_no<?php else: ?>/<?php endif; ?>                  
                </p>
              </div>
            </div>
            
            
            <div class="item">
              <div class="item-hd">
                <h2 class="tit">收货人信息</h2>
              </div>
              <div class="item-bd">
                <p>
收货人姓名：<?php echo $this->_var['order']['consignee']; ?><br />
联系电话：<?php echo $this->_var['order']['tel']; ?><br />
E-mail：<?php echo $this->_var['order']['email']; ?><br />
详细地址：<?php echo $this->_var['order']['address']; ?>
                </p>
              </div>
            </div>
            
            
            <div class="item">
              <div class="item-hd">
                <h2 class="tit">商品支付信息</h2>
              </div>
              <div class="item-bd">
                <table class="shopping-list">
                  <thead>
                    <tr>
                      <th>商品名称</th>
                      <th>购买数量</th>
                      <th>小计</th>
                    </tr>
                  </thead>
                  <tbody>
				<?php $_from = $this->_var['goods_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods');if (count($_from)):
    foreach ($_from AS $this->_var['goods']):
?>
                    <tr>
                      <td><a href="goods.php?id=<?php echo $this->_var['goods']['goods_id']; ?>" target="_blank"><?php echo $this->_var['goods']['goods_name']; ?></a></td>
                      <td><?php echo $this->_var['goods']['goods_number']; ?></td>
                      <td><?php echo $this->_var['goods']['subtotal']; ?></td>
                    </tr>
				<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                  </tbody>
                </table>
                <p class="pay-info">
                <?php echo $this->_var['lang']['goods_all_price']; ?><?php if ($this->_var['order']['extension_code'] == "group_buy"): ?><?php echo $this->_var['lang']['gb_deposit']; ?><?php endif; ?>: 
				<strong><?php echo $this->_var['order']['formated_goods_amount']; ?></strong><br />
              <?php if ($this->_var['order']['discount'] > 0): ?>
              - <?php echo $this->_var['lang']['discount']; ?>: <strong><?php echo $this->_var['order']['formated_discount']; ?></strong><br />
              <?php endif; ?>
              <?php if ($this->_var['order']['tax'] > 0): ?>
              + <?php echo $this->_var['lang']['tax']; ?>: <strong><?php echo $this->_var['order']['formated_tax']; ?></strong><br />
              <?php endif; ?>
              <?php if ($this->_var['order']['shipping_fee'] > 0): ?>
              + 运费: <strong><?php echo $this->_var['order']['formated_shipping_fee']; ?></strong><br />
              <?php endif; ?>
              <?php if ($this->_var['order']['insure_fee'] > 0): ?>
              + <?php echo $this->_var['lang']['insure_fee']; ?>: <strong><?php echo $this->_var['order']['formated_insure_fee']; ?></strong><br />
              <?php endif; ?>
              <?php if ($this->_var['order']['pay_fee'] > 0): ?>
              + <?php echo $this->_var['lang']['pay_fee']; ?>: <strong><?php echo $this->_var['order']['formated_pay_fee']; ?></strong><br />
              <?php endif; ?>
              <?php if ($this->_var['order']['pack_fee'] > 0): ?>
              + <?php echo $this->_var['lang']['pack_fee']; ?>: <strong><?php echo $this->_var['order']['formated_pack_fee']; ?></strong><br />
              <?php endif; ?>
              <?php if ($this->_var['order']['card_fee'] > 0): ?>
              + <?php echo $this->_var['lang']['card_fee']; ?>: <strong><?php echo $this->_var['order']['formated_card_fee']; ?></strong><br />
              <?php endif; ?>
			  
              <?php if ($this->_var['order']['money_paid'] > 0): ?>
              - <?php echo $this->_var['lang']['order_money_paid']; ?>: <strong><?php echo $this->_var['order']['formated_money_paid']; ?></strong><br />
              <?php endif; ?>
              <?php if ($this->_var['order']['surplus'] > 0): ?>
              - <?php echo $this->_var['lang']['use_surplus']; ?>: <strong><?php echo $this->_var['order']['formated_surplus']; ?></strong><br />
              <?php endif; ?>
              <?php if ($this->_var['order']['integral_money'] > 0): ?>
              - <?php echo $this->_var['lang']['use_integral']; ?>: <strong><?php echo $this->_var['order']['formated_integral_money']; ?></strong><br />
              <?php endif; ?>
              <?php if ($this->_var['order']['bonus'] > 0): ?>
              - <?php echo $this->_var['lang']['use_bonus']; ?>: <strong><?php echo $this->_var['order']['formated_bonus']; ?></strong><br />
              <?php endif; ?>
				<?php if ($this->_var['order']['order_amount'] > 0): ?>应付金额: <strong>￥<?php echo $this->_var['order']['order_amount']; ?>元</strong><br /><?php endif; ?>
                </p>
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