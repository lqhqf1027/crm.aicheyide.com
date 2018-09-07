define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var goeasy = new GoEasy({
        appkey: 'BC-04084660ffb34fd692a9bd1a40d7b6c2'
    });

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
            $('ul.nav-tabs li.active a[data-toggle="tab"]').trigger("shown.bs.tab");


        },


        table: {

            newcar_entry: function () {
                // 新车录入定金
                var newcarEntry = $("#newcarEntry");
               
                // 初始化表格
                newcarEntry.bootstrapTable({
                    url: 'backoffice/carreservation/newcarEntry',
                    extend: {
                        // edit_url: 'backoffice/carreservation/newactual_amount',
                        // table: 'sales_order',
                    },
                    toolbar: '#toolbar1',
                    pk: 'id',
                    sortName: 'id',
                    searchFormVisible: true,
                    columns: [
                        [
                            {checkbox: true},
                            {field: 'id', title: '编号',operate:false},
                            {field: 'createtime', title: __('订车日期'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime },
                            {field: 'admin.nickname', title: __('销售员')},
                            {field: 'username', title: __('客户姓名')},
                            {field: 'id_card', title: __('身份证号')},
                            {field: 'city', title: __('居住地址'),operate:false},
                            {field: 'detailed_address', title: __('详细地址'),operate:false},
                            {field: 'phone', title: __('联系电话')},
                            {field: 'models.name', title: __('订车车型')},
                            {field: 'planacar.payment', title: __('首付(元)'),operate:false},
                            {field: 'planacar.monthly', title: __('月供(元)'),operate:false},
                            {field: 'planacar.nperlist', title: __('期数'),operate:false},
                            {field: 'planacar.margin', title: __('保证金(元)'),operate:false},
                            {field: 'planacar.tail_section', title: __('尾款(元)'),operate:false},
                            {field: 'planacar.gps', title: __('GPS(服务费)'),operate:false},
                            {field: 'car_total_price', title: __('车款总价(元)'),operate:false},
                            {field: 'downpayment', title: __('首期款(元)'),operate:false},
                            {field: 'newinventory.household', title: __('行驶证所有户')},
                            {field: 'newinventory.4s_shop', title: __('4S店')},
                            {
                                field: 'operate', title: __('Operate'), table: newcarEntry,
                                buttons: [
                                    {
                                        name: 'newactual_amount', text: '录入实际订车金额', title: __('录入实际订车金额'), icon: 'fa fa-pencil', extend: 'data-toggle="tooltip"', classname: 'btn btn-xs btn-danger btn-newactual_amount',
                                        url: 'backoffice/carreservation/newactual_amount',
                                        hidden: function (row) { /**录入实际订车金额 */
                                            if (row.review_the_data == 'inhouse_handling') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'send_car_tube') {
                                                return true;
                                            }
                                        }
                            
                                    },
                                    {
                                        name: 'send_car_tube', text: '已录入实际订车金额',
                                        hidden: function (row) { /**已录入实际订车金额 */
                                            if (row.review_the_data == 'send_car_tube') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'inhouse_handling') {
                                                return true;
                                            }
                                        }
                                    },
                                ],

                                events: Controller.api.events.operate,
                                formatter: Controller.api.formatter.operate
                            }
                        ]
                    ]
                });
                // 为表格1绑定事件
                Table.api.bindevent(newcarEntry);

                //数据实时统计
                newcarEntry.on('load-success.bs.table',function(e,data){ 

                    $(".btn-newactual_amount").data("area", ["50%", "40%"]);
                    var newcarEntry =  $('#badge_newcar_entry').text(data.total); 
                    newcarEntry = parseInt($('#badge_newcar_entry').text());
                    
                   
                })

                //销售推送
                goeasy.subscribe({
                    channel: 'demo-sales',
                    onMessage: function (message) {
                        Layer.alert('新消息：' + message.content, {icon: 0}, function (index) {
                            Layer.close(index);
                            $(".btn-refresh").trigger("click");
                        });

                    }
                });


            },
            secondcar_entry: function () {
                // 二手车录入定金
                var secondcarEntry = $("#secondcarEntry");
               
                // 初始化表格
                secondcarEntry.bootstrapTable({
                    url: 'backoffice/carreservation/secondcarEntry',
                    extend: {
                        // edit_url: 'backoffice/carreservation/secondactual_amount',
                        table: 'second_sales_order',
                    },
                    toolbar: '#toolbar2',
                    pk: 'id',
                    sortName: 'id',
                    searchFormVisible: true,
                    columns: [
                        [
                            {checkbox: true},
                            {field: 'id', title: '编号',operate:false},
                            {field: 'createtime', title: __('订车日期')},
                            {field: 'admin.nickname', title: __('销售员')},
                            {field: 'plansecond.companyaccount', title: __('所属公司户')},
                            {field: 'username', title: __('客户姓名')},
                            {field: 'id_card', title: __('身份证号')},
                            {field: 'city', title: __('居住地址'),operate:false},
                            {field: 'detailed_address', title: __('详细地址'),operate:false},
                            {field: 'phone', title: __('联系电话')},
                            {field: 'models.name', title: __('订车车型')},
                            {field: 'plansecond.newpayment', title: __('首付(元)'),operate:false},
                            {field: 'plansecond.monthlypaymen', title: __('月供(元)'),operate:false},
                            {field: 'plansecond.periods', title: __('期数'),operate:false},
                            {field: 'plansecond.bond', title: __('保证金(元)'),operate:false},
                            {field: 'plansecond.tailmoney', title: __('尾款(元)'),operate:false},
                            {field: 'plansecond.totalprices', title: __('车款总价(元)'),operate:false},
                            {field: 'downpayment', title: __('首期款(元)'),operate:false},
                            {
                                field: 'operate', title: __('Operate'), table: secondcarEntry,
                                buttons: [
                                    {
                                        name: 'secondactual_amount', text: '录入实际订车金额', title: '录入实际订车金额', icon: 'fa fa-pencil', extend: 'data-toggle="tooltip"', classname: 'btn btn-xs btn-info btn-secondactual_amount',
                                        url: 'backoffice/carreservation/secondactual_amount',
                                       
                                        hidden: function (row) { /**录入实际订车金额 */
                                            if (row.review_the_data == 'is_reviewing_true') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'send_car_tube') {
                                                return true;
                                            }
                                        }
                                    },
                                    {
                                        name: 'send_car_tube', text: '已录入实际订车金额',
                                        hidden: function (row) {  /**已录入实际订车金额 */
                                            if (row.review_the_data == 'send_car_tube') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_true') {
                                                return true;
                                            }
                                        }
                                    }
                                ],
                                events: Controller.api.events.operate,

                                formatter: Controller.api.formatter.operate

                            }
                        ]
                    ]
                });
                // 为表格1绑定事件
                Table.api.bindevent(secondcarEntry);

                //数据实时统计
                secondcarEntry.on('load-success.bs.table',function(e,data){ 

                    $(".btn-secondactual_amount").data("area", ["50%", "40%"]);
                    var secondcarEntry =  $('#badge_secondcar_entry').text(data.total); 
                    secondcarEntry = parseInt($('#badge_secondcar_entry').text());
                    
                   
                })

                //销售推送
                goeasy.subscribe({
                    channel: 'demo-second_backoffice',
                    onMessage: function (message) {
                        Layer.alert('新消息：' + message.content, {icon: 0}, function (index) {
                            Layer.close(index);
                            $(".btn-refresh").trigger("click");
                        });

                    }
                });

            },
            fullcar_entry: function () {
                // 全款车录入定金
                var fullcarEntry = $("#fullcarEntry");
               
                // 初始化表格
                fullcarEntry.bootstrapTable({
                    url: 'backoffice/carreservation/fullcarEntry',
                    extend: {
                        // edit_url: 'backoffice/carreservation/secondactual_amount',
                        table: 'full_parment_order',
                    },
                    toolbar: '#toolbar3',
                    pk: 'id',
                    sortName: 'id',
                    searchFormVisible: true,
                    columns: [
                        [
                            {checkbox: true},
                            {field: 'id', title: '编号',operate:false},
                            {field: 'createtime', title: __('订车日期')},
                            {field: 'admin.nickname', title: __('销售员')},
                            {field: 'models.name', title: __('订车车型')},
                            {field: 'planfull.full_total_price', title: __('车款总价(元)'),operate:false},
                            {field: 'username', title: __('客户姓名')},
                            {field: 'id_card', title: __('身份证号')},
                            {field: 'phone', title: __('联系电话')},
                            {field: 'city', title: __('居住地址'),operate:false},
                            {field: 'detailed_address', title: __('详细地址'),operate:false},
                           
                            {
                                field: 'operate', title: __('Operate'), table: fullcarEntry,
                                buttons: [
                                    {
                                        name: 'fullactual_amount', text: '录入实际订车金额', title: '录入实际订车金额', icon: 'fa fa-pencil', extend: 'data-toggle="tooltip"', classname: 'btn btn-xs btn-info btn-fullactual_amount',
                                        url: 'backoffice/carreservation/fullactual_amount',
                                       
                                        hidden: function (row) { /**录入实际订车金额 */
                                            if (row.review_the_data == 'inhouse_handling') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_true') {
                                                return true;
                                            }
                                        }
                                    },
                                    {
                                        name: 'send_car_tube', text: '已录入实际订车金额',
                                        hidden: function (row) {  /**已录入实际订车金额 */
                                            if (row.review_the_data == 'is_reviewing_true') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'inhouse_handling') {
                                                return true;
                                            }
                                        }
                                    }
                                ],
                                events: Controller.api.events.operate,

                                formatter: Controller.api.formatter.operate

                            }
                        ]
                    ]
                });
                // 为表格1绑定事件
                Table.api.bindevent(fullcarEntry);

                //数据实时统计
                fullcarEntry.on('load-success.bs.table',function(e,data){ 

                    $(".btn-fullactual_amount").data("area", ["50%", "40%"]);
                    var fullcarEntry =  $('#badge_fullcar_entry').text(data.total); 
                    fullcarEntry = parseInt($('#badge_fullcar_entry').text());
                    
                   
                })


            },


        },
        add: function () {
            Controller.api.bindevent();

        },
        edit: function () {
            Controller.api.bindevent();
        },
        newactual_amount: function () {
            Controller.api.bindevent();

            // $(".btn-add").data("area", ["300px","200px"]);
            Table.api.init({});
            Form.api.bindevent($("form[role=form]"), function (data, ret) {


                //这里是表单提交处理成功后的回调函数，接收来自php的返回数据
                Fast.api.close(data);//这里是重点
                // console.log(data);
                Toastr.success("成功");//这个可有可无
            }, function (data, ret) {


                Toastr.success("失败");

            });
            // Controller.api.bindevent();
            // console.log(Config.id);

        },
        secondactual_amount: function () {
            Controller.api.bindevent();
            
            // $(".btn-add").data("area", ["300px","200px"]);
            Table.api.init({});
            Form.api.bindevent($("form[role=form]"), function (data, ret) {


                //这里是表单提交处理成功后的回调函数，接收来自php的返回数据
                Fast.api.close(data);//这里是重点
                // console.log(data);
                Toastr.success("成功");//这个可有可无
            }, function (data, ret) {


                Toastr.success("失败");

            });
            // Controller.api.bindevent();
            // console.log(Config.id);

        },
        fullactual_amount: function () {
            Controller.api.bindevent();
            
            // $(".btn-add").data("area", ["300px","200px"]);
            Table.api.init({});
            Form.api.bindevent($("form[role=form]"), function (data, ret) {


                //这里是表单提交处理成功后的回调函数，接收来自php的返回数据
                Fast.api.close(data);//这里是重点
                // console.log(data);
                Toastr.success("成功");//这个可有可无
            }, function (data, ret) {


                Toastr.success("失败");

            });
            // Controller.api.bindevent();
            // console.log(Config.id);

        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            },
            formatter: {
                operate: function (value, row, index) {

                    var table = this.table;
                    // 操作配置
                    var options = table ? table.bootstrapTable('getOptions') : {};
                    // 默认按钮组
                    var buttons = $.extend([], this.buttons || []);


                    return Table.api.buttonlink(this, buttons, value, row, index, 'operate');
                },
            },
            
            events: {
                operate: {
                    //新车录入订车金额
                    'click .btn-newactual_amount': function (e, value, row, index) {
                        e.stopPropagation();
                        e.preventDefault();
                        var table = $(this).closest('table');
                        var options = table.bootstrapTable('getOptions');
                        var ids = row[options.pk];

                        row = $.extend({}, row ? row : {}, {ids: ids});
                        var url = 'backoffice/carreservation/newactual_amount';
                        Fast.api.open(Table.api.replaceurl(url, row, table), __('录入实际订车金额'), $(this).data() || {});
                    },
                    //二手车录入订车金额
                    'click .btn-secondactual_amount': function (e, value, row, index) {
                        e.stopPropagation();
                        e.preventDefault();
                        var table = $(this).closest('table');
                        var options = table.bootstrapTable('getOptions');
                        var ids = row[options.pk];

                        row = $.extend({}, row ? row : {}, {ids: ids});
                        var url = 'backoffice/carreservation/secondactual_amount';
                        Fast.api.open(Table.api.replaceurl(url, row, table), __('录入实际订车金额'), $(this).data() || {});
                    },
                    //全款车录入订车金额
                    'click .btn-fullactual_amount': function (e, value, row, index) {
                        e.stopPropagation();
                        e.preventDefault();
                        var table = $(this).closest('table');
                        var options = table.bootstrapTable('getOptions');
                        var ids = row[options.pk];

                        row = $.extend({}, row ? row : {}, {ids: ids});
                        var url = 'backoffice/carreservation/fullactual_amount';
                        Fast.api.open(Table.api.replaceurl(url, row, table), __('录入实际订车金额'), $(this).data() || {});
                    },
                }
            }
        }

    };
    return Controller;
});