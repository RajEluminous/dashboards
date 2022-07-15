@extends('layouts.email')
@section('content')
<div class="right_col" role="main" style="margin:0px !important;padding:0px !important;">
    <div class="">
      <div class="page-title">
        <div class="title_left">
          {{-- <h3>List Growth</h3> --}}
        </div>

      </div>

      <div class="clearfix"></div>

      <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="x_panel">
              <div class="x_title">
                <h2>List Growth</h2>
                <div class="clearfix"></div>
              </div>
              <div class="x_content table-responsive">
                <h6>Last Update Time: {{ ($last_update_time !='')?date('Y-m-d',strtotime($last_update_time)).' | '.date('g:i A',strtotime($last_update_time)).' ('.date('e',strtotime($last_update_time)).')' : '' }}</h6>
                  <table class="table table-bordered table-striped">
                    <thead>
                      <tr class="text-center">
                        <th rowspan="2" style="vertical-align : middle;text-align:center;">Accounts / Types</th>
                        <th colspan="3" rowspan="1">Growth</th>
                        <th colspan="3" rowspan="1">Size</th>
                        <th colspan="3" rowspan="1" >Click</th>
                      </tr>
                      <tr class="text-center">
                        <th>Times</th>
                        <th>Leads</th>
                        <th>Cust</th>
                        <th>Times</th>
                        <th>Leads</th>
                        <th>Cust</th>
                        <th>Times</th>
                        <th>Leads</th>
                        <th style="width: 100px; !important;">Cust</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($accounts as $account)
                      <tr class="text-center" style="page-break-after: always;">
                        <td rowspan="3" style="vertical-align : middle;text-align:center;">{{($account->account_name == 'amazeyou')?'amazeyou2':$account->account_name}}</td>
                        <td><strong>Last 24 Hours</strong></td>
                        <td><?= App\Http\Controllers\ListGrowthController::getValue($account->account_id,1,1,1) ?></td>
                        <td><?= App\Http\Controllers\ListGrowthController::getValue($account->account_id,1,1,2) ?></td>
                        <td><strong>Now</strong></td>
                        <td><?= App\Http\Controllers\ListGrowthController::getValue($account->account_id,2,1,1) ?></td>
                        <td><?= App\Http\Controllers\ListGrowthController::getValue($account->account_id,2,1,2) ?></td>
                        <td><strong>Last 7 Avg</strong></td>
                        <td><?= App\Http\Controllers\ListGrowthController::getValue($account->account_id,3,1,1) ?></td>
                        <td><?= App\Http\Controllers\ListGrowthController::getValue($account->account_id,3,1,2) ?></td>
                      </tr>
                      <tr class="text-center">
                        <td><strong>Last 7 Days</strong></td>
                        <td><?= App\Http\Controllers\ListGrowthController::getValue($account->account_id,1,2,1) ?></td>
                        <td><?= App\Http\Controllers\ListGrowthController::getValue($account->account_id,1,2,2) ?></td>
                        <td><strong>7 Days Ago</strong></td>
                        <td><?= App\Http\Controllers\ListGrowthController::getValue($account->account_id,2,2,1) ?></td>
                        <td><?= App\Http\Controllers\ListGrowthController::getValue($account->account_id,2,2,2) ?></td>
                        <td><strong>Last 14 Avg</strong></td>
                        <td><?= App\Http\Controllers\ListGrowthController::getValue($account->account_id,3,2,1) ?></td>
                        <td><?= App\Http\Controllers\ListGrowthController::getValue($account->account_id,3,2,2) ?></td>
                      </tr>
                      <tr class="text-center">
                        <td><strong>Last 30 Days</strong></td>
                        <td><?= App\Http\Controllers\ListGrowthController::getValue($account->account_id,1,3,1) ?></td>
                        <td><?= App\Http\Controllers\ListGrowthController::getValue($account->account_id,1,3,2) ?></td>
                        <td><strong>30 Days Ago</strong></td>
                        <td><?= App\Http\Controllers\ListGrowthController::getValue($account->account_id,2,3,1) ?></td>
                        <td><?= App\Http\Controllers\ListGrowthController::getValue($account->account_id,2,3,2) ?></td>
                        <td><strong>Last 30 Avg</strong></td>
                        <td><?= App\Http\Controllers\ListGrowthController::getValue($account->account_id,3,3,1) ?></td>
                        <td><?= App\Http\Controllers\ListGrowthController::getValue($account->account_id,3,3,2) ?></td>
                      </tr>
                      @endforeach
                    </tbody>

                  </table>
              </div>
            </div>
        </div>
      </div>
    </div>
  </div>
@endsection