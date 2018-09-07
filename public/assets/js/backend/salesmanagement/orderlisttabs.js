define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var goeasy = new GoEasy({
        appkey: 'BC-04084660ffb34fd692a9bd1a40d7b6c2'
    });

    var Controller = {
        index: function () {
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
            /**
             * 新车单
             */
            order_acar: function () {
                var orderAcar = $("#orderAcar");

                $(".btn-add").data("area", ["95%", "95%"]);
                $(".btn-edit").data("area", ["95%", "95%"]);
                $.fn.bootstrapTable.locales[Table.defaults.locale]['formatSearch'] = function(){return "快速搜索:客户姓名";};

                // 初始化表格
                orderAcar.bootstrapTable({
                    url: 'salesmanagement/Orderlisttabs/orderAcar',
                    extend: {
                        // index_url: 'order/salesorder/index',
                        add_url: 'salesmanagement/Orderlisttabs/add',
                        edit_url: 'salesmanagement/Orderlisttabs/edit',
                        del_url: 'salesmanagement/Orderlisttabs/del',
                        multi_url: 'order/salesorder/multi',
                        //table: 'sales_order',
                    },
                    toolbar: '#toolbar1',
                    pk: 'id',
                    sortName: 'id',
                    searchFormVisible: true,
                    columns: [
                        [
                            { checkbox: true },
                            { field: 'id', title: __('Id') ,operate:false},
                            { field: 'order_no', title: __('Order_no') },
                            { field: 'createtime', title: __('Createtime'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime,datetimeFormat:"YYYY-MM-DD" },

                            { field: 'admin.nickname', title: __('销售员') },
                            { field: 'newinventory.licensenumber', title: __('车牌号') },
                            { field: 'models.name', title: __('销售车型') },
                            { field: 'username', title: __('Username') },
                            // { field: 'genderdata', title: __('Genderdata'), visible: false, searchList: { "male": __('genderdata male'), "female": __('genderdata female') } },
                            // { field: 'genderdata_text', title: __('Genderdata'), operate: false },
                            { field: 'phone', title: __('Phone') },
                            { field: 'id_card', title: __('Id_card') },
                            {
                                field: 'id', title: __('查看详细资料'), table: orderAcar, buttons: [
                                    {
                                        name: 'details', text: '查看详细资料', title: '查看订单详细资料', icon: 'fa fa-eye', classname: 'btn btn-xs btn-primary btn-dialog btn-details',
                                        url: 'salesmanagement/Orderlisttabs/details', callback: function (data) {

                                        }
                                    }
                                ],

                                operate: false, formatter: Table.api.formatter.buttons
                            },
                            { field: 'planacar.payment', title: __('首付（元）') ,operate:false },
                            { field: 'planacar.monthly', title: __('月供（元）')  ,operate:false},
                            { field: 'planacar.nperlist', title: __('期数') ,operate:false},
                            { field: 'planacar.margin', title: __('保证金（元）'),operate:false },
                            { field: 'planacar.tail_section', title: __('尾款（元）'),operate:false },
                            { field: 'planacar.gps', title: __('GPS（元）'),operate:false },

                            {
                                field: 'operate', title: __('Operate'), table: orderAcar,
                                buttons: [
                                    {
                                        name: 'submit_audit', text: '提交给内勤', title: '提交到当前部门内勤', icon: 'fa fa-share', extend: 'data-toggle="tooltip"', classname: 'btn btn-xs btn-info btn-submit_audit',
                                        url: 'salesmanagement/orderlisttabs/sedAudit',
                                        //等于is_reviewing_true 的时候操作栏显示的是正在审核四个字，隐藏编辑和删除
                                        //等于is_reviewing 的时候操作栏显示的是提交审核按钮 四个字，显示编辑和删除 
                                        //....
                                        hidden: function (row) { /**提交给内勤 */
                                            if (row.review_the_data == 'send_to_internal') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_true') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_financial') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'internal_over') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'send_car_tube') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'inhouse_handling') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'for_the_car') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'not_through') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_guarantor') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_car') {
                                                return true;
                                            }
                                        }
                                    },
                                    {
                                        icon: 'fa fa-trash', name: 'del', icon: 'fa fa-trash', extend: 'data-toggle="tooltip"', title: __('Del'), classname: 'btn btn-xs btn-danger btn-delone',
                                        url: 'order/salesorder/del',/**删除 */
                                        hidden: function (row) {
                                            if (row.review_the_data == 'send_to_internal') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_true') {

                                                return true;
                                            }
                                            else if (row.review_the_data == 'for_the_car') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_financial') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'send_car_tube') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'inhouse_handling') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'not_through') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_guarantor') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_car') {
                                                return true;
                                            }
                                        },

                                    },
                                    {
                                        name: 'edit', text: '', icon: 'fa fa-pencil', extend: 'data-toggle="tooltip"', title: __('Edit'), classname: 'btn btn-xs btn-success btn-editone',
                                        url: 'order/salesorder/edit',/**编辑 */
                                        hidden: function (row, value, index) {
                                            if (row.review_the_data == 'send_to_internal') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_true') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'for_the_car') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_financial') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'send_car_tube') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'inhouse_handling') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'not_through') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_guarantor') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_car') {
                                                return true;
                                            }
                                        },
                                    },
                                    {
                                        name: 'inhouse_handling', text: '内勤正在处理中',
                                        hidden: function (row) {  /**内勤正在处理中 */
                                            if (row.review_the_data == 'inhouse_handling') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'is_reviewing') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'for_the_car') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_financial') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'send_to_internal') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_true') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'send_car_tube') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'not_through') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_guarantor') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_car') {
                                                return true;
                                            }
                                        }
                                    },
                                    {
                                        name: 'send_car_tube', text: '车管正在处理中',
                                        hidden: function (row) {  /**车管正在处理中 */
                                            if (row.review_the_data == 'send_car_tube') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'is_reviewing') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'for_the_car') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_financial') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'send_to_internal') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'inhouse_handling') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_true') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'not_through') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_guarantor') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_car') {
                                                return true;
                                            }
                                        }
                                    },
                                    {
                                        name: 'is_reviewing_true', text: '风控正在审核中',
                                        hidden: function (row) {  /**风控正在审核中 */
                                            if (row.review_the_data == 'is_reviewing_true') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'is_reviewing') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'for_the_car') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_financial') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'send_to_internal') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'inhouse_handling') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'send_car_tube') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'not_through') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_guarantor') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_car') {
                                                return true;
                                            }
                                        }
                                    },
                                    {
                                        name: 'is_reviewing', text: '正在匹配金融',
                                        hidden: function (row) {  /**正在匹配金融 */
                                            if (row.review_the_data == 'is_reviewing') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'the_financial') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'for_the_car') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'send_to_internal') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'send_car_tube') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'inhouse_handling') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_true') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'not_through') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_guarantor') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_car') {
                                                return true;
                                            }
                                        }
                                    },
                                    {
                                        name: 'for_the_car', icon: 'fa fa-check-circle', text: '征信已通过，车管正在备车中', classname: ' text-info ',
                                        hidden: function (row) {  /**征信已通过，车管正在备车中 */
                                            if (row.review_the_data == 'for_the_car') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_true') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_financial') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'send_to_internal') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'send_car_tube') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'inhouse_handling') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'not_through') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_guarantor') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_car') {
                                                return true;
                                            }
                                        }
                                    },
                                    {
                                        name: 'not_through', icon: 'fa fa-times', text: '征信未通过，订单已关闭', classname: ' text-danger ',
                                        hidden: function (row) {  /**征信不通过 */

                                            if (row.review_the_data == 'not_through') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'for_the_car') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_true') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'send_to_internal') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'send_car_tube') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'inhouse_handling') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_financial') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_guarantor') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_car') {
                                                return true;
                                            }
                                        }
                                    },
                                    {
                                        name: 'the_guarantor', icon: 'fa fa-upload', text: '需交保证金', extend: 'data-toggle="tooltip"', title: __('点击上传保证金收据'), classname: 'btn btn-xs btn-warning btn-the_guarantor',
                                        hidden: function (row) {  /**需交保证金 */

                                            if (row.review_the_data == 'the_guarantor') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'not_through') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'for_the_car') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_true') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_financial') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'send_to_internal') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'inhouse_handling') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'send_car_tube') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_car') {
                                                return true;
                                            }
                                        }
                                    },
                                    {

                                        name: 'the_car', icon: 'fa fa-automobile', text: '已提车', extend: 'data-toggle="tooltip"', title: __('订单已完成，客户已提车'), classname: ' text-success ',
                                        hidden: function (row) {  /**已提车 */
                                            if (row.review_the_data == 'the_car') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'the_guarantor') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'not_through') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'for_the_car') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_financial') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'send_to_internal') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'send_car_tube') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'inhouse_handling') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_true') {
                                                return true;
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


                orderAcar.on('load-success.bs.table', function (e, data) {
                    // console.log(data);
                    $('#badge_order_acar').text(data.total);
                    $(".btn-details").data("area", ["95%", "95%"]);
                })
                // 为表格1绑定事件
                Table.api.bindevent(orderAcar);

                //实时消息
                //通过
                goeasy.subscribe({
                    channel: 'demo-newcar_pass',
                    onMessage: function(message){
                        Layer.alert('新消息：'+message.content,{ icon:0},function(index){
                            Layer.close(index);
                            $(".btn-refresh").trigger("click");
                        });
                        
                    }
                });
                //提供保证金
                goeasy.subscribe({
                    channel: 'demo-newcar_data',
                    onMessage: function(message){
                        Layer.alert('新消息：'+message.content,{ icon:0},function(index){
                            Layer.close(index);
                            $(".btn-refresh").trigger("click");
                        });
                        
                    }
                });

                //不通过
                goeasy.subscribe({
                    channel: 'demo-newcar_nopass',
                    onMessage: function(message){
                        Layer.alert('新消息：'+message.content,{ icon:0},function(index){
                            Layer.close(index);
                            $(".btn-refresh").trigger("click");
                        });
                        
                    }
                });


                // alert(Table.api.getrowdata(table, index));
            },
            /**
             * 租车单
             */
            order_rental: function () {
                var orderRental = $("#orderRental"); 
                 
                $(".btn-add").data("area", ["95%","95%"]); 
                $(".btn-edit").data("area", ["95%","95%"]);
                $.fn.bootstrapTable.locales[Table.defaults.locale]['formatSearch'] = function(){return "快速搜索:客户姓名";};

                // 初始化表格
                 orderRental.bootstrapTable({
                url: 'salesmanagement/Orderlisttabs/orderRental',
                extend: {
                    // index_url: 'order/salesorder/index',
                    rentaladd_url: 'salesmanagement/Orderlisttabs/rentaladd',
                    rentaledit_url: 'salesmanagement/Orderlisttabs/rentaledit',
                    rentaldel_url: 'salesmanagement/Orderlisttabs/rentaldel',
                    rentalmulti_url: 'order/rentalorder/multi',
                    reserve_url: 'salesmanagement/Orderlisttabs/reserve',
                    table: 'rental_order',
                },
                toolbar: '#toolbar2',
                pk: 'id',
                sortName: 'id',
                searchFormVisible: true,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'),operate:false},
                        // {field: 'plan_car_rental_name', title: __('Plan_car_rental_name')},
                        // {field: 'sales_id', title: __('Sales_id')},
                        // {field: 'admin_id', title: __('Admin_id')},
                        // {field: 'control_id', title: __('Control_id')},
                        // {field: 'rental_car_id', title: __('Rental_car_id')},
                        // {field: 'insurance_id', title: __('Insurance_id')},
                        // {field: 'general_manager_id', title: __('General_manager_id')},
                        {field: 'order_no', title: __('Order_no')},

                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Controller.api.formatter.datetime},
                        {field: 'admin.nickname', title: __('销售员')},
                        {field: 'models.name', title: __('车型')},
                        {field: 'carrentalmodelsinfo.licenseplatenumber', title: __('车牌号')},
                        {field: 'carrentalmodelsinfo.vin', title: __('车架号')},
                        {field: 'username', title: __('Username'),formatter:function(value,row,index){
                            if(row.order_no ==  null){ /**如果订单编号为空，就处于预定状态 */
                                return row.username+' <span class="label label-success">预定中</span>'

                            }
                            else{
                                return row.username
                            }
                        }}, 
                        {field: 'phone', title: __('Phone')},
                        // {field: 'id_card', title: __('Id_card')},
                        {field: 'id', title: __('查看详细资料'), table: orderRental, buttons: [
                            {name: 'rentalDetails', text: '查看详细资料', title: '查看订单详细资料' ,icon: 'fa fa-eye',classname: 'btn btn-xs btn-primary btn-dialog btn-rentalDetails', 
                                url: 'salesmanagement/Orderlisttabs/rentaldetails', callback:function(data){
                                    console.log(data)
                                }
                            } 
                            ],
                            
                            operate:false, formatter: Table.api.formatter.buttons
                        },
                        // {field: 'genderdata', title: __('Genderdata'), searchList: {"male":__('Genderdata male'),"female":__('Genderdata female')}, formatter: Table.api.formatter.normal},
                        {field: 'cash_pledge', title: __('Cash_pledge'),operate:false},
                        {field: 'rental_price', title: __('Rental_price'),operate:false},
                        {field: 'tenancy_term', title: __('Tenancy_term'),operate:false},
                        {field: 'delivery_datetime', title: __('开始租车日期'),operate:false,formatter:Controller.api.formatter.datetime},
                        {field: 'delivery_datetime', title: __('应退车日期'),operate:false,formatter:Controller.api.formatter.car_back},
                        {field: 'operate', title: __('Operate'), table: orderRental, 
                        buttons: [
                            /**
                             * 补全客户信息，开始提车
                             */
                            {
                                name:'customerInformation',text:'开始提车', title:'补全客户信息，开始提车', icon: 'fa fa-share',extend: 'data-toggle="tooltip"',classname: 'btn btn-xs btn-info btn-customerInformation',
                                // url: 'order/rentalorder/add',
                                hidden:function(row){
                                    if(row.review_the_data == 'is_reviewing_argee'){ 
                                        return false; 
                                    }  
                                    else if(row.review_the_data == 'is_reviewing_true'){
                                        return true;
                                    }
                                    else if(row.review_the_data == 'is_reviewing_control'){
                                        return true;
                                    }
                                    else if(row.review_the_data == 'is_reviewing_pass'){
                                        return true;
                                    }
                                    else if(row.review_the_data == 'for_the_car'){
                                        return true;
                                    }
                                    else if(row.review_the_data == 'is_reviewing_false'){
                                        return true;
                                    }
                                    else if(row.review_the_data == 'is_reviewing_nopass'){
                                        return true;
                                    }
                                }
                            },
                            /**
                             * 提交风控审核
                             */
                            {
                                name:'control',text:'提交风控审核', title:'提交风控审核', icon: 'fa fa-share',extend: 'data-toggle="tooltip"',classname: 'btn btn-xs btn-info btn-control',
                                // url: 'order/rentalorder/control',  
                                hidden:function(row){ /** */
                                    if(row.review_the_data == 'is_reviewing_false'){ 
                                        return false; 
                                    }  
                                    else if(row.review_the_data == 'is_reviewing_true'){
                                        return true;
                                    }
                                    else if(row.review_the_data == 'is_reviewing_pass'){
                                        return true;
                                    }
                                    else if(row.review_the_data == 'for_the_car'){
                                        return true;
                                    }
                                    else if(row.review_the_data == 'is_reviewing_argee'){
                                        return true;
                                    }
                                    else if(row.review_the_data == 'is_reviewing_control'){
                                        return true;
                                    }
                                    else if(row.review_the_data == 'is_reviewing_nopass'){
                                        return true;
                                    }
                                }
                            },
                            /**
                             * 取消预定
                             */
                            { 
                                icon: 'fa fa-trash', name: 'rentaldel', icon: 'fa fa-trash', extend: 'data-toggle="tooltip"',text:'取消预定', title: __('取消预定'),classname: 'btn btn-xs btn-danger btn-rentaldelone',
                                // url:'order/rentalorder/del',/** */
                                hidden:function(row){
                                    if(row.review_the_data == 'is_reviewing_argee'){ 
                                        return false; 
                                    }
                                    else if(row.review_the_data == 'is_reviewing_true'){
                                      
                                        return true;
                                    } 
                                    else if(row.review_the_data == 'is_reviewing_control'){
                                        return true;
                                    }
                                    else if(row.review_the_data == 'for_the_car'){
                                        return true;
                                    }
                                    else if(row.review_the_data == 'is_reviewing_false'){
                                      
                                        return true;
                                    } 
                                    else if(row.review_the_data == 'is_reviewing_pass'){
                                        return true;
                                    }
                                    else if(row.review_the_data == 'is_reviewing_nopass'){
                                        return true;
                                    }
                                   
                                },
                                
                            },
                            /**
                             * 删除
                             */
                            { 
                                icon: 'fa fa-trash', name: 'rentaldel', icon: 'fa fa-trash', extend: 'data-toggle="tooltip"',text:'删除订单', title: __('删除订单'),classname: 'btn btn-xs btn-danger btn-rentaldelone',
                                // url:'order/rentalorder/del',/** */
                                hidden:function(row){
                                    if(row.review_the_data == 'is_reviewing_false'){ 
                                        return false; 
                                    }
                                    else if(row.review_the_data == 'is_reviewing_true'){
                                      
                                        return true;
                                    } 
                                    else if(row.review_the_data == 'is_reviewing_argee'){
                                      
                                        return true;
                                    } 
                                    else if(row.review_the_data == 'for_the_car'){
                                        return true;
                                    }
                                    else if(row.review_the_data == 'is_reviewing_control'){
                                        return true;
                                    }
                                    else if(row.review_the_data == 'is_reviewing_pass'){
                                        return true;
                                    }
                                    else if(row.review_the_data == 'is_reviewing_nopass'){
                                        return true;
                                    }
                                   
                                },
                                
                            },
                            /**
                             * 修改订单
                             */
                            { 
                                name: 'rentaledit',text: '',icon: 'fa fa-pencil',extend: 'data-toggle="tooltip"',text:'修改订单', title: __('修改订单'),classname: 'btn btn-xs btn-success btn-rentaleditone', 
                                // url:'order/rentalorder/edit',/**修改订单 */
                                hidden:function(row,value,index){ 
                                    if(row.review_the_data == 'is_reviewing_false'){ 
                                        return false; 
                                    } 
                                    else if(row.review_the_data == 'is_reviewing_argee'){
                                      
                                        return true;
                                    }
                                    else if(row.review_the_data == 'is_reviewing_control'){
                                        return true;
                                    } 
                                    else if(row.review_the_data == 'for_the_car'){
                                        return true;
                                    }
                                    else if(row.review_the_data == 'is_reviewing_true'){ 
                                        return true;
                                    } 
                                    else if(row.review_the_data == 'is_reviewing_pass'){
                                        return true;
                                    }
                                    else if(row.review_the_data == 'is_reviewing_nopass'){
                                        return true;
                                    }
                                }, 
                            },
                            /**
                             * 车管正在处理中
                             */
                            {
                                name: 'is_reviewing_true',text: '车管正在处理中',title:'车管正在处理你的租车请求',extend: 'data-toggle="tooltip"',
                                hidden:function(row){  /**车管正在处理中 */
                                    if(row.review_the_data == 'is_reviewing_true'){ 
                                        return false; 
                                    }
                                    else if(row.review_the_data == 'is_reviewing_false'){
                                        return true;
                                    }
                                    else if(row.review_the_data == 'is_reviewing_pass'){
                                        return true;
                                    }
                                    else if(row.review_the_data == 'is_reviewing_control'){
                                        return true;
                                    }
                                    else if(row.review_the_data == 'for_the_car'){
                                        return true;
                                    }
                                    else if(row.review_the_data == 'is_reviewing_argee'){
                                      
                                        return true;
                                    } 
                                    else if(row.review_the_data == 'is_reviewing_nopass'){
                                        return true;
                                    }
                                }
                            },
                            /**
                             * 风控正在处理中
                             */
                            {
                                name: 'is_reviewing_control',text: '风控正在处理中',title:'风控正在处理中',
                                hidden:function(row){  /**风控正在处理中 */
                                    if(row.review_the_data == 'is_reviewing_control'){ 
                                        return false; 
                                    }
                                    else if(row.review_the_data == 'is_reviewing_false'){
                                        return true;
                                    }
                                    else if(row.review_the_data == 'is_reviewing_pass'){
                                        return true;
                                    }
                                    else if(row.review_the_data == 'is_reviewing_true'){
                                        return true;
                                    }
                                    else if(row.review_the_data == 'is_reviewing_argee'){
                                      
                                        return true;
                                    } 
                                    else if(row.review_the_data == 'for_the_car'){
                                        return true;
                                    }
                                    else if(row.review_the_data == 'is_reviewing_nopass'){
                                        return true;
                                    }
                                }
                            },
                            /**
                             * 征信已通过，待提车
                             */
                            {
                                name: 'is_reviewing_pass', icon: 'fa fa-check-circle', text: '征信已通过，待提车', classname: ' text-info ',
                                hidden: function (row) {  /**征信已通过，待提车 */
                                    if (row.review_the_data == 'is_reviewing_pass') {
                                        return false;
                                    }
                                    else if (row.review_the_data == 'is_reviewing_true') {
                                        return true;
                                    }
                                    else if(row.review_the_data == 'is_reviewing_argee'){
                                      
                                        return true;
                                    } 
                                    else if(row.review_the_data == 'is_reviewing_control'){
                                        return true;
                                    }
                                    else if(row.review_the_data == 'is_reviewing_false'){
                                        return true;
                                    }
                                    else if(row.review_the_data == 'for_the_car'){
                                        return true;
                                    }
                                    else if (row.review_the_data == 'is_reviewing_nopass') {
                                        return true;
                                    }
                                }
                            },
                            /**
                             * 征信不通过
                             */
                            {
                                name: 'is_reviewing_nopass', icon: 'fa fa-times', text: '征信未通过，订单已关闭', classname: ' text-danger ',
                                hidden: function (row) {  /**征信不通过 */

                                    if (row.review_the_data == 'is_reviewing_nopass') {
                                        return false;
                                    }
                                    else if (row.review_the_data == 'is_reviewing_pass') {
                                        return true;
                                    }
                                    else if(row.review_the_data == 'is_reviewing_control'){
                                        return true;
                                    }
                                    else if(row.review_the_data == 'is_reviewing_argee'){
                                      
                                        return true;
                                    } 
                                    else if(row.review_the_data == 'for_the_car'){
                                        return true;
                                    }
                                    else if (row.review_the_data == 'is_reviewing_true') {
                                        return true;
                                    }
                                    else if(row.review_the_data == 'is_reviewing_false'){
                                        return true;
                                    }
                                }
                            },
                            /**
                             * 已提车
                             */
                            {

                                name: 'for_the_car', icon: 'fa fa-automobile', text: '已提车', extend: 'data-toggle="tooltip"', title: __('订单已完成，客户已提车'), classname: ' text-success ',
                                hidden: function (row) {  /**已提车 */
                                    if (row.review_the_data == 'for_the_car') {
                                        return false;
                                    }
                                    else if (row.review_the_data == 'is_reviewing_pass') {
                                        return true;
                                    }
                                    else if(row.review_the_data == 'is_reviewing_control'){
                                        return true;
                                    }
                                    else if(row.review_the_data == 'is_reviewing_argee'){
                                      
                                        return true;
                                    } 
                                    else if(row.review_the_data == 'is_reviewing_nopass'){
                                        return true;
                                    }
                                    else if (row.review_the_data == 'is_reviewing_true') {
                                        return true;
                                    }
                                    else if(row.review_the_data == 'is_reviewing_false'){
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

            goeasy.subscribe({
                channel: 'demo3',
                onMessage: function(message){
                   
                    $(".btn-refresh").trigger("click");
                }
            });

            //车管同意预定---销售接受消息
            goeasy.subscribe({
                channel: 'demo-rental_argee',
                onMessage: function(message){
                    Layer.alert('新消息：'+message.content,{ icon:0},function(index){
                        Layer.close(index);
                        $(".btn-refresh").trigger("click");
                    });
                }
            });

            //实时消息
            //风控通过---可以提车
            goeasy.subscribe({
                channel: 'demo-rental_pass',
                onMessage: function(message){
                    Layer.alert('新消息：'+message.content,{ icon:0},function(index){
                        Layer.close(index);
                        $(".btn-refresh").trigger("click");
                    });
                    
                }
            });

        
            //不通过
            goeasy.subscribe({
                channel: 'demo-rental_nopass',
                onMessage: function(message){
                    Layer.alert('新消息：'+message.content,{ icon:0},function(index){
                        Layer.close(index);
                        $(".btn-refresh").trigger("click");
                    });
                    
                }
            });

            orderRental.on('load-success.bs.table', function (e, data) {
                // console.log(data);
                $('#badge_order_rental').text(data.total);
                $(".btn-rentalDetails").data("area", ["95%", "95%"]);
            })
            // 为表格1绑定事件
            Table.api.bindevent(orderRental);
               
            $(document).on("click", ".rental-list", function (index) {   
                    
                    
                var url = 'order/rentalorder/add';
                var options = {
                    shadeClose: false,
                    shade: [0.3, '#393D49'],
                    area:['95%','95%'],
                    // closeBtn: 0, //不显示关闭按钮
                    cancel: function(index){ 
                        //右上角关闭回调
                            
                        if(confirm('确定要关闭么')){ //只有当点击confirm框的确定时，该层才会关闭
                                
                            Fast.api.ajax({
                                url: 'order/rentalorder/giveup',
                                // data: {ids: JSON.stringify($ids)}
                            }, function (data, rets) {
                                // console.log(data);
                                // return;
                                // Toastr.success("成功");
                                Layer.close(index);
                                    
                                // return false;
                            }, function (data, ret) {
                                //失败的回调
                                    
                                return false;
                            });

                                layer.close(index)
                            }

                        return false; 
                            
                    },
                    callback:function(value){
                                                        
                    }
                }
                Fast.api.open(url,'新增租车单',options)
            })

            //销售预定租车
            $(document).on("click", ".btn-reserve", function () {   
                    
                var url = 'salesmanagement/Orderlisttabs/reserve';
                var options = {
                    shadeClose: false,
                    shade: [0.3, '#393D49'],
                    area:['70%','70%'],
                    // closeBtn: 0, //不显示关闭按钮
                    callback:function(value){
                        console.log(123);

                        // var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                        // parent.layer.close(index);  
                        // $(".btn-refresh").trigger("click");   

                    }
                }
                Fast.api.open(url,'租车预定',options)
            })


            },
            /**
             * 二手车单
             */
            order_second: function () {

                // 二手车单
                var orderSecond = $("#orderSecond");

                $(".btn-add").data("area", ["95%", "95%"]);
                $(".btn-edit").data("area", ["95%", "95%"]);

                // 初始化表格
                orderSecond.bootstrapTable({
                    url: 'salesmanagement/Orderlisttabs/orderSecond',
                    extend: {
                        // index_url: 'order/secondsalesorder/index',
                        secondadd_url: 'salesmanagement/Orderlisttabs/secondadd',
                        secondedit_url: 'salesmanagement/Orderlisttabs/secondedit',
                        del_url: 'salesmanagement/Orderlisttabs/del',
                        multi_url: 'salesmanagement/Orderlisttabs/multi',
                        table: 'second_sales_order',
                    },
                    toolbar: '#toolbar3',
                    pk: 'id',
                    searchFormVisible: true,
                    sortName: 'id',
                    columns: [
                        [
                            { checkbox: true },
                            { field: 'id', title: __('Id') ,operate:false},
                            { field: 'order_no', title: __('Order_no') },
                            { field: 'createtime', title: __('Createtime'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime ,datetimeFormat:"YYYY-MM-DD" },

                            { field: 'plansecond.licenseplatenumber', title: __('车牌号') },
                            { field: 'models.name', title: __('销售车型') },
                            { field: 'admin.nickname', title: __('销售员') },
                            { field: 'username', title: __('Username') },
                            { field: 'phone', title: __('Phone') },
                            {
                                field: 'id', title: __('查看详细资料'), table: orderSecond, buttons: [
                                    {
                                        name: 'seconddetails', text: '查看详细资料', title: '查看订单详细资料', icon: 'fa fa-eye', classname: 'btn btn-xs btn-primary btn-dialog btn-seconddetails',
                                        url: 'salesmanagement/Orderlisttabs/seconddetails', callback: function (data) {
                                            console.log(data)
                                        }
                                    }
                                ],

                                operate: false, formatter: Table.api.formatter.buttons
                            },
                            { field: 'id_card', title: __('Id_card') },
                            
                            { field: 'plansecond.newpayment', title: __('新首付（元）'),operate:false },
                            { field: 'plansecond.monthlypaymen', title: __('月供（元）'),operate:false },
                            { field: 'plansecond.periods', title: __('期数') , operate: false},
                            { field: 'plansecond.totalprices', title: __('总价（元）'), operate: false },
                            { field: 'plansecond.bond', title: __('保证金（元）') , operate: false},
                            { field: 'plansecond.tailmoney', title: __('尾款（元）'), operate: false },

                            {
                                field: 'operate', title: __('Operate'), table: orderSecond,
                                buttons: [
                                    {
                                        name: 'second_audit', text: '提交内勤', title: '提交内勤', icon: 'fa fa-share', extend: 'data-toggle="tooltip"', classname: 'btn btn-xs btn-info btn-second_audit',
                                        url: 'order/secondsalesorder/setAudit',
                                        //等于is_reviewing_true 的时候操作栏显示的是正在审核四个字，隐藏编辑和删除
                                        //等于is_reviewing 的时候操作栏显示的是提交审核按钮 四个字，显示编辑和删除 
                                        //....
                                        hidden: function (row) { /**提交内勤 */
                                            if (row.review_the_data == 'is_reviewing') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_true') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_control') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_finance') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'send_car_tube') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'for_the_car') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'not_through') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_guarantor') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_car') {
                                                return true;
                                            }
                                        }
                                    },
                                    {
                                        icon: 'fa fa-trash', name: 'del', icon: 'fa fa-trash', extend: 'data-toggle="tooltip"', title: __('Del'), classname: 'btn btn-xs btn-danger btn-delone',
                                        // url: 'order/secondsalesorder/del',/**删除 */
                                        hidden: function (row) {
                                            if (row.review_the_data == 'is_reviewing') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_true') {

                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_control') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_finance') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'send_car_tube') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'for_the_car') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'not_through') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_guarantor') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_car') {
                                                return true;
                                            }
                                        },

                                    },
                                    {
                                        name: 'secondedit', text: '', icon: 'fa fa-pencil', extend: 'data-toggle="tooltip"', title: __('Edit'), classname: 'btn btn-xs btn-success btn-secondeditone',
                                        // url: 'order/secondsalesorder/edit',/**编辑 */
                                        hidden: function (row, value, index) {
                                            if (row.review_the_data == 'is_reviewing') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_true') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_control') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_finance') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'send_car_tube') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'for_the_car') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'not_through') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_guarantor') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_car') {
                                                return true;
                                            }
                                        },
                                    },
                                    {
                                        name: 'is_reviewing_true', text: '内勤正在处理中',
                                        hidden: function (row) {  /**内勤正在处理中 */
                                            if (row.review_the_data == 'is_reviewing_true') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'is_reviewing') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_control') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_finance') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'send_car_tube') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'for_the_car') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'not_through') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_guarantor') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_car') {
                                                return true;
                                            }
                                        }
                                    },
                                    {
                                        name: 'is_reviewing_control', text: '风控正在审核中',
                                        hidden: function (row) {  /**风控正在审核中 */
                                            if (row.review_the_data == 'is_reviewing_control') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_finance') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_true') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'send_car_tube') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'for_the_car') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'not_through') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_guarantor') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_car') {
                                                return true;
                                            }
                                        }
                                    },
                                    {
                                        name: 'is_reviewing_finance', text: '正在匹配金融',
                                        hidden: function (row) {  /**正在匹配金融 */
                                            if (row.review_the_data == 'is_reviewing_finance') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_control') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_true') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'send_car_tube') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'for_the_car') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'not_through') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_guarantor') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_car') {
                                                return true;
                                            }
                                        }
                                    },
                                    {
                                        name: 'send_car_tube', text: '车管正在处理中',
                                        hidden: function (row) {  /**车管正在处理中 */
                                            if (row.review_the_data == 'send_car_tube') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'is_reviewing') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_control') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_finance') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_true') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'for_the_car') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'not_through') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_guarantor') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_car') {
                                                return true;
                                            }
                                        }
                                    },
                                    {
                                        name: 'for_the_car', icon: 'fa fa-check-circle', text: '征信已通过，车管正在备车中', classname: ' text-info ',
                                        hidden: function (row) {  /**征信已通过，车管正在备车中 */
                                            if (row.review_the_data == 'for_the_car') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_true') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_control') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_finance') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'send_car_tube') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'not_through') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_guarantor') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_car') {
                                                return true;
                                            }
                                        }
                                    },
                                    {
                                        name: 'not_through', icon: 'fa fa-times', text: '征信未通过，订单已关闭', classname: ' text-danger ',
                                        hidden: function (row) {  /**征信不通过 */

                                            if (row.review_the_data == 'not_through') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'for_the_car') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_true') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_control') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_finance') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'send_car_tube') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_guarantor') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_car') {
                                                return true;
                                            }
                                        }
                                    },
                                    {
                                        name: 'the_guarantor', icon: 'fa fa-upload', text: '需交保证金', extend: 'data-toggle="tooltip"', title: __('点击上传保证金收据'), classname: 'btn btn-xs btn-warning btn-secondthe_guarantor',
                                        hidden: function (row) {  /**需交保证金 */

                                            if (row.review_the_data == 'the_guarantor') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'not_through') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'for_the_car') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_true') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_control') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_finance') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'send_car_tube') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'the_car') {
                                                return true;
                                            }
                                        }
                                    },
                                    {

                                        name: 'the_car', icon: 'fa fa-automobile', text: '已提车', extend: 'data-toggle="tooltip"', title: __('订单已完成，客户已提车'), classname: ' text-success ',
                                        hidden: function (row) {  /**已提车 */
                                            if (row.review_the_data == 'the_car') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'the_guarantor') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'not_through') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'for_the_car') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_true') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_control') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_finance') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'send_car_tube') {
                                                return true;
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


                orderSecond.on('load-success.bs.table', function (e, data) {
                    // console.log(data);
                    $('#badge_order_second').text(data.total);
                    $(".btn-seconddetails").data("area", ["95%", "95%"]);
                })
                // 为表格1绑定事件
                Table.api.bindevent(orderSecond);

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

                //提供保证金
                goeasy.subscribe({
                    channel: 'demo-second_data',
                    onMessage: function(message){
                        Layer.alert('新消息：'+message.content,{ icon:0},function(index){
                            Layer.close(index);
                            $(".btn-refresh").trigger("click");
                        });
                        
                    }
                });

                //不通过
                goeasy.subscribe({
                    channel: 'demo-second_nopass',
                    onMessage: function(message){
                        Layer.alert('新消息：'+message.content,{ icon:0},function(index){
                            Layer.close(index);
                            $(".btn-refresh").trigger("click");
                        });
                        
                    }
                });

                //新增二手车单
                $(document).on("click", ".btn-secondadd", function () {   
                        
                    var url = 'salesmanagement/Orderlisttabs/secondadd';
                    var options = {
                        shadeClose: false,
                        shade: [0.3, '#393D49'],
                        area:['95%','95%'],
                        // closeBtn: 0, //不显示关闭按钮
                        callback:function(value){
                            console.log(123);

                            // var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                            // parent.layer.close(index);  
                            // $(".btn-refresh").trigger("click");   

                        }
                    }
                    Fast.api.open(url,'新增二手车单',options)
                })


                // alert(Table.api.getrowdata(table, index));
            },
            /**
             * 全款单
             */
            order_full: function () {

                // 全款单
                var orderFull = $("#orderFull");

                $(".btn-add").data("area", ["95%", "95%"]);
                $(".btn-edit").data("area", ["95%", "95%"]);

                // 初始化表格
                orderFull.bootstrapTable({
                    url: 'salesmanagement/Orderlisttabs/orderFull',
                    extend: {
                        // index_url: 'order/fullparmentorder/index',
                        fulladd_url: 'salesmanagement/Orderlisttabs/fulladd',
                        fulledit_url: 'salesmanagement/Orderlisttabs/fulledit',
                        del_url: 'salesmanagement/Orderlisttabs/del',
                        multi_url: 'salesmanagement/Orderlisttabs/multi',
                        table: 'full_parment_order',
                    },
                    toolbar: '#toolbar4',
                    pk: 'id',
                    sortName: 'id',
                    searchFormVisible: true,
                    columns: [
                        [
                            { checkbox: true },
                            { field: 'id', title: __('Id') ,operate:false},
                            { field: 'order_no', title: __('Order_no') },
                            { field: 'createtime', title: __('Createtime'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime, datetimeFormat:"YYYY-MM-DD" },
                            { field: 'models.name', title: __('销售车型') },
                            { field: 'admin.nickname', title: __('销售员') },
                            { field: 'planfull.full_total_price', title: __('全款总价（元）') },

                            {
                                field: 'id', title: __('查看详细资料'), table: orderFull, buttons: [
                                    {
                                        name: 'fulldetails', text: '查看详细资料', title: '查看订单详细资料', icon: 'fa fa-eye', classname: 'btn btn-xs btn-primary btn-dialog btn-fulldetails',
                                        url: 'salesmanagement/Orderlisttabs/fulldetails', callback: function (data) {

                                        }
                                    }
                                ],

                                operate: false, formatter: Table.api.formatter.buttons
                            },

                            {field: 'username', title: __('Username')},
                            {field: 'phone', title: __('Phone')},
                            // {field: 'id_card', title: __('Id_card')},
                            // {field: 'city', title: __('居住地址')},
                            // {field: 'detailed_address', title: __('详细地址')},
                            // {field: 'detailed_address', title: __('Detailed_address')},
                            { field: 'delivery_datetime', title: __('Delivery_datetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime,datetimeFormat:"YYYY-MM-DD" },

                            {
                                field: 'operate', title: __('Operate'), table: orderFull,
                                buttons: [
                                    {
                                        name: 'submitCar', text: '提交内勤', icon: 'fa fa-share', extend: 'data-toggle="tooltip"', title: __('提交内勤'), classname: 'btn btn-xs btn-info btn-submitCar',
                                        url: 'order/fullparmentorder/submitCar',/**提交内勤 */
                                       
                                        hidden: function (row) { /**提交内勤 */
                                            if (row.review_the_data == 'send_to_internal') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_true') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'inhouse_handling') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_pass') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'for_the_car') {
                                                return true;
                                            }
                                            
                                        }
                                    },
                                    {
                                        icon: 'fa fa-trash', name: 'del', icon: 'fa fa-trash', extend: 'data-toggle="tooltip"', title: __('Del'), classname: 'btn btn-xs btn-danger btn-delone',
                                        // url: 'order/fullparmentorder/del',/**删除 */
                                        hidden: function (row) {
                                            if (row.review_the_data == 'send_to_internal') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_true') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'inhouse_handling') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_pass') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'for_the_car') {
                                                return true;
                                            }
                                            
                                        },

                                    },
                                    {
                                        name: 'fulledit', text: '', icon: 'fa fa-pencil', extend: 'data-toggle="tooltip"', title: __('Edit'), classname: 'btn btn-xs btn-success btn-fulleditone',
                                        // url: 'order/fullparmentorder/edit',/**编辑 */
                                        hidden: function (row, value, index) {
                                            if (row.review_the_data == 'send_to_internal') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_true') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'inhouse_handling') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_pass') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'for_the_car') {
                                                return true;
                                            }
                                        },
                                    },
                                    {
                                        name: 'is_reviewing_true', icon: 'fa fa-check-circle', text: '车管正在备车中', classname: ' text-info ',
                                        hidden: function (row) {  /**车管正在备车中 */
                                            if (row.review_the_data == 'is_reviewing_true') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'send_to_internal') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_pass') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'inhouse_handling') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'for_the_car') {
                                                return true;
                                            }
                                           
                                        }
                                    },
                                    {
                                        name: 'inhouse_handling', text: '内勤正在处理',
                                        hidden: function (row) {  /**内勤正在处理 */
                                            if (row.review_the_data == 'inhouse_handling') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'send_to_internal') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_pass') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_true') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'for_the_car') {
                                                return true;
                                            }
                                           
                                        }
                                    },
                                    {
                                        name: 'is_reviewing_pass', icon: 'fa fa-check-circle', text: '车管备车成功，等待提车', classname: ' text-info ',
                                        hidden: function (row) {  /**车管备车成功，等待提车 */
                                            if (row.review_the_data == 'is_reviewing_pass') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'send_to_internal') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_true') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'inhouse_handling') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'for_the_car') {
                                                return true;
                                            }
                                           
                                        }
                                    },
                                    {

                                        name: 'for_the_car', icon: 'fa fa-automobile', text: '已提车', extend: 'data-toggle="tooltip"', title: __('订单已完成，客户已提车'), classname: ' text-success ',
                                        hidden: function (row) {  /**已提车 */
                                            if (row.review_the_data == 'for_the_car') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_pass') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_true') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'send_to_internal') {
                                                return true;
                                            }
                                            else if (row.review_the_data == 'inhouse_handling') {
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


                orderFull.on('load-success.bs.table', function (e, data) {
                    // console.log(data);
                    $('#badge_order_full').text(data.total);
                    $(".btn-fulldetails").data("area", ["95%", "95%"]);
                })
                // 为表格1绑定事件
                Table.api.bindevent(orderFull);

                //车管发送----可以进行提车
                goeasy.subscribe({
                    channel: 'demo-full_takecar',
                    onMessage: function(message){
                        Layer.alert('新消息：'+message.content,{ icon:0},function(index){
                            Layer.close(index);
                            $(".btn-refresh").trigger("click");
                        });
                        
                    }
                });

                //新增全款单
                $(document).on("click", ".btn-fulladd", function () {   
                        
                    var url = 'salesmanagement/Orderlisttabs/fulladd';
                    var options = {
                        shadeClose: false,
                        shade: [0.3, '#393D49'],
                        area:['95%','95%'],
                        // closeBtn: 0, //不显示关闭按钮
                        callback:function(value){
                            console.log(123);

                            // var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                            // parent.layer.close(index);  
                            // $(".btn-refresh").trigger("click");   

                        }
                    }
                    Fast.api.open(url,'新增全款单',options)
                })


            },
        },
        reserve:function(){
            
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
            // Controller.api.bindevent();
            // console.log(Config.id);
            
 
        },
        rentaladd:function(){
            
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
            // Controller.api.bindevent();
            // console.log(Config.id);
            
 
        },
        rentaledit:function(){
            
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
            // Controller.api.bindevent();
            // console.log(Config.id);
            
 
        },
        secondadd:function(){
            
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
            // Controller.api.bindevent();
            // console.log(Config.id);
            
 
        },
        secondedit:function(){
            
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
            // Controller.api.bindevent();
            // console.log(Config.id);
            
 
        },
        fulladd:function(){
            
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
            // Controller.api.bindevent();
            // console.log(Config.id);
            
 
        },
        fulledit:function(){
            
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
                Form.api.bindevent($("form[role=form]"));
            },
            events: {
                operate: {
                    //新车提交内勤审核
                    'click .btn-submit_audit': function (e, value, row, index) {

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
                            __('请确认资料完整，是否开始提交给内勤?'),
                            { icon: 3, title: __('Warning'), offset: [top, left], shadeClose: true },

                            function (index) {
                                var table = $(that).closest('table');
                                var options = table.bootstrapTable('getOptions');


                                Fast.api.ajax({

                                    url: 'salesmanagement/orderlisttabs/sedAudit',
                                    data: {id: row[options.pk]}
 
                                }, function (data, ret) {

                                    Toastr.success('操作成功');
                                    Layer.close(index);
                                    table.bootstrapTable('refresh');
                                    return false;
                                }, function (data, ret) {
                                    //失败的回调
                                    Toastr.success(ret.msg);

                                    return false;
                                });


                            }
                        );

                    },
                    //租车客户信息的补全
                    'click .btn-customerInformation': function (e, value, row, index) {

                        $(".btn-customerInformation").data("area", ["95%", "95%"]);

                        e.stopPropagation();
                        e.preventDefault();
                        var table = $(this).closest('table');
                        var options = table.bootstrapTable('getOptions');
                        var ids = [options.pk];
                        row = $.extend({}, row ? row : {}, { ids:ids}); 
                        var url = 'salesmanagement/Orderlisttabs/rentaladd';


                        // console.log(url);
                        Fast.api.open(Table.api.replaceurl(url,row, table), __('补全客户信息'), $(this).data() || {});


                    },
                    //租车提交风控审核
                    'click .btn-control': function (e, value, row, index) {

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
                            __('请确认资料完整，是否开始提交风控审核?'),
                            { icon: 3, title: __('Warning'), offset: [top, left], shadeClose: true },

                            function (index) {
                                var table = $(that).closest('table');
                                var options = table.bootstrapTable('getOptions');


                                Fast.api.ajax({

                                    url: 'salesmanagement/Orderlisttabs/control',
                                    data: {id: row[options.pk]}
 
                                }, function (data, ret) {

                                    Toastr.success('操作成功');
                                    Layer.close(index);
                                    table.bootstrapTable('refresh');
                                    return false;
                                }, function (data, ret) {
                                    //失败的回调
                                    Toastr.success(ret.msg);

                                    return false;
                                });


                            }
                        );

                    },
                    //二手车提交内勤
                    'click .btn-second_audit': function (e, value, row, index) {

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
                            __('请确认资料完整，是否开始提交审核?'),
                            { icon: 3, title: __('Warning'), offset: [top, left], shadeClose: true },

                            function (index) {
                                var table = $(that).closest('table');
                                var options = table.bootstrapTable('getOptions');


                                Fast.api.ajax({

                                    url: 'salesmanagement/orderlisttabs/setAudit',
                                    data: {id: row[options.pk]}
 
                                }, function (data, ret) {

                                    Toastr.success('操作成功');
                                    Layer.close(index);
                                    table.bootstrapTable('refresh');
                                    return false;
                                }, function (data, ret) {
                                    //失败的回调
                                    Toastr.success(ret.msg);

                                    return false;
                                });


                            }
                        );

                    },
                    //全款车提交内勤
                    'click .btn-submitCar': function (e, value, row, index) {

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
 
                            __('请确认资料完整并发送给内勤生成提车单?'),
 
 
                            { icon: 3, title: __('Warning'), offset: [top, left], shadeClose: true },

                            function (index) {
                                var table = $(that).closest('table');
                                var options = table.bootstrapTable('getOptions');
                                Fast.api.ajax({

                                    url: 'salesmanagement/orderlisttabs/submitCar',
                                    data: {id: row[options.pk]}
 
                                }, function (data, ret) {

                                    Toastr.success('操作成功');
                                    Layer.close(index);
                                    table.bootstrapTable('refresh');
                                    return false;
                                }, function (data, ret) {
                                    //失败的回调
                                    Toastr.success(ret.msg);

                                    return false;
                                });


                            }
                        );

                    },
                    //编辑按钮
                    'click .btn-editone': function (e, value, row, index) { /**编辑按钮 */
                        $(".btn-editone").data("area", ["95%", "95%"]);

                        e.stopPropagation();
                        e.preventDefault();
                        var table = $(this).closest('table');
                        var options = table.bootstrapTable('getOptions');
                        var ids = row[options.pk];
                        row = $.extend({}, row ? row : {}, { ids: ids });
                        var url = options.extend.edit_url+'/posttype/edit';
                        Fast.api.open(Table.api.replaceurl(url, row, table), __('Edit'), $(this).data() || {});
                    },
                    //租车编辑按钮
                    'click .btn-rentaleditone': function (e, value, row, index) { /**二手车编辑按钮 */
                        $(".btn-rentaleditone").data("area", ["95%", "95%"]);

                        e.stopPropagation();
                        e.preventDefault();
                        var table = $(this).closest('table');
                        var options = table.bootstrapTable('getOptions');
                        var ids = row[options.pk];
                        row = $.extend({}, row ? row : {}, { ids: ids });
                        var url = options.extend.rentaledit_url;
                        Fast.api.open(Table.api.replaceurl(url, row, table), __('Edit'), $(this).data() || {});
                    },
                    //二手车编辑按钮
                    'click .btn-secondeditone': function (e, value, row, index) { /**二手车编辑按钮 */
                        $(".btn-secondeditone").data("area", ["95%", "95%"]);

                        e.stopPropagation();
                        e.preventDefault();
                        var table = $(this).closest('table');
                        var options = table.bootstrapTable('getOptions');
                        var ids = row[options.pk];
                        row = $.extend({}, row ? row : {}, { ids: ids });
                        var url = options.extend.secondedit_url+'/posttype/edit';
                        Fast.api.open(Table.api.replaceurl(url, row, table), __('Edit'), $(this).data() || {});
                    },
                    //全款车编辑按钮
                    'click .btn-fulleditone': function (e, value, row, index) { /**二手车编辑按钮 */
                        $(".btn-fulleditone").data("area", ["95%", "95%"]);

                        e.stopPropagation();
                        e.preventDefault();
                        var table = $(this).closest('table');
                        var options = table.bootstrapTable('getOptions');
                        var ids = row[options.pk];
                        row = $.extend({}, row ? row : {}, { ids: ids });
                        var url = options.extend.fulledit_url;
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
                    //租车删除按钮
                    'click .btn-rentaldelone': function (e, value, row, index) {  /**删除按钮 */

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
                    //提交保证金
                    'click .btn-the_guarantor': function (e, value, row, index) { /**提交保证金 */
                        $(".btn-the_guarantor").data("area", ["95%", "95%"]); 
                        e.stopPropagation();
                        e.preventDefault();
                        var table = $(this).closest('table');
                        var options = table.bootstrapTable('getOptions');
                        var ids = [options.pk];
                        row = $.extend({}, row ? row : {}, { ids:ids}); 
                        var url = options.extend.edit_url+'/posttype/the_guarantor';  
                        Fast.api.open(Table.api.replaceurl(url,row, table), __('请上传保证金收据'), $(this).data() || {});
                    },
                    //二手车提交保证金
                    'click .btn-secondthe_guarantor': function (e, value, row, index) { /**二手车提交保证金 */
                        $(".btn-secondthe_guarantor").data("area", ["95%", "95%"]); 
                        e.stopPropagation();
                        e.preventDefault();
                        var table = $(this).closest('table');
                        var options = table.bootstrapTable('getOptions');
                        var ids = [options.pk];
                        row = $.extend({}, row ? row : {}, { ids:ids}); 
                        var url = options.extend.secondedit_url+'/posttype/the_guarantor';  
                        Fast.api.open(Table.api.replaceurl(url,row, table), __('请上传保证金收据'), $(this).data() || {});
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
                datetime: function (value, row, index) {

                    if(value){
                        return timestampToTime(value);
                    }

                    function timestampToTime(timestamp) {
                        var date = new Date(timestamp * 1000);//时间戳为10位需*1000，时间戳为13位的话不需乘1000
                        var Y = date.getFullYear() + '-';
                        var M = (date.getMonth()+1 < 10 ? '0'+(date.getMonth()+1) : date.getMonth()+1) + '-';
                        var D = date.getDate()<10? '0'+date.getDate():date.getDate();

                        return Y+M+D;
                    }
                },
                car_back:function (value, row, index) {

                    if(value && row.tenancy_term){
                        value = timestampToTime(value);


                         return GetNextMonthDay(value,row.tenancy_term);
                    }

                    //获取几个月后的日期
                     function GetNextMonthDay(date, monthNum){
                         var dateArr = date.split('-');
                         var year = dateArr[0]; //获取当前日期的年份
                         var month = dateArr[1]; //获取当前日期的月份
                         var day = dateArr[2]; //获取当前日期的日
                         var days = new Date(year, month, 0);
                         days = days.getDate(); //获取当前日期中的月的天数
                         var year2 = year;
                         var month2 = parseInt(month) + parseInt(monthNum);
                         if (month2 >12) {
                             year2 = parseInt(year2) + parseInt((parseInt(month2) / 12 == 0 ? 1 : parseInt(month2) / 12));
                             month2 = parseInt(month2) % 12;
                         }
                         var day2 = day;
                         var days2 = new Date(year2, month2, 0);
                         days2 = days2.getDate();
                         if (day2 > days2) {
                             day2 = days2;
                         }
                         if (month2 < 10) {
                             month2 = '0' + month2;
                         }

                         var t2 = year2 + '-' + month2 + '-' + day2;
                         return t2;
                     }

                    function timestampToTime(timestamp) {
                        var date = new Date(timestamp * 1000);//时间戳为10位需*1000，时间戳为13位的话不需乘1000
                        var Y = date.getFullYear() + '-';
                        var M = (date.getMonth()+1 < 10 ? '0'+(date.getMonth()+1) : date.getMonth()+1) + '-';
                        var D = date.getDate()<10? '0'+date.getDate():date.getDate();

                        return Y+M+D;
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