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

            $('ul.nav-tabs li a[data-toggle="tab"]').each(function () {
                $(this).trigger("shown.bs.tab");
            })
        },

        choose_stock: function () {

            // $(".btn-add").data("area", ["300px","200px"]);
            Table.api.init({});
            Form.api.bindevent($("form[role=form]"), function (data, ret) {
                // console.log(data);

                //这里是表单提交处理成功后的回调函数，接收来自php的返回数据
                Fast.api.close(data);
                // console.log(data);
                Toastr.success("成功");
            }, function (data, ret) {
                Toastr.success("失败");

            });
            // Controller.api.bindevent();
            // console.log(Config.id);


        },

        table: {

            prepare_lift_car: function () {
                // 表格1
                var prepareLiftCar = $("#prepareLiftCar");
                prepareLiftCar.on('load-success.bs.table', function (e, data) {
                    console.log(data.total);
                    $('#badge_prepare').text(data.total);

                });
                prepareLiftCar.on('post-body.bs.table', function (e, settings, json, xhr) {
                    $(".btn-chooseStock").data("area", ["60%", "60%"]);
                    $(".btn-showOrder").data("area", ["95%", "95%"]);
                });
                // 初始化表格
                prepareLiftCar.bootstrapTable({
                    url: "newcars/Newcarscustomer/prepare_lift_car",
                    extend: {
                        index_url: 'order/salesorder/index',
                        add_url: 'order/salesorder/add',
                        // edit_url: 'order/salesorder/edit',
                        // del_url: 'order/salesorder/del',
                        multi_url: 'order/salesorder/multi',
                        table: 'sales_order',
                    },
                    toolbar: '#toolbar1',
                    pk: 'id',
                    sortName: 'id',
                    searchFormVisible: true,
                    columns: [
                        [
                            {checkbox: true},
                            {field: 'id', title: __('Id'),operate:false},
                            {field: 'order_no', title: __('订单编号')},
                            {field: 'models.name', title: __('销售车型')},
                            {field: 'admin.nickname', title: __('销售员')},
                            {field: 'username', title: __('Username')},
                            {field: 'phone', title: __('电话号码')},
                            {field: 'id_card', title: __('身份证号')},
                            {field: 'planacar.payment', title: __('首付'),operate:false},
                            {field: 'planacar.monthly', title: __('月供'),operate:false},
                            {field: 'planacar.nperlist', title: __('期数'),operate:false},
                            {field: 'planacar.margin', title: __('保证金'),operate:false},
                            {field: 'planacar.tail_section', title: __('尾款'),operate:false},
                            {field: 'planacar.gps', title: __('GPS(元)'),operate:false},
                            {
                                field: 'createtime',
                                title: __('订车时间'),
                                formatter: Table.api.formatter.datetime,
                                operate: false
                            },
                            {
                                field: 'operate',
                                title: __('Operate'),
                                table: prepareLiftCar,
                                events: Controller.api.events.operate,
                                formatter: Controller.api.formatter.operate,
                                buttons: [
                                    {
                                        name: 'take_the_car', text: '提交给销售，通知客户补全资料提车', title: '提交给销售，通知客户补全资料提车', icon: 'fa fa-share', extend: 'data-toggle="tooltip"', classname: 'btn btn-xs btn-info btn-submit_newcustomer',
                                        hidden: function (row) {  /**提交给销售，通知客户补全资料提车 */

                                            if (row.review_the_data == 'take_the_car') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'take_the_data') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'send_the_car') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'inform_the_tube') {
                                                return true;
                                            }
                                        }
                                    },
                                    {
                                        name: 'take_the_data', icon: 'fa fa-check-circle', text: '销售正在录入客户资料', classname: ' text-info ',
                                        hidden: function (row) {  /**销售正在录入客户资料 */
                                            if (row.review_the_data == 'take_the_data') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'take_the_car') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'send_the_car') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'inform_the_tube') {
                                                return true;
                                            }
                                        }
                                    },
                                    {
                                        name: 'inform_the_tube', icon: 'fa fa-check-circle', text: '销售已录入客户资料，准备提交', classname: ' text-info ',
                                        hidden: function (row) {  /**销售已录入客户资料，准备提交 */
                                            if (row.review_the_data == 'inform_the_tube') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'take_the_car') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'send_the_car') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'take_the_data') {
                                                return true;
                                            }
                                        }
                                    },
                                    {
                                        name: 'send_the_car', text: '确认提车', title: '确认提车', icon: 'fa fa-share', extend: 'data-toggle="tooltip"', classname: 'btn btn-xs btn-info btn-sendcar',
                                        hidden: function (row) {  /**确认提车 */
                                            if (row.review_the_data == 'send_the_car') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'take_the_data') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'take_the_car') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'inform_the_tube') {
                                                return true;
                                            }
                                        }
                                    },
                                    {
                                        name: 'look',
                                        text: '查看客户详细资料',
                                        title: '查看客户详细资料',
                                        icon: 'fa fa-eye',
                                        classname: 'btn btn-xs btn-info btn-dialog btn-showOrder',
                                        url: 'newcars/newcarscustomer/show_order',
                                    }
                                ]
                            }
                        ]
                    ]
                });
                // 为表格1绑定事件
                Table.api.bindevent(prepareLiftCar);

                //风控选完库存推送---车管
                goeasy.subscribe({
                    channel: 'demo-newchoose_stock',
                    onMessage: function(message){
                        Layer.alert('新消息：'+message.content,{ icon:0},function(index){
                            Layer.close(index);
                            $(".btn-refresh").trigger("click");
                        });
                        
                    }
                });

                //销售补全资料推送---车管----提车
                goeasy.subscribe({
                    channel: 'demo-newsend_car',
                    onMessage: function(message){
                        Layer.alert('新消息：'+message.content,{ icon:0},function(index){
                            Layer.close(index);
                            $(".btn-refresh").trigger("click");
                        });
                        
                    }
                });
            },
            already_lift_car: function () {

                // 表格2
                var alreadyLiftCar = $("#alreadyLiftCar");
                alreadyLiftCar.on('load-success.bs.table', function (e, data) {
                    console.log(data.total);
                    $('#badge_already').text(data.total);

                });
                alreadyLiftCar.on('post-body.bs.table', function (e, settings, json, xhr) {
                    $(".btn-showOrderAndStock").data("area", ["95%", "95%"]);
                });
                // 初始化表格
                alreadyLiftCar.bootstrapTable({
                    url: 'newcars/Newcarscustomer/already_lift_car',
                    extend: {
                        index_url: 'order/salesorder/index',
                        add_url: 'order/salesorder/add',
                        // edit_url: 'order/salesorder/edit',
                        // del_url: 'order/salesorder/del',
                        multi_url: 'order/salesorder/multi',
                        table: 'sales_order',
                    },
                    toolbar: '#toolbar2',
                    pk: 'id',
                    sortName: 'id',
                    searchFormVisible: true,
                    columns: [
                        [
                            {checkbox: true},
                            {field: 'id', title: __('Id'),operate:false},
                            {field: 'order_no', title: __('订单编号')},
                            {field: 'models.name', title: __('销售车型')},
                            {field: 'username', title: __('Username')},
                            {field: 'phone', title: __('电话号码')},
                            {field: 'id_card', title: __('身份证号')},
                            {field: 'planacar.payment', title: __('首付'),operate:false},
                            {field: 'planacar.monthly', title: __('月供'),operate:false},
                            {field: 'planacar.nperlist', title: __('期数'),operate:false},
                            {field: 'planacar.margin', title: __('保证金'),operate:false},
                            {field: 'planacar.tail_section', title: __('尾款'),operate:false},
                            {field: 'planacar.gps', title: __('GPS(元)'),operate:false},
                            {field: 'newinventory.licensenumber', title: __('车牌号')},
                            {field: 'newinventory.frame_number', title: __('车架号')},
                            {field: 'newinventory.engine_number', title: __('发动机号')},
                            {field: 'newinventory.household', title: __('所属户')},
                            {field: 'newinventory.4s_shop', title: __('4S店')},
                            {
                                field: 'createtime',
                                title: __('订车时间'),
                                formatter: Table.api.formatter.datetime,
                                operate: false
                            },
                            {
                                field: 'delivery_datetime',
                                title: __('提车时间'),
                                formatter: Table.api.formatter.datetime,
                                operate: false
                            },
                            {
                                field: 'operate',
                                title: __('Operate'),
                                table: alreadyLiftCar,
                                events: Table.api.events.operate,
                                formatter: Table.api.formatter.operate,
                                buttons: [
                                    {
                                        name: 'look',
                                        text: '查看客户详细资料',
                                        title: '查看客户详细资料',
                                        icon: 'fa fa-eye',
                                        classname: 'btn btn-xs btn-info btn-dialog btn-showOrderAndStock',
                                        url: 'newcars/newcarscustomer/show_order_and_stock',
                                    }
                                ]
                            }
                        ]
                    ]
                });
                // 为表格2绑定事件
                Table.api.bindevent(alreadyLiftCar);

                alreadyLiftCar.on('load-success.bs.table', function (e, data) {
                    $('#assigned-customer').text(data.total);

                })

            }


        },
        add: function () {
            Controller.api.bindevent();

        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                $(document).on('click', "input[name='row[ismenu]']", function () {
                    var name = $("input[name='row[name]']");
                    name.prop("placeholder", $(this).val() == 1 ? name.data("placeholder-menu") : name.data("placeholder-node"));
                });
                $("input[name='row[ismenu]']:checked").trigger("click");
                Form.api.bindevent($("form[role=form]"));
            },
            events: {
                operate: {
                    //新车提交销售，让销售通知客户进行提车
                    'click .btn-submit_newcustomer': function (e, value, row, index) {

                        e.stopPropagation();
                        e.preventDefault();
                        var that = this;
                        var top = $(that).offset().top - $(window).scrollTop();
                        var left = $(that).offset().left - $(window).scrollLeft() - 260;
                        if (top + 154 > $(window).height()) {
                            top = top - 154;
                        }
                        if ($(window).width() < 480) {
                            top = left = undefined;
                        }
                        Layer.confirm(
                            __('是否提交销售，通知客户进行补全资料进行提车?'),
                            { icon: 3, title: __('Warning'), offset: [top, left], shadeClose: true },

                            function (index) {
                                var table = $(that).closest('table');
                                var options = table.bootstrapTable('getOptions');


                                Fast.api.ajax({

                                    url: 'newcars/newcarscustomer/newcustomer',
                                    data: {id: row[options.pk]}
 
                                }, function (data, ret) {

                                    Toastr.success('操作成功');
                                    Layer.close(index);
                                    table.bootstrapTable('refresh');
                                    return false;
                                }, function (data, ret) {
                                    //失败的回调
                                    Toastr.success(ret.msg);

                                    return false;
                                });


                            }
                        );

                    },
                    //确认提车
                    'click .btn-sendcar': function (e, value, row, index) {

                        e.stopPropagation();
                        e.preventDefault();
                        var that = this;
                        var top = $(that).offset().top - $(window).scrollTop();
                        var left = $(that).offset().left - $(window).scrollLeft() - 260;
                        if (top + 154 > $(window).height()) {
                            top = top - 154;
                        }
                        if ($(window).width() < 480) {
                            top = left = undefined;
                        }
                        Layer.confirm(
                            __('是否确认提车?'),
                            { icon: 3, title: __('Warning'), offset: [top, left], shadeClose: true },

                            function (index) {
                                var table = $(that).closest('table');
                                var options = table.bootstrapTable('getOptions');


                                Fast.api.ajax({

                                    url: 'newcars/newcarscustomer/sendcar',
                                    data: {id: row[options.pk]}
 
                                }, function (data, ret) {

                                    Toastr.success('操作成功');
                                    Layer.close(index);
                                    table.bootstrapTable('refresh');
                                    return false;
                                }, function (data, ret) {
                                    //失败的回调
                                    Toastr.success(ret.msg);

                                    return false;
                                });


                            }
                        );

                    }
                }
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
            }
        }

    };

    return Controller;
});