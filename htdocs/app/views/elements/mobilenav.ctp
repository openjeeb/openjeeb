<nav class="navbar navbar-jeeb xs-only">
    <div class="container-fluid">
        <div class="navbar-header" style="text-align: center">
            <?php echo $this->Html->link($this->Html->image('jeeb_logo.png', array('alt'=> 'جیب','style'=>'width:100%;max-width:300px;margin:auto;')),'/',array('escape' => false,'class'=>'navbar-brand','style'=>'width:100%;text-align:center;')); ?>
        </div>
    </div>
</nav>
<div class="container-fluid  xs-only"  style="background-color: #676767;">
    <div id="header">
        <div class="row">
            <div class="">
                <nav class="navbar navbar-jeeb">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#jeebNavbar">
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <?php if(!$this->Session->read('Auth.User.id')): ?>
                            <div id="user-menu">
                                <ul>
                                    <li id="ssl" >
                                        <?php
                                        if (empty($_SERVER['HTTPS'])) :
                                            echo $this->Html->link($this->Html->image('unlock.png', array('alt'=> 'no-ssl','style'=>'display:block;')),'#',array('escape' => false));
                                        else :
                                            echo $this->Html->link($this->Html->image('lock.png', array('alt'=> 'ssl','style'=>'display:block;')),'#',array('escape' => false));
                                        endif;
                                        ?>
                                    </li>
                                    <li id="mobileLogin">ورود</li>
                                    <li class="mobile-signup" onClick="location.href='<?php echo $this->Html->url(array('controller'=>'users','action'=>'join')); ?>'"><?php echo $this->Html->link('ثبت نام',array('controller'=>'users','action'=>'join')); ?></li>
                                </ul>
                            </div>
                        <?php else: ?>
                            <div id="user-menu">
                                <ul>
                                    <li id="ssl" >
                                        <?php
                                        if (empty($_SERVER['HTTPS'])) :
                                            echo $this->Html->link($this->Html->image('unlock.png', array('alt'=> 'no-ssl','style'=>'display:block;')),'#',array('escape' => false));
                                        else :
                                            echo $this->Html->link($this->Html->image('lock.png', array('alt'=> 'ssl','style'=>'display:block;')),'#',array('escape' => false));
                                        endif;
                                        ?>
                                    </li>
                                    <li onClick="location.href='<?php echo $this->Html->url(array('controller'=>'users','action'=>'logout')); ?>'"><?php echo $this->Html->link('خروج',array('controller'=>'users','action'=>'logout')); ?></li>
                                    <li >
                                        <div>
                                            <div class="float-right remainingDaysHead" style="color: <?php echo ($remaining_days<=0)? '#FF7B7B' : 'inherit' ?>;" onclick="window.location='<?php echo $this->Html->url( array( 'controller'=>'users' , 'action'=>'extend' ) ) ?>'">
                                                <div>&nbsp;</div>
                                                <div><?php echo PersianLib::FA_( intval($remaining_days) ) ?></div>
                                            </div>
                                            <div class="float-right remainingSmsHead" class="float-right" style="color: <?php echo ($remaining_sms<=0)? '#FF7B7B' : 'inherit' ?>;" onclick="window.location='<?php echo $this->Html->url( array( 'controller'=>'reminders' ) ) ?>'">
                                                <div>&nbsp;</div>
                                                <div><?php echo PersianLib::FA_( intval($remaining_sms) ) ?></div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div id="user-id">
                                <ul>
                                    <li style="width: 100%;text-align: center;"><?php echo $this->Html->link($this->Session->read('Auth.User.email'),array('controller'=>'users','action'=>'account',$this->Session->read('Auth.User.id'))); ?></li>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="collapse navbar-collapse" id="jeebNavbar">
                        <?php if(!$this->Session->read('Auth.User.id')): ?>
                            <ul class="nav navbar-nav navbar-left">
                                <li><?php echo $this->Html->link('<i class="fa fa-angle-double-left"></i>&nbsp;'."<span>".'صفحه نخست'."</span>",array('controller'=>'pages','action'=>'home'),array('escape'=>false));  ?></li>
                                <li><?php echo $this->Html->link('<i class="fa fa-angle-double-left"></i>&nbsp;'."<span>".'جیب چیست؟'."</span>",array('controller'=>'pages','action'=>'whatisjeeb'),array('escape'=>false));  ?></li>
                                <li><?php echo $this->Html->link('<i class="fa fa-angle-double-left"></i>&nbsp;'."<span>".'امکانات جیب'."</span>",array('controller'=>'pages','action'=>'features'),array('escape'=>false));  ?></li>
                            </ul>
                        <?php else: ?>
                            <div class="row">
                                <div class="col-xs-8">
                                    <ul class="nav navbar-nav navbar-left">
                                        <li><?php echo $this->Html->link('<i class="fa fa-angle-double-left"></i>&nbsp;'."<span>".'پیشخوان'."</span>",array('controller'=>'reports','action'=>'dashboard'),array('escape'=>false)); ?></li>
                                        <li><?php echo $this->Html->link('<i class="fa fa-angle-double-left"></i>&nbsp;'."<span>".'هزینه'."</span>",array('controller'=>'expenses','action'=>'index'),array('escape'=>false));  ?></li>
                                        <li><?php echo $this->Html->link('<i class="fa fa-angle-double-left"></i>&nbsp;'."<span>".'درآمد'."</span>",array('controller'=>'incomes','action'=>'index'),array('escape'=>false));  ?></li>
                                        <li><?php echo $this->Html->link('<i class="fa fa-angle-double-left"></i>&nbsp;'."<span>".'تراکنش‌ها'."</span>",array('controller'=>'transactions','action'=>'index'),array('escape'=>false));  ?></li>
                                        <li><?php echo $this->Html->link('<i class="fa fa-angle-double-left"></i>&nbsp;'."<span>".'چک‌'."</span>",array('controller'=>'checks','action'=>'index'),array('escape'=>false));  ?></li>
                                        <li><?php echo $this->Html->link('<i class="fa fa-angle-double-left"></i>&nbsp;'."<span>".'وام‌'."</span>",array('controller'=>'loans','action'=>'index'),array('escape'=>false));  ?></li>
                                        <li><?php echo $this->Html->link('<i class="fa fa-angle-double-left"></i>&nbsp;'."<span>".'بدهی / طلب'."</span>",array('controller'=>'debts','action'=>'index'),array('escape'=>false));  ?></li>
                                        <li><?php echo $this->Html->link('<i class="fa fa-angle-double-left"></i>&nbsp;'."<span>".'حساب‌ها'."</span>",array('controller'=>'accounts','action'=>'index'),array('escape'=>false));  ?></li>
                                    </ul>
                                </div>
                                <div class="col-xs-8">
                                    <ul class="nav navbar-nav navbar-left">
                                        <li><?php echo $this->Html->link('<i class="fa fa-angle-double-left"></i>&nbsp;'."<span>".'اشخاص'."</span>",array('controller'=>'individuals','action'=>'index'),array('escape'=>false));  ?></li>
                                        <li><?php echo $this->Html->link('<i class="fa fa-angle-double-left"></i>&nbsp;'."<span>".'یادداشت'."</span>",array('controller'=>'notes','action'=>'index'),array('escape'=>false));  ?></li>
                                        <li><?php echo $this->Html->link('<i class="fa fa-angle-double-left"></i>&nbsp;'."<span>".'گزارش‌ها'."</span>",array('controller'=>'reports','action'=>'index'),array('escape'=>false));  ?></li>
                                        <li><?php echo $this->Html->link('<i class="fa fa-angle-double-left"></i>&nbsp;'."<span>".'یادآور'."</span>",array('controller'=>'reminders','action'=>'index'),array('escape'=>false));  ?></li>
                                        <li><?php echo $this->Html->link('<i class="fa fa-angle-double-left"></i>&nbsp;'."<span>".'بودجه بندی'."</span>",array('controller'=>'budgets','action'=>'index'),array('escape'=>false));  ?></li>
                                        <li><?php echo $this->Html->link('<i class="fa fa-angle-double-left"></i>&nbsp;'."<span>".'سرمایه'."</span>",array('controller'=>'investments','action'=>'index'),array('escape'=>false));  ?></li>
                                        <li><?php echo $this->Html->link('<i class="fa fa-angle-double-left"></i>&nbsp;'."<span>".'تمدید'."</span>",array('controller'=>'users','action'=>'extend'),array('escape'=>false));  ?></li>
                                    </ul>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </nav>
            </div>
        </div>
    </div>
</div>
<?php if(!$this->Session->read('Auth.User.id')): ?>
    <div class="container-fluid" id="mobile-login-bar" style="display:none;background-color: #676767">
        <div class="row">
            <div class="col-xs-16">
                <div style="padding: 10px;margin-bottom: 10px;">
                    <?php echo $this->Form->create('User', array('action' => 'login','class'=>'form-inline'));?>
                    <div class="form-group">
                        <?php echo $this->Form->label('User.email','نام کاربری (آدرس ایمیل): ',array('for'=>'email','style'=>'font-weight:normal;color:white;')); ?>
                        <?php echo $this->Form->text('User.email',array('class'=>'form-control','id'=>'email')); ?>
                    </div>
                    <div class="form-group">
                        <?php echo $this->Form->label('User.password','رمز عبور: ',array('for'=>'password','style'=>'font-weight:normal;color:white;')); ?>
                        <?php echo $this->Form->text('password',array('type'=>'password','class'=>'form-control','id'=>'password')); ?>
                    </div>
                    <?php echo $this->Form->button('ورود', array('type'=>'submit','class'=>'btn btn-default')); ?>
                    &nbsp;&nbsp;<?php echo $this->Html->link('رمز عبور خود را فراموش کرده اید؟',array('controller'=>'users','action'=>'forgotPassword'),array('class'=>'mobile-forggotpass'));?>
                    <?php echo $this->Form->end();?>
                </div>
            </div>
        </div>
        <div class="clear"></div>
    </div>
<?php endif; ?>