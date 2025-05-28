var jeeb = {
    Data:[],
    DatePicker : function(){
        $(".datepicker").persianCalendar();
    },
    ThemeChart: function()
    {
        /**
        * Grid theme for Highcharts JS
        * @author Torstein Hønsi
        */
        var titleFontSize = 25;
        var pageWidth = $(document).width();
        if(pageWidth < 768)
            titleFontSize=12;

       var theme = {
          colors: ['#058DC7', '#ED561B', '#50B432', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4'],
          chart: {
             backgroundColor: '#FFF',
             borderWidth: 0,
             plotBackgroundColor: null,//'rgba(255, 255, 255, .9)',
             plotShadow: true,
             plotBorderWidth: 1,
              style: {
                  color: '#000',
                  fontFamily: 'Iransans'
              }
          },
          title: {
             style: {
                color: '#000',
                font: titleFontSize+'px Iransans'
             }
          },
          subtitle: {
             style: {
                color: '#666666',
                font: 'bold 12px Iransans, Verdana, sans-serif'
             }
          },
          xAxis: {
             gridLineWidth: 1,
             lineColor: '#000',
             tickColor: '#000',
             labels: {
                style: {
                   color: '#000',
                   font: '11px Iransans, Verdana, sans-serif'
                }
             },
             title: {
                style: {
                   color: '#333',
                   fontWeight: 'bold',
                   fontSize: '12px',
                   fontFamily: 'Iransans, Verdana, sans-serif'

                }
             }
          },
          yAxis: {
             minorTickInterval: 'auto',
             lineColor: '#000',
             lineWidth: 1,
             tickWidth: 1,
             tickColor: '#000',
             labels: {
                style: {
                   color: '#000',
                   font: '11px Iransans, Verdana, sans-serif'
                }
             },
             title: {
                style: {
                   color: '#333',
                   fontWeight: 'bold',
                   fontSize: '12px',
                   fontFamily: 'Iransans, Verdana, sans-serif'
                }
             }
          },
          legend: {
             itemStyle: {
                font: '9pt Iransans, Verdana, sans-serif',
                color: 'black'
             },
             itemHoverStyle: {
                color: '#039'
             },
             itemHiddenStyle: {
                color: 'gray'
             }
          },
          labels: {
             style: {
                color: '#99b'
             }
          },

          navigation: {
             buttonOptions: {
                theme: {
                   stroke: '#CCCCCC'
                }
             }
          }
       };

       // Apply the theme
       var highchartsOptions = Highcharts.setOptions(theme);
    },
            
    radializeChartColor: function()
    {
    },


    Pie : function(container,title,pieData){
        var sum=0;
        $.each(pieData,function(){
            sum+=this[1];
        });
        
        this.ThemeChart();
        // Build the chart
        return new Highcharts.Chart({
            chart: {
                renderTo: container,
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
            },
            title: {
                text: title
            },
            credits: {
                enabled: false
            },
            tooltip: {
                useHTML: true,
                style: {'textAlign': 'right'},
                formatter: function() {
                    return '<b>'+ this.point.name +'</b><br/>'+Highcharts.numberFormat(this.y, 0, ',')+' ('+((this.y/sum)*100).toFixed(2) +'%)';
                }
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    shadow: false,
                    borderWidth: 0,
                    colorByPoint: true,
                    dataLabels: {
                        enabled: true,
                        color: '#000000',
                        connectorColor: '#000000',
                        formatter: function() {
                            return '<b>'+ this.point.name +'</b>';
                        },
                        useHTML: true
                    }
                }
            },
            exporting: {
                url: ('https:' == document.location.protocol ? 'https://' : 'http://') + 'jeeb.ir/chart_export/index.php',
                type: 'image/png',
                buttons: {
                    exportButton: {
                        menuItems: null,
                        onclick: function() {
                            this.exportChart();
                        }
                    }
                }
            },
            series: [{
                type: 'pie',
                data: pieData
            }]
        });
        
    },
    Line : function(container,title,ytitle,categoriesData,lineData){
        this.ThemeChart();
        return new Highcharts.Chart({
            chart: {
                renderTo: container,
                defaultSeriesType: 'line'
            },
            title: {
                text: title,    
                x: -20 //center
            },
            credits: {
                enabled: false
            },            
            xAxis: {
                categories: categoriesData,
                labels: {
                    rotation: -70
                }
            },
            yAxis: {
                title: {
                    text: ytitle,
                    x:-20,
                    y:10
                },
                labels: {
                    rotation: -90
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }]
            },
            tooltip: {
                useHTML: true,
                style: {'textAlign': 'right'},                
                formatter: function() {
                    return '<b>'+ this.series.name +'</b><br/>'+
                    this.x +': '+ Highcharts.numberFormat(this.y, 0, ',');
                }               
            },
            legend: {
                layout: 'horizontal',
                align: 'center',
                verticalAlign: 'bottom',
                borderWidth: 0
            },
            exporting: {
                url: ('https:' == document.location.protocol ? 'https://' : 'http://') + 'jeeb.ir/chart_export/index.php',
                type: 'image/png',
                buttons: {
                    exportButton: {
                        menuItems: null,
                        onclick: function() {
                            this.exportChart();
                        }
                    }
                }
            },
            series: lineData
        });            
    },
    Column : function(container,title,ytitle,categoriesData,columnData){
        this.ThemeChart();
        return new Highcharts.Chart({
            chart: {
                renderTo: container,
                defaultSeriesType: 'column'
            },
            title: {
                text: title,              
                x: -20 //center
            },
            credits: {
                enabled: false
            },            
            xAxis: {
                categories: categoriesData,
                labels: {
                    rotation: -70
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: ytitle,
                    x:-20,
                    y:10
                },
                labels: {
                    rotation: -90
                }
            },
            tooltip: {
                useHTML: true,
                style: {'textAlign': 'right'},                
                formatter: function() {
                    return '<b>'+ this.series.name +'</b><br/>'+
                    this.x +': '+ Highcharts.numberFormat(this.y, 0, ',');
                }                
            },
            legend: {
                layout: 'horizontal',
                align: 'center',
                verticalAlign: 'bottom',
                borderWidth: 0
            },
            plotOptions: {
                column: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    shadow: false,
                    borderWidth: 0
                }
            },
            exporting: {
                url: ('https:' == document.location.protocol ? 'https://' : 'http://') + 'jeeb.ir/chart_export/index.php',
                type: 'image/png',
                buttons: {
                    exportButton: {
                        menuItems: null,
                        onclick: function() {
                            this.exportChart();
                        }
                    }
                }
            },
            series: columnData
        });            
    },
    Area : function(container,title,ytitle,categoriesData,areaData){
        this.ThemeChart();
        return new Highcharts.Chart({
            chart: {
                renderTo: container,
                defaultSeriesType: 'area'
            },
            title: {
                text: title,              
                x: -20 //center
            },
            credits: {
                enabled: false
            },            
            xAxis: {
                categories: categoriesData
            },
            yAxis: {
                min: 0,
                title: {
                    text: ytitle
                }
            },
            tooltip: {
                useHTML: true,
                style: {'textAlign': 'right'},                
                formatter: function() {
                    return '<b>'+ this.series.name +'</b><br/>'+
                    this.x +': '+ this.y;
                }                
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'top',
                x: -10,
                y: 100,
                borderWidth: 0
            },
            exporting: {
                url: ('https:' == document.location.protocol ? 'https://' : 'http://') + 'jeeb.ir/chart_export/index.php',
                type: 'image/png',
                buttons: {
                    exportButton: {
                        menuItems: null,
                        onclick: function() {
                            this.exportChart();
                        }
                    }
                }
            },
            series: areaData
        });            
    },
    
    BarChart: function(container,title,ytitle,categoriesData,barData)
    {
        this.ThemeChart();
        return new Highcharts.Chart({
            chart: {
                renderTo: container,
                defaultSeriesType: 'bar',
                type: 'bar'
            },
            title: {
                text: title
            },
            /*subtitle: {
                text: 'Source: Wikipedia.org'
            },*/
            xAxis: {
                categories: categoriesData,
                title: {
                    text: null
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: ytitle,
                    align: 'high'
                },
                labels: {
                    overflow: 'justify'
                }
            },
            tooltip: {
                valueSuffix: ''
            },
            plotOptions: {
                bar: {
                    dataLabels: {
                        enabled: true
                    }
                }
            },
            legend: {
                layout: 'horizontal',
                align: 'center',
                verticalAlign: 'bottom',
                x: 5,
                y: 5,
                floating: true,
                borderWidth: 1,
                backgroundColor: '#FFFFFF',
                shadow: true
            },
            exporting: {
                url: ('https:' == document.location.protocol ? 'https://' : 'http://') + 'jeeb.ir/chart_export/index.php',
                type: 'image/png',
                buttons: {
                    exportButton: {
                        menuItems: null,
                        onclick: function() {
                            this.exportChart();
                        }
                    }
                }
            },
            credits: {
                enabled: false
            },
            series: barData
        });
    },
    
    Toggle : function(){
        var default_hide = {
            "grid": true,
            "filter": true
        };
        $.each(
            ["help", "filter", "grid", "accordion", "shortlinks"],
            function() {
                var el = $("#" +  this);
                if (default_hide[this]) {
                    el.hide();
                    $("[id='toggle-"+this+"']").addClass("hidden")
                }
                $("[id='toggle-"+this+"']")
                .bind("click", function(e) {
                    el.toggle('blind',400);
                    $(this).toggleClass('hidden visible');
                    e.preventDefault();
                });
            }
        );
    },
    bindExpenseSubCategories : function(that,expenseCategories,expense_sub_category_id,expense_sub_category_tag_id){
        //check if the subcategory select exists
        if(that.next().attr('id')!=that.attr('id')){
            that.after('<select id="'+expense_sub_category_tag_id+'" name="data[Expense][expense_sub_category_id]"><option value=""></option></select>');
        }
        $('#'+expense_sub_category_tag_id).html('<option value="0">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>');
        var content='';
        $.each(expenseCategories,function(i, val){
            if(val['id']==that.val()) {
                $.each(val['subs'],function(i2, val2){
                    if(val2['id']==expense_sub_category_id) {
                        content+='<option selected="selected" value="'+val2['id']+'">'+val2['name']+'</option>';
                    } else {
                        content+='<option value="'+val2['id']+'">'+val2['name']+'</option>';
                    }

                });
            }
        });
        $('#'+expense_sub_category_tag_id).html('<option value="0">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>'+content);

        //modify options on change
        that.change(function(){
            //check if the subcategory select exists
            if(that.next().attr('id')!=expense_sub_category_tag_id){
                that.after('<select id="'+expense_sub_category_tag_id+'" name="data[Expense][expense_sub_category_id]"><option value=""></option></select>');
            }
            $('#'+expense_sub_category_tag_id).html('<option value="0">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>');
            content='';
            $.each(expenseCategories,function(i, val){
                if(val['id']==that.val()) {
                    $.each(val['subs'],function(i2, val2){
                        content+='<option value="'+val2['id']+'">'+val2['name']+'</option>';
                    });
                }
            });
            $('#'+expense_sub_category_tag_id).html('<option value="0">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>'+content);
        });    
        
    },
    bindIncomeSubTypes : function(that,incomeTypes,income_sub_type_id,income_sub_type_tag_id){
        //check if the subcategory select exists
        if(that.next().attr('id')!=that.attr('id')){
            that.after('<select id="'+income_sub_type_tag_id+'" name="data[Income][income_sub_type_id]"><option value=""></option></select>');
        }
        $('#'+income_sub_type_tag_id).html('<option value="0">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>');
        var content='';
        $.each(incomeTypes,function(i, val){
            if(val['id']==that.val()) {
                $.each(val['subs'],function(i2, val2){
                    if(val2['id']==income_sub_type_id) {
                        content+='<option selected="selected" value="'+val2['id']+'">'+val2['name']+'</option>';
                    } else {
                        content+='<option value="'+val2['id']+'">'+val2['name']+'</option>';
                    }

                });
            }
        });
        $('#'+income_sub_type_tag_id).html('<option value="0">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>'+content);

        //modify options on change
        that.change(function(){
            //check if the subcategory select exists
            if(that.next().attr('id')!=income_sub_type_tag_id){
                that.after('<select id="'+income_sub_type_tag_id+'" name="data[Income][income_sub_type_id]"><option value=""></option></select>');
            }
            $('#'+income_sub_type_tag_id).html('<option value="0">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>');
            content='';
            $.each(incomeTypes,function(i, val){
                if(val['id']==that.val()) {
                    $.each(val['subs'],function(i2, val2){
                        content+='<option value="'+val2['id']+'">'+val2['name']+'</option>';
                    });
                }
            });
            $('#'+income_sub_type_tag_id).html('<option value="0">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>'+content);
        });    
        
    },
    FormatPrice : function(that) {
        that.formatCurrency({
            symobol: 0,
            roundToDecimalPlace: 0
        });
        that.blur(function() {
            that.formatCurrency({
                symobol: 0,
                roundToDecimalPlace: 0
            });
        })
        .keyup(function(e) {
            num = that[that.is('input, select, textarea') ? 'val' : 'html']();
            num=num.replace("۰", "0");
            num=num.replace("۱", "1");
            num=num.replace("۲", "2");
            num=num.replace("۳", "3");
            num=num.replace("۴", "4");
            num=num.replace("۵", "5");
            num=num.replace("۶", "6");
            num=num.replace("۷", "7");
            num=num.replace("۸", "8");
            num=num.replace("۹", "9");
            that.val(num);
            var e = window.event || e;
            var keyUnicode = e.charCode || e.keyCode;
            if (e !== undefined) {
                switch (keyUnicode) {
                    case 16:
                        break; // Shift
                    case 17:
                        break; // Ctrl
                    case 18:
                        break; // Alt
                    case 27:
                        this.value = '';
                        break; // Esc: clear entry
                    case 35:
                        break; // End
                    case 36:
                        break; // Home
                    case 37:
                        break; // cursor left
                    case 38:
                        break; // cursor up
                    case 39:
                        break; // cursor right
                    case 40:
                        break; // cursor down
                    case 78:
                        break; // N (Opera 9.63+ maps the "." from the number key section to the "N" key too!) (See: http://unixpapa.com/js/key.html search for ". Del")
                    case 110:
                        break; // . number block (Opera 9.63+ maps the "." from the number block to the "N" key (78) !!!)
                    case 190:
                        break; // .
                    default:
                        that.formatCurrency({
                            symobol: 0,
                            roundToDecimalPlace: 0
                        });
                }
            }
        });
    },
    GetAjaxPage: function(url) {
        return $.ajax({
            url: url,
            context: document.body,
            async: false,
            error:function (XMLHttpRequest, textStatus, errorThrown){
                alert('مشکلی در ارتباط با سرور پیش آمد لطفا دوباره تلاش کنید.');
                location.reload();
            }
        }).responseText;
    },
    InstallmentDone: function(url,addExpense) {
        $.ajax({
            type: "POST",
            url: url,
            data: {
                'status':'done',
                'addExpense':addExpense
            },
            dataType: "json",
            async: false,
            error:function (XMLHttpRequest, textStatus, errorThrown){
                alert('مشکلی در ارتباط با سرور پیش آمد لطفا دوباره تلاش کنید.');
            }
        });
    },
    CheckDone: function(url,account_id) {
        $.ajax({
            type: "POST",
            url: url,
            data: {
                'status':'done',
                'account_id':account_id
            },
            dataType: "json",
            async: false,
            error:function (XMLHttpRequest, textStatus, errorThrown){
                alert('مشکلی در ارتباط با سرور پیش آمد لطفا دوباره تلاش کنید.');
            }
        });
        return;
    },
    DebtDone: function(url,addData,account_id,state,settled_amount) {
        $.ajax({
            type: "POST",
            url: url,
            data: {
                'status':'done',
                'state':state,//part,all
                'settled_amount':settled_amount,
                'addData':addData,
                'account_id':account_id
            },
            dataType: "json",
            async: false,
            error:function (XMLHttpRequest, textStatus, errorThrown){
                alert('مشکلی در ارتباط با سرور پیش آمد لطفا دوباره تلاش کنید.');
            }
        });
    },
    showBalance: function(url,id)
    {
        return $.ajax({
            type: "POST",
            url: url,
            data: {
                'id':id
            },
            dataType: "json",
            async: false,
            error:function (XMLHttpRequest, textStatus, errorThrown){
                alert('مشکلی در ارتباط با سرور پیش آمد لطفا دوباره تلاش کنید.');
            }
        }).responseText;
    },
    CheckDiscountCode: function(url,code) {
        var result=false;
        $.ajax({
            type: "POST",
            url: url,
            data: {
                'code':code
            },
            dataType: "json",
            async: false,
            success: function(response){
                result=response;
            },
            error:function (XMLHttpRequest, textStatus, errorThrown){
                alert('مشکلی در ارتباط با سرور پیش آمد لطفا دوباره تلاش کنید.');
                return false;
            }
        });
        return result;
    },
    highlightRow: function() {
        $("#dataTable tr").hover(
            function()
            {
                $(this).addClass("highlight");
            },
            function()
            {
                $(this).removeClass("highlight");
            }
        );
    },
    tip: function(object,width,content) {
        object.bt(content,{
            positions: 'top',
            width: width,
            fill: '#EFF2F5', 
            strokeStyle: '#B7B7B7',
            spikeLength: 10, 
            spikeGirth: 10,
            padding: 10, 
            cornerRadius: 8, 
            cssStyles: {
                fontSize: '11px'
            }
        });        
    },
    
    accountBalance: function (balance, select, target)
    {
        select = $(select);
        target = $(target);
        var b = Number(balance[select.val()]);
        b = '<span class="ltr" style="color:'+((b>0)? 'green' : '#C62121')+'">'+Highcharts.numberFormat(b, 0, ',').replace(/([-])?([{0-9\,}]+)/i,'$2')+' '+((b>0)? '+' : '-')+'</span>';
        target.html(""+b);
    },
            
    /*
     * @param HTMLDOMElement st start field
     * @param HTMLDOMElement en end field
     * @param string frd from date
     * @param string tod date
     * @returns bool
     */
    fillDate: function(st,en,frd,tod) {
        $(st).val(frd);
        $(en).val(tod);
    },
            
    processHash: function()
    {
        var url = window.location.href;
        var match = url.match(/#(.+)$/);
        if( match ) {
            match = decodeURIComponent(match[1]);
            if( !$(match) )
                return;
            $('html, body').animate({
                scrollTop: $( match ).offset().top
            }, 500);
        }
    }
    
}

FloatingList = $.klass({        
    options: {},
    selectCallback: function() { return true; },
    listChangeItem: function() { return true; },
    onmake: function() { return true; },
    inpId: null,
    AddedListHolder: null,
    CategoryListHolder: null,
    CategoryList: null,
    inputName: null,
    holderId: 'CategoryListHolder',
    listId:'CategoryList',
    groupListClass: 'groupListHolder',
    data: {},
    eachHeight:0,    
    postSelectCallback: null,
    allowNew: false,
    multiSelect: true,
    empty: true,
    
    init: function(options)
    {
        this.options = options;
        jQuery.each(options, jQuery.proxy( function(n,v){
            if( 'object' == typeof v ) {
                d = jQuery.extend({}, v);
            } else {
                d = v;
            }
            this[n] = d;
            delete this.options[n];
        }, this) );
        
        this.AddedListHolder = $(this.listholder);
        this.inpId = $(this.input);
        
        preload = [];
        for(i in  this.preload) {
            preload[preload.length] = this.preload[i];
        }
        
        this.addedItems = {};
        
        this.make(preload || []);
    },
    
    make: function(dflt)
    {    
        /*this.selectedItem = jQuery('<input/>',{
            'id': this.inpId.attr('id')+'SelectedItem',
            'type': 'hidden',
            'name': 
        });
        this.selectedItem.insertAfter(this.inpId);*/
        this.inputName = this.inpId.attr('name');
        this.inpId.attr('name','');
        this.inpId.attr('selectedIndex',0);
        
        this.AddedListHolder.addClass(this.groupListClass);
        
        this.CategoryListHolder = jQuery('<div/>', {
            'id': this.holderId,
            'class': 'floatingList'
        });
        this.CategoryListHolder.insertAfter(this.inpId);
        
        this.CategoryList = jQuery('<div/>', {
            'id': this.listId,
        });
        this.CategoryList.appendTo(this.CategoryListHolder);
        
        if(this.allowNew) {
            this.makeListItem('new','');
        }
        
        jQuery.each(this.data, jQuery.proxy(function(k,v){
            this.makeListItem(k,v);
        },this));
        this.eachHeight = 35; //this.CategoryList.find('.listItem').outerHeight();
        this.CategoryListHolder.css('display','none');
        this.moveInList(0);
        
        if(this.empty) {
            this.data[0] = 'همه موارد';
        }
        
        if(this.allowNew) {
            this.data['new'] = '';
        }       
        
        this.onmake();
        
        if(dflt.length) {
            jQuery.each(dflt, jQuery.proxy( function(k,v){
                this.addItem(v);
            }, this));
        }else if(this.empty){
            this.addItem(0);//this.insertItemBox(0,'همه موارد');
        }
        
        this.inpId.keydown( jQuery.proxy(this.keydownEvent,this) );
        this.inpId.keyup( jQuery.proxy(this.keyupEvent,this) );
        this.inpId.keypress( jQuery.proxy(this.keypressEvent,this) );
        this.inpId.focus( jQuery.proxy(this.focusEvent,this) );
        this.inpId.blur( jQuery.proxy(this.blurEvent,this) );
        
        this.CategoryListHolder.bind('DOMMouseScroll', jQuery.proxy( this.scrollEvent, this ) );
        this.CategoryListHolder.bind('mousewheel', jQuery.proxy( this.scrollEvent, this ) );
    },
            
    makeListItem: function(id,title)
    {
        var Data = jQuery('<div>', {
            'id': 'listItem_'+id,
            'class': 'listItem visible',
            'identifier': id,
            'html': title
        });
        Data.mouseover( jQuery.proxy( this.mouseoveronitemEvent, this) );
        Data.click( jQuery.proxy( this.clickonitemEvent, this) );
        Data.appendTo(this.CategoryList);
    },
            
    updateNewItem: function(val)
    {
        if(!this.allowNew) return;
        val = val || '';
        
        var newItem = this.CategoryList.find('.listItem#listItem_new');
        
        var exists = false;
        this.CategoryList.find(".listItem:contains('"+val+"')").each(function(e,v){
            if($(v).attr('identifier')[0]=='n') {
                return;
            }
            exists = ($(v).html()==val) || exists;
        });
        
        if(exists) {
            val = '';
        }
        
        newItem.html(val);
        delete this.data['n'+newItem.html()];
        newItem.attr('identifier','n'+val);
        this.data['n'+val] = val;
    },
            
    keydownEvent: function(e)
    {
        switch(e.which){
            case 38: //up
                this.showList();
                this.moveUp();
                break;
            case 40: //down
                this.showList();
                this.moveDown()
                break;
            case 39:
            case 37:
                break;
            default:
                break;
        }
    },
            
    keyupEvent: function(e)
    {
        var target = $(e.currentTarget);
        switch(e.which) {
            case 38:
            case 40:
            case 39:
            case 37:
                break;
            case 13:
                e.preventDefault();
                return false;
            case 27:
                this.hideList();
                break;
            default:
                this.showList();
                this.updateNewItem(target.val());
                this.filterList(target.val());
                this.moveInList(0);
        }
    },
            
    keypressEvent: function(e)
    {
        if(e.which != 13) {
            return;
        }
        e.preventDefault();
        
        this.selectItem();
        
        return false;
    },
            
    mouseoveronitemEvent: function(e)
    {
        var l = this.CategoryList.find('.listItem.visible');
        var id = $(e.currentTarget).attr('identifier');
        for(i in l) {
            if( $(l[i]).attr('identifier') == id ) {
                break;
            }
        }
        this.moveInList(i);
    },
            
    scrollEvent: function(e)
    {
        if( (typeof(e.originalEvent.detail) != 'undefined') && e.originalEvent.detail ) {
            if(e.originalEvent.detail > 0) {
                //scroll down
                this.moveDown();
            }else {
                //scroll up
                this.moveUp();
            }
        } else {
            if(e.originalEvent.wheelDelta < 0) {
                //scroll down
                this.moveDown();
            }else {
                //scroll up
                this.moveUp();
            }
        }
        //prevent page fom scrolling
        return false;
    },
            
    selectItem: function()
    {
        $ind = this.inpId.attr('selectedIndex');
        $item = $(this.CategoryList.find('.listItem.visible')[this.inpId.attr('selectedIndex')]);
        $itemid = $item.attr('identifier');
        
        this.addItem($itemid);
        
        this.inpId.val('');
        this.hideList();
    },
            
    clickonitemEvent: function(e)
    {
        this.selectItem();
    },
            
    moveDown: function()
    {
        var currInd = this.inpId.attr('selectedIndex');
        if(currInd<this.CategoryList.find('.listItem.visible').size()-1) { 
            this.moveInList(++currInd);
        }
    },
            
    moveUp: function()
    {
        var currInd = this.inpId.attr('selectedIndex');
        if(currInd>0) {
            this.moveInList(--currInd);
        }
    },
            
    moveInList: function(id)
    {
        this.inpId.attr('selectedIndex',id);
        this.CategoryList.find('.listItem.selected').removeClass('selected');
        $(this.CategoryList.find('.listItem.visible')[id]).addClass('selected');
        
        var mtop = Number(this.CategoryList.css('margin-top').match(/^(.+)px/)[1]);
        
        var visibleHeight = this.eachHeight*id*-1;
        var maxVisible = mtop-(this.eachHeight*3);
        if( (visibleHeight<=maxVisible) || (visibleHeight>mtop)) {
            this.CategoryList.css('margin-top', (visibleHeight<mtop)? mtop+(visibleHeight-(mtop-(this.eachHeight*2))) : visibleHeight );
        }
    },
    
    filterList: function(filter)
    {
        this.CategoryList.find('.listItem').each(function(e,v){
            if( $(v).html().match(filter) ) {
                $(v).addClass('visible');
            }else{
                $(v).removeClass('visible');
            }
        });
        
    },
            
    focusEvent: function(e)
    {
        this.showList();
        this.filterList(this.inpId.val());
        this.moveInList(0);
    },
            
    blurEvent: function(e)
    {
        setTimeout( jQuery.proxy(this.hideList,this) , 200 ) ;
    },
            
    hideList: function()
    {
        this.CategoryListHolder.css('display','none');
    },
            
    showList: function()
    {
        if( this.CategoryListHolder.css('display') != 'block' ) {
            $('#'+this.inpId.attr('id')+' + #'+this.CategoryListHolder.attr('id')).css('left',this.inpId.position().left);
            $('#'+this.inpId.attr('id')+' + #'+this.CategoryListHolder.attr('id')).css('width',this.inpId.width() + Number(this.inpId.css('padding-left').match(/\d+/)[0]) + Number(this.inpId.css('padding-right').match(/\d+/)[0]) );
            this.CategoryListHolder.css('display','block');
        }
    },
     
    addItem: function(itemid)
    {
        if(itemid.length<2 || itemid == 'new') {
            return;
        }
        if(!this.selectCallback(itemid)) {
            return false;
        }
        
        if(typeof(this.addedItems[itemid])!='undefined') {
            return;
        }
        
        this.insertItemBox(itemid, this.data[itemid]);
        this.listChangeItem(itemid);
        this.updateNewItem();
        this.inpId.val('');
    },
            
    insertItemBox: function(itemid,text)
    {
        this.addedItems[itemid] = jQuery('<div>', {
            'id': 'groupListItem_'+itemid,
            'class': 'groupListItem include'
        });
        var inp = jQuery('<input>',{
            'type': 'hidden',
            'id': 'groupListItemInput_'+itemid,
            'value': itemid,
            'name': this.inputName+'[]'
            
        });
        var remBut = jQuery('<a>', {
            'html': '&nbsp;'
        });
        remBut.click( jQuery.proxy( this.removebuttonEvent, this ) );
        
        var incName = jQuery('<span>', {
            'html': text
        });
        this.addedItems[itemid].appendTo( this.AddedListHolder );
        
        remBut.appendTo(this.addedItems[itemid]);
        incName.appendTo(this.addedItems[itemid]);
        inp.appendTo(this.addedItems[itemid]);
        
        if(itemid!=0) {
            this.removeItem(0);
        }
        
    },
            
    removebuttonEvent: function(e)
    {
        this.removeItem( $(e.currentTarget).parent().attr('id').match(/groupListItem_(.+)/)[1] );
    },
            
    removeItem: function(itemid)
    {
        if(typeof(this.addedItems[itemid])=='undefined') {
            return;
        }
        this.addedItems[itemid].remove();
        delete this.addedItems[itemid];
        if( !this.objectLength(this.addedItems) && this.empty ){
            this.addItem(0);
        }
        this.listChangeItem(itemid);
    },
    
    objectLength: function(obj)
    {
        var count = 0;
        var i;
        for (i in obj) {
            if (obj.hasOwnProperty(i)) {
                count++;
            }
        }
        return count;
    }
    
});

jQuery(function ($) {
    if($(".datepicker").length){
        jeeb.DatePicker();
    }
    if($("[id^='toggle']").length){
        jeeb.Toggle();
    }
});
