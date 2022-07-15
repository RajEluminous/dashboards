<?php

namespace App\Http\Controllers;

use App\Models\UserRole;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserRoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_roles = UserRole::all()->except([1]);

        return view('user_roles.index', compact('user_roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('user_roles.create');
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
        ]);

        $objUserRole = new UserRole([
            'name'          => $request->get('name'),
            'created_at'    => Carbon::now()
        ]);

        $objUserRole->save();

        return redirect()->route('user_roles.index')
            ->with(
                'flash_success_message',
                trans('global.data_created', ['name' => "$objUserRole->name"])
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
        $user_role = UserRole::findOrFail($id);

        return view('user_roles.edit', compact('user_role'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UserRole $user_role)
    {
        $this->validate($request, [
            'name' => 'required|min:4|unique:user_roles,name,' . $user_role->id,
        ]);

        $objUserRole = UserRole::find($user_role->id);
        $objUserRole->name = $request->input('name');
        $objUserRole->updated_at = Carbon::now();
        $objUserRole->save();

        return redirect()->route('user_roles.index')
            ->with(
                'flash_success_message',
                trans('global.data_created', ['name' => "$objUserRole->name"])
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
        $objUserRole = UserRole::whereId($id)->first();
        $objUserRole->delete();

        return redirect()->route('user_roles.index')
            ->with(
                'flash_info_message',
                trans('global.user_account_deleted', ['username' => "$objUserRole->name"])
            );
    }
}
