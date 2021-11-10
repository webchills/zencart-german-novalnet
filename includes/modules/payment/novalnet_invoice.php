<?php

#########################################################
#                                                       #
#  Invoice payment method class                         #
#  This module is used for real time processing of      #
#  Invoice data of customers.                       	#
#                                                       #
#  Released under the GNU General Public License.       #
#  This free contribution made by request.              #
#  If you have found this script useful a small         #
#  recommendation as well as a comment on merchant form #
#  would be greatly appreciated.                        #
#                                                       #
#  Script : novalnet_invoice.php                        #
#                                                       #
#########################################################

class novalnet_invoice {

    var $code;
    var $title;
    var $description;
    var $enabled;
    var $proxy;
    var $callback_type;
    var $payment_key = '27';
    var $vendor_id;
    var $auth_code;
    var $product_id;
    var $tariff_id;
    var $nninv_allowed_pin_country_list = array('de', 'at', 'ch');

    function novalnet_invoice() {
        global $order, $messageStack;

        $this->vendor_id = trim(MODULE_PAYMENT_NOVALNET_INVOICE_VENDOR_ID);
        $this->auth_code = trim(MODULE_PAYMENT_NOVALNET_INVOICE_AUTH_CODE);
        $this->product_id = trim(MODULE_PAYMENT_NOVALNET_INVOICE_PRODUCT_ID);
        $this->tariff_id = trim(MODULE_PAYMENT_NOVALNET_INVOICE_TARIFF_ID);
        $this->test_mode = (strtolower(MODULE_PAYMENT_NOVALNET_INVOICE_TEST_MODE) == 'true' or MODULE_PAYMENT_NOVALNET_INVOICE_TEST_MODE == '1') ? 1 : 0;
        $this->code = 'novalnet_invoice';
        $this->title = MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_TITLE;
        $this->public_title = MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_PUBLIC_TITLE;
        $this->description = MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_DESCRIPTION;
        $this->sort_order = MODULE_PAYMENT_NOVALNET_INVOICE_SORT_ORDER;
        $this->enabled = ((MODULE_PAYMENT_NOVALNET_INVOICE_STATUS == 'True') ? true : false);
        $this->proxy = MODULE_PAYMENT_NOVALNET_INVOICE_PROXY;
        
        if (MODULE_PAYMENT_NOVALNET_INVOICE_LOGO_STATUS == 'True') {
            $this->public_title = 'Novalnet'.' '. MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_PUBLIC_TITLE;
			 $this->title = 'Novalnet'.' '. MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_TITLE;
        }
	$this->checkConfigure();

        if ((int) MODULE_PAYMENT_NOVALNET_INVOICE_ORDER_STATUS_ID > 0) {
            $this->order_status = MODULE_PAYMENT_NOVALNET_INVOICE_ORDER_STATUS_ID;
        }
        if (is_object($order))
            $this->update_status();
        // Check the tid in session and make the second call
        if ($_SESSION['nn_tid_invoice']) {
            if ((empty($_SESSION['invalid_count_invoice'])) || ( isset($_SESSION['max_time_invoice']) && (time() >= $_SESSION['max_time_invoice']))) {
                $_SESSION['invalid_count_invoice'] = 0;
            }
            if (!empty($_SESSION['invalid_count_invoice']) && $_SESSION['invalid_count_invoice'] == 3) {

                if ($_SESSION['max_time_invoice'] && (time() < $_SESSION['max_time_invoice'])) {
                    $payment_error_return = 'payment_error=' . $this->code . '&error=' . utf8_encode(MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SESSION_ERROR);
                }
            }
            //Check the time limit
            if ($_SESSION['max_time_invoice'] && time() > $_SESSION['max_time_invoice']) {
                unset($_SESSION['nn_tid_invoice']);
                unset($_SESSION['invalid_count_invoice']);
                $payment_error_return = 'payment_error=' . $this->code;
                $messageStack->add_session('checkout_payment', MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SESSION_ERROR . '<!-- [' . $this->code . '] -->', 'error');
                zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
            }
            if ($_GET['new_novalnet_pin_invoice'] == 'true') {
                $_SESSION['new_novalnet_pin_invoice'] = true;
                $this->secondcall();
            }
        }
        // define callback types
        $this->isActivatedCallback = false;
        if (MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS != 'False') {
            $this->isActivatedCallback = true;
        }
    }

    function checkConfigure() {
        if (IS_ADMIN_FLAG == true) {
            $this->title = MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_TITLE; // Payment module title in Admin
			if (MODULE_PAYMENT_NOVALNET_INVOICE_LOGO_STATUS == 'True') {
				$this->public_title = 'Novalnet'.' '. MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_PUBLIC_TITLE;
				$this->title = 'Novalnet'.' '. MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_TITLE;
			}
            if ($this->enabled == 'true' && (!$this->vendor_id || !$this->auth_code || !$this->product_id || !$this->tariff_id )) {
                $this->title .= '<span class="alert">' . MODULE_PAYMENT_NOVALNET_INVOICE_NOT_CONFIGURED . '</span>';
            } elseif ($this->test_mode == '1') {
                $this->title .= '<span class="alert">' . MODULE_PAYMENT_NOVALNET_INVOICE_INVOICE_TEST_MODE . '</span>';
            }
        }
    }

    ### calculate zone matches and flag settings to determine whether this module should display to customers or not ###

    function update_status() {
        global $order, $db;
        if (($this->enabled == true) && ((int) MODULE_PAYMENT_NOVALNET_INVOICE_ZONE > 0)) {
            $check_flag = false;
            $check = $db->Execute("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_NOVALNET_INVOICE_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
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

    ### JS validation which does error-checking of data-entry if this module is selected for use ###
    ### the fields to be cheked are (Bank Owner, Bank Account Number and Bank Code Lengths)      ###
    ### currently this function is not in use ###
    // @return string

    function javascript_validation() {
        return false;
    }

    ### Builds set of input fields for collecting Bankdetail info ###
    // @return array

    function selection() {
        global $order, $HTTP_POST_VARS, $_POST;
        $onFocus = '';
        $billing_iso_code = strtolower($order->customer['country']['iso_code_2']);
        if (count($HTTP_POST_VARS) == 0 || $HTTP_POST_VARS == '')
            $HTTP_POST_VARS = $_POST;
        if (!$_SESSION['nn_tid_invoice']) {
            $selection = array('id' => $this->code,
                'module' => $this->public_title,
                'fields' => array(array('title' => '', 'field' => MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_BANK_INFO),
                    array('title' => '', 'field' => MODULE_PAYMENT_NOVALNET_INVOICE_INFO)
                    ));
            #}
            // Display callback fields
            $amount_check = $this->findTotalAmount();
            if ($this->isActivatedCallback && in_array($billing_iso_code, $this->nninv_allowed_pin_country_list) && $amount_check >= MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_MIN_LIMIT) {
                if (MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS == 'Email Reply') {
                    $_SESSION['user_email_invoice'] = ($_SESSION['user_email_invoice'] == '') ? $order->customer['email_address'] : $_SESSION['user_email_invoice'];
                    $selection['fields'][] = array('title' => MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_EMAIL_REQ, 'field' => zen_draw_input_field('user_email_invoice', $_SESSION['user_email_invoice'], 'id="' . $this->code . '-callback" AUTOCOMPLETE="OFF"' . $onFocus));
                } else {
                    $_SESSION['user_tel_invoice'] = ($_SESSION['user_tel_invoice'] == '') ? $order->customer['telephone'] : $_SESSION['user_tel_invoice'];

                    $label_str = (MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS == 'Callback (Telefon & Handy)') ? MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_TEL_REQ : MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS_REQ;

                    $selection['fields'][] = array('title' => $label_str, 'field' => zen_draw_input_field('user_tel_invoice', $_SESSION['user_tel_invoice'], 'id="' . $this->code . '-callback" AUTOCOMPLETE="OFF"' . $onFocus));
                }
            }
        }

        $amount_check = $this->findTotalAmount();
        $_SESSION['nn_amount_invoice'] = $amount_check;
        if ($this->isActivatedCallback && in_array($billing_iso_code, $this->nninv_allowed_pin_country_list) && $amount_check >= MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_MIN_LIMIT && isset($_SESSION['nn_tid_invoice']) && ($_SESSION['invalid_count_invoice'] < 3)) {
            $selection = array('id' => $this->code, 'module' => $this->public_title);
            if (MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS == 'Email Reply') {
                $selection['fields'][] = array('title' => MODULE_PAYMENT_NOVALNET_INVOICE_EMAIL_INPUT_REQUEST_DESC);
            } else {
                $selection = array('id' => $this->code,
                    'module' => $this->public_title);
                // Show PIN field, after first call
                $selection['fields'][] = array('title' => MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS_PIN, 'field' => zen_draw_input_field('novalnet_pin_invoice', '', 'id="' . $this->code . '-callback" AUTOCOMPLETE="OFF"' . $onFocus));
                $selection['fields'][] = array('title' => '<a href="' . zen_href_link(FILENAME_CHECKOUT_PAYMENT, 'new_novalnet_pin_invoice=true', 'SSL', true, false) . '">' . MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS_NEW_PIN . '</a>');
            }
        }


        if (function_exists('get_percent')) {
            $selection['module_cost'] = $GLOBALS['ot_payment']->get_percent($this->code);
        }

        return $selection;
    }

    ### Precheck to Evaluate the Bank Datas ###

    function pre_confirmation_check() {
        global $HTTP_POST_VARS, $_POST, $order, $messageStack;
        $billing_iso_code = strtolower($order->customer['country']['iso_code_2']);
        if (count($HTTP_POST_VARS) == 0 || $HTTP_POST_VARS == '')
            $HTTP_POST_VARS = $_POST;
        if (isset($HTTP_POST_VARS['user_tel_invoice']))
           $HTTP_POST_VARS['user_tel_invoice'] = trim($HTTP_POST_VARS['user_tel_invoice']);
        if (isset($HTTP_POST_VARS['user_email_invoice']))
            $HTTP_POST_VARS['user_email_invoice'] = trim($HTTP_POST_VARS['user_email_invoice']);
        if (isset($HTTP_POST_VARS['novalnet_pin_invoice']))
            $HTTP_POST_VARS['novalnet_pin_invoice'] = trim($HTTP_POST_VARS['novalnet_pin_invoice']);
        // Callback stuff....
        if ($_SESSION['nn_tid_invoice']) {
            //check the amount is equal with the first call or not
            $amount = $this->findTotalAmount();
            if ($_SESSION['invoice_order_amount'] != $amount) {
                if (MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS == 'Email Reply') {
                    $error_message = MODULE_PAYMENT_NOVALNET_INVOICE_AMOUNT_VARIATION_MESSAGE_EMAIL;
                } elseif (MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS == 'Callback (Telefon & Handy)' || MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS == 'SMS (nur Handy)') {
                    $error_message = MODULE_PAYMENT_NOVALNET_INVOICE_AMOUNT_VARIATION_MESSAGE;
                }
                unset($_SESSION['nn_tid_invoice']);
                unset($_SESSION['invoice_order_amount']);
                if (isset($_SESSION['invalid_count_invoice'])) {
                    unset($_SESSION['invalid_count_invoice']);
                }
                $payment_error_return = 'payment_error=' . $this->code;
                $messageStack->add_session('checkout_payment', $error_message . '<!-- [' . $this->code . '] -->', 'error');
                zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
            }
            if (isset($HTTP_POST_VARS['novalnet_pin_invoice']) && isset($_SESSION['nn_tid_invoice'])) {
                // check pin
                //if( !is_numeric( $HTTP_POST_VARS['novalnet_pin_invoice'] ) || strlen( $HTTP_POST_VARS['novalnet_pin_invoice'] ) != 4 )
                if ($HTTP_POST_VARS['novalnet_pin_invoice'] == '' || (preg_match('/[&_#%\^<>@$=*!]/', $HTTP_POST_VARS['novalnet_pin_invoice']))) {
                    $payment_error_return = 'payment_error=' . $this->code;
                    $messageStack->add_session('checkout_payment', MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS_PIN_NOTVALID . '<!-- [' . $this->code . '] -->', 'error');
                    zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
                } else {
                    if ($HTTP_POST_VARS['novalnet_pin_invoice'])
                        $_SESSION['novalnet_pin_invoice'] = $HTTP_POST_VARS['novalnet_pin_invoice'];
                }
            }
            return;
        }else {
            $error = '';
            if (!function_exists('curl_init') && ($this->_code == 'novalnet_invoice')) {
                ini_set('display_errors', 1);
                ini_set('error_reporting', E_ALL);
                $error = MODULE_PAYMENT_NOVALNET_INVOICE_CURL_MESSAGE;
			}
            if (!isset($_SESSION['nn_tid_invoice'])) {
                if (!$this->vendor_id || !$this->auth_code || !$this->product_id || !$this->tariff_id) {
                    $error = MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_JS_NN_MISSING;
                }
                if (isset($HTTP_POST_VARS['user_email_invoice'])) {
                    $_SESSION['user_email_invoice'] = $HTTP_POST_VARS['user_email_invoice'];
                }
                if (isset($HTTP_POST_VARS['user_tel_invoice'])) {
                    $_SESSION['user_tel_invoice'] = $HTTP_POST_VARS['user_tel_invoice'];
                }
                // Callback stuff....
                //$amount_check = $_SESSION['nn_amount_invoice'];				
                $amount_check = $this->findTotalAmount();
                if ($this->isActivatedCallback && in_array($billing_iso_code, $this->nninv_allowed_pin_country_list) && $amount_check >= MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_MIN_LIMIT) {
                   //checking email address
                    if (isset($HTTP_POST_VARS['user_email_invoice'])) {
                        if (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $HTTP_POST_VARS['user_email_invoice'])) {
                            $error .= MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_EMAIL_NOTVALID;
                        }
                    }
                    //checking telephone number
                    if (isset($HTTP_POST_VARS['user_tel_invoice'])) {
                        if (strlen($HTTP_POST_VARS['user_tel_invoice']) < 8 || !is_numeric($HTTP_POST_VARS['user_tel_invoice'])) {
                            $error .= MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS_TEL_NOTVALID;
                        }
                    }
                    if ($error != '') {
                        /* $payment_error_return = 'payment_error=' . $this->code . '&error=' .utf8_encode($error);		  
                          zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false)); */
                        $payment_error_return = 'payment_error=' . $this->code;
                        $messageStack->add_session('checkout_payment', utf8_encode($error) . '<!-- [' . $this->code . '] -->', 'error');
                        zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
                    } else {
                        $_SESSION['user_tel_invoice'] = $HTTP_POST_VARS['user_tel_invoice'];
                        if (isset($HTTP_POST_VARS['user_email_invoice'])) {
                            $error_msg = MODULE_PAYMENT_NOVALNET_INVOICE_EMAIL_INPUT_REQUEST_DESC;
                        } else {
                            $error_msg = MODULE_PAYMENT_NOVALNET_INVOICE_PIN_INPUT_REQUEST_DESC;
                        }
                        // firstcall()
                        $this->before_process();
                        $payment_error_return = 'payment_error=' . $this->code;
                        $messageStack->add_session('checkout_payment', $error_msg . '<!-- [' . $this->code . '] -->', 'error');
                        zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
                        //$messageStack->add_session('checkout_payment', MODULE_PAYMENT_NOVALNET_INVOICE_PIN_INPUT_REQUEST_DESC . '<!-- ['.$this->code.'] -->', 'error');				
                        //zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', true, false));  

                        return;
                    }
                }
                if ($error != '') {
                    $payment_error_return = 'payment_error=' . $this->code;
                    $messageStack->add_session('checkout_payment', $error . '<!-- [' . $this->code . '] -->', 'error');
                    zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
                }
            }
        }
    }

    ### Display Bank Information on the Checkout Confirmation Page ###
    // @return array

    function confirmation() {
        global $order;
        $_SESSION['nn_total'] = $order->info['total'];
        $confirmation = array('fields' => array(array('field' => '')));
        return $confirmation;
    }

    ### Build the data and actions to process when the "Submit" button is pressed on the order-confirmation screen. ###
    ### These are hidden fields on the checkout confirmation page ###
    // @return string

    function process_button() {
        global $HTTP_POST_VARS, $_POST;
        if (count($HTTP_POST_VARS) == 0 || $HTTP_POST_VARS == '')
            $HTTP_POST_VARS = $_POST;
        return $process_button_string;
    }

    public function secondCall() {
        global $messageStack;
        $xmlresponse_erros = '';
        // If customer forgets PIN, send a new PIN
        if ($_SESSION['new_novalnet_pin_invoice'])
            $request_type = 'TRANSMIT_PIN_AGAIN';
        else
            $request_type = 'PIN_STATUS';
        if ($_SESSION['email_reply_check_invoice'] == 'Email Reply')
            $request_type = 'REPLY_EMAIL_STATUS';
        if ($_SESSION['new_novalnet_pin_invoice'])
            $_SESSION['new_novalnet_pin_invoice'] = false;
        $xml = '';
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
				<nnxml>                               
		  			<info_request>
			    		<vendor_id>' . $this->vendor_id . '</vendor_id>
			    		<vendor_authcode>' . $this->auth_code . '</vendor_authcode>
			    		<request_type>' . $request_type . '</request_type>
			    		<tid>' . $_SESSION['nn_tid_invoice'] . '</tid>';
        if ($request_type != 'REPLY_EMAIL_STATUS')
            $xml .= '<pin>' . $_SESSION['novalnet_pin_invoice'] . '</pin>';$xml .= '
		  			</info_request>
				</nnxml>';
        $xml_response = $this->curl_xml_post($xml);
        // Parse XML Response to object
        $xml_response = simplexml_load_string($xml_response);
        #$_SESSION['status'] = $xml_response->status;
        if ($xml_response->status != '') {
            $xmlresponse_erros = $xml_response->status;
        }
        if ($xmlresponse_erros == '') {
            $errormesage = $xml_response->pin_status->status_message;
            $payment_error_return = 'payment_error=' . $this->code . '&error=' . utf8_encode($errormesage);
            zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
        } else {
            if ($xml_response->status != 100) {
                if ($xml_response->status == '0529005') {
                    $_SESSION['invalid_count_invoice'] = $_SESSION['invalid_count_invoice'] + 1;
                    if ($_SESSION['invalid_count_invoice'] == 3) {
                        $payment_error_return = 'payment_error=' . $this->code . '&error=' . utf8_encode(MODULE_PAYMENT_NOVALNET_INVOICE_MAX_TIME_ERROR);
                        // $payment_error_return     = 'payment_error='.$this->code;
                    } else {
                        $payment_error_return = 'payment_error=' . $this->code . '&error=' . utf8_encode($xml_response->status_message);
                    }
                    zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
                } else {
                    $payment_error_return = 'payment_error=' . $this->code . '&error=' . utf8_encode($xml_response->status_message);
                    zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
                }
            } else {
                $array = (array) $xml_response;
                // add tid, because it's missing in the answer
                $array['tid'] = $_SESSION['nn_tid_invoice'];
                $array['statusdesc'] = $array['status_message']; // Param-name is changed
                $array['test_mode'] = $_SESSION['test_mode_invoice'];
                return $array;
            }
        }
    }

    public function curl_xml_post($request) {
        $ch = curl_init("https://payport.novalnet.de/nn_infoport.xml");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: close'));
        curl_setopt($ch, CURLOPT_POST, 1);  // a non-zero parameter tells the library to do a regular HTTP post.
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);  // add POST fields
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);  // don't allow redirects
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  // decomment it if you want to have effective ssl checking
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  // decomment it if you want to have effective ssl checking
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  // return into a variable
        curl_setopt($ch, CURLOPT_TIMEOUT, 240);  // maximum time, in seconds, that you'll allow the CURL functions to take		  
        ## establish connection
        $xml_response = curl_exec($ch);
        ## determine if there were some problems on cURL execution
        $errno = curl_errno($ch);
        $errmsg = curl_error($ch);
        ###bug fix for PHP 4.1.0/4.1.2 (curl_errno() returns high negative value in case of successful termination)
        if ($errno < 0)
            $errno = 0;
        ##bug fix for PHP 4.1.0/4.1.2
        if ($debug) {
            print_r(curl_getinfo($ch));
            echo "\n<BR><BR>\n\n\nperform_https_request: cURL error number:" . $errno . "\n<BR>\n\n";
            echo "\n\n\nperform_https_request: cURL error:" . $errmsg . "\n<BR>\n\n";
        }
        #close connection
        curl_close($ch);
        return $xml_response;
    }

    //This is user defined function used for getting order amount in cents with tax
    public function findTotalAmount() {
        global $order, $currencies;
        if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
            $total = $order->info['total'] + $order->info['tax'];
        } else {
            $total = $order->info['total'];
        }
        $totalamount = number_format($total * $currencies->get_value($order->info['currency']), 2);
        $amount = str_replace(',', '', $totalamount);
        $amount = intval(round($amount * 100));
        if (preg_match('/[^\d\.]/', $total) or !$total) {
            ### $amount contains some unallowed chars or empty ###
            $err = 'amount (' . $total . ') is empty or has a wrong format';
            $payment_error_return = 'payment_error=' . $this->code;
            $messageStack->add_session('checkout_payment', $err . '<!-- [' . $this->code . '] -->', 'error');
            zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
        }
        // $amount = sprintf('%0.2f', $total);
        // $amount = preg_replace('/^0+/', '', $amount);
        // $amount = str_replace('.', '', $amount);
        return $amount;
    }

    ### Store the BANK info to the order ###
    ### This sends the data to the payment gateway for processing and Evaluates the Bankdatas for acceptance and the validity of the Bank Details ###

    function before_process() {
        global $HTTP_POST_VARS, $_POST, $order, $currencies, $customer_id, $db, $messageStack;
        $billing_iso_code = strtolower($order->customer['country']['iso_code_2']);
        $_SESSION['nn_amount_invoice'] = $this->findTotalAmount();
        if (count($HTTP_POST_VARS) == 0 || $HTTP_POST_VARS == '')
            $HTTP_POST_VARS = $_POST;
		// First call is done, so check PIN / second call...
        if ($_SESSION['nn_tid_invoice'] && $this->isActivatedCallback) {
            if (MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS == 'Email Reply')
                $_SESSION['email_reply_check_invoice'] = 'Email Reply';
            else
                unset($_SESSION['email_reply_check_invoice']);
            $_SESSION['new_novalnet_pin_invoice'] = false;
            if ($aryResponse = $this->secondCall()) {
                if ($this->order_status)
                    $order->info['order_status'] = $this->order_status;
                //$old_comments = $order->info['comments'];
                //$order->info['comments'] = "";
                $transferinvoice_info = utf8_encode(MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_TRANSFER_INFO);
                $days_limit = utf8_encode(MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_DURATION_LIMIT_END_INFO);
                $inrenationalinvoice_info = utf8_encode(MODULE_PAYMENT_NOVALNET_TEXT_DETAILS_INVOICE_INTERNATIONAL_INFO);
                //Test mode based on the responsone test mode value
                if ($_SESSION['test_mode_invoice'] == 1 || $test_mode) {
                    $order->info['comments'] .= '<b><br>' . MODULE_PAYMENT_NOVALNET_INVOICE_TEST_ORDER_MESSAGE . '</b>';
                    unset($_SESSION['test_mode_invoice']);
                }
                //$order->info['comments'] .= '<br><b>' . MODULE_PAYMENT_NOVALNET_INVOICE_PAYMNETNAME . '</b>';
				if (MODULE_PAYMENT_NOVALNET_INVOICE_LOGO_STATUS == 'True') {
					$order->info['comments'] .= '<b><br>' . 'Novalnet'.' '. MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_TITLE;
				}
				else{
					$order->info['comments'] .= '<b><br>' . MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_TITLE;
				}
                $order->info['comments'] .= '<br><b>' . MODULE_PAYMENT_NOVALNET_INVOICE_TID . ' ' . $_SESSION['nn_tid_invoice'] . '</b><br />';
                if ($_SESSION['due_date_invoice']) {
                    $order->info['comments'] .= '<br><b>' . $transferinvoice_info . ' ' . '</b>';
                    $order->info['comments'] .= '<br><b>' . $days_limit . ' ' . $_SESSION['due_date_invoice'] . '</b>';
                } else {
                    $order->info['comments'] .= '<br><b>' . $transferinvoice_info . '</b>';
                }
                //$amount = $currencies->format($amount/100);
                // $ss_amount = str_replace('.', ',', sprintf("%.2f", $_SESSION['original_amount_invoice']));
                $order->info['comments'] .= '<br><b>' . MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_BANK_ACCOUNT_OWNER . ' ' . MODULE_PAYMENT_NOVALNET_INVOICE_NAME . '</b>';
                $order->info['comments'] .= '<br><b>' . MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_BANK_ACCOUNT_NUMBER . ' ' . $_SESSION['nn_invoice_account'] . '</b>';
                $order->info['comments'] .= '<br><b>' . MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_BANK_CODE . ' ' . $_SESSION['nn_invoice_bankcode'] . '</b>';
                $order->info['comments'] .= '<br><b>' . MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_BANK_BANK . ' ' . $_SESSION['nn_invoice_bankname'] . ' ' . $_SESSION['nn_invoice_bankplace'] . '</b>';
                //$order->info['comments'] .= '<b>'.MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_AMOUNT.' '.$ss_amount.' '.$order->info['currency'].'</b>';
                $order->info['comments'] .= '<b>' . MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_AMOUNT . ' ' . $currencies->format($_SESSION['original_amount_invoice']) . '</b>';
                $order->info['comments'] .= '<br><b>' . MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_REFERENCE . ' ' . $_SESSION['nn_tid_invoice'] . '<br>';
                $order->info['comments'] .= '<br><b>' . $inrenationalinvoice_info . '</b>';
                $order->info['comments'] .= '<br><b>' . MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_BANK_IBAN . ' ' . $_SESSION['nn_invoice_iban'] . '</b>';
                $order->info['comments'] .= '<br><b>' . MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_BANK_BIC . ' ' . $_SESSION['nn_invoice_bic'] . '</b>';
                $order->info['comments'] .= '<br><b>' . MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_REFERENCE_INFO . '</b><br>';
                //$order->info['comments'] .= $old_comments;
                $order->info['comments'] = str_replace(array('<B>', '</B>', '</b>', '<b>', '<br>', '<BR>', '<br />', '<BR />'), array('', '', '', '', "\n", "\n", "\n", "\n"), $order->info['comments']);
                return;
            }
        }
        #Get the required additional customer details from DB
        $nn_customer_id = (isset($_SESSION['customer_id'])) ? $_SESSION['customer_id'] : '';
        $customer = $db->Execute("SELECT customers_gender, customers_dob, customers_fax FROM " . TABLE_CUSTOMERS . " WHERE customers_id='" . (int) $nn_customer_id . "'");
        if ($customer->RecordCount() > 0) {
            $customer = $customer->fields;
        }
        list($customer['customers_dob'], $extra) = explode(' ', $customer['customers_dob']);
        ### Process the payment to paygate ##
        $url = 'https://payport.novalnet.de/paygate.jsp';
        //$amount = $this->findTotalAmount();
        $amount = $_SESSION['nn_amount_invoice'];
        $vendor_id = $this->vendor_id;
        $auth_code = $this->auth_code;
        $product_id = $this->product_id;
        $tariff_id = $this->tariff_id;
        $payment_duration = MODULE_PAYMENT_NOVALNET_INVOICE_DURATION;
        $payment_duration = trim($payment_duration);
        $payment_duration = str_replace(' ', '', $payment_duration);
        if (!eregi("^[0-9]*$", $payment_duration)) {
            $payment_duration = '';
        }
        $due_date = '';
        $due_date_string = '';
        if ($payment_duration) {
            $due_date = date("d.m.Y", mktime(0, 0, 0, date("m"), date("d") + $payment_duration, date("Y")));
            $due_date_string = '&due_date=' . date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") + $payment_duration, date("Y")));
        }
        $user_ip = $this->getRealIpAddr();
        //set the user telephone
        $tel_param = '&tel=';
        if ($_SESSION['user_tel_invoice'])
            $user_telephone = $_SESSION['user_tel_invoice'];
        else
            $user_telephone = $order->customer['telephone'];
        //set the user email
        if ($_SESSION['user_email_invoice'])
            $user_email = $_SESSION['user_email_invoice'];
        else
            $user_email = $order->customer['email_address'];
        //set the user telephone
        if ($_SESSION['user_tel_invoice']) {
            $user_telephone = $_SESSION['user_tel_invoice'];
        } else {
            $user_telephone = '&tel=' . $order->customer['telephone'];
        }
        // set post params
        if ($this->isActivatedCallback && in_array($billing_iso_code, $this->nninv_allowed_pin_country_list) && $amount >= MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_MIN_LIMIT) {
            if (MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS == 'Callback (Telefon & Handy)') {
                $this->callback_type = '&pin_by_callback=1';
                $user_telephone = '&tel=' . $user_telephone;
            }
            if (MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS == 'SMS (nur Handy)') {
                $this->callback_type = '&pin_by_sms=1';
                $user_telephone = '&mobile=' . $user_telephone;
            }
            if (MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS == 'Email Reply') {
                $this->callback_type = '&reply_email_check=1';
            }
        }
        $firstname = !empty($order->customer['firstname']) ? $order->customer['firstname'] : $order->billing['firstname'];
        $lastname = !empty($order->customer['lastname']) ? $order->customer['lastname'] : $order->billing['lastname'];
        $email_address = !empty($order->customer['email_address']) ? $order->customer['email_address'] : $order->billing['email_address'];
        $street_address = !empty($order->customer['street_address']) ? $order->customer['street_address'] : $order->billing['street_address'];
        $city = !empty($order->customer['city']) ? $order->customer['city'] : $order->billing['city'];
        $postcode = !empty($order->customer['postcode']) ? $order->customer['postcode'] : $order->billing['postcode'];
        $country_iso_code_2 = !empty($order->customer['country']['iso_code_2']) ? $order->customer['country']['iso_code_2'] : $order->billing['country']['iso_code_2'];
        $customer_no = ($customer['customers_status'] != 1) ? $nn_customer_id : MODULE_PAYMENT_NOVALNET_INVOICE_GUEST_USER;

        $urlparam = 'vendor=' . $vendor_id . '&product=' . $product_id . '&key=' . $this->payment_key . '&tariff=' . $tariff_id;
        $urlparam .= '&auth_code=' . $auth_code . '&currency=' . $order->info['currency'];
        $testmode = (strtolower(MODULE_PAYMENT_NOVALNET_INVOICE_TEST_MODE) == 'true' or MODULE_PAYMENT_NOVALNET_INVOICE_TEST_MODE == '1') ? 1 : 0;
        $urlparam .='&test_mode=' . $testmode;
        $urlparam .= '&invoice_type=INVOICE' . $due_date_string;
        $urlparam .= '&first_name=' . $firstname . '&last_name=' . $lastname;
        $urlparam .= '&street=' . $street_address . '&city=' . $city . '&zip=' . $postcode;
        $urlparam .= '&country=' . $country_iso_code_2 . '&email=' . $email_address;
        $urlparam .= '&search_in_street=1' . '&tel=' . $user_telephone . '&remote_ip=' . $user_ip;
        $urlparam .= '&gender=' . $customer['customers_gender'] . '&birth_date=' . $customer['customers_dob'] . '&fax=' . $customer['customers_fax'];
        $urlparam .= '&language=' . MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_LANG;
        $urlparam .= '&lang=' . MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_LANG;
        $urlparam .= '&customer_no=' . $customer_no;
        $urlparam .= '&use_utf8=1';
        $urlparam .= '&amount=' . $amount;
        // For PIN by call back
        $urlparam .= $this->callback_type;
        list($errno, $errmsg, $data) = $this->perform_https_request($url, $urlparam);
        $aryResponse = array();
        #capture the result and message and other parameters from response data '$data' in an array
        $aryPaygateResponse = explode('&', $data);
        foreach ($aryPaygateResponse as $key => $value) {
            if ($value != "") {
                $aryKeyVal = explode("=", $value);
                $aryResponse[$aryKeyVal[0]] = $aryKeyVal[1];
            }
        }
        #Get the type of the comments field on TABLE_ORDERS
        $customer = $db->Execute("SHOW FIELDS FROM " . TABLE_ORDERS_STATUS_HISTORY . " WHERE FIELD='comments'");
        if ($customer->RecordCount() > 0) {
            $customer = $customer->fields;
        }
        if (strtolower($customer['Type']) != 'text') {
            ### ALTER TABLE ORDERS modify the column comments ###
            $db->Execute("ALTER TABLE " . TABLE_ORDERS_STATUS_HISTORY . " MODIFY comments text");
        }
		
        if ($aryResponse['status'] == 100) {
            ### Passing through the Transaction ID from Novalnet's paygate into order-info ###
            if ($this->isActivatedCallback && in_array($billing_iso_code, $this->nninv_allowed_pin_country_list) && $amount >= MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_MIN_LIMIT) {
                $_SESSION['invoice_order_amount'] = $amount;
                $_SESSION['nn_tid_invoice'] = $aryResponse['tid'];
                // To avoide payment method confussion add code in session
                //set session for maximum time limit to 30 minutes
                $_SESSION['max_time_invoice'] = time() + (30 * 60);
                //TEST BILLING MESSAGE BASED ON THE RESPONSE TEST MODE
                $_SESSION['test_mode_invoice'] = $aryResponse['test_mode'];
                $_SESSION['original_amount_invoice'] = $amount / 100;
                $_SESSION['due_date_invoice'] = $due_date;
                ### WRITE THE INVOICE BANK DATA ON SESSION ###     
                $_SESSION['nn_invoice_account'] = $aryResponse['invoice_account'];
                $_SESSION['nn_invoice_bankcode'] = $aryResponse['invoice_bankcode'];
                $_SESSION['nn_invoice_iban'] = $aryResponse['invoice_iban'];
                $_SESSION['nn_invoice_bic'] = $aryResponse['invoice_bic'];
                $_SESSION['nn_invoice_bankname'] = $aryResponse['invoice_bankname'];
                $_SESSION['nn_invoice_bankplace'] = $aryResponse['invoice_bankplace'];
            } else {
                // $old_comments = $order->info['comments'];
                // $order->info['comments'] ="";
                $transferinvoice_info = utf8_encode(MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_TRANSFER_INFO);
                $days_limit = utf8_encode(MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_DURATION_LIMIT_END_INFO);
                $inrenationalinvoice_info = utf8_encode(MODULE_PAYMENT_NOVALNET_TEXT_DETAILS_INVOICE_INTERNATIONAL_INFO);
                $test_mode = (strtolower(MODULE_PAYMENT_NOVALNET_INVOICE_TEST_MODE) == 'true' or MODULE_PAYMENT_NOVALNET_INVOICE_TEST_MODE == '1') ? 1 : 0;
                if ($aryResponse['test_mode'] == 1 || $test_mode) {

                    $order->info['comments'] .= '<b><br>' . MODULE_PAYMENT_NOVALNET_INVOICE_TEST_ORDER_MESSAGE . '</b>';
                }
                //$amount = str_replace('.', ',', sprintf("%.2f", $amount/100));
                $amount = $currencies->format($amount / 100);
                //$order->info['comments'] .= '<br><b>' . MODULE_PAYMENT_NOVALNET_INVOICE_PAYMNETNAME . '</b>';
				if (MODULE_PAYMENT_NOVALNET_INVOICE_LOGO_STATUS == 'True') {
					$order->info['comments'] .= '<b><br>' . 'Novalnet'.' '. MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_TITLE;
				}else 
				{
					$order->info['comments'] .= '<b><br>' . MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_TITLE;
				}
                $order->info['comments'] .= '<br><b>' . MODULE_PAYMENT_NOVALNET_INVOICE_TID . ' ' . $aryResponse['tid'] . '</b><br />';

                if ($due_date) {
                    $order->info['comments'] .= '<br><b>' . $transferinvoice_info . ' ' . '</b>';
                    $order->info['comments'] .= '<br><b>' . $days_limit . ' ' . $due_date . '</b>';
                } else {
                    $order->info['comments'] .= '<br><b>' . $transferinvoice_info . '</b>';
                }
                $order->info['comments'] .= '<br><b>' . MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_BANK_ACCOUNT_OWNER . ' ' . MODULE_PAYMENT_NOVALNET_INVOICE_NAME . '</b>';
                $order->info['comments'] .= '<br><b>' . MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_BANK_ACCOUNT_NUMBER . ' ' . $aryResponse['invoice_account'] . '</b>';
                $order->info['comments'] .= '<br><b>' . MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_BANK_CODE . ' ' . $aryResponse['invoice_bankcode'] . '</b>';
                $order->info['comments'] .= '<br><b>' . MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_BANK_BANK . ' ' . $aryResponse['invoice_bankname'] . ' ' . $aryResponse['invoice_bankplace'] . '</b>';
                //$order->info['comments'] .= '<b>'.MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_AMOUNT.' '.$amount.' '.$order->info['currency'].'</b>';
                $order->info['comments'] .= '<b>' . MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_AMOUNT . ' ' . $amount . '</b>';
                $order->info['comments'] .= '<br><b>' . MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_REFERENCE . ' ' . $aryResponse['tid'] . '<br>';
                $order->info['comments'] .= '<br><b>' . $inrenationalinvoice_info . '</b>';
                $order->info['comments'] .= '<br><b>' . MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_BANK_IBAN . ' ' . $aryResponse['invoice_iban'] . '</b>';
                $order->info['comments'] .= '<br><b>' . MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_BANK_BIC . ' ' . $aryResponse['invoice_bic'] . '</b>';
                $order->info['comments'] .= '<br><b>' . MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_REFERENCE_INFO . '</b>';
                //$order->info['comments'] .= $old_comments;
                $order->info['comments'] = str_replace(array('<B>', '</B>', '</b>', '<b>', '<br>', '<BR>', '<br />', '<BR />'), array('', '', '', '', "\n", "\n", "\n", "\n"), $order->info['comments']);
                $_SESSION['nn_tid_invoice'] = $aryResponse['tid'];
            }
        } else {
            ### Passing through the Error Response from Novalnet's paygate into order-info ###
            $order->info['comments'] .= '. Novalnet Error Code : ' . $aryResponse['status'] . ', Novalnet Error Message : ' . $aryResponse['status_desc'];
            $payment_error_return = 'payment_error=' . $this->code;
            $messageStack->add_session('checkout_payment', $aryResponse['status_desc'] . '<!-- [' . $this->code . '] -->', 'error');
            zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
        }
        return;
    }

    ### Realtime accesspoint for communication to the Novalnet paygate ###

    function perform_https_request($nn_url, $urlparam) {
        $debug = 0; #set it to 1 if you want to activate the debug mode
        if ($debug)
            print "<BR>perform_https_request: $nn_url<BR>\n\r\n";
        if ($debug)
            print "perform_https_request: $urlparam<BR>\n\r\n";
        ## some prerquisites for the connection
        $ch = curl_init($nn_url);
        curl_setopt($ch, CURLOPT_POST, 1);  // a non-zero parameter tells the library to do a regular HTTP post.
        curl_setopt($ch, CURLOPT_POSTFIELDS, $urlparam);  // add POST fields
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);  // don't allow redirects
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  // decomment it if you want to have effective ssl checking
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  // decomment it if you want to have effective ssl checking
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  // return into a variable
        curl_setopt($ch, CURLOPT_TIMEOUT, 240);  // maximum time, in seconds, that you'll allow the CURL functions to take
        if ($this->proxy) {
            curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
        }
        ## establish connection
        $data = curl_exec($ch);
        //$data = $this->ReplaceSpecialGermanChars($data);
        ## determine if there were some problems on cURL execution
        $errno = curl_errno($ch);
        $errmsg = curl_error($ch);

        ###bug fix for PHP 4.1.0/4.1.2 (curl_errno() returns high negative value in case of successful termination)
        if ($errno < 0)
            $errno = 0;
        ##bug fix for PHP 4.1.0/4.1.2

        if ($debug) {
            print_r(curl_getinfo($ch));
            echo "\n<BR><BR>\n\n\nperform_https_request: cURL error number:" . $errno . "\n<BR>\n\n";
            echo "\n\n\nperform_https_request: cURL error:" . $errmsg . "\n<BR>\n\n";
        }

        #close connection
        curl_close($ch);

        ## read and return data from novalnet paygate
        if ($debug)
            print "<BR>\n\n" . $data . "\n<BR>\n\n";

        return array($errno, $errmsg, $data);
    }

    function isPublicIP($value) {
        if (!$value || count(explode('.', $value)) != 4)
            return false;
        return !preg_match('~^((0|10|172\.16|192\.168|169\.254|255|127\.0)\.)~', $value);
    }

    ### get the real Ip Adress of the User ###

    function getRealIpAddr() {
        if ($_SERVER['HTTP_X_FORWARDED_FOR'] and $this->isPublicIP($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        if ($_SERVER['HTTP_X_FORWARDED_FOR'] and $iplist = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])) {
            if ($this->isPublicIP($iplist[0]))
                return $iplist[0];
        }
        if ($_SERVER['HTTP_CLIENT_IP'] and $this->isPublicIP($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        if ($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'] and $this->isPublicIP($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) {
            return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
        }
        if ($_SERVER['HTTP_FORWARDED_FOR'] and $this->isPublicIP($_SERVER['HTTP_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_FORWARDED_FOR'];
        }
        return $_SERVER['REMOTE_ADDR'];
    }

    ### replace the Special German Charectors ###

    function ReplaceSpecialGermanChars($string) {
        $what = array("ä", "ö", "ü", "Ä", "Ö", "Ü", "ß");
        $how = array("ae", "oe", "ue", "Ae", "Oe", "Ue", "ss");

        $string = str_replace($what, $how, $string);

        return $string;
    }

    ### Send the order detail to Novalnet ###

    function after_process() {
        global $order, $customer_id, $insert_id, $db;

        if ($_SESSION['nn_tid_invoice'] != '') {
            ### Pass the Order Reference to paygate ##
            $url = 'https://payport.novalnet.de/paygate.jsp';
            $urlparam = 'vendor=' . $this->vendor_id . '&product=' . $this->product_id . '&key=' . $this->payment_key . '&tariff=' . $this->tariff_id;
            $urlparam .= '&auth_code=' . $this->auth_code . '&status=100&tid=' . $_SESSION['nn_tid_invoice'];
            $urlparam .= '&order_no=' . $insert_id;
            $urlparam .= "&invoice_ref=BNR-" . $this->product_id . "-" . $insert_id;
            list($errno, $errmsg, $data) = $this->perform_https_request($url, $urlparam);
        }
        unset($_SESSION['user_tel_invoice']);
        unset($_SESSION['nn_tid_invoice']);
        unset($_SESSION['max_time_invoice']);
        if (isset($_SESSION['invalid_count_invoice'])) {
            unset($_SESSION['invalid_count_invoice']);
        }

        #print "$customer_id, $insert_id"; exit;
        ### Implement here the Emailversand and further functions, incase if you want to send a own email ###
        /*
          $db->Execute("update ".TABLE_ORDERS_STATUS_HISTORY." set comments = '".$order->info['comments']."' , orders_status_id= '".$this->order_status."' where orders_id = '".$insert_id."'");
          $db->Execute("update ".TABLE_ORDERS." set orders_status = '".$this->order_status."' where orders_id = '".$insert_id."'");
         */

        return false;
    }

    ### Used to display error message details ###
    // @return array

    function get_error() {
        global $HTTP_GET_VARS, $_GET;
        if (count($HTTP_GET_VARS) == 0 || $HTTP_GET_VARS == '')
            $HTTP_GET_VARS = $_GET;

        $error = array('title' => MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_ERROR,
            'error' => stripslashes(urldecode($HTTP_GET_VARS['error'])));

        return $error;
    }

    ### Check to see whether module is installed ###
    // @return boolean

    function check() {
        global $db;
        if (!isset($this->_check)) {
            $check_query = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_NOVALNET_INVOICE_STATUS'");
            $this->_check = $check_query->RecordCount();
        }
        return $this->_check;
    }

    function install_lang($field_text, $lang) {
        #Allowed Zones
        $install_text['allowed_title'] = array('en' => "Allowed zones",
            'de' => "erlaubte Zonen");
        $install_text['allowed_desc'] = array('en' => "Please enter the desired zones separated by comma (Eg: AT, DE) or leave it blank",
            'de' => "Bitte die gew&uuml;nschten Zonen durch Komma getrennt eingeben (z.B: AT,DE) oder einfach leer lassen");
        #Enable Module								   
        $install_text['enable_title'] = array('en' => "Enable Module",
            'de' => "Modul aktivieren");
        $install_text['enable_desc'] = array('en' => "Do you want to activate the Credit Card of Novalnet AG?",
            'de' => "Wollen Sie das Kreditkarten Modul des Novalnet AG aktivieren?");
        #Test Mode								   
        $install_text['test_title'] = array('en' => "Enable Test Mode:",
            'de' => "Testmodus einschalten");
        $install_text['test_desc'] = array('en' => "Do you want to activate test mode?",
            'de' => "Wollen Sie den Test-Modus aktivieren?");
        #Vendor id							   
        $install_text['vendor_title'] = array('en' => "Novalnet Merchant ID",
            'de' => "Novalnet H&auml;ndler ID");
        $install_text['vendor_desc'] = array('en' => "Enter your Novalnet Merchant ID ",
            'de' => "Geben Sie Ihre Novalnet H&auml;ndler-ID ein ");
        #Auth Code
        $install_text['auth_title'] = array('en' => "Novalnet Merchant Authorisation Code",
            'de' => "Novalnet Authorisierungsschl&uuml;ssel");
        $install_text['auth_desc'] = array('en' => "Enter your Novalnet Merchant Authorisation code ",
            'de' => "Geben Sie Ihren Novalnet-Authorisierungsschl&uuml;ssel ein");

        #Product id
        $install_text['product_title'] = array('en' => "Novalnet Product ID",
            'de' => "Novalnet Produkt ID");
        $install_text['product_desc'] = array('en' => "Enter your Novalnet Product ID",
            'de' => "Geben Sie Ihre Novalnet Produkt-ID ein");

        #Tariff id
        $install_text['tariff_title'] = array('en' => "Novalnet Tariff ID",
            'de' => "Novalnet Tarif ID");
        $install_text['tariff_desc'] = array('en' => "Enter your Novalnet Tariff ID ",
            'de' => "Geben Sie Ihre Novalnet Tarif-ID ein");

        #Enduser info
        $install_text['enduser_title'] = array('en' => "Information to the end customer",
            'de' => "Informationen f&uuml;r den Endkunden");
        $install_text['enduser_desc'] = array('en' => "will appear in the payment form",
            'de' => "wird im Bezahlformular erscheinen");

        #Sortorder display
        $install_text['sortorder_title'] = array('en' => "Sort order of display",
            'de' => "Sortierung nach");
        $install_text['sortorder_desc'] = array('en' => "Sort order of display. Lowest is displayed first.",
            'de' => "Sortierung der Anzeige. Der niedrigste Wert wird zuerst angezeigt.");

        #Setorder status display
        $install_text['setorderstatus_title'] = array('en' => "Set Order Status",
            'de' => "Bestellungsstatus setzen");
        $install_text['setorderstatus_desc'] = array('en' => "Set the status of orders made with this payment module to this value.",
            'de' => "Setzen Sie den Status von &uuml;ber dieses Zahlungsmodul durchgef&uuml;hrten Bestellungen auf diesen Wert.");

        #Proxy
        $install_text['proxy_title'] = array('en' => "Proxy-Server",
            'de' => "Proxy-Server");
        $install_text['proxy_desc'] = array('en' => " If you use a Proxy Server, enter the Proxy Server IP with port here (e.g. www.proxy.de:80).",
            'de' => "Wenn Sie einen Proxy-Server einsetzen, tragen Sie hier Ihre Proxy-IP und den Port ein (z.B. www.proxy.de:80).");

        #Payment Zone
        $install_text['paymnetzone_title'] = array('en' => "Payment Zone",
            'de' => "Zahlungsgebiet");
        $install_text['paymnetzone_desc'] = array('en' => "If a zone is selected then this module is activated only for Selected zone. ",
            'de' => "Wird ein Bereich ausgew&auml;hlt, dann wird dieses Modul nur f&uuml;r den ausgew&auml;hlten Bereich aktiviert.");




        #Novalnet Payment Duration
        $install_text['timelimit_title'] = array('en' => "Payment period in days",
            'de' => "Zahlungsfrist in tagen");
        $install_text['timelimit_desc'] = array('en' => "Payment duration of the Invoice in Days",
            'de' => "Payment Dauer der Rechnung in Tagen");


        #Pin by callback sms
        $install_text['pinbycallback_title'] = array('en' => "PIN by Callback/SMS/E-Mail",
            'de' => "PIN by Callback/SMS/E-Mail");
        $install_text['pinbycallback_desc'] = array('en' => "When activated by PIN Callback / SMS / E-Mail the customer to enter their phone / mobile number / E-Mail requested. By phone or SMS, the customer receives a PIN from Novalnet AG, which must enter before ordering. If the PIN is valid, the payment process has been completed successfully, otherwise the customer will be prompted again to enter the PIN. This service is only available for customers from specified countries.",
            'de' => "Wenn durch PIN Callback / SMS / E-Mail des Kunden aktiviert, um ihre Telefonnummer / Handynummer / E-Mail angefordert geben. Per Telefon oder SMS, erh&auml;lt der Kunde eine PIN von Novalnet AG, die vor der Bestellung eingeben m&uuml;ssen. Wenn die PIN g&uuml;ltig ist, hat die Zahlung Prozess erfolgreich beendet wurde, andernfalls hat der Kunde erneut aufgefordert, die PIN einzugeben. Dieser Service ist nur f&uuml;r Kunden aus bestimmten L&auml;ndern.");

        #Manual Amount Limit For Pin by callback/sms
        $install_text['amountlimitpin_title'] = array('en' => "Minimum Amount Limit for Callback in cents",
            'de' => "Grenzwert (Mindestbetrag) in Cent für Rückruf");
        $install_text['amountlimitpin_desc'] = array('en' => "Please enter minimum amount limit to enable Pin by CallBackmodule (In Cents, e.g. 100,200)",
            'de' => "Bitte geben Sie Mindestbetrag Grenze zu Pin durch CallBack Modul (in Cent, z. B. 100,200) erm&Ouml;glichen");

        #Activate Logo Mode
        $install_text['logo_title'] = array('en' => "Activate logo mode:",
            'de' => "Aktivieren Sie Logo Modus:");
        $install_text['logo_desc'] = array('en' => "Do you want to activate logo mode?",
            'de' => "Wollen Sie Logo-Modus zu aktivieren?");

        return $install_text[$field_text][$lang];
    }

    ### Install the payment module and its configuration settings ###

    function install() {
        global $db;

        $allowed_title = $this->install_lang('allowed_title', DEFAULT_LANGUAGE);
        $allowed_desc = $this->install_lang('allowed_desc', DEFAULT_LANGUAGE);

        $enable_title = $this->install_lang('enable_title', DEFAULT_LANGUAGE);
        $enable_desc = $this->install_lang('enable_desc', DEFAULT_LANGUAGE);

        $test_title = $this->install_lang('test_title', DEFAULT_LANGUAGE);
        $test_desc = $this->install_lang('test_desc', DEFAULT_LANGUAGE);

        $vendor_title = $this->install_lang('vendor_title', DEFAULT_LANGUAGE);
        $vendor_desc = $this->install_lang('vendor_desc', DEFAULT_LANGUAGE);

        $auth_title = $this->install_lang('auth_title', DEFAULT_LANGUAGE);
        $auth_desc = $this->install_lang('auth_desc', DEFAULT_LANGUAGE);

        $product_title = $this->install_lang('product_title', DEFAULT_LANGUAGE);
        $product_desc = $this->install_lang('product_desc', DEFAULT_LANGUAGE);

        $tariff_title = $this->install_lang('tariff_title', DEFAULT_LANGUAGE);
        $tariff_desc = $this->install_lang('tariff_desc', DEFAULT_LANGUAGE);

        $enduser_title = $this->install_lang('enduser_title', DEFAULT_LANGUAGE);
        $enduser_desc = $this->install_lang('enduser_desc', DEFAULT_LANGUAGE);

        $sortorder_title = $this->install_lang('sortorder_title', DEFAULT_LANGUAGE);
        $sortorder_desc = $this->install_lang('sortorder_desc', DEFAULT_LANGUAGE);

        $setorderstatus_title = $this->install_lang('setorderstatus_title', DEFAULT_LANGUAGE);
        $setorderstatus_desc = $this->install_lang('setorderstatus_desc', DEFAULT_LANGUAGE);

        $proxy_title = $this->install_lang('proxy_title', DEFAULT_LANGUAGE);
        $proxy_desc = $this->install_lang('proxy_desc', DEFAULT_LANGUAGE);

        $paymnetzone_title = $this->install_lang('paymnetzone_title', DEFAULT_LANGUAGE);
        $paymnetzone_desc = $this->install_lang('paymnetzone_desc', DEFAULT_LANGUAGE);

        $timelimit_title = $this->install_lang('timelimit_title', DEFAULT_LANGUAGE);
        $timelimit_desc = $this->install_lang('timelimit_desc', DEFAULT_LANGUAGE);

        $pinbycallback_title = $this->install_lang('pinbycallback_title', DEFAULT_LANGUAGE);
        $pinbycallback_desc = $this->install_lang('pinbycallback_desc', DEFAULT_LANGUAGE);

        $amountlimitpin_title = $this->install_lang('amountlimitpin_title', DEFAULT_LANGUAGE);
        $amountlimitpin_desc = $this->install_lang('amountlimitpin_desc', DEFAULT_LANGUAGE);

        $logo_title = $this->install_lang('logo_title', DEFAULT_LANGUAGE);
        $logo_desc = $this->install_lang('logo_desc', DEFAULT_LANGUAGE);


        /* $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('".$allowed_title."','MODULE_PAYMENT_NOVALNET_INVOICE_ALLOWED', '','".$allowed_desc."', '6', '0', now())"); */

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('" . $enable_title . "', 'MODULE_PAYMENT_NOVALNET_INVOICE_STATUS', 'True', '" . $enable_desc . "', '6', '0', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('" . $test_title . "', 'MODULE_PAYMENT_NOVALNET_INVOICE_TEST_MODE', 'True', '" . $test_desc . "', '6', '1', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('" . $pinbycallback_title . "','MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS','False','" . $pinbycallback_desc . "', '6', '2', 'zen_cfg_select_option(array( \'False\', \'Callback (Telefon & Handy)\', \'SMS (nur Handy)\',\'Email Reply\'), ', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . $amountlimitpin_title . "','MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_MIN_LIMIT', '','" . $amountlimitpin_desc . "', '6', '3', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . $vendor_title . "', 'MODULE_PAYMENT_NOVALNET_INVOICE_VENDOR_ID', '', '" . $vendor_desc . "', '6', '4', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . $auth_title . "', 'MODULE_PAYMENT_NOVALNET_INVOICE_AUTH_CODE', '', '" . $auth_desc . "', '6', '5', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . $product_title . "', 'MODULE_PAYMENT_NOVALNET_INVOICE_PRODUCT_ID', '', '" . $product_desc . "', '6', '6', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . $tariff_title . "', 'MODULE_PAYMENT_NOVALNET_INVOICE_TARIFF_ID', '', '" . $tariff_desc . "', '6', '7', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . $timelimit_title . "', 'MODULE_PAYMENT_NOVALNET_INVOICE_DURATION', '', '" . $timelimit_desc . "', '6', '8', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . $enduser_title . "', 'MODULE_PAYMENT_NOVALNET_INVOICE_INFO', '', '" . $enduser_desc . "', '6', '9', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . $sortorder_title . "', 'MODULE_PAYMENT_NOVALNET_INVOICE_SORT_ORDER', '0', '" . $sortorder_desc . "', '6', '10', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('" . $setorderstatus_title . "', 'MODULE_PAYMENT_NOVALNET_INVOICE_ORDER_STATUS_ID', '0', '" . $setorderstatus_desc . "', '6', '11', 'zen_cfg_pull_down_order_statuses(', 'zen_get_order_status_name', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('" . $paymnetzone_title . "', 'MODULE_PAYMENT_NOVALNET_INVOICE_ZONE', '0', '" . $paymnetzone_desc . "', '6', '12', 'zen_get_zone_class_title', 'zen_cfg_pull_down_zone_classes(', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . $proxy_title . "', 'MODULE_PAYMENT_NOVALNET_INVOICE_PROXY', '', '" . $proxy_desc . "', '6', '13', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('" . $logo_title . "', 'MODULE_PAYMENT_NOVALNET_INVOICE_LOGO_STATUS', 'True', '" . $logo_desc . "', '6', '14', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
    }

    ### Remove the module and all its settings ###

    function remove() {
        global $db;
        $db->Execute("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    ### Internal list of configuration keys used for configuration of the module ###
    // @return array

    function keys() {
        return array( 'MODULE_PAYMENT_NOVALNET_INVOICE_LOGO_STATUS', 'MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS', 'MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_MIN_LIMIT', 'MODULE_PAYMENT_NOVALNET_INVOICE_STATUS', 'MODULE_PAYMENT_NOVALNET_INVOICE_TEST_MODE',
            'MODULE_PAYMENT_NOVALNET_INVOICE_VENDOR_ID', 'MODULE_PAYMENT_NOVALNET_INVOICE_AUTH_CODE', 'MODULE_PAYMENT_NOVALNET_INVOICE_PRODUCT_ID',
            'MODULE_PAYMENT_NOVALNET_INVOICE_TARIFF_ID', 'MODULE_PAYMENT_NOVALNET_INVOICE_DURATION', 'MODULE_PAYMENT_NOVALNET_INVOICE_INFO',
            'MODULE_PAYMENT_NOVALNET_INVOICE_SORT_ORDER', 'MODULE_PAYMENT_NOVALNET_INVOICE_ORDER_STATUS_ID', 'MODULE_PAYMENT_NOVALNET_INVOICE_ZONE',
            'MODULE_PAYMENT_NOVALNET_INVOICE_PROXY');
    }

    function html_to_utf8($data) {
        return preg_replace("/\\&\\#([0-9]{3,10})\\;/e", '$this->_html_to_utf8("\\1")', $data);
    }

    function _html_to_utf8($data) {
        if ($data > 127) {
            $i = 5;
            while (($i--) > 0) {
                if ($data != ($a = $data % ($p = pow(64, $i)))) {
                    $ret = chr(base_convert(str_pad(str_repeat(1, $i + 1), 8, "0"), 2, 10) + (($data - $a) / $p));
                    for ($i; $i > 0; $i--)
                        $ret .= chr(128 + ((($data % pow(64, $i)) - ($data % ($p = pow(64, $i - 1)))) / $p));
                    break;
                }
            }
        } else {
            $ret = "&#$data;";
        }
        return $ret;
    }

}

/*
  order of functions:
  selection              -> $order-info['total'] wrong, cause shipping_cost is net
  pre_confirmation_check -> $order-info['total'] wrong, cause shipping_cost is net
  confirmation           -> $order-info['total'] right, cause shipping_cost is gross
  process_button         -> $order-info['total'] right, cause shipping_cost is gross
  before_process         -> $order-info['total'] wrong, cause shipping_cost is net
  after_process          -> $order-info['total'] right, cause shipping_cost is gross
 */
?>
