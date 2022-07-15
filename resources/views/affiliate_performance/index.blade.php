@extends('layouts.new_app')
@section('content')
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Affiliate Performance</h3>
        </div>
    </div>

    <div class="clearfix"></div>
    <h6>Last Update Time: {{ ($last_update_time !='')?date('Y-m-d',strtotime($last_update_time)).' | '.date('g:i A',strtotime($last_update_time)).' ('.date('e',strtotime($last_update_time)).')' : '' }}</h6>

    <div class="row">
        <div class="col-md-5">
            <div class="form-group">
                <label for="affiliate_select">Affiliate selection</label>
                <select class="form-control" name="affiliate_select1" id="affiliate_select1" multiple>
                    @foreach ($affiliate_list as $key => $affiliate)
                        <option value="{{$key}}" @if(in_array($key,$selected_affiliate_arr)) selected  @endif>{{$affiliate}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label for="affiliate_select1">&nbsp;</label>
                <button type="submit" class="align-self-end btn-block btn btn-info" id="btnshow"> Search </button>
            </div>
        </div>
        <div class="col-md-5">
            <div class="form-group">
                <label for="vendor_select">Vendor selection</label>
                <select class="form-control" name="vendor_select" id="vendor_select">
                    <option value="all-vendors">ALL VENDORS</option>
                    @foreach ($vendor_lists as $v)
                        <option value="{{$v}}" {{($selected_vendor == $v)?'selected':''}}>{{$v}}</option>
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
                <div class="tile_count">
                    <div class="col-md-3 tile_stats_count">
                        <span class="count_top">Total Net Revenue</span>
                        <div class="count">${{ number_format($vendor['total_net_revenue']['amount']) }}</div>
                    </div>

                    <div class="col-md-3 tile_stats_count">
                        <span class="count_top">Initial Revenue</span>
                        <div class="count">${{ number_format($vendor['net_sale_amount_val']['amount']) }}</div>
                        <span class="count_bottom"><i class="green">{{ $vendor['net_sale_amount_val']['percent'] }}% </i></span>
                    </div>

                    <div class="col-md-3 tile_stats_count">
                        <span class="count_top">Upsell Revenue</span>
                        <div class="count">${{ number_format($vendor['upsell']['amount']) }}</div>
                        <span class="count_bottom"><i class="green">{{ $vendor['upsell']['percent'] }}% </i></span>
                    </div>
                    <div class="col-md-3 tile_stats_count">
                        <span class="count_top">Rebill</span>
                        <div class="count">${{ number_format($vendor['rebill']['amount']) }}</div>
                        <span class="count_bottom"><i class="green">{{ $vendor['rebill']['percent'] }}% </i></span>
                    </div>
                </div>
            </div>
        </div>
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
                    <h2>Vendor EPC from Affiliate</h2>
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

                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="x_panel">

                            <div class="x_title">
                                <h2>Top Vendor</h2>
                                <div class="clearfix"></div>
                            </div>

                            <div class="x_content">
                                <?php $top_affiliates = App\Http\Controllers\AffiliatePerformanceController::getTopVendor($selected_affiliate,$vendor_lists);
                                $cnt = 1;
                                ?>
                                <div class="row mb-3">
                                    <div class="col-12 col-md-12">
                                        <div id="date_filter">
                                            <div class="row">
                                                <div class="col-12">
                                                    <form>
                                                        <div class="form-group row">
                                                            <label for="date-selection" class="col-sm-2 col-form-label">Date :</label>
                                                            <div class="col-sm-10">
                                                                <select class="form-control" id="date-selection">
                                                                    <option value="1">Today</option>
                                                                    <option value="2">Yesterday</option>
                                                                    <option value="3">Last 7 Days</option>
                                                                    <option value="4">Last 14 Days</option>
                                                                    <option value="5">This Month</option>
                                                                    <option value="6">Last 30 days</option>
                                                                    <option value="7">Last 60 days</option>
                                                                    <option value="8">Custom Range</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>

                                                <div class="custom-range-filter col-md-12 d-none">
                                                    <div class="row">
                                                        <div class="col-md-2"></div>
                                                        <div class="col-md-10">
                                                            <div class="row">
                                                            <div class="col-md-6">
                                                                <input type="text" name="datefilter" id="datefilter" value="" class="date_range_filter date hasDatepicker form-control" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%" />
                                                                <input type="text" id="min" name="min" style="display: none"><input type="text" id="max" name="max" style="display: none">
                                                            </div>
                                                                <div class="col-md-6">
                                                                    <button class="btn btn-primary search_date_button" id="btnSearchTopVendors">Search</button>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>

                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive">

                                    <table id="example" class="table table-striped table-bordered display nowrap" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th style="width:140px;">Affiliate ID</th>
                                                <th style="width:140px;">Vendor ID</th>
                                                <th>Hop Count</th>
                                                <th>Aff. EPC</th>
                                                <th>FE CVR</th>
                                                <th>Estimate Aff. Comms</th>
                                                <th>Vendor Rev.</th>
                                            </tr>
                                        </thead>
                                        <tbody id="showTopVendors">
                                        </tbody>
                                        <tfoot>
                                        </tfoot>
                                    </table>
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

    <!-- for filters -->
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/2.1.24/daterangepicker.min.css'>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/datetime/1.0.3/css/dataTables.dateTime.min.css">
    <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/datetime/1.0.3/js/dataTables.dateTime.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <!-- End: Filters-->


    <script type="text/javascript">
    var view = $('#show_month :selected').val();//"<?= $view ?>";
    var selected_vendor = "<?= $selected_vendor ?>";
    var selected_affiliate = "<?= $selected_affiliate ?>";
    var selView = $('#show_month :selected').val();
    console.log('-------- Slected View---------');
    console.log(selView);

        $("#vendor_select").select2();
        $("#show_month").select2();
        $("#affiliate_select1").select2({
            tags: true,
            maximumSelectionSize: 12,
            minimumResultsForSearch: Infinity,
            multiple: true,
            minimumInputLength: 0,
            placeholder: "Search Affiliate",

        });

        $("#btnshow").click(function(){
            var affValues = $("#affiliate_select1").val();
            encodedAffURL = encodeURIComponent(window.btoa(affValues));
            refreshVendorData(encodedAffURL);
        });
        $(function() {
            $('#date-selection').val( 3 ).trigger('change');;
        });

        $("#vendor_select").change(function(){
            selected_vendor = this.value;

            var affValues = $("#affiliate_select1").val();
            encodedAffURL = encodeURIComponent(window.btoa(affValues));

            if(selected_vendor=='all-vendors')
                refreshVendorData(encodedAffURL)
            else
                refreshData(encodedAffURL);
        });

        $("#show_month").change(function(){

            var affValues = $("#affiliate_select1").val();
            encodedAffURL = encodeURIComponent(window.btoa(affValues));

            view = $(this).children("option:selected").val();
            refreshVendorData(encodedAffURL);
        });

        function refreshData(encURL){
            var view2 = $('#show_month :selected').val();
            var origin = getBaseURL();

            var url = origin+'affiliate-performance/'+view2+'/'+encURL+'/'+selected_vendor;

            location.href = url;

        }

        function refreshVendorData(encURL){
            var view1 = $('#show_month :selected').val();
            var origin = getBaseURL();
            var url = origin+'affiliate-performance/'+view1+'/'+encURL;

            location.href = url;
        }

        // to get base url of the page
        function getBaseURL(){
            var url=window.location.href;
            var arr=url.split('affiliate-performance')[0];
            return arr;
        }
        //########### end : Drop down ###############

        // Comman configuration
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

        var options_for_rate = {
            responsive: true,
            maintainAspectRatio: false,
            bezierCurve: false,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: false,
                        stepSize:20000,
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

        // Net revenue chart
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
                    data: [{{implode(',',$vendor['net_sale_amount_graph'])}}]
                }]
            },

            // Configuration options go here
            options: options_for_revenue,
        });

        // For Refund rate chart
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

        var ctx = document.getElementById('vendor_vendor_epc_chart').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'line',
            // The data for our dataset
            data: {
                labels: [{!!implode(',',$vendor['months'])!!}],
                datasets: [{
                    label: 'Vendor EPC from Affiliate',
                    borderColor: 'rgb(149, 229, 149)',
                    fill:false,
                    lineTension: 0.1,
                    data: [{{implode(',',$vendor['affiliate_epc'])}}]
                }]
            },

            // Configuration options go here
            options: options_for_epc,
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

        // Filters
        var minDate, maxDate;

// Custom filtering function which will search data in column four between two values
$.fn.dataTable.ext.search.push(
    function( settings, data, dataIndex ) {
        var min = minDate.val();
        var max = maxDate.val();
        var date = new Date( data[4] );

        if (
            ( min === null && max === null ) ||
            ( min === null && date <= max ) ||
            ( min <= date   && max === null ) ||
            ( min <= date   && date <= max )
        ) {
            return true;
        }
        return false;
    }
);

$(document).ready(function() {

    // Create date inputs
    minDate = new DateTime($('#min'), {
        format: 'YYYY-MM-DD'
    });
    maxDate = new DateTime($('#max'), {
        format: 'YYYY-MM-DD'
    });

    // Refilter the table
    $('#min, #max').on('change', function () {
       // table.draw();
    });
     // Refilter the table
    $('#max').on('focus', function () {
    });
    var minDateFilter = '2021-05-07';
    var maxDateFilter = '2021-05-07';

    $("#date-selection").change(function() {
                view = this.value;

                 maxDateFilter = moment().format("YYYY-MM-DD");
                 $(".custom-range-filter").addClass('d-none');
                if (view == 1) {
                    minDateFilter = maxDateFilter;
                    //refreshData();
                } else if (view == 2) {
                    minDateFilter = moment().subtract(1, 'days').format("YYYY-MM-DD");
                    maxDateFilter = minDateFilter;
                   // refreshData();
                } else if (view == 3) {
                    minDateFilter = moment().subtract(7, 'days').format("YYYY-MM-DD");
                    maxDateFilter = moment().subtract(1, 'days').format("YYYY-MM-DD");
                   // refreshData();
                } else if (view == 4) {
                    minDateFilter = moment().subtract(14, 'days').format("YYYY-MM-DD");
                    maxDateFilter = moment().subtract(1, 'days').format("YYYY-MM-DD");
                } else if (view == 5) {
                    minDateFilter = moment().format("YYYY-MM-01");
                    maxDateFilter = moment().subtract(1, 'days').format("YYYY-MM-DD");
                } else if (view == 6) {
                    minDateFilter = moment().subtract(30, 'days').format("YYYY-MM-DD");
                    maxDateFilter = moment().subtract(1, 'days').format("YYYY-MM-DD");
                } else if (view == 7) {
                    minDateFilter = moment().subtract(60, 'days').format("YYYY-MM-DD");
                    maxDateFilter = moment().subtract(1, 'days').format("YYYY-MM-DD");
                } else if (view == 8) {
                    $(".custom-range-filter").removeClass('d-none');
                    $("#min").val("");
                    $("#max").val("");
                }


   });
});

$("#date-selection55555555").change(function() {

    console.log("tes");
				$('#example').DataTable({
					'processing': true,
					'serverSide': true,
					'order': [[ 3, 'desc' ]],
					'destroy': true,
					'pageLength': 50,
					'lengthMenu': [[5, 10, 20, -1], [5, 10, 20, "Todos"]],
					'serverMethod': 'post',
					'ajax': {
						'url':"{{ url('/affiliate-performance/gettopvendor') }}",
						'type': 'POST',
						'data' : {
							"cmd" : "refresh",
							"from": "2021",
							"to"  : "2020"
						},
					},

					'columns': [
						{ data: 'emp_name' },
						{ data: 'email' },
						{ data: 'gender' },
						{ data: 'salary' },
						{ data: 'city' },
					]
				});
			});



$("#date-selection").change(function (e) {

        view = this.value;

        minDateFilter = moment().format("YYYY-MM-DD");
        maxDateFilter = moment().format("YYYY-MM-DD");

        if (view == 1) {
            minDateFilter = maxDateFilter;
            //refreshData();
        } else if (view == 2) {
            minDateFilter = moment().subtract(1, 'days').format("YYYY-MM-DD");
            maxDateFilter = minDateFilter;
            // refreshData();
        } else if (view == 3) {
            minDateFilter = moment().subtract(7, 'days').format("YYYY-MM-DD");
            maxDateFilter = moment().subtract(1, 'days').format("YYYY-MM-DD");
            // refreshData();
        } else if (view == 4) {
            minDateFilter = moment().subtract(14, 'days').format("YYYY-MM-DD");
            maxDateFilter = moment().subtract(1, 'days').format("YYYY-MM-DD");
        } else if (view == 5) {
            minDateFilter = moment().format("YYYY-MM-01");
            maxDateFilter = moment().subtract(1, 'days').format("YYYY-MM-DD");
        } else if (view == 6) {
            minDateFilter = moment().subtract(30, 'days').format("YYYY-MM-DD");
            maxDateFilter = moment().subtract(1, 'days').format("YYYY-MM-DD");
        } else if (view == 7) {
            minDateFilter = moment().subtract(60, 'days').format("YYYY-MM-DD");
            maxDateFilter = moment().subtract(1, 'days').format("YYYY-MM-DD");
        } else {

        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
        e.preventDefault();
        var formData = {
            selected_affiliate: "{{$selected_affiliate}}", //$selected_affiliate,$vendor_lists
            vendor_lists :"{{ implode(',',$vendor_lists) }}",
            startdate : minDateFilter,
            enddate : maxDateFilter,
        };

        console.log("--- REQUEST Params -----")
        console.log(formData);

        if(view <=7) {
            var result="";

            $('#example').DataTable({
                'processing': true,
                'searching': false,
                'bLengthChange': false,
                'order': [[ 6, 'desc' ]],
                'destroy': true,
                'pageLength': 50,
                'lengthMenu': [[5, 10, 20, -1], [5, 10, 20, "Todos"]],
                'ajax': {
                    'url':"{{ url('/affiliate-performance/gettopvendor') }}",
                    'type': 'POST',
                    'data': formData,
                },
                'columns': [
                    { data: 'affiliate' },
                    { data: 'account' },
                    { data: 'hop_count' },
                    { data: 'affiliate_epc', render(data){
                            return Number(data).toFixed(2)}   },
                    { data: 'fe_cvr' , render(data){
                            return Number(data).toFixed(2)}   },
                    { data: 'affiliate_comms' , render(data){
                            return  Intl.NumberFormat('en-US').format(Math.round(data)) }   },
                    { data: 'net_sale_amount' , render(data){
                            return  Intl.NumberFormat('en-US').format(Math.round(data)) }   },

                ]
            });

        }
    });

    $('#btnSearchTopVendors').click(function (e) {

        e.preventDefault();
        var formData = {
            selected_affiliate: "{{$selected_affiliate}}", //$selected_affiliate,$vendor_lists
            vendor_lists :"{{ implode(',',$vendor_lists) }}",
            startdate :  $("#min").val() ,
            enddate :  $("#max").val(),
        };

        var result="";

        $('#example').DataTable({
            'processing': true,
            'searching': false,
            'bLengthChange': false,
            'order': [[ 6, 'desc' ]],
            'destroy': true,
            'pageLength': 50,
			'lengthMenu': [[5, 10, 20, -1], [5, 10, 20, "Todos"]],
            'ajax': {
                'url':"{{ url('/affiliate-performance/gettopvendor') }}",
                'type': 'POST',
                'data': formData,
            },
            'columns': [
                { data: 'affiliate' },
                { data: 'account' },
                { data: 'hop_count' },
                { data: 'affiliate_epc', render(data){
                        return Number(data).toFixed(2)}   },
                { data: 'fe_cvr' , render(data){
                        return Number(data).toFixed(2)}   },
                { data: 'affiliate_comms' , render(data){
                        return  Intl.NumberFormat('en-US').format(Math.round(data)) }   },
                { data: 'net_sale_amount' , render(data){
                        return  Intl.NumberFormat('en-US').format(Math.round(data)) }   },

            ]
        });

    });

            $(function() {
                var start = moment().subtract(29, 'days');
                var end = moment();
                function cb(start, end) {
                    $('#datefilter span').html(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
                }
                $('input[name="datefilter"]').daterangepicker({
                    startDate: start,
                        endDate: end,
                    autoUpdateInput: false,
                    locale: {
                        cancelLabel: 'Clear',
                        format: "YYYY-MM-DD",
                        separator: " to ",
                        cancelLabel: "Clear",
                        customRangeLabel: "Custom",
                    }
                },cb);
                cb(start, end);

                $('input[name="datefilter"]').on('apply.daterangepicker', function(ev, picker) {
                    $(this).val(picker.startDate.format('YYYY-MM-DD') + ' to ' + picker.endDate.format('YYYY-MM-DD'));
                    $("#min").val(picker.startDate.format('YYYY-MM-DD'));
                    $("#max").val(picker.endDate.format('YYYY-MM-DD'));
                });

                $('input[name="datefilter"]').on('cancel.daterangepicker', function(ev, picker) {
                    $(this).val('');
                });

        });
    </script>
@endpush
@endsection
