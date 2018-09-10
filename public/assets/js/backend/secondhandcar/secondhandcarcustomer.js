define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'secondhandcar/secondhandcarcustomer/index',
                    table: 'second_sales_order',
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
                        {field: 'id', title: __('Id'),operate:false},
                        {field: 'plansecond.licenseplatenumber', title: __('车牌号')},
                        {field: 'order_no', title: __('订单编号')}, 
                        {field: 'createtime', title: __('订车时间'), operate:'RANGE', addclass:'datetimerange', formatter: Controller.api.formatter.datetime},
                        {field: 'delivery_datetime', title: __('提车时间'), operate:'RANGE', addclass:'datetimerange', formatter: Controller.api.formatter.datetime},
                        {field: 'models.name', title: __('二手车型号')},
                        {field: 'admin.nickname', title: __('销售员')},

                        {field: 'username', title: __('客户姓名')}, 
                        {field: 'phone', title: __('手机号')},
                        {field: 'id_card', title: __('身份证号')},
                        
                        {field: 'genderdata', title: __('性别'), searchList: {"male":__('男'),"female":__('女')}, formatter: Table.api.formatter.normal},
                        {field: 'plansecond.newpayment', title: __('新首付（元）'),operate:false },
                        {field: 'plansecond.monthlypaymen', title: __('月供（元）'),operate:false },
                        {field: 'plansecond.periods', title: __('期数'),operate:false },
                        {field: 'plansecond.totalprices', title: __('总价（元）'),operate:false },
                        {field: 'plansecond.bond', title: __('保证金（元）'),operate:false },
                        {field: 'plansecond.tailmoney', title: __('尾款（元）'),operate:false },

                        {field: 'operate', title: __('Operate'), table: table, buttons: [
                            {name: 'secondDetails', text: '查看详细资料', title: '查看订单详细资料' ,icon: 'fa fa-eye',classname: 'btn btn-xs btn-primary btn-dialog btn-secondDetails', 
                                url: 'secondhandcar/secondhandcarcustomer/secondDetails', callback:function(data){
                                    console.log(data)
                                }
                            } 
                            ],
                        
                            events: Table.api.events.operate,
                             
                            formatter: Table.api.formatter.operate
                           
                        }
                    ]
                ]
            });

            

            // 为表格绑定事件
            Table.api.bindevent(table);

            table.on('load-success.bs.table', function (e, data) {
                // console.log(data);
               
                $(".btn-secondDetails").data("area", ["95%", "95%"]);
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
                Form.api.bindevent($("form[role=form]"));
            },
            formatter:{
                datetime:function (value, row, index) {

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

                }
            }

        }
    };
    return Controller;
});