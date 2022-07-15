<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes(['register' => false, 'reset' => false]);

// HOME
Route::get('/', 'HomeController@index')->name('home');
Route::get('/home', 'HomeController@index')->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
    return view('dashboard');
})->name('dashboard');

// SYSTEM DASHBOARD
// System Tools
Route::get('/system_dashboard', 'SystemDashboardController@index')->name('system_dashboard.index');
Route::get('/aweber/integrate', 'SystemDashboardController@integrate')->name('system_dashboard.integrate');
Route::get('/aweber/integrate-callback', 'SystemDashboardController@integrateCB')->name('system_dashboard.integrateCB');
Route::post('/aweber/integrate-store', 'SystemDashboardController@integrateStore')->name('system_dashboard.integrateStore');
Route::get('/aweber/refresh-token', 'SystemDashboardController@refreshToken')->name('system_dashboard.refreshToken');
Route::get('/screenshot-and-send', 'SystemDashboardController@screenShotAndSend')->name('system_dashboard.screenShotAndSend');
Route::get('/get-product-api', 'SystemDashboardController@getProductAPI')->name('system_dashboard.getProductAPI');
Route::get('/browsershot-test', 'SystemDashboardController@browserShotTest')->name('system_dashboard.browserShotTest');
// Route::get('/read-csv-and-process', 'SystemDashboardController@readCSVAndProcess')->name('system_dashboard.readCSVAndProcess');

// Reporting Tools
Route::get('/send-list-growth-report', 'SystemDashboardController@sendListGrowthReport')->name('system_dashboard.sendListGrowthReport');
Route::get('/send-sales-by-tid-report', 'SystemDashboardController@sendSalesByTIDReport')->name('system_dashboard.sendSalesByTIDReport');
Route::get('/send-affiliate-revenue-report', 'SystemDashboardController@sendAffiliateRevenueReport')->name('system_dashboard.sendAffiliateRevenueReport');
Route::get('/send-new-customers-list-report', 'SystemDashboardController@sendNewCustomersListData')->name('system_dashboard.sendNewCustomersList');

// Import Data Tools
Route::get('/aweber/get-list-growth-data', 'SystemDashboardController@getListGrowthData')->name('system_dashboard.getListGrowthData');
Route::get('get-affiliate-revenue-data', 'SystemDashboardController@getAffiliateRevenueData')->name('system_dashboard.getAffiliateRevenueData');
Route::get('/get-sales-by-ars-data', 'SystemDashboardController@getSalesByARSData')->name('system_dashboard.getSalesByARSData');
Route::get('/get-sales-ranking-data', 'SystemDashboardController@getSalesRankingData')->name('system_dashboard.getSalesRankingData');
Route::get('/get-incoming-traffic-status-data', 'SystemDashboardController@getIncomingTrafficStatusData')->name('system_dashboard.getIncomingTrafficStatusData');
Route::get('/get-top-affiliate-data', 'SystemDashboardController@getTopAffiliateData')->name('system_dashboard.getTopAffiliateData');
Route::get('/get-kendago-data', 'SystemDashboardController@getKendagoData')->name('system_dashboard.getKendagoData');
Route::get('/get-kendago-order-data', 'SystemDashboardController@getKendagoOrderData')->name('system_dashboard.getKendagoOrderData');
Route::get('/get-rfs-order-data', 'SystemDashboardController@getRfsOrderData')->name('system_dashboard.getRfsOrderData');
Route::get('/get-vendor-order-data', 'SystemDashboardController@getVendorOrderData')->name('system_dashboard.getVendorOrderData');
Route::get('/get-vendor-hopcount-data', 'SystemDashboardController@getVendorHopcountData')->name('system_dashboard.getVendorHopcountData');
Route::get('/get-vendor-top-affiliate-data', 'SystemDashboardController@getVendorTopAffiliateData')->name('system_dashboard.getVendorTopAffiliateData');
Route::get('/get-newly-add-vendor-order-data', 'SystemDashboardController@getNewlyAddVendorOrderData')->name('system_dashboard.getNewlyAddVendorOrderData');
Route::get('/get-newly-add-hopcount-data', 'SystemDashboardController@getNewlyAddVendorHopCountData')->name('system_dashboard.getNewlyAddVendorHopCountData');
Route::get('/get-analytics-data', 'SystemDashboardController@getAnalyticsData')->name('system_dashboard.getAnalyticsData');

Route::get('/create-storage-link', function () {
    Artisan::call("storage:link");
});

// SALES BY ARS
Route::get('/affiliate_revenue/{view?}', 'AffiliateRevenueController@index')->name('affiliate_revenue.index');

// LIST GROWTH
Route::get('/list_growth', 'ListGrowthController@index')->name('list_growth.index');

// SALES BY ARS
Route::get('/ar_sales', 'SalesByARSController@index')->name('sales_by_ars.index');

// KENDAGO
Route::get('/kendago', 'KendagoController@index')->name('kendago.index');

// KENDAGO ORDER
Route::get('/kendago-order/{view?}', 'KendagoOrderController@index')->name('kendago-order.index');

// RFS ORDER
Route::get('/rfs-order/{view?}', 'RfsOrderController@index')->name('rfs-order.index');

// VENDOR ORDER
Route::get('/vendor-order/{view?}/{vendor?}/{affiliate?}', 'VendorOrderController@index')->name('vendor-order.index');

// AFFILIATE PERFORMANCE
Route::get('/affiliate-performance/{view?}/{affiliate?}/{vendor?}', 'AffiliatePerformanceController@index')->name('affiliate-performance.index');
Route::post('/affiliate-performance/gettopvendor','AffiliatePerformanceController@ajaxGetTopVendor');

// SPLIT TEST
Route::get('/split-test/{account?}', 'SplitTestController@index')->name('split-test.index');

// USERS
Route::resource('user_roles', 'UserRoleController');
Route::resource('users', 'UserController');

Route::post('user/activate/{id}', [
    'uses' => 'UserController@userActivate',
    'as' => 'user.userActivate'
]);

Route::post('user/suspend/{id}', [
    'uses' => 'UserController@userSuspend',
    'as' => 'user.userSuspend'
]);

// for CB Masterlist
Route::get('/cb-master', 'CbMasterListController@index')->name('cbmaster');
Route::post('/cb-master', 'CbMasterListController@index')->name('cbmaster');
Route::get('/cb-master/create_affiliate', 'CbMasterListController@create_affiliate')->name('cbmaster.create_affiliate');
Route::post('/cb-master/create_affiliate', 'CbMasterListController@create_affiliate')->name('cbmaster.create_affiliate');
Route::get('/cb-master/create_partner', 'CbMasterListController@create_partner')->name('cbmaster.create_partner');
Route::post('/cb-master/create_partner', 'CbMasterListController@create_partner')->name('cbmaster.create_partner');
//Route::post('/cb-master', 'CbMasterListController@store');
Route::get('/cb-master/delete/{id}', 'CbMasterListController@destroy')->name('cbmaster.destroy');
Route::get('/cb-master/deleteaaffiliate/{id}', 'CbMasterListController@deleteaaffiliate')->name('cbmaster.deleteaaffiliate');
Route::get('/cb-master/deleteapartner/{id}', 'CbMasterListController@deleteapartner')->name('cbmaster.deleteapartner');
