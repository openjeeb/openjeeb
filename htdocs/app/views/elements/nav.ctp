<?php if($this->Session->read('Auth.User.id')): ?>
<?php $groupId=$this->Session->read('Auth.User.user_group_id'); ?>

<?php if($groupId==2): ?>
<nav class="navbar navbar-jeeb hidden-xs">
    <ul class="nav navbar-nav">
        <li><?php echo $this->Html->link('پیشخوان',array('controller'=>'reports','action'=>'dashboard')); ?></li>
        <li><?php echo $this->Html->link('هزینه',array('controller'=>'expenses','action'=>'index')); ?></li>
        <li><?php echo $this->Html->link('درآمد',array('controller'=>'incomes','action'=>'index')); ?></li>
        <li><?php echo $this->Html->link('تراکنش‌ها',array('controller'=>'transactions','action'=>'index')); ?></li>
        <li><?php echo $this->Html->link('چک‌',array('controller'=>'checks','action'=>'index')); ?></li>
        <li><?php echo $this->Html->link('وام‌',array('controller'=>'loans','action'=>'index')); ?></li>
        <li><?php echo $this->Html->link('بدهی / طلب',array('controller'=>'debts','action'=>'index')); ?></li>
        <li><?php echo $this->Html->link('حساب‌ها',array('controller'=>'accounts','action'=>'index')); ?></li>
        <li><?php echo $this->Html->link('اشخاص',array('controller'=>'individuals','action'=>'index')); ?></li>
        <li><?php echo $this->Html->link('یادداشت',array('controller'=>'notes','action'=>'index')); ?></li>
        <li><?php echo $this->Html->link('گزارش‌ها',array('controller'=>'reports','action'=>'index')); ?></li>
        <li><?php echo $this->Html->link('یادآور',array('controller'=>'reminders','action'=>'index')); ?></li>
        <li><?php echo $this->Html->link('بودجه بندی',array('controller'=>'budgets','action'=>'index')); ?></li>
        <li><?php echo $this->Html->link('سرمایه',array('controller'=>'investments','action'=>'index')); ?></li>
    </ul>
</nav>
<?php else: ?>
<nav class="navbar navbar-jeeb hidden-xs">
    <ul class="nav navbar-nav">
        <li><?php echo $this->Html->link('پیشخوان',array('controller'=>'reports','action'=>'dashboard')); ?></li>
        <li><?php echo $this->Html->link('هزینه',array('controller'=>'expenses','action'=>'index')); ?></li>
        <li><?php echo $this->Html->link('درآمد',array('controller'=>'incomes','action'=>'index')); ?></li>
        <li><?php echo $this->Html->link('تراکنش‌ها',array('controller'=>'transactions','action'=>'index')); ?></li>
        <li><?php echo $this->Html->link('چک‌',array('controller'=>'checks','action'=>'index')); ?></li>
        <li><?php echo $this->Html->link('وام‌',array('controller'=>'loans','action'=>'index')); ?></li>
        <li><?php echo $this->Html->link('بدهی / طلب',array('controller'=>'debts','action'=>'index')); ?></li>
        <li><?php echo $this->Html->link('حساب‌ها',array('controller'=>'accounts','action'=>'index')); ?></li>
        <li><?php echo $this->Html->link('اشخاص',array('controller'=>'individuals','action'=>'index')); ?></li>
        <li><?php echo $this->Html->link('یادداشت',array('controller'=>'notes','action'=>'index')); ?></li>

        <li class="sm-hidden"><?php echo $this->Html->link('گزارش‌ها',array('controller'=>'reports','action'=>'index')); ?></li>
        <li class="sm-hidden"><?php echo $this->Html->link('یادآور',array('controller'=>'reminders','action'=>'index')); ?></li>
        <li class="sm-hidden"><?php echo $this->Html->link('بودجه بندی',array('controller'=>'budgets','action'=>'index')); ?></li>
        <li class="sm-hidden" style="position: relative;">
            <?php echo $this->Html->link('سرمایه',array('controller'=>'investments','action'=>'index')); ?>
        </li>

        <li class="dropdown sm-only">
            <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-caret-down"></i> <span>بیشتر</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-left">
                <li><?php echo $this->Html->link('گزارش‌ها',array('controller'=>'reports','action'=>'index')); ?></li>
                <li><?php echo $this->Html->link('یادآور',array('controller'=>'reminders','action'=>'index')); ?></li>
                <li><?php echo $this->Html->link('بودجه بندی',array('controller'=>'budgets','action'=>'index')); ?></li>
                <li style="position: relative;">
                    <?php echo $this->Html->link('سرمایه',array('controller'=>'investments','action'=>'index')); ?>
                    <span style="display: block; position: absolute; left: -14px; top: -3px; text-align: center; width: 23px; height: 13px; background: #D34444; border-radius: 5px; line-height: 12px; font-size: 11px; color: #FFF;">جدید</span>
                </li>
            </ul>
        </li>
    </ul>
</nav>
<?php endif; ?>
<?php else: ?>
<nav class="navbar navbar-jeeb hidden-xs">
    <ul class="nav navbar-nav">
        <li><?php echo $this->Html->link('صفحه نخست',array('controller'=>'pages','action'=>'home')); ?></li>
        <li><?php echo $this->Html->link('جیب چیست؟',array('controller'=>'pages','action'=>'whatisjeeb')); ?></li>
        <li><?php echo $this->Html->link('امکانات جیب',array('controller'=>'pages','action'=>'features')); ?></li>
    </ul>
</nav>
<?php endif; ?>
