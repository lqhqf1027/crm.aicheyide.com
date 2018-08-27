define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

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

            prepare_match: function () {
                // 表格1
                var prepareMatch = $("#prepareMatch");
                prepareMatch.on('load-success.bs.table', function (e, data) {
                    console.log(data.total);
                    $('#badge_prepare').text(data.total);

                });
                prepareMatch.on('post-body.bs.table', function (e, settings, json, xhr) {
                    $(".btn-editone").data("area", ["40%", "40%"]);
                    $(".btn-test").data("area", ["40%", "40%"]);
                    // $(".btn-showOrder").data("area", ["95%", "95%"]);
                });
                // 初始化表格
                prepareMatch.bootstrapTable({
                    url: "planmanagement/matchfinance/prepare_match",
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
                            {field: 'createtime', title: __('订车日期')},
                            {field: 'household', title: __('公司')},
                            {field: 'sales_name', title: __('销售员')},
                            {field: 'username', title: __('客户姓名')},
                            // {field: 'id_card', title: __('身份证号')},
                            // {field: 'detailed_address', title: __('地址')},
                            // {field: 'phone', title: __('联系电话')},
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
                            // {field: 'engine_number', title: __('发动机号')},
                            // {field: 'household', title: __('行驶证所有户')},
                            {field: '4s_shop', title: __('4S店')},
                            {field: 'amount_collected', title: __('实收金额')},
                            {field: 'decorate', title: __('装饰')},

                            {
                                field: 'operate', title: __('Operate'), table: prepareMatch,
                                buttons: [
                                    {
                                        name: 'detail',
                                        text: '匹配金融',
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
                Table.api.bindevent(prepareMatch);

                Controller.api.bindevent(prepareMatch);

                  $(document).on('click','.btn-test',function () {

                      var ids = Table.api.selectedids(prepareMatch);

                      Layer.prompt(

                          { title: __('请匹配对应的金融平台'), shadeClose: true },
                          function (text, index) {
                              Fast.api.ajax({
                                  url:"planmanagement/matchfinance/batch",
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
            already_match: function () {

                // 表格2
                var alreadyMatch = $("#alreadyMatch");
                alreadyMatch.on('load-success.bs.table', function (e, data) {
                    console.log(data.total);
                    $('#badge_already').text(data.total);

                });
                alreadyMatch.on('post-body.bs.table', function (e, settings, json, xhr) {
                    $(".btn-showOrderAndStock").data("area", ["95%", "95%"]);
                });
                // 初始化表格
                alreadyMatch.bootstrapTable({
                    url: "planmanagement/matchfinance/already_match",
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
                            // {field: 'id_card', title: __('身份证号')},
                            // {field: 'detailed_address', title: __('地址')},
                            // {field: 'phone', title: __('联系电话')},
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
                            // {field: 'household', title: __('行驶证所有户')},
                            {field: '4s_shop', title: __('4S店')},
                            {field: 'amount_collected', title: __('实收金额')},
                            {field: 'decorate', title: __('装饰')},

                        ]
                    ]
                });
                // 为表格2绑定事件
                Table.api.bindevent(alreadyMatch);


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
                    'click .btn-editone': function (e, value, row, index) {
                        var table = $(this).closest('table');
                        Layer.prompt(
                            // __('请输入客户手机服务密码'),测试服务密码：202304
                            { title: __('请匹配对应的金融平台'), shadeClose: true },
                            //text为输入的服务密码
                            function (text, index) {
                                Fast.api.ajax({
                                    url:"planmanagement/matchfinance/edit",
                                    data:{
                                        text:text,
                                        id:row.id
                                    }
                                },function (data,ret) {
                                    layer.close(index);
                                    table.bootstrapTable('refresh');
                                },function (data,ret) {
                                    alert(ret);
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