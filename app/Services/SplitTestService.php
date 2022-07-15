<?php

namespace App\Services;

use GuzzleHttp\Client;

class SplitTestService
{
    public $account_list = ['mmswitch', 'amazeyou'];

    public function getResponse($host, $key, $cid, $start_date, $end_date)
    {
        $client = new Client();
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ];

        $url = "https://cpv.$host/api/stats/?key=$key&camp=$cid&report=all&start=$start_date&end=$end_date";
        $response = json_decode($client->request('GET', $url, ['headers' => $headers])->getBody(), true);
        return $response;
    }

    public function getAccountDetails($account)
    {
        $data = [];
        switch ($account) {
            case 'mmswitch':
                $data['host'] = "millionairesmindswitch.com";
                $data['key'] = "fq5m2rnxauibae7m";
                $data['campaign_id'] = [3, 4, 5];
                break;
            case 'amazeyou':
                $data['host'] = "theamazingyou.com";
                $data['key'] = "2ixx85vhrkbh2ig7";
                $data['campaign_id'] = [12, 13];
                break;
        }
        return $data;
    }

    public function getAccountToProcess($account)
    {
        $data = [];
        switch ($account) {
            case 'all-account':
                $data = $this->account_list;
                break;
            default:
                $data[] = $account;
        }
        return $data;
    }

    public function getViews($response, $type)
    {
        $view = 0;
        foreach ($response['Rows']['Landing'] as $r) {
            if (strstr($r['LP'], $type)) {
                $view += $r['Views'];
            }
        }
        return $view;
    }

    public function getConvertion($response, $type)
    {
        $total_conversion = 0;
        foreach ($response['Rows']['Offer'] as $r) {
            if (strstr($r['LP'], $type)) {
                $total_conversion += $r['Conversion'];
            }
        }
        return $total_conversion;
    }

    public function setResults($response, $type)
    {
        $data = [];
        foreach ($response['Rows']['Offer'] as $r) {
            if (strstr($r['LP'], $type)) {
                $data[] = $r;
            }
        }
        return $data;
    }
}
