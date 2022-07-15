@extends('layouts.new_app')

@section('content')
<div class="right_col" role="main">
    <div class="x_panel">
        <div class="x_title">
            <h2>Create User</h2>
            <div class="clearfix"></div>
        </div>

        <div class="x_content">
            <br />
            <form method="POST" action="{{ route("users.store") }}" enctype="multipart/form-data" id="demo-form2" data-parsley-validate class="form-horizontal form-label-left">
                @csrf
                <div class="item form-group">
                    <label class="col-form-label col-md-3 col-sm-3 label-align" for="name">Name <span class="required">*</span>
                    </label>
                    <div class="col-md-6 col-sm-6 ">
                        <input type="text" id="name" required="required" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" name="name" value="{{ old('name', '') }}" required>

                        @if($errors->has('name'))
                            <div class="invalid-feedback">
                                {{ $errors->first('name') }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="item form-group">
                    <label class="col-form-label col-md-3 col-sm-3 label-align" for="email">Email Address <span class="required">*</span>
                    </label>
                    <div class="col-md-6 col-sm-6 ">
                        <input type="email" id="email" required="required" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" name="email" value="{{ old('email', '') }}" required>

                        @if($errors->has('email'))
                            <div class="invalid-feedback">
                                {{ $errors->first('email') }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="item form-group">
                    <label class="col-form-label col-md-3 col-sm-3 label-align" for="password">Password <span class="required">*</span>
                    </label>
                    <div class="col-md-6 col-sm-6 ">
                        <input type="password" id="password" required="required" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" name="password" value="{{ old('password', '') }}" required>

                        @if($errors->has('password'))
                            <div class="invalid-feedback">
                                {{ $errors->first('password') }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="item form-group">
                    <label class="col-form-label col-md-3 col-sm-3 label-align" for="user_role">User Role <span class="required">*</span>
                    </label>
                    <div class="col-md-3 col-sm-6 ">
                        <select class="form-control {{ $errors->has('user_role') ? 'is-invalid' : '' }}" name="user_role">
                            @foreach($user_roles as $id => $user_role)
                                <option value="{{ $id }}" {{ old('user_role_id') == $id ? 'selected' : '' }}>{{ $user_role }}</option>
                            @endforeach
                        </select>

                        @if($errors->has('user_role'))
                            <div class="invalid-feedback">
                                {{ $errors->first('user_role') }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="item form-group">
                    <label class="col-form-label col-md-3 col-sm-3 label-align" for="status">Status <span class="required">*</span>
                    </label>
                    <div class="col-md-3 col-sm-6 ">
                        <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" name="status">
                            @foreach(App\User::LoadStatus(true) as $id => $status)
                                <option value="{{ $id }}" {{ old('status') == $id ? 'selected' : '' }}>{{ $status }}</option>
                            @endforeach
                        </select>

                        @if($errors->has('status'))
                            <div class="invalid-feedback">
                                {{ $errors->first('status') }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="ln_solid"></div>
                <div class="item form-group">
                    <div class="col-md-6 col-sm-6 offset-md-3">
                        <button type="submit" class="btn btn-success">Create</button>
                        <button class="btn btn-link" type="button">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
