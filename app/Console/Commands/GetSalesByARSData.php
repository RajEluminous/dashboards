<?php

namespace App\Console\Commands;

use App\Helper\Helper;
use Illuminate\Console\Command;

class GetSalesByARSData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sales_by_ars:get_sales_by_ars_data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Sales By ARS Data';

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
        // SalesByARS::truncate();
        $account_list = ['15manifest', 'wactivator', 'amazeyou', 'godfreq'];
        foreach ($account_list as $account) {
            if ($account != '15manifest') {
                Helper::getCBSale('-7 day', $account, 'free', '2');
                Helper::getCBSale('-7 day', $account, 'cust', '1');

                Helper::getCBSale('-60 day', $account, 'free', '2');
                Helper::getCBSale('-60 day', $account, 'cust', '1');

                Helper::getCBSale('-90 day', $account, 'free', '2');
                Helper::getCBSale('-90 day', $account, 'cust', '1');
            } else {
                Helper::getCBSale('-7 day', $account, '15manifest_world_pp', '3');
                Helper::getCBSale('-7 day', $account, 'digimani', '4');
                Helper::getCBSale('-7 day', $account, '15manifest_mw', '5');
                Helper::getCBSale('-7 day', $account, '15manifest_me', '6');
                Helper::getCBSale('-7 day', $account, '15manifest_quiz', '7');

                Helper::getCBSale('-60 day', $account, '15manifest_world_pp', '3');
                Helper::getCBSale('-60 day', $account, 'digimani', '4');
                Helper::getCBSale('-60 day', $account, '15manifest_mw', '5');
                Helper::getCBSale('-60 day', $account, '15manifest_me', '6');
                Helper::getCBSale('-60 day', $account, '15manifest_quiz', '7');


                Helper::getCBSale('-90 day', $account, '15manifest_world_pp', '3');
                Helper::getCBSale('-90 day', $account, 'digimani', '4');
                Helper::getCBSale('-90 day', $account, '15manifest_mw', '5');
                Helper::getCBSale('-90 day', $account, '15manifest_me', '6');
                Helper::getCBSale('-90 day', $account, '15manifest_quiz', '7');
            }
        }
    }
}
