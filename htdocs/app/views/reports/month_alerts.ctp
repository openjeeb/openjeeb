<?php
App::import('Vendor', 'PersianDate', array('file' => 'persian.date.php'));
$persianDate=new PersianDate();
function formatDays(&$days) {
    if($days->days==0) {
        echo '<span style="color:#FFBB00">امروز<span>';
    }
    else if($days->invert) {
        echo '<span style="color:#C62121">'.$days->days.' روز قبل<span>';
    } else {
        echo '<span style="color:green">'.$days->days.' روز دیگر<span>';
    }
}
?>
<div class="table-responsive"><table class="table table-striped table-hover table-bordered"  id="dataTable" cellpadding="0" cellspacing="0">
<?php foreach ( sortByKey($data, 'time', 'asc') as $entry ): ?>
    
    <?php if( $entry['entry'] == 'check' AND $entry['type'] == 'received' ):?>
    <tr>
        <td><span style="padding:2px;border: 1px solid #84CC84;border-radius:5px;background:none repeat scroll 0 0 #84CC84;text-shadow:0 1px 1px rgba(255, 255, 255, 0.6)">&nbsp;چک دریافتی&nbsp;<?php echo $entry['description']; ?></span></td>
        <td><span style="color:green"><?php echo number_format($entry['amount']); ?>&nbsp;ریال</span></td>
        <td><span><?php echo $persianDate->pdate_format($entry['due_date'], 'l j F Y'); ?></span></td>
        <td><?php formatDays($entry['days']); ?></td>
        <td><span style="text-decoration: underline;"><?php echo $this->Html->link('دریافت شد',array('controller'=>'checks','action'=>'ajaxCheckDone',$entry['id']),array('onclick'=>'return false','class'=>'action_button RecievedCheckDone')); ?></span></td>
    </tr>
    <?php endif; ?>
    
    <?php if( $entry['entry'] == 'check' AND $entry['type'] == 'drawed' ):?>
    <tr>
        <td><span style="padding:2px;border: 1px solid #E55252;border-radius:5px;background:none repeat scroll 0 0 #E55252;text-shadow:0 1px 1px rgba(255, 255, 255, 0.6)">&nbsp;چک صادره&nbsp;<?php echo $entry['description'];?></span></td>
        <td><span style="color:#C62121"><?php echo number_format(abs($entry['amount'])); ?>&nbsp;ریال</span></td>
        <td><span><?php echo $persianDate->pdate_format($entry['due_date'], 'l j F Y'); ?></span></td>
        <td><span><?php formatDays($entry['days']); ?></span></td>
        <td><span style="text-decoration: underline;"><?php echo $this->Html->link('تسویه شد',array('controller'=>'checks','action'=>'drawedcheckdDone',$entry['id']),array('class'=>'action_button DrawedCheckDone')); ?></span></td>
    </tr>    
    <?php endif; ?>
    
    <?php if( $entry['entry'] == 'debt' AND $entry['type'] == 'debt' ):?>
    <tr>
        <td><span style="padding:2px;border: 1px solid #E55252;border-radius:5px;background:none repeat scroll 0 0 #E55252;text-shadow:0 1px 1px rgba(255, 255, 255, 0.6)">بدهی&nbsp;<?php echo $entry['name'].' '.$entry['description']; ?></span></td>
        <td><span style="color:#C62121;"><?php echo number_format(abs($entry['amount'])-$entry['settled']); ?>&nbsp;ریال</span></td>
        <td><span><?php echo $persianDate->pdate_format($entry['due_date'], 'l j F Y'); ?></span></td>
        <td><span><?php formatDays($entry['days']); ?></span></td>
        <td><span style="text-decoration: underline;"><?php echo $this->Html->link('پرداخت شد',array('controller'=>'debts','action'=>'ajaxDebtDone',$entry['id']),array('id'=>'DebtDo','onclick'=>'return false','class'=>'action_button')); ?></span></td>
    </tr>
    <?php endif; ?>
    
    <?php if( $entry['entry'] == 'debt' AND $entry['type'] == 'credit' ):?>
    <tr>
        <td><span style="padding:2px;border: 1px solid #84CC84;border-radius:5px;background:none repeat scroll 0 0 #84CC84;text-shadow:0 1px 1px rgba(255, 255, 255, 0.6)">طلب&nbsp;<?php echo $entry['name'].' '.$entry['description']; ?></span></td>
        <td><span style="color:green;"><?php echo number_format(abs($entry['amount'])-$entry['settled']); ?>&nbsp;ریال</span></td>
        <td><span><?php echo $persianDate->pdate_format($entry['due_date'], 'l j F Y'); ?></span></td>
        <td><span><?php formatDays($entry['days']); ?></span></td>
        <td><span style="text-decoration: underline;"><?php echo $this->Html->link('دریافت شد',array('controller'=>'debts','action'=>'ajaxDebtDone',$entry['id']),array('id'=>'CreditDo','onclick'=>'return false','class'=>'action_button')); ?></span></td>
    </tr>
    <?php endif; ?>
    
    <?php if( $entry['entry'] == 'installment'):?>
    <tr>
        <td><span style="padding:2px;border: 1px solid #E55252;border-radius:5px;background:none repeat scroll 0 0 #E55252;text-shadow:0 1px 1px rgba(255, 255, 255, 0.6)">&nbsp;قسط&nbsp;<?php echo $entry['loan_name']; ?>&nbsp;</span></td>
        <td><span style="color:#C62121;"><?php echo number_format($entry['amount']); ?>&nbsp;ریال</span></td>
        <td><span><?php echo $persianDate->pdate_format($entry['due_date'], 'l j F Y'); ?></span></td>
        <td><span><?php formatDays($entry['days']); ?></span></td>
        <td><span style="text-decoration: underline;"><?php echo $this->Html->link('پرداخت شد',array('controller'=>'installments','action'=>'ajaxInstallmentDone',$entry['id']),array('onclick'=>'return false','class'=>'action_button InstallmentDone')); ?></span></td>
    </tr>
    <?php endif; ?>
    
<?php endforeach; ?>
</table></div>

<script>
    
    flash = "<?php echo addslashes($this->Session->flash()); ?>"
    if(flash) {
        $('#notificaionbox').css('display','block');
        $('#notificaionbox').html(flash);
    } else {
        $('#notificaionbox').css('display','none');
    }
</script>