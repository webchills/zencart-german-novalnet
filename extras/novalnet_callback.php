<?php
/**
* This script is used for handling callback details
*
* @author Novalnet AG
* @copyright Copyright (c) Novalnet
* @license https://www.novalnet.de/payment-plugins/kostenlos/lizenz
* @link https://www.novalnet.de
*
* This free contribution made by request.
*
* If you have found this script useful a small
* recommendation as well as a comment on merchant
*
* Script : novalnet_callback.php
*/
chdir('../');
require('includes/application_top.php');
include_once(DIR_FS_CATALOG . DIR_WS_CLASSES . 'class.novalnetutil.php');
$server_request    = array_map('trim', $_REQUEST);

new NovalnetVendorScript($server_request);

class NovalnetVendorScript {

    /**
     * Request parameters.
     *
     * @var array
     */
    protected $server_request = array();

    /**
     * Order reference values.
     *
     * @var array
     */
    protected $order_reference = array();

    /**
     * Level - 0 Payment types.
     *
     * @var array
     */
    protected $payments = array(
        'CREDITCARD',
        'INVOICE_START',
        'DIRECT_DEBIT_SEPA',
        'GUARANTEED_INVOICE',
        'GUARANTEED_DIRECT_DEBIT_SEPA',
        'PAYPAL',
        'PRZELEWY24',
        'ONLINE_TRANSFER',
        'IDEAL',
        'GIROPAY',
        'EPS',
        'CASHPAYMENT',
        'INSTALMENT_INVOICE',
        'INSTALMENT_DIRECT_DEBIT_SEPA',
        'POSTFINANCE_CARD',
        'POSTFINANCE'
    );

    /**
     * Level - 1 Payment types.
     *
     * @var array
     */
    protected $chargebacks = array(
        'RETURN_DEBIT_SEPA',
        'REVERSAL',
        'CREDITCARD_BOOKBACK',
        'CREDITCARD_CHARGEBACK',
        'PAYPAL_BOOKBACK',
        'REFUND_BY_BANK_TRANSFER_EU',
        'PRZELEWY24_REFUND',
        'CASHPAYMENT_REFUND',
        'POSTFINANCE_REFUND',
        'GUARANTEED_INVOICE_BOOKBACK',
        'GUARANTEED_SEPA_BOOKBACK',
        'INSTALMENT_SEPA_BOOKBACK',
        'INSTALMENT_INVOICE_BOOKBACK',
    );

    /**
     * Level - 2 Payment types.
     *
     * @var array
     */
    protected $collections = array(
        'INVOICE_CREDIT',
        'CREDIT_ENTRY_CREDITCARD',
        'CREDIT_ENTRY_SEPA',
        'CREDIT_ENTRY_DE',
        'DEBT_COLLECTION_SEPA',
        'DEBT_COLLECTION_DE',
        'DEBT_COLLECTION_CREDITCARD',
        'ONLINE_TRANSFER_CREDIT',
        'CASHPAYMENT_CREDIT',
    );

    /**
     * Novalnet Transaction Cancellation catagory.
     *
     * @var array
     */
    protected $cancellation = array(
        'TRANSACTION_CANCELLATION',
    );

    /**
     * Novalnet payments catagory.
     *
     * @var array
     */
    protected $payment_groups = array(
        'novalnet_cc' => array('CREDITCARD','CREDITCARD_BOOKBACK','CREDITCARD_CHARGEBACK','CREDIT_ENTRY_CREDITCARD','DEBT_COLLECTION_CREDITCARD','TRANSACTION_CANCELLATION'),
        'novalnet_sepa' => array('DIRECT_DEBIT_SEPA','RETURN_DEBIT_SEPA','CREDIT_ENTRY_SEPA','DEBT_COLLECTION_SEPA','REFUND_BY_BANK_TRANSFER_EU','TRANSACTION_CANCELLATION'),
        'novalnet_guarantee_sepa' => array('RETURN_DEBIT_SEPA','GUARANTEED_DIRECT_DEBIT_SEPA','GUARANTEED_SEPA_BOOKBACK','REFUND_BY_BANK_TRANSFER_EU','TRANSACTION_CANCELLATION'),
        'novalnet_ideal'=> array('IDEAL','REFUND_BY_BANK_TRANSFER_EU','ONLINE_TRANSFER_CREDIT','REVERSAL','CREDIT_ENTRY_DE','DEBT_COLLECTION_DE'),
        'novalnet_eps' => array('EPS','REFUND_BY_BANK_TRANSFER_EU','ONLINE_TRANSFER_CREDIT','REVERSAL','CREDIT_ENTRY_DE','DEBT_COLLECTION_DE'),
        'novalnet_giropay' => array('GIROPAY','REFUND_BY_BANK_TRANSFER_EU','ONLINE_TRANSFER_CREDIT','REVERSAL','CREDIT_ENTRY_DE','DEBT_COLLECTION_DE'),
        'novalnet_banktransfer' => array('ONLINE_TRANSFER','REFUND_BY_BANK_TRANSFER_EU','ONLINE_TRANSFER_CREDIT','REVERSAL','CREDIT_ENTRY_DE','DEBT_COLLECTION_DE'),
        'novalnet_PayPal'  => array('PAYPAL','PAYPAL_BOOKBACK','TRANSACTION_CANCELLATION'),
        'novalnet_prepayment' => array('INVOICE_START','INVOICE_CREDIT','REFUND_BY_BANK_TRANSFER_EU'),
        'novalnet_invoice' => array('INVOICE_START','INVOICE_CREDIT','REFUND_BY_BANK_TRANSFER_EU','TRANSACTION_CANCELLATION','CREDIT_ENTRY_DE','DEBT_COLLECTION_DE'),
        'novalnet_guarantee_invoice'  => array('GUARANTEED_INVOICE','GUARANTEED_INVOICE_BOOKBACK','REFUND_BY_BANK_TRANSFER_EU','TRANSACTION_CANCELLATION'),
        'novalnet_przelewy24'  => array('PRZELEWY24','PRZELEWY24_REFUND'),
        'novalnet_cashpayment'  => array('CASHPAYMENT','CASHPAYMENT_REFUND','CASHPAYMENT_CREDIT'),
        'novalnet_instalment_invoice' => array('INSTALMENT_INVOICE','INSTALMENT_INVOICE_BOOKBACK','TRANSACTION_CANCELLATION'),
        'novalnet_instalment_sepa'  => array('INSTALMENT_DIRECT_DEBIT_SEPA','INSTALMENT_SEPA_BOOKBACK',
        'TRANSACTION_CANCELLATION'),
        'novalnet_postfinance' => array('POSTFINANCE', 'POSTFINANCE_REFUND'),
        'novalnet_postfinance_card' => array('POSTFINANCE_CARD', 'POSTFINANCE_REFUND'),
    );

    /**
     * Mandatory Parameters.
     *
     * @var array
     */
    protected $required_params = array(
        'vendor_id',
        'status',
        'payment_type',
        'tid_status',
        'tid',
    );

    /**
     * Novalnet success codes.
     *
     * @var array
     */
    protected $success_code = array(
        'PAYPAL'  => array('100', '90','85'),
        'INVOICE_START' => array('100','91'),
        'INSTALMENT_INVOICE'  => array('100','91','75'),
        'GUARANTEED_INVOICE'  => array('100','91','75'),
        'CREDITCARD' => array('100','98'),
        'DIRECT_DEBIT_SEPA' => array('100','99'),
        'INSTALMENT_DIRECT_DEBIT_SEPA' => array('100','99','75'),
        'GUARANTEED_DIRECT_DEBIT_SEPA' => array('100','99','75'),
        'ONLINE_TRANSFER' => array('100'),
        'ONLINE_TRANSFER_CREDIT' => array('100'),
        'GIROPAY'  => array('100'),
        'IDEAL'   => array('100'),
        'EPS'   => array('100'),
        'PRZELEWY24'  => array('100','86'),
        'CASHPAYMENT' => array('100'),
        'POSTFINANCE_CARD' => array('100','83'),
        'POSTFINANCE' => array('100','83'),
    );

    /**
     * construct
     *
     * @return none
     */
    public function __construct($request)
    {
        $this->validate_ipaddress();

        $this->server_request = $this->validate_server_request($request);

        $this->order_reference = $this->get_order_reference();
        include_once( DIR_FS_CATALOG . DIR_WS_INCLUDES .'languages/' . $this->order_reference['nn_order_lang'] . '/modules/payment/novalnet.php');
        $this->transaction_cancellation($this->order_reference);

        $payment_type_level = $this->get_payment_type();

        $this->formatted_amount = sprintf('%0.2f', $this->server_request['amount'] / 100) .' '. $this->server_request['currency'];

        switch($payment_type_level) {
            case 0:
              $this->zero_level_process();
              break;
            case 1:
              $this->first_level_process();
              break;
            case 2:
              $this->second_level_process();
              break;
            default:
              $this->display_message('Novalnet Callbackscript received. Payment type ( ' . $this->server_request['payment_type'] . ' ) is not applicable for this process!');
              break;
        }
        $this->display_message(($this->server_request['tid_status'] != '100' || $this->server_request['status'] != '100') ? 'Novalnet callback received. Status is not valid.' : 'Novalnet callback received. Callback Script executed already.');
    }

    /**
     * Payment types of Level 0 - Initial level payments processing
     *
     * @return none
     */
    public function zero_level_process()
    {
       global $db;

       if (in_array($this->server_request['payment_type'], $this->payments, true ) && $this->server_request['status'] == '100' && in_array($this->server_request['tid_status'], $this->success_code[$this->server_request['payment_type']], true)) {
          if (in_array( $this->server_request['payment_type'], array('INSTALMENT_INVOICE', 'INSTALMENT_DIRECT_DEBIT_SEPA')) && isset($this->server_request['instalment_billing']) && $this->server_request['instalment_billing'] == '1' && $this->server_request['tid_status'] == '100' && $this->server_request['status'] == '100' ) {
            $comments = NovalnetUtil::formPaymentComments($this->server_request['tid'], $this->server_request['test_mode']);
            $amount = sprintf('%0.2f', $this->server_request['amount'] / 100);
            if ($this->server_request['payment_type'] == 'INSTALMENT_INVOICE') {
                $comments .= PHP_EOL . PHP_EOL . NovalnetUtil::formInvoicePrepaymentComments($this->server_request, $amount);
                $comments .=  PHP_EOL . NovalnetUtil::novalnetReferenceComments($this->server_request['order_no'],$this->server_request, $this->order_reference['payment_type']);
            }
            if ($this->server_request['payment_type'] == 'INSTALMENT_DIRECT_DEBIT_SEPA') {
                $comments .= PHP_EOL.PHP_EOL . sprintf(MODULE_PAYMENT_NOVALNET_INSTALMENT_SEPA_INFO, $amount, $this->server_request['currency']);
            }

            $comments .= PHP_EOL . MODULE_PAYMENT_NOVALNET_INSTALMENT_INFO;
            $comments .= PHP_EOL . MODULE_PAYMENT_NOVALNET_INSTALMENT_PROCESSED . (!empty($this->server_request['instalment_cycles_executed']) ? $this->server_request['instalment_cycles_executed']  : '') ;
            $comments .= PHP_EOL . MODULE_PAYMENT_NOVALNET_INSTALMENT_DUE . (isset($this->server_request['due_instalment_cycles']) ? $this->server_request['due_instalment_cycles'] : '');

            $amount = (!empty($this->server_request['instalment_cycle_amount']) ? $this->server_request['instalment_cycle_amount'] :  $this->server_request['amount']);
            $amount = sprintf('%0.2f', $amount / 100);

            $comments .= PHP_EOL . MODULE_PAYMENT_NOVALNET_INSTALMENT_NXT_AMOUNT .$amount. ' '.$this->server_request['currency'] ;

            $instalment = unserialize($this->order_reference['instalment_details']);

            $instalment[$this->server_request['instalment_cycles_executed']] = array(
                'amount' => ($this->server_request['amount']) ? sprintf('%0.2f', $this->server_request['amount'] / 100) : '',
                'nextCycle' => ($this->server_request['next_instalment_date']) ? $this->server_request['next_instalment_date'] : '',
                'paidDate' => ($this->server_request['tid_status'] == '100') ? date('Y-m-d') : '',
                'status' => 'Paid',
                'reference' => !empty($this->server_request['tid']) ? $this->server_request['tid'] : ''
            );

              $instalment_details = serialize($instalment);

              $param['gateway_status'] = $this->server_request['tid_status'];
              $param['instalment_details'] = serialize($instalment);
             zen_db_perform('novalnet_transaction_detail', $param, "update", "tid='" . $this->server_request['shop_tid'] . "'");

            $payment_name = strtoupper($this->order_reference['payment_type']);

            $order_status = NovalnetUtil::checkDefaultOrderStatus(constant('MODULE_PAYMENT_' . $payment_name . '_ORDER_STATUS_ID'));

            $db->Execute("UPDATE " . TABLE_ORDERS . " SET orders_status= " . $order_status . " where orders_id=" . $this->order_reference['order_no']);

            $this->update_final_comments($this->server_request, $comments, $order_status, $this->order_reference['order_no']);
            }
            $this->update_pending_payments();

            // After execution.
            $this->display_message('Novalnet Callbackscript received. Payment type ( ' . $this->server_request['payment_type'] . ' ) is not applicable for this process!');
        }
    }

    /**
     * Payment types of Level 1 - Chargeback payments processing
     *
     * @return none
     */
    public function first_level_process()
    {
        if (in_array( $this->server_request['payment_type'], $this->chargebacks, true ) && $this->server_request['tid_status'] == '100' && $this->server_request['status'] == '100') {
            $comments = MODULE_PAYMENT_NOVALNET_CALLBACK_CHARGEBACK;
            if ( in_array( $this->server_request['payment_type'], array( 'PAYPAL_BOOKBACK', 'CREDITCARD_BOOKBACK', 'REFUND_BY_BANK_TRANSFER_EU', 'PRZELEWY24_REFUND', 'POSTFINANCE_REFUND', 'CASHPAYMENT_REFUND', 'GUARANTEED_INVOICE_BOOKBACK', 'GUARANTEED_SEPA_BOOKBACK', 'INSTALMENT_SEPA_BOOKBACK', 'INSTALMENT_INVOICE_BOOKBACK' ), true ) ) {
                $comments = MODULE_PAYMENT_NOVALNET_CALLBACK_BOOKBACK;
            }
            $callback_comments = sprintf($comments, $this->server_request['shop_tid'], $this->formatted_amount, date('Y-m-d H:i:s'), $this->server_request['tid']) . PHP_EOL;
            //Update the comments , order id and status id in Novalnet table
            $this->update_final_comments($this->server_request, $callback_comments, $this->order_reference['order_current_status'], $this->order_reference['order_no']);
        }
    }

    /**
     * Payment types of Level 2 - Credit entry and collection payments processing
     *
     * @return none
     */
    public function second_level_process()
    {
        if (in_array($this->server_request['payment_type'], $this->collections) && $this->server_request['tid_status'] == '100' && $this->server_request['status'] == '100') {

          if (in_array($this->server_request['payment_type'], array('INVOICE_CREDIT', 'ONLINE_TRANSFER_CREDIT', 'CASHPAYMENT_CREDIT'))) {

            if ($this->order_reference['order_paid_amount'] < $this->order_reference['order_total_amount']) {
                $callback_comments = sprintf(MODULE_PAYMENT_NOVALNET_CALLBACK_CREDIT, $this->server_request['shop_tid'], $this->formatted_amount, date('Y-m-d H:i:s'), $this->server_request['tid']);

                $callback_status_id = NovalnetUtil::checkDefaultOrderStatus(constant('MODULE_PAYMENT_'.strtoupper($this->order_reference['payment_type']).'_ORDER_STATUS_ID'));

                $total_amount = $this->order_reference['order_paid_amount'] + $this->server_request['amount'];
                 if ($this->order_reference['order_total_amount'] <= $total_amount) {
                    $callback_status_id = NovalnetUtil::checkDefaultOrderStatus(constant('MODULE_PAYMENT_'.strtoupper($this->order_reference['payment_type']).'_CALLBACK_STATUS_ID'));
                    $callback_comments .= ($this->order_reference['order_total_amount'] < $total_amount) ? ' Paid amount is greater than Order amount.' : '';
                }

                //Update callback order status due to full payment
                zen_db_perform(TABLE_ORDERS, array(
                    'orders_status' => $callback_status_id
                ), 'update', 'orders_id="' . $this->order_reference['order_no'] . '"');

                $this->update_final_comments($this->server_request, $callback_comments, $callback_status_id, $this->order_reference['order_no'], $total_amount);
              }
                $this->display_message('Novalnet callback script executed already');
            } else {
                $callback_comments = sprintf(MODULE_PAYMENT_NOVALNET_CALLBACK_CREDIT, $this->server_request['shop_tid'], $this->formatted_amount, date('Y-m-d H:i:s'), $this->server_request['tid']);

                $callback_status_id = $this->order_reference['order_current_status'];

                $this->update_final_comments($this->server_request, $callback_comments, $callback_status_id, $this->order_reference['order_no']);
            }
            $this->display_message('Novalnet Callbackscript received. Payment type ( ' . $this->server_request['payment_type'] . ' ) is not applicable for this process!' );
        }
    }

    /**
     * Update pending payments.
     *
     * @return none
     */
    public function update_pending_payments()
    {
        global $db;
        if (isset($this->order_reference['gateway_status'] ) && $this->order_reference['gateway_status'] != 100 ) {
            $comments = $callback_comments = '';
            $param = array();
            $paymentName = strtoupper($this->order_reference['payment_type']);

            if ($this->server_request['payment_type'] =='PAYPAL' &&  in_array($this->order_reference['gateway_status'] , array(85, 90))){
                if ($this->order_reference['gateway_status'] == 85 && $this->server_request['tid_status'] == 90) {
                    $order_status = MODULE_PAYMENT_NOVALNET_PAYPAL_PENDING_ORDER_STATUS_ID;
                    $callback_comments .= PHP_EOL . sprintf(MODULE_PAYMENT_NOVALNET_CALLBACK_HOLD_TO_PENDING, $this->server_request['shop_tid'], date('Y-m-d H:i:s') ) . PHP_EOL;
                } elseif (in_array($this->order_reference['gateway_status'], array(85, 90)) && $this->server_request['tid_status'] == 100) {
                    $order_status = MODULE_PAYMENT_NOVALNET_PAYPAL_ORDER_STATUS_ID;
                    $callback_comments = PHP_EOL . sprintf(MODULE_PAYMENT_NOVALNET_CALLBACK_CONFIRM, date('Y-m-d H:i:s')) . PHP_EOL;
                }
                $order_status = NovalnetUtil::checkDefaultOrderStatus($order_status);

                $db->Execute("UPDATE novalnet_transaction_detail SET gateway_status=  " . $this->server_request['tid_status'] . " where order_no=" . $this->order_reference['order_no']);

                $db->Execute("UPDATE " . TABLE_ORDERS . " SET orders_status= " . $order_status . " where orders_id=" . $this->order_reference['order_no']);

                $this->update_final_comments($this->server_request, $callback_comments, $order_status, $this->order_reference['order_no']);
             } elseif (in_array($this->server_request['payment_type'], array('PAYPAL', 'PRZELEWY24')) && $this->server_request['tid_status'] == 100)  {
                if ($this->order_reference['callback_amount'] <= 0 ) {
                    $callback_comments = PHP_EOL . sprintf(MODULE_PAYMENT_NOVALNET_CALLBACK_EXECUTE, $this->server_request['shop_tid'], $this->formatted_amount, date('Y-m-d H:i:s')) . PHP_EOL;

                    $order_status = NovalnetUtil::checkDefaultOrderStatus(constant('MODULE_PAYMENT_' . $paymentName . '_ORDER_STATUS_ID'));

                    $db->Execute("UPDATE novalnet_transaction_detail SET gateway_status=  " . $this->server_request['tid_status'] . " where order_no=" . $this->order_reference['order_no']);

                    $db->Execute("UPDATE " . TABLE_ORDERS . " SET orders_status= " . $order_status . " where orders_id=" . $this->order_reference['order_no']);

                    $this->update_final_comments($this->server_request, $callback_comments, $order_status, $this->order_reference['order_no']);
                }
                $this->display_message('Novalnet Callbackscript received. Order already Paid');

            } else if (in_array($this->server_request['payment_type'], array('POSTFINANCE','POSTFINANCE_CARD')) && $this->order_reference['gateway_status'] == 83 && $this->server_request['tid_status'] == 100 && $this->server_request['status'] == 100) {
                $callback_comments = PHP_EOL . sprintf(MODULE_PAYMENT_NOVALNET_CALLBACK_CONFIRM, date('Y-m-d H:i:s')) . PHP_EOL;

                    $order_status = NovalnetUtil::checkDefaultOrderStatus(constant('MODULE_PAYMENT_' . $paymentName . '_ORDER_STATUS_ID'));

                    $db->Execute("UPDATE novalnet_transaction_detail SET gateway_status=  " . $this->server_request['tid_status'] . " where order_no=" . $this->order_reference['order_no']);

                    $db->Execute("UPDATE " . TABLE_ORDERS . " SET orders_status= " . $order_status . " where orders_id=" . $this->order_reference['order_no']);

                    $this->update_final_comments($this->server_request, $callback_comments, $order_status, $this->order_reference['order_no']);
           } elseif ($this->server_request['payment_type'] == 'PRZELEWY24' && $this->server_request['tid_status'] != '86') {
                //Handle Przelewy cancel
                $message = $this->updatePrzelewyCancelcomments($this->order_reference);
                $this->display_message($message);
            } elseif (in_array($this->server_request['payment_type'], $this->payments)) {
                if (in_array($this->server_request['tid_status'], array(99, 91) ) && $this->order_reference['gateway_status'] == 75) {
                    $order_status = NovalnetUtil::checkDefaultOrderStatus(MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_COMPLETE_STATUS_ID);
                    $callback_comments .= sprintf(MODULE_PAYMENT_NOVALNET_CALLBACK_PENDING_TO_HOLD, $this->server_request['shop_tid'], date('Y-m-d H:i:s') );
                     $callback_comments .= NovalnetUtil::formPaymentComments($this->server_request['shop_tid'], $this->server_request['test_mode']);

                      if (in_array($this->server_request['payment_type'], array('INSTALMENT_INVOICE', 'GUARANTEED_INVOICE'))) {
                        $amount = sprintf('%0.2f', $this->server_request['amount'] / 100);
                        $callback_comments .= PHP_EOL . PHP_EOL . NovalnetUtil::formInvoicePrepaymentComments($this->server_request, $amount);

                        $callback_comments .=  NovalnetUtil::novalnetReferenceComments($this->server_request['order_no'],$this->server_request, $this->order_reference['payment_type']);
                      }
                      
                } elseif($this->server_request['tid_status'] == 100 && in_array($this->order_reference['gateway_status'], array(75,98,91,99))) {
                    $order_status = NovalnetUtil::checkDefaultOrderStatus(constant('MODULE_PAYMENT_'.$paymentName.'_ORDER_STATUS_ID'));
                    $comments = '';
                    if (in_array($this->server_request['payment_type'],array('INSTALMENT_INVOICE', 'INSTALMENT_DIRECT_DEBIT_SEPA', 'GUARANTEED_INVOICE', 'GUARANTEED_DIRECT_DEBIT_SEPA'))) {
                      $comments .= NovalnetUtil::formPaymentComments($this->server_request['shop_tid'], $this->server_request['test_mode']);

                      if (in_array($this->server_request['payment_type'], array ('INSTALMENT_INVOICE', 'GUARANTEED_INVOICE'))) {
                        $amount = sprintf('%0.2f', $this->server_request['amount'] / 100);
                        $comments .= PHP_EOL . PHP_EOL . NovalnetUtil::formInvoicePrepaymentComments($this->server_request, $amount);

                        $comments .=  NovalnetUtil::novalnetReferenceComments($this->server_request['order_no'],$this->server_request, $this->order_reference['payment_type']);
                      }
                      if(in_array($this->server_request['payment_type'],array('INSTALMENT_INVOICE', 'INSTALMENT_DIRECT_DEBIT_SEPA'))) {
                        $comments .= PHP_EOL . MODULE_PAYMENT_NOVALNET_INSTALMENT_INFO;
                        $comments .= PHP_EOL . MODULE_PAYMENT_NOVALNET_INSTALMENT_PROCESSED . (!empty($this->server_request['instalment_cycles_executed']) ? $this->server_request['instalment_cycles_executed']  : '') ;
                        $comments .= PHP_EOL . MODULE_PAYMENT_NOVALNET_INSTALMENT_DUE . (isset($this->server_request['due_instalment_cycles']) ? $this->server_request['due_instalment_cycles'] : '');
                        $amount = (!empty($this->server_request['instalment_cycle_amount']) ? $this->server_request['instalment_cycle_amount'] :  $this->server_request['amount']);
                        $amount = sprintf('%0.2f', $amount / 100);

                        $comments .= PHP_EOL . MODULE_PAYMENT_NOVALNET_INSTALMENT_NXT_AMOUNT .$amount. ' '.$this->server_request['currency'] ;
                        if ($future_instalment = $this->server_request['future_instalment_dates']) {
                            $future_instalments = explode('|', $future_instalment);
                            foreach ($future_instalments as $future_instalment) {
                                $cycle = strtok($future_instalment, "-");
                                $cycle_date = explode('-', $future_instalment, 2);
                                $instalment_details[$cycle] = [
                                    'amount' => ($this->server_request['amount']) ? sprintf('%0.2f', $this->server_request['amount'] / 100) : '',
                                    'nextCycle' => $cycle_date[1],
                                    'paidDate' => ($cycle == 1) ? date('Y-m-d') : '',
                                    'status' => ($cycle == 1) ? 'Paid' : 'Pending',
                                    'reference' => ($cycle == 1) ? $this->server_request['shop_tid'] : ''
                                ];
                            }
                       }
                    $instalment_details = serialize($instalment_details);
                    $param['gateway_status'] = $this->server_request['tid_status'];
                    $param['instalment_details'] = $instalment_details;
                    zen_db_perform('novalnet_transaction_detail', $param, "update", "tid='" . $this->server_request['shop_tid'] . "'");
				 }
                }
                $callback_comments .= $comments . PHP_EOL. sprintf(MODULE_PAYMENT_NOVALNET_CALLBACK_CONFIRM, date('Y-m-d H:i:s')) . PHP_EOL;
                }
                
                if(in_array($this->server_request['payment_type'],array('INSTALMENT_INVOICE', 'GUARANTEED_INVOICE', 'INVOICE_START')) && in_array($this->order_reference['gateway_status'], array(75, 91)) && $this->server_request['tid_status'] == 100) {
                    $this->sentPaymentConfirmationMail($callback_comments);
                }

                $param ['gateway_status'] = $this->server_request['tid_status'];
                $order_status = NovalnetUtil::checkDefaultOrderStatus($order_status);

                zen_db_perform('novalnet_transaction_detail', $param, "update", "tid='" . $this->server_request['shop_tid'] . "'");
                // Update the order status in shop
                zen_db_perform(TABLE_ORDERS, array(
                    'orders_status' => $order_status
                ), 'update', 'orders_id="' . $this->order_reference['order_no'] . '"');
                // To update order details in shop
                $this->update_callback_comments(array(
                'order_no'         => $this->order_reference['order_no'],
                'orders_status_id' => $order_status,
                'comments'         => $callback_comments
                ));
                // Send notification mail to Merchant
                $this->send_notify_mail(array(
                    'comments'      => $callback_comments,
                    'order_no'      => $this->order_reference['order_no'],
                ));
             } else {

                $this->display_message('Novalnet Callbackscript received Payment type ( ' . $this->server_request['payment_type'] . ' ) is not applicable for this process!');
             }
         }
    }

    /**
     * Validate ip address
     *
     * @return none
     */
    public function validate_ipaddress()
    {
        $remote_ip  = zen_get_ip_address();

        $remote_ip = (filter_var($remote_ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) || empty($remote_ip)) ? '127.0.0.1' : $remote_ip;

        $get_host_name = gethostbyname('pay-nn.de');
        if (empty($get_host_name)) {
           $this->display_message('Novalnet HOST IP missing');
        }

        if ($remote_ip != $get_host_name && MODULE_PAYMENT_NOVALNET_CALLBACK_TEST_MODE == 'False') {
            $this->display_message("Novalnet callback received. Unauthorised access from the IP " . $remote_ip);
        }
    }

    /**
     * Validate callback request param
     *
     * @param $request array
     *
     * @return none
     */
    public function validate_server_request($request)
    {
        $this->required_params[] = $shop_tid = $this->get_required_tid($request);

        // Validate the callback mandatory request parameters.
        $this->validate_required_fields($this->required_params, $request);

        if (!empty($request['payment_type']) && !in_array($request['payment_type'], array_merge($this->payments, $this->chargebacks, $this->collections, $this->cancellation), true)) {
            $this->display_message('Novalnet callback received. Payment type ( ' . $request['payment_type'] . ' ) is mismatched!');
        }

        $request['shop_tid'] = $request[$shop_tid];

        return $request;
    }

    /**
     * Validate request param
     *
     * @param $required_params array
     * @param $request array
     *
     * @return none
     */
    public function validate_required_fields($required_params, $request) {

        foreach ($required_params as $params) {
            if (empty($request[$params])) {
                $this->display_message( "Required param ( $params ) missing!" );
            } elseif (in_array($params, array( 'tid', 'tid_payment', 'instalment_tid' ), true ) && ! preg_match( '/^\d{17}$/', $request[ $params])) {
                $this->display_message('Novalnet callback received. Invalid TID [ ' . $request[$params] . ' ] for Order.');
            }
        }
    }

    /**
     * Get tid details
     *
     * @param $request array
     *
     * @return integer
     */
    public function get_required_tid( $request ) {

        $shop_tid = 'tid';
        if (in_array($request['payment_type'], array_merge( $this->chargebacks, $this->collections ), true ) ) { // Collection Payments or Chargeback Payments
            if (in_array($request['payment_type'], array('INSTALMENT_INVOICE_BOOKBACK', 'INSTALMENT_SEPA_BOOKBACK' ))) {
                $shop_tid = 'instalment_tid';
            } else {
                $shop_tid = 'tid_payment';
            }
        }
        if (in_array($request['payment_type'], array( 'INSTALMENT_DIRECT_DEBIT_SEPA', 'INSTALMENT_INVOICE' )) && $request['instalment_billing'] == '1' ) { // Instalment Payments
            $shop_tid = 'instalment_tid';
        }
        return $shop_tid;
    }

    /**
     * Get order details
     *
     * @return array
     */
    public function get_order_reference() {
        global $db;

        if (in_array($this->server_request['payment_type'], array_merge($this->payments,$this->cancellation))) {
            $tid = zen_db_input($this->server_request['shop_tid']);
        } elseif (in_array($this->server_request['payment_type'], $this->chargebacks)) {
           $tid = zen_db_input($this->server_request['shop_tid']);
        } elseif (in_array($this->server_request['payment_type'], $this->collections)) {
           $tid = zen_db_input($this->server_request['tid_payment']);
        }
        if (!empty($this->server_request['order_no'])){
            $db_val = $db->Execute("SELECT order_no, amount, payment_id, payment_type,language,callback_amount,gateway_status,instalment_details from novalnet_transaction_detail where order_no = '" .$this->server_request['order_no']. "'");
        } else {
            $db_val = $db->Execute("SELECT order_no, amount, payment_id, payment_type,language,callback_amount,gateway_status,instalment_details from novalnet_transaction_detail where tid = '" .$tid. "'");
        }
        $db_val = $db_val->fields;
        $db_val['tid'] = $this->server_request['shop_tid'];

        if (!empty($db_val)) {
            if(is_array($this->payment_groups[$db_val['payment_type']])) {
                if (!in_array($this->server_request['payment_type'], $this->payment_groups[$db_val['payment_type']])) {
                    $this->display_message('Novalnet callback received. Payment Type [' . $this->server_request['payment_type'] . '] is not valid.');
                }
            }

            $order_no = (!empty($this->server_request['order_no']) ? $this->server_request['order_no'] : '');
            if (!empty($order_no)) {
                $order_detail = $db->Execute('SELECT orders_id FROM '.TABLE_ORDERS.' WHERE orders_id = '.zen_db_input($order_no));
                if ( empty($order_detail) ) {
                    $this->sentCriticalMail();
                }
            }

            if (!empty($order_no) && $order_no != $db_val['order_no']) {
                $this->display_message('Novalnet callback received. Order Number is not valid.');
            }

            $db_val['nn_order_lang']        = $db_val['language'];
            $db_val['order_current_status'] = $this->getOrderCurrentStatus($db_val['order_no']);

            if (in_array($db_val['payment_type'], array('novalnet_invoice', 'novalnet_prepayment'))) {
                $db_val['callback_script_status'] = constant('MODULE_PAYMENT_' . strtoupper($db_val['payment_type']) . '_CALLBACK_STATUS_ID');
            }
            $db_val['order_total_amount']        = $db_val['amount'];
            $db_val['order_paid_amount']          = isset($db_val['callback_amount']) ? $db_val['callback_amount'] : 0;

        } else {
            $this->display_message('Novalnet callback script order number not valid');
        }

        return $db_val;
    }

    /**
     * Get orders_status from the orders table on shop database
     *
     * @param $order_id integer
     *
     * @return array
     */
    function getOrderCurrentStatus($order_id = '')
    {
        global $db;
        $db_val = $db->Execute("select orders_status from " . TABLE_ORDERS . " where orders_id = '" . $order_id . "'");
        return NovalnetUtil::checkDefaultOrderStatus($db_val->fields['orders_status']);
    }

    /**
     * Update Przelewy24 cancel status
     *
     * @param $nntrans_history array
     *
     * @return string
     */
    function updatePrzelewyCancelcomments($nntrans_history)
    {
        $nncapture_params   = $this->server_request();
        $callback_status_id = NovalnetUtil::checkDefaultOrderStatus($nntrans_history['callback_script_status']);

        // Assign przelewy24 payment status
        zen_db_perform(TABLE_ORDERS, array(
            'orders_status' => $callback_status_id
        ), 'update', 'orders_id="' . $nntrans_history['order_no'] . '"');

        // Form failure comments
        $comments          = !empty($nncapture_params['status_text']) ? PHP_EOL . $nncapture_params['status_text'] : (!empty($nncapture_params['status_desc']) ? PHP_EOL . $nncapture_params['status_desc'] : (!empty($nncapture_params['status_message']) ? PHP_EOL . $nncapture_params['status_message'] : ''));
        $callback_comments = sprintf('The transaction has been canceled due to: %s', $comments);

        $this->update_callback_comments(array('order_no' => $nntrans_history['order_no'], 'comments' => $callback_comments,
        'orders_status_id' => $callback_status_id));

        return $callback_comments;
    }

   /**
     * Handle transaction_cancellation process
     *
     * $param $order_reference array
     *
     * @return none
     */
    function transaction_cancellation($order_reference)
    {
        if ($this->server_request['payment_type'] == 'TRANSACTION_CANCELLATION') {

            // To form the callback comments
           $callback_comments = sprintf(MODULE_PAYMENT_NOVALNET_CALLBACK_CANCEL, date('Y-m-d H:i:s'));

           $param ['gateway_status'] = $this->server_request['tid_status'];
           zen_db_perform('novalnet_transaction_detail', $param, "update", "tid='" . $this->server_request['shop_tid'] . "'");
           $order_status = NovalnetUtil::checkDefaultOrderStatus(MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_CANCELLED_STATUS_ID);
           //Update callback order status due to full payment
            zen_db_perform(TABLE_ORDERS, array(
                'orders_status' => $order_status
            ), 'update', 'orders_id="' . $order_reference['order_no'] . '"');

           // To update order details in shop
           $this->update_callback_comments(array(
           'order_no'        => $order_reference['order_no'],
           'orders_status_id' => $order_status,
            'comments'         => $callback_comments
            ));
          // Send notification mail to Merchant
          $this->send_notify_mail(array(
             'comments'      => $callback_comments,
             'order_no'      => $order_reference['order_no'],
          ));
        }
    }

    /**
     * Get given payment_type level for process
     *
     * @return integer
     */
    function get_payment_type()
    {
        if (in_array($this->server_request['payment_type'], $this->payments))
            return 0;
        if (in_array($this->server_request['payment_type'], $this->chargebacks))
            return 1;
        if (in_array($this->server_request['payment_type'], $this->collections))
            return 2;
    }

    /**
     * update callback comments
     *
     * @param $server_request
     * @param $comments
     * @param $callback_status_id
     * @param $order_id     
     * @param $total_amount
     *
     * @return none
     */
    function update_final_comments($server_request, $comments, $callback_status_id, $order_id,  $total_amount = '')
    {
        $this->update_callback_comments(array('order_no' => $order_id, 'comments' => $comments, 'orders_status_id' => $callback_status_id));
        $this->logCallbackProcess($server_request, $order_id, $total_amount);
        $this->send_notify_mail(array('comments' => $comments, 'order_no' => $order_id));
     }

    /**
     * Log callback process in novalnet_transaction_detail table
     * @param $datas
     * @param $order_no
     * @param $total_amount
     *
     * @return none
     */
    function logCallbackProcess($datas, $order_no, $total_amount)
    {
        global $db;
        if (!empty($datas['amount'])) {
            $datas['amount'] = !empty($total_amount) ? $total_amount : $datas['amount'];
            $db->Execute("UPDATE novalnet_transaction_detail SET callback_amount= " . $datas['amount'] . " where order_no=$order_no");
        }
    }

    /**
     * Update Callback comments in orders_status_history table
     * @param $datas
     *
     * @return none
     */
    function update_callback_comments($datas)
    {
        global $db;
        $comments = ((!empty($datas['comments'])) ? $datas['comments'] : '');
        $db->Execute("INSERT INTO " . TABLE_ORDERS_STATUS_HISTORY . " (orders_id, orders_status_id, date_added, customer_notified, comments) VALUES ('" . $datas['order_no'] . "', '" . $datas['orders_status_id'] . "', NOW(), '1','$comments')");
    }

    /**
     * Send notify mail
     *
     * @param $message
     *
     * @return none
     */
    function send_notify_mail($message) {
        // Check for callback notification
        if (MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_SEND == 'True' && NovalnetUtil::validateEmail(strip_tags(MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_TO))) {
            // Get E-mail to address
            $email_to        = ((strip_tags(MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_TO) != '') ? strip_tags(MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_TO) : STORE_OWNER_EMAIL_ADDRESS);

            // Get E-mail to name
            $email_to_name   = (strpos($email_to,',')) ? '' : STORE_OWNER;

            // Assign Mail subject
            $email_subject   = 'Novalnet Callback script notification - '. STORE_NAME;

            if ($email_to != '') {
                // Send E-mail
                zen_mail($email_to_name ,$email_to, $email_subject, $message['comments'] , STORE_NAME, EMAIL_FROM);
                echo 'Mail sent!<br>';
            } else {
                echo 'Mail not sent!';
            }
        }

        // Display message
        $this->display_message($message['comments']);
    }

    /**
     * sent payment confirmation mail to customer.
     *
     * @param $comments string
     *
     * @return string
     */
    function sentPaymentConfirmationMail($comments)
    {
        $customer_details = $this->get_customer_details($this->server_request['customer_id']);
        $customer_name    = $customer_details['customers_firstname'] . ' ' . $customer_details['customers_lastname'];
        $email_subject   = sprintf(MODULE_PAYMENT_GUARANTEE_PAYMENT_MAIL_SUBJECT, $this->order_reference['order_no'],STORE_NAME);
        $email_content   = '<b>Dear Mr./Ms./Mrs.</b> '. $customer_name .'</br></br>' . MODULE_PAYMENT_GUARANTEE_PAYMENT_MAIL_MESSAGE.'</br></br><b>Payment Information:</b></br></br>' . nl2br($comments) .'<br>';

        zen_mail($customer_name, $customer_details['customers_email_address'], $email_subject, str_replace(PHP_EOL,'<br>',$email_content), STORE_NAME, EMAIL_FROM);
    }

    /**
     * Get customer details from customer table.
     *
     * @param $customer_id interger
     *
     * @return array
     */
    public static function get_customer_details($customer_id)
    {
        global $db;
        $customer_value   = $db->Execute("SELECT customers_firstname, customers_lastname, customers_email_address FROM " . TABLE_CUSTOMERS . " WHERE customers_id='" . $customer_id ."'");
        return $customer_value->fields;
    }

    /**
     * Send critical mail
     *
     * @return none
     */
    public static function sentCriticalMail()
    {
        $subject = 'Critical error on shop system '.STORE_NAME.': order not found for TID: ' . $this->server_request['shop_tid'];
        $message = "Dear Technic team,<br/><br/>Please evaluate this transaction and contact our payment module team at Novalnet.<br/><br/>";
        $message .= 'Merchant ID: ' . $this->server_request['vendor_id'] . '<br/>';
        $message .= 'Project ID: ' . $this->server_request['product_id'] . '<br/>';
        $message .= 'TID: ' . $this->server_request['shop_tid'] . '<br/>';
        $message .= 'TID status: ' . $this->server_request['tid_status'] . '<br/>';
        $message .= 'Order no: ' . $this->server_request['order_no'] . '<br/>';
        $message .= 'Payment type: ' . $this->server_request['payment_type'] . '<br/>';
        $message .= 'E-mail: ' . $this->server_request['email'] . '<br/>';

        $message .= '<br/><br/>Regards,<br/>Novalnet Team';

        zen_mail('Technic team' ,'technic@novalnet.de', $subject, $message ,STORE_NAME, EMAIL_FROM);
    }

    /**
     * Display message
     *
     * @param $message string
     * @param $order_no integer
     *
     * @return none
     */
    public static function display_message($message, $order_no = '')
    {
        echo !empty($order_no) ? 'message='. $message.'&order_no='.$order_no : 'message='.$message;
        exit;
    }
}
?>
