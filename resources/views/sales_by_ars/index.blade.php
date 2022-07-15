@extends('layouts.new_app')
@section('content')
<div class="right_col" role="main">
  <div class="">
    <div class="page-title">
      <div class="title_left">
        <h3>AR Sales</h3>
      </div>

    </div>

    <div class="clearfix"></div>

    @foreach ($accounts as $account)
    <div class="row">
      <div class="col-md-12 col-sm-12  ">
        <div class="x_panel">
          <div class="x_title">
            <h2>{{ App\Http\Controllers\SalesByARSController::getAccountName($account) }}</h2>
            <div class="clearfix"></div>
            <h6>Last Update Time: {{ ($last_update_time !='')?date('Y-m-d',strtotime($last_update_time)).' | '.date('g:i A',strtotime($last_update_time)).' ('.date('e',strtotime($last_update_time)).')' : '' }}</h6>
          </div>
          <?php $sales_lists = App\Http\Controllers\SalesByARSController::getARSLists($account); ?>
          @foreach ($sales_lists as $sl)
          <div class="x_content">
            <h4>{{ $sl->list_name }}</h4>
            <div class="table-responsive">
              <table class="table table-bordered table-striped">
                <thead>
                  <tr class="text-center">
                    <th rowspan="2" style="vertical-align : middle;text-align:center;">AR</th>
                    <th rowspan="2" style="vertical-align : middle;text-align:center;">Tracking ID</th>
                    <th colspan="3">Last 7 Day: <?= App\Helper\Helper::getNewDate()->modify('-7 day')->format("Y-m-d") ?> - <?= App\Helper\Helper::getNewDate()->modify('-1 day')->format("Y-m-d") ?></th>
                    <th colspan="3">Last 60 Day: <?= App\Helper\Helper::getNewDate()->modify('-60 day')->format("Y-m-d") ?> - <?= App\Helper\Helper::getNewDate()->modify('-1 day')->format("Y-m-d") ?></th>
                    <th colspan="3">Last 90 Day: <?= App\Helper\Helper::getNewDate()->modify('-90 day')->format("Y-m-d") ?> - <?= App\Helper\Helper::getNewDate()->modify('-1 day')->format("Y-m-d") ?></th>
                  </tr>
                  <tr class="text-center">
                    <th>Revenue</th>
                    <th>Hops</th>
                    <th>EPC</th>
                    <th>Revenue</th>
                    <th>Hops</th>
                    <th>EPC</th>
                    <th>Revenue</th>
                    <th>Hops</th>
                    <th>EPC</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $sales_details = App\Http\Controllers\SalesByARSController::getARSDetails($account, $sl->list_name); ?>
                  @foreach ($sales_details as $sales)
                  <tr class="text-center">
                    <td>{{ $sales->ar_number }}</td>
                    <td>{{ $sales->tracking_id }}</td>
                    <td>{{ App\Http\Controllers\SalesByARSController::getARSSales($sales->tracking_id,1,1) }}</td>
                    <td>{{ App\Http\Controllers\SalesByARSController::getARSSales($sales->tracking_id,1,2) }}</td>
                    <td>{{ App\Http\Controllers\SalesByARSController::getARSSales($sales->tracking_id,1,3) }}</td>
                    <td>{{ App\Http\Controllers\SalesByARSController::getARSSales($sales->tracking_id,2,1) }}</td>
                    <td>{{ App\Http\Controllers\SalesByARSController::getARSSales($sales->tracking_id,2,2) }}</td>
                    <td>{{ App\Http\Controllers\SalesByARSController::getARSSales($sales->tracking_id,2,3) }}</td>
                    <td>{{ App\Http\Controllers\SalesByARSController::getARSSales($sales->tracking_id,3,1) }}</td>
                    <td>{{ App\Http\Controllers\SalesByARSController::getARSSales($sales->tracking_id,3,2) }}</td>
                    <td>{{ App\Http\Controllers\SalesByARSController::getARSSales($sales->tracking_id,3,3) }}</td>
                  </tr>
                  @endforeach
                  <tr class="text-center">
                    <td></td>
                    <td></td>
                    <td><strong>{{App\Http\Controllers\SalesByARSController::getTotalSales($account,$sl->list_name,1)}}</strong></td>
                    <td></td>
                    <td></td>
                    <td><strong>{{App\Http\Controllers\SalesByARSController::getTotalSales($account,$sl->list_name,2)}}</strong></td>
                    <td></td>
                    <td></td>
                    <td><strong>{{App\Http\Controllers\SalesByARSController::getTotalSales($account,$sl->list_name,3)}}</strong></td>
                    <td></td>
                    <td></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          @endforeach

        </div>
      </div>
    </div>
    @endforeach

  </div>
</div>
@endsection