define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var goeasy = new GoEasy({
        appkey: 'BC-04084660ffb34fd692a9bd1a40d7b6c2'
    });

    var Controller = {
        index: function () {

            // 初始化表格参数配置
            Table.api.init({});


            var goeasy = new GoEasy({
                appkey: 'BC-04084660ffb34fd692a9bd1a40d7b6c2'
            });
            goeasy.subscribe({
                channel: 'pushFinance',
                onMessage: function(message){
                    Layer.alert('您有<span class="text-danger">'+message.content+"</span>条新消息进入,请注意查看",{ icon:0},function(index){
                        Layer.close(index);
                        $(".btn-refresh").trigger("click");
                    });

                }
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

            /**
             * 新车匹配
             */
            newprepare_match: function () {
                // 新车匹配金融
                var newprepareMatch = $("#newprepareMatch");
                newprepareMatch.on('load-success.bs.table', function (e, data) {
                    console.log(data.total);
                    $('#badge_newprepare_match').text(data.total);

                });
                newprepareMatch.on('post-body.bs.table', function (e, settings, json, xhr) {
                    $(".btn-neweditone").data("area", ["40%", "40%"]);
                    $(".btn-newtest").data("area", ["40%", "40%"]);
                    $(".btn-new").data("area", ["95%", "95%"]);
                    // $(".btn-showOrder").data("area", ["95%", "95%"]);
                });
                // 初始化表格
                newprepareMatch.bootstrapTable({
                    url: "planmanagement/matchfinance/newprepare_match",
                    extend: {
                        index_url: 'order/salesorder/index',
                        add_url: 'order/salesorder/add',
                        newedit_url: 'planmanagement/matchfinance/newedit',

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
                            {field: 'id', title: '编号',operate:false},
                            {field: 'createtime', title: __('订车日期'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime,datetimeFormat:"YYYY-MM-DD"},
                            {field: 'newinventory.household', title: __('公司')},
                            {field: 'financial_name', title: __('金融平台')},
                            {field: 'admin.nickname', title: __('销售员')},
                            {field: 'username', title: __('客户姓名')},
                            // {field: 'id_card', title: __('身份证号')},
                            // {field: 'detailed_address', title: __('地址')},
                            // {field: 'phone', title: __('联系电话')},
                            {field: 'models.name', title: __('订车车型')},
                            {field: 'planacar.payment', title: __('首付(元)'),operate:false},
                            {field: 'planacar.monthly', title: __('月供(元)'),operate:false},
                            {field: 'planacar.nperlist', title: __('期数'),operate:false},
                            {field: 'planacar.margin', title: __('保证金(元)'),operate:false},
                            {field: 'planacar.tail_section', title: __('尾款(元)'),operate:false},
                            {field: 'planacar.gps', title: __('GPS(服务费)'),operate:false},
                            {field: 'car_total_price', title: __('车款总价(元)'),operate:false},
                            {field: 'downpayment', title: __('首期款(元)'),operate:false},
                            {field: 'difference', title: __('差额(元)'),operate:false},
                            {
                                field: 'id', title: __('查看详细资料'), table: newprepareMatch, buttons: [
                                    {
                                        name: 'details', text: '查看详细资料', title: '查看订单详细资料', icon: 'fa fa-eye', classname: 'btn btn-xs btn-primary btn-dialog btn-new',
                                        url: 'planmanagement/Matchfinance/new_details', callback: function (data) {

                                        }
                                    }
                                ],

                                operate: false, formatter: Table.api.formatter.buttons
                            },
                            // {field: 'engine_number', title: __('发动机号')},
                            // {field: 'household', title: __('行驶证所有户')},
                            {field: 'newinventory.4s_shop', title: __('4S店')},
                            {field: 'amount_collected', title: __('实收定金金额'),operate:false},
                            {field: 'decorate', title: __('装饰'),operate:false},

                            {
                                field: 'operate', title: __('Operate'), table: newprepareMatch,
                                buttons: [
                                    {
                                        name:'is_reviewing',text:'匹配金融', title:'匹配金融', icon: 'fa fa-pencil',extend: 'data-toggle="tooltip"',classname: 'btn btn-xs btn-danger btn-neweditone',
                                       
                                        hidden:function(row){
                                            if(row.review_the_data == 'is_reviewing'){ 
                                                return false; 
                                            }  
                                            else if(row.review_the_data == 'is_reviewing_true'){
                                                return true;
                                            }
                                            
                                        }
                                    },
                                    {
                                        name: 'is_reviewing_true', text: '已成功匹配金融',
                                        hidden: function (row) {  /**已成功匹配金融 */
                                            if (row.review_the_data == 'is_reviewing_true') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'is_reviewing') {
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

                //实时消息
                //车管发给金融
                goeasy.subscribe({
                    channel: 'demo-newcar_finance',
                    onMessage: function(message){
                        Layer.alert('新消息：'+message.content,{ icon:0},function(index){
                            Layer.close(index);
                            $(".btn-refresh").trigger("click");
                        });
                        
                    }
                });

                // 为表格1绑定事件
                Table.api.bindevent(newprepareMatch);

                Controller.api.bindevent(newprepareMatch);

                  $(document).on('click','.btn-newtest',function () {

                      var ids = Table.api.selectedids(newprepareMatch);

                      Layer.prompt(

                          { title: __('请输入需要匹配的金融平台名称'), shadeClose: true },
                          function (text, index) {
                              Fast.api.ajax({
                                  url:"planmanagement/matchfinance/newbatch",
                                  data:{
                                      text:text,
                                      id:JSON.stringify(ids)
                                  }
                              },function (data,ret) {
                                  layer.close(index);
                                  prepareMatch.bootstrapTable('refresh');
                              },function (data,ret) {
                                  console.log(ret);
                              })
                          })

                      // var row = {ids:ids};
                      // Fast.api.open(Table.api.replaceurl(url, row, prepareMatch), __('Edit'), $(this).data() || {});
                  })

            },
            /**
             * 二手车匹配
             */
            secondprepare_match: function () {

                //二手车匹配金融
                var secondprepareMatch = $("#secondprepareMatch");
                secondprepareMatch.on('load-success.bs.table', function (e, data) {
                    console.log(data.total);
                    $('#badge_secondprepare_match').text(data.total);

                });
                secondprepareMatch.on('post-body.bs.table', function (e, settings, json, xhr) {
                    $(".btn-showOrderAndStock").data("area", ["95%", "95%"]);
                    $(".btn-secondeditone").data("area", ["40%", "40%"]);
                    $(".btn-secondtest").data("area", ["40%", "40%"]);
                    $(".btn-used").data("area", ["95%", "95%"]);
                });
                // 初始化表格
                secondprepareMatch.bootstrapTable({
                    url: "planmanagement/matchfinance/secondprepare_match",
                    extend: {
                        index_url: 'order/salesorder/index',
                        add_url: 'order/salesorder/add',
                        secondedit_url: 'planmanagement/matchfinance/secondedit',
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
                            {field: 'id', title: '编号',operate:false},
                            {field: 'createtime', title: __('订车日期'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime},
                            {field: 'plansecond.companyaccount', title: __('公司')},
                            {field: 'admin.nickname', title: __('销售员')},
                            {field: 'username', title: __('客户姓名')},
                            // {field: 'id_card', title: __('身份证号')},
                            // {field: 'phone', title: __('联系电话')},
                            {field: 'models.name', title: __('订车车型')},
                            {field: 'plansecond.newpayment', title: __('首付(元)'),operate:false},
                            {field: 'plansecond.monthlypaymen', title: __('月供(元)'),operate:false},
                            {field: 'plansecond.periods', title: __('期数'),operate:false},
                            {field: 'plansecond.bond', title: __('保证金(元)'),operate:false},
                            {field: 'plansecond.tailmoney', title: __('尾款(元)'),operate:false},
                            {field: 'plansecond.totalprices', title: __('车款总价(元)'),operate:false},
                            {field: 'downpayment', title: __('首期款(元)'),operate:false},
                            {field: 'difference', title: __('差额(元)'),operate:false},
                            {
                                field: 'id', title: __('查看详细资料'), table: secondprepareMatch, buttons: [
                                    {
                                        name: 'details', text: '查看详细资料', title: '查看订单详细资料', icon: 'fa fa-eye', classname: 'btn btn-xs btn-primary btn-dialog btn-used',
                                        url: 'planmanagement/Matchfinance/used_details', callback: function (data) {

                                        }
                                    }
                                ],

                                operate: false, formatter: Table.api.formatter.buttons
                            },
                            {field: 'amount_collected', title: __('实收定金金额'),operate:false},
                            {field: 'decorate', title: __('装饰'),operate:false},

                            {
                                field: 'operate', title: __('Operate'), table: secondprepareMatch,
                                buttons: [
                                    {
                                        name:'is_reviewing_finance',text:'匹配金融', title:'匹配金融', icon: 'fa fa-pencil',extend: 'data-toggle="tooltip"',classname: 'btn btn-xs btn-danger btn-secondeditone',
                                       
                                        hidden:function(row){
                                            if(row.review_the_data == 'is_reviewing_finance'){ 
                                                return false; 
                                            }  
                                            else if(row.review_the_data == 'is_reviewing_control'){
                                                return true;
                                            }
                                            
                                        }
                                    },
                                    {
                                        name: 'is_reviewing_control', text: '已成功匹配金融',
                                        hidden: function (row) {  /**已成功匹配金融 */
                                            if (row.review_the_data == 'is_reviewing_control') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_finance') {
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

                //实时消息
                //车管发给金融
                goeasy.subscribe({
                    channel: 'demo-second_finance',
                    onMessage: function(message){
                        Layer.alert('新消息：'+message.content,{ icon:0},function(index){
                            Layer.close(index);
                            $(".btn-refresh").trigger("click");
                        });
                        
                    }
                });

                // 为表格2绑定事件
                Table.api.bindevent(secondprepareMatch);

                Controller.api.bindevent(secondprepareMatch);

                  $(document).on('click','.btn-secondtest',function () {

                      var ids = Table.api.selectedids(secondprepareMatch);

                      Layer.prompt(

                          { title: __('请输入需要匹配的金融平台名称'), shadeClose: true },
                          function (text, index) {
                              Fast.api.ajax({
                                  url:"planmanagement/matchfinance/secondbatch",
                                  data:{
                                      text:text,
                                      id:JSON.stringify(ids)
                                  }
                              },function (data,ret) {
                                  layer.close(index);
                                  prepareMatch.bootstrapTable('refresh');
                              },function (data,ret) {
                                  console.log(ret);
                              })
                          })

                      // var row = {ids:ids};
                      // Fast.api.open(Table.api.replaceurl(url, row, prepareMatch), __('Edit'), $(this).data() || {});
                  })


            }


        },
        add: function () {
            Controller.api.bindevent();

        },
        newedit: function () {
            // $(".btn-add").data("area", ["300px","200px"]);
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
            Controller.api.bindevent();
        },
        secondedit: function () {
            // $(".btn-add").data("area", ["300px","200px"]);
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
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        batch:function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function (table) {
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

                    /**
                     * 新车匹配金融
                     * @param e
                     * @param value
                     * @param row
                     * @param index
                     */
                    'click .btn-neweditone': function (e, value, row, index) {
                        e.stopPropagation();
                        e.preventDefault();
                        var table = $(this).closest('table');
                        var options = table.bootstrapTable('getOptions');
                        var ids = row[options.pk];
                        row = $.extend({}, row ? row : {}, {ids: ids});
                        var url = options.extend.newedit_url;
                        Fast.api.open(Table.api.replaceurl(url, row, table), __('新车匹配金融'), $(this).data() || {});
                    },
                    /**
                     * 二手车匹配金融
                     * @param e
                     * @param value
                     * @param row
                     * @param index
                     */
                    'click .btn-secondeditone': function (e, value, row, index) {
                        e.stopPropagation();
                        e.preventDefault();
                        var table = $(this).closest('table');
                        var options = table.bootstrapTable('getOptions');
                        var ids = row[options.pk];
                        row = $.extend({}, row ? row : {}, {ids: ids});
                        var url = options.extend.secondedit_url;
                        Fast.api.open(Table.api.replaceurl(url, row, table), __('二手车匹配金融'), $(this).data() || {});
                    },
                    /**
                     * 新车匹配金融
                     * @param e
                     * @param value
                     * @param row
                     * @param index
                     */
                    'click .btn-new': function (e, value, row, index) {
                        e.stopPropagation();
                        e.preventDefault();
                        var table = $(this).closest('table');
                        var options = table.bootstrapTable('getOptions');
                        var ids = row[options.pk];
                        row = $.extend({}, row ? row : {}, {ids: ids});
                        var url = 'planmanagement/Matchfinance/new_details';
                        Fast.api.open(Table.api.replaceurl(url, row, table), __('二手车匹配金融'), $(this).data() || {});
                    },
                    /**
                     * 二手车匹配金融
                     * @param e
                     * @param value
                     * @param row
                     * @param index
                     */
                    'click .btn-used': function (e, value, row, index) {
                        e.stopPropagation();
                        e.preventDefault();
                        var table = $(this).closest('table');
                        var options = table.bootstrapTable('getOptions');
                        var ids = row[options.pk];
                        row = $.extend({}, row ? row : {}, {ids: ids});
                        var url = 'planmanagement/Matchfinance/used_details';
                        Fast.api.open(Table.api.replaceurl(url, row, table), __('二手车匹配金融'), $(this).data() || {});
                    }

                }
            }
        }

    };
    function get_easy() {
        return new GoEasy({
            appkey: 'BC-04084660ffb34fd692a9bd1a40d7b6c2'
        });
    }
    return Controller;
});