@extends('layouts.new_app')
<style>
.select2-container {
    box-sizing: border-box;
    display: inline-block;
    margin: 0;
    position: relative;
    vertical-align: middle;
    width: 100% !important;
}
</style>
@section('content')
    <div class="right_col" role="main">
        <div class="row">
            <div class="col-md-12">
                <a href="{{ url("cb-master/create_partner") }}" class="btn btn-primary float-sm-right">
                    Add/Edit Partner Name
                </a>

                <a href="{{ url("cb-master/create_affiliate") }}" class="btn btn-primary float-sm-right">
                    Add/Edit Affiliate ID
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 col-sm-12">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="x_panel">
                            <div class="x_title">
                                <h2>Clickbank ID Master List</h2>
                                <div class="clearfix"></div>
                            </div>

                            <div class="x_content">
                                <div class="table-responsive">
                                @if ($message = Session::get('success'))
                                    <div class="alert alert-success">
                                        <p>{{ $message }}</p>
                                    </div>
                                @endif
                                @if($message = Session::get('error'))
                                    <div class="alert alert-error">
                                        <p>{{ $message }}</p>
                                    </div>
                                @endif
                                <form method="POST" action="{{ url("cb-master") }}" enctype="multipart/form-data" id="frmFilter" data-parsley-validate class="form-horizontal form-label-left">
                                    @csrf
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr class="text-center table-secondary">
                                                <td style="width:30%;"><h2>Search Affiliate ID or Partner Name</h2></td>
                                                <td>
                                                    <select class="form-control" name="fAffiliate" id="fAffiliate">
                                                    <option value="">Select Affiliate ID</option>
                                                        @foreach($affArry as $id => $affName)
                                                            <option value="{{ $id }}">{{ $affName }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-control" name="fPartner" id="fPartner">
                                                    <option value="">Select Partner Name</option>
                                                        @foreach($partArry as $id => $partName)
                                                            <option value="{{ $id }}">{{ $partName }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <button type="submit" class="btn btn-info" id="btnSearch"> Search </button>
                                                    <!-- <button type="reset" class="btn" id="btnCancel">Cancel</button>-->
                                                </td>
                                            </tr>
                                        </thead>
                                    </table>
                                    </form>
                                <form method="POST" action="{{ url("cb-master") }}" enctype="multipart/form-data" id="frmMap" data-parsley-validate class="form-horizontal form-label-left">
                                @csrf
                                <table class="table table-striped table-bordered top_affiliates">
                                        <thead>
                                            <tr class="text-center table-info">
                                                <td style="width:30%;"><h2>Map Affiliate ID - Partner Name</h2></td>
                                                <td>
                                                    <select class="form-control" name="affiliate" id="affiliate">
                                                    <option value="">Select Affiliate ID</option>
                                                        @foreach($affArry as $id => $affName)
                                                            <option value="{{ $id }}">{{ $affName }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-control" name="partner" id="partner">
                                                       <option value="">Select Partner Name</option>
                                                        @foreach($partArry as $id => $partName)
                                                            <option value="{{ $id }}">{{ $partName }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>  <input type="hidden" id="recid" name="recid" value="">
                                                      <button type="submit" class="btn btn-success" id="btnSubmit">Assign</button>
                                                      <button type="reset" class="btn" id="btnReset" style="display:none;">Cancel</button>
                                                      <!-- <button type="button" class="btn btn-info" id="btnFilter">Filter</button> -->
                                                </td>
                                            </tr>
                                        </thead>
                                    </table>
                                    </form>

                                    <table class="table table-striped table-bordered top_affiliates">
                                        <thead>
                                            <tr class="text-center">
                                                <th class="d-none"></th>
                                                <th>#No</th>
                                                <th>Affiliate ID</th>
                                                <th>Partner Name</th>
                                                <th></th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($cbArr as $user)
                                                <tr data-entry-id="{{ $user['id'] }}" class="text-center">
                                                <td class="d-none">
                                                    </td>
                                                    <td>
                                                    {{ $user['count'] ?? '' }}
                                                    </td>
                                                    <td>
                                                        {{ $user['affiliate_id'] ?? '' }}
                                                    </td>
                                                    <td>
                                                    {{ $user['partner_id'] ?? '' }}
                                                    </td>
                                                    <td>
                                                      <a href="#" onClick="selList({{ $user['id'] ?? '' }},{{ $user['aff_id'] ?? '' }},{{ $user['part_id'] ?? '' }})" class="edit btn btn-primary btn-sm editProduct"> Edit</a>

                                                      <a href="#" onClick="delRecord({{ $user['id'] ?? '' }})" class="edit btn btn-danger btn-sm">Delete</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    {!! $cbmasterlist->links() !!}
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
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

    <script type="text/javascript">
    // Apply select2 class
     $("#affiliate").select2();
     $("#partner").select2();
     $("#fAffiliate").select2();
     $("#fPartner").select2();

    // Edit the record
     function selList(recid,affid,partid) {
         $('#btnSubmit').text("Update");
         $('#btnReset').show();
         $('#btnFilter').hide();
        if(affid>0) {
            $('#affiliate').val(affid);
            $('#affiliate').trigger('change');
        }
        if(partid>0) {
            $('#partner').val(partid);
            $('#partner').trigger('change');
        }
        $('#recid').val(recid);
     }

     // Reset mapping form
     $('#btnReset').on('click',function(){
        $('#affiliate').val(null).trigger('change');
        $('#partner').val(null).trigger('change');
        $('#recid').val(null);
        $('#btnSubmit').text("Assign");
        $('#btnReset').hide();
        $('#btnFilter').show();

     });

    $('#btnFilter').on('click',function(){
        $('#frmFilter').show();
    });
    $('#btnCancel').on('click',function(){
        window.location="cb-master";
        $('#frmFilter').hide();
    });
     // Delete record
    function delRecord(delid) {
        if(delid>0) {
            var con = confirm("Are you sure you want to delete this record?");
            if(con) {
                window.location="cb-master/delete/"+delid;
            }
        }
    }

    </script>
    @endpush
@endsection
