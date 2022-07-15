<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $affiliate_revenue = array(1, 2, 3);

        if (in_array(Auth::user()->user_role_id, $affiliate_revenue)) {
            //return redirect()->route('affiliate_revenue.index');
			return redirect('/affiliate_revenue/2');
        } else {
            return redirect()->route('dashboard');
        }
    }
}
