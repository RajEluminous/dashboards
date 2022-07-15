<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\KendagoOrder;
use Illuminate\Http\Request;

class KendagoOrderController extends Controller
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

        if (KendagoOrder::orderBy('id', 'desc')->first()->updated_at) {
            $last_update_time = KendagoOrder::orderBy('id', 'desc')->first()->updated_at;
        } else {
            $last_update_time = KendagoOrder::orderBy('id', 'desc')->first()->created_at;
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

            $get_kendago = KendagoOrder::where('month', '=', $month)->where('year', '=', $year)->first();

            if ($get_kendago != null) {
                $revenue = $get_kendago->sale + $get_kendago->bill - $get_kendago->refund - $get_kendago->charge_back - $get_kendago->fee;
                $hop_count = $get_kendago->hop_count;
                $frontend_amount = $get_kendago->sale - $get_kendago->upsell_sales_amount;
                $upsell_amount = $get_kendago->upsell_sales_amount;
                $rebill_amount = $get_kendago->bill;

                $kendago['months'][] = "'" . $monthDesc . "'";
                $kendago['revenue'][] = round($revenue);
                $kendago['initial_sales_count'][] = round($get_kendago->initial_sales_count);
                $kendago['hop_count'][] = $hop_count;
                $kendago['fe_cvr'][] = ($get_kendago->hop_count == 0) ? 0 : number_format(($get_kendago->initial_sales_count / $get_kendago->hop_count) * 100, 2);
                $kendago['aov'][] = ($get_kendago->initial_sales_count == 0) ? 0 : number_format($revenue / $get_kendago->initial_sales_count, 2);
                $affiliate_comms = Helper::calculateAffiliateCommission('15manifest', $frontend_amount, $upsell_amount, $rebill_amount, '101fb2c', $month, $year);

                $affiliate_epc = ($hop_count == 0) ? 0 : number_format($affiliate_comms / $hop_count, 2);
                $vendor_epc = ($hop_count == 0) ? 0 : $revenue / $hop_count;

                $raw_epc = $vendor_epc + str_replace(',', '', $affiliate_epc);
                $kendago['raw_epc'][] = number_format($raw_epc, 2);
            }
        }
        return view('kendago_order.index', ['kendago' => $kendago, 'last_update_time' => $last_update_time, 'view' => $request->view]);
    }
}
