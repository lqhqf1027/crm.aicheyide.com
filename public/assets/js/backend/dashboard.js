define(['jquery', 'bootstrap', 'backend', 'addtabs', 'table', 'echarts', 'echarts-theme', 'template'], function ($, undefined, Backend, Datatable, Table, Echarts, undefined, Template) {

    var Controller = {
        index: function () {

            //58同城
            // 基于准备好的dom，初始化echarts实例
            var cityEchart = Echarts.init(document.getElementById('city'), 'walden');

            var option = {
                title : {
                    text: '58同城客户',
                    subtext: '',
                    x:'center'
                },
                tooltip : {
                    trigger: 'item',
                    formatter: "{a} <br/>{b} : {c} ({d}%)"
                },
                legend: {
                    orient : 'vertical',
                    x : 'left',
                    data:['新客户','待联系','有意向','暂无意向','已放弃', '跟进时间过期客户']
                },
                toolbox: {
                    show : true,
                    feature : {
                        mark : {show: true},
                        dataView : {show: true, readOnly: false},
                        magicType : {
                            show: true, 
                            type: ['pie', 'funnel'],
                            option: {
                                funnel: {
                                    x: '25%',
                                    width: '50%',
                                    funnelAlign: 'left',
                                    max: Citydata.num
                                }
                            }
                        },
                        restore : {show: true},
                        saveAsImage : {show: true}
                    }
                },
                calculable : true,
                series : [
                    {
                        name:'访问来源',
                        type:'pie',
                        radius : '55%',
                        center: ['50%', '60%'],
                        data:[
                            {value:Citydata.newpeoplecity, name:'新客户'},
                            {value:Citydata.relationcity, name:'待联系'},
                            {value:Citydata.intentioncity, name:'有意向'},
                            {value:Citydata.nointentioncity, name:'暂无意向'},
                            {value:Citydata.giveupcity, name:'已放弃'},
                            {value:Citydata.overduecity, name:'跟进时间过期客户'}
                        ]
                    }
                ],
                color: ['rgb(87,210,211)','rgb(252,157,154)','rgb(24,188,156)','rgb(190,180,228)','rgb(254,67,101)','rgb(229,207,13)']
            };            

            // 使用刚指定的配置项和数据显示图表。
            // cityEchart.setOption(option);


            //今日头条
            // 基于准备好的dom，初始化echarts实例
            var todayEchart = Echarts.init(document.getElementById('today'), 'walden');

            var option = {
                title : {
                    text: '今日头条客户',
                    subtext: '',
                    x:'center'
                },
                tooltip : {
                    trigger: 'item',
                    formatter: "{a} <br/>{b} : {c} ({d}%)"
                },
                legend: {
                    orient : 'vertical',
                    x : 'left',
                    data:['新客户','待联系','有意向','暂无意向','已放弃', '跟进时间过期客户']
                },
                toolbox: {
                    show : true,
                    feature : {
                        mark : {show: true},
                        dataView : {show: true, readOnly: false},
                        magicType : {
                            show: true, 
                            type: ['pie', 'funnel'],
                            option: {
                                funnel: {
                                    x: '25%',
                                    width: '50%',
                                    funnelAlign: 'left',
                                    max: Todaydata.num
                                }
                            }
                        },
                        restore : {show: true},
                        saveAsImage : {show: true}
                    }
                },
                calculable : true,
                series : [
                    {
                        name:'访问来源',
                        type:'pie',
                        radius : '55%',
                        center: ['50%', '60%'],
                        data:[
                            {value:Todaydata.newpeopletoday, name:'新客户'},
                            {value:Todaydata.relationtoday, name:'待联系'},
                            {value:Todaydata.intentiontoday, name:'有意向'},
                            {value:Todaydata.nointentiontoday, name:'暂无意向'},
                            {value:Todaydata.giveuptoday, name:'已放弃'},
                            {value:Todaydata.overduetoday, name:'跟进时间过期客户'}
                        ],
                        
                    }
                ],
                color: ['rgb(87,210,211)','rgb(252,157,154)','rgb(24,188,156)','rgb(190,180,228)','rgb(254,67,101)','rgb(229,207,13)']
            };   

            // 使用刚指定的配置项和数据显示图表。
            // todayEchart.setOption(option);


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