define(['jquery', 'bootstrap', 'backend', 'addtabs', 'table', 'echarts', 'echarts-theme', 'template'], function ($, undefined, Backend, Datatable, Table, Echarts, undefined, Template) {

    var Controller = {
        index: function () {

            //新车表
            // 基于准备好的dom，初始化echarts实例
            var newEchart = Echarts.init(document.getElementById('newechart'), 'walden');

            // 指定图表的配置项和数据
            var option = {
                title: {
                    text: '新车销售情况',
                    subtext: ''
                },
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    data: ["提车数","订车数"]
                },
                toolbox: {
                    show: false,
                    feature: {
                        magicType: {show: true, type: ['stack', 'tiled']},
                        saveAsImage: {show: true}
                    }
                },
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: Ordernewdata.column
                },
                yAxis: {},
                grid: [{
                    left: 'left',
                    top: 'top',
                    right: '10',
                    bottom: 30
                }],
                series: [{
                    name: "提车数",
                    type: 'line',
                    smooth: true,
                    areaStyle: {
                        normal: {}
                    },
                    lineStyle: {
                        normal: {
                            width: 1.5
                        }
                    },
                    data: Ordernewdata.newtake
                },
                    {
                        name: "订车数",
                        type: 'line',
                        smooth: true,
                        areaStyle: {
                            normal: {}
                        },
                        lineStyle: {
                            normal: {
                                width: 1.5
                            }
                        },
                        data: Ordernewdata.neworder
                    }]
            };

            // 使用刚指定的配置项和数据显示图表。
            newEchart.setOption(option);

            //动态添加数据，可以通过Ajax获取数据然后填充
            setInterval(function () {
                Ordernewdata.column.push((new Date()).toLocaleTimeString().replace(/^\D*/, ''));
                var amount = Math.floor(Math.random() * 200) + 20;
                Ordernewdata.neworder.push(amount);
                Ordernewdata.newtake.push(Math.floor(Math.random() * amount) + 1);

                //按自己需求可以取消这个限制
                if (Ordernewdata.column.length >= 20) {
                    //移除最开始的一条数据
                    Ordernewdata.column.shift();
                    Ordernewdata.newtake.shift();
                    Ordernewdata.neworder.shift();
                }
                // newEchart.setOption({
                //     xAxis: {
                //         data: Orderdata.column
                //     },
                //     series: [{
                //         name: "成交数",
                //         data: Orderdata.paydata
                //     },
                //         {
                //             name: "签单流程中数",
                //             data: Orderdata.createdata
                //         }]
                // });
                if ($("#newechart").width() != $("#newechart canvas").width() && $("#newechart canvas").width() < $("#newechart").width()) {
                    newEchart.resize();
                }
            }, 2000);
            $(window).resize(function () {
                newEchart.resize();
            });

            $(document).on("click", ".btn-checkversion", function () {
                top.window.$("[data-toggle=checkupdate]").trigger("click");
            });



            //租车表
            // 基于准备好的dom，初始化echarts实例
            var rentalEchart = Echarts.init(document.getElementById('rentalechart'), 'walden');

            // 指定图表的配置项和数据
            var option = {
                title: {
                    text: '租车出租情况',
                    subtext: ''
                },
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    data: ["提车数","订车数"]
                },
                toolbox: {
                    show: false,
                    feature: {
                        magicType: {show: true, type: ['stack', 'tiled']},
                        saveAsImage: {show: true}
                    }
                },
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: Orderrentaldata.column
                },
                yAxis: {},
                grid: [{
                    left: 'left',
                    top: 'top',
                    right: '10',
                    bottom: 30
                }],
                series: [{
                    name: "提车数",
                    type: 'line',
                    smooth: true,
                    areaStyle: {
                        normal: {
                            color: 'yellow'
                        }
                    },
                    lineStyle: {
                        normal: {
                            width: 1.5
                        }
                    },
                    data: Orderrentaldata.rentaltake
                },
                    {
                        name: "订车数",
                        type: 'line',
                        smooth: true,
                        areaStyle: {
                            normal: {
                                color: 'pink'
                            }
                        },
                        lineStyle: {
                            normal: {
                                width: 1.5
                            }
                        },
                        data: Orderrentaldata.rentalorder
                    }]
            };

            // 使用刚指定的配置项和数据显示图表。
            rentalEchart.setOption(option);

            //动态添加数据，可以通过Ajax获取数据然后填充
            setInterval(function () {
                Orderrentaldata.column.push((new Date()).toLocaleTimeString().replace(/^\D*/, ''));
                var amount = Math.floor(Math.random() * 200) + 20;
                Orderrentaldata.rentalorder.push(amount);
                Orderrentaldata.rentaltake.push(Math.floor(Math.random() * amount) + 1);

                //按自己需求可以取消这个限制
                if (Orderrentaldata.column.length >= 20) {
                    //移除最开始的一条数据
                    Orderrentaldata.column.shift();
                    Orderrentaldata.rentalorder.shift();
                    Orderrentaldata.rentaltake.shift();
                }
                // rentalEchart.setOption({
                //     xAxis: {
                //         data: Orderdata.column
                //     },
                //     series: [{
                //         name: __('Sales'),
                //         data: Orderdata.paydata
                //     },
                //         {
                //             name: __('Orders'),
                //             data: Orderdata.createdata
                //         }]
                // });
                if ($("#rentalechart").width() != $("#rentalechart canvas").width() && $("#rentalechart canvas").width() < $("#rentalechart").width()) {
                    rentalEchart.resize();
                }
            }, 2000);
            $(window).resize(function () {
                rentalEchart.resize();
            });

            $(document).on("click", ".btn-checkversion", function () {
                top.window.$("[data-toggle=checkupdate]").trigger("click");
            });


            //二手车表
            // 基于准备好的dom，初始化echarts实例
            var secondEchart = Echarts.init(document.getElementById('secondechart'), 'walden');

            // 指定图表的配置项和数据
            var option = {
                title: {
                    text: '二手车销售情况',
                    subtext: ''
                },
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    data: ["提车数","订车数"]
                },
                toolbox: {
                    show: false,
                    feature: {
                        magicType: {show: true, type: ['stack', 'tiled']},
                        saveAsImage: {show: true}
                    }
                },
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: Orderseconddata.column
                },
                yAxis: {},
                grid: [{
                    left: 'left',
                    top: 'top',
                    right: '10',
                    bottom: 30
                }],
                series: [{
                    name: "提车数",
                    type: 'line',
                    smooth: true,
                    areaStyle: {
                        normal: {
                            color: 'cyan'
                        }
                    },
                    lineStyle: {
                        normal: {
                            width: 1.5
                        }
                    },
                    data: Orderseconddata.secondtake
                },
                    {
                        name: "订车数",
                        type: 'line',
                        smooth: true,
                        areaStyle: {
                            normal: {
                                color: 'lavender'
                            }
                        },
                        lineStyle: {
                            normal: {
                                width: 1.5
                            }
                        },
                        data: Orderseconddata.secondorder
                    }]
            };

            // 使用刚指定的配置项和数据显示图表。
            secondEchart.setOption(option);

            //动态添加数据，可以通过Ajax获取数据然后填充
            setInterval(function () {
                Orderseconddata.column.push((new Date()).toLocaleTimeString().replace(/^\D*/, ''));
                var amount = Math.floor(Math.random() * 200) + 20;
                Orderseconddata.secondorder.push(amount);
                Orderseconddata.secondtake.push(Math.floor(Math.random() * amount) + 1);

                //按自己需求可以取消这个限制
                if (Orderseconddata.column.length >= 20) {
                    //移除最开始的一条数据
                    Orderseconddata.column.shift();
                    Orderseconddata.secondtake.shift();
                    Orderseconddata.secondorder.shift();
                }
                // secondEchart.setOption({
                //     xAxis: {
                //         data: Orderdata.column
                //     },
                //     series: [{
                //         name: __('Sales'),
                //         data: Orderdata.paydata
                //     },
                //         {
                //             name: __('Orders'),
                //             data: Orderdata.createdata
                //         }]
                // });
                if ($("#secondechart").width() != $("#secondechart canvas").width() && $("#secondechart canvas").width() < $("#secondechart").width()) {
                    secondEchart.resize();
                }
            }, 2000);
            $(window).resize(function () {
                sedcondEchart.resize();
            });

            $(document).on("click", ".btn-checkversion", function () {
                top.window.$("[data-toggle=checkupdate]").trigger("click");
            });


            //全款车表
            // 基于准备好的dom，初始化echarts实例
            var fullEchart = Echarts.init(document.getElementById('fullechart'), 'walden');

            // 指定图表的配置项和数据
            var option = {
                title: {
                    text: '全款车销售情况',
                    subtext: ''
                },
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    data: ["提车数","订车数"]
                },
                toolbox: {
                    show: false,
                    feature: {
                        magicType: {show: true, type: ['stack', 'tiled']},
                        saveAsImage: {show: true}
                    }
                },
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: Orderfulldata.column
                },
                yAxis: {},
                grid: [{
                    left: 'left',
                    top: 'top',
                    right: '10',
                    bottom: 30
                }],
                series: [{
                    name: "提车数",
                    type: 'line',
                    smooth: true,
                    areaStyle: {
                        normal: {
                            color: 'lilac'
                        }
                    },
                    lineStyle: {
                        normal: {
                            width: 1.5
                        }
                    },
                    data: Orderfulldata.fulltake
                },
                    {
                        name: "订车数",
                        type: 'line',
                        smooth: true,
                        areaStyle: {
                            normal: {
                                color: 'red'
                            }
                        },
                        lineStyle: {
                            normal: {
                                width: 1.5
                            }
                        },
                        data: Orderfulldata.fullorder
                    }]
            };

            // 使用刚指定的配置项和数据显示图表。
            fullEchart.setOption(option);

            //动态添加数据，可以通过Ajax获取数据然后填充
            setInterval(function () {
                Orderfulldata.column.push((new Date()).toLocaleTimeString().replace(/^\D*/, ''));
                var amount = Math.floor(Math.random() * 200) + 20;
                Orderfulldata.fullorder.push(amount);
                Orderfulldata.fulltake.push(Math.floor(Math.random() * amount) + 1);

                //按自己需求可以取消这个限制
                if (Orderfulldata.column.length >= 20) {
                    //移除最开始的一条数据
                    Orderfulldata.column.shift();
                    Orderfulldata.fulltake.shift();
                    Orderfulldata.fullorder.shift();
                }
                // fullEchart.setOption({
                //     xAxis: {
                //         data: Orderdata.column
                //     },
                //     series: [{
                //         name: __('Sales'),
                //         data: Orderdata.paydata
                //     },
                //         {
                //             name: __('Orders'),
                //             data: Orderdata.createdata
                //         }]
                // });
                if ($("#fullechart").width() != $("#fullechart canvas").width() && $("#fullechart canvas").width() < $("#fullechart").width()) {
                    fullEchart.resize();
                }
            }, 2000);
            $(window).resize(function () {
                fullEchart.resize();
            });

            $(document).on("click", ".btn-checkversion", function () {
                top.window.$("[data-toggle=checkupdate]").trigger("click");
            });

        }
    };

    return Controller;
});