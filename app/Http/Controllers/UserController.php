<?php

namespace App\Http\Controllers;

use App\Models\UserRole;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all()->except([1]);

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user_roles = UserRole::all()->pluck('name', 'id')->prepend('Please Select', '')->except([1]);

        return view('users.create', compact('user_roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:4',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'user_role' => 'required',
            'status' => 'required',
        ]);

        $objUser = new User([
            'name'          => $request->get('name'),
            'email'         => $request->get('email'),
            'password'      => bcrypt($request->get('password')),
            'user_role_id'  => $request->get('user_role'),
            'status'        => $request->get('status'),
            'created_by'    => Auth::id(),
            'created_at'    => Carbon::now()
        ]);

        $objUser->save();

        return redirect()->route('users.index')
            ->with(
                'flash_success_message',
                trans('global.data_created', ['name' => "$objUser->name"])
            );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $user_roles = UserRole::all()->pluck('name', 'id')->prepend('Please Select', '')->except([1]);

        return view('users.edit', compact('user', 'user_roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $this->validate($request, [
            'name' => 'required|min:4|unique:users,name,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'user_role' => 'required',
            'status' => 'required',
        ]);

        $objUser = User::find($user->id);
        $objUser->name = $request->input('name');
        $objUser->email = $request->input('email');
        $objUser->user_role_id = $request->input('user_role');
        $objUser->status = $request->input('status');
        $objUser->updated_at = Carbon::now();
        $objUser->save();

        return redirect()->route('users.index')
            ->with(
                'flash_success_message',
                trans('global.data_created', ['name' => "$objUser->name"])
            );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $objUser = User::whereId($id)->first();
        $objUser->delete();

        return redirect()->route('users.index')
            ->with(
                'flash_info_message',
                trans('global.user_account_deleted', ['username' => "$objUser->name"])
            );
    }

    /**
     * Activate user
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function UserActivate(Request $request)
    {
        $objUser = User::whereId($request->id)->first();
        $objUser->status = User::STATUS_ACTIVE;
        $objUser->save();

        return redirect()->route('users.index')
            ->with(
                'flash_info_message',
                trans('global.user_account_activate', ['username' => "$objUser->name"])
            );
    }

    /**
     * Suspend user
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function UserSuspend(Request $request)
    {
        $objUser = User::whereId($request->id)->first();
        $objUser->status = User::STATUS_SUSPENDED;
        $objUser->save();

        return redirect()->route('users.index')
            ->with(
                'flash_info_message',
                trans('global.user_account_suspended', ['username' => "$objUser->name"])
            );
    }
}
