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
                searchFormVisible: true,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'),operate:false},
                        // {field: 'sales_order_id', title: __('Sales_order_id')},
                        // {field: 'plan_acar_id', title: __('Plan_acar_id')},
                        { field: 'financial_platform_id', title: __('Financial_platform_id') },
                        // { field: 'plan_acar_name', title: __('Plan_acar_name') },
                        { field: 'createtime', title: __('Createtime'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime },
                        // {field: 'sales_order_id', title: __('Sales_order_id')},
                        { field: 'sales_id', title: __('Sales_id') },
                        { field: 'username', title: __('Username') },
                        { field: 'id_card', title: __('Id_card') },
                        { field: 'city', title: __('City') ,operate:false},
                        { field: 'phone', title: __('Phone') },
                        { field: 'models_id', title: __('Models_id') },

                        {field: 'payment', title: __('Payment'),operate:false},
                        {field: 'monthly', title: __('NewcarMonthly'),operate:false},
                        {field: 'nperlist', title: __('Nperlist'),operate:false},
                        {field: 'margin', title: __('Margin'),operate:false},
                        {field: 'tail_section', title: __('Tail_section'),operate:false},
                        {field: 'gps', title: __('Gps'),operate:false},

                        {field: 'openingbank', title: __('Openingbank')},
                        {field: 'bankcardnumber', title: __('Bankcardnumber')},
                        {field: 'totalmoney', title: __('Totalmoney'),operate:false},
                        {field: 'downpayment', title: __('Downpayment'),operate:false},
                        {field: 'moneyreceived', title: __('Moneyreceived'),operate:false},
                        {field: 'marginmoney', title: __('Marginmoney'),operate:false},
                        {field: 'gatheringaccount', title: __('Gatheringaccount')},
                        {field: 'note', title: __('Note'),operate:false},
                        {field: 'decorate', title: __('Decorate'),operate:false},
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