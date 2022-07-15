<?php

namespace App\Console\Commands;

use App\Helper\Helper;
use App\Models\AweberAccounts;
use App\Models\AweberLists;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RefreshToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aweber:refresh_token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh Token for all aweber accounts!';

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
        // REFRESH TOKEN
        AweberLists::truncate();
        $aweber_accounts = AweberAccounts::all();
        foreach ($aweber_accounts as $a) {
            $response = Helper::AjaxRefreshToken($a->refresh_token);
            $body = json_decode($response, true);

            $a->access_token = $body['access_token'];
            $a->refresh_token = $body['refresh_token'];
            $a->updated_at = Carbon::now();
            $a->save();

            $url = 'https://api.aweber.com/1.0/accounts/' . $a->account_id . '/lists';
            $token = $body['access_token'];

            $lists = Helper::AjaxGetResponse('GET', $url, $token);
            // enter lists
            foreach ($lists['entries'] as $index => $list) {
                $l = new AweberLists();
                $l->list_id = $list['id'];
                $l->account_id = $a->account_id;
                $l->name = $list['name'];
                $l->created_at = Carbon::now();
                $l->save();
            }
        }
    }
}
