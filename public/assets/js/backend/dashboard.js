define(['jquery', 'bootstrap', 'backend', 'addtabs', 'table', 'echarts', 'echarts-theme', 'template'], function ($, undefined, Backend, Datatable, Table, Echarts, undefined, Template) {

    var Controller = {
        index: function () {

            //新车表
            // 基于准备好的dom，初始化echarts实例
            var newEchart = Echarts.init(document.getElementById('newechart'), 'walden');

            // 指定图表的配置项和数据
            var option = {
                title: {
                    text: '销售情况（单位：月）'
                },
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    data: ["销售一部","销售二部","销售三部"]
                },
                xAxis: {
                    data: Orderdata.column
                },
                yAxis: {
                    splitLine: {
                        show: false
                    }
                },
                dataZoom: [{
                    startValue: Orderdata.column['10']
                }, {
                    type: 'inside'
                }],
                series: [
                    {
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
                        data: Orderdata.onesales
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
                        data: Orderdata.twosales
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
                        data: Orderdata.threesales
                    }
                ]
            };

            // 使用刚指定的配置项和数据显示图表。
            newEchart.setOption(option);
            
        }
    };

    return Controller;
});