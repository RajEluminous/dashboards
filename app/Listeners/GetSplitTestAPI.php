<?php

namespace App\Listeners;

use App\Events\PrepareSplitTestAPI;
use App\Services\SplitTestService;
use GuzzleHttp\Client;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class GetSplitTestAPI
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  PrepareSplitTestAPI  $event
     * @return void
     */
    public function handle(PrepareSplitTestAPI $event)
    {
        $splitTestService = new SplitTestService();
        $records = [];
        $start_date = '2019-01-01';
        $end_date = '2050-12-31';

        $accountToProcess = $splitTestService->getAccountToProcess($event->selected_account);

        foreach ($accountToProcess as $atp) {
            $rows = [];
            $accountDetail = $splitTestService->getAccountDetails($atp);

            foreach ($accountDetail['campaign_id'] as $cid) {
                $data = [];

                $response = $splitTestService->getResponse($accountDetail['host'], $accountDetail['key'], $cid, $start_date, $end_date);

                // SET ACCOUNT
                $data['account'] = $atp;
                // SET ACCOUNT

                // GET CAMPAIGN NAME
                $data['campaign_name'] = $response['Rows']['Campaign'][0]['LP'];
                // GET CAMPAIGN NAME

                // GET VIEWS 
                $ctrl_view = $splitTestService->getViews($response, 'Ctrl');
                $test_view = $splitTestService->getViews($response, 'Test');
                // GET VIEWS

                // GET CONVERSION AND REVENUE
                $ctrl_total_conversion = $splitTestService->getConvertion($response, 'Ctrl');
                $test_total_conversion = $splitTestService->getConvertion($response, 'Test');
                // GET CONVERSION AND REVENUE

                // SET DATA
                $data['rows']['ctrl']['results'] = $splitTestService->setResults($response, 'Ctrl');
                $data['rows']['test']['results'] = $splitTestService->setResults($response, 'Test');
                $data['rows']['ctrl']['views'] = $ctrl_view;
                $data['rows']['test']['views'] = $test_view;
                $data['rows']['ctrl']['total_conversion'] = $ctrl_total_conversion;
                $data['rows']['test']['total_conversion'] = $test_total_conversion;
                $data['rows']['winner'] = ($ctrl_total_conversion >= $test_total_conversion) ? 'Ctrl' : 'Test';

                $significiant = 0;

                if ($ctrl_total_conversion == 0 || $test_total_conversion == 0) {
                    $significiant = 0;
                } else {
                    $significiant = ((($test_total_conversion / $test_view) - ($ctrl_total_conversion / $ctrl_view)) / sqrt(($ctrl_total_conversion / $ctrl_view) * (1 - $ctrl_total_conversion / $ctrl_view) / $test_view) - 1.64 > 0) ? 'YES' : 'NO';
                }
                $data['rows']['significiant'] = $significiant;
                $rows[] = $data;
                // SET DATA
            }
            $records[] = $rows;
        }
        return $records;
    }
}
