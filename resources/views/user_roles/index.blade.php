@extends('layouts.new_app')

@section('content')
    <div class="right_col" role="main">
        <div class="row">
            <div class="col-md-12">
                <a href="{{ route("user_roles.create") }}" class="btn btn-primary float-sm-right">
                    Create User Role
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 col-sm-12">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="x_panel">
                            <div class="x_title">
                                <h2>User Roles</h2>
                                <div class="clearfix"></div>
                            </div>

                            <div class="x_content">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered top_affiliates">
                                        <thead>
                                            <tr class="text-center">
                                                <th class="d-none"></th>
                                                <th>Name</th>
                                                <th>&nbsp;</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @foreach ($user_roles as $key => $user_role)
                                                <tr data-entry-id="{{ $user_role->id }}">
                                                    <td class="d-none">
                                                        {{ $user_role->id ?? '' }}
                                                    </td>

                                                    <td>
                                                        {{ $user_role->name ?? '' }}
                                                    </td>

                                                    <td>
                                                        <a class="btn btn-sm btn-info" href="{{ route('user_roles.edit', $user_role->id) }}">
                                                            Edit
                                                        </a>
                                                    </td>
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
        </div>
    </div>
@endsection
