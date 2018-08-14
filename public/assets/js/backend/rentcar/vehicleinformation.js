define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'rentcar/vehicleinformation/index',
                    add_url: 'rentcar/vehicleinformation/add',
                    edit_url: 'rentcar/vehicleinformation/edit',
                    del_url: 'rentcar/vehicleinformation/del',
                    multi_url: 'rentcar/vehicleinformation/multi',
                    table: 'car_rental_models_info',
                }
            });
            //实时消息
            var goeasy = new GoEasy({
                appkey: 'BC-04084660ffb34fd692a9bd1a40d7b6c2'
            });
            goeasy.subscribe({
                channel: 'demo',
                onMessage: function(message){
                    Layer.alert('新消息：'+message.content,{ icon:0},function(index){
                        Layer.close(index);
                        $(".btn-refresh").trigger("click");
                    });
                    
                }
            });
            
            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                searchFormVisible: true,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        // {field: 'sales_id', title: __('Sales_id')},
                        {field: 'licenseplatenumber', title: __('Licenseplatenumber')},
                        // {field: 'models_id', title: __('Models_id')},
                        {field: 'models.name', title: __('Models.name')},
                        {field: 'kilometres', title: __('Kilometres'), operate:'BETWEEN'},
                        {field: 'companyaccount', title: __('Companyaccount')},
                        {field: 'cashpledge', title: __('Cashpledge')},
                        {field: 'threemonths', title: __('Threemonths')},
                        {field: 'sixmonths', title: __('Sixmonths')},
                        {field: 'manysixmonths', title: __('Manysixmonths')},
                        {field: 'drivinglicenseimages', title: __('Drivinglicenseimages'), formatter: Table.api.formatter.images},
                        {field: 'vin', title: __('Vin')},
                        {field: 'expirydate', title: __('Expirydate'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'annualverificationdate', title: __('Annualverificationdate'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'carcolor', title: __('Carcolor')},
                        {field: 'aeratedcard', title: __('Aeratedcard')},
                        {field: 'volumekeys', title: __('Volumekeys')},
                        {field: 'Parkingposition', title: __('Parkingposition')},
                        {field: 'shelfismenu', title: __('Shelfismenu'), formatter: Table.api.formatter.toggle},
                        {field: 'vehiclestate', title: __('Vehiclestate')},
                        {field: 'note', title: __('Note')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        // {field: 'models.name', title: __('Models.name')},
                        {field: 'sales', title: __('预定销售人员')},
                        {field: 'operate', title: __('Operate'), table: table, 
                        buttons: [
                            {
                                name:'detail',text:'销售预定', title:'销售预定', icon: 'fa fa-share',extend: 'data-toggle="tooltip"',classname: 'btn btn-xs btn-info btn-dialog btn-newCustomer',
                                url: 'rentcar/vehicleinformation/salesbook',  
                                //等于null 的时候操作栏显示的是销售预定四个字，
                                //不等于null 的时候操作栏显示的是已有销售人员预定 四个字，
                                //....
                                hidden:function(row){ /**销售预定 */
                                    if(row.sales_id == null){ 
                                        return false; 
                                    }  
                                    else if(row.sales_id != null){
                                        return true;
                                    }
                                }
                            },
                            { 
                                name: 'del', icon: 'fa fa-trash', extend: 'data-toggle="tooltip"',title: __('Del'),classname: 'btn btn-xs btn-danger btn-delone',
                                url:'rentcar/vehicleinformation/del',/**删除 */
                                hidden:function(row){
                                    if(row.sales_id == null){ 
                                        return false; 
                                    }
                                    else if(row.sales_id != null){
                                      
                                        return true;
                                    } 
                                },
                                
                            },
                            { 
                                name: 'edit',text: '',icon: 'fa fa-pencil',extend: 'data-toggle="tooltip"',  title: __('Edit'),classname: 'btn btn-xs btn-success btn-editone', 
                                url:'rentcar/vehicleinformation/edit',/**编辑 */
                                hidden:function(row,value,index){ 
                                    if(row.sales_id == null){ 
                                        return false; 
                                    } 
                                    else if(row.sales_id != null){ 
                                        return true;
                                    } 
                                }, 
                            },
                            {
                                name: 'detail',icon:'fa fa-check-circle',text: '已有销售人员预定', classname: ' text-info ',
                                hidden:function(row){  /**已有销售人员预定 */ 
                                    if(row.sales_id != null){ 
                                        return false; 
                                    }
                                    else if(row.sales_id == null){ 
                                        return true; 
                                    }
                                }
                            },
                            { 
                                name: 'salesbookedit',text:'修改销售预定', title:'修改销售预定',icon: 'fa fa-pencil', extend: 'data-toggle="tooltip"',classname: 'btn btn-xs btn-success btn-dialog btn-newCustomer',
                                url:'rentcar/vehicleinformation/salesbookedit',/**修改销售预定 */
                                hidden:function(row){
                                    if(row.sales_id != null){ 
                                        return false; 
                                    }
                                    else if(row.sales_id == null){
                                      
                                        return true;
                                    } 
                                },
                                
                            },
                            { 
                                name: 'rentalrequest',text:'销售员租车请求', title:'销售员租车请求',icon: 'fa fa-automobile', extend: 'data-toggle="tooltip"',classname: 'btn btn-xs btn-success btn-dialog btn-rentalrequest',
                                // url:'rentcar/vehicleinformation/rentalrequest',/**销售员租车请求 */
                                hidden:function(row){
                                    if(row.review_the_data == 'is_reviewing'){ 
                                        return false; 
                                    }
                                    else if(row.review_the_data == 'is_reviewing_true'){
                                      
                                        return true;
                                    } 
                                    else if(row.review_the_data == ''){
                                      
                                        return true;
                                    } 
                                },
                                
                            },
                        ],
                            events: Controller.api.events.operate,
                             
                            formatter: Controller.api.formatter.operate
                           
                        }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        //销售预定
        salesbook:function(){
 
            // $(".btn-add").data("area", ["300px","200px"]);
            Table.api.init({
               
            });
            Form.api.bindevent($("form[role=form]"), function(data, ret){
                //这里是表单提交处理成功后的回调函数，接收来自php的返回数据
                Fast.api.close(data);//这里是重点
                // console.log(data);
                // Toastr.success("成功");//这个可有可无
            }, function(data, ret){
                // console.log(data); 
                Toastr.success("失败"); 
            });
            // Controller.api.bindevent();
            // console.log(Config.id);
 
        },
        //修改销售预定
        salesbookedit:function(){
 
            // $(".btn-add").data("area", ["300px","200px"]);
            Table.api.init({
               
            });
            Form.api.bindevent($("form[role=form]"), function(data, ret){
                //这里是表单提交处理成功后的回调函数，接收来自php的返回数据
                Fast.api.close(data);//这里是重点
                // console.log(data);
                // Toastr.success("成功");//这个可有可无
            }, function(data, ret){
                // console.log(data); 
                Toastr.success("失败"); 
            });
            // Controller.api.bindevent();
            // console.log(Config.id);
 
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
                    'click .btn-delone': function (e, value, row, index) {  /**编辑按钮 */

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
                    'click .btn-rentalrequest': function (e, value, row, index) {

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
                            __('确定可以进行租车?'),
                            {icon: 3, title: __('Warning'), offset: [top, left], shadeClose: true},

                            function (index) {
                                var table = $(that).closest('table');
                                var options = table.bootstrapTable('getOptions');


                                Fast.api.ajax({
                                    url: 'rentcar/vehicleinformation/rentalrequest',
                                    data: {id: row[options.pk]}
                                }, function (data, ret) {

                                    Toastr.success("成功");
                                    Layer.close(index);
                                    table.bootstrapTable('refresh');
                                    return false;
                                }, function (data, ret) {
                                    //失败的回调

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