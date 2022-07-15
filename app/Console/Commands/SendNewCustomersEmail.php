<?php

namespace App\Console\Commands;

use App\Http\Controllers\SendCustomerNameController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendNewCustomersEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tay:send_new_customer_list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email to Marion with last month new customers';

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
        SendCustomerNameController::sendEmail();
    }
}
