@extends('layouts.new_app')
@section('content')
<div class="right_col system_dashboard" role="main">
    <div class="">
      <div class="page-title">
        <div class="title_left">
          <h3>System Dashboard</h3>
        </div>

      </div>

      <div class="clearfix"></div>

      <div class="row">
        <div class="col-md-12 col-sm-12">
          <div class="x_panel">
            <div class="x_title">
              <h2>AWeber Tools</h2>
              <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <div class="row">
                    <div class="col-md-3">
                      <div class="card mb-3">
                        <div class="card-header">
                          <h4 class="text-success">Integrate</h4>
                        </div>
                        <div class="card-body">
                          <p class="card-text">Integrate AWeber Accounts To Use AWeber API</p>
                        </div>
                        <div class="card-footer text-center">
                          <a href="{{ url('/aweber/integrate') }}" class="btn btn-success">Integrate</a>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="card mb-3">
                        <div class="card-header">
                          <h4 class="text-success">Refresh Token</h4>
                        </div>
                        <div class="card-body">
                          <p class="card-text">Each 1 hours will refresh</p>
                        </div>
                        <div class="card-footer text-center">
                          <a href="{{ url('/aweber/refresh-token') }}" class="btn btn-success">Refresh Token</a>
                        </div>
                      </div>
                    </div>
                    {{-- <div class="col-md-3">
                      <div class="card mb-3">
                        <div class="card-header">
                          <h4 class="text-success">Screenshot And Send</h4>
                        </div>
                        <div class="card-body">
                          <p class="card-text">Generate PDF and Send
                            (Don Simply Click! Email not only send to Andrew)</p>
                        </div>
                        <div class="card-footer text-center">
                          <a href="{{ url('/screenshot-and-send') }}" class="btn btn-success">Generate</a>
                        </div>
                      </div>
                    </div> --}}
                    {{-- <div class="col-md-3">
                      <div class="card mb-3">
                        <div class="card-header">
                          <h4 class="text-success">Get Product API</h4>
                        </div>
                        <div class="card-body">
                          <p class="card-text">Check Product API</p>
                        </div>
                        <div class="card-footer text-center">
                          <a href="{{ url('/get-product-api') }}" class="btn btn-success">Generate</a>
                        </div>
                      </div>
                    </div> --}}
                    {{-- <div class="col-md-3">
                      <div class="card mb-3">
                        <div class="card-header">
                          <h4 class="text-success">Browsershot Test</h4>
                        </div>
                        <div class="card-body">
                          <p class="card-text">Browsershot Test</p>
                        </div>
                        <div class="card-footer text-center">
                          <a href="{{ url('/browsershot-test') }}" class="btn btn-success">Test</a>
                        </div>
                      </div>
                    </div> --}}
                    {{-- <div class="col-md-3"><a href="{{ url('/read-csv-and-process') }}" class="btn btn-info">Read CSV and proccess</a></div> --}}
                </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12 col-sm-12">
          <div class="x_panel">
            <div class="x_title">
              <h2>Email Reporting Tools</h2>
              <div class="clearfix"></div>
            </div>
            <div class="x_content">
              <div class="row">
                  <div class="col-md-3">
                    <div class="card mb-3">
                      <div class="card-header">
                        <h4 class="text-danger">Send Affiliate Revenue PDF</h4>
                      </div>
                      <div class="card-body">
                        <p class="card-text">Generate Affiliate Revenue PDF and Send</p>
                      </div>
                      <div class="card-footer text-center">
                        <a href="{{ url('/send-affiliate-revenue-report') }}" class="btn btn-danger">Send Report</a>
                      </div>
                    </div>
                  </div>
                    <div class="col-md-3">
                      <div class="card mb-3">
                        <div class="card-header">
                          <h4 class="text-danger">Send List Growth PDF</h4>
                        </div>
                        <div class="card-body">
                          <p class="card-text">Generate List Growth PDF and Send</p>
                        </div>
                        <div class="card-footer text-center">
                          <a href="{{ url('/send-list-growth-report') }}" class="btn btn-danger">Send Report</a>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="card mb-3">
                        <div class="card-header">
                          <h4 class="text-danger">Send Sales By TID PDF</h4>
                        </div>
                        <div class="card-body">
                          <p class="card-text">Generate Sales By TID PDF and Send</p>
                        </div>
                        <div class="card-footer text-center">
                          <a href="{{ url('/send-sales-by-tid-report') }}" class="btn btn-danger">Send Report</a>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card mb-3">
                          <div class="card-header">
                            <h4 class="text-danger">Send New Customers Email</h4>
                          </div>
                          <div class="card-body">
                            <p class="card-text">Generate Last Month New Customer's List and Send</p>
                          </div>
                          <div class="card-footer text-center">
                            <a href="{{ url('/send-new-customers-list-report') }}" class="btn btn-danger">Send Report</a>
                          </div>
                        </div>
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
              <h2>Import Data Tools</h2>
              <div class="clearfix"></div>
            </div>
            <div class="x_content">
              <div class="row">
                    <div class="col-md-3">
                      <div class="card mb-3">
                        <div class="card-header">
                          <h4 class="text-primary">Get Affiliate Revenue Data</h4>
                        </div>
                        <div class="card-body">
                          <p class="card-text">Affiliate Revenue Data</p>
                        </div>
                        <div class="card-footer text-center">
                          <a href="{{ url('/get-affiliate-revenue-data') }}" class="btn btn-primary">Get Data</a>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="card mb-3">
                        <div class="card-header">
                          <h4 class="text-primary">Get Sales Ranking Data</h4>
                        </div>
                        <div class="card-body">
                          <p class="card-text">Sales Ranking Data</p>
                        </div>
                        <div class="card-footer text-center">
                          <a href="{{ url('/get-sales-ranking-data') }}" class="btn btn-primary">Get Data</a>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3">
                    <div class="card mb-3">
                      <div class="card-header">
                        <h4 class="text-primary">Get Incoming Traffic Status Data</h4>
                      </div>
                      <div class="card-body">
                        <p class="card-text">Incoming Traffic Status Data</p>
                      </div>
                      <div class="card-footer text-center">
                        <a href="{{ url('/get-incoming-traffic-status-data') }}" class="btn btn-primary">Get Data</a>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="card mb-3">
                      <div class="card-header">
                        <h4 class="text-primary">Get Top Affiliate Data</h4>
                      </div>
                      <div class="card-body">
                        <p class="card-text">Top Affiliate Data</p>
                      </div>
                      <div class="card-footer text-center">
                        <a href="{{ url('/get-top-affiliate-data') }}" class="btn btn-primary">Get Data</a>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="card mb-3">
                      <div class="card-header">
                        <h4 class="text-primary">Get List Growth Data</h4>
                      </div>
                      <div class="card-body">
                        <p class="card-text">List Growth Data</p>
                      </div>
                      <div class="card-footer text-center">
                        <a href="{{ url('/aweber/get-list-growth-data') }}" class="btn btn-primary">Get Data</a>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="card mb-3">
                      <div class="card-header">
                        <h4 class="text-primary">Get Sales By TID Data</h4>
                      </div>
                      <div class="card-body">
                        <p class="card-text">Sales By TID Data</p>
                      </div>
                      <div class="card-footer text-center">
                        <a href="{{ url('/get-sales-by-ars-data') }}" class="btn btn-primary">Get Data</a>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="card mb-3">
                      <div class="card-header">
                        <h4 class="text-primary">Get Analytics Data</h4>
                      </div>
                      <div class="card-body">
                        <p class="card-text">Affiliate Performance Data</p>
                      </div>
                      <div class="card-footer text-center">
                        <a href="{{ url('/get-analytics-data') }}" class="btn btn-primary">Get Data</a>
                      </div>
                    </div>
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
              <h2>By Account Data Tools</h2>
              <div class="clearfix"></div>
            </div>
            <div class="x_content">
              <div class="row">
                <div class="col-md-3">
                    <div class="card mb-3">
                      <div class="card-header">
                        <h4 class="text-secondary">Get Kendago Data</h4>
                      </div>
                      <div class="card-body">
                        <p class="card-text">Get Kendago Data</p>
                      </div>
                      <div class="card-footer text-center">
                        <a href="{{ url('/get-kendago-data') }}" class="btn btn-secondary">Get Data</a>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="card mb-3">
                      <div class="card-header">
                        <h4 class="text-secondary">Get Kendago Order Data</h4>
                      </div>
                      <div class="card-body">
                        <p class="card-text">Get Kendago Order Data</p>
                      </div>
                      <div class="card-footer text-center">
                        <a href="{{ url('/get-kendago-order-data') }}" class="btn btn-secondary">Get Data</a>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="card mb-3">
                      <div class="card-header">
                        <h4 class="text-secondary">Get RFS Order Data</h4>
                      </div>
                      <div class="card-body">
                        <p class="card-text">Get RFS Order Data</p>
                      </div>
                      <div class="card-footer text-center">
                        <a href="{{ url('/get-rfs-order-data') }}" class="btn btn-secondary">Get Data</a>
                      </div>
                    </div>
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
              <h2>Vendor Order Tools</h2>
              <div class="clearfix"></div>
            </div>
            <div class="x_content">
              <div class="row">
                <div class="col-md-3">
                    <div class="card mb-3">
                      <div class="card-header">
                        <h4 class="text-info">Get Newly Add Vendor Order Data</h4>
                      </div>
                      <div class="card-body">
                        <p class="card-text">Get Newly Add Vendor Order Data</p>
                      </div>
                      <div class="card-footer text-center">
                        <a href="{{ url('/get-newly-add-vendor-order-data') }}" class="btn btn-info">Get Data</a>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="card mb-3">
                      <div class="card-header">
                        <h4 class="text-info">Get Newly Add Vendor Hop Count Data</h4>
                      </div>
                      <div class="card-body">
                        <p class="card-text">Get Newly Add Vendor Hop Count Data</p>
                      </div>
                      <div class="card-footer text-center">
                        <a href="{{ url('/get-newly-add-hopcount-data') }}" class="btn btn-info">Get Data</a>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="card mb-3">
                      <div class="card-header">
                        <h4 class="text-info">Get Vendor Order Data</h4>
                      </div>
                      <div class="card-body">
                        <p class="card-text">Get Vendor Order Data</p>
                      </div>
                      <div class="card-footer text-center">
                        <a href="{{ url('/get-vendor-order-data') }}" class="btn btn-info">Get Data</a>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="card mb-3">
                      <div class="card-header">
                        <h4 class="text-info">Get Vendor Hop Count Data</h4>
                      </div>
                      <div class="card-body">
                        <p class="card-text">Get Vendor Hop Count Data</p>
                      </div>
                      <div class="card-footer text-center">
                        <a href="{{ url('/get-vendor-hopcount-data') }}" class="btn btn-info">Get Data</a>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="card mb-3">
                      <div class="card-header">
                        <h4 class="text-info">Get Vendor Top Affiliate Data</h4>
                      </div>
                      <div class="card-body">
                        <p class="card-text">Get Vendor Top Affiliate Data</p>
                      </div>
                      <div class="card-footer text-center">
                        <a href="{{ url('/get-vendor-top-affiliate-data') }}" class="btn btn-info">Get Data</a>
                      </div>
                    </div>
                  </div>
              </div>
            </div>
          </div>
        </div>
    </div>
  </div>
@endsection
