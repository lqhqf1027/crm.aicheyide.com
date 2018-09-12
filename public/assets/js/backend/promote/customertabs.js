define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {

            // 初始化表格参数配置
            Table.api.init({});

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


        //批量分配 
        distribution: function () {


            // $(".btn-add").data("area", ["300px","200px"]);
            Table.api.init({});
            Form.api.bindevent($("form[role=form]"), function (data, ret) {
                //这里是表单提交处理成功后的回调函数，接收来自php的返回数据

                // console.log(data);
                // newAllocationNum = parseInt($('#badge_new_allocation').text());
                // num = parseInt(data);
                // $('#badge_new_allocation').text(num+newAllocationNum); 
                Fast.api.close(data);//这里是重点

                // Toastr.success("成功");//这个可有可无
            }, function (data, ret) {
                // console.log(data);

                Toastr.success("失败");

            });
            // Controller.api.bindevent();
            // console.log(Config.id);


        },
        //批量导入
        import: function () {
            // console.log(123);
            // return;
            Form.api.bindevent($("form[role=form]"), function (data, ret) {
                //这里是表单提交处理成功后的回调函数，接收来自php的返回数据
                Fast.api.close(data);//这里是重点
                console.log(data);
                // Toastr.success("成功");//这个可有可无
            }, function (data, ret) {
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
                        del_url: 'promote/customertabs/del',
                        multi_url: 'promote/customertabs/multi',
                        distribution_url: 'promote/customertabs/distribution',
                        import_url: 'promote/customertabs/import',
                        table: 'customer_resource',
                    },
                    toolbar: '#toolbar1',
                    pk: 'id',
                    sortName: 'id',
                    searchFormVisible: true,
                    columns: [
                        [
                            {checkbox: true},
                            {field: 'id', title: __('Id'), operate: false},
                            // {field: 'platform_id', title: __('Platform_id')},
                            // {field: 'backoffice_id', title: __('Backoffice_id')},
                            {field: 'platform.name', title: __('所属平台')},

                            // {field: 'sales_id', title: __('Sales_id')},
                            {field: 'username', title: __('Username')},
                            {field: 'phone', title: __('Phone')},
                            {field: 'age', title: __('Age')},
                            {
                                field: 'genderdata',
                                title: __('Genderdata'),
                                visible: false,
                                searchList: {"male": __('genderdata male'), "female": __('genderdata female')}
                            },
                            {field: 'genderdata_text', title: __('Genderdata'), operate: false},
                            {
                                field: 'createtime',
                                title: __('Createtime'),
                                operate: false,
                                addclass: 'datetimerange',
                                formatter: Table.api.formatter.datetime
                            },
                            {
                                field: 'updatetime',
                                title: __('Updatetime'),
                                operate: false,
                                addclass: 'datetimerange',
                                formatter: Table.api.formatter.datetime,

                            },

                            {
                                field: 'operate',
                                title: __('Operate'),
                                table: newCustomer,

                                buttons: [
                                    {
                                        name: 'detail',
                                        text: '分配',
                                        title: '分配',
                                        icon: 'fa fa-share',
                                        classname: 'btn btn-xs btn-info btn-newCustomer',
                                    }
                                ],
                                events: Controller.api.events.operate,
                                formatter: Table.api.formatter.operate
                            }
                        ]
                    ]
                });

                // 为表格newCustomer绑定事件
                Table.api.bindevent(newCustomer);
                // 批量分配 
                var num = 0;
                $(document).on("click", ".btn-selected", function () {
                    var ids = Table.api.selectedids(newCustomer);
                    num = parseInt(ids.length);
                    //    console.log(num);
                    var url = 'promote/customertabs/distribution?ids=' + ids;
                    var options = {
                        shadeClose: false,
                        shade: [0.3, '#393D49'],
                        area: ['30%', '30%'],
                        callback: function (value) {

                        }
                    }
                    Fast.api.open(url, '批量分配', options)
                })

                //数据实时统计
                newCustomer.on('load-success.bs.table', function (e, data) {

                    var newCustomerNum = $('#badge_new_customer').text(data.total);
                    newCustomerNum = parseInt($('#badge_new_customer').text());

                    // var newAllocationNum = parseInt($('#badge_new_allocation').text());
                    // num = parseInt(num);
                    // $('#badge_new_allocation').text(num+newAllocationNum); 

                })
                // f(".btn-export",newCustomer);
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
                            {field: 'id', title: __('Id'), operate: false},
                            // {field: 'platform_id', title: __('Platform_id')},
                            // {field: 'backoffice_id', title: __('Backoffice_id')},
                            {field: 'platform.name', title: __('所属平台')},

                            // {field: 'sales_id', title: __('Sales_id')},
                            {field: 'username', title: __('Username')},
                            {field: 'phone', title: __('Phone')},
                            {field: 'age', title: __('Age')},
                            {
                                field: 'genderdata',
                                title: __('Genderdata'),
                                visible: false,
                                searchList: {"male": __('genderdata male'), "female": __('genderdata female')}
                            },
                            {field: 'genderdata_text', title: __('Genderdata'), operate: false},
                            {
                                field: 'distributinternaltime',
                                title: __('Distributinternaltime'),
                                operate: false,
                                addclass: 'datetimerange',
                                formatter: Table.api.formatter.datetime
                            },
                            {
                                field: 'createtime',
                                title: __('Createtime'),
                                operate: false,
                                addclass: 'datetimerange',
                                formatter: Table.api.formatter.datetime
                            },
                            {
                                field: 'updatetime',
                                title: __('Updatetime'),
                                operate: false,
                                addclass: 'datetimerange',
                                formatter: Table.api.formatter.datetime,

                            },
                            // {field: 'feedback', title: __('Feedback')},
                            // {field: 'note', title: __('Note')},
                            // {field: 'operate', title: __('Operate'), table: newAllocation, events: Table.api.events.operate,formatter: Table.api.formatter.operate}
                        ]
                    ]

                });

                // 为已分配的客户表格绑定事件
                Table.api.bindevent(newAllocation);

                //数据实时统计
                newAllocation.on('load-success.bs.table', function (e, data) {

                    var newAllocationNum = $('#badge_new_allocation').text(data.total);
                    // var newAllocationNum = parseInt($('#badge_new_allocation').text());
                    // num = parseInt(num);
                    // $('#badge_new_allocation').text(num+newAllocationNum); 

                })

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
                            {field: 'id', title: __('Id'), operate: false},
                            // {field: 'platform_id', title: __('Platform_id')},
                            // {field: 'backoffice_id', title: __('Backoffice_id')},
                            {field: 'platform.name', title: __('所属平台')},

                            // {field: 'sales_id', title: __('Sales_id')},
                            {field: 'username', title: __('Username')},
                            {field: 'phone', title: __('Phone')},
                            {field: 'age', title: __('Age')},
                            {
                                field: 'genderdata',
                                title: __('Genderdata'),
                                visible: false,
                                searchList: {"male": __('genderdata male'), "female": __('genderdata female')}
                            },
                            {field: 'genderdata_text', title: __('Genderdata'), operate: false},
                            {
                                field: 'feedbacktime',
                                title: __('Feedbacktime'),
                                operate: false,
                                addclass: 'datetimerange',
                                formatter: Table.api.formatter.datetime
                            },
                            {
                                field: 'createtime',
                                title: __('Createtime'),
                                operate: false,
                                addclass: 'datetimerange',
                                formatter: Table.api.formatter.datetime
                            },
                            {
                                field: 'updatetime',
                                title: __('Updatetime'),
                                operate: false,
                                addclass: 'datetimerange',
                                formatter: Table.api.formatter.datetime
                            },
                            {field: 'feedback', title: __('Feedback'), operate: false},
                            // {field: 'feedback', title: __('Feedback')},
                            // {field: 'note', title: __('Note')},
                            // {field: 'operate', title: __('Operate'), table: newFeedback, events: Table.api.events.operate,formatter: Table.api.formatter.operate}
                        ]
                    ]
                });

                // 为已反馈的客户表格绑定事件
                Table.api.bindevent(newFeedback);

                //数据实时统计
                newFeedback.on('load-success.bs.table', function (e, data) {

                    var newFeedback = $('#badge_new_feedback').text(data.total);
                    // var newFeedback = parseInt($('#badge_new_feedback').text());
                    // num = parseInt(num);
                    // $('#badge_new_allocation').text(num+newFeedback); 

                })

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
            },
            //今日头条
            headline: function () {
                // 已分配的客户
                var headlines = $("#headlines");
                headlines.bootstrapTable({
                    url: 'promote/Customertabs/headline',
                    extend: {
                        index_url: 'customer/customerresource/index',
                        add_url: 'customer/customerresource/add',
                        // edit_url: 'customer/customerresource/edit',
                        del_url: 'promote/customertabs/del',
                        multi_url: 'promote/customertabs/multi',
                        distribution_url: 'promote/customertabs/distribution',
                        import_url: 'promote/customertabs/import',
                        table: 'customer_resource',
                    },
                    toolbar: '#toolbar4',
                    pk: 'id',
                    sortName: 'id',
                    searchFormVisible: true,
                    columns: [
                        [
                            {checkbox: true},
                            {field: 'id', title: __('Id'), operate: false},
                            // {field: 'platform_id', title: __('Platform_id')},
                            // {field: 'backoffice_id', title: __('Backoffice_id')},
                            {field: 'platform.name', title: __('所属平台')},

                            // {field: 'sales_id', title: __('Sales_id')},
                            {field: 'username', title: __('Username')},
                            {field: 'phone', title: __('Phone')},
                            {field: 'age', title: __('Age')},
                            {
                                field: 'genderdata',
                                title: __('Genderdata'),
                                visible: false,
                                searchList: {"male": __('genderdata male'), "female": __('genderdata female')}
                            },
                            {field: 'genderdata_text', title: __('Genderdata'), operate: false},
                            {
                                field: 'distributinternaltime',
                                title: __('Distributinternaltime'),
                                operate: false,
                                addclass: 'datetimerange',
                                formatter: Table.api.formatter.datetime
                            },
                            {
                                field: 'createtime',
                                title: __('Createtime'),
                                operate: false,
                                addclass: 'datetimerange',
                                formatter: Table.api.formatter.datetime
                            },
                            {
                                field: 'updatetime',
                                title: __('Updatetime'),
                                operate: false,
                                addclass: 'datetimerange',
                                formatter: Table.api.formatter.datetime,

                            },
                            {
                                field: 'operate',
                                title: __('Operate'),
                                table: headlines,

                                events: Controller.api.events.operate,
                                formatter: Controller.api.operate
                            }

                        ]
                    ]

                });

                // 为已分配的客户表格绑定事件
                Table.api.bindevent(headlines);

                //数据实时统计
                headlines.on('load-success.bs.table', function (e, data) {

                    var newAllocationNum = $('#badge_new_allocation').text(data.total);
                    // var newAllocationNum = parseInt($('#badge_new_allocation').text());
                    // num = parseInt(num);
                    // $('#badge_new_allocation').text(num+newAllocationNum);

                })

                f(headlines);

                add_data('.add-headline', headlines, 'promote/Customertabs/add_headline');

                batch_share('.btn-selected-headline', headlines);


            },
            //百度
            baidu: function () {
                // 已分配的客户
                var baidus = $("#baidus");
                baidus.bootstrapTable({
                    url: 'promote/Customertabs/baidu',
                    extend: {
                        index_url: 'customer/customerresource/index',
                        add_url: 'customer/customerresource/add',
                        // edit_url: 'customer/customerresource/edit',
                        del_url: 'promote/customertabs/del',
                        multi_url: 'promote/customertabs/multi',
                        distribution_url: 'promote/customertabs/distribution',
                        import_url: 'promote/customertabs/import',
                        table: 'customer_resource',
                    },
                    toolbar: '#toolbar5',
                    pk: 'id',
                    sortName: 'id',
                    searchFormVisible: true,
                    columns: [
                        [
                            {checkbox: true},
                            {field: 'id', title: __('Id'), operate: false},
                            // {field: 'platform_id', title: __('Platform_id')},
                            // {field: 'backoffice_id', title: __('Backoffice_id')},
                            {field: 'platform.name', title: __('所属平台')},

                            // {field: 'sales_id', title: __('Sales_id')},
                            {field: 'username', title: __('Username')},
                            {field: 'phone', title: __('Phone')},
                            {field: 'age', title: __('Age')},
                            {
                                field: 'genderdata',
                                title: __('Genderdata'),
                                visible: false,
                                searchList: {"male": __('genderdata male'), "female": __('genderdata female')}
                            },
                            {field: 'genderdata_text', title: __('Genderdata'), operate: false},
                            {
                                field: 'distributinternaltime',
                                title: __('Distributinternaltime'),
                                operate: false,
                                addclass: 'datetimerange',
                                formatter: Table.api.formatter.datetime
                            },
                            {
                                field: 'createtime',
                                title: __('Createtime'),
                                operate: false,
                                addclass: 'datetimerange',
                                formatter: Table.api.formatter.datetime
                            },
                            {
                                field: 'updatetime',
                                title: __('Updatetime'),
                                operate: false,
                                addclass: 'datetimerange',
                                formatter: Table.api.formatter.datetime,

                            },
                            {
                                field: 'operate',
                                title: __('Operate'),
                                table: baidus,


                                events: Controller.api.events.operate,
                                formatter: Controller.api.operate
                            }

                        ]
                    ]

                });

                // 为已分配的客户表格绑定事件
                Table.api.bindevent(baidus);

                //数据实时统计
                baidus.on('load-success.bs.table', function (e, data) {

                    var newAllocationNum = $('#badge_new_allocation').text(data.total);
                    // var newAllocationNum = parseInt($('#badge_new_allocation').text());
                    // num = parseInt(num);
                    // $('#badge_new_allocation').text(num+newAllocationNum);

                })

                f(baidus);

                add_data('.add-baidu', baidus, 'promote/Customertabs/add_baidu');

                batch_share('.btn-selected-baidu', baidus);


            },
            //58同城
            same_city: function () {
                // 已分配的客户
                var sameCity = $("#sameCity");
                sameCity.bootstrapTable({
                    url: 'promote/Customertabs/same_city',
                    extend: {
                        index_url: 'customer/customerresource/index',
                        add_url: 'customer/customerresource/add',
                        // edit_url: 'customer/customerresource/edit',
                        del_url: 'promote/customertabs/del',
                        multi_url: 'promote/customertabs/multi',
                        distribution_url: 'promote/customertabs/distribution',
                        import_url: 'promote/customertabs/import',
                        table: 'customer_resource',
                    },
                    toolbar: '#toolbar6',
                    pk: 'id',
                    sortName: 'id',
                    searchFormVisible: true,
                    columns: [
                        [
                            {checkbox: true},
                            {field: 'id', title: __('Id'), operate: false},
                            // {field: 'platform_id', title: __('Platform_id')},
                            // {field: 'backoffice_id', title: __('Backoffice_id')},
                            {field: 'platform.name', title: __('所属平台')},

                            // {field: 'sales_id', title: __('Sales_id')},
                            {field: 'username', title: __('Username')},
                            {field: 'phone', title: __('Phone')},
                            {field: 'age', title: __('Age')},
                            {
                                field: 'genderdata',
                                title: __('Genderdata'),
                                visible: false,
                                searchList: {"male": __('genderdata male'), "female": __('genderdata female')}
                            },
                            {field: 'genderdata_text', title: __('Genderdata'), operate: false},
                            {
                                field: 'distributinternaltime',
                                title: __('Distributinternaltime'),
                                operate: false,
                                addclass: 'datetimerange',
                                formatter: Table.api.formatter.datetime
                            },
                            {
                                field: 'createtime',
                                title: __('Createtime'),
                                operate: false,
                                addclass: 'datetimerange',
                                formatter: Table.api.formatter.datetime
                            },
                            {
                                field: 'updatetime',
                                title: __('Updatetime'),
                                operate: false,
                                addclass: 'datetimerange',
                                formatter: Table.api.formatter.datetime,

                            },
                            {
                                field: 'operate',
                                title: __('Operate'),
                                table: sameCity,

                                events: Controller.api.events.operate,
                                formatter: Controller.api.operate
                            }

                        ]
                    ]

                });

                // 为已分配的客户表格绑定事件
                Table.api.bindevent(sameCity);

                //数据实时统计
                sameCity.on('load-success.bs.table', function (e, data) {

                    var newAllocationNum = $('#badge_new_allocation').text(data.total);
                    // var newAllocationNum = parseInt($('#badge_new_allocation').text());
                    // num = parseInt(num);
                    // $('#badge_new_allocation').text(num+newAllocationNum);

                })

                f(sameCity);

                add_data('.add-same_city', sameCity, 'promote/Customertabs/add_same_city');

                batch_share('.btn-selected-baidu', sameCity);


            },
            //抖音
            //58同城
            music: function () {
                // 已分配的客户
                var musics = $("#musics");
                musics.bootstrapTable({
                    url: 'promote/Customertabs/music',
                    extend: {
                        index_url: 'customer/customerresource/index',
                        add_url: 'customer/customerresource/add',
                        // edit_url: 'customer/customerresource/edit',
                        del_url: 'promote/customertabs/del',
                        multi_url: 'promote/customertabs/multi',
                        distribution_url: 'promote/customertabs/distribution',
                        import_url: 'promote/customertabs/import',
                        table: 'customer_resource',
                    },
                    toolbar: '#toolbar7',
                    pk: 'id',
                    sortName: 'id',
                    searchFormVisible: true,
                    columns: [
                        [
                            {checkbox: true},
                            {field: 'id', title: __('Id'), operate: false},
                            // {field: 'platform_id', title: __('Platform_id')},
                            // {field: 'backoffice_id', title: __('Backoffice_id')},
                            {field: 'platform.name', title: __('所属平台')},

                            // {field: 'sales_id', title: __('Sales_id')},
                            {field: 'username', title: __('Username')},
                            {field: 'phone', title: __('Phone')},
                            {field: 'age', title: __('Age')},
                            {
                                field: 'genderdata',
                                title: __('Genderdata'),
                                visible: false,
                                searchList: {"male": __('genderdata male'), "female": __('genderdata female')}
                            },
                            {field: 'genderdata_text', title: __('Genderdata'), operate: false},
                            {
                                field: 'distributinternaltime',
                                title: __('Distributinternaltime'),
                                operate: false,
                                addclass: 'datetimerange',
                                formatter: Table.api.formatter.datetime
                            },
                            {
                                field: 'createtime',
                                title: __('Createtime'),
                                operate: false,
                                addclass: 'datetimerange',
                                formatter: Table.api.formatter.datetime
                            },
                            {
                                field: 'updatetime',
                                title: __('Updatetime'),
                                operate: false,
                                addclass: 'datetimerange',
                                formatter: Table.api.formatter.datetime,

                            },
                            {
                                field: 'operate',
                                title: __('Operate'),
                                table: musics,

                                // buttons: [
                                //     {
                                //         name: 'detail',
                                //         text: '分配',
                                //         title: '分配',
                                //         icon: 'fa fa-share',
                                //         classname: 'btn btn-xs btn-info btn-newCustomer',
                                //     },
                                //
                                // ],
                                events: Controller.api.events.operate,
                                formatter: Controller.api.operate
                            }

                        ]
                    ]

                });

                // 为已分配的客户表格绑定事件
                Table.api.bindevent(musics);

                //数据实时统计
                musics.on('load-success.bs.table', function (e, data) {

                    var newAllocationNum = $('#badge_new_allocation').text(data.total);
                    // var newAllocationNum = parseInt($('#badge_new_allocation').text());
                    // num = parseInt(num);
                    // $('#badge_new_allocation').text(num+newAllocationNum);

                })

                f(musics);

                add_data('.add-music', musics, 'promote/Customertabs/add_music');

                batch_share('.btn-selected-music', musics);


            },
        },
        import: function () {
            Controller.api.bindevent();

        },
        add: function () {
            Controller.api.bindevent();

        },
        add_headline: function () {
            Controller.api.bindevent();
        },
        add_baidu: function () {
            Controller.api.bindevent();
        },
        add_same_city: function () {
            Controller.api.bindevent();
        },
        add_music: function () {
            Controller.api.bindevent();
        },
        //单个分配
        dstribution: function () {

            // $(".btn-add").data("area", ["300px","200px"]);
            Table.api.init({});
            Form.api.bindevent($("form[role=form]"), function (data, ret) {
                //这里是表单提交处理成功后的回调函数，接收来自php的返回数据

                Fast.api.close(data);//这里是重点
                // console.log(data);
                // Toastr.success("成功");//这个可有可无
            }, function (data, ret) {
                // console.log(data);
                Toastr.success("失败");
            });
            Controller.api.bindevent();
            // console.log(Config.id);

        },
        api: {
            bindevent: function () {
                $(document).on('click', "input[name='row[ismenu]']", function () {
                    var name = $("input[name='row[name]']");
                    name.prop("placeholder", $(this).val() == 1 ? name.data("placeholder-menu") : name.data("placeholder-node"));
                });
                $("input[name='row[ismenu]']:checked").trigger("click");
                Form.api.bindevent($("form[role=form]"));

            },
            events: {
                operate: {
                    'click .btn-newCustomer': function (e, value, row, index) {
                        e.stopPropagation();
                        e.preventDefault();
                        var table = $(this).closest('table');
                        var options = table.bootstrapTable('getOptions');
                        var ids = row[options.pk];
                        row = $.extend({}, row ? row : {}, {ids: ids});
                        var url = 'promote/customertabs/dstribution';
                        Fast.api.open(Table.api.replaceurl(url, row, table), __('Edit'), $(this).data() || {});
                    },
                    //删除按钮
                    'click .btn-delone': function (e, value, row, index) {
                        /**删除按钮 */

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
            operate: function (value, row, index) {
                var table = this.table;
                // 操作配置
                var options = table ? table.bootstrapTable('getOptions') : {};
                // 默认按钮组
                var buttons = $.extend([], this.buttons || []);

                console.log(row);
                // if (options.extend.del_url !== '') {
                //     buttons.push({
                //         name: 'del',
                //         icon: 'fa fa-trash',
                //         title: __('Del'),
                //         extend: 'data-toggle="tooltip"',
                //         classname: 'btn btn-xs btn-danger btn-delone'
                //     });
                // }

                if (row.backoffice_id == null) {
                    buttons.push(
                        {
                            name: 'detail',
                            text: '分配',
                            title: '分配',
                            icon: 'fa fa-share',
                            classname: 'btn btn-xs btn-info btn-newCustomer',
                        },
                        {
                            name: 'del',
                            icon: 'fa fa-trash',
                            title: __('Del'),
                            extend: 'data-toggle="tooltip"',
                            classname: 'btn btn-xs btn-danger btn-delone'
                        }
                    )
                }else{
                    buttons.push(
                        {
                            name: 'allocated',
                            text: '已分配给内勤',
                            title: '已分配',
                            icon: 'fa fa-check',
                            classname: 'text-info',
                        }
                    );
                }


                return Table.api.buttonlink(this, buttons, value, row, index, 'operate');
            }
        }

    };

    function f(table) {

        //导出分配客户的信息
        var submitForm = function (ids, layero) {
            var options = table.bootstrapTable('getOptions');
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

        $(document).on("click", '.btn-export', function () {
            var ids = Table.api.selectedids(table);
            var page = table.bootstrapTable('getData');
            var all = table.bootstrapTable('getOptions').totalRows;
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
    }

    //添加按钮
    function add_data(clickname, table, urls) {
        $(document).on('click', clickname, function () {
            var ids = Table.api.selectedids(table);
            var url = urls;
            if (url.indexOf("{ids}") !== -1) {
                url = Table.api.replaceurl(url, {ids: ids.length > 0 ? ids.join(",") : 0}, table);
            }
            Fast.api.open(url, __('Add'), $(this).data() || {});
        });
    }

    // 批量分配
    function batch_share(clickname, table) {
        var num = 0;
        $(document).on("click", clickname, function () {
            var ids = Table.api.selectedids(table);
            num = parseInt(ids.length);
            var url = 'promote/customertabs/distribution?ids=' + ids;
            var options = {
                shadeClose: false,
                shade: [0.3, '#393D49'],
                area: ['30%', '30%'],
                callback: function (value) {

                }
            }
            Fast.api.open(url, '批量分配', options)
        })
    }


    return Controller;
});