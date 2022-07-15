<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\Vendor15Manifest;
use App\Models\VendorHopcount;
use App\Models\VendorTopAffiliate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorOrderController extends Controller
{
    public function index(Request $request)
    {
        // $date = Helper::getNewDate()->modify('-1 day');
        // $month = $date->format('m');
        // $year = $date->format('Y');
        if ($request->view == 1) {
            $view = 6;
        } else if ($request->view == 2) {
            $view = 12;
        } else {
            $view = 24;
        }

        $selected_vendor = ($request->vendor == null) ? '15manifest' : $request->vendor;

        $modal = Helper::getVendorModal($selected_vendor);

        $excludeVendorArray = $this->getExcludeVendorArray($selected_vendor);

        // $selected_affiliate = ($request->affiliate == null) ? $modal::where('vendor', '=', $selected_vendor)->orderBy('affiliate', 'asc')->first()->affiliate : $request->affiliate;
        $selected_affiliate = ($request->affiliate == null) ? 'all-affiliate' : $request->affiliate;

        $last_updated_time = $modal::orderBy('transactionTime', 'desc')->first()->created_at;
        $affiliate_list = $modal::whereNotIn('affiliate', Helper::$excludeVendorAffiliateArray)->groupBy('affiliate')->get();


        for ($i = $view; $i >= 0; $i--) {
            // $i = 3;
            $date = Helper::getNewDate()->modify("-" . $i . "month");

            $month = $date->format('m');
            $year = $date->format('Y');
            $yearDesc = "";
            if (($date->format('m') == '01') || ($i == $view)) {
                $yearDesc =   $year;
            }

            $monthDesc = ($i == 0) ? $date->format('M') . ' ' . $date->format('d') . '/' . $date->format('t') : $date->format('M') . '  ' . $yearDesc;

            // IF affiliate selection = ALL AFFILIATE
            if ($selected_affiliate == 'all-affiliate') {
                $get_vendor = $modal::whereRaw('MONTH(transactionTime) = ' . $month)->whereRaw('YEAR(transactionTime) = ' . $year)->where('vendor', '=', $selected_vendor)->whereNotIn('affiliate', $excludeVendorArray)->get();

                $total_order = $modal::whereRaw('MONTH(transactionTime) = ' . $month)->whereRaw('YEAR(transactionTime) = ' . $year)->where('vendor', '=', $selected_vendor)->whereIn('transactionType', ['SALE', 'RFND', 'BILL'])->whereNotIn('affiliate', $excludeVendorArray)->count();

                $total_refund = $modal::whereRaw('MONTH(transactionTime) = ' . $month)->whereRaw('YEAR(transactionTime) = ' . $year)->where('vendor', '=', $selected_vendor)->where('transactionType', '=', 'RFND')->whereNotIn('affiliate', $excludeVendorArray)->count();

                $vendor_hopcount = VendorHopcount::selectRaw('SUM(hop_count) as hop_count')->where('month', '=', $month)->where('year', '=', $year)->where('vendor', '=', $selected_vendor)->whereNotIn('affiliate', $excludeVendorArray)->first();

                $initial_sales_count = $modal::where('transactionType', '=', 'SALE')->whereRaw('MONTH(transactionTime) = ' . $month)->whereRaw('YEAR(transactionTime) = ' . $year)->where('lineItemType', '=', 'STANDARD')->where('vendor', '=', $selected_vendor)->whereNotIn('affiliate', $excludeVendorArray)->count();
            } else {
                $get_vendor = $modal::whereRaw('MONTH(transactionTime) = ' . $month)->whereRaw('YEAR(transactionTime) = ' . $year)->where('affiliate', '=', $selected_affiliate)->whereNotIn('affiliate', $excludeVendorArray)->get();

                $total_order = $modal::whereRaw('MONTH(transactionTime) = ' . $month)->whereRaw('YEAR(transactionTime) = ' . $year)->where('affiliate', '=', $selected_affiliate)->whereIn('transactionType', ['SALE', 'RFND', 'BILL'])->whereNotIn('affiliate', $excludeVendorArray)->count();

                $total_refund = $modal::whereRaw('MONTH(transactionTime) = ' . $month)->whereRaw('YEAR(transactionTime) = ' . $year)->where('affiliate', '=', $selected_affiliate)->where('transactionType', '=', 'RFND')->whereNotIn('affiliate', $excludeVendorArray)->count();

                $vendor_hopcount = VendorHopcount::where('month', '=', $month)->where('year', '=', $year)->where('vendor', '=', $selected_vendor)->where('affiliate', '=', $selected_affiliate)->whereNotIn('affiliate', $excludeVendorArray)->first();

                $initial_sales_count = $modal::where('transactionType', '=', 'SALE')->whereRaw('MONTH(transactionTime) = ' . $month)->whereRaw('YEAR(transactionTime) = ' . $year)->where('lineItemType', '=', 'STANDARD')->where('vendor', '=', $selected_vendor)->where('affiliate', '=', $selected_affiliate)->whereNotIn('affiliate', $excludeVendorArray)->count();
            }

            // IF affiliate selection = ALL AFFILIATE



            if ($get_vendor != null) {
                $sale = 0;
                $bill = 0;
                $refund = 0;
                $chargeback = 0;
                $fee = 0;
                $hop_count = ($vendor_hopcount == null || $vendor_hopcount->hop_count == null) ? 0 : $vendor_hopcount->hop_count;

                $vendor['months'][] = "'" . $monthDesc . "'";

                foreach ($get_vendor as $v) {
                    $transactionType = $v->transactionType;
                    switch ($transactionType) {
                        case 'SALE':
                            $sale += $v->accountAmount;
                            break;
                        case 'BILL':
                            $bill += $v->accountAmount;
                            break;
                        case 'RFND':
                            $refund += $v->accountAmount;
                            break;
                        case 'CGBK':
                            $chargeback += $v->accountAmount;
                            break;
                        case 'FEE':
                            $fee += $v->accountAmount;
                            break;
                    }
                }

                $vendor['revenue'][] = $sale + $bill + $refund + $chargeback + $fee;
                $vendor['refund_rate'][] = ($total_order == 0 || $total_refund == 0) ? 0 : number_format(($total_refund / $total_order) * 100, 2);
                $vendor['hop_count'][] = $hop_count;
                $vendor['initial_sales_count'][] = $initial_sales_count;
                $vendor['fe_cvr'][] = ($hop_count == 0) ? 0 : number_format(($initial_sales_count / $hop_count) * 100, 2);
                $vendor['vendor_epc'][] = ($hop_count == 0) ? 0 : number_format(($sale + $bill - $refund - $chargeback - $fee) / $hop_count, 2);

                // GET ACTIVE AFFILIATES
                $active_affiliates = $modal::selectRaw('affiliate,count(*) as initial_sales_count')->whereRaw('MONTH(transactionTime) = ' . $month)->whereRaw('YEAR(transactionTime) = ' . $year)->where('transactionType', '=', 'SALE')->where('lineItemType', '=', 'STANDARD')->where('vendor', '=', $selected_vendor)->havingRaw('count(*) > 0')->whereNotIn('affiliate', Helper::$tableArray)->groupBy('affiliate')->get();
                $vendor['active_affiliates'][] = $active_affiliates->count();
                // GET ACTIVE AFFILIATES
            }
        }

        // dd($vendor);
        // START TOP AFFILIATE LABEL
        $date = Helper::getNewDate();
        $day = $date->format('d');
        $month = $date->format('m');
        $year = $date->format('Y');

        $top_affiliate_label[] = "Yesterday : $year-$month-$day";
        $top_affiliate_label[] = "Last 7 Days : " . Helper::getNewDate()->modify('-7 day')->format('Y') . '-' . Helper::getNewDate()->modify('-7 day')->format('m') . '-' . Helper::getNewDate()->modify('-7 day')->format('d') . ' - ' . $year . '-' . $month . '-' . $day;
        $top_affiliate_label[] = "Last 30 Days : " . Helper::getNewDate()->modify('-30 day')->format('Y') . '-' . Helper::getNewDate()->modify('-30 day')->format('m') . '-' . Helper::getNewDate()->modify('-30 day')->format('d') . ' - ' . $year . '-' . $month . '-' . $day;
        $top_affiliate_label[] = "Last 120 Days : " . Helper::getNewDate()->modify('-120 day')->format('Y') . '-' . Helper::getNewDate()->modify('-120 day')->format('m') . '-' . Helper::getNewDate()->modify('-120 day')->format('d') . ' - ' . $year . '-' . $month . '-' . $day;
        // END TOP AFFILIATE LABEL


        return view('vendor_order.index', [
            'last_update_time' => $last_updated_time,
            'view' => $request->view,
            'vendor_list' => Helper::$vendorOrderAccount,
            'selected_vendor' => $selected_vendor,
            'selected_affiliate' => $selected_affiliate,
            'affiliate_list' => $affiliate_list,
            'vendor' => $vendor,
            'top_affiliate_label' => $top_affiliate_label
        ]);
    }

    public static function getTopAffiliate($times, $selected_vendor)
    {
        return VendorTopAffiliate::where('times', '=', $times)->where('vendor_id', '=', $selected_vendor)->orderByRaw('vendor_revenue DESC')->get();
    }

    private function getExcludeVendorArray($selected_vendor)
    {
        if ($selected_vendor == '15manifest') {
            $excludeVendorArray = ['101FB2C'];
        } else {
            $excludeVendorArray = ['Not Set'];
        }
        return $excludeVendorArray;
    }
}
