<?php
/**
* This script is used for Direct Debit SEPA
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
* Script : novalnet_sepa.php
*/
include_once(DIR_FS_CATALOG . DIR_WS_CLASSES.'class.novalnetutil.php');
class novalnet_sepa {

    var $code, $title, $public_title, $description, $enabled, $sort_order;

    /**
     * Function : novalnet_sepa()
     *
     */
    function __construct() {
        global $order, $messageStack;

        $this->code = 'novalnet_sepa';
        $this->title = MODULE_PAYMENT_NOVALNET_SEPA_TEXT_TITLE;
        $this->public_title = MODULE_PAYMENT_NOVALNET_SEPA_PUBLIC_TITLE;
        $this->description = MODULE_PAYMENT_NOVALNET_SEPA_TEXT_DESCRIPTION;
        $this->enabled = ((MODULE_PAYMENT_NOVALNET_SEPA_STATUS == 'True') ? true : false);
        $this->sort_order = MODULE_PAYMENT_NOVALNET_SEPA_SORT_ORDER;
        if (is_object($order))
            $this->update_status();
    }
    /**
     * Function : update_status()
     *
     */
    function update_status() {
       global $order, $db;

        if (($this->enabled == true) && ((int) MODULE_PAYMENT_NOVALNET_SEPA_ZONE > 0)) {
            $check_flag = false;
            $check = $db->Execute("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_NOVALNET_SEPA_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
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
     */
    function javascript_validation() {
        return false;
    }
    /**
     * Function : selection()
     *
     */
    function selection() {
        global $order;

        if (!NovalnetUtil::checkMerchantConfiguration() || !$this->validateAdminConfiguration() ) {
            return false;
        }

        // Display payment description and notification of the buyer message
        $notification = trim(strip_tags(MODULE_PAYMENT_NOVALNET_SEPA_CUSTOMER_INFO));
        $notification = !empty($notification) ? $notification.'<br/>' :'';

        $sepa_fields = '<script src="'.DIR_WS_CATALOG.'includes/ext/novalnet/js/novalnet_sepa.js'.'" type="text/javascript"></script><link rel="stylesheet" type="text/css" href="' . DIR_WS_CATALOG . 'includes/ext/novalnet/css/novalnet.css">';

        $selection['id'] = $this->code;
        $selection['module'] = $this->public_title.MODULE_PAYMENT_NOVALNET_SEPA_LOGO;
        $selection['module'] .= $this->description;
        $selection['module'] .= ((MODULE_PAYMENT_NOVALNET_SEPA_TEST_MODE == 'True') ? MODULE_PAYMENT_NOVALNET_TEST_MODE_MSG : '') . $sepa_fields . $notification;
        $fieldArray = array();
        $fieldArray[] = array('title' => MODULE_PAYMENT_NOVALNET_ACCOUNT_HOLDER,
                        'field' => zen_draw_input_field($this->code.'_bank_account_holder', $order->billing['firstname'] . ' ' . $order->billing['lastname'], 'id="' . $this->code . '_bank_account_holder" placeholder="'.MODULE_PAYMENT_NOVALNET_ACCOUNT_HOLDER.'" AUTOCOMPLETE="OFF" onkeypress="return allowName(event);"'),
                        'tag' => $this->code . '_bank_account_holder');
        $fieldArray[] = array('title' => MODULE_PAYMENT_NOVALNET_IBAN,
                        'field' => zen_draw_input_field($this->code.'_bank_iban', '', 'id="' . $this->code . '_bank_iban" placeholder="'.MODULE_PAYMENT_NOVALNET_IBAN.'" AUTOCOMPLETE="OFF" onkeypress="return allowAlphaNumeric(event);"'),
                        'tag' => $this->code . '_bank_iban');
        $fieldArray[] = array('title' => '', 'field' => '<div id="nn_sepa_mandate" style="cursor: pointer;"><table><tr><td class="main">'.
                    MODULE_PAYMENT_NOVALNET_SEPA_FORM_MANDATE_CONFIRM_TEXT.'</tr></td ></table></div>');
        $selection['fields'] = $fieldArray;
        return $selection;
    }
    /**
     * Function : pre_confirmation_check()
     *
     */
    function pre_confirmation_check() {
        global $messageStack;
        $post = $_REQUEST;

        if (empty($post[$this->code.'_bank_iban']) || empty($post[$this->code.'_bank_account_holder'])) {
            $messageStack->add_session('checkout_payment', MODULE_PAYMENT_NOVALNET_VALID_ACCOUNT_CREDENTIALS_ERROR . '<!-- ['.$this->code.'] -->', 'error');
            // Novalnet transaction status got failure displaying error message
            zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', true, false));
        }
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
        $post = $_REQUEST;
        if (isset($_SESSION['novalnet'][$this->code]['order_amount'])) {
            $_SESSION['novalnet'][$this->code] = array_merge($_SESSION['novalnet'][$this->code], $post);
        }
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
            $test_mode = (int)(!empty($payment_response['test_mode']) || MODULE_PAYMENT_NOVALNET_SEPA_TEST_MODE == 'True');

            // Form transaction comments
            $transaction_comments = NovalnetUtil::formPaymentComments($payment_response['tid'], $test_mode);

            $order_status = ($payment_response['tid_status'] == 99) ? MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_COMPLETE_STATUS_ID : MODULE_PAYMENT_NOVALNET_SEPA_ORDER_STATUS_ID;

            // Set order status
            $order->info['order_status'] = NovalnetUtil::checkDefaultOrderStatus($order_status);

            $order->info['comments'] .= $transaction_comments;

            $_SESSION['novalnet'][$this->code] = array_merge(NovalnetUtil::paymentInitialParams($request), array(
            'tid'                 => $payment_response['tid'],
            'amount'              => $request['amount'],
            'gateway_status'      => $payment_response['tid_status'],
            'total_amount'        => $_SESSION['novalnet'][$this->code]['order_amount']
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
            $check_query = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_NOVALNET_SEPA_STATUS'");
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

        include(DIR_FS_CATALOG . DIR_WS_LANGUAGES . $_SESSION['language'] . '/modules/payment/novalnet_sepa.php');

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('".MODULE_PAYMENT_NOVALNET_SEPA_STATUS_TITLE."', 'MODULE_PAYMENT_NOVALNET_SEPA_STATUS', 'False', '".MODULE_PAYMENT_NOVALNET_SEPA_STATUS_DESC."', '6', '0', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('".MODULE_PAYMENT_NOVALNET_SEPA_TEST_MODE_TITLE."', 'MODULE_PAYMENT_NOVALNET_SEPA_TEST_MODE', 'False', '".MODULE_PAYMENT_NOVALNET_SEPA_TEST_MODE_DESC."', '6', '1', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('".MODULE_PAYMENT_NOVALNET_SEPA_PAYMENT_DUE_DATE_TITLE."', 'MODULE_PAYMENT_NOVALNET_SEPA_PAYMENT_DUE_DATE', '', '".MODULE_PAYMENT_NOVALNET_SEPA_PAYMENT_DUE_DATE_DESC."', '6', '2', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('".MODULE_PAYMENT_NOVALNET_SEPA_ONHOLD_TITLE."', 'MODULE_PAYMENT_NOVALNET_SEPA_ONHOLD', 'Capture', '".MODULE_PAYMENT_NOVALNET_SEPA_ONHOLD_DESC."', '6', '3', 'zen_cfg_select_option(array(\'Capture\', \'Authorize\'), ', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('".MODULE_PAYMENT_NOVALNET_SEPA_ONHOLD_LIMIT_TITLE."', 'MODULE_PAYMENT_NOVALNET_SEPA_ONHOLD_LIMIT', '', '".MODULE_PAYMENT_NOVALNET_SEPA_ONHOLD_LIMIT_DESC."', '6', '4', now())");


        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('".MODULE_PAYMENT_NOVALNET_SEPA_CUSTOMER_INFO_TITLE."', 'MODULE_PAYMENT_NOVALNET_SEPA_CUSTOMER_INFO', '', '".MODULE_PAYMENT_NOVALNET_SEPA_CUSTOMER_INFO_DESC."', '6', '5', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('".MODULE_PAYMENT_NOVALNET_SEPA_SORT_ORDER_TITLE."', 'MODULE_PAYMENT_NOVALNET_SEPA_SORT_ORDER', '1', '".MODULE_PAYMENT_NOVALNET_SEPA_SORT_ORDER_DESC."', '6', '6', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('".MODULE_PAYMENT_NOVALNET_SEPA_ORDER_STATUS_ID_TITLE."', 'MODULE_PAYMENT_NOVALNET_SEPA_ORDER_STATUS_ID', '0', '".MODULE_PAYMENT_NOVALNET_SEPA_ORDER_STATUS_ID_DESC."', '6', '7', 'zen_cfg_pull_down_order_statuses(', 'zen_get_order_status_name', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('".MODULE_PAYMENT_NOVALNET_SEPA_ZONE_TITLE."', 'MODULE_PAYMENT_NOVALNET_SEPA_ZONE', '0', '".MODULE_PAYMENT_NOVALNET_SEPA_ZONE_DESC."', '6', '8', 'zen_get_zone_class_title', 'zen_cfg_pull_down_zone_classes(', now())");
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
        echo '<input type="hidden" id="sepa_due_date_error" value="'.MODULE_PAYMENT_SEPA_DUE_DATE_VALIDATION.'"><script src="' . DIR_WS_CATALOG . 'includes/ext/novalnet/js/novalnet_admin.js"></script>';
        return array( 'MODULE_PAYMENT_NOVALNET_SEPA_STATUS',
        'MODULE_PAYMENT_NOVALNET_SEPA_TEST_MODE',
        'MODULE_PAYMENT_NOVALNET_SEPA_PAYMENT_DUE_DATE',
        'MODULE_PAYMENT_NOVALNET_SEPA_ONHOLD',
        'MODULE_PAYMENT_NOVALNET_SEPA_ONHOLD_LIMIT',
        'MODULE_PAYMENT_NOVALNET_SEPA_CUSTOMER_INFO',
        'MODULE_PAYMENT_NOVALNET_SEPA_SORT_ORDER',
        'MODULE_PAYMENT_NOVALNET_SEPA_ORDER_STATUS_ID',
        'MODULE_PAYMENT_NOVALNET_SEPA_ZONE'
        );
    }

    /**
     * Validate admin configuration
     *
     * @return boolean
     */
    function validateAdminConfiguration()
    {
        if (MODULE_PAYMENT_NOVALNET_SEPA_STATUS == 'True') {
            if (strpos(MODULE_PAYMENT_INSTALLED, 'novalnet_config.php') === false) {
               return false;
            }
        }
        return true;
    }
}
?>
