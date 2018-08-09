
define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {


    var Controller = {
        index: function () {

            // 初始化表格参数配置
            Table.api.init({

            });

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

            to_audit: function () {
                // 待审核
                var toAudit = $("#toAudit"); 
                // 初始化表格
                toAudit.bootstrapTable({
                    url: 'riskcontrol/creditreview/toAudit',
                    extend: {
                        // index_url: 'customer/customerresource/index',
                        // add_url: 'customer/customerresource/add',
                        // edit_url: 'customer/customerresource/edit',
                        // del_url: 'customer/customerresource/del',
                        // multi_url: 'customer/customerresource/multi',
                        // distribution_url: 'promote/customertabs/distribution',
                        // import_url: 'customer/customerresource/import',
                        table: 'sales_order',
                    },
                    toolbar: '#toolbar1',
                    pk: 'id',
                    sortName: 'id',
                    searchFormVisible: true,
                    columns: [
                        [
                            { checkbox: true },
                            { field: 'id', title: __('Id') },
                            { field: 'order_no', title: __('Order_no') },
                            { field: 'createtime', title: __('Createtime'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime },

                            { field: 'financial_platform_name', title: __('金融平台') },
                            { field: 'models_name', title: __('销售车型') },
                            { field: 'admin_nickname', title: __('销售员') },
                            { field: 'username', title: __('Username') },
                            { field: 'genderdata', title: __('Genderdata'), visible: false, searchList: { "male": __('genderdata male'), "female": __('genderdata female') } },
                            { field: 'genderdata_text', title: __('Genderdata'), operate: false },
                            { field: 'phone', title: __('Phone') },
                            { field: 'id_card', title: __('Id_card') },

                            { field: 'payment', title: __('首付（元）') },
                            { field: 'monthly', title: __('月供（元）') },
                            { field: 'nperlist', title: __('期数') },
                            { field: 'margin', title: __('保证金（元）') },
                            { field: 'tail_section', title: __('尾款（元）') },
                            { field: 'gps', title: __('GPS（元）') },
                            {
                                field: 'operate', title: __('Operate'), table: toAudit,
                                buttons: [
                                    {
                                        name: 'auditResult',
                                        text: '审核',
                                        title: '审核',
                                        icon: 'fa fa-check-square-o',
                                        classname: 'btn btn-xs btn-info btn-auditResult btn-dialog',
                                    },
                                    {
                                        name: 'bigData',
                                        text: '查看大数据',
                                        title: '查看大数据征信',
                                        icon: 'fa fa-eye',
                                        classname: 'btn btn-xs btn-success btn-bigData btn-dialog',

                                    }

                                ],
                                events: Controller.api.events.operate,

                                formatter: Controller.api.formatter.operate
                            }
                        ]
                    ]
                });
                Table.api.bindevent(toAudit);
                //数据实时统计
                toAudit.on('load-success.bs.table', function (e, data) {
                    $(".btn-auditResult").data("area", ["95%", "95%"]);
                    $(".btn-bigData").data("area", ["95%", "95%"]);
                    var toAudit = $('#badge_new_toAudit').text(data.total);
                    toAudit = parseInt($('#badge_new_toAudit').text());
                })

            },
            pass_audit: function () {
                // 审核通过
                var passAudit = $("#passAudit");
                passAudit.bootstrapTable({
                    url: 'riskcontrol/creditreview/passAudit',
                    // extend: {
                    //     index_url: 'plan/planusedcar/index',
                    //     add_url: 'plan/planusedcar/add',
                    //     edit_url: 'plan/planusedcar/edit',
                    //     del_url: 'plan/planusedcar/del',
                    //     multi_url: 'plan/planusedcar/multi',
                    //     table: 'plan_used_car',
                    // },
                    toolbar: '#toolbar2',
                    pk: 'id',
                    sortName: 'id',
                    searchFormVisible: true,
                    columns: [
                        [
                            { checkbox: true },
                            { field: 'id', title: __('Id') },
                            { field: 'order_no', title: __('Order_no') },
                            { field: 'createtime', title: __('Createtime'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime },

                            { field: 'financial_platform_name', title: __('金融平台') },
                            { field: 'models_name', title: __('销售车型') },
                            { field: 'admin_nickname', title: __('销售员') },
                            { field: 'username', title: __('Username') },
                            { field: 'genderdata', title: __('Genderdata'), visible: false, searchList: { "male": __('genderdata male'), "female": __('genderdata female') } },
                            { field: 'genderdata_text', title: __('Genderdata'), operate: false },
                            { field: 'phone', title: __('Phone') },
                            { field: 'id_card', title: __('Id_card') },

                            { field: 'payment', title: __('首付（元）') },
                            { field: 'monthly', title: __('月供（元）') },
                            { field: 'nperlist', title: __('期数') },
                            { field: 'margin', title: __('保证金（元）') },
                            { field: 'tail_section', title: __('尾款（元）') },
                            { field: 'gps', title: __('GPS（元）') },
                        ]
                    ]

                });

                Table.api.bindevent(passAudit);

                //数据实时统计
                passAudit.on('load-success.bs.table', function (e, data) {

                    var passAudit = $('#badge_new_passAudit').text(data.total);
                })

            },
            no_approval: function () {
                // 审核未通过
                var noApproval = $("#noApproval");
                noApproval.bootstrapTable({
                    url: 'riskcontrol/creditreview/noApproval',
                    // extend: {
                    //     index_url: 'plan/planfull/index',
                    //     add_url: 'plan/planfull/add',
                    //     edit_url: 'plan/planfull/edit',
                    //     del_url: 'plan/planfull/del',
                    //     multi_url: 'plan/planfull/multi',
                    //     table: 'plan_full',
                    // },
                    toolbar: '#toolbar3',
                    pk: 'id',
                    sortName: 'id',
                    searchFormVisible: true,
                    columns: [
                        [
                            { checkbox: true },
                            { field: 'id', title: __('Id') },
                            { field: 'order_no', title: __('Order_no') },
                            { field: 'createtime', title: __('Createtime'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime },

                            { field: 'financial_platform_name', title: __('金融平台') },
                            { field: 'models_name', title: __('销售车型') },
                            { field: 'admin_nickname', title: __('销售员') },
                            { field: 'username', title: __('Username') },
                            { field: 'genderdata', title: __('Genderdata'), visible: false, searchList: { "male": __('genderdata male'), "female": __('genderdata female') } },
                            { field: 'genderdata_text', title: __('Genderdata'), operate: false },
                            { field: 'phone', title: __('Phone') },
                            { field: 'id_card', title: __('Id_card') },

                            { field: 'payment', title: __('首付（元）') },
                            { field: 'monthly', title: __('月供（元）') },
                            { field: 'nperlist', title: __('期数') },
                            { field: 'margin', title: __('保证金（元）') },
                            { field: 'tail_section', title: __('尾款（元）') },
                            { field: 'gps', title: __('GPS（元）') },
                        ]
                    ]
                });

                Table.api.bindevent(noApproval);

                //数据实时统计
                noApproval.on('load-success.bs.table', function (e, data) {

                    var noApproval = $('#badge_new_noApproval').text(data.total);
                })

            }
        },

        //审核
        auditResult: function () {
            Table.api.init({

            });
            Form.api.bindevent($("form[role=form]"), function (data, ret) {
                //这里是表单提交处理成功后的回调函数，接收来自php的返回数据

                Fast.api.close(data);//这里是重点
                // console.log(data);
                // Toastr.success("成功");//这个可有可无
            }, function (data, ret) {
                // console.log(data); 
                Toastr.success("失败");
            });
            // Controller.api.bindevent();
            // console.log(Config.id);

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
                    //审核
                    'click .btn-auditResult': function (e, value, row, index) {

                        e.stopPropagation();
                        e.preventDefault();
                        var table = $(this).closest('table');
                        var options = table.bootstrapTable('getOptions');
                        var ids = row[options.pk];
                        row = $.extend({}, row ? row : {}, { ids: ids });
                        var url = 'riskcontrol/creditreview/auditResult';
                        Fast.api.open(Table.api.replaceurl(url, row, table), __('审核'), $(this).data() || {
                            callback: function (value) {
                                //    在这里可以接收弹出层中使用`Fast.api.close(data)`进行回传的数据
                            }
                        })
                    },
                    //查看大数据
                    'click .btn-bigData': function (e, value, row, index) { 
                        e.stopPropagation();
                        e.preventDefault();
                        var table = $(this).closest('table');
                        var options = table.bootstrapTable('getOptions');
                        var ids = row[options.pk];
                        row = $.extend({}, row ? row : {}, { ids: ids });
                        var url = 'riskcontrol/creditreview/toViewBigData';
                        Fast.api.open(Table.api.replaceurl(url, row, table), __('审核'), $(this).data() || {
                            callback: function (value) {
                                //    在这里可以接收弹出层中使用`Fast.api.close(data)`进行回传的数据
                            }
                        })
                    },
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
                }

            }
        }

    };

    $('#pass').click(function () {
        // alert(123);
        // return false;   

        Form.api.bindevent($("form[role=form]"), function (data, ret) {
            //这里是表单提交处理成功后的回调函数，接收来自php的返回数据

            Fast.api.close(data);//这里是重点
            // console.log(data);
            // Toastr.success("成功");//这个可有可无
        }, function (data, ret) {
            // console.log(data); 
            Toastr.success("失败");
        });


    });
    $('#data').click(function () {
        Form.api.bindevent($("form[role=form]"), function (data, ret) {
            //这里是表单提交处理成功后的回调函数，接收来自php的返回数据

            Fast.api.close(data);//这里是重点
            // console.log(data);
            // Toastr.success("成功");//这个可有可无
        }, function (data, ret) {
            // console.log(data); 
            Toastr.success("失败");
        });
    });
    $('#nopass').click(function () {
        Form.api.bindevent($("form[role=form]"), function (data, ret) {
            //这里是表单提交处理成功后的回调函数，接收来自php的返回数据

            Fast.api.close(data);//这里是重点
            // console.log(data);
            // Toastr.success("成功");//这个可有可无
        }, function (data, ret) {
            // console.log(data); 
            Toastr.success("失败");
        });
    });
    return Controller;
});