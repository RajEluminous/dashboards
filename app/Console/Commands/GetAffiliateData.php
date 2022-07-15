<?php

namespace App\Console\Commands;

use App\Helper\Helper;
use App\Models\AffiliateRevenue;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GetAffiliateData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'affiliate_revenue:get_affiliate_revenue_data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Affiliate Revenue Data';

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
        Helper::importAffiliateRevenueData();
    }
}
