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

            prepare_lift_car: function () {
                // 表格1
                var prepareLiftCar = $("#prepareLiftCar");
                prepareLiftCar.on('load-success.bs.table', function (e, data) {
                    console.log(data.total);
                    $('#new-customer').text(data.total);

                })
                prepareLiftCar.on('post-body.bs.table', function (e, settings, json, xhr) {
                    $(".btn-newCustomer").data("area", ["50%", "50%"]);
                });
                // 初始化表格
                prepareLiftCar.bootstrapTable({
                    url: "newcars/Newcarscustomer/prepare_lift_car",
                    extend: {
                        index_url: 'order/salesorder/index',
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
                            { checkbox: true },
                            { field: 'id', title: __('Id') },
                            { field: 'order_no', title: __('订单编号') },
                            { field: 'financial_name', title: __('金融平台') },
                            { field: 'models_name', title: __('销售车型') },
                            { field: 'username', title: __('Username') },
                            { field: 'phone', title: __('电话号码') },
                            { field: 'id_card', title: __('身份证号') },
                            { field: 'payment', title: __('首付') },
                            { field: 'monthly', title: __('月供') },
                            { field: 'nperlist', title: __('期数') },
                            { field: 'margin', title: __('保证金') },
                            { field: 'tail_section', title: __('尾款') },
                            { field: 'gps', title: __('GPS(元)') },
                            { field: 'operate', title: __('Operate'), table: prepareLiftCar, events: Table.api.events.operate, formatter: Table.api.formatter.operate }
                        ]
                    ]
                });
                // 为表格1绑定事件
                Table.api.bindevent(prepareLiftCar);

            },
            already_lift_car: function () {
                // 表格2
                var alreadyLiftCar = $("#alreadyLiftCar");
                alreadyLiftCar.on('post-body.bs.table', function (e, settings, json, xhr) {
                    // $(".btn-newCustomer").data("area", ["30%", "30%"]);
                });
                // 初始化表格
                alreadyLiftCar.bootstrapTable({
                    url: 'newcars/Newcarscustomer/already_lift_car',
                    extend: {
                        index_url: 'order/salesorder/index',
                        add_url: 'order/salesorder/add',
                        edit_url: 'order/salesorder/edit',
                        del_url: 'order/salesorder/del',
                        multi_url: 'order/salesorder/multi',
                        table: 'sales_order',
                    },
                    toolbar: '#toolbar2',
                    pk: 'id',
                    sortName: 'id',
                    columns: [
                        [
                            { checkbox: true },
                            { field: 'id', title: __('Id') },
                            { field: 'order_no', title: __('订单编号') },
                            { field: 'financial_name', title: __('金融平台') },
                            { field: 'models_name', title: __('销售车型') },
                            { field: 'username', title: __('Username') },
                            { field: 'phone', title: __('电话号码') },
                            { field: 'id_card', title: __('身份证号') },
                            { field: 'payment', title: __('首付') },
                            { field: 'monthly', title: __('月供') },
                            { field: 'nperlist', title: __('期数') },
                            { field: 'margin', title: __('保证金') },
                            { field: 'tail_section', title: __('尾款') },
                            { field: 'gps', title: __('GPS(元)') },
                            { field: 'operate', title: __('Operate'), table: alreadyLiftCar, events: Table.api.events.operate, formatter: Table.api.formatter.operate }
                        ]
                    ]
                });
                // 为表格2绑定事件
                Table.api.bindevent(alreadyLiftCar);

                alreadyLiftCar.on('load-success.bs.table', function (e, data) {
                    $('#assigned-customer').text(data.total);

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
            }
        }

    };
    return Controller;
});