<?php

namespace App\Console\Commands;

use App\Helper\Helper;
use App\Models\VendorTopAffiliate;
use Illuminate\Console\Command;

class GetVendorTopAffiliate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vendor_order:get_top_affiliate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        VendorTopAffiliate::truncate();
        foreach (Helper::$vendorOrderAccount as $account) {
            Helper::importVendorTopAffiliateData($account, '-1 day', 'yesterday', '1');
            Helper::importVendorTopAffiliateData($account, '-7 day', 'last7day', '2');
            Helper::importVendorTopAffiliateData($account, '-30 day', 'last30day', '3');
            Helper::importVendorTopAffiliateData($account, '-120 day', 'last120day', '4');
        }
    }
}
