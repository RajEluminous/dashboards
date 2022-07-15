@extends('layouts.new_app')

@section('content')
    <div class="right_col" role="main">
        <div class="row">
            <div class="col-md-12">
                <a href="{{ route("users.create") }}" class="btn btn-primary float-sm-right">
                    Create User
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 col-sm-12">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="x_panel">
                            <div class="x_title">
                                <h2>Users</h2>
                                <div class="clearfix"></div>
                            </div>

                            <div class="x_content">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered top_affiliates">
                                        <thead>
                                            <tr class="text-center">
                                                <th class="d-none"></th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Role</th>
                                                <th>Status</th>
                                                <th>&nbsp;</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @foreach ($users as $key => $user)
                                                <tr data-entry-id="{{ $user->id }}">
                                                    <td class="d-none">
                                                        {{ $user->id ?? '' }}
                                                    </td>

                                                    <td>
                                                        {{ $user->name ?? '' }}
                                                    </td>

                                                    <td>
                                                        {{ $user->email ?? '' }}
                                                    </td>

                                                    <td>
                                                        {{ $user->userRole->name ?? '' }}
                                                    </td>

                                                    <td>
                                                        <span class="badge {{ $user->status == "ACTIVE" ? 'badge-success' : 'badge-secondary' }}">
                                                            {{ Str::of($user->status)->title }}
                                                        </span>
                                                    </td>

                                                    <td>
                                                        <a class="btn btn-sm btn-info" href="{{ route('users.edit', $user->id) }}">
                                                            Edit
                                                        </a>

                                                        {{-- <form action="{{ route('user.userSuspend', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure want to suspend?');" class="d-inline">
                                                            @method('POST')
                                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                            <input type="submit" class="btn btn-sm btn-danger" value="Suspend">
                                                        </form> --}}
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
