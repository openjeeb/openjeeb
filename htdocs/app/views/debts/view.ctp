<div class="col-xs-16 col-md-4">
    <div class="box">
                <h2>
            <a href="#" id="toggle-shortlinks">دسترسی سریع</a>
        </h2>
        <div class="block" id="shortlinks">
            <ul class="menu">
                <li><?php echo $this->Html->link('بدهی / طلب‌ها', array('action' => 'index')); ?></li>
                <li><?php echo $this->Html->link('ویرایش', array('action' => 'edit',$debt['Debt']['id'])); ?></li>
                <li><?php echo $this->Html->link('پاک کردن', array('action' => 'delete',$debt['Debt']['id']), array('escape'=>false,'alt'=>'حذف'), sprintf('آیا مطمئنید که میخواهید این اطلاعات را پاک کنید؟ در صورت پاک کردن این وام تمامی تراکنشهای مربوط به اقساط آن هم حذف خواهند شد.', $debt['Debt']['id'])); ?></li>
            </ul>
            <br/>
        </div>
    </div>
</div>

<div class="col-xs-16 col-md-12">
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-primary">
            <tr>
                <th colspan="2" class="centered">
                    <h2>جزئیات بدهی / طلب</h2>
                </th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th>نوع</th>
                <td> <?php __($debt['Debt']['type']); ?>&nbsp;</td>
            </tr>
            <tr>
                <th>عنوان</th>
                <td><?php echo $debt['Debt']['name']; ?></td>
            </tr>
            <tr>
                <th>مبلغ</th>
                <td><?php echo number_format(abs($debt['Debt']['amount'])); ?></td>
            </tr>
            <tr>
                <th>تسویه شده</th>
                <td><?php echo number_format($debt['Debt']['settled']); ?>&nbsp;</td>
            </tr>
            <tr>
                <th>باقیمانده</th>
                <td> <?php echo number_format((abs($debt['Debt']['amount']) - $debt['Debt']['settled'])); ?></td>
            </tr>
            <tr>
                <th>تاریخ موعد</th>
                <td><?php echo $debt['Debt']['due_date']; ?></td>
            </tr>
            <tr>
                <th>آگاه‌سازی</th>
                <td> <?php __($debt['Debt']['notify']); ?>&nbsp;</td>
            </tr>
            <tr>
                <th>تاریخ ایجاد</th>
                <td> <?php echo $debt['Debt']['created']; ?></td>
            </tr>
            <tr>
                <th>تاریخ ویرایش</th>
                <td><?php echo $debt['Debt']['modified']; ?></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>


<div class="col-xs-16 col-md-16">
    <h2 id="page-heading">لیست تسویه‌ها</h2>
    
    <div class="table-responsive"><table class="table table-striped table-hover table-bordered"  id="dataTable" cellpadding="0" cellspacing="0">
    <?php $tableHeaders = $html->tableHeaders(array('ردیف','مبلغ','تاریخ','تاریخ ویرایش','عملیات'));
echo '<thead class="table-primary" >'.$tableHeaders.'</thead>'; ?>

<?php $i=1;foreach ($debt['DebtSettlement'] as $debtSettlement): ?>
	<tr>
		<td><?php echo $i; ?>&nbsp;</td>
		<td><?php echo number_format($debtSettlement['amount']); ?>&nbsp;</td>
		<td><?php echo $debtSettlement['created']; ?>&nbsp;</td>
		<td><?php echo $debtSettlement['modified']; ?>&nbsp;</td>
		<td style="width: 100px">
                    <?php echo $this->Html->link('<i class="fa fa-times"></i>', array('controller'=>'debt_settlements', 'action' => 'delete', $debtSettlement['id']),array('escape'=>false,'class'=>'delete'), sprintf('آیا مطمئنید که میخواهید این اطلاعات را پاک کنید؟ در صورت پاک کردن وضعیت تسویه نیز ممکن است تغییر کند.', $debtSettlement['id'])); ?>&nbsp;
		</td>
	</tr>
<?php $i++;endforeach; ?>
<?php echo '<tfoot class=\'dark\'>'.$tableHeaders.'</tfoot>'; ?>    </table></div>
</div>
<div class="clear"></div>
<script type="text/javascript">
//<![CDATA[
$(function(){
    //tips
    jeeb.tip($('.edit'),'50','ویرایش');
    jeeb.tip($('.delete'),'70','پاک کردن');
});
//]]>
</script>