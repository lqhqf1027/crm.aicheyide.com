define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'cms/coupon/index',
                    add_url: 'cms/coupon/add',
                    edit_url: 'cms/coupon/edit',
                    del_url: 'cms/coupon/del',
                    multi_url: 'cms/coupon/multi',
                    table: 'cms_coupon',
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
                        {field: 'coupon_name', title: __('Coupon_name')},
                        {field: 'circulation', title: __('Circulation')},
                        {field: 'city_id', title: __('City_id')},
                        {field: 'display_diagram', title: __('Display_diagram')},
                        {field: 'threshold', title: __('Threshold'), searchList: {"no_limit_use":__('Threshold no_limit_use'),"full_use_reduction":__('Threshold full_use_reduction')}, formatter: Table.api.formatter.normal},
                        {field: 'membership_grade', title: __('Membership_grade')},
                        {field: 'limit_collar', title: __('Limit_collar'), searchList: {"no_limit":__('Limit_collar no_limit'),"limit":__('Limit_collar limit')}, formatter: Table.api.formatter.normal},
                        {field: 'term_validity', title: __('Term_validity')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
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