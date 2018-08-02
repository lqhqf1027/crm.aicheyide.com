define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
          
            // 初始化表格参数配置
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

        table: {
         
            to_audit: function () {
                // 新客户
                var toAudit = $("#toAudit"); 
               
                toAudit.on('post-body.bs.table', function (e, settings, json, xhr) {
                    $(".btn-auditResult").data("area", ["90%", "90%"]);
                });
                 // 初始化表格
                 toAudit.bootstrapTable({
                url: 'riskcontrol/creditreview/toAudit',
                extend: {
                    // index_url: 'customer/customerresource/index',
                    // add_url: 'customer/customerresource/add',
                    // edit_url: 'customer/customerresource/edit',
                    // del_url: 'customer/customerresource/del',
                    // multi_url: 'customer/customerresource/multi',
                    // distribution_url: 'promote/customertabs/distribution',
                    // import_url: 'customer/customerresource/import',
                    table: 'sales_order',
                },
                toolbar: '#toolbar1',
                pk: 'id',
                sortName: 'id',
                searchFormVisible: true,
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
                        {field: 'operate', title: __('Operate'), table: toAudit, 
                            events: Table.api.events.operate, 
                            buttons: [
                                {
                                    name:'detail',
                                    text:'审核',
                                    title:'审核',
                                    icon: 'fa fa-share',
                                    classname: 'btn btn-xs btn-info btn-dialog btn-auditResult',
                                    url: 'riskcontrol/creditreview/auditResult'
                                }

                            ],
                            formatter: Table.api.formatter.operate
                        }
                    ]
                ]
            });
          
                // 为表格newCustomer绑定事件
                Table.api.bindevent(toAudit);

                //数据实时统计
                toAudit.on('load-success.bs.table',function(e,data){ 
                
                    var toAudit =  $('#badge_new_toAudit').text(data.total); 
                    toAudit = parseInt($('#badge_new_toAudit').text());
                    
                    // var newAllocationNum = parseInt($('#badge_new_allocation').text());
                    // num = parseInt(num);
                    // $('#badge_new_allocation').text(num+newAllocationNum); 
                   
                })

            },
            pass_audit: function () {
                // 已分配的客户
                var passAudit = $("#passAudit");
                passAudit.bootstrapTable({
                    url: 'riskcontrol/creditreview/passAudit',
                    // extend: {
                    //     index_url: 'plan/planusedcar/index',
                    //     add_url: 'plan/planusedcar/add',
                    //     edit_url: 'plan/planusedcar/edit',
                    //     del_url: 'plan/planusedcar/del',
                    //     multi_url: 'plan/planusedcar/multi',
                    //     table: 'plan_used_car',
                    // },
                    toolbar: '#toolbar2',
                    pk: 'id',
                    sortName: 'id',
                    searchFormVisible: true,
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
                            
                        ]
                    ]

                });

                // 为已分配的客户表格绑定事件
                Table.api.bindevent(passAudit);

                //数据实时统计
                passAudit.on('load-success.bs.table',function(e,data){ 
                
                    var passAudit =  $('#badge_new_passAudit').text(data.total); 
                    // var newAllocationNum = parseInt($('#badge_new_allocation').text());
                    // num = parseInt(num);
                    // $('#badge_new_allocation').text(num+newAllocationNum); 
                   
                })

            },
            no_approval: function () {
                // 已反馈的客户
                var noApproval = $("#noApproval");
                noApproval.bootstrapTable({
                    url: 'riskcontrol/creditreview/noApproval',
                    // extend: {
                    //     index_url: 'plan/planfull/index',
                    //     add_url: 'plan/planfull/add',
                    //     edit_url: 'plan/planfull/edit',
                    //     del_url: 'plan/planfull/del',
                    //     multi_url: 'plan/planfull/multi',
                    //     table: 'plan_full',
                    // },
                    toolbar: '#toolbar3',
                    pk: 'id',
                    sortName: 'id',
                    searchFormVisible: true,
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
                            
                        ]
                    ]
                });
                
                // 为已反馈的客户表格绑定事件
                Table.api.bindevent(noApproval);

                //数据实时统计
                noApproval.on('load-success.bs.table',function(e,data){ 
                
                    var noApproval =  $('#badge_new_noApproval').text(data.total); 
                    // var newFeedback = parseInt($('#badge_new_feedback').text());
                    // num = parseInt(num);
                    // $('#badge_new_allocation').text(num+newFeedback); 
                   
                })

            }
        },
        add: function () { 
            Controller.api.bindevent();
           
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                $(document).on('click', "input[name='row[ismenu]']", function () {
                    var name = $("input[name='row[name]']");
                    name.prop("placeholder", $(this).val() == 1 ? name.data("placeholder-menu") : name.data("placeholder-node"));
                });
                $("input[name='row[ismenu]']:checked").trigger("click");
                Form.api.bindevent($("form[role=form]"));
            }
        }
         
    };
    return Controller;
});