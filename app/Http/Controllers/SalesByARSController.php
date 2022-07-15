<?php

namespace App\Http\Controllers;

use App\Models\AweberAccounts;
use App\Models\SalesByARS;

class SalesByARSController extends Controller
{
    public function index()
    {
        $accounts = ['1071969', '1267038', '1022666', '1425050'];
        $last_update_time = SalesByARS::first()->updated_at;
        return view('sales_by_ars.index', ['last_update_time' => $last_update_time, 'accounts' => $accounts]);
    }

    public static function getARSSales($tracking_id, $times, $type)
    {
        // type 1 = revenue, 2 = hops, 3 = epc
        $sales = SalesByARS::where('tracking_id', '=', $tracking_id)->where('times', '=', $times)->first();

        if ($type == 1) {
            return number_format($sales->revenue);
        } else if ($type == 2) {
            return number_format($sales->hops);
        } else if ($type == 3) {
            return number_format($sales->EPC, 2);
        }
        // return $id;
    }

    public static function getARSDetails($account_id, $list_name)
    {
        $sales = SalesByARS::where('account_id', '=', $account_id)->where('list_name', '=', $list_name)->where('times', '=', '1')->get();

        return $sales;
    }

    public static function getARSLists($account_id)
    {
        if ($account_id == '1071969') {
            $lists = ['3', '4', '5', '6', '7'];
            foreach ($lists as $list) {
                $sales_lists[] = SalesByARS::where('account_id', '=', $account_id)->where('list_type', '=', $list)->first();
            }
        } else {
            $sales_lists = SalesByARS::where('account_id', '=', $account_id)->groupBy('list_name')->get();
        }
        return $sales_lists;
    }

    public static function getAccountName($account_id)
    {
        $account = AweberAccounts::where('account_id', '=', $account_id)->first();
        return $account->account_name;
    }

    public static function getTotalSales($account_id, $list_name, $times)
    {
        $revenue = 0;
        $total_revenue = SalesByARS::where('account_id', '=', $account_id)->where('list_name', '=', $list_name)->where('times', '=', $times)->get();
        foreach ($total_revenue as $tr) {
            $revenue += $tr->revenue;
        }
        return number_format($revenue);
    }
}
