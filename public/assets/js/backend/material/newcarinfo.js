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

            new_customer: function () {
                // 表格1
                var newCustomer = $("#newCustomer");

                newCustomer.on('post-body.bs.table', function (e, settings, json, xhr) {
                    $(".btn-editone").data("area", ["80%", "80%"]);
                    $(".btn-detail").data("area", ["95%", "95%"]);
                    $(".btn-edit").data("area", ["80%", "80%"]);
                });
                // 初始化表格
                newCustomer.bootstrapTable({
                    url: 'material/Newcarinfo/new_customer',
                    extend: {
                        edit_url: 'material/newcarinfo/edit',
                        del_url: 'material/mortgageregistration/del',
                        multi_url: 'material/mortgageregistration/multi',
                        table: 'mortgage_registration',
                    },
                    toolbar: '#toolbar1',
                    pk: 'id',
                    sortName: 'id',
                    searchFormVisible: true,
                    columns: [
                        [
                            {checkbox: true},
                            {field: 'id', title: __('ID')},
                            {field: 'archival_coding', title: __('档案编码')},
                            {field: 'sales_name', title: __('部门-销售员')},
                            {
                                field: 'signdate',
                                title: __('签订日期'),
                                operate: false
                            },
                            {field: 'username', title: __('Username')},
                            {field: 'id_card', title: __('身份证号')},
                            {field: 'phone', title: __('联系方式')},
                            {field: 'payment', title: __('首付')},
                            {field: 'monthly', title: __('月供')},
                            {field: 'nperlist', title: __('期数')},
                            {field: 'end_money', title: __('末期租金')},
                            {field: 'tail_section', title: __('尾款')},
                            {field: 'margin', title: __('保证金')},
                            {field: 'hostdate', title: __('上户日期'), operate: false},
                            {field: 'models_name', title: __('规格型号')},
                            {field: 'licensenumber', title: __('车牌号')},
                            {field: 'frame_number', title: __('车架号')},
                            {field: 'mortgage_people', title: __('抵押人')},
                            // {field: 'ticketdate', title: __('开票日期'), operate: false},
                            // {field: 'supplier', title: __('供货商')},
                            // {field: 'tax_amount', title: __('含税金额(元)')},
                            // {field: 'no_tax_amount', title: __('不含税金额(元)')},
                            // {field: 'pay_taxesdate', title: __('缴税日期'), operate: false},
                            // {field: 'tax', title: __('购置税(元)'), operate: false},
                            // {field: 'house_fee', title: __('上户费(元)'), operate: false},
                            // {field: 'luqiao_fee', title: __('路桥费(元)'), operate: false},
                            // {field: 'insurance_buydate', title: __('保险购买日期'), operate: false},
                            // {field: 'insurance_policy', title: __('交强险保单'), operate: false},
                            // {field: 'insurance', title: __('交强险金额'), operate: false},
                            // {field: 'insurance', title: __('交强险金额'), operate: false},
                            // {field: 'car_boat_tax', title: __('车船税金额(元)'), operate: false},
                            // {field: 'commercial_insurance_policy', title: __('商业险保单'), operate: false},
                            // {field: 'business_risks', title: __('商业险金额(元)'), operate: false},
                            // {field: 'transferdate', title: __('过户日期'), operate: false},
                            {
                                field: 'operate', title: __('Operate'), table: newCustomer,
                                buttons: [
                                    {
                                        name: 'edit',
                                        icon: 'fa fa-pencil',
                                        text: __('Edit'),
                                        title: __('Edit'),
                                        extend: 'data-toggle="tooltip"',
                                        classname: 'btn btn-xs btn-success btn-editone',
                                    },
                                    {
                                        name: 'detail',
                                        text: '查看详细信息',
                                        icon: 'fa fa-eye',
                                        title: __('detail'),
                                        extend: 'data-toggle="tooltip"',
                                        classname: 'btn btn-xs btn-info btn-detail',
                                    },

                                ],
                                events: Controller.api.events.operate,
                                formatter: Controller.api.formatter.operate
                            }
                        ]
                    ]
                });
                // 为表格1绑定事件
                Table.api.bindevent(newCustomer);

                // 批量分配
                $(document).on("click", ".btn-selected", function () {
                    var ids = Table.api.selectedids(newCustomer);
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
            registry_registration: function () {
                // 表格2
                var registryRegistration = $("#registryRegistration");
                registryRegistration.on('post-body.bs.table', function (e, settings, json, xhr) {
                    $(".btn-edittwo").data("area", ["50%", "80%"]);
                    $(".btn-edit").data("area", ["80%", "80%"]);
                });
                // 初始化表格
                registryRegistration.bootstrapTable({
                    url: 'material/Newcarinfo/data_warehousing',
                    extend: {
                        index_url: 'registry/Newcarinfo/index',
                        add_url: 'registry/registration/add',
                        edit_url: 'material/newcarinfo/warehousing',
                        del_url: 'registry/registration/del',
                        multi_url: 'registry/registration/multi',
                        table: 'registry_registration',
                    },
                    toolbar: '#toolbar2',
                    pk: 'id',
                    sortName: 'id',
                    columns: [
                        [
                            {checkbox: true},
                            {field: 'id', title: __('ID')},
                            {field: 'archival_coding', title: __('档案编码')},
                            {field: 'username', title: __('Username')},
                            {field: 'full_mortgage', title: __('全款/按揭')},
                            {field: 'financial_name', title: __('金融公司')},
                            {field: 'phone', title: __('电话')},
                            {field: 'licensenumber', title: __('车牌号')},
                            {field: 'frame_number', title: __('车架号')},
                            {field: 'household', title: __('所属分公司')},
                            {field: 'sales_name', title: __('销售员')},
                            {field: 'rr_id_card', title: __('身份证复印件'), formatter: Controller.api.formatter.judge},
                            {
                                field: 'registered_residence',
                                title: __('户口复印件'),
                                formatter: Controller.api.formatter.judge
                            },
                            {
                                field: 'marry_and_divorceimages',
                                title: __('结婚证或者离婚证'),
                                formatter: Controller.api.formatter.judge
                            },
                            {field: 'credit_reportimages', title: __('征信报告'), formatter: Controller.api.formatter.judge},
                            {
                                field: 'halfyear_bank_flowimages',
                                title: __('半年银行流水'),
                                formatter: Controller.api.formatter.judge
                            },
                            {field: 'detailed_list', title: __('通话清单'), formatter: Controller.api.formatter.judge},
                            {
                                field: 'guarantee',
                                title: __('担保人'),
                                formatter: Controller.api.formatter.judge
                            },
                            {field: 'residence_permitimages', title: __('居住证/租房合同/房产证'), formatter: Controller.api.formatter.judge},
                            {
                                field: 'driving_license',
                                title: __('驾照'),
                                formatter: Controller.api.formatter.judge
                            },
                            {field: 'company_contractimages', title: __('公司合同'), formatter: Controller.api.formatter.judge},
                            {field: 'car_keys', title: __('钥匙'), formatter: Controller.api.formatter.judge},
                            {field: 'lift_listimages', title: __('提车单'), formatter: Controller.api.formatter.judge},
                            {
                                field: 'deposit_contractimages',
                                title: __('定金协议'),
                                formatter: Controller.api.formatter.judge
                            },
                            {
                                field: 'truth_management_protocolimages',
                                title: __('道路管理条例告知书'),
                                formatter: Controller.api.formatter.judge
                            },
                            {
                                field: 'confidentiality_agreementimages',
                                title: __('保密协议'),
                                formatter: Controller.api.formatter.judge
                            },
                            {
                                field: 'supplementary_contract_agreementimages',
                                title: __('合同补充协议/客户告知书'),
                                formatter: Controller.api.formatter.judge
                            },
                            {field: 'explain_situation', title: __('情况说明'), formatter: Controller.api.formatter.judge},
                            {
                                field: 'tianfu_bank_cardimages',
                                title: __('天府银行卡附件'),
                                formatter: Controller.api.formatter.judge
                            },
                            {field: 'other_documentsimages', title: __('其他'), formatter: Controller.api.formatter.judge},
                            {field: 'driving_licenseimages', title: __('行驶证'), formatter: Controller.api.formatter.judge},
                            {field: 'strong_insurance', title: __('交强险'), formatter: Controller.api.formatter.judge},
                            {field: 'tax_proofimages', title: __('完税证明'), formatter: Controller.api.formatter.judge},
                            {
                                field: 'invoice_or_deduction_coupletimages',
                                title: __('发票或抵扣联'),
                                formatter: Controller.api.formatter.judge
                            },
                            {
                                field: 'registration_certificateimages',
                                title: __('登记证书'),
                                formatter: Controller.api.formatter.judge
                            },
                            {field: 'commercial_insurance', title: __('商业险'), formatter: Controller.api.formatter.judge},

                            {field: 'rr_tax', title: __('购置税'), formatter: Controller.api.formatter.judge},
                            {
                                field: 'maximum_guarantee_contractimages',
                                title: __('最高保障合同'),
                                formatter: Controller.api.formatter.judge
                            },
                            {field: 'information_remark', title: __('备注')},
                            {
                                field: 'operate', title: __('Operate'), table: registryRegistration,
                                buttons: [
                                    {
                                        name: 'edit2',
                                        icon: 'fa fa-pencil',
                                        title: __('Edit'),

                                        extend: 'data-toggle="tooltip"',
                                        classname: 'btn btn-xs btn-success btn-edittwo',
                                    },

                                ],
                                events: Controller.api.events.operate,
                                formatter: Controller.api.formatter.operate
                            },
                        ]
                    ]
                });
                // 为表格2绑定事件
                Table.api.bindevent(registryRegistration);

                registryRegistration.on('load-success.bs.table', function (e, data) {
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

        warehousing: function () {
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
                operate:{

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

                    'click .btn-edittwo': function (e, value, row, index) {
                        e.stopPropagation();
                        e.preventDefault();
                        var table = $(this).closest('table');
                        var options = table.bootstrapTable('getOptions');
                        var ids = row[options.pk];
                        row = $.extend({}, row ? row : {}, {ids: ids});
                        var url = options.extend.edit_url;
                        Fast.api.open(Table.api.replaceurl(url, row, table), __('Edit'), $(this).data() || {});
                    },
                    'click .btn-detail':function (e, value, row, index) {
                        e.stopPropagation();
                        e.preventDefault();
                        var table = $(this).closest('table');
                        var options = table.bootstrapTable('getOptions');
                        var ids = row[options.pk];
                        row = $.extend({}, row ? row : {}, {ids: ids});
                        var url = "material/newcarinfo/detail";
                        Fast.api.open(Table.api.replaceurl(url, row, table), __('查看详细信息'), $(this).data() || {});
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

                judge: function (value) {
                    var res = "";
                    var color = "";
                    if (value == "no" ||value==""||value==null) {
                        res = "<i class='fa fa-times'></i>";
                        color = "danger";
                    } else {
                        res = "<i class='fa fa-check'></i>"
                        color = "success";
                    }

                    //渲染状态
                    var html = '<span class="text-' + color + '"> ' + __(res) + '</span>';

                    return html;
                }
            }
        }

    };
    return Controller;
});