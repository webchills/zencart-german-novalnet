<?php
/**
 * Novalnet payment module
 * This script is used for helper function of post process of
 * Novalnet payment orders
 *
 * @author     Novalnet AG
 * @copyright  Copyright (c) Novalnet
 * @license    https://www.novalnet.de/payment-plugins/kostenlos/lizenz
 * @link       https://www.novalnet.de
 *
 * Script : novalnet_extension_helper.php
 *
 */
require('includes/application_top.php');
include_once(DIR_FS_CATALOG . DIR_WS_MODULES.'payment/novalnet/NovalnetHelper.php');
include_once(DIR_FS_CATALOG . DIR_WS_LANGUAGES. $_SESSION['language']."/modules/payment/novalnet_payments.php");
include_once DIR_FS_CATALOG . DIR_WS_CLASSES . 'order.php';

global $messageStack,  $db;
require(DIR_WS_CLASSES . 'currencies.php');
$currencies = new currencies();

$request        = $_REQUEST;

$txn_details    = NovalnetHelper::getNovalnetTransDetails($request['oID']);
if ($txn_details->RecordCount()) {
    $payment_details = '{}';
    $payment_details = !empty($txn_details->fields['payment_details']) ? json_decode($txn_details->fields['payment_details'], true) : [];
    $order = new order($_REQUEST['oID']);
    $current_order_status = $db->Execute("SELECT orders_status from " . TABLE_ORDERS . " where orders_id = " . zen_db_input($request['oID']));
    if (!empty($request['nn_book_confirm']) && !empty($request['book_amount'])) {   // Zero amount booking transaction process
        $params =[];
        NovalnetHelper::buildRequestParams($params);
        $params['transaction']['payment_type'] = $txn_details->fields['payment_type'];
        $params['transaction']['amount'] = $request['book_amount'];
        $params['transaction']['payment_data']['token'] = $payment_details['token'];
        $params['custom']['shop_invoked'] = 1;
        $response = NovalnetHelper::sendRequest($params, NovalnetHelper::getActionEndpoint('payment'));
        if ($response['result']['status'] == 'SUCCESS') {
            $order_status_value = $db->Execute("SELECT orders_status from " . TABLE_ORDERS . " where orders_id = " . zen_db_input($request['oID']));
            $message =  PHP_EOL .PHP_EOL. sprintf(MODULE_PAYMENT_NOVALNET_TRANS_BOOKED_MESSAGE, $currencies->format(($request['book_amount'] / 100), 1, $response['transaction']['currency']), $response['transaction']['tid']) . PHP_EOL;
            $update_data = [
                    'amount' => $response['transaction']['amount'],
                    'tid'    => $response['transaction']['tid'],
                ];
            if (!empty($request['oID'])) {
                zen_db_perform(TABLE_NOVALNET_TRANSACTION_DETAIL, $update_data, 'update', 'order_no='.$request['oID']);
            }
            NovalnetHelper::novalnetUpdateOrderStatus($request['oID'], $message, $current_order_status->fields['orders_status']);
            $messageStack->add_session($response['result']['status_text'], 'success');
        } else {
            $messageStack->add_session($response['result']['status_text'], 'error');
        }
        zen_redirect(zen_href_link(FILENAME_ORDERS, zen_get_all_get_params(['action']) . 'action=edit' . '&oID=' . (int)$request['oID']));
    } elseif (!empty($request['nn_instacancel_allcycles']) || !empty($request['nn_instacancel_remaincycles'])) { // Instalment cancel process
        $data = [
        'instalment' => [
            'tid' => $txn_details->fields['tid'],
            'cancel_type' => isset($request['nn_instacancel_allcycles']) ? 'CANCEL_ALL_CYCLES' : 'CANCEL_REMAINING_CYCLES'
        ],
        'custom' => [
            'lang' => (isset($_SESSION['languages_code'])) ? strtoupper($_SESSION['languages_code']) : 'DE',
            'shop_invoked' => 1
        ]
        ];

        $response = NovalnetHelper::sendRequest($data, NovalnetHelper::getActionEndpoint('instalment_cancel'));
        if ($response['result']['status'] == 'SUCCESS') {
            if (!empty($request['nn_instacancel_remaincycles'])) {
                $instalment_details = !empty($txn_details->fields['instalment_cycle_details']) ? json_decode($txn_details->fields['instalment_cycle_details'], true) : [];
                if (!empty($instalment_details)) {
                    foreach ($instalment_details as $key => $instalment_details_data) {
                        if (empty($instalment_details_data['reference_tid']) && ($instalment_details_data['status'] == 'Pending')) {
                            $instalment_details[$key]['status'] = 'Canceled';
                        }
                    }
                    $update_data = [
                    'instalment_cycle_details' => !empty($instalment_details) ? json_encode($instalment_details) : '{}',
                    ];
                }
                $update_data['status'] = 'CONFIRMED';
                $message = PHP_EOL. sprintf((MODULE_PAYMENT_NOVALNET_INSTALMENT_CANCEL_REMAINING_CYCLES_TEXT), $txn_details->fields['tid'], date('Y-m-d H:i:s'));
            } elseif (!empty($request['nn_instacancel_allcycles'])) {
                $instalment_details = !empty($txn_details->fields['instalment_cycle_details']) ? json_decode($txn_details->fields['instalment_cycle_details'], true) : [];
                if (!empty($instalment_details)) {
                    foreach ($instalment_details as $cycle => $cycle_details) {
                        $refunded_amount = $response['transaction']['refund']['amount'];
                        if (!empty($cycle_details['reference_tid']) && ($cycle_details['status'] == 'Paid')) {
                            $instalment_details[$cycle]['status'] = 'Refunded';
                        }
                        if ($cycle_details['status'] == 'Pending') {
                            $instalment_details[$cycle]['status'] = 'Canceled';
                        }
                        if (!empty($cycle_details['reference_tid']) && (($cycle_details['reference_tid'] == $txn_details->fields['tid']) || ($cycle_details['reference_tid'] != $txn_details->fields['tid']))) {
                            $instalment_amount = (strpos((string)$instalment_details[$cycle]['instalment_cycle_amount'], '.')) ? $instalment_details[$cycle]['instalment_cycle_amount']*100 :$instalment_details[$cycle]['instalment_cycle_amount'];
                            $instalment_amount = $instalment_amount - $refunded_amount;
                            $instalment_details[$cycle]['instalment_cycle_amount'] = $instalment_amount;
                        }
                    }
                    $update_data = [
                    'instalment_cycle_details' => !empty($instalment_details) ? json_encode($instalment_details) : '{}',
                    'refund_amount' => (!empty($txn_details->fields['refund_amount'])) ? ($refunded_amount + $txn_details->fields['refund_amount']) : $refunded_amount,
                    ];
                }
                $update_data['status'] = 'DEACTIVATED';
                $message = PHP_EOL. sprintf((MODULE_PAYMENT_NOVALNET_INSTALMENT_CANCEL_ALLCYCLES_TEXT), $txn_details->fields['tid'], date('Y-m-d H:i:s'), $currencies->format(($response['transaction']['refund']['amount']/100), 1, $txn_details->fields['currency']));
            }

            if (!empty($request['oID'])) {
                zen_db_perform(TABLE_NOVALNET_TRANSACTION_DETAIL, $update_data, 'update', 'order_no='.$request['oID']);
            }
            $order_status = !empty($request['nn_instacancel_allcycles']) ? NovalnetHelper::getOrderStatusId() : $current_order_status->fields['orders_status'];
            NovalnetHelper::novalnetUpdateOrderStatus($request['oID'], $message, $order_status);
            $messageStack->add_session($response['result']['status_text'], 'success');
        } else {
            $messageStack->add_session($response['result']['status_text'], 'error');
        }
        zen_redirect(zen_href_link(FILENAME_ORDERS, zen_get_all_get_params(['action']) . 'action=edit' . '&oID=' . (int)$request['oID']));
    } elseif ((!empty($request['nn_refund_confirm']) && ($request['refund_trans_amount'] != '' ) && $txn_details->fields['status'] != 'Canceled')) { // To process refund process
        $refunded_amount = 0;
        $data['transaction'] = [
            'tid'    => (!empty($request['refund_tid'])) ? $request['refund_tid'] : $txn_details->fields['tid'],
            'amount' => $request['refund_trans_amount'],
        ];
        $data['custom'] = [
            'lang' => (isset($_SESSION['languages_code'])) ? strtoupper($_SESSION['languages_code']) : 'DE',
            'shop_invoked' => 1
        ];
        if (!empty($request['refund_reason'])) {
            $data['transaction']['reason'] = $request['refund_reason'];
        }
        $response = NovalnetHelper::sendRequest($data, NovalnetHelper::getActionEndpoint('transaction_refund'));
        if ($response['result']['status'] == 'SUCCESS') {
            $refunded_amount = $response['transaction']['refund']['amount'];
            if (in_array($response['transaction']['payment_type'], array('INSTALMENT_INVOICE','INSTALMENT_DIRECT_DEBIT_SEPA'))) {
                $instalment_details = (!empty($txn_details->fields['instalment_cycle_details'])) ? json_decode($txn_details->fields['instalment_cycle_details'], true) : unserialize($txn_details->fields['payment_details']);
                if (!empty($instalment_details)) {
                    $cycle = $request['instalment_cycle'];
                    $instalment_amount = (strpos((string)$instalment_details[$cycle]['instalment_cycle_amount'], '.')) ? $instalment_details[$cycle]['instalment_cycle_amount']*100 : $instalment_details[$cycle]['instalment_cycle_amount'];
                    $instalment_amount = $instalment_amount - $refunded_amount;
                    $instalment_details[$cycle]['instalment_cycle_amount'] = $instalment_amount;
                    if ($instalment_details[$cycle]['instalment_cycle_amount'] <= 0) {
                        $instalment_details[$cycle]['status'] = 'Refunded';
                    }
                    $update_data = [
                        'instalment_cycle_details' => json_encode($instalment_details),
                    ];
                }
            }
            $update_data['refund_amount'] = (!empty($txn_details->fields['refund_amount'])) ? ($refunded_amount + $txn_details->fields['refund_amount']) : $refunded_amount;
            $message = PHP_EOL. sprintf((MODULE_PAYMENT_NOVALNET_REFUND_PARENT_TID_MSG), $txn_details->fields['tid'], $currencies->format(($refunded_amount/100), 1, $txn_details->fields['currency']));
            // Check for refund TID
            if (!empty($response['transaction']['refund']['tid'])) {
                $message .= PHP_EOL. sprintf((MODULE_PAYMENT_NOVALNET_REFUND_CHILD_TID_MSG), $response['transaction']['refund']['tid']);
            }
            if (!empty($request['oID'])) {
                zen_db_perform(TABLE_NOVALNET_TRANSACTION_DETAIL, $update_data, 'update', 'order_no='.$request['oID']);
            }
            $order_status_value = ($update_data['refund_amount'] >= $txn_details->fields['amount']) ? NovalnetHelper::getOrderStatusId() : $current_order_status->fields['orders_status'];

            NovalnetHelper::novalnetUpdateOrderStatus($request['oID'], $message, $order_status_value);
            $messageStack->add_session($response['result']['status_text'], 'success');
        } else {
            $messageStack->add_session($response['result']['status_text'], 'error');
        }
        zen_redirect(zen_href_link(FILENAME_ORDERS, zen_get_all_get_params(['action']) . 'action=edit' . '&oID=' . (int)$request['oID']));
    } else {
        zen_redirect(zen_href_link(FILENAME_ORDERS, zen_get_all_get_params(['action']) . 'action=edit' . '&oID=' . (int)$request['oID']));
    }
}
