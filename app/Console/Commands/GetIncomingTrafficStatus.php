<?php

namespace App\Console\Commands;

use App\Helper\Helper;
use Illuminate\Console\Command;

class GetIncomingTrafficStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'affiliate_revenue:get_incoming_traffic_status';

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
        $helper = new Helper();
        $helper::insertIncomingTrafficStatus($helper::getNewDate(), 'current_month');
        $helper::insertIncomingTrafficStatus($helper::getNewDate()->modify(date("Y-m-d", strtotime('last day of previous month'))), 'last_month');
    }
}
