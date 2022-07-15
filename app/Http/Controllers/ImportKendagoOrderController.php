<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\KendagoOrder;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ImportKendagoOrderController extends Controller
{
    public static function importKendagoOrderData()
    {

        $vendor = '15MANIFEST';
        $affiliate = '101FB2C';
        $orders = array();
        $analytics = array();

        for ($i = 3; $i >= 0; $i--) {
            // $i = 3;
            $date = Helper::getNewDate();
            $end_day = ($i == 0) ? $date->format('d') : $date->format('t');
            $month = $date->format('m');
            $year = $date->format('Y');

            $upsell_sales_amount = 0;
            $initial_sales_count = 0;
            for ($j = 0; $j <= 4; $j++) {
                $amount = 0;
                $upsell_sales_amount = 0;
                $type = Helper::$types[$j];

                // To grab the data from the Orders
                $url = "https://api.clickbank.com/rest/1.3/orders2/list?vendor=$vendor&affiliate=$affiliate&type=$type&startDate=$year-$month-01&endDate=$year-$month-$end_day";
                $api_result = Helper::getAllOrderDataNew($url);
                //print_r($api_result);
                if (count($api_result) > 0) {
                    // $arrOrddata = current($api_result);

                    foreach ($api_result as $apData) {

                        if (isset($apData['lineItemData']['lineItemType'])) {
                            //echo '<br>---Yes----<br>';
                            $orders[$year][$month][] = $apData;
                        } else {
                            // echo '<br>------NO-----<br>';
                            //print_r($apData);
                            foreach ($apData as $arrOrder) {
                                $orders[$year][$month][] = $arrOrder;
                            }
                        }
                    }
                }
            }


            // process the analytics call
            $url1 = "https://api.clickbank.com/rest/1.3/analytics/vendor/affiliate/summary?account=15manifest&dimensionFilter=101fb2c&select=HOP_COUNT&select=SALE_COUNT&summaryType=AFFILIATE_ONLY&startDate=$year-$month-01&endDate=$year-$month-$end_day";
            $api_result1 = Helper::getAllAnalyticsDataNew($url1);
            $analytics['hop_count'][$year][$month] =  0;
            $analytics['sales_count'][$year][$month] = 0;
            foreach ($api_result1 as $a1) {

                if (isset($a1[0]['value']['$']))
                    $analytics['hop_count'][$year][$month] =  $a1[0]['value']['$'];

                if (isset($a1[1]['value']['$']))
                    $analytics['sales_count'][$year][$month] = $a1[1]['value']['$'];
            }
        }

        foreach ($orders as $keyYear => $valYear) {
            foreach ($valYear as $keyMonth => $valMonth) {
                $salesAmount = 0;
                $refundAmount = 0;
                $billAmount = 0;
                $chargebackAmount = 0;
                $feeAmount = 0;
                $upsellAmount = 0;
                $bumpAmount = 0;
                foreach ($valMonth as $keyData => $valData) {

                    // For SALE
                    if (isset($valData['lineItemData']['lineItemType'])) {

                        // for SALE
                        if (@$valData['transactionType'] == 'SALE' && in_array(@$valData['lineItemData']['lineItemType'], array('STANDARD', 'UPSELL', 'BUMP'))) {
                            $salesAmount += @$valData['lineItemData']['accountAmount'];
                        }

                        // for RFND
                        if (@$valData['transactionType'] == 'RFND' && in_array(@$valData['lineItemData']['lineItemType'], array('STANDARD'))) {
                            $refundAmount += @$valData['lineItemData']['accountAmount'];
                        }

                        // for Bill
                        if (@$valData['transactionType'] == 'BILL' && in_array(@$valData['lineItemData']['lineItemType'], array('STANDARD'))) {
                            $billAmount += @$valData['lineItemData']['accountAmount'];
                        }

                        // for CGBK
                        if (@$valData['transactionType'] == 'CGBK' && in_array(@$valData['lineItemData']['lineItemType'], array('STANDARD'))) {
                            $chargebackAmount += @$valData['lineItemData']['accountAmount'];
                        }

                        // for FEE
                        if (@$valData['transactionType'] == 'FEE' && in_array(@$valData['lineItemData']['lineItemType'], array('STANDARD'))) {
                            $feeAmount += @$valData['lineItemData']['accountAmount'];
                        }

                        // for UPSELL Amount
                        if (isset($valData['lineItemData']['lineItemType']) && $valData['lineItemData']['lineItemType'] == 'UPSELL') {
                            $upsellAmount += @$valData['lineItemData']['accountAmount'];
                        }

                        // for BUMP Amount
                        if (isset($valLIdata['lineItemData']['lineItemType']) && $valLIdata['lineItemData']['lineItemType'] == 'BUMP') {
                            $bumpAmount += @$valData['lineItemData']['accountAmount'];
                        }


                        //echo '<br> If SALE AMT: '.$salesAmount += @$valData['lineItemData']['accountAmount'];
                    } else {
                        if (isset($valData['lineItemData'])) {
                            //print_r($valData);
                            foreach ($valData['lineItemData'] as $valLIdata) {

                                // for SALE
                                if (@$valData['transactionType'] == 'SALE' && in_array(@$valLIdata['lineItemType'], array('STANDARD', 'UPSELL', 'BUMP'))) {
                                    $salesAmount += @$valLIdata['accountAmount'];
                                }

                                // for RFND
                                if (@$valData['transactionType'] == 'RFND' && in_array(@$valLIdata['lineItemType'], array('STANDARD'))) {
                                    $refundAmount += @$valLIdata['accountAmount'];
                                }

                                // for Bill
                                if (@$valData['transactionType'] == 'BILL' && in_array(@$valLIdata['lineItemType'], array('STANDARD'))) {
                                    $billAmount += @$valLIdata['accountAmount'];
                                }

                                // for CGBK
                                if (@$valData['transactionType'] == 'CGBK' && (isset($valLIdata['lineItemType']) && $valLIdata['lineItemType'] == 'STANDARD')) {
                                    $chargebackAmount += @$valLIdata['accountAmount'];
                                }

                                // for FEE
                                if (@$valData['transactionType'] == 'FEE' && in_array(@$valLIdata['lineItemType'], array('STANDARD'))) {
                                    $feeAmount += @$valLIdata['accountAmount'];
                                }

                                // for UPSELL Amount
                                if (isset($valLIdata['lineItemType']) && $valLIdata['lineItemType'] == 'UPSELL') {
                                    $upsellAmount += @$valLIdata['accountAmount'];
                                }

                                // for BUMP Amount
                                if (isset($valLIdata['lineItemType']) && $valLIdata['lineItemType'] == 'BUMP') {
                                    $bumpAmount += @$valLIdata['accountAmount'];
                                }
                            }
                        } else {
                            // print_r($valData);
                        }
                    }

                    // echo "<br>$keyMonth - $keyYear Final  Sales Amount".$salesAmount;

                }


                $exist = KendagoOrder::where('month', '=', $keyMonth)->where('year', '=', $keyYear)->first();

                if ($exist == null) {
                    // echo '<br> NULL';
                    KendagoOrder::insert(['month' => $keyMonth, 'year' => $keyYear, 'hop_count' => $analytics['hop_count'][$keyYear][$keyMonth], 'sale' => $salesAmount, 'bill' => $billAmount, 'refund' => abs($refundAmount), 'charge_back' => abs($chargebackAmount), 'fee' => abs($feeAmount), 'upsell_sales_amount' => $upsellAmount, 'bump_sales_amount' => $bumpAmount, 'initial_sales_count' => $analytics['sales_count'][$keyYear][$keyMonth], 'created_at' => Carbon::now()]);
                } else {
                    //echo '<br> Found:'.$exist->id;
                    $id = $exist->id;
                    $rfs_order = KendagoOrder::findOrFail($id);
                    $rfs_order->month = $keyMonth;
                    $rfs_order->year = $keyYear;
                    $rfs_order->hop_count = $analytics['hop_count'][$keyYear][$keyMonth];
                    $rfs_order->sale = $salesAmount;
                    $rfs_order->bill = $billAmount;
                    $rfs_order->refund = abs($refundAmount);
                    $rfs_order->charge_back = abs($chargebackAmount);
                    $rfs_order->fee = abs($feeAmount);
                    $rfs_order->upsell_sales_amount = $upsellAmount;
                    $rfs_order->bump_sales_amount = $bumpAmount;
                    $rfs_order->initial_sales_count = $analytics['sales_count'][$keyYear][$keyMonth];
                    $rfs_order->updated_at = Carbon::now();
                    $rfs_order->save();
                }
            } // keyMonth
        } // foreach
    }
}
