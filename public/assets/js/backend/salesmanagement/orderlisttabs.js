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
            order_acar: function () {

                // 表格1
                var orderAcar = $("#orderAcar");

                $(".btn-add").data("area", ["95%", "95%"]);
                $(".btn-edit").data("area", ["95%", "95%"]);

                // 初始化表格
                orderAcar.bootstrapTable({
                    url: 'salesmanagement/Orderlisttabs/orderAcar',
                    extend: {
                        // index_url: 'order/salesorder/index',
                        add_url: 'order/salesorder/add',
                        edit_url: 'order/salesorder/edit',
                        del_url: 'order/salesorder/del',
                        multi_url: 'order/salesorder/multi',
                        table: 'sales_order',
                    },
                    toolbar: '#toolbar1',
                    pk: 'id',
                    sortName: 'id',
                    columns: [
                        [
                            { checkbox: true },
                            { field: 'id', title: __('Id') },
                            { field: 'order_no', title: __('Order_no') },
                            { field: 'createtime', title: __('Createtime'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime },

                            // { field: 'financial_platform_name', title: __('金融平台') },
                            { field: 'models_name', title: __('销售车型') },
                            { field: 'username', title: __('Username') },
                            { field: 'genderdata', title: __('Genderdata'), visible: false, searchList: { "male": __('genderdata male'), "female": __('genderdata female') } },
                            { field: 'genderdata_text', title: __('Genderdata'), operate: false },
                            { field: 'phone', title: __('Phone') },
                            { field: 'id_card', title: __('Id_card') },
                            {
                                field: 'id', title: __('查看详细资料'), table: orderAcar, buttons: [
                                    {
                                        name: 'details', text: '查看详细资料', title: '查看订单详细资料', icon: 'fa fa-eye', classname: 'btn btn-xs btn-primary btn-dialog btn-details',
                                        url: 'salesmanagement/Orderlisttabs/details', callback: function (data) {
                                            console.log(data)
                                        }
                                    }
                                ],

                                operate: false, formatter: Table.api.formatter.buttons
                            },
                            { field: 'payment', title: __('首付（元）') },
                            { field: 'monthly', title: __('月供（元）') },
                            { field: 'nperlist', title: __('期数') },
                            { field: 'margin', title: __('保证金（元）') },
                            { field: 'tail_section', title: __('尾款（元）') },
                            { field: 'gps', title: __('GPS（元）') },

                            {
                                field: 'operate', title: __('Operate'), table: orderAcar,
                                buttons: [
                                    {
                                        name: 'submit_audit', text: '提交给内勤', title: '提交给内勤', icon: 'fa fa-share', extend: 'data-toggle="tooltip"', classname: 'btn btn-xs btn-info btn-submit_audit',
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
                                        hidden: function (row) {  /**提供担保人 */
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
                    channel: 'demo-newpass',
                    onMessage: function(message){
                        Layer.alert('新消息：'+message.content,{ icon:0},function(index){
                            Layer.close(index);
                            $(".btn-refresh").trigger("click");
                        });
                        
                    }
                });
                //提供保证金
                goeasy.subscribe({
                    channel: 'demo-newdata',
                    onMessage: function(message){
                        Layer.alert('新消息：'+message.content,{ icon:0},function(index){
                            Layer.close(index);
                            $(".btn-refresh").trigger("click");
                        });
                        
                    }
                });

                //不通过
                goeasy.subscribe({
                    channel: 'demo-newnopass',
                    onMessage: function(message){
                        Layer.alert('新消息：'+message.content,{ icon:0},function(index){
                            Layer.close(index);
                            $(".btn-refresh").trigger("click");
                        });
                        
                    }
                });


                // alert(Table.api.getrowdata(table, index));
            },
            order_rental: function () {

                // 租车单
 
                var orderRental = $("#orderRental"); 
                 
                $(".btn-add").data("area", ["95%","95%"]); 
                $(".btn-edit").data("area", ["95%","95%"]); 
                
                 // 初始化表格
                 orderRental.bootstrapTable({
                url: 'salesmanagement/Orderlisttabs/orderRental',
                extend: {
                    // index_url: 'order/salesorder/index',
                    // add_url: 'order/rentalorder/add',
                    edit_url: 'order/rentalorder/edit',
                    del_url: 'order/rentalorder/del',
                    multi_url: 'order/rentalorder/multi',
                    reserve_url: 'order/rentalorder/reserve',
                    table: 'rental_order',
                },
                toolbar: '#toolbar2',
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        // {field: 'plan_car_rental_name', title: __('Plan_car_rental_name')},
                        // {field: 'sales_id', title: __('Sales_id')},
                        // {field: 'admin_id', title: __('Admin_id')},
                        // {field: 'control_id', title: __('Control_id')},
                        // {field: 'rental_car_id', title: __('Rental_car_id')},
                        // {field: 'insurance_id', title: __('Insurance_id')},
                        // {field: 'general_manager_id', title: __('General_manager_id')},
                        {field: 'order_no', title: __('Order_no')}, 
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'username', title: __('Username'),formatter:function(value,row,index){
                            if(row.order_no ==  null){ /**如果订单编号为空，就处于预定状态 */
                                return row.username+' <span class="label label-success">预定中</span>'

                            }
                            else{
                                return row.username
                            }
                        }}, 
                        {field: 'phone', title: __('Phone')},
                        {field: 'id_card', title: __('Id_card')},
                        {field: 'id', title: __('查看详细资料'), table: orderRental, buttons: [
                            {name: 'rentalDetails', text: '查看详细资料', title: '查看订单详细资料' ,icon: 'fa fa-eye',classname: 'btn btn-xs btn-primary btn-dialog btn-rentalDetails', 
                                url: 'salesmanagement/Orderlisttabs/rentaldetails', callback:function(data){
                                    console.log(data)
                                }
                            } 
                            ],
                            
                            operate:false, formatter: Table.api.formatter.buttons
                        },
                        {field: 'genderdata', title: __('Genderdata'), searchList: {"male":__('Genderdata male'),"female":__('Genderdata female')}, formatter: Table.api.formatter.normal},
                        {field: 'cash_pledge', title: __('Cash_pledge')},
                        {field: 'rental_price', title: __('Rental_price')},
                        {field: 'tenancy_term', title: __('Tenancy_term')},
                        // {field: 'gps_installation_name', title: __('Gps_installation_name')},
                        // {field: 'gps_installation_datetime', title: __('Gps_installation_datetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        // {field: 'information_audition_name', title: __('Information_audition_name')},
                        // {field: 'information_audition_datetime', title: __('Information_audition_datetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        // {field: 'Insurance_status_name', title: __('Insurance_status_name')},
                        // {field: 'Insurance_status_datetime', title: __('Insurance_status_datetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        // {field: 'general_manager_name', title: __('General_manager_name')},
                        // {field: 'general_manager_datetime', title: __('General_manager_datetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        // {field: 'emergency_contact_1', title: __('Emergency_contact_1')},
                        // {field: 'emergency_contact_2', title: __('Emergency_contact_2')},
                        // {field: 'id_cardimages', title: __('Id_cardimages'), formatter: Table.api.formatter.images},
                        // {field: 'drivers_licenseimages', title: __('Drivers_licenseimages'), formatter: Table.api.formatter.images},
                        // {field: 'residence_bookletimages', title: __('Residence_bookletimages'), formatter: Table.api.formatter.images},
                        // {field: 'call_listfilesimages', title: __('Call_listfilesimages'), formatter: Table.api.formatter.images},
                        // {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        // {field: 'delivery_datetime', title: __('Delivery_datetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        // {field: 'operate', title: __('Operate'), table: orderRental, events: Table.api.events.operate, formatter: Table.api.formatter.operate}

                        {field: 'operate', title: __('Operate'), table: orderRental, 
                        buttons: [
                            {
                                name:'customerInformation',text:'补全客户信息', title:'补全客户信息', icon: 'fa fa-share',extend: 'data-toggle="tooltip"',classname: 'btn btn-xs btn-info btn-customerInformation',
                                url: 'order/rentalorder/add',  
                                //等于is_reviewing_true 的时候操作栏显示的是正在审核四个字，隐藏编辑和删除
                                //等于is_reviewing 的时候操作栏显示的是提交审核按钮 四个字，显示编辑和删除 
                                //....
                                hidden:function(row){ /**补全客户信息 */
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
                                    else if(row.review_the_data == 'is_reviewing_false'){
                                        return true;
                                    }
                                    else if(row.review_the_data == 'is_reviewing_nopass'){
                                        return true;
                                    }
                                }
                            },
                            {
                                name:'control',text:'提交风控审核', title:'提交风控审核', icon: 'fa fa-share',extend: 'data-toggle="tooltip"',classname: 'btn btn-xs btn-info btn-control',
                                url: 'order/rentalorder/control',  
                                hidden:function(row){ /**提交风控审核 */
                                    if(row.review_the_data == 'is_reviewing_false'){ 
                                        return false; 
                                    }  
                                    else if(row.review_the_data == 'is_reviewing_true'){
                                        return true;
                                    }
                                    else if(row.review_the_data == 'is_reviewing_pass'){
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
                            { 
                                icon: 'fa fa-trash', name: 'del', icon: 'fa fa-trash', extend: 'data-toggle="tooltip"',text:'删除预定', title: __('删除预定'),classname: 'btn btn-xs btn-danger btn-delone',
                                url:'order/rentalorder/del',/**删除 */
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
                            { 
                                icon: 'fa fa-trash', name: 'del', icon: 'fa fa-trash', extend: 'data-toggle="tooltip"',text:'删除订单', title: __('删除订单'),classname: 'btn btn-xs btn-danger btn-delone',
                                url:'order/rentalorder/del',/**删除 */
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
                            { 
                                name: 'edit',text: '',icon: 'fa fa-pencil',extend: 'data-toggle="tooltip"',text:'修改订单', title: __('修改订单'),classname: 'btn btn-xs btn-success btn-editone', 
                                url:'order/rentalorder/edit',/**修改订单 */
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
                            {
                                name: 'is_reviewing_true',text: '车管正在处理中',title:'车管正在处理中',
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
                                    else if(row.review_the_data == 'is_reviewing_argee'){
                                      
                                        return true;
                                    } 
                                    else if(row.review_the_data == 'is_reviewing_nopass'){
                                        return true;
                                    }
                                }
                            },
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
                                    else if(row.review_the_data == 'is_reviewing_nopass'){
                                        return true;
                                    }
                                }
                            },
                            {
                                name: 'is_reviewing_pass', icon: 'fa fa-check-circle', text: '征信已通过', classname: ' text-info ',
                                hidden: function (row) {  /**征信已通过 */
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
                                    else if (row.review_the_data == 'is_reviewing_nopass') {
                                        return true;
                                    }
                                }
                            },
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
                                    else if (row.review_the_data == 'is_reviewing_true') {
                                        return true;
                                    }
                                    else if(row.review_the_data == 'is_reviewing_false'){
                                        return true;
                                    }
                                }
                            },

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
                channel: 'demo-argee',
                onMessage: function(message){
                    Layer.alert('新消息：'+message.content,{ icon:0},function(index){
                        Layer.close(index);
                        $(".btn-refresh").trigger("click");
                    });
                }
            });

            //通过
            goeasy.subscribe({
                channel: 'demo-rentalpass',
                onMessage: function(message){
                    Layer.alert('新消息：'+message.content,{ icon:0},function(index){
                        Layer.close(index);
                        $(".btn-refresh").trigger("click");
                    });
                    
                }
            });
            //不通过
            goeasy.subscribe({
                channel: 'demo-rentalnopass',
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
                    
                var url = 'order/rentalorder/reserve';
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
            order_second: function () {

                // 表格3
                var orderSecond = $("#orderSecond");

                $(".btn-add").data("area", ["95%", "95%"]);
                $(".btn-edit").data("area", ["95%", "95%"]);

                // 初始化表格
                orderSecond.bootstrapTable({
                    url: 'salesmanagement/Orderlisttabs/orderSecond',
                    extend: {
                        // index_url: 'order/secondsalesorder/index',
                        add_url: 'order/secondsalesorder/add',
                        edit_url: 'order/secondsalesorder/edit',
                        del_url: 'order/secondsalesorder/del',
                        multi_url: 'order/secondsalesorder/multi',
                        table: 'second_sales_order',
                    },
                    toolbar: '#toolbar3',
                    pk: 'id',
                    sortName: 'id',
                    columns: [
                        [
                            { checkbox: true },
                            { field: 'id', title: __('Id') },
                            { field: 'order_no', title: __('Order_no') },
                            { field: 'createtime', title: __('Createtime'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime },

                            { field: 'models_name', title: __('销售车型') },
                            { field: 'username', title: __('Username') },
                            { field: 'genderdata', title: __('Genderdata'), visible: false, searchList: { "male": __('genderdata male'), "female": __('genderdata female') } },
                            { field: 'genderdata_text', title: __('Genderdata'), operate: false },
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
                            
                            { field: 'newpayment', title: __('新首付（元）') },
                            { field: 'monthlypaymen', title: __('月供（元）') },
                            { field: 'periods', title: __('期数') },
                            { field: 'totalprices', title: __('总价（元）') },

                            {
                                field: 'operate', title: __('Operate'), table: orderSecond,
                                buttons: [
                                    {
                                        name: 'second_audit', text: '提交审核', title: '审核征信', icon: 'fa fa-share', extend: 'data-toggle="tooltip"', classname: 'btn btn-xs btn-info btn-second_audit',
                                        url: 'order/secondsalesorder/setAudit',
                                        //等于is_reviewing_true 的时候操作栏显示的是正在审核四个字，隐藏编辑和删除
                                        //等于is_reviewing 的时候操作栏显示的是提交审核按钮 四个字，显示编辑和删除 
                                        //....
                                        hidden: function (row) { /**提交审核 */
                                            if (row.review_the_data == 'is_reviewing') {
                                                return false;
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
                                        icon: 'fa fa-trash', name: 'del', icon: 'fa fa-trash', extend: 'data-toggle="tooltip"', title: __('Del'), classname: 'btn btn-xs btn-danger btn-delone',
                                        url: 'order/secondsalesorder/del',/**删除 */
                                        hidden: function (row) {
                                            if (row.review_the_data == 'is_reviewing') {
                                                return false;
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
                                        },

                                    },
                                    {
                                        name: 'edit', text: '', icon: 'fa fa-pencil', extend: 'data-toggle="tooltip"', title: __('Edit'), classname: 'btn btn-xs btn-success btn-editone',
                                        url: 'order/secondsalesorder/edit',/**编辑 */
                                        hidden: function (row, value, index) {
                                            if (row.review_the_data == 'is_reviewing') {
                                                return false;
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
                                        },
                                    },
                                    {
                                        name: 'is_reviewing_true', text: '正在审核中',
                                        hidden: function (row) {  /**正在审核 */
                                            if (row.review_the_data == 'is_reviewing_true') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'is_reviewing') {
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

                 //通过
                goeasy.subscribe({
                    channel: 'demo-secondpass',
                    onMessage: function(message){
                        Layer.alert('新消息：'+message.content,{ icon:0},function(index){
                            Layer.close(index);
                            $(".btn-refresh").trigger("click");
                        });
                        
                    }
                });
                 //提供保证金
                goeasy.subscribe({
                    channel: 'demo-seconddata',
                    onMessage: function(message){
                        Layer.alert('新消息：'+message.content,{ icon:0},function(index){
                            Layer.close(index);
                            $(".btn-refresh").trigger("click");
                        });
                        
                    }
                });
                //不通过
                goeasy.subscribe({
                    channel: 'demo-secondnopass',
                    onMessage: function(message){
                        Layer.alert('新消息：'+message.content,{ icon:0},function(index){
                            Layer.close(index);
                            $(".btn-refresh").trigger("click");
                        });
                        
                    }
                });

                // alert(Table.api.getrowdata(table, index));
            },
            order_full: function () {

                // 表格1
                var orderFull = $("#orderFull");

                $(".btn-add").data("area", ["95%", "95%"]);
                $(".btn-edit").data("area", ["95%", "95%"]);

                // 初始化表格
                orderFull.bootstrapTable({
                    url: 'salesmanagement/Orderlisttabs/orderFull',
                    extend: {
                        // index_url: 'order/fullparmentorder/index',
                        add_url: 'order/fullparmentorder/add',
                        edit_url: 'order/fullparmentorder/edit',
                        del_url: 'order/fullparmentorder/del',
                        multi_url: 'order/fullparmentorder/multi',
                        table: 'full_parment_order',
                    },
                    toolbar: '#toolbar4',
                    pk: 'id',
                    sortName: 'id',
                    columns: [
                        [
                            { checkbox: true },
                            { field: 'id', title: __('Id') },
                            { field: 'order_no', title: __('Order_no') },
                            { field: 'createtime', title: __('Createtime'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime },
                            { field: 'delivery_datetime', title: __('Delivery_datetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                            { field: 'models_name', title: __('销售车型') },
                            { field: 'full_total_price', title: __('全款总价（元）') },

                            {
                                field: 'id', title: __('查看详细资料'), table: orderFull, buttons: [
                                    {
                                        name: 'fulldetails', text: '查看详细资料', title: '查看订单详细资料', icon: 'fa fa-eye', classname: 'btn btn-xs btn-primary btn-dialog btn-fulldetails',
                                        url: 'salesmanagement/Orderlisttabs/fulldetails', callback: function (data) {
                                            console.log(data)
                                        }
                                    }
                                ],

                                operate: false, formatter: Table.api.formatter.buttons
                            },

                            {field: 'username', title: __('Username')},
                            {field: 'phone', title: __('Phone')},
                            {field: 'id_card', title: __('Id_card')},
                            {field: 'genderdata', title: __('Genderdata'), searchList: {"male":__('Genderdata male'),"female":__('Genderdata female')}, formatter: Table.api.formatter.normal},
                            {field: 'city', title: __('City')},
                            // {field: 'detailed_address', title: __('Detailed_address')},
                            {
                                field: 'operate', title: __('Operate'), table: orderFull,
                                buttons: [
                                    {
                                        name: 'submitCar', text: '提交车管备车', icon: 'fa fa-share', extend: 'data-toggle="tooltip"', title: __('提交车管备车'), classname: 'btn btn-xs btn-success btn-submitCar',
                                        url: 'order/fullparmentorder/submitCar',/**提交给车管 */
                                        //等于is_reviewing_true 的时候操作栏显示的是正在审核四个字，隐藏编辑和删除
                                        //等于is_reviewing 的时候操作栏显示的是提交审核按钮 四个字，显示编辑和删除 
                                        //....
                                        hidden: function (row) { /**提交审核 */
                                            if (row.review_the_data == 'is_reviewing') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_true') {
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
                                        url: 'order/fullparmentorder/del',/**删除 */
                                        hidden: function (row) {
                                            if (row.review_the_data == 'is_reviewing') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_true') {
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
                                        name: 'edit', text: '', icon: 'fa fa-pencil', extend: 'data-toggle="tooltip"', title: __('Edit'), classname: 'btn btn-xs btn-success btn-editone',
                                        url: 'order/fullparmentorder/edit',/**编辑 */
                                        hidden: function (row, value, index) {
                                            if (row.review_the_data == 'is_reviewing') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'is_reviewing_true') {
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
                                            else if (row.review_the_data == 'is_reviewing') {
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
                                        name: 'getCar', text: '是否提取车辆', icon: 'fa fa-share', extend: 'data-toggle="tooltip"', title: __('车管备车成功，等待提车'), classname: 'btn btn-xs btn-success btn-getCar',
                                        url: 'order/fullparmentorder/getCar',
                                        hidden: function (row) {  /**车管备车成功，等待提车 */
                                            if (row.review_the_data == 'is_reviewing_pass') {
                                                return false;
                                            }
                                            else if (row.review_the_data == 'is_reviewing') {
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


                orderFull.on('load-success.bs.table', function (e, data) {
                    // console.log(data);
                    $('#badge_order_full').text(data.total);
                    $(".btn-fulldetails").data("area", ["95%", "95%"]);
                })
                // 为表格1绑定事件
                Table.api.bindevent(orderFull);

                // alert(Table.api.getrowdata(table, index));
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

                                    Toastr.success(ret.msg);
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
                        var url = 'order/rentalorder/add';


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

                                    url: 'order/rentalorder/control',
                                    data: {id: row[options.pk]}
 
                                }, function (data, ret) {

                                    Toastr.success(ret.msg);
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
                    //二手车提交风控审核
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

                                    url: 'order/secondsalesorder/setAudit',
                                    data: {id: row[options.pk]}
 
                                }, function (data, ret) {

                                    Toastr.success(ret.msg);
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
                    //全款车提交车管
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
                            __('请确认资料完整，是否开始提交车管备车?'),
                            { icon: 3, title: __('Warning'), offset: [top, left], shadeClose: true },

                            function (index) {
                                var table = $(that).closest('table');
                                var options = table.bootstrapTable('getOptions');


                                Fast.api.ajax({

                                    url: 'order/fullparmentorder/submitCar',
                                    data: {id: row[options.pk]}
 
                                }, function (data, ret) {

                                    Toastr.success(ret.msg);
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
                    //全款车提取
                    'click .btn-getCar': function (e, value, row, index) {

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
                            __('车管备车成功，是否进行提取?'),
                            { icon: 3, title: __('Warning'), offset: [top, left], shadeClose: true },

                            function (index) {
                                var table = $(that).closest('table');
                                var options = table.bootstrapTable('getOptions');


                                Fast.api.ajax({

                                    url: 'order/fullparmentorder/getCar',
                                    data: {id: row[options.pk]}
 
                                }, function (data, ret) {

                                    Toastr.success(ret.msg);
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


                        // console.log(url);
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