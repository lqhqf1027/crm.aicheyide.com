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

            secondcar_waitconfirm: function () {
                // 表格1
                var secondcarWaitconfirm = $("#secondcarWaitconfirm");
                
                // 初始化表格
                secondcarWaitconfirm.bootstrapTable({
                    url: "secondhandcar/carreservation/secondcarWaitconfirm",
                    extend: {
                        index_url: 'order/salesorder/index',
                        add_url: 'order/salesorder/add',
                        // edit_url: 'order/salesorder/edit',
                        // del_url: 'order/salesorder/del',
                        multi_url: 'order/salesorder/multi',
                        table: 'second_sales_order',
                    },
                    toolbar: '#toolbar1',
                    pk: 'id',
                    sortName: 'id',
                    columns: [
                        [
                            {checkbox: true},
                            {field: 'id', title: '编号'},
                            {field: 'createtime', title: __('订车日期')},
                            {field: 'licenseplatenumber', title: __('车牌号')},
                            {field: 'companyaccount', title: __('所属公司户')},
                            {field: 'sales_name', title: __('销售员')},
                            {field: 'username', title: __('客户姓名')},
                            {field: 'id_card', title: __('身份证号')},
                            {field: 'detailed_address', title: __('地址')},
                            {field: 'phone', title: __('联系电话')},
                            {field: 'models_name', title: __('订车车型')},
                            {field: 'newpayment', title: __('首付(元)')},
                            {field: 'monthlypaymen', title: __('月供(元)')},
                            {field: 'periods', title: __('期数')},
                            {field: 'bond', title: __('保证金(元)')},
                            {field: 'tailmoney', title: __('尾款(元)')},
                            {field: 'car_total_price', title: __('车款总价(元)')},
                            {field: 'downpayment', title: __('首期款(元)')},
                            

                            {
                                field: 'operate', title: __('Operate'), table: secondcarWaitconfirm,
                                buttons: [
                                    {
                                        name: 'secondcarWaitconfirm',
                                        text: '提交风控审核',
                                        icon: 'fa fa-pencil',
                                        title: __('提交风控审核'),
                                        extend: 'data-toggle="tooltip"',
                                        classname: 'btn btn-xs btn-danger btn-secondcarWaitconfirm',

                                    }
                                ],

                                events: Controller.api.events.operate,
                                formatter: Controller.api.formatter.operate
                            }
                        ]
                    ]
                });
                // 为表格1绑定事件
                Table.api.bindevent(secondcarWaitconfirm);

                //数据实时统计
                secondcarWaitconfirm.on('load-success.bs.table',function(e,data){ 

                    var secondcarWaitconfirm =  $('#badge_secondcar_waitconfirm').text(data.total); 
                    secondcarWaitconfirm = parseInt($('#badge_secondcar_waitconfirm').text());
                    
                   
                })

                //通过
                goeasy.subscribe({
                    channel: 'demo-second_amount',
                    onMessage: function(message){
                        Layer.alert('新消息：'+message.content,{ icon:0},function(index){
                            Layer.close(index);
                            $(".btn-refresh").trigger("click");
                        });
                        
                    }
                });

            },
            secondcar_confirm: function () {

                // 表格2
                var secondcarConfirm = $("#secondcarConfirm");
                
                // 初始化表格
                secondcarConfirm.bootstrapTable({
                    url: "secondhandcar/carreservation/secondcarConfirm",
                    extend: {
                        index_url: 'order/salesorder/index',
                        add_url: 'order/salesorder/add',
                        // edit_url: 'order/salesorder/edit',
                        // del_url: 'order/salesorder/del',
                        multi_url: 'order/salesorder/multi',
                        table: 'second_sales_order',
                    },
                    toolbar: '#toolbar2',
                    pk: 'id',
                    sortName: 'id',
                    columns: [
                        [
                            {checkbox: true},
                            {field: 'id', title: '编号'},
                            {field: 'createtime', title: __('订车日期')},
                            {field: 'licenseplatenumber', title: __('车牌号')},
                            {field: 'companyaccount', title: __('所属公司户')},
                            {field: 'sales_name', title: __('销售员')},
                            {field: 'username', title: __('客户姓名')},
                            {field: 'id_card', title: __('身份证号')},
                            {field: 'detailed_address', title: __('地址')},
                            {field: 'phone', title: __('联系电话')},
                            {field: 'models_name', title: __('订车车型')},
                            {field: 'newpayment', title: __('首付(元)')},
                            {field: 'monthlypaymen', title: __('月供(元)')},
                            {field: 'periods', title: __('期数')},
                            {field: 'bond', title: __('保证金(元)')},
                            {field: 'tailmoney', title: __('尾款(元)')},
                            {field: 'car_total_price', title: __('车款总价(元)')},
                            {field: 'downpayment', title: __('首期款(元)')},

                        ]
                    ]
                });
                // 为表格2绑定事件
                Table.api.bindevent(secondcarConfirm);

                //数据实时统计
                secondcarConfirm.on('load-success.bs.table',function(e,data){ 

                    var secondcarConfirm =  $('#badge_secondcar_confirm').text(data.total); 
                    secondcarConfirm = parseInt($('#badge_secondcar_confirm').text());
                    
                   
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
                    'click .btn-secondcarWaitconfirm': function (e, value, row, index) {
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
                            __('确定提交风控进行审核吗?'),
                            {icon: 3, title: __('Warning'), offset: [top, left], shadeClose: true},
                            function (index) {
                                var table = $(that).closest('table');
                                var options = table.bootstrapTable('getOptions');

                                Fast.api.ajax({
                                        url: "secondhandcar/carreservation/setAudit",
                                        data: {id: row[options.pk]},

                                    }, function (data, ret) {
                                        // console.log(data);

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

