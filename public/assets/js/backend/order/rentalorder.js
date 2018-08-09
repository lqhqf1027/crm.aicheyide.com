define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'order/rentalorder/index',
                    add_url: 'order/rentalorder/add',
                    edit_url: 'order/rentalorder/edit',
                    del_url: 'order/rentalorder/del',
                    multi_url: 'order/rentalorder/multi',
                    table: 'rental_order',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'plan_car_rental_name', title: __('Plan_car_rental_name')},
                        {field: 'sales_id', title: __('Sales_id')},
                        {field: 'admin_id', title: __('Admin_id')},
                        {field: 'control_id', title: __('Control_id')},
                        {field: 'rental_car_id', title: __('Rental_car_id')},
                        {field: 'insurance_id', title: __('Insurance_id')},
                        {field: 'general_manager_id', title: __('General_manager_id')},
                        {field: 'order_no', title: __('Order_no')},
                        {field: 'cash_pledge', title: __('Cash_pledge')},
                        {field: 'rental_price', title: __('Rental_price')},
                        {field: 'tenancy_term', title: __('Tenancy_term')},
                        {field: 'username', title: __('Username')},
                        {field: 'phone', title: __('Phone')},
                        {field: 'id_card', title: __('Id_card')},
                        {field: 'genderdata', title: __('Genderdata'), searchList: {"male":__('Genderdata male'),"female":__('Genderdata female')}, formatter: Table.api.formatter.normal},
                        {field: 'gps_installation_name', title: __('Gps_installation_name')},
                        {field: 'gps_installation_datetime', title: __('Gps_installation_datetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'information_audition_name', title: __('Information_audition_name')},
                        {field: 'information_audition_datetime', title: __('Information_audition_datetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'Insurance_status_name', title: __('Insurance_status_name')},
                        {field: 'Insurance_status_datetime', title: __('Insurance_status_datetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'general_manager_name', title: __('General_manager_name')},
                        {field: 'general_manager_datetime', title: __('General_manager_datetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'emergency_contact_1', title: __('Emergency_contact_1')},
                        {field: 'emergency_contact_2', title: __('Emergency_contact_2')},
                        {field: 'id_cardimages', title: __('Id_cardimages'), formatter: Table.api.formatter.images},
                        {field: 'drivers_licenseimages', title: __('Drivers_licenseimages'), formatter: Table.api.formatter.images},
                        {field: 'residence_bookletimages', title: __('Residence_bookletimages'), formatter: Table.api.formatter.images},
                        {field: 'call_listfilesimages', title: __('Call_listfilesimages'), formatter: Table.api.formatter.images},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'delivery_datetime', title: __('Delivery_datetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            

            // 为表格绑定事件
            Table.api.bindevent(table);

        },
        vehicleManagement:function(){
 
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
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    //第一步的点击
    $('#onebtn').click(function(){
        var onebtns=document.getElementById("onebtn");
     	var twobtns=document.getElementById("twobtn");
     	var soutside1ab=document.getElementById("outside1abs");
     	var soutside2as=document.getElementById("outside2as");
        var oneforms=document.getElementById("oneform");
        var twoforms=document.getElementById("twoform");
        var threeforms=document.getElementById("add-form");
        
        var $ids = $('#hiddenPlanName').val();
        console.log($ids);
        // return;
        Layer.confirm(
            __('需要车管文员确认车辆状态及证件是否齐全，是否发起确认请求?'),
            {icon: 3, title: __('Warning'), shadeClose: true},

            function (index) {

                Fast.api.ajax({
                    url: 'order/rentalorder/vehicleManagement',
                    data: {id: JSON.stringify($ids)}
                }, function (data, rets) {
                    // console.log(data);
                    // return;
                    Toastr.success("成功");
                    Layer.close(index);
                    Layer.open({
                        type: 1,
                        skin: 'layui-layer-demo', //样式类名
                        closeBtn: 0, //不显示关闭按钮
                        anim: 2,
                        shadeClose: false, //开启遮罩关闭
                        content: '请耐心等待车管人员的确认'
                      });

                    
                    return false;
                }, function (data, ret) {
                    //失败的回调
                    
                    return false;
                });


            }
        );
     	
		soutside1ab.classList.remove("outside1ab");
		oneforms.style.display="none";
        twoforms.style.display="block";
        
       
        
    });

    //第二步的点击
    $('#twobtn').click(function(){
        var onebtns=document.getElementById("onebtn");
     	var twobtns=document.getElementById("twobtn");
     	var soutside1ab=document.getElementById("outside1abs");
     	var soutside2as=document.getElementById("outside2as");
        var oneforms=document.getElementById("oneform");
        var twoforms=document.getElementById("twoform");
        var threeforms=document.getElementById("add-form");
     	
		threeforms.style.display="block";
        twoforms.style.display="none";
        soutside2as.classList.remove("outside2a");
       
        
    });

    return Controller;
});