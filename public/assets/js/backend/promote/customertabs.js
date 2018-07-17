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
        dstribution:function(){
            // $(".btn-add").data("area", ["300px","200px"]);
            Table.api.init({
               
            });
            Form.api.bindevent($("form[role=form]"), function(data, ret){
                //这里是表单提交处理成功后的回调函数，接收来自php的返回数据
                Fast.api.close(data);//这里是重点
                // console.log(data);
                // Toastr.success("成功");//这个可有可无
            }, function(data, ret){
                // console.log(data);
                
                Toastr.success("失败");
                
            });
            // Controller.api.bindevent();
            // console.log(Config.id);
            
        },
        table: {
         
            new_customer: function () {
                // 表格1
                var newCustomer = $("#newCustomer"); 
                newCustomer.on('post-body.bs.table', function (e, settings, json, xhr) {
                    $(".btn-newCustomer").data("area", ["30%", "30%"]);
                });
                 // 初始化表格
                 newCustomer.bootstrapTable({
                url: 'promote/Customertabs/newCustomer',
                extend: {
                    index_url: 'customer/customerresource/index',
                    add_url: 'customer/customerresource/add',
                    edit_url: 'customer/customerresource/edit',
                    del_url: 'customer/customerresource/del',
                    multi_url: 'customer/customerresource/multi',
                    table: 'customer_resource',
                },
                toolbar: '#toolbar1',
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        // {field: 'platform_id', title: __('Platform_id')},
                        // {field: 'backoffice_id', title: __('Backoffice_id')},
                        {field: 'platform.name', title: __('所属平台')},
                        
                        // {field: 'sales_id', title: __('Sales_id')},
                        {field: 'username', title: __('Username')},
                        {field: 'phone', title: __('Phone')},
                        {field: 'age', title: __('Age')},
                        {field: 'genderdata', title: __('Genderdata'), visible:false, searchList: {"male":__('genderdata male'),"female":__('genderdata female')}},
                        {field: 'genderdata_text', title: __('Genderdata'), operate:false},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        // {field: 'feedback', title: __('Feedback')},
                        // {field: 'note', title: __('Note')},
                        {field: 'operate', title: __('Operate'), table: newCustomer, events: Table.api.events.operate,
                        buttons: [
                                    {name: 'detail', text: '分配', title: '分配', icon: 'fa fa-share', classname: 'btn btn-xs btn-info btn-dialog btn-newCustomer', url: 'promote/customertabs/dstribution',
                                        success:function(data, ret){
                                        }, 
                                        error:function(data,ret){

                                        }
                                    }
                                ],
                                
                                 events: Table.api.events.operate,formatter: Table.api.formatter.operate}
                    ]
                ]
            });
                // 为表格1绑定事件
                Table.api.bindevent(newCustomer);
            },
            second: function () {
                // 表格2
                var table2 = $("#table2");
                table2.bootstrapTable({
                    url: 'planmanagement/plantabs/table2',
                    extend: {
                        index_url: 'plan/planusedcar/index',
                        add_url: 'plan/planusedcar/add',
                        edit_url: 'plan/planusedcar/edit',
                        del_url: 'plan/planusedcar/del',
                        multi_url: 'plan/planusedcar/multi',
                        table: 'plan_used_car',
                    },
                    toolbar: '#toolbar2',
                    sortName: 'id,statusdata',
                    
                    columns: [
                        [
                            {checkbox: true},
                            {field: 'id', title: __('Id')},
                            {field: 'models.name', title: '销售车型'},
                            {field: 'financialplatform.name', title: '所属金融平台'},
                            
                            {field: 'the_door', title: __('The_door')},
                            {field: 'new_payment', title: __('New_payment'), operate:'BETWEEN'},
                            {field: 'new_monthly', title: __('New_monthly'), operate:'BETWEEN'},
                            {field: 'nperlist', title: __('Nperlist'), visible:false, searchList: {"12":__('Nperlist 12'),"24":__('Nperlist 24'),"36":__('Nperlist 36'),"48":__('Nperlist 48'),"60":__('Nperlist 60')}},
                            {field: 'nperlist_text', title: __('Nperlist'), operate:false},
                            {field: 'new_total_price', title: __('New_total_price'), operate:'BETWEEN'},
                            {field: 'mileage', title: __('Mileage')},
                            {field: 'contrarytodata', title: __('Contrarytodata'),   searchList: {"1":__('Contrarytodata 1'),"2":__('Contrarytodata 2')},formatter:function(value, row, index){

                               
                              return value==1?'对公':'非对公';
                            }},
                             
                            {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                            {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                            {field: 'statusdata', title: __('Statusdata'),  formatter: Table.api.formatter.statusdata,sortable: true},
                       
                            {field: 'operate', title: __('Operate'), table: table2, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                        ]
                    ]

 
                });

                // 为表格2绑定事件
                Table.api.bindevent(table2);
            },
            planfull: function () {
                // 表格3
                var table3 = $("#table3");
                table3.bootstrapTable({
                    url: 'planmanagement/plantabs/table3',
                    extend: {
                        index_url: 'plan/planfull/index',
                        add_url: 'plan/planfull/add',
                        edit_url: 'plan/planfull/edit',
                        del_url: 'plan/planfull/del',
                        multi_url: 'plan/planfull/multi',
                        table: 'plan_full',
                    },
                    toolbar: '#toolbar3',
                    pk: 'id',
                    sortName: 'id',
                    columns: [
                        [
                            {checkbox: true},
                            {field: 'id', title: __('Id')},
                            {field: 'models.name', title: '销售车型'},
                            
                            {field: 'full_total_price', title: __('Full_total_price'), operate:'BETWEEN'},
                        
                            {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                            {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                            {field: 'ismenu', title: __('Ismenu'), formatter: Table.api.formatter.toggle},
                            
                            {field: 'operate', title: __('Operate'), table: table3, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                        ]
                    ]
                });
                // 为表格3绑定事件
                Table.api.bindevent(table3);
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