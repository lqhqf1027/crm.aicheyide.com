define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var goeasy = new GoEasy({
        appkey: 'BC-04084660ffb34fd692a9bd1a40d7b6c2'
    });

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'vehiclemanagement/newnventory/index',
                    add_url: 'vehiclemanagement/newnventory/add',
                    edit_url: 'vehiclemanagement/newnventory/edit',
                    del_url: 'vehiclemanagement/newnventory/del',
                    multi_url: 'vehiclemanagement/newnventory/multi',
                    table: 'car_new_inventory',
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
                        {field: 'models.name', title: __('车型名称')},
                        // {field: 'carnumber', title: __('Carnumber')},
                        // {field: 'reservecar', title: __('Reservecar')},
                        {field: 'licensenumber', title: __('车牌号')},
                        {field: 'frame_number', title: __('车架号')},
                        {field: 'engine_number', title: __('发动机号')},
                        {field: 'household', title: __('所属户')},
                        {field: '4s_shop', title: __('4S店')},
                        {field: 'note', title: __('备注'),operate:false},
                        {field: 'createtime', title: __('创建时间'), operate:false, addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('更新时间'), operate:false, addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);

            //实时消息
            //通过---录入库存通知
            goeasy.subscribe({
                channel: 'demo-newcontrol_tube',
                onMessage: function(message){
                    Layer.alert('新消息：'+message.content,{ icon:0},function(index){
                        Layer.close(index);
                        $(".btn-refresh").trigger("click");
                    });
                        
                }
            });

            //实时消息----其他金融
            //通过---录入库存通知
            goeasy.subscribe({
                channel: 'demo-newcontrol_tube_finance',
                onMessage: function(message){
                    Layer.alert('新消息：'+message.content,{ icon:0},function(index){
                        Layer.close(index);
                        $(".btn-refresh").trigger("click");
                    });
                        
                }
            });


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