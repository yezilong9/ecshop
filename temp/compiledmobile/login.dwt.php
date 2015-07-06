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

<?php echo $this->smarty_insert_scripts(array('files'=>'common.js,user.js,transport.js')); ?>
</head>
<body>

<div class="views">
  
  <div class="view view-main">
    
    <div class="navbar">
      <div class="navbar-inner">
        <div class="left">
          <a href="javascript:history.go(-1);" class="back link">
            <i class="icon icon-back"></i>
            <h1 class="left navbar-tit">会员登录</h1>
          </a>
        </div>
        <div class="right">
          <a href="index.php" class="link icon-only"><i class="icon icon-home"></i></a>
        </div>
      </div>
    </div>
    
    
    <div class="pages navbar-through toolbar-through">
      
      <div data-page="member-login" class="page">
        
        <div class="page-content">
          
          <div class="section">
            <form class="list-block inset" id="J_Login"  name="formLogin" action="user.php" method="post" onSubmit="return userLogin()">
              <fieldset class="field">
                <ul>
                  <li>
                    <div class="item-content">
                      <div class="item-media"><i class="icon icon-member"></i></div>
                      <div class="item-inner">
                        <div class="item-input">
                          <input type="text" name="username" id="J_UserNameTxt" placeholder="会员名" />
                        </div>
                      </div>
                    </div>
                  </li>
                  <li>
                    <div class="item-content">
                      <div class="item-media"><i class="icon icon-lock"></i></div>
                      <div class="item-inner">
                        <div class="item-input">
                          <input type="password" name="password" id="J_PassWordTxt" placeholder="密码" />
                        </div>
                      </div>
                    </div>
                  </li>
				 <?php if ($this->_var['enabled_captcha']): ?>
                  <li>
                    <div class="item-content">
                      <div class="item-media"><i class="icon icon-key"></i></div>
                      <div class="item-inner">
                        <div class="item-input">
                          <input type="test"  name="captcha"  id="J_AuthCodeTxt" id="J_PassWordTxt" placeholder="验证码" style="border:none;"/>
                        </div>
                        <div class="item-after">
                          <img src="captcha.php?is_login=1&<?php echo $this->_var['rand']; ?>" alt="captcha" style="vertical-align: middle;cursor: pointer;" onClick="this.src='captcha.php?is_login=1&'+Math.random()" />
                        </div>
                      </div>
                    </div>
                  </li>
				  <?php endif; ?>
				  
                </ul>
              </fieldset>
              <fieldset class="field">
			  <input type="hidden" name="act" value="act_login" />
			  <input type="hidden" name="back_act" value="<?php echo $this->_var['back_act']; ?>" />
			  <input type="submit" class="button button-big button-fill color-red button-submit" value="登 录">

                <div class="row links">
                  <a href="user.php?act=register">极速免费注册</a>
                  
                </div>
              </fieldset>
            </form>
          </div>
          
        </div>
        
      </div>
      
    </div>
    
  </div>
  
</div>

<script type="text/javascript">
var process_request = "<?php echo $this->_var['lang']['process_request']; ?>";
<?php $_from = $this->_var['lang']['passport_js']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
var <?php echo $this->_var['key']; ?> = "<?php echo $this->_var['item']; ?>";
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
var username_exist = "<?php echo $this->_var['lang']['username_exist']; ?>";
</script>
</body>
</html>