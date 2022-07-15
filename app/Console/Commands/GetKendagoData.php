<?php

namespace App\Console\Commands;

use App\Helper\Helper;
use Illuminate\Console\Command;

class GetKendagoData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kendago:get_kendago_data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Kendago Data';

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
        Helper::importKendagoData();
    }
}
