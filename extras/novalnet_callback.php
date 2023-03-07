<?php
/**
 * Novalnet payment module
 *
 * This script is used for handle novalnet webhook event
 *
 * @author     Novalnet AG
 * @copyright  Copyright (c) Novalnet
 * @license    https://www.novalnet.de/payment-plugins/kostenlos/lizenz
 * @link       https://www.novalnet.de
 *
 * File: callback.php
 *
 */

chdir('../');
require('includes/application_top.php');
include_once('includes/filenames.php');
include_once(DIR_FS_CATALOG. 'includes/functions/functions_email.php');

class NovalnetWebhooks {

	/**
	 * Allowed host from Novalnet.
	 *
	 * @var string
	 */
	protected $novalnet_host_name = 'pay-nn.de';

	/**
	 * Mandatory Parameters.
	 *
	 * @var array
	 */
	protected $mandatory = [
		'event'       => [
			'type',
			'checksum',
			'tid',
		],
		'merchant'    => [
			'vendor',
			'project',
		],
		'result'      => [
			'status',
		],
		'transaction' => [
			'tid',
			'payment_type',
			'status',
		],
	];

	/**
	 * Request parameters.
	 *
	 * @var array
	 */
	protected $event_data = [];

	/**
	 * Order reference values.
	 *
	 * @var array
	 */
	protected $order_details = [];

	/**
	 * Recived Event type.
	 *
	 * @var string
	 */
	protected $event_type;

	/**
	 * Recived Event TID.
	 *
	 * @var int
	 */
	protected $event_tid;

	/**
	 * Recived Event parent TID.
	 *
	 * @var int
	 */
	protected $parent_tid;

	/**
	 * Order language details.
	 *
	 * @var array
	 */
	protected $order_lang;

	/**
	 * Core Function : Constructor()
	 *
	 */
	function __construct() {
		try {
			$this->event_data = json_decode(file_get_contents('php://input'), true);
		} catch (Exception $e) {
			$this->displayMessage([ 'message' => 'Received data is not in the JSON format' . $e]);
		}
		$this->authenticateEventData();
		$this->event_tid  = !empty($this->event_data ['event'] ['tid']) ? $this->event_data ['event'] ['tid'] : '';
		$this->event_type = $this->event_data['event']['type'];
		$this->parent_tid = (!empty($this->event_data['event']['parent_tid'])) ? $this->event_data['event']['parent_tid'] :$this->event_data['event']['tid'];
		$this->order_details = $this->getOrderDetails();
		// If the order in the Novalnet server to the order number in Novalnet database doesn't match, then there is an issue
		if (!empty($this->event_data['transaction']['order_no']) && !empty($this->order_details['shop_order'])
		&& (($this->event_data['transaction']['order_no']) != $this->order_details['shop_order'])) {
			$this->displayMessage(['message' => 'Order reference not matching for the order number ' . $this->order_details['shop_order']]);
		}
		// If both the order number from Novalnet and in shop is missing, then something is wrong
		if (!empty($this->event_data['transaction']['order_no']) && empty($this->order_details['shop_order'])) {
			$this->displayMessage(['message' => 'Order reference not found for the TID ' . $this->parent_tid]);
		}

		if (NovalnetHelper::is_success_status($this->event_data)) {
			switch($this->event_type) {
				case 'PAYMENT':
					$this->displayMessage(['message' => "The webhook notification received ('".$this->event_data['transaction']['payment_type']."') for the TID: '".$this->event_tid."'"]);
					break;
				case 'TRANSACTION_CAPTURE':
					$this->handleTransactionCapture();
					break;
				case 'TRANSACTION_CANCEL':
					$this->handleTransactionCancel();
					break;
				case 'TRANSACTION_REFUND':
				 $this->handleTransactionRefund();
					break;
				case 'CREDIT':
					$this->handleTransactionCredit();
					break;
				case 'CHARGEBACK':
					$this->handleChargeback();
					break;
				case 'INSTALMENT':
					$this->handleInstalment();
					break;
				case 'INSTALMENT_CANCEL':
					$this->handleInstalmentCancel();
					break;
				case 'TRANSACTION_UPDATE':
					$this->handleTransactionUpdate();
					break;
				case 'PAYMENT_REMINDER_1':
				case 'PAYMENT_REMINDER_2':
					$this->handlePaymentReminder();
					break;
				case 'SUBMISSION_TO_COLLECTION_AGENCY':
					$this->handleCollectionSubmission();
					break;
				default:
					$message = "The webhook notification has been received for the unhandled EVENT type('".$this->event_type."')";
					$this->displayMessage(['message' => $message]);
			}
		}
	}

	/**
	 * Authenticate server request
	 *
	 */
	function authenticateEventData() {
		$request_received_ip = zen_get_ip_address();
		$novalnet_host_ip  = gethostbyname($this->novalnet_host_name);
		if (!empty($novalnet_host_ip) && !empty($request_received_ip)) {
			if ($novalnet_host_ip !== $request_received_ip && MODULE_PAYMENT_NOVALNET_CALLBACK_TEST_MODE == 'false') {
				$this->displayMessage(['message' => 'Unauthorised access from the IP ' . $request_received_ip]);
			}
		} else {
			$this->displayMessage([ 'message' => 'Unauthorised access from the IP. Host/recieved IP is empty' ]);
		}
		$this->validateEventData();
		$this->validateCheckSum();
	}

	/**
	 * Validate event_data
	 *
	 */
	function validateEventData() {
		foreach ($this->mandatory as $category => $parameters) {
			if (empty($this->event_data[ $category ])) {
				$this->displayMessage([ 'message' => "Required parameter category($category) not received" ]);
			} elseif (!empty($parameters)) {
				foreach ($parameters as $parameter) {
					if (empty($this->event_data [ $category ] [ $parameter ])) {
						$this->displayMessage([ 'message' => "Required parameter($parameter) in the category($category) not received" ]);
					} elseif (in_array($parameter, [ 'tid', 'parent_tid' ], true) && ! preg_match('/^\d{17}$/', $this->event_data [ $category ] [ $parameter ])) {
						$this->displayMessage([ 'message' => "Invalid TID received in the category($category) not received $parameter" ]);
					}
				}
			}
		}
	}

	/**
	 * Validate checksum
	 *
	 */
	function validateCheckSum() {
		if (!empty($this->event_data['event']['checksum']) && ! empty($this->event_data['event']['tid']) && ! empty($this->event_data['event']['type'])
		&& !empty($this->event_data['result']['status'])) {
			$token_string = $this->event_data['event']['tid'] . $this->event_data['event']['type'] . $this->event_data['result']['status'];
			if (isset($this->event_data['transaction']['amount'])) {
			  $token_string .= $this->event_data['transaction']['amount'];
			}
			if (isset($this->event_data['transaction']['currency'])) {
			  $token_string .= $this->event_data['transaction']['currency'];
			}
			if (defined('MODULE_PAYMENT_NOVALNET_PAYMENT_ACCESS_KEY') && !empty(MODULE_PAYMENT_NOVALNET_PAYMENT_ACCESS_KEY)) {
			  $token_string .= strrev(MODULE_PAYMENT_NOVALNET_PAYMENT_ACCESS_KEY);
			}
			$generated_checksum = hash('sha256', $token_string);
			if ($generated_checksum != $this->event_data['event']['checksum']) {
				$this->displayMessage([ 'message' =>'While notifying some data has been changed. The hash check failed']);
			}
		}
	}

	function getOrderDetails(){
		global $db;
		$order_details = [];
		$novalnet_order_details = $db->Execute("SELECT * FROM novalnet_transaction_detail WHERE tid = '".$this->parent_tid."'");
		$orderNumber = !empty($novalnet_order_details->fields['order_no']) ? $novalnet_order_details->fields['order_no'] : $this->event_data['transaction']['order_no'];
		if(empty($orderNumber)) {
			$this->displayMessage(array( 'message' => 'Order reference not found in the shop' ));
		}
		$shop_order_details = $db->Execute("SELECT order_total,orders_id, customers_id,orders_status, language_code FROM ".TABLE_ORDERS." WHERE orders_id = '".$orderNumber."'");
		$this->order_lang = $db->Execute("SELECT directory FROM " . TABLE_LANGUAGES . " WHERE code = '" . $shop_order_details->fields['language_code'] ."'");
		$this->include_required_files($this->order_lang->fields['directory']);

		$order_details['nn_trans_details'] =  $novalnet_order_details->fields;
		$order_details['shop_order'] =  $orderNumber;
		$order_details['shop_order_details'] =  $shop_order_details->fields;
		return $order_details;
	}

	/**
	 * Handle transaction capture
	 *
	 */
	function handleTransactionCapture() {
		if ($this->order_details['nn_trans_details']['status'] != $this->event_data['transaction']['status']) {
			$novalnet_update_data = [
				'status' => $this->event_data['transaction']['status'],
			];

			$order_status = NovalnetHelper::getOrderStatus ($this->event_data['transaction']['status'], $this->order_details['nn_trans_details']['payment_type']);
			if (in_array($this->order_details['nn_trans_details']['payment_type'], array('INVOICE', 'GUARANTEED_INVOICE', 'INSTALMENT_INVOICE'))) {
				$comments = PHP_EOL.sprintf(MODULE_PAYMENT_NOVALNET_TRANS_CONFIRM_SUCCESSFUL_MESSAGE, $this->event_data['transaction']['tid'], gmdate('d-m-Y')).PHP_EOL.PHP_EOL;
			} else {
				$comments = PHP_EOL.sprintf(MODULE_PAYMENT_NOVALNET_TRANS_CONFIRM_SUCCESSFUL_MESSAGE_TEXT, gmdate('d-m-Y')).PHP_EOL.PHP_EOL;
			}
			if (in_array($this->order_details['nn_trans_details']['payment_type'], array('INSTALMENT_DIRECT_DEBIT_SEPA', 'INSTALMENT_INVOICE'))) {
				$total_amount = ($this->order_details['nn_trans_details']['amount'] < $this->event_data['transaction']['amount']) ? $this->event_data['transaction']['amount'] : $this->order_details['nn_trans_details']['amount'];
				$novalnet_update_data['instalment_cycle_details'] = NovalnetHelper::storeInstalmentdetails($this->event_data, $total_amount);
			}
			$order_comment = $comments;
			$order_comment .= NovalnetHelper::getTransactionDetails($this->event_data);
			if (in_array($this->order_details['nn_trans_details']['payment_type'], array('INVOICE', 'GUARANTEED_INVOICE', 'INSTALMENT_INVOICE'))) {
				if (empty($this->event_data ['transaction']['bank_details'])) {
					if(!empty($this->order_details['nn_trans_details']['payment_details'])) {
							$bank_data = json_decode($this->order_details['nn_trans_details']['payment_details']);
							$bank_details = array(
								'account_holder' => $bank_data->account_holder,
								'iban' => $bank_data->iban,
								'bic' => $bank_data->bic,
								'bank_name' => $bank_data->bank_name,
								'bank_place' => $bank_data->bank_place,
							);
					} else {
						$bank_details = json_decode($this->order_details['nn_trans_details']['payment_details'], true);
					}
					$this->event_data ['transaction']['bank_details'] = $bank_details;
				}
				$order_comment .= NovalnetHelper::getBankDetails($this->event_data);
			}
			if (in_array($this->order_details['nn_trans_details']['payment_type'], array('INSTALMENT_DIRECT_DEBIT_SEPA', 'INSTALMENT_INVOICE'))) {
				$order_comment .= NovalnetHelper::getInstalmentDetails($this->event_data);
			}
			$this->updateOrderStatusHistory($this->event_data['transaction']['order_no'], $order_status, $order_comment);
			if ( $this->event_data['transaction']['status'] == 'CONFIRMED' && in_array($this->event_data['transaction']['payment_type'], array( 'INSTALMENT_INVOICE','GUARANTEED_INVOICE'))) {
				NovalnetHelper::sendPaymentConfirmationMail($order_comment, $this->event_data['transaction']['order_no']);
			}
			$this->updateNovalnetTransaction($novalnet_update_data, "tid='{$this->parent_tid}'");
			$this->sendWebhookMail($comments);
			$this->displayMessage([ 'message' => $comments]);
		}
	}

	/**
	 * Handle transaction cancel
	 *
	 */
	function handleTransactionCancel() {
		if ($this->order_details['nn_trans_details']['status'] != $this->event_data['transaction']['status']) {
			$order_status = '';
			$order_status = NovalnetHelper::getOrderStatusId();
			$comments = sprintf(MODULE_PAYMENT_NOVALNET_TRANS_DEACTIVATED_MESSAGE, gmdate('d-m-Y'), gmdate('H:i:s'));
			$novalnet_update_data = [
				'status' => $this->event_data['transaction']['status'],
			];
			$this->updateNovalnetTransaction($novalnet_update_data, "tid='{$this->parent_tid}'");
			$this->updateOrderStatusHistory($this->event_data['transaction']['order_no'], $order_status, $comments);
			$this->sendWebhookMail($comments);
			$this->displayMessage([ 'message' => $comments]);
		}
	}


	/**
	 * Handle transaction refund
	 *
	 */
	function handleTransactionRefund() {
		global $currencies;
		if (!empty($this->event_data['transaction']['refund']['amount'])) {
			$order_status_id = '';
			$comments = PHP_EOL . sprintf(MODULE_PAYMENT_NOVALNET_REFUND_PARENT_TID_MSG, $this->parent_tid, $currencies->format(($this->event_data['transaction']['refund']['amount']/100), 1, $this->event_data['transaction']['currency']));
			if (!empty($this->event_data['transaction']['refund']['tid'])) {
				$comments .= sprintf(MODULE_PAYMENT_NOVALNET_REFUND_CHILD_TID_MSG, $this->event_data['transaction']['refund']['tid']);
			}
			$refund_amount = $this->event_data['transaction']['refund']['amount'];
			$refunded_amount = $this->order_details['nn_trans_details']['refund_amount'] + $refund_amount;
			$novalnet_update_data = array(
				'refund_amount' => $refunded_amount,
				'status'        => $this->event_data['transaction']['status'],
			);
			if (in_array($this->event_data['transaction']['payment_type'], array('INSTALMENT_INVOICE','INSTALMENT_DIRECT_DEBIT_SEPA'))) {
				$instalment_details = (!empty($this->order_details['nn_trans_details']['instalment_cycle_details'])) ? json_decode($this->order_details['nn_trans_details']['instalment_cycle_details'], true) : unserialize($this->order_details['nn_trans_details']['payment_details']);
				if(!empty($instalment_details)) {
					foreach($instalment_details as $cycle => $cycle_details){
						if(!empty($cycle_details['reference_tid']) && ($cycle_details['reference_tid'] == $this->event_data['transaction']['tid'])) {
							$instalment_amount = (strpos((string)$instalment_details[$cycle]['instalment_cycle_amount'], '.')) ? $instalment_details[$cycle]['instalment_cycle_amount']*100 : $instalment_details[$cycle]['instalment_cycle_amount'];
							$instalment_amount = $instalment_amount - $refund_amount;
							$instalment_details[$cycle]['instalment_cycle_amount'] = $instalment_amount;
							if($instalment_details[$cycle]['instalment_cycle_amount'] <= 0) {
								$instalment_details[$cycle]['status'] = 'Refunded';
							}
						}
						$novalnet_update_data['instalment_cycle_details'] =json_encode($instalment_details);
					}
				}
				$novalnet_update_data['instalment_cycle_details'] =json_encode($instalment_details);
			}
			if ($refunded_amount >= $this->order_details['nn_trans_details']['amount']) {
				$order_status_id = NovalnetHelper::getOrderStatusId();
			}
			$this->updateNovalnetTransaction($novalnet_update_data, "tid='{$this->parent_tid}'");
			$this->updateOrderStatusHistory($this->order_details['shop_order'], $order_status_id, $comments);
			$this->sendWebhookMail($comments);
			$this->displayMessage([ 'message' => $comments]);
		}
	}

	/**
	 * Handle chargeback
	 *
	 */
	function handleTransactionCredit() {
		global $currencies;
		$update_comments = true;
		$order_status = '';
		$comments = sprintf(NOVALNET_WEBHOOK_CREDIT_NOTE, $this->parent_tid, $currencies->format(($this->event_data['transaction']['amount']/100), 1, $this->event_data['transaction']['currency']), gmdate('d-m-Y H:i:s'), $this->event_tid);
		if (in_array($this->event_data['transaction']['payment_type'], ['INVOICE_CREDIT', 'CASHPAYMENT_CREDIT', 'MULTIBANCO_CREDIT'])) {
			$paid_amount = (!empty($this->order_details['nn_trans_details']['refund_amount'])) ? ((int)$this->order_details['nn_trans_details']['refund_amount'] + (int)$this->order_details['nn_trans_details']['callback_amount']) : $this->order_details['nn_trans_details']['callback_amount'];
			if ($paid_amount < $this->order_details['nn_trans_details']['amount']) {
				$total_paid_amount = $paid_amount + $this->event_data['transaction']['amount'];
				$update_data = array(
					'callback_amount' => $total_paid_amount
				);
				if ($total_paid_amount >= $this->order_details['nn_trans_details']['amount']) { // Full amount paid
					if ($this->order_details['nn_trans_details']['payment_type'] == 'INVOICE') { // Invoice payment type
						$order_status = 3;
					} else { // Other than Invoice payment
						$order_status = 2;
					}
				} else { // Partial paid
					if ($this->order_details['nn_trans_details']['payment_type'] == 'INVOICE') { // Invoice payment type
						$order_status = 2;
					} else { // Other than Invoice payment
						$order_status = 1;
					}
				}
				$update_data['status'] = $this->event_data['transaction']['status'];
				$this->updateNovalnetTransaction($update_data, "tid='{$this->parent_tid}'");
			} else {
				$update_comments = false;
				$comments = sprintf(('Callback script executed already'), gmdate('d-m-Y'), gmdate('H:i:s'));
			}
		}
		if($update_comments) {
			$this->updateOrderStatusHistory($this->event_data['transaction']['order_no'], $order_status, $comments, 1);
			$this->sendWebhookMail($comments);
		}
		$this->displayMessage([ 'message' => $comments]);
	}

	/**
	 * Handle chargeback
	 *
	 */
	function handleChargeback() {
		global $currencies;
		if (($this->order_details['nn_trans_details']['status'] == 'CONFIRMED') && !empty($this->event_data ['transaction'] ['amount'])) {
			$comments =sprintf(NOVALNET_WEBHOOK_CHARGEBACK_NOTE , $this->parent_tid, $currencies->format(($this->event_data['transaction']['amount']/100), 1, $this->event_data['transaction']['currency']), gmdate('d.m.Y'), gmdate('H:i:s'), $this->event_tid);
			$this->updateOrderStatusHistory($this->event_data['transaction']['order_no'], '', $comments);
			$this->sendWebhookMail($comments);
			$this->displayMessage([ 'message' => $comments]);
		}
	}

	/**
	 * Handle instalment
	 *
	 */
	function handleInstalment() {
		global $currencies;
		$comment = '';
		if ($this->event_data['transaction']['status'] == 'CONFIRMED' && !empty($this->event_data['instalment']['cycles_executed'])
		&& in_array($this->event_data['transaction']['payment_type'], array('INSTALMENT_INVOICE','INSTALMENT_DIRECT_DEBIT_SEPA'))) {
			$instalment_details = (!empty($this->order_details['nn_trans_details']['instalment_cycle_details'])) ? json_decode($this->order_details['nn_trans_details']['instalment_cycle_details'], true) : unserialize($this->order_details['nn_trans_details']['payment_details']);
			$instalment = $this->event_data['instalment'];
			$cycle_index = $instalment['cycles_executed'] - 1;
			if (!empty($instalment)) {
				$instalment_details[$cycle_index]['next_instalment_date'] = (!empty($instalment['next_cycle_date'])) ? $instalment['next_cycle_date'] : '-';
				if (!empty($this->event_data['transaction']['tid'])) {
					$instalment_details[$cycle_index]['reference_tid'] = $this->event_data['transaction']['tid'];
					$instalment_details[$cycle_index]['status'] = 'Paid';
					$instalment_details[$cycle_index]['paid_date'] = date('Y-m-d H:i:s');
				}
			}
			if ($this->event_data['transaction']['payment_type'] == 'INSTALMENT_INVOICE' && empty($this->event_data ['transaction']['bank_details'])) {
				$this->event_data ['transaction']['bank_details'] = json_decode($this->order_details['nn_trans_details']['payment_details'], true);
			}
			$comment = sprintf(NOVALNET_WEBHOOK_NEW_INSTALMENT_NOTE, $this->parent_tid, $currencies->format(($this->event_data['instalment']['cycle_amount']/100), 1, $this->event_data['transaction']['currency']), gmdate('d-m-Y'), $this->event_tid);
			$this->updateNovalnetTransaction(array('instalment_cycle_details' => json_encode($instalment_details)), "tid='{$this->parent_tid}'");
			$comment .= PHP_EOL. NovalnetHelper::insertTransactionDetails($this->event_data, false, $this->order_details['shop_order']);
			$this->updateOrderStatusHistory($this->event_data['transaction']['order_no'], '', $comment, 1);
			$this->sendWebhookMail($comment);
			$this->displayMessage([ 'message' => $comment]);
		}
	}

	/**
	 * Handle instalment cancel
	 *
	 */
	function handleInstalmentCancel() {
		global $currencies;
		$comments = '';
		if ($this->event_data['transaction']['status'] == 'CONFIRMED') {
			$order_status = '';
			$instalment_details = json_decode($this->order_details['nn_trans_details']['instalment_cycle_details'], true);
				if (isset($this->event_data['instalment']['cancel_type']) && $this->event_data['instalment']['cancel_type'] != 'ALL_CYCLES') {
					if(!empty($instalment_details)) {
						foreach($instalment_details as $key => $instalment_details_data) {
							if (empty($instalment_details_data['reference_tid']) && ($instalment_details_data['status'] == 'Pending'))	{
								$instalment_details[$key]['status'] = 'Canceled';
							}
						}
						$novalnet_update_data = [
							'instalment_cycle_details' => json_encode($instalment_details),
							'status' => 'DEACTIVATED',
						];
					}
					$comments .= sprintf(MODULE_PAYMENT_NOVALNET_INSTALMENT_CANCEL_REMAINING_CYCLES_TEXT, $this->parent_tid, gmdate('d.m.Y'));
				} else {
					$order_status = NovalnetHelper::getOrderStatusId();
					$refunded_amount = $this->order_details['nn_trans_details']['refund_amount'] + $this->event_data['transaction']['refund']['amount'];
					if(!empty($instalment_details)) {
						foreach($instalment_details as $cycle => $cycle_details) {
							if (!empty($cycle_details['reference_tid']) && ($cycle_details['status'] == 'Paid')) {
								$instalment_details[$cycle]['status'] = 'Refunded';
							}
							if ($cycle_details['status'] == 'Pending') {
								$instalment_details[$cycle]['status'] = 'Canceled';
							}
							if(!empty($cycle_details['reference_tid']) && (($cycle_details['reference_tid'] == $this->parent_tid) || ($cycle_details['reference_tid'] != $this->parent_tid))) {
								$instalment_amount = (strpos((string)$instalment_details[$cycle]['instalment_cycle_amount'], '.')) ? $instalment_details[$cycle]['instalment_cycle_amount']*100 : $instalment_details[$cycle]['instalment_cycle_amount'];
								$instalment_amount = $instalment_amount - $this->event_data['transaction']['refund']['amount'];
								$instalment_details[$cycle]['instalment_cycle_amount'] = $instalment_amount;
							}
						}
						$novalnet_update_data = [
							'instalment_cycle_details' => json_encode($instalment_details),
							'status' => 'DEACTIVATED',
							'refund_amount' => $refunded_amount,
						];
					}
					$comments .= sprintf(MODULE_PAYMENT_NOVALNET_INSTALMENT_CANCEL_ALLCYCLES_TEXT, $this->parent_tid, gmdate('d.m.Y'), $currencies->format(($this->event_data['transaction']['refund']['amount']/100), 1, $this->event_data['transaction']['currency']));
				}
		}
		$this->updateNovalnetTransaction($novalnet_update_data, "tid='{$this->parent_tid}'");
		$this->updateOrderStatusHistory($this->event_data['transaction']['order_no'], $order_status, $comments);
		$this->sendWebhookMail($comments);
		$this->displayMessage(['message' => $comments]);
	}

	/**
	 * Handle transaction update
	 *
	 */
	function handleTransactionUpdate() {
		global $currencies;
		if (in_array( $this->event_data['transaction']['status'], array('PENDING', 'ON_HOLD', 'CONFIRMED', 'DEACTIVATED'))) {
			$novalnet_update_data = [
				'status' => $this->event_data ['transaction']['status'],
			];
			$order_status = '';
			if ($this->event_data['transaction']['status'] == 'DEACTIVATED') {
				$transaction_comments = sprintf(MODULE_PAYMENT_NOVALNET_TRANS_DEACTIVATED_MESSAGE, gmdate('d.m.Y'), gmdate('H:i:s'));
				$order_status = NovalnetHelper::getOrderStatusId();
			} else { 
				if (in_array($this->order_details['nn_trans_details']['status'], array('PENDING', 'ON_HOLD' ), true)) {
					if (empty($this->event_data['instalment']['cycle_amount'])) {
						$amount = $this->event_data['transaction']['amount'];
					} else {
						$amount = $this->event_data['instalment']['cycle_amount'];
					}				
					
					if ($this->order_details['nn_trans_details']['status'] == 'PENDING') {
						if ($this->event_data['transaction']['status'] == 'ON_HOLD'){
							$order_status = 99;
							$transaction_comments = sprintf(NOVALNET_PAYMENT_STATUS_PENDING_TO_ONHOLD_TEXT, $this->event_tid, gmdate('d.m.Y'), gmdate('H:i:s'));
						} else if ($this->event_data['transaction']['status'] == 'CONFIRMED') {
							$order_status = 2;							
							if (in_array($this->event_data['transaction']['payment_type'], array('INVOICE', 'GUARANTEED_INVOICE','INSTALMENT_INVOICE'))) {
								$transaction_comments = PHP_EOL.sprintf(NOVALNET_WEBHOOK_TRANSACTION_UPDATE_NOTE_DUE_DATE, $this->event_data['transaction']['tid'], $currencies->format(($this->event_data['transaction']['amount']/100), 1, $this->event_data['transaction']['currency']), $this->event_data['transaction']['due_date']).PHP_EOL.PHP_EOL;
							} else {
								$transaction_comments = PHP_EOL.sprintf(NOVALNET_WEBHOOK_TRANSACTION_UPDATE_NOTE, $this->event_data['transaction']['tid'], $currencies->format(($amount/100), 1, $this->event_data['transaction']['currency']),gmdate('d.m.Y')).PHP_EOL.PHP_EOL;
							}
														
						}
					}			
					
					if ($this->order_details['nn_trans_details']['status'] == 'ON_HOLD') {
						if($this->event_data['transaction']['status'] == 'CONFIRMED') {
							$order_status = 2;
							if (in_array($this->event_data['transaction']['payment_type'], array('INVOICE', 'GUARANTEED_INVOICE','INSTALMENT_INVOICE'))) {
								$transaction_comments = PHP_EOL.sprintf(MODULE_PAYMENT_NOVALNET_TRANS_CONFIRM_SUCCESSFUL_MESSAGE, $this->event_data['transaction']['tid'], $this->event_data['transaction']['due_date']).PHP_EOL.PHP_EOL;
							} else {
								$transaction_comments = PHP_EOL.sprintf(MODULE_PAYMENT_NOVALNET_TRANS_CONFIRM_SUCCESSFUL_MESSAGE_TEXT, $this->event_data['transaction']['tid'], gmdate('d.m.Y')).PHP_EOL.PHP_EOL;
							}
						}						
					}				
					
					if ($this->event_data['transaction']['status'] == 'CONFIRMED') {
						if (in_array( $this->event_data['transaction']['payment_type'], array('INSTALMENT_INVOICE','INSTALMENT_DIRECT_DEBIT_SEPA'))) {
							if (empty($this->order_details['nn_trans_details']['instalment_cycle_details'])) {
								$total_amount = ($this->order_details['nn_trans_details']['amount'] < $this->event_data['transaction']['amount']) ? $this->event_data['transaction']['amount'] : $this->order_details['nn_trans_details']['amount'];
								$novalnet_update_data['instalment_cycle_details'] = NovalnetHelper::storeInstalmentdetails($this->event_data, $total_amount);
							}
						}
						$order_status = 2;
						$novalnet_update_data['callback_amount'] = $this->order_details['nn_trans_details']['amount'];
					}
					
					// Reform the transaction comments.
					$transaction_comments .= NovalnetHelper::getTransactionDetails($this->event_data);
					if (in_array($this->event_data['transaction']['payment_type'], array( 'INSTALMENT_INVOICE','GUARANTEED_INVOICE', 'INVOICE', 'PREPAYMENT'))) {
						if (empty($this->event_data ['transaction']['bank_details'])) {
							if(!empty($this->order_details['nn_trans_details']['payment_details'])) {
								$bank_data = json_decode($this->order_details['nn_trans_details']['payment_details']);
								$this->event_data ['transaction']['bank_details'] = array(
									'account_holder' => $bank_data->account_holder,
									'iban' => $bank_data->iban,
									'bic' => $bank_data->bic,
									'bank_name' => $bank_data->bank_name,
									'bank_place' => $bank_data->bank_city,
								);
							} else {
								$this->event_data ['transaction']['bank_details'] = json_decode($this->order_details['nn_trans_details']['payment_details'], true);
							}
						}
						$transaction_comments .= NovalnetHelper::getBankDetails($this->event_data);

					}				
					if ('CASHPAYMENT' === $this->event_data ['transaction']['payment_type']) {
						$this->event_data ['transaction']['nearest_stores'] = json_decode($this->order_details['nn_trans_details']['payment_details'], true);
						$transaction_comments .= NovalnetHelper::getBankDetails($this->event_data);
					}
					if (in_array($this->event_data['transaction']['payment_type'], array( 'INSTALMENT_INVOICE','INSTALMENT_DIRECT_DEBIT_SEPA'))) {
						$transaction_comments .= NovalnetHelper::getInstalmentDetails($this->event_data);
					} else {
						if ((int)$this->event_data['transaction']['amount'] != (int)$this->order_details['nn_trans_details']['amount']) {
							$novalnet_update_data['amount'] = $this->event_data['transaction']['amount'];
							if('CONFIRMED' === $this->event_data['transaction']['status']) {
								$novalnet_update_data['callback_amount'] = $this->event_data['transaction']['amount'];
							}
						}
					}
				}
			}
			if ( in_array($this->event_data['transaction']['status'], array('CONFIRMED', 'ON_HOLD'))&& in_array($this->event_data['transaction']['payment_type'], array( 'INSTALMENT_INVOICE','GUARANTEED_INVOICE'))) {
				NovalnetHelper::sendPaymentConfirmationMail($transaction_comments, $this->event_data['transaction']['order_no']);
			}
			$this->updateNovalnetTransaction($novalnet_update_data, "tid='{$this->parent_tid}'");
			$this->updateOrderStatusHistory($this->order_details['shop_order'], $order_status, $transaction_comments);
			$this->sendWebhookMail($comments);
			$this->displayMessage([ 'message' => $transaction_comments]);
		}
	}

	/**
	 * Handle Payment Reminder
	 *
	 */
	function handlePaymentReminder() {
		$comments =sprintf(NOVALNET_PAYMENT_REMINDER_NOTE , explode('_', $this->event_type)[2]);
		$this->updateOrderStatusHistory($this->event_data['transaction']['order_no'], '', $comments, 0);
		$this->sendWebhookMail($comments);
		$this->displayMessage([ 'message' => $comments]);
	}

	/**
	 * Handle Collection Agency Submission
	 *
	 */
	function handleCollectionSubmission() {
		$comments =sprintf(NOVALNET_COLLECTION_SUBMISSION_NOTE , $this->event_data['collection']['reference']);
		$this->updateOrderStatusHistory($this->event_data['transaction']['order_no'], '', $comments, 0);
		$this->sendWebhookMail($comments);
		$this->displayMessage([ 'message' => $comments]);
	}

	/**
	 * Print the Webhook messages.
	 *
	 * @param $message
	 *
	 * @return void
	 */
	function displayMessage($message) {
		echo json_encode($message);
		exit;
	}

	/**
	 * Update the details in Shop order status table.
	 *
	 * @param $order_id
	 * @param $order_status_id
	 * @param $comments
	 * @param $customer_notified
	 */
	function updateOrderStatusHistory($order_id, $order_status_id = '', $comments = '', $customer_notified = 1) {
		global $db;
		$datas_need_to_update = [];
		if ($order_status_id == '') {
			$current_order_status = $db->Execute("SELECT orders_status from " . TABLE_ORDERS . " where orders_id = " . $order_id);
			$order_status_id = $current_order_status->fields['orders_status'];
		}
		$datas_need_to_update['orders_status'] = $order_status_id;
		zen_db_perform(TABLE_ORDERS, $datas_need_to_update, "update", "orders_id='$order_id'");
		
		
		$data_array = array(
			'orders_id'         => $order_id,
			'orders_status_id'  => $order_status_id,
			'date_added'        => date('Y-m-d H:i:s'),
			'customer_notified' => $customer_notified,
			'comments'          => zen_db_prepare_input($comments . PHP_EOL)
		);
		zen_db_perform(TABLE_ORDERS_STATUS_HISTORY,$data_array, 'insert');
	}

	/*
	 * Update the transaction details in Novalnet table
	 *
	 * @param $data
	 * @param $parameters
	 * @param $action
	 */
	function updateNovalnetTransaction($data, $parameters = '', $action = 'update') {
		if ($action != 'insert' && $parameters == '') {
			return false;
		}
		zen_db_perform('novalnet_transaction_detail', $data, $action, $parameters);
	}

	/**
	 * Send notification mail to Merchant
	 *
	 * @param $comments
	 */
	function sendWebhookMail($comments) {
		$email = NovalnetHelper::validateEmail(MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_TO);
		// Assign email to address
		$email_to = !empty($email) ? $email : STORE_OWNER_EMAIL_ADDRESS;
		$order_subject = 'Novalnet Callback Script Access Report - '.STORE_NAME;
		// Send mail
		zen_mail($email ,$email_to, $order_subject, $comments , STORE_NAME, EMAIL_FROM);
	}

	/**
	 * Include language file and helper file.
	 */
	function include_required_files($lang) {
		// include language
		include_once(DIR_FS_CATALOG . DIR_WS_LANGUAGES. $lang."/modules/payment/novalnet_payments.php");

		// include helper file after language files.
		require_once(DIR_FS_CATALOG . DIR_WS_MODULES. 'payment/novalnet/NovalnetHelper.class.php');
		return;
	}
}
new NovalnetWebhooks();
