@extends('layouts.app')

@section('custom-js')
<script type="text/javascript">


  $(function () {
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
      $('#records').DataTable( {
        "order": [[ 0, "desc" ]],
        "processing": true,
        "serverSide": true,
        "ajax": {
            url: "{{ route('dataTable') }}"
            },
            "columns": [
              { data: 'created_at', visible: false },
            { 
              data: 'created_at', 
              render: function (data, type, row) { 
                var d = new Date(data);
                var month = d.toLocaleString('en-us', { month: 'long' });
                var formattedDate = d.getDate() + " " + month + " " + parseInt(d.getYear()+1900);
                return formattedDate; 
              }
            },
            { 
              data: 'created_at', 
              render: function (data, type, row) { 
                var d = new Date(data);
                var h = d.getHours();
                if (h.toString().length == 1) {
                  h+="0";
                }
                var m = d.getMinutes();
                if (m.toString().length == 1) {
                  m+="0";
                }
                var s = d.getMinutes();
                if (s.toString().length == 1) {
                  s+="0";
                }
                return tConvert(h + ":" + m + ":" + s, true); 
              }
            },
            { 
              data: 'numPeopleDetected', 
              render: function (data, type, row) { 
                return '<a href=/sale/' + data + '>' + data + '</a>'; 
              }
            },
            { 
              data: 'numPeopleDetected', 
              render: function (data, type, row) { 
                return Math.round(data / "{{ env('MAX_OCCUPANCY') }}" * 100) + "%"; 
              }
            },
            { 
              data: 'imageName', 
              render: function (data, type, row) { 
                
                return '<a target="_blank" href="{{ Storage::disk('s3')->url('/processed/') }}' + data + '.jpg"><img width="200" class="img-fluid" src="{{ Storage::disk('s3')->url('/processed/') }}' + data + '.jpg" /></a>';
              }
            }
        ]
    } );
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
                      {{-- @foreach ($liveImages as $index => $liveImage) 
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
                      @endforeach --}}
                  </tbody>
                </table>

              </div>
            </div>
          </div>



        </div>
      </div>
@endsection