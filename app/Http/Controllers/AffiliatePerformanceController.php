<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\VendorHopcount;
use App\Models\VendorTopAffiliate;
use Illuminate\Http\Request;
use App\Models\Affiliate15Happy;
use App\Models\Affiliate15Manifest;
use App\Models\Affiliate15Weight;
use App\Models\AffiliateAmazeyou2;
use App\Models\AffiliateAncientsec;
use App\Models\AffiliateGodfreq;
use App\Models\AffiliateMedicicode;
use App\Models\AffiliateMetabolicb;
use App\Models\AffiliateMillionb;
use App\Models\AffiliateMmswitch;
use App\Models\AffiliateMtimewarp;
use App\Models\AffiliatePnmanifest;
use App\Models\AffiliateQmanifest;
use App\Models\AffiliateSleepwaves;
use App\Models\AffiliateWactivator;
use App\Models\AffiliateType2free;
use App\Models\AffiliateUpmagnet;
use Illuminate\Support\Facades\DB;

class AffiliatePerformanceController extends Controller
{
    public function index(Request $request)
    {
        if ($request->view == 1) {
            $view = 6;
        } else if ($request->view == 2) {
            $view = 12;
        } else {
            $view = 24;
        }
        $selected_vendor = ($request->vendor == null) ? 'all-vendors' : $request->vendor;

        $selected_affiliate = ($request->affiliate == null) ? 'all-affiliate' : base64_decode($request->affiliate);


        $last_updated_time = Affiliate15Manifest::orderBy('id', 'desc')->first()->updated_at;
        $rsAff = $this->getAllVendors();
        $affiliate_list =  $rsAff['affiliate_list'];


        // For Vendor
        $vendorList =  "";
        $selAffs = array();
        if (isset($request->affiliate) && !empty($request->affiliate)) {
            $vendorList =  $this->getAffiliatesAllVendors(base64_decode($request->affiliate));
            $selAffs = explode(',',base64_decode($request->affiliate));
        }

        $standard = 0;
        $net_sale_amount = 0;
        $upsell = 0;
        $rebill = 0;

        for ($i = $view; $i >= 0; $i--) {
            // $i = 3;
            $date =  Helper::getNewDate()->modify("-" . $i . "month");
            $month = $date->format('m');
            $year = $date->format('Y');
            $yearDesc = "";
            if (($date->format('m') == '01') || ($i == $view)) {
                $yearDesc =   ' ' . $year;
            }
            $monthDesc = ($i == 0) ? $date->format('M') . ' ' . $date->format('d') . '/' . $date->format('t') : $date->format('M') . $yearDesc;

            // loop the vendor table data for an affiliate,  with and without vendor
            foreach ($vendorList as $venRs) {
                $modal = Helper::getAffiliateVendorModal(strtolower($venRs));

                // if affiliate and vendors both are selected
                if (isset($request->affiliate) && isset($request->vendor)) {
                    $get_vendor = $modal::whereRaw('MONTH(created_at) = ' . $month)->whereRaw('YEAR(created_at) = ' . $year)->where('vendor', '=', $request->vendor)->whereIn('affiliate_id', $selAffs)->get();
                    $total_order = $modal::whereRaw('MONTH(created_at) = ' . $month)->whereRaw('YEAR(created_at) = ' . $year)->where('vendor', '=', $request->vendor)->whereIn('affiliate_id', $selAffs)->sum(DB::raw('gross_sale_amount'));
                    $total_refund = $modal::whereRaw('MONTH(created_at) = ' . $month)->whereRaw('YEAR(created_at) = ' . $year)->where('vendor', '=', $request->vendor)->whereIn('affiliate_id', $selAffs)->sum(DB::raw('refund_amount'));
                    $vendor_hopcount =  $modal::select('hop_count')->whereRaw('MONTH(created_at) = ' . $month)->whereRaw('YEAR(created_at) = ' . $year)->where('vendor', '=', $request->vendor)->whereIn('affiliate_id', $selAffs)->first();
                    $initial_sales_count = $modal::whereRaw('MONTH(created_at) = ' . $month)->whereRaw('YEAR(created_at) = ' . $year)->where('vendor', '=', $request->vendor)->whereIn('affiliate_id', $selAffs)->sum(DB::raw('sale_count'));
                } else {
                    // only affiliate is selected $selAffs
                    $get_vendor = $modal::whereRaw('MONTH(created_at) = ' . $month)->whereRaw('YEAR(created_at) = ' . $year)->whereIn('affiliate_id', $selAffs)->get();
                    $total_order = $modal::whereRaw('MONTH(created_at) = ' . $month)->whereRaw('YEAR(created_at) = ' . $year)->whereIn('affiliate_id', $selAffs)->sum(DB::raw('gross_sale_amount'));
                    $total_refund = $modal::whereRaw('MONTH(created_at) = ' . $month)->whereRaw('YEAR(created_at) = ' . $year)->whereIn('affiliate_id', $selAffs)->sum(DB::raw('refund_amount'));
                    $vendor_hopcount =  $modal::select('hop_count')->whereRaw('MONTH(created_at) = ' . $month)->whereRaw('YEAR(created_at) = ' . $year)->whereIn('affiliate_id', $selAffs)->first();
                    $initial_sales_count = $modal::whereRaw('MONTH(created_at) = ' . $month)->whereRaw('YEAR(created_at) = ' . $year)->whereIn('affiliate_id', $selAffs)->sum(DB::raw('sale_count'));
                }

                if ($get_vendor != null) {
                    $sale = 0;
                    $rebill = 0;
                    $refund = 0;
                    $chargeback = 0;
                    $fee = 0;
                    $net_sale_amount = 0;
                    $upsell = 0;
                    $hop_count = 0;

                    foreach ($get_vendor as $v) {
                        if ($v->sale_amount > 0) {
                            $sale += $v->sale_amount;
                            $standard += $v->sale_amount;
                        }
                        if ($v->rebill_amount > 0) {
                            $rebill += $v->rebill_amount;
                        }
                        if ($v->refund_amount > 0) {
                            $refund += $v->refund_amount;
                        }
                        if ($v->chargeback_amount > 0) {
                            $chargeback += $v->chargeback_amount;
                        }
                        if ($v->upsell_amount > 0) {
                            $upsell += $v->upsell_amount;
                        }

                        if ($v->net_sale_amount > 0) {
                            $net_sale_amount += $v->net_sale_amount;
                        }
                        if ($v->hop_count > 0) {
                            $hop_count += $v->hop_count;
                        }
                    }

                    $vendor['months'][] = "'" . $monthDesc . "'";
                    $monthVal = "'" . $monthDesc . "'";

                    if (isset($vendor['month']["$month-$year"])) {
                        $vendor['month']["$month-$year"] += $sale + $upsell;
                        $vendor['refund_rate']["$month-$year"] += ($total_order == 0 || $total_refund == 0) ? 0 : number_format(($total_refund / $total_order) * 100, 2);
                        $vendor['hop_count']["$month-$year"] += $hop_count;
                        $vendor['initial_sales_count']["$month-$year"] += $initial_sales_count;
                        $vendor['net_sale_amount']["$month-$year"] += $net_sale_amount;
                        $vendor['net_sale_amount_graph']["$month-$year"] += $net_sale_amount + $upsell;
                        $vendor['rebill']["$month-$year"] += $rebill;
                        $vendor['upsell']["$month-$year"] += $upsell;
                        $vendor['fe_cvr']["$month-$year"] += ($hop_count == 0) ? 0 : number_format(($initial_sales_count / $hop_count) * 100, 2, '.', '');
                        $vendor['affiliate_epc']["$month-$year"] += ($hop_count == 0) ? 0 : number_format($net_sale_amount / $hop_count, 2, '.', '');
                    } else {
                        $vendor['month']["$month-$year"] = $sale + $upsell;
                        $vendor['refund_rate']["$month-$year"] = ($total_order == 0 || $total_refund == 0) ? 0 : number_format(($total_refund / $total_order) * 100, 2);
                        $vendor['hop_count']["$month-$year"] = $hop_count;
                        $vendor['initial_sales_count']["$month-$year"] = $initial_sales_count;
                        $vendor['net_sale_amount']["$month-$year"] = $net_sale_amount;
                        $vendor['net_sale_amount_graph']["$month-$year"] = $net_sale_amount + $upsell;
                        $vendor['rebill']["$month-$year"] = $rebill;
                        $vendor['upsell']["$month-$year"] = $upsell;
                        $vendor['fe_cvr']["$month-$year"] = ($hop_count == 0) ? 0 : number_format(($initial_sales_count / $hop_count) * 100, 2, '.', ''); // for Affiliate EPC calculation
                        $vendor['affiliate_epc']["$month-$year"] = ($hop_count == 0) ? 0 : number_format($net_sale_amount / $hop_count, 2, '.', '');
                    }

                }
            }
        }


        // Processing of the STANDARD, UPSELL, BUMP revenue
        $total_net_revenue = number_format(array_sum($vendor['net_sale_amount']), 2, '.', '') + number_format(array_sum($vendor['upsell']), 2, '.', '') + number_format(array_sum($vendor['rebill']), 2, '.', '');
        $standard_per = ($total_net_revenue > 0) ? number_format($standard * 100 / $total_net_revenue, 2, '.', '') : 0;
        $net_sale_amount_per = ($total_net_revenue > 0) ? number_format(array_sum($vendor['net_sale_amount']) * 100 / $total_net_revenue, 2, '.', '') : 0;
        $upsell_per = ($total_net_revenue > 0) ? number_format(array_sum($vendor['upsell']) * 100 / $total_net_revenue, 2, '.', '') : 0;
        $rebill_per = ($total_net_revenue > 0) ? number_format(array_sum($vendor['rebill']) * 100 / $total_net_revenue, 2, '.', '') : 0;

        $vendor['total_net_revenue'] = array('amount' => intval($total_net_revenue), 'percent' => $standard_per);
        $vendor['standard'] = array('amount' => intval($standard), 'percent' => $standard_per);
        $vendor['net_sale_amount_val'] = array('amount' => intval(array_sum($vendor['net_sale_amount'])), 'percent' => $net_sale_amount_per);
        $vendor['upsell'] = array('amount' => intval(array_sum($vendor['upsell'])), 'percent' => $upsell_per);
        $vendor['rebill'] = array('amount' => intval(array_sum($vendor['rebill'])), 'percent' => $rebill_per);

        $array_month = array_keys($vendor['month']);
        $array_revenue = array_values($vendor['month']);
        $array_refund_rate = array_values($vendor['refund_rate']);
        $array_hop_count = array_values($vendor['hop_count']);
        $array_initial_sales_count = array_values($vendor['initial_sales_count']);
        $array_fe_cvr = array_values($vendor['fe_cvr']);
        $array_affiliate_epc = array_values($vendor['affiliate_epc']);

        // Process Vendor months
        $arrayMonth = array();
        $netsaleamount = 0;
        for ($i = 0; $i < count($array_month); $i++) {
            if (date('m', strtotime('01-' . $array_month[$i])) == '01' || $i == 0 || $i == count($array_month) - 1) {
                $arrayMonth[] = "'" . date('M Y', strtotime('01-' . $array_month[$i])) . "'";
            } else {
                $arrayMonth[] = "'" . date('M', strtotime('01-' . $array_month[$i])) . "'";
            }

        }

        $vendor['months'] = array();
        $vendor['months'] = $arrayMonth;

        $vendor['revenue'] = array();
        $vendor['revenue'] = $array_revenue;

        $vendor['refund_rate'] = array();
        $vendor['refund_rate'] = $array_refund_rate;

        $vendor['hop_count'] = array();
        $vendor['hop_count'] = $array_hop_count;

        $vendor['initial_sales_count'] = array();
        $vendor['initial_sales_count'] = $array_initial_sales_count;

        $vendor['fe_cvr'] = array();
        $vendor['fe_cvr'] = $array_fe_cvr;

        $vendor['affiliate_epc'] = array();
        $vendor['affiliate_epc'] = $array_affiliate_epc;

        $vendor['net_sale_amount'] = array_values($vendor['net_sale_amount']);
        $vendor['net_sale_amount_graph'] = array_values($vendor['net_sale_amount_graph']);

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

        return view('affiliate_performance.index', [
            'last_update_time' => $last_updated_time,
            'view' => $request->view,
            'vendor_list' => Helper::$vendorOrderAccount,
            'vendor_lists' => $vendorList,
            'selected_vendor' => $selected_vendor,
            'selected_affiliate' => $selected_affiliate,
            'selected_affiliate_arr' => explode(',',$selected_affiliate),
            'affiliate_list' => $affiliate_list,
            'vendor' => $vendor,
            'top_affiliate_label' => $top_affiliate_label
        ]);
    }

    public static function getTopAffiliate($times, $selected_vendor)
    {
        return VendorTopAffiliate::where('times', '=', $times)->where('vendor_id', '=', $selected_vendor)->orderByRaw('vendor_revenue DESC')->get();
    }

    public static function getTopVendor($selected_affiliate, $vendor_lists)
    {

        $date = \Carbon\Carbon::today()->subDays(120);
        $date->toDateString();

        $allArray = [];
        if (!empty($selected_affiliate) && count($vendor_lists) > 0) {

            foreach ($vendor_lists as $venRs) {
                $data = [];
                $modal = '';
                $modal = Helper::getAffiliateVendorModal(strtolower($venRs));
                $get_vendor = $modal::whereDate('created_at', '>=', $date)->where('affiliate_id', '=', $selected_affiliate)->get();

                $data[$venRs]['sales'] = 0;
                $data[$venRs]['hop_count'] = 0;
                $data[$venRs]['affiliate_comms'] = 0;
                $data[$venRs]['affiliate_epc'] = 0;
                $data[$venRs]['fe_cvr'] = 0;
                $data[$venRs]['account'] = $venRs;
                $data[$venRs]['affiliate'] = $selected_affiliate;

                $data[$venRs]['net_sale_amount'] = 0;

                if ($get_vendor != null) {
                    foreach ($get_vendor as $v) {

                        $data[$venRs]['sales'] += $v->sale_amount;
                        $data[$venRs]['hop_count'] += $v->hop_count;
                        $month = $v->created_at->format('m');
                        $year = $v->created_at->format('Y');

                        $frontend_amount = $v->sale_amount;
                        $upsell_amount = $v->upsell_amount;
                        $rebill_amount = $v->rebill_amount;



                        $affiliate_comms = Helper::calculateAffiliateCommission(strtolower($venRs), $frontend_amount, $upsell_amount, $rebill_amount, strtolower($selected_affiliate), $month, $year);
                        $data[$venRs]['affiliate_comms'] += $affiliate_comms;
                        $affiliate_epc = ($v->hop_count == 0) ? 0 : number_format($affiliate_comms / $v->hop_count, 2);
                        $data[$venRs]['affiliate_epc'] += $affiliate_epc;
                        $data[$venRs]['fe_cvr'] += ($v->hop_count == 0) ? 0 : number_format(($v->sale_count / $v->hop_count) * 100, 2);
                        $data[$venRs]['net_sale_amount'] += $v->net_sale_amount;
                    }
                    array_push($allArray, $data);
                }
            }
        }
        return ($allArray);
    }

    public function ajaxGetTopVendor(Request $request)
    {

        $selected_affiliate = $request->selected_affiliate;
        $selected_affiliate_arr = explode(',', $request->selected_affiliate);
        $vendor_lists = explode(',', $request->vendor_lists);

        $allArray = [];
        if (!empty($selected_affiliate) && count($vendor_lists) > 0) {

            $startdate = \Carbon\Carbon::parse($request->startdate)->format('Y-m-d H:i:s');
            $enddate = \Carbon\Carbon::parse($request->enddate)->endOfDay()->format('Y-m-d H:i:s');

            foreach ($vendor_lists as $venRs) {
                $data = [];
                $modal = '';
                $modal = Helper::getAffiliateVendorModal(strtolower($venRs));
                $ddarr = [];

                foreach($selected_affiliate_arr as $selAff) {

                    if ($request->startdate == $request->enddate) {
                        $get_vendor = $modal::whereDate('created_at', '=', $startdate)->where('affiliate_id', '=', $selAff)->get();
                    } else {
                        $get_vendor = $modal::whereBetween('created_at', [$startdate, $enddate])->where('affiliate_id', '=', $selAff)->get();
                    }

                    $data[$venRs]['sales'] = 0;
                    $data[$venRs]['hop_count'] = 0;
                    $data[$venRs]['sale_count'] = 0;
                    $data[$venRs]['affiliate_comms'] = 0;
                    $data[$venRs]['affiliate_epc'] = 0;
                    $data[$venRs]['fe_cvr'] = 0;
                    $data[$venRs]['account'] = $venRs;
                    $data[$venRs]['affiliate'] = $selAff;

                    $data[$venRs]['net_sale_amount'] = 0;


                    if ($get_vendor != null) {

                        foreach ($get_vendor as $v) {

                            $data[$venRs]['sales'] += $v->sale_amount;
                            $data[$venRs]['hop_count'] += $v->hop_count;
                            $data[$venRs]['sale_count'] += $v->sale_count;
                            $month = $v->created_at->format('m');
                            $year = $v->created_at->format('Y');

                            $frontend_amount = $v->sale_amount;
                            $upsell_amount = $v->upsell_amount;
                            $rebill_amount = $v->rebill_amount;

                            $affiliate_comms = Helper::calculateAffiliateCommission(strtolower($venRs), $frontend_amount, $upsell_amount, $rebill_amount, strtolower($selAff), $month, $year);
                            $data[$venRs]['affiliate_comms'] += $affiliate_comms;
                            $affiliate_epc = ($v->hop_count == 0) ? 0 : $v->net_sale_amount / $v->hop_count;
                            $data[$venRs]['affiliate_epc'] =  $affiliate_epc;
                            $data[$venRs]['fe_cvr'] += ($v->hop_count == 0) ? 0 : number_format(($v->sale_count / $v->hop_count) * 100, 2);
                            $data[$venRs]['net_sale_amount'] += $v->net_sale_amount + $v->upsell_amount;

                        }

                        array_push($allArray, $data);
                    }

                }
            }

        }

        $finalArr = [];
        foreach($allArray as $rsArr) {
            foreach($rsArr as $rsKey => $rsVal) {

                array_push($finalArr, $rsVal);
            }

        }

        $finalCount = count($finalArr);
        ## Response
        $response = array(
            "draw" => 1,
            "iTotalRecords" => $finalCount,
            "iTotalDisplayRecords" => $finalCount,
            "aaData" => $finalArr
        );

        echo json_encode($response);

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

    // Getl all vendors
    public static function getAllVendors_old($selected_affiliate)
    {
        return VendorHopcount::where('affiliate', '=', $selected_affiliate)->orderByRaw('vendor ASC')->get();
    }

    public static function getAffiliatesAllVendors($selected_affiliate)
    {
        $selectedAffs = explode(',',$selected_affiliate);
        $manifest15 = Affiliate15Manifest::select('vendor')->whereIn('affiliate_id',$selectedAffs)->distinct()->get();
        $amazeyou2 = AffiliateAmazeyou2::select('vendor')->whereIn('affiliate_id',$selectedAffs)->distinct()->get();
        $wactivator = AffiliateWactivator::select('vendor')->whereIn('affiliate_id',$selectedAffs)->distinct()->get();
        $qmanifest = AffiliateQmanifest::select('vendor')->whereIn('affiliate_id',$selectedAffs)->distinct()->get();
        $weight15 = Affiliate15Weight::select('vendor')->whereIn('affiliate_id',$selectedAffs)->distinct()->get();
        $pnmanifest = AffiliatePnmanifest::select('vendor')->whereIn('affiliate_id',$selectedAffs)->distinct()->get();
        $sleepwaves = AffiliateSleepwaves::select('vendor')->whereIn('affiliate_id',$selectedAffs)->distinct()->get();
        $ancientsec = AffiliateAncientsec::select('vendor')->whereIn('affiliate_id',$selectedAffs)->distinct()->get();
        $millionb = AffiliateMillionb::select('vendor')->whereIn('affiliate_id',$selectedAffs)->distinct()->get();
        $medicicode = AffiliateMedicicode::select('vendor')->whereIn('affiliate_id',$selectedAffs)->distinct()->get();
        $happy15 = Affiliate15Happy::select('vendor')->whereIn('affiliate_id',$selectedAffs)->distinct()->get();
        $godfreq = AffiliateGodfreq::select('vendor')->whereIn('affiliate_id',$selectedAffs)->distinct()->get();
        $mtimewarp = AffiliateMtimewarp::select('vendor')->whereIn('affiliate_id',$selectedAffs)->distinct()->get();
        $mmswitch = AffiliateMmswitch::select('vendor')->whereIn('affiliate_id',$selectedAffs)->distinct()->get();
        $metabolicb = AffiliateMetabolicb::select('vendor')->whereIn('affiliate_id',$selectedAffs)->distinct()->get();
        $type2free = AffiliateType2free::select('vendor')->whereIn('affiliate_id',$selectedAffs)->distinct()->get();
        $upmagnet = AffiliateUpmagnet::select('vendor')->whereIn('affiliate_id',$selectedAffs)->distinct()->get();

        $list = array();

        foreach ($manifest15 as $venName) {
            $list[] = $venName->vendor;
        }

        foreach ($amazeyou2 as $venName) {
            $list[] = $venName->vendor;
        }

        foreach ($wactivator as $venName) {
            $list[] = $venName->vendor;
        }

        foreach ($qmanifest as $venName) {
            $list[] = $venName->vendor;
        }

        foreach ($weight15 as $venName) {
            $list[] = $venName->vendor;
        }

        foreach ($pnmanifest as $venName) {
            $list[] = $venName->vendor;
        }

        foreach ($sleepwaves as $venName) {
            $list[] = $venName->vendor;
        }

        foreach ($ancientsec as $venName) {
            $list[] = $venName->vendor;
        }

        foreach ($millionb as $venName) {
            $list[] = $venName->vendor;
        }

        foreach ($medicicode as $venName) {
            $list[] = $venName->vendor;
        }

        foreach ($happy15 as $venName) {
            $list[] = $venName->vendor;
        }

        foreach ($godfreq as $venName) {
            $list[] = $venName->vendor;
        }

        foreach ($mtimewarp as $venName) {
            $list[] = $venName->vendor;
        }

        foreach ($mmswitch as $venName) {
            $list[] = $venName->vendor;
        }

        foreach ($metabolicb as $venName) {
            $list[] = $venName->vendor;
        }

        foreach ($type2free as $venName) {
            $list[] = $venName->vendor;
        }

        foreach ($upmagnet as $venName) {
            $list[] = $venName->vendor;
        }

        return array_unique($list);
    }

    public static function getAllVendors()
    {
        $partnerList = DB::table('affiliate_master')
            ->join('cb_affiliate_partner_list', 'affiliate_master.id', '=', 'cb_affiliate_partner_list.affiliate_id')
            ->join('partner_master', 'cb_affiliate_partner_list.partner_id', '=', 'partner_master.id')
            ->select('affiliate_master.name as aff_id', 'partner_master.name as partner_id')
            ->get();
        $partnerArr = [];
        foreach ($partnerList as $rsd) {
            $partnerArr[$rsd->aff_id] = $rsd->partner_id;
        }

        $manifest15 = Affiliate15Manifest::select('affiliate_id')->distinct()->get();
        $amazeyou2 = AffiliateAmazeyou2::select('affiliate_id')->distinct()->get();
        $wactivator = AffiliateWactivator::select('affiliate_id')->distinct()->get();
        $qmanifest = AffiliateQmanifest::select('affiliate_id')->distinct()->get();
        $weight15 = Affiliate15Weight::select('affiliate_id')->distinct()->get();
        $pnmanifest = AffiliatePnmanifest::select('affiliate_id')->distinct()->get();
        $sleepwaves = AffiliateSleepwaves::select('affiliate_id')->distinct()->get();
        $ancientsec = AffiliateAncientsec::select('affiliate_id')->distinct()->get();
        $millionb = AffiliateMillionb::select('affiliate_id')->distinct()->get();
        $medicicode = AffiliateMedicicode::select('affiliate_id')->distinct()->get();
        $happy15 = Affiliate15Happy::select('affiliate_id')->distinct()->get();
        $godfreq = AffiliateGodfreq::select('affiliate_id')->distinct()->get();
        $mtimewarp = AffiliateMtimewarp::select('affiliate_id')->distinct()->get();
        $mmswitch = AffiliateMmswitch::select('affiliate_id')->distinct()->get();
        $metabolicb = AffiliateMetabolicb::select('affiliate_id')->distinct()->get();
        $type2free = AffiliateType2free::select('affiliate_id')->distinct()->get();
        $upmagnet = AffiliateUpmagnet::select('affiliate_id')->distinct()->get();

        $list = array();

        foreach ($manifest15 as $affName) {
            $list[] = $affName->affiliate_id;
        }

        foreach ($amazeyou2 as $affName) {
            $list[] = $affName->affiliate_id;
        }

        foreach ($wactivator as $affName) {
            $list[] = $affName->affiliate_id;
        }

        foreach ($qmanifest as $affName) {
            $list[] = $affName->affiliate_id;
        }

        foreach ($weight15 as $affName) {
            $list[] = $affName->affiliate_id;
        }

        foreach ($pnmanifest as $affName) {
            $list[] = $affName->affiliate_id;
        }

        foreach ($sleepwaves as $affName) {
            $list[] = $affName->affiliate_id;
        }

        foreach ($ancientsec as $affName) {
            $list[] = $affName->affiliate_id;
        }

        foreach ($millionb as $affName) {
            $list[] = $affName->affiliate_id;
        }

        foreach ($medicicode as $affName) {
            $list[] = $affName->affiliate_id;
        }

        foreach ($happy15 as $affName) {
            $list[] = $affName->affiliate_id;
        }

        foreach ($godfreq as $affName) {
            $list[] = $affName->affiliate_id;
        }

        foreach ($mtimewarp as $affName) {
            $list[] = $affName->affiliate_id;
        }

        foreach ($mmswitch as $affName) {
            $list[] = $affName->affiliate_id;
        }

        foreach ($metabolicb as $affName) {
            $list[] = $affName->affiliate_id;
        }

        foreach ($type2free as $affName) {
            $list[] = $affName->affiliate_id;
        }

        foreach ($upmagnet as $affName) {
            $list[] = $affName->affiliate_id;
        }

        $affList = array_diff(array_unique($list), Helper::$excludeVendorAffiliateArray);

        $aff_list = [];
        foreach ($affList as $aff) {
            if (isset($partnerArr[$aff])) {
                $aff_list[strtoupper($aff)] = $aff . ' (' . $partnerArr[$aff] . ')';
            } else {
                $aff_list[strtoupper($aff)] = $aff;
            }
        }

        $affList = array_map('strtoupper', $aff_list);

        return array('affiliate_list' => $affList);
    }

    // cron job
    public static function getAffiliateVendorData()
    {
        $vendorAccount = ['15manifest', 'amazeyou2', 'wactivator', '15weight', '15happy', 'qmanifest', 'millionb', 'medicicode', 'ancientsec', 'godfreq', 'pnmanifest', 'sleepwaves', 'mtimewarp', 'mmswitch', 'metabolicb', 'type2free', 'upmagnet'];

        $allArray = [];

        $today =  Helper::getNewDate();
        $todayDate = $today->format('Y-m-d');
		
        foreach ($vendorAccount as $account) {
            $modal = Helper::getAffiliateVendorModal(strtolower($account));
            $url = "https://api.clickbank.com/rest/1.3/analytics/vendor/affiliate/summary/?account=$account&summaryType=AFFILIATE_ONLY&startDate=$todayDate&endDate=$todayDate";
            $result = curl_init();
            curl_setopt($result, CURLOPT_URL, $url);
            curl_setopt($result, CURLOPT_HEADER, false);
            curl_setopt($result, CURLOPT_HTTPGET, false);
            curl_setopt($result, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($result, CURLOPT_HTTPHEADER, array("Accept: application/json", "Authorization: " . env('CLICKBANK_CLIENT_ID') . ":" . env('CLICKBANK_CLIENT_SECRET')));

            $api_result = json_decode(curl_exec($result), true);

            if (isset($api_result['rows']['row']))
                $total_count = count($api_result['rows']['row']);

            // START EACH ACCOUNT WILL INSERT TOP 10 SALES TO ARRAY
            $i = 0;
            $inserted_data_count = 1;

            if (isset($api_result['rows']['row'])) {
                if (isset($api_result['rows']['row']['dimensionValue'])) {
                    $data['account'] =  $account;
                    $data['affiliate'] = $api_result['rows']['row']['dimensionValue'];
                    $data['earnings_per_hop'] = $api_result['rows']['row']['data']['0']['value']['$'];
                    $data['upsell_count'] = $api_result['rows']['row']['data']['3']['value']['$'];
                    $data['rebill_count'] = $api_result['rows']['row']['data']['4']['value']['$'];
                    $data['hops_per_sale'] = $api_result['rows']['row']['data']['5']['value']['$'];
                    $data['earnings_per_click'] = $api_result['rows']['row']['data']['6']['value']['$'];
                    $data['refund_rate'] = $api_result['rows']['row']['data']['7']['value']['$'];
                    $data['chargeback_amount'] = $api_result['rows']['row']['data']['8']['value']['$'];
                    $data['refund_amount'] = $api_result['rows']['row']['data']['9']['value']['$'];
                    $data['gross_sale_amount'] = $api_result['rows']['row']['data']['10']['value']['$'];
                    $data['rebill_amount'] = $api_result['rows']['row']['data']['11']['value']['$'];
                    $data['net_sale_amount'] = $api_result['rows']['row']['data']['13']['value']['$'];
                    $data['sale_amount'] = $api_result['rows']['row']['data']['14']['value']['$'];
                    $data['gross_sale_count'] = $api_result['rows']['row']['data']['15']['value']['$'];
                    $data['chargeback_rate'] = $api_result['rows']['row']['data']['16']['value']['$'];
                    $data['refund_count'] = $api_result['rows']['row']['data']['17']['value']['$'];
                    $data['net_sale_count'] = $api_result['rows']['row']['data']['19']['value']['$'];
                    $data['upsell_amount'] = $api_result['rows']['row']['data']['20']['value']['$'];
                    $data['hop_count'] = $api_result['rows']['row']['data']['21']['value']['$'];
                    $data['order_form_sale_conversion'] = $api_result['rows']['row']['data']['22']['value']['$'];
                    $data['chargeback_count'] = $api_result['rows']['row']['data']['23']['value']['$'];
                    $data['sale_count'] = $api_result['rows']['row']['data']['25']['value']['$'];

                    array_push($allArray, $data);
                    $data = [];
                } else {
                    foreach ($api_result['rows']['row'] as $row) {
                        $data['account'] =  $account;
                        $data['affiliate'] = $row['dimensionValue'];
                        $data['earnings_per_hop'] = $row['data']['0']['value']['$'];
                        $data['upsell_count'] = $row['data']['3']['value']['$'];
                        $data['rebill_count'] = $row['data']['4']['value']['$'];
                        $data['hops_per_sale'] = $row['data']['5']['value']['$'];
                        $data['earnings_per_click'] = $row['data']['6']['value']['$'];
                        $data['refund_rate'] = $row['data']['7']['value']['$'];
                        $data['chargeback_amount'] = $row['data']['8']['value']['$'];
                        $data['refund_amount'] = $row['data']['9']['value']['$'];
                        $data['gross_sale_amount'] = $row['data']['10']['value']['$'];
                        $data['rebill_amount'] = $row['data']['11']['value']['$'];
                        $data['net_sale_amount'] = $row['data']['13']['value']['$'];
                        $data['sale_amount'] = $row['data']['14']['value']['$'];
                        $data['gross_sale_count'] = $row['data']['15']['value']['$'];
                        $data['chargeback_rate'] = $row['data']['16']['value']['$'];
                        $data['refund_count'] = $row['data']['17']['value']['$'];
                        $data['net_sale_count'] = $row['data']['19']['value']['$'];
                        $data['upsell_amount'] = $row['data']['20']['value']['$'];
                        $data['hop_count'] = $row['data']['21']['value']['$'];
                        $data['order_form_sale_conversion'] = $row['data']['22']['value']['$'];
                        $data['chargeback_count'] = $row['data']['23']['value']['$'];
                        $data['sale_count'] = $row['data']['25']['value']['$'];

                        array_push($allArray, $data);
                        $data = [];

                        $i++;
                        if ($i >= $total_count) {
                            break;
                        }
                    }
                }
            } // End: isset

        }

        foreach ($allArray as $rsVal) {
            $modalObj = Helper::getAffiliateVendorModal(strtolower($rsVal['account']));

            $exist = $modalObj::where('affiliate_id', '=', $rsVal['affiliate'])->whereDate('created_at', $todayDate)->first();

            if ($exist == null) {

                $modalObj::insert([
                    'affiliate_id' => $rsVal['affiliate'],
                    'vendor' => strtoupper($rsVal['account']),
                    'hop_count' => $rsVal['hop_count'],
                    'earnings_per_hop' => $rsVal['earnings_per_hop'],
                    'earnings_per_click' => $rsVal['earnings_per_click'],
                    'hops_per_sale' => $rsVal['hops_per_sale'],
                    'order_form_sale_conversion' => $rsVal['order_form_sale_conversion'],
                    'sale_count' => $rsVal['sale_count'],
                    'sale_amount' => $rsVal['sale_amount'],
                    'rebill_count' => $rsVal['rebill_count'],
                    'rebill_amount' => $rsVal['rebill_amount'],
                    'upsell_count' => $rsVal['upsell_count'],
                    'upsell_amount' => $rsVal['upsell_amount'],
                    'gross_sale_count' => $rsVal['gross_sale_count'],
                    'gross_sale_amount' => $rsVal['gross_sale_amount'],
                    'refund_count' => $rsVal['refund_count'],
                    'chargeback_count' => $rsVal['chargeback_count'],
                    'net_sale_count' => $rsVal['net_sale_count'],
                    'refund_amount' => $rsVal['refund_amount'],
                    'chargeback_amount' => $rsVal['chargeback_amount'],
                    'net_sale_amount' => $rsVal['net_sale_amount'],
                    'refund_rate' => $rsVal['refund_rate'],
                    'chargeback_rate' => $rsVal['chargeback_rate'],
                    'created_at' => $today,
                    'updated_at' => \Carbon\Carbon::now()
                ]);
            } else {

                $id = $exist->id;
                $row = $modalObj::find($id);
                $row->hop_count = $rsVal['hop_count'];
                $row->earnings_per_hop = $rsVal['earnings_per_hop'];
                $row->earnings_per_click = $rsVal['earnings_per_click'];
                $row->hops_per_sale = $rsVal['hops_per_sale'];
                $row->order_form_sale_conversion = $rsVal['order_form_sale_conversion'];
                $row->sale_count = $rsVal['sale_count'];
                $row->sale_amount = $rsVal['sale_amount'];
                $row->rebill_count = $rsVal['rebill_count'];
                $row->rebill_amount = $rsVal['rebill_amount'];
                $row->upsell_count = $rsVal['upsell_count'];
                $row->upsell_amount = $rsVal['upsell_amount'];
                $row->gross_sale_count = $rsVal['gross_sale_count'];
                $row->gross_sale_amount = $rsVal['gross_sale_amount'];
                $row->refund_count = $rsVal['refund_count'];
                $row->chargeback_count = $rsVal['chargeback_count'];
                $row->net_sale_count = $rsVal['net_sale_count'];
                $row->refund_amount = $rsVal['refund_amount'];
                $row->chargeback_amount = $rsVal['chargeback_amount'];
                $row->net_sale_amount = $rsVal['net_sale_amount'];
                $row->refund_rate = $rsVal['refund_rate'];
                $row->chargeback_rate = $rsVal['chargeback_rate'];
                $row->updated_at = \Carbon\Carbon::now();
                $row->save();
            }
        }
    }
}
