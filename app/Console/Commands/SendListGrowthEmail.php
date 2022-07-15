<?php

namespace App\Console\Commands;

use App\Helper\Helper;

use Illuminate\Console\Command;

class SendListGrowthEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'list_growth:send_email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily list growth email';

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
        Helper::generateListGrowthPDF();
    }
}
