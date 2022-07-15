<?php

namespace App\Http\Controllers;

use App\Mail\SendCustomerNamesMail;
use App\Helper\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SendCustomerNameController extends Controller
{
    public static function sendEmail()
    {
        // Process curl for vendor=amazeyou2

        $date = Helper::getNewDate()->modify('-1 month');
        $month = $date->format('m');
        $year = $date->format('Y');
        $end_day = $date->format('t');

        $startDate = $year . '-' . $month . '-1';
        $endDate =   $year . '-' . $month . '-' . $end_day;

        $url = "https://api.clickbank.com/rest/1.3/orders2/list?vendor=amazeyou2&startDate=$startDate&endDate=$endDate";

        $result = curl_init();
        curl_setopt($result, CURLOPT_URL, $url);
        curl_setopt($result, CURLOPT_HEADER, false);
        curl_setopt($result, CURLOPT_HTTPGET, false);
        curl_setopt($result, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($result, CURLOPT_TIMEOUT, 0);
        curl_setopt($result, CURLOPT_HTTPHEADER, array("Accept: application/json", "Authorization: " . env('CLICKBANK_CLIENT_ID') . ":" . env('CLICKBANK_CLIENT_SECRET')));

        $api_result = json_decode(curl_exec($result), true);
        $arrNames = array();
        $i = 0;
        //print_r($api_result['orderData']);

        if ($api_result['orderData'] != null) {

            foreach ($api_result['orderData'] as $row) {
                $i++;
                if ((!is_array($row['firstName']) && !is_array($row['lastName'])) && (isset($row['firstName']) && isset($row['lastName']))) {

                    $custName = ucfirst(strtolower($row['firstName'])) . ' ' . ucfirst(strtolower($row['lastName']));
                    if (!in_array($custName, $arrNames))
                        $arrNames[] = $custName;
                }
            }

            if (count($arrNames) > 0) {
                if (in_array(env('APP_ENV'), ['staging', 'production'])) {
                    $recipient = 'marionneubronner@gmail.com';
                } else {
                    $recipient = 'chungsung.ong@limitlessfactor.com';
                }

                $result = Mail::to($recipient)->send(new SendCustomerNamesMail($arrNames));
            }
        }

        // End of curl call
    }
}
