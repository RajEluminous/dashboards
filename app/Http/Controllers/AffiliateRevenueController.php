<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\AffiliateRevenue;
use App\Models\SalesRankingRevenue;
use App\Models\IncomingTrafficStatus;
use App\Models\TopAffiliate;
use Illuminate\Http\Request;

class AffiliateRevenueController extends Controller
{
    public function index(Request $request)
    {
        $date = Helper::getNewDate();
        $month = $date->format('m');
        $month_with_no_zero = $date->format('n');
        $day = $date->format('d');
        $year = $date->format('Y');
        $total_day = $date->format('t');
        // $view = ($request->view == 2 || $request->view == NULL) ? 12 : 6;

        if ($request->view == 3) {
            $view = 24;
        } else if ($request->view == 2) {
            $view = 12;
        } else if ($request->view == 1) {
            $view = 6;
        } else {
            $view = 12;
        }


        // START CURRENT MONTH DIFFERENCE
        $current_month_difference = AffiliateRevenue::where('month', '=', $month_with_no_zero)->where('year', '=', $year)->first();
        $current_month_difference->target = ($current_month_difference->target / $total_day) * $day;
        $current_month_difference->date = Helper::getNewDate()->format('d F, Y');
        // END CURRENT MONTH DIFFERENCE

        // START SALES RANKING BY ACCOUNT
        $rs_sales_ranking = SalesRankingRevenue::orderByRaw('currentmonth_revenue DESC')->get();

        $total_month_to_date = 0;
        $total_projected_month = 0;
        $total_last_month = 0;
        $total_variance = 0;

        for ($i = 0; $i <= count($rs_sales_ranking) - 1; $i++) {
            $projected_month = ($rs_sales_ranking[$i]->currentmonth_revenue / $day) * $total_day;
            $sales_ranking_by_account['account'][] = $rs_sales_ranking[$i]->account;
            $sales_ranking_by_account['month_to_date'][] = $rs_sales_ranking[$i]->currentmonth_revenue;
            $sales_ranking_by_account['projected_month'][] = $projected_month;
            $sales_ranking_by_account['last_month'][] = $rs_sales_ranking[$i]->lastmonth_revenue;
            $sales_ranking_by_account['variance'][] = ($rs_sales_ranking[$i]->lastmonth_revenue == 0) ? 0 : ($projected_month - $rs_sales_ranking[$i]->lastmonth_revenue) / $rs_sales_ranking[$i]->lastmonth_revenue * 100;

            $total_month_to_date +=  $rs_sales_ranking[$i]->currentmonth_revenue;
            $total_projected_month += $projected_month;
            $total_last_month += $rs_sales_ranking[$i]->lastmonth_revenue;
        }

        foreach ($sales_ranking_by_account['month_to_date'] as $each_month_to_date) {
            $sales_ranking_by_account['month_to_date_percent'][] = $each_month_to_date / $total_month_to_date * 100;
        }

        foreach ($sales_ranking_by_account['last_month'] as $each_last_month) {
            $sales_ranking_by_account['last_month_percent'][] = $each_last_month / $total_last_month * 100;
        }

        // Total SALES RANKING DATA - RM30122020
        $sales_ranking_by_account['total_month_to_date'] = $total_month_to_date;
        $sales_ranking_by_account['total_projected_month'] = $total_projected_month;
        $sales_ranking_by_account['total_last_month'] = $total_last_month;

        $total_variance = ($total_projected_month - $total_last_month) / $total_last_month * 100;
        $sales_ranking_by_account['total_variance'] = $total_variance;

        if (AffiliateRevenue::orderBy('id', 'desc')->first()->updated_at) {
            $last_update_time = AffiliateRevenue::orderBy('id', 'desc')->first()->updated_at;
        } else {
            $last_update_time = AffiliateRevenue::orderBy('id', 'desc')->first()->created_at;
        }
        // END SALES RANKING BY ACCOUNT

        // INCOMING TRAFFIC STATUS
        $rs_its  = IncomingTrafficStatus::orderByRaw('current_hopcount desc')->get();

        $total_hop_month_to_date = 0;
        $total_hop_last_month = 0;
        $total_hop_projected_month = 0;

        $total_fecvr_month_to_date = 0;
        $total_fecvr_last_month = 0;

        for ($i = 0; $i <= count($rs_its) - 1; $i++) {
            $hop_projected_month  = ($rs_its[$i]->current_hopcount / $day) * $total_day;
            $incoming_traffic_status['account'][] = $rs_its[$i]->account;
            $incoming_traffic_status['hop_month_to_date'][] = $rs_its[$i]->current_hopcount;
            $incoming_traffic_status['hop_last_month'][] = $rs_its[$i]->last_hopcount;
            $incoming_traffic_status['hop_projected_month'][]  = round($hop_projected_month);
            $incoming_traffic_status['hop_variance'][] = ($hop_projected_month - $rs_its[$i]->last_hopcount) / $rs_its[$i]->last_hopcount * 100;

            $fecvr_month_to_date = ($rs_its[$i]->current_hopcount == 0) ? 0 : ($rs_its[$i]->current_salescount / $rs_its[$i]->current_hopcount) * 100;
            $fecvr_last_month = ($rs_its[$i]->last_salescount / $rs_its[$i]->last_hopcount) * 100;

            $incoming_traffic_status['fecvr_month_to_date'][] = number_format($fecvr_month_to_date, 2);
            $incoming_traffic_status['fecvr_last_month'][] =  number_format($fecvr_last_month, 2);
            $incoming_traffic_status['fecvr_variance'][] = ($fecvr_month_to_date - $fecvr_last_month) / $fecvr_last_month * 100;

            // total
            $total_hop_month_to_date += $rs_its[$i]->current_hopcount;
            $total_hop_last_month += $rs_its[$i]->last_hopcount;
            $total_hop_projected_month += round($hop_projected_month);

            $total_fecvr_month_to_date += number_format($fecvr_month_to_date, 2);
            $total_fecvr_last_month += number_format($fecvr_last_month, 2);
        }

        // for total incoming traffic
        $incoming_traffic_status['total_hop_month_to_date'] = $total_hop_month_to_date;
        $incoming_traffic_status['total_hop_last_month'] = $total_hop_last_month;
        $incoming_traffic_status['total_hop_projected_month'] = $total_hop_projected_month;
        $incoming_traffic_status['total_hop_variance'] = ($total_hop_projected_month -  $total_hop_last_month) /  $total_hop_last_month * 100;

        $incoming_traffic_status['total_fecvr_month_to_date'] = $total_fecvr_month_to_date;
        $incoming_traffic_status['total_fecvr_last_month'] = $total_fecvr_last_month;
        $incoming_traffic_status['total_fecvr_variance'] = ($total_fecvr_month_to_date - $total_fecvr_last_month) / $total_fecvr_last_month * 100;

        // START AFFILIATE REVENUE
        for ($i = $view; $i >= 0; $i--) {
            $date = Helper::getNewDate()->modify("-" . $i . "month");

            $month = $date->format('m');
            $year = $date->format('Y');
            $yearDesc = "";
            if (($date->format('m') == '01') || ($i == $view)) {
                $yearDesc =   $year;
            }

            $monthDesc = ($i == 0) ? $date->format('M') . ' ' . $date->format('d') . '/' . $date->format('t') : $date->format('M') . '  ' . $yearDesc;

            $get_revenue = AffiliateRevenue::where('month', '=', $month)->where('year', '=', $year)->first();
            if ($get_revenue != null) {
                $affiliate_revenue['months'][] = "'" . $monthDesc . "'";
                $affiliate_revenue['revenue'][] = round($get_revenue->revenue);
                $affiliate_revenue['target'][] = ($i == 0) ? ($get_revenue->target / $date->format('t')) * $date->format('d') : $get_revenue->target;
            } else {
                $affiliate_revenue['months'][] = "'" . $monthDesc . "'";
                $affiliate_revenue['revenue'][] = 0;
                $affiliate_revenue['target'][] = 0;
            }
        }
        // END AFFILIATE REVENUE

        // START TOP AFFILIATE LABEL
        $top_affiliate_label[] = "Today : " . Helper::getNewDate()->format('Y-m-d');
        $top_affiliate_label[] = "Yesterday : " . Helper::getNewDate()->modify('-1 day')->format('Y') . '-' . Helper::getNewDate()->modify('-1 day')->format('m') . '-' . Helper::getNewDate()->modify('-1 day')->format('d');
        $top_affiliate_label[] = "Last 7 Days : " . Helper::getNewDate()->modify('-7 day')->format('Y') . '-' . Helper::getNewDate()->modify('-7 day')->format('m') . '-' . Helper::getNewDate()->modify('-7 day')->format('d') . ' - ' . $year . '-' . $month . '-' . $day;
        $top_affiliate_label[] = "Last 30 Days : " . Helper::getNewDate()->modify('-30 day')->format('Y') . '-' . Helper::getNewDate()->modify('-30 day')->format('m') . '-' . Helper::getNewDate()->modify('-30 day')->format('d') . ' - ' . $year . '-' . $month . '-' . $day;
        // END TOP AFFILIATE LABEL

        // To get Timezone at Top Affiliate Today
        if (TopAffiliate::orderBy('id', 'desc')->first()->updated_at) {
            $topaffiliate_today_updated_at = TopAffiliate::orderBy('id', 'desc')->first()->updated_at;
        } else {
            $topaffiliate_today_updated_at = TopAffiliate::orderBy('id', 'desc')->first()->created_at;
        }

        return view('affiliate_revenue.index', [
            'last_update_time' => $last_update_time,
            'cmd' => $current_month_difference,
            //'current_month_sales_ranking' => $current_month_sales_ranking,
            //'last_month_sales_ranking' => $last_month_sales_ranking,
            'affiliate_revenue' => $affiliate_revenue,
            'top_affiliate_label' => $top_affiliate_label,
            'view' => $request->view,
            'sales_ranking_by_account' => $sales_ranking_by_account,
            'incoming_traffic_status' => $incoming_traffic_status,
            'topaffiliate_today_updated_at' => $topaffiliate_today_updated_at
        ]);
    }

    public static function getTopAffiliate($times)
    {
        return TopAffiliate::where('times', '=', $times)->orderByRaw('vendor_revenue DESC')->get();
    }
}
