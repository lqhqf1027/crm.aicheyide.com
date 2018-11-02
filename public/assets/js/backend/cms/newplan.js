define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'cms/newplan/index',
                    add_url: 'cms/newplan/add',
                    edit_url: 'cms/newplan/fistedit',
                    del_url: 'cms/newplan/del',
                    multi_url: 'cms/newplan/multi',
                    table: 'plan_acar',
                }
            });

            var table = $("#table");

            table.on('load-success.bs.table', function (e, data) {
                $(".btn-editone").data("area", ["50%", "50%"]);

            });

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {
                            checkbox: true, 
                        },
                        {field: 'id', title: __('Id'), operate: false},
                        {
                            field: 'subject.title', title: __('专题标题'), 
                        },
                        {
                            field: 'models.name', title: '销售车型', operate: false, formatter: function (v, r, i) {
                                return v != null ? "<img src="+r.brand_log+" alt='品牌logo' width='30' height='30'>" +r.brand_name + '-' + v : v;
                            }
                        },
                        {field: 'payment', title: __('首付（元）'), operate: 'BETWEEN', operate: false},
                        {field: 'monthly', title: __('月供（元）'), operate: 'BETWEEN', operate: false},
                        {
                            field: 'nperlist',
                            title: __('期数'),
                            visible: false,
                            searchList: {
                                "12": __('Nperlist 12'),
                                "24": __('Nperlist 24'),
                                "36": __('Nperlist 36'),
                                "48": __('Nperlist 48'),
                                "60": __('Nperlist 60')
                            }
                        },
                        {field: 'nperlist_text', title: __('Nperlist'), operate: false},
                        {field: 'margin', title: __('保证金（元）'), operate: 'BETWEEN', operate: false},
                        {field: 'tail_section', title: __('尾款（元）'), operate: 'BETWEEN', operate: false},
                        {field: 'gps', title: __('GPS（元）'), operate: false},
                        {field: 'guide_price', title: __('厂商指导价（元）'), operate: false},
                        {
                            field: 'flashviewismenu',
                            title: __('是否为首页轮播'),
                            formatter: Controller.api.formatter.toggle,searchList:{"1":"是","0":"否"}
                        },
                        {field: 'note', title: __('销售方案备注'), operate: false},
                        {
                            field: 'createtime',
                            title: __('创建时间'),
                            operate: 'RANGE',
                            addclass: 'datetimerange',
                            formatter: Table.api.formatter.datetime,
                            datetimeFormat: 'YYYY-MM-DD'
                        },
                        {
                            field: 'updatetime',
                            title: __('更新时间'),
                            operate: 'RANGE',
                            addclass: 'datetimerange',
                            formatter: Table.api.formatter.datetime,
                            datetimeFormat: 'YYYY-MM-DD'
                        },
                        {
                            field: 'operate',title: __('Operate'),table: table,
                            buttons: [
                                {
                                    name: 'firstedit',
                                    icon: 'fa fa-pencil',
                                    text:'编辑方案',
                                    title:'编辑方案', 
                                    extend: 'data-toggle="tooltip"',
                                    classname: 'btn btn-xs btn-info btn-editone',
                                },
                            ],
                            events: Controller.api.events.operate,
                            formatter: Controller.api.formatter.operate
                        }
                    ]
                ]
            });

            // 为表格1绑定事件
            Table.api.bindevent(table);

            
        },
        // table: {
        //     /**
        //      * （以租代购）新车
        //      */
        //     first: function () {

        //         var table1 = $("#table1");

        //         table1.on('load-success.bs.table', function (e, data) {
        //             $(".btn-editone").data("area", ["50%", "50%"]);

        //         });

        //         $.fn.bootstrapTable.locales[Table.defaults.locale]['formatSearch'] = function () {
        //             return "快速搜索车型";
        //         };
        //         $(".btn-add").data("area", ["80%", "80%"]);
        //         // 初始化表格
        //         table1.bootstrapTable({
        //             url: 'cms/newplan/table1',
        //             extend: {
        //                 index_url: 'cms/newplan/index',
        //                 add_url: 'cms/newplan/add',
        //                 edit_url: 'cms/newplan/firstedit',
        //                 del_url: 'cms/newplan/del',
        //                 multi_url: 'cms/newplan/multi',
        //                 table: 'plan_acar',
        //             },
        //             toolbar: '#toolbar1',
        //             pk: 'id',
        //             sortName: 'id',
        //             searchFormVisible: true,
        //             columns: [
        //                 [
        //                     {
        //                         checkbox: true, 
        //                     },
        //                     {field: 'id', title: __('Id'), operate: false},
        //                     {
        //                         field: 'subject.title', title: __('专题标题'), 
        //                     },
        //                     {
        //                         field: 'models.name', title: '销售车型', operate: false, formatter: function (v, r, i) {
        //                             return v != null ? "<img src="+r.brand_log+" alt='品牌logo' width='30' height='30'>" +r.brand_name + '-' + v : v;
        //                         }
        //                     },
        //                     {field: 'payment', title: __('首付（元）'), operate: 'BETWEEN', operate: false},
        //                     {field: 'monthly', title: __('月供（元）'), operate: 'BETWEEN', operate: false},
        //                     {
        //                         field: 'nperlist',
        //                         title: __('期数'),
        //                         visible: false,
        //                         searchList: {
        //                             "12": __('Nperlist 12'),
        //                             "24": __('Nperlist 24'),
        //                             "36": __('Nperlist 36'),
        //                             "48": __('Nperlist 48'),
        //                             "60": __('Nperlist 60')
        //                         }
        //                     },
        //                     {field: 'nperlist_text', title: __('Nperlist'), operate: false},
        //                     {field: 'margin', title: __('保证金（元）'), operate: 'BETWEEN', operate: false},
        //                     {field: 'tail_section', title: __('尾款（元）'), operate: 'BETWEEN', operate: false},
        //                     {field: 'gps', title: __('GPS（元）'), operate: false},
        //                     {field: 'guide_price', title: __('厂商指导价（元）'), operate: false},
        //                     {
        //                         field: 'recommendismenu',
        //                         title: __('是否为推荐'),
        //                         formatter: Controller.api.formatter.toggle,searchList:{"1":"是","0":"否"}
        //                     },
        //                     {field: 'note', title: __('销售方案备注'), operate: false,},
        //                     {
        //                         field: 'createtime',
        //                         title: __('创建时间'),
        //                         operate: 'RANGE',
        //                         addclass: 'datetimerange',
        //                         formatter: Table.api.formatter.datetime,
        //                         datetimeFormat: 'YYYY-MM-DD'
        //                     },
        //                     {
        //                         field: 'updatetime',
        //                         title: __('更新时间'),
        //                         operate: 'RANGE',
        //                         addclass: 'datetimerange',
        //                         formatter: Table.api.formatter.datetime,
        //                         datetimeFormat: 'YYYY-MM-DD'
        //                     },

        //                     {
        //                         field: 'operate',title: __('Operate'),table: table1,
        //                         buttons: [
        //                             {
        //                                 name: 'firstedit',
        //                                 icon: 'fa fa-pencil',
        //                                 text:'编辑方案',
        //                                 title:'编辑方案', 
        //                                 extend: 'data-toggle="tooltip"',
        //                                 classname: 'btn btn-xs btn-info btn-editone',
        //                             },
        //                         ],
        //                         events: Controller.api.events.operate,
        //                         formatter: Controller.api.formatter.operate
        //                     }
        //                 ]
        //             ]
        //         });
        //         // 为表格1绑定事件
        //         Table.api.bindevent(table1);
        
        //     },

        // },
        add: function () {
            
            Controller.api.bindevent();
        },
        edit: function () {

            Controller.api.bindevent();
        },
        firstedit: function () {
            // $(".btn-add").data("area", ["300px","200px"]);
            Table.api.init({});
            Form.api.bindevent($("form[role=form]"), function (data, ret) {
                //这里是表单提交处理成功后的回调函数，接收来自php的返回数据

                Fast.api.close(data);//这里是重点
                // console.log(data);
                // Toastr.success("成功");//这个可有可无
            }, function (data, ret) {
                // console.log(data);
                Toastr.success("失败");
            });

            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                $(document).on('click', "input[name='row[flashviewismenu]']", function () {
                    var name = $("input[name='row[name]']");
                    name.prop("placeholder", $(this).val() == 1 ? name.data("placeholder-menu") : name.data("placeholder-node"));
                });
                $("input[name='row[flashviewismenu]']:checked").trigger("click");
                Form.api.bindevent($("form[role=form]"));
            },
            events: {
                operate: {
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
                /**
                 * 是否
                 * @param value
                 * @param row
                 * @param index
                 * @returns {string}
                 */
                toggle: function (value, row, index) {
                    
                    var color = typeof this.color !== 'undefined' ? this.color : 'success';
                    var yes = typeof this.yes !== 'undefined' ? this.yes : 1;
                    var no = typeof this.no !== 'undefined' ? this.no : 0;
                    return "<a href='javascript:;' data-toggle='tooltip' title='" + __('Click to toggle') + "' class='btn-change' data-id='"
                            + row.id + "' data-params='" + this.field + "=" + (value ? no : yes) + "'><i class='fa fa-toggle-on " + (value == yes ? 'text-' + color : 'fa-flip-horizontal text-gray') + " fa-2x'></i></a>";
                   
                    
                }

            },
            
        }

    };
    return Controller;
});