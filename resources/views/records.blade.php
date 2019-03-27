@extends('layouts.app')

@section('custom-css')
@endsection

@section('custom-js')
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

                <table id="datatable" class="table table-striped table-bordered">
                  <thead>
                    <tr>
                      <th>Date</th>
                      <th>Time Range (5 Min Intervals)</th>
                      <th>No. of People</th>
                      <th>Occupancy Rate</th>

                    </tr>
                  </thead>


                  <tbody>
                    <tr>
                      <td>07/03/2019</td>
                      <td>12:30:00 - 12:35:00</td>
                      <td>40</td>
                    </tr>


                  </tbody>
                </table>
              </div>
            </div>
          </div>



        </div>
      </div>
@endsection