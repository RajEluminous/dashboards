<?php

namespace App\Console\Commands;

use App\Helper\Helper;
use App\Models\AffiliateRevenue;
use App\Models\CurrentMonth;
use App\Models\LastMonth;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SendAffiliateRevenueEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'affiliate_revenue:send_email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily affiliate revenue email';

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
        set_time_limit(-1);
        Helper::generateAffiliateRevenuePDF();
    }
}
