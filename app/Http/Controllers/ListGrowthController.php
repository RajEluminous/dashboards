<?php

namespace App\Http\Controllers;

use App\Models\AweberAccounts;
use App\Models\ListGrowth;
use Illuminate\Http\Request;

class ListGrowthController extends Controller
{
    public function index()
    {
        $accounts = AweberAccounts::all();
        $leads1 = ListGrowth::where('types', '=', '1')->where('from', '=', '1')->get();
        $last_update_time = ListGrowth::orderBy('id', 'DESC')->first()->updated_at;
        return view('list_growth.index', ['accounts' => $accounts, 'leads1' => $leads1, 'last_update_time' => $last_update_time]);
    }

    public static function getValue($account_id, $type, $row, $from)
    {
        $data = ListGrowth::where('account_id', '=', $account_id)->where('types', '=', $type)->where('row', '=', $row)->where('from', '=', $from)->first();
        return number_format($data['value']);
    }
}
