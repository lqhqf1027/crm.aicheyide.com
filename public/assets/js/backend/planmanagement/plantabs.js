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
            first: function () {

                var table1 = $("#table1");

                table1.on('load-success.bs.table', function (e, data) {
                    var arr = data.rows;
                   // console.log(arr);
                    Controller.merge(arr, table1);

                });

                $.fn.bootstrapTable.locales[Table.defaults.locale]['formatSearch'] = function () {
                    return "快速搜索车型";
                };
                $(".btn-add").data("area", ["80%", "80%"]);
                 // 初始化表格
            table1.bootstrapTable({
                url: 'planmanagement/plantabs/table1',
                extend: {
                    index_url: 'plan/planacar/index',
                    add_url: 'planmanagement/plantabs/firstadd',
                    edit_url: 'planmanagement/plantabs/firstedit',
                    del_url: 'planmanagement/plantabs/firstdel',
                    multi_url: 'planmanagement/plantabs/firstmulti',
                    table: 'plan_acar',
                },
                toolbar: '#toolbar1',
                pk: 'id',
                sortName: 'id',
                searchFormVisible: true,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'),operate:false},
                        {field: 'schemecategory.name', title: __('方案类型'),formatter:function (v,r,i) {

                                return r.schemecategory.category_note!=null?v+"<br />"+'<u>'+r.schemecategory.category_note+'</u>':v;

                            }},
                        // {field: 'schemecategory.category_note', title: __('方案类型备注'),operate:false},
                        {field: 'models.name', title: '销售车型',operate:false,formatter:function (v,r,i) {

                                return v!=null?r.brand_name +'-'+v:v;
                            }},
                        // {field: 'financialplatform.name', title: '所属金融平台'},
                        {field: 'payment', title: __('Payment'), operate:'BETWEEN',operate:false},
                        {field: 'monthly', title: __('NewcarMonthly'), operate:'BETWEEN',operate:false},
                        {field: 'nperlist', title: __('Nperlist'), visible:false, searchList: {"12":__('Nperlist 12'),"24":__('Nperlist 24'),"36":__('Nperlist 36'),"48":__('Nperlist 48'),"60":__('Nperlist 60')}},
                        {field: 'nperlist_text', title: __('Nperlist'), operate:false},
                        {field: 'margin', title: __('Margin'), operate:'BETWEEN',operate:false},
                        {field: 'tail_section', title: __('Tail_section'), operate:'BETWEEN',operate:false},
                        {field: 'gps', title: __('Gps'), operate:false},
                        {field: 'admin.nickname', title: __('销售定制方案')},
                        {field: 'working_insurance', title: __('是否营运险'), searchList:{"yes":'是',"no":"否"},formatter:function (v,r,i) {
                            if(r.working_insurance=='yes'){
                                  return '是'  ;
                            }
                            else{
                                return '否';
                            }


                            }},

                        {field: 'note', title: __('Note'),operate:false},
                      
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime,datetimeFormat:'YYYY-MM-DD'},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime,datetimeFormat:'YYYY-MM-DD'},
                        {field: 'ismenu', title: __('Ismenu'), formatter: Controller.api.formatter.toggle,operate:false},
                      
                        {field: 'operate', title: __('Operate'), table: table1, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });
                // 为表格1绑定事件
                Table.api.bindevent(table1);
            },


            planfull: function () {
                // 表格3
                var table3 = $("#table3");
                table3.bootstrapTable({
                    url: 'planmanagement/plantabs/table3',
                    extend: {
                        index_url: 'plan/planfull/index',
                        add_url: 'planmanagement/plantabs/fulladd',
                        edit_url: 'planmanagement/plantabs/fulledit',
                        del_url: 'planmanagement/plantabs/fulldel',
                        multi_url: 'planmanagement/plantabs/fullmulti',
                        table: 'plan_full',
                    },
                    toolbar: '#toolbar3',
                    pk: 'id',
                    sortName: 'id',
                    columns: [
                        [
                            {checkbox: true},
                            {field: 'id', title: __('Id')},
                            {field: 'models.name', title: '销售车型',formatter:function (v,r,i) {

                                    return v!=null?r.brand_name +'-'+v:v;
                                }},
                            
                            {field: 'full_total_price', title: __('Full_total_price'), operate:'BETWEEN'},
                        
                            {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                            {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                            {field: 'ismenu', title: __('Ismenu'), formatter: Controller.api.formatter.toggle},
                            
                            {field: 'operate', title: __('Operate'), table: table3, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                        ]
                    ]
                });
                // 为表格3绑定事件
                Table.api.bindevent(table3);
            }
        },
        add: function () {

            Controller.api.bindevent();
        },
        edit: function () {
            alert(1);
            Controller.api.bindevent();
        },
        merge: function (arr, obj) {
            var hash = [];
            var data_arr = [];
            for (var i in arr) {



                if (hash.indexOf(arr[i]['schemecategory']['name']) == -1 ) {

                    hash.push(arr[i]['schemecategory']['name']);

                    data_arr.push([i, arr[i]['schemecategory']['name'], 0]);
                }


            }


            for (var i in arr) {
                for (var j in data_arr) {
                    if (arr[i]['schemecategory']['name'] == data_arr[j][1]) {
                        data_arr[j][2]++;
                    }
                }
            }


            for (var i in data_arr) {

                obj.bootstrapTable("mergeCells", {
                    index: data_arr[i][0],
                    field: 'schemecategory.name',
                    rowspan: data_arr[i][2]
                });

                var td = $(obj).find("tr[data-index=" + data_arr[i][0] + "]").find("td");

                if(data_arr[i][1]!=null){
                    i % 2 == 0 ? td.eq(2).css({"background-color": "#fff"}) : td.eq(2).css({"background-color": "#ddd"});
                }


            }
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
        fulledit: function () {
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
        firstadd:function(){
            Controller.api.bindevent();
        },
        fulladd:function(){
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
            formatter: {
                operate: function (value, row, index) {

                    var table = this.table;
                    // 操作配置
                    var options = table ? table.bootstrapTable('getOptions') : {};
                    // 默认按钮组
                    var buttons = $.extend([], this.buttons || []);

                    return Table.api.buttonlink(this, buttons, value, row, index, 'operate');
                },
                toggle: function (value, row, index) {
                    
                    var color = typeof this.color !== 'undefined' ? this.color : 'success';
                    var yes = typeof this.yes !== 'undefined' ? this.yes : 1;
                    var no = typeof this.no !== 'undefined' ? this.no : 0;
                    return "<a href='javascript:;' data-toggle='tooltip' title='" + __('Click to toggle') + "' class='btn-change' data-id='"
                            + row.id + "' data-params='" + this.field + "=" + (value ? no : yes) + "'><i class='fa fa-toggle-on " + (value == yes ? 'text-' + color : 'fa-flip-horizontal text-gray') + " fa-2x'></i></a>";
                    
                    
                }
               
            }
        }
         
    };
    return Controller;
});