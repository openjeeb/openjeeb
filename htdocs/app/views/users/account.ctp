<?php
//Persian Date
App::import('Vendor', 'PersianDate', array('file' => 'persian.date.php'));
$persianDate=new PersianDate();
?>
<div class="col-xs-16 col-md-14 col-md-offset-1">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2>حساب کاربری</h2>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-16 col-md-6 col-md-offset-2">
                    <p style="line-height: 4rem"><b>نام کاربری: </b>&nbsp; <span class="label label-info"><?php echo $this->Session->read('Auth.User.email'); ?></span></p>
                </div>
                <div class="col-xs-16 col-md-4">
                    <p style="line-height: 4rem"><b>اعتبار باقیمانده: </b> <span class="label label-info"> <?php if($remaining_days>0) {echo $persianDate->Convertnumber2farsi($remaining_days);} else {echo '0';} ?> روز</span></p>
                </div>
                <div  class="col-xs-16 col-md-4">
                    <p style="line-height: 4rem"><b>پیامک باقی مانده: </b><span class="label label-info"><?php if($remaining_sms>0) {echo $persianDate->Convertnumber2farsi($remaining_sms);} else {echo '0';} ?></span> </p>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-xs-16 col-md-8">
                    <div class="centered">
                        <?php echo $this->Html->link( $this->Html->image( 'extend_ad.png', array( 'alt' => 'تمدید اعتبار' ,'class'=>'img-responsive mobile-margin-bottom-20','style'=>'display:inherit;') ), array( 'controller' => 'users', 'action' => 'extend' ), array( 'escape' => false ) ); ?>
                    </div>
                </div>
                <div class="col-xs-16 col-md-8">
                    <div class="centered">
                        <?php echo $this->Html->link( $this->Html->image( 'change_pass_ad.png', array( 'alt' => 'تغییر رمز','class'=>'img-responsive','style'=>'display:inherit;' ) ), array( 'controller' => 'users', 'action' => 'edit' ), array( 'escape' => false ) ); ?>
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-xs-16 col-md-8">
                    <div class="centered">
                        <?php echo $this->Html->link( $this->Html->image( 'changemail.png', array( 'alt' => 'تغییر ایمیل','class'=>'img-responsive mobile-margin-bottom-20','style'=>'display:inherit;' ) ), array( 'controller' => 'users', 'action' => 'changemail' ), array( 'escape' => false ) ); ?>
                    </div>
                </div>
                <div class="col-xs-16 col-md-8">
                    <div class="centered">
                        <?php echo $this->Html->link( $this->Html->image( 'reminder.png', array( 'alt' => 'تنظیمات یادآور','class'=>'img-responsive','style'=>'display:inherit;' ) ), array('controller'=>'reminders'), array( 'escape' => false ) ); ?>
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-xs-16 col-md-8">
                    <div class="centered">
                        <?php echo $this->Html->link( $this->Html->image( 'resetdata.png', array( 'alt' => 'حذف کلیه اطلاعات' ,'class'=>'img-responsive','style'=>'display:inherit;') ), array('action'=>'resetData'), array( 'escape' => false, 'onclick'=>'return confirm(\''.sprintf('آیا مطمئنید که میخواهید تمام اطلاعات خود را در جیب پاک کنید؟').'\');' ) ); ?>
                    </div>
                </div>
                <div class="col-xs-16 col-md-8">
                    <div class="centered">
                        <?php echo $this->Html->link( $this->Html->image( 'backup.png', array( 'alt' => 'پشتیبان‌گیری از اطلاعات' ,'class'=>'img-responsive','style'=>'display:inherit;') ), array('action'=>'backup'), array( 'escape' => false, 'onclick'=>'return confirm(\''.sprintf('آیا مطمئنید که میخواهید از اطلاعات خود پشتیبان‌گیری کنید؟').'\');' ) ); ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<br/><br/><br/><br/>

<div class="col-xs-16 col-md-14 col-md-offset-1">
    <h2 align="center">سابقه سفارشات</h2>
    <div class="table-responsive"><table class="table table-striped table-hover table-bordered"  id="dataTable" cellpadding="0" cellspacing="0">
        <thead class="table-primary" >
            <th>شماره فاکتور</th>
            <th>پلن</th>
            <th>مبلغ</th>
            <th>تاریخ</th>
            <th>وضعیت</th>
            <th></th>
        </thead>
    <?php foreach ($orders as $order):	?>
    <?php $success=false; if($order['Order']['result']=='success'){$success=true;} ?>
	<tr>
            <td <?php if($success) {echo 'style="background-color:#b4edb4;"';} ?>>
                <?php echo $this->Html->link($order['Order']['id'], array( 'controller'=>'orders', 'action'=>'view', $order['Order']['id'] )); ?>
            </td>
            <td <?php if($success) {echo 'style="background-color:#b4edb4;"';} ?>><?php __($order['Order']['plan']); ?></td>
            <td <?php if($success) {echo 'style="background-color:#b4edb4;"';} ?>><?php echo $order['Order']['amount'];?></td>
            <td <?php if($success) {echo 'style="background-color:#b4edb4;"';} ?>><?php echo $order['Order']['created'];?></td>
            <td <?php if($success) {echo 'style="background-color:#b4edb4;"';} ?>><?php __($order['Order']['result']); ?></td><td <?php if($success) {echo 'style="background-color:#b4edb4;"';} ?>>
                <?php echo $this->Html->link('مشاهده', array( 'controller'=>'orders', 'action'=>'view', $order['Order']['id'] )); ?>
            </td>
	</tr>
    <?php endforeach; ?>
    </table></div>
    
</div>
<div class="clear" style="margin-bottom: 100px;"></div>
