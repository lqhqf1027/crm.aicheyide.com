define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'wechat/wechatuser/index',
                    add_url: 'wechat/wechatuser/add',
                    edit_url: 'wechat/wechatuser/edit',
                    del_url: 'wechat/wechatuser/del',
                    multi_url: 'wechat/wechatuser/multi',
                    table: 'wechat_user',
                }
            });

            var table = $("#table");
            $.fn.bootstrapTable.locales[Table.defaults.locale]['formatSearch'] = function(){return "快速搜索名称";};

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'nickname', title: __('Nickname')},
                        // {field: 'sex', title: __('Sex'), visible:false, searchList: {"1":__('Sex 1')}},
                        // {field: 'sex_text', title: __('Sex'), operate:false},
                       
                        {field: 'headimgurl', title: __('Headimgurl'), formatter: Table.api.formatter.image},
                        {field: 'openid', title: __('Openid')},
                        {field: 'city', title: __('City')},
                        {field: 'province', title: __('Province')},
                        // {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
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