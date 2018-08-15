
define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var goeasy = new GoEasy({
        appkey: 'BC-04084660ffb34fd692a9bd1a40d7b6c2'
    });
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

            newcar_audit: function () {
                // 待审核
                var newcarAudit = $("#newcarAudit"); 
                // 初始化表格
                newcarAudit.bootstrapTable({
                    url: 'riskcontrol/creditreview/newcarAudit',
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
                            {
                                field: 'id', title: __('查看详细资料'), table: newcarAudit, buttons: [
                                    {
                                        name: 'newcardetails', text: '查看详细资料', title: '查看订单详细资料', icon: 'fa fa-eye', classname: 'btn btn-xs btn-primary btn-dialog btn-newcardetails',
                                        url: 'riskcontrol/creditreview/newcardetails', callback: function (data) {
                                            console.log(data)
                                        }
                                    }
                                ],

                                operate: false, formatter: Table.api.formatter.buttons
                            },
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
                            // {
                            //     field: 'operate', title: __('Operate'), table: newcarAudit,
                            //     buttons: [
                            //         {
                            //             name: 'newauditResult',
                            //             text: '审核',
                            //             title: '审核',
                            //             icon: 'fa fa-check-square-o',
                            //             classname: 'btn btn-xs btn-info btn-newauditResult btn-dialog',
                            //         },
                            //         {
                            //             name: 'bigData',
                            //             text: '查看大数据',
                            //             title: '查看大数据征信',
                            //             icon: 'fa fa-eye',
                            //             classname: 'btn btn-xs btn-success btn-bigData btn-dialog',

                            //         }

                            //     ],
                            //     events: Controller.api.events.operate,

                            //     formatter: Controller.api.formatter.operate
                            // }

                            {
                                field: 'operate', title: __('Operate'), table: newcarAudit,
                                buttons: [
                                    {
                                        name: 'newauditResult', text: '审核', title: '审核征信', icon: 'fa fa-check-square-o', extend: 'data-toggle="tooltip"', classname: 'btn btn-xs btn-info btn-newauditResult btn-dialog',
                                        url: 'riskcontrol/creditreview/newauditResult',
                                        //等于is_reviewing_true 的时候操作栏显示的是正在审核四个字，隐藏编辑和删除
                                        //等于is_reviewing 的时候操作栏显示的是提交审核按钮 四个字，显示编辑和删除 
                                        //....
                                        hidden: function (row) { /**审核 */
                                            if (row.review_the_data == 'is_reviewing_true') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'for_the_car') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'not_through') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_car') {
                                                return true;
                                            }
                                        }
                                    },
                                    {
                                        name: 'bigData', text: '查看大数据', title: '查看大数据征信', icon: 'fa fa-eye', extend: 'data-toggle="tooltip"', classname: 'btn btn-xs btn-success btn-bigData btn-dialog',
                                        url: 'riskcontrol/creditreview/toViewBigData',/**查看大数据 */
                                        hidden: function (row, value, index) {
                                            if (row.review_the_data == 'is_reviewing_true') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'for_the_car') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'not_through') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_car') {
                                                return true;
                                            }
                                        },
                                    },
                                    {
                                        name: 'for_the_car', icon: 'fa fa-check-circle', text: '征信已通过', classname: ' text-info ',
                                        hidden: function (row) {  /**征信已通过 */
                                            if (row.review_the_data == 'for_the_car') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_true') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'not_through') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_car') {
                                                return true;
                                            }
                                        }
                                    },
                                    {
                                        name: 'not_through', icon: 'fa fa-times', text: '征信未通过，订单已关闭', classname: ' text-danger ',
                                        hidden: function (row) {  /**征信不通过 */

                                            if (row.review_the_data == 'not_through') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'for_the_car') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_true') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_car') {
                                                return true;
                                            }
                                        }
                                    },
                                    {

                                        name: 'the_car', icon: 'fa fa-automobile', text: '已提车', extend: 'data-toggle="tooltip"', title: __('订单已完成，客户已提车'), classname: ' text-success ',
                                        hidden: function (row) {  /**已提车 */
                                            if (row.review_the_data == 'the_car') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'not_through') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'for_the_car') {
                                                return true;
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
                Table.api.bindevent(newcarAudit);

                goeasy.subscribe({
                    channel: 'demo4',
                    onMessage: function(message){
                       
                        $(".btn-refresh").trigger("click");
                    }
                });

                //数据实时统计
                newcarAudit.on('load-success.bs.table', function (e, data) {
                    $(".btn-newauditResult").data("area", ["95%", "95%"]);
                    $(".btn-bigData").data("area", ["95%", "95%"]);
                    $(".btn-newcardetails").data("area", ["95%", "95%"]);
                    var newcarAudit = $('#badge_newcar_audit').text(data.total);
                    newcarAudit = parseInt($('#badge_newcar_audit').text());
                })

            },
            rentalcar_audit: function () {
                // 审核租车单
                var rentalcarAudit = $("#rentalcarAudit");
                rentalcarAudit.bootstrapTable({
                    url: 'riskcontrol/creditreview/rentalcarAudit',
                    extend: {
                    //     index_url: 'plan/planusedcar/index',
                    //     add_url: 'plan/planusedcar/add',
                    //     edit_url: 'plan/planusedcar/edit',
                    //     del_url: 'plan/planusedcar/del',
                    //     multi_url: 'plan/planusedcar/multi',
                        table: 'rental_order',
                    },
                    toolbar: '#toolbar2',
                    pk: 'id',
                    sortName: 'id',
                    searchFormVisible: true,
                    columns: [
                        [
                            {checkbox: true},
                            {field: 'id', title: __('Id')},
                            // {field: 'plan_car_rental_name', title: __('Plan_car_rental_name')},
                            // {field: 'sales_id', title: __('Sales_id')},
                            // {field: 'admin_id', title: __('Admin_id')},
                            // {field: 'control_id', title: __('Control_id')},
                            // {field: 'rental_car_id', title: __('Rental_car_id')},
                            // {field: 'insurance_id', title: __('Insurance_id')},
                            // {field: 'general_manager_id', title: __('General_manager_id')},
                            {field: 'order_no', title: __('Order_no')}, 
                            {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                            {field: 'models_name', title: __('租车车型') },
                            {field: 'admin_nickname', title: __('销售员') },
                            {
                                field: 'id', title: __('查看详细资料'), table: rentalcarAudit, buttons: [
                                    {
                                        name: 'rentalcardetails', text: '查看详细资料', title: '查看订单详细资料', icon: 'fa fa-eye', classname: 'btn btn-xs btn-primary btn-dialog btn-rentalcardetails',
                                        url: 'riskcontrol/creditreview/rentalcardetails', callback: function (data) {
                                            console.log(data)
                                        }
                                    }
                                ],

                                operate: false, formatter: Table.api.formatter.buttons
                            },
                            {field: 'username', title: __('Username')},
                            {field: 'phone', title: __('Phone')},
                            {field: 'id_card', title: __('Id_card')},
                            {field: 'genderdata', title: __('Genderdata'), searchList: {"male":__('Genderdata male'),"female":__('Genderdata female')}, formatter: Table.api.formatter.normal},
                            {field: 'cash_pledge', title: __('押金（元）')},
                            {field: 'rental_price', title: __('租金（元）')},
                            {field: 'tenancy_term', title: __('租期（月）')},
                            // {
                            //     field: 'operate', title: __('Operate'), table: rentalcarAudit,
                            //     buttons: [
                            //         {
                            //             name: 'rentalauditResult',
                            //             text: '审核',
                            //             title: '审核',
                            //             icon: 'fa fa-check-square-o',
                            //             classname: 'btn btn-xs btn-info btn-rentalauditResult btn-dialog',
                            //         },
                            //         {
                            //             name: 'bigData',
                            //             text: '查看大数据',
                            //             title: '查看大数据征信',
                            //             icon: 'fa fa-eye',
                            //             classname: 'btn btn-xs btn-success btn-bigData btn-dialog',

                            //         }

                            //     ],
                            //     events: Controller.api.events.operate,

                            //     formatter: Controller.api.formatter.operate
                            // }

                            {
                                field: 'operate', title: __('Operate'), table: rentalcarAudit,
                                buttons: [
                                    {
                                        name: 'rentalauditResult', text: '审核', title: '审核征信', icon: 'fa fa-check-square-o', extend: 'data-toggle="tooltip"', classname: 'btn btn-xs btn-info btn-rentalauditResult btn-dialog',
                                        url: 'riskcontrol/creditreview/rentalauditResult',
                                        //等于is_reviewing_true 的时候操作栏显示的是审核两个字，
                                        //等于is_reviewing_pass 的时候操作栏显示的是通过审核四个字，
                                        //....
                                        hidden: function (row) { /**审核 */
                                            if (row.review_the_data == 'is_reviewing_true') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_pass') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_nopass') {
                                                return true;
                                            }
                                        }
                                    },
                                    {
                                        name: 'bigData', text: '查看大数据', title: '查看大数据征信', icon: 'fa fa-eye', extend: 'data-toggle="tooltip"', classname: 'btn btn-xs btn-success btn-bigData btn-dialog',
                                        url: 'riskcontrol/creditreview/toViewBigData',/**查看大数据 */
                                        hidden: function (row, value, index) {
                                            if (row.review_the_data == 'is_reviewing_true') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_pass') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_nopass') {
                                                return true;
                                            }
                                        },
                                    },
                                    {
                                        name: 'is_reviewing_pass', icon: 'fa fa-check-circle', text: '征信已通过', classname: ' text-info ',
                                        hidden: function (row) {  /**征信已通过 */
                                            if (row.review_the_data == 'is_reviewing_pass') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_true') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_nopass') {
                                                return true;
                                            }
                                        }
                                    },
                                    {
                                        name: 'is_reviewing_nopass', icon: 'fa fa-times', text: '征信未通过，订单已关闭', classname: ' text-danger ',
                                        hidden: function (row) {  /**征信不通过 */

                                            if (row.review_the_data == 'is_reviewing_nopass') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_pass') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_true') {
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

                Table.api.bindevent(rentalcarAudit);

                goeasy.subscribe({
                    channel: 'demo5',
                    onMessage: function(message){
                       
                        $(".btn-refresh").trigger("click");
                    }
                });

                //数据实时统计
                rentalcarAudit.on('load-success.bs.table', function (e, data) {
                    $(".btn-rentalauditResult").data("area", ["95%", "95%"]);
                    $(".btn-rentalcardetails").data("area", ["95%", "95%"]);
                    $(".btn-bigData").data("area", ["95%", "95%"]);
                    var rentalcarAudit = $('#badge_rental_audit').text(data.total);
                })

            },
            secondhandcar_audit: function () {
                // 待审核
                var secondhandcarAudit = $("#secondhandcarAudit"); 
                // 初始化表格
                secondhandcarAudit.bootstrapTable({
                    url: 'riskcontrol/creditreview/secondhandcarAudit',
                    extend: {
                        // index_url: 'customer/customerresource/index',
                        // add_url: 'customer/customerresource/add',
                        // edit_url: 'customer/customerresource/edit',
                        // del_url: 'customer/customerresource/del',
                        // multi_url: 'customer/customerresource/multi',
                        // distribution_url: 'promote/customertabs/distribution',
                        // import_url: 'customer/customerresource/import',
                        table: 'second_sales_order',
                    },
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

                            { field: 'models_name', title: __('销售车型') },
                            { field: 'admin_nickname', title: __('销售员') },
                            {
                                field: 'id', title: __('查看详细资料'), table: secondhandcarAudit, buttons: [
                                    {
                                        name: 'secondhandcardetails', text: '查看详细资料', title: '查看订单详细资料', icon: 'fa fa-eye', classname: 'btn btn-xs btn-primary btn-dialog btn-secondhandcardetails',
                                        url: 'riskcontrol/creditreview/secondhandcardetails', callback: function (data) {
                                            console.log(data)
                                        }
                                    }
                                ],

                                operate: false, formatter: Table.api.formatter.buttons
                            },
                            { field: 'username', title: __('Username') },
                            { field: 'genderdata', title: __('Genderdata'), visible: false, searchList: { "male": __('genderdata male'), "female": __('genderdata female') } },
                            { field: 'genderdata_text', title: __('Genderdata'), operate: false },
                            { field: 'phone', title: __('Phone') },
                            { field: 'id_card', title: __('Id_card') },

                            { field: 'newpayment', title: __('新首付（元）') },
                            { field: 'monthlypaymen', title: __('月供（元）') },
                            { field: 'periods', title: __('期数（月）') },
                            { field: 'totalprices', title: __('总价（元）') },
                            // {
                            //     field: 'operate', title: __('Operate'), table: newcarAudit,
                            //     buttons: [
                            //         {
                            //             name: 'newauditResult',
                            //             text: '审核',
                            //             title: '审核',
                            //             icon: 'fa fa-check-square-o',
                            //             classname: 'btn btn-xs btn-info btn-newauditResult btn-dialog',
                            //         },
                            //         {
                            //             name: 'bigData',
                            //             text: '查看大数据',
                            //             title: '查看大数据征信',
                            //             icon: 'fa fa-eye',
                            //             classname: 'btn btn-xs btn-success btn-bigData btn-dialog',

                            //         }

                            //     ],
                            //     events: Controller.api.events.operate,

                            //     formatter: Controller.api.formatter.operate
                            // }

                            {
                                field: 'operate', title: __('Operate'), table: secondhandcarAudit,
                                buttons: [
                                    {
                                        name: 'secondhandcarResult', text: '审核', title: '审核征信', icon: 'fa fa-check-square-o', extend: 'data-toggle="tooltip"', classname: 'btn btn-xs btn-info btn-secondhandcarResult btn-dialog',
                                        url: 'riskcontrol/creditreview/secondhandcarResult',
                                        //等于is_reviewing_true 的时候操作栏显示的是正在审核四个字，隐藏编辑和删除
                                        //等于is_reviewing 的时候操作栏显示的是提交审核按钮 四个字，显示编辑和删除 
                                        //....
                                        hidden: function (row) { /**审核 */
                                            if (row.review_the_data == 'is_reviewing_true') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'for_the_car') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'not_through') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_car') {
                                                return true;
                                            }
                                        }
                                    },
                                    {
                                        name: 'bigData', text: '查看大数据', title: '查看大数据征信', icon: 'fa fa-eye', extend: 'data-toggle="tooltip"', classname: 'btn btn-xs btn-success btn-bigData btn-dialog',
                                        url: 'riskcontrol/creditreview/toViewBigData',/**查看大数据 */
                                        hidden: function (row, value, index) {
                                            if (row.review_the_data == 'is_reviewing_true') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'for_the_car') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'not_through') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_car') {
                                                return true;
                                            }
                                        },
                                    },
                                    {
                                        name: 'for_the_car', icon: 'fa fa-check-circle', text: '征信已通过', classname: ' text-info ',
                                        hidden: function (row) {  /**征信已通过 */
                                            if (row.review_the_data == 'for_the_car') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_true') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'not_through') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_car') {
                                                return true;
                                            }
                                        }
                                    },
                                    {
                                        name: 'not_through', icon: 'fa fa-times', text: '征信未通过，订单已关闭', classname: ' text-danger ',
                                        hidden: function (row) {  /**征信不通过 */

                                            if (row.review_the_data == 'not_through') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'for_the_car') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_true') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_car') {
                                                return true;
                                            }
                                        }
                                    },
                                    {

                                        name: 'the_car', icon: 'fa fa-automobile', text: '已提车', extend: 'data-toggle="tooltip"', title: __('订单已完成，客户已提车'), classname: ' text-success ',
                                        hidden: function (row) {  /**已提车 */
                                            if (row.review_the_data == 'the_car') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'not_through') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'for_the_car') {
                                                return true;
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
                Table.api.bindevent(secondhandcarAudit);

                goeasy.subscribe({
                    channel: 'demo6',
                    onMessage: function(message){
                       
                        $(".btn-refresh").trigger("click");
                    }
                });
                //数据实时统计
                secondhandcarAudit.on('load-success.bs.table', function (e, data) {
                    $(".btn-secondhandcarResult").data("area", ["95%", "95%"]);
                    $(".btn-bigData").data("area", ["95%", "95%"]);
                    $(".btn-secondhandcardetails").data("area", ["95%", "95%"]);
                    var secondhandcarAudit = $('#badge_secondhandcar_audit').text(data.total);
                    secondhandcarAudit = parseInt($('#badge_secondhandcar_audit').text());
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
            events: {
                operate: {
                    //审核新车单
                    'click .btn-newauditResult': function (e, value, row, index) {

                        e.stopPropagation();
                        e.preventDefault();
                        var table = $(this).closest('table');
                        var options = table.bootstrapTable('getOptions');
                        var ids = row[options.pk];
                        row = $.extend({}, row ? row : {}, { ids: ids });
                        var url = 'riskcontrol/creditreview/newauditResult';
                        Fast.api.open(Table.api.replaceurl(url, row, table), __('审核'), $(this).data() || {
                            callback: function (value) {
                                //    在这里可以接收弹出层中使用`Fast.api.close(data)`进行回传的数据
                            }
                        })
                    },
                    //审核租车单
                    'click .btn-rentalauditResult': function (e, value, row, index) {

                        e.stopPropagation();
                        e.preventDefault();
                        var table = $(this).closest('table');
                        var options = table.bootstrapTable('getOptions');
                        var ids = row[options.pk];
                        row = $.extend({}, row ? row : {}, { ids: ids });
                        var url = 'riskcontrol/creditreview/rentalauditResult';
                        Fast.api.open(Table.api.replaceurl(url, row, table), __('审核'), $(this).data() || {
                            callback: function (value) {
                                //    在这里可以接收弹出层中使用`Fast.api.close(data)`进行回传的数据
                            }
                        })
                    },
                    //审核二手车单
                    'click .btn-secondhandcarResult': function (e, value, row, index) {

                        e.stopPropagation();
                        e.preventDefault();
                        var table = $(this).closest('table');
                        var options = table.bootstrapTable('getOptions');
                        var ids = row[options.pk];
                        row = $.extend({}, row ? row : {}, { ids: ids });
                        var url = 'riskcontrol/creditreview/secondhandcarResult';
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

    //新车审核

    $('#newpass').click(function () {
        // alert(123);
        // return false;   
        var id = $('#hidden1').val();
        var confirm = Layer.confirm(
            __('确定通过征信审核吗?'),
            {icon: 3, title: __('Warning'), shadeClose: true},

            function (index) {

                Fast.api.ajax({
                    url: 'riskcontrol/creditreview/newpass',
                    data: {id: JSON.stringify(id)}
                }, function (data, ret) {

                    Toastr.success("成功");
                    Layer.close(confirm);
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.layer.close(index);

                    goeasy.publish ({
                        channel: 'demo4', 
                        message: '123'
                    });
                    
                    return false;
                }, function (data, ret) {
                    //失败的回调

                    return false;
                });


            }
        );


    });

    $('#newdata').click(function () {
        // alert(123);
        // return false;   
        var id = $('#hidden1').val();
        var confirm = Layer.confirm(
            __('是否需要提供担保人吗?'),
            {icon: 3, title: __('Warning'), shadeClose: true},

            function (index) {

                Fast.api.ajax({
                    url: 'riskcontrol/creditreview/newdata',
                    data: {id: JSON.stringify(id)}
                }, function (data, ret) {

                    Toastr.success("成功");
                    Layer.close(confirm);
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.layer.close(index);

                    goeasy.publish ({
                        channel: 'demo4', 
                        message: '123'
                    });
                    
                    return false;
                }, function (data, ret) {
                    //失败的回调

                    return false;
                });


            }
        );


    });

    $('#newnopass').click(function () {
        // alert(123);
        // return false;   
        var id = $('#hidden1').val();
        var confirm = Layer.confirm(
            __('确定不通过征信审核吗?'),
            {icon: 3, title: __('Warning'), shadeClose: true},

            function (index) {

                Fast.api.ajax({
                    url: 'riskcontrol/creditreview/newnopass',
                    data: {id: JSON.stringify(id)}
                }, function (data, ret) {

                    Toastr.success("成功");
                    Layer.close(confirm);
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.layer.close(index);

                    goeasy.publish ({
                        channel: 'demo4', 
                        message: '123'
                    });
                    
                    return false;
                }, function (data, ret) {
                    //失败的回调

                    return false;
                });


            }
        );


    });


    //租车审核

    $('#rentalpass').click(function () {
        // alert(123);
        // return false;   
        var id = $('#hidden1').val();
        var confirm = Layer.confirm(
            __('确定通过征信审核吗?'),
            {icon: 3, title: __('Warning'), shadeClose: true},

            function (index) {

                Fast.api.ajax({
                    url: 'riskcontrol/creditreview/rentalpass',
                    data: {id: JSON.stringify(id)}
                }, function (data, ret) {

                    Toastr.success("成功");
                    Layer.close(confirm);
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.layer.close(index);

                    goeasy.publish ({
                        channel: 'demo5', 
                        message: '123'
                    });
                    
                    return false;
                }, function (data, ret) {
                    //失败的回调

                    return false;
                });


            }
        );

    });

    $('#rentaldata').click(function () {
        // alert(123);
        // return false;   
        var id = $('#hidden1').val();
        var confirm = Layer.confirm(
            __('是否需要提供担保人吗?'),
            {icon: 3, title: __('Warning'), shadeClose: true},

            function (index) {

                Fast.api.ajax({
                    url: 'riskcontrol/creditreview/rentaldata',
                    data: {id: JSON.stringify(id)}
                }, function (data, ret) {

                    Toastr.success("成功");
                    Layer.close(confirm);
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.layer.close(index);

                    goeasy.publish ({
                        channel: 'demo5', 
                        message: '123'
                    });
                    
                    return false;
                }, function (data, ret) {
                    //失败的回调

                    return false;
                });


            }
        );


    });

    $('#rentalnopass').click(function () {
        // alert(123);
        // return false;   
        var id = $('#hidden1').val();
        var confirm = Layer.confirm(
            __('确定不通过征信审核吗?'),
            {icon: 3, title: __('Warning'), shadeClose: true},

            function (index) {

                Fast.api.ajax({
                    url: 'riskcontrol/creditreview/rentalnopass',
                    data: {id: JSON.stringify(id)}
                }, function (data, ret) {

                    Toastr.success("成功");
                    Layer.close(confirm);
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.layer.close(index);

                    goeasy.publish ({
                        channel: 'demo5', 
                        message: '123'
                    });
                    
                    return false;
                }, function (data, ret) {
                    //失败的回调

                    return false;
                });


            }
        );


    });


    //二手车审核

    $('#secondpass').click(function () {
        // alert(123);
        // return false;   
        var id = $('#hidden1').val();
        var confirm = Layer.confirm(
            __('确定通过征信审核吗?'),
            {icon: 3, title: __('Warning'), shadeClose: true},

            function (index) {

                Fast.api.ajax({
                    url: 'riskcontrol/creditreview/secondpass',
                    data: {id: JSON.stringify(id)}
                }, function (data, ret) {

                    Toastr.success("成功");
                    Layer.close(confirm);
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.layer.close(index);

                    goeasy.publish ({
                        channel: 'demo6', 
                        message: '123'
                    });
                    
                    return false;
                }, function (data, ret) {
                    //失败的回调

                    return false;
                });


            }
        );

    });

    $('#seconddata').click(function () {
        // alert(123);
        // return false;   
        var id = $('#hidden1').val();
        var confirm = Layer.confirm(
            __('是否需要提供担保人吗?'),
            {icon: 3, title: __('Warning'), shadeClose: true},

            function (index) {

                Fast.api.ajax({
                    url: 'riskcontrol/creditreview/seconddata',
                    data: {id: JSON.stringify(id)}
                }, function (data, ret) {

                    Toastr.success("成功");
                    Layer.close(confirm);
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.layer.close(index);

                    goeasy.publish ({
                        channel: 'demo6', 
                        message: '123'
                    });
                    
                    return false;
                }, function (data, ret) {
                    //失败的回调

                    return false;
                });


            }
        );


    });

    $('#secondnopass').click(function () {
        // alert(123);
        // return false;   
        var id = $('#hidden1').val();
        var confirm = Layer.confirm(
            __('确定不通过征信审核吗?'),
            {icon: 3, title: __('Warning'), shadeClose: true},

            function (index) {

                Fast.api.ajax({
                    url: 'riskcontrol/creditreview/secondnopass',
                    data: {id: JSON.stringify(id)}
                }, function (data, ret) {

                    Toastr.success("成功");
                    Layer.close(confirm);
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.layer.close(index);

                    goeasy.publish ({
                        channel: 'demo6', 
                        message: '123'
                    });
                    
                    return false;
                }, function (data, ret) {
                    //失败的回调

                    return false;
                });


            }
        );


    });

    
    return Controller;
});