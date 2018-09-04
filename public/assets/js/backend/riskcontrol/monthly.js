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

            newcar_monthly: function () {
                // 表格1
                var newcarMonthly = $("#newcarMonthly");

                // 初始化表格
                newcarMonthly.bootstrapTable({
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
                            {field: 'createtime', title: __('订车日期'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime},
                            {field: 'plansecond.licenseplatenumber', title: __('车牌号')},
                            {field: 'plansecond.companyaccount', title: __('所属公司户')},
                            {field: 'admin.nickname', title: __('销售员')},
                            {field: 'username', title: __('客户姓名')},
                            {field: 'id_card', title: __('身份证号')},
                            {field: 'city', title: __('居住地址')},
                            {field: 'detailed_address', title: __('详细地址')},
                            {field: 'phone', title: __('联系电话')},
                            {field: 'models.name', title: __('订车车型')},
                            {field: 'plansecond.newpayment', title: __('首付(元)')},
                            {field: 'plansecond.monthlypaymen', title: __('月供(元)')},
                            {field: 'plansecond.periods', title: __('期数')},
                            {field: 'plansecond.bond', title: __('保证金(元)')},
                            {field: 'plansecond.tailmoney', title: __('尾款(元)')},
                            {field: 'plansecond.totalprices', title: __('车款总价(元)')},
                            {field: 'downpayment', title: __('首期款(元)')},


                            {
                                field: 'operate', title: __('Operate'), table: secondcarWaitconfirm,
                                buttons: [
                                    {
                                        name: 'secondcarWaitconfirm',
                                        text: '提交金融匹配',
                                        icon: 'fa fa-pencil',
                                        title: __('提交金融匹配'),
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
                Table.api.bindevent(newcarMonthly);

                //数据实时统计
                secondcarWaitconfirm.on('load-success.bs.table',function(e,data){
                })
            },


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


        }

    };



    return Controller;
});

