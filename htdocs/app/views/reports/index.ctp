<?php echo $this->element('../reports/menu') ?>
<div class="col-xs-16 col-md-16 box2 rounded" >
    <h2>گزارش کلی</h2>
    <div>
        <div class="table-responsive"><table class="table table-striped table-hover table-bordered"  id="dataTable" cellpadding="0" cellspacing="0" style="margin-bottom: 0px;">
                <tr>
                    <td>متوسط هزینه ماهانه</td>
                    <td style="color:#C62121;"><?php echo number_format(round($averageExpense)); ?> ریال</td>
                </tr>
                <tr>
                    <td>متوسط درآمد ماهیانه</td>
                    <td style="color:green;"><?php echo number_format(round($averageIncome)); ?> ریال</td>
                </tr>
                <tr>
                    <td>پر هزینه‌ترین ماه</td>
                    <td style="color:#C62121;"><?php echo $highestExpnseMonth; ?></td>
                </tr>
                <tr>
                    <td>پر درآمد ترین ماه</td>
                    <td style="color:green;"><?php echo $highestIncomeMonth; ?></td>
                </tr>
                <tr>
                    <td>کم درآمد ترین ماه</td>
                    <td style="color:#C62121;"><?php echo $lowestIncomeMonth; ?></td>
                </tr>
                <tr>
                    <td>کم هزینه‌ترین ماه</td>
                    <td style="color:green;"><?php echo $lowestExpnseMonth; ?></td>
                </tr>
                <tr>
                    <td>پر هزینه‌ترین روز هفته</td>
                    <td style="color:#C62121;"><?php __($highestExpnseWeekDay); ?></td>
                </tr>
                <tr>
                    <td>کم هزینه ترین روز هفته</td>
                    <td style="color:green;"><?php __($lowestExpnseWeekDay); ?></td>
                </tr>
            </table></div>
    </div>
</div>

<div class="col-xs-16 col-md-4">
    <div class="rounded box">
        <h2 align="center">متوسط هزینه ماهیانه</h2>
        <br/>
        <div align="center" style="font-size: 150%;padding-bottom: 15px;color:#C62121;"><?php echo number_format(round($averageExpense)); ?> ریال</div>
    </div>
</div>


<div class="col-xs-16 col-md-4">
    <div class="rounded box">
        <h2 align="center">متوسط درآمد ماهیانه</h2>
        <br/>
        <div align="center" style="font-size: 150%;padding-bottom: 15px;color:green;"><?php echo number_format(round($averageIncome)); ?> ریال</div>
    </div>
</div>

<div class="col-xs-16 col-md-4">
    <div class="rounded box">
        <h2 align="center">پر هزینه‌ترین روز هفته</h2>
        <br/>
        <div align="center" style="font-size: 150%;padding-bottom: 15px;color:#C62121;"><?php __($highestExpnseWeekDay); ?></div>
    </div>
</div>

<div class="col-xs-16 col-md-4">
    <div class="rounded box">
        <h2 align="center">اختلاف هزینه و درآمد کل</h2>
        <br/>
        <div align="center" style="font-size: 150%;padding-bottom: 15px;<?php if(($incomeSum-$expenseSum)>=0){echo 'color:green;';}else{echo 'color:#C62121;';} ?>"><?php echo number_format($incomeSum-$expenseSum); ?> ریال</div>
    </div>
</div>

<div class="col-xs-16 col-md-4">
    <div class="rounded box">
        <h2 align="center">پر هزینه‌ترین ماه</h2>
        <br/>
        <div align="center" style="font-size: 150%;padding-bottom: 15px;color:#C62121;"><?php echo $highestExpnseMonth; ?></div>
    </div>
</div>

<div class="col-xs-16 col-md-4">
    <div class="rounded box">
        <h2 align="center">پر درآمد ترین ماه</h2>
        <br/>
        <div align="center" style="font-size: 150%;padding-bottom: 15px;color:green;"><?php echo $highestIncomeMonth; ?></div>
    </div>
</div>

<div class="col-xs-16 col-md-4">
    <div class=" rounded box">
        <h2 align="center">کم درآمد ترین ماه</h2>
        <br/>
        <div align="center" style="font-size: 150%;padding-bottom: 15px;color:#C62121;"><?php echo $lowestIncomeMonth; ?></div>
    </div>
</div>

<div class="col-xs-16 col-md-4">
    <div class="rounded box">
        <h2 align="center">کم هزینه‌ترین ماه</h2>
        <br/>
        <div align="center" style="font-size: 150%;padding-bottom: 15px;color:green;"><?php echo $lowestExpnseMonth; ?></div>
    </div>
</div>


<div class="col-xs-16 col-md-7">
    <div id="ExpensePieChart" style="direction: ltr;"></div>
</div>

<div class="col-xs-16 col-md-7 col-md-offset-2">
    <div id="IncomePieChart" style="direction: ltr;"></div>
</div>
<div class="clear"></div>
<?php echo $this->Chart->pie('IncomePieChart','تقسیم درآمد',$incomePieData,400); ?>
<?php echo $this->Chart->pie('ExpensePieChart','تقسیم هزینه',$expensePieData,400); ?>

