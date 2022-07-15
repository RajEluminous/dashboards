@extends('layouts.new_app')
@section('content')
<div class="right_col" role="main">
    <div class="">
      <div class="page-title">
        <div class="title_left">
          {{-- <h3>List Growth</h3> --}}
        </div>

      </div>

      <div class="clearfix"></div>

      <div class="row">
        <div class="col-md-12 col-sm-12  ">
            <div class="x_panel">
              <div class="x_title">
                <h2 style="color:black;">Affiliate Revenue</h2>
                <div class="clearfix"></div>
               <div class="form-group">
                  <label for="show_month">View selection</label>
                  <select class="form-control" name="show_month" id="show_month">
                    <option value="1" {{($view == '1')?'selected':''}}>Current Month + Last 6 Months</option>
                    <option value="2" {{($view == '2')?'selected':''}}>Current Month + Last 12 Months</option>
					<option value="3" {{($view == '3')?'selected':''}}>Current Month + Last 24 Months</option>
                  </select>
                </div>
              </div>
              <div class="x_content">
                <h6>Last Update Time: {{ ($last_update_time !='')?date('Y-m-d',strtotime($last_update_time)).' | '.date('g:i A',strtotime($last_update_time)).' ('.date('e',strtotime($last_update_time)).')' : '' }}</h6>
                <canvas id="affiliate_revenue_chart" style="height:20px !important;width:100px !important;"></canvas>
              </div>

            </div>
        </div>
      </div>

      <div class="row">
      <div class="col-md-12 col-sm-12">
        <div class="row">
          <div class="col-md-12 col-sm-12">
            <div class="row">
              <div class="col-md-12 col-sm-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2 style="color:black;">Current Month Difference</h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <h4>{{ $cmd->date }}</h4>
                    <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                      <thead>
                        <tr class="text-center">
                          <th>Revenue</th>
                          <th>Target</th>
                          <th>Amount</th>
                          <th>Percentage (%)</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr class="text-center">
                          <td>{{number_format($cmd->revenue)}}</td>
                          <td>{{number_format($cmd->target)}}</td>
                          <td style="color:{{($cmd->target > $cmd->revenue)?'red':'green'}}"><strong> {{number_format($cmd->revenue-$cmd->target)}} <i class="fa fa-caret-{{($cmd->target > $cmd->revenue)?'down':'up'}}"></i></strong></td>
                          <td style="color:{{($cmd->target > $cmd->revenue)?'red':'green'}}"><strong>{{number_format((($cmd->revenue-$cmd->target)/$cmd->target)*100,1) }} <i class="fa fa-caret-{{($cmd->target > $cmd->revenue)?'down':'up'}}"></i></strong></td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12 col-sm-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2 style="color:black;">Sales Ranking By Account</h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                      <thead>
                        <tr class="text-center">
                          <th>Ranking</th>
                          <th>Account</th>
                          <th>Month To Date</th>
                          <th>Projected Month</th>
                          <th>Last Month</th>
                          <th>Variance (%)</th>
                        </tr>
                      </thead>
                      <tbody>
                        @for($i = 0; $i <= count($sales_ranking_by_account['account'])-1;$i++)
                        <?php
                          $index = $i+1;
                          $account = $sales_ranking_by_account['account'][$i];
                          $month_to_date = $sales_ranking_by_account['month_to_date'][$i];
                          $month_to_date_percent = $sales_ranking_by_account['month_to_date_percent'][$i];
                          $projected_month = $sales_ranking_by_account['projected_month'][$i];
                          $last_month = $sales_ranking_by_account['last_month'][$i];
                          $last_month_percent = $sales_ranking_by_account['last_month_percent'][$i];
                          $variance = $sales_ranking_by_account['variance'][$i];
                        ?>
                          <tr class="text-center">
                            <td>{{$index}}</td>
                            <td>{{$account}}</td>
                            <td>{{number_format($month_to_date)}} <span class="badge badge-pill badge-secondary ml-1 fs-xs">{{ number_format($month_to_date_percent, 1) }}%</span></td>
                            <td>{{number_format($projected_month)}}</td>
                            <td>{{number_format($last_month)}} <span class="badge badge-pill badge-secondary ml-1 fs-xs">{{ number_format($last_month_percent, 1) }}%</span></td>
                            <td style="color:{{($variance < 0)?'red':'green'}}"><strong>{{number_format($variance,1) }} <i class="fa fa-caret-{{($variance < 0)?'down':'up'}}"></i></strong></td>
                          </tr>
                        @endfor
                        <tr class="text-center table-info">
                            <td>&nbsp;</td>
                            <td><strong>Total:</strong></td>
                            <td><strong>{{number_format($sales_ranking_by_account['total_month_to_date'])}}</strong></td>
                            <td><strong>{{number_format($sales_ranking_by_account['total_projected_month'])}}</strong></td>
                            <td><strong>{{number_format($sales_ranking_by_account['total_last_month'])}}</strong></td>
                            <td style="color:{{($sales_ranking_by_account['total_variance'] < 0)?'red':'green'}}"><strong>{{number_format($sales_ranking_by_account['total_variance'],1) }} <i class="fa fa-caret-{{($sales_ranking_by_account['total_variance'] < 0)?'down':'up'}}"></i></strong></td>
                          </tr>
                      </tbody>
                    </table>
                  </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-12">
                <div class="x_panel">
                  <div class="x_title">
                  <h2 style="color:black;">Incoming Traffic Status</h2>
                  <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                  <div class="table-responsive">
                  <table class="table table-striped table-bordered">
                    <thead>
                    <tr class="text-center">
                      <th rowspan="2" style="vertical-align : middle;text-align:center;">Ranking</th>
                      <th rowspan="2" style="vertical-align : middle;text-align:center;">Account</th>
                      <th colspan="4">Hop Count</th>
                      <th colspan="3">FE CVR</th>
                    </tr>
                    <tr class="text-center">
                      <th>Month To Date</th>
                      <th>Projected Month</th>
                      <th>Last Month</th>
                      <th>Variance (%)</th>
                      <th>Current Month</th>
                      <th>Last Month</th>
                      <th>Variance (%)</th>
                    </tr>
                    </thead>
                    <tbody>
                    @for($i = 0; $i <= count($incoming_traffic_status['account'])-1;$i++)
                      <?php
                        $index = $i+1;
                        $account = $incoming_traffic_status['account'][$i];
                        $hop_month_to_date = $incoming_traffic_status['hop_month_to_date'][$i];
                        $hop_last_month = $incoming_traffic_status['hop_last_month'][$i];

                        // projected month
                        $hop_projected_month = $incoming_traffic_status['hop_projected_month'][$i];
                        $hop_variance =  $incoming_traffic_status['hop_variance'][$i];
                        $fecvr_month_to_date = $incoming_traffic_status['fecvr_month_to_date'][$i];
                        $fecvr_last_month = $incoming_traffic_status['fecvr_last_month'][$i];
                        $fecvr_variance = $incoming_traffic_status['fecvr_variance'][$i];
                      ?>
                      <tr class="text-center">
                        <td>{{$index}}</td>
                        <td>{{$account}}</td>
                        <td>{{number_format($hop_month_to_date)}}</td>
                        <td>{{number_format($hop_projected_month)}}</td>
                        <td>{{number_format($hop_last_month)}}</td>
                        <td style="color:{{($hop_variance < 0)?'red':'green'}}"><strong>{{number_format($hop_variance,1) }} <i class="fa fa-caret-{{($hop_variance < 0)?'down':'up'}}"></i></strong></td>
                        <td>{{ $fecvr_month_to_date}} %</td>
                        <td>{{ $fecvr_last_month}} %</td>
                        <td style="color:{{($fecvr_variance < 0)?'red':'green'}}"><strong>{{number_format($fecvr_variance,1) }} <i class="fa fa-caret-{{($fecvr_variance < 0)?'down':'up'}}"></i></strong></td>
                      </tr>
                      @endfor
                      <tr class="text-center table-info">
                        <td>&nbsp;</td>
                        <td><strong>Total:</strong></td>
                        <td><strong>{{number_format($incoming_traffic_status['total_hop_month_to_date'])}}</strong></td>
                        <td><strong>{{number_format($incoming_traffic_status['total_hop_projected_month'])}}</strong></td>
                        <td><strong>{{number_format($incoming_traffic_status['total_hop_last_month'])}}</strong></td>
                        <td style="color:{{($incoming_traffic_status['total_hop_variance'] < 0)?'red':'green'}}"><strong>{{number_format($incoming_traffic_status['total_hop_variance'],1) }} <i class="fa fa-caret-{{($incoming_traffic_status['total_hop_variance'] < 0)?'down':'up'}}"></i></strong></td>
                        <td><strong>{{$incoming_traffic_status['total_fecvr_month_to_date']}} %</strong></td>
                        <td><strong>{{$incoming_traffic_status['total_fecvr_last_month']}} %</strong></td>
                        <td style="color:{{($incoming_traffic_status['total_fecvr_variance'] < 0)?'red':'green'}}"><strong>{{number_format($incoming_traffic_status['total_fecvr_variance'],1) }} <i class="fa fa-caret-{{($incoming_traffic_status['total_fecvr_variance'] < 0)?'down':'up'}}"></i></strong></td>
                      </tr>
                    </tbody>
                  </table>
                  </div>
                  </div>
                </div>
                </div>
              </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12 col-sm-12">
            @for($i = 1;$i<= 4; $i++)
            <?php
                $varDate = "";
                if($i==1)
                $varDate = ($topaffiliate_today_updated_at !='')?' | Updated at: '.date('g:i A',strtotime($topaffiliate_today_updated_at)).' ('.date('e',strtotime($topaffiliate_today_updated_at)).')' : '';
            ?>
            <div class="row">
              <div class="col-md-12 col-sm-12">
                <div class="x_panel">
                  @if($i == 1)
                  <div class="x_title">
                    <h2 style="color:black;">Top Affiliate {{$varDate}}</h2>
                    <div class="clearfix"></div>
                  </div>
                  @endif

            <div class="x_content">

            <h4>{{ $top_affiliate_label[$i-1] }}</h4>
            <?php $top_affiliates = App\Http\Controllers\AffiliateRevenueController::getTopAffiliate($i); ?>

            <div class="table-responsive">
            <table class="table table-striped table-bordered">
              <thead>
                <tr class="text-center">
                  <th>#</th>
                  <th style="width:140px;">Affiliate ID</th>
                  <th>Vendor ID</th>
                  <th>Hop Count</th>
                  <th>Aff. EPC</th>
                  <th>FE CVR</th>
                  <th>Aff. Comms</th>
                  <th>Vendor Rev.</th>
                </tr>
              </thead>
              <tbody>
                @if(count($top_affiliates)>0)
                @foreach($top_affiliates as $ta)
                <tr class="text-center">
                  <td>{{$loop->iteration}}</td>
                  <td>{{$ta->affiliate_id}}</td>
                  <td>{{$ta->vendor_id}}</td>
                  <td>{{number_format($ta->hop_count)}}</td>
                  <td>{{$ta->affiliate_epc}}</td>
                  <td>{{number_format($ta->fe_cvr,2)}}%</td>
                  <td>{{number_format(ceil($ta->affiliate_revenue))}}</td>
                  <td>{{number_format(ceil($ta->vendor_revenue))}}</td>
                </tr>
                @endforeach
                @else
                <tr class="text-center">
                    <td colspan="8">No data available, system will refresh again in next hour.</td>
                </tr>
                @endif
              </tbody>
            </table>
          </div>
          </div>
        </div>

            @endfor
      </div>
    </div>
  </div>
        </div>
      </div>
    </div>
    </div>
  </div>

  @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0/dist/chartjs-plugin-datalabels.js"></script>

    <script>
      var ctx = document.getElementById('affiliate_revenue_chart').getContext('2d');
      var chart = new Chart(ctx, {
          type: 'line',
          // ctx.canvas.parentNode.style.height = '300px';

          // The data for our dataset
          data: {
              labels: [{!!implode(',',$affiliate_revenue['months'])!!}],
              datasets: [{
                  label: 'Revenue',
                  borderColor: 'rgb(149, 229, 149)',
                  fill:false,
                  lineTension: 0.1,
                  data: [{{implode(',',$affiliate_revenue['revenue'])}}]
                },{
                    label: 'Target',
                    borderColor: 'rgb(149, 153, 223)',
                    fill:false,
                    lineTension: 0.1,
                    data: [{{implode(',',$affiliate_revenue['target'])}}],
                }]
            },

            // Configuration options go here
            options: {
                responsive: true,
                maintainAspectRatio: false,
                bezierCurve: false,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: false,
                            stepSize:10000,
                            callback: function(value, index, values) {
                                return value.toLocaleString();
                            }
                        }
                    }]
                },
                tooltips: {
			        callbacks: {
                        title: function(tooltipItems, data) {
                            return data.datasets[tooltipItems[0].datasetIndex].label;
                        },
                        label: function(tooltipItem, data) {
                            var result =  Math.round(tooltipItem.yLabel);
                            return '$' + result.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                        }
                    }
                },
                plugins: {
                    datalabels: {
                        align: function(context) {
                            var index = context.dataIndex;
                            var value = context.dataset.data[index];
                            var invert = Math.abs(value) <= 1;
                            return value < 1 ? 'end' : 'start'
                        },
                        anchor: 'end',
                        backgroundColor: null,
                        borderColor: null,
                        borderRadius: 4,
                        borderWidth: 1,
                        color: '#000000',
                        font: {
                            size: 11,
                            weight: 600
                        },
                        offset: 4,
                        padding: 0,
                        formatter: function(value) {
                            var result =  Math.round(value);
                            return result.toLocaleString();
                        }
                    }
                }
            },
        });

        $("#show_month").change(function(){
          var view = $(this).children("option:selected").val();
          var url = document.URL; // original url
          origin = url.slice(0, url.lastIndexOf('/')); //removed last parameter
          var newurl = origin+'/'+view; // added new paramter
          location.href = newurl;
        });


    </script>
  @endpush
@endsection
