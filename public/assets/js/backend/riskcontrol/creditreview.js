
define(['jquery', 'bootstrap', 'backend', 'table', 'form','echarts', 'echarts-theme','addtabs'], function ($, undefined, Backend, Table, Form,Echarts, undefined, Template) {

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
        bigdata:function(){
            //欺诈评分图表
            
                var myChart = Echarts.init(document.getElementById('echart'));
            // 指定图表的配置项和数据
            var option = {
                tooltip : {
                    formatter: "{a} <br/><br/>{b} : {c}"
                },
                toolbox: {
                    feature: {
                        restore: {},
                        saveAsImage: {}
                    }
                },
               
                series: [
                    {
                        name: '欺诈评分',
                        type: 'gauge',
                        detail: {formatter:' {value}'},
                        data: [{value: Config.zcFraudScore, name: '欺诈评分'}],
                        axisLine:{
                            lineStyle:{
                                 color: [[0.2, 'lime'],[0.70, '#1e90ff'],[4, '#ff4500']],
                            width: 3,
                            shadowColor : '#fff', //默认透明
                            shadowBlur: 10

                            }
                        },
                        splitLine:{
                            show:false,
                        },
                        axisTick:{
                            show:false,
                            length:0
                        },
                        axisLabel:{
                            show:false,
                            length:0
                        }
                    }
                ],
            };

            // 使用刚指定的配置项和数据显示图表。
            myChart.setOption(option);
        },
        table: {
            /**新车 */
            newcar_audit: function () {
                // 待审核
                var newcarAudit = $("#newcarAudit"); 
                console.log($('.fixed-table-toolbar').attr('class'));
                // 初始化表格 
                newcarAudit.bootstrapTable({
                    url: 'riskcontrol/creditreview/newcarAudit',
                    extend: {
                        //  add_url: 'order/salesorder/add',
                        // edit_url: 'order/salesorder/edit',
                        // del_url: 'order/salesorder/del',
                        // multi_url: 'order/salesorder/multi',
                        table: 'sales_order',
                    },
                    toolbar: '#toolbar1',
                    pk: 'id',
                    sortName: 'id',
                    search:false,
                    searchFormVisible: true,
                    columns: [
                        [
                            { checkbox: true },
                            { field: 'id', title: __('Id'),operate:false},
                            { field: 'order_no', title: __('Order_no') },
                            { field: 'createtime', title: __('Createtime'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime },

                            { field: 'financial_name', title: __('金融平台') },
                            { field: 'models.name', title: __('销售车型') },
                            { field: 'admin.nickname', title: __('销售员') },
                            {
                                field: 'id', title: __('查看详细资料'), table: newcarAudit, buttons: [
                                    {
                                        name: 'newcardetails', text: '查看详细资料', title: '查看订单详细资料', icon: 'fa fa-eye', classname: 'btn btn-xs btn-primary btn-dialog btn-newcardetails',
                                        url: 'riskcontrol/creditreview/newcardetails', callback: function (data) {
                                            // console.log(data)
                                        }
                                    }
                                ],

                                operate: false, formatter: Table.api.formatter.buttons
                            },
                            { field: 'username', title: __('Username') },
                            // { field: 'genderdata', title: __('Genderdata'), visible: false, searchList: { "male": __('genderdata male'), "female": __('genderdata female') } },
                            // { field: 'genderdata_text', title: __('Genderdata'), operate: false },
                            { field: 'phone', title: __('Phone') },
                            { field: 'id_card', title: __('Id_card') },

                            { field: 'planacar.payment', title: __('首付（元）'),operate:false },
                            { field: 'planacar.monthly', title: __('月供（元）'),operate:false },
                            { field: 'planacar.nperlist', title: __('期数'),operate:false },
                            { field: 'planacar.margin', title: __('保证金（元）'),operate:false },
                            { field: 'planacar.tail_section', title: __('尾款（元）'),operate:false },
                            { field: 'planacar.gps', title: __('GPS（元）'),operate:false },
                            {
                                field: 'operate', title: __('Operate'), table: newcarAudit,
                                buttons: [
                                    {
                                        name: '', icon: 'fa fa-times', text: '审核资料还未完善，无法进行审核', classname: ' text-danger ',
                                        hidden: function (row) {  /**审核资料还未完善，无法进行审核 */
                                           
                                            if (!row.id_cardimages || !row.drivers_licenseimages || !row.bank_cardimages || !row.undertakingimages || !row.accreditimages || !row.faceimages  || !row.informationimages) {
                                                return false;
                                            }
                                            else if (row.id_cardimages && row.drivers_licenseimages && row.bank_cardimages && row.undertakingimages && row.accreditimages && row.faceimages  && row.informationimages) {
                                                return true;
                                            }
                                        },
                                    },
                                    {
                                        name: 'newauditResult', text: '审核', title: '审核征信', icon: 'fa fa-check-square-o', extend: 'data-toggle="tooltip"', classname: 'btn btn-xs btn-info btn-newauditResult btn-dialog',
                                        url: 'riskcontrol/creditreview/newauditResult',
                                        //等于is_reviewing_true 的时候操作栏显示的是正在审核四个字，隐藏编辑和删除
                                        //等于is_reviewing 的时候操作栏显示的是提交审核按钮 四个字，显示编辑和删除 
                                        //....
                                        hidden: function (row) { /**审核 */
                                            if ((row.review_the_data == 'is_reviewing_true') && row.id_cardimages && row.drivers_licenseimages && row.bank_cardimages && row.undertakingimages && row.accreditimages && row.faceimages  && row.informationimages) {
                                                return false;
                                            }
                                            else if ((row.review_the_data == 'is_reviewing_true') || !row.id_cardimages || !row.drivers_licenseimages || !row.bank_cardimages || !row.undertakingimages || !row.accreditimages || !row.faceimages  || !row.informationimages) {
                                                return true;
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
                                            else if (row.review_the_data == 'conclude_the_contract') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'tube_into_stock') {
                                                return true;
                                            }
                                        }
                                    },
                                    {
                                        name: 'bigData', text: '查看大数据', title: '查看大数据征信', icon: 'fa fa-eye', extend: 'data-toggle="tooltip"', classname: 'btn btn-xs btn-success btn-bigData btn-dialog',
                                        url: 'riskcontrol/creditreview/toViewBigData',/**查看大数据 */
                                        hidden: function (row, value, index) {
                                            if ((row.review_the_data == 'is_reviewing_true') && row.id_cardimages && row.drivers_licenseimages && row.bank_cardimages && row.undertakingimages && row.accreditimages && row.faceimages  && row.informationimages) {
                                                return false;
                                            }
                                            else if ((row.review_the_data == 'is_reviewing_true') || !row.id_cardimages || !row.drivers_licenseimages || !row.bank_cardimages || !row.undertakingimages || !row.accreditimages || !row.faceimages  || !row.informationimages) {
                                                return true;
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
                                            else if (row.review_the_data == 'conclude_the_contract') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'tube_into_stock') {
                                                return true;
                                            }
                                        },
                                    },
                                    {
                                        name: 'for_the_car', text: '提交给销售，通知客户签订金融合同', title: '提交到销售，通知客户签订金融合同', icon: 'fa fa-share', extend: 'data-toggle="tooltip"', classname: 'btn btn-xs btn-info btn-submit_newsales',
                                        hidden: function (row) {   
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
                                            else if (row.review_the_data == 'conclude_the_contract') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'tube_into_stock') {
                                                return true;
                                            }
                                        }
                                    },
                                    {
                                        name: 'conclude_the_contract', text: '提交车管，进行录入库存', title: '提交车管，进行录入库存', icon: 'fa fa-share', extend: 'data-toggle="tooltip"', classname: 'btn btn-xs btn-info btn-submit_newtube',
                                        
                                        hidden: function (row) { 
                                            if (row.review_the_data == 'conclude_the_contract') {
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
                                            else if (row.review_the_data == 'for_the_car') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'tube_into_stock') {
                                                return true;
                                            }
                                        }
                                    },
                                    {
                                        name: 'tube_into_stock', text: '选择库存车', title: '选择库存车', icon: 'fa fa-arrows', extend: 'data-toggle="tooltip"', classname: 'btn btn-xs btn-danger btn-dialog btn-chooseStock',
                                        
                                        hidden: function (row) { 
                                            if (row.review_the_data == 'tube_into_stock') {
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
                                            else if (row.review_the_data == 'for_the_car') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'conclude_the_contract') {
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
                                            else if (row.review_the_data == 'conclude_the_contract') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'tube_into_stock') {
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
                                            else if (row.review_the_data == 'conclude_the_contract') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'tube_into_stock') {
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

                //实时消息
                //金融发送给风控
                goeasy.subscribe({
                    channel: 'demo-newcar_control',
                    onMessage: function(message){
                        Layer.alert('新消息：'+message.content,{ icon:0},function(index){
                            Layer.close(index);
                            $(".btn-refresh").trigger("click");
                        });
                        
                    }
                });

                goeasy.subscribe({
                    channel: 'demo4',
                    onMessage: function(message){
                       
                        $(".btn-refresh").trigger("click");
                    }
                });
                //提供保证金
                goeasy.subscribe({
                    channel: 'demo-the_guarantor',
                    onMessage: function(message){
                        Layer.alert('新消息：'+message.content,{ icon:0},function(index){
                            Layer.close(index);
                            $(".btn-refresh").trigger("click");
                        });
                        
                    }
                });
                 //指定搜索条件
            $(document).on("click", ".btn-singlesearch", function () {
               
                var options = newcarAudit.bootstrapTable('getOptions');
                options.pageNumber = 1;
                options.queryParams = function (params) {
                    return {
                        search: params.search,
                        sort: params.sort,
                        order: params.order,
                        filter: JSON.stringify({username: '测试客户'}),
                        op: JSON.stringify({username: '='}),
                        offset: params.offset,
                        limit: params.limit,
                    };
                };
                newcarAudit.bootstrapTable('refresh', {});
                Toastr.info("当前执行的是自定义搜索");
                return false;
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
            /**租车 */
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
                            {field: 'id', title: __('Id'),operate:false},
                            {field: 'order_no', title: __('Order_no')}, 
                            {field: 'createtime', title: __('提交时间'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                            {field: 'models.name', title: __('租车车型') },
                            {field: 'admin.nickname', title: __('销售员') },

                            {field: 'username', title: __('Username')},
                            {field: 'phone', title: __('Phone')},
                            {field: 'id_card', title: __('Id_card')},
                            {field: 'cash_pledge', title: __('押金（元）'),operate:false},
                            {field: 'rental_price', title: __('租金（元）'),operate:false},
                            {field: 'tenancy_term', title: __('租期（月）'),operate:false},
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
                                            if (row.review_the_data == 'is_reviewing_control') {
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
                                            if (row.review_the_data == 'is_reviewing_control') {
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
                                            else if (row.review_the_data == 'is_reviewing_control') {
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
                                            else if (row.review_the_data == 'is_reviewing_control') {
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

                //实时推送 -- 关闭弹窗
                goeasy.subscribe({
                    channel: 'demo5',
                    onMessage: function(message){
                       
                        $(".btn-refresh").trigger("click");
                    }
                });

                //实时消息
                //租车销售发送给风控
                goeasy.subscribe({
                    channel: 'demo-rental_control',
                    onMessage: function(message){
                        Layer.alert('新消息：'+message.content,{ icon:0},function(index){
                            Layer.close(index);
                            $(".btn-refresh").trigger("click");
                        });
                        
                    }
                });

                //数据实时统计
                rentalcarAudit.on('load-success.bs.table', function (e, data) {
                    $(".btn-rentalauditResult").data("area", ["95%", "95%"]);
                    $(".btn-rentalcardetails").data("area", ["95%", "95%"]);
                    $(".btn-bigData").data("area", ["95%", "95%"]);
                    $(".btn-signature").data("area", ["80%", "80%"]);
                    var rentalcarAudit = $('#badge_rental_audit').text(data.total);
                })

            },
            /**二手车 */
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
                            { field: 'id', title: __('Id'),operate:false },
                            { field: 'order_no', title: __('Order_no') },
                            { field: 'createtime', title: __('Createtime'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime },

                            { field: 'models.name', title: __('销售车型') },
                            { field: 'admin.nickname', title: __('销售员') },
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

                            { field: 'plansecond.newpayment', title: __('新首付（元）'),operate:false },
                            { field: 'plansecond.monthlypaymen', title: __('月供（元）'),operate:false },
                            { field: 'plansecond.periods', title: __('期数（月）'),operate:false },
                            { field: 'plansecond.totalprices', title: __('总价（元）'),operate:false },
                            { field: 'plansecond.bond', title: __('保证金（元）'),operate:false },
                            { field: 'plansecond.tailmoney', title: __('尾款（元）'),operate:false },
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
                                            if (row.review_the_data == 'is_reviewing_control') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'for_the_car') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_car') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'not_through') {
                                                return true;
                                            }
                                        }
                                    },
                                    {
                                        name: 'bigData', text: '查看大数据', title: '查看大数据征信', icon: 'fa fa-eye', extend: 'data-toggle="tooltip"', classname: 'btn btn-xs btn-success btn-bigData btn-dialog',
                                        url: 'riskcontrol/creditreview/toViewBigData',/**查看大数据 */
                                        hidden: function (row, value, index) {
                                            if (row.review_the_data == 'is_reviewing_control') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'the_car') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'not_through') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'for_the_car') {
                                                return true;
                                            }
                                        },
                                    },
                                    {
                                        name: 'for_the_car', icon: 'fa fa-check-circle', text: '征信已通过，车管正在备车中', classname: ' text-info ',
                                        hidden: function (row) {  /**征信已通过，车管正在备车中 */
                                            if (row.review_the_data == 'for_the_car') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_control') {
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
                                            else if (row.review_the_data == 'the_car') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_control') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'for_the_car') {
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
                                            else if (row.review_the_data == 'is_reviewing_control') {
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
                

                //实时消息
                //金融发给风控
                goeasy.subscribe({
                    channel: 'demo-second_control',
                    onMessage: function(message){
                        Layer.alert('新消息：'+message.content,{ icon:0},function(index){
                            Layer.close(index);
                            $(".btn-refresh").trigger("click");
                        });
                        
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
        choosestock: function () {
            
            Table.api.init({
               
            });
            Form.api.bindevent($("form[role=form]"), function(data, ret){
                //这里是表单提交处理成功后的回调函数，接收来自php的返回数据
                
                // console.log(data);
                // newAllocationNum = parseInt($('#badge_new_allocation').text());
                // num = parseInt(data);
                // $('#badge_new_allocation').text(num+newAllocationNum); 
                Fast.api.close(data);//这里是重点
                
                Toastr.success("成功");//这个可有可无
            }, function(data, ret){
                // console.log(data);
                
                Toastr.success("失败");
                
            });
            // Controller.api.bindevent();

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
                    //新车提交销售，通知客户签金融合同
                    'click .btn-submit_newsales': function (e, value, row, index) {

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
                            __('是否提交销售，通知客户进行签订金融合同?'),
                            { icon: 3, title: __('Warning'), offset: [top, left], shadeClose: true },

                            function (index) {
                                var table = $(that).closest('table');
                                var options = table.bootstrapTable('getOptions');


                                Fast.api.ajax({

                                    url: 'riskcontrol/creditreview/newsales',
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
                    //新车提交车管，通知进行录入库存
                    'click .btn-submit_newtube': function (e, value, row, index) {

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
                            __('是否已签订完金融合同，提交车管录入库存?'),
                            { icon: 3, title: __('Warning'), offset: [top, left], shadeClose: true },

                            function (index) {
                                var table = $(that).closest('table');
                                var options = table.bootstrapTable('getOptions');


                                Fast.api.ajax({

                                    url: 'riskcontrol/creditreview/newtube',
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
                    //选择库存车
                    'click .btn-chooseStock': function (e, value, row, index) {

                        e.stopPropagation();
                        e.preventDefault();
                        var table = $(this).closest('table');
                        var options = table.bootstrapTable('getOptions');
                        var ids = row[options.pk];
                        row = $.extend({}, row ? row : {}, { ids: ids });
                        var url = 'riskcontrol/creditreview/choosestock';
                        Fast.api.open(Table.api.replaceurl(url, row, table), __('选择库存车'), $(this).data() || {
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
                        var bigdatatype = row.plan_acar_name?'sales_order':row.plan_car_rental_name?'rental_order':row.plan_car_second_name?'second_sales_order':0;
                        var url = 'riskcontrol/creditreview/bigdata'+'/bigdatatype/'+bigdatatype;
                        // console.log(row);return;
                        Fast.api.open(Table.api.replaceurl(url, row, table), __('大数据'), $(this).data() || {
                            callback: function (value) {
                            },success:function(ret){
                                console.log(ret);
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
                    // console.log(data);
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
console.log(ret);
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