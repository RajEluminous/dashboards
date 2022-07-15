<?php

namespace App\Http\Controllers;

use App\Http\Controllers\SendCustomerNameController;
use App\Http\Controllers\ImportKendagoOrderController;
use App\Http\Controllers\AffiliatePerformanceController;
use App\Models\AweberAccounts;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Helper\Helper;
use App\Models\AffiliateRevenue;
use App\Models\AweberLists;
use App\Models\KendagoOrder;
use App\Models\ListGrowth;
use App\Models\TopAffiliate;
use App\Models\VendorTopAffiliate;
use Exception;
use League\Csv\Reader;
use League\Csv\Statement;
use Spatie\Browsershot\Browsershot;

class SystemDashboardController extends Controller
{
    public function index()
    {
        return view('system_dashboard.index');
    }

    public function integrate()
    {
        //redirect to verify page
        $scopes = array(
            'account.read',
            'list.read',
            'list.write',
            'subscriber.read',
            'subscriber.write',
            'email.read',
            'email.write',
            'subscriber.read-extended'
        );

        if (env('APP_ENV') != 'local') {
            $clientId = env('AWEBER_PRODUCTION_CLIENT_ID');
            $clientSecret = env('AWEBER_PRODUCTION_CLIENT_SECRET');
        } else {
            $clientId = env('AWEBER_CLIENT_ID');
            $clientSecret = env('AWEBER_CLIENT_SECRET');
        }

        $redirectUrl = redirect()->route('system_dashboard.integrateCB');

        $provider = new \League\OAuth2\Client\Provider\GenericProvider([
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
            'redirectUri' => $redirectUrl,
            'scopes' => $scopes,
            'scopeSeparator' => ' ',
            'urlAuthorize' => 'https://auth.aweber.com/oauth2/authorize',
            'urlAccessToken' => 'https://auth.aweber.com/oauth2/token',
            'urlResourceOwnerDetails' => 'https://api.aweber.com/1.0/accounts'
        ]);

        // dd($provider);
        // If we don't have an authorization code then get one
        if (!isset($provider->getAuthorizationUrl)) {
            $authorizationUrl = $provider->getAuthorizationUrl();
            $_SESSION['oauth2state'] = $provider->getState();
            return redirect($authorizationUrl);

            // Check given state against previously stored one to mitigate CSRF attack
        } elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
            unset($_SESSION['oauth2state']);
            exit('Invalid state');
        }
    }

    public function integrateCB()
    {
        $code = $_GET['code'];
        return view('system_dashboard.integrate', ['code' => $code]);
    }

    public function integrateStore(Request $request)
    {
        $request->validate(['account_name' => 'required', 'code' => 'required']);

        $error = 0;
        $errors = [];

        $exist_account_name = AweberAccounts::where('account_name', '=', $request->account)->first();
        if ($exist_account_name) {
            $error = 1;
            $errors[] = 'Account name exist!';
        }

        if ($error == 1) {
            $error = ValidationException::withMessages($errors);
            throw $error;
        } else {

            if (env("APP_ENV") != 'local') {
                $clientId = env("AWEBER_PRODUCTION_CLIENT_ID");
                $clientSecret = env('AWEBER_PRODUCTION_CLIENT_SECRET');
            } else {
                // LIVE
                $clientId = env("AWEBER_CLIENT_ID");
                $clientSecret = env('AWEBER_CLIENT_SECRET');
            }

            // START GET ACCESS TOKEN
            try {

                $provider = new \League\OAuth2\Client\Provider\GenericProvider([
                    'clientId' => $clientId,
                    'clientSecret' => $clientSecret,
                    'urlAuthorize' => 'https://auth.aweber.com/oauth2/authorize',
                    'urlAccessToken' => 'https://auth.aweber.com/oauth2/token',
                    'urlResourceOwnerDetails' => 'https://api.aweber.com/1.0/accounts'
                ]);

                // Try to get an access token using the authorization code grant.
                $code = $request->code;

                $accessToken = $provider->getAccessToken('authorization_code', [
                    'code' => $code
                ]);

                $refreshToken = $accessToken->getRefreshToken();

                $resourceOwner = $provider->getResourceOwner($accessToken);
                $arrOwner = $resourceOwner->toArray();

                $a = new AweberAccounts();
                $a->account_name = $request->account_name;
                $a->account_id = $arrOwner['entries'][0]['id'];
                $a->access_token = $accessToken;
                $a->refresh_token = $refreshToken;
                $a->status = 1;
                $a->created_at = Carbon::now();
                $a->save();

                $id = $a->id;

                Helper::insertAllAccountLists($arrOwner, $accessToken);

                return redirect()->route('system_dashboard.index');
            } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
                echo $e->getMessage();
            }
            // END GET ACCESS TOKEN
        }
    }

    public function refreshToken()
    {
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

        return redirect()->route('system_dashboard.index');
    }

    public function getListGrowthData()
    {
        $data = [];
        try {
            $data['growth'][] = Helper::GetGrowthResponse("15manifest_leads", "1071969", "1");
            $data['growth'][] = Helper::GetGrowthResponse("15_manifest_customers", "1071969", "2");
            $data['growth'][] = Helper::GetGrowthResponse("qmanifest_leads", "1011193", "1");
            $data['growth'][] = Helper::GetGrowthResponse("qmanifest_customers", "1011193", "2");
            $data['growth'][] = Helper::GetGrowthResponse("amazeyou_leads", "1022666", "1");
            $data['growth'][] = Helper::GetGrowthResponse("amazeyou_customers", "1022666", "2");
            $data['growth'][] = Helper::GetGrowthResponse("wactivator_leads", "1267038", "1");
            $data['growth'][] = Helper::GetGrowthResponse("wactivator_customers", "1267038", "2");
            $data['growth'][] = Helper::GetGrowthResponse("15weight_leads", "1374619", "1");
            $data['growth'][] = Helper::GetGrowthResponse("15weight_customers", "1374619", "2");
            $data['growth'][] = Helper::GetGrowthResponse("millionb_leads", "1023113", "1");
            $data['growth'][] = Helper::GetGrowthResponse("millionb_customers", "1023113", "2");
            $data['growth'][] = Helper::GetGrowthResponse("ancientsec_leads", "930132", "1");
            $data['growth'][] = Helper::GetGrowthResponse("ancientsec_customers", "930132", "2");
            $data['growth'][] = Helper::GetGrowthResponse("15happy_leads", "1515534", "1");
            $data['growth'][] = Helper::GetGrowthResponse("15happy_customers", "1515534", "2");
            $data['growth'][] = Helper::GetGrowthResponse("godfreq_leads", "1425050", "1");
            $data['growth'][] = Helper::GetGrowthResponse("godfreq_customers", "1425050", "2");

            $data['size'][] = Helper::GetSizeResponse("15manifest_leads", "1071969", "1");
            $data['size'][] = Helper::GetSizeResponse("15_manifest_customers", "1071969", "2");
            $data['size'][] = Helper::GetSizeResponse("qmanifest_leads", "1011193", "1");
            $data['size'][] = Helper::GetSizeResponse("qmanifest_customers", "1011193", "2");
            $data['size'][] = Helper::GetSizeResponse("amazeyou_leads", "1022666", "1");
            $data['size'][] = Helper::GetSizeResponse("amazeyou_customers", "1022666", "2");
            $data['size'][] = Helper::GetSizeResponse("wactivator_leads", "1267038", "1");
            $data['size'][] = Helper::GetSizeResponse("wactivator_customers", "1267038", "2");
            $data['size'][] = Helper::GetSizeResponse("15weight_leads", "1374619", "1");
            $data['size'][] = Helper::GetSizeResponse("15weight_customers", "1374619", "2");
            $data['size'][] = Helper::GetSizeResponse("millionb_leads", "1023113", "1");
            $data['size'][] = Helper::GetSizeResponse("millionb_customers", "1023113", "2");
            $data['size'][] = Helper::GetSizeResponse("ancientsec_leads", "930132", "1");
            $data['size'][] = Helper::GetSizeResponse("ancientsec_customers", "930132", "2");
            $data['size'][] = Helper::GetSizeResponse("15happy_leads", "1515534", "1");
            $data['size'][] = Helper::GetSizeResponse("15happy_customers", "1515534", "2");
            $data['size'][] = Helper::GetSizeResponse("godfreq_leads", "1425050", "1");
            $data['size'][] = Helper::GetSizeResponse("godfreq_customers", "1425050", "2");

            $data['click'][] = Helper::GetClickResponse("15manifest_leads", "1071969", "1");
            $data['click'][] = Helper::GetClickResponse("15_manifest_customers", "1071969", "2");
            $data['click'][] = Helper::GetClickResponse("qmanifest_leads", "1011193", "1");
            $data['click'][] = Helper::GetClickResponse("qmanifest_customers", "1011193", "2");
            $data['click'][] = Helper::GetClickResponse("amazeyou_leads", "1022666", "1");
            $data['click'][] = Helper::GetClickResponse("amazeyou_customers", "1022666", "2");
            $data['click'][] = Helper::GetClickResponse("wactivator_leads", "1267038", "1");
            $data['click'][] = Helper::GetClickResponse("wactivator_customers", "1267038", "2");
            $data['click'][] = Helper::GetClickResponse("15weight_leads", "1374619", "1");
            $data['click'][] = Helper::GetClickResponse("15weight_customers", "1374619", "2");
            $data['click'][] = Helper::GetClickResponse("millionb_leads", "1023113", "1");
            $data['click'][] = Helper::GetClickResponse("millionb_customers", "1023113", "2");
            $data['click'][] = Helper::GetClickResponse("ancientsec_leads", "930132", "1");
            $data['click'][] = Helper::GetClickResponse("ancientsec_customers", "930132", "2");
            $data['click'][] = Helper::GetClickResponse("15happy_leads", "1515534", "1");
            $data['click'][] = Helper::GetClickResponse("15happy_customers", "1515534", "2");
            $data['click'][] = Helper::GetClickResponse("godfreq_leads", "1425050", "1");
            $data['click'][] = Helper::GetClickResponse("godfreq_customers", "1425050", "2");
        } catch (Exception $e) {
            sleep(62);
        }


        // INSERT INTO DATABASE
        foreach ($data as $unique_data) {
            foreach ($unique_data as $d) {
                $account_id = $d['account_id'];
                $types = $d['types'];
                $from = $d['from'];
                $row = 1;

                foreach ($d['times'] as $time_label => $value) {
                    $get_account = ListGrowth::where('account_id', '=', $account_id)->where('types', '=', $types)->where('from', '=', $from)->where('row', '=', $row)->get();
                    if ($get_account->count() == 0) {
                        $lg = new ListGrowth();
                        $lg->account_id = $account_id;
                        $lg->types = $types;
                        $lg->from = $from;
                        $lg->row = $row;
                        $lg->time_label = $time_label;
                        $lg->value = str_replace(',', '', $value);
                        $lg->save();
                    } else {
                        ListGrowth::where('account_id', '=', $account_id)->where('types', '=', $types)->where('from', '=', $from)->where('row', '=', $row)->update(['value' => str_replace(',', '', $value)]);
                    }

                    $row++;
                }
            }
        }
    }

    public function readCSVAndProcess()
    {
        if (!ini_get("auto_detect_line_endings")) {
            ini_set("auto_detect_line_endings", '1');
        }

        // $reader = Reader::createFromPath(public_path() . '/15manifest_2019.csv', 'r');
        // $records = Statement::create()->process($reader);
        // foreach ($records as $r) {
        //     dd($r);
        // }
        // dd(count($records));
        // $account_id = '1023113';
        // $list_id = '5244564';
        // $token = Helper::GetToken($account_id);

        // $file_path = public_path() . '/email_lists.xlsx';
        // $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file_path);
        // $sheet = $spreadsheet->getSheet(0);
        // $total_data = $sheet->getHighestRow();

        // for ($i = 1; $i <= 2; $i++) {
        //     $cell = $sheet->getCell("A$i");
        //     if ($i != 1) {
        //         $client = new Client();
        //         $url = 'https://api.aweber.com/1.0/accounts/' . $account_id . '/lists/' . $list_id . '/subscribers';
        //         $response = $client->request('POST', $url, [
        //             'headers' => [
        //                 'Accept' => 'application/json',
        //                 'Authorization' => 'Bearer ' . $token
        //             ],
        //             'form_params' => [
        //                 'email' => $cell->getValue()
        //             ]
        //         ]);
        //     }
        // }


    }

    public function getSalesByARSData()
    {
        // SalesByARS::truncate();
        $account_list = ['15manifest', 'wactivator', 'amazeyou', 'godfreq'];
        foreach ($account_list as $account) {
            if ($account != '15manifest') {
                Helper::getCBSale('-7 day', $account, 'free', '2');
                Helper::getCBSale('-7 day', $account, 'cust', '1');

                Helper::getCBSale('-60 day', $account, 'free', '2');
                Helper::getCBSale('-60 day', $account, 'cust', '1');

                Helper::getCBSale('-90 day', $account, 'free', '2');
                Helper::getCBSale('-90 day', $account, 'cust', '1');
            } else {
                Helper::getCBSale('-7 day', $account, '15manifest_world_pp', '3');
                Helper::getCBSale('-7 day', $account, 'digimani', '4');
                Helper::getCBSale('-7 day', $account, '15manifest_mw', '5');
                Helper::getCBSale('-7 day', $account, '15manifest_me', '6');
                Helper::getCBSale('-7 day', $account, '15manifest_quiz', '7');

                Helper::getCBSale('-60 day', $account, '15manifest_world_pp', '3');
                Helper::getCBSale('-60 day', $account, 'digimani', '4');
                Helper::getCBSale('-60 day', $account, '15manifest_mw', '5');
                Helper::getCBSale('-60 day', $account, '15manifest_me', '6');
                Helper::getCBSale('-60 day', $account, '15manifest_quiz', '7');


                Helper::getCBSale('-90 day', $account, '15manifest_world_pp', '3');
                Helper::getCBSale('-90 day', $account, 'digimani', '4');
                Helper::getCBSale('-90 day', $account, '15manifest_mw', '5');
                Helper::getCBSale('-90 day', $account, '15manifest_me', '6');
                Helper::getCBSale('-90 day', $account, '15manifest_quiz', '7');
            }
        }
    }

    public function getAffiliateRevenueData()
    {
        Helper::importAffiliateRevenueData();
    }

    public function getSalesRankingData()
    {
        $helper = new Helper();
        $helper::insertSalesRanking($helper::getNewDate(), 'current_month');
        $helper::insertSalesRanking($helper::getNewDate()->modify('-1 month'), 'last_month');
    }

    public function getIncomingTrafficStatusData()
    {
        $helper = new Helper();
        // $helper::insertIncomingTrafficStatus($helper::getNewDate()->modify('-1 day'), 'current_month');
        $helper::insertIncomingTrafficStatus($helper::getNewDate()->modify('-1 month'), 'last_month');
    }

    public function sendNewCustomersListData()
    {
        SendCustomerNameController::sendEmail();
    }

    public function getAnalyticsData()
    {
        AffiliatePerformanceController::getAffiliateVendorData();
    }
    public function getTopAffiliateData()
    {
        TopAffiliate::truncate();
        Helper::topAffiliate('1 day', 'today', '1');
        Helper::topAffiliate('-1 day', 'yesterday', '2');
        Helper::topAffiliate('-7 day', 'last7day', '3');
        Helper::topAffiliate('-30 day', 'last30day', '4');
    }

    public function screenshotAndSend()
    {
        set_time_limit(-1);

        Helper::generateAffiliateRevenuePDF();
        Helper::generateListGrowthPDF();
        Helper::generateSalesByTIDPDF();
    }

    public function getProductAPI()
    {
        $url = "https://api.clickbank.com/rest/1.3/products/list?site=15manifest";
        $result = curl_init();
        curl_setopt($result, CURLOPT_URL, $url);
        curl_setopt($result, CURLOPT_HEADER, false);
        curl_setopt($result, CURLOPT_HTTPGET, false);
        curl_setopt($result, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($result, CURLOPT_TIMEOUT, 360);
        curl_setopt($result, CURLOPT_HTTPHEADER, array("Accept: application/json", "Authorization: DEV-PMP8PL7CANEMMUNLBS940FCGM7OLDEP4:API-080K3VBKB1BKM5982RGEF1CAMFMNKJID"));

        $api_result = json_decode(curl_exec($result), true);
        echo $url;
        dd($api_result);
    }

    public function getKendagoData()
    {
        Helper::importKendagoData();
    }

    public function sendListGrowthReport()
    {
        Helper::generateListGrowthPDF();
    }

    public function sendSalesByTIDReport()
    {
        Helper::generateSalesByTIDPDF();
    }

    public function sendAffiliateRevenueReport()
    {
        Helper::generateAffiliateRevenuePDF();
    }

    public function browserShotTest()
    {
        Browsershot::url('https://www.google.com')
            ->format('A4')
            ->savePdf('storage/csv/test.pdf');
        return "Done!";
    }

    public function getKendagoOrderData()
    {
        ImportKendagoOrderController::importKendagoOrderData();
    }

    public function getRfsOrderData()
    {
        Helper::importRfsOrderData();
    }

    public function getVendorOrderData()
    {
        Helper::importVendorOrderData();
    }

    public function getVendorHopcountData()
    {
        Helper::importVendorHopcountData();
    }

    public function getVendorTopAffiliateData()
    {
        VendorTopAffiliate::truncate();
        foreach (Helper::$vendorOrderAccount as $account) {
            Helper::importVendorTopAffiliateData($account, '-1 day', 'yesterday', '1');
            Helper::importVendorTopAffiliateData($account, '-7 day', 'last7day', '2');
            Helper::importVendorTopAffiliateData($account, '-30 day', 'last30day', '3');
            Helper::importVendorTopAffiliateData($account, '-120 day', 'last120day', '4');
        }
    }

    public function getNewlyAddVendorOrderData()
    {
        Helper::importNewlyAddVendorOrderData();
    }

    public function getNewlyAddVendorHopCountData()
    {
        Helper::importNewlyAddVendorHopcountData();
    }
}
