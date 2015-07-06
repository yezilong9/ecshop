          <div class="header-nav">
            <div class="header-nav-bd">
              <ul class="items">
                <li <?php if ($this->_var['ecs_session'] == 'index'): ?>class="current"<?php endif; ?>>
                  <a href="index.php">
                    <i class="icon icon-fire"></i><span class="label-for-icon">热门</span>
                  </a>
                </li>
                <li <?php if ($this->_var['ecs_session'] == 'allcate'): ?>class="current"<?php endif; ?>>
                  <a href="allcate.php">
                    <i class="icon icon-category"></i><span class="label-for-icon">分类</span>
                  </a>
                </li>
                <li <?php if ($this->_var['ecs_session'] == 'topic'): ?>class="current"<?php endif; ?>>
                  <!-- <a href="topic.php">-->
                    <a href="christmas_activity.php">
                    <i class="icon icon-gift"></i><span class="label-for-icon">活动</span>
                  </a>
                </li>
                <li <?php if ($this->_var['ecs_session'] == 'user'): ?>class="current"<?php endif; ?>>
                  <a href="user.php">
                    <i class="icon icon-member"></i><span class="label-for-icon">帐户</span>
                  </a>
                </li>
              </ul>
            </div>
          </div>