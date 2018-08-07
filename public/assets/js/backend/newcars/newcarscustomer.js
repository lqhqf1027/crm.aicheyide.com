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

            prepare_lift_car: function () {
                // 表格1
                var prepareLiftCar = $("#prepareLiftCar");
                prepareLiftCar.on('load-success.bs.table', function (e, data) {
                    console.log(data.total);
                    $('#new-customer').text(data.total);

                })
                prepareLiftCar.on('post-body.bs.table', function (e, settings, json, xhr) {
                    $(".btn-newCustomer").data("area", ["50%", "50%"]);
                });
                // 初始化表格
                prepareLiftCar.bootstrapTable({
                    url: "newcars/Newcarscustomer/prepare_lift_car",
                    extend: {
                        index_url: 'order/salesorder/index',
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
                            { field: 'plan_acar_name', title: __('Plan_acar_name') },
                            // { field: 'sales_id', title: __('Sales_id') },
                            // { field: 'backoffice_id', title: __('Backoffice_id') },
                            // { field: 'control_id', title: __('Control_id') },
                            // { field: 'new_car_id', title: __('New_car_id') },
                            { field: 'order_no', title: __('Order_no') },
                            { field: 'username', title: __('Username') },
                            { field: 'phone', title: __('Phone') },
                            { field: 'id_card', title: __('Id_card') },
                            { field: 'genderdata', title: __('Genderdata'), visible: false, searchList: { "male": __('genderdata male'), "female": __('genderdata female') } },
                            { field: 'genderdata_text', title: __('Genderdata'), operate: false },
                            { field: 'city', title: __('City') },
                            { field: 'detailed_address', title: __('Detailed_address') },
                            { field: 'emergency_contact_1', title: __('Emergency_contact_1') },
                            { field: 'emergency_contact_2', title: __('Emergency_contact_2') },
                            { field: 'family_members', title: __('Family_members') },
                            { field: 'customer_source', title: __('Customer_source'), visible: false, searchList: { "direct_the_guest": __('customer_source direct_the_guest'), "turn_to_introduce": __('customer_source turn_to_introduce') } },
                            { field: 'customer_source_text', title: __('Customer_source'), operate: false },
                            { field: 'turn_to_introduce_name', title: __('Turn_to_introduce_name') },
                            { field: 'turn_to_introduce_phone', title: __('Turn_to_introduce_phone') },
                            { field: 'turn_to_introduce_card', title: __('Turn_to_introduce_card') },
                            { field: 'id_cardimages', title: __('Id_cardimages'), formatter: Table.api.formatter.images },
                            { field: 'drivers_licenseimages', title: __('Drivers_licenseimages'), formatter: Table.api.formatter.images },
                            { field: 'residence_bookletimages', title: __('Residence_bookletimages'), formatter: Table.api.formatter.images },
                            { field: 'housingimages', title: __('Housingimages'), formatter: Table.api.formatter.images },
                            { field: 'bank_cardimages', title: __('Bank_cardimages'), formatter: Table.api.formatter.images },
                            { field: 'application_formimages', title: __('Application_formimages'), formatter: Table.api.formatter.images },
                            { field: 'call_listfiles', title: __('Call_listfiles') },
                            { field: 'credit_reportimages', title: __('Credit_reportimages'), formatter: Table.api.formatter.images },
                            { field: 'deposit_contractimages', title: __('Deposit_contractimages'), formatter: Table.api.formatter.images },
                            { field: 'deposit_receiptimages', title: __('Deposit_receiptimages'), formatter: Table.api.formatter.images },
                            { field: 'guarantee_id_cardimages', title: __('Guarantee_id_cardimages'), formatter: Table.api.formatter.images },
                            { field: 'guarantee_agreementimages', title: __('Guarantee_agreementimages'), formatter: Table.api.formatter.images },
                            { field: 'review_the_data', title: __('Review_the_data'), visible: false, searchList: { "not_through": __('review_the_data not_through'), "through": __('review_the_data through'), "credit_report": __('review_the_data credit_report'), "the_guarantor": __('review_the_data the_guarantor'), "for_the_car": __('review_the_data for_the_car'), "the_car": __('review_the_data the_car') } },
                            { field: 'review_the_data_text', title: __('Review_the_data'), operate: false },
                            { field: 'createtime', title: __('Createtime'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime },
                            { field: 'delivery_datetime', title: __('Delivery_datetime'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime },
                            { field: 'operate', title: __('Operate'), table: prepareLiftCar, events: Table.api.events.operate, formatter: Table.api.formatter.operate }
                        ]
                    ]
                });
                // 为表格1绑定事件
                Table.api.bindevent(prepareLiftCar);

            },
            // already_lift_car: function () {
            //     // 表格2
            //     var alreadyLiftCar = $("#alreadyLiftCar");
            //     alreadyLiftCar.on('post-body.bs.table', function (e, settings, json, xhr) {
            //         // $(".btn-newCustomer").data("area", ["30%", "30%"]);
            //     });
            //     // 初始化表格
            //     alreadyLiftCar.bootstrapTable({
            //         url: 'newcars/Newcarscustomer/already_lift_car',
            //         extend: {
            //             index_url: 'order/salesorder/index',
            //             add_url: 'order/salesorder/add',
            //             edit_url: 'order/salesorder/edit',
            //             del_url: 'order/salesorder/del',
            //             multi_url: 'order/salesorder/multi',
            //             table: 'sales_order',
            //         },
            //         toolbar: '#toolbar2',
            //         pk: 'id',
            //         sortName: 'id',
            //         columns: [
            //             [
            //                 { checkbox: true },
            //                 { field: 'id', title: __('Id') },
            //                 { field: 'plan_acar_name', title: __('Plan_acar_name') },
            //                 { field: 'sales_id', title: __('Sales_id') },
            //                 { field: 'backoffice_id', title: __('Backoffice_id') },
            //                 { field: 'control_id', title: __('Control_id') },
            //                 { field: 'new_car_id', title: __('New_car_id') },
            //                 { field: 'order_no', title: __('Order_no') },
            //                 { field: 'username', title: __('Username') },
            //                 { field: 'phone', title: __('Phone') },
            //                 { field: 'id_card', title: __('Id_card') },
            //                 { field: 'genderdata', title: __('Genderdata'), visible: false, searchList: { "male": __('genderdata male'), "female": __('genderdata female') } },
            //                 { field: 'genderdata_text', title: __('Genderdata'), operate: false },
            //                 { field: 'city', title: __('City') },
            //                 { field: 'detailed_address', title: __('Detailed_address') },
            //                 { field: 'emergency_contact_1', title: __('Emergency_contact_1') },
            //                 { field: 'emergency_contact_2', title: __('Emergency_contact_2') },
            //                 { field: 'family_members', title: __('Family_members') },
            //                 { field: 'customer_source', title: __('Customer_source'), visible: false, searchList: { "direct_the_guest": __('customer_source direct_the_guest'), "turn_to_introduce": __('customer_source turn_to_introduce') } },
            //                 { field: 'customer_source_text', title: __('Customer_source'), operate: false },
            //                 { field: 'turn_to_introduce_name', title: __('Turn_to_introduce_name') },
            //                 { field: 'turn_to_introduce_phone', title: __('Turn_to_introduce_phone') },
            //                 { field: 'turn_to_introduce_card', title: __('Turn_to_introduce_card') },
            //                 { field: 'id_cardimages', title: __('Id_cardimages'), formatter: Table.api.formatter.images },
            //                 { field: 'drivers_licenseimages', title: __('Drivers_licenseimages'), formatter: Table.api.formatter.images },
            //                 { field: 'residence_bookletimages', title: __('Residence_bookletimages'), formatter: Table.api.formatter.images },
            //                 { field: 'housingimages', title: __('Housingimages'), formatter: Table.api.formatter.images },
            //                 { field: 'bank_cardimages', title: __('Bank_cardimages'), formatter: Table.api.formatter.images },
            //                 { field: 'application_formimages', title: __('Application_formimages'), formatter: Table.api.formatter.images },
            //                 { field: 'call_listfiles', title: __('Call_listfiles') },
            //                 { field: 'credit_reportimages', title: __('Credit_reportimages'), formatter: Table.api.formatter.images },
            //                 { field: 'deposit_contractimages', title: __('Deposit_contractimages'), formatter: Table.api.formatter.images },
            //                 { field: 'deposit_receiptimages', title: __('Deposit_receiptimages'), formatter: Table.api.formatter.images },
            //                 { field: 'guarantee_id_cardimages', title: __('Guarantee_id_cardimages'), formatter: Table.api.formatter.images },
            //                 { field: 'guarantee_agreementimages', title: __('Guarantee_agreementimages'), formatter: Table.api.formatter.images },
            //                 { field: 'review_the_data', title: __('Review_the_data'), visible: false, searchList: { "not_through": __('review_the_data not_through'), "through": __('review_the_data through'), "credit_report": __('review_the_data credit_report'), "the_guarantor": __('review_the_data the_guarantor'), "for_the_car": __('review_the_data for_the_car'), "the_car": __('review_the_data the_car') } },
            //                 { field: 'review_the_data_text', title: __('Review_the_data'), operate: false },
            //                 { field: 'createtime', title: __('Createtime'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime },
            //                 { field: 'delivery_datetime', title: __('Delivery_datetime'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime },
            //                 { field: 'operate', title: __('Operate'), table: alreadyLiftCar, events: Table.api.events.operate, formatter: Table.api.formatter.operate }
            //             ]
            //         ]
            //     });
            //     // 为表格2绑定事件
            //     Table.api.bindevent(alreadyLiftCar);
            //
            //     alreadyLiftCar.on('load-success.bs.table', function (e, data) {
            //         $('#assigned-customer').text(data.total);
            //
            //     })
            //
            // }


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
            formatter: {
                operate: function (value, row, index) {

                    var table = this.table;
                    // 操作配置
                    var options = table ? table.bootstrapTable('getOptions') : {};
                    // 默认按钮组
                    var buttons = $.extend([], this.buttons || []);


                    return Table.api.buttonlink(this, buttons, value, row, index, 'operate');
                },
            }
        }

    };
    return Controller;
});