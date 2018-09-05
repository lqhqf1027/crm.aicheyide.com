define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'backoffice/depositmanage/index',
                    // add_url: 'backoffice/depositmanage/add',
                    edit_url: 'backoffice/depositmanage/edit',
                    // del_url: 'backoffice/depositmanage/del',
                    // multi_url: 'backoffice/depositmanage/multi',
                    table: 'customer_downpayment',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        // {field: 'sales_order_id', title: __('Sales_order_id')},
                        // {field: 'plan_acar_id', title: __('Plan_acar_id')},
                        { field: 'financial_platform_id', title: __('Financial_platform_id') },
                        // { field: 'plan_acar_name', title: __('Plan_acar_name') },
                        { field: 'createtime', title: __('Createtime'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime },
                        // {field: 'sales_order_id', title: __('Sales_order_id')},
                        { field: 'sales_id', title: __('Sales_id') },
                        { field: 'username', title: __('Username') },
                        { field: 'id_card', title: __('Id_card') },
                        { field: 'city', title: __('City') },
                        { field: 'phone', title: __('Phone') },
                        { field: 'models_id', title: __('Models_id') },

                        {field: 'payment', title: __('Payment')},
                        {field: 'monthly', title: __('NewcarMonthly')},
                        {field: 'nperlist', title: __('Nperlist')},
                        {field: 'margin', title: __('Margin')},
                        {field: 'tail_section', title: __('Tail_section')},
                        {field: 'gps', title: __('Gps')},

                        {field: 'openingbank', title: __('Openingbank')},
                        {field: 'bankcardnumber', title: __('Bankcardnumber')},
                        {field: 'totalmoney', title: __('Totalmoney')},
                        {field: 'downpayment', title: __('Downpayment')},
                        {field: 'moneyreceived', title: __('Moneyreceived')},
                        {field: 'marginmoney', title: __('Marginmoney')},
                        {field: 'gatheringaccount', title: __('Gatheringaccount')},
                        {field: 'note', title: __('Note')},
                        {field: 'decorate', title: __('Decorate')},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
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
            }
        }
    };
    return Controller;
});