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
            table.on('post-body.bs.table', function (e, settings, json, xhr) {
                $(".btn-details").data("area", ["90%", "90%"]);

            });
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
                        {field: 'total_deduction', title: __('Total_deduction'),operate: 'BETWEEN'},
                        {field: 'total_fine', title: __('Total_fine'), operate: 'BETWEEN'},
                        {field: 'query_times', title: __('Query_times')},
                        {
                            field: 'car_type',
                            title: __('Car_type'),
                            searchList: {
                                "1": __('Car_type 1'),
                                "2": __('Car_type 2'),
                                "3": __('Car_type 3'),
                                "4": __('Car_type 4')
                            },
                            formatter: Controller.api.formatter.normal
                        },
                        {field: 'peccancy_status', title: __('Peccancy_status'),formatter:Controller.api.formatter.status},
                        {
                            field: 'id', title: __('查看详细资料'), table: table, buttons: [
                                {
                                    name: 'details', text: '查看详细资料', title: '查看订单详细资料', icon: 'fa fa-eye', classname: 'btn btn-xs btn-primary btn-dialog btn-details',
                                    url: 'riskcontrol/Peccancy/details'
                                }
                            ],

                            operate: false, formatter: Table.api.formatter.buttons
                        },
                        {
                            field: 'final_time',
                            title: __('Final_time'),
                            operate: 'RANGE',
                            addclass: 'datetimerange',
                            formatter: Table.api.formatter.datetime,
                            datetimeFormat:"YYYY-MM-DD"
                        },

                        {
                            field: 'operate',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            formatter: Table.api.formatter.operate
                        }
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

                // console.log(tableRow);return;
                var closeLay = Layer.confirm("请选择要查询的客户数据", {
                    title: '查询数据',
                    btn: ["选中项(" + tableRow.length + "条)", "本页(" + page.length + "条)"],
                    success: function (layero, index) {
                        $(".layui-layer-btn a", layero).addClass("layui-layer-btn0");
                    }
                    ,
                    //选中项
                    yes: function (index, layero) {

                        // var sendTemplte = Layer.confirm('请选择发送类型',{
                        //     title:'选择要发送的模板类型',
                        //     btn:['①提醒']
                        // })
                        if (tableRow.length < 1) {
                            Layer.alert('数据不能为空!', {icon: 5});
                            return false;
                        }
                        ids = [];
                        // console.log(tableRow);
                        for (var i in tableRow) {
                            ids.push({
                                hphm: tableRow[i]['license_plate_number'].substr(0, 2),
                                hphms: tableRow[i]['license_plate_number'],
                                engineno: tableRow[i]['engine_number'],
                                classno: tableRow[i]['frame_number']
                            })
                        }

                        // console.log(ids);

                        Fast.api.ajax({
                            url: 'riskcontrol/Peccancy/sendMessage',
                            data: {ids}

                        }, function (data, ret) {
                            console.log(data);
                            Layer.close(closeLay);
                            table.bootstrapTable('refresh');
                        })
                    }
                    ,
                    //本页
                    btn2: function (index, layero) {
                        ids = [];
                        // console.log(page);
                        for (var i in page) {
                            ids.push({
                                hphm: page[i]['license_plate_number'].substr(0, 2),
                                hphms: page[i]['license_plate_number'],
                                engineno: page[i]['engine_number'],
                                classno: page[i]['frame_number']
                            });
                        }

                        // return;false;
                        Fast.api.ajax({
                            url: 'riskcontrol/Peccancy/sendMessage',
                            data: {ids}
                        }, function (data, ret) {
                            Layer.close(closeLay);

                            table.bootstrapTable('refresh');
                        })
                    }


                })
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
            formatter: {
                normal: function (value, row, index) {
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
                },
                status: function (value, row, index) {
                    if(!value){
                        return '-';
                    }

                    value==1?value='已处理':value='未处理';
                    var custom = {'已处理': 'success', '未处理': 'danger'};
                    if (typeof this.custom !== 'undefined') {
                        custom = $.extend(custom, this.custom);
                    }
                    this.custom = custom;

                    this.icon = 'fa fa-circle';
                    return Table.api.formatter.normal.call(this, value, row, index);
                },
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