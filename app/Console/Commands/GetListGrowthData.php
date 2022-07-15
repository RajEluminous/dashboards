<?php

namespace App\Console\Commands;

use App\Helper\Helper;
use App\Models\ListGrowth;
use Exception;
use Illuminate\Console\Command;

class GetListGrowthData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'list_growth:get_list_growth_data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get List Growth Data';

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
     * @return mixed
     */
    public function handle()
    {
        $data = [];
        try {
            $data['growth'][] = Helper::GetGrowthResponse("15manifest_leads", "1071969", "1");
            $data['growth'][] = Helper::GetGrowthResponse("15_manifest_customers", "1071969", "2");
            $data['growth'][] = Helper::GetGrowthResponse("qmanifest_leads", "1011193", "1");
            $data['growth'][] = Helper::GetGrowthResponse("qmanifest_customers", "1011193", "2");
            $data['growth'][] = Helper::GetGrowthResponse("amazeyou_leads", "1022666", "1");
            $data['growth'][] = Helper::GetGrowthResponse("amazeyou_customers", "1022666", "2");
            $data['growth'][] = Helper::GetGrowthResponse("wactivator_leads", "1267038", "1");
            $data['growth'][] = Helper::GetGrowthResponse("wactivator_customers", "1267038", "2");
            $data['growth'][] = Helper::GetGrowthResponse("15weight_leads", "1374619", "1");
            $data['growth'][] = Helper::GetGrowthResponse("15weight_customers", "1374619", "2");
            $data['growth'][] = Helper::GetGrowthResponse("millionb_leads", "1023113", "1");
            $data['growth'][] = Helper::GetGrowthResponse("millionb_customers", "1023113", "2");
            $data['growth'][] = Helper::GetGrowthResponse("ancientsec_leads", "930132", "1");
            $data['growth'][] = Helper::GetGrowthResponse("ancientsec_customers", "930132", "2");
            $data['growth'][] = Helper::GetGrowthResponse("15happy_leads", "1515534", "1");
            $data['growth'][] = Helper::GetGrowthResponse("15happy_customers", "1515534", "2");
            $data['growth'][] = Helper::GetGrowthResponse("godfreq_leads", "1425050", "1");
            $data['growth'][] = Helper::GetGrowthResponse("godfreq_customers", "1425050", "2");

            $data['size'][] = Helper::GetSizeResponse("15manifest_leads", "1071969", "1");
            $data['size'][] = Helper::GetSizeResponse("15_manifest_customers", "1071969", "2");
            $data['size'][] = Helper::GetSizeResponse("qmanifest_leads", "1011193", "1");
            $data['size'][] = Helper::GetSizeResponse("qmanifest_customers", "1011193", "2");
            $data['size'][] = Helper::GetSizeResponse("amazeyou_leads", "1022666", "1");
            $data['size'][] = Helper::GetSizeResponse("amazeyou_customers", "1022666", "2");
            $data['size'][] = Helper::GetSizeResponse("wactivator_leads", "1267038", "1");
            $data['size'][] = Helper::GetSizeResponse("wactivator_customers", "1267038", "2");
            $data['size'][] = Helper::GetSizeResponse("15weight_leads", "1374619", "1");
            $data['size'][] = Helper::GetSizeResponse("15weight_customers", "1374619", "2");
            $data['size'][] = Helper::GetSizeResponse("millionb_leads", "1023113", "1");
            $data['size'][] = Helper::GetSizeResponse("millionb_customers", "1023113", "2");
            $data['size'][] = Helper::GetSizeResponse("ancientsec_leads", "930132", "1");
            $data['size'][] = Helper::GetSizeResponse("ancientsec_customers", "930132", "2");
            $data['size'][] = Helper::GetSizeResponse("15happy_leads", "1515534", "1");
            $data['size'][] = Helper::GetSizeResponse("15happy_customers", "1515534", "2");
            $data['size'][] = Helper::GetSizeResponse("godfreq_leads", "1425050", "1");
            $data['size'][] = Helper::GetSizeResponse("godfreq_customers", "1425050", "2");

            $data['click'][] = Helper::GetClickResponse("15manifest_leads", "1071969", "1");
            $data['click'][] = Helper::GetClickResponse("15_manifest_customers", "1071969", "2");
            $data['click'][] = Helper::GetClickResponse("qmanifest_leads", "1011193", "1");
            $data['click'][] = Helper::GetClickResponse("qmanifest_customers", "1011193", "2");
            $data['click'][] = Helper::GetClickResponse("amazeyou_leads", "1022666", "1");
            $data['click'][] = Helper::GetClickResponse("amazeyou_customers", "1022666", "2");
            $data['click'][] = Helper::GetClickResponse("wactivator_leads", "1267038", "1");
            $data['click'][] = Helper::GetClickResponse("wactivator_customers", "1267038", "2");
            $data['click'][] = Helper::GetClickResponse("15weight_leads", "1374619", "1");
            $data['click'][] = Helper::GetClickResponse("15weight_customers", "1374619", "2");
            $data['click'][] = Helper::GetClickResponse("millionb_leads", "1023113", "1");
            $data['click'][] = Helper::GetClickResponse("millionb_customers", "1023113", "2");
            $data['click'][] = Helper::GetClickResponse("ancientsec_leads", "930132", "1");
            $data['click'][] = Helper::GetClickResponse("ancientsec_customers", "930132", "2");
            $data['click'][] = Helper::GetClickResponse("15happy_leads", "1515534", "1");
            $data['click'][] = Helper::GetClickResponse("15happy_customers", "1515534", "2");
            $data['click'][] = Helper::GetClickResponse("godfreq_leads", "1425050", "1");
            $data['click'][] = Helper::GetClickResponse("godfreq_customers", "1425050", "2");
        } catch (Exception $e) {
            sleep(62);
        }

        // INSERT INTO DATABASE
        foreach ($data as $unique_data) {
            foreach ($unique_data as $d) {
                $account_id = $d['account_id'];
                $types = $d['types'];
                $from = $d['from'];
                $row = 1;

                foreach ($d['times'] as $time_label => $value) {
                    $get_account = ListGrowth::where('account_id', '=', $account_id)->where('types', '=', $types)->where('from', '=', $from)->where('row', '=', $row)->get();
                    if ($get_account->count() == 0) {
                        $lg = new ListGrowth();
                        $lg->account_id = $account_id;
                        $lg->types = $types;
                        $lg->from = $from;
                        $lg->row = $row;
                        $lg->time_label = $time_label;
                        $lg->value = str_replace(',', '', $value);
                        $lg->save();
                    } else {
                        ListGrowth::where('account_id', '=', $account_id)->where('types', '=', $types)->where('from', '=', $from)->where('row', '=', $row)->update(['value' => str_replace(',', '', $value)]);
                    }

                    $row++;
                }
            }
        }
    }
}
