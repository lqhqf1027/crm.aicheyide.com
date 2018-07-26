define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'vehiclemanagement/rentcar/index',
                    add_url: 'vehiclemanagement/rentcar/add',
                    edit_url: 'vehiclemanagement/rentcar/edit',
                    del_url: 'vehiclemanagement/rentcar/del',
                    multi_url: 'vehiclemanagement/rentcar/multi',
                    table: 'car_rental_models_info',
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
                        {field: 'sales_id', title: __('Sales_id')},
                        {field: 'licenseplatenumber', title: __('Licenseplatenumber')},
                        {field: 'models_id', title: __('Models_id')},
                        {field: 'kilometres', title: __('Kilometres'), operate:'BETWEEN'},
                        {field: 'companyaccount', title: __('Companyaccount')},
                        {field: 'cashpledge', title: __('Cashpledge')},
                        {field: 'threemonths', title: __('Threemonths')},
                        {field: 'sixmonths', title: __('Sixmonths')},
                        {field: 'manysixmonths', title: __('Manysixmonths')},
                        {field: 'drivinglicense', title: __('Drivinglicense')},
                        {field: 'vin', title: __('Vin')},
                        {field: 'expirydate', title: __('Expirydate')},
                        {field: 'annualverificationdate', title: __('Annualverificationdate')},
                        {field: 'carcolor', title: __('Carcolor')},
                        {field: 'aeratedcard', title: __('Aeratedcard')},
                        {field: 'volumekeys', title: __('Volumekeys')},
                        {field: 'Parkingposition', title: __('Parkingposition')},
                        {field: 'shelf', title: __('Shelf'), visible:false, searchList: {"1":__('Shelf 1')}},
                        {field: 'shelf_text', title: __('Shelf'), operate:false},
                        {field: 'vehiclestate', title: __('Vehiclestate')},
                        {field: 'note', title: __('Note')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'models.name', title: __('Models.name')},
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