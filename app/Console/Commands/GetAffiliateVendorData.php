<?php

namespace App\Console\Commands;

use App\Http\Controllers\AffiliatePerformanceController;
use Illuminate\Console\Command;
 

class GetAffiliateVendorData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'affiliate_performance:get_affiliate_vendor_data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'cron job to get the newly added affiliate vendor data';

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
        AffiliatePerformanceController::getAffiliateVendorData();
    }
}
