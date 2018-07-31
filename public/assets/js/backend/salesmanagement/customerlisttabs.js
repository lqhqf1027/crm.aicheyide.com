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

        table: {

            new_customer: function () {

                // 表格1
                $.fn.bootstrapTable.locales[Table.defaults.locale]['formatSearch'] = function () {
                    return "快速搜索客户姓名";
                };

                var newCustomer = $("#newCustomer");
                newCustomer.on('post-body.bs.table', function (e, settings, json, xhr) {
                    $(".btn-newSalesList").data("area", ["50%", "50%"]);
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
                                field: 'distributsaletime',
                                title: __('Distributsaletime'),
                                operate: false,
                                formatter: Table.api.formatter.datetime
                            },

                            {
                                field: 'operate', title: __('Operate'), table: newCustomer,
                                buttons: [
                                    {
                                        name:'detail',
                                        text:'新增销售单',
                                        title:'新增销售单',
                                        icon: 'fa fa-share',
                                        classname: 'btn btn-xs btn-info btn-dialog btn-newSalesList',
                                        url: 'salesmanagement/customerlisttabs/newSalesList'
                                    }

                                ],
                                events: Controller.api.events.operate,
                                formatter: Controller.api.formatter.operate
                            }
                        ]
                    ]
                });


                // 为表格1绑定事件
                Table.api.bindevent(newCustomer);

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

                })

                newCustomer.on('load-success.bs.table', function (e, data) {
                    // data.total
                    var newCustomerNum = $('#badge_new_customer').text(data.total);

                })

            },
            relation: function () {
                // 表格2     待联系
                $.fn.bootstrapTable.locales[Table.defaults.locale]['formatSearch'] = function () {
                    return "快速搜索客户姓名";
                };

                var relations = $("#relations");
                relations.on('post-body.bs.table', function (e, settings, json, xhr) {
                    $(".btn-newCustomer").data("area", ["50%", "50%"]);
                });
                // 初始化表格
                relations.bootstrapTable({
                    url: 'salesmanagement/Customerlisttabs/relation',
                    extend: {
                        index_url: 'customer/customerresource/index',
                        add_url: 'salesmanagement/customerlisttabs/add',
                        edit_url: 'salesmanagement/customerlisttabs/edit',
                        del_url: 'customer/customerresource/del',
                        multi_url: 'customer/customerresource/multi',
                        table: 'customer_resource',
                    },
                    toolbar: '#toolbar2',
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
                            {field: 'followupdate', title: '下次跟进时间', operate: false},
                            {
                                field: 'customerlevel',
                                title: '客户等级',
                                operate: false,
                                formatter: Controller.api.formatter.status
                            },
                            // {
                            //     field: 'distributinternaltime',
                            //     title: __('Distributinternaltime'),
                            //     operate: false,
                            //     formatter: Table.api.formatter.datetime
                            // },
                            // {
                            //     field: 'distributsaletime',
                            //     title: __('Distributsaletime'),
                            //     operate: false,
                            //     formatter: Table.api.formatter.datetime
                            // },

                            {
                                field: 'operate', title: __('Operate'), table: relations,

                                events: Controller.api.events.operate,
                                formatter: Controller.api.formatter.operate
                            }
                        ]
                    ]
                });
                // 为表格1绑定事件
                Table.api.bindevent(relations);

                $(document).on("click", ".btn-selected", function (e, value, row, index) {
                    var ids = Table.api.selectedids(relations);
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
                                relations.bootstrapTable('refresh');
                                return false;
                            }, function (data, ret) {
                                //失败的回调
                                relations.bootstrapTable('refresh');
                                return false;
                            });


                        }
                    );

                });
                relations.on('load-success.bs.table', function (e, data) {

                    var newCustomerNum = $('#badge_relation').text(data.total);

                })


            },
            intention: function () {
                // 表格3     有意向
                $.fn.bootstrapTable.locales[Table.defaults.locale]['formatSearch'] = function () {
                    return "快速搜索客户姓名";
                };

                var intentions = $("#intentions");
                intentions.on('post-body.bs.table', function (e, settings, json, xhr) {
                    $(".btn-newCustomer").data("area", ["50%", "50%"]);
                });
                // 初始化表格
                intentions.bootstrapTable({
                    url: 'salesmanagement/Customerlisttabs/intention',
                    extend: {
                        index_url: 'customer/customerresource/index',
                        add_url: 'salesmanagement/customerlisttabs/add',
                        edit_url: 'salesmanagement/customerlisttabs/edit',
                        del_url: 'customer/customerresource/del',
                        multi_url: 'customer/customerresource/multi',
                        table: 'customer_resource',
                    },
                    toolbar: '#toolbar3',
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
                            {field: 'followupdate', title: '下次跟进时间', operate: false},
                            {
                                field: 'customerlevel',
                                title: '客户等级',
                                operate: false,
                                formatter: Controller.api.formatter.status
                            },

                            // {
                            //     field: 'distributinternaltime',
                            //     title: __('Distributinternaltime'),
                            //     operate: false,
                            //     formatter: Table.api.formatter.datetime
                            // },
                            // {
                            //     field: 'distributsaletime',
                            //     title: __('Distributsaletime'),
                            //     operate: false,
                            //     formatter: Table.api.formatter.datetime
                            // },

                            {
                                field: 'operate', title: __('Operate'), table: intentions,

                                events: Controller.api.events.operate,
                                formatter: Controller.api.formatter.operate
                            }
                        ]
                    ]
                });
                // 为表格1绑定事件
                Table.api.bindevent(intentions);

                $(document).on("click", ".btn-selected", function (e, value, row, index) {
                    var ids = Table.api.selectedids(intentions);
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
                                intentions.bootstrapTable('refresh');
                                return false;
                            }, function (data, ret) {
                                //失败的回调
                                intentions.bootstrapTable('refresh');
                                return false;
                            });


                        }
                    );

                });

                intentions.on('load-success.bs.table', function (e, data) {
                    // data.total
                    var newCustomerNum = $('#badge_intention').text(data.total);

                })

            },
            nointention: function () {
                // 表格4     暂无意向
                $.fn.bootstrapTable.locales[Table.defaults.locale]['formatSearch'] = function () {
                    return "快速搜索客户姓名";
                };

                var nointentions = $("#nointentions");
                nointentions.on('post-body.bs.table', function (e, settings, json, xhr) {
                    $(".btn-newSalesList").data("area", ["50%", "50%"]);
                });
                // 初始化表格
                nointentions.bootstrapTable({
                    url: 'salesmanagement/Customerlisttabs/nointention',
                    extend: {
                        index_url: 'customer/customerresource/index',
                        add_url: 'salesmanagement/customerlisttabs/add',
                        edit_url: 'salesmanagement/customerlisttabs/edit',
                        del_url: 'customer/customerresource/del',
                        multi_url: 'customer/customerresource/multi',
                        table: 'customer_resource',
                    },
                    toolbar: '#toolbar4',
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
                            {field: 'followupdate', title: '下次跟进时间', operate: false},
                            {
                                field: 'customerlevel',
                                title: '客户等级',
                                operate: false,
                                formatter: Controller.api.formatter.status
                            },

                            // {
                            //     field: 'distributinternaltime',
                            //     title: __('Distributinternaltime'),
                            //     operate: false,
                            //     formatter: Table.api.formatter.datetime
                            // },
                            // {
                            //     field: 'distributsaletime',
                            //     title: __('Distributsaletime'),
                            //     operate: false,
                            //     formatter: Table.api.formatter.datetime
                            // },

                            {
                                field: 'operate', title: __('Operate'), table: nointentions,

                                events: Controller.api.events.operate,
                                formatter: Controller.api.formatter.operate
                            }
                        ]
                    ]
                });
                // 为表格1绑定事件
                Table.api.bindevent(nointentions);

                $(document).on("click", ".btn-selected", function (e, value, row, index) {
                    var ids = Table.api.selectedids(nointentions);


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
                                nointentions.bootstrapTable('refresh');
                                return false;
                            }, function (data, ret) {
                                //失败的回调
                                nointentions.bootstrapTable('refresh');
                                return false;
                            });


                        }
                    );

                });
                nointentions.on('load-success.bs.table', function (e, data) {
                    // data.total
                    var newCustomerNum = $('#badge_no_intention').text(data.total);

                })


            },
            giveup: function () {
                // 表格5     已放弃
                $.fn.bootstrapTable.locales[Table.defaults.locale]['formatSearch'] = function () {
                    return "快速搜索客户姓名";
                };

                var giveups = $("#giveups");
                giveups.on('post-body.bs.table', function (e, settings, json, xhr) {
                    $(".btn-newCustomer").data("area", ["50%", "50%"]);
                });
                // 初始化表格
                giveups.bootstrapTable({
                    url: 'salesmanagement/Customerlisttabs/giveup',
                    extend: {
                        index_url: 'customer/customerresource/index',
                        add_url: 'salesmanagement/customerlisttabs/add',
                        edit_url: 'salesmanagement/customerlisttabs/edit',
                        del_url: 'customer/customerresource/del',
                        multi_url: 'customer/customerresource/multi',
                        table: 'customer_resource',
                    },
                    toolbar: '#toolbar5',
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

                        ]
                    ]
                });
                // 为表格1绑定事件
                Table.api.bindevent(giveups);

                giveups.on('load-success.bs.table', function (e, data) {

                    var newCustomerNum = $('#badge_give_up').text(data.total);

                })


            },

            overdue: function () {
                // 表格3     有意向
                $.fn.bootstrapTable.locales[Table.defaults.locale]['formatSearch'] = function () {
                    return "快速搜索客户姓名";
                };

                var overdues = $("#overdues");
                overdues.on('post-body.bs.table', function (e, settings, json, xhr) {
                    $(".btn-newCustomer").data("area", ["50%", "50%"]);
                });
                // 初始化表格
                overdues.bootstrapTable({
                    url: 'salesmanagement/Customerlisttabs/overdue',
                    extend: {
                        index_url: 'customer/customerresource/index',
                        add_url: 'salesmanagement/customerlisttabs/add',
                        edit_url: 'salesmanagement/customerlisttabs/edit',
                        del_url: 'customer/customerresource/del',
                        multi_url: 'customer/customerresource/multi',
                        table: 'customer_resource',
                    },
                    toolbar: '#toolbar6',
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
                            {field: 'followupdate', title: '下次跟进时间', operate: false},
                            {
                                field: 'customerlevel',
                                title: '客户等级',
                                operate: false,
                                formatter: Controller.api.formatter.status
                            },

                            // {
                            //     field: 'distributinternaltime',
                            //     title: __('Distributinternaltime'),
                            //     operate: false,
                            //     formatter: Table.api.formatter.datetime
                            // },
                            // {
                            //     field: 'distributsaletime',
                            //     title: __('Distributsaletime'),
                            //     operate: false,
                            //     formatter: Table.api.formatter.datetime
                            // },

                            {
                                field: 'operate', title: __('Operate'), table: overdues,

                                events: Controller.api.events.operate,
                                formatter: Controller.api.formatter.operate
                            }
                        ]
                    ]
                });
                // 为表格1绑定事件
                Table.api.bindevent(overdues);

                $(document).on("click", ".btn-selected", function (e, value, row, index) {
                    var ids = Table.api.selectedids(overdues);
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
                                intentions.bootstrapTable('refresh');
                                return false;
                            }, function (data, ret) {
                                //失败的回调
                                intentions.bootstrapTable('refresh');
                                return false;
                            });


                        }
                    );

                });

                overdues.on('load-success.bs.table', function (e, data) {
                    $('#badge_overdue').text(data.total);

                })

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
                status: function (value, row, index) {
                    //颜色状态数组,可使用red/yellow/aqua/blue/navy/teal/olive/lime/fuchsia/purple/maroon
                    // if(value==1) value ='可出租';
                    // if(value==0) value ='正在维修';
                    // if(value==2) value ='正在出租';


                    var colorArr = {relation: 'info', intention: 'success', nointention: 'danger'};
                    //如果字段列有定义custom
                    if (typeof this.custom !== 'undefined') {
                        colorArr = $.extend(colorArr, this.custom);
                    }
                    value = value === null ? '' : value.toString();

                    var color = value && typeof colorArr[value] !== 'undefined' ? colorArr[value] : 'primary';

                    var newValue = value.charAt(0).toUpperCase() + value.slice(1);
                    //渲染状态
                    var html = '<span class="text-' + color + '"><i class="fa fa-circle"></i> ' + __(newValue) + '</span>';
                    // if (this.operate != false) {
                    //     html = '<a href="javascript:;" class="searchit" data-toggle="tooltip" title="' + __('Click to search %s', __(newValue)) + '" data-field="' + this.field + '" data-value="' + value + '">' + html + '</a>';
                    // }
                    return html;
                },
            }
        }

    };

    return Controller;
});