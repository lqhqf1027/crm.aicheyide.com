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

        //刷新
        admeasure: function () {

            // $(".btn-add").data("area", ["300px","200px"]);
            Table.api.init({});
            Form.api.bindevent($("form[role=form]"), function (data, ret) {
                var pre = parseInt($('#assigned-customer').text());

                $('#assigned-customer').text(pre+1);

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

        table: {

            not_entry: function () {
                // 表格1
                var notEntry = $("#notEntry");
                notEntry.on('load-success.bs.table', function (e, data) {
                    console.log(data.total);
                    $('#new-customer').text(data.total);

                })
                notEntry.on('post-body.bs.table', function (e, settings, json, xhr) {
                    $(".btn-editone").data("area", ["50%", "40%"]);
                });
                // 初始化表格
                notEntry.bootstrapTable({
                    url: 'backoffice/carreservation/not_entry',

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

                            {
                                field: 'operate', title: __('Operate'), table: notEntry,
                                buttons:[
                                    {
                                        name: 'detail',
                                        text: '录入金额',
                                        icon: 'fa fa-pencil',
                                        title: __('Edit'),
                                        extend: 'data-toggle="tooltip"',
                                        classname: 'btn btn-xs btn-danger btn-editone',
                                        url: 'backoffice/carreservation/actual_amount'
                                    }
                                ],

                                events: Controller.api.events.operate,
                                formatter: Controller.api.formatter.operate
                            }
                        ]
                    ]
                });
                // 为表格1绑定事件
                Table.api.bindevent(notEntry);

                // 批量分配
                $(document).on("click", ".btn-selected", function () {
                    var ids = Table.api.selectedids(notEntry);
                    var url = 'backoffice/custominfotabs/batch?ids=' + ids;

                    var options = {
                        shadeClose: false,
                        shade: [0.3, '#393D49'],
                        area: ['50%', '50%'],
                        callback: function (value) {

                        }
                    };
                    Fast.api.open(url, '批量分配', options)
                });




            },
            entry: function () {
                // 表格1
                var entrys = $("#entrys");
                entrys.on('load-success.bs.table', function (e, data) {
                    console.log(data.total);
                    $('#new-customer').text(data.total);

                })
                entrys.on('post-body.bs.table', function (e, settings, json, xhr) {

                });
                // 初始化表格
                entrys.bootstrapTable({
                    url: 'backoffice/carreservation/entry',
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

                        ]
                    ]
                });
                // 为表格1绑定事件
                Table.api.bindevent(entrys);




            },


        },
        add: function () {
            Controller.api.bindevent();

        },
        edit: function () {
            Controller.api.bindevent();
        },
        actual_amount:function(){
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
            events:{
                operate:{
                    'click .btn-editone': function (e, value, row, index) {
                        e.stopPropagation();
                        e.preventDefault();
                        var table = $(this).closest('table');
                        var options = table.bootstrapTable('getOptions');
                        var ids = row[options.pk];

                        row = $.extend({}, row ? row : {}, {ids: ids});
                        var url = 'backoffice/carreservation/actual_amount';
                        Fast.api.open(Table.api.replaceurl(url, row, table), __('录入实际订车金额'), $(this).data() || {});
                    },
                }
            }
        }

    };
    return Controller;
});