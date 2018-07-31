define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'newcars/newcarscustomer/index',
                    // add_url: 'newcars/newcarscustomer/add',
                    // edit_url: 'newcars/newcarscustomer/edit',
                    // del_url: 'newcars/newcarscustomer/del',
                    // multi_url: 'newcars/newcarscustomer/multi',
                    table: 'car_new_user_info',
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
                        {field: 'salesorder.username', title: __('Salesorder.username')},
                        {field: 'salesorder.phone', title: __('Salesorder.phone')},
                        {field: 'salesorder.id_card', title: __('Salesorder.id_card')},
                        {field: 'salesorder.genderdata', title: __('Salesorder.genderdata')},
                        {field: 'salesorder.city', title: __('Salesorder.city')},
                        {field: 'salesorder.createtime', title: __('Salesorder.createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'salesorder.delivery_datetime', title: __('Salesorder.delivery_datetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
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