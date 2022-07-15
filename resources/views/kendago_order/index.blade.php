@extends('layouts.new_app')
@section('content')
<div class="right_col" role="main">
    <div class="">
      <div class="page-title">
        <div class="title_left">
          <h3>15manifest Kendago</h3>
        </div>

      </div>

      <div class="clearfix"></div>
      <h6>Last Update Time: {{ ($last_update_time !='')?date('Y-m-d',strtotime($last_update_time)).' | '.date('g:i A',strtotime($last_update_time)).' ('.date('e',strtotime($last_update_time)).')' : '' }}</h6>

      <div class="form-group">
        <label for="show_month">View selection</label>
        <select class="form-control" name="show_month" id="show_month">
          <option value="1" {{($view == '1')?'selected':''}}>Current Month + Last 6 Months</option>
          <option value="2" {{($view == '2')?'selected':''}}>Current Month + Last 12 Months</option>
          <option value="3" {{($view == '3')?'selected':''}}>Current Month + Last 24 Months</option>
        </select>
      </div>

      <div class="row">
        <div class="col-md-12 col-sm-12">
          <div class="x_panel">
            <div class="x_title">
              <h2>Revenue</h2>
              <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <div class="row">
                    <div class="col-md-12 col-12">
                      <canvas id="kendago_revenue_chart" style="height:20px !important;width:100px !important;"></canvas>
                    </div>
                </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12 col-sm-12">
          <div class="x_panel">
            <div class="x_title">
              <h2>Hop Count</h2>
              <div class="clearfix"></div>

            </div>
            <div class="x_content">
              <div class="row">
                <div class="col-md-12 col-12">
                  <canvas id="kendago_hop_count_chart" style="height:20px !important;width:100px !important;"></canvas>
                </div>
            </div>
          </div>
        </div>
      </div>
      </div>
      <div class="row">
      <div class="col-md-12 col-sm-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Initial Sales Count</h2>
            <div class="clearfix"></div>

          </div>
          <div class="x_content">
            <div class="row">
              <div class="col-md-12 col-12">
                <canvas id="kendago_initial_sales_count_chart" style="height:20px !important;width:100px !important;"></canvas>
              </div>
          </div>
        </div>
      </div>
    </div>
    </div>
    <div class="row">
      <div class="col-md-12 col-sm-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>FE Conversion Rate</h2>
            <div class="clearfix"></div>

          </div>
          <div class="x_content">
            <div class="row">
              <div class="col-md-12 col-12">
                <canvas id="kendago_fe_cvr_chart" style="height:20px !important;width:100px !important;"></canvas>
              </div>
          </div>
        </div>
      </div>
    </div>
    </div>
    <div class="row">
      <div class="col-md-12 col-sm-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Raw EPC</h2>
            <div class="clearfix"></div>

          </div>
          <div class="x_content">
            <div class="row">
              <div class="col-md-12 col-12">
                <canvas id="kendago_raw_epc_chart" style="height:20px !important;width:100px !important;"></canvas>
              </div>
          </div>
        </div>
      </div>
    </div>
    </div>

    <div class="row">
        <div class="col-md-12 col-sm-12">
          <div class="x_panel">
            <div class="x_title">
              <h2>Average Order Value (Vendor share)</h2>
              <div class="clearfix"></div>

            </div>
            <div class="x_content">
              <div class="row">
                <div class="col-md-12 col-12">
                  <canvas id="kendago_aov_chart" style="height:20px !important;width:100px !important;"></canvas>
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
      var ctx = document.getElementById('kendago_revenue_chart').getContext('2d');
      var chart = new Chart(ctx, {
          type: 'line',
          // ctx.canvas.parentNode.style.height = '300px';

          // The data for our dataset
          data: {
              labels: [{!!implode(',',$kendago['months'])!!}],
              datasets: [{
                  label: 'Revenue',
                  borderColor: 'rgb(149, 229, 149)',
                  fill:false,
                  lineTension: 0.1,
                  data: [{{implode(',',$kendago['revenue'])}}]
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
                            stepSize:2000,
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

        var ctx = document.getElementById('kendago_hop_count_chart').getContext('2d');
        var chart = new Chart(ctx, {
          type: 'line',
          // ctx.canvas.parentNode.style.height = '300px';

          // The data for our dataset
          data: {
              labels: [{!!implode(',',$kendago['months'])!!}],
              datasets: [{
                  label: 'Hop Count',
                  borderColor: 'rgb(149, 229, 149)',
                  fill:false,
                  lineTension: 0.1,
                  data: [{{implode(',',$kendago['hop_count'])}}]
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
                            stepSize:50000,
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
                            return result.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
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

        var ctx = document.getElementById('kendago_initial_sales_count_chart').getContext('2d');
        var chart = new Chart(ctx, {
          type: 'line',
          // ctx.canvas.parentNode.style.height = '300px';

          // The data for our dataset
          data: {
              labels: [{!!implode(',',$kendago['months'])!!}],
              datasets: [{
                  label: 'Initial Sales Count',
                  borderColor: 'rgb(149, 229, 149)',
                  fill:false,
                  lineTension: 0.1,
                  data: [{{implode(',',$kendago['initial_sales_count'])}}]
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
                            stepSize:2000,
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
                            return result.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
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

        var ctx = document.getElementById('kendago_fe_cvr_chart').getContext('2d');

      var chart = new Chart(ctx, {
          type: 'line',
          // ctx.canvas.parentNode.style.height = '300px';

          // The data for our dataset
          data: {
              labels: [{!!implode(',',$kendago['months'])!!}],
              datasets: [{
                  label: 'FE Conversion Rate',
                  borderColor: 'rgb(149, 229, 149)',
                  fill:false,
                  lineTension: 0.1,
                  data: [{{implode(',',$kendago['fe_cvr'])}}]
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
                            stepSize:2000,
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
                            var result =  tooltipItem.yLabel;
                            return result.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
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
                        padding: 0
                    }
                }
            },
        });

        var ctx = document.getElementById('kendago_raw_epc_chart').getContext('2d');

      var chart = new Chart(ctx, {
          type: 'line',
          // ctx.canvas.parentNode.style.height = '300px';

          // The data for our dataset
          data: {
              labels: [{!!implode(',',$kendago['months'])!!}],
              datasets: [{
                  label: 'Raw EPC',
                  borderColor: 'rgb(149, 229, 149)',
                  fill:false,
                  lineTension: 0.1,
                  data: [{{implode(',',$kendago['raw_epc'])}}]
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
                            stepSize:2000,
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
                            var result =  tooltipItem.yLabel;
                            return result.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
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
                        padding: 0
                    }
                }
            },
        });

            var ctx = document.getElementById('kendago_aov_chart').getContext('2d');

        var chart = new Chart(ctx, {
          type: 'line',
          // ctx.canvas.parentNode.style.height = '300px';

          // The data for our dataset
          data: {
              labels: [{!!implode(',',$kendago['months'])!!}],
              datasets: [{
                  label: 'Average Order Value (Vendor share)',
                  borderColor: 'rgb(149, 229, 149)',
                  fill:false,
                  lineTension: 0.1,
                  data: [{{implode(',',$kendago['aov'])}}]
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
                            stepSize:2000,
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
                            var result =  tooltipItem.yLabel;
                            return result.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
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
                        padding: 0
                    }
                }
            },
        });
    </script>
  @endpush
@endsection
