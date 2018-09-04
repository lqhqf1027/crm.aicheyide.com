
define(['jquery', 'bootstrap', 'backend', 'table', 'form','echarts', 'echarts-theme','addtabs'], function ($, undefined, Backend, Table, Form,Echarts, undefined, Template) {

    var goeasy = new GoEasy({
        appkey: 'BC-04084660ffb34fd692a9bd1a40d7b6c2'
    });
    var Controller = {
        index: function () {

            // // 初始化表格参数配置
            Table.api.init({
            });
            // //绑定事件
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
        //批量导入
        import: function () {
            // console.log(123);
            // return;
            Form.api.bindevent($("form[role=form]"), function (data, ret) {
                //这里是表单提交处理成功后的回调函数，接收来自php的返回数据
                Fast.api.close(data);//这里是重点
                console.log(data);
                // Toastr.success("成功");//这个可有可无
            }, function (data, ret) {
                // console.log(data);

                Toastr.success("失败");

            });
            Controller.api.bindevent();
            // console.log(Config.id);

        },

        table: {
            /**新车 */
            newcar_monthly: function () {
                // 待审核
                var newcarMonthly = $("#newcarMonthly");
                newcarMonthly.bootstrapTable({
                    url: 'financial/monthlytabs/newcarMonthly',
                    extend: {
                        add_url: 'monthly/newcarmonthly/add',
                        edit_url: 'monthly/newcarmonthly/edit',
                        del_url: 'monthly/newcarmonthly/del',
                        import_url: 'monthly/newcarmonthly/import',
                        multi_url: 'monthly/newcarmonthly/multi',
                        table: 'monthly',
                    },
                    toolbar: '#toolbar1',
                    pk: 'id',
                    sortName: 'id',
                    search:false,
                    searchFormVisible: true,
                    columns: [
                        [
                            {checkbox: true},
                            {field: 'id', title: __('Id')},
                            {field: 'monthly_card_number', title: __('Monthly_card_number')},
                            {field: 'monthly_name', title: __('Monthly_name')},
                            {field: 'monthly_phone_number', title: __('Monthly_phone_number')},
                            {field: 'monthly_models', title: __('Monthly_models')},
                            {field: 'monthly_monney', title: __('Monthly_monney'), operate:'BETWEEN'},
                            {field: 'monthly_data', title: __('Monthly_data'), searchList: {"failure":__('Monthly_data failure'),"success":__('Monthly_data success')}, formatter: Table.api.formatter.normal},
                            {field: 'monthly_failure_why', title: __('Monthly_failure_why')},
                            {field: 'monthly_in_arrears_time', title: __('Monthly_in_arrears_time'), operate:'RANGE', addclass:'datetimerange'},
                            {field: 'monthly_company', title: __('Monthly_company')},
                            {field: 'monthly_car_number', title: __('Monthly_car_number')},
                            {field: 'monthly_arrears_months', title: __('Monthly_arrears_months')},
                            {field: 'monthly_note', title: __('Monthly_note')},
                            {
                                field: 'operate', title: __('Operate'), table: newcarMonthly,
                                buttons: [
                                    {
                                        icon: 'fa fa-trash', name: 'del', icon: 'fa fa-trash', extend: 'data-toggle="tooltip"', title: __('Del'), classname: 'btn btn-xs btn-danger btn-delone',
                                        url: 'monthly/newcarmonthly/del',/**删除 */


                                    },
                                    {
                                        name: 'edit', text: '', icon: 'fa fa-pencil', extend: 'data-toggle="tooltip"', title: __('Edit'), classname: 'btn btn-xs btn-success btn-editone',
                                        url: 'monthly/newcarmonthly/edit',/**编辑 */

                                    },


                                ],
                                events: Controller.api.events.operate,

                                formatter: Controller.api.formatter.operate

                            }
                        ]
                    ]
                });
                Table.api.bindevent(newcarMonthly);
                newcarMonthly.on('load-success.bs.table', function (e, data) {

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
                    //编辑按钮
                    'click .btn-editone': function (e, value, row, index) { /**编辑按钮 */
                    $(".btn-editone").data("area", ["95%", "95%"]);

                        e.stopPropagation();
                        e.preventDefault();
                        var table = $(this).closest('table');
                        var options = table.bootstrapTable('getOptions');
                        var ids = row[options.pk];
                        row = $.extend({}, row ? row : {}, { ids: ids });
                        var url = options.extend.edit_url;
                        Fast.api.open(Table.api.replaceurl(url, row, table), __('Edit'), $(this).data() || {});
                    },
                    //删除按钮
                    'click .btn-delone': function (e, value, row, index) {  /**删除按钮 */

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
                            __('Are you sure you want to delete this item?'),
                            { icon: 3, title: __('Warning'), offset: [top, left], shadeClose: true },
                            function (index) {
                                var table = $(that).closest('table');
                                var options = table.bootstrapTable('getOptions');
                                Table.api.multi("del", row[options.pk], table, that);
                                Layer.close(index);
                            }
                        );
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
    return Controller;
});