<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\RefreshToken::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // if no timezone set it will follow project time zone
        // Log::error("Works!");

        // AFFILIATE REVENUE
        $schedule->exec("php artisan affiliate_revenue:get_affiliate_revenue_data")->dailyAt('00:05')->timezone('America/Los_Angeles');
        $schedule->exec("php artisan affiliate_revenue:get_sales_ranking")->dailyAt('00:05')->timezone('America/Los_Angeles');
        $schedule->exec("php artisan affiliate_revenue:get_incoming_traffic_status")->dailyAt('00:15')->timezone('America/Los_Angeles');
        $schedule->exec("php artisan affiliate_revenue:get_top_affiliate")->hourly()->timezone('America/Los_Angeles');
        $schedule->exec("php artisan affiliate_revenue:send_email")->dailyAt('00:30')->timezone('America/Los_Angeles');
        // AFFILIATE REVENUE

        // KENDAGO
        $schedule->exec("php artisan kendago:get_kendago_data")->dailyAt('00:05')->timezone('America/Los_Angeles');
        // KENDAGO

        // KENDAGO ORDER
        $schedule->exec("php artisan kendago:get_kendago_order_data")->dailyAt('00:05')->timezone('America/Los_Angeles');
        // KENDAGO ORDER

        // RFS ORDER
        $schedule->exec("php artisan rfs:get_rfs_order_data")->dailyAt('00:05')->timezone('America/Los_Angeles');
        // RFS ORDER

        // VENDOR ORDER
        $schedule->exec("php artisan vendor_order:get_hop_count")->dailyAt('00:05')->timezone('America/Los_Angeles');
        $schedule->exec("php artisan vendor_order:get_vendor_data")->dailyAt('00:05')->timezone('America/Los_Angeles');
        $schedule->exec("php artisan vendor_order:get_top_affiliate")->dailyAt('00:05')->timezone('America/Los_Angeles');
        // VENDOR ORDER

        // LIST GROWTH
        $schedule->exec("php artisan list_growth:get_list_growth_data")->hourly();
        $schedule->exec("php artisan list_growth:send_email")->dailyAt('14:00');
        // LIST GROWTH

        // SALES BY ARS
        $schedule->exec("php artisan sales_by_ars:get_sales_by_ars_data")->hourly();
        $schedule->exec("php artisan sales_by_tid:send_email")->dailyAt('14:30');
        // SALES BY ARS

        $schedule->exec("php artisan aweber:refresh_token")->hourly();
        $schedule->exec("php artisan log:data_empty")->hourly();
        // $schedule->exec("php artisan log:data_test")->dailyAt('12:15')->timezone('America/Los_Angeles');

        // SEND NEW CUSTOMER LIST
        $schedule->exec("php artisan tay:send_new_customer_list")->monthly('00:05')->timezone('America/Los_Angeles');
        // SEND NEW CUSTOMER LIST
		
		// GET AFFILIATE PERFORMANCE DATA
        $schedule->exec("php artisan affiliate_performance:get_affiliate_vendor_data")->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }

    protected function scheduleTimezone()
    {
        return 'Asia/Singapore';
    }
}
