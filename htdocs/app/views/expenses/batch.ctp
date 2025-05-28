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
                    <label for="TransactionDate">تاریخ</label>
                    <?php echo $this->Form->text('Transaction.date',array('class'=>'datepicker','value'=>empty($formdate)? $persianDate->pdate('Y/m/d') : $formdate, 'style'=>'')); ?>
                    <?php echo $this->Html->image('info.png', array('id'=>'TransactionDateTip', 'alt'=>'راهنما', 'border' => '0')); ?>
                </div>

                <div>
                    <div class="batchRow row">
                        <input type="hidden" name="[row0][data]" id="row0" value='1' />
                        <div class="form-inline">
                            <div class="form-group">
                                <label>(ریال) مبلغ:</label>
                                <input name="[row0][Transaction][amount]" type="text" maxlength="15" style="direction:ltr;" class="TransactionAmount">
                            </div>
                            <div class="form-group">
                                <label>برداشت از:</label>
                                <select name="[row0][Transaction][account_id]" id="TransactionAccountId">
                                    <?php foreach($accounts as $k=>$v): ?>
                                        <option value="<?= $k ?>"><?= $v ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>شخص:</label>
                                <select name="[row0][Expense][individual_id]" id="ExpenseIndividualId">
                                    <option value=""></option>
                                    <?php foreach($individuals as $k=>$v): ?>
                                        <option value="<?=$k?>"><?=$v?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>توضیح:</label>
                                <input name="[row0][Expense][description]" type="text" id="ExpenseDescription">
                            </div>
                        </div>
                        <div class="form-inline">
                            <div class="form-group">
                                <label>نوع هزینه: </label>
                                <select name="[row0][Expense][expense_category_id]" style="" class="ExpenseCategory">
                                    <option value=""></option>
                                    <?php foreach ($expenseCategories as $k=>$v) : ?>
                                        <option value="<?=$k?>"><?=$v?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label> </label>
                                <select name="[row0][Expense][expense_sub_category_id]" class="ExpenseSubCategory" style=""></select>
                            </div>
                            <div class="form-group">
                                <label for="ExpenseExpenseCategoryId">برچسب: </label>
                                <input name="[row0][TransactionTag][tag_id]" type="text" value="" autocomplete="off" style="" class="TransactionTagId" selectedindex="0" />
                            </div>
                            <div class="form-group" style="float: left;">
                                <img src="/img/add.png" class="duplicateButton" id="TransactionDateTip" alt="راهنما" border="0" onclick="duplicateExpenseRow();" />
                                <img src="/img/remove.png" class="removeButton" id="TransactionDateTip" alt="راهنما" border="0" onclick="removeExpenseRow(this);" />
                            </div>
                        </div>
                        <div class="clear"></div>
                        <div class="groupListHolder"></div>
                        <div class="clear">&nbsp;</div>
                        <div class="error-message"></div>
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
    //tips
    jeeb.tip($('#TransactionDateTip'),'105','تاریخ هزینه را وارد کنید.');
    
    
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
    
    duplicateExpenseRow = function(key,data)
    {
        var objs = $('.batchRow');
        var obj = $(objs[0]).clone().insertAfter(objs[objs.length-1]);
        
        if(!key) {
            var newId = 0;
            do {
                newId = 'row'+Math.floor((Math.random()*1000)+1);
            } while($('#'+newId).length);
        } else {
            newId = key;
        }
        
        obj.find('INPUT,SELECT').each(function(k,o){
            o = $(o);
            o.attr('name', 'data[data]'+o.attr('name').replace(/row\d+/i,newId));
            o.attr('id', o.attr('name').replace(/[\[\]]/g,'_'));
        });
        
        jeeb.FormatPrice($('#'+obj.find('.TransactionAmount').attr('id')));
        
        var groupListHolder = obj.find('.groupListHolder');
        groupListHolder.attr('id', 'groupListHolder_'+newId);
        
        var fl = new FloatingList({
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
            fillSubCategory((typeof(expense_categories[ExpenseCategory.val()])!='undefined')? expense_categories[ExpenseCategory.val()].subs : {}, ExpenseSubCategory);
        });
        
        fillSubCategory((typeof(expense_categories[ExpenseCategory.val()])!='undefined')? expense_categories[ExpenseCategory.val()].subs : {}, ExpenseSubCategory);
        
        if(data) {
            $.each(data, function(k,v) {
                if(k=='tag_id') {
                    return;
                }
                var fld = obj.find( "*[name*='["+k+"]']" );
                fld.val(v);
                fld.change();
            });
            
            if(typeof(data.tag_id)!='undefined') {
                $.each(data.tag_id, function(k,v){
                    if( fl.data[v] ) {
                        fl.addItem(v);
                    } else {
                        fl.insertItemBox(v,v.substring(1));
                    }
                    
                });
            }
            
            obj.find('.error-message').html(data.error_message).css('display','block');            
        }
        
    }
    
    removeExpenseRow = function(obj)
    {
        $(obj).parentsUntil('.batchRow').parent().remove();
        if( $('.batchRow').length<2 ) {
            duplicateExpenseRow();
        }
    }
    
    <?php if(!empty($this->data['data'])): ?>
        errors = <?php echo $this->Javascript->object($this->data['data']); ?>;
        $.each(errors, function(k,v){
            duplicateExpenseRow(k,v);
        });
    <?php else: ?>
        duplicateExpenseRow();
    <?php endif; ?>
    
});
//]]>
</script>