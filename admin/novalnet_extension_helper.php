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
require ('includes/application_top.php');
include_once(DIR_FS_CATALOG . DIR_WS_MODULES.'payment/novalnet/NovalnetHelper.class.php');
include_once(DIR_FS_CATALOG . DIR_WS_LANGUAGES. $_SESSION['language']."/modules/payment/novalnet_payments.php");
include_once DIR_FS_CATALOG . DIR_WS_CLASSES . 'order.php';

global $messageStack,  $db;
require(DIR_WS_CLASSES . 'currencies.php');
$currencies = new currencies();

$request        = $_REQUEST;

$txn_details    = NovalnetHelper::getNovalnetTransDetails($request['oID']);
if ($txn_details->RecordCount()) {
	if (!empty($txn_details->fields['payment_details'])) {
		$payment_details = json_decode($txn_details->fields['payment_details'], true);
	}
	$order = new order($_REQUEST['oID']);
	$current_order_status = $db->Execute("SELECT orders_status from " . TABLE_ORDERS . " where orders_id = " . zen_db_input($request['oID']));
	//  On-hold transaction prcoess
	if (isset($request['nn_manage_confirm']) && !empty($request['trans_status'])) {
		$order_status    = '';
		$comments = '';
		$data['transaction'] = [
			'tid' => $txn_details->fields['tid']
		];
		$data['custom'] = [
			'lang' => (isset($_SESSION['languages_code'])) ? strtoupper($_SESSION['languages_code']) : 'DE',
		];
		$endpoint = (!empty($request['trans_status']) && $request['trans_status'] == 'CONFIRM') ? 'transaction_capture' : 'transaction_cancel';
		$response = NovalnetHelper::sendRequest($data, NovalnetHelper::getActionEndpoint($endpoint));
		$update_data = [
			'status' => $response['transaction']['status'],
		];
		if ($response['result']['status'] == 'SUCCESS') { // Success
		$order_status = NovalnetHelper::getOrderStatus ($update_data['status'], $response['transaction']['payment_type']);
		$order_status = ($request['trans_status'] == 'CONFIRM') ? $order_status : 99;
			if ($request['trans_status'] == 'CONFIRM') {
				$comments .= PHP_EOL . sprintf(MODULE_PAYMENT_NOVALNET_TRANS_CONFIRM_SUCCESSFUL_MESSAGE_TEXT, date('d.m.Y', strtotime(date('d.m.Y'))), date('H:i:s')) . PHP_EOL;
				$comments .= NovalnetHelper::getTransactionDetails($response);
				if (in_array($response['transaction']['payment_type'], array( 'INSTALMENT_INVOICE','GUARANTEED_INVOICE', 'INVOICE', 'PREPAYMENT'))) {
					if (empty($response['transaction']['bank_details'])) {
						$response['transaction']['bank_details'] = $payment_details;
					}
					$comments .= NovalnetHelper::getBankDetails($response);
				}
				if (in_array($response['transaction']['payment_type'], array( 'INSTALMENT_INVOICE','INSTALMENT_DIRECT_DEBIT_SEPA'))) {
					$comments .= NovalnetHelper::getInstalmentDetails($response);
					if (in_array($response['transaction']['status'],array('CONFIRMED', 'PENDING'))) {
						$total_amount = ($txn_details->fields['amount'] < $response['transaction']['amount']) ? $response['transaction']['amount'] : $txn_details->fields['amount'];
						$instalment_details = NovalnetHelper::storeInstalmentdetails($response, $total_amount);
						$update_data['instalment_cycle_details'] = $instalment_details;
					}
				}
			} elseif ($request['trans_status'] == 'CANCEL') {
				$comments .= PHP_EOL.sprintf(MODULE_PAYMENT_NOVALNET_TRANS_DEACTIVATED_MESSAGE, date('d.m.Y', strtotime(date('d.m.Y'))), date('H:i:s'));
			}
			updateOrderStatus($request['oID'], $comments, $order_status);
			$messageStack->add_session($response['result']['status_text'], 'success');
		} else { // Failure
			$messageStack->add_session($response['result']['status_text'], 'error');
		}
		if (!empty($request['oID'])) {
			zen_db_perform('novalnet_transaction_detail', $update_data, 'update', 'order_no='.$request['oID']);
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
		];
		if (!empty($request['refund_reason'])){
			$data['transaction']['reason'] = $request['refund_reason'];
		}
		$response = NovalnetHelper::sendRequest($data, NovalnetHelper::getActionEndpoint('transaction_refund'));
		if ($response['result']['status'] == 'SUCCESS') {
			$refunded_amount = $response['transaction']['refund']['amount'];
			if (in_array($response['transaction']['payment_type'], array('INSTALMENT_INVOICE','INSTALMENT_DIRECT_DEBIT_SEPA'))) {
				$instalment_details = (!empty($txn_details->fields['instalment_cycle_details'])) ? json_decode($txn_details->fields['instalment_cycle_details'], true) : unserialize($txn_details->fields['payment_details']);
				if(!empty($instalment_details)) {
					$cycle = $request['instalment_cycle'];
					$instalment_amount = (strpos((string)$instalment_details[$cycle]['instalment_cycle_amount'], '.')) ? $instalment_details[$cycle]['instalment_cycle_amount']*100 : $instalment_details[$cycle]['instalment_cycle_amount'];
					$instalment_amount = $instalment_amount - $refunded_amount;
					$instalment_details[$cycle]['instalment_cycle_amount'] = $instalment_amount;
					if($instalment_details[$cycle]['instalment_cycle_amount'] <= 0) {
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
				zen_db_perform('novalnet_transaction_detail', $update_data, 'update', 'order_no='.$request['oID']);
			}
			updateOrderStatus($request['oID'], $message, $current_order_status->fields['orders_status']);
			$messageStack->add_session($response['result']['status_text'], 'success');
		} else {
			$messageStack->add_session($response['result']['status_text'], 'error');
		}
		zen_redirect(zen_href_link(FILENAME_ORDERS, zen_get_all_get_params(['action']) . 'action=edit' . '&oID=' . (int)$request['oID']));
	} else if (!empty($request['nn_book_confirm']) && !empty($request['book_amount'])) {	// Zero amount booking transaction process
		$merchant_data    = NovalnetHelper::getMerchantData();
		$customer_data    = NovalnetHelper::getCustomerData();
		$transaction_data = NovalnetHelper::getTransactionData();
		$custom_data	  = NovalnetHelper::getCustomData();
		$customer_data['customer']['billing']['country_code'] = $order->billing['country']['iso_code_2'];
		if (empty($customer_data['customer']['shipping']['same_as_billing'])) {
			$customer_data['customer']['shipping']['country_code'] = $order->delivery['country']['iso_code_2'];
		}  
		$transaction_data['transaction']['payment_type'] = $txn_details->fields['payment_type'];
		$data = array_merge($merchant_data, $customer_data, $transaction_data, $custom_data);
		$data['transaction']['amount'] = $request['book_amount'];
		$data['transaction']['payment_data']['token'] = $payment_details['token'];
		$response = NovalnetHelper::sendRequest($data, NovalnetHelper::getActionEndpoint('payment'));
		if ($response['result']['status'] == 'SUCCESS' ) {
			$order_status_value = $db->Execute("SELECT orders_status from " . TABLE_ORDERS . " where orders_id = " . zen_db_input($request['oID']));
			$message =  PHP_EOL .PHP_EOL. sprintf(MODULE_PAYMENT_NOVALNET_TRANS_BOOKED_MESSAGE, $currencies->format(($request['book_amount'] / 100), 1, $response['transaction']['currency']), $response['transaction']['tid']) . PHP_EOL;
			$update_data = [
						'amount' => $response['transaction']['amount'],
						'tid' 	 => $response['transaction']['tid'],
					];
			if (!empty($request['oID'])) {
				zen_db_perform('novalnet_transaction_detail', $update_data, 'update', 'order_no='.$request['oID']);
			}
			updateOrderStatus($request['oID'], $message, $current_order_status->fields['orders_status']);
			$messageStack->add_session($response['result']['status_text'], 'success');
		} else {
			$messageStack->add_session($response['result']['status_text'], 'error');
		}
		zen_redirect(zen_href_link(FILENAME_ORDERS, zen_get_all_get_params(['action']) . 'action=edit' . '&oID=' . (int)$request['oID']));
	} else if (!empty($request['nn_instacancel_allcycles']) || !empty($request['nn_instacancel_remaincycles'])) { // Instalment cancel process
		$data['instalment']['tid'] = $txn_details->fields['tid'];
		$data['custom'] = [
			'lang' => (isset($_SESSION['languages_code'])) ? strtoupper($_SESSION['languages_code']) : 'DE',
		];
		if (isset($request['nn_instacancel_allcycles'])) {
			$data['instalment']['cancel_type'] = 'CANCEL_ALL_CYCLES';
		} else if (isset($request['nn_instacancel_remaincycles'])){
			$data['instalment']['cancel_type'] = 'CANCEL_REMAINING_CYCLES';
		}
		$response = NovalnetHelper::sendRequest($data, NovalnetHelper::getActionEndpoint('instalment_cancel'));
		if ($response['result']['status'] == 'SUCCESS') {
			if (!empty($request['nn_instacancel_remaincycles'])) {
				$instalment_details = json_decode($txn_details->fields['instalment_cycle_details'], true);
				if(!empty($instalment_details)) {
					foreach($instalment_details as $key => $instalment_details_data) {						
						if (empty($instalment_details_data['reference_tid']) && ($instalment_details_data['status'] == 'Pending'))	{					
							$instalment_details[$key]['status'] = constant('MODULE_PAYMENT_NOVALNET_INSTALMENT_STATUS_CANCELED');
						}
						if ($instalment_details_data['status'] == 'Paid') {
							$instalment_details[$key]['status'] = constant('MODULE_PAYMENT_NOVALNET_INSTALMENT_STATUS_REFUNDED');
						}						
					}
					$update_data = [
						'instalment_cycle_details' => json_encode($instalment_details),
					];				
				}
				$update_data['status'] = 'CONFIRMED';
				$message = PHP_EOL. sprintf((MODULE_PAYMENT_NOVALNET_INSTALMENT_CANCEL_REMAINING_CYCLES_TEXT), $txn_details->fields['tid'], date('Y-m-d H:i:s')); 
			} else if (!empty($request['nn_instacancel_allcycles'])) {
				$instalment_details = json_decode($txn_details->fields['instalment_cycle_details'], true);
				if(!empty($instalment_details)) {
					foreach($instalment_details as $key => $instalment_details_data) {						
						if (!empty($instalment_details_data['reference_tid']) && ($instalment_details_data['status'] == 'Paid'))	{					
							$instalment_details[$key]['status'] = constant('MODULE_PAYMENT_NOVALNET_INSTALMENT_STATUS_REFUNDED');
						} else {
							$instalment_details[$key]['status'] = constant('MODULE_PAYMENT_NOVALNET_INSTALMENT_STATUS_CANCELED');
						}
					}
					$update_data = [
						'instalment_cycle_details' => json_encode($instalment_details),
					];					
				}
				$update_data['status'] = 'CANCELED';
				$message = PHP_EOL. sprintf((MODULE_PAYMENT_NOVALNET_INSTALMENT_CANCEL_ALLCYCLES_TEXT), $txn_details->fields['tid'], date('Y-m-d H:i:s'), $currencies->format(($response['transaction']['refund']['amount']/100), 1, $txn_details->fields['currency'])); 			
			}		
		
			if (!empty($request['oID'])) {
				zen_db_perform('novalnet_transaction_detail', $update_data, 'update', 'order_no='.$request['oID']);
			}
			updateOrderStatus($request['oID'], $message, $current_order_status->fields['orders_status']);
			$messageStack->add_session($response['result']['status_text'], 'success');
		} else {
			$messageStack->add_session($response['result']['status_text'], 'error');
		}
		zen_redirect(zen_href_link(FILENAME_ORDERS, zen_get_all_get_params(['action']) . 'action=edit' . '&oID=' . (int)$request['oID']));
	} else {
		zen_redirect(zen_href_link(FILENAME_ORDERS, zen_get_all_get_params(['action']) . 'action=edit' . '&oID=' . (int)$request['oID']));
	}
}

/**
* Update order status in the shop
*
* @param integer $order_id
* @param string $order_status
* @param string $message
*/
function updateOrderStatus($order_id, $message, $order_status = '') {
	global $db;
	zen_db_perform(TABLE_ORDERS, array(
		'orders_status' => $order_status,
	), "update", "orders_id='$order_id'");
	
	$db->Execute("insert into " . TABLE_ORDERS_STATUS_HISTORY . " (orders_id, orders_status_id, date_added, customer_notified, comments) values ('".zen_db_input($order_id)."', '".zen_db_input($order_status)."', '" .date('Y-m-d H:i:s') . "', '1', '".zen_db_input($message)."')");
}
?>
