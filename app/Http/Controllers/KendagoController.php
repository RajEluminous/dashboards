<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\Kendago;
use Illuminate\Http\Request;

class KendagoController extends Controller
{
    public function index()
    {
        $date = Helper::getNewDate();
        $month = $date->format('m');
        $month_with_no_zero = $date->format('n');
        $year = $date->format('Y');

        if (Kendago::orderBy('id', 'desc')->first()->updated_at) {
            $last_update_time = Kendago::orderBy('id', 'desc')->first()->updated_at;
        } else {
            $last_update_time = Kendago::orderBy('id', 'desc')->first()->created_at;
        }

        for ($i = 4; $i >= 0; $i--) {
            $date = Helper::getNewDate()->modify("-" . $i . "month");

            $month = $date->format('m');
            $year = $date->format('Y');

            $monthDesc = ($i == 0) ? $date->format('M') . ' ' . $date->format('d') . '/' . $date->format('t') : $date->format('M');

            $get_kendago = Kendago::where('month', '=', $month)->where('year', '=', $year)->first();

            if ($get_kendago != null) {
                $kendago['months'][] = "'" . $monthDesc . "'";
                $kendago['revenue'][] = round($get_kendago->revenue);
                $kendago['hop_count'][] = round($get_kendago->hop_count);
                $kendago['initial_sales_count'][] = round($get_kendago->initial_sales_count);
                $kendago['fe_cvr'][] = $get_kendago->fe_cvr;
                $kendago['raw_epc'][] = $get_kendago->raw_epc;
            }
        }
        return view('kendago.index', ['kendago' => $kendago, 'last_update_time' => $last_update_time]);
    }
}
