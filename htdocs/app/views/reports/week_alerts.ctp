<?php foreach ($weekChecks as $check): ?>
    <?php if ($check['Check']['type'] == 'received'): ?>
        <div>
            <span>شما یک چک دریافتی به مبلغ <?php echo number_format($check['Check']['amount']); ?> ریال به تاریخ <?php echo $check['Check']['due_date']; ?> دارید.</span>
            <span><?php echo $this->Html->link('دریافت شد',array('controller'=>'checks','action'=>'ajaxCheckDone',$check['Check']['id']),array('id'=>'CheckDone','onclick'=>'return false')); ?></span>
        </div>
    <?php elseif ($check['Check']['type'] == 'drawed'): ?>
        <span>شما یک چک صادره به مبلغ <?php echo number_format($check['Check']['amount']); ?> ریال به تاریخ <?php echo $check['Check']['due_date']; ?> دارید.</span>
        <span><?php echo $this->Html->link('تسویه شد',array('controller'=>'checks','action'=>'ajaxCheckDone',$check['Check']['id']),array('id'=>'CheckDone','onclick'=>'return false')); ?></span>
    <?php endif; ?>
<?php endforeach; ?>

<?php foreach ($weekInstallments as $installment): ?>
    <div>
        <span> شما یک قسط به مبلغ <?php echo number_format($installment['Installment']['amount']); ?> ریال از  <?php echo $installment['Loan']['name']; ?> را باید در تاریخ <?php echo $installment['Installment']['due_date']; ?>  پرداخت کنید.</span>
        <span><?php echo $this->Html->link('پرداخت شد',array('controller'=>'installments','action'=>'ajaxInstallmentDone',$installment['Installment']['id']),array('id'=>'InstallmentDone','onclick'=>'return false')); ?></span>
    </div>
<?php endforeach; ?>
<br/>