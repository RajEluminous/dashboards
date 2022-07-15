<?php

namespace App\Console\Commands;

use App\Helper\Helper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class LogDataEmpty extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'log:data_empty';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Log out revenue';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $tableArray = ['15manifest', 'qmanifest', 'wactivator', 'amazeyou', '15weight', 'millionb', 'ancientsec', 'pnmanifest', 'amazeyou2', 'sleepwaves'];
        $excludeAffiliateArray = ['101fb2c', 'magicplay', 'leekuanyew', 'verifydata', 'wactivator', 'amazeyou', 'Not Set', 'ancientsec', 'futureaff', 'checkdata'];


        for ($i = 4; $i >= 0; $i--) {
            // $i = 0;
            $pre_set_date = Helper::getNewDate();
            $date = $pre_set_date->modify('-' . $i . ' month');
            $end_day = ($i == 0) ? $date->format('d') : $date->format('t');
            $month = $date->format('m');
            $year = $date->format('Y');

            // END SET TARGET

            $revenue = 0;
            foreach ($tableArray as $table) {
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
                                if (!in_array($name, $excludeAffiliateArray)) {
                                    $revenue += $api_result['rows']['row']['data']['value']['$'];
                                }
                            } else {
                                foreach ($api_result['rows']['row'] as $row) {
                                    $name = $row['dimensionValue'];

                                    if (!in_array($name, $excludeAffiliateArray)) {
                                        $revenue += $row['data']['value']['$'];
                                    }
                                }
                            }
                        }
                    }
                    $page++;
                } while (isset($api_result['rows']['row']['99']));
            }

            Log::info('Revenue = ' . $revenue);
        }
    }
}
