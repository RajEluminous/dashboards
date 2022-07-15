<?php

namespace App\Console\Commands;

use App\Helper\Helper;
use Illuminate\Console\Command;

class SendSalesByTIDEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sales_by_tid:send_email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily sales by tid email';

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
        Helper::generateSalesByTIDPDF();
    }
}
