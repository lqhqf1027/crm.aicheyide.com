define(['jquery', 'bootstrap', 'backend', 'addtabs', 'table', 'echarts', 'echarts-theme', 'template'], function ($, undefined, Backend, Datatable, Table, Echarts, undefined, Template) {

    var Controller = {
        index: function () {

            //新车表
            // 基于准备好的dom，初始化echarts实例
            var newEchart = Echarts.init(document.getElementById('newechart'), 'walden');

            // 指定图表的配置项和数据
            var option = {
                title: {
                    text: '新车销售情况（单位：月）',
                    subtext: ''
                },
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    data: ["以租代购（新车）","租车","以租代购（二手车）","全款车"]
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
                    name: "销售一部",
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
                    data: Ordernewdata.newonesales
                },
                    {
                        name: "销售二部",
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
                        data: Ordernewdata.newtwosales
                    },
                    {
                        name: "销售三部",
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
                        data: Ordernewdata.newthreesales
                    }
                ]
            };

            // 使用刚指定的配置项和数据显示图表。
            newEchart.setOption(option);


            //租车表
            // 基于准备好的dom，初始化echarts实例
            var rentalEchart = Echarts.init(document.getElementById('rentalechart'), 'walden');

            // 指定图表的配置项和数据
            var option = {
                title: {
                    text: '租车出租情况（单位：月）',
                    subtext: ''
                },
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    data: ["销售一部","销售二部","销售三部"]
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
                    name: "销售一部",
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
                    data: Orderrentaldata.rentalonesales
                },
                    {
                        name: "销售二部",
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
                        data: Orderrentaldata.rentaltwosales
                    },
                    {
                        name: "销售三部",
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
                        data: Orderrentaldata.rentalthreesales
                    }
                ]
            };

            // 使用刚指定的配置项和数据显示图表。
            rentalEchart.setOption(option);


            //二手车表
            // 基于准备好的dom，初始化echarts实例
            var secondEchart = Echarts.init(document.getElementById('secondechart'), 'walden');

            // 指定图表的配置项和数据
            var option = {
                title: {
                    text: '二手车销售情况（单位：月）',
                    subtext: ''
                },
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    data: ["销售一部","销售二部","销售三部"]
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
                    name: "销售一部",
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
                    data: Orderseconddata.secondonesales
                },
                    {
                        name: "销售二部",
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
                        data: Orderseconddata.secondtwosales
                    },
                    {
                        name: "销售三部",
                        type: 'line',
                        smooth: true,
                        areaStyle: {
                            normal: {
                                color: 'orange'
                            }
                        },
                        lineStyle: {
                            normal: {
                                width: 1.5
                            }
                        },
                        data: Orderseconddata.secondthreesales
                    }
                ]
            };

            // 使用刚指定的配置项和数据显示图表。
            secondEchart.setOption(option);
            

            //全款车表
            // 基于准备好的dom，初始化echarts实例
            var fullEchart = Echarts.init(document.getElementById('fullechart'), 'walden');

            // 指定图表的配置项和数据
            var option = {
                title: {
                    text: '全款车销售情况（单位：月）',
                    subtext: ''
                },
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    data: ["销售一部","销售二部","销售三部"]
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
                    name: "销售一部",
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
                    data: Orderfulldata.fullonesales
                },
                    {
                        name: "销售二部",
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
                        data: Orderfulldata.fulltwosales
                    },
                    {
                        name: "销售三部",
                        type: 'line',
                        smooth: true,
                        areaStyle: {
                            normal: {
                                color: 'blue'
                            }
                        },
                        lineStyle: {
                            normal: {
                                width: 1.5
                            }
                        },
                        data: Orderfulldata.fullthreesales
                    }
                ]
            };

            // 使用刚指定的配置项和数据显示图表。
            fullEchart.setOption(option);

        }
    };

    return Controller;
});