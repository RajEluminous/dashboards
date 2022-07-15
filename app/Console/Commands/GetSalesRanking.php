<?php

namespace App\Console\Commands;

use App\Helper\Helper;
use Illuminate\Console\Command;

class GetSalesRanking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'affiliate_revenue:get_sales_ranking';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Sales Ranking';

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
        $helper = new Helper();
        $helper::insertSalesRanking($helper::getNewDate(), 'current_month');
        $helper::insertSalesRanking($helper::getNewDate()->modify(date("Y-m-d", strtotime('last day of previous month'))), 'last_month');
    }
}
