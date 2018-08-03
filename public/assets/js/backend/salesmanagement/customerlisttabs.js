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
                    $(".btn-newSalesList").data("area", ["90%", "90%"]);

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
                                        name: 'detail',
                                        text: '新增销售单',
                                        title: '新增销售单',
                                        icon: 'fa fa-share',
                                        classname: 'btn btn-xs btn-warning btn-newSalesList'
                                    },
                                    {

                                        name: 'edit',
                                        text: __('Feedback'),
                                        icon: 'fa fa-pencil',
                                        title: '反馈',
                                        extend: 'data-toggle="tooltip"',
                                        classname: 'btn btn-xs btn-success btn-editone',
                                        url: 'salesmanagement/customerlisttabs/edits'

                                    },
                                    {

                                        name: 'del',
                                        text: '放弃',
                                        icon: 'fa fa-trash',
                                        extend: 'data-toggle="tooltip"',
                                        title: __('Del'),
                                        classname: 'btn btn-xs btn-danger btn-give_up'

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

                                var addData = parseInt(data);
                                var pre = $('#badge_give_up').text();

                                pre = parseInt(pre);

                                $('#badge_give_up').text(pre + addData);
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

                newCustomer.on('load-success.bs.table', function (e, data) {
                    $('#badge_new_customer').text(data.total);

                })

            },
            relation: function () {
                // 表格2     待联系
                $.fn.bootstrapTable.locales[Table.defaults.locale]['formatSearch'] = function () {
                    return "快速搜索客户姓名";
                };

                var relations = $("#relations");
                relations.on('post-body.bs.table', function (e, settings, json, xhr) {

                    $(".btn-showFeedback").data("area", ["80%", "80%"]);
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

                            {
                                field: 'operate', title: __('Operate'), table: relations,
                                buttons: [
                                    {
                                        name: 'detail',
                                        text: '新增销售单',
                                        title: '新增销售单',
                                        icon: 'fa fa-share',
                                        classname: 'btn btn-xs btn-warning btn-newSalesList'
                                    },
                                    {

                                        name: 'edit',
                                        text: __('Feedback'),
                                        icon: 'fa fa-pencil',
                                        title: '反馈',
                                        extend: 'data-toggle="tooltip"',
                                        classname: 'btn btn-xs btn-success btn-editone',
                                        url: 'salesmanagement/customerlisttabs/edit'

                                    },
                                    {

                                        name: 'del',
                                        text: '放弃',
                                        icon: 'fa fa-trash',
                                        extend: 'data-toggle="tooltip"',
                                        title: __('Del'),
                                        classname: 'btn btn-xs btn-danger btn-give_up'

                                    },
                                    {

                                        name: 'edit',
                                        text: __('查看跟进结果'),
                                        icon: 'fa fa-eye',
                                        title: '查看跟进结果',
                                        extend: 'data-toggle="tooltip"',
                                        classname: 'btn btn-xs btn-info btn-showFeedback',
                                        url: "salesmanagement/customerlisttabs/showFeedback"

                                    }
                                ],

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

                    $('#badge_relation').text(data.total);

                })


            },
            intention: function () {
                // 表格3     有意向
                $.fn.bootstrapTable.locales[Table.defaults.locale]['formatSearch'] = function () {
                    return "快速搜索客户姓名";
                };

                var intentions = $("#intentions");
                intentions.on('post-body.bs.table', function (e, settings, json, xhr) {
                    $(".btn-showFeedback").data("area", ["80%", "80%"]);
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


                            {
                                field: 'operate', title: __('Operate'), table: intentions,
                                buttons: [
                                    {
                                        name: 'detail',
                                        text: '新增销售单',
                                        title: '新增销售单',
                                        icon: 'fa fa-share',
                                        classname: 'btn btn-xs btn-warning btn-newSalesList'
                                    },
                                    {

                                        name: 'edit',
                                        text: __('Feedback'),
                                        icon: 'fa fa-pencil',
                                        title: '反馈',
                                        extend: 'data-toggle="tooltip"',
                                        classname: 'btn btn-xs btn-success btn-editone',
                                        url: 'salesmanagement/customerlisttabs/edit'

                                    },
                                    {

                                        name: 'del',
                                        text: '放弃',
                                        icon: 'fa fa-trash',
                                        extend: 'data-toggle="tooltip"',
                                        title: __('Del'),
                                        classname: 'btn btn-xs btn-danger btn-give_up'

                                    },
                                    {

                                        name: 'edit',
                                        text: __('查看跟进结果'),
                                        icon: 'fa fa-eye',
                                        title: '查看跟进结果',
                                        extend: 'data-toggle="tooltip"',
                                        classname: 'btn btn-xs btn-info btn-showFeedback',
                                        url: "salesmanagement/customerlisttabs/showFeedback"

                                    }
                                ],
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
                    $('#badge_intention').text(data.total);

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


                            {
                                field: 'operate', title: __('Operate'), table: nointentions,
                                buttons: [
                                    {
                                        name: 'detail',
                                        text: '新增销售单',
                                        title: '新增销售单',
                                        icon: 'fa fa-share',
                                        classname: 'btn btn-xs btn-warning btn-newSalesList'
                                    },
                                    {

                                        name: 'edit',
                                        text: __('Feedback'),
                                        icon: 'fa fa-pencil',
                                        title: '反馈',
                                        extend: 'data-toggle="tooltip"',
                                        classname: 'btn btn-xs btn-success btn-editone',
                                        url: 'salesmanagement/customerlisttabs/edit'

                                    },
                                    {

                                        name: 'del',
                                        text: '放弃',
                                        icon: 'fa fa-trash',
                                        extend: 'data-toggle="tooltip"',
                                        title: __('Del'),
                                        classname: 'btn btn-xs btn-danger btn-give_up'

                                    },
                                    {

                                        name: 'edit',
                                        text: __('查看跟进结果'),
                                        icon: 'fa fa-eye',
                                        title: '查看跟进结果',
                                        extend: 'data-toggle="tooltip"',
                                        classname: 'btn btn-xs btn-info btn-showFeedback',
                                        url: "salesmanagement/customerlisttabs/showFeedback"

                                    }
                                ],
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
                    $('#badge_no_intention').text(data.total);

                })


            },
            giveup: function () {
                // 表格5     已放弃
                $.fn.bootstrapTable.locales[Table.defaults.locale]['formatSearch'] = function () {
                    return "快速搜索客户姓名";
                };

                var giveups = $("#giveups");
                giveups.on('post-body.bs.table', function (e, settings, json, xhr) {
                    $(".btn-showFeedback").data("area", ["80%", "80%"]);
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

                    $('#badge_give_up').text(data.total);

                })


            },

            overdue: function () {
                // 表格3     有意向
                $.fn.bootstrapTable.locales[Table.defaults.locale]['formatSearch'] = function () {
                    return "快速搜索客户姓名";
                };

                var overdues = $("#overdues");
                overdues.on('post-body.bs.table', function (e, settings, json, xhr) {
                    $(".btn-showFeedback").data("area", ["80%", "80%"]);
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


                            {
                                field: 'operate', title: __('Operate'), table: overdues,
                                buttons: [
                                    {
                                        name: 'detail',
                                        text: '新增销售单',
                                        title: '新增销售单',
                                        icon: 'fa fa-share',
                                        classname: 'btn btn-xs btn-warning btn-newSalesList'
                                    },
                                    {

                                        name: 'edit',
                                        text: __('Feedback'),
                                        icon: 'fa fa-pencil',
                                        title: '反馈',
                                        extend: 'data-toggle="tooltip"',
                                        classname: 'btn btn-xs btn-success btn-editone',
                                        url: 'salesmanagement/customerlisttabs/edit'

                                    },
                                    {

                                        name: 'del',
                                        text: '放弃',
                                        icon: 'fa fa-trash',
                                        extend: 'data-toggle="tooltip"',
                                        title: __('Del'),
                                        classname: 'btn btn-xs btn-danger btn-give_up'

                                    },
                                    {

                                        name: 'edit',
                                        text: __('查看跟进结果'),
                                        icon: 'fa fa-eye',
                                        title: '查看跟进结果',
                                        extend: 'data-toggle="tooltip"',
                                        classname: 'btn btn-xs btn-info btn-showFeedback',
                                        url: "salesmanagement/customerlisttabs/showFeedback"

                                    }
                                ],
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
        edits: function () {
            Form.api.bindevent($("form[role=form]"), function (data, ret) {
                alert(1111);
                console.log(data);
                console.log(ret);
                //这里是表单提交处理成功后的回调函数，接收来自php的返回数据
                // Fast.api.close(data);//这里是重点

                // console.log(data);
                Toastr.success("成功");//这个可有可无
            }, function (data, ret) {
                // console.log(data);
                alert(22222)

                Toastr.error("失败");

            });
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

                    'click .btn-editone': function (e, value, row, index) {  //编辑

                        e.stopPropagation();
                        e.preventDefault();
                        var table = $(this).closest('table');
                        var options = table.bootstrapTable('getOptions');
                        var ids = row[options.pk];
                        row = $.extend({}, row ? row : {}, {ids: ids});
                        var url = 'salesmanagement/customerlisttabs/edits';
                        Fast.api.open(Table.api.replaceurl(url, row, table), __('反馈'), $(this).data() || {});
                    },


                    'click .btn-showFeedback': function (e, value, row, index) {
                        e.stopPropagation();
                        e.preventDefault();
                        var table = $(this).closest('table');
                        var options = table.bootstrapTable('getOptions');
                        var ids = row[options.pk];
                        row = $.extend({}, row ? row : {}, {ids: ids});
                        var url = "salesmanagement/customerlisttabs/showFeedback";
                        Fast.api.open(Table.api.replaceurl(url, row, table), __('查看跟进信息'), $(this).data() || {});
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
                                }, function (data, ret) {


                                    var pre = $('#badge_give_up').text();

                                    pre = parseInt(pre);

                                    $('#badge_give_up').text(pre + 1);

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

                    'click .btn-newSalesList': function (e, value, row, index) {
                        e.stopPropagation();
                        e.preventDefault();
                        var table = $(this).closest('table');
                        var options = table.bootstrapTable('getOptions');
                        var ids = row[options.pk];
                        // alert(row[options.pk]);
                        //第三方扩展皮肤

                        layer.alert('请前往订单列表,选择对应的方案进行新增销售单', {
                            // icon: 1,
                            skin: 'layer-ext-moon', //该皮肤由layer.seaning.com友情扩展。关于皮肤的扩展规则，去这里查阅
                            // content: "<h4>请前往订单列表,选择对应的方案进行新增销售单</h4>",
                            btn: ['前往'],
                            btn1: function () {

                                window.location.href="../backoffice/Custominfotabs/test1";
                                // var planid = $("input[name=plan]:checked").val();
                                // window.location.href="Orderlisttabs/index"
                                // window.location.href="../order/Salesorder/index";
                                // window.location.href="../order/Salesorder/chooseAdd?userid="+row[options.pk]+"&planid="+planid;
                                // row = $.extend({}, row ? row : {}, {ids: ids});
                                // var url = "salesmanagement/customerlisttabs/addzu";
                                // Fast.api.open(Table.api.replaceurl(url, row, table), __('查看跟进信息'), $(this).data() || {});

                            }

                        });
                        $(".layui-layer-btn0").css({"margin-right": "132px"});
                        $(".la").css({"margin-left": "10px"});


                        //     "<h4 style='line-height: 50px'>请选择新增方案：</h4>" +
                        // "<label><input name='plan' type=\"radio\" value='0' /> 以租代购(新车)</label>" +
                        // "<label class='la'><input name='plan' type=\"radio\" value='1' /> 纯租</label>" +
                        // "<label class='la'><input name='plan' type=\"radio\" value='2' checked/> 以租代购(二手车)</label>" +
                        // "<label class='la'><input name='plan' type=\"radio\" value='3' /> 全款</label>",


                    }
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
                },
                status: function (value, row, index) {

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