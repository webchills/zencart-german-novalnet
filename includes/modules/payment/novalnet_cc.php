<?php
/**
* This script is used for Credit Card
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
* Script : novalnet_cc.php
*/
include_once(DIR_FS_CATALOG . DIR_WS_CLASSES.'class.novalnetutil.php');

class novalnet_cc {

    var $code, $title, $public_title, $description, $enabled, $sort_order;

    /**
     * Function : novalnet_cc()
     *
     */
    function __construct() {
        global $order;

        $this->code = 'novalnet_cc';
        $this->title = MODULE_PAYMENT_NOVALNET_CC_TEXT_TITLE;
        $this->public_title = MODULE_PAYMENT_NOVALNET_CC_PUBLIC_TITLE;
        $this->description = MODULE_PAYMENT_NOVALNET_CC_REDIRECTION_TEXT_DESCRIPTION;
        $this->enabled = ((MODULE_PAYMENT_NOVALNET_CC_STATUS == 'True') ? true : false);
        $this->sort_order = MODULE_PAYMENT_NOVALNET_CC_SORT_ORDER;
        $this->form_action_url = 'https://payport.novalnet.de/pci_payport';
        if (is_object($order))
            $this->update_status();
    }
    /**
     * Function : update_status()
     *
     */
    function update_status() {
       global $order, $db;

        if (($this->enabled == true) && ((int) MODULE_PAYMENT_NOVALNET_CC_ZONE > 0)) {
            $check_flag = false;
            $check = $db->Execute("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_NOVALNET_CC_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
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

        if (!NovalnetUtil::checkMerchantConfiguration() || !$this->validateAdminConfiguration() ) { // Validate the Novalnet merchant details
          return false;
        }

        // Display payment description and notification of the buyer message
        $notification = trim(strip_tags(MODULE_PAYMENT_NOVALNET_CC_CUSTOMER_INFO));
        $notification = !empty($notification) ? $notification.'<br>' : '';

        $cc_fields = '<script src="'.DIR_WS_CATALOG.'includes/ext/novalnet/js/novalnet_cc.js'.'" type="text/javascript"></script>';

        $selection['id'] = $this->code;
        $selection['module'] = $this->public_title.MODULE_PAYMENT_NOVALNET_CC_LOGO;
        $selection['module'] .= $this->description. $cc_fields;
        $selection['module'] .= ((MODULE_PAYMENT_NOVALNET_CC_TEST_MODE == 'True') ? MODULE_PAYMENT_NOVALNET_TEST_MODE_MSG : '').$notification;

        $fieldArray = array();
        $fieldArray[] = array(
                'title' => '',
                'field' => $this->renderCCForm());
        $selection['fields'] = $fieldArray;

        return $selection;
    }
    /**
     * Function : pre_confirmation_check()
     *
     */
    function pre_confirmation_check() {
        $post = $_REQUEST;

        if(empty($post['nn_cc_pan_hash']) || empty($post['nn_cc_uniqueid'])) {
            $messageStack->add_session('checkout_payment', MODULE_PAYMENT_NOVALNET_VALID_CC_DETAILS . '<!-- ['.$this->code.'] -->', 'error');
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
        global $order;

        $post = $_REQUEST;
        if (isset($_SESSION['novalnet'][$this->code]['order_amount'])) {
            $_SESSION['novalnet'][$this->code] = array_merge($_SESSION['novalnet'][$this->code], $post);
        }
        $urlparam =  array_merge((array)$order, $_SESSION['novalnet'][$this->code]);
        $request = NovalnetUtil::getCommonRequestParams($urlparam, $this->code);
        NovalnetUtil::generateEncodeValue($request);

        $process_button_string = '';
        foreach ($request as $k => $v) {
            $process_button_string .= zen_draw_hidden_field($k, $v);
        }
        $process_button_string .= zen_draw_hidden_field(zen_session_name(), zen_session_id());
        return $process_button_string;
    }
    /**
     * Function : before_process()
     *
     */
    function before_process() {
        global $order, $messageStack;

        $post = $_REQUEST;

        if (isset($post['status']) && $post['status'] == 100) {

            // Hash Validation failed
            if (NovalnetUtil::validateHashResponse($post)) {
                $messageStack->add_session('checkout_payment', MODULE_PAYMENT_NOVALNET_TRANSACTION_REDIRECT_ERROR . '<!-- ['.$this->code.'] -->', 'error');
                // Novalnet transaction status got failure displaying error message
                zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', true, false));
            }
            // Hash Validation failed
            if (NovalnetUtil::validateHashResponse($post)) {
                $messageStack->add_session('checkout_payment', MODULE_PAYMENT_NOVALNET_TRANSACTION_REDIRECT_ERROR . '<!-- ['.$this->code.'] -->', 'error');
            // displaying error message
            zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', true, false));
            }

            // Decoding Novalnet server response
            $payment_response = NovalnetUtil::decodePaygateResponse($post);

            $this->update_response($post, $payment_response);
        } else {
            $messageStack->add_session('checkout_payment',  NovalnetUtil::getTransactionMessage($post) . '<!-- ['.$this->code.'] -->', 'error');
            // displaying error message
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
            $check_query = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_NOVALNET_CC_STATUS'");
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

       include(DIR_FS_CATALOG . DIR_WS_LANGUAGES . $_SESSION['language'] . '/modules/payment/novalnet_cc.php');

         $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('".MODULE_PAYMENT_NOVALNET_CC_STATUS_TITLE."', 'MODULE_PAYMENT_NOVALNET_CC_STATUS', 'False', '".MODULE_PAYMENT_NOVALNET_CC_STATUS_DESC."', '6', '0', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('".MODULE_PAYMENT_NOVALNET_CC_TEST_MODE_TITLE."', 'MODULE_PAYMENT_NOVALNET_CC_TEST_MODE', 'False', '".MODULE_PAYMENT_NOVALNET_CC_TEST_MODE_DESC."', '6', '1', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('".MODULE_PAYMENT_NOVALNET_CC_ONHOLD_TITLE."', 'MODULE_PAYMENT_NOVALNET_CC_ONHOLD', 'Capture', '".MODULE_PAYMENT_NOVALNET_CC_ONHOLD_DESC."', '6', '2', 'zen_cfg_select_option(array(\'Capture\', \'Authorize\'), ', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('".MODULE_PAYMENT_NOVALNET_CC_ONHOLD_LIMIT_TITLE."', 'MODULE_PAYMENT_NOVALNET_CC_ONHOLD_LIMIT', '', '".MODULE_PAYMENT_NOVALNET_CC_ONHOLD_LIMIT_DESC."', '6', '3', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('".MODULE_PAYMENT_NOVALNET_CC_AMEX_LOGO_TITLE."', 'MODULE_PAYMENT_NOVALNET_CC_AMEX_LOGO', 'False', '".MODULE_PAYMENT_NOVALNET_CC_AMEX_LOGO_DESC."', '6', '4', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('".MODULE_PAYMENT_NOVALNET_CC_MAESTRO_LOGO_TITLE."', 'MODULE_PAYMENT_NOVALNET_CC_MAESTRO_LOGO', 'False', '".MODULE_PAYMENT_NOVALNET_CC_MAESTRO_LOGO_DESC."', '6', '5', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('".MODULE_PAYMENT_NOVALNET_CC_CUSTOMER_INFO_TITLE."', 'MODULE_PAYMENT_NOVALNET_CC_CUSTOMER_INFO', '', '".MODULE_PAYMENT_NOVALNET_CC_CUSTOMER_INFO_DESC."', '6', '6', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('".MODULE_PAYMENT_NOVALNET_CC_FORM_LABEL_STYLE_TITLE."', 'MODULE_PAYMENT_NOVALNET_CC_FORM_LABEL_STYLE', 'font-family: verdana, arial, helvetica, sans-serif;font-size: 12px;', '".MODULE_PAYMENT_NOVALNET_CC_FORM_LABEL_STYLE_DESC."', '6', '7', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('".MODULE_PAYMENT_NOVALNET_CC_FORM_INPUT_STYLE_TITLE."', 'MODULE_PAYMENT_NOVALNET_CC_FORM_INPUT_STYLE', 'border: 3px solid #ccc;  height: 42px;  padding-left: 5px;', '".MODULE_PAYMENT_NOVALNET_CC_FORM_INPUT_STYLE_DESC."', '6', '8', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('".MODULE_PAYMENT_NOVALNET_CC_FORM_CSS_STYLE_TITLE."', 'MODULE_PAYMENT_NOVALNET_CC_FORM_CSS_STYLE', '#iframeForm { max-width:600px;margin-left:25px;} .label-group {width:25%;} .input-group {width:71%} .input-group input {height:38px !important;}@media screen and (min-width:270px) and (max-width:570px) {.label-group,.input-group {width: 100%;}}', '".MODULE_PAYMENT_NOVALNET_CC_FORM_CSS_STYLE_DESC."', '6', '9', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('".MODULE_PAYMENT_NOVALNET_CC_SORT_ORDER_TITLE."', 'MODULE_PAYMENT_NOVALNET_CC_SORT_ORDER', '2', '".MODULE_PAYMENT_NOVALNET_CC_SORT_ORDER_DESC."', '6', '10', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('".MODULE_PAYMENT_NOVALNET_CC_ORDER_STATUS_ID_TITLE."', 'MODULE_PAYMENT_NOVALNET_CC_ORDER_STATUS_ID', '0', '".MODULE_PAYMENT_NOVALNET_CC_ORDER_STATUS_ID_DESC."', '6', '11', 'zen_cfg_pull_down_order_statuses(', 'zen_get_order_status_name', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('".MODULE_PAYMENT_NOVALNET_CC_ZONE_TITLE."', 'MODULE_PAYMENT_NOVALNET_CC_ZONE', '0', '".MODULE_PAYMENT_NOVALNET_CC_ZONE_DESC."', '6', '12', 'zen_get_zone_class_title', 'zen_cfg_pull_down_zone_classes(', now())");
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
        echo '<script src="' . DIR_WS_CATALOG . 'includes/ext/novalnet/js/novalnet_admin.js"></script>';
        return array( 'MODULE_PAYMENT_NOVALNET_CC_STATUS',
        'MODULE_PAYMENT_NOVALNET_CC_TEST_MODE',
        'MODULE_PAYMENT_NOVALNET_CC_ONHOLD',
        'MODULE_PAYMENT_NOVALNET_CC_ONHOLD_LIMIT',
        'MODULE_PAYMENT_NOVALNET_CC_AMEX_LOGO',
        'MODULE_PAYMENT_NOVALNET_CC_MAESTRO_LOGO',
        'MODULE_PAYMENT_NOVALNET_CC_FORM_LABEL_STYLE',
        'MODULE_PAYMENT_NOVALNET_CC_FORM_INPUT_STYLE',
        'MODULE_PAYMENT_NOVALNET_CC_FORM_CSS_STYLE',
        'MODULE_PAYMENT_NOVALNET_CC_CUSTOMER_INFO',
        'MODULE_PAYMENT_NOVALNET_CC_SORT_ORDER',
        'MODULE_PAYMENT_NOVALNET_CC_ORDER_STATUS_ID',
        'MODULE_PAYMENT_NOVALNET_CC_ZONE'
        );
    }

    /**
     * Update transaction details
     *
     *  @param $request
     *  @param $response
     *
     *  @return none
     */
    function update_response($request, $response) {
            global $order;
            $test_mode = (int)(!empty($response['test_mode']) || MODULE_PAYMENT_NOVALNET_CC_TEST_MODE == 'True');

            // Form transaction comments
            $transaction_comments = NovalnetUtil::formPaymentComments($response['tid'], $test_mode);
            $order_status =  $response['tid_status'] == 98 ? MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_COMPLETE_STATUS_ID : MODULE_PAYMENT_NOVALNET_CC_ORDER_STATUS_ID;

            // Set order status
            $order->info['order_status'] = NovalnetUtil::checkDefaultOrderStatus($order_status);

            $order->info['comments'] .= $transaction_comments;

            $_SESSION['novalnet'][$this->code] = array_merge(NovalnetUtil::paymentInitialParams($request), array(
            'tid'                 => $response['tid'],
            'amount'              => $request['amount'],
            'gateway_status'      => $response['tid_status'],
            'total_amount'        =>  $_SESSION['novalnet'][$this->code]['order_amount']
            ));
    }

    /**
     * To get Iframe form in checkout page
     *
     * @return string
     */
    function renderCCForm() {

        $language = ($_SESSION['language'] == 'english') ? 'en' : 'de';
        $vendor   = MODULE_PAYMENT_NOVALNET_VENDOR_ID;
        $product  = MODULE_PAYMENT_NOVALNET_PRODUCT_ID;
        $serverIp = (filter_var($_SERVER['SERVER_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) || $_SERVER['SERVER_ADDR'] == '::1' || empty($_SERVER['SERVER_ADDR'])) ? '127.0.0.1' : $_SERVER['SERVER_ADDR'];

        // Form signature
        $signature = base64_encode("vendor=$vendor&product=$product&server_ip=$serverIp");

        // Iframe URL path
        $path = 'https://secure.novalnet.de/cc?api='. $signature . '&ln=' . $language ;

        // CC CSS Configurations
        $cc_hidden_field = '<input type="hidden" id="validate_account_details" value="'.MODULE_PAYMENT_NOVALNET_VALID_ACCOUNT_CREDENTIALS_ERROR.'"><input type="hidden" id="nn_cc_default_label" value="'.strip_tags(MODULE_PAYMENT_NOVALNET_CC_FORM_LABEL_STYLE).'">
        <input type="hidden" id="nn_cc_default_input" value="'.strip_tags(MODULE_PAYMENT_NOVALNET_CC_FORM_INPUT_STYLE).'">
        <input type="hidden" id="nn_cc_default_css" value="'.strip_tags(MODULE_PAYMENT_NOVALNET_CC_FORM_CSS_STYLE).'">
        <input type="hidden" value="" id="nn_cc_pls_wait">
        <input type="hidden" value="" id="nn_cvc_hint">
        <input type="hidden" value="" id="nn_holder_label">
        <input type="hidden" value="" id="nn_holder_input">
        <input type="hidden" value="" id="nn_number_label">
        <input type="hidden" value="" id="nn_number_input">
        <input type="hidden" value="" id="nn_expiry_label">
        <input type="hidden" value="" id="nn_expiry_input">
        <input type="hidden" value="" id="nn_cvc_label">
        <input type="hidden" value="" id="nn_cvc_input">
        <input type="hidden" id="nn_cc_pan_hash" name="nn_cc_pan_hash" value="" /> <input type="hidden" id="nn_cc_uniqueid" name="nn_cc_uniqueid" value="" />';


        return '<iframe onload="getFormValue()" id="nnIframe" src="'.$path.'" style="border-style:none !important;" width="100%" height="100%"></iframe>'.$cc_hidden_field;
    }

    /**
     * Validate admin configuration
     *
     * @return boolean
     */
    function validateAdminConfiguration()
    {
        if (MODULE_PAYMENT_NOVALNET_CC_STATUS == 'True') {
            if (strpos(MODULE_PAYMENT_INSTALLED, 'novalnet_config.php') === false) {
                return false;
            }
        }
        return true;
    }
}
?>
