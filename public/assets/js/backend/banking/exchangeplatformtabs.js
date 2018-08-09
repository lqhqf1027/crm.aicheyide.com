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

        change_platform: function () {

            // $(".btn-add").data("area", ["300px","200px"]);
            Table.api.init({});
            Form.api.bindevent($("form[role=form]"), function (data, ret) {
                // console.log(data);

                //这里是表单提交处理成功后的回调函数，接收来自php的返回数据
                Fast.api.close(data);
                // console.log(data);
                Toastr.success("成功");
            }, function (data, ret) {
                Toastr.success("失败");

            });
            // Controller.api.bindevent();
            // console.log(Config.id);


        },

        table: {

            new_car: function () {
                // 表格1
                var newCar = $("#newCar");

                newCar.on('post-body.bs.table', function (e, settings, json, xhr) {
                    $(".btn-changePlatform").data("area", ["30%", "30%"]);

                });
                // 初始化表格
                newCar.bootstrapTable({
                    url: "banking/Exchangeplatformtabs/new_car",
                    extend: {
                        index_url: 'order/salesorder/index',
                        add_url: 'order/salesorder/add',
                        edit_url: 'banking/exchangeplatformtabs/edit',
                        // del_url: 'order/salesorder/del',
                        multi_url: 'order/salesorder/multi',
                        table: 'sales_order',
                    },
                    toolbar: '#toolbar1',
                    pk: 'id',
                    sortName: 'id',
                    columns: [
                        [
                            {checkbox: true},
                            {field: 'id', title: __('ID')},
                            {field: 'name', title: __('车型')},
                            {field: 'licensenumber', title: __('车牌号')},
                            {field: 'username', title: __('Username')},
                            {field: 'phone', title: __('电话号码')},
                            {field: 'id_card', title: __('身份证号')},
                            {field: 'detailed_address', title: __('身份证地址')},
                            {field: 'household', title: __('开户公司名')},
                            {field: 'bank_card', title: __('扣款卡号')},
                            {field: 'payment', title: __('首付')},
                            {field: 'monthly', title: __('月供')},
                            {field: 'nperlist', title: __('期数')},
                            {field: 'registration_code', title: __('登记编码')},
                            {field: 'tax', title: __('购置税')},
                            {field: 'business_risks', title: __('商业险')},
                            {field: 'insurance', title: __('交强险')},
                            {field: 'lending_date', title: __('放款日期')},
                            {field: 'createtime', title: __('订车时间'), formatter: Table.api.formatter.datetime, operate: false},
                            {field: 'delivery_datetime', title: __('提车时间')},
                            {
                                field: 'operate',
                                title: __('Operate'),
                                table: newCar,
                                events: Controller.api.events.operate,
                                formatter: Controller.api.formatter.operate,
                                buttons: [
                                    {
                                        name: 'edit',
                                        text: __('编辑'),
                                        icon: 'fa fa-pencil',
                                        title: '编辑',
                                        extend: 'data-toggle="tooltip"',
                                        classname: 'btn btn-xs btn-success btn-editone',
                                    },
                                    {
                                        name: 'change',
                                        text: __('更改平台'),
                                        icon: 'fa fa-pencil',
                                        title: __('更改平台'),
                                        extend: 'data-toggle="tooltip"',
                                        classname: 'btn btn-xs btn-danger btn-changePlatform',
                                    },

                                ]
                            }
                        ]
                    ]
                });
                // 为表格1绑定事件
                Table.api.bindevent(newCar);

            },
            yue_da_car: function () {
                // 表格1
                var yueDaCar = $("#yueDaCar");

                yueDaCar.on('post-body.bs.table', function (e, settings, json, xhr) {
                    $(".btn-changePlatform").data("area", ["30%", "30%"]);

                    // $(".btn-showOrder").data("area", ["80%", "80%"]);
                });
                // 初始化表格
                yueDaCar.bootstrapTable({
                    url: "banking/Exchangeplatformtabs/yue_da_car",
                    extend: {
                        index_url: 'order/salesorder/index',
                        add_url: 'order/salesorder/add',
                        edit_url: 'banking/exchangeplatformtabs/edit',
                        // del_url: 'order/salesorder/del',
                        multi_url: 'order/salesorder/multi',
                        table: 'sales_order',
                    },
                    toolbar: '#toolbar2',
                    pk: 'id',
                    sortName: 'id',
                    columns: [
                        [
                            {checkbox: true},
                            {field: 'id', title: __('ID')},
                            {field: 'name', title: __('车型')},
                            {field: 'licensenumber', title: __('车牌号')},
                            {field: 'username', title: __('Username')},
                            {field: 'phone', title: __('电话号码')},
                            {field: 'id_card', title: __('身份证号')},
                            {field: 'detailed_address', title: __('身份证地址')},
                            {field: 'household', title: __('开户公司名')},
                            {field: 'bank_card', title: __('扣款卡号')},
                            {field: 'payment', title: __('首付')},
                            {field: 'monthly', title: __('月供')},
                            {field: 'nperlist', title: __('期数')},
                            {field: 'registration_code', title: __('登记编码')},
                            {field: 'tax', title: __('购置税')},
                            {field: 'business_risks', title: __('商业险')},
                            {field: 'insurance', title: __('交强险')},
                            {field: 'lending_date', title: __('放款日期')},
                            {field: 'createtime', title: __('订车时间'), formatter: Table.api.formatter.datetime, operate: false},
                            {field: 'delivery_datetime', title: __('提车时间')},
                            {
                                field: 'operate',
                                title: __('Operate'),
                                table: yueDaCar,
                                events: Controller.api.events.operate,
                                formatter: Controller.api.formatter.operate,
                                buttons: [
                                    {
                                        name: 'edit',
                                        text: __('编辑'),
                                        icon: 'fa fa-pencil',
                                        title: '编辑',
                                        extend: 'data-toggle="tooltip"',
                                        classname: 'btn btn-xs btn-success btn-editone',
                                    },
                                    {
                                        name: 'change',
                                        text: __('更改平台'),
                                        icon: 'fa fa-pencil',
                                        title: __('更改平台'),
                                        extend: 'data-toggle="tooltip"',
                                        classname: 'btn btn-xs btn-danger btn-changePlatform',
                                    },

                                ]
                            }
                        ]
                    ]
                });
                // 为表格1绑定事件
                Table.api.bindevent(yueDaCar);

            },
            other_car: function () {
                // 表格1
                var otherCar = $("#otherCar");

                otherCar.on('post-body.bs.table', function (e, settings, json, xhr) {
                    $(".btn-changePlatform").data("area", ["30%", "30%"]);
                    // $(".btn-showOrder").data("area", ["80%", "80%"]);
                });
                // 初始化表格
                otherCar.bootstrapTable({
                    url: "banking/Exchangeplatformtabs/other_car",
                    extend: {
                        index_url: 'order/salesorder/index',
                        add_url: 'order/salesorder/add',
                        edit_url: 'banking/exchangeplatformtabs/edit',
                        // del_url: 'order/salesorder/del',
                        multi_url: 'order/salesorder/multi',
                        table: 'sales_order',
                    },
                    toolbar: '#toolbar3',
                    pk: 'id',
                    sortName: 'id',
                    columns: [
                        [
                            {checkbox: true},
                            {field: 'id', title: __('ID')},
                            {field: 'name', title: __('车型')},
                            {field: 'licensenumber', title: __('车牌号')},
                            {field: 'username', title: __('Username')},
                            {field: 'phone', title: __('电话号码')},
                            {field: 'id_card', title: __('身份证号')},
                            {field: 'detailed_address', title: __('身份证地址')},
                            {field: 'household', title: __('开户公司名')},
                            {field: 'bank_card', title: __('扣款卡号')},
                            {field: 'payment', title: __('首付')},
                            {field: 'monthly', title: __('月供')},
                            {field: 'nperlist', title: __('期数')},
                            {field: 'registration_code', title: __('登记编码')},
                            {field: 'tax', title: __('购置税')},
                            {field: 'business_risks', title: __('商业险')},
                            {field: 'insurance', title: __('交强险')},
                            {field: 'lending_date', title: __('放款日期')},
                            {field: 'createtime', title: __('订车时间'), formatter: Table.api.formatter.datetime, operate: false},
                            {field: 'delivery_datetime', title: __('提车时间')},
                            {
                                field: 'operate',
                                title: __('Operate'),
                                table: otherCar,
                                events: Controller.api.events.operate,
                                formatter: Controller.api.formatter.operate,
                                buttons: [
                                    {
                                        name: 'edit',
                                        text: __('编辑'),
                                        icon: 'fa fa-pencil',
                                        title: '编辑',
                                        extend: 'data-toggle="tooltip"',
                                        classname: 'btn btn-xs btn-success btn-editone',
                                    },
                                    {
                                        name: 'change',
                                        text: __('更改平台'),
                                        icon: 'fa fa-pencil',
                                        title: __('更改平台'),
                                        extend: 'data-toggle="tooltip"',
                                        classname: 'btn btn-xs btn-danger btn-changePlatform',
                                    },

                                ]
                            }
                        ]
                    ]
                });
                // 为表格1绑定事件
                Table.api.bindevent(otherCar);

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
            formatter: {
                operate: function (value, row, index) {

                    var table = this.table;
                    // 操作配置
                    var options = table ? table.bootstrapTable('getOptions') : {};
                    // 默认按钮组
                    var buttons = $.extend([], this.buttons || []);


                    return Table.api.buttonlink(this, buttons, value, row, index, 'operate');
                },
            },
            events:{
                operate:{
                    'click .btn-editone': function (e, value, row, index) {  //编辑
                        e.stopPropagation();
                        e.preventDefault();
                        var table = $(this).closest('table');
                        var options = table.bootstrapTable('getOptions');
                        var ids = row[options.pk];
                        row = $.extend({}, row ? row : {}, {ids: ids});
                        var url = 'banking/exchangeplatformtabs/edit';
                        Fast.api.open(Table.api.replaceurl(url, row, table), __('编辑'), $(this).data() || {});

                    },
                    'click .btn-changePlatform': function (e, value, row, index) {
                        e.stopPropagation();
                        e.preventDefault();
                        var table = $(this).closest('table');
                        var options = table.bootstrapTable('getOptions');
                        var ids = row[options.pk];
                        row = $.extend({}, row ? row : {}, {ids: ids});
                        var url = 'banking/exchangeplatformtabs/change_platform';
                        Fast.api.open(Table.api.replaceurl(url, row, table), __('更换平台'), $(this).data() || {});
                    }
                }
            },


        }

    };
    return Controller;
});