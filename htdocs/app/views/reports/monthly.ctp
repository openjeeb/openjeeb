<div class="row">
    <?php echo $this->element('../reports/menu') ?>
    <div class="col-xs-16 col-md-16 ">
        <div class="box rounded">
            <h2>گزارش تفصیلی ماهانه
                &nbsp;
                <?php
                echo $this->Form->select('year', array('0' => 'همه سالها') + $years, isset($this->passedArgs['year']) ? $this->passedArgs['year'] : 0, array(
                    'empty' => false,
                    'style' => 'font-size:13px; color:#333',
                    'id' => 'selectedYear'
                ));
                ?>
                <?php echo $this->Html->link(
                        $this->Html->image('excel.png', array('alt' => 'خروجی اکسل', 'border' => '0')),
                        array('export'),
                        array('escape' => false, 'id' => 'excelExport')); ?>
            </h2>
            <div>
                <div class="table-responsive"><table class="table table-striped table-hover table-bordered"  id="dataTable" cellpadding="0" cellspacing="0" style="margin-bottom: 0px;">
                        <thead class="table-primary" >
                        <tr>
                            <th style="text-align:center">ماه</th>
                            <th style="text-align:center">هزینه</th>
                            <th style="text-align:center">درآمد</th>
                            <th style="text-align:center">برآیند</th>
                            <th style="text-align:center">قسط</th>
                            <th style="text-align:center">طلب</th>
                            <th style="text-align:center">بدهی</th>
                            <th style="text-align:center">دریافتی</th>
                            <th style="text-align:center">چک صادره</th>
                        </tr>
                        </thead>
                        <?php foreach ($data as $entry): ?>
                            <tbody>
                            <tr>
                                <td><div align="right"><?php __('month_' . $entry['month']); ?>&nbsp;<?php echo $entry['year']; ?></div></td>
                                <td><div align="center" style="color:#C62121;"><?php echo number_format($entry['expense']); ?></div></td>
                                <td><div align="center" style="color:green;"><?php echo number_format($entry['income']); ?></div></td>
                                <td><div align="center" style="direction:ltr;<?php echo ($entry['outcome']>=0) ? "color:green":"color:#C62121"?>;">
                                        <?php echo number_format($entry['outcome']); ?>
                                    </div></td>
                                <td><div align="center" style="color:#EF1C1C;"><?php echo number_format($entry['installment']); ?></div></td>
                                <td><div align="center" style="color:#62af56;"><?php echo number_format($entry['credit']); ?></div></td>
                                <td><div align="center" style="color:#EF1C1C;"><?php echo number_format($entry['debt']); ?></div></td>
                                <td><div align="center" style="color:#62af56"><?php echo number_format($entry['received_check']); ?></div></td>
                                <td><div align="center" style="color:#ED6736"><?php echo number_format($entry['drawed_check']); ?></div></td>
                            </tr>
                            </tbody>
                        <?php endforeach; ?>
                    </table></div>
            </div>
        </div>
    </div>
</div>
<div class="clear"></div>

<div class="col-xs-16 col-md-16">
    <div id="LineChart" style="direction: ltr;"></div>
</div>
<div class="clear"></div>
<br/>

<div class="col-xs-16 col-md-16">
    <div id="IncomeExpenseColumnChart" style="direction: ltr;"></div>
</div>
<div class="clear"></div>
<br/>

<div class="col-xs-16 col-md-16">
    <div id="CreditsDebtsColumnChart" style="direction: ltr;"></div>
</div>
<div class="clear"></div>
<br/>

<div class="col-xs-16 col-md-16">
    <div id="ReceivedDrawedChecksColumnChart" style="direction: ltr;"></div>
</div>
<div class="clear"></div>
<br/>

<?php echo $this->Chart->line('LineChart', 'برآیند هزینه و درآمد', 'درآمد', $incomesColumnData, 'هزینه', $expensesColumnData, 900) ?>
<?php echo $this->Chart->doubleColumn('IncomeExpenseColumnChart', 'مقایسه درآمد و هزینه ماهانه', 'درآمد', $incomesColumnData, 'هزینه', $expensesColumnData, false, 900); ?>
<?php echo $this->Chart->doubleColumn('CreditsDebtsColumnChart', 'مقایسه بدهی و طلب ماهانه', 'طلب', $creditsColumnData, 'بدهی', $debtsColumnData, false, 900); ?>
<?php echo $this->Chart->doubleColumn('ReceivedDrawedChecksColumnChart', 'مقایسه چکهای دریافتی و صادره ماهانه', 'چکهای دریافتی', $receivedChecksColumnData, 'چکهای صادره', $drawedChecksColumnData, false, 900); ?>
<script type="text/javascript">
//<![CDATA[
    $(function() {
        //tips
        $('#selectedYear').change(function(el) {
            var year = $(el.target).val();
            var url = '<?php echo $this->Html->url(array()) ?>';
            window.location = url + '/' + 'year:' + year;
        });
    });
//]]>
</script>