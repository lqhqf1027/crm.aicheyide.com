define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'riskcontrol/peccancy/index',
                    add_url: 'riskcontrol/peccancy/add',
                    edit_url: 'riskcontrol/peccancy/edit',
                    del_url: 'riskcontrol/peccancy/del',
                    multi_url: 'riskcontrol/peccancy/multi',
                    table: 'violation_inquiry',
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
                        {field: 'id', title: __('Id')},
                        {field: 'username', title: __('Username')},
                        {field: 'phone', title: __('Phone')},
                        {field: 'models', title: __('Models')},
                        {field: 'license_plate_number', title: __('License_plate_number')},
                        {field: 'frame_number', title: __('Frame_number')},
                        {field: 'engine_number', title: __('Engine_number')},
                        {field: 'total_deduction', title: __('Total_deduction')},
                        {field: 'total_fine', title: __('Total_fine'), operate:'BETWEEN'},
                        {field: 'query_times', title: __('Query_times')},
                        {field: 'car_type', title: __('Car_type'), searchList: {"1":__('Car_type 1'),"2":__('Car_type 2'),"3":__('Car_type 3'),"4":__('Car_type 4')}, formatter: Controller.api.formatter.normal},
                        {field: 'peccancy_status', title: __('Peccancy_status')},
                        {field: 'final_time', title: __('Final_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);

            /**
             * 批量查询违章
             */
            $(document).on("click", ".btn-peccancy", function () {
                var ids = [];
                var tableRow = Controller.api.selectIdsRow(table);//获取选中的行数据

                var page = table.bootstrapTable('getData');

               console.log(tableRow);

                // var closeLay = Layer.confirm("请选择要查询的客户数据", {
                //     title: '查询数据',
                //     btn: ["选中项(" + ids.length + "条)", "本页(" + page.length + "条)"],
                //     success: function (layero, index) {
                //         $(".layui-layer-btn a", layero).addClass("layui-layer-btn0");
                //     }
                //     ,
                //     //选中项
                //     yes: function (index, layero) {
                //         // var sendTemplte = Layer.confirm('请选择发送类型',{
                //         //     title:'选择要发送的模板类型',
                //         //     btn:['①提醒']
                //         // })
                //         if (ids.length < 1) {
                //             Layer.alert('数据不能为空!', {icon: 5})
                //             return false;
                //         }
                //
                //         Fast.api.ajax({
                //             url: 'riskcontrol/monthly/sedMessage',
                //             data: {ids}
                //
                //         }, function (data, ret) {
                //             Layer.close(closeLay);
                //             newcarMonthly.bootstrapTable('refresh');
                //         })
                //     }
                //     ,
                //     //本页
                //     btn2: function (index, layero) {
                //         ids = [];
                //         for (var i in page) {
                //             ids.push({
                //                 'id':page[i]['id'],
                //                 'monthly_name':page[i]['monthly_name'],
                //                 'monthly_phone_number':page[i]['monthly_phone_number'].slice(0,11),
                //                 'monthly_card_number':page[i]['monthly_card_number'].match(/.*(.{4})/)[1],
                //                 'monthly_monney':page[i]['monthly_monney']
                //             });
                //         }
                //
                //         // return;false;
                //         Fast.api.ajax({
                //             url: 'riskcontrol/monthly/sedMessage',
                //             data: {ids}
                //         }, function (data, ret) {
                //             Layer.close(closeLay);
                //
                //             newcarMonthly.bootstrapTable('refresh');
                //         })
                //     }
                //     ,
                // })
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
            },
            formatter:{
                normal:function (value, row, index) {
                    switch (value) {
                        case 1:
                            return "<span class='text-success'>(以租代购)新车</span>";
                        case 2:
                            return "<span class='text-danger'>二手车</span>";
                        case 3:
                            return "<span class='text-warning'>全款车</span>";
                        case 4:
                            return "<span class='text-info'>租车</span>";
                    }
                }
            },
            selectIdsRow: function (table) {
                var options = table.bootstrapTable('getOptions');
                if (options.templateView) {
                    return $.map($("input[data-id][name='checkbox']:checked"), function (dom) {
                        return $(dom)
                    });
                } else {
                    return $.map(table.bootstrapTable('getSelections'), function (row) {
                        return row;
                    });
                }
            }
        }
    };
    return Controller;
});