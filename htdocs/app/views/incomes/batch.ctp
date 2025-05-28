<?php
App::import('Vendor', 'PersianDate', array('file' => 'persian.date.php'));
$persianDate=new PersianDate();
?>
<div class="col-xs-16 col-md-16 box rounded">
    <h2>ثبت هزینه جدید</h2>
    <div class="expenses form">
        <?php echo $this->Form->create('Expense'); ?>
            <fieldset>

                <div class="input text">
                    <label id="TransactionDate">تاریخ</label>
                    <?php echo $this->Form->text('Transaction.date',array('class'=>'datepicker','value'=>$persianDate->pdate('Y/m/d'), 'style'=>'width:250px;')); ?>
                    <?php echo $this->Html->image('info.png', array('id'=>'TransactionDateTip', 'alt'=>'راهنما', 'border' => '0')); ?>
                </div>

                <div>
                    <div class="batchRow">
                        <input type="hidden" name="data[row0][data]" id="row0" value='1' />
                        <div style="width:885px;">
                            <div class="item">
                                <label>(ریال) مبلغ:</label>
                                <input name="data[row0][Transaction][amount]" type="text" maxlength="15" style="direction:ltr; width:100px;" class="TransactionAmount">
                            </div>
                            <div class="item">
                                <label>برداشت از:</label>
                                <select name="data[row0][Transaction][account_id]" id="TransactionAccountId">
                                    <?php foreach($accounts as $k=>$v): ?>
                                        <option value="<?= $k ?>"><?= $v ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="item">
                                <label>شخص:</label>
                                <select name="data[row0][Expense][individual_id]" id="ExpenseIndividualId">
                                    <option value=""></option>
                                    <?php foreach($individuals as $k=>$v): ?>
                                        <option value="<?=$k?>"><?=$v?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="item">
                                <label>توضیح:</label>
                                <input name="data[row0][Expense][description]" type="text" id="ExpenseDescription">
                            </div>
                            <div class="item">
                                <img src="/img/add.png" class="duplicateButton" id="TransactionDateTip" alt="راهنما" border="0" onclick="duplicateExpenseRow(this);" />
                                <img src="/img/remove.png" class="removeButton" id="TransactionDateTip" alt="راهنما" border="0" onclick="removeExpenseRow(this);" />
                            </div>
                        </div>
                        <div class="clear">&nbsp;</div>
                        <div style="width:700px;">
                            <div style="float:right; margin-left: 10px;">
                                <label>نوع هزینه</label>
                                <select name="data[row0][Expense][expense_category_id]" style="width:130px;" class="ExpenseCategory">
                                    <?php foreach ($expenseCategories as $k=>$v) : ?>
                                        <option value="<?=$k?>"><?=$v?></option>
                                    <?php endforeach; ?>
                                </select>
                                &nbsp;
                                <select name="data[row0][Expense][expense_sub_category_id]" class="ExpenseSubCategory" style="width:130px;"></select>
                            </div>
                            <div style="float:right; margin-left: 10px;">
                                <label for="ExpenseExpenseCategoryId" style="float: right;">برچسب: </label>
                                <div style="float:right;">
                                    <input name="data[row0][Transaction][tag_id]" type="text" value="" autocomplete="off" style="width:210px" class="TransactionTagId" selectedindex="0" />
                                </div>
                            </div>
                            <div class="clear"></div>
                            <div class="groupListHolder"></div>
                        </div>
                        <div class="clear">&nbsp;</div>
                    </div>
                </div>
            </fieldset>
        <?php echo $this->Form->end('ثبت');?>
    </div>
</div>

<script type="text/javascript">
//<![CDATA[
$(function(){
    
    expense_categories = <?php echo $this->Javascript->object($expenseCategoriesData); ?>;    
    jeeb.FormatPrice($('#TransactionAmount'));
    jeeb.FormatPrice($('#TransactionAmountSearch'));
    //tips
    jeeb.tip($('#TransactionDateTip'),'105','تاریخ هزینه را وارد کنید.');    
    $('#TransactionAccountId').change(function(ev){ jeeb.accountBalance(balances, ev.target, '#AccountBalance > span'); });
    
    categories = <?php echo json_encode(empty($tags)? array() : $tags); ?>;
    
    fillSubCategory = function(data, target)
    {
        target.html('');
        jQuery('<option>', {}).appendTo(target);
        $.each(data, function(k,v) {
            jQuery('<option>', {
                html: v.name,
                value: v.id,
            }).appendTo(target);
        });
    }
    
    duplicateExpenseRow = function(obj)
    {
        var objs = $('.batchRow');
        var obj = $(objs[0]).clone().insertAfter(objs[objs.length-1]);
        
        var newId = 0;
        do {
            newId = Math.floor((Math.random()*1000)+1);
        } while($('#row'+newId).length);
        
        obj.find('INPUT,SELECT').each(function(k,o){
            o = $(o);
            o.attr('name', o.attr('name').replace(/row\d+/i,'row'+newId));
            o.attr('id', o.attr('name').replace(/[\[\]]/g,'_'));
        });
        
        var groupListHolder = obj.find('.groupListHolder');
        groupListHolder.attr('id', 'groupListHolder_'+newId);
        
        
        new FloatingList({
            input: '#'+obj.find('.TransactionTagId').attr('id'),
            listholder: '#groupListHolder_'+newId, 
            data: categories,
            allowNew: true,
            empty: false
        });
        
        var ExpenseCategory = obj.find('.ExpenseCategory');
        var ExpenseSubCategory = obj.find('.ExpenseSubCategory');
        
        ExpenseCategory.change(function(e){
            var target = $(e.currentTarget);
            fillSubCategory(expense_categories[target.val()].subs, ExpenseSubCategory);
        });
        
        fillSubCategory(expense_categories[ExpenseCategory.val()].subs, ExpenseSubCategory);        
    }
    
    removeExpenseRow = function(obj)
    {
        $(obj).parentsUntil('.batchRow').parent().remove();
        if( $('.batchRow').length<2 ) {
            duplicateExpenseRow();
        }
    }
    
    duplicateExpenseRow();
    
});
//]]>
</script>