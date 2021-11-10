<?php
/**
* This script is used for Cashpayment payment
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
* Script : novalnet_cashpayment.php
*/
include_once(DIR_FS_CATALOG . DIR_WS_CLASSES.'class.novalnetutil.php');

class novalnet_cashpayment {

    var $code, $title, $public_title, $description, $enabled, $sort_order;
    
    /**
     * Function : novalnet_cashpayment()
     *
     */
    function __construct() {
        global $order;

        $this->code = 'novalnet_cashpayment';
        $this->title = MODULE_PAYMENT_NOVALNET_CASHPAYMENT_TEXT_TITLE;
        $this->public_title = MODULE_PAYMENT_NOVALNET_CASHPAYMENT_PUBLIC_TITLE;
        $this->description = MODULE_PAYMENT_NOVALNET_CASHPAYMENT_TEXT_DESCRIPTION;
        $this->sort_order = MODULE_PAYMENT_NOVALNET_CASHPAYMENT_SORT_ORDER;
        $this->enabled = ((MODULE_PAYMENT_NOVALNET_CASHPAYMENT_STATUS == 'True') ? true : false);

        if (is_object($order))
            $this->update_status();
    }

    /**
     * Function : update_status()
     *
     */
    function update_status() {
       global $order, $db;

        if (($this->enabled == true) && ((int) MODULE_PAYMENT_NOVALNET_CASHPAYMENT_ZONE > 0)) {
            $check_flag = false;
            $check = $db->Execute("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_NOVALNET_CASHPAYMENT_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
            while (!$check->EOF) {
                if ($check->fields['zone_id'] < 1) {
                    $check_flag = true;
                    break;
                } elseif ($check->fields['zone_id'] == $order->billing['zone_id']) {
                    $check_flag = true;
                    break;
                }
                $check->MoveNext();
            }
            if ($check_flag == false) {
                $this->enabled = false;
            }
        }
    }

    /**
     * Function : javascript_validation()
     * 
     * @return boolean
     */
    function javascript_validation() {
        return false;
    }

    /**
     * Function : selection()
     *
     */
    function selection() {
        
        if (!NovalnetUtil::checkMerchantConfiguration() || !$this->validateAdminConfiguration() ) { // Validate the Novalnet merchant details
         return false;
        }
        // Display payment description and notification of the buyer message
        $notification = trim(strip_tags(MODULE_PAYMENT_NOVALNET_CASHPAYMENT_CUSTOMER_INFO));
        $notification = !empty($notification) ? $notification.'<br/>' :'';

        $selection['id'] = $this->code;
        $selection['module'] = $this->public_title.MODULE_PAYMENT_NOVALNET_CASHPAYMENT_LOGO;
        $selection['module'] .= $this->description;
        $selection['module'] .= ((MODULE_PAYMENT_NOVALNET_CASHPAYMENT_TEST_MODE == 'True') ? MODULE_PAYMENT_NOVALNET_TEST_MODE_MSG : '').$notification;

        return $selection;
    }

    /**
     * Function : pre_confirmation_check()
     *
     */
    function pre_confirmation_check() {
        return false;
    }

    /**
     * Function : confirmation()
     *
     */
    function confirmation() {
        global $order;
        $_SESSION['novalnet'][$this->code]['order_amount'] = NovalnetUtil::getPaymentAmount((array)$order, $this->code);
        return false;
    }

    /**
     * Function : process_button()
     *
     */
    function process_button() {

        return false;
    }

    /**
     * Function : before_process()
     *
     */
    function before_process() {
        global $order, $messageStack;

        $urlparam =  array_merge((array)$order, $_SESSION['novalnet'][$this->code]);
        $request = NovalnetUtil::getCommonRequestParams($urlparam, $this->code);
        $response = NovalnetUtil::doPaymentCurlCall('https://payport.novalnet.de/paygate.jsp', $request, $this->code);
        parse_str($response, $payment_response);

        if ($payment_response['status'] == 100) {
            $test_mode = (int)(!empty($payment_response['test_mode']) || MODULE_PAYMENT_NOVALNET_CASHPAYMENT_TEST_MODE == 'True');

            // Form transaction comments
            $transaction_comments = NovalnetUtil::formPaymentComments($payment_response['tid'], $test_mode);

            // Set order status
            $order->info['order_status'] = NovalnetUtil::checkDefaultOrderStatus(MODULE_PAYMENT_NOVALNET_CASHPAYMENT_ORDER_STATUS_ID);

            // Get Barzahlen Comments
            $cashpayment_comments = NovalnetUtil::formCashpaymentComments($payment_response);

            $order->info['comments'] .= $transaction_comments. $cashpayment_comments;
			
			$_SESSION['novalnet_cp_token'] = $payment_response['cp_checkout_token'].'|'. $payment_response['test_mode'];
			
            $_SESSION['novalnet'][$this->code] = array_merge(NovalnetUtil::paymentInitialParams($request), array(
            'tid'                 => $payment_response['tid'],
            'amount'              => $request['amount'],            
            'gateway_status'      => $payment_response['tid_status'],
            'total_amount'        => '0'
            ));

        } else {
            $messageStack->add_session('checkout_payment', NovalnetUtil::getTransactionMessage($payment_response) . '<!-- ['.$this->code.'] -->', 'error');
            // Novalnet transaction status got failure displaying error message
            zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', true, false));
        }
    }

    /**
     * Function : after_process()
     *
     */
    function after_process() {
        global $insert_id;

         // Update payment process based on the response.
         NovalnetUtil::logInitialTransaction(array(
            'payment'  => $this->code,
            'order_no' => $insert_id
         ));
        // Sending post back call to Novalnet server
        NovalnetUtil::postBackCall(array( 'payment'  => $this->code, 'order_no' => $insert_id ));

        return false;
    }

    /**
     * Function : check()
     *
     */
    function check() {
        global $db;
        if (!isset($this->_check)) {
            $check_query = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_NOVALNET_CASHPAYMENT_STATUS'");
            $this->_check = $check_query->RecordCount();
        }
        return $this->_check;
    }

    /**
     * Function : install()
     *
     */
    function install() {
        global $db;

       include(DIR_FS_CATALOG . DIR_WS_LANGUAGES . $_SESSION['language'] . '/modules/payment/novalnet_cashpayment.php');

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('".MODULE_PAYMENT_NOVALNET_CASHPAYMENT_STATUS_TITLE."', 'MODULE_PAYMENT_NOVALNET_CASHPAYMENT_STATUS', 'False', '".MODULE_PAYMENT_NOVALNET_CASHPAYMENT_STATUS_DESC."', '6', '0', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('".MODULE_PAYMENT_NOVALNET_CASHPAYMENT_TEST_MODE_TITLE."', 'MODULE_PAYMENT_NOVALNET_CASHPAYMENT_TEST_MODE', 'False', '".MODULE_PAYMENT_NOVALNET_CASHPAYMENT_TEST_MODE_DESC."', '6', '1', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('".MODULE_PAYMENT_NOVALNET_CASHPAYMENT_SLIP_EXPIRY_DATE_TITLE."', 'MODULE_PAYMENT_NOVALNET_CASHPAYMENT_SLIP_EXPIRY_DATE', '', '".MODULE_PAYMENT_NOVALNET_CASHPAYMENT_SLIP_EXPIRY_DATE_DESC."', '6', '2', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('".MODULE_PAYMENT_NOVALNET_CASHPAYMENT_CUSTOMER_INFO_TITLE."', 'MODULE_PAYMENT_NOVALNET_CASHPAYMENT_CUSTOMER_INFO', '', '".MODULE_PAYMENT_NOVALNET_CASHPAYMENT_CUSTOMER_INFO_DESC."', '6', '3', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('".MODULE_PAYMENT_NOVALNET_CASHPAYMENT_SORT_ORDER_TITLE."', 'MODULE_PAYMENT_NOVALNET_CASHPAYMENT_SORT_ORDER', '10', '".MODULE_PAYMENT_NOVALNET_CASHPAYMENT_SORT_ORDER_DESC."', '6', '4', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('".MODULE_PAYMENT_NOVALNET_CASHPAYMENT_ORDER_STATUS_ID_TITLE."', 'MODULE_PAYMENT_NOVALNET_CASHPAYMENT_ORDER_STATUS_ID', '0', '".MODULE_PAYMENT_NOVALNET_CASHPAYMENT_ORDER_STATUS_ID_DESC."', '6', '5', 'zen_cfg_pull_down_order_statuses(', 'zen_get_order_status_name', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('".MODULE_PAYMENT_NOVALNET_CASHPAYMENT_CALLBACK_STATUS_ID_TITLE."', 'MODULE_PAYMENT_NOVALNET_CASHPAYMENT_CALLBACK_STATUS_ID', '0', '".MODULE_PAYMENT_NOVALNET_CASHPAYMENT_CALLBACK_STATUS_ID_DESC."', '6', '6', 'zen_cfg_pull_down_order_statuses(', 'zen_get_order_status_name', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('".MODULE_PAYMENT_NOVALNET_CASHPAYMENT_ZONE_TITLE."', 'MODULE_PAYMENT_NOVALNET_CASHPAYMENT_ZONE', '0', '".MODULE_PAYMENT_NOVALNET_CASHPAYMENT_ZONE_DESC."', '6', '7', 'zen_get_zone_class_title', 'zen_cfg_pull_down_zone_classes(', now())");
    }

    /**
     * Function : remove()
     *
     */
     function remove() {
        global $db;
        $db->Execute("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    /**
     * Function : keys()
     *
     * @return array
     */
     function keys() {
		 echo '<input type="hidden" id="cashpayment_slip_error" value="'.MODULE_PAYMENT_NOVALNET_CASHPAYMENT_SLIP_DATE_ERROR.'"><script src="' . DIR_WS_CATALOG . 'includes/ext/novalnet/js/novalnet_admin.js"></script>'; 
        return array( 'MODULE_PAYMENT_NOVALNET_CASHPAYMENT_STATUS',
        'MODULE_PAYMENT_NOVALNET_CASHPAYMENT_TEST_MODE',
        'MODULE_PAYMENT_NOVALNET_CASHPAYMENT_SLIP_EXPIRY_DATE',
        'MODULE_PAYMENT_NOVALNET_CASHPAYMENT_CUSTOMER_INFO',
        'MODULE_PAYMENT_NOVALNET_CASHPAYMENT_SORT_ORDER',
        'MODULE_PAYMENT_NOVALNET_CASHPAYMENT_ORDER_STATUS_ID',
        'MODULE_PAYMENT_NOVALNET_CASHPAYMENT_CALLBACK_STATUS_ID',
        'MODULE_PAYMENT_NOVALNET_CASHPAYMENT_ZONE'
        );
    }

     /**
     * Validate admin configuration
     *
     * @return boolean
     */
    function validateAdminConfiguration()
    {
        if (MODULE_PAYMENT_NOVALNET_CASHPAYMENT_STATUS == 'True') {
           if (strpos(MODULE_PAYMENT_INSTALLED, 'novalnet_config.php') === false) {
                return false;
            }
        }
        return true;
    }
}
?>
