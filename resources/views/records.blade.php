@extends('layouts.app')

@section('custom-js')
<script type="text/javascript">
  $(function () {
      $('#records').DataTable({
          "order": [[ 0, "desc" ]],
          "bPaginate": false,
          "bInfo": false,
      });
  });
  </script>
@endsection

@section('content')
<div class="page-title">
    <h3>Scape HubQuarters Dashboard </h3>
</div>
<div class="clearfix"></div>
<div class="">


        <div class="clearfix"></div>

        <div class="row">
          <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
              <div class="x_title">
                <h2>Past Records</h2>
                <div class="clearfix"></div>
              </div>
              <div class="x_content">

                <table id="records" class="table table-striped table-bordered">
                  <thead>
                    <tr>
                      <th class="hidden">Timestamp</th>
                      <th>Date</th>
                      <th>Time Range (5 Min Intervals)</th>
                      <th class="text-center">No. of People</th>
                      <th class="text-center">Occupancy Rate</th>
                      <th class="text-center" width="20%">Live Image</th>
                    </tr>
                  </thead>


                  <tbody>
                      @foreach ($liveImages as $index => $liveImage) 
                      <tr>
                          <td class="hidden">{{ $liveImage['created_at'] }}</td>
                          <td>{{ date('d F Y', strtotime($liveImage['created_at'])) }}</td>
                          <td>
                            {{ date('h:i:s A', round(strtotime($liveImage['created_at'])/60)*60) }}
                          </td>
                          <td class='text-center'>{{ $liveImage['numPeopleDetected'] }}</td>
                          <td class='text-center'>{{ number_format($liveImage['numPeopleDetected']/env('MAX_OCCUPANCY')*100) }}%</td>
                          @if (Storage::disk('s3')->exists('/processed/' . $liveImage['imageName'] . '.jpg'))
                          <td class='text-center'><a target="_blank" href="{{ Storage::disk('s3')->url('/processed/' . $liveImage['imageName'] . '.jpg') }}"><img width="200" class="img-fluid" src="{{ Storage::url('/processed/' . $liveImage['imageName'] . '.jpg') }}" /></a></td>
                          @else
                          <td class='text-center'>Error</td>
                          @endif
                      </tr>
                      @endforeach
                  </tbody>
                </table>

                {{ $liveImages->links() }}
              </div>
            </div>
          </div>



        </div>
      </div>
@endsection