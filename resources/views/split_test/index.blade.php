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
        <div class="col-md-12">
            <div class="form-group">
                <label for="account_select">Account selection</label>
                <select class="form-control" name="account_select" id="account_select">
                  <option value="all-account" {{($selected_account == "all-account")?'selected':''}}>All Account</option>
                    @foreach ($account_list as $al)
                        <option value="{{$al}}" {{($selected_account == $al)?'selected':''}}>{{$al}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

      <div class="row">
            @foreach ($records as $rows)

            @foreach($rows as $data)
            <div class="col-md-12 col-sm-12">
              <div class="x_panel">
              <div class="x_title">
                <h6>{{ ($loop->index == 0)?$data['account'] ?? '':'' }}</h6>
                <h6>{{$data['campaign_name']??'Split Test'}}</h6>
                <div class="clearfix"></div>
              </div>
              <div class="x_content">
                <div class="table-responsive">
                  <table class="table table-bordered table-striped">
                    <thead>
                      <tr class="text-center">
                        <th style="vertical-align : middle;text-align:center;">Campaign Name</th>
                        <th style="vertical-align : middle;text-align:center;">Conversion</th>
                        <th style="vertical-align : middle;text-align:center;">Visitors</th>
                        <th style="vertical-align : middle;text-align:center;">Total Conversion</th>
                        <th style="vertical-align : middle;text-align:center;">Result</th>
                        <th style="vertical-align : middle;text-align:center;">Significant</th>
                      </tr>
                    </thead>
                    <tbody>
                        @foreach($data['rows']['ctrl']['results'] as $r)
                        <tr class="text-center">
                            <td style="vertical-align : middle;text-align:center;">{{$r['LP']}}</td>
                            <td style="vertical-align : middle;text-align:center;">{{$r['Conversion']}}</td>

                            @if($loop->index == 0)
                                <td rowspan="{{count($data['rows']['ctrl']['results'])}}" style="vertical-align : middle;text-align:center;">{{$data['rows']['ctrl']['views']}}</td>
                                <td rowspan="{{count($data['rows']['ctrl']['results'])}}" style="vertical-align : middle;text-align:center;">{{$data['rows']['ctrl']['total_conversion']}}</td>
                                <td rowspan="{{count($data['rows']['ctrl']['results'])}}" style="vertical-align : middle;text-align:center;">@if($data['rows']['winner'] == 'Ctrl')<i class="fa fa-trophy" aria-hidden="true" style="color:gold"></i> WINNER @endif</td>
                                <td rowspan="{{count($data['rows']['ctrl']['results'])+count($data['rows']['test']['results'])}}" style="vertical-align : middle;text-align:center;">{{$data['rows']['significiant']}}</td>
                            @endif
                        </tr>
                        @endforeach

                        @foreach($data['rows']['test']['results'] as $r)
                        <tr class="text-center">
                            <td style="vertical-align : middle;text-align:center;">{{$r['LP']}}</td>
                            <td style="vertical-align : middle;text-align:center;">{{$r['Conversion']}}</td>

                            @if($loop->index == 0)
                                <td rowspan="{{count($data['rows']['test']['results'])}}" style="vertical-align : middle;text-align:center;">{{$data['rows']['test']['views']}}</td>
                                <td rowspan="{{count($data['rows']['test']['results'])}}" style="vertical-align : middle;text-align:center;">{{$data['rows']['test']['total_conversion']}}</td>
                                <td rowspan="{{count($data['rows']['test']['results'])}}" style="vertical-align : middle;text-align:center;">@if($data['rows']['winner'] == 'Test')<i class="fa fa-trophy" aria-hidden="true" style="color:gold"></i> WINNER @endif</td>
                            @endif
                            
                        </tr>
                        @endforeach
                        
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
            @endforeach
            @endforeach
      </div>
    </div>
  </div>
  @push('scripts')
  <script>
    $(function(){
      var selected_account = "<?= $selected_account ?>";

      $("#account_select").change(function(){
            selected_account = this.value;
            refreshData();
        });

        function refreshData(){
            var origin = window.location.origin;
            var url = origin+'/split-test/'+selected_account;
            location.href = url;
        }
    })
  </script>
  @endpush
@endsection