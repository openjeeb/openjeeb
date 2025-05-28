<?php if(!$this->Session->read('Auth.User.id')): ?>
    <div class="container-fluid hidden-xs" id="login-bar" style="display:none;">
        <div class="row">
            <div class="col-md-15 col-md-offset-1">
                <div style="padding: 3px;">
                    <?php echo $this->Form->create('User', array('action' => 'login','class'=>'form-inline'));?>
                    <div class="form-group">
                        <?php echo $this->Form->label('User.email','نام کاربری (آدرس ایمیل): ',array('for'=>'email')); ?>
                        <?php echo $this->Form->text('User.email',array('class'=>'form-control','id'=>'email')); ?>
                    </div>
                    <div class="form-group">
                        <?php echo $this->Form->label('User.password','رمز عبور: ',array('for'=>'password')); ?>
                        <?php echo $this->Form->text('password',array('type'=>'password','class'=>'form-control','id'=>'password')); ?>
                    </div>
                    <?php echo $this->Form->button('ورود', array('type'=>'submit','class'=>'btn btn-default')); ?>
                    <?php echo $this->Form->end();?>
                    &nbsp;&nbsp;<?php echo $this->Html->link('رمز عبور خود را فراموش کرده اید؟',array('controller'=>'users','action'=>'forgotPassword'));?>
                </div>
            </div>
        </div>
        <div class="clear"></div>
    </div>
<?php endif; ?>

<div id="header" class="container hidden-xs">
    <div class="row">
        <div id="logo" class="col-md-5">
            <?php echo $this->Html->link($this->Html->image('jeeb_logo.png', array('alt'=> 'جیب')),'/',array('escape' => false)); ?>
        </div>
        <div class="col-md-11">
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
                        <li id="topLogin">ورود</li>
                    </ul>
                </div>
                <div id="user-join">
                    <ul>
                        <li onClick="location.href='<?php echo $this->Html->url(array('controller'=>'users','action'=>'join')); ?>'"><?php echo $this->Html->link('ثبت نام',array('controller'=>'users','action'=>'join')); ?></li>
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
                        <li><?php echo $this->Html->link($this->Session->read('Auth.User.email'),array('controller'=>'users','action'=>'account',$this->Session->read('Auth.User.id'))); ?></li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>