<?php
/**
* This script is used for Instalment Invoice payment
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
* Script : novalnet_instalment_invoice.php
*/
include_once(DIR_FS_CATALOG . DIR_WS_CLASSES.'class.novalnetutil.php');
class novalnet_instalment_invoice {

    var $code, $title, $public_title, $description, $enabled, $sort_order;

    /**
     * Function : novalnet_instalment_invoice()
     *
     */
    function __construct() {
        global $order;

        $this->code = 'novalnet_instalment_invoice';
        $this->title = MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_TEXT_TITLE;
        $this->public_title = MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_PUBLIC_TITLE;
        $this->description = MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_TEXT_DESCRIPTION;
        $this->sort_order = MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_SORT_ORDER;
        $this->enabled = ((MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_STATUS == 'True') ? true : false);

        if (is_object($order))
            $this->update_status();
    }
   /**
     * Function : update_status()
     *
     */
    function update_status() {
       global $order, $db;
        if (($this->enabled == true) && ((int) MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_ZONE > 0)) {
            $check_flag = false;
            $check = $db->Execute("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
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
        global $order, $currencies;

        if (!NovalnetUtil::checkMerchantConfiguration() || !$this->validateAdminConfiguration() ) { // Validate the Novalnet merchant details
         return false;
        }      

        list($payment_implementation_type, $error) = NovalnetUtil::checkGuaranteeConditions((array)$order, $this->code); // Check condition for displaying birthdate field

        $notification = '';
        if ($payment_implementation_type == 'error') {
            $notification .= $error. '<br>';
        }

        $customer_info = trim(strip_tags(MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_CUSTOMER_INFO));
        $customer_info = !empty($customer_info) ? $customer_info.'<br/>' :'';

        $selection['id'] = $this->code;
        $selection['module'] = $this->public_title.MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_LOGO;
        $selection['module'] .= $this->description. '<script src="' . DIR_WS_CATALOG . 'includes/ext/novalnet/js/novalnet_instalment_invoice.js" type="text/javascript"></script>
      <link rel="stylesheet" type="text/css" href="' . DIR_WS_CATALOG . 'includes/ext/novalnet/css/novalnet.css">';
        $selection['module'] .= ((MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_TEST_MODE == 'True') ? MODULE_PAYMENT_NOVALNET_TEST_MODE_MSG: '').$notification. $customer_info;

        $amount = $currencies->format($order->info['total'], 1, $order->info['currency']);

        $periods = NovalnetUtil::getInstalmentCycles($this->code, $order);

        $cycles = NovalnetUtil::getInstalmentPlanDetails($this->code);

        if (empty($cycles) || empty($periods)) {
            return false;
        }

        $lang = ($_SESSION['language'] == 'english') ? 'en' : 'de';
        $fieldArray = array();
        if (empty($order->billing['company'])) {
            $customer_details = NovalnetUtil::getCustomerfields();
            $fieldArray[] = array(
                'title' => MODULE_PAYMENT_NOVALNET_ENDCUSTOMER_BIRTH_DATE."<span style='color:red'> * </span>",
                'field' => NovalnetUtil::getGuaranteeField($this->code.'_birthdate', $customer_details)
            );
        }
        $totalamount = number_format($order->info['total'] * $currencies->get_value($order->info['currency']), 2);
        $total = str_replace(',', '', $totalamount);

        $fieldArray[] = array('title' => '<b>' . MODULE_PAYMENT_NOVALNET_INSTALMENT_PLAN . '</b><br />' . MODULE_PAYMENT_NOVALNET_INSTALMENT_PLAN_TEXT . '<br /><b>' . MODULE_PAYMENT_NOVALNET_INSTALMENT_AMOUNT_TEXT . $amount . '</b><br />' ,
                       'field' => zen_draw_pull_down_menu('novalnet_instalment_invoice_period', $periods, $periods[0], 'id="novalnet_instalment_invoice_period" class="form-control"', false));
        $fieldArray[] = array(
                 'title' => '',
                'field' => '<input type="hidden" id="nn_order_amount" value="' . $total . '">
                            <input type="hidden" id="instalment_cycles" value="' . $cycles . '">
                            <input type="hidden" id="nn_lang" value="' . $lang . '">
                            <input type="hidden" id="instalment_text" value="'.MODULE_PAYMENT_NOVALNET_INSTALMENT_TEXT.'">
                            <input type="hidden" id="instalment_number" value="'.MODULE_PAYMENT_NOVALNET_INSTALMENT_NUMBER.'">
                            <input type="hidden" id="monthly_instalment" value="'.MODULE_PAYMENT_NOVALNET_INSTALMENT_MONTHLY_AMOUNT.'">
                            <table id="novalnet_instalment_table_invoice" border="0" cellspacing="2" cellpadding="0" style="display:none;margin-left:21px;width:80%!important"><thead></thead><tbody></tbody></table>'
                             );
        $selection['fields'] = $fieldArray;

      return $selection;
    }
    /**
     * Function : pre_confirmation_check()
     *
     */
    function pre_confirmation_check() {
        global $order, $messageStack;
        $post = $_REQUEST;
        
        list($payment_implementation_type, $error) = NovalnetUtil::checkGuaranteeConditions((array)$order, $this->code); // Check condition for displaying birthdate field
       
        if ($payment_implementation_type == 'error') {
            $messageStack->add_session('checkout_payment', $error . '<!-- ['.$this->code.'] -->', 'error');
           zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', true, false));           
        }
        
        $birthdate = $post[$this->code.'_birthdate'];
        if (empty($order->billing['company']) && NovalnetUtil::validateAge($birthdate, $this->code)) {
            $messageStack->add_session('checkout_payment', MODULE_PAYMENT_NOVALNET_AGE_ERROR . '<!-- ['.$this->code.'] -->', 'error');
           zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', true, false));
        }
        if ($post['novalnet_instalment_invoice_period'] == '0'){
          $messageStack->add_session('checkout_payment', MODULE_PAYMENT_NOVALNET_INSTALMENT_CHOOSE_PLAN . '<!-- ['.$this->code.'] -->', 'error');
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
            $test_mode = (int)(!empty($payment_response['test_mode']) || MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_TEST_MODE == 'True');

            // Form transaction comments
            $transaction_comments = NovalnetUtil::formPaymentComments($payment_response['tid'], $test_mode);

            if ($payment_response['tid_status'] != 75) {
                // Get Invoice / Prepayment Comments and Bank details
                $invoice_comments = NovalnetUtil::formInvoicePrepaymentComments($payment_response);

            } else {
                $invoice_comments = MODULE_PAYMENT_NOVALNET_GUARANTEE_MESSAGE;
            }

            $order_status = $payment_response['tid_status'] == 75 ? MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_PENDING_ORDER_STATUS_ID : ($payment_response['tid_status'] == 91 ? MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_COMPLETE_STATUS_ID : MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_ORDER_STATUS_ID);

            // Set order status
            $order->info['order_status'] = NovalnetUtil::checkDefaultOrderStatus($order_status);

            $order->info['comments'] .= $transaction_comments. $invoice_comments;
			$instalment_details = array();
			if ($future_instalment = $payment_response['future_instalment_dates']) {
                $future_instalments = explode('|', $future_instalment);
                foreach ($future_instalments as $future_instalment) {
                    $cycle = strtok($future_instalment, "-");
                    $cycle_date = explode('-', $future_instalment, 2);
                    $instalment_details[$cycle] = [
                        'amount' => $payment_response['instalment_cycle_amount'],
                        'nextCycle' => $cycle_date[1],
                        'paidDate' => ($cycle == 1) ? date('Y-m-d') : '',
                        'status' => ($cycle == 1) ? 'Paid' : 'Pending',
                        'reference' => ($cycle == 1) ? $payment_response['tid'] : ''
                    ];
                }
              }
            $_SESSION['novalnet'][$this->code] = array_merge(NovalnetUtil::paymentInitialParams($request), array(
            'tid'                 => $payment_response['tid'],
            'amount'              => $request['amount'],
            'comments'            => $order->info['comments'],
            'gateway_status'      => $payment_response['tid_status'],
            'gateway_response'    => $payment_response,
            'total_amount'        => $_SESSION['novalnet'][$this->code]['order_amount'],
            'instalment_details' => serialize($instalment_details)
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
        global $db, $insert_id;
        
        $instalment_comments =  NovalnetUtil::instalmentComments($_SESSION['novalnet'][$this->code]['gateway_response'], $this->code);
        $reference_comments = '';
        if ($_SESSION['novalnet'][$this->code]['gateway_status'] != 75) {

        // Get Prepayment payment reference comments
            $reference_comments .= NovalnetUtil::novalnetReferenceComments($insert_id, $_SESSION['novalnet'][$this->code], $this->code);
        }
        
        $comments = $reference_comments . PHP_EOL. $instalment_comments;
        
        $trans_comments = $_SESSION['novalnet'][$this->code]['comments'].PHP_EOL.$comments;

        $db->Execute("update ".TABLE_ORDERS_STATUS_HISTORY." set comments = '".$trans_comments."' where orders_id = '".$insert_id."'");      

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
            $check_query = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_STATUS'");
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

        include(DIR_FS_CATALOG . DIR_WS_LANGUAGES . $_SESSION['language'] . '/modules/payment/novalnet_instalment_invoice.php');

         $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('".MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_STATUS_TITLE."', 'MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_STATUS', 'False', '".MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_STATUS_DESC."', '6', '0', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('".MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_TEST_MODE_TITLE."', 'MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_TEST_MODE', 'False', '".MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_TEST_MODE_DESC."', '6', '1', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('".MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_ONHOLD_TITLE."', 'MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_ONHOLD', 'Capture', '".MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_ONHOLD_DESC."', '6', '2', 'zen_cfg_select_option(array(\'Capture\', \'Authorize\'), ', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('".MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_ONHOLD_LIMIT_TITLE."', 'MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_ONHOLD_LIMIT', '', '".MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_ONHOLD_LIMIT_DESC."', '6', '3', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('".MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_PERIOD_TITLE."', 'MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_PERIOD', '', '".MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_PERIOD_DESC."', '6', '4', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('".MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_CYCLE_TITLE."', 'MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_CYCLE', '', '".MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_CYCLE_DESC."', '6', '5', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('".MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_MIN_AMOUNT_LIMIT_TITLE."', 'MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_MIN_AMOUNT_LIMIT', '', '".MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_MIN_AMOUNT_LIMIT_DESC."', '6', '6', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('".MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_CUSTOMER_INFO_TITLE."', 'MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_CUSTOMER_INFO', '', '".MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_CUSTOMER_INFO_DESC."', '6', '7', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('".MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_SORT_ORDER_TITLE."', 'MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_SORT_ORDER', '13', '".MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_SORT_ORDER_DESC."', '6', '8', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('".MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_PENDING_ORDER_STATUS_ID_TITLE."', 'MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_PENDING_ORDER_STATUS_ID', '0', '".MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_PENDING_ORDER_STATUS_ID_DESC."', '6', '9', 'zen_cfg_pull_down_order_statuses(', 'zen_get_order_status_name', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('".MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_ORDER_STATUS_ID_TITLE."', 'MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_ORDER_STATUS_ID', '0', '".MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_ORDER_STATUS_ID_DESC."', '6', '10', 'zen_cfg_pull_down_order_statuses(', 'zen_get_order_status_name', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('".MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_ZONE_TITLE."', 'MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_ZONE', '0', '".MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_ZONE_DESC."', '6', '11', 'zen_get_zone_class_title', 'zen_cfg_pull_down_zone_classes(', now())");
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
		$lang = ($_SESSION['language'] == 'english') ? 'en' : 'de';
		echo '<input type="hidden" id="nn_lang" value="'.$lang.'"><script src="'.DIR_WS_CATALOG.'includes/ext/novalnet/js/novalnet_admin.js" type="text/javascript"></script>';
        if( isset( $_GET['action'] ) && $_GET['action'] == 'edit' ) {
           echo '<script>$(document).ready(function(){create_installment_fields( "MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE", "novalnet_instalment_invoice");});</script>';
        }        
        return array( 'MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_STATUS',
        'MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_TEST_MODE',
        'MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_ONHOLD',
        'MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_ONHOLD_LIMIT',
        'MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_PERIOD',
        'MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_CYCLE',
        'MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_MIN_AMOUNT_LIMIT',
        'MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_CUSTOMER_INFO',
        'MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_SORT_ORDER',
        'MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_PENDING_ORDER_STATUS_ID',
        'MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_ORDER_STATUS_ID',
        'MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_ZONE');
    }
    /**
     * Validate admin configuration
     *
     * @return boolean
     */
    function validateAdminConfiguration()
    {
        if (MODULE_PAYMENT_NOVALNET_INSTALMENT_INVOICE_STATUS == 'True') {
           if (strpos(MODULE_PAYMENT_INSTALLED, 'novalnet_config.php') === false) {
               return false;
            }
        }
        return true;
    }
}
?>
