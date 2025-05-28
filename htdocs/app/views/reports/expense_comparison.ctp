<div class="row">
    <?php echo $this->element('../reports/menu') ?>
    <div class="col-xs-16 col-md-16">
        <div class=" box rounded">
            <h2>گزارش مقایسه هزینه ماهانه</h2>
            <div id="filter" class="form">
                <?php echo $this->Form->create('Expense',array('url'=>array('controller'=>'reports','action'=>'expense_comparison'))); ?>
                <fieldset>
                    <div class="input text" style="position: relative;">
                        <label for="ExpenseExpenseCategoryId">نوع هزینه</label>
                        <div style="float: right; width:570px;">
                            <?php echo $this->Form->text('Expense.category_id',array('value'=>'','autocomplete'=>'off')); ?>&nbsp;<span id="limitShow" style="font-size:10px;">&nbsp;</span>
                            <div id="groupListHolder"></div>
                        </div>
                    </div>
                    <div class="clear">&nbsp;</div>
                    <?php
                    echo $this->Form->input('Expense.search',array('type'=>'hidden','value'=>true));
                    //echo $this->Form->input('Expense.expense_category_id',array('label'=>'نوع هزینه','type'=>'select','empty'=>true));
                    echo $this->Form->input('Transaction.start_date',array('label'=>'از تاریخ','class'=>'datepicker'));
                    echo $this->Form->input('Transaction.end_date',array('label'=>'تا تاریخ','class'=>'datepicker'));
                    ?>
                    <div class="input text"><label>&nbsp;</label><?php echo $this->element('filldate', array( 'start_date' => '#TransactionStartDate', 'end_date' => '#TransactionEndDate', 'showthisyear' => true )); ?></div>
                </fieldset>
                <?php echo $this->Form->end('گزارش گیری');?>
            </div>
        </div>
    </div>
</div>
<div class="clear"></div>

<div class="col-xs-16 col-md-16">
    <div id="ExpenseColumnChart" style="direction: ltr;"></div>
</div>
<div class="clear"></div>

<?php echo $this->Chart->nativeLineMany('ExpenseColumnChart','مقایسه هزینه ها',$columnData); ?>
<script type="text/javascript">
//<![CDATA[

$(function(){
    //bind the sub categories
    <?php /*jeeb.bindExpenseSubCategories($('#ExpenseExpenseCategoryId'),<?php echo $this->Javascript->object($expenseCategoriesData); ?>,'','ExpenseExpenseSubCategoryId');  */ ?>
    categories = <?php echo json_encode($catlist?: array()); ?>;
    catcalc = <?php echo json_encode($catcalc?: array()); ?>;
    values = <?php echo json_encode(!empty($report_catlist)? $report_catlist : array()); ?>;
    dflt = [];
    for(i in  values) {
        dflt[dflt.length] = values[i];
    }
    new FloatingList({
        input: '#ExpenseCategoryId', 
        listholder: '#groupListHolder', 
        data: categories,
        preload: dflt,
        catcalc: catcalc,
        maxitems: 15,
        limitshowelement: '#limitShow',
        onmake: function()
        {
            this.limitshowelement = $(this.limitshowelement);
        },
        calcnum: function()
        {
            var n = 0;
            $.each(this.addedItems || {}, jQuery.proxy(function(k,v){
                n += Number(this.catcalc[k]) || 1;
            }, this));
            return n;
        },
        selectCallback: function(itemid) {
            if( (this.calcnum() + this.catcalc[itemid]) > this.maxitems ) {
                if(this.addedItems[0]) {
                    return true;
                }
                this.limitshowelement.html( this.limitshowelement.html() + ' (حداکثر پانزده مورد)' );
                this.limitshowelement.css('color','red');
                return false;
            }else{
                return true;
            }
        },
        listChangeItem: function(itemid)
        {
            this.limitshowelement.html( 'نمایش '+ this.calcnum() +' نمودار' );
            this.limitshowelement.css('color','black');
        }
    
    });
});
//]]>
</script>