
// 数据传输时间 - 今日传输时段

$(function () {
    var data1 = [];
    var data2 = [];
    
    function onDataReceived(series) {
        
        data1 = series["today"];
        data2 = series["today_average"];
        var plot = $.plotAnimator($("#data-within-a-day"),
        [	
            { 
                data: data1, 
                lines: {
                    show: true, 
                    fill: true,
                    fillColor: { colors: [ { opacity: 0.2 }, { opacity: 0.2 } ] },
                    lineWidth: 1.5
                },
                points: {
                    show: true,
                    radius: 2.5,
                    fill: true,
                    fillColor: "#ffffff",
                    symbol: "circle",
                    lineWidth: 1.1
                },
                label: "传输量"
            },
            { 
                data: data2, 
                animator: {
                    steps: 128, 
                    duration: 2000, 
                    start: 0
                },
                lines: {
                    show: true, 
                    fill: false,
                    lineWidth: 1.5
                },
                hoverable: false,
                label: "平均值"
            }
        ],
        {
            colors: ["#46bb00", "#32434D"],
            shadowSize: 0,
            grid: {
                borderWidth: 0,
                hoverable: true
            },
            xaxis: {
                mode: "time",
                minTickSize: [1, "hour"],
                min: 0,
                max: 24 * 60 * 60 * 1000
            },
            yaxis: {
                min: 0
            }
        });
        
        function showTooltip(x, y, contents, areAbsoluteXY) {
            var rootElt = 'body';
        
            $('<div id="tooltip" class="chart-tooltip">' + contents + '</div>').css( {
                top: y - 50,
                left: x - 9,
                opacity: 0.9
            }).prependTo(rootElt).show();
        };

        var previousPoint = null;
        $("#data-within-a-day").bind("plothover", function (event, pos, item) {
            $("#x").text(pos.x.toFixed(2));
            $("#y").text(pos.y.toFixed(2));

            if ($("#data-within-a-day").length > 0) {
                if (item) {
                    if (previousPoint != item.dataIndex) {
                        previousPoint = item.dataIndex;
                        
                        $("#tooltip").remove();
                        var msg = item.datapoint[1];
                        
                        showTooltip(item.pageX, item.pageY, msg);
                    }
                }
                else {
                    $("#tooltip").remove();
                    previousPoint = null;            
                }
            }
        });

        $("#data-within-a-day").bind("plotclick", function (event, pos, item) {
            if (item) {
                $("#clickdata").text("You clicked point " + item.dataIndex + " in " + item.series.label + ".");
                plot.highlight(item.series, item.datapoint);
            }
        });
    }
    $.ajax({
        url: 'db/json_monitor_charts.php',
        type: 'GET',
        data: 'type=todayhrs',
        dataType: "json",
        success: onDataReceived,
        error: function () {
            console.log("An error occurred.");
        }
    });
});

// 本周传输量 - 本周平均值

$(function () {
    var data1 = [];
    var data2 = [];
    var d = new Date();
    var millisecond = 24 * 60 * 60 * 1000; 
    
    function onDataReceived(series) {
        data1 = series["concrete"];
        data2 = series["average"];
        var plot = $.plotAnimator($("#data-within-a-week"),
        [	
            { 
                data: data1,
                lines: {
                    show: true, 
                    fill: true,
                    fillColor: { colors: [ { opacity: 0.2 }, { opacity: 0.2 } ] },
                    lineWidth: 2
                },
                points: {
                    show: true,
                    radius: 2.5,
                    fill: true,
                    fillColor: "#ffffff",
                    symbol: "circle",
                    lineWidth: 1.1
                },
                label: "传输量"
            }, 
			{ 
				data: data2, 
                animator: {
                    steps: 128, 
                    duration: 2000, 
                    start: 0
                }, 
				lines: { 
					show: true, 
					fill: false,
					lineWidth: 2
				}, 
                hoverable: false,
				label: "平均值"
			}
        ],
        {
            colors: ["#ee7951", "#3bb2d9"],
            shadowSize: 0,
            grid: {
                borderWidth: 0,
                hoverable: true
            },
            xaxis: {
                mode: "time",
                timezone: "browser",
                tickSize: [1, "day"],
                monthNames: ["1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12"],
                timeformat: "%b/%e",
                min: (new Date(d.toLocaleDateString())).getTime() - 6 * millisecond,
                max: (new Date(d.toLocaleDateString())).getTime()
            }
        });
        
        function showTooltip(x, y, contents, areAbsoluteXY) {
            var rootElt = 'body';
        
            $('<div id="tooltip" class="chart-tooltip">' + contents + '</div>').css( {
                top: y - 50,
                left: x - 9,
                opacity: 0.9
            }).prependTo(rootElt).show();
        };

        var previousPoint = null;
        $("#data-within-a-week").bind("plothover", function (event, pos, item) {
            $("#x").text(pos.x.toFixed(2));
            $("#y").text(pos.y.toFixed(2));

            if ($("#data-within-a-week").length > 0) {
                if (item) {
                    if (previousPoint != item.dataIndex) {
                        previousPoint = item.dataIndex;
                        
                        $("#tooltip").remove();
                        var msg = item.datapoint[1];
                        
                        showTooltip(item.pageX, item.pageY, msg);
                    }
                }
                else {
                    $("#tooltip").remove();
                    previousPoint = null;            
                }
            }
        });

        $("#data-within-a-week").bind("plotclick", function (event, pos, item) {
            if (item) {
                $("#clickdata").text("You clicked point " + item.dataIndex + " in " + item.series.label + ".");
                plot.highlight(item.series, item.datapoint);
            }
        });
    }
    $.ajax({
        url: 'db/json_monitor_charts.php',
        type: 'GET',
        data: 'type=lastweek',
        dataType: "json",
        success: onDataReceived,
        error: function () {
            console.log("An error occurred.");
        }
    });
});

// 本年传输月份 - 全年平均值

$(function () {
    var data1 = [];
    //var d = new Date();
    
    function onDataReceived(series) {

        data1 = series;
        var plot = $.plot($("#data-within-a-year"),
        [	
            { 
                data: data1,
                lines: {
                    show: true, 
                    fill: true,
                    fillColor: { colors: [ { opacity: 0.2 }, { opacity: 0.2 } ] },
                    lineWidth: 2
                },
                points: {
                    show: true,
                    radius: 2.5,
                    fill: true,
                    fillColor: "#ffffff",
                    symbol: "circle",
                    lineWidth: 1.1
                },
                label: "今年"
            }
        ],
        {
            colors: ["#1aacf0", "#993eb7"],
            shadowSize: 0,
            grid: {
                borderWidth: 0,
                hoverable: true
            },
            xaxis: {
                //mode: "time",
                //timezone: "browser",
                //tickSize: [1, "month"],
                //monthNames: ["1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12"],
                //min: (new Date(d.getFullYear(),0,1)).getTime(),                                     // 今年1月1日
                //max: (new Date(d.getFullYear(),11,1)).getTime()                                     // 今年12月1日
                tickSize: 1,
                min: 1,
                max: 12
            }
        });
        
        function showTooltip(x, y, contents, areAbsoluteXY) {
            var rootElt = 'body';
        
            $('<div id="tooltip" class="chart-tooltip">' + contents + '</div>').css( {
                top: y - 50,
                left: x - 9,
                opacity: 0.9
            }).prependTo(rootElt).show();
        };

        var previousPoint = null;
        $("#data-within-a-year").bind("plothover", function (event, pos, item) {
            $("#x").text(pos.x.toFixed(2));
            $("#y").text(pos.y.toFixed(2));

            if ($("#data-within-a-year").length > 0) {
                if (item) {
                    if (previousPoint != item.dataIndex) {
                        previousPoint = item.dataIndex;
                        
                        $("#tooltip").remove();
                        var msg = item.datapoint[1];
                        
                        showTooltip(item.pageX, item.pageY, msg);
                    }
                }
                else {
                    $("#tooltip").remove();
                    previousPoint = null;            
                }
            }
        });

        $("#data-within-a-year").bind("plotclick", function (event, pos, item) {
            if (item) {
                $("#clickdata").text("You clicked point " + item.dataIndex + " in " + item.series.label + ".");
                plot.highlight(item.series, item.datapoint);
            }
        });
    }
    $.ajax({
        url: 'db/json_monitor_charts.php',
        type: 'GET',
        data: 'type=thisyear',
        dataType: "json",
        success: onDataReceived,
        error: function () {
            console.log("An error occurred.");
        }
    });
});