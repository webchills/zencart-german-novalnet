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
 * File: novalnet_callback.php
 * critical emails only if transaction successful
 */

chdir('../');
require('includes/application_top.php');
include_once('includes/filenames.php');
include_once(DIR_FS_CATALOG . 'includes/functions/functions_email.php');

class NovalnetWebhooks
{
    /**
     * Mandatory Parameters.
     *
     * @var array
     */
    protected $mandatory = [
        'event' => [
            'type',
            'checksum',
            'tid',
        ],
        'result' => [
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
     * Core Function : Constructor()
     *
     */
    public function __construct()
    {
        try {
            $this->event_data = json_decode(file_get_contents('php://input'), true);
        } catch (Exception $e) {
            $this->displayMessage(['message' => 'Received data is not in the JSON format' . $e]);
        }
        $this->authenticateEventData();
        $this->event_tid = $this->event_data['event']['tid'];
        $this->event_type = $this->event_data['event']['type'];
        $this->parent_tid = (!empty($this->event_data['event']['parent_tid'])) ? $this->event_data['event']['parent_tid'] : $this->event_tid;

        $this->order_details = $this->getOrderDetails();
        if (NovalnetHelper::is_success_status($this->event_data)) {
            switch ($this->event_type) {
                case 'PAYMENT':
                    $this->displayMessage(['message' => "The webhook notification received ('" . $this->event_data['transaction']['payment_type'] . "') for the TID: '" . $this->event_tid . "'"]);
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
                    $message = "The webhook notification has been received for the unhandled EVENT type('" . $this->event_type . "')";
                    $this->displayMessage(['message' => $message]);
            }
        }
    }

    /**
     * Authenticate the request received from Novalnet or not
     *
     */
    private function authenticateEventData()
    {
        $novalnet_host_name = 'pay-nn.de';
        $novalnet_host_ip = gethostbyname($novalnet_host_name);
        $request_received_ip = $this->getRemoteAddress($novalnet_host_ip);
        if (!empty($novalnet_host_ip) && !empty($request_received_ip)) {
            if (MODULE_PAYMENT_NOVALNET_CALLBACK_TEST_MODE == 'False' && $novalnet_host_ip != $request_received_ip) {
                $this->displayMessage(['message' => 'Unauthorised access from the IP ' . $request_received_ip]);
            }
        } else {
            $this->displayMessage(['message' => 'Unauthorised access from the IP. Host/recieved IP is empty']);
        }
        $this->validateEventData();
        $this->validateCheckSum();
    }

    /**
     * Get remote address
     *
     * @param $novalnet_host_ip
     *
     */
    public function getRemoteAddress($novalnet_host_ip)
    {
        $ip_keys = array('HTTP_X_FORWARDED_HOST', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR');
        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                if (in_array($key, ['HTTP_X_FORWARDED_HOST', 'HTTP_X_FORWARDED_FOR'])) {
                    $forwarded_ip = !empty($_SERVER[$key]) ? explode(',', $_SERVER[$key]) : [];
                    return in_array($novalnet_host_ip, $forwarded_ip) ? $novalnet_host_ip : $_SERVER[$key];
                }
                return $_SERVER[$key];
            }
        }
    }

    /**
     * Validate event data mandatory parameters
     *
     */
    private function validateEventData()
    {
        if (!empty($this->event_data['custom']['shop_invoked'])) {
            $this->displayMessage(['message' => 'Process already handled in the shop.']);
        }
        foreach ($this->mandatory as $category => $parameters) {
            if (empty($this->event_data[$category])) {
                $this->displayMessage(['message' => "Required parameter category($category) not received"]);
            } elseif (!empty($parameters)) {
                foreach ($parameters as $parameter) {
                    if (empty($this->event_data[$category][$parameter])) {
                        $this->displayMessage(['message' => "Required parameter($parameter) in the category($category) not received"]);
                    } elseif (in_array($parameter, ['tid', 'parent_tid'], true) && !preg_match('/^\d{17}$/', $this->event_data[$category][$parameter])) {
                        $this->displayMessage(['message' => "Invalid TID received in the category($category) not received $parameter"]);
                    }
                }
            }
        }
    }

    /**
     * Validate checksum
     *
     */
    private function validateCheckSum()
    {
        if (
            !empty($this->event_data['event']['checksum']) && !empty($this->event_tid) && !empty($this->event_data['event']['type'])
            && !empty($this->event_data['result']['status'])
        ) {
            $token_string = $this->event_tid . $this->event_data['event']['type'] . $this->event_data['result']['status'];
            if (isset($this->event_data['transaction']['amount'])) {
                $token_string .= $this->event_data['transaction']['amount'];
            }
            if (isset($this->event_data['transaction']['currency'])) {
                $token_string .= $this->event_data['transaction']['currency'];
            }
            if (defined('MODULE_PAYMENT_NOVALNET_ACCESS_KEY') && !empty(MODULE_PAYMENT_NOVALNET_ACCESS_KEY)) {
                $token_string .= strrev(MODULE_PAYMENT_NOVALNET_ACCESS_KEY);
            }
            $generated_checksum = hash('sha256', $token_string);
            if ($generated_checksum != $this->event_data['event']['checksum']) {
                $this->displayMessage(['message' => 'While notifying some data has been changed. The hash check failed']);
            }
        }
    }

    /**
     * Get order details
     *
     */
    private function getOrderDetails()
    {
        global $db;
        $order_details = [];
        $novalnet_order_details = $db->Execute("SELECT * FROM " . TABLE_NOVALNET_TRANSACTION_DETAIL . " WHERE tid = '" . $this->parent_tid . "'");
        if (isset($novalnet_order_details->fields['order_no'])) {
            $order_number = $novalnet_order_details->fields['order_no'];
        }
        if (empty($order_number) && !empty($this->event_data['transaction']['order_no'])) {
            $novalnet_order_details = $db->Execute("SELECT * FROM " . TABLE_NOVALNET_TRANSACTION_DETAIL . " WHERE order_no = '" . $this->event_data['transaction']['order_no'] . "'");
            if (!isset($novalnet_order_details->fields['order_no'])) {
				  $this->displayMessage(['message' => 'Bestellung im Shop nicht gefunden']);
			}
			$order_number = $novalnet_order_details->fields['order_no'];
		}
        //order creation process for redirect payment (redirect time transaction success but not update shop)
        if (empty($order_number) && in_array($this->event_data['result']['status'], array('SUCCESS', 'ON_HOLD'))) {
            if(!empty($this->event_data['transaction']['txn_secret'])){
            $novalnet_get_txn_data = $db->Execute("SELECT * FROM " . TABLE_NOVALNET_TRANSACTION_DETAIL . " WHERE novalnet_txn_secret = '" . $this->event_data['transaction']['txn_secret'] . "'");
            if ($novalnet_get_txn_data->RecordCount() > 0) {
                $order_number = $this->novalnetOrderCreation($novalnet_get_txn_data, $this->event_data);
            } 
        }
        }

        // If order number not found in shop and Novalnet
        if (empty($order_number)) {
            if ($this->event_data['result']['status'] == 'SUCCESS') {
                if ($this->event_data['transaction']['payment_type'] == 'ONLINE_TRANSFER_CREDIT') {
			$this->sentCriticalMail($this->event_data);
            
        }
              $this->displayMessage(['message' => 'Bestellung nicht gefunden für TID '. $this->parent_tid]);
            } else {
                $this->displayMessage(['message' => $this->event_data['result']['status_text']]);
            }
        }
        // If the order number at Novalnet and the shop doesn't match
        if (
            !empty($this->event_data['transaction']['order_no']) && !empty($novalnet_order_details->fields['order_no'])
            && (($this->event_data['transaction']['order_no']) != $novalnet_order_details->fields['order_no'])
        ) {
            $this->displayMessage(['message' => 'Shop Bestellnummer entspricht nicht der Novalnet Bestellnummer '. $this->event_data['transaction']['order_no']]);
        }
        $shop_order_details = $db->Execute("SELECT order_total, orders_id, orders_status, language_code FROM " . TABLE_ORDERS . " WHERE orders_id = '" . $order_number . "'");
        $order_lang = $db->Execute("SELECT directory FROM " . TABLE_LANGUAGES . " WHERE code = '" . $shop_order_details->fields['language_code'] . "'");
        $this->includeRequiredFiles($order_lang->fields['directory']);
        $order_details['nn_trans_details'] = $novalnet_order_details->fields;
        $order_details['shop_order_no'] = $order_number;
        $order_details['shop_order_details'] = $shop_order_details->fields;
        return $order_details;
    }

    /**
     * Handle transaction capture
     *
     */
    private function handleTransactionCapture()
    {
        $novalnet_update_data = [
            'status' => $this->event_data['transaction']['status'],
        ];

        $order_status = NovalnetHelper::getOrderStatus($this->event_data['transaction']['status'], $this->event_data['transaction']['payment_type']);
        if (!empty($this->event_data['transaction']['due_date'])) {
            $comments = sprintf(MODULE_PAYMENT_NOVALNET_TRANS_CONFIRM_SUCCESSFUL_MESSAGE, $this->event_data['transaction']['tid'], date('d.m.Y', strtotime($this->event_data['transaction']['due_date']))) . PHP_EOL;
        } else {
            $comments = sprintf(MODULE_PAYMENT_NOVALNET_TRANS_CONFIRM_SUCCESSFUL_MESSAGE_TEXT, gmdate('d.m.Y')) . PHP_EOL;
        }
        if (!empty($this->event_data['instalment']['cycle_dates'])) {
            $total_amount = ((isset($this->order_details['nn_trans_details']['amount']) ? $this->order_details['nn_trans_details']['amount'] : 0) < $this->event_data['transaction']['amount']) ? $this->event_data['transaction']['amount'] : $this->order_details['nn_trans_details']['amount'];
            $novalnet_update_data['instalment_cycle_details'] = NovalnetHelper::storeInstalmentdetails($this->event_data, $total_amount);
        }
        $order_comment = $comments;
        $order_comment .= NovalnetHelper::getTransactionDetails($this->event_data);
        $this->event_data['transaction']['bank_details'] = empty($this->event_data['transaction']['bank_details']) ? (!empty($this->order_details['nn_trans_details']['payment_details']) ? json_decode($this->order_details['nn_trans_details']['payment_details'], true) : []) : $this->event_data['transaction']['bank_details'];
        if (!empty($this->event_data['transaction']['bank_details'])) {
            $order_comment .= NovalnetHelper::getBankDetails($this->event_data);
        }
        if (!empty($this->event_data['instalment'])) {
            $order_comment .= NovalnetHelper::getInstalmentDetails($this->event_data);
        }
        $this->updateOrderStatusHistory($this->event_data['transaction']['order_no'], $order_status, $order_comment);
        if (in_array($this->event_data['transaction']['payment_type'], array('INSTALMENT_INVOICE', 'GUARANTEED_INVOICE')) && $this->event_data['transaction']['status'] == 'CONFIRMED') {
            NovalnetHelper::sendPaymentConfirmationMail($order_comment, $this->event_data['transaction']['order_no']);
        }
        $this->updateNovalnetTransaction($novalnet_update_data, "tid='{$this->parent_tid}'");
        $comments .= $this->nnDetails($this->parent_tid);
        $this->sendWebhookMail($comments, MODULE_PAYMENT_NOVALNET_WEBHOOK_MAIL_CAPTURE_SUBJECT);
        $this->displayMessage(['message' => zen_db_prepare_input($comments . PHP_EOL)]);
    }

    /**
     * Handle transaction cancel
     *
     */
    private function handleTransactionCancel()
    {
        $comments = sprintf(MODULE_PAYMENT_NOVALNET_TRANS_DEACTIVATED_MESSAGE, gmdate('d.m.Y'), gmdate('H:i:s'));
        $novalnet_update_data = [
            'status' => $this->event_data['transaction']['status'],
        ];
        $this->updateNovalnetTransaction($novalnet_update_data, "tid = '{$this->parent_tid}'");
        $this->updateOrderStatusHistory($this->event_data['transaction']['order_no'], NovalnetHelper::getOrderStatusId(), $comments);
        $comments .= $this->nnDetails($this->parent_tid);
        $this->sendWebhookMail($comments, MODULE_PAYMENT_NOVALNET_WEBHOOK_MAIL_CANCEL_SUBJECT);
        $this->displayMessage(['message' => zen_db_prepare_input($comments . PHP_EOL)]);
    }


    /**
     * Handle transaction refund
     *
     */
    private function handleTransactionRefund()
    {
        global $currencies;
        $order_status_id = '';
        $refund_amount = 0;
        $comments = sprintf(MODULE_PAYMENT_NOVALNET_REFUND_PARENT_TID_MSG, $this->parent_tid, $currencies->format(($this->event_data['transaction']['refund']['amount'] / 100), 1, $this->event_data['transaction']['currency']));
        if (!empty($this->event_data['transaction']['refund']['tid'])) {
            $comments .= sprintf(MODULE_PAYMENT_NOVALNET_REFUND_CHILD_TID_MSG, $this->event_data['transaction']['refund']['tid']);
        }
        $refund_amount = $this->event_data['transaction']['refund']['amount'];
        $refunded_amount = (isset($this->order_details['nn_trans_details']['refund_amount']) ? $this->order_details['nn_trans_details']['refund_amount'] : 0) + $refund_amount;
        $novalnet_update_data = array(
            'refund_amount' => $refunded_amount,
            'status' => $this->event_data['transaction']['status'],
        );
        if (in_array($this->event_data['transaction']['refund']['payment_type'], array('INSTALMENT_INVOICE_BOOKBACK', 'INSTALMENT_SEPA_BOOKBACK'))) {
            $instalment_details = (!empty($this->order_details['nn_trans_details']['instalment_cycle_details'])) ? json_decode($this->order_details['nn_trans_details']['instalment_cycle_details'], true) : [];
            if (!empty($instalment_details)) {
                foreach ($instalment_details as $cycle => $cycle_details) {
                    if (!empty($cycle_details['reference_tid']) && ($cycle_details['reference_tid'] == $this->event_data['transaction']['tid'])) {
                        $instalment_amount = (strpos((string) $instalment_details[$cycle]['instalment_cycle_amount'], '.')) ? $instalment_details[$cycle]['instalment_cycle_amount'] * 100 : $instalment_details[$cycle]['instalment_cycle_amount'];
                        $instalment_amount = $instalment_amount - $refund_amount;
                        $instalment_details[$cycle]['instalment_cycle_amount'] = $instalment_amount;
                        if ($instalment_details[$cycle]['instalment_cycle_amount'] <= 0) {
                            $instalment_details[$cycle]['status'] = 'Refunded';
                        }
                    }
                }
            }
            $novalnet_update_data['instalment_cycle_details'] = (!empty($instalment_details) ? json_encode($instalment_details) : '{}');
        }
        if (isset($this->order_details['nn_trans_details']['amount']) && $refunded_amount >= $this->order_details['nn_trans_details']['amount']) {
            $order_status_id = NovalnetHelper::getOrderStatusId();
        }

        $this->updateNovalnetTransaction($novalnet_update_data, "tid='{$this->order_details['nn_trans_details']['tid']}'");
        $this->updateOrderStatusHistory($this->order_details['shop_order_no'], $order_status_id, $comments);
        $tid = !empty($this->event_data['transaction']['refund']['tid']) ? $this->event_data['transaction']['refund']['tid'] : $this->parent_tid;
        $comments .= PHP_EOL;
        $comments .= $this->nnDetails($tid);
        $this->sendWebhookMail($comments, MODULE_PAYMENT_NOVALNET_WEBHOOK_MAIL_REFUND_SUBJECT);
        $this->displayMessage(['message' => zen_db_prepare_input($comments . PHP_EOL)]);
    }

    /**
     * Handle chargeback
     *
     */
    private function handleTransactionCredit()
    {
        global $db, $currencies;
        $update_comments = true;
        $order_status = '';
        $comments = PHP_EOL . sprintf(NOVALNET_WEBHOOK_CREDIT_NOTE, $this->parent_tid, $currencies->format(($this->event_data['transaction']['amount'] / 100), 1, $this->event_data['transaction']['currency']), gmdate('d.m.Y H:i:s'), $this->event_tid);
        if (in_array($this->event_data['transaction']['payment_type'], ['INVOICE_CREDIT', 'CASHPAYMENT_CREDIT', 'MULTIBANCO_CREDIT'])) {
            $paid_amount = (!empty($this->order_details['nn_trans_details']['refund_amount'])) ? ((int) $this->order_details['nn_trans_details']['refund_amount'] + (int) $this->order_details['nn_trans_details']['callback_amount']) : $this->order_details['nn_trans_details']['callback_amount'];
            if ($paid_amount < $this->order_details['nn_trans_details']['amount']) {
                $total_paid_amount = $paid_amount + $this->event_data['transaction']['amount'];
                $update_data = array(
                    'callback_amount' => $total_paid_amount
                );

                if ($total_paid_amount >= $this->order_details['nn_trans_details']['amount']) { // Full amount paid
                    $order_status = ($this->order_details['nn_trans_details']['payment_type'] == 'INVOICE') ? 3 : 2;
                } else { // Partial paid
                    $order_status = ($this->order_details['nn_trans_details']['payment_type'] == 'INVOICE') ? 2 : 1;
                }
                $update_data['status'] = $this->event_data['transaction']['status'];
                $this->updateNovalnetTransaction($update_data, "tid='{$this->parent_tid}'");
            } else {
                $update_comments = false;
                $comments = sprintf(('Callback script executed already'), gmdate('d.m.Y'), gmdate('H:i:s'));
            }
        }
        if ($update_comments) {
            $this->updateOrderStatusHistory($this->order_details['shop_order_no'], $order_status, $comments);
            $tid = !empty($this->event_tid) ? $this->event_tid : $this->parent_tid;
            $comments .= PHP_EOL;
            $comments .= $this->nnDetails($tid);
            $this->sendWebhookMail($comments, MODULE_PAYMENT_NOVALNET_WEBHOOK_MAIL_CREDIT_SUBJECT);
        }
        $this->displayMessage(['message' => zen_db_prepare_input($comments . PHP_EOL)]);
    }

    /**
     * Handle chargeback
     *
     */
    private function handleChargeback()
    {
        global $currencies;
        $comments = '';
        if (($this->order_details['nn_trans_details']['status'] == 'CONFIRMED') && !empty($this->event_data['transaction']['amount'])) {
            $comments = sprintf(NOVALNET_WEBHOOK_CHARGEBACK_NOTE, $this->parent_tid, $currencies->format(($this->event_data['transaction']['amount'] / 100), 1, $this->event_data['transaction']['currency']), gmdate('d.m.Y'), gmdate('H:i:s'), $this->event_tid);
            $this->updateOrderStatusHistory($this->event_data['transaction']['order_no'], '', $comments);
            $tid = !empty($this->event_tid) ? $this->event_tid : $this->parent_tid;
            $comments .= PHP_EOL . $this->nnDetails($tid);
            $this->sendWebhookMail($comments, MODULE_PAYMENT_NOVALNET_WEBHOOK_MAIL_CHARGEBACK_SUBJECT);
            $this->displayMessage(['message' => zen_db_prepare_input($comments . PHP_EOL)]);
        }
    }

    /**
     * Handle instalment
     *
     */
    private function handleInstalment()
    {
        global $currencies;
        $comment = '';
        if ($this->event_data['transaction']['status'] == 'CONFIRMED' && !empty($this->event_data['instalment']['cycles_executed'])) {
            $instalment_details = (!empty($this->order_details['nn_trans_details']['instalment_cycle_details'])) ? json_decode($this->order_details['nn_trans_details']['instalment_cycle_details'], true) : [];
            $instalment = $this->event_data['instalment'];
            $cycle_index = $instalment['cycles_executed'] - 1;
            if (!empty($instalment)) {
                $instalment_details[$cycle_index]['next_instalment_date'] = (!empty($instalment['next_cycle_date'])) ? $instalment['next_cycle_date'] : '-';
                if (!empty($this->event_data['transaction']['tid'])) {
                    $instalment_details[$cycle_index]['reference_tid'] = $this->event_data['transaction']['tid'];
                    $instalment_details[$cycle_index]['status'] = 'Paid';
                    $instalment_details[$cycle_index]['paid_date'] = date('d.m.Y H:i:s');
                }
            }
            if (empty($this->event_data['transaction']['bank_details'])) {
                $this->event_data['transaction']['bank_details'] = !empty($this->order_details['nn_trans_details']['payment_details']) ? json_decode($this->order_details['nn_trans_details']['payment_details'], true) : [];
            }
            $comment = sprintf(NOVALNET_WEBHOOK_NEW_INSTALMENT_NOTE, $this->parent_tid, $currencies->format(($this->event_data['instalment']['cycle_amount'] / 100), 1, $this->event_data['transaction']['currency']), gmdate('d.m.Y'), $this->event_tid);
            $this->updateNovalnetTransaction(array('instalment_cycle_details' => json_encode($instalment_details)), "tid='{$this->parent_tid}'");
            $tid = !empty($this->event_tid) ? $this->event_tid : $this->parent_tid;
            $comments = $comment . $this->nnDetails($tid);
            $comment .= PHP_EOL . NovalnetHelper::insertTransactionDetails($this->event_data, $this->order_details['shop_order_no']);
            $this->updateOrderStatusHistory($this->event_data['transaction']['order_no'], '', $comment);
            $this->sendWebhookMail($comments, MODULE_PAYMENT_NOVALNET_WEBHOOK_MAIL_INSTALMENT_SUBJECT);
            $this->displayMessage(['message' => zen_db_prepare_input($comment . PHP_EOL)]);
        }
    }

    /**
     * Handle instalment cancel
     *
     */
    private function handleInstalmentCancel()
    {
        global $currencies;
        $comments = '';
        $novalnet_update_data = [];
        if ($this->event_data['transaction']['status'] == 'CONFIRMED') {
            $order_status = '';
            $instalment_details = isset($this->order_details['nn_trans_details']['instalment_cycle_details']) ? json_decode($this->order_details['nn_trans_details']['instalment_cycle_details'], true) : [];
            if (!empty($instalment_details)) {
                $comments = sprintf(MODULE_PAYMENT_NOVALNET_INSTALMENT_CANCEL_ALLCYCLES_TEXT, $this->parent_tid, gmdate('d.m.Y'), $currencies->format(((isset($this->event_data['transaction']['refund']['amount']) ? $this->event_data['transaction']['refund']['amount'] / 100 : 0)), 1, ($this->event_data['transaction']['refund']['currency'])));
                foreach ($instalment_details as $key => $instalment_details_data) {
                    if ($instalment_details_data['status'] == 'Pending') {
                        $instalment_details[$key]['status'] = 'Canceled';
                    }

                    if ($this->event_data['instalment']['cancel_type'] == 'ALL_CYCLES' && $instalment_details_data['status'] == 'Paid') {
                        $instalment_details[$key]['status'] = 'Refunded';
                    }
                }

                if (isset($this->event_data['instalment']['cancel_type']) && $this->event_data['instalment']['cancel_type'] == 'REMAINING_CYCLES') {
                    $comments = sprintf(MODULE_PAYMENT_NOVALNET_INSTALMENT_CANCEL_REMAINING_CYCLES_TEXT, $this->parent_tid, gmdate('d.m.Y'));
                }
            }
            $novalnet_update_data = [
                'instalment_cycle_details' => !empty($instalment_details) ? json_encode($instalment_details) : '{}',
                'status' => 'DEACTIVATED',
            ];
            $this->updateNovalnetTransaction($novalnet_update_data, "tid='{$this->parent_tid}'");
            $this->updateOrderStatusHistory($this->event_data['transaction']['order_no'], $order_status, $comments);
            $comments .= $this->nnDetails($this->parent_tid);
            $this->sendWebhookMail($comments, MODULE_PAYMENT_NOVALNET_WEBHOOK_MAIL_INSTALMENT_CANCEL_SUBJECT);
            $this->displayMessage(['message' => zen_db_prepare_input($comments . PHP_EOL)]);
        }
    }

    /**
     * Handle transaction update
     *
     */
    private function handleTransactionUpdate()
    {
        global $currencies;
        $transaction_comments = '';
        $comments = '';
        $order_status = '';
        $novalnet_update_data = [
            'status' => $this->event_data['transaction']['status'],
        ];
        $amount = (!empty($this->event_data['instalment']['cycle_amount'])) ? $this->event_data['instalment']['cycle_amount'] : $this->event_data['transaction']['amount'];
        if ($this->event_data['transaction']['update_type'] == 'STATUS') {
            if ($this->event_data['transaction']['status'] == 'DEACTIVATED') {
                $transaction_comments = sprintf(MODULE_PAYMENT_NOVALNET_TRANS_DEACTIVATED_MESSAGE, gmdate('d.m.Y'), gmdate('H:i:s'));
                $comments = $transaction_comments . PHP_EOL;
                $comments .= $this->nnDetails($this->parent_tid);
                $order_status = NovalnetHelper::getOrderStatusId();
            } else if (in_array($this->order_details['nn_trans_details']['status'], array('PENDING', 'ON_HOLD'), true)) {
                if ($this->event_data['transaction']['status'] == 'ON_HOLD') {
                    $order_status = 99;
                    $transaction_comments = sprintf(NOVALNET_PAYMENT_STATUS_PENDING_TO_ONHOLD_TEXT, $this->event_tid, gmdate('d.m.Y'), gmdate('H:i:s'));
                    $comments = $transaction_comments . PHP_EOL;
                    $comments .= $this->nnDetails($this->event_tid);
                } elseif ($this->event_data['transaction']['status'] == 'CONFIRMED') {
                    $order_status = 2;
                    if (!empty($this->event_data['transaction']['bank_details']) || !empty($this->event_data['instalment'])) {
                        if (!empty($this->event_data['transaction']['due_date'])) {
                            $transaction_comments = PHP_EOL . sprintf(NOVALNET_WEBHOOK_TRANSACTION_UPDATE_NOTE_DUE_DATE, $this->event_data['transaction']['tid'], $currencies->format(($this->event_data['transaction']['amount'] / 100), 1, $this->event_data['transaction']['currency']), $this->event_data['transaction']['due_date']) . PHP_EOL;
                            $comments = $transaction_comments . PHP_EOL;
                            $comments .= $this->nnDetails($this->event_data['transaction']['tid']);
                        } else {
                            $transaction_comments = PHP_EOL . sprintf(NOVALNET_WEBHOOK_TRANSACTION_UPDATE_NOTE, $this->event_data['transaction']['tid'], $currencies->format(($amount / 100), 1, $this->event_data['transaction']['currency']), gmdate('d.m.Y')) . PHP_EOL;
                            $comments = $transaction_comments . PHP_EOL;
                            $comments .= $this->nnDetails($this->event_data['transaction']['tid']);
                        }
                        if (empty($this->order_details['nn_trans_details']['instalment_cycle_details'])) {
                            $total_amount = ($this->order_details['nn_trans_details']['amount'] < $this->event_data['transaction']['amount']) ? $this->event_data['transaction']['amount'] : $this->order_details['nn_trans_details']['amount'];
                            $novalnet_update_data['instalment_cycle_details'] = NovalnetHelper::storeInstalmentdetails($this->event_data, $total_amount);
                        }
                    } else {
                        $transaction_comments = PHP_EOL . sprintf(NOVALNET_WEBHOOK_TRANSACTION_UPDATE_NOTE, $this->event_data['transaction']['tid'], $currencies->format(($amount / 100), 1, $this->event_data['transaction']['currency']), gmdate('d.m.Y')) . PHP_EOL;
                        $comments = $transaction_comments . PHP_EOL;
                        $comments .= $this->nnDetails($this->event_data['transaction']['tid']);
                    }
                    $novalnet_update_data['callback_amount'] = $this->order_details['nn_trans_details']['amount'];
                }
            }
        } else if ($this->event_data['transaction']['update_type'] == 'AMOUNT') {
            $transaction_comments = PHP_EOL . sprintf(MODULE_PAYMENT_NOVALNET_AMOUNT_UPDATE_NOTE, $currencies->format(($amount / 100), 1, $this->event_data['transaction']['currency']), gmdate('d.m.Y')) . PHP_EOL;
            $comments = $transaction_comments;
            $comments .= $this->nnDetails($this->parent_tid);
        } elseif ($this->event_data['transaction']['update_type'] == 'DUE_DATE') {
            $transaction_comments = PHP_EOL . sprintf(MODULE_PAYMENT_NOVALNET_DUEDATE_UPDATE_NOTE, $this->event_data['transaction']['due_date'], gmdate('d.m.Y')) . PHP_EOL;
            $comments = $transaction_comments . PHP_EOL;
            $comments .= $this->nnDetails($this->parent_tid);
        } elseif ($this->event_data['transaction']['update_type'] == 'AMOUNT_DUE_DATE') {
            $transaction_comments = PHP_EOL . sprintf(MODULE_PAYMENT_NOVALNET_AMOUNT_DUEDATE_UPDATE_NOTE, $currencies->format(($amount / 100), 1, $this->event_data['transaction']['currency']), $this->event_data['transaction']['due_date'], gmdate('d.m.Y')) . PHP_EOL;
            $comments = $transaction_comments . PHP_EOL;
            $comments .= $this->nnDetails($this->parent_tid);
        }

        // Reform the transaction comments.
        $transaction_comments .= NovalnetHelper::getTransactionDetails($this->event_data);
        $this->event_data['transaction']['bank_details'] = empty($this->event_data['transaction']['bank_details']) ? (!empty($this->order_details['nn_trans_details']['payment_details']) ? json_decode($this->order_details['nn_trans_details']['payment_details'], true) : []) : $this->event_data['transaction']['bank_details'];
        if (($this->event_data['transaction']['status'] != 'DEACTIVATED') && !empty($this->event_data['transaction']['bank_details'])) {
            $transaction_comments .= NovalnetHelper::getBankDetails($this->event_data);
        }
        if ($this->event_data['transaction']['payment_type'] === 'CASHPAYMENT') {
            $nn_barzahlen_stores = json_decode($this->order_details['nn_trans_details']['payment_details'], true);
            $this->event_data['transaction']['nearest_stores'] = !empty($nn_barzahlen_stores) ? $nn_barzahlen_stores['nearest_stores'] : [];
            $transaction_comments .= NovalnetHelper::getNearestStoreDetails($this->event_data);
        }
        if (!empty($this->event_data['instalment'])) {
            $transaction_comments .= NovalnetHelper::getInstalmentDetails($this->event_data);
        } else {
            if ((int) $this->event_data['transaction']['amount'] != (int) $this->order_details['nn_trans_details']['amount']) {
                $novalnet_update_data['amount'] = $this->event_data['transaction']['amount'];
                if ($this->event_data['transaction']['status'] === 'CONFIRMED') {
                    $novalnet_update_data['callback_amount'] = $this->event_data['transaction']['amount'];
                }
            }
        }
        if (in_array($this->event_data['transaction']['payment_type'], array('INSTALMENT_INVOICE', 'GUARANTEED_INVOICE')) && in_array($this->event_data['transaction']['status'], array('CONFIRMED', 'ON_HOLD'))) {
            NovalnetHelper::sendPaymentConfirmationMail($transaction_comments, $this->event_data['transaction']['order_no']);
        }
        $this->updateNovalnetTransaction($novalnet_update_data, "tid='{$this->parent_tid}'");
        $this->updateOrderStatusHistory($this->order_details['shop_order_no'], $order_status, $transaction_comments);
        $this->sendWebhookMail($comments, MODULE_PAYMENT_NOVALNET_WEBHOOK_MAIL_TRANS_UPDATE_SUBJECT);
        $this->displayMessage(['message' => zen_db_prepare_input($transaction_comments . PHP_EOL)]);
    }

    /**
     * Handle Payment Reminder
     *
     */
    private function handlePaymentReminder()
    {
        global $db;
        $comments = sprintf(NOVALNET_PAYMENT_REMINDER_NOTE, explode('_', $this->event_type)[2]) . PHP_EOL;
        $this->updateOrderStatusHistory($this->event_data['transaction']['order_no'], '', $comments);
        $comments .= $this->nnDetails($this->parent_tid);
        $this->sendWebhookMail($comments, MODULE_PAYMENT_NOVALNET_WEBHOOK_MAIL_REMINDER_SUBJECT);
        $this->displayMessage(['message' => zen_db_prepare_input($comments . PHP_EOL)]);
    }

    /**
     * Handle Collection Agency Submission
     *
     */
    private function handleCollectionSubmission()
    {
        $comments = sprintf(NOVALNET_COLLECTION_SUBMISSION_NOTE, $this->event_data['collection']['reference']) . PHP_EOL;
        $this->updateOrderStatusHistory($this->event_data['transaction']['order_no'], '', $comments);
        $comments .= $this->nnDetails($this->parent_tid);
        $comments .= MODULE_PAYMENT_NOVALNET_WEBHOOK_MAIL_COLLECTION_STATUS . $this->event_data['collection']['status_text'];
        $this->sendWebhookMail($comments, MODULE_PAYMENT_NOVALNET_WEBHOOK_MAIL_COLLECTION_SUBMISSION_SUBJECT);
        $this->displayMessage(['message' => zen_db_prepare_input($comments . PHP_EOL)]);
    }

    /**
     * Print the Webhook messages.
     *
     * @param $message
     *
     * @return void
     */
    private function displayMessage($message)
    {
        echo json_encode($message);
        exit;
    }

    /**
     * Update the details in Shop order status table.
     *
     * @param $order_no
     * @param $order_status_id
     * @param $comments
     */
    private function updateOrderStatusHistory($order_no, $order_status_id = '', $comments = '')
    {
        global $db;
        if ($order_status_id == '') {
            $current_order_status = $db->Execute("SELECT orders_status FROM " . TABLE_ORDERS . " WHERE orders_id = " . $order_no);
            $order_status_id = $current_order_status->fields['orders_status'];
        }
        $datas_need_to_update['orders_status'] = $order_status_id;
        // Update order status id in orders table
        zen_db_perform(TABLE_ORDERS, $datas_need_to_update, "update", "orders_id = '$order_no'");
        $data_array = array(
            'orders_id' => $order_no,
            'orders_status_id' => $order_status_id,
            'date_added' => date('Y-m-d H:i:s'),
            'customer_notified' => 1,
            'comments' => zen_db_prepare_input($comments . PHP_EOL)
        );
        // Update order details in history table
        zen_db_perform(TABLE_ORDERS_STATUS_HISTORY, $data_array, 'insert');
    }

    private function nnDetails($tid)
    {
        $transaction_details = NovalnetHelper::getNovalnetTransDetails($this->event_data['transaction']['order_no']);
        $payment_type = !empty($transaction_details->fields['payment_type']) ? $transaction_details->fields['payment_type'] : $this->event_data['transaction']['payment_type'];
        $comments = PHP_EOL . MODULE_PAYMENT_NOVALNET_WEBHOOK_MAIL_TRANS_DETAILS_TEXT;
        $comments .= PHP_EOL . MODULE_PAYMENT_NOVALNET_TRANSACTION_ID . $tid . PHP_EOL;
        $comments .= MODULE_PAYMENT_NOVALNET_WEBHOOK_PAYMENT_TYPE . $payment_type . PHP_EOL;
        $comments .= sprintf(MODULE_PAYMENT_NOVALNET_ORDER_NUMBER, $this->event_data['transaction']['order_no']) . PHP_EOL;
        return $comments;
    }
    /*
     * Update the transaction details in Novalnet table
     *
     * @param $data
     * @param $parameters
     */
    private function updateNovalnetTransaction($data, $parameters = '')
    {
        if ($parameters == '') {
            return false;
        }
        zen_db_perform(TABLE_NOVALNET_TRANSACTION_DETAIL, $data, 'update', $parameters);
    }

    /**
     * Send notification mail to Merchant
     *
     * @param $comments
     */
    private function sendWebhookMail($comments, $order_subject)
    {
        $email = NovalnetHelper::validateEmail(MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_TO);
        // Assign email to address
        $email_to = !empty($email) ? $email : STORE_OWNER_EMAIL_ADDRESS;
        // Send mail

        $email_body = sprintf(MODULE_PAYMENT_NOVALNET_MAIL_TEMPLATE_DEAR_TEXT, STORE_OWNER) . PHP_EOL . PHP_EOL . $comments . PHP_EOL . PHP_EOL . MODULE_PAYMENT_NOVALNET_WEBHOOK_MAIL_SUPPORT_TEXT . PHP_EOL . PHP_EOL . MODULE_PAYMENT_NOVALNET_MAIL_TEMPLATE_BEST_REAGRDS . PHP_EOL . MODULE_PAYMENT_NOVALNET_MAIL_TEMPLATE_NOVALNET;

        zen_mail($email, $email_to, STORE_NAME . ' - ' . $order_subject, $email_body, STORE_NAME, EMAIL_FROM);
    }

    /**
     * Include language file and helper file.
     */
    private function includeRequiredFiles($lang)
    {
        // include language
        include_once(DIR_FS_CATALOG . DIR_WS_LANGUAGES . $lang . "/modules/payment/novalnet_payments.php");

        // include helper file after language files.
        require_once(DIR_FS_CATALOG . DIR_WS_MODULES . 'payment/novalnet/NovalnetHelper.php');
        return;
    }

    /**
     * Send critical mail
     *
     * @return none
     */
    private function sentCriticalMail($data)
    {
        global $currencies;
        $this->includeRequiredFiles('german');
        $subject = MODULE_PAYMENT_NOVALNET_WEBHOOK_ACTION_REQUIRED . $data['event']['tid'] . MODULE_PAYMENT_NOVALNET_WEBHOOK_IN . STORE_NAME;
        $message = MODULE_PAYMENT_NOVALNET_WEBHOOK_SALUTATION . STORE_OWNER . ',' . PHP_EOL . PHP_EOL . MODULE_PAYMENT_NOVALNET_WEBHOOK_ATTENTION . PHP_EOL;
        $message .= MODULE_PAYMENT_NOVALNET_WEBHOOK_PROJECT_ID . $data['merchant']['project'] . PHP_EOL;
        $message .= MODULE_PAYMENT_NOVALNET_WEBHOOK_TID . $data['event']['tid'] . PHP_EOL;
        $message .= MODULE_PAYMENT_NOVALNET_WEBHOOK_TID_STATUS . $data['transaction']['status'] . PHP_EOL;
        $message .= MODULE_PAYMENT_NOVALNET_WEBHOOK_PAYMENT_TYPE . $data['transaction']['payment_type'] . PHP_EOL;
        $message .= MODULE_PAYMENT_NOVALNET_AMOUNT . $currencies->format(($data['transaction']['amount'] / 100), 1, $data['transaction']['currency']) . PHP_EOL;
        $message .= MODULE_PAYMENT_NOVALNET_WEBHOOK_EMAIL . $data['customer']['email'] . PHP_EOL;
        $message .= PHP_EOL . MODULE_PAYMENT_NOVALNET_WEBHOOK_COMMUNICATION_PROBLEM . PHP_EOL;
        $message .= PHP_EOL . MODULE_PAYMENT_NOVALNET_WEBHOOK_DISCREPANCIES . PHP_EOL;
        $message .= PHP_EOL . MODULE_PAYMENT_NOVALNET_WEBHOOK_MANUAL_ORDER_CREATION . PHP_EOL;
        $message .= MODULE_PAYMENT_NOVALNET_WEBHOOK_REFUND_INITIATION . PHP_EOL;
        $message .= PHP_EOL . MODULE_PAYMENT_NOVALNET_WEBHOOK_PROMPT_REVIEW . PHP_EOL;
        $message .= PHP_EOL . MODULE_PAYMENT_NOVALNET_WEBHOOK_REGARDS . PHP_EOL . 'Novalnet Team';
        zen_mail(STORE_NAME, STORE_OWNER_EMAIL_ADDRESS, $subject, str_replace('</br>', PHP_EOL, $message), STORE_NAME, EMAIL_FROM, array(), '', '', STORE_NAME, EMAIL_FROM);
    }

    /**
     * Send critical mail
     *
     * @return none
     */
    public function novalnetOrderCreation($stored_datas, $nn_event_data)
    {
        global $zco_notifier, $db;
        $decode_stored_datas = json_decode($stored_datas->fields['novalnet_order_datas'], true);
        $lang = $decode_stored_datas['nn_addtional_info']['lang'] == 'en' ? 'english' : 'german';
        include_once(DIR_FS_CATALOG . 'includes/languages/' . $lang . '/checkout_process.php');
        $_SESSION['language'] = $lang;
        $this->includeRequiredFiles($lang);
        $order_data = $decode_stored_datas['order'];
        $shipping_data = $decode_stored_datas['shipping'];
        $zco_notifier->notify('NOTIFY_CHECKOUT_PROCESS_BEGIN');
        global $payment_modules;
        // load selected payment module
        require(DIR_WS_CLASSES . 'payment.php');
        $_SESSION['payment'] = $order_data['info']['payment_module_code'];
        $payment_modules = new payment($_SESSION['payment']);

        require(DIR_WS_CLASSES . 'order.php');
        global $order;
        $order = new order;
        $order->billing = $order_data['billing'];
        $order->content_type = $order_data['content_type'];
        $order->customer = $order_data['customer'];
        $order->delivery = $order_data['delivery'];
        $order->products = $order_data['products'];
        $order->info = $order_data['info'];

        require(DIR_WS_CLASSES . 'shipping.php');
        $_SESSION['shipping'] = $shipping_data;
        $shipping_modules = new shipping($_SESSION['shipping']);

        require(DIR_WS_CLASSES . 'order_total.php');
        global $order_total_modules;
        $order_total_modules = new order_total;
        $zco_notifier->notify('NOTIFY_CHECKOUT_PROCESS_BEFORE_ORDER_TOTALS_PROCESS');
        global $order_totals;
        $order_totals = $order_total_modules->process();
        $zco_notifier->notify('NOTIFY_CHECKOUT_PROCESS_AFTER_ORDER_TOTALS_PROCESS');
        $_SESSION['customer_id'] = $nn_event_data['customer']['customer_no'];

        if (isset($GLOBALS[$_SESSION['payment']]->order_status) && ((int) $GLOBALS[$_SESSION['payment']]->order_status) > 0) {
            $order->info['order_status'] = (int) $GLOBALS[$_SESSION['payment']]->order_status;
        }
        global $insert_id;
        $insert_id = $order->create($order_totals);
        $zco_notifier->notify('NOTIFY_CHECKOUT_PROCESS_AFTER_ORDER_CREATE', $insert_id);
        $zco_notifier->notify('NOTIFY_CHECKOUT_PROCESS_AFTER_PAYMENT_MODULES_AFTER_ORDER_CREATE', $insert_id);
        if (!empty($decode_stored_datas['nn_addtional_info']['payment_action']) && $decode_stored_datas['nn_addtional_info']['payment_action'] == 'zero_amount') {
            $_SESSION['nn_booking_details'] = new stdClass();
            $_SESSION['nn_booking_details']->payment_action = 'zero_amount';
        }
        $comments = NovalnetHelper::getTransactionDetails($nn_event_data);
        $status = NovalnetHelper::getOrderStatus($nn_event_data['transaction']['status'], $nn_event_data['transaction']['payment_type']);
        $_SESSION['nn_response']['transaction']['tid'] = $nn_event_data['event']['tid'];
        NovalnetHelper::sendTransactionUpdate($insert_id);
        NovalnetHelper::updateOrderStatus($insert_id, $comments, $nn_event_data, $nn_event_data['transaction']['txn_secret'], $decode_stored_datas['nn_addtional_info']['lang']);

        $order->create_add_products($insert_id);
        $_SESSION['order_number_created'] = $insert_id;
        $zco_notifier->notify('NOTIFY_CHECKOUT_PROCESS_AFTER_ORDER_CREATE_ADD_PRODUCTS', $insert_id, $order);
        $_SESSION['billto'] = (int) $decode_stored_datas['nn_addtional_info']['billto'];
        $_SESSION['sendto'] = (int) $decode_stored_datas['nn_addtional_info']['sendto'];
        $_SESSION['customer_id'] = $nn_event_data['customer']['customer_no'];
        $GLOBALS[$_SESSION['payment']]->title = $decode_stored_datas['order']['info']['payment_method'];
        $order->send_order_email($insert_id);
        $zco_notifier->notify('NOTIFY_CHECKOUT_PROCESS_AFTER_SEND_ORDER_EMAIL', $insert_id, $order);

        $oshipping = $otax = $ototal = $order_subtotal = $credits_applied = 0;
        for ($i = 0, $n = sizeof($order_totals); $i < $n; $i++) {
            if ($order_totals[$i]['code'] == 'ot_subtotal')
                $order_subtotal = $order_totals[$i]['value'];
            if (!empty(${$order_totals[$i]['code']}->credit_class))
                $credits_applied += $order_totals[$i]['value'];
            if ($order_totals[$i]['code'] == 'ot_total')
                $ototal = $order_totals[$i]['value'];
            if ($order_totals[$i]['code'] == 'ot_tax')
                $otax = $order_totals[$i]['value'];
            if ($order_totals[$i]['code'] == 'ot_shipping')
                $oshipping = $order_totals[$i]['value'];
        }
        global $currencies;
        $commissionable_order = ($order_subtotal - $credits_applied);
        $commissionable_order_formatted = $currencies->format($commissionable_order);
        $_SESSION['order_summary']['order_number'] = $insert_id;
        $_SESSION['order_summary']['order_subtotal'] = $order_subtotal;
        $_SESSION['order_summary']['credits_applied'] = $credits_applied;
        $_SESSION['order_summary']['order_total'] = $ototal;
        $_SESSION['order_summary']['commissionable_order'] = $commissionable_order;
        $_SESSION['order_summary']['commissionable_order_formatted'] = $commissionable_order_formatted;
        $_SESSION['order_summary']['coupon_code'] = urlencode($order->info['coupon_code']);
        $_SESSION['order_summary']['currency_code'] = $order->info['currency'];
        $_SESSION['order_summary']['currency_value'] = $order->info['currency_value'];
        $_SESSION['order_summary']['payment_module_code'] = $order->info['payment_module_code'];
        $_SESSION['order_summary']['shipping_method'] = $order->info['shipping_method'];
        $_SESSION['order_summary']['order_status'] = $order->info['order_status'];
        $_SESSION['order_summary']['orders_status'] = $order->info['order_status']; // alias for older versions
        $_SESSION['order_summary']['tax'] = $otax;
        $_SESSION['order_summary']['shipping'] = $oshipping;
        $products_array = array();
        foreach ($order->products as $key => $val) {
            $products_array[urlencode($val['id'])] = urlencode($val['model']);
        }
        $_SESSION['order_summary']['products_ordered_ids'] = implode('|', array_keys($products_array));
        $_SESSION['order_summary']['products_ordered_models'] = implode('|', array_values($products_array));
        $zco_notifier->notify('NOTIFY_CHECKOUT_PROCESS_HANDLE_AFFILIATES');
        unset($_SESSION['billto'], $_SESSION['sendto'], $_SESSION['customer_id']);
        unset($_SESSION['language']);
        return $insert_id;
    }

}

new NovalnetWebhooks();

