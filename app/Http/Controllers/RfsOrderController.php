<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\RfsOrder;
use Illuminate\Http\Request;

class RfsOrderController extends Controller
{
    public function index(Request $request)
    {
        $date = Helper::getNewDate();
        $month = $date->format('m');
        $year = $date->format('Y');
        if ($request->view == 1) {
            $view = 6;
        } else if ($request->view == 2) {
            $view = 12;
        } else {
            $view = 24;
        }

        if (RfsOrder::orderBy('id', 'desc')->first()->updated_at) {
            $last_update_time = RfsOrder::orderBy('id', 'desc')->first()->updated_at;
        } else {
            $last_update_time = RfsOrder::orderBy('id', 'desc')->first()->created_at;
        }

        for ($i = $view; $i >= 0; $i--) {
            // $i = 1;
            $date = Helper::getNewDate()->modify("-" . $i . "month");

            $month = $date->format('m');
            $year = $date->format('Y');
            $yearDesc = "";
            if (($date->format('m') == '01') || ($i == $view)) {
                $yearDesc =   $year;
            }

            $monthDesc = ($i == 0) ? $date->format('M') . ' ' . $date->format('d') . '/' . $date->format('t') : $date->format('M') . '  ' . $yearDesc;

            $get_rfs = RfsOrder::where('month', '=', $month)->where('year', '=', $year)->first();

            if ($get_rfs != null) {
                $revenue = $get_rfs->sale + $get_rfs->bill - $get_rfs->refund - $get_rfs->charge_back - $get_rfs->fee;
                $hop_count = $get_rfs->hop_count;
                $frontend_amount = $get_rfs->sale - $get_rfs->upsell_sales_amount;
                $upsell_amount = $get_rfs->upsell_sales_amount;
                $rebill_amount = $get_rfs->bill;

                $rfs['months'][] = "'" . $monthDesc . "'";
                $rfs['revenue'][] = round($revenue);
                $rfs['initial_sales_count'][] = round($get_rfs->initial_sales_count);
                $rfs['hop_count'][] = $hop_count;
                $rfs['fe_cvr'][] = ($get_rfs->hop_count == 0) ? 0 : number_format(($get_rfs->initial_sales_count / $get_rfs->hop_count) * 100, 2);
                $rfs['aov'][] = ($get_rfs->initial_sales_count) ? 0 : number_format($revenue / $get_rfs->initial_sales_count, 2);
                $affiliate_comms = Helper::calculateAffiliateCommission('godfreq', $frontend_amount, $upsell_amount, $rebill_amount, 'checkdata', $month, $year);

                $affiliate_epc = ($hop_count == 0) ? 0 : number_format($affiliate_comms / $hop_count, 2);
                $vendor_epc = ($hop_count == 0) ? 0 : $revenue / $hop_count;

                $raw_epc = $vendor_epc + str_replace(',', '', $affiliate_epc);
                $rfs['raw_epc'][] = number_format($raw_epc, 2);
            }
        }
        return view('rfs_order.index', ['rfs' => $rfs, 'last_update_time' => $last_update_time, 'view' => $request->view]);
    }
}
