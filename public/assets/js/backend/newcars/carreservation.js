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
            $('ul.nav-tabs li.active a[data-toggle="tab"]').trigger("shown.bs.tab");


        },

        table: {

            prepare_submit: function () {
                // 表格1
                var prepareSubmit = $("#prepareSubmit");
                prepareSubmit.on('load-success.bs.table', function (e, data) {
                    console.log(data.total);
                    $('#badge_prepare').text(data.total);

                });
                prepareSubmit.on('post-body.bs.table', function (e, settings, json, xhr) {
                    $(".btn-chooseStock").data("area", ["60%", "60%"]);
                    $(".btn-showOrder").data("area", ["95%", "95%"]);
                });

                var easy = new GoEasy({
                    appkey: 'BC-04084660ffb34fd692a9bd1a40d7b6c2'
                });

                easy.subscribe({
                    channel: 'pushCarTube',
                    onMessage: function(message){
                        Layer.alert('有<span class="text-info">1</span>条姓名为:<span class="text-info">'+message.content+"</span>的消息进入,请注意查看",{ icon:0},function(index){
                            Layer.close(index);
                            $(".btn-refresh").trigger("click");
                        });

                    }
                });

                // 初始化表格
                prepareSubmit.bootstrapTable({
                    url: "newcars/carreservation/prepare_submit",
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
                    columns: [
                        [
                            {checkbox: true},
                            {field: 'id', title: '编号'},
                            {field: 'createtime', title: __('订车日期')},
                            {field: 'household', title: __('公司')},
                            {field: 'sales_name', title: __('销售员')},
                            {field: 'username', title: __('客户姓名')},
                            {field: 'id_card', title: __('身份证号')},
                            {field: 'detailed_address', title: __('地址')},
                            {field: 'phone', title: __('联系电话')},
                            {field: 'models_name', title: __('订车车型')},
                            {field: 'payment', title: __('首付(元)')},
                            {field: 'monthly', title: __('月供(元)')},
                            {field: 'nperlist', title: __('期数')},
                            {field: 'margin', title: __('保证金(元)')},
                            {field: 'tail_section', title: __('尾款(元)')},
                            {field: 'gps', title: __('GPS(服务费)')},
                            {field: 'car_total_price', title: __('车款总价(元)')},
                            {field: 'downpayment', title: __('首期款(元)')},
                            {field: 'difference', title: __('差额(元)')},
                            {field: 'delivery_datetime', title: __('提车日期')},
                            {field: 'licensenumber', title: __('车牌号')},
                            {field: 'frame_number', title: __('车架号')},
                            {field: 'engine_number', title: __('发动机号')},
                            {field: 'household', title: __('行驶证所有户')},
                            {field: '4s_shop', title: __('4S店')},
                            {field: 'amount_collected', title: __('实收金额')},
                            {field: 'decorate', title: __('装饰')},

                            {
                                field: 'operate', title: __('Operate'), table: prepareSubmit,
                                buttons: [
                                    {
                                        name: 'detail',
                                        text: '提交给金融',
                                        icon: 'fa fa-pencil',
                                        title: __('Edit'),
                                        extend: 'data-toggle="tooltip"',
                                        classname: 'btn btn-xs btn-danger btn-editone',

                                    }
                                ],

                                events: Controller.api.events.operate,
                                formatter: Controller.api.formatter.operate
                            }
                        ]
                    ]
                });
                // 为表格1绑定事件
                Table.api.bindevent(prepareSubmit);

                // 批量提交金融事件
                $(".btn-mass-finance").on('click',  function () {

                    var ids = Table.api.selectedids(prepareSubmit);

                    Layer.confirm(
                        __('确定提交匹配金融?', ids.length),
                        {icon: 3, title: __('Warning'), offset: 'auto', shadeClose: true},
                        function (index) {


                            Fast.api.ajax({
                                    url: "newcars/carreservation/mass_finance",
                                    data: {id:JSON.stringify(ids)},

                                }, function (data, ret) {


                                // var easys = new GoEasy({
                                //     appkey: 'BC-04084660ffb34fd692a9bd1a40d7b6c2'
                                // });
                                //
                                // easys.publish({
                                //     channel:"pushFinance",
                                //     message:data
                                // });

                                Toastr.success("成功");
                                    Layer.close(index);
                                prepareSubmit.bootstrapTable('refresh');
                                return false;
                                }, function (data, ret) {

                                    console.log("error");
                                return false;
                                },
                            )
                        }
                    );
                });

            },
            already_submit: function () {

                // 表格2
                var alreadySubmit = $("#alreadySubmit");
                alreadySubmit.on('load-success.bs.table', function (e, data) {
                    console.log(data.total);
                    $('#badge_already').text(data.total);

                });
                alreadySubmit.on('post-body.bs.table', function (e, settings, json, xhr) {
                });
                // 初始化表格
                alreadySubmit.bootstrapTable({
                    url: "newcars/carreservation/already_submit",
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
                    columns: [
                        [
                            {checkbox: true},
                            {field: 'id', title: '编号'},
                            {field: 'createtime', title: __('订车日期')},
                            {field: 'household', title: __('公司')},
                            {field: 'sales_name', title: __('销售员')},
                            {field: 'username', title: __('客户姓名')},
                            {field: 'id_card', title: __('身份证号')},
                            {field: 'detailed_address', title: __('地址')},
                            {field: 'phone', title: __('联系电话')},
                            {field: 'models_name', title: __('订车车型')},
                            {field: 'payment', title: __('首付(元)')},
                            {field: 'monthly', title: __('月供(元)')},
                            {field: 'nperlist', title: __('期数')},
                            {field: 'margin', title: __('保证金(元)')},
                            {field: 'tail_section', title: __('尾款(元)')},
                            {field: 'gps', title: __('GPS(服务费)')},
                            {field: 'car_total_price', title: __('车款总价(元)')},
                            {field: 'downpayment', title: __('首期款(元)')},
                            {field: 'difference', title: __('差额(元)')},
                            {field: 'delivery_datetime', title: __('提车日期')},
                            {field: 'licensenumber', title: __('车牌号')},
                            {field: 'frame_number', title: __('车架号')},
                            {field: 'engine_number', title: __('发动机号')},
                            {field: 'household', title: __('行驶证所有户')},
                            {field: '4s_shop', title: __('4S店')},
                            {field: 'amount_collected', title: __('实收金额')},
                            {field: 'decorate', title: __('装饰')},

                        ]
                    ]
                });
                // 为表格2绑定事件
                Table.api.bindevent(alreadySubmit);

                // alreadyLiftCar.on('load-success.bs.table', function (e, data) {
                //     $('#assigned-customer').text(data.total);
                //
                // })

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
                    'click .btn-editone': function (e, value, row, index) {
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
                            __('确定提交匹配金融?'),
                            {icon: 3, title: __('Warning'), offset: [top, left], shadeClose: true},
                            function (index) {
                                var table = $(that).closest('table');
                                var options = table.bootstrapTable('getOptions');

                                Fast.api.ajax({
                                        url: "newcars/carreservation/matching_finance",
                                        data: {id: row[options.pk]},

                                    }, function (data, ret) {


                                    // var easys = new GoEasy({
                                    //     appkey: 'BC-04084660ffb34fd692a9bd1a40d7b6c2'
                                    // });
                                    //
                                    // easys.publish({
                                    //     channel:"pushFinance",
                                    //     message:data
                                    // });

                                        // Toastr.success("成功");
                                        Layer.close(index);
                                        table.bootstrapTable('refresh');

                                    }, function (data, ret) {

                                        console.log(ret);

                                    },
                                )

                                // Table.api.multi("del", row[options.pk], table, that);
                                // Layer.close(index);
                            }
                        );
                    }
                }
            }
        }

    };



    return Controller;
});

function easy() {
    return new GoEasy({
        appkey: 'BC-04084660ffb34fd692a9bd1a40d7b6c2'
    });
}