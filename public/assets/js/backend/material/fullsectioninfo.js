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

            full_register: function () {
                // 表格1
                var fullRegister = $("#fullRegister");

                fullRegister.on('post-body.bs.table', function (e, settings, json, xhr) {
                    $(".btn-editone").data("area", ["80%", "80%"]);
                    $(".btn-detail").data("area", ["95%", "95%"]);
                    $(".btn-edit").data("area", ["80%", "80%"]);
                });
                // 初始化表格
                fullRegister.bootstrapTable({
                    url: 'material/Fullsectioninfo/full_register',
                    extend: {
                        edit_url: 'material/fullsectioninfo/edit',
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
                            {field: 'sales_name', title: __('部门-销售员'),operate: false},
                            {
                                field: 'signdate',
                                title: __('签订日期'),
                                operate: false
                            },
                            {field: 'username', title: __('Username'),formatter:Controller.api.formatter.inspection},
                            {field: 'id_card', title: __('身份证号')},
                            {field: 'phone', title: __('联系方式')},
                            {field: 'hostdate', title: __('上户日期'), operate: false},
                            {field: 'models_name', title: __('规格型号')},
                            {field: 'licensenumber', title: __('车牌号')},
                            {field: 'frame_number', title: __('车架号')},
                            {field: 'mortgage_people', title: __('抵押人')},
                            {
                                field: 'operate', title: __('Operate'), table: fullRegister,
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
                Table.api.bindevent(fullRegister);

                // 批量分配
                $(document).on("click", ".btn-selected", function () {
                    var ids = Table.api.selectedids(fullRegister);
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
                        var url = "material/Fullsectioninfo/detail";
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
                },

                inspection:function (value, row, index) {

                    var first = row.mon_first;
                    var last = row.mon_last;
                    var now = new Date().getTime();

                    first = new Date(first).getTime();
                    last = new Date(last).getTime();

                    if(now>first && now<last){
                        return value+"<span class='label label-warning' style='cursor: pointer'>即将年检</span>";
                    }else if(now>last){
                        return value+"<span class='label label-danger' style='cursor: pointer'>年检已过期</span>";
                    }else{
                        return value;
                    }
                }
            }
        }

    };
    return Controller;
});