@extends('layouts.new_app')

@section('content')
<div class="right_col" role="main">
    <div class="x_panel">
        <div class="x_title">
            <h2>Add Affiliate ID</h2>
            <a href="{{ url("cb-master/create_partner") }}" class="btn btn-primary float-sm-right">
                Add Partner Name
            </a>
            <div class="clearfix"></div>
        </div>

        <div class="x_content">
            <br />
            @if($message = Session::get('error'))
                <div class="alert alert-error">
                    <p>{{ $message }}</p>
                </div>
            @endif

            @if($message = Session::get('success'))
                <div class="alert alert-success">
                    <p>{{ $message }}</p>
                </div>
            @endif

            <form method="POST" action="{{ url("cb-master/create_affiliate") }}" enctype="multipart/form-data" id="demo-form2" data-parsley-validate class="form-horizontal form-label-left">
                @csrf
                <div class="item form-group">
                    <label class="col-form-label col-md-3 col-sm-3 label-align" for="name">Affiliate ID <span class="required">*</span>
                    </label>
                    <div class="col-md-6 col-sm-6 ">
                        <input type="text" id="name" maxlength="35" required="required" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" name="name" value="{{ old('name', '') }}" required>

                        @if($errors->has('name'))
                            <div class="invalid-feedback">
                                {{ $errors->first('name') }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="ln_solid"></div>
                <div class="item form-group">
                    <div class="col-md-6 col-sm-6 offset-md-3">
                        <input type="hidden" id="recid" name="recid" value="">
                        <button type="submit" class="btn btn-success" name="submit" id="btnSubmit">Create</button>
                        <button type="submit" class="btn btn-info" name="submit" value="Search" id="btnSearch">Search</button>
                        <button type="reset" class="btn btn-secondary" id="btnReset">Reset</button>
                        <button class="btn btn-link" type="button" onclick="window.location='{{ url("cb-master") }}'">Cancel</button>
                    </div>
                </div>
            </form>
            <table class="table table-striped table-bordered top_affiliates">
                <thead>
                    <tr class="text-center">
                        <th class="d-none"></th>
                        <th>#No</th>
                        <th>Affiliate ID</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($affArr as $user)
                        <tr data-entry-id="{{ $user['id'] }}" class="text-center">
                        <td class="d-none">
                            </td>
                            <td>
                            {{ $user['count'] ?? '' }}
                            </td>
                            <td>
                                {{ $user['name'] ?? '' }}
                            </td>
                            <td>
                                <a href="#"  onClick="selList({{ $user['id'] ?? '' }}, '{{ $user['name'] ?? '' }}')" class="edit btn btn-primary btn-sm editProduct"> Edit</a>
                                @if( $user['isInMasterList'] == true)
                                 <a href="#" onClick="delRecord({{ $user['id'] ?? '' }})" class="edit btn btn-danger btn-sm">Delete</a>
                                @endif


                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {!! $affiliatelist->links() !!}
        </div>
    </div>
</div>
@push('scripts')
<script>
 function selList(recid,affName) {
        console.log(recid+':'+affName);
        $('#btnSubmit').text("Update");
        $('#recid').val(recid);
        $('#name').val(affName);
}
$('#btnReset').on('click',function(){
        $('#name').val(null).trigger('change');
        $('#recid').val(null).trigger('change');
        $('#btnSubmit').text("Create").trigger('change');
     });

  // Delete record
  function delRecord(delid) {
        if(delid>0) {
            var con = confirm("Are you sure you want to delete this record?");
            if(con) {
                window.location="../cb-master/deleteaaffiliate/"+delid;
            }
        }
    }
</script>
@endpush
@endsection
