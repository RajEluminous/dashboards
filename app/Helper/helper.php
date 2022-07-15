<?php

namespace App\Helper;

use App\Models\AffiliateRevenue;
use App\Models\AweberAccounts;
use App\Models\AweberLists;
use App\Models\SalesRankingRevenue;
use App\Models\IncomingTrafficStatus;
use App\Models\Kendago;
use App\Models\RfsOrder;
use App\Models\ListGrowth;
use App\Models\SalesByARS;
use App\Models\TopAffiliate;
use App\Models\Affiliate15Happy;
use App\Models\Affiliate15Manifest;
use App\Models\Affiliate15Weight;
use App\Models\AffiliateAmazeyou2;
use App\Models\AffiliateAncientsec;
use App\Models\AffiliateGodfreq;
use App\Models\AffiliateMedicicode;
use App\Models\AffiliateMillionb;
use App\Models\AffiliateMmswitch;
use App\Models\AffiliateMtimewarp;
use App\Models\AffiliatePnmanifest;
use App\Models\AffiliateQmanifest;
use App\Models\AffiliateSleepwaves;
use App\Models\AffiliateWactivator;
use App\Models\CbMasterList;
use App\Models\AffiliateMaster;
use App\Models\AffiliateMetabolicb;
use App\Models\AffiliateType2free;
use App\Models\AffiliateUpmagnet;
use App\Models\PartnerMaster;
use App\Models\Vendor15Happy;
use App\Models\Vendor15Manifest;
use App\Models\Vendor15Weight;
use App\Models\VendorAmazingYou2;
use App\Models\VendorAncientsec;
use App\Models\VendorGodfreq;
use App\Models\VendorHopcount;
use App\Models\VendorMedicicode;
use App\Models\VendorMillionb;
use App\Models\VendorMmswitch;
use App\Models\VendorMtimewarp;
use App\Models\VendorPnmanifest;
use App\Models\VendorQmanifest;
use App\Models\VendorSleepwaves;
use App\Models\VendorWactivator;
use App\Models\VendorMetabolicb;
use App\Models\VendorType2free;
use App\Models\VendorTopAffiliate;
use App\Models\VendorUpmagnet;
use Carbon\Carbon;
use GuzzleHttp\Client;
use DateTime;
use DateTimeZone;
use FFI\Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use DB;

class Helper
{
    // private $tableArray = ['15manifest', 'qmanifest', 'wactivator', 'amazeyou', '15weight', 'millionb', 'ancientsec', 'pnmanifest', 'amazeyou2', 'sleepwaves'];
    public static $tableArray = ['15manifest', 'qmanifest', 'wactivator', '15weight', 'millionb', 'ancientsec', 'pnmanifest', 'amazeyou2', 'sleepwaves', '15happy', 'godfreq', 'type2free'];
    public static $excludeAffiliateArray = ['101fb2c', 'magicplay', 'leekuanyew', 'verifydata', 'wactivator', 'amazeyou', 'Not Set', 'ancientsec', 'futureaff', 'checkdata', 'YOURXCBXID', 'Limitless Factor Pte Ltd (checkdata)', 'Limitless Factor Pte Ltd (magicplay)', 'Limitless Factor Pte Ltd (verifydata)', 'Limitless Factor Pte Ltd (leekuanyew)', 'Limitless Factor Pte Ltd (amazeyou)', 'Limitless Factor Pte Ltd (futureaff)'];
    public static $types = ['SALE', 'BILL', 'RFND', 'CGBK', 'FEE'];
    // public static $vendorOrderAccount = ['metabolicb'];
    public static $vendorOrderAccount = ['15manifest', 'amazeyou2', 'wactivator', '15weight', '15happy', 'qmanifest', 'millionb', 'medicicode', 'ancientsec', 'godfreq', 'pnmanifest', 'metabolicb', 'type2free', 'upmagnet', 'sleepwaves', 'mtimewarp', 'mmswitch'];
    public static $excludeVendorAffiliateArray = ['101fb2c', 'Not Set', 'YOURXCBXID'];
    public static $tableArrayStartMonth = array('15happy' => array('month' => 11, 'year' => 2020), 'godfreq' => array('month' => 01, 'year' => 2021));

    public static function getResponse($method, $url, $token)
    {
        $client = new Client();
        $headers = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json'
        ];

        $response = $client->request($method, $url, ['headers' => $headers]);
        $body = json_decode($response->getBody(), true);
        return $body;
    }

    public static function insertAllAccountLists($arrOwner, $token)
    {
        $account_id = $arrOwner['entries'][0]['id'];
        $url = 'https://api.aweber.com/1.0/accounts/' . $account_id . '/lists';

        $lists = Helper::getResponse('GET', $url, $token);
        // enter lists
        foreach ($lists['entries'] as $index => $list) {
            $l = new AweberLists();
            $l->list_id = $list['id'];
            $l->account_id = $account_id;
            $l->name = $list['name'];
            $l->created_at = Carbon::now();
            $l->save();
        }
    }

    public static function AjaxGetResponse($method, $url, $token)
    {
        $client = new Client();
        $headers = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json'
        ];

        $response = $client->request($method, $url, ['headers' => $headers]);
        $body = json_decode($response->getBody(), true);
        return $body;
    }

    public static function AjaxRefreshToken($refresh_token)
    {
        $client = new Client();
        $url = 'https://auth.aweber.com/oauth2/token';
        if (env('APP_ENV') == 'local') {
            $clientId = env('AWEBER_CLIENT_ID');
            $clientSecret = env('AWEBER_CLIENT_SECRET');
        } else {
            $clientId = env('AWEBER_PRODUCTION_CLIENT_ID');
            $clientSecret = env('AWEBER_PRODUCTION_CLIENT_SECRET');
        }


        $response = $client->post(
            $url,
            [
                'auth' => [
                    $clientId, $clientSecret
                ],
                'json' => [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $refresh_token
                ]
            ]
        );

        $body = $response->getBody();
        return $body;
    }

    public static function GetToken($account_id)
    {
        $account = AweberAccounts::where('account_id', '=', $account_id)->first();
        return $account->access_token;
    }

    public static function getNewDate()
    {
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('US/Mountain'));
        return $now;
    }

    public static function getNewDateEST()
    {
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('America/New_York'));
        return $now;
    }

    /** Growth will get all initial FREE and FE + UPSELL subscriber list **/
    public static function GetGrowthResponse($label, $account_id, $from)
    {
        $end_date = Helper::getNewDateEST()->format('Y-m-d H:i:s');
        $data = [];
        $data['from'] = $from;
        $data['types'] = 1;
        $data['account_id'] = $account_id;

        $token = Helper::GetToken($account_id);

        // For Leads we will use free, quiz, hotlist list,
        // For Cust we will use FE list only

        if ($label == '15manifest_leads') {
            $list_array = ['4746400', '4554052', '4866287', '5619018', '5648287'];
        } else if ($label == '15_manifest_customers') {
            $list_array = ['4951974', '4554035'];
        } else if ($label == 'qmanifest_leads') {
            $list_array = ['4921610'];
        } else if ($label == 'qmanifest_customers') {
            $list_array = ['5021558'];
        } else if ($label == 'amazeyou_leads') {
            $list_array = ['4171322', '5263634'];
        } else if ($label == 'amazeyou_customers') {
            $list_array = ['4171539', '5230268', '5300209'];
        } else if ($label == 'wactivator_leads') {
            $list_array = ['5048209', '5575931'];
        } else if ($label == 'wactivator_customers') {
            $list_array = ['5048221'];
        } else if ($label == '15weight_leads') {
            $list_array = ['5310668'];
        } else if ($label == '15weight_customers') {
            $list_array = ['5310676'];
        } else if ($label == 'millionb_leads') {
            $list_array = ['4209904', '4211406', '4215573'];
        } else if ($label == 'millionb_customers') {
            $list_array = ['4211417'];
        } else if ($label == 'ancientsec_leads') {
            $list_array = ['3846073', '3859242', '3854851'];
        } else if ($label == 'ancientsec_customers') {
            $list_array = ['3849181'];
        } else if ($label == '15happy_leads') {
            $list_array = ['5654854'];
        } else if ($label == '15happy_customers') {
            $list_array = ['5653790'];
        } else if ($label == 'godfreq_leads') {
            $list_array = ['5897373', '5897376'];
        } else if ($label == 'godfreq_customers') {
            $list_array = ['5897359'];
        }

        for ($i = 0; $i <= 2; $i++) {
            $client = new Client();

            // START 24 HOUR, 7 DAYS, 30 DAYS FILTER
            switch ($i) {
                case 0: {
                        $start_date = Helper::getNewDateEST()->modify('-24 hour')->format('Y-m-d');
                        $date_label = 'Last 24 Hours';
                    }
                    break;
                case 1: {
                        $start_date = Helper::getNewDateEST()->modify('-7 day')->format('Y-m-d');
                        $date_label = 'Last 7 Days';
                    }
                    break;
                case 2: {
                        $start_date = Helper::getNewDateEST()->modify('-30 day')->format('Y-m-d');
                        $date_label = 'Last 30 Days';
                    }
                    break;
            }
            // END 24 HOUR, 7 DAYS, 30 DAYS FILTER

            // START GET EACH LIST TOTAL SIZE
            $growth_value = 0;
            foreach ($list_array as $list) {
                $url = 'https://api.aweber.com/1.0/accounts/' . $account_id . '/lists/' . $list . '/subscribers?ws.op=find&subscribed_after=' . $start_date . '&subscribed_before=' . $end_date . '&ws.show=total_size';

                $headers = [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json'
                ];

                try {
                    $response = $client->request('GET', $url, ['headers' => $headers]);
                    $body = json_decode($response->getBody(), true);
                } catch (Exception $e) {
                    sleep(62);
                }

                $growth_value += $body;
            }
            // $data['value'][] = $date_label . ' : ' . number_format($growth_value);
            $data['times']["$date_label"] = number_format($growth_value);
            // END GET EACH LIST TOTAL SIZE
        }

        return $data;
    }

    public static function GetSizeResponse($label, $account_id, $from)
    {
        $now_total_subscribers = 0;
        $days_7_total_subscribers = 0;
        $days_30_total_subscribers = 0;

        $data = [];
        $data['from'] = $from;
        $data['types'] = 2;
        $data['account_id'] = $account_id;
        $token = Helper::GetToken($account_id);

        // START GET LIST ARRAY FUNCTION
        $list_array = Helper::IfConditionForSizeAndCLick($label);
        // END GET LIST ARRAY FUNCTION

        for ($i = 0; $i <= 2; $i++) {
            $client = new Client();

            switch ($i) {
                case 0: {
                        $date_label = 'Now';
                    }
                    break;
                case 1: {
                        $start_date = Helper::getNewDateEST()->modify('-7 day')->format('Y-m-d');
                        $date_label = '7 Days Ago';
                    }
                    break;
                case 2: {
                        $start_date = Helper::getNewDateEST()->modify('-30 day')->format('Y-m-d');;
                        $date_label = '30 Days Ago';
                    }
                    break;
            }

            // START GET EACH LIST TOTAL SIZE
            foreach ($list_array as $list) {

                if ($date_label == 'Now') {
                    $url = 'https://api.aweber.com/1.0/accounts/' . $account_id . '/lists/' . $list . '/subscribers?ws.op=find&status=subscribed&ws.show=total_size';
                } else {
                    $url = 'https://api.aweber.com/1.0/accounts/' . $account_id . '/lists/' . $list . '/subscribers?ws.op=find&status=subscribed&subscribed_after=' . $start_date . '&ws.show=total_size';
                }

                $headers = [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json'
                ];
                try {
                    $response = $client->request('GET', $url, ['headers' => $headers]);
                    $body = json_decode($response->getBody(), true);
                } catch (Exception $e) {
                    sleep(62);
                }


                //START STORE NOW TOTAL SUBSCRIBERS
                if ($date_label == 'Now') {
                    $now_total_subscribers += $body;
                } else if ($date_label == '7 Days Ago') {
                    $days_7_total_subscribers += $body;
                } else {
                    $days_30_total_subscribers += $body;
                }
                //END STORE NOW TOTAL SUBSCRIBERS

            }
            if ($date_label == 'Now') {
                $data['times']["$date_label"] = number_format($now_total_subscribers);
            } else if ($date_label == '7 Days Ago') {
                $data['times']["$date_label"] = number_format($now_total_subscribers - $days_7_total_subscribers);
            } else {
                $data['times']["$date_label"] = number_format($now_total_subscribers - $days_30_total_subscribers);
            }
            // END GET EACH LIST TOTAL SIZE
        }

        return $data;
    }

    public static function GetClickResponse($label, $account_id, $from)
    {
        $last_7_avg_result = 0;
        $last_14_avg_result = 0;
        $last_30_avg_result = 0;

        $data = [];
        $data['from'] = $from;
        $data['types'] = 3;
        $data['account_id'] = $account_id;
        $token = Helper::GetToken($account_id);
        $headers = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json'
        ];

        // START GET LIST ARRAY FUNCTION
        $list_array = Helper::IfConditionForSizeAndCLick($label);
        // END GET LIST ARRAY FUNCTION

        foreach ($list_array as $list) {
            $client = new Client();
            try {
                // START GET TOTAL BROADCASTS SIZE
                $total_size_link = 'https://api.aweber.com/1.0/accounts/' . $account_id . '/lists/' . $list . '/broadcasts/total?status=sent';

                $total_size_response = $client->request('GET', $total_size_link, ['headers' => $headers]);
                $total_size_body = json_decode($total_size_response->getBody(), true);

                $total_size = ($total_size_body['total_size'] >= 30) ? 30 : $total_size_body['total_size'];
                // END GET TOTAL BROADCASTS SIZE

                // START GET BROADCASTS
                if ($total_size != 0) {
                    $url = 'https://api.aweber.com/1.0/accounts/' . $account_id . '/lists/' . $list . '/broadcasts?status=sent&ws.start=0&ws.size=' . $total_size;

                    $response = $client->request('GET', $url, ['headers' => $headers]);
                    $body = json_decode($response->getBody(), true);
                }
            } catch (Exception $e) {
                $e->getMessage()['response'];
                sleep(62);
            }
            // END GET BROADCASTS

            // START LOOP 7,14,30 BROADCAST AVG
            if ($total_size != 0) {
                $last_7_avg_result += Helper::GetBroadcastResult(7, $total_size, $body['entries'], $headers);
                $last_14_avg_result += Helper::GetBroadcastResult(14, $total_size, $body['entries'], $headers);
                $last_30_avg_result += Helper::GetBroadcastResult(30, $total_size, $body['entries'], $headers);
            }
            // END LOOP 7,14,30 BROADCAST AVG
        }
        $data['times']['Last 7 Avg'] = number_format($last_7_avg_result);
        $data['times']['Last 14 Avg'] = number_format($last_14_avg_result);
        $data['times']['Last 30 Avg'] = number_format($last_30_avg_result);

        return $data;
    }

    /** Size and Click will get data from broadcast list **/
    public static function IfConditionForSizeAndCLick($label)
    {
        $list_array = [];
        if ($label == '15manifest_leads') {
            // 15manifest_mw_brdcst, digimani_BrdCst, digimani_quiz
            $list_array = ['4796378', '4997061', '5619018'];
        } else if ($label == '15_manifest_customers') {
            $list_array = ['4799132'];
        } else if ($label == 'qmanifest_leads') {
            $list_array = ['4978720'];
        } else if ($label == 'qmanifest_customers') {
            $list_array = ['5003544'];
        } else if ($label == 'amazeyou_leads') {
            $list_array = ['5507737'];
        } else if ($label == 'amazeyou_customers') {
            $list_array = ['5504062'];
        } else if ($label == 'wactivator_leads') {
            $list_array = ['5507692'];
        } else if ($label == 'wactivator_customers') {
            $list_array = ['5504065'];
        } else if ($label == '15weight_leads') {
            $list_array = ['5506211'];
        } else if ($label == '15weight_customers') {
            $list_array = ['5504069'];
        } else if ($label == 'millionb_leads') {
            $list_array = ['4377915'];
        } else if ($label == 'millionb_customers') {
            $list_array = ['4433199'];
        } else if ($label == 'ancientsec_leads') {
            $list_array = ['4377967'];
        } else if ($label == 'ancientsec_customers') {
            $list_array = ['4377968'];
        } else if ($label == '15happy_leads') {
            // due to no broadcast list we use free list
            $list_array = ['5654854'];
        } else if ($label == '15happy_customers') {
            // due to no broadcast list we use fe + upsell list
            $list_array = ['5653790', '5654845', '5654846', '5654847'];
        } else if ($label == 'godfreq_leads') {
            // due to no broadcast list we use leads_ar list
            $list_array = ['5939377'];
        } else if ($label == 'godfreq_customers') {
            // due to no broadcast list we use cust_ar list
            $list_array = ['5939379'];
        }
        return $list_array;
    }

    public static function GetBroadcastResult($size, $total_size, $broadcasts, $headers)
    {
        $new_size = ($size > $total_size) ? $total_size : $size;
        $total_unique_clicks = 0;
        $sum_unique_clicks = 0;

        for ($i = 0; $i < $new_size; $i++) {
            try {
                $client = new Client();
                $obj = $broadcasts[$i];

                $self_link = $obj['self_link'];

                $broadcast_response = $client->request('GET', $self_link, ['headers' => $headers]);
                $broadcast_body = json_decode($broadcast_response->getBody(), true);
                $total_unique_clicks += $broadcast_body['stats']['unique_clicks'];
            } catch (Exception $e) {
                $e->getMessage()['response'];
                sleep(62);
            }
        }
        $sum_unique_clicks += $total_unique_clicks / $size;

        return intval($sum_unique_clicks);
    }

    public static function getCBSale($minusDay, $account, $account_type, $list_type)
    {
        $data = [];
        // '101FB2C'
        $affiliate_accounts = ['magicplay', 'verifydata', 'leekuanyew'];
        $godfreq_affiliate_accounts = ['magicplay'];

        $dateDay = Helper::getNewDate()->modify($minusDay)->format('d');
        $dateMonth = Helper::getNewDate()->modify($minusDay)->format('m');
        $dateYear = Helper::getNewDate()->modify($minusDay)->format('Y');

        $lists = Helper::getTIDList($account, $account_type);

        if ($minusDay == '-7 day') {
            $times = 1;
        } else if ($minusDay == '-60 day') {
            $times = 2;
        } else {
            $times = 3;
        }

        // $url = "https://api.clickbank.com/rest/1.3/analytics/vendor/tracking_id/summary?account=15manifest&summaryType=AFFILIATE_ONLY&dimensionFilter=15m_quiz01&select=NET_SALE_AMOUNT&select=HOP_COUNT&startDate=$dateYear-$dateMonth-$dateDay&endDate=" . Helper::getNewDate()->modify('-1 day')->format('Y-m-d');

        // $page = 1;
        // $result = curl_init();
        // curl_setopt($result, CURLOPT_URL, $url);
        // curl_setopt($result, CURLOPT_HEADER, false);
        // curl_setopt($result, CURLOPT_HTTPGET, false);
        // curl_setopt($result, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($result, CURLOPT_TIMEOUT, 360);
        // curl_setopt($result, CURLOPT_HTTPHEADER, array("Accept: application/json", "Authorization: " . env('CLICKBANK_CLIENT_ID') . ":" . env('CLICKBANK_CLIENT_SECRET'), "page:$page"));

        // $api_result = json_decode(curl_exec($result), true);
        // echo $url;
        // dd($api_result);

        try {
            if ($account == '15manifest') {
                foreach ($affiliate_accounts as $aa) {
                    $url = "https://api.clickbank.com/rest/1.3/analytics/affiliate/TRACKING_ID/?account=$aa&select=NET_SALE_AMOUNT&select=HOP_COUNT&startDate=$dateYear-$dateMonth-$dateDay&endDate=" . Helper::getNewDate()->format('Y-m-d');

                    // GET DATA AND SET TO $all_data
                    $results[] = Helper::getSalesByARSResponse($url, $aa);
                }
            } elseif ($account == 'godfreq') {
                foreach ($godfreq_affiliate_accounts as $godfreq_aff_acc) {
                    $url = "https://api.clickbank.com/rest/1.3/analytics/affiliate/TRACKING_ID/?account=$godfreq_aff_acc&select=NET_SALE_AMOUNT&select=HOP_COUNT&startDate=$dateYear-$dateMonth-$dateDay&endDate=" . Helper::getNewDate()->format('Y-m-d');

                    // GET DATA AND SET TO $all_data
                    $results[] = Helper::getSalesByARSResponse($url, $godfreq_aff_acc);
                }
            } else {
                $url = "https://api.clickbank.com/rest/1.3/analytics/affiliate/TRACKING_ID/?account=$account&select=NET_SALE_AMOUNT&select=HOP_COUNT&startDate=$dateYear-$dateMonth-$dateDay&endDate=" . Helper::getNewDate()->format('Y-m-d');

                // GET DATA AND SET TO $all_data
                $results[] = Helper::getSalesByARSResponse($url, $account);
            }

            if ($account_type == 'digimani') {
                for ($i = 1; $i <= 11; $i++) {
                    $i = sprintf("%02d", $i);
                    $url = "https://api.clickbank.com/rest/1.3/analytics/vendor/tracking_id/summary?account=15manifest&summaryType=AFFILIATE_ONLY&dimensionFilter=dmar$i&select=NET_SALE_AMOUNT&select=HOP_COUNT&startDate=$dateYear-$dateMonth-$dateDay&endDate=" . Helper::getNewDate()->format('Y-m-d');

                    $result = curl_init();
                    curl_setopt($result, CURLOPT_URL, $url);
                    curl_setopt($result, CURLOPT_HEADER, false);
                    curl_setopt($result, CURLOPT_HTTPGET, false);
                    curl_setopt($result, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($result, CURLOPT_TIMEOUT, 360);
                    curl_setopt($result, CURLOPT_HTTPHEADER, array("Accept: application/json", "Authorization: " . env('CLICKBANK_CLIENT_ID') . ":" . env('CLICKBANK_CLIENT_SECRET')));

                    $api_result = json_decode(curl_exec($result), true);

                    if ($api_result['rows'] != null) {
                        $api_result['rows']['row']['account'] = 'digimani';
                        $data[] = $api_result['rows']['row'];
                    }
                }
                $results[] = $data;
            }

            if ($account_type == '15manifest_quiz') {
                for ($i = 1; $i <= 11; $i++) {
                    $i = sprintf("%02d", $i);
                    $url = "https://api.clickbank.com/rest/1.3/analytics/vendor/tracking_id/summary?account=15manifest&summaryType=AFFILIATE_ONLY&dimensionFilter=15m_quiz$i&select=NET_SALE_AMOUNT&select=HOP_COUNT&startDate=$dateYear-$dateMonth-$dateDay&endDate=" . Helper::getNewDate()->format('Y-m-d');

                    $result = curl_init();
                    curl_setopt($result, CURLOPT_URL, $url);
                    curl_setopt($result, CURLOPT_HEADER, false);
                    curl_setopt($result, CURLOPT_HTTPGET, false);
                    curl_setopt($result, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($result, CURLOPT_TIMEOUT, 360);
                    curl_setopt($result, CURLOPT_HTTPHEADER, array("Accept: application/json", "Authorization: " . env('CLICKBANK_CLIENT_ID') . ":" . env('CLICKBANK_CLIENT_SECRET')));

                    $api_result = json_decode(curl_exec($result), true);

                    if ($api_result['rows'] != null) {
                        $api_result['rows']['row']['account'] = 'digimani';
                        $data[] = $api_result['rows']['row'];
                    }
                }
                $results[] = $data;
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        foreach ($results as $result) {
            foreach ($result as $final_result) {
                $data[] = $final_result;
            }
        }

        Helper::setResult($lists, $data, $account, $times, $list_type);
    }

    public static function setResult($lists, $all_data, $account, $times, $list_type)
    {
        $list_name = $lists['account_type'];
        $account_id = Helper::getAccountID($account);

        $lists = $lists['list'];
        $hop_count = [];
        $revenue = [];
        $tracking_id = [];

        foreach ($all_data as $ad) {
            // FOR TRACKING ID digi_qmanifest2_gift AND digi_wactivator1 TAKE FROM MAGICPLAY NOT LEEKUANYEW
            if ($ad['dimensionValue'] != 'digi_qmanifest2_gift' || $ad['dimensionValue'] != 'digi_wactivator1' && $ad['account'] != 'leekuanyew') {
                $hop_count[] = $ad['data']['0']['value']['$'];
                $revenue[] = $ad['data']['1']['value']['$'];
                $tracking_id[] = $ad['dimensionValue'];
            }
        }

        foreach ($lists as $list) {
            $data[$list]['tracking_id'] = $list;
            $data[$list]['ar'] = Helper::getARnumber($list);
            $data[$list]['account_id'] = $account_id;
            $data[$list]['list_name'] = $list_name;
            $data[$list]['times'] = $times;

            if (in_array($list, $tracking_id)) {
                $index = array_search($list, $tracking_id);
                $data[$list]['revenue'] = $revenue[$index];
                $data[$list]['epc'] = ($hop_count[$index] == 0) ? 0 : ($revenue[$index] / $hop_count[$index]);
                $data[$list]['hop_count'] = $hop_count[$index];
            } else {
                $data[$list]['revenue'] = 0;
                $data[$list]['epc'] = 0;
                $data[$list]['hop_count'] = 0;
            }
        }

        // INSERT INTO DATABASE
        foreach ($data as $d) {
            // IF LOOP UNTIL DIGIMANI, LIST NAME DIFFERENT
            $d['list_name'] = ($d['list_name'] == 'digimani') ? 'digimani/digimani_quiz' : $d['list_name'];
            $get_account = SalesByARS::where('times', '=', $times)->where('list_name', '=', $d['list_name'])->where('tracking_id', '=', $d['tracking_id'])->get();
            if ($get_account->count() == 0) {
                $sales_by_ars = new SalesByARS();
                $sales_by_ars->account_id = $d['account_id'];
                $sales_by_ars->list_type = $list_type;
                $sales_by_ars->list_name = $d['list_name'];
                $sales_by_ars->ar_number = $d['ar'];
                $sales_by_ars->tracking_id = $d['tracking_id'];
                $sales_by_ars->times = $d['times'];
                $sales_by_ars->revenue = $d['revenue'];
                $sales_by_ars->hops = $d['hop_count'];
                $sales_by_ars->EPC = $d['epc'];
                $sales_by_ars->save();
            } else {
                SalesByARS::where('id', '=', $get_account[0]->id)->update(['revenue' => $d['revenue'], 'hops' => $d['hop_count'], 'EPC' => $d['epc'], 'updated_at' => Carbon::now()]);
            }
        }
    }

    public static function getTIDList($account, $account_type)
    {
        $list = '';
        switch ($account) {
            case 'godfreq':
                if ($account_type == 'free') {
                    $data['list'] = [
                        'godfreq_leads_to_qmc_mwg_01',
                        'godfreq_leads_to_qmc_01',
                        'godfreq_leads_to_15m_quiz_01',
                        'godfreq_leads_to_15m_01',
                        'godfreq_leads_to_natvision_01',
                        'godfreq_leads_to_amazeyou2_cymm_01',
                        'godfreq_leads_to_amazeyou2_01',
                        'godfreq_leads_to_15weight_01',
                        'godfreq_leads_to_15happy_01',
                        'godfreq_leads_to_15happy_02',
                    ];
                } else {
                    $data['list'] = [
                        'godfreq_cust_to_qmc_mwg_01',
                        'godfreq_cust_to_qmc_01',
                        'godfreq_cust_to_15m_quiz_01',
                        'godfreq_cust_to_15m_01',
                        'godfreq_cust_to_natvision_01',
                        'godfreq_cust_to_amazeyou2_cymm_01',
                        'godfreq_cust_to_amazeyou2_01',
                        'godfreq_cust_to_15weight_01',
                        'godfreq_cust_to_15happy_01',
                        'godfreq_cust_to_15happy_02',
                    ];
                }

                break;
            case 'wactivator':
                if ($account_type == 'free') {
                    $data['list'] = [
                        'wact_free_to_qmanifest_mwg_01',
                        'wact_free_to_millionb_01',
                        'wact_free_to_millionb_02',
                        'wact_free_to_amazeyou2_cymm_01',
                        'wact_free_to_lottery60k_01',
                        'wact_free_to_lottery60k_02',
                        'wact_free_to_15manifest_01',
                        'wact_free_to_qmanifest_01',
                        'wact_free_to_amazeyou2_01',
                        'wact_free_to_ancientsec_01',
                        'wact_free_to_15weight_01',
                        'wact_free_to_sbs_01',
                        'wact_free_to_sbs_02',
                        'wact_free_to_qam_01',
                        'wact_free_to_qam_02',
                        'wact_free_to_pnmanifest_01',
                        'wact_free_to_sleepwaves_01',
                    ];
                } else {
                    $data['list'] = [
                        'wact_cust_to_qmanifest_mwg_01',
                        'wact_cust_ar_to_millionb_01',
                        'wact_cust_ar_to_millionb_02',
                        'wact_cust_ar_to_amazeyou2_cymm_01',
                        'wact_cust_ar_to_lottery60k_01',
                        'wact_cust_ar_to_lottery60k_02',
                        'wact_cust_ar_to_15manifest_01',
                        'wact_cust_ar_to_qmanifest_01',
                        'wact_cust_ar_to_amazeyou2_01',
                        'wact_cust_ar_to_ancientsec_01',
                        'wact_cust_ar_to_15weight_01',
                        'wact_cust_ar_to_pnmanifest_01',
                        'wact_cust_ar_to_sleepwaves_01',
                        'wact_cust_ar_to_sbs_01',
                        'wact_cust_ar_to_sbs_02',
                        'wact_cust_ar_to_qam_01',
                        'wact_cust_ar_to_qam_02',
                    ];
                }

                break;
            case 'amazeyou':
                if ($account_type == 'free') {
                    $data['list'] = [
                        'amzyu_free_to_qmanifest_mwg',
                        'amzyu_free_to_qmanifest',
                        'amzyu_free_to_wactivator',
                        'amzyu_free_to_wact_tme',
                        'amzyu_free_to_lottery60k',
                        'amzyu_free_to_15manifest',
                        'amzyu_free_to_15m_me',
                        'amzyu_free_to_15weight',
                        'amzyu_free_to_15w_mplwl',
                        'amzyu_free_to_sleepwaves',
                        'amzyu_free_to_mtimewarp',
                        'amzyu_free_to_medicicode',
                        'amzyu_free_to_millionb',
                        'amzyu_free_to_ancientsec',
                        'amzyu_free_to_pnmanifest',
                        'amzyu_free_to_mmswitch',
                    ];
                } else {
                    $data['list'] = [
                        'amzyu_cust_to_qmanifest_mwg',
                        'amzyu_cust_to_qmanifest',
                        'amzyu_cust_to_wactivator',
                        'amzyu_cust_to_wac_tme',
                        'amzyu_cust_to_lottery60k',
                        'amzyu_cust_to_15manifest',
                        'amzyu_cust_to_15m_me',
                        'amzyu_cust_to_15weight',
                        'amzyu_cust_to_15w_mplwl',
                        'amzyu_cust_to_sleepwaves',
                        'amzyu_cust_to_mtimewarp',
                        'amzyu_cust_to_medicicode',
                        'amzyu_cust_to_millionb',
                        'amzyu_cust_to_ancientsec',
                        'amzyu_cust_to_pnmanifest',
                        'amzyu_cust_to_mmswitch'
                    ];
                }
                break;
            case '15manifest':
                if ($account_type == 'digimani') {
                    $data['list'] = [
                        'dmar01',
                        'dmar02',
                        'dmar03',
                        'dmar04',
                        'dmar05',
                        'dmar06',
                        'dmar07',
                        'dmar08',
                        'dmar09',
                        'dmar10',
                        'dmar11',
                        'digi_qmanifest2_gift',
                        'digi_15weight2',
                        'digi_wactivator1',
                        'digi_15weight1',
                    ];
                } else if ($account_type == '15manifest_world_pp') {
                    $data['list'] = [
                        'mwg_frm_15m_cust',
                        '15m_qmanifest1',
                        '15m_amazeyou2_01',
                        '15m_15weight01',
                        '15m_qmanifest2',
                        '15m_amazeyou2_02',
                        'cymm_frm_15m_wrld_pp',
                        '15m_millionb01',
                        '15m_millionb02',
                        '15m_wactivator01',
                        '15m_wactivator02',
                        '15m_medicicode01',
                        '15m_medicicode02',
                        '15m_mtimewarp01',
                        '15m_ancientsec01',
                        '15m_sbs01',
                        '15m_sbs02',
                        '15m_15weight02',
                        '15m_amazeyou03',
                        '15m_sleepwaves01',
                        '15m_pnmanifest01',
                        '15m_manimir1',
                        '15m_manimir2',
                        '15m_lottery90k01',
                        '15m_lottery90k02',
                    ];
                } else if ($account_type == '15manifest_mw') {
                    $data['list'] = [
                        'mw_qmanifest2_gift',
                        'mw_qmanifest1',
                        'mw_15weight2',
                        'mw_15weight1',
                        'mw_pnmanifest2_gift',
                        'mw_pnmanifest1',
                        'mw1ezbattery',
                        'mw2ezbattery',
                        'mw_reiki2',
                        'mw_reiki1',
                    ];
                } else if ($account_type == '15manifest_me') {
                    $data['list'] = [
                        'me_qmanifest2_gift',
                        'me_qmanifest1',
                        'me_pnmanifest2_gift',
                        'me_pnmanifest1',
                        'me_15weight1',
                        'me_15weight2'
                    ];
                } else if ($account_type == '15manifest_quiz') {
                    $data['list'] = [
                        '15m_quiz01',
                        '15m_quiz02',
                        '15m_quiz03',
                        '15m_quiz04',
                        '15m_quiz05',
                        '15m_quiz06',
                        '15m_quiz07',
                        '15m_quiz08',
                        '15m_quiz09',
                        '15m_quiz10',
                        '15m_quiz11',
                        '15m_quiz_to_qmanifest2_gift',
                        '15m_quiz_to_15weight2',
                        '15m_quiz_to_wactivator1',
                        '15m_quiz_to_15weight1',
                    ];
                }
                break;
        }
        $data['account_type'] = $account_type;
        return $data;
    }

    public static function getARnumber($list)
    {
        $ar = '';
        switch ($list) {
            case 'digi_qmanifest2_gift':
                $ar = '12';
                break;
            case 'digi_15weight2':
                $ar = '13';
                break;
            case 'digi_wactivator1':
                $ar = '14';
                break;
            case 'digi_15weight1':
                $ar = '15';
                break;
            case 'mwg_frm_15m_cust':
                $ar = '1';
                break;
            case '15m_qmanifest1':
                $ar = '6';
                break;
            case '15m_amazeyou2_01':
                $ar = '7';
                break;
            case '15m_15weight01':
                $ar = '8';
                break;
            case '15m_qmanifest2':
                $ar = '9';
                break;
            case '15m_amazeyou2_02':
                $ar = '10';
                break;
            case 'cymm_frm_15m_wrld_pp':
                $ar = '11';
                break;
            case '15m_millionb01':
                $ar = '12';
                break;
            case '15m_millionb02':
                $ar = '13';
                break;
            case '15m_wactivator01':
                $ar = '14';
                break;
            case '15m_wactivator02':
                $ar = '15';
                break;
            case '15m_medicicode01':
                $ar = '16';
                break;
            case '15m_medicicode02':
                $ar = '17';
                break;
            case '15m_mtimewarp01':
                $ar = '18';
                break;
            case '15m_ancientsec01':
                $ar = '20';
                break;
            case '15m_sbs01':
                $ar = '22';
                break;
            case '15m_sbs02':
                $ar = '24';
                break;
            case '15m_15weight02':
                $ar = '25';
                break;
            case '15m_amazeyou03':
                $ar = '26';
                break;
            case '15m_sleepwaves01':
                $ar = '27';
                break;
            case '15m_pnmanifest01':
                $ar = '28';
                break;
            case '15m_manimir1':
                $ar = '29';
                break;
            case '15m_manimir2':
                $ar = '30';
                break;
            case '15m_lottery90k01':
                $ar = '32';
                break;
            case '15m_lottery90k02':
                $ar = '34';
                break;
            case 'mw_qmanifest2_gift':
                $ar = '11';
                break;
            case 'mw_qmanifest1':
                $ar = '12';
                break;
            case 'mw_15weight2':
                $ar = '13';
                break;
            case 'mw_15weight1':
                $ar = '14';
                break;
            case 'mw_pnmanifest2_gift':
                $ar = '15';
                break;
            case 'mw_pnmanifest1':
                $ar = '16';
                break;
            case 'mw1ezbattery':
                $ar = '17';
                break;
            case 'mw2ezbattery':
                $ar = '18';
                break;
            case 'mw_reiki2':
                $ar = '19';
                break;
            case 'mw_reiki1':
                $ar = '20';
                break;
            case 'me_qmanifest2_gift':
                $ar = '17';
                break;
            case 'me_qmanifest1':
                $ar = '18';
                break;
            case 'me_pnmanifest2_gift':
                $ar = '19';
                break;
            case 'me_pnmanifest1':
                $ar = '20';
                break;
            case 'me_15weight1':
                $ar = '21';
                break;
            case 'me_15weight2':
                $ar = '22';
                break;
            case 'wact_free_to_qmanifest_mwg_01':
                $ar = '8';
                break;
            case 'wact_free_to_millionb_01':
                $ar = '9';
                break;
            case 'wact_free_to_millionb_02':
                $ar = '10';
                break;
            case 'wact_free_to_amazeyou2_cymm_01':
                $ar = '11';
                break;
            case 'wact_free_to_lottery60k_01':
                $ar = '12';
                break;
            case 'wact_free_to_lottery60k_02':
                $ar = '13';
                break;
            case 'wact_free_to_15manifest_01':
                $ar = '14';
                break;
            case 'wact_free_to_qmanifest_01':
                $ar = '15';
                break;
            case 'wact_free_to_amazeyou2_01':
                $ar = '16';
                break;
            case 'wact_free_to_ancientsec_01':
                $ar = '17';
                break;
            case 'wact_free_to_15weight_01':
                $ar = '18';
                break;
            case 'wact_free_to_sbs_01':
                $ar = '19';
                break;
            case 'wact_free_to_sbs_02':
                $ar = '20';
                break;
            case 'wact_free_to_qam_01':
                $ar = '21';
                break;
            case 'wact_free_to_qam_02':
                $ar = '22';
                break;
            case 'wact_free_to_pnmanifest_01':
                $ar = '23';
                break;
            case 'wact_free_to_sleepwaves_01':
                $ar = '24';
                break;
            case 'wact_cust_to_qmanifest_mwg_01':
                $ar = '1';
                break;
            case 'wact_cust_ar_to_millionb_01':
                $ar = '2';
                break;
            case 'wact_cust_ar_to_millionb_02':
                $ar = '3';
                break;
            case 'wact_cust_ar_to_amazeyou2_cymm_01':
                $ar = '4';
                break;
            case 'wact_cust_ar_to_lottery60k_01':
                $ar = '5';
                break;
            case 'wact_cust_ar_to_lottery60k_02':
                $ar = '6';
                break;
            case 'wact_cust_ar_to_15manifest_01':
                $ar = '7';
                break;
            case 'wact_cust_ar_to_qmanifest_01':
                $ar = '8';
                break;
            case 'wact_cust_ar_to_amazeyou2_01':
                $ar = '9';
                break;
            case 'wact_cust_ar_to_ancientsec_01':
                $ar = '10';
                break;
            case 'wact_cust_ar_to_15weight_01':
                $ar = '11';
                break;
            case 'wact_cust_ar_to_pnmanifest_01':
                $ar = '12';
                break;
            case 'wact_cust_ar_to_sleepwaves_01':
                $ar = '13';
                break;
            case 'wact_cust_ar_to_sbs_01':
                $ar = '14';
                break;
            case 'wact_cust_ar_to_sbs_02':
                $ar = '15';
                break;
            case 'wact_cust_ar_to_qam_01':
                $ar = '16';
                break;
            case 'wact_cust_ar_to_qam_02':
                $ar = '17';
                break;
            case 'amzyu_free_to_qmanifest_mwg':
                $ar = '16';
                break;
            case 'amzyu_free_to_qmanifest':
                $ar = '17';
                break;
            case 'amzyu_free_to_wactivator':
                $ar = '18';
                break;
            case 'amzyu_free_to_wact_tme':
                $ar = '19';
                break;
            case 'amzyu_free_to_lottery60k':
                $ar = '20';
                break;
            case 'amzyu_free_to_15manifest':
                $ar = '21';
                break;
            case 'amzyu_free_to_15m_me':
                $ar = '22';
                break;
            case 'amzyu_free_to_15weight':
                $ar = '23';
                break;
            case 'amzyu_free_to_15w_mplwl':
                $ar = '24';
                break;
            case 'amzyu_free_to_sleepwaves':
                $ar = '25';
                break;
            case 'amzyu_free_to_mtimewarp':
                $ar = '26';
                break;
            case 'amzyu_free_to_medicicode':
                $ar = '27';
                break;
            case 'amzyu_free_to_millionb':
                $ar = '28';
                break;
            case 'amzyu_free_to_ancientsec':
                $ar = '29';
                break;
            case 'amzyu_free_to_pnmanifest':
                $ar = '30';
                break;
            case 'amzyu_free_to_mmswitch':
                $ar = '31';
                break;
            case 'amzyu_cust_to_qmanifest_mwg':
                $ar = '1';
                break;
            case 'amzyu_cust_to_qmanifest':
                $ar = '2';
                break;
            case 'amzyu_cust_to_wactivator':
                $ar = '3';
                break;
            case 'amzyu_cust_to_wac_tme':
                $ar = '4';
                break;
            case 'amzyu_cust_to_lottery60k':
                $ar = '5';
                break;
            case 'amzyu_cust_to_15manifest':
                $ar = '6';
                break;
            case 'amzyu_cust_to_15m_me':
                $ar = '7';
                break;
            case 'amzyu_cust_to_15weight':
                $ar = '8';
                break;
            case 'amzyu_cust_to_15w_mplwl':
                $ar = '9';
                break;
            case 'amzyu_cust_to_sleepwaves':
                $ar = '10';
                break;
            case 'amzyu_cust_to_mtimewarp':
                $ar = '11';
                break;
            case 'amzyu_cust_to_medicicode':
                $ar = '12';
                break;
            case 'amzyu_cust_to_millionb':
                $ar = '13';
                break;
            case 'amzyu_cust_to_ancientsec':
                $ar = '14';
                break;
            case 'amzyu_cust_to_pnmanifest':
                $ar = '15';
                break;
            case 'amzyu_cust_to_mmswitch':
                $ar = '16';
                break;
            case 'dmar01':
                $ar = '1';
                break;
            case 'dmar02':
                $ar = '2';
                break;
            case 'dmar03':
                $ar = '3';
                break;
            case 'dmar04':
                $ar = '4';
                break;
            case 'dmar05':
                $ar = '5';
                break;
            case 'dmar06':
                $ar = '6';
                break;
            case 'dmar07':
                $ar = '7';
                break;
            case 'dmar08':
                $ar = '8';
                break;
            case 'dmar09':
                $ar = '9';
                break;
            case 'dmar10':
                $ar = '10';
                break;
            case 'dmar11':
                $ar = '11';
                break;
            case '15m_quiz01':
                $ar = '1';
                break;
            case '15m_quiz02':
                $ar = '2';
                break;
            case '15m_quiz03':
                $ar = '3';
                break;
            case '15m_quiz04':
                $ar = '4';
                break;
            case '15m_quiz05':
                $ar = '5';
                break;
            case '15m_quiz06':
                $ar = '6';
                break;
            case '15m_quiz07':
                $ar = '7';
                break;
            case '15m_quiz08':
                $ar = '8';
                break;
            case '15m_quiz09':
                $ar = '9';
                break;
            case '15m_quiz10':
                $ar = '10';
                break;
            case '15m_quiz11':
                $ar = '11';
                break;
            case '15m_quiz_to_qmanifest2_gift':
                $ar = '12';
                break;
            case '15m_quiz_to_15weight2':
                $ar = '13';
                break;
            case '15m_quiz_to_wactivator1':
                $ar = '14';
                break;
            case '15m_quiz_to_15weight1':
                $ar = '15';
                break;
            case 'godfreq_leads_to_qmc_mwg_01':
                $ar = '1';
                break;
            case 'godfreq_leads_to_qmc_01':
                $ar = '2';
                break;
            case 'godfreq_leads_to_15m_quiz_01':
                $ar = '3';
                break;
            case 'godfreq_leads_to_15m_01':
                $ar = '4';
                break;
            case 'godfreq_leads_to_natvision_01':
                $ar = '5';
                break;
            case 'godfreq_leads_to_amazeyou2_cymm_01':
                $ar = '6';
                break;
            case 'godfreq_leads_to_amazeyou2_01':
                $ar = '7';
                break;
            case 'godfreq_leads_to_15weight_01':
                $ar = '8';
                break;
            case 'godfreq_leads_to_15happy_01':
                $ar = '9';
                break;
            case 'godfreq_leads_to_15happy_02':
                $ar = '10';
                break;
            case 'godfreq_cust_to_qmc_mwg_01':
                $ar = '1';
                break;
            case 'godfreq_cust_to_qmc_01':
                $ar = '2';
                break;
            case 'godfreq_cust_to_15m_quiz_01':
                $ar = '3';
                break;
            case 'godfreq_cust_to_15m_01':
                $ar = '4';
                break;
            case 'godfreq_cust_to_natvision_01':
                $ar = '5';
                break;
            case 'godfreq_cust_to_amazeyou2_cymm_01':
                $ar = '6';
                break;
            case 'godfreq_cust_to_amazeyou2_01':
                $ar = '7';
                break;
            case 'godfreq_cust_to_15weight_01':
                $ar = '8';
                break;
            case 'godfreq_cust_to_15happy_01':
                $ar = '9';
                break;
            case 'godfreq_cust_to_15happy_02':
                $ar = '10';
                break;
        }
        return $ar;
    }

    public static function getSalesByARSResponse($url, $account)
    {
        $data = [];
        $page = 1;
        do {
            $result = curl_init();
            curl_setopt($result, CURLOPT_URL, $url);
            curl_setopt($result, CURLOPT_HEADER, false);
            curl_setopt($result, CURLOPT_HTTPGET, false);
            curl_setopt($result, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($result, CURLOPT_TIMEOUT, 360);
            curl_setopt($result, CURLOPT_HTTPHEADER, array("Accept: application/json", "Authorization: " . env('CLICKBANK_CLIENT_ID') . ":" . env('CLICKBANK_CLIENT_SECRET'), "page:$page"));

            $api_result = json_decode(curl_exec($result), true);

            if (is_array($api_result['rows']) || is_object($api_result['rows'])) {
                if (isset($api_result['rows']['row']['dimensionValue'])) {
                    $row['account'] = $account;
                    $row['dimensionValue'] = $api_result['rows']['row']['dimensionValue'];
                    $row['data'] = $api_result['rows']['row']['data'];
                    $data[] = $row;
                } else {
                    foreach ($api_result['rows']['row'] as $row) {
                        $row['account'] = $account;
                        $data[] = $row;
                    }
                }
            }
            $page++;
        } while (isset($api_result['rows']['row']['99']));

        return $data;
    }

    public static function getAccountID($account)
    {
        $account = AweberAccounts::where('account_name', '=', $account)->first();
        return $account->account_id;
    }

    public static function insertSalesRanking($date, $account)
    {
        if ($account == 'current_month') {
            $lastDay = $date->format('d');
        } else {
            $lastDay = $date->format('t');
        }

        $lastMonth = $date->format('m');
        $lastYear = $date->format('Y');

        foreach (static::$tableArray as $table) {
            $page = 1;
            $revenue = 0;
            do {
                $url = "https://api.clickbank.com/rest/1.3/analytics/vendor/affiliate/?account=$table&select=NET_SALE_AMOUNT&orderBy=NET_SALE_AMOUNT&startDate=$lastYear-$lastMonth-01&endDate=$lastYear-$lastMonth-$lastDay";

                $result = curl_init();
                curl_setopt($result, CURLOPT_URL, $url);
                curl_setopt($result, CURLOPT_HEADER, false);
                curl_setopt($result, CURLOPT_HTTPGET, false);
                curl_setopt($result, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($result, CURLOPT_TIMEOUT, 0);
                curl_setopt($result, CURLOPT_HTTPHEADER, array("Accept: application/json", "Authorization: " . env('CLICKBANK_CLIENT_ID') . ":" . env('CLICKBANK_CLIENT_SECRET'), "page:$page"));

                $api_result = json_decode(curl_exec($result), true);

                if (is_array($api_result['rows']['row']) || is_object($api_result['rows']['row'])) {
                    if (isset($api_result['rows']['row']['dimensionValue'])) {
                        $name = $api_result['rows']['row']['dimensionValue'];
                        if (!in_array($name, static::$excludeAffiliateArray) && !in_array($name, static::$tableArray)) {
                            $revenue += $api_result['rows']['row']['data']['value']['$'];
                        }
                    } else {
                        foreach ($api_result['rows']['row'] as $row) {
                            $name = $row['dimensionValue'];

                            if (!in_array($name, static::$excludeAffiliateArray) && !in_array($name, static::$tableArray)) {
                                $revenue += $row['data']['value']['$'];
                            }
                        }
                    }
                }
                $page++;
            } while (isset($api_result['rows']['row']['99']));

            $get_account = SalesRankingRevenue::where('account', '=', $table)->get();
            if ($account == 'current_month') {
                if ($get_account->count() == 0) {
                    $a = new SalesRankingRevenue();
                    $a->account = $table;
                    $a->currentmonth_revenue = $revenue;
                    $a->save();
                } else {
                    SalesRankingRevenue::where('account', '=', $table)->update(['currentmonth_revenue' => $revenue]);
                }
            } else {

                if ($get_account->count() == 0) {
                    $a = new SalesRankingRevenue();
                    $a->account = $table;
                    $a->lastmonth_revenue = $revenue;
                    $a->save();
                } else {
                    SalesRankingRevenue::where('account', '=', $table)->update(['lastmonth_revenue' => $revenue]);
                }
            }
        }
    }

    // To show incoming traffic status - RM20210106
    public static function insertIncomingTrafficStatus($date, $account)
    {
        if ($account == 'current_month') {
            $lastDay = $date->format('d');
        } else {
            $lastDay = $date->format('t');
        }

        $lastMonth = $date->format('m');
        $lastYear = $date->format('Y');

        foreach (static::$tableArray as $table) {
            $page = 1;
            $hop_count = 0;
            $sales_amount = 0;
            $sales_count = 0;

            do {
                $url = "https://api.clickbank.com/rest/1.3/analytics/vendor/affiliate/?account=$table&select=SALE_COUNT&select=SALE_AMOUNT&select=HOP_COUNT&startDate=$lastYear-$lastMonth-01&endDate=$lastYear-$lastMonth-$lastDay";

                $result = curl_init();
                curl_setopt($result, CURLOPT_URL, $url);
                curl_setopt($result, CURLOPT_HEADER, false);
                curl_setopt($result, CURLOPT_HTTPGET, false);
                curl_setopt($result, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($result, CURLOPT_TIMEOUT, 0);
                curl_setopt($result, CURLOPT_HTTPHEADER, array("Accept: application/json", "Authorization: " . env('CLICKBANK_CLIENT_ID') . ":" . env('CLICKBANK_CLIENT_SECRET'), "page:$page"));

                $api_result = json_decode(curl_exec($result), true);

                // Processing of response
                if (is_array($api_result['rows']['row']) || is_object($api_result['rows']['row'])) {

                    foreach ($api_result['rows']['row'] as $row) {
                        if (isset($row['dimensionValue']) && !empty($row['dimensionValue'])) {
                            $name = $row['dimensionValue']; // Dimension name

                            if (!in_array($name, static::$excludeAffiliateArray) && !in_array($name, static::$tableArray)) {
                                $hop_count += $row['data'][0]['value']['$'];     // HOP count
                                $sales_amount += $row['data'][1]['value']['$'];  // SALE_AMOUNT
                                $sales_count += $row['data'][2]['value']['$'];   // SALE_COUNT
                            }
                        }
                    }
                }

                $page++;
            } while (isset($api_result['rows']['row']['99']));

            if ($account == 'current_month') {

                $get_account = IncomingTrafficStatus::where('account', '=', $table)->get();
                if ($get_account->count() == 0) {
                    $a = new IncomingTrafficStatus();
                    $a->account = $table;
                    $a->current_hopcount = $hop_count;
                    $a->current_salesamount = $sales_amount;
                    $a->current_salescount = $sales_count;
                    $a->save();
                } else {
                    IncomingTrafficStatus::where('account', '=', $table)->update(['current_hopcount' => $hop_count, 'current_salesamount' => $sales_amount, 'current_salescount' => $sales_count]);
                }
            } else {

                $get_account = IncomingTrafficStatus::where('account', '=', $table)->get();
                if ($get_account->count() == 0) {
                    $a = new IncomingTrafficStatus();
                    $a->account = $table;
                    $a->last_hopcount = $hop_count;
                    $a->last_salesamount = $sales_amount;
                    $a->last_salescount = $sales_count;
                    $a->save();
                } else {
                    IncomingTrafficStatus::where('account', '=', $table)->update(['last_hopcount' => $hop_count, 'last_salesamount' => $sales_amount, 'last_salescount' => $sales_count]);
                }
            }
        }
    }

    public static function topAffiliate($minusDay, $table, $times)
    {
        $dateDay = Helper::getNewDate()->modify($minusDay)->format('d');
        $dateMonth = Helper::getNewDate()->modify($minusDay)->format('m');
        $dateYear = Helper::getNewDate()->modify($minusDay)->format('Y');

        $allArray = [];
        foreach (static::$tableArray as $account) {

            if ($table == 'today') {

                $dateDay = Helper::getNewDate()->format('d');
                $dateMonth = Helper::getNewDate()->format('m');
                $dateYear = Helper::getNewDate()->format('Y');

                $url = "https://api.clickbank.com/rest/1.3/analytics/vendor/affiliate/?account=$account&select=SALE_COUNT&select=NET_SALE_AMOUNT&select=REBILL_AMOUNT&select=SALE_AMOUNT&select=UPSELL_AMOUNT&select=HOP_COUNT&orderBy=NET_SALE_AMOUNT&startDate=$dateYear-$dateMonth-$dateDay&endDate=$dateYear-$dateMonth-$dateDay";
            } else if ($table == 'yesterday') {
                $url = "https://api.clickbank.com/rest/1.3/analytics/vendor/affiliate/?account=$account&select=SALE_COUNT&select=NET_SALE_AMOUNT&select=REBILL_AMOUNT&select=SALE_AMOUNT&select=UPSELL_AMOUNT&select=HOP_COUNT&orderBy=NET_SALE_AMOUNT&startDate=$dateYear-$dateMonth-$dateDay&endDate=$dateYear-$dateMonth-$dateDay";
            } else {
                $url = "https://api.clickbank.com/rest/1.3/analytics/vendor/affiliate/?account=$account&select=SALE_COUNT&select=NET_SALE_AMOUNT&select=REBILL_AMOUNT&select=SALE_AMOUNT&select=UPSELL_AMOUNT&select=HOP_COUNT&orderBy=NET_SALE_AMOUNT&startDate=$dateYear-$dateMonth-$dateDay&endDate=" . Helper::getNewDate()->format('Y-m-d');
            }

            $result = curl_init();
            curl_setopt($result, CURLOPT_URL, $url);
            curl_setopt($result, CURLOPT_HEADER, false);
            curl_setopt($result, CURLOPT_HTTPGET, false);
            curl_setopt($result, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($result, CURLOPT_HTTPHEADER, array("Accept: application/json", "Authorization: " . env('CLICKBANK_CLIENT_ID') . ":" . env('CLICKBANK_CLIENT_SECRET')));

            $api_result = json_decode(curl_exec($result), true);
            $total_count = 0;
            if (isset(($api_result['rows']['row'])))
                $total_count = count($api_result['rows']['row']);
            // START EACH ACCOUNT WILL INSERT TOP 10 SALES TO ARRAY
            $i = 0;
            $inserted_data_count = 1;

            if ($total_count > 0) {
                foreach ($api_result['rows']['row'] as $row) {
                    if (isset($row['dimensionValue']) && !empty($row['dimensionValue'])) {
                        $affiliate = $row['dimensionValue'];
                        $rebill = $row['data']['0']['value']['$'];
                        $up_sell = $row['data']['1']['value']['$'];
                        $hop_count = $row['data']['2']['value']['$'];
                        $sales = $row['data']['3']['value']['$'];
                        $front_end = $row['data']['4']['value']['$'];
                        $sale_count = $row['data']['5']['value']['$'];
                        $fe_cvr = ($front_end == 0 || $hop_count == 0) ? 0 : ($sale_count / $hop_count) * 100;

                        $affiliate_comms = Helper::calculateAffiliateCommission($account, $front_end, $up_sell, $rebill, $affiliate, $dateMonth, $dateYear);
                        $affiliate_EPC = ($hop_count == 0) ? 0 : number_format($affiliate_comms / $hop_count, 2);
                        $affiliate = (Helper::getPartnerName($affiliate) == '') ? $affiliate : Helper::getPartnerName($affiliate) . ' (' . $affiliate . ')';

                        if (!in_array($affiliate, static::$excludeAffiliateArray) && !in_array($affiliate, static::$tableArray)) {
                            $data['sales'] = $sales;
                            $data['count'] = $hop_count;
                            $data['account'] = $account;
                            $data['affiliate'] = $affiliate;
                            $data['affiliate_comms'] = $affiliate_comms;
                            $data['affiliate_epc'] = str_replace(',', '', $affiliate_EPC);
                            $data['fe_cvr'] = $fe_cvr;

                            array_push($allArray, $data);
                            $data = [];
                            $inserted_data_count++;
                        }
                        $i++;
                        if ($i >= $total_count) {
                            break;
                        }
                    }
                }
            }
            // while ($inserted_data_count <= 10) {
            //     $affiliate = $api_result['rows']['row'][$i]['dimensionValue'];
            //     $rebill = $api_result['rows']['row'][$i]['data']['0']['value']['$'];
            //     $up_sell = $api_result['rows']['row'][$i]['data']['1']['value']['$'];
            //     $hop_count = $api_result['rows']['row'][$i]['data']['2']['value']['$'];
            //     $sales = $api_result['rows']['row'][$i]['data']['3']['value']['$'];
            //     $front_end = $api_result['rows']['row'][$i]['data']['4']['value']['$'];


            //     $affiliate_comms = Helper::calculateAffiliateCommission($account, $front_end, $up_sell, $rebill, $affiliate);
            //     $affiliate_EPC = ($hop_count == 0) ? 0 : number_format($affiliate_comms / $hop_count, 2);
            //     $affiliate = (Helper::getPartnerName($affiliate) == '') ? $affiliate : Helper::getPartnerName($affiliate) . ' (' . $affiliate . ')';

            //     //START SKIP IF MEET EXCLUDE ID
            //     if (!in_array($affiliate, $excludeAffiliateArray)) {
            //         $data['sales'] = $sales;
            //         $data['count'] = $hop_count;
            //         $data['account'] = $account;
            //         $data['affiliate'] = $affiliate;
            //         $data['affiliate_comms'] = $affiliate_comms;
            //         $data['affiliate_epc'] = $affiliate_EPC;

            //         array_push($allArray, $data);
            //         $data = [];
            //         $inserted_data_count++;
            //     }
            //     //END SKIP IF MEET EXCLUDE ID
            //     $i++;
            // }
            // END EACH ACCOUNT WILL INSERT TOP 10 SALES TO ARRAY
        }

        arsort($allArray);
        $allArray = array_slice($allArray, 0, 10);

        foreach ($allArray as $a) {
            $ta = new TopAffiliate();
            $ta->times = $times;
            $ta->affiliate_id = $a['affiliate'];
            $ta->vendor_id = $a['account'];
            $ta->hop_count = $a['count'];
            $ta->fe_cvr = $a['fe_cvr'];
            $ta->affiliate_epc = str_replace(',', '', $a['affiliate_epc']);
            $ta->affiliate_revenue = $a['affiliate_comms'];
            $ta->vendor_revenue = $a['sales'];
            $ta->created_at = Carbon::now();
            $ta->save();
        }
    }

    public static function calculateAffiliateCommission($account, $front_end, $up_sell, $rebill, $affiliate, $month, $year)
    {
        //fe = frontend sales
        //us = upsell
        //rp = rebill amount

        switch ($account) {
            case '15manifest': {
                    // by date 94%
                    if ($affiliate == '101fb2c') {
                        if ($year <= '2019') {
                            $fe = 0.90;
                            $us = 0.90;
                            $rp = 0.25;
                        } else {
                            $fe = 0.94;
                            $us = 0.94;
                            $rp = 0.25;
                        }
                    }
                    // 90%
                    else if (
                        $affiliate == 'achieversu' || $affiliate == 'affdweeb' || $affiliate == 'amazeyou' || $affiliate == 'attractg' || $affiliate == 'authnumber' ||
                        $affiliate == 'b2cms' || $affiliate == 'backachev' || $affiliate == 'beaconsv' || $affiliate == 'beautyfb' || $affiliate == 'bellyfatf' ||
                        $affiliate == 'bustup' || $affiliate == 'cflpx' ||  $affiliate == 'chakraaff' || $affiliate == 'confidentx' ||  $affiliate == 'chrisuls' ||
                        $affiliate == 'cosmicorde' ||  $affiliate == 'diabeteslh' || $affiliate == 'diabetesv' || $affiliate == 'dogbreedsh' || $affiliate == 'dreamsu' ||
                        $affiliate == 'greyhairv' || $affiliate == 'growthsu' || $affiliate == 'hairv' ||  $affiliate == 'healthsu' ||  $affiliate == 'healthv' ||
                        $affiliate == 'heartsu' || $affiliate == 'herpev' ||  $affiliate == 'hodemand' ||  $affiliate == 'homeweal' ||  $affiliate == 'hypnogene' ||
                        $affiliate == 'ivpaysv10' || $affiliate == 'ivpaysv20' ||  $affiliate == 'ldmastery' ||  $affiliate == 'meditatev' || $affiliate == 'memoryx' ||
                        $affiliate == 'mentis' || $affiliate == 'moneymas' || $affiliate == 'mproduct2k' || $affiliate == 'naturalc' || $affiliate == 'neuromax' ||
                        $affiliate == 'nutraact' || $affiliate == 'nutrifront' || $affiliate == 'nutronow' || $affiliate == 'omemall' || $affiliate == 'oxysolu' ||
                        $affiliate == 'photomem' || $affiliate == 'pnhaff' || $affiliate == 'pnhealing' || $affiliate == 'pnhnew' || $affiliate == 'purechakra' ||
                        $affiliate == 'pureheal' || $affiliate == 'purereik' || $affiliate == 'sanddyjv10' || $affiliate == 'secretnum' || $affiliate == 'socialhj' ||
                        $affiliate == 'successmx' || $affiliate == 'survivalli' || $affiliate == 'svpayjv10' || $affiliate == 'lteslacode' || $affiliate == 'textaffirm' ||
                        $affiliate == 'the8wonder' || $affiliate == 'transformc' || $affiliate == 'tubeloom' || $affiliate == 'ucashacad' || $affiliate == 'ulifecode' ||
                        $affiliate == 'unifest' || $affiliate == 'unravel' || $affiliate == 'vibram' || $affiliate == 'visionv' || $affiliate == 'vogenisis' ||
                        $affiliate == 'wealthacti' || $affiliate == 'wealthdna'
                    ) {
                        $fe = 0.90;
                        $us = 0.90;
                        $rp = 0.25;
                    } else if (
                        $affiliate == 'leekuanyew' || $affiliate == 'worklifeg' || $affiliate == 'yuxiong94' ||
                        $affiliate == 'coachmm' || $affiliate == 'davandrews' || $affiliate == 'fgem7' || $affiliate == 'instasells' || $affiliate == 'jadle' ||
                        $affiliate == 'jadtube' || $affiliate == 'kittay' || $affiliate == 'lightwks' || $affiliate == 'onsalesnow' || $affiliate == 'steelersqj' ||
                        $affiliate == 'twittersel' || $affiliate == '1crypto' || $affiliate == '224042' || $affiliate == '303mickey' || $affiliate == 'alliesintl' ||
                        $affiliate == 'awklight' || $affiliate == 'beapicou' || $affiliate == 'blueshanti' || $affiliate == 'clickchao' || $affiliate == 'copyjake' ||
                        $affiliate == 'court222' || $affiliate == 'danbrands' || $affiliate == 'donnarama' || $affiliate == 'drstephen' || $affiliate == 'ggreg7' ||
                        $affiliate == 'indulis' || $affiliate == 'inspirehit' || $affiliate == 'jalliances' || $affiliate == 'jamesprez' || $affiliate == 'kamecom' ||
                        $affiliate == 'kohrlaw' || $affiliate == 'l0v3ja' || $affiliate == 'latitude90' || $affiliate == 'malchiang' || $affiliate == 'mutchun' ||
                        $affiliate == 'naiwanat' || $affiliate == 'naventures' || $affiliate == 'nlgoodsllc' || $affiliate == 'olyasor' || $affiliate == 'oxana' ||
                        $affiliate == 'prsaputo8' || $affiliate == 'robengine' || $affiliate == 'scrib77' || $affiliate == 'segura8' || $affiliate == 'skicker33' ||
                        $affiliate == 'sonalpand' || $affiliate == 'stuart74' || $affiliate == 'stufflogue' || $affiliate == 'ten12golf' || $affiliate == 'toprateapp' ||
                        $affiliate == 'trish444' || $affiliate == 'valueadd77' || $affiliate == 'yesisyes'
                    ) {
                        $fe = 0.90;
                        $us = 0.50;
                        $rp = 0.25;
                    }
                    // 75%
                    else if ($affiliate == 'goalzilla') {
                        $fe = 0.75;
                        $us = 0.50;
                        $rp = 0.25;
                    } else {
                        $fe = 0.75;
                        $us = 0.50;
                        $rp = 0.25;
                    }
                }
                break;
            case 'qmanifest': {
                    // 90%
                    if ($affiliate == 'omemail' || $affiliate == 'pradv' || $affiliate == 'statbook') {
                        $fe = 0.90;
                        $us = 0.90;
                    } else if (
                        $affiliate == '15manifest' || $affiliate == 'bonus998' ||
                        $affiliate == 'channelsun' || $affiliate == 'goalzila' || $affiliate == 'haruki92' || $affiliate == 'ignite1993' || $affiliate == 'individua1' ||
                        $affiliate == 'lightwks' || $affiliate == 'liyuqi' || $affiliate == 'yes2407' || $affiliate == '1crypto' || $affiliate == '224042' ||
                        $affiliate == '303mickey' || $affiliate == 'alliesintl' || $affiliate == 'awklight' || $affiliate == 'beapicou' || $affiliate == 'blueshanti' ||
                        $affiliate == 'clickchao' || $affiliate == 'copyjake' || $affiliate == 'danbrands' || $affiliate == 'donnarama' || $affiliate == 'drstephen' ||
                        $affiliate == 'ggreg7' || $affiliate == 'indulis' || $affiliate == 'inspirehit' || $affiliate == 'jalliances' || $affiliate == 'jamesprez' ||
                        $affiliate == 'kamecom' || $affiliate == 'kohrlaw' || $affiliate == 'l0v3ja' || $affiliate == 'latitude90' || $affiliate == 'malchiang' ||
                        $affiliate == 'mutchun' || $affiliate == 'naiwanat' || $affiliate == 'naventures' || $affiliate == 'olyasor' || $affiliate == 'oxana' ||
                        $affiliate == 'prsaputo8' || $affiliate == 'robengine' || $affiliate == 'scrib77' || $affiliate == 'segura8' || $affiliate == 'sonalpand' ||
                        $affiliate == 'stuart74' || $affiliate == 'stufflogue' || $affiliate == 'ten12golf' || $affiliate == 'toprateapp' || $affiliate == 'trish444' ||
                        $affiliate == 'valueadd77' || $affiliate == 'vt247' || $affiliate == 'yesisyes'
                    ) {
                        $fe = 0.90;
                        $us = 0.75;
                    }
                    // 80%
                    else if ($affiliate == 'kittay' || $affiliate == 'yuxiong94') {
                        $fe = 0.80;
                        $us = 0.75;
                    } else {
                        $fe = 0.75;
                        $us = 0.75;
                    }
                }
                break;
            case 'wactivator': {
                    $fe_jv = 0.25;
                    $us_jv = 0.25;
                    // 90%
                    if ($affiliate == '15manifest' || $affiliate == 'leekuanyew') {
                        $fe = 0.90;
                        $us = 0.75;
                    } else {
                        $fe = 0.75;
                        $us = 0.75;
                    }
                }
                break;
            case 'amazeyou2': {
                    $fe_jv = 0.30;
                    $us_jv = 0.20;
                    $rp_jv = 0.20;
                    // 90%
                    if ($affiliate == 'goalzilla' || $affiliate == 'mentis' || $affiliate == 'naturalc' || $affiliate == 'pureheal' || $affiliate == 'vogensis') {
                        $fe = 0.90;
                        $us = 0.90;
                        $rp = 0.20;
                    } else if ($affiliate == 'affdweeb' || $affiliate == 'haruki92' || $affiliate == 'individua1' || $affiliate == 'worklifeg' || $affiliate == 'yuxiong94') {
                        $fe = 0.90;
                        $us = 0.75;
                        $rp = 0.20;
                    } else {
                        $fe = 0.75;
                        $us = 0.75;
                        $rp = 0.20;
                    }
                }
                break;
            case 'amazeyou': {
                    // 90%
                    if (
                        $affiliate == 'awklight' || $affiliate == 'bonus998' || $affiliate == 'channelsun' || $affiliate == 'ignite1993' || $affiliate == 'liyuqi'
                        || $affiliate == 'yes2407'
                    ) {
                        $fe = 0.90;
                        $us = 0.75;
                    } else if ($affiliate == 'omemail') {
                        $fe = 0.90;
                        $us = 0.90;
                    } else {
                        $fe = 0.75;
                        $us = 0.75;
                    }
                }
                break;
            case 'millionb': {
                    $fe_jv = 0.20;
                    $us_jv = 0.20;
                    // 100%
                    if ($affiliate == 'ivaff' || $affiliate == 'ivaff2') {
                        $fe = 1.00;
                        $us = 0.70;
                    } else {
                        $fe = 0.70;
                        $us = 0.70;
                    }
                }
                break;
            case 'ancientsec': {
                    $fe_jv = 0.20;
                    $us_jv = 0.20;
                    $fe = 0.70;
                    $us = 0.70;
                }
                break;
            case 'pnmanifest': {
                    $fe = 0.75;
                    $us = 0.75;
                }
            case 'mtimewarp': {
                    $fe = 0.75;
                    $us = 0.75;
                }
            case 'medicicode': {
                    $fe = 0.75;
                    $us = 0.75;
                }
            case '15happy': {
                    $fe = 0.75;
                    $us = 0.75;
                }
            case 'godfreq': {
                    $fe = 0.75;
                    $us = 0.75;
                }
            case 'metabolicb': {
                    $fe = 0.75;
                    $us = 0.50;
                }
            case 'type2free': {
                    $fe = 0.75;
                    $us = 0.50;
                }
            case 'upmagnet': {
                    $fe = 0.75;
                    $us = 0.50;
                }
            case 'mmswitch': {
                    $fe = 0.75;
                    $us = 0.75;
                }
            case '15weight': {
                    if ($affiliate == 'b2cms' || $affiliate == 'brainev' || $affiliate == 'worklifeg') {
                        $fe = 0.90;
                        $us = 0.90;
                        $rp = 0.25;
                    } else if ($affiliate == 'affdweeb' || $affiliate == 'yuxiong94') {
                        $fe = 0.80;
                        $us = 0.80;
                        $rp = 0.25;
                    } else {
                        $fe = 0.75;
                        $us = 0.75;
                        $rp = 0.25;
                    }
                }
            case 'sleepwaves': {
                    if ($affiliate == '101fb2c' || $affiliate == 'amazeyou' || $affiliate == 'worklifeg' || $affiliate == 'leekuanyew' || $affiliate == 'magicplay' || $affiliate == 'verifydata' || $affiliate == 'wactivator') {
                        $fe = 0.90;
                        $us = 0.90;
                    } else {
                        $fe = 0.75;
                        $us = 0.75;
                    }
                }

            default: {
                    $fe = 0.75;
                    $us = 0.75;
                    $rp = 0.25;
                }
        }

        // $account = 'amazeyou2';
        // $front_end = 184.06;
        // $fe_jv = 0.3;
        // $fe = 0.75;
        // $us_jv = 0.2;
        // $us = 0.75;
        // $rp_jv = 0.2;
        // $rp = 0.2;
        // $up_sell = 133.82;
        // $rebill = 10.00;

        // $account = '15weight';
        // $front_end = 24.68;
        // $up_sell = 14.21;
        // $rebill = 5;
        // $fe = 0.90;
        // $us = 0.90;
        // $rp = 0.25;


        if ($account == 'qmanifest' || $account == 'pnmanifest' || $account == 'amazeyou' || $account == 'sleepwaves' || $account == 'medicicode' || $account == 'mtimewarp' || $account == '15happy' || $account == 'godfreq' || $account == 'metabolicb' || $account == 'type2free' || $account == 'upmagnet' || $account == 'mmswitch') {
            $data['fe'] = $front_end / (1 - $fe) * $fe;
            $data['us'] = $up_sell / (1 - $us) * $us;

            $affiliate_revenue = $data['fe'] + $data['us'];
        } else if ($account == '15manifest') {
            $data['fe'] = $front_end / (1 - $fe) * $fe;
            $data['us'] = $up_sell / (1 - $us) * $us;
            $data['rp'] = $rebill / (1 - $rp) * $rp;

            $affiliate_revenue = $data['fe'] + $data['us'] + $data['rp'];
        } else if ($account == 'amazeyou2') {
            $data['fe'] = $front_end / (1 - $fe_jv);
            $data['fe'] = ($data['fe'] == 0) ? 0 : $data['fe'] / (1 - $fe) * $fe;

            $data['us'] = $up_sell / (1 - $us_jv);
            $data['us'] = ($data['us'] == 0) ? 0 : $data['us'] / (1 - $us) * $us;

            $data['rp'] = $rebill / (1 - $rp_jv);
            $data['rp'] = ($data['rp'] == 0) ? 0 : $data['rp'] / (1 - $rp) * $rp;
            $affiliate_revenue = $data['fe'] + $data['us'] + $data['rp'];
        } else if ($account == '15weight') {
            $data['fe'] = $front_end / (1 - $fe) * $fe;
            $data['us'] = $up_sell / (1 - $us) * $us;
            $data['rp'] = $rebill / (1 - $rp) * $rp;

            $affiliate_revenue = $data['fe'] + $data['us'] + $data['rp'];
        } else {
            $data['fe'] = $front_end / (1 - $fe_jv);
            $data['fe'] = ($data['fe'] == 0) ? 0 : $data['fe'] / (1 - $fe) * $fe;

            $data['us'] = $up_sell / (1 - $us_jv);
            $data['us'] = ($data['us'] == 0) ? 0 : $data['us'] / (1 - $us) * $us;
            $affiliate_revenue = $data['fe'] + $data['us'];
        }

        // echo $data['fe'] . "</br>";
        // echo $data['us'] . "</br>";
        // echo $data['rp'] . "</br>";
        // echo $affiliate_revenue;
        // die();

        return $affiliate_revenue;
    }

    // Function to get the Partner name from affiliate name - RM22122020
    public static function getPartnerName($affiliate)
    {

        $return = "";
        $aff = AffiliateMaster::where('name', '=', $affiliate)->first();
        if (isset($aff->id) && $aff->id > 0) {

            $cbmasterlist = CbMasterList::where('affiliate_id', $aff->id)->first();
            if ($cbmasterlist->partner_id > 0) {
                $partnerlist = PartnerMaster::where('id', '=', $cbmasterlist->partner_id)->first();
                if (isset($partnerlist->name) && !empty($partnerlist->name))
                    $return = $partnerlist->name;
            }
        }
        return $return;
    }

    public static function getPartnerNameOLD($affiliate)
    {
        // 109
        $partnerName = '';
        if ($affiliate == 'mentis' || $affiliate == 'naturalc' || $affiliate == 'pureheal' || $affiliate == 'vogenesis') {
            $partnerName = 'Truegenics';
        } else if ($affiliate == 'likeblue' || $affiliate == 'manimir' || $affiliate == 'discounts1' || $affiliate == 'totalmon' || $affiliate == 'erictaller' || $affiliate == 'thoughtucp') {
            $partnerName = 'Mark Ling';
        } else if ($affiliate == 'drinkless1') {
            $partnerName = 'Georgia Foster';
        } else if ($affiliate == 'drpompa') {
            $partnerName = 'Revelation Health - Andrea Duchonovic';
        } else if ($affiliate == 'biorhythm') {
            $partnerName = 'Derek Seymour';
        } else if ($affiliate == 'bluemindaf') {
            $partnerName = 'Allegra Strategy, Joanna';
        } else if ($affiliate == 'wholetones') {
            $partnerName = 'Wholetones';
        } else if ($affiliate == 'brainev') {
            $partnerName = 'Karl Moore';
        } else if ($affiliate == 'hypnosis4u' || $affiliate == 'hypwealth' || $affiliate == 'outrageous' || $affiliate == 'hypwealth2' || $affiliate == 'statbrook') {
            $partnerName = 'Frank Mangano';
        } else if ($affiliate == 'mproduct2k') {
            $partnerName = 'Sean Michael Goudelock';
        } else if ($affiliate == '1secure' || $affiliate == 'convo') {
            $partnerName = 'Letian';
        } else if ($affiliate == 'numerology') {
            $partnerName = 'Numerologist';
        } else if ($affiliate == 'manifmagic' || $affiliate == 'mrdweeb' || $affiliate == 'affdweeb') {
            $partnerName = 'John Cho';
        } else if ($affiliate == 'yuxiong94') {
            $partnerName = 'Yuxiong';
        } else if ($affiliate == 'worklifeg') {
            $partnerName = 'Tay Yeng Hwee';
        } else if ($affiliate == 'yes2407') {
            $partnerName = 'Javier Chua';
        } else if ($affiliate == 'individua1' || $affiliate == 'haruki92') {
            $partnerName = 'Wilson Lau';
        } else if ($affiliate == 'astrolan' || $affiliate == 'bendreal' || $affiliate == 'goalzila') {
            $partnerName = 'Isabel Wong';
        } else if ($affiliate == 'davandrews') {
            $partnerName = 'Daniel Toh';
        } else if ($affiliate == 'gt3009') {
            $partnerName = 'Gary Teo (Rufers Tan)';
        } else if ($affiliate == 'lightwks' || $affiliate == 'khaisiung') {
            $partnerName = 'Khai Ng';
        } else if ($affiliate == 'onsalesnow') {
            $partnerName = 'Yee Shun Jian';
        } else if ($affiliate == 'fameoffer' || $affiliate == 'liyuqi' || $affiliate == 'montoffer' || $affiliate == 'doraecharm' || $affiliate == 'channelsun' || $affiliate == 'ignite1993' || $affiliate == 'tonghai93' || $affiliate == 'easypro12') {
            $partnerName = 'Tom Lua / Matthew Tang';
        } else if ($affiliate == 'zxginxz33') {
            $partnerName = 'Gin Ng';
        } else if ($affiliate == 'hospark' || $affiliate == 'davandrews') {
            $partnerName = 'Zenith Labs (Celeste, Tyler)';
        } else if ($affiliate == 'pradv' || $affiliate == 'jasonsteph') {
            $partnerName = 'Ippokratis Boboras';
        } else if ($affiliate == 'mindfulaff' || $affiliate == 'dmsupps') {
            $partnerName = 'Cacao Bliss (DanetteMay.com) / Ashton Marshall';
        } else if ($affiliate == 'piscesman' || $affiliate == 'libraanna' || $affiliate == 'leosecrets' || $affiliate == 'caprman' || $affiliate == 'cansecrets' || $affiliate == 'ariesecret' || $affiliate == 'gemiman' || $affiliate == 'scorpman' || $affiliate == 'virgoanna' || $affiliate == 'aquarman' || $affiliate == 'taurusman') {
            $partnerName = 'Stefan Gajic';
        } else if ($affiliate == 'ypnic' || $affiliate == 'horostone') {
            $partnerName = 'Nicholas Nic / Mango';
        } else if ($affiliate == 'nobs01' || $affiliate == 'wsftc') {
            $partnerName = 'Ric Thompson';
        } else if ($affiliate == 'spaid' || $affiliate == '1minweight') {
            $partnerName = 'Brandon Harris';
        } else if ($affiliate == 'kennycztan' || $affiliate == 'potato871' || $affiliate == '365mojo') {
            $partnerName = 'Kenny Tan / Wen Li';
        } else if ($affiliate == 'zenfulife') {
            $partnerName = 'Lin Zhixin';
        } else if ($affiliate == '4elink') {
            $partnerName = 'John Valenty';
        } else if ($affiliate == 'daily101') {
            $partnerName = 'Zackary Ang How Siang';
        } else if ($affiliate == 'masterysi' || $affiliate == 'ythmastery') {
            $partnerName = 'Simon Stanley';
        } else if ($affiliate == 'attractmag') {
            $partnerName = 'Anukrity Gupta';
        } else if ($affiliate == 'zakyid') {
            $partnerName = 'Zaki';
        } else if ($affiliate == 'revealed5') {
            $partnerName = 'Revealed Films (Katerina K)';
        } else if ($affiliate == 'l2f3r3d') {
            $partnerName = 'New Clickbank.com Shop';
        } else if ($affiliate == '3founders') {
            $partnerName = 'Agora Financial (Meghan)';
        } else if ($affiliate == '7daymind') {
            $partnerName = 'Mark Coughlan';
        } else if ($affiliate == 'annakovach') {
            $partnerName = 'Stefan Gajic & Bosko';
        } else if ($affiliate == 'neuro99') {
            $partnerName = 'Mike (10 Minute Awakening)';
        } else if ($affiliate == 'ballcorn') {
            $partnerName = 'Flora Springs';
        } else if ($affiliate == 'loalive') {
            $partnerName = 'Tiberiu Uriasu';
        } else if ($affiliate == 'STATJD') {
            $partnerName = 'Frank Mangano';
        } else if ($affiliate == 'gfdesserts') {
            $partnerName = 'Healing Gourmet/Keto Breads';
        } else if ($affiliate == 'affrh83') {
            $partnerName = 'Ryan Hamada/Urgent Money Miracle';
        } else if ($affiliate == 'natmentor') {
            $partnerName = 'PuraThrive';
        } else if ($affiliate == 'RickKaselj' || $affiliate == 'Ex4injury' || $affiliate == 'FJohns' || $affiliate == 'FixPain' || $affiliate == 'Abacad' || $affiliate == 'PainFoot' || $affiliate == 'MiRLower' || $affiliate == 'MIRAssess' || $affiliate == 'MIRtool') {
            $partnerName = 'Rick Kaselj , Turmeric';
        } else if ($affiliate == 'simplesmar') {
            $partnerName = 'Karlie Knight';
        } else if ($affiliate == 'quirkytips') {
            $partnerName = 'Revelation Health';
        } else if ($affiliate == 'kenmanifst') {
            $partnerName = 'Lim Wan Heng';
        } else if ($affiliate == 'papeak') {
            $partnerName = 'Astrology Answers (Hernan)';
        } else if ($affiliate == 'manreader') {
            $partnerName = 'Chris Haddad';
        } else if ($affiliate == 'lifetraf' || $affiliate == 'medbraff') {
            $partnerName = 'Allegra Strategy, Joanna';
        } else if ($affiliate == 'vitalhlth') {
            $partnerName = 'Luke DiMarco';
        } else if ($affiliate == 'NSLIM') {
            $partnerName = 'James Johnson';
        } else if ($affiliate == 'Kasolive') {
            $partnerName = 'Tony Kasandrino';
        } else if ($affiliate == 'Peakbioaff') {
            $partnerName = 'Kaitlyn Buskirk';
        } else if ($affiliate == 'aceofharts') {
            $partnerName = 'Neudeck Ace';
        } else if ($affiliate == 'neurobank') {
            $partnerName = 'Mike (10 Minute Awakening)';
        } else if ($affiliate == 'Cef2011') {
            $partnerName = '7 Minute Ageless Body Secrets/Dawn Sylvester';
        } else if ($affiliate == 'bryangsc') {
            $partnerName = 'Bryan Lim';
        } else if ($affiliate == 'HealthyPat' || $affiliate == 'Kilnecreati') {
            $partnerName = 'Laura Jimenez / Lexapure';
        } else if ($affiliate == 'ginseah' || $affiliate == 'amanifest' || $affiliate == 'seraphimd') {
            $partnerName = 'Gin Seah';
        } else if ($affiliate == 'wclarity') {
            $partnerName = 'Wellness Clarity (Andrea Duchonovic)';
        } else if ($affiliate == 'Albyfittv') {
            $partnerName = 'Ironwood (Alby Gonzales)';
        } else if ($affiliate == 'mvpscott') {
            $partnerName = 'Rufers Tan';
        } else if ($affiliate == 'fgem7') {
            $partnerName = 'Sergio Rubio';
        } else if ($affiliate == 'snfoobiz') {
            $partnerName = 'Dayvid Foo';
        } else if ($affiliate == 'imbasales') {
            $partnerName = 'Hubert Koh';
        } else if ($affiliate == 'dumbtut14') {
            $partnerName = 'KC Jason';
        } else if ($affiliate == 'valuebank') {
            $partnerName = 'Bridget Tan';
        } else if ($affiliate == 'goodlifer0') {
            $partnerName = 'Andrea Duchonovic';
        } else if ($affiliate == 'bouska8') {
            $partnerName = 'Pavel Bouska';
        } else if ($affiliate == 'thorstone8') {
            $partnerName = 'Kim Armstrong';
        } else if ($affiliate == 'shubhx' || $affiliate == 'soraya1331') {
            $partnerName = 'Shubhashish Mendhe (Charlie Gates)';
        } else if ($affiliate == 'bricy23') {
            $partnerName = 'Bridger Lee';
        } else if ($affiliate == 'ollie23') {
            $partnerName = 'Ody Ong';
        } else if ($affiliate == 'getoffer5') {
            $partnerName = 'Oliver Lee (YH)';
        } else if ($affiliate == 'bestpromo6') {
            $partnerName = 'Shen Siew Yue';
        } else if ($affiliate == 'yaybehappy') {
            $partnerName = 'Kari Samuels';
        } else if ($affiliate == 'ttarot') {
            $partnerName = 'Trusted Tarot (Jeremy David Peters, Brad Lundhal)';
        } else if ($affiliate == 'tssaccount') {
            $partnerName = 'Sacred Science (Mileen Patel) ';
        } else if ($affiliate == 'soulspring') {
            $partnerName = 'Panache Desai (Marcia)';
        } else if ($affiliate == 'soniarigap') {
            $partnerName = 'Sonia Ricotti (Naomi McKenna)';
        } else if ($affiliate == 'plmedia') {
            $partnerName = 'Personal Life Media (Susan Bratton)';
        } else if ($affiliate == 'mindmovies') {
            $partnerName = 'Mind Movies (Kesira / Glenn Ledwell)';
        } else if ($affiliate == 'askangels') {
            $partnerName = 'Ask Angels (Melanie Beckler)';
        } else if ($affiliate == 'greatbids') {
            $partnerName = 'Shawn Josiah Ong';
        } else if ($affiliate == 'hpvybes') {
            $partnerName = 'Vybesource (Spencer)';
        } else if ($affiliate == 'loanet11' || $affiliate == 'readtarot') {
            $partnerName = 'Tarot Reading Daily (Stanley Dawejko Jr)';
        } else if ($affiliate == 'tgabrielle') {
            $partnerName = 'Tania Gabrielle';
        } else if ($affiliate == 'musici') {
            $partnerName = 'Ty Cohen';
        } else if ($affiliate == 'youwealth') {
            $partnerName = 'You Wealth (Darius Barazandeh & Kristen Hayes)';
        } else if ($affiliate == 'daymillion') {
            $partnerName = 'Manifestation Hero (Darius Copac/ Darius V Thomas)';
        } else if ($affiliate == 'chikara2') {
            $partnerName = 'Chikara-Reiki-Do (Chris & Judith Conroy)';
        } else if ($affiliate == 'rvltioniz') {
            $partnerName = 'Crack Your Egg (Henk Schram)';
        } else if ($affiliate == 'instasells' || $affiliate == 'jadle') {
            $partnerName = 'Jad Le';
        } else if ($affiliate == 'kittay') {
            $partnerName = 'Kit Tay';
        } else if ($affiliate == 'ybnllc') {
            $partnerName = 'Korey Rose';
        } else if ($affiliate == 'indigoheal') {
            $partnerName = 'Laura Warnke';
        } else if ($affiliate == 'Kilnecreati' || $affiliate == 'HealthyPat') {
            $partnerName = 'LexaPure (Laura Jimenez-McCoy)';
        } else if ($affiliate == 'nykkih') {
            $partnerName = 'Nykki Hardin, 21 Cleanse';
        } else if ($affiliate == 'neopage1' || $affiliate == 'neopage' || $affiliate == 'loadnet' || $affiliate == 'neosite') {
            $partnerName = 'Adam Mentor';
        } else if ($affiliate == 'healthybak') {
            $partnerName = 'The Healthy Back Institute (Wes Marks)';
        } else if ($affiliate == 'cfp600' || $affiliate == 'xalmaff') {
            $partnerName = "Dayan / Xalm";
        } else if ($affiliate == 'authnumer') {
            $partnerName = "Joel Chue";
        } else if ($affiliate == 'thoughtop') {
            $partnerName = "Jeremy David Peters/Trusted Tarrot";
        } else if ($affiliate == 'madoffer') {
            $partnerName = "Loki Ong";
        } else if ($affiliate == 'mustshare') {
            $partnerName = "Murphy Ong";
        } else if ($affiliate == 'Charlescys') {
            $partnerName = "Charles Reuben Cheong";
        } else if ($affiliate == 'voices3') {
            $partnerName = "Robert Leavitt";
        } else if ($affiliate == 'horostone') {
            $partnerName = "Mango Mangz";
        } else if ($affiliate == 'sim1022') {
            $partnerName = "Ying Liang Sim";
        } else if ($affiliate == 'goodd3als') {
            $partnerName = "Kelvin Teoh";
        } else if ($affiliate == 'thewiseyou') {
            $partnerName = "Christian Green";
        } else if ($affiliate == 'Tdpickle1') {
            $partnerName = "Tim Dill";
        } else if ($affiliate == 'mbmatrix') {
            $partnerName = "TK/Liss Graham Mind Body Matrix";
        } else if ($affiliate == 'millions35') {
            $partnerName = "Wes Virgin";
        } else if ($affiliate == 'wowbee31') {
            $partnerName = "Volkan Altinoz";
        }

        return $partnerName;
    }

    public static function generateAffiliateRevenuePDF()
    {
        $date = Helper::getNewDate();
        $month = $date->format('m');
        $month_with_no_zero = $date->format('n');
        $day = $date->format('d');
        $year = $date->format('Y');
        $total_day = $date->format('t');

        // START CURRENT MONTH DIFFERENCE
        $current_month_difference = AffiliateRevenue::where('month', '=', $month_with_no_zero)->where('year', '=', $year)->first();
        $current_month_difference->target = ($current_month_difference->target / $total_day) * $day;
        $current_month_difference->date = Helper::getNewDate()->format('d F, Y');
        // END CURRENT MONTH DIFFERENCE

        // START SALES RANKING BY ACCOUNT
        $rs_sales_ranking = SalesRankingRevenue::orderByRaw('currentmonth_revenue DESC')->get();

        // For Total Revenue - RM31122020
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
            $sales_ranking_by_account['variance'][] = ($projected_month - $rs_sales_ranking[$i]->lastmonth_revenue) / $rs_sales_ranking[$i]->lastmonth_revenue * 100;

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

        // Total SALES RANKING DATA - RM31122020
        $sales_ranking_by_account['total_month_to_date'] = $total_month_to_date;
        $sales_ranking_by_account['total_projected_month'] = $total_projected_month;
        $sales_ranking_by_account['total_last_month'] = $total_last_month;

        $total_variance = ($total_projected_month - $total_last_month) / $total_last_month * 100;
        $sales_ranking_by_account['total_variance'] = $total_variance;

        $last_update_time = AffiliateRevenue::orderBy('id', 'DESC')->first()->updated_at;
        // END SALES RANKING BY ACCOUNT

        // INCOMING TRAFFIC STATUS
        $rs_its  = IncomingTrafficStatus::orderByRaw('current_hopcount DESC')->get();

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
        for ($i = 11; $i >= 0; $i--) {
            $date = Helper::getNewDate()->modify("-" . $i . "month");

            $month = $date->format('m');
            $year = $date->format('Y');

            $monthDesc = ($i == 0) ? $date->format('M') . ' ' . $date->format('d') . '/' . $date->format('t') : $date->format('M');

            $get_revenue = AffiliateRevenue::where('month', '=', $month)->where('year', '=', $year)->first();

            if ($get_revenue != null) {
                $affiliate_revenue['months'][] = "'" . $monthDesc . "'";
                $affiliate_revenue['revenue'][] = round($get_revenue->revenue);
                $affiliate_revenue['target'][] = ($i == 0) ? round(($get_revenue->target / $date->format('t')) * $date->format('d')) : round($get_revenue->target);
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
        $topaffiliate_today_updated_at = TopAffiliate::orderBy('id', 'DESC')->first()->updated_at;

        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', "https://quickchart.io/chart?c={type:'line',data:{labels:[" . implode(',', $affiliate_revenue['months']) . "],datasets:[{label:'Revenue',borderColor:'rgb(149, 229, 149)',fill:false,data:[" . implode(',', $affiliate_revenue['revenue']) . "]},{label:'Target',borderColor:'rgb(149, 153, 223)',fill:false,data:[" . implode(',', $affiliate_revenue['target']) . "]}]},options:{responsive:true,maintainAspectRatio:false,plugins:{datalabels:{display:true}}}}&f=PNG");

        $result_chart = $response->getBody()->getContents();
        $result_chart = 'data:image/png;base64,' . base64_encode($result_chart);

        list(, $result_chart)      = explode(',', $result_chart);
        $result_chart = base64_decode($result_chart);

        if (Storage::exists('public/csv/chart-rv-' . $date->format('Y-m-d') . '.png')) {
            Storage::delete('public/csv/chart-rv-' . $date->format('Y-m-d') . '.png');
        }
        $name = 'chart-rv-' . date('Y-m-d') . '.png';
        Storage::put('public/csv/' . $name, $result_chart);

        $data['last_update_time'] = $last_update_time;
        $data['cmd'] = $current_month_difference;
        //$data['current_month_sales_ranking'] = $current_month_sales_ranking;
        //$data['last_month_sales_ranking'] = $last_month_sales_ranking;
        $data['affiliate_revenue'] = $affiliate_revenue;
        $data['top_affiliate_label'] = $top_affiliate_label;
        $data['sales_ranking_by_account'] = $sales_ranking_by_account;
        $data['incoming_traffic_status'] = $incoming_traffic_status;
        $data['topaffiliate_today_updated_at'] = $topaffiliate_today_updated_at;

        // Browsershot::url('http://localhost/dashboards/public/affiliate_revenue')->save('affiliate_revenue.pdf');

        // $pdf = PDF::loadView('emails.affiliate_revenue', $data);
        // $pdf->setOptions(['isPhpEnabled' => true, 'isJavascriptEnabled' => true]);
        // Storage::put('public/csv/affiliate_revenue.pdf', $pdf->download()->getOriginalContent());

        Mail::send('emails.affiliate_revenue', $data, function ($message) {
            $message->from('no-reply@dashboards.com', 'Dashboards');

            if (in_array(env('APP_ENV'), ['staging', 'production'])) {
                $message->to('chungsung.ong@limitlessfactor.com', 'JS');
                $message->to('roy.chua@limitlessfactor.com', 'Roy');
                $message->to('melany.navarro@limitlessfactor.com', 'Lanie');
                $message->to('boston.toh@limitlessfactor.com', 'Boston');
            } else {
                $message->to('chungsung.ong@limitlessfactor.com', 'JS');
            }

            $message->subject('Affiliate Revenue - ' . Carbon::now()->format('Y-m-d'));
            // $message->attach('storage/csv/affiliate_revenue.pdf', ['mime' => 'application/pdf']);
        });
    }

    public static function generateListGrowthPDF()
    {
        //START LIST GROWTH
        $accounts = AweberAccounts::all();
        $leads1 = ListGrowth::where('types', '=', '1')->where('from', '=', '1')->get();
        $last_update_time = ListGrowth::first()->updated_at;

        $data['accounts'] = $accounts;
        $data['leads1'] = $leads1;
        $data['last_update_time'] = $last_update_time;

        // Browsershot::url('http://localhost/dashboards/public/list_growth')
        //     ->format('A4')
        //     ->savePdf('storage/csv/list_growth.pdf');

        // // $pdf = PDF::loadView('emails.list_growth', $data);
        // // Storage::put('public/csv/list_growth.pdf', $pdf->download()->getOriginalContent());

        Mail::send('emails.list_growth', $data, function ($message) {
            $message->from('no-reply@dashboards.com', 'Dashboards');
            $message->to('chungsung.ong@limitlessfactor.com', 'JS');
            $message->to('roy.chua@limitlessfactor.com', 'Roy');
            $message->subject('List Growth - ' . Carbon::now()->format('Y-m-d'));
            // $message->attach('storage/csv/list_growth.pdf', ['mime' => 'application/pdf']);
        });
        //END LIST GROWTH
    }

    public static function generateSalesByTIDPDF()
    {
        // START SALES BY ARS
        $accounts = ['1071969', '1267038', '1022666', '1425050'];
        $last_update_time = SalesByARS::first()->updated_at;

        $data['accounts'] = $accounts;
        $data['last_update_time'] = $last_update_time;

        // $pdf = PDF::loadView('emails.sales_by_ars', $data);
        // Storage::put('public/csv/sales_by_ars.pdf', $pdf->download()->getOriginalContent());

        Mail::send('emails.sales_by_ars', $data, function ($message) {
            $message->from('no-reply@dashboards.com', 'Dashboards');
            $message->to('chungsung.ong@limitlessfactor.com', 'JS');
            $message->to('roy.chua@limitlessfactor.com', 'Roy');
            $message->subject('Sales By TID - ' . Carbon::now()->format('Y-m-d'));
            // $message->attach('storage/csv/sales_by_ars.pdf', ['mime' => 'application/pdf']);
        });
        // END SALES BY ARS
    }

    public static function importKendagoData()
    {
        $account = '15manifest';
        $affiliate = '101fb2c';

        for ($i = 4; $i >= 0; $i--) {
            $date = Helper::getNewDate();
            $end_day = ($i == 0) ? $date->format('d') : $date->format('t');
            $month = $date->format('m');
            $year = $date->format('Y');

            $url = "https://api.clickbank.com/rest/1.3/analytics/vendor/affiliate/summary?account=$account&select=NET_SALE_AMOUNT&select=REBILL_AMOUNT&select=UPSELL_AMOUNT&select=HOP_COUNT&select=SALE_COUNT&select=SALE_AMOUNT&summaryType=AFFILIATE_ONLY&dimensionFilter=$affiliate&startDate=$year-$month-01&endDate=$year-$month-$end_day";

            $result = curl_init();
            curl_setopt($result, CURLOPT_URL, $url);
            curl_setopt($result, CURLOPT_HEADER, false);
            curl_setopt($result, CURLOPT_HTTPGET, false);
            curl_setopt($result, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($result, CURLOPT_TIMEOUT, 360);
            curl_setopt($result, CURLOPT_HTTPHEADER, array("Accept: application/json", "Authorization: " . env('CLICKBANK_CLIENT_ID') . ":" . env('CLICKBANK_CLIENT_SECRET')));

            $api_result = json_decode(curl_exec($result), true);

            if (is_array($api_result['rows']['row']) || is_object($api_result['rows']['row'])) {
                $rebill_amount = $api_result['rows']['row']['data']['0']['value']['$'];
                $upsell_amount = $api_result['rows']['row']['data']['1']['value']['$'];
                $hop_count = $api_result['rows']['row']['data']['2']['value']['$'];
                $revenue = $api_result['rows']['row']['data']['3']['value']['$'];
                $initiate_sales_revenue = $api_result['rows']['row']['data']['4']['value']['$'];
                $initial_sales_count = $api_result['rows']['row']['data']['5']['value']['$'];
                $fe_cvr = ($initial_sales_count / $hop_count) * 100;


                $affiliate_comms = Helper::calculateAffiliateCommission($account, $initiate_sales_revenue, $upsell_amount, $rebill_amount, $affiliate, $month, $year);
                $affiliate_epc = ($hop_count == 0) ? 0 : number_format($affiliate_comms / $hop_count, 2);
                $vendor_epc = $revenue / $hop_count;

                $raw_epc = str_replace(',', '', $affiliate_epc) + $vendor_epc;


                $exist = Kendago::where('month', '=', $month)->where('year', '=', $year)->first();
                if ($exist == null) {
                    Kendago::insert(['month' => $month, 'year' => $year, 'revenue' => $revenue, 'hop_count' => $hop_count, 'initial_sales_count' => $initial_sales_count, 'fe_cvr' => $fe_cvr, 'raw_epc' => $raw_epc, 'created_at' => Carbon::now()]);
                } else {
                    $id = $exist->id;
                    $row = Kendago::find($id);
                    $row->month = $month;
                    $row->year = $year;
                    $row->revenue = $revenue;
                    $row->hop_count = $hop_count;
                    $row->initial_sales_count = $initial_sales_count;
                    $row->fe_cvr = $fe_cvr;
                    $row->raw_epc = $raw_epc;
                    $row->updated_at = Carbon::now();
                    $row->save();
                }
            }

            if ($i == 0) {
                $row = Kendago::where([
                    ['month', '=', $month],
                    ['year', '=', $year]
                ])->first();

                if ($row == null) {
                    Kendago::insert(['month' => $month, 'year' => $year, 'revenue' => $revenue, 'hop_count' => $hop_count, 'initial_sales_count' => $initial_sales_count, 'fe_cvr' => $fe_cvr, 'raw_epc' => $raw_epc, 'created_at' => Carbon::now()]);
                } else {
                    $row->hop_count = $hop_count;
                    $row->updated_at = Carbon::now();
                    $row->save();
                }
            }
        }
    }

    // To import RFS order data.
    public static function importRfsOrderData()
    {
        $vendor = 'GODFREQ';
        $affiliate = 'CHECKDATA';
        $orders = array();
        $analytics = array();
        for ($i = 3; $i >= 0; $i--) {
            // $i = 3;

            $date = Helper::getNewDate()->modify("-" . $i . "month");
            $end_day = ($i == 0) ? $date->format('d') : $date->format('t');
            $month = $date->format('m');
            $year = $date->format('Y');

            $initial_sales_count = 0;
            $upsell_sales_amount = 0;
            for ($j = 0; $j <= 4; $j++) {
                $amount = 0;
                $upsell_sales_amount = 0;
                $type = static::$types[$j];

                // To grab the data from the Orders
                $url = "https://api.clickbank.com/rest/1.3/orders2/list?vendor=$vendor&affiliate=$affiliate&type=$type&startDate=$year-$month-01&endDate=$year-$month-$end_day";
                $api_result = Helper::getAllOrderDataNew($url);
                //print_r($api_result);
                if (count($api_result) > 0) {
                    // $arrOrddata = current($api_result);

                    foreach ($api_result as $apData) {

                        if (isset($apData['lineItemData']['lineItemType'])) {
                            //echo '<br>---Yes----<br>';
                            $orders[$year][$month][] = $apData;
                        } else {
                            // echo '<br>------NO-----<br>';
                            //print_r($apData);
                            foreach ($apData as $arrOrder) {
                                $orders[$year][$month][] = $arrOrder;
                            }
                        }
                    }
                }
            }

            // process the analytics call
            $url1 = "https://api.clickbank.com/rest/1.3/analytics/vendor/affiliate/summary?account=godfreq&dimensionFilter=checkdata&select=HOP_COUNT&select=SALE_COUNT&summaryType=AFFILIATE_ONLY&startDate=$year-$month-01&endDate=$year-$month-$end_day";
            $api_result1 = Helper::getAllAnalyticsDataNew($url1);
            $analytics['hop_count'][$year][$month] =  0;
            $analytics['sales_count'][$year][$month] = 0;
            foreach ($api_result1 as $a1) {

                if (isset($a1[0]['value']['$']))
                    $analytics['hop_count'][$year][$month] =  $a1[0]['value']['$'];

                if (isset($a1[1]['value']['$']))
                    $analytics['sales_count'][$year][$month] = $a1[1]['value']['$'];
            }
        }

        foreach ($orders as $keyYear => $valYear) {
            foreach ($valYear as $keyMonth => $valMonth) {
                $salesAmount = 0;
                $refundAmount = 0;
                $billAmount = 0;
                $chargebackAmount = 0;
                $feeAmount = 0;
                $upsellAmount = 0;
                $bumpAmount = 0;
                foreach ($valMonth as $keyData => $valData) {

                    // For SALE
                    if (isset($valData['lineItemData']['lineItemType'])) {

                        // for SALE
                        if (@$valData['transactionType'] == 'SALE' && @$valData['role'] == 'AFFILIATE' && in_array(@$valData['lineItemData']['lineItemType'], array('STANDARD', 'UPSELL', 'BUMP'))) {
                            $salesAmount += @$valData['lineItemData']['accountAmount'];
                        }

                        // for RFND
                        if (@$valData['transactionType'] == 'RFND' && @$valData['role'] == 'AFFILIATE' && in_array(@$valData['lineItemData']['lineItemType'], array('STANDARD'))) {
                            $refundAmount += @$valData['lineItemData']['accountAmount'];
                        }

                        // for Bill
                        if (@$valData['transactionType'] == 'BILL' && in_array(@$valData['lineItemData']['lineItemType'], array('STANDARD'))) {
                            $billAmount += @$valData['lineItemData']['accountAmount'];
                        }

                        // for CGBK
                        if (@$valData['transactionType'] == 'CGBK' && in_array(@$valData['lineItemData']['lineItemType'], array('STANDARD'))) {
                            $chargebackAmount += @$valData['lineItemData']['accountAmount'];
                        }

                        // for FEE
                        if (@$valData['transactionType'] == 'FEE' && in_array(@$valData['lineItemData']['lineItemType'], array('STANDARD'))) {
                            $feeAmount += @$valData['lineItemData']['accountAmount'];
                        }

                        // for UPSELL Amount
                        if (isset($valData['lineItemData']['lineItemType']) && $valData['lineItemData']['lineItemType'] == 'UPSELL') {
                            $upsellAmount += @$valData['lineItemData']['accountAmount'];
                        }

                        // for BUMP Amount
                        if (isset($valLIdata['lineItemData']['lineItemType']) && $valLIdata['lineItemData']['lineItemType'] == 'BUMP') {
                            $bumpAmount += @$valData['lineItemData']['accountAmount'];
                        }


                        //echo '<br> If SALE AMT: '.$salesAmount += @$valData['lineItemData']['accountAmount'];
                    } else {
                        if (isset($valData['lineItemData'])) {
                            //print_r($valData);
                            foreach ($valData['lineItemData'] as $valLIdata) {

                                // for SALE
                                if (@$valData['transactionType'] == 'SALE' && @$valLIdata['role'] == 'AFFILIATE' && in_array(@$valLIdata['lineItemType'], array('STANDARD', 'UPSELL', 'BUMP'))) {
                                    $salesAmount += @$valLIdata['accountAmount'];
                                }

                                // for RFND
                                if (@$valData['transactionType'] == 'RFND' && @$valLIdata['role'] == 'AFFILIATE' && in_array(@$valLIdata['lineItemType'], array('STANDARD'))) {
                                    $refundAmount += @$valLIdata['accountAmount'];
                                }

                                // for Bill
                                if (@$valData['transactionType'] == 'BILL' && in_array(@$valLIdata['lineItemType'], array('STANDARD'))) {
                                    $billAmount += @$valLIdata['accountAmount'];
                                }

                                // for CGBK
                                if (@$valData['transactionType'] == 'CGBK' && (isset($valLIdata['lineItemType']) && $valLIdata['lineItemType'] == 'STANDARD')) {
                                    $chargebackAmount += @$valLIdata['accountAmount'];
                                }

                                // for FEE
                                if (@$valData['transactionType'] == 'FEE' && in_array(@$valLIdata['lineItemType'], array('STANDARD'))) {
                                    $feeAmount += @$valLIdata['accountAmount'];
                                }

                                // for UPSELL Amount
                                if (isset($valLIdata['lineItemType']) && $valLIdata['lineItemType'] == 'UPSELL') {
                                    $upsellAmount += @$valLIdata['accountAmount'];
                                }

                                // for BUMP Amount
                                if (isset($valLIdata['lineItemType']) && $valLIdata['lineItemType'] == 'BUMP') {
                                    $bumpAmount += @$valLIdata['accountAmount'];
                                }
                            }
                        } else {
                            // print_r($valData);
                        }
                    }
                }


                $exist = RfsOrder::where('month', '=', $keyMonth)->where('year', '=', $keyYear)->first();

                if ($exist == null) {
                    // echo '<br> NULL';
                    RfsOrder::insert(['month' => $keyMonth, 'year' => $keyYear, 'hop_count' => $analytics['hop_count'][$keyYear][$keyMonth], 'sale' => $salesAmount, 'bill' => $billAmount, 'refund' => abs($refundAmount), 'charge_back' => abs($chargebackAmount), 'fee' => abs($feeAmount), 'upsell_sales_amount' => $upsellAmount, 'bump_sales_amount' => $bumpAmount, 'initial_sales_count' => $analytics['sales_count'][$keyYear][$keyMonth], 'created_at' => Carbon::now()]);
                } else {
                    //echo '<br> Found:'.$exist->id;
                    $id = $exist->id;
                    $rfs_order = RfsOrder::findOrFail($id);
                    $rfs_order->month = $keyMonth;
                    $rfs_order->year = $keyYear;
                    $rfs_order->hop_count = $analytics['hop_count'][$keyYear][$keyMonth];
                    $rfs_order->sale = $salesAmount;
                    $rfs_order->bill = $billAmount;
                    $rfs_order->refund = abs($refundAmount);
                    $rfs_order->charge_back = $chargebackAmount;
                    $rfs_order->fee = abs($feeAmount);
                    $rfs_order->upsell_sales_amount = $upsellAmount;
                    $rfs_order->bump_sales_amount = $bumpAmount;
                    $rfs_order->initial_sales_count = $analytics['sales_count'][$keyYear][$keyMonth];
                    $rfs_order->updated_at = Carbon::now();
                    $rfs_order->save();
                }
            } // keyMonth
        } // foreach
    }

    public static function getAllOrderData($url)
    {
        $data = [];
        $page = 1;
        do {
            $result = curl_init();
            curl_setopt($result, CURLOPT_URL, $url);
            curl_setopt($result, CURLOPT_HEADER, false);
            curl_setopt($result, CURLOPT_HTTPGET, false);
            curl_setopt($result, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($result, CURLOPT_TIMEOUT, 360);
            curl_setopt($result, CURLOPT_HTTPHEADER, array("Accept: application/json", "Authorization: " . env('CLICKBANK_CLIENT_ID') . ":" . env('CLICKBANK_CLIENT_SECRET'), "page:$page"));

            $api_result = json_decode(curl_exec($result), true);

            if ($api_result != null) {
                foreach ($api_result['orderData'] as $row) {
                    $data[] = $row;
                }
            }
            $page++;
        } while (isset($api_result['orderData']['99']));

        return $data;
    }

    public static function getAllOrderDataNew($url)
    {
        $data = [];
        $page = 1;

        do {
            $result = curl_init();
            curl_setopt($result, CURLOPT_URL, $url);
            curl_setopt($result, CURLOPT_HEADER, false);
            curl_setopt($result, CURLOPT_HTTPGET, false);
            curl_setopt($result, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($result, CURLOPT_TIMEOUT, 360);
            curl_setopt($result, CURLOPT_HTTPHEADER, array("Accept: application/json", "Authorization: " . env('CLICKBANK_CLIENT_ID') . ":" . env('CLICKBANK_CLIENT_SECRET'), "page:$page"));

            $api_result = json_decode(curl_exec($result), true);

            if ($api_result != null) {
                $data[] = $api_result['orderData'];
                //echo '<br>'.$url;
                //print_r($api_result['orderData']);
            }
            $page++;
        } while (isset($api_result['orderData']['99']));

        return $data;
    }

    public static function getAllAnalyticsData($url)
    {
        $data = [];
        $page = 1;
        do {
            $result = curl_init();
            curl_setopt($result, CURLOPT_URL, $url);
            curl_setopt($result, CURLOPT_HEADER, false);
            curl_setopt($result, CURLOPT_HTTPGET, false);
            curl_setopt($result, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($result, CURLOPT_TIMEOUT, 360);
            curl_setopt($result, CURLOPT_HTTPHEADER, array("Accept: application/json", "Authorization: " . env('CLICKBANK_CLIENT_ID') . ":" . env('CLICKBANK_CLIENT_SECRET'), "page:$page"));

            $api_result = json_decode(curl_exec($result), true);
            if ($api_result != null) {
                if (isset($api_result['rows']['row'])) {
                    foreach ($api_result['rows']['row'] as $row) {
                        $data[] = $row;
                    }
                }
            }
            $page++;
        } while (isset($api_result['rows']['row']['99']));

        return $data;
    }

    public static function getAllAnalyticsDataNew($url)
    {
        $data = [];
        $page = 1;
        do {
            $result = curl_init();
            curl_setopt($result, CURLOPT_URL, $url);
            curl_setopt($result, CURLOPT_HEADER, false);
            curl_setopt($result, CURLOPT_HTTPGET, false);
            curl_setopt($result, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($result, CURLOPT_TIMEOUT, 360);
            curl_setopt($result, CURLOPT_HTTPHEADER, array("Accept: application/json", "Authorization: " . env('CLICKBANK_CLIENT_ID') . ":" . env('CLICKBANK_CLIENT_SECRET'), "page:$page"));

            $api_result = json_decode(curl_exec($result), true);
            if ($api_result != null) {
                if (isset($api_result['rows']['row'])) {
                    foreach ($api_result['rows']['row'] as $row) {
                        $data[] = $row;
                    }
                }
            }
            $page++;
        } while (isset($api_result['rows']['row']['99']));

        return $data;
    }

    public static function importAffiliateRevenueData()
    {
        for ($i = 4; $i >= 0; $i--) {
            // $i = 0;
            $date = Helper::getNewDate()->modify("-" . $i . "month");
            $end_day = ($i == 0) ? $date->format('d') : $date->format('t');
            $month = $date->format('m');
            $year = $date->format('Y');

            // START SET TARGET

            if ($year == 2019 && $month == 12) {
                $target = 21000;
            } else if ($year == 2020 && $month <= 03) {
                $target = 21000;
            } else if ($year == 2020 && $month >= 7 && $month <= 9) {
                $target = 21000;
            } else if ($year == 2021 && $month == 1) {
                $target = 28000;
            } else if ($year == 2021 && $month == 2) {
                $target = 32500;
            } else if ($year == 2021 && $month == 3) {
                $target = 40000;
            } else if ($year == 2021 && $month == 4) {
                $target = 46400;
            } else if ($year == 2021 && $month == 5) {
                $target = 50000;
            } else if ($year == 2021 && $month == 6) {
                $target = 60000;
            } else if ($year == 2021 && $month == 7) {
                $target = 70000;
            } else if ($year == 2021 && $month == 8) {
                $target = 80000;
            } else if ($year == 2021 && $month == 9) {
                $target = 93000;
            } else if ($year == 2021 && $month == 10) {
                $target = 107000;
            } else if ($year == 2021 && $month == 11) {
                $target = 124500;
            } else if ($year == 2021 && $month == 12) {
                $target = 143000;
            } else {
                $target = 26000;
            }

            // END SET TARGET

            $revenue = 0;
            foreach (static::$tableArray as $table) {

                if ($table == '15happy') {
                    $actStartMonthYear =  strtotime('01' . '/' . static::$tableArrayStartMonth[$table]['month'] . '/' . static::$tableArrayStartMonth[$table]['year']);
                    $curStartMonthYear =  strtotime('01' . '/' . $month . '/' . $year);

                    if ($curStartMonthYear >= $actStartMonthYear) {
                        // Process the curl call
                    } else {
                        break;
                    }
                }

                if ($table == 'godfreq') {
                    $actStartMonthYear1 =  strtotime('01' . '/' . static::$tableArrayStartMonth[$table]['month'] . '/' . static::$tableArrayStartMonth[$table]['year']);
                    $curStartMonthYear1 =  strtotime('01' . '/' . $month . '/' . $year);

                    if ($curStartMonthYear1 >= $actStartMonthYear1) {
                        // Process the curl call
                    } else {
                        break;
                    }
                }

                $page = 1;
                do {
                    $url = "https://api.clickbank.com/rest/1.3/analytics/vendor/affiliate/?account=$table&select=NET_SALE_AMOUNT&startDate=$year-$month-01&endDate=$year-$month-$end_day";

                    $result = curl_init();
                    curl_setopt($result, CURLOPT_URL, $url);
                    curl_setopt($result, CURLOPT_HEADER, false);
                    curl_setopt($result, CURLOPT_HTTPGET, false);
                    curl_setopt($result, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($result, CURLOPT_TIMEOUT, 0);
                    curl_setopt($result, CURLOPT_HTTPHEADER, array("Accept: application/json", "Authorization: " . env('CLICKBANK_CLIENT_ID') . ":" . env('CLICKBANK_CLIENT_SECRET'), "page:$page"));

                    $api_result = json_decode(curl_exec($result), true);
                    if ($api_result['rows'] != null) {

                        if (is_array($api_result['rows']['row']) || is_object($api_result['rows']['row'])) {
                            if (isset($api_result['rows']['row']['dimensionValue'])) {
                                $name = $api_result['rows']['row']['dimensionValue'];
                                if (!in_array($name, static::$excludeAffiliateArray) && !in_array($name, static::$tableArray)) {
                                    $revenue += $api_result['rows']['row']['data']['value']['$'];
                                }
                            } else {
                                foreach ($api_result['rows']['row'] as $row) {
                                    $name = $row['dimensionValue'];

                                    if (!in_array($name, static::$excludeAffiliateArray) && !in_array($name, static::$tableArray)) {
                                        $revenue += $row['data']['value']['$'];
                                    }
                                }
                            }
                        }
                    }
                    $page++;
                } while (isset($api_result['rows']['row']['99']));
            }

            $exist = AffiliateRevenue::where('month', '=', $month)->where('year', '=', $year)->first();
            if ($exist == null) :
                AffiliateRevenue::insert(['month' => $month, 'year' => $year, 'revenue' => $revenue, 'target' => $target, 'created_at' => Carbon::now()]);
            else :
                $id = $exist->id;
                $row = AffiliateRevenue::find($id);
                $row->month = $month;
                $row->year = $year;
                $row->revenue = $revenue;
                $row->target = $target;
                $row->updated_at = Carbon::now();
                $row->save();
            endif;
        }
    }

    public static function importVendorOrderData()
    {
        $i = 0;
        $date = Helper::getNewDate()->modify("-" . $i . "month-1day");
        $start_day = $date->format('d');
        $end_day = $date->format('d');
        $month = $date->format('m');
        $year = $date->format('Y');
        $orders = array();

        foreach (static::$vendorOrderAccount as $va) {
            $modal = Helper::getVendorModal($va);

            for ($j = 0; $j <= 4; $j++) {
                $type = static::$types[$j];
                $url = "https://api.clickbank.com/rest/1.3/orders2/list?vendor=$va&role=VENDOR&type=$type&startDate=$year-$month-$start_day&endDate=$year-$month-$end_day";
                $api_result = Helper::getAllOrderDataNew($url);
                // print_r($api_result);

                if (count($api_result) > 0) {
                    // $arrOrddata = current($api_result);

                    foreach ($api_result as $apData) {
                        if (isset($apData['transactionTime'])) {
                            // echo '<br>---Yes----<br>';
                            $orders[] = $apData;
                        } else {
                            // echo '<br>------NO-----<br>';
                            foreach ($apData as $arrOrder) {
                                $orders[] = $arrOrder;
                            }
                        }
                    }
                }
            }
        }

        //echo '<br>========== FINAL ORDERs ==========<br>';
        //print_r( $orders);

        foreach ($orders as $dataOrd) {
            $transactionTime =  Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', strtotime($dataOrd['transactionTime'])))->setTimezone('America/Los_Angeles');
            $transactionType = $dataOrd['transactionType'];
            $affiliate = (is_array($dataOrd['affiliate']) ? 'Not Set' : $dataOrd['affiliate']);
            $vendor = $dataOrd['vendor'];
            $modal = Helper::getVendorModal(strtolower($vendor));
			
            // check if multiple 'lineItemData' exists
            if (isset($dataOrd['lineItemData']['lineItemType'])) {
                $customerAmount = $dataOrd['lineItemData']['customerAmount'];
                $accountAmount = $dataOrd['lineItemData']['accountAmount'];
                $lineItemType = $dataOrd['lineItemData']['lineItemType'];

                // Save in DB
                $modal::insert([
                    'transactionTime' => $transactionTime,
                    'transactionType' => $transactionType,
                    'vendor' => $vendor,
                    'affiliate' => $affiliate,
                    'customerAmount' => $customerAmount,
                    'accountAmount' => $accountAmount,
                    'lineItemType' => $lineItemType,
                    'created_at' => Carbon::now()
                ]);
            } else {
                foreach ($dataOrd['lineItemData'] as $dataLineItem) {
                    $customerAmount = $dataLineItem['customerAmount'];
                    $accountAmount = $dataLineItem['accountAmount'];
                    $lineItemType = $dataLineItem['lineItemType'];

                    // Save in DB
                    $modal::insert([
                        'transactionTime' => $transactionTime,
                        'transactionType' => $transactionType,
                        'vendor' => $vendor,
                        'affiliate' => $affiliate,
                        'customerAmount' => $customerAmount,
                        'accountAmount' => $accountAmount,
                        'lineItemType' => $lineItemType,
                        'created_at' => Carbon::now()
                    ]);
                }
            }
        }
    }

    public static function getVendorModal($account)
    {
        switch ($account) {
            case '15manifest':
                $modal = new Vendor15Manifest();
                break;
            case 'amazeyou2':
                $modal = new VendorAmazingYou2();
                break;
            case 'wactivator':
                $modal = new VendorWactivator();
                break;
            case 'qmanifest':
                $modal = new VendorQmanifest();
                break;
            case '15weight':
                $modal = new Vendor15Weight();
                break;
            case 'pnmanifest':
                $modal = new VendorPnmanifest();
                break;
            case 'sleepwaves':
                $modal = new VendorSleepwaves();
                break;
            case 'ancientsec':
                $modal = new VendorAncientsec();
                break;
            case 'millionb':
                $modal = new VendorMillionb();
                break;
            case 'medicicode':
                $modal = new VendorMedicicode();
                break;
            case '15happy':
                $modal = new Vendor15Happy();
                break;
            case 'godfreq':
                $modal = new VendorGodfreq();
                break;
            case 'mtimewarp':
                $modal = new VendorMtimewarp();
                break;
            case 'mmswitch':
                $modal = new VendorMmswitch();
                break;
            case 'metabolicb':
                $modal = new VendorMetabolicb();
                break;
            case 'type2free':
                $modal = new VendorType2free();
                break;
            case 'upmagnet':
                $modal = new VendorUpmagnet();
                break;
        }

        return $modal;
    }

    public static function getAffiliateVendorModal($account)
    {
        switch ($account) {
            case '15manifest':
                $modal = new Affiliate15Manifest();
                break;
            case 'amazeyou2':
                $modal = new AffiliateAmazeyou2();
                break;
            case 'wactivator':
                $modal = new AffiliateWactivator();
                break;
            case 'qmanifest':
                $modal = new AffiliateQmanifest();
                break;
            case '15weight':
                $modal = new Affiliate15Weight();
                break;
            case 'pnmanifest':
                $modal = new AffiliatePnmanifest();
                break;
            case 'sleepwaves':
                $modal = new AffiliateSleepwaves();
                break;
            case 'ancientsec':
                $modal = new AffiliateAncientsec();
                break;
            case 'millionb':
                $modal = new AffiliateMillionb();
                break;
            case 'medicicode':
                $modal = new AffiliateMedicicode();
                break;
            case '15happy':
                $modal = new Affiliate15Happy();
                break;
            case 'godfreq':
                $modal = new AffiliateGodfreq();
                break;
            case 'mtimewarp':
                $modal = new AffiliateMtimewarp();
                break;
            case 'mmswitch':
                $modal = new AffiliateMmswitch();
                break;
            case 'metabolicb':
                $modal = new AffiliateMetabolicb();
                break;
            case 'type2free':
                $modal = new AffiliateType2free();
                break;
            case 'upmagnet':
                $modal = new AffiliateUpmagnet();
                break;
        }

        return $modal;
    }

    public static function importVendorHopcountData()
    {
        // MAXIMUM PASS MONTH CAN GET IS 4
        $i = 0;
        $date = Helper::getNewDate()->modify("-" . $i . "month");
        $end_day = ($i == 0) ? $date->format('d') : $date->format('t');
        $month = $date->format('m');
        $year = $date->format('Y');

        foreach (static::$vendorOrderAccount as $va) {
            $url = "https://api.clickbank.com/rest/1.3/analytics/vendor/affiliate/?account=$va&select=HOP_COUNT&select=SALE_COUNT&startDate=$year-$month-01&endDate=$year-$month-$end_day";
            $api_result = Helper::getAllAnalyticsData($url);
            foreach ($api_result as $a) {
                if (isset($a['dimensionValue']) && !empty($a['dimensionValue']) && !in_array($a['dimensionValue'], static::$excludeVendorAffiliateArray)) {
                    $affiliate = $a['dimensionValue'];
                    $hop_count = $a['data'][0]['value']['$'];
                    $initial_sales_count = $a['data'][1]['value']['$'];
                    $count = VendorHopcount::where('month', '=', $month)->where('year', '=', $year)->where('vendor', '=', $va)->where('affiliate', '=', $affiliate)->get()->count();
                    if ($count == 0) {
                        VendorHopcount::insert(['month' => $month, 'year' => $year, 'vendor' => $va, 'affiliate' => $affiliate, 'hop_count' => $hop_count, 'initial_sales_count' => $initial_sales_count, 'created_at' => Carbon::now()]);
                    } else {
                        $vh = VendorHopcount::where('month', '=', $month)->where('year', '=', $year)->where('vendor', '=', $va)->where('affiliate', '=', $affiliate)->first();
                        $vh->hop_count = $hop_count;
                        $vh->initial_sales_count = $initial_sales_count;
                        $vh->updated_at = Carbon::now();
                        $vh->save();
                    }
                }
            }
        }
    }

    public static function importVendorTopAffiliateData($account, $minusDay, $table, $times)
    {
        $dateDay = Helper::getNewDate()->modify($minusDay)->format('d');
        $dateMonth = Helper::getNewDate()->modify($minusDay)->format('m');
        $dateYear = Helper::getNewDate()->modify($minusDay)->format('Y');

        $allArray = [];

        if ($table == 'yesterday') {
            $url = "https://api.clickbank.com/rest/1.3/analytics/vendor/affiliate/?account=$account&select=SALE_COUNT&select=NET_SALE_AMOUNT&select=REBILL_AMOUNT&select=SALE_AMOUNT&select=UPSELL_AMOUNT&select=HOP_COUNT&orderBy=NET_SALE_AMOUNT&startDate=$dateYear-$dateMonth-$dateDay&endDate=$dateYear-$dateMonth-$dateDay";
        } else {
            $url = "https://api.clickbank.com/rest/1.3/analytics/vendor/affiliate/?account=$account&select=SALE_COUNT&select=NET_SALE_AMOUNT&select=REBILL_AMOUNT&select=SALE_AMOUNT&select=UPSELL_AMOUNT&select=HOP_COUNT&orderBy=NET_SALE_AMOUNT&startDate=$dateYear-$dateMonth-$dateDay&endDate=" . Helper::getNewDate()->format('Y-m-d');
        }

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

                $affiliate =  $api_result['rows']['row']['dimensionValue'];
                $rebill =    $api_result['rows']['row']['data']['0']['value']['$'];
                $up_sell = $api_result['rows']['row']['data']['1']['value']['$'];
                $hop_count = $api_result['rows']['row']['data']['2']['value']['$'];
                $sales = $api_result['rows']['row']['data']['3']['value']['$'];
                $front_end = $api_result['rows']['row']['data']['4']['value']['$'];
                $sale_count = $api_result['rows']['row']['data']['5']['value']['$'];
                $fe_cvr = ($front_end == 0 || $hop_count == 0) ? 0 : ($sale_count / $hop_count) * 100;

                $affiliate_comms = Helper::calculateAffiliateCommission($account, $front_end, $up_sell, $rebill, $affiliate, $dateMonth, $dateYear);
                $affiliate_EPC = ($hop_count == 0) ? 0 : number_format($affiliate_comms / $hop_count, 2);
                $affiliate = (Helper::getPartnerName($affiliate) == '') ? $affiliate : Helper::getPartnerName($affiliate) . ' (' . $affiliate . ')';

                if (!in_array($affiliate, static::$excludeVendorAffiliateArray)) {
                    $data['sales'] = $sales;
                    $data['count'] = $hop_count;
                    $data['account'] = $account;
                    $data['affiliate'] = $affiliate;
                    $data['affiliate_comms'] = $affiliate_comms;
                    $data['affiliate_epc'] = str_replace(',', '', $affiliate_EPC);
                    $data['fe_cvr'] = $fe_cvr;

                    array_push($allArray, $data);
                    $data = [];
                    $inserted_data_count++;
                }
            } else {

                foreach ($api_result['rows']['row'] as $row) {

                    $affiliate = $row['dimensionValue'];
                    $rebill = $row['data']['0']['value']['$'];
                    $up_sell = $row['data']['1']['value']['$'];
                    $hop_count = $row['data']['2']['value']['$'];
                    $sales = $row['data']['3']['value']['$'];
                    $front_end = $row['data']['4']['value']['$'];
                    $sale_count = $row['data']['5']['value']['$'];
                    $fe_cvr = ($front_end == 0 || $hop_count == 0) ? 0 : ($sale_count / $hop_count) * 100;

                    $affiliate_comms = Helper::calculateAffiliateCommission($account, $front_end, $up_sell, $rebill, $affiliate, $dateMonth, $dateYear);
                    $affiliate_EPC = ($hop_count == 0) ? 0 : number_format($affiliate_comms / $hop_count, 2);
                    $affiliate = (Helper::getPartnerName($affiliate) == '') ? $affiliate : Helper::getPartnerName($affiliate) . ' (' . $affiliate . ')';

                    if (!in_array($affiliate, static::$excludeVendorAffiliateArray)) {
                        $data['sales'] = $sales;
                        $data['count'] = $hop_count;
                        $data['account'] = $account;
                        $data['affiliate'] = $affiliate;
                        $data['affiliate_comms'] = $affiliate_comms;
                        $data['affiliate_epc'] = str_replace(',', '', $affiliate_EPC);
                        $data['fe_cvr'] = $fe_cvr;

                        array_push($allArray, $data);
                        $data = [];
                        $inserted_data_count++;
                    }
                    $i++;
                    if ($i >= $total_count) {
                        break;
                    }
                }
            }
        }

        arsort($allArray);
        $allArray = array_slice($allArray, 0, 10);

        foreach ($allArray as $a) {
            $ta = new VendorTopAffiliate();
            $ta->times = $times;
            $ta->affiliate_id = $a['affiliate'];
            $ta->vendor_id = $a['account'];
            $ta->hop_count = $a['count'];
            $ta->fe_cvr = $a['fe_cvr'];
            $ta->affiliate_epc = str_replace(',', '', $a['affiliate_epc']);
            $ta->affiliate_revenue = $a['affiliate_comms'];
            $ta->vendor_revenue = $a['sales'];
            $ta->created_at = Carbon::now();
            $ta->save();
        }
    }

    public static function importNewlyAddVendorOrderData()
    {
        $va = '';
        $modal = Helper::getVendorModal($va);

        for ($j = 0; $j <= 4; $j++) {
            $type = static::$types[$j];

            $url = "https://api.clickbank.com/rest/1.3/orders2/list?vendor=$va&role=VENDOR&type=$type&startDate=2020-12-01&endDate=2021-05-01";
            $api_result = Helper::getAllOrderData($url);
            if ($api_result != null) {
                foreach ($api_result as $a) {
                    $skip = 0;
                    try {
                        if ($a != []) {
                            if (isset($a['lineItemData']['accountAmount'])) {
                                $transactionTime = Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', strtotime($a['transactionTime'])))->setTimezone('America/Los_Angeles');
                                $transactionType = $a['transactionType'];
                                $affiliate = (is_array($a['affiliate']) ? 'Not Set' : $a['affiliate']);
                                $vendor = $a['vendor'];
                                $customerAmount = $a['lineItemData']['customerAmount'];
                                $accountAmount = $a['lineItemData']['accountAmount'];
                                $lineItemType = $a['lineItemData']['lineItemType'];
                            } elseif (($a['lineItemData'][0])) {
                                $transactionTime = Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', strtotime($a['transactionTime'])))->setTimezone('America/Los_Angeles');
                                $transactionType = $a['transactionType'];
                                $affiliate = (is_array($a['affiliate']) ? 'Not Set' : $a['affiliate']);
                                $vendor = $a['vendor'];
                                $customerAmount = $a['lineItemData'][0]['customerAmount'] + $a['lineItemData'][1]['customerAmount'];
                                $accountAmount = $a['lineItemData'][0]['accountAmount'] + $a['lineItemData'][1]['accountAmount'];
                                $lineItemType = $a['lineItemData'][0]['lineItemType'];
                            } else {
                                $skip = 1;
                                $transactionTime = Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', strtotime($api_result[0])))->setTimezone('America/Los_Angeles');
                                $transactionType = $api_result[4];
                                $vendor = $api_result[8];

                                $affiliate = (is_array($api_result[9]) ? 'Not Set' : $api_result[9]);
                                if (is_object($api_result[22])) {
                                    $customerAmount = $api_result[22][0]['customerAmount'];
                                    $accountAmount = $api_result[22][0]['accountAmount'];
                                    $lineItemType = $api_result[22][0]['lineItemType'];
                                } else {
                                    $customerAmount = $api_result[22]['customerAmount'];
                                    $accountAmount = $api_result[22]['accountAmount'];
                                    $lineItemType = $api_result[22]['lineItemType'];
                                }
                            }
                        }

                        $transactionTime = date('Y-m-d H:i:s', strtotime($transactionTime));
                    } catch (Exception $e) {
                        echo $e->getMessage();
                    }

                    $modal::insert([
                        'transactionTime' => $transactionTime,
                        'transactionType' => $transactionType,
                        'vendor' => $vendor,
                        'affiliate' => $affiliate,
                        'customerAmount' => $customerAmount,
                        'accountAmount' => $accountAmount,
                        'lineItemType' => $lineItemType,
                        'created_at' => Carbon::now()
                    ]);

                    if ($skip == 1) {
                        break;
                    }
                }
            }
        }
    }

    public static function importNewlyAddVendorHopcountData()
    {
        $va = '';
        // MAXIMUM PASS MONTH CAN GET IS 4
        for ($i = 4; $i >= 0; $i--) {
            $pre_set_date = Helper::getNewDate();
            $date = $pre_set_date->modify('-' . $i . ' month');
            $end_day = ($i == 0) ? $date->format('d') : $date->format('t');
            $month = $date->format('m');
            $year = $date->format('Y');

            $url = "https://api.clickbank.com/rest/1.3/analytics/vendor/affiliate/?account=$va&select=HOP_COUNT&select=SALE_COUNT&startDate=$year-$month-01&endDate=$year-$month-$end_day";
            $api_result = Helper::getAllAnalyticsData($url);
            foreach ($api_result as $a) {
                if (isset($a['dimensionValue']) && !empty($a['dimensionValue'])) {
                    $affiliate = $a['dimensionValue'];
                    $hop_count = $a['data'][0]['value']['$'];
                    $initial_sales_count = $a['data'][1]['value']['$'];

                    $count = VendorHopcount::where('month', '=', $month)->where('year', '=', $year)->where('vendor', '=', $va)->where('affiliate', '=', $affiliate)->get()->count();
                    if ($count == 0) {
                        VendorHopcount::insert(['month' => $month, 'year' => $year, 'vendor' => $va, 'affiliate' => $affiliate, 'hop_count' => $hop_count, 'initial_sales_count' => $initial_sales_count, 'created_at' => Carbon::now()]);
                    } else {
                        $vh = VendorHopcount::where('month', '=', $month)->where('year', '=', $year)->where('vendor', '=', $va)->where('affiliate', '=', $affiliate)->first();
                        $vh->hop_count = ($hop_count != null) ? $hop_count : 0;
                        $vh->initial_sales_count = $initial_sales_count;
                        $vh->updated_at = Carbon::now();
                        $vh->save();
                    }
                }
            }
        }
    }
}
