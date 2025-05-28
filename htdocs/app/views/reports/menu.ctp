<br><br>
<div class="col-xs-16 col-md-16">
    <div class="box rounded">
        <h2 style="text-align: center;font-size: 20px;">لیست گزارش‌ها</h2>
        <div class="row block" id="shortlinks" style="padding:0px 0px 0; font-size:12px;text-align: center;">

            <div align="center" class="menu_report col-xs-16 col-md-3">
                <?php echo $this->Html->image('reports/poll-solid50blue.png', array('style' => 'margin-top:5px;','url' => array( 'action' => 'index'))); ?>
                <?php echo $this->Html->link('گزارش کلی', array('action' => 'index')); ?>
            </div>
            <div align="center" class="menu_report col-xs-16 col-md-3">
                <?php echo $this->Html->image('reports/poll-solid50blue.png', array('style' => 'margin-top:5px;','url' => array( 'action' => 'expenses'))); ?>
                <?php echo $this->Html->link('گزارش هزینه', array('action' => 'expenses')); ?>
            </div>
            <div align="center" class="menu_report col-xs-16 col-md-3">
                <?php echo $this->Html->image('reports/poll-solid50blue.png', array('style' => 'margin-top:5px;','url' => array( 'action' => 'expenses_new'))); ?>
                <?php echo $this->Html->link('گزارش هزینه (جدید)', array('action' => 'expenses_new')); ?>
            </div>
            <div align="center" class="menu_report col-xs-16 col-md-3">
                <?php echo $this->Html->image('reports/poll-solid50blue.png', array('style' => 'margin-top:5px;','url' => array( 'action' => 'incomes'))); ?>
                <?php echo $this->Html->link('گزارش درآمد', array('action' => 'incomes')); ?>
            </div>
            <div align="center" class="menu_report col-xs-16 col-md-3">
                <?php echo $this->Html->image('reports/poll-solid50blue.png', array('style' => 'margin-top:5px;','url' => array( 'action' => 'incomes_new'))); ?>
                <?php echo $this->Html->link('گزارش درآمد (جدید)', array('action' => 'incomes_new')); ?>
            </div>
            <div align="center" class="menu_report col-xs-16 col-md-3">
                <?php echo $this->Html->image('reports/poll-solid50blue.png', array('style' => 'margin-top:5px;','url' => array( 'action' => 'monthly'))); ?>
                <?php echo $this->Html->link('گزارش تفصیلی ماهانه', array('action' => 'monthly')); ?>
            </div>
            <div align="center" class="menu_report col-xs-16 col-md-3">
                <?php echo $this->Html->image('reports/poll-solid50blue.png', array('style' => 'margin-top:5px;','url' => array( 'action' => 'expense_comparison'))); ?>
                <?php echo $this->Html->link('گزارش مقایسه هزینه ماهانه', array('action' => 'expense_comparison')); ?>
            </div>
            <div align="center" class="menu_report col-xs-16 col-md-3">
                <?php echo $this->Html->image('reports/poll-solid50blue.png', array('style' => 'margin-top:5px;','url' => array( 'action' => 'income_comparison'))); ?>
                <?php echo $this->Html->link('گزارش مقایسه درآمد ماهانه', array('action' => 'income_comparison')); ?>
            </div>
            <div align="center" class="menu_report col-xs-16 col-md-3">
                <?php echo $this->Html->image('reports/poll-solid50blue.png', array('style' => 'margin-top:5px;','url' => array( 'action' => 'tags'))); ?>
                <?php echo $this->Html->link('گزارش برچسب ها', array('action' => 'tags')); ?>
            </div>
            <div align="center" class="menu_report col-xs-16 col-md-3">
                <?php echo $this->Html->image('reports/poll-solid50blue.png', array('style' => 'margin-top:5px;','url' => array( 'action' => 'accounts'))); ?>
                <?php echo $this->Html->link('گزارش تفصیلی حساب‌ها', array('action' => 'accounts')); ?>
            </div>
            <div align="center" class="menu_report col-xs-16 col-md-3">
                <?php echo $this->Html->image('reports/poll-solid50blue.png', array('style' => 'margin-top:5px;','url' => array( 'action' => 'individuals'))); ?>
                <?php echo $this->Html->link('گزارش اشخاص', array('action' => 'individuals')); ?>
            </div>
        </div>
    </div>
</div>