@extends('layouts.app')

@section('custom-css')
<link href="{{ asset('css/button.css') }}" rel="stylesheet">
<style>
    #myDiv {
        margin: 0 auto;
        height: 500px;
        min-height: 200px;
    }
</style>
@endsection

@section('custom-js')
<script type="text/javascript">
$(function () {

//Enter a level between 0 and 4
var numPpl = $("#numPpL").text();
var percentages = numPpl/85*100;
var level = percentages/25;

// Trig to calc meter point
var degrees = 180 - (level) * 45;
radius = .5;
var radians = degrees * Math.PI / 180;
var x = radius * Math.cos(radians);
var y = radius * Math.sin(radians);

// Path: may have to change to create a better triangle
var mainPath = 'M -.0 -0.035 L .0 0.035 L ',
    pathX = String(x),
    space = ' ',
    pathY = String(y),
    pathEnd = ' Z';
var path = mainPath.concat(pathX, space, pathY, pathEnd);

var data = [{
    type: 'category',
    x: [0], y: [0],
    marker: { size: 28, color: '000000' },
    showlegend: false,
    name: 'speed',
    text: level,
    sort: false,
    hoverinfo: 'text+name'
},
{
    sort: false, values: [50, 5, 15, 30],
    rotation: 90,

    text: ['', 'high', 'medium', 'low'],
    textinfo: 'text',
    textposition: 'inside',
    marker: {
        colors: ['rgba(255, 255, 255, 0)', 'rgba(255, 0, 0, 1)',
            'rgba(255, 140, 0, 1)',
            'rgba(60,179,113,1)']
    },
    labels: ['', '70-85', '21-69', '0-20'],
    hoverinfo: 'label',
    hole: .5,
    type: 'pie',
    showlegend: false
}];

var layout = {
    shapes: [{
        type: 'path',
        path: path,
        fillcolor: '000000',
        line: {
            color: '000000'
        }
    }],
    title: 'Occupancy Rate: ' + percentages + '%',
    //height: 500,
    //width: 600,
    xaxis: {
        type: 'category', zeroline: false, showticklabels: false,
        showgrid: false, range: [-1, 1]
    },
    yaxis: {
        type: 'category', zeroline: false, showticklabels: false,
        showgrid: false, range: [-1, 1]
    }
};

Plotly.newPlot('myDiv', data, layout, { responsive: true });


    $(".pushme").click(function () {
        $(this).text("DON'T PUSH ME");
    });

    $(".pushme-with-color").click(function () {
        $(this).text("REQUESTED");
        $(this).addClass("btn-danger");
        $(this).removeClass("btn-success");
        $.ajax ({
        type: "POST",
        url: "http://3.1.241.179/api/tenants/request",
        datatype: "application/json",
        contentType:"application/json",
        data: JSON.stringify({ 
            'requested': true,
            'email':'tenant@hubquarters.com'
            }),
        success: function (e) {
            console.log(e);
        }
        });
    });


    $(".pushme2").click(function(){
        $(this).text(function(i, v){
            return v === 'PUSH ME' ? 'PULL ME' : 'PUSH ME'
        });
    });
});
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

</script>

@endsection

@section('content')
<div class="page-title">
    <h3>Scape HubQuarters Dashboard </h3>
</div>
@if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
@endif
<div class="clearfix"></div>

        <!----------- First Tier  --------------------->
        <div class="row top_tiles">

            <div class="col-lg-12 col-md-3 col-sm-6 col-xs-12 widget widget_tally_box">
                <div class="x_panel">
                    <div class="x_content">
                        <div class="flex">

                        </div>

                        <h2 class="name" id="numPpl">
                            <strong>{{ $latest['numPeopleDetected'] }} </strong></i><i class="fa fa-male"></i>
                        </h2>
                        <p>@ HubQuarters</p>



                        <div class="flex">
                            <ul class="list-inline count2">
                                <h3 id="clock"></h3>
                                <span>{{ date('d F Y') }}</span>
                            </ul>
                        </div>

                    </div>
                </div>
            </div>



        </div>
        <div class="clearfix"></div>
        <!----------- /First Tier End--------------------->

        <!----------- Second Tier  --------------------->
        <div class="row">
            <div class="col-lg-12 col-md-3 col-sm-6 col-xs-12">
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

                    <div id="myDiv"></div>

                </div>


            </div>
        </div>


        <!----------- /Second Tier End  --------------------->

@endsection