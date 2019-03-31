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
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
var currNumPpl = 0;
google.charts.load('current', {'packages':['corechart']});
$(document).ready(function(){
    var WeekUrl = "http://3.1.241.179/api/liveimage?order=desc&aggregate=week";
    $.ajax ({
        url: WeekUrl,
        datatype: "json",
        success: function (e) {
            var data = e["images"];
            //plotMonthlyGraph(data);
            peakhours(data);
        }
    });
    var MonthUrl = "http://3.1.241.179/api/liveimage?order=desc&aggregate=month";
    $.ajax ({
        url: MonthUrl,
        datatype: "json",
        success: function (e) {
            var data = e["images"];
            MonthlyOccupancy(data);
        }

    });
    var WeekUrlAsc = "http://3.1.241.179/api/liveimage?order=asc&aggregate=week";
    $.ajax ({
        url: WeekUrlAsc,
        datatype: "json",
        success: function (e) {
            var data = e["images"];
            plotMonthlyGraph(data);
            //peakhours(data);
        }
    });
});
function plotMonthlyGraph(data){
    var totalOcc = 0; 
    var avgOcc= 0;
    var counter = 0 ;  
    var week = [];
    for(var k in data) {
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
        string = "Week "+k.split("-")[0]+","+avgOcc+","+Math.round(avgOcc/85*100);
        week.push(string);
    }
    //figure out how to feed data in 
    var data = google.visualization.arrayToDataTable([
            ['Week', 'Forcast', 'Present'],
            ['Week 0',  0,      0]
        ]);
        for(var i in week)
        {
            array = week[i].split(",");
            console.log(array);
            data.addRow([array[0],parseInt(array[1]),parseInt(array[2])]);

        }
       
        var options = {
            title: '',
            curveType: 'function',
            legend: { position: 'bottom' }
        };

        var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

        chart.draw(data, options);
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
       var numPpl = rawData[i]["numPeopleDetected"];
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
   addToList(mon,"Monday",count,1,result);
   addToList(tue,"Tuesday",count,2,result);
   addToList(wed,"Wednesday",count,3,result);
   addToList(thu,"Thursday",count,4,result);
   addToList(fri,"Friday",count,5,result);
   addToList(sat,"Saturaday",count,6,result);
   addToList(sun,"Sunday",count,0,result);
   var output="";
   for(var i in result)
   {
       var arry = result[i].split(",");
       output += "<tr>"
       output += "  <th scope=\"row\">"+(parseInt(i)+1)+"</th>"
       output += "      <td>"+arry[0]+"</td>"
       output += "      <td>"+arry[1]+" </td>"
       output += "      <td>"+arry[2]+"</td>"
       output += "      <td>"+arry[3]+"</td>"
       output += "      </tr>"

   } 
   $("#peakhours").empty(); 
   $("#peakhours").append(output); 
}
function addToList (array, day,count,index,result)
{
    for(var i in array)
    {
        avg = getAvg(array[i],count[index]);
        occupancyRate = checker(avg)
        // change to 10 when is done.
        if(occupancyRate>10)
        {
            nextHr = parseInt(i) + 1; 
            string = day+"," + i+":00-"+nextHr+":00,"+avg+","+occupancyRate;
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
        output+="   <th scope=\"row\">"+(parseInt(i+1))+"</th>";
        output+="   <td>"+array[0]+"</td>";
        output+="   <td>"+array[1]+"</td>";
        output+="   <td>"+array[2]+"</td>";
        output+="</tr>";
    }
    $("#monthlyOcc").empty();
    $("#monthlyOcc").append(output);
    //
}

$(function () {
    var chartData1 = [];
    var chartData2 = [];
    var chartData3 = [];
    var chartData4 = [];

    generateChartData();

    function generateChartData() {
        var firstDate = new Date();
        firstDate.setDate(firstDate.getDate() - 500);
        firstDate.setHours(0, 0, 0, 0);

        var a1 = 1500;
        var b1 = 1500;
        var a2 = 1700;
        var b2 = 1700;
        var a3 = 1600;
        var b3 = 1600;
        var a4 = 1400;
        var b4 = 1400;

        for (var i = 0; i < 500; i++) {
            var newDate = new Date(firstDate);
            newDate.setDate(newDate.getDate() + i);

            a1 += Math.round((Math.random() < 0.5 ? 1 : -1) * Math.random() * 10);
            b1 += Math.round((Math.random() < 0.5 ? 1 : -1) * Math.random() * 10);

            a2 += Math.round((Math.random() < 0.5 ? 1 : -1) * Math.random() * 10);
            b2 += Math.round((Math.random() < 0.5 ? 1 : -1) * Math.random() * 10);

            chartData1.push({
                date: newDate,
                value: a1,
                volume: b1 + 1500
            });
            chartData2.push({
                date: newDate,
                value: a2,
                volume: b2 + 1500
            });
          
        }
    }

    var chart = AmCharts.makeChart("chartdiv", {
        type: "stock",
        theme: "none",
        dataSets: [
            {
                title: "Avg No. of Customers", //first data set
                fieldMappings: [
                    {
                        fromField: "value",
                        toField: "value"
                    },
                    {
                        fromField: "volume",
                        toField: "volume"
                    }
                ],
                dataProvider: chartData1,
                categoryField: "date"
            },
            {
                title: "Avg Forcasted No. of Customers", //second data set
                fieldMappings: [
                    {
                        fromField: "value",
                        toField: "value"
                    },
                    {
                        fromField: "volume",
                        toField: "volume"
                    }
                ],
                dataProvider: chartData2,
                categoryField: "date"
            },

        ],

        panels: [
            {
                showCategoryAxis: false,
                title: "Value",
                percentHeight: 70,
                stockGraphs: [
                    {
                        id: "g1",
                        valueField: "value",
                        comparable: true,
                        compareField: "value",
                        balloonText: "[[title]]:<b>[[value]]</b>",
                        compareGraphBalloonText: "[[title]]:<b>[[value]]</b>"
                    }
                ],
                stockLegend: {
                    periodValueTextComparing: "[[percents.value.close]]%",
                    periodValueTextRegular: "[[value.close]]"
                }
            },
            {
                title: "Volume",
                percentHeight: 30,
                stockGraphs: [
                    {
                        valueField: "volume",
                        type: "column",
                        showBalloon: false,
                        fillAlphas: 1
                    }
                ],
                stockLegend: {
                    periodValueTextRegular: "[[value.close]]"
                }
            }
        ],

        chartScrollbarSettings: {
            graph: "g1"
        },

        chartCursorSettings: {
            valueBalloonsEnabled: true,
            fullWidth: true,
            cursorAlpha: 0.1,
            valueLineBalloonEnabled: true,
            valueLineEnabled: true,
            valueLineAlpha: 0.5
        },

        periodSelector: {
            position: "left",
            periods: [
                {
                    period: "MM",
                    selected: true,
                    count: 1,
                    label: "1 month"
                },
                {
                    period: "YYYY",
                    count: 1,
                    label: "1 year"
                },
                {
                    period: "YTD",
                    label: "YTD"
                },
                {
                    period: "MAX",
                    label: "MAX"
                }
            ]
        },

        dataSetSelector: {
            position: "left"
        },

        export: {
            enabled: true
        }
    });
});

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
    myClock.innerText = h + ":" + m + ":" + s;

    setTimeout(renderTime, 1000);
}
renderTime();

// google charts
//google.charts.load('current', {'packages':['corechart']});
//google.charts.setOnLoadCallback(drawChart);

/*function drawChart() {
    var data = google.visualization.arrayToDataTable([
        ['Year', 'Forcast', 'Present'],
        ['2004',  1000,      400],
        ['2005',  1170,      460],
        ['2006',  660,       1120],
        ['2007',  1030,      540]
    ]);
    data.addRow(['2019',500,400]);
    var options = {
        title: '',
        curveType: 'function',
        legend: { position: 'bottom' }
    };

    var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

    chart.draw(data, options);
}*/





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
            <h3>Occupancy: {{ number_format($latest['numPeopleDetected']/env('MAX_OCCUPANCY')*100) }}%</h3>
            <p>Real-Time No. Of Customers</p>
        </div>
    </div>

    <div class="animated flipInY col-lg-4 col-md-3 col-sm-6 col-xs-12">
        <div class="tile-stats">
            <div class="icon"><i class="fa fa-users"></i></div>
            <div class="count">80</div>
            <h3>Max Sign-Up: {{env('MAX_OCCUPANCY')}}</h3>
            <p>Present Customer Sign-Ups</p>
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

    <div class="col-md-4 col-sm-6 col-xs-12 ">
        <div class="x_panel">
            <div class="x_title">
                <h2>Daily Average Occupancy Rate</h2>
                <ul class="nav navbar-right panel_toolbox">
                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </li>
                </ul>
                <div class="clearfix"></div>
                <h5>To identify the need for action for days reaching maximum occupancy</h5>
            </div>
            <div class="x_content">
                <div class="col-xs-12 bg-white progress_summary">
                    <div class="row" style="margin-top:10px;">
                        <div class="col-xs-2">
                            <span>Monday</span>
                        </div>
                        <div class="col-xs-8">
                            <div class="progress progress_sm">
                                <div class="progress-bar bg-green" role="progressbar" data-transitiongoal="89"
                                    style="width:0%;" aria-valuenow="87">
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-2 more_info">
                            <span>89%</span>
                        </div>
                    </div>
                    <div class="row" style="margin-top:10px;">
                        <div class="col-xs-2">
                            <span>Tuesday</span>
                        </div>
                        <div class="col-xs-8">
                            <div class="progress progress_sm">
                                <div class="progress-bar bg-green" role="progressbar" data-transitiongoal="79"
                                    style="width:0%;" aria-valuenow="77">
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-2 more_info">
                            <span>79%</span>
                        </div>
                    </div>
                    <div class="row" style="margin-top:10px;">
                        <div class="col-xs-2">
                            <span>Weds</span>
                        </div>
                        <div class="col-xs-8">
                            <div class="progress progress_sm">
                                <div class="progress-bar bg-green" role="progressbar" data-transitiongoal="69"
                                    style="width:0%;" aria-valuenow="67">
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-2 more_info">
                            <span>69%</span>
                        </div>
                    </div>
                    <div class="row" style="margin-top:10px;">
                        <div class="col-xs-2">
                            <span>Thursday</span>
                        </div>
                        <div class="col-xs-8">
                            <div class="progress progress_sm">
                                <div class="progress-bar bg-green" role="progressbar" data-transitiongoal="79"
                                    style="width:0%;" aria-valuenow="77">
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-2 more_info">
                            <span>79%</span>
                        </div>
                    </div>
                    <div class="row" style="margin-top:10px;">

                        <div class="col-xs-2">
                            <span>Friday</span>
                        </div>
                        <div class="col-xs-8">
                            <div class="progress progress_sm">
                                <div class="progress-bar bg-green" role="progressbar" data-transitiongoal="69"
                                    style="width: 0%;" aria-valuenow="67">
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-2 more_info">
                            <span>69%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="col-md-4 col-sm-4 col-xs-12">
        <div class="x_panel tile fixed_height_320 overflow_hidden">
            <div class="x_title">
                <h2>Weekly Usage Breakdown(%)</h2>
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
                            <canvas class="canvasDoughnut" height="140" width="140"
                                style="margin: 15px 10px 10px 0"></canvas>
                        </td>
                        <td>
                            <table class="tile_info">
                                <tr>
                                    <td>
                                        <p><i class="fa fa-square blue"></i>Monday</p>
                                    </td>
                                    <td>20%</td>
                                </tr>
                                <tr>
                                    <td>
                                        <p><i class="fa fa-square green"></i>Tuesday</p>
                                    </td>
                                    <td>10%</td>
                                </tr>
                                <tr>
                                    <td>
                                        <p><i class="fa fa-square purple"></i>Wednesday</p>
                                    </td>
                                    <td>20%</td>
                                </tr>
                                <tr>
                                    <td>
                                        <p><i class="fa fa-square aero"></i>Thursday</p>
                                    </td>
                                    <td>15%</td>
                                </tr>
                                <tr>
                                    <td>
                                        <p><i class="fa fa-square red"></i>Friday</p>
                                    </td>
                                    <td>30%</td>
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
    <div class="col-md-6 col-sm-6 col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>Peak Hours ( >= 90% Occupancy Rate) </h2>

                <ul class="nav navbar-right panel_toolbox">
                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </li>
                </ul>
                <div class="clearfix"></div>

                <h5>To identify the peak hours in the week nearing maximum occupancy rate that call for
                    further action. </h5>
            </div>
            <div class="x_content">

                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Day</th>
                            <th>Time(Hourly)</th>
                            <th>No. of Customers</th>
                            <th>Occupancy Rate(%)</th>
                        </tr>
                    </thead>
                    <tbody id="peakhours">
                    </tbody>
                </table>

            </div>
        </div>
    </div>


    <div class="col-md-6 col-sm-6 col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>Average Monthly Occupancy (Ascending Order) </h2>

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
                            <th>#</th>
                            <th>Months</th>
                            <th>Average No. of Customers</th>
                            <th>Average Occupancy Rate(%)</th>
                        </tr>
                    </thead>
                    <tbody id="monthlyOcc">
                    </tbody>
                </table>

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
                    <h2>Daily Average Occupancy Historical & Forecasted Records</h2>
                </div>
                <div class="col-md-6">

                </div>
            </div>
            <div class="col-md-3">
                <h3>Filter</h3>
            </div>
            <div class="col-md-9">
                <div id="curve_chart" style="width: 900px; height: 500px"></div>
            </div>
            
        </div>
    </div>

</div>

<!--------------------------- Fourth Tier (History plus Forecast) --------------------->
@endsection