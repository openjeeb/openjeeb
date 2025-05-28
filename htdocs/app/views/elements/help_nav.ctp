<div id="helpSidebarNav">
    <ul>
        <li><a href="#" title="پیشخوان">پیشخوان</a>
            <ul class="secondNav">
                <li><?php echo $this->Html->link('یادآوری',array('controller'=>'pages','action'=>'help','alarm')); ?></li>
                <li><?php echo $this->Html->link('آمار ماهانه',array('controller'=>'pages','action'=>'help','dashboard_statistic')); ?></li>
                <li><?php echo $this->Html->link('نمودار دایره‌ای هزینه',array('controller'=>'pages','action'=>'help','dashboard_expenses')); ?></li>
                <li><?php echo $this->Html->link('نمودار دایره‌ای درآمد',array('controller'=>'pages','action'=>'help','dashboard_incomes')); ?></li>
            </ul>
        </li>
        <li><a href="#" title="هزینه">هزینه</a>
            <ul class="secondNav">
                <li><?php echo $this->Html->link('ثبت هزینه جدید',array('controller'=>'pages','action'=>'help','new-expenses')); ?></li>
                <li><?php echo $this->Html->link('تعریف نوع هزینه',array('controller'=>'pages','action'=>'help','new_expenses_type')); ?></li>
                <li><?php echo $this->Html->link('نمودار دایره‌ای',array('controller'=>'pages','action'=>'help','statistic_expenses')); ?></li>
                <li><?php echo $this->Html->link('جستجوی هزینه',array('controller'=>'pages','action'=>'help','search_expenses')); ?></li>
                <li><?php echo $this->Html->link('لیست هزینه‌ها',array('controller'=>'pages','action'=>'help','list_expenses')); ?></li>
                <li><?php echo $this->Html->link('ویرایش هزینه',array('controller'=>'pages','action'=>'help','edit_expenses')); ?></li>
                <li><?php echo $this->Html->link('حذف هزینه',array('controller'=>'pages','action'=>'help','delete-expenses')); ?></li>
                <li><?php echo $this->Html->link('خروجی اکسل هزینه‌ها',array('controller'=>'pages','action'=>'help','excel_expenses')); ?></li>
            </ul>
        </li>
        <li><a href="#" title="درآمد">درآمد</a>
            <ul class="secondNav">
                <li><?php echo $this->Html->link('ثبت درآمد جدید',array('controller'=>'pages','action'=>'help','new_income')); ?></li>
                <li><?php echo $this->Html->link('تعریف نوع درآمد',array('controller'=>'pages','action'=>'help','new_incomes_type')); ?></li>
                <li><?php echo $this->Html->link('نمودار دایره‌ای',array('controller'=>'pages','action'=>'help','statistic_incomes')); ?></li>
                <li><?php echo $this->Html->link('جستجوی درآمد',array('controller'=>'pages','action'=>'help','search_incomes')); ?></li>
                <li><?php echo $this->Html->link('لیست درآمدها',array('controller'=>'pages','action'=>'help','list_incomes')); ?></li>
                <li><?php echo $this->Html->link('ویرایش درآمد',array('controller'=>'pages','action'=>'help','edit_incomes')); ?></li>
                <li><?php echo $this->Html->link('حذف درآمد',array('controller'=>'pages','action'=>'help','delete_incomes')); ?></li>
                <li><?php echo $this->Html->link('خروجی اکسل درآمدها',array('controller'=>'pages','action'=>'help','excel_income')); ?></li>
            </ul>
        </li>
        <li><a href="#" title="حساب">حساب</a>
            <ul class="secondNav">
                <li><?php echo $this->Html->link('ایجاد حساب',array('controller'=>'pages','action'=>'help','new_acount')); ?></li>
                <li><?php echo $this->Html->link('لیست حساب‌ها',array('controller'=>'pages','action'=>'help','list_acount')); ?></li>
                <li><?php echo $this->Html->link('ویرایش حساب',array('controller'=>'pages','action'=>'help','edit_acount')); ?></li>
                <li><?php echo $this->Html->link('حذف حساب',array('controller'=>'pages','action'=>'help','delete_acount')); ?></li>
                <li><?php echo $this->Html->link('بالانس حساب',array('controller'=>'pages','action'=>'help','balance_acount')); ?></li>
                <li><?php echo $this->Html->link('تراکنش‌ها',array('controller'=>'pages','action'=>'help','transactions')); ?></li>
                <li><?php echo $this->Html->link('انتقال وجه',array('controller'=>'pages','action'=>'help','new_transfer')); ?></li>
            </ul>
        </li>
        <li><a href="#" title="چک">چک</a>
            <ul class="secondNav">
                <li><?php echo $this->Html->link('ثبت چک جدید',array('controller'=>'pages','action'=>'help','new_check')); ?></li>
                <li><?php echo $this->Html->link('نمودار ستونی',array('controller'=>'pages','action'=>'help','statistic_checks')); ?></li>
                <li><?php echo $this->Html->link('جستجوی چک‌ها',array('controller'=>'pages','action'=>'help','search_checks')); ?></li>
                <li><?php echo $this->Html->link('لیست چک‌ها',array('controller'=>'pages','action'=>'help','list_checks')); ?></li>
                <li><?php echo $this->Html->link('ویرایش چک',array('controller'=>'pages','action'=>'help','edit_checks')); ?></li>
                <li><?php echo $this->Html->link('تسویه چک',array('controller'=>'pages','action'=>'help','settlement_checks')); ?></li>
                <li><?php echo $this->Html->link('حذف چک',array('controller'=>'pages','action'=>'help','delete_checks')); ?></li>
                <li><?php echo $this->Html->link('خروجی اکسل چک‌ها',array('controller'=>'pages','action'=>'help','excel_checks')); ?></li>
            </ul>
        </li>
        <li><a href="#" title="وام">وام</a>
            <ul class="secondNav">
                <li><?php echo $this->Html->link('ثبت وام جدید',array('controller'=>'pages','action'=>'help','new_loan')); ?></li>
                <li><?php echo $this->Html->link('نمودار دایره‌ای',array('controller'=>'pages','action'=>'help','statistic_loans')); ?></li>
                <li><?php echo $this->Html->link('لیست وام‌ها',array('controller'=>'pages','action'=>'help','list_loans')); ?></li>
                <li><?php echo $this->Html->link('ویرایش وام',array('controller'=>'pages','action'=>'help','edit_loans')); ?></li>
                <li><?php echo $this->Html->link('حذف وام',array('controller'=>'pages','action'=>'help','delete_loans')); ?></li>
                <li><?php echo $this->Html->link('لیست اقساط',array('controller'=>'pages','action'=>'help','installments')); ?></li>
                <li><?php echo $this->Html->link('خروجی اکسل وام',array('controller'=>'pages','action'=>'help','excel_loans')); ?></li>
            </ul>
        </li>
        <li><a href="#" title="بدهی / طلب">بدهی / طلب</a>
            <ul class="secondNav">
                <li><?php echo $this->Html->link('ثبت بدهی / طلب جدید',array('controller'=>'pages','action'=>'help','new_debts')); ?></li>
                <li><?php echo $this->Html->link('نمودار ستونی',array('controller'=>'pages','action'=>'help','statistic_debts')); ?></li>
                <li><?php echo $this->Html->link('جسنجوی بدهی / طلب',array('controller'=>'pages','action'=>'help','search_debts')); ?></li>
                <li><?php echo $this->Html->link('لیست بدهی /طلب',array('controller'=>'pages','action'=>'help','list_debts')); ?></li>
                <li><?php echo $this->Html->link('ویرایش بدهی / طلب',array('controller'=>'pages','action'=>'help','edit_debts')); ?></li>
                <li><?php echo $this->Html->link('تسویه بدهی / طلب',array('controller'=>'pages','action'=>'help','settlement_debts')); ?></li>
                <li><?php echo $this->Html->link('حذف بدهی / طلب',array('controller'=>'pages','action'=>'help','delete_debts')); ?></li>
                <li><?php echo $this->Html->link('اکسل بدهی / طلب',array('controller'=>'pages','action'=>'help','excel_debts')); ?></li>
            </ul>
        </li>
        <li><a href="#" title="سایر">سایر</a>
            <ul class="secondNav">
                <li><?php echo $this->Html->link('راهنمای ثبت نام',array('controller'=>'pages','action'=>'help','registration')); ?></li>
                <li><?php echo $this->Html->link('راهنمای خرید اینترنتی',array('controller'=>'pages','action'=>'help','online_purchase')); ?></li>
                <li><?php echo $this->Html->link('راهنمای دریافت رمز اینترنتی',array('controller'=>'pages','action'=>'help','internet_pass')); ?></li>
            </ul>
        </li>
    </ul>
</div>

<script type="text/javascript">
    //<![CDATA[
    $(function(){
        /*** Help Sidebar Navigation Control ***/
        $('#helpSidebarNav ul li ul.secondNav').parent().addClass('hasChildNav');
        $('#helpSidebarNav ul li').click(function () {
            $('#helpSidebarNav ul li').children('ul.secondNav:visible').slideUp();
            $('#helpSidebarNav ul li').removeClass('activeMenu');
            $(this).children('ul.secondNav').addClass('openMenu').slideDown();
            $(this).addClass('activeMenu');
        });    
    });
    //]]>
</script>