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
                    // $(".btn-showOrder").data("area", ["95%", "95%"]);
                });
                // 初始化表格
                newprepareMatch.bootstrapTable({
                    url: "planmanagement/matchfinance/newprepare_match",
                    extend: {
                        index_url: 'order/salesorder/index',
                        add_url: 'order/salesorder/add',
                        edit_url: 'order/salesorder/edit',
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
                            {field: 'createtime', title: __('订车日期'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime,datetimeFormat:"YYYY-MM-DD"},
                            {field: 'newinventory.household', title: __('公司')},
                            {field: 'financial_name', title: __('金融平台')},
                            {field: 'admin.nickname', title: __('销售员')},
                            {field: 'username', title: __('客户姓名')},
                            // {field: 'id_card', title: __('身份证号')},
                            // {field: 'detailed_address', title: __('地址')},
                            // {field: 'phone', title: __('联系电话')},
                            {field: 'models.name', title: __('订车车型')},
                            {field: 'planacar.payment', title: __('首付(元)')},
                            {field: 'planacar.monthly', title: __('月供(元)')},
                            {field: 'planacar.nperlist', title: __('期数')},
                            {field: 'planacar.margin', title: __('保证金(元)')},
                            {field: 'planacar.tail_section', title: __('尾款(元)')},
                            {field: 'planacar.gps', title: __('GPS(服务费)')},
                            {field: 'car_total_price', title: __('车款总价(元)')},
                            {field: 'downpayment', title: __('首期款(元)')},
                            {field: 'difference', title: __('差额(元)')},
                            // {field: 'engine_number', title: __('发动机号')},
                            // {field: 'household', title: __('行驶证所有户')},
                            {field: 'newinventory.4s_shop', title: __('4S店')},
                            {field: 'amount_collected', title: __('实收金额')},
                            {field: 'decorate', title: __('装饰')},

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
                });
                // 初始化表格
                secondprepareMatch.bootstrapTable({
                    url: "planmanagement/matchfinance/secondprepare_match",
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
                            {field: 'createtime', title: __('订车日期'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime},
                            {field: 'plansecond.companyaccount', title: __('公司')},
                            {field: 'admin.nickname', title: __('销售员')},
                            {field: 'username', title: __('客户姓名')},
                            // {field: 'id_card', title: __('身份证号')},
                            // {field: 'phone', title: __('联系电话')},
                            {field: 'models.name', title: __('订车车型')},
                            {field: 'plansecond.newpayment', title: __('首付(元)')},
                            {field: 'plansecond.monthlypaymen', title: __('月供(元)')},
                            {field: 'plansecond.periods', title: __('期数')},
                            {field: 'plansecond.bond', title: __('保证金(元)')},
                            {field: 'plansecond.tailmoney', title: __('尾款(元)')},
                            {field: 'plansecond.totalprices', title: __('车款总价(元)')},
                            {field: 'downpayment', title: __('首期款(元)')},
                            {field: 'difference', title: __('差额(元)')},
                            {field: 'amount_collected', title: __('实收金额')},
                            {field: 'decorate', title: __('装饰')},

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


                //Bootstrap-table的父元素,包含table,toolbar,pagnation
                var parenttable = table.closest('.bootstrap-table');
                //Bootstrap-table配置
                var options = table.bootstrapTable('getOptions');
                //Bootstrap操作区
                var toolbar = $(options.toolbar, parenttable);



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
                    'click .btn-neweditone': function (e, value, row, index) {
                        var table = $(this).closest('table');
                        Layer.prompt(
                            // __('请输入客户手机服务密码'),测试服务密码：202304
                            { title: __('请输入需要匹配的金融平台名称'), shadeClose: true },
                            //text为输入的服务密码
                            function (text, index) {
                                Fast.api.ajax({
                                    url:"planmanagement/matchfinance/newedit",
                                    data:{
                                        text:text,
                                        id:row.id
                                    }
                                },function (data,ret) {
                                    layer.close(index);
                                    table.bootstrapTable('refresh');
                                },function (data,ret) {
                                    console.log(ret);
                                })
                            })

                        // e.stopPropagation();
                        // e.preventDefault();
                        // var table = $(this).closest('table');
                        // var options = table.bootstrapTable('getOptions');
                        // var ids = row[options.pk];
                        // row = $.extend({}, row ? row : {}, {ids: ids});
                        // var url = 'planmanagement/matchfinance/edit';
                        // Fast.api.open(Table.api.replaceurl(url, row, table), __('Edit'), $(this).data() || {});
                    },
                    'click .btn-secondeditone': function (e, value, row, index) {
                        var table = $(this).closest('table');
                        Layer.prompt(
                            // __('请输入客户手机服务密码'),测试服务密码：202304
                            { title: __('请输入需要匹配的金融平台名称'), shadeClose: true },
                            //text为输入的服务密码
                            function (text, index) {
                                Fast.api.ajax({
                                    url:"planmanagement/matchfinance/secondedit",
                                    data:{
                                        text:text,
                                        id:row.id
                                    }
                                },function (data,ret) {
                                    layer.close(index);
                                    table.bootstrapTable('refresh');
                                },function (data,ret) {
                                    console.log(ret);
                                })
                            })

                        // e.stopPropagation();
                        // e.preventDefault();
                        // var table = $(this).closest('table');
                        // var options = table.bootstrapTable('getOptions');
                        // var ids = row[options.pk];
                        // row = $.extend({}, row ? row : {}, {ids: ids});
                        // var url = 'planmanagement/matchfinance/edit';
                        // Fast.api.open(Table.api.replaceurl(url, row, table), __('Edit'), $(this).data() || {});
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