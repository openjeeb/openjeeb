<?php if( Configure::read('Newrouz.inTime') && false ): ?>
    <div id="flashMessage" class="error-message text-center">
        مجموعه جیب نوروز ۹۹ را <?= (time()<=strtotime('2020-03-20 07:49:00'))? "پیشاپیش" : "" ?> به شما تبریک میگوید.
        <br />
        به مناسبت فرارسیدن سال نو، مجموعه جیب تخفیف‌های پکیج‌ها را از ۱۷ اسفند الی ۱۵ فروردین برای شما در نظر گرفته است.
        <br />
        توجه داشته باشید مدت زمان اشتراک خریداری شده به اعتبار شما اضافه میشود و اعتبار قبلی شما از بین نمیرود.<br />
        برای مشاهده تخفیف ها به صفحه <a href="https://jeeb.ir/users/extend">تمدید</a>  رجوع نمایید.
    </div>
<?php endif; ?>
<div id="notificaionbox" class="col-xs-16 col-md-16" style='margin: 0 10px 20px 0; display: none;'>
    
</div>
<div class="col-xs-16 col-md-16 rounded box2">
    <h2 align="center">یادآوری‌ها&nbsp;<?php echo $this->Html->link($this->Html->image('reload.png',array('alt'=>'reload')), '#',array('id'=>'ReloadMonthAlerts','onclick'=>'return false','escape'=>false)); ?></h2>
    <div id="monthAlerts"></div>
</div>
<div class="clear"></div>
<div class="row">
    <div class="col-xs-16 col-md-4">
        <div class="rounded box">
            <h2 align="center">جمع هزینه در ماه جاری</h2>
            <br/>
            <div align="center" style="font-size: 150%;padding-bottom: 15px;color:#C62121;"><?php echo number_format($monthExpense); ?>&nbsp; ریال</div>
        </div>
    </div>


    <div class="col-xs-16 col-md-4">
        <div class="rounded box">
            <h2 align="center">جمع درآمد در ماه جاری</h2>
            <br/>
            <div align="center" style="font-size: 150%;padding-bottom: 15px;color:green;"><?php echo number_format($monthIncome); ?>&nbsp; ریال</div>
        </div>
    </div>


    <div class="col-xs-16 col-md-4">
        <div class="rounded box">
            <h2 align="center">موجودی کل</h2>
            <br/>
            <div align="center" style="font-size: 150%;padding-bottom: 15px;color:<?php if($totalBalance>=0){echo 'green';}else{ echo '#C62121';}?>;"><?php if(!$totalBalance) {echo '- - -';} else {echo number_format($totalBalance);} ?> ریال</div>
        </div>
    </div>


    <div class="col-xs-16 col-md-4">
        <div class="rounded box">
            <h2 align="center">اختلاف هزینه و درآمد ماه جاری</h2>
            <br/>
            <div align="center" style="font-size: 150%;padding-bottom: 15px;<?php if(($monthIncome-$monthExpense)>=0){echo 'color:green;';}else{echo 'color:#C62121;';} ?>"><?php echo number_format($monthIncome-$monthExpense); ?> ریال</div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-16 col-md-4">
        <div class="box rounded">
            <h2 align="center">جمع چک‌های دریافتی ماه جاری</h2>
            <br/>
            <div align="center" style="font-size: 150%;padding-bottom: 15px;color:green;"><?php echo number_format($monthReceivedCheck); ?>&nbsp;ریال</div>
        </div>
    </div>

    <div class="col-xs-16 col-md-4">
        <div class="rounded box">
            <h2 align="center">جمع چک‌های صادره ماه جاری</h2>
            <br/>
            <div align="center" style="font-size: 150%;padding-bottom: 15px;color:#C62121;"><?php echo number_format(abs($monthDrawedCheck)); ?>&nbsp;ریال</div>
        </div>
   </div>

    <div class="col-xs-16 col-md-4">
        <div class="rounded box">
            <h2 align="center">جمع طلب‌های ماه جاری</h2>
            <br/>
            <div align="center" style="font-size: 150%;padding-bottom: 15px;color:green;"><?php echo number_format($monthCredits); ?>&nbsp;ریال</div>
        </div>
    </div>


    <div class="col-xs-16 col-md-4">
        <div class="rounded box">
            <h2 align="center">جمع بدهی‌های ماه جاری</h2>
            <br/>
            <div align="center" style="font-size: 150%;padding-bottom: 15px;color:#C62121;"><?php echo number_format(abs($monthDebts)); ?>&nbsp;ریال</div>
        </div>
    </div>

</div>




<div class="clear"></div>

<div class="col-xs-16 col-md-7">
    <div id="ExpensePieChart" style="direction: ltr;"></div>
</div>

<div class="col-xs-16 col-md-7 col-md-offset-2">
    <div id="IncomePieChart" style="direction: ltr;"></div>
</div>
<div class="clear"></div>


<!-- Modal -->
<div id="InstallmentDoneConfirm" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close pull-left" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">پرداخت قسط</h4>
            </div>
            <div class="modal-body">
                <?php echo $this->Form->create('Installment'); ?>
                <span>مبلغ این قسط را از کدام حساب پرداخت کردید؟</span>
                <br/><br/>
                <?php echo $this->Form->label('Transaction.account_id','برداشت از حساب'); ?>
                <?php echo $this->Form->select('Transaction.account_id',$accounts,null,array('empty'=>false)); ?><br/>
                <?php echo $this->Form->end();?>
            </div>
            <div class="modal-footer">
                <button type="button" id="InstallmentDoneConfirm-submit" class="btn btn-success" data-dismiss="modal">ثبت</button>
                <button type="button" id="InstallmentDoneConfirm-cancel" class="btn btn-warning" data-dismiss="modal">لغو</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div id="RecievedCheckDoneConfirm" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close pull-left" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">دریافت چک</h4>
            </div>
            <div class="modal-body">
                <?php echo $this->Form->create('Check'); ?>
                <span>مبلغ این چک دریافتی به کدام حساب واریز شد؟</span>
                <br/><br/>
                <?php echo $this->Form->label('Transaction.account_id','واریز به حساب'); ?>
                <?php echo $this->Form->select('Transaction.account_id',$accounts,null,array('empty'=>false)); ?><br/>
                <?php echo $this->Form->end();?>
            </div>
            <div class="modal-footer">
                <button type="button" id="RecievedCheckDoneConfirm-submit" class="btn btn-success" data-dismiss="modal">ثبت</button>
                <button type="button" id="RecievedCheckDoneConfirm-cancel" class="btn btn-warning" data-dismiss="modal">لغو</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal -->
<div id="DebtDoneConfirm" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close pull-left" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">تسویه بدهی</h4>
            </div>
            <div class="modal-body">
                <?php echo $this->Form->create('Debt',array('id'=>'DebtDoForm')); ?>
                <fieldset>
                    <legend>نحوه تسویه</legend>
                    <span>آیا کل بدهی تسویه شده یا بخشی از آن تسویه شده؟</span><br/>
                    <?php echo $this->Form->radio('Debt.state',array('all'=>'کل بدهی','part'=>'بخشی از بدهی'),array('legend'=>false,'separator'=>'  ','value'=>'all')); ?>
                    <br/>
                    <?php echo $this->Form->label('DebtDoneAmount','میزان تسویه شده'); ?>
                    <?php echo $this->Form->text('Debt.done_amount',array('disabled'=>'disabled', 'style'=>'direction:ltr;')); ?> ریال
                    <br/>
                </fieldset>
                <br/>

                <fieldset>
                    <legend>افزودن مبلغ به هزینه</legend>
                    <span>آیا مایلید مبلغ تسویه بدهی به هزینه‌های شما اضافه گردد؟ در اینصورت حسابی که از آن برای تسویه بدهی برداشت کرده‌اید را مشخص کنید.</span>    <br/><br/>
                    <?php echo $this->Form->radio('Debt.addExpense',array('yes'=>'بله','no'=>'خیر'),array('legend'=>false,'separator'=>'  ','value'=>'yes')); ?>
                    <br/>
                    <?php echo $this->Form->label('TransactionAccountIdDebt','برداشت از حساب'); ?>
                    <?php echo $this->Form->select('Transaction.account_id',$accounts,null,array('id'=>'TransactionAccountIdDebt','empty'=>false)); ?>
                </fieldset>
                <?php echo $this->Form->end();?>
            </div>
            <div class="modal-footer">
                <button type="button" id="DebtDoneConfirm-submit" class="btn btn-success" data-dismiss="modal">ثبت</button>
                <button type="button" id="DebtDoneConfirm-cancel" class="btn btn-warning" data-dismiss="modal">لغو</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div id="CreditDoneConfirm" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close pull-left" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">تسویه طلب</h4>
            </div>
            <div class="modal-body">
                <?php echo $this->Form->create('Debt',array('id'=>'CreditDoForm')); ?>
                <fieldset>
                    <legend>نحوه تسویه</legend>
                    <span>آیا کل طلب تسویه شده یا بخشی از آن تسویه شده؟</span><br/>
                    <?php echo $this->Form->radio('Credit.state',array('all'=>'کل طلب','part'=>'بخشی از طلب'),array('legend'=>false,'separator'=>'  ','value'=>'all')); ?>
                    <br/>
                    <?php echo $this->Form->label('CreditDoneAmount','میزان تسویه شده'); ?>
                    <?php echo $this->Form->text('Credit.done_amount',array('disabled'=>'disabled', 'style'=>'direction:ltr;')); ?> ریال
                    <br/>
                </fieldset>
                <br/>

                <fieldset>
                    <legend>افزودن مبلغ به درآمد</legend>
                    <span>آیا مایلید مبلغ تسویه طلب به درآمدهای شما اضافه گردد؟ در اینصورت حسابی که مبلغ طلب را به آن واریز کرده‌اید مشخص کنید.</span>    <br/><br/>
                    <?php echo $this->Form->radio('Debt.addIncome',array('yes'=>'بله','no'=>'خیر'),array('legend'=>false,'separator'=>'  ','value'=>'yes')); ?>
                    <br/>
                    <?php echo $this->Form->label('TransactionAccountIdCredit','واریز به حساب'); ?>
                    <?php echo $this->Form->select('Transaction.account_id',$accounts,null,array('id'=>'TransactionAccountIdCredit','empty'=>false)); ?>
                </fieldset>
                <?php echo $this->Form->end();?>
            </div>
            <div class="modal-footer">
                <button type="button" id="CreditDoneConfirm-submit" class="btn btn-success" data-dismiss="modal">ثبت</button>
                <button type="button" id="CreditDoneConfirm-cancel" class="btn btn-warning" data-dismiss="modal">لغو</button>
            </div>
        </div>
    </div>
</div>


<?php echo $this->Chart->pie('IncomePieChart','نمودار تقسیم درآمد ماه جاری',$incomePieData,400); ?>
<?php echo $this->Chart->pie('ExpensePieChart','نمودار تقسیم هزینه ماه جاری',$expensePieData,400); ?>
<script type="text/javascript">
//<![CDATA[
$(function(){
    //load alerts
    $('#monthAlerts').append(jeeb.GetAjaxPage('<?php echo $this->Html->url(array('controller'=>'reports','action'=>'monthAlerts')); ?>'));
    //mark installment as done
    $('.InstallmentDone').live('click',function () {
        that=$(this);
        $("#InstallmentDoneConfirm-submit").unbind('click');
        $("#InstallmentDoneConfirm-cancel").unbind('click');
        $("#InstallmentDoneConfirm-submit").on('click',function (e) {
            jeeb.InstallmentDone(that.attr('href'),$('#InstallmentDashboardForm #TransactionAccountId').val());
            $('#monthAlerts').html(jeeb.GetAjaxPage('<?php echo $this->Html->url(array('controller'=>'reports','action'=>'monthAlerts')); ?>'));
        });
        $("#InstallmentDoneConfirm-cancel").on('click',function (e) {
            $('#monthAlerts').html(jeeb.GetAjaxPage('<?php echo $this->Html->url(array('controller'=>'reports','action'=>'monthAlerts')); ?>'));
        });
        $("#InstallmentDoneConfirm").modal();
    });
    //mark recieved check as done
    $('.RecievedCheckDone').live('click',function () {
        that=$(this);
        $("#RecievedCheckDoneConfirm-submit").unbind('click');
        $("#RecievedCheckDoneConfirm-cancel").unbind('click');
        $("#RecievedCheckDoneConfirm-submit").on('click',function (e) {
            jeeb.CheckDone(that.attr('href'),$('#CheckDashboardForm #TransactionAccountId').val());
            $('#monthAlerts').html('');
            $('#monthAlerts').html(jeeb.GetAjaxPage('<?php echo $this->Html->url(array('controller'=>'reports','action'=>'monthAlerts')); ?>'));
        });
        $("#RecievedCheckDoneConfirm-cancel").on('click',function (e) {
            $('#monthAlerts').html('');
            $('#monthAlerts').html(jeeb.GetAjaxPage('<?php echo $this->Html->url(array('controller'=>'reports','action'=>'monthAlerts')); ?>'));
        });
        $("#RecievedCheckDoneConfirm").modal();
    });
    //prevent enter submit
    $('#DebtDoneConfirm').keypress(function(e){
        if ( e.which == 13 ) e.preventDefault();
    });
    $('#CreditDoneConfirm').keypress(function(e){
        if ( e.which == 13 ) e.preventDefault();
    });
    //mark debt as done
    $('#DebtDo').live('click',function () {        
        that=$(this);
        $('#DebtDoForm')[0].reset();
        $("#DebtDoneConfirm-submit").unbind('click');
        $("#DebtDoneConfirm-submit").on('click',function (e) {
            if($('input[name="data[Debt][addExpense]"]:checked').val()=='yes') {
                jeeb.DebtDone(that.attr('href'),true,$('#DebtDoForm #TransactionAccountIdDebt').val(),$('input[name="data[Debt][state]"]:checked').val(),$('#DebtDoForm #DebtDoneAmount').val());
            } else {
                jeeb.DebtDone(that.attr('href'),'',0,$('input[name="data[Debt][state]"]:checked').val(),$('#DebtDoForm #DebtDoneAmount').val());
            }
            $('#monthAlerts').html('');
            $('#monthAlerts').html(jeeb.GetAjaxPage('<?php echo $this->Html->url(array('controller'=>'reports','action'=>'monthAlerts')); ?>'));
        });
        $("#DebtDoneConfirm").modal();

    });
    $('#DebtStateAll').click( function() {
        $('#DebtDoneAmount').attr('disabled', 'disabled');
    });
    $('#DebtStatePart').click( function() {
        $('#DebtDoneAmount').attr('disabled', false);
    });
    $('#DebtAddExpenseYes').click( function() {
        $('#TransactionAccountIdDebt').attr('disabled', false);
    });
    $('#DebtAddExpenseNo').click( function() {
        $('#TransactionAccountIdDebt').attr('disabled', 'disabled');
    });
    //mark credit as done
    $('#CreditDo').live('click',function () {        
        that=$(this);
        $('#CreditDoForm')[0].reset();
        $("#CreditDoneConfirm-submit").unbind('click');
        $("#CreditDoneConfirm-submit").on('click',function (e) {
            if($('input[name="data[Debt][addIncome]"]:checked').val()=='yes') {
                jeeb.DebtDone(that.attr('href'),true,$('#CreditDoForm #TransactionAccountIdCredit').val(),$('input[name="data[Credit][state]"]:checked').val(),$('#CreditDoForm #CreditDoneAmount').val());
            } else {
                jeeb.DebtDone(that.attr('href'),'',0,$('input[name="data[Credit][state]"]:checked').val(),$('#CreditDoForm #CreditDoneAmount').val());
            }
            $('#monthAlerts').html('');
            $('#monthAlerts').html(jeeb.GetAjaxPage('<?php echo $this->Html->url(array('controller'=>'reports','action'=>'monthAlerts')); ?>'));
        });
        $("#CreditDoneConfirm").modal();
    });
    $('#CreditStateAll').click( function() {
        $('#CreditDoneAmount').attr('disabled', 'disabled');
    });
    $('#CreditStatePart').click( function() {
        $('#CreditDoneAmount').attr('disabled', false);
    });
    $('#DebtAddIncomeYes').click( function() {
        $('#TransactionAccountIdCredit').attr('disabled', false);
    });
    $('#DebtAddIncomeNo').click( function() {
        $('#TransactionAccountIdCredit').attr('disabled', 'disabled');
    });
    //reload month alerts
    $('#ReloadMonthAlerts').click(function(){
        $('#monthAlerts').html(jeeb.GetAjaxPage('<?php echo $this->Html->url(array('controller'=>'reports','action'=>'monthAlerts')); ?>'));
    });
    //number format
    jeeb.FormatPrice($('#DebtDoneAmount'));
    jeeb.FormatPrice($('#CreditDoneAmount'));

});
//]]>
</script>