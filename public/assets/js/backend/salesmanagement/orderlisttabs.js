define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            Table.api.init({
               
            });
          
              //绑定事件
              $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                var panel = $($(this).attr("href"));
                if (panel.size() > 0) {
                    Controller.table[panel.attr("id")].call(this);
                    $(this).on('click', function (e) {
                        $($(this).attr("href")).find(".btn-refresh").trigger("click");
                    });
                }
                //移除绑定的事件
                $(this).unbind('shown.bs.tab');
            });
            
            //必须默认触发shown.bs.tab事件
            $('ul.nav-tabs li.active a[data-toggle="tab"]').trigger("shown.bs.tab"); 
        
        },
        table:{
            order_acar: function () {

                // 表格1
                var orderAcar = $("#orderAcar"); 
                 
                $(".btn-add").data("area", ["95%","95%"]); 
                $(".btn-edit").data("area", ["95%","95%"]); 
                
                 // 初始化表格
                 orderAcar.bootstrapTable({
                url: 'salesmanagement/Orderlisttabs/orderAcar',
                extend: {
                    // index_url: 'order/salesorder/index',
                    add_url: 'order/salesorder/add',
                    edit_url: 'order/salesorder/edit',
                    del_url: 'order/salesorder/del',
                    multi_url: 'order/salesorder/multi',
                    table: 'sales_order',
                },
                toolbar: '#toolbar1',
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'order_no', title: __('Order_no')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},

                        {field: 'financial_platform_name', title: __('金融平台')},
                        {field: 'models_name', title: __('销售车型')},
                        {field: 'username', title: __('Username')},
                        {field: 'genderdata', title: __('Genderdata'), visible:false, searchList: {"male":__('genderdata male'),"female":__('genderdata female')}},
                        {field: 'genderdata_text', title: __('Genderdata'), operate:false},
                        {field: 'phone', title: __('Phone')},
                        {field: 'id_card', title: __('Id_card')},
                        {field: 'id', title: __('查看详细资料'), table: orderAcar, buttons: [
                            {name: 'details', text: '查看详细资料', title: '查看订单详细资料' ,icon: 'fa fa-eye',classname: 'btn btn-xs btn-primary btn-dialog btn-details', 
                                url: 'salesmanagement/Orderlisttabs/details', callback:function(data){
                                    console.log(data)
                                }
                            } 
                            ],
                            
                            operate:false, formatter: Table.api.formatter.buttons
                        },
                        {field: 'payment', title: __('首付（元）')},
                        {field: 'monthly', title: __('月供（元）')},
                        {field: 'nperlist', title: __('期数')},
                        {field: 'margin', title: __('保证金（元）')},
                        {field: 'tail_section', title: __('尾款（元）')},
                        {field: 'gps', title: __('GPS（元）')},
                        // {field: 'sales_id', title: __('Sales_id')},
                        // {field: 'backoffice_id', title: __('Backoffice_id')},
                        // {field: 'control_id', title: __('Control_id')},
                        // {field: 'new_car_id', title: __('New_car_id')},
                    
                      
                        // {field: 'city', title: __('City'),formatter:function(value,row,index){
                        //     return value+''+row.detailed_address;
                        // }},
                        // {field: 'detailed_address', title: __('Detailed_address')},
                        // {field: 'emergency_contact_1', title: __('Emergency_contact_1')},
                        // {field: 'emergency_contact_2', title: __('Emergency_contact_2')},
                        // {field: 'family_members', title: __('Family_members')},
                        // {field: 'customer_source', title: __('Customer_source'), visible:false, searchList: {"direct_the_guest":__('customer_source direct_the_guest'),"turn_to_introduce":__('customer_source turn_to_introduce')}},
                        // {field: 'customer_source_text', title: __('Customer_source'), operate:false},
                        // {field: 'turn_to_introduce_name', title: __('Turn_to_introduce_name')},
                        // {field: 'turn_to_introduce_phone', title: __('Turn_to_introduce_phone')},
                        // {field: 'turn_to_introduce_card', title: __('Turn_to_introduce_card')},
                        // {field: 'id_cardimages', title: __('Id_cardimages'), formatter: Table.api.formatter.images},
                        // {field: 'drivers_licenseimages', title: __('Drivers_licenseimages'), formatter: Table.api.formatter.images},
                        // {field: 'residence_bookletimages', title: __('Residence_bookletimages'), formatter: Table.api.formatter.images},
                        // {field: 'housingimages', title: __('Housingimages'), formatter: Table.api.formatter.images},
                        // {field: 'bank_cardimages', title: __('Bank_cardimages'), formatter: Table.api.formatter.images},
                        // {field: 'application_formimages', title: __('Application_formimages'), formatter: Table.api.formatter.images},
                        // {field: 'call_listfiles', title: __('Call_listfiles')},
                        // {field: 'credit_reportimages', title: __('Credit_reportimages'), formatter: Table.api.formatter.images},
                        // {field: 'deposit_contractimages', title: __('Deposit_contractimages'), formatter: Table.api.formatter.images},
                        // {field: 'deposit_receiptimages', title: __('Deposit_receiptimages'), formatter: Table.api.formatter.images},
                        // {field: 'guarantee_id_cardimages', title: __('Guarantee_id_cardimages'), formatter: Table.api.formatter.images},
                        // {field: 'guarantee_agreementimages', title: __('Guarantee_agreementimages'), formatter: Table.api.formatter.images},
                        // {field: 'review_the_data', title: __('Review_the_data'), visible:false, searchList: {"not_through":__('review_the_data not_through'),"through":__('review_the_data through'),"credit_report":__('review_the_data credit_report'),"the_guarantor":__('review_the_data the_guarantor'),"for_the_car":__('review_the_data for_the_car'),"the_car":__('review_the_data the_car')}},
                        // {field: 'review_the_data_text', title: __('Review_the_data'), operate:false},
                        // {field: 'delivery_datetime', title: __('Delivery_datetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        // {field: 'planacar.models_id', title: __('Planacar.models_id')},
                        {field: 'operate', title: __('Operate'), table: orderAcar, 
                        buttons: [
                            {
                                name:'submit_audit',text:'提交审核', title:'提交到风控审核征信', icon: 'fa fa-share',extend: 'data-toggle="tooltip"',classname: 'btn btn-xs btn-info btn-submit_audit',
                                url: 'salesmanagement/orderlisttabs/sedAudit',  
                                //等于is_reviewing_true 的时候操作栏显示的是正在审核四个字，隐藏编辑和删除
                                //等于is_reviewing 的时候操作栏显示的是提交审核按钮 四个字，显示编辑和删除 
                                //....
                                hidden:function(row){ /**提交审核 */
                                    if(row.review_the_data == 'is_reviewing'){ 
                                        return false; 
                                    }  
                                    else if(row.review_the_data == 'is_reviewing_true'){
                                        return true;
                                    }
                                    else if(row.review_the_data == 'for_the_car'){ 
                                        return true; 
                                    }
                                    else if(row.review_the_data == 'not_through'){
                                        return true;
                                    }
                                    else if(row.review_the_data == 'the_guarantor'){
                                        return true;
                                    }
                                }
                            },
                            { 
                                icon: 'fa fa-trash', name: 'del', icon: 'fa fa-trash', extend: 'data-toggle="tooltip"',title: __('Del'),classname: 'btn btn-xs btn-danger btn-delone',
                                url:'order/salesorder/del',/**删除 */
                                hidden:function(row){
                                    if(row.review_the_data == 'is_reviewing'){ 
                                        return false; 
                                    }
                                    else if(row.review_the_data == 'is_reviewing_true'){
                                      
                                        return true;
                                    } 
                                    else if(row.review_the_data == 'for_the_car'){ 
                                        return true; 
                                    }
                                    else if(row.review_the_data == 'not_through'){
                                        return true;
                                    }
                                    else if(row.review_the_data == 'the_guarantor'){
                                        return true;
                                    }
                                },
                                
                            },
                            { 
                                name: 'edit',text: '',icon: 'fa fa-pencil',extend: 'data-toggle="tooltip"',  title: __('Edit'),classname: 'btn btn-xs btn-success btn-editone', 
                                url:'order/salesorder/edit',/**编辑 */
                                hidden:function(row,value,index){ 
                                    if(row.review_the_data == 'is_reviewing'){ 
                                        return false; 
                                    } 
                                    else if(row.review_the_data == 'is_reviewing_true'){ 
                                        return true;
                                    } 
                                    else if(row.review_the_data == 'for_the_car'){ 
                                        return true; 
                                    }
                                    else if(row.review_the_data == 'not_through'){
                                        return true;
                                    }
                                    else if(row.review_the_data == 'the_guarantor'){
                                        return true;
                                    }
                                }, 
                            },
                            {
                                name: 'edit',text: '正在审核中', 
                                hidden:function(row){  /**正在审核 */
                                    if(row.review_the_data == 'is_reviewing_true'){ 
                                        return false; 
                                    }
                                    else if(row.review_the_data == 'is_reviewing'){
                                        return true;
                                    }
                                    else if(row.review_the_data == 'for_the_car'){
                                        return true;
                                    }
                                    else if(row.review_the_data == 'not_through'){
                                        return true;
                                    }
                                    else if(row.review_the_data == 'the_guarantor'){
                                        return true;
                                    }
                                }
                            },
                            {
                                name: 'for_the_car',icon:'fa fa-check-circle',text: '征信已通过，车管正在备车中', classname: ' text-success ',
                                hidden:function(row){  /**征信已通过，车管正在备车中 */ 
                                    if(row.review_the_data == 'for_the_car'){ 
                                        return false; 
                                    }
                                    else if(row.review_the_data == 'is_reviewing_true'){ 
                                        return true; 
                                    }
                                    else if(row.review_the_data == 'is_reviewing'){
                                        return true;
                                    }
                                    else if(row.review_the_data == 'not_through'){
                                        return true;
                                    }
                                    else if(row.review_the_data == 'the_guarantor'){
                                        return true;
                                    }
                                }
                            },
                            {
                                name: 'not_through',icon:'fa fa-times',text: '征信未通过，订单已关闭', classname: ' text-danger ',
                                hidden:function(row){  /**征信不通过 */ 
                                 
                                    if(row.review_the_data == 'not_through'){ 
                                        return false; 
                                    }
                                    else if(row.review_the_data == 'for_the_car'){ 
                                        return true; 
                                    }
                                    else if(row.review_the_data == 'is_reviewing_true'){ 
                                        return true; 
                                    }
                                    else if(row.review_the_data == 'is_reviewing'){
                                        return true;
                                    }
                                    else if(row.review_the_data == 'the_guarantor'){
                                        return true;
                                    }
                                }
                            },
                            {
                                name: 'the_guarantor',icon:'fa fa-upload',text: '需提供担保人',extend: 'data-toggle="tooltip"',  title: __('点击上传提供担保人信息'), classname: ' text-danger ',classname: 'btn btn-xs btn-warning btn-the_guarantor',
                                hidden:function(row){  /**提供担保人 */ 
                                    
                                    if(row.review_the_data == 'the_guarantor'){ 
                                        return false; 
                                    }
                                    else if(row.review_the_data == 'not_through'){ 
                                        return true; 
                                    }
                                    else if(row.review_the_data == 'for_the_car'){ 
                                        return true; 
                                    }
                                    else if(row.review_the_data == 'is_reviewing_true'){ 
                                        return true; 
                                    }
                                    else if(row.review_the_data == 'is_reviewing'){
                                        return true;
                                    }
                                }
                            }


                        ],
                            events: Controller.api.events.operate,
                             
                            formatter: Controller.api.formatter.operate
                           
                        }
                    ]
                ]
                
            });
         

            orderAcar.on('load-success.bs.table',function(e,data){
                // console.log(data);
                $('#badge_order_acar').text(data.total);
                $(".btn-details").data("area", ["95%","95%"]); 
            })
                // 为表格1绑定事件
                Table.api.bindevent(orderAcar);
               
                // alert(Table.api.getrowdata(table, index));
            },
            order_rental: function () {

                // 表格2
                var orderRental = $("#orderRental"); 
                 
                $(".btn-add").data("area", ["95%","95%"]); 
                $(".btn-edit").data("area", ["95%","95%"]); 
                
                 // 初始化表格
                 orderRental.bootstrapTable({
                url: 'salesmanagement/Orderlisttabs/orderRental',
                extend: {
                    // index_url: 'order/salesorder/index',
                    add_url: 'order/rentalorder/add',
                    edit_url: 'order/rentalorder/edit',
                    del_url: 'order/rentalorder/del',
                    multi_url: 'order/rentalorder/multi',
                    table: 'rental_order',
                },
                toolbar: '#toolbar2',
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        // {field: 'plan_car_rental_name', title: __('Plan_car_rental_name')},
                        // {field: 'sales_id', title: __('Sales_id')},
                        // {field: 'admin_id', title: __('Admin_id')},
                        // {field: 'control_id', title: __('Control_id')},
                        // {field: 'rental_car_id', title: __('Rental_car_id')},
                        // {field: 'insurance_id', title: __('Insurance_id')},
                        // {field: 'general_manager_id', title: __('General_manager_id')},
                        {field: 'order_no', title: __('Order_no')}, 
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'username', title: __('Username')},
                        {field: 'phone', title: __('Phone')},
                        {field: 'id_card', title: __('Id_card')},
                        {field: 'genderdata', title: __('Genderdata'), searchList: {"male":__('Genderdata male'),"female":__('Genderdata female')}, formatter: Table.api.formatter.normal},
                        {field: 'cash_pledge', title: __('Cash_pledge')},
                        {field: 'rental_price', title: __('Rental_price')},
                        {field: 'tenancy_term', title: __('Tenancy_term')},
                        {field: 'id', title: __('查看详细资料'), table: orderRental, buttons: [
                            {name: 'rentalDetails', text: '查看详细资料', title: '查看订单详细资料' ,icon: 'fa fa-eye',classname: 'btn btn-xs btn-primary btn-dialog btn-rentalDetails', 
                                url: 'salesmanagement/Orderlisttabs/rentaldetails', callback:function(data){
                                    console.log(data)
                                }
                            } 
                            ],
                            
                            operate:false, formatter: Table.api.formatter.buttons
                        },
                        {field: 'gps_installation_name', title: __('Gps_installation_name')},
                        {field: 'gps_installation_datetime', title: __('Gps_installation_datetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'information_audition_name', title: __('Information_audition_name')},
                        {field: 'information_audition_datetime', title: __('Information_audition_datetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'Insurance_status_name', title: __('Insurance_status_name')},
                        {field: 'Insurance_status_datetime', title: __('Insurance_status_datetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'general_manager_name', title: __('General_manager_name')},
                        {field: 'general_manager_datetime', title: __('General_manager_datetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        // {field: 'emergency_contact_1', title: __('Emergency_contact_1')},
                        // {field: 'emergency_contact_2', title: __('Emergency_contact_2')},
                        // {field: 'id_cardimages', title: __('Id_cardimages'), formatter: Table.api.formatter.images},
                        // {field: 'drivers_licenseimages', title: __('Drivers_licenseimages'), formatter: Table.api.formatter.images},
                        // {field: 'residence_bookletimages', title: __('Residence_bookletimages'), formatter: Table.api.formatter.images},
                        // {field: 'call_listfilesimages', title: __('Call_listfilesimages'), formatter: Table.api.formatter.images},
                        // {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        // {field: 'delivery_datetime', title: __('Delivery_datetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: orderRental, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
                
            });
         

            orderRental.on('load-success.bs.table',function(e,data){
                // console.log(data);
                $('#badge_order_rental').text(data.total); 
                $(".btn-rentalDetails").data("area", ["95%","95%"]); 
            })
                // 为表格1绑定事件
                Table.api.bindevent(orderRental);
               
                // alert(Table.api.getrowdata(table, index));
            },
        },
        add: function () {
            
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            },
            events:{
                operate: {
                    //提交审核
                    'click .btn-submit_audit': function (e, value, row, index) {

                        e.stopPropagation();
                        e.preventDefault();
                        var that = this;
                        var top = $(that).offset().top - $(window).scrollTop();
                        var left = $(that).offset().left - $(window).scrollLeft() - 260;
                        if (top + 154 > $(window).height()) {
                            top = top - 154;
                        }
                        if ($(window).width() < 480) {
                            top = left = undefined;
                        }
                        Layer.confirm(
                            __('请确认资料完整，是否开始提交审核?'),
                            {icon: 3, title: __('Warning'), offset: [top, left], shadeClose: true},

                            function (index) {
                                var table = $(that).closest('table');
                                var options = table.bootstrapTable('getOptions');


                                Fast.api.ajax({
                                    url: 'salesmanagement/orderlisttabs/sedAudit',
                                    data: {id: row[options.pk]}
                                }, function (data, ret) {

                                    Toastr.success(ret.msg);
                                    Layer.close(index);
                                    table.bootstrapTable('refresh');
                                    return false;
                                }, function (data, ret) {
                                    //失败的回调
                                    Toastr.success(ret.msg);

                                    return false;
                                });


                            }
                        );

                    },
                    'click .btn-editone': function (e, value, row, index) {
                    $(".btn-editone").data("area", ["95%","95%"]); 

                        e.stopPropagation();
                        e.preventDefault();
                        var table = $(this).closest('table');
                        var options = table.bootstrapTable('getOptions');
                        var ids = row[options.pk];
                        row = $.extend({}, row ? row : {}, {ids: ids});
                        var url = options.extend.edit_url;
                        Fast.api.open(Table.api.replaceurl(url, row, table), __('Edit'), $(this).data() || {});
                    },
                    'click .btn-delone': function (e, value, row, index) {  /**编辑按钮 */

                        e.stopPropagation();
                        e.preventDefault();
                        var that = this;
                        var top = $(that).offset().top - $(window).scrollTop();
                        var left = $(that).offset().left - $(window).scrollLeft() - 260;
                        if (top + 154 > $(window).height()) {
                            top = top - 154;
                        }
                        if ($(window).width() < 480) {
                            top = left = undefined;
                        }
                        Layer.confirm(
                            __('Are you sure you want to delete this item?'),
                            {icon: 3, title: __('Warning'), offset: [top, left], shadeClose: true},
                            function (index) {
                                var table = $(that).closest('table');
                                var options = table.bootstrapTable('getOptions');
                                Table.api.multi("del", row[options.pk], table, that);
                                Layer.close(index);
                            }
                        );
                    },
                }
               
            },
            formatter: {
                operate: function (value, row, index) {

                    var table = this.table;
                    // 操作配置
                    var options = table ? table.bootstrapTable('getOptions') : {};
                    // 默认按钮组
                    var buttons = $.extend([], this.buttons || []);

                    return Table.api.buttonlink(this, buttons, value, row, index, 'operate');
                }
                // status: function (value, row, index) {

                //     var colorArr = {relation: 'info', intention: 'success', nointention: 'danger'};
                //     //如果字段列有定义custom
                //     if (typeof this.custom !== 'undefined') {
                //         colorArr = $.extend(colorArr, this.custom);
                //     }
                //     value = value === null ? '' : value.toString();

                //     var color = value && typeof colorArr[value] !== 'undefined' ? colorArr[value] : 'primary';

                //     var newValue = value.charAt(0).toUpperCase() + value.slice(1);
                //     //渲染状态
                //     var html = '<span class="text-' + color + '"><i class="fa fa-circle"></i> ' + __(newValue) + '</span>';
                //     // if (this.operate != false) {
                //     //     html = '<a href="javascript:;" class="searchit" data-toggle="tooltip" title="' + __('Click to search %s', __(newValue)) + '" data-field="' + this.field + '" data-value="' + value + '">' + html + '</a>';
                //     // }
                //     return html;
                // },
            }
        }
    };
    return Controller;
});