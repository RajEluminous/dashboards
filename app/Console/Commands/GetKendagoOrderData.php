<?php

namespace App\Console\Commands;

use App\Helper\Helper;
use App\Http\Controllers\ImportKendagoOrderController;
use Illuminate\Console\Command;

class GetKendagoOrderData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kendago:get_kendago_order_data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Kendago Order Data';

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
        ImportKendagoOrderController::importKendagoOrderData();
    }
}
