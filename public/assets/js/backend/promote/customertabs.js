define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {


    var num = 0;

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

        //单个分配
        // dstribution:function(){
 
        //     // $(".btn-add").data("area", ["300px","200px"]);
        //     Table.api.init({
               
        //     });
        //     Form.api.bindevent($("form[role=form]"), function(data, ret){
        //         //这里是表单提交处理成功后的回调函数，接收来自php的返回数据
                
        //         Fast.api.close(data);//这里是重点
        //         // console.log(data);
        //         // Toastr.success("成功");//这个可有可无
        //     }, function(data, ret){
        //         // console.log(data); 
        //         Toastr.success("失败"); 
        //     });
        //     // Controller.api.bindevent();
        //     // console.log(Config.id);
 
        // },
        //批量分配 
        distribution:function(){
            
          
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
        //批量导入
        import:function(){
            // console.log(123);
            // return;
            Form.api.bindevent($("form[role=form]"), function(data, ret){
                //这里是表单提交处理成功后的回调函数，接收来自php的返回数据
                Fast.api.close(data);//这里是重点
                console.log(data);
                // Toastr.success("成功");//这个可有可无
            }, function(data, ret){
                // console.log(data);
                
                Toastr.success("失败");
                
            });
            Controller.api.bindevent();
            // console.log(Config.id); 
 
        },
        table: {
         
            new_customer: function () {
                // 新客户
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
                    // edit_url: 'customer/customerresource/edit',
                    del_url: 'customer/customerresource/del',
                    // multi_url: 'customer/customerresource/multi',
                    distribution_url: 'promote/customertabs/distribution',
                    import_url: 'customer/customerresource/import',
                    table: 'customer_resource',
                },
                toolbar: '#toolbar1',
                pk: 'id',
                sortName: 'id',
                searchFormVisible: true,
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
                        // {field: 'allocationtime', title: __('Allocationtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        // {field: 'feedbacktime', title: __('Feedbacktime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        // {field: 'feedback', title: __('Feedback')},
                        // {field: 'note', title: __('Note')},
                        {field: 'operate', title: __('Operate'), table: newCustomer, events: Table.api.events.operate,
                        buttons: [
                                    {name: 'detail', text: '分配', title: '分配', icon: 'fa fa-share', classname: 'btn btn-xs btn-info btn-newCustomer btn-selected'
                                    }
                                ],
                                
                                 events: Table.api.events.operate,formatter: Table.api.formatter.operate}
                    ]
                ]
            });
            // var num = 0;
          
                // 为表格newCustomer绑定事件
                Table.api.bindevent(newCustomer);
                // 批量分配 
                $(document).on("click", ".btn-selected", function () {   
                    var ids = Table.api.selectedids(newCustomer);
                    num= parseInt(ids.length);
                    //    console.log(num);
                    var url = 'promote/customertabs/distribution?ids='+ids;
                    var options = {
                        shadeClose: false,
                        shade: [0.3, '#393D49'],
                        area:['30%','30%'],
                        callback:function(value){
                                                        
                        }
                    }
                    Fast.api.open(url,'批量分配',options)
                })

                //数据实时统计
                newCustomer.on('load-success.bs.table',function(e,data){ 
                
                    var newCustomerNum =  $('#badge_new_customer').text(data.total); 
                        newCustomerNum = parseInt($('#badge_new_customer').text());
                    var newAllocationNum = parseInt($('#badge_new_allocation').text());
                    num = parseInt(num);
                    $('#badge_new_allocation').text(num+newAllocationNum); 
                   
                })

                //导出新客户的信息
                var submitForm = function (ids, layero) {
                    var options = newCustomer.bootstrapTable('getOptions');
                    console.log(options);
                    var columns = [];
                    $.each(options.columns[0], function (i, j) {
                        if (j.field && !j.checkbox && j.visible && j.field != 'operate') {
                            columns.push(j.field);
                        }
                    });
                    var search = options.queryParams({});
                    $("input[name=search]", layero).val(options.searchText);
                    $("input[name=ids]", layero).val(ids);
                    $("input[name=filter]", layero).val(search.filter);
                    $("input[name=op]", layero).val(search.op);
                    $("input[name=columns]", layero).val(columns.join(','));
                    $("form", layero).submit();
                };

                $(document).on("click", ".btn-export", function () {
                    var ids = Table.api.selectedids(newCustomer);
                    var page = newCustomer.bootstrapTable('getData');
                    var all = newCustomer.bootstrapTable('getOptions').totalRows;
                    console.log(ids, page, all);
                    Layer.confirm("请选择导出的选项<form action='" + Fast.api.fixurl("promote/customertabs/export") + "' method='post' target='_blank'><input type='hidden' name='ids' value='' /><input type='hidden' name='filter' ><input type='hidden' name='op'><input type='hidden' name='search'><input type='hidden' name='columns'></form>", {
                        title: '导出数据',
                        btn: ["选中项(" + ids.length + "条)", "本页(" + page.length + "条)", "全部(" + all + "条)"],
                        success: function (layero, index) {
                            $(".layui-layer-btn a", layero).addClass("layui-layer-btn0");
                        }
                        , 
                        yes: function (index, layero) {
                            submitForm(ids.join(","), layero);
                            // return false;
                        }
                        ,
                        btn2: function (index, layero) {
                            var ids = [];
                            $.each(page, function (i, j) {
                                ids.push(j.id);
                            });
                            submitForm(ids.join(","), layero);
                            // return false;
                        }
                        ,
                        btn3: function (index, layero) {
                            submitForm("all", layero);
                            // return false;
                        }
                    })
                });


            },
            new_allocation: function () {
                // 已分配的客户
                var newAllocation = $("#newAllocation");
                newAllocation.bootstrapTable({
                    url: 'promote/Customertabs/newAllocation',
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
                            // {field: 'platform_id', title: __('Platform_id')},
                            // {field: 'backoffice_id', title: __('Backoffice_id')},
                            {field: 'platform.name', title: __('所属平台')},
                            
                            // {field: 'sales_id', title: __('Sales_id')},
                            {field: 'username', title: __('Username')},
                            {field: 'phone', title: __('Phone')},
                            {field: 'age', title: __('Age')},
                            {field: 'genderdata', title: __('Genderdata'), visible:false, searchList: {"male":__('genderdata male'),"female":__('genderdata female')}},
                            {field: 'genderdata_text', title: __('Genderdata'), operate:false},
                            {field: 'distributinternaltime', title: __('Distributinternaltime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                            {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                            {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                            // {field: 'feedback', title: __('Feedback')},
                            // {field: 'note', title: __('Note')},
                            // {field: 'operate', title: __('Operate'), table: newAllocation, events: Table.api.events.operate,formatter: Table.api.formatter.operate}
                        ]
                    ]

                });

                // 为已分配的客户表格绑定事件
                Table.api.bindevent(newAllocation);

                //导出分配客户的信息
                var submitForm = function (ids, layero) {
                    var options = newAllocation.bootstrapTable('getOptions');
                    console.log(options);
                    var columns = [];
                    $.each(options.columns[0], function (i, j) {
                        if (j.field && !j.checkbox && j.visible && j.field != 'operate') {
                            columns.push(j.field);
                        }
                    });
                    var search = options.queryParams({});
                    $("input[name=search]", layero).val(options.searchText);
                    $("input[name=ids]", layero).val(ids);
                    $("input[name=filter]", layero).val(search.filter);
                    $("input[name=op]", layero).val(search.op);
                    $("input[name=columns]", layero).val(columns.join(','));
                    $("form", layero).submit();
                };

                $(document).on("click", ".btn-allocationexport", function () {
                    var ids = Table.api.selectedids(newAllocation);
                    var page = newAllocation.bootstrapTable('getData');
                    var all = newAllocation.bootstrapTable('getOptions').totalRows;
                    console.log(ids, page, all);
                    Layer.confirm("请选择导出的选项<form action='" + Fast.api.fixurl("promote/customertabs/allocationexport") + "' method='post' target='_blank'><input type='hidden' name='ids' value='' /><input type='hidden' name='filter' ><input type='hidden' name='op'><input type='hidden' name='search'><input type='hidden' name='columns'></form>", {
                        title: '导出数据',
                        btn: ["选中项(" + ids.length + "条)", "本页(" + page.length + "条)", "全部(" + all + "条)"],
                        success: function (layero, index) {
                            $(".layui-layer-btn a", layero).addClass("layui-layer-btn0");
                        }
                        , yes: function (index, layero) {
                            submitForm(ids.join(","), layero);
                            // return false;
                        }
                        ,
                        btn2: function (index, layero) {
                            var ids = [];
                            $.each(page, function (i, j) {
                                ids.push(j.id);
                            });
                            submitForm(ids.join(","), layero);
                            // return false;
                        }
                        ,
                        btn3: function (index, layero) {
                            submitForm("all", layero);
                            // return false;
                        }
                    })
                });

            },
            new_feedback: function () {
                // 已反馈的客户
                var newFeedback = $("#newFeedback");
                newFeedback.bootstrapTable({
                    url: 'promote/Customertabs/newFeedback',
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
                            // {field: 'platform_id', title: __('Platform_id')},
                            // {field: 'backoffice_id', title: __('Backoffice_id')},
                            {field: 'platform.name', title: __('所属平台')},
                            
                            // {field: 'sales_id', title: __('Sales_id')},
                            {field: 'username', title: __('Username')},
                            {field: 'phone', title: __('Phone')},
                            {field: 'age', title: __('Age')},
                            {field: 'genderdata', title: __('Genderdata'), visible:false, searchList: {"male":__('genderdata male'),"female":__('genderdata female')}},
                            {field: 'genderdata_text', title: __('Genderdata'), operate:false},
                            {field: 'feedbacktime', title: __('Feedbacktime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                            {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                            {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                            {field: 'feedback', title: __('Feedback')},
                            // {field: 'feedback', title: __('Feedback')},
                            // {field: 'note', title: __('Note')},
                            // {field: 'operate', title: __('Operate'), table: newFeedback, events: Table.api.events.operate,formatter: Table.api.formatter.operate}
                        ]
                    ]
                });
                
                // 为已反馈的客户表格绑定事件
                Table.api.bindevent(newFeedback);

                //导出反馈客户的信息
                var submitForm = function (ids, layero) {
                    var options = newFeedback.bootstrapTable('getOptions');
                    console.log(options);
                    var columns = [];
                    $.each(options.columns[0], function (i, j) {
                        if (j.field && !j.checkbox && j.visible && j.field != 'operate') {
                            columns.push(j.field);
                        }
                    });
                    var search = options.queryParams({});
                    $("input[name=search]", layero).val(options.searchText);
                    $("input[name=ids]", layero).val(ids);
                    $("input[name=filter]", layero).val(search.filter);
                    $("input[name=op]", layero).val(search.op);
                    $("input[name=columns]", layero).val(columns.join(','));
                    $("form", layero).submit();
                };

                $(document).on("click", ".btn-feedbackexport", function () {
                    var ids = Table.api.selectedids(newFeedback);
                    var page = newFeedback.bootstrapTable('getData');
                    var all = newFeedback.bootstrapTable('getOptions').totalRows;
                    console.log(ids, page, all);
                    Layer.confirm("请选择导出的选项<form action='" + Fast.api.fixurl("promote/customertabs/feedbackexport") + "' method='post' target='_blank'><input type='hidden' name='ids' value='' /><input type='hidden' name='filter' ><input type='hidden' name='op'><input type='hidden' name='search'><input type='hidden' name='columns'></form>", {
                        title: '导出数据',
                        btn: ["选中项(" + ids.length + "条)", "本页(" + page.length + "条)", "全部(" + all + "条)"],
                        success: function (layero, index) {
                            $(".layui-layer-btn a", layero).addClass("layui-layer-btn0");
                        }
                        , yes: function (index, layero) {
                            submitForm(ids.join(","), layero);
                            // return false;
                        }
                        ,
                        btn2: function (index, layero) {
                            var ids = [];
                            $.each(page, function (i, j) {
                                ids.push(j.id);
                            });
                            submitForm(ids.join(","), layero);
                            // return false;
                        }
                        ,
                        btn3: function (index, layero) {
                            submitForm("all", layero);
                            // return false;
                        }
                    })
                });
            }
        },
        import: function () { 
            Controller.api.bindevent();
               
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