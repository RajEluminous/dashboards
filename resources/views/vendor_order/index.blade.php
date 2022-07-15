@extends('layouts.new_app')
@section('content')
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Vendor Order</h3>
        </div>
    </div>

    <div class="clearfix"></div>
    <h6>Last Update Time: {{ ($last_update_time !='')?date('Y-m-d',strtotime($last_update_time)).' | '.date('g:i A',strtotime($last_update_time)).' ('.date('e',strtotime($last_update_time)).')' : '' }}</h6>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="vendor_select">Vendor selection</label>
                <select class="form-control" name="vendor_select" id="vendor_select">
                    @foreach ($vendor_list as $v)
                        <option value="{{$v}}" {{($selected_vendor == $v)?'selected':''}}>{{$v}}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="affiliate_select">Affiliate selection</label>
                <select class="form-control" name="affiliate_select" id="affiliate_select">
                    <option value="all-affiliate" {{($selected_affiliate == "all-affiliate")?'selected':''}}>ALL AFFILIATE</option>
                    @foreach ($affiliate_list as $affiliate)
                        <option value="{{$affiliate->affiliate}}" {{($selected_affiliate == $affiliate->affiliate)?'selected':''}}>{{$affiliate->affiliate}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

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
                    <h2>Net Revenue ($)</h2>
                    <div class="clearfix"></div>
                </div>

                <div class="x_content">
                    <div class="row">
                        <div class="col-md-12 col-12">
                            <canvas id="vendor_revenue_chart" style="height:20px !important;width:100px !important;"></canvas>
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
                    <h2>Refund Rate (%)</h2>
                    <div class="clearfix"></div>
                </div>

                <div class="x_content">
                    <div class="row">
                        <div class="col-md-12 col-12">
                            <canvas id="vendor_refund_rate_chart" style="height:20px !important;width:100px !important;"></canvas>
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
                    <h2>FE Conversion Rate (%)</h2>
                    <div class="clearfix"></div>
                </div>

                <div class="x_content">
                    <div class="row">
                        <div class="col-md-12 col-12">
                            <canvas id="vendor_fe_conversion_rate_chart" style="height:20px !important;width:100px !important;"></canvas>
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
                            <canvas id="vendor_initial_sales_count_chart" style="height:20px !important;width:100px !important;"></canvas>
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
                            <canvas id="vendor_hop_count_chart" style="height:20px !important;width:100px !important;"></canvas>
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
                    <h2>Vendor EPC</h2>
                    <div class="clearfix"></div>
                </div>

                <div class="x_content">
                    <div class="row">
                        <div class="col-md-12 col-12">
                            <canvas id="vendor_vendor_epc_chart" style="height:20px !important;width:100px !important;"></canvas>
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
                    <h2>Active Affiliates</h2>
                    <div class="clearfix"></div>
                </div>

                <div class="x_content">
                    <div class="row">
                        <div class="col-md-12 col-12">
                            <canvas id="vendor_active_affiliates_chart" style="height:20px !important;width:100px !important;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Active Affilites (Current Month)</h2>
                    <div class="clearfix"></div>
                </div>

                <div class="x_content">
                    <div class="row">
                        <div class="col-md-12 col-12">
                            <div class="table-responsive">
                                <table id="active_affiliates" class="display table table-striped table-bordered" style="width:100%">
                                    <thead>
                                        <tr>
                                        <th>Affiliate</th>
                                        <th>Initial Sales Count</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($vendor['active_affiliates'] as $aa)
                                            <tr>
                                                <td>{{$aa->affiliate}}</td>
                                                <td>{{$aa->initial_sales_count}}</td>
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
    </div> --}}

    <div class="row">
        <div class="col-md-12 col-sm-12">
            @for($i = 1;$i<= 4; $i++)
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="x_panel">
                            @if($i == 1)
                                <div class="x_title">
                                    <h2>Top Affiliate</h2>
                                    <div class="clearfix"></div>
                                </div>
                            @endif

                            <div class="x_content">
                                <h4>{{ $top_affiliate_label[$i-1] }}</h4>
                                <?php $top_affiliates = App\Http\Controllers\VendorOrderController::getTopAffiliate($i,$selected_vendor); ?>

                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered top_affiliates">
                                        <thead>
                                            <tr class="text-center">
                                                <th>#</th>
                                                <th style="width:140px;">Affiliate ID</th>
                                                <th>Hop Count</th>
                                                <th>Aff. EPC</th>
                                                <th>FE CVR</th>
                                                <th>Aff. Comms</th>
                                                <th>Vendor Rev.</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($top_affiliates as $ta)
                                                <tr class="text-center">
                                                    <td>{{$loop->iteration}}</td>
                                                    <td>{{$ta->affiliate_id}}</td>
                                                    <td>{{number_format($ta->hop_count)}}</td>
                                                    <td>{{$ta->affiliate_epc}}</td>
                                                    <td>{{number_format($ta->fe_cvr,2)}}%</td>
                                                    <td>{{number_format(ceil($ta->affiliate_revenue))}}</td>
                                                    <td>{{number_format(ceil($ta->vendor_revenue))}}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endfor
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0/dist/chartjs-plugin-datalabels.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

    <script type="text/javascript">
    var view = "<?= $view ?>";
    var selected_vendor = "<?= $selected_vendor ?>";
    var selected_affiliate = "<?= $selected_affiliate ?>";

        $("#vendor_select").select2();
        $("#show_month").select2();
        $("#affiliate_select").select2();

        $('#active_affiliates').DataTable({
        order:[[1,"desc"]],
        });

        $('.top_affiliates').DataTable({
        "searching": false,
        "lengthChange": false,
        "bPaginate": false,
        "aoColumns": [
            null,
            null,
            { "orderSequence": [ "desc", "asc"] },
            { "orderSequence": [ "desc", "asc"] },
            { "orderSequence": [ "desc", "asc"] },
            { "orderSequence": [ "desc", "asc"] },
            { "orderSequence": [ "desc", "asc"] },
        ],
        "bInfo" : false,
        order:[[6,"desc"]],
        columnDefs: [
            {bSortable: false, targets: [0,1]}
        ]
        });

        $("#show_month").change(function(){
            view = $(this).children("option:selected").val();
            refreshData();
        });

        var options_for_revenue = {
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
        };

        var options_for_count = {
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
        };

        var options_for_rate = {
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
                        return result.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")+'%';
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
        };

        var options_for_epc = {
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
                        // var result =  Math.round(tooltipItem.yLabel);
                        return '$' + tooltipItem.yLabel.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
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
                }
            }
        };

        var ctx = document.getElementById('vendor_revenue_chart').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'line',
            // The data for our dataset
            data: {
                labels: [{!!implode(',',$vendor['months'])!!}],
                datasets: [{
                    label: 'Net Revenue',
                    borderColor: 'rgb(149, 229, 149)',
                    fill:false,
                    lineTension: 0.1,
                    data: [{{implode(',',$vendor['revenue'])}}]
                }]
            },

            // Configuration options go here
            options: options_for_revenue,
        });

        var ctx = document.getElementById('vendor_refund_rate_chart').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'line',
            // The data for our dataset
            data: {
                labels: [{!!implode(',',$vendor['months'])!!}],
                datasets: [{
                    label: 'Refund Rate',
                    borderColor: 'rgb(149, 229, 149)',
                    fill:false,
                    lineTension: 0.1,
                    data: [{{implode(',',$vendor['refund_rate'])}}]
                }]
            },

            // Configuration options go here
            options: options_for_rate,
        });

        var ctx = document.getElementById('vendor_fe_conversion_rate_chart').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'line',
            // The data for our dataset
            data: {
                labels: [{!!implode(',',$vendor['months'])!!}],
                datasets: [{
                    label: 'FE Conversion Rate',
                    borderColor: 'rgb(149, 229, 149)',
                    fill:false,
                    lineTension: 0.1,
                    data: [{{implode(',',$vendor['fe_cvr'])}}]
                    }]
                },

            // Configuration options go here
            options: options_for_rate,
        });

        var ctx = document.getElementById('vendor_initial_sales_count_chart').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'line',
            // The data for our dataset
            data: {
                labels: [{!!implode(',',$vendor['months'])!!}],
                datasets: [{
                    label: 'Initial Sales Count',
                    borderColor: 'rgb(149, 229, 149)',
                    fill:false,
                    lineTension: 0.1,
                    data: [{{implode(',',$vendor['initial_sales_count'])}}]
                    }]
                },

            // Configuration options go here
            options: options_for_count,
        });

        var ctx = document.getElementById('vendor_hop_count_chart').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'line',
            // The data for our dataset
            data: {
                labels: [{!!implode(',',$vendor['months'])!!}],
                datasets: [{
                    label: 'Hop Count',
                    borderColor: 'rgb(149, 229, 149)',
                    fill:false,
                    lineTension: 0.1,
                    data: [{{implode(',',$vendor['hop_count'])}}]
                    }]
                },

            // Configuration options go here
            options: options_for_count,
        });

        var ctx = document.getElementById('vendor_vendor_epc_chart').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'line',
            // The data for our dataset
            data: {
                labels: [{!!implode(',',$vendor['months'])!!}],
                datasets: [{
                    label: 'Vendor EPC',
                    borderColor: 'rgb(149, 229, 149)',
                    fill:false,
                    lineTension: 0.1,
                    data: [{{implode(',',$vendor['vendor_epc'])}}]
                }]
            },

            // Configuration options go here
            options: options_for_epc,
        });

        var ctx = document.getElementById('vendor_active_affiliates_chart').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'line',
            // The data for our dataset
            data: {
                labels: [{!!implode(',',$vendor['months'])!!}],
                datasets: [{
                    label: 'Active Affiliates',
                    borderColor: 'rgb(149, 229, 149)',
                    fill:false,
                    lineTension: 0.1,
                    data: [{{implode(',',$vendor['active_affiliates'])}}]
                }]
            },

            // Configuration options go here
            options: options_for_count,
        });

        $("#affiliate_select").change(function(){
            selected_affiliate = this.value;
            refreshData();
        });

        $("#vendor_select").change(function(){
            selected_vendor = this.value;
            refreshVendorData();
        });

        function refreshData(){

            var origin = getBaseURL();
            //console.log('origin:'+origin);
            var url = origin+'vendor-order/'+view+'/'+selected_vendor+'/'+selected_affiliate;
            location.href = url;
            //console.log(url);
        }

        function refreshVendorData(){
            var origin = getBaseURL();
            var url = origin+'vendor-order/'+view+'/'+selected_vendor;
            //console.log(url);
            location.href = url;
        }

        // to get base url of the page
        function getBaseURL(){
            var url=window.location.href;
            var arr=url.split('vendor-order')[0];
            return arr;
        }

    </script>
@endpush
@endsection
