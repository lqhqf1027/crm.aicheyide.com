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
        ajaxBatchGiveup:function(){

            // $(".btn-add").data("area", ["300px","200px"]);
            Table.api.init({

            });
            Form.api.bindevent($("form[role=form]"), function(data, ret){
                //这里是表单提交处理成功后的回调函数，接收来自php的返回数据
                Fast.api.close(data);//这里是重点
                // console.log(data);
                // Toastr.success("成功");//这个可有可无
            }, function(data, ret){


                Toastr.success("失败");

            });
            // Controller.api.bindevent();
            // console.log(Config.id);


        },


        table: {

            new_customer: function () {
                // 表格1
                $.fn.bootstrapTable.locales[Table.defaults.locale]['formatSearch'] = function () {
                    return "快速搜索客户姓名";
                };

                var newCustomer = $("#newCustomer");
                newCustomer.on('post-body.bs.table', function (e, settings, json, xhr) {
                    $(".btn-newCustomer").data("area", ["50%", "50%"]);
                });
                // 初始化表格
                newCustomer.bootstrapTable({
                    url: 'salesmanagement/Customerlisttabs/newCustomer',
                    extend: {
                        index_url: 'customer/customerresource/index',
                        add_url: 'salesmanagement/customerlisttabs/add',
                        edit_url: 'salesmanagement/customerlisttabs/edit',
                        del_url: 'customer/customerresource/del',
                        multi_url: 'customer/customerresource/multi',
                        give_up_url: 'salesmanagement/customerlisttabs/give_up',
                        table: 'customer_resource',
                    },
                    toolbar: '#toolbar1',
                    pk: 'id',
                    sortName: 'id',
                    searchFormVisible: true,
                    columns: [
                        [
                            {checkbox: true},
                            {field: 'id', title: Fast.lang('Id')},
                            {field: 'platform.name', title: __('Platform_id')},

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
                                formatter: Table.api.formatter.datetime
                            },
                            {
                                field: 'distributsaletime',
                                title: __('Distributsaletime'),
                                operate: false,
                                formatter: Table.api.formatter.datetime
                            },

                            {
                                field: 'operate', title: __('Operate'), table: newCustomer,

                                events: Controller.api.events.operate,
                                formatter: Controller.api.formatter.operate
                            }
                        ]
                    ]
                });
                // 为表格1绑定事件
                Table.api.bindevent(newCustomer);


                // 批量分配
                // $(document).on("click", ".btn-selected", function () {
                //     var ids = Table.api.selectedids(newCustomer);
                //
                //
                //     var url = 'backoffice/custominfotabs/batch?ids=' + ids;
                //     var options = {
                //         shadeClose: false,
                //         shade: [0.3, '#393D49'],
                //         area: ['50%', '50%'],
                //         callback: function (value) {
                //
                //         }
                //     };
                //     Fast.api.open(url, '批量分配', options)
                // });


                // 批量加入放弃客户


                $(document).on("click", ".btn-selected", function (e, value, row, index) {
                    var ids = Table.api.selectedids(newCustomer);
                    e.stopPropagation();
                    e.preventDefault();
                    var that = this;
                    var top = $(that).offset().top - $(window).scrollTop() + 100;
                    var left = $(that).offset().left - $(window).scrollLeft() + 500;
                    if (top + 154 > $(window).height()) {
                        top = top - 154;
                    }
                    if ($(window).width() < 480) {
                        top = left = undefined;
                    }
                    Layer.confirm(

                        __('确定加入放弃客户名单吗?'),
                        {icon: 3, title: __('Warning'), offset: [top, left], shadeClose: true},

                        function (index) {

                            Fast.api.ajax({
                                url: 'salesmanagement/Customerlisttabs/ajaxBatchGiveup',
                                data: {id: JSON.stringify(ids)}
                            }, function (data, rets) {

                                Toastr.success("成功");
                                Layer.close(index);
                                 newCustomer.bootstrapTable('refresh');
                                return false;
                            }, function (data, ret) {
                                //失败的回调
                                newCustomer.bootstrapTable('refresh');
                                return false;
                            });


                        }
                    );

                });


            },
            intention: function () {
                // 表格1
                $.fn.bootstrapTable.locales[Table.defaults.locale]['formatSearch'] = function () {
                    return "快速搜索客户姓名";
                };

                var newCustomer = $("#newCustomer");
                newCustomer.on('post-body.bs.table', function (e, settings, json, xhr) {
                    $(".btn-newCustomer").data("area", ["50%", "50%"]);
                });
                // 初始化表格
                newCustomer.bootstrapTable({
                    url: 'salesmanagement/Customerlisttabs/newCustomer',
                    extend: {
                        index_url: 'customer/customerresource/index',
                        add_url: 'salesmanagement/customerlisttabs/add',
                        edit_url: 'salesmanagement/customerlisttabs/edit',
                        del_url: 'customer/customerresource/del',
                        multi_url: 'customer/customerresource/multi',
                        give_up_url: 'salesmanagement/customerlisttabs/give_up',
                        table: 'customer_resource',
                    },
                    toolbar: '#toolbar1',
                    pk: 'id',
                    sortName: 'id',
                    searchFormVisible: true,
                    columns: [
                        [
                            {checkbox: true},
                            {field: 'id', title: Fast.lang('Id')},
                            {field: 'platform.name', title: __('Platform_id')},

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
                                formatter: Table.api.formatter.datetime
                            },
                            {
                                field: 'distributsaletime',
                                title: __('Distributsaletime'),
                                operate: false,
                                formatter: Table.api.formatter.datetime
                            },

                            {
                                field: 'operate', title: __('Operate'), table: newCustomer,

                                events: Controller.api.events.operate,
                                formatter: Controller.api.formatter.operate
                            }
                        ]
                    ]
                });
                // 为表格1绑定事件
                Table.api.bindevent(newCustomer);


                // 批量分配
                // $(document).on("click", ".btn-selected", function () {
                //     var ids = Table.api.selectedids(newCustomer);
                //
                //
                //     var url = 'backoffice/custominfotabs/batch?ids=' + ids;
                //     var options = {
                //         shadeClose: false,
                //         shade: [0.3, '#393D49'],
                //         area: ['50%', '50%'],
                //         callback: function (value) {
                //
                //         }
                //     };
                //     Fast.api.open(url, '批量分配', options)
                // });


                // 批量加入放弃客户



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
                $(document).on('click', "input[name='row[ismenu]']", function () {
                    var name = $("input[name='row[name]']");
                    name.prop("placeholder", $(this).val() == 1 ? name.data("placeholder-menu") : name.data("placeholder-node"));
                });
                $("input[name='row[ismenu]']:checked").trigger("click");
                Form.api.bindevent($("form[role=form]"));
            },
            events: {
                operate: {

                    'click .btn-editone': function (e, value, row, index) {

                        e.stopPropagation();
                        e.preventDefault();
                        var table = $(this).closest('table');
                        var options = table.bootstrapTable('getOptions');
                        var ids = row[options.pk];
                        row = $.extend({}, row ? row : {}, {ids: ids});
                        var url = options.extend.edit_url;
                        Fast.api.open(Table.api.replaceurl(url, row, table), __('Edit'), $(this).data() || {});
                    },

                    'click .btn-give_up': function (e, value, row, index) {

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
                            __('确定加入放弃客户名单吗?'),
                            {icon: 3, title: __('Warning'), offset: [top, left], shadeClose: true},

                            function (index) {
                                var table = $(that).closest('table');
                                var options = table.bootstrapTable('getOptions');


                                Fast.api.ajax({
                                    url: 'salesmanagement/Customerlisttabs/ajaxGiveup',
                                    data: {id: row[options.pk]}
                                }, function (datas, rets) {
                                    //成功的回调
                                    // Fast.api.close(data);
                                    Toastr.success("成功");
                                    Layer.close(index);
                                    table.bootstrapTable('refresh');
                                    return false;
                                }, function (data, ret) {
                                    //失败的回调

                                    return false;
                                });


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

                    if (options.extend.edit_url !== '') {
                        buttons.push({
                            name: 'edit',
                            text: __('Feedback'),
                            icon: 'fa fa-pencil',
                            title: '反馈',
                            extend: 'data-toggle="tooltip"',
                            classname: 'btn btn-xs btn-success btn-editone',
                            url: options.extend.edit_url
                        });
                    }

                    buttons.push({
                        name: 'detail',
                        text: __('Newsaleslist'),
                        title: __('Newsaleslist'),
                        icon: 'fa fa-share',
                        classname: 'btn btn-xs btn-info btn-dialog btn-newCustomer',
                        url: 'backoffice/custominfotabs/admeasure',
                    });

                    if (options.extend.give_up_url !== '') {
                        //
                        buttons.push({
                            name: 'del',
                            text: '放弃',
                            icon: 'fa fa-trash',
                            extend: 'data-toggle="tooltip"',
                            title: __('Del'),
                            classname: 'btn btn-xs btn-danger btn-give_up'
                            // url:options.extend.give_up_url
                        });
                    }


                    return Table.api.buttonlink(this, buttons, value, row, index, 'operate');
                },
            }
        }

    };
    return Controller;
});