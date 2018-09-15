define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var goeasy = new GoEasy({
        appkey: 'BC-04084660ffb34fd692a9bd1a40d7b6c2'
    });

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'secondhandcar/secondvehicleinformation/index',
                    add_url: 'secondhandcar/secondvehicleinformation/add',
                    edit_url: 'secondhandcar/secondvehicleinformation/edit',
                    del_url: 'secondhandcar/secondvehicleinformation/del',
                    multi_url: 'secondhandcar/secondvehicleinformation/multi',
                    table: 'secondcar_rental_models_info',
                }
            });

            var table = $("#table");
            $.fn.bootstrapTable.locales[Table.defaults.locale]['formatSearch'] = function () {
                return "快速搜索车牌号";
            };
            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                searchFormVisible: true,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'),operate:false},
                        // {field: 'sales_id', title: __('Sales_id')},
                        {field: 'licenseplatenumber', title: __('Licenseplatenumber'), formatter:function(value,row,index){
                            if(row.status_data !=  ""){
                                return row.licenseplatenumber+' <span class="label label-danger">该车在签单流程中</span>';

                            }
                            else{
                                return row.licenseplatenumber
                            }
                        }},
                        // {field: 'models_id', title: __('Models_id')},
                        {field: 'models.name', title: __('Models.name')},
                        {field: 'kilometres', title: __('Kilometres'), operate:'BETWEEN',operate:false},
                        {field: 'companyaccount', title: __('Companyaccount')},
                        {field: 'newpayment', title: __('Newpayment'),operate:false},
                        {field: 'monthlypaymen', title: __('Monthlypaymen'),operate:false},
                        {field: 'periods', title: __('Periods'),operate:false},
                        {field: 'totalprices', title: __('Totalprices'),operate:false},
                        {field: 'drivinglicenseimages', title: __('Drivinglicenseimages'), formatter: Table.api.formatter.images,operate:false},
                        {field: 'vin', title: __('Vin')},
                        {field: 'engine_number', title: __('发动机号')},

                        {field: 'expirydate', title: __('Expirydate'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime,datetimeFormat:'YYYY-MM-DD'},
                        {field: 'annualverificationdate', title: __('Annualverificationdate'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime,datetimeFormat:'YYYY-MM-DD'},
                        {field: 'carcolor', title: __('Carcolor')},
                        {field: 'aeratedcard', title: __('Aeratedcard')},
                        {field: 'volumekeys', title: __('Volumekeys'),operate:false},
                        {field: 'Parkingposition', title: __('Parkingposition'),operate:false},
                        {field: 'shelfismenu', title: __('Shelfismenu'), formatter: Controller.api.formatter.toggle,searchList:{"1":"是","0":"否"}},
                        // {field: 'shelf_text', title: __('Shelf'), operate:false},
                        {field: 'vehiclestate', title: __('Vehiclestate'),operate:false},
                        {field: 'note', title: __('Note'),operate:false},
                        {field: 'createtime', title: __('Createtime'), operate:false, addclass:'datetimerange', formatter: Table.api.formatter.datetime,datetimeFormat:'YYYY-MM-DD'},
                        {field: 'updatetime', title: __('Updatetime'), operate:false, addclass:'datetimerange', formatter: Table.api.formatter.datetime,datetimeFormat:'YYYY-MM-DD'},
                        // {field: 'models.name', title: __('Models.name')},
                        {field: 'operate', title: __('Operate'), table: table, 
                        buttons: [
                            {
                                name: '', icon: 'fa fa-check-circle', text: '等待出售', classname: ' text-info ',
                                hidden: function (row) {  /**等待出售 */
                                    if(row.status_data == ''){
                                        return false; 
                                    }
                                    else if(row.status_data == 'for_the_car'){
                                      
                                        return true;
                                    } 
                                    else if(row.status_data == 'the_car'){
                                      
                                        return true;
                                    } 
                                }
                            },
                            {
                                name: 'for_the_car', icon: 'fa fa-check-circle', text: '该车在签单流程中', classname: ' text-info ',
                                hidden: function (row) {  /**该车在签单流程中 */
                                    if(row.status_data == 'for_the_car'){
                                        return false; 
                                    } 
                                    else if(row.status_data == ''){
                                      
                                        return true;
                                    } 
                                    else if(row.status_data == 'the_car'){
                                      
                                        return true;
                                    } 
                                }
                            },
                            /**
                             * 删除
                             */
                            { 
                                icon: 'fa fa-trash', name: 'del', icon: 'fa fa-trash', extend: 'data-toggle="tooltip"',text:'删除', title: __('删除'),classname: 'btn btn-xs btn-danger btn-delone',
                                // url:'rentcar/vehicleinformation/del',/** */
                                hidden:function(row){
                                    if(row.status_data == ''){
                                        return false; 
                                    }
                                    else if(row.status_data == 'for_the_car'){
                                      
                                        return true;
                                    } 
                                    else if(row.status_data == 'the_car'){
                                      
                                        return true;
                                    } 
                                   
                                },
                                
                            },
                            /**
                             * 编辑
                             */
                            { 
                                name: 'edit',text: '',icon: 'fa fa-pencil',extend: 'data-toggle="tooltip"',text:'编辑', title: __('编辑'),classname: 'btn btn-xs btn-success btn-editone',
                                // url:'rencar/vehicleinformation/edit',/**编辑信息 */
                                hidden:function(row,value,index){ 
                                    if(row.status_data == ''){
                                        return false; 
                                    } 
                                    else if(row.status_data == 'for_the_car'){
                                      
                                        return true;
                                    } 
                                    else if(row.status_data == 'the_car'){
                                      
                                        return true;
                                    } 
                                }, 
                            },
                            {

                                name: 'the_car', icon: 'fa fa-automobile', text: '已提车', extend: 'data-toggle="tooltip"', title: __('订单已完成，客户已提车'), classname: ' text-success ',
                                hidden: function (row) {  /**已提车 */
                                    if(row.status_data == 'the_car'){
                                        return false; 
                                    }
                                    else if(row.status_data == ''){
                                      
                                        return true;
                                    }
                                    else if(row.status_data == 'for_the_car'){
                                      
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
            //风控通过---可以提车
            goeasy.subscribe({
                channel: 'demo-second_pass',
                onMessage: function(message){
                    Layer.alert('新消息：'+message.content,{ icon:0},function(index){
                        Layer.close(index);
                        $(".btn-refresh").trigger("click");
                    });
                    
                }
            });

            // 为表格绑定事件
            Table.api.bindevent(table);

            table.on('load-success.bs.table', function (e, data) {
               
                $(".btn-add").data("area", ["90%", "90%"]);
                $(".btn-edit").data("area", ["90%", "90%"]);
                
            })
        },
        
        
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                $(document).on('click', "input[name='row[shelfismenu]']", function () {
                    var name = $("input[name='row[name]']");
                    name.prop("placeholder", $(this).val() == 1 ? name.data("placeholder-menu") : name.data("placeholder-node"));
                });
                $("input[name='row[shelfismenu]']:checked").trigger("click");
                Form.api.bindevent($("form[role=form]"));
            },
            events:{
                operate: {
                    /**编辑按钮 */
                    'click .btn-editone': function (e, value, row, index) {
                    $(".btn-editone").data("area", ["95%","95%"]); 

                        e.stopPropagation();
                        e.preventDefault();
                        var table = $(this).closest('table');
                        var options = table.bootstrapTable('getOptions');
                        var ids = row[options.pk];
                        row = $.extend({}, row ? row : {}, {ids: ids});
                        var url = options.extend.edit_url;
                        Fast.api.open(Table.api.replaceurl(url, row, table), __('Edit'), $(this).data() || {});
                    },
                    /**删除按钮 */
                    'click .btn-delone': function (e, value, row, index) {  

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
                            {icon: 3, title: __('Warning'), offset: [top, left], shadeClose: true},
                            function (index) {
                                var table = $(that).closest('table');
                                var options = table.bootstrapTable('getOptions');
                                Table.api.multi("del", row[options.pk], table, that);
                                Layer.close(index);
                            }
                        );
                    },
                    /**确认提车按钮 */
                    'click .btn-takecar': function (e, value, row, index) {  

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
                            __('确定进行车辆提取吗?'),
                            { icon: 3, title: __('Warning'), offset: [top, left], shadeClose: true },

                            function (index) {
                                var table = $(that).closest('table');
                                var options = table.bootstrapTable('getOptions');


                                Fast.api.ajax({

                                    url: 'secondhandcar/secondvehicleinformation/takecar',
                                    data: {id: row[options.pk]}
 
                                }, function (data, ret) {

                                    Toastr.success(ret.msg);
                                    Layer.close(index);
                                    table.bootstrapTable('refresh');

                                    Layer.alert('提车成功后，可到二手车客户信息查看客户信息',{ icon:0},function(index){
                                        Layer.close(index);
                                        $(".btn-refresh").trigger("click");
                                    });
                                    
                                    return false;
                                }, function (data, ret) {
                                    //失败的回调
                                    Toastr.success(ret.msg);

                                    return false;
                                });


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
                },
                toggle: function (value, row, index) {
                    if(row.status_data == ''){
                        var color = typeof this.color !== 'undefined' ? this.color : 'success';
                        var yes = typeof this.yes !== 'undefined' ? this.yes : 1;
                        var no = typeof this.no !== 'undefined' ? this.no : 0;
                        return "<a href='javascript:;' data-toggle='tooltip' title='" + __('Click to toggle') + "' class='btn-change' data-id='"
                            + row.id + "' data-params='" + this.field + "=" + (value ? no : yes) + "'><i class='fa fa-toggle-on " + (value == yes ? 'text-' + color : 'fa-flip-horizontal text-gray') + " fa-2x'></i></a>";
                    }
                    else{
                        return "该车在签单流程中";
                    }
                    
                }
                // status: function (value, row, index) {

                //     var colorArr = {relation: 'info', intention: 'success', nointention: 'danger'};
                //     //如果字段列有定义custom
                //     if (typeof this.custom !== 'undefined') {
                //         colorArr = $.extend(colorArr, this.custom);
                //     }
                //     value = value === null ? '' : value.toString();

                //     var color = value && typeof colorArr[value] !== 'undefined' ? colorArr[value] : 'primary';

                //     var newValue = value.charAt(0).toUpperCase() + value.slice(1);
                //     //渲染状态
                //     var html = '<span class="text-' + color + '"><i class="fa fa-circle"></i> ' + __(newValue) + '</span>';
                //     // if (this.operate != false) {
                //     //     html = '<a href="javascript:;" class="searchit" data-toggle="tooltip" title="' + __('Click to search %s', __(newValue)) + '" data-field="' + this.field + '" data-value="' + value + '">' + html + '</a>';
                //     // }
                //     return html;
                // },
            }
        }
    };
    return Controller;
});