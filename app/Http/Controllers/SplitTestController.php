<?php

namespace App\Http\Controllers;

use App\Events\PrepareSplitTestAPI;
use App\Services\SplitTestService;
use Illuminate\Http\Request;

class SplitTestController extends Controller
{
    public function index(Request $request)
    {
        $splitTestService = new SplitTestService();
        $selected_account =  ($request->account == null) ? 'all-account' : $request->account;
        $results = event(new PrepareSplitTestAPI($selected_account));

        foreach ($results as $r) {
            $records = $r;
        }
        // dd($records);
        return view('split_test.index', ['records' => $records, 'selected_account' => $selected_account, 'account_list' => $splitTestService->account_list]);
    }
}
