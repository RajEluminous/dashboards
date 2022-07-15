<?php

namespace App\Console\Commands;

use App\Helper\Helper;
use App\Models\TopAffiliate;
use Illuminate\Console\Command;

class GetTopAffiliate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'affiliate_revenue:get_top_affiliate';

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
     * @return mixed
     */
    public function handle()
    {
        TopAffiliate::truncate();
        Helper::topAffiliate('1 day', 'today', '1');
        Helper::topAffiliate('-1 day', 'yesterday', '2');
        Helper::topAffiliate('-7 day', 'last7day', '3');
        Helper::topAffiliate('-30 day', 'last30day', '4');
    }
}
