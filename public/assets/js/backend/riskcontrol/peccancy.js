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
            // $('ul.nav-tabs li.active a[data-toggle="tab"]').trigger("shown.bs.tab");

            $('ul.nav-tabs li a[data-toggle="tab"]').each(function () {
                $(this).trigger("shown.bs.tab");
            });


        },
        table: {
            /**
             * 待发送给客服
             */
            prepare_send: function () {

                var table = $("#prepareSend");



                table.on('post-body.bs.table', function (e, settings, json, xhr) {
                    $(".btn-detail").data("area", ["90%", "90%"]);
                    $(".btn-editone").data("area", ["70%", "70%"]);

                });
                $.fn.bootstrapTable.locales[Table.defaults.locale]['formatSearch'] = function () {
                    return "快速搜索：客户姓名,车牌号";
                };
                table.on('load-success.bs.table', function (e, data) {

                    $('#prepare_send_total').text(data.total);

                })
                // 初始化表格
                table.bootstrapTable({
                    url: 'riskcontrol/Peccancy/prepare_send',
                    extend: {
                        index_url: 'riskcontrol/peccancy/index',
                        add_url: 'riskcontrol/peccancy/add',
                        edit_url: 'riskcontrol/peccancy/edit',
                        del_url: 'riskcontrol/peccancy/del',
                        multi_url: 'riskcontrol/peccancy/multi',
                        table: 'violation_inquiry',
                    },
                    pk: 'id',
                    toolbar: '#toolbar1',
                    sortName: 'id',
                    searchFormVisible: true,
                    columns: [
                        [
                            {checkbox: true},
                            {field: 'id', title: 'ID', operate: false},
                            {field: 'username', title: __('Username')},
                            {field: 'phone', title: __('Phone')},
                            {field: 'models', title: __('Models')},
                            {field: 'license_plate_number', title: __('License_plate_number')},
                            {field: 'frame_number', title: __('Frame_number')},
                            {field: 'engine_number', title: __('Engine_number')},
                            {field: 'total_deduction', title: __('Total_deduction'), operate: 'BETWEEN',formatter:Controller.api.formatter.fen},
                            {field: 'total_fine', title: __('Total_fine'), operate: false,formatter:Controller.api.formatter.fen},
                            {field: 'query_times', title: __('Query_times'), operate: false},
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
                            {
                                field: 'start_renttime',
                                title: __('起租时间'),
                                operate: 'RANGE',
                                addclass: 'datetimerange',
                                formatter: Table.api.formatter.datetime,
                                datetimeFormat: "YYYY-MM-DD",

                            },
                            {
                                field: 'end_renttime',
                                title: __('退租时间'),
                                operate: false,
                                formatter: Table.api.formatter.datetime,
                                datetimeFormat: "YYYY-MM-DD",

                            },
                            {
                                field:'strong_deadtime',
                                title:'交强险截止时间',
                                operate: false,
                                formatter: Table.api.formatter.datetime,
                                datetimeFormat: "YYYY-MM-DD",
                            },
                            {
                                field:'business_deadtime',
                                title:'商业险截止时间',
                                operate: false,
                                formatter: Table.api.formatter.datetime,
                                datetimeFormat: "YYYY-MM-DD",
                            },
                            {
                                field: 'peccancy_status',
                                title: __('Peccancy_status'),
                                formatter: Controller.api.formatter.status,
                                searchList: {
                                    "1": __('已处理'),
                                    "2": __('未处理'),
                                },
                            },
                            {
                                field: 'final_time',
                                title: __('Final_time'),
                                operate: false,
                                addclass: 'datetimerange',
                                formatter: function (v, r, i) {
                                    if (v != null) {
                                        return Controller.getDateDiff(v) + '<br>' + '(' + Controller.getLocalTime(v) + ')';
                                    }

                                },
                                datetimeFormat: "YYYY-MM-DD H:m",

                            },
                            {field: 'inquiry_note', title: __('违章备注'),operate:false},
                            {
                                field: 'operate',
                                title: __('Operate'),
                                table: table,
                                events: Controller.api.events.operate,
                                formatter: Controller.api.formatter.operate
                            }
                        ]
                    ]
                });

                // 为表格绑定事件
                Table.api.bindevent(table);

                Controller.api.inquire_violation('.btn-peccancy', table);

                $(document).on('click', '.btn-share', function () {
                    var ids = Table.api.selectedids(table);

                    ids = JSON.stringify(ids);
                    Layer.confirm(
                        __('确定发送给客服?', ids.length),
                        {icon: 3, title: __('Warning'), offset: 0, shadeClose: true},
                        function (index) {
                            Fast.api.ajax({
                                url: 'riskcontrol/Peccancy/sendCustomer',
                                data: {ids:ids}

                            }, function (data, ret) {

                               var pre = $('#already_send_total').text();
                               pre = parseInt(pre);

                                $('#already_send_total').text(pre+parseInt(data));
                                Layer.close(index);
                                table.bootstrapTable('refresh');

                            })

                        }
                    );
                })


            },

            /**
             * 已发送给客服
             */
            already_send: function () {

                var alreadySend = $("#alreadySend");
                alreadySend.on('post-body.bs.table', function (e, settings, json, xhr) {
                    $(".btn-detail").data("area", ["90%", "90%"]);

                });
                $.fn.bootstrapTable.locales[Table.defaults.locale]['formatSearch'] = function () {
                    return "快速搜索：客户姓名,车牌号";
                };
                alreadySend.on('load-success.bs.table', function (e, data) {

                    $('#already_send_total').text(data.total);

                })
                // 初始化表格
                alreadySend.bootstrapTable({
                    url: 'riskcontrol/Peccancy/already_send',
                    extend: {
                        index_url: 'riskcontrol/peccancy/index',
                        add_url: 'riskcontrol/peccancy/add',
                        edit_url: 'riskcontrol/peccancy/edit',
                        del_url: 'riskcontrol/peccancy/del',
                        multi_url: 'riskcontrol/peccancy/multi',
                        table: 'violation_inquiry',
                    },
                    pk: 'id',
                    toolbar: '#toolbar2',
                    sortName: 'id',
                    searchFormVisible: true,
                    columns: [
                        [
                            {checkbox: true},
                            {field: 'id', title: 'ID', operate: false},
                            {field: 'username', title: __('Username')},
                            {field: 'phone', title: __('Phone')},
                            {field: 'models', title: __('Models')},
                            {field: 'license_plate_number', title: __('License_plate_number')},
                            {field: 'frame_number', title: __('Frame_number')},
                            {field: 'engine_number', title: __('Engine_number')},
                            {field: 'total_deduction', title: __('Total_deduction'), operate: 'BETWEEN'},
                            {field: 'total_fine', title: __('Total_fine'), operate: false},
                            {field: 'query_times', title: __('Query_times'), operate: false},
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
                            {
                                field: 'start_renttime',
                                title: __('起租时间'),
                                operate: 'RANGE',
                                addclass: 'datetimerange',
                                formatter: Controller.api.formatter.datetime,
                                datetimeFormat: "YYYY-MM-DD",

                            },
                            {
                                field: 'end_renttime',
                                title: __('退租时间'),
                                operate: false,
                                formatter: Table.api.formatter.datetime,
                                datetimeFormat: "YYYY-MM-DD",

                            },
                            {
                                field: 'peccancy_status',
                                title: __('Peccancy_status'),
                                formatter: Controller.api.formatter.status,
                                searchList: {
                                    "1": __('已处理'),
                                    "2": __('未处理'),
                                },
                            },
                            {
                                field: 'final_time',
                                title: __('Final_time'),
                                operate: false,
                                addclass: 'datetimerange',
                                formatter: Table.api.formatter.datetime,
                                datetimeFormat: "YYYY-MM-DD H:m",

                            },
                            {
                                field: 'customer_status',
                                title: __('是否发送给客服'),
                                formatter: function (value, row, index) {

                                    value = '已发送';

                                    var custom = {'已发送': 'success'};
                                    if (typeof this.custom !== 'undefined') {
                                        custom = $.extend(custom, this.custom);
                                    }
                                    this.custom = custom;

                                    this.icon = 'fa fa-circle';
                                    return Table.api.formatter.normal.call(this, value, row, index);
                                },
                                operate: false
                            },
                            {
                                field: 'customer_time',
                                title: __('发送给客服时间'),
                                operate: false,
                                formatter: Table.api.formatter.datetime,
                                datetimeFormat: "YYYY-MM-DD",

                            },
                            {field: 'inquiry_note', title: __('违章备注'),operate:false},
                            {
                                field: 'operate',
                                title: __('Operate'),
                                table: alreadySend,
                                events: Controller.api.events.operate,
                                formatter: Controller.api.formatter.operate
                            }
                        ]
                    ]
                });

                // 为表格绑定事件
                Table.api.bindevent(alreadySend);

                Controller.api.inquire_violation('.btn-peccancy2', alreadySend);


            },
        },

        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },

        /**
         * 格式化时间 几天前 时 分 秒
         * @param dateTimeStamp
         * @returns {*|string}
         */
        getDateDiff: function (timestamp) {
            var mistiming = Math.round(new Date() / 1000) - timestamp;
            var postfix = mistiming > 0 ? '前' : '后'
            mistiming = Math.abs(mistiming)
            var arrr = ['年', '个月', '星期', '天', '小时', '分钟', '秒'];
            var arrn = [31536000, 2592000, 604800, 86400, 3600, 60, 1];

            for (var i = 0; i < 7; i++) {
                var inm = Math.floor(mistiming / arrn[i])
                if (inm != 0) {
                    return inm + arrr[i] + postfix
                }
            }
        },

        /**
         * 时间戳格式化日期
         * @param Ns
         * @returns {string}
         */
        getLocalTime: function (nS) {
            return new Date(parseInt(nS) * 1000).toLocaleString().replace(/年|月/g, "-").replace(/日/g, " ");

        },


        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            },
            formatter: {
                normal: function (value, row, index) {
                    switch (value) {
                        case 1:
                            return "(以租代购)新车";
                        case 2:
                            return "二手车";
                        case 3:
                            return "全款车";
                        case 4:
                            return "租车";
                    }
                },
                status: function (value, row, index) {
                    if (!value) {
                        return '-';
                    }

                    value == 1 ? value = '已处理' : value = '未处理';
                    var custom = {'已处理': 'success', '未处理': 'danger'};
                    if (typeof this.custom !== 'undefined') {
                        custom = $.extend(custom, this.custom);
                    }
                    this.custom = custom;

                    this.icon = 'fa fa-circle';
                    return Table.api.formatter.normal.call(this, value, row, index);
                },

                datetime: function (value, row, index) {
                    var datetimeFormat = typeof this.datetimeFormat === 'undefined' ? 'YYYY-MM-DD HH:mm:ss' : this.datetimeFormat;
                    if (isNaN(value)) {
                        return value ? Moment(value).format(datetimeFormat) : __('None');
                    } else {
                        return value ? Moment(parseInt(value) * 1000).format(datetimeFormat) : __('None');
                    }
                },

                fen: function (value, row, index) {

                    if(value){
                        return "<span class='text-danger'>"+value+"</span>";
                    }
                   return value == null ? '-' : "<span class='text-success'><strong>"+value+"</strong></span>";

                },

                operate: function (value, row, index) {
                    var table = this.table;
                    // 操作配置
                    var options = table ? table.bootstrapTable('getOptions') : {};
                    // 默认按钮组
                    var buttons = $.extend([], this.buttons || []);

                    buttons.push({
                        name: 'edit',
                        text:'编辑',
                        icon: 'fa fa-pencil',
                        title: __('Edit'),
                        extend: 'data-toggle="tooltip"',
                        classname: 'btn btn-xs btn-success btn-editone',
                        url: 'riskcontrol/peccancy/edit'
                    });

                    buttons.push({
                        name: '查询违章',
                        text: '查询违章',
                        icon: 'fa fa-search',
                        title: __('查看违章详情'),
                        // extend: 'data-toggle="tooltip"',
                        classname: 'btn btn-xs btn-info btn-search',
                    });

                    if (row && row.total_fine == 0 && row.total_deduction == 0) {

                        return Table.api.buttonlink(this, buttons, value, row, index, 'operate');
                    }

                    if (row && !row.total_fine && !row.total_deduction) {
                        return Table.api.buttonlink(this, buttons, value, row, index, 'operate');
                    }

                    buttons.push({
                        name: 'detail',
                        text: '查看违章详情',
                        icon: 'fa fa-eye',
                        title: __('查看违章详情'),
                        // extend: 'data-toggle="tooltip"',
                        classname: 'btn btn-xs btn-primary btn-detail',
                    });


                    return Table.api.buttonlink(this, buttons, value, row, index, 'operate');
                }
            },
            events: {
                operate: {
                    /**
                     * 查看违章详情
                     * @param e
                     * @param value
                     * @param row
                     * @param index
                     */
                    'click .btn-detail': function (e, value, row, index) {
                        e.stopPropagation();
                        e.preventDefault();
                        var table = $(this).closest('table');
                        var options = table.bootstrapTable('getOptions');
                        var ids = row[options.pk];
                        row = $.extend({}, row ? row : {}, {ids: ids});
                        var url = 'riskcontrol/Peccancy/details';
                        Fast.api.open(Table.api.replaceurl(url, row, table), __('查看违章详情'), $(this).data() || {});
                    },
                    /**
                     * 查询违章
                     * @param e
                     * @param value
                     * @param row
                     * @param index
                     */
                    'click .btn-search': function (e, value, row, index) {
                        e.stopPropagation();
                        e.preventDefault();
                        var that = this;
                        Layer.confirm('是否查询违章?', {icon: 3, title:'提示'}, function(index){



                            if(!row['license_plate_number'] || row['license_plate_number']==''){
                                Layer.msg('请补全车牌号');
                                return
                            }

                            if(!row['engine_number'] || row['engine_number']==''){
                                Layer.msg('请补全发动机号');
                                return
                            }

                            if(!row['frame_number'] ||row['frame_number']==''){
                                Layer.msg('请补全车架号');
                                return
                            }


                            var table = $(that).closest('table');
                            var ids = [{
                                hphm: row['license_plate_number'].substr(0, 2),
                                hphms: row['license_plate_number'],
                                engineno: row['engine_number'],
                                classno: row['frame_number']
                            }];

                            Fast.api.ajax({
                                url: 'riskcontrol/Peccancy/sendMessagePerson',
                                data: {ids}

                            }, function (data, ret) {

                                Layer.close(index);
                                table.bootstrapTable('refresh');


                            })


                        });



                    },
                    'click .btn-editone': function (e, value, row, index) {
                        e.stopPropagation();
                        e.preventDefault();
                        var table = $(this).closest('table');
                        var options = table.bootstrapTable('getOptions');
                        var ids = row[options.pk];
                        row = $.extend({}, row ? row : {}, {ids: ids});
                        var url = options.extend.edit_url;
                        Fast.api.open(Table.api.replaceurl(url, row, table), __('Edit'), $(this).data() || {});

                    },

                }
            },
            /**
             * 得到选中行信息
             * @param table
             * @returns {*}
             */
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
            },


            /**
             * 批量查询违章
             */
            inquire_violation: function (clickobj, table) {
                $(document).on("click", clickobj, function () {
                    var ids = [];
                    var tableRow = Controller.api.selectIdsRow(table);//获取选中的行数据
                    var flag = -1;
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
                            for (var i in tableRow) {

                                if(!tableRow[i]['license_plate_number'] || tableRow[i]['license_plate_number']==''){
                                    flag = -2;
                                    break;
                                }

                                if(!tableRow[i]['engine_number'] || tableRow[i]['engine_number']==''){
                                    flag = -3;
                                    break;
                                }

                                if(!tableRow[i]['frame_number'] || tableRow[i]['frame_number']==''){
                                    flag = -4;
                                    break;
                                }

                                ids.push({
                                    hphm: tableRow[i]['license_plate_number'].substr(0, 2),
                                    hphms: tableRow[i]['license_plate_number'],
                                    engineno: tableRow[i]['engine_number'],
                                    classno: tableRow[i]['frame_number']
                                })
                            }

                            if(flag==-2){
                                layer.msg('选中行中有数据没有车牌号，请添加后查询');
                                return;
                            }

                            if(flag==-3){
                                layer.msg('选中行中有数据没有发动机号，请添加后查询');
                                return;
                            }

                            if(flag==-4){
                                layer.msg('选中行中有数据没有车架号，请添加后查询');
                                return;
                            }


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
                            for (var i in page) {

                                if(!page[i]['license_plate_number'] || page[i]['license_plate_number']==''){
                                    flag = -2;
                                    break;
                                }

                                if(!page[i]['engine_number'] || page[i]['engine_number']==''){
                                    flag = -3;
                                    break;
                                }

                                if(!page[i]['frame_number'] || page[i]['frame_number']==''){
                                    flag = -4;
                                    break;
                                }


                                ids.push({
                                    hphm: page[i]['license_plate_number'].substr(0, 2),
                                    hphms: page[i]['license_plate_number'],
                                    engineno: page[i]['engine_number'],
                                    classno: page[i]['frame_number']
                                });
                            }

                            if(flag==-2){
                                layer.msg('本页中有数据没有车牌号，请添加后查询');
                                return;
                            }

                            if(flag==-3){
                                layer.msg('本页中有数据没有发动机号，请添加后查询');
                                return;
                            }

                            if(flag==-4){
                                layer.msg('本页中有数据没有车架号，请添加后查询');
                                return;
                            }

                            Fast.api.ajax({
                                url: 'riskcontrol/Peccancy/sendMessage',
                                data: {ids}
                            }, function (data, ret) {
                                console.log(data);
                                Layer.close(closeLay);

                                table.bootstrapTable('refresh');
                            })
                        }


                    })
                });
            },

        }
    };


    return Controller;
});