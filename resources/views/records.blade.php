@extends('layouts.app')

@section('custom-js')
<script type="text/javascript">
  $(function () {
      $('#records').DataTable({
          "order": [[ 0, "desc" ]]
      });
  });
  </script>
@endsection

@section('content')
<div class="page-title">
    <h3>Scape HubQuaters Dashboard </h3>
</div>
<div class="clearfix"></div>
<div class="">


        <div class="clearfix"></div>

        <div class="row">
          <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
              <div class="x_title">
                <h2>Past Records</h2>
                <ul class="nav navbar-right panel_toolbox">
                  <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                  </li>
                  <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                    <ul class="dropdown-menu" role="menu">
                      <li><a href="#">Settings 1</a>
                      </li>
                      <li><a href="#">Settings 2</a>
                      </li>
                    </ul>
                  </li>
                  <li><a class="close-link"><i class="fa fa-close"></i></a>
                  </li>
                </ul>
                <div class="clearfix"></div>
              </div>
              <div class="x_content">

                <table id="records" class="table table-striped table-bordered">
                  <thead>
                    <tr>
                      <th class="hidden">Timestamp</th>
                      <th>Date</th>
                      <th>Time Range (5 Min Intervals)</th>
                      <th>No. of People</th>
                      <th>Occupancy Rate</th>

                    </tr>
                  </thead>


                  <tbody>
                      @foreach ($liveImages as $index => $liveImage) 
                      <tr>
                          <td class="hidden">{{ $liveImage['created_at'] }}</td>
                          <td>{{ date('d F Y', strtotime($liveImage['created_at'])) }}</td>
                          <td>
                            @if (isset($liveImages[$index - 1]))
                            {{ date('h:i:s A', round(strtotime($liveImages[$index - 1]['created_at'])/60)*60) }} -
                            @endif
                            {{ date('h:i:s A', round(strtotime($liveImage['created_at'])/60)*60) }}
                          </td>
                          <td class='text-center'>{{ $liveImage['numPeopleDetected'] }}</td>
                          <td class='text-center'>{{ number_format($liveImage['numPeopleDetected']/env('MAX_OCCUPANCY')*100) }}%</td>
                      </tr>
                      @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>



        </div>
      </div>
@endsection