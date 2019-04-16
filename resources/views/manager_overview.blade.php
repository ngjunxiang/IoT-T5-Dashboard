@extends('layouts.app')

@section('custom-css')
<!-- Amcharts Style-->
<style>
    #chartdiv {
        width: 100%;
        height: 500px;
    }
</style>
@endsection

@section('custom-js')
<!-- Monthly Forecast Chart -->
<!--<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>-->
<script src="{{ URL::asset('js/chart.js') }}"></script>
<link rel="stylesheet" href="{{ URL::asset('css/bar.css') }}">
<link rel="stylesheet" href="{{ URL::asset('css/style.css') }}">
<script src="{{ URL::asset('js/graph.js') }}"></script>
<script type="text/javascript">
var ctx = document.getElementById('myChart').getContext('2d');
var config = {
    type: 'line',
    data: {
        labels: [],
        datasets: [
            {
                label: 'Actual', 
                borderColor: '#2196f3',
                fill: false,
                data: []
            },
            {
                label: 'Predicted', 
                borderColor: '#4CAF50',
                fill: false,
                data: []
            }
        ]
    },
    options: {
        responsive: true, // Instruct chart js to respond nicely.
        maintainAspectRatio: false, // Add to prevent default behaviour of full-width/height 
        scales: {
            yAxes: [{
                ticks: {
                beginAtZero: true,
                }
            }]
        }
    }
};
var myLineChart = new Chart(ctx, config);
/*
var myChart = new Chart(ctx, 
});*/



var currNumPpl = 0;
//google.charts.load('current', {'packages':['corechart']});
$(document).ready(function(){
    
    var WeekUrl = "{{ route('WeekUrl') }}";
    $.ajax ({
        url: WeekUrl,
        datatype: "json",
        success: function (e) {
            var data = e["images"];
            //plotGraph(data,"month");
            peakhours(data);
            plotDoughnutChart(data);
        }
    });
    var MonthUrl = "{{ route('MonthUrl') }}";
    $.ajax ({
        url: MonthUrl,
        datatype: "json",
        success: function (e) {
            var data = e["images"];
            MonthlyOccupancy(data);
        }

    });
    var WeekUrlAsc = "{{ route('WeekUrlAsc') }}";
    $.ajax ({
        url: WeekUrlAsc,
        datatype: "json",
        success: function (e) {
            var data = e["images"];
            plotGraph(data,"week");
            //peakhours(data);
        }
    });
    plotGauge();
});
function plotMonth(){
   
    var MonthUrlAsc = "{{ route('MonthUrlAsc') }}";
    $.ajax ({
        url: MonthUrlAsc,
        datatype: "json",
        success: function (e) {
            var data = e["images"];
            plotGraph(data,"month");
        }

    });
}
function plotWeek(){
    
    var WeekUrlAsc = "{{ route('WeekUrlAsc') }}";
    $.ajax ({
        url: WeekUrlAsc,
        datatype: "json",
        success: function (e) {
            
            var data = e["images"];
            plotGraph(data,"week");
            //peakhours(data);
        }
    });
}

function plotGraph(data,type){
    var totalOcc = 0; 
    var avgOcc= 0;
    var counter = 0 ;  
    var x_axis = [];
    var Rawdata=[];
    var result= [];
    for(var k in data) {
        var DataArray = data[k];
        for (var i in DataArray)
        {
            obj = DataArray[i]
            numPpl = obj["numPeopleDetected"];
            if(numPpl> 0){
                if( k ==0 )
                    totalOcc = numPpl;
                else
                    totalOcc += numPpl;
                counter+=1;
            }
        
        }
        avgOcc = Math.round(totalOcc/counter);
        string = k.split("-")[0]+","+avgOcc+","+Math.round(avgOcc/85*100);
        result.push(string);
        x_axis.push(k.split("-")[0]);
        Rawdata.push(avgOcc);
        
    }
    var predictedData = predict(Rawdata,result,type);
    var test = $("#echart_gauge");
    
    //figure out how to feed data in
    
}

function plotGauge(){
    var value = $("#percentage_Occ").text();
    var rawValue = value.split(' ')[1];
    var percentage = rawValue.substring(0,rawValue.length-1);
    var a = {
            color: ["#26B99A", "#34495E", "#BDC3C7", "#3498DB", "#9B59B6", "#8abb6f", "#759c6a", "#bfd3b7"],
            title: {
                itemGap: 8,
                textStyle: {
                    fontWeight: "normal",
                    color: "#408829"
                }
            },
            dataRange: {
                color: ["#1f610a", "#97b58d"]
            },
            toolbox: {
                color: ["#408829", "#408829", "#408829", "#408829"]
            },
            tooltip: {
                backgroundColor: "rgba(0,0,0,0.5)",
                axisPointer: {
                    type: "line",
                    lineStyle: {
                        color: "#408829",
                        type: "dashed"
                    },
                    crossStyle: {
                        color: "#408829"
                    },
                    shadowStyle: {
                        color: "rgba(200,200,200,0.3)"
                    }
                }
            },
            dataZoom: {
                dataBackgroundColor: "#eee",
                fillerColor: "rgba(64,136,41,0.2)",
                handleColor: "#408829"
            },
            grid: {
                borderWidth: 0
            },
            categoryAxis: {
                axisLine: {
                    lineStyle: {
                        color: "#408829"
                    }
                },
                splitLine: {
                    lineStyle: {
                        color: ["#eee"]
                    }
                }
            },
            valueAxis: {
                axisLine: {
                    lineStyle: {
                        color: "#408829"
                    }
                },
                splitArea: {
                    show: !0,
                    areaStyle: {
                        color: ["rgba(250,250,250,0.1)", "rgba(200,200,200,0.1)"]
                    }
                },
                splitLine: {
                    lineStyle: {
                        color: ["#eee"]
                    }
                }
            },
            timeline: {
                lineStyle: {
                    color: "#408829"
                },
                controlStyle: {
                    normal: {
                        color: "#408829"
                    },
                    emphasis: {
                        color: "#408829"
                    }
                }
            },
            k: {
                itemStyle: {
                    normal: {
                        color: "#68a54a",
                        color0: "#a9cba2",
                        lineStyle: {
                            width: 1,
                            color: "#408829",
                            color0: "#86b379"
                        }
                    }
                }
            },
            map: {
                itemStyle: {
                    normal: {
                        areaStyle: {
                            color: "#ddd"
                        },
                        label: {
                            textStyle: {
                                color: "#c12e34"
                            }
                        }
                    },
                    emphasis: {
                        areaStyle: {
                            color: "#99d2dd"
                        },
                        label: {
                            textStyle: {
                                color: "#c12e34"
                            }
                        }
                    }
                }
            },
            force: {
                itemStyle: {
                    normal: {
                        linkStyle: {
                            strokeColor: "#408829"
                        }
                    }
                }
            },
            chord: {
                padding: 4,
                itemStyle: {
                    normal: {
                        lineStyle: {
                            width: 1,
                            color: "rgba(128, 128, 128, 0.5)"
                        },
                        chordStyle: {
                            lineStyle: {
                                width: 1,
                                color: "rgba(128, 128, 128, 0.5)"
                            }
                        }
                    },
                    emphasis: {
                        lineStyle: {
                            width: 1,
                            color: "rgba(128, 128, 128, 0.5)"
                        },
                        chordStyle: {
                            lineStyle: {
                                width: 1,
                                color: "rgba(128, 128, 128, 0.5)"
                            }
                        }
                    }
                }
            },
            gauge: {
                startAngle: 225,
                endAngle: -45,
                axisLine: {
                    show: !0,
                    lineStyle: {
                        color: [
                            [.2, "#86b379"],
                            [.8, "#68a54a"],
                            [1, "#408829"]
                        ],
                        width: 8
                    }
                },
                axisTick: {
                    splitNumber: 10,
                    length: 12,
                    lineStyle: {
                        color: "auto"
                    }
                },
                axisLabel: {
                    textStyle: {
                        color: "auto"
                    }
                },
                splitLine: {
                    length: 18,
                    lineStyle: {
                        color: "auto"
                    }
                },
                pointer: {
                    length: "90%",
                    color: "auto"
                },
                title: {
                    textStyle: {
                        color: "#333"
                    }
                },
                detail: {
                    textStyle: {
                        color: "auto"
                    }
                }
            },
            textStyle: {
                fontFamily: "Arial, Verdana, sans-serif"
            }
        };
    var e = echarts.init(document.getElementById("echart_gauge"), a);
    e.setOption({
        tooltip: {
            formatter: "{a} <br/>{b} : {c}%"
        },
        toolbox: {
            show: !0,
            feature: {
                restore: {
                    show: !0,
                    title: "Restore"
                },
                saveAsImage: {
                    show: !0,
                    title: "Save Image"
                }
            }
        },
        series: [{
            name: "Performance",
            type: "gauge",
            center: ["50%", "50%"],
            startAngle: 140,
            endAngle: -140,
            min: 0,
            max: 100,
            precision: 0,
            splitNumber: 10,
            axisLine: {
                show: !0,
                lineStyle: {
                    color: [
                        [.2, "lightgreen"],
                        [.4, "lightgreen"],
                        [.8, "skyblue"],
                        [1, "#ff4500"]
                    ],
                    width: 30
                }
            },
            axisTick: {
                show: !0,
                splitNumber: 5,
                length: 8,
                lineStyle: {
                    color: "#eee",
                    width: 1,
                    type: "solid"
                }
            },
            axisLabel: {
                show: !0,
                formatter: function(a) {
                    switch (a + "") {
                        case "10":
                            return "Low";
                        case "60":
                            return "Medium";
                        case "90":
                            return "High";
                        default:
                            return ""
                    }
                },
                textStyle: {
                    color: "#333"
                }
            },
            splitLine: {
                show: !0,
                length: 30,
                lineStyle: {
                    color: "#eee",
                    width: 2,
                    type: "solid"
                }
            },
            pointer: {
                length: "80%",
                width: 8,
                color: "auto"
            },
            title: {
                show: !0,
                offsetCenter: ["-65%", -10],
                textStyle: {
                    color: "#333",
                    fontSize: 15
                }
            },
            detail: {
                show: !0,
                backgroundColor: "rgba(0,0,0,0)",
                borderWidth: 0,
                borderColor: "#ccc",
                width: 100,
                height: 40,
                offsetCenter: ["-60%", 10],
                formatter: "{value}%",
                textStyle: {
                    color: "auto",
                    fontSize: 30
                }
            },
            data: [{
                value: percentage,
                name: "Occupancy"
            }]
        }]
    })
    
}
function predict(Rawdata,result,type){
    $.ajax ({
        type: "POST",
        url: "http://159.89.204.164:8081/predict",
        datatype: "application/json",
        contentType:"application/json",
        data: JSON.stringify({ 
            'data': Rawdata
            }),
        success: function (e) {
            myLineChart.data.datasets[0] = {
                    label: 'Actual', 
                    borderColor: '#2196f3',
                    fill: false,
                    data: []
                };
            myLineChart.data.datasets[1] = {
                    label: 'Predicted', 
                    borderColor: '#4CAF50',
                    fill: false,
                    data: []
                };
            myLineChart.data.labels =[];
            var json = JSON.parse(e)
            var jsonResult = json["data"];
            if(type =="week"){
                for(var i in result)
                {
                    array = result[i].split(",");
                    myLineChart.data.labels.push("Week "+array[0]);
                    myLineChart.data.datasets[0].data.push(Rawdata[i]);
                    myLineChart.data.datasets[1].data.push(Rawdata[i]);

                }
                myLineChart.data.labels.push("Week "+(parseInt(array[0])+1));
                myLineChart.data.datasets[1].data.push(jsonResult);
            }else{
                for(var i in result)
                { 
                    
                    array = result[i].split(",");
                    myLineChart.data.labels.push(array[0]);
                    myLineChart.data.datasets[0].data.push(Rawdata[i]);
                    myLineChart.data.datasets[1].data.push(Rawdata[i]);

                }
                var month = new Array();
                month[0] = "Jan";
                month[1] = "Feb";
                month[2] = "Mar";
                month[3] = "Apr";
                month[4] = "May";
                month[5] = "Jun";
                month[6] = "Jul";
                month[7] = "Aug";
                month[8] = "Sep";
                month[9] = "Oct";
                month[10] = "Nov";
                month[11] = "Dec";
                nextMonth = month[Date.today().next().month().getMonth()];
                myLineChart.data.labels.push(nextMonth);
                myLineChart.data.datasets[1].data.push(jsonResult);
            }

            myLineChart.update();
        }
    });
}

function peakhours(data){
    var result =[]; 
    var rawData ="";
    var counter = 0 ;
    var mon = []; 
    var tue = []; 
    var wed = [];
    var thu = []; 
    var fri = []; 
    var sat = []; 
    var sun = [];
    var count = []; 
    var dates = [0,0,0,0,0,0,0]; 
    for(var k in data) {
        if (counter == 1)
        {
            rawData = data[k];
            break;
        }
        counter ++; 
    };
   for(var i in rawData){
       var dt = new Date(rawData[i]["created_at"]);
       var day = dt.getDay();
       var hour = dt.getHours(); 
       var date = dt.getDate()+"/"+dt.getMonth()+"/"+parseInt(dt.getYear()+1900);
       var month = dt.toLocaleString('en-us', { month: 'long' });
       var formattedDate = dt.getDate() + " " + month + " " + parseInt(dt.getYear()+1900);
       var numPpl = rawData[i]["numPeopleDetected"];
       dates[day-1] = formattedDate;
       if(day == 1)
       {
           if(mon[hour]== null ){
                mon[hour] = numPpl;
           }
           else {
                var value = mon[hour];
                mon[hour] = value + numPpl;  
           }
           
             
       }
       else if (day == 2 )
       {
            if(tue[hour]== null ){
                tue[hour] = numPpl;
            }
            else {
                var value = tue[hour];
                tue[hour] = value + numPpl;  
            }
       }
       else if (day == 3 )
       {
            if(wed[hour]== null ){
                wed[hour] = numPpl;
            }
            else {
                var value = wed[hour];
                wed[hour] = value + numPpl;  
            }
       }
       else if (day == 4 )
       {
            if(thu[hour]== null ){
                thu[hour] = numPpl;
            }
            else {
                var value = thu[hour];
                thu[hour] = value + numPpl;  
            }
       }
       else if (day == 5 )
       {
            if(fri[hour]== null ){
                fri[hour] = numPpl;
            }
            else {
                var value = fri[hour];
                fri[hour] = value + numPpl;  
            }
       }
       else if (day == 6 )
       {
            if(sat[hour]== null ){
                sat[hour] = numPpl;
            }
            else {
                var value = sat[hour];
                sat[hour] = value + numPpl;  
            }
       }
       else 
       {
            if(sun[hour]== null ){
                sun[hour] = numPpl;
            }
            else {
                var value = sun[hour];
                sun[hour] = value + numPpl;  
            }
       }
         if(count[day]== null)
            count[day] = 0; 
        if(numPpl >0 )
            count[day]++; 

   }
   addToList(mon,"Monday",count,1,result,dates[1]);
   addToList(tue,"Tuesday",count,2,result,dates[2]);
   addToList(wed,"Wednesday",count,3,result,dates[3]);
   addToList(thu,"Thursday",count,4,result,dates[4]);
   addToList(fri,"Friday",count,5,result,dates[5]);
   addToList(sat,"Saturday",count,6,result,dates[6]);
   addToList(sun,"Sunday",count,0,result,dates[0]);
   var output="";
   for(var i in result)
   {
       var arry = result[i].split(",");
       output += "<tr>"
       output += "  <th scope=\"row\">"+(parseInt(i)+1)+"</th>"
       output += "      <td>"+arry[0]+"</td>"
       output += "      <td>"+arry[1]+" </td>"
       output += "      <td class='text-center'>"+arry[2]+"</td>"
       output += "      <td class='text-center'>"+arry[3]+"</td>"
       output += "      </tr>"

   } 
   $("#peakhours").empty(); 
   $("#peakhours").append(output); 
}
function addToList (array, day,count,index,result,date)
{
    for(var i in array)
    {
        avg = getAvg(array[i],count[index]);
        occupancyRate = checker(avg)
        // change to 10 when is done.
        if(occupancyRate>10)
        {
            nextHr = parseInt(i) + 1; 
            string = day+","+date+ " "+ tConvert(i+":00")+" - "+tConvert(nextHr+":00")+","+avg+","+occupancyRate;
            result.push(string);
        }
                
    }
}
function checker(avg)
{
    return Math.round(avg/85* 100) ; 
}
function getAvg (numPpl,count)
{
    return Math.round(numPpl/12);
}

function MonthlyOccupancy(data)
{
    var totalOcc = 0; 
    var avgOcc= 0;
    var counter = 0 ;  
    var month = [];
    for(var k in data) {
        //month.push(k);
        var DataArray = data[k];
        for (var i in DataArray)
        {
            obj = DataArray[i]
            numPpl = obj["numPeopleDetected"];
            if(numPpl> 0){
                if( k ==0 )
                    totalOcc = numPpl;
                totalOcc += numPpl;
                counter+=1;
            }
        
        }
        avgOcc = Math.round(totalOcc/counter);
        string = k.split("-")[0]+","+avgOcc+","+Math.round(avgOcc/85*100);
        month.push(string);
        counter = 0 ; 
        totalOcc= 0;

    }
    var output = "";
    for(var i in month){
        array = month[i].split(",");
        output+="<tr>";
        output+="   <th scope=\"row\">"+(parseInt(i)+1)+"</th>";
        output+="   <td>"+array[0]+"</td>";
        output+="   <td class='text-center'>"+array[1]+"</td>";
        output+="   <td class='text-center'>"+array[2]+"</td>";
        output+="</tr>";
    }
    $("#monthlyOcc").empty();
    $("#monthlyOcc").append(output);
    //
}


// Running clock 
var myClock = document.getElementById("clock");
function renderTime () {
    var d = new Date();
    utc = d.getTime() + (d.getTimezoneOffset() * 60000);
    currentTime = new Date(utc + (3600000*8));
    var h = currentTime.getHours();
    var m = currentTime.getMinutes();
    var s = currentTime.getSeconds();

    if (h < 10) {
        h = "0" + h;
    }


    if (m < 10) {
        m = "0" + m;
    }


    if (s < 10) {
        s = "0" + s;
    }

    //myClock.textContent = h + ":" + m + ":" + s;
    myClock.innerText = tConvert(h + ":" + m + ":" + s, true);

    setTimeout(renderTime, 1000);
}
renderTime();

function tConvert (time, seconds = false) {
  // Check correct time format and split into components
  time = time.toString ().match (/^([01]\d|2[0-3])(:)([0-5]\d)(:[0-5]\d)?$/) || [time];

  if (time.length > 1) { // If time format correct
    time = time.slice (1);  // Remove full string match value
    if (seconds) {
        time[5] = +time[0] < 12 ? ' AM' : ' PM'; // Set AM/PM
    } else {
        time[5] = +time[0] < 12 ? ':00 AM' : ':00 PM'; // Set AM/PM
    }
    time[0] = +time[0] % 12 || 12; // Adjust hours
  }
  return time.join (''); // return adjusted time or original string
}


// draw doughnut  chart
function plotDoughnutChart(data){
    var week = new Array(7);
    var rawData ="";
    var counter = 0 ;
    var total = 0 ;
    for(var k in data) {
        if (counter == 1)
        {
            rawData = data[k];
            break;
        }
        counter ++; 
        
    }
    for (var i in rawData)
    {
        
        var dt = new Date(rawData[i]["created_at"]);
        var day = dt.getDay();
        var numPpl = rawData[i]["numPeopleDetected"];
        if(day != 0 ){
            if(week[day] == null)
                week[day] = 0;
            var value = week[day];
            week[day] = parseInt(value)+parseInt(numPpl);
            total += numPpl;
        }

    }
    $("#doughnutChart").drawDoughnutChart(
    [
      { title: "Monday", value:week[1]/total , color: "#3498DB" },
      { title: "Tuesday", value: week[2]/total, color: "#1ABB9C" },
      { title: "Wednesday", value: week[3]/total, color: "#9B59B6" },
      { title: "Thursday", value: week[4]/total, color: "#9CC2CB" },
      { title: "Friday", value: week[5]/total, color: "#E74C3C" },
      { title: "Saturday", value: week[6]/total, color: "#F2E68F" }
    ],
    {},
    myFunc
    );
    $("#mon").text(Math.round(week[1]/total*100));
    $("#tue").text(Math.round(week[2]/total*100));
    $("#wed").text(Math.round(week[3]/total*100));
    $("#thu").text(Math.round(week[4]/total*100));
    $("#fri").text(Math.round(week[5]/total*100));
    $("#sat").text(Math.round(week[6]/total*100));
}


function myFunc(data) {
  alert(data.title);
}
/*!
 * jquery.drawDoughnutChart.js
 * Version: 0.4.1(Beta)
 * Inspired by Chart.js(http://www.chartjs.org/)
 *
 * Copyright 2014 hiro
 * https://github.com/githiro/drawDoughnutChart
 * Released under the MIT license.
 * 
 */
(function($, undefined) {
  $.fn.drawDoughnutChart = function(data, options, clickCallBack) {
    var $this = this,
      W = $this.width(),
      H = $this.height(),
      centerX = W / 2,
      centerY = H / 2,
      cos = Math.cos,
      sin = Math.sin,
      PI = Math.PI,
      settings = $.extend(
        {
          segmentShowStroke: true,
          segmentStrokeColor: "#0C1013",
          segmentStrokeWidth: 1,
          baseColor: "rgba(0,0,0,0.5)",
          baseOffset: 4,
          edgeOffset: 10, //offset from edge of $this
          percentageInnerCutout: 40,
          animation: true,
          animationSteps: 90,
          animationEasing: "easeInOutExpo",
          animateRotate: true,
          tipOffsetX: -8,
          tipOffsetY: -45,
          tipClass: "doughnutTip",

          beforeDraw: function() {},
          afterDrawed: function() {},
          onPathEnter: function(e, data) {},
          onPathLeave: function(e, data) {}
        },
        options
      ),
      animationOptions = {
        linear: function(t) {
          return t;
        },
        easeInOutExpo: function(t) {
          var v = t < 0.5 ? 8 * t * t * t * t : 1 - 8 * --t * t * t * t;
          return v > 1 ? 1 : v;
        }
      },
      requestAnimFrame = (function() {
        return (
          window.requestAnimationFrame ||
          window.webkitRequestAnimationFrame ||
          window.mozRequestAnimationFrame ||
          window.oRequestAnimationFrame ||
          window.msRequestAnimationFrame ||
          function(callback) {
            window.setTimeout(callback, 1000 / 60);
          }
        );
      })();

    settings.beforeDraw.call($this);

    var $svg = $(
        '<svg width="' +
          W +
          '" height="' +
          H +
          '" viewBox="0 0 ' +
          W +
          " " +
          H +
          '" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"></svg>'
      ).appendTo($this),
      $paths = [],
      easingFunction = animationOptions[settings.animationEasing],
      doughnutRadius = Min([H / 2, W / 2]) - settings.edgeOffset,
      cutoutRadius = doughnutRadius * (settings.percentageInnerCutout / 100),
      segmentTotal = 0;

    //Draw base doughnut
    var baseDoughnutRadius = doughnutRadius + settings.baseOffset,
      baseCutoutRadius = cutoutRadius - settings.baseOffset;
    $(document.createElementNS("http://www.w3.org/2000/svg", "path"))
      .attr({
        d: getHollowCirclePath(baseDoughnutRadius, baseCutoutRadius),
        fill: settings.baseColor
      })
      .appendTo($svg);

    //Set up pie segments wrapper
    var $pathGroup = $(
      document.createElementNS("http://www.w3.org/2000/svg", "g")
    );
    $pathGroup.attr({ opacity: 0 }).appendTo($svg);

    //Set up tooltip
    var $tip = $('<div class="' + settings.tipClass + '" />')
        .appendTo("body")
        .hide(),
      tipW = $tip.width(),
      tipH = $tip.height();

    //Set up center text area
    var summarySize = (cutoutRadius - (doughnutRadius - cutoutRadius)) * 2,
      $summary = $('<div class="' + settings.summaryClass + '" />')
        .appendTo($this)
        .css({
          width: summarySize + "px",
          height: summarySize + "px",
          "margin-left": -(summarySize / 2) + "px",
          "margin-top": -(summarySize / 2) + "px"
        });

    var $summaryNumber = $()


    var total = calcTotal();

    for (var i = 0, len = data.length; i < len; i++) {
      segmentTotal += data[i].value;
      $paths[i] = $(
        document.createElementNS("http://www.w3.org/2000/svg", "path")
      )
        .attr({
          "stroke-width": settings.segmentStrokeWidth,
          stroke: settings.segmentStrokeColor,
          fill: data[i].color,
          "data-order": i
        })
        .appendTo($pathGroup)
        .on("mouseenter", pathMouseEnter)
        .on("mouseleave", pathMouseLeave)
        .on("mousemove", pathMouseMove)
        .on("click", pathClick);
      //.append(getLabel(i));
    }

    //Animation start
    animationLoop(drawPieSegments);

    //Functions
    function getHollowCirclePath(doughnutRadius, cutoutRadius) {
      //Calculate values for the path.
      //We needn't calculate startRadius, segmentAngle and endRadius, because base doughnut doesn't animate.
      var startRadius = -1.57, // -Math.PI/2
        segmentAngle = 6.2831, // 1 * ((99.9999/100) * (PI*2)),
        endRadius = 4.7131, // startRadius + segmentAngle
        startX = centerX + cos(startRadius) * doughnutRadius,
        startY = centerY + sin(startRadius) * doughnutRadius,
        endX2 = centerX + cos(startRadius) * cutoutRadius,
        endY2 = centerY + sin(startRadius) * cutoutRadius,
        endX = centerX + cos(endRadius) * doughnutRadius,
        endY = centerY + sin(endRadius) * doughnutRadius,
        startX2 = centerX + cos(endRadius) * cutoutRadius,
        startY2 = centerY + sin(endRadius) * cutoutRadius;
      var cmd = [
        "M",
        startX,
        startY,
        "A",
        doughnutRadius,
        doughnutRadius,
        0,
        1,
        1,
        endX,
        endY, //Draw outer circle
        "Z", //Close path
        "M",
        startX2,
        startY2, //Move pointer
        "A",
        cutoutRadius,
        cutoutRadius,
        0,
        1,
        0,
        endX2,
        endY2, //Draw inner circle
        "Z"
      ];
      cmd = cmd.join(" ");
      return cmd;
    }
    function pathMouseEnter(e) {
      var order = $(this).data().order;
      $tip.text(data[order].title + ": " + data[order].value).fadeIn(200);
      settings.onPathEnter.apply($(this), [e, data]);
    }
    function pathMouseLeave(e) {
      $tip.hide();
      settings.onPathLeave.apply($(this), [e, data]);
    }
    function pathMouseMove(e) {
      $tip.css({
        top: e.pageY + settings.tipOffsetY,
        left: e.pageX - $tip.width() / 2 + settings.tipOffsetX
      });
    }
    function pathClick(e) {
      var order = $(this).data().order;
      clickCallBack(data[order]);
    }
    function drawPieSegments(animationDecimal) {
      var startRadius = -PI / 2, //-90 degree
        rotateAnimation = 1;
      if (settings.animation && settings.animateRotate)
        rotateAnimation = animationDecimal; //count up between0~1

      drawDoughnutText(animationDecimal, segmentTotal);

      $pathGroup.attr("opacity", animationDecimal);

      //If data have only one value, we draw hollow circle(#1).
      if (
        data.length === 1 &&
        4.7122 <
          rotateAnimation * (data[0].value / segmentTotal * (PI * 2)) +
            startRadius
      ) {
        $paths[0].attr("d", getHollowCirclePath(doughnutRadius, cutoutRadius));
        return;
      }
      for (var i = 0, len = data.length; i < len; i++) {
        var segmentAngle =
            rotateAnimation * (data[i].value / segmentTotal * (PI * 2)),
          endRadius = startRadius + segmentAngle,
          largeArc = (endRadius - startRadius) % (PI * 2) > PI ? 1 : 0,
          startX = centerX + cos(startRadius) * doughnutRadius,
          startY = centerY + sin(startRadius) * doughnutRadius,
          endX2 = centerX + cos(startRadius) * cutoutRadius,
          endY2 = centerY + sin(startRadius) * cutoutRadius,
          endX = centerX + cos(endRadius) * doughnutRadius,
          endY = centerY + sin(endRadius) * doughnutRadius,
          startX2 = centerX + cos(endRadius) * cutoutRadius,
          startY2 = centerY + sin(endRadius) * cutoutRadius;
        var cmd = [
          "M",
          startX,
          startY, //Move pointer
          "A",
          doughnutRadius,
          doughnutRadius,
          0,
          largeArc,
          1,
          endX,
          endY, //Draw outer arc path
          "L",
          startX2,
          startY2, //Draw line path(this line connects outer and innner arc paths)
          "A",
          cutoutRadius,
          cutoutRadius,
          0,
          largeArc,
          0,
          endX2,
          endY2, //Draw inner arc path
          "Z" //Cloth path
        ];
        $paths[i].attr("d", cmd.join(" "));
        startRadius += segmentAngle;
      }
    }
    function drawDoughnutText(animationDecimal, segmentTotal) {

    }
    function animateFrame(cnt, drawData) {
      var easeAdjustedAnimationPercent = settings.animation
        ? CapValue(easingFunction(cnt), null, 0)
        : 1;
      drawData(easeAdjustedAnimationPercent);
    }
    function animationLoop(drawData) {
      var animFrameAmount = settings.animation
          ? 1 / CapValue(settings.animationSteps, Number.MAX_VALUE, 1)
          : 1,
        cnt = settings.animation ? 0 : 1;
      requestAnimFrame(function() {
        cnt += animFrameAmount;
        animateFrame(cnt, drawData);
        if (cnt <= 1) {
          requestAnimFrame(arguments.callee);
        } else {
          settings.afterDrawed.call($this);
        }
      });
    }
    function Max(arr) {
      return Math.max.apply(null, arr);
    }
    function Min(arr) {
      return Math.min.apply(null, arr);
    }
    function isNumber(n) {
      return !isNaN(parseFloat(n)) && isFinite(n);
    }
    function CapValue(valueToCap, maxValue, minValue) {
      if (isNumber(maxValue) && valueToCap > maxValue) return maxValue;
      if (isNumber(minValue) && valueToCap < minValue) return minValue;
      return valueToCap;
    }
    function getLabel(i) {
      var per = parseInt(data[i].value / total * 100);
      var lblTxt = $('<div class="slicetext">' + per + "%</div>");
      return lblTxt;
    }
    function calcTotal() {
      var result = 0;
      for (var i in data) {
        result += parseInt(data[i].value);
      }
      return result;
    }
    return $this;
  };
})(jQuery);




</script>
@endsection

@section('content')
<div class="page-title">
    <h3>Scape HubQuaters Dashboard </h3>
</div>
@if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
@endif
<div class="clearfix"></div>

<!----------- First Tier  --------------------->
<div class="row top_tiles">
    <div class="animated flipInY col-lg-4 col-md-3 col-sm-6 col-xs-12">
        <div class="tile-stats">
            <div class="icon"><i class="fa fa-clock-o"></i></div>
            <div class="count"id="clock">{{ date('H:i:s') }}</div>
            <h3>{{ date('d F Y') }}</h3>
            <p>Present Date-Time</p>
        </div>
    </div>
    <div class="animated flipInY col-lg-4 col-md-3 col-sm-6 col-xs-12">
        <div class="tile-stats">
            <div class="icon"><i class="fa fa-male"></i></div>
            <div class="count">{{ $latest['numPeopleDetected'] }}</div>
            <h3 id="percentage_Occ">Occupancy: {{ number_format($latest['numPeopleDetected']/env('MAX_OCCUPANCY')*100) }}%</h3>
            <p>Real-Time # Tenants</p>
        </div>
    </div>

    <div class="animated flipInY col-lg-4 col-md-3 col-sm-6 col-xs-12">
        <div class="tile-stats">
            <div class="icon"><i class="fa fa-users"></i></div>
            <div class="count">80</div>
            <h3>Max Sign-Up: {{env('MAX_OCCUPANCY')}}</h3>
            <p>Present Tenant Sign-Ups</p>
        </div>
    </div>
</div>
<div class="clearfix"></div>
<!----------- /First Tier End--------------------->

<!----------- Second Tier  --------------------->
<div class="row">
    <div class="col-md-4 col-sm-4 col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>Real-Time Occupancy Rate</h2>
                <ul class="nav navbar-right panel_toolbox">
                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </li>
                </ul>
                <div class="clearfix"></div>
                <h5>An indication of the present occupancy rate at the hub.</h5>
            </div>
            <div class="x_content">
                <!----- In custom.js || #echart_gauge---->
                <div id="echart_gauge" style="height:230px;"></div>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-sm-6 col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>Avg Monthly Occupancy</h2>

                <ul class="nav navbar-right panel_toolbox">
                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </li>
                </ul>
                <div class="clearfix"></div>

                <h5>To identify the peak and non-peak months to strategize customized plans.</h5>
            </div>
            <div class="x_content">

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="5%">Months</th>
                            <th width="35%" class='text-center'>Avg Tenants</th>
                            <th width="60%" class='text-center'>Avg Occupancy (%)</th>
                        </tr>
                    </thead>
                    <tbody id="monthlyOcc">
                    </tbody>
                </table>

            </div>
        </div>
    </div>

   


    <div class="col-md-4 col-sm-4 col-xs-12">
        <div class="x_panel tile overflow_hidden">
            <div class="x_title">
                <h2>Weekly Usage Breakdown</h2>
                <ul class="nav navbar-right panel_toolbox">
                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </li>
                </ul>
                <div class="clearfix"></div>
                <h5>To identity the difference in the proportion of occupancy rate throughout the week.
                </h5>
            </div>
            <div class="x_content">
                <table class="" style="width:100%">
                    <tr>
                        <th style="width:37%;">
                            <p>Weekly</p>
                        </th>
                        <th>
                            <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7">
                                <p class="">Days</p>
                            </div>
                            <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
                                <p class="">Percentage</p>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <td>
                            <div id="doughnutChart" class="chart"></div>
                        </td>
                        <td>
                            <table class="tile_info">
                                <tr>
                                    <td>
                                        <p><i class="fa fa-square blue"></i>Monday</p>
                                    </td>
                                    <td><span id="mon"></span>%</td>
                                </tr>
                                <tr>
                                    <td>
                                        <p><i class="fa fa-square green"></i>Tuesday</p>
                                    </td>
                                    <td><span id="tue"></span>%</td>
                                </tr>
                                <tr>
                                    <td>
                                        <p><i class="fa fa-square purple"></i>Wednesday</p>
                                    </td>
                                    <td><span id="wed"></span>%</td>
                                </tr>
                                <tr>
                                    <td>
                                        <p><i class="fa fa-square aero"></i>Thursday</p>
                                    </td>
                                    <td><span id="thu"></span>%</td>
                                </tr>
                                <tr>
                                    <td>
                                        <p><i class="fa fa-square red"></i>Friday</p>
                                    </td>
                                    <td><span id="fri"></span>%</td>
                                </tr>
                                <tr>
                                    <td>
                                        <p><i class="fa fa-square" style="color:#F2E68F"></i>Saturday</p>
                                    </td>
                                    <td><span id="sat"></span>%</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

</div>
<!----------- /Second Tier End  --------------------->

<!----------------- Third Tier (Tables)--------------------->
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>Peak Hours ( >= 90% Occupancy Rate) </h2>

                <ul class="nav navbar-right panel_toolbox">
                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </li>
                </ul>
                <div class="clearfix"></div>

                <h5>To identify the peak hours in the previous week nearing maximum occupancy rate that call for
                    further action. </h5>
            </div>
            <div class="x_content">

                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Day</th>
                            <th>Date Time (Hourly)</th>
                            <th class='text-center'># Tenants</th>
                            <th class='text-center'>Occupancy (%)</th>
                        </tr>
                    </thead>
                    <tbody id="peakhours">
                    </tbody>
                </table>

            </div>
        </div>
    </div>

    <div class="col-md-12 col-sm-12 col-xs-12 ">
        <div class="x_panel">
            <div class="x_title">
                <h2>Hourly Occupancy Rate Prediction</h2>
                <ul class="nav navbar-right panel_toolbox">
                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </li>
                </ul>
                <div class="clearfix"></div>
                <h5>To identify the need for action for days reaching maximum occupancy </h5>
            </div>
            <div class="x_content">
                <!--<div class="col-xs-12 bg-white progress_summary">
                    <div class="row" style="margin-top:10px;">
                        <div class="col-xs-2">
                            <span>9 AM</span>
                        </div>
                        <div class="col-xs-8">
                            <div class="progress progress_sm">
                                <div class="progress-bar bg-green" role="progressbar" data-transitiongoal={{ $percentage['09'] }}
                                    style="width:0%;" aria-valuenow="87">
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-2 more_info">
                            <span>{{ $percentage['09'] }}%</span>
                        </div>
                    </div>
                    <div class="row" style="margin-top:10px;">
                        <div class="col-xs-2">
                            <span>10 AM</span>
                        </div>
                        <div class="col-xs-8">
                            <div class="progress progress_sm">
                                <div class="progress-bar bg-green" role="progressbar" data-transitiongoal={{ $percentage['10'] }}
                                    style="width:0%;" aria-valuenow="77">
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-2 more_info">
                            <span>{{ $percentage['10'] }}%</span>
                        </div>
                    </div>
                    <div class="row" style="margin-top:10px;">
                        <div class="col-xs-2">
                            <span>11 AM</span>
                        </div>
                        <div class="col-xs-8">
                            <div class="progress progress_sm">
                                <div class="progress-bar bg-green" role="progressbar" data-transitiongoal={{ $percentage['11'] }}
                                    style="width:0%;" aria-valuenow="67">
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-2 more_info">
                            <span>{{ $percentage['11'] }}%</span>
                        </div>
                    </div>
                    <div class="row" style="margin-top:10px;">
                        <div class="col-xs-2">
                            <span>12 PM</span>
                        </div>
                        <div class="col-xs-8">
                            <div class="progress progress_sm">
                                <div class="progress-bar bg-green" role="progressbar" data-transitiongoal={{ $percentage['12'] }}
                                    style="width:0%;" aria-valuenow="77">
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-2 more_info">
                            <span>{{ $percentage['12'] }}%</span>
                        </div>
                    </div>
                    <div class="row" style="margin-top:10px;">

                        <div class="col-xs-2">
                            <span>1 PM</span>
                        </div>
                        <div class="col-xs-8">
                            <div class="progress progress_sm">
                                <div class="progress-bar bg-green" role="progressbar" data-transitiongoal={{ $percentage['13'] }}
                                    style="width: 0%;" aria-valuenow="67">
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-2 more_info">
                            <span>{{ $percentage['13'] }}%</span>
                        </div>
                    </div>
                    <div class="row" style="margin-top:10px;">
                        <div class="col-xs-2">
                            <span>2 PM</span>
                        </div>
                        <div class="col-xs-8">
                            <div class="progress progress_sm">
                                <div class="progress-bar bg-green" role="progressbar" data-transitiongoal={{ $percentage['14'] }}
                                    style="width:0%;" aria-valuenow="77">
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-2 more_info">
                            <span>{{ $percentage['14'] }}%</span>
                        </div>
                    </div>
                    <div class="row" style="margin-top:10px;">
                        <div class="col-xs-2">
                            <span>3 PM</span>
                        </div>
                        <div class="col-xs-8">
                            <div class="progress progress_sm">
                                <div class="progress-bar bg-green" role="progressbar" data-transitiongoal={{ $percentage['15'] }}
                                    style="width:0%;" aria-valuenow="77">
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-2 more_info">
                            <span>{{ $percentage['15'] }}%</span>
                        </div>
                    </div>
                    <div class="row" style="margin-top:10px;">
                        <div class="col-xs-2">
                            <span>4 PM</span>
                        </div>
                        <div class="col-xs-8">
                            <div class="progress progress_sm">
                                <div class="progress-bar bg-green" role="progressbar" data-transitiongoal={{ $percentage['16'] }}
                                    style="width:0%;" aria-valuenow="77">
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-2 more_info">
                            <span>{{ $percentage['16'] }}%</span>
                        </div>
                    </div>
                    <div class="row" style="margin-top:10px;">
                        <div class="col-xs-2">
                            <span>5 PM</span>
                        </div>
                        <div class="col-xs-8">
                            <div class="progress progress_sm">
                                <div class="progress-bar bg-green" role="progressbar" data-transitiongoal={{ $percentage['17'] }}
                                    style="width:0%;" aria-valuenow="77">
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-2 more_info">
                            <span>{{ $percentage['17'] }}%</span>
                        </div>
                    </div>
                    <div class="row" style="margin-top:10px;">
                        <div class="col-xs-2">
                            <span>6 PM</span>
                        </div>
                        <div class="col-xs-8">
                            <div class="progress progress_sm">
                                <div class="progress-bar bg-green" role="progressbar" data-transitiongoal={{ $percentage['18'] }}
                                    style="width:0%;" aria-valuenow="77">
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-2 more_info">
                            <span>{{ $percentage['18'] }}%</span>
                        </div>
                    </div>
                    <div class="row" style="margin-top:10px;">
                        <div class="col-xs-2">
                            <span>7 PM</span>
                        </div>
                        <div class="col-xs-8">
                            <div class="progress progress_sm">
                                <div class="progress-bar bg-green" role="progressbar" data-transitiongoal={{ $percentage['19'] }}
                                    style="width:0%;" aria-valuenow="77">
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-2 more_info">
                            <span>{{ $percentage['19'] }}%</span>
                        </div>
                    </div>
                    <div class="row" style="margin-top:10px;">
                        <div class="col-xs-2">
                            <span>8 PM</span>
                        </div>
                        <div class="col-xs-8">
                            <div class="progress progress_sm">
                                <div class="progress-bar bg-green" role="progressbar" data-transitiongoal={{ $percentage['20'] }}
                                    style="width:0%;" aria-valuenow="77">
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-2 more_info">
                            <span>{{ $percentage['20'] }}%</span>
                        </div>
                    </div>
                    <div class="row" style="margin-top:10px;">
                        <div class="col-xs-2">
                            <span>9 PM</span>
                        </div>
                        <div class="col-xs-8">
                            <div class="progress progress_sm">
                                <div class="progress-bar bg-green" role="progressbar" data-transitiongoal={{ $percentage['21'] }}
                                    style="width:0%;" aria-valuenow="77">
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-2 more_info">
                            <span>{{ $percentage['21'] }}%</span>
                        </div>
                    </div>
                    <div class="row" style="margin-top:10px;">
                        <div class="col-xs-2">
                            <span>10 PM</span>
                        </div>
                        <div class="col-xs-8">
                            <div class="progress progress_sm">
                                <div class="progress-bar bg-green" role="progressbar" data-transitiongoal={{ $percentage['22'] }}
                                    style="width:0%;" aria-valuenow="77">
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-2 more_info">
                            <span>{{ $percentage['22'] }}%</span>
                        </div>
                    </div>
                    <div class="row" style="margin-top:10px;">
                        <div class="col-xs-2">
                            <span>11 PM</span>
                        </div>
                        <div class="col-xs-8">
                            <div class="progress progress_sm">
                                <div class="progress-bar bg-green" role="progressbar" data-transitiongoal={{ $percentage['23'] }}
                                    style="width:0%;" aria-valuenow="77">
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-2 more_info">
                            <span id="2300">{{ $percentage['23'] }}%</span>
                        </div>
                    </div>
                    <div class="row" style="margin-top:10px;">
                        <div class="col-xs-2">
                            <span>12 AM</span>
                        </div>
                        <div class="col-xs-8">
                            <div class="progress progress_sm">
                                <div class="progress-bar bg-green" role="progressbar" data-transitiongoal={{ $percentage['00'] }}
                                    style="width:0%;" aria-valuenow="77">
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-2 more_info">
                            <span>{{ $percentage['00'] }}%</span>
                        </div>
                    </div>


                </div>-->
                <div class="Plotchart">
                    <table id="data-table" border="1" cellpadding="0" cellspacing="0"
                    summary="Percentage of knowledge acquired during my experience
                    for each technology or language.">
                        <thead>
                        <tr>
                            <td>&nbsp;</td>
                            <th scope="col"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <th scope="row">9 AM</th>
                            <td>{{ $percentage['09'] }}</td>
                        </tr>
                        <tr>
                            <th scope="row">10 AM</th>
                            <td>{{ $percentage['10'] }}</td>
                        </tr>
                        <tr>
                            <th scope="row">11 AM</th>
                            <td>{{ $percentage['11'] }}</td>
                        </tr>
                        <tr>
                            <th scope="row">12 PM</th>
                            <td>{{ $percentage['12'] }}</td>
                        </tr>
                        <tr>
                            <th scope="row">1 PM</th>
                            <td>{{ $percentage['13'] }}</td>
                        </tr>
                        <tr>
                            <th scope="row">2 PM</th>
                            <td>{{ $percentage['14'] }}</td>
                        </tr>
                        <tr>
                            <th scope="row">3 PM</th>
                            <td>{{ $percentage['15'] }}</td>
                        </tr>
                        <tr>
                            <th scope="row">4 PM</th>
                            <td>{{ $percentage['16'] }}</td>
                        </tr>
                        <tr>
                            <th scope="row">5 PM</th>
                            <td>{{ $percentage['17'] }}</td>
                        </tr>
                        <tr>
                            <th scope="row">6 PM</th>
                            <td>{{ $percentage['18'] }}</td>
                        </tr>
                        <tr>
                            <th scope="row">7 PM</th>
                            <td>{{ $percentage['19'] }}</td>
                        </tr>
                        <tr>
                            <th scope="row">8 PM</th>
                            <td>{{ $percentage['20'] }}</td>
                        </tr>
                        <tr>
                            <th scope="row">9 PM</th>
                            <td>{{ $percentage['21'] }}</td>
                        </tr>
                        <tr>
                            <th scope="row">10 PM</th>
                            <td>{{ $percentage['22'] }}</td>
                        </tr>
                        <tr>
                            <th scope="row">11 PM</th>
                            <td>{{ $percentage['23'] }}</td>
                        </tr>
                        <tr>
                            <th scope="row">12 AM</th>
                            <td>{{ $percentage['00'] }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    

    <div class="clearfix"></div>
</div>
<!----------------- /Third Tier (Tables) End--------------------->

<!--------------------------- Fourth Tier (History plus Forecast) --------------------->
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="dashboard_graph">
            <div class="row x_title">
                <div class="col-md-6">
                    <h2>Daily Avg Occupancy Historical &amp; Forecasted Records</h2>
                </div>
                <div class="col-md-6">

                </div>
            </div>
            <div class="col-md-3">
                <h3>Filter</h3>
                <br> 
                <button onclick="plotMonth()">Month</button>
                <br>
                <button onclick="plotWeek()">Week</button>

            </div>
            <div class="col-md-9" id="graphArea">
<!--                <div id="curve_chart" style="width: 900px; height: 500px"></div>-->
                <canvas id="myChart"  style=" height: 500px"></canvas>
            </div>
            
        </div>
    </div>

</div>

<!--------------------------- Fourth Tier (History plus Forecast) --------------------->
@endsection