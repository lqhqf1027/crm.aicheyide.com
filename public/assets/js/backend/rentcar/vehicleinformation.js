define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var goeasy = new GoEasy({
        appkey: 'BC-04084660ffb34fd692a9bd1a40d7b6c2'
    });

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
           
            goeasy.subscribe({
                channel: 'demo',
                onMessage: function(message){
                    Layer.alert('新消息：'+message.content,{ icon:0},function(index){
                        Layer.close(index);
                        $(".btn-refresh").trigger("click");
                    });
                    
                }
            });

            //预定
            goeasy.subscribe({
                channel: 'demo-reserve',
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


                        // {field: 'models.name', title: __('Models.name')},
                        {field: 'sales.nickname', title: __('预定销售人员')},
                        {field: 'note', title: __('Note')},
                        {field: 'operate', title: __('Operate'), table: table, 
                        buttons: [
                            { 
                                name: 'rentalrequest',text:'销售员租车请求', title:'销售员租车请求',icon: 'fa fa-automobile', extend: 'data-toggle="tooltip"',classname: 'btn btn-xs btn-success btn-dialog btn-rentalrequest',
                                // url:'rentcar/vehicleinformation/rentalrequest',/**销售员租车请求 */
                                hidden:function(row){
                                    if(row.status == 'is_reviewing'){
                                        return false; 
                                    }
                                    else if(row.status == 'is_reviewing_true'){
                                      
                                        return true;
                                    } 
                                    else if(row.status == 'is_reviewing_pass'){
                                      
                                        return true;
                                    } 
                                    else if(row.status == 'for_the_car'){
                                      
                                        return true;
                                    } 
                                    else if(row.status == 'the_car'){
                                      
                                        return true;
                                    } 
                                    else if(row.status == ''){
                                      
                                        return true;
                                    } 
                                },
                                
                            },
                            { 
                                name: 'is_reviewing_pass',text:'打印提车单', title:'打印提车单',icon: 'fa fa-automobile', extend: 'data-toggle="tooltip"',classname: 'btn btn-xs btn-success btn-dialog btn-carsingle',
                                // url:'rentcar/vehicleinformation/rentalrequest',/**打印提车单 */
                                hidden:function(row){
                                    if(row.status == 'is_reviewing_pass'){
                                        return false; 
                                    }
                                    else if(row.status == 'is_reviewing_true'){
                                      
                                        return true;
                                    } 
                                    else if(row.status == 'is_reviewing'){
                                      
                                        return true;
                                    } 
                                    else if(row.status == 'for_the_car'){
                                      
                                        return true;
                                    } 
                                    else if(row.status == 'the_car'){
                                      
                                        return true;
                                    } 
                                    else if(row.status == ''){
                                      
                                        return true;
                                    } 
                                },
                                
                            },
                            { 
                                name: 'for_the_car',text:'确认提车', title:'确认提车',icon: 'fa fa-automobile', extend: 'data-toggle="tooltip"',classname: 'btn btn-xs btn-success btn-dialog btn-takecar',
                                // url:'rentcar/vehicleinformation/rentalrequest',/**打印提车单 */
                                hidden:function(row){
                                    if(row.status == 'for_the_car'){
                                        return false; 
                                    }
                                    else if(row.status == 'is_reviewing_pass'){
                                      
                                        return true;
                                    } 
                                    else if(row.status == 'is_reviewing_true'){
                                      
                                        return true;
                                    } 
                                    else if(row.status == 'is_reviewing'){
                                      
                                        return true;
                                    } 
                                    else if(row.status == 'the_car'){
                                      
                                        return true;
                                    } 
                                    else if(row.status == ''){
                                      
                                        return true;
                                    } 
                                },
                                
                            },
                            {
                                name: 'is_reviewing_true', icon: 'fa fa-check-circle', text: '已有销售预定', classname: ' text-info ',
                                hidden: function (row) {  /**已有销售预定 */
                                    if(row.status == 'is_reviewing_true'){
                                        return false; 
                                    }
                                    else if(row.status == 'is_reviewing_pass'){
                                      
                                        return true;
                                    } 
                                    else if(row.status == 'for_the_car'){
                                      
                                        return true;
                                    } 
                                    else if(row.status == 'is_reviewing'){
                                      
                                        return true;
                                    } 
                                    else if(row.status == 'the_car'){
                                      
                                        return true;
                                    } 
                                    else if(row.status == ''){
                                      
                                        return true;
                                    } 
                                }
                            },
                            {
                                name: '', icon: 'fa fa-check-circle', text: '等待出租', classname: ' text-info ',
                                hidden: function (row) {  /**等待出租 */
                                    if(row.status == ''){
                                        return false; 
                                    }
                                    else if(row.status == 'is_reviewing_pass'){
                                      
                                        return true;
                                    } 
                                    else if(row.status == 'for_the_car'){
                                      
                                        return true;
                                    } 
                                    else if(row.status == 'is_reviewing'){
                                      
                                        return true;
                                    } 
                                    else if(row.status == 'the_car'){
                                      
                                        return true;
                                    } 
                                    else if(row.status == 'is_reviewing_true'){
                                      
                                        return true;
                                    } 
                                }
                            },
                            {

                                name: 'the_car', icon: 'fa fa-automobile', text: '已提车', extend: 'data-toggle="tooltip"', title: __('订单已完成，客户已提车'), classname: ' text-success ',
                                hidden: function (row) {  /**已提车 */
                                    if(row.status == 'the_car'){
                                        return false; 
                                    }
                                    else if(row.status == ''){
                                      
                                        return true;
                                    } 
                                    else if(row.status == 'is_reviewing_pass'){
                                      
                                        return true;
                                    } 
                                    else if(row.status == 'for_the_car'){
                                      
                                        return true;
                                    } 
                                    else if(row.status == 'is_reviewing'){
                                      
                                        return true;
                                    } 
                                    else if(row.status == 'is_reviewing_true'){
                                      
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

            // 为表格绑定事件
            Table.api.bindevent(table);
            //风控通过---可以提车
            goeasy.subscribe({
                channel: 'demo-rentalpass',
                onMessage: function(message){
                    Layer.alert('新消息：'+message.content,{ icon:0},function(index){
                        Layer.close(index);
                        $(".btn-refresh").trigger("click");
                    });
                    
                }
            });

            //数据实时统计
            table.on('load-success.bs.table', function (e, data) {
               
                $(".btn-carsingle").data("area", ["80%", "80%"]);
                $(".btn-add").data("area", ["90%", "90%"]);
                $(".btn-edit").data("area", ["90%", "90%"]);
                
            })

        },
        carsingle: function () {
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
                    //车管同意租车预定
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
                    /**打印提车单 */
                    'click .btn-carsingle': function (e, value, row, index) {
    
                            e.stopPropagation();
                            e.preventDefault();
                            var table = $(this).closest('table');
                            var options = table.bootstrapTable('getOptions');
                            var ids = row[options.pk];
                            row = $.extend({}, row ? row : {}, {ids: ids});
                            var url = 'rentcar/vehicleinformation/carsingle';
                            Fast.api.open(Table.api.replaceurl(url, row, table), __('打印提车单'), $(this).data() || {});
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

                                    url: 'rentcar/vehicleinformation/takecar',
                                    data: {id: row[options.pk]}
 
                                }, function (data, ret) {

                                    Toastr.success(ret.msg);
                                    Layer.close(index);
                                    table.bootstrapTable('refresh');

                                    Layer.alert('提车成功后，可到租车客户信息查看客户信息',{ icon:0},function(index){
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