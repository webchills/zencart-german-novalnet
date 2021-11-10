<?php
#########################################################
#                                                       #
#  ELVAT / DIRECT DEBIT payment method class            #
#  This module is used for real time processing of      #
#  Austrian Bankdata of customers.                      #
#                                                       #
#  Released under the GNU General Public License.       #
#  This free contribution made by request.              #
#  If you have found this script useful a small         #
#  recommendation as well as a comment on merchant form #
#  would be greatly appreciated.                        #
#                                                       #
#  Script : novalnet_elv_at.php                         #
#                                                       #
#########################################################
class novalnet_elv_at {

    var $code;
    var $title;
    var $description;
    var $enabled;
    var $proxy;
    var $callback_type;
    var $payment_key = '8';
    var $vendor_id;
    var $auth_code;
    var $product_id;
    var $tariff_id;
    var $manual_check_limit;
    var $product_id2;
    var $tariff_id2;
    var $nnelvat_allowed_pin_country_list = array('de', 'at', 'ch');

    function novalnet_elv_at() {
        global $order;

        $this->vendor_id = trim(MODULE_PAYMENT_NOVALNET_ELV_AT_VENDOR_ID);
        $this->auth_code = trim(MODULE_PAYMENT_NOVALNET_ELV_AT_AUTH_CODE);
        $this->product_id = trim(MODULE_PAYMENT_NOVALNET_ELV_AT_PRODUCT_ID);
        $this->tariff_id = trim(MODULE_PAYMENT_NOVALNET_ELV_AT_TARIFF_ID);
        $this->manual_check_limit = trim(MODULE_PAYMENT_NOVALNET_ELV_AT_MANUAL_CHECK_LIMIT);
        $this->product_id2 = trim(MODULE_PAYMENT_NOVALNET_ELV_AT_PRODUCT_ID2);
        $this->tariff_id2 = trim(MODULE_PAYMENT_NOVALNET_ELV_AT_TARIFF_ID2);
        $this->test_mode = (strtolower(MODULE_PAYMENT_NOVALNET_ELV_AT_TEST_MODE) == 'true' or MODULE_PAYMENT_NOVALNET_ELV_AT_TEST_MODE == '1') ? 1 : 0;

        $this->code = 'novalnet_elv_at';
        $this->title = MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_TITLE;
        $this->public_title = MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_PUBLIC_TITLE;
        $this->description = MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_DESCRIPTION;
        $this->sort_order = MODULE_PAYMENT_NOVALNET_ELV_AT_SORT_ORDER;
        $this->enabled = ((MODULE_PAYMENT_NOVALNET_ELV_AT_STATUS == 'True') ? true : false);
        $this->proxy = MODULE_PAYMENT_NOVALNET_ELV_AT_PROXY;
        

        if (MODULE_PAYMENT_NOVALNET_ELV_AT_LOGO_STATUS == 'True') {
            $this->public_title =  'Novalnet'.' '. MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_PUBLIC_TITLE;
			$this->title = 'Novalnet'.' '.MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_TITLE;
        }
		$this->title  = html_entity_decode($this->title, ENT_QUOTES, "UTF-8");
		$this->checkConfigure();

        if ((int) MODULE_PAYMENT_NOVALNET_ELV_AT_ORDER_STATUS_ID > 0) {
            $this->order_status = MODULE_PAYMENT_NOVALNET_ELV_AT_ORDER_STATUS_ID;
        }
        if (is_object($order))
            $this->update_status();

        // Check the tid in session and make the second call
        if ($_SESSION['nn_tid_elv_at']) {
            //echo $_SESSION['customer_id'];
            if ((empty($_SESSION['invalid_count_at'])) || ( isset($_SESSION['max_time_elv_at']) && (time() >= $_SESSION['max_time_elv_at']))) {
                $_SESSION['invalid_count_at'] = 0;
            }
            if (!empty($_SESSION['invalid_count_at']) && $_SESSION['invalid_count_at'] == 3) {

                if ($_SESSION['max_time_elv_at'] && (time() < $_SESSION['max_time_elv_at'])) {
                    $payment_error_return = 'payment_error=' . $this->code . '&error=' . utf8_encode(MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_BY_CALLBACK_SESSION_ERROR);
                    //$payment_error_return = MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_BY_CALLBACK_SESSION_ERROR;
                }
            }

            //Check the time limit
            if ($_SESSION['max_time_elv_at'] && time() > $_SESSION['max_time_elv_at']) {
                unset($_SESSION['nn_tid_elv_at']);
                unset($_SESSION['invalid_count_at']);
                $payment_error_return = 'payment_error=' . $this->code;
                $messageStack->add_session('checkout_payment', MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_BY_CALLBACK_SESSION_ERROR . '<!-- [' . $this->code . '] -->', 'error');
                zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
            }

            if ($_GET['new_novalnet_pin_elv_at'] == 'true') {
                $_SESSION['new_novalnet_pin_elv_at'] = true;
                $this->secondcall();
            }
        }

        // define callback types
        $this->isActivatedCallback = false;
        if (MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_BY_CALLBACK_SMS != 'False') {
            $this->isActivatedCallback = true;
        }
    }

    function checkConfigure() {
        if (IS_ADMIN_FLAG == true) {
            $this->title = MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_TITLE; // Payment module title in Admin
			if (MODULE_PAYMENT_NOVALNET_ELV_AT_LOGO_STATUS == 'True') {
				$this->public_title =  'Novalnet'.' '. MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_PUBLIC_TITLE;
				$this->title = 'Novalnet'.' '.MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_TITLE;
			}
            if ($this->enabled == 'true' && (!$this->vendor_id || !$this->auth_code || !$this->product_id || !$this->tariff_id )) {
                $this->title .= '<span class="alert">' . MODULE_PAYMENT_NOVALNET_ELV_AT_NOT_CONFIGURED . '</span>';
            } elseif ($this->test_mode == '1') {
                $this->title .= '<span class="alert">' . MODULE_PAYMENT_NOVALNET_ELV_AT_IN_TEST_MODE . '</span>';
            }
        }
    }

    ### calculate zone matches and flag settings to determine whether this module should display to customers or not ###

    function update_status() {
        global $order, $db;

        if (($this->enabled == true) && ((int) MODULE_PAYMENT_NOVALNET_ELV_AT_ZONE > 0)) {
            $check_flag = false;
            $check = $db->Execute("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_NOVALNET_ELV_AT_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
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
        global $order, $_POST, $HTTP_POST_VARS;

        $onFocus = ' onfocus="methodSelect(\'pmt-' . $this->code . '\')"';
        $billing_iso_code = strtolower($order->customer['country']['iso_code_2']);
        $bank_account = '';
        if (isset($_POST['bank_account_at'])) {
            $bank_account = $_POST['bank_account_at'];
        }
        if (!$bank_account and isset($_GET['bank_account_at'])) {
            $bank_account = $_GET['bank_account_at'];
        }
        $bank_code = '';
        if (isset($_POST['bank_code_at'])) {
            $bank_code = $_POST['bank_code_at'];
        }
        if (!$bank_code and isset($_GET['bank_code_at'])) {
            $bank_code = $_GET['bank_code_at'];
        }


        if (!$_SESSION['nn_tid_elv_at']) {
            $selection = array('id' => $this->code,
                'module' => $this->title,
                'fields' => array(array('title' => MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_BANK_ACCOUNT_OWNER,
                        'field' => zen_draw_input_field('bank_account_holder_at', $order->billing['firstname'] . ' ' . $order->billing['lastname'], 'id="' . $this->code . '-bank_account_holder_at" AUTOCOMPLETE="OFF"' . $onFocus),
                        'tag' => $this->code . '-bank_account_holder_at'),
                    array('title' => MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_BANK_ACCOUNT_NUMBER,
                        'field' => zen_draw_input_field('bank_account_at', '', 'id="' . $this->code . '-bank_account_at" AUTOCOMPLETE="OFF"' . $onFocus),
                        'tag' => $this->code . '-bank_account_at'),
                    array('title' => MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_BANK_CODE,
                        'field' => zen_draw_input_field('bank_code_at', '', 'id="' . $this->code . '-bank_code_at" AUTOCOMPLETE="OFF"' . $onFocus),
                        'tag' => $this->code . '-bank_code_at'),
                    array('title' => '', 'field' => MODULE_PAYMENT_NOVALNET_INFORMATION_PAYMENT_AT),
                    array('title' => '', 'field' => MODULE_PAYMENT_NOVALNET_ELV_AT_INFO)
                    ));


            // Display callback fields
            $amount_check = $this->findTotalAmount();

            if ($this->isActivatedCallback && in_array($billing_iso_code, $this->nnelvat_allowed_pin_country_list) && $amount_check >= MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_BY_CALLBACK_MIN_LIMIT) {
                if (MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_BY_CALLBACK_SMS == 'Email Reply') {
                    $_SESSION['user_email_elv_at'] = ($_SESSION['user_email_elv_at'] == '') ? $order->customer['email_address'] : $_SESSION['user_email_elv_at'];

                    $selection['fields'][] = array('title' => MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_BY_CALLBACK_EMAIL_REQ, 'field' => zen_draw_input_field('user_email_elv_at', $_SESSION['user_email_elv_at'], 'id="' . $this->code . '-callback" AUTOCOMPLETE="OFF"' . $onFocus));
                } else {
                    $_SESSION['user_tel_elv_at'] = ($_SESSION['user_tel_elv_at'] == '') ? $order->customer['telephone'] : $_SESSION['user_tel_elv_at'];

                    $label_str = (MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_BY_CALLBACK_SMS == 'Callback (Telefon & Handy)') ? MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_BY_CALLBACK_TEL_REQ : MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_BY_CALLBACK_SMS_REQ;

                    $selection['fields'][] = array('title' => $label_str, 'field' => zen_draw_input_field('user_tel_elv_at', $_SESSION['user_tel_elv_at'], 'id="' . $this->code . '-callback" AUTOCOMPLETE="OFF"' . $onFocus));
                }
            }
        }
        $amount_check = $_SESSION['nn_amount_elv_at'];

        if ($this->isActivatedCallback && in_array($billing_iso_code, $this->nnelvat_allowed_pin_country_list) && $amount_check >= MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_BY_CALLBACK_MIN_LIMIT && isset($_SESSION['nn_tid_elv_at']) && ($_SESSION['invalid_count_at'] < 3)) {

            $selection = array('id' => $this->code, 'module' => $this->public_title);
            if (MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_BY_CALLBACK_SMS == 'Email Reply') {
                $selection['fields'][] = array('title' => MODULE_PAYMENT_NOVALNET_ELV_AT_EMAIL_INPUT_REQUEST_DESC);
            } else {
                $selection = array('id' => $this->code,
                    'module' => $this->public_title);
                // Show PIN field, after first call
                $selection['fields'][] = array('title' => MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_BY_CALLBACK_SMS_PIN, 'field' => zen_draw_input_field('novalnet_pin_elv_at', '', 'id="' . $this->code . '-callback" AUTOCOMPLETE="OFF"' . $onFocus));
                $selection['fields'][] = array('title' => '<a href="' . zen_href_link(FILENAME_CHECKOUT_PAYMENT, 'new_novalnet_pin_elv_at=true', 'SSL', true, false) . '">' . MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_BY_CALLBACK_SMS_NEW_PIN . '</a>');
            }
        }


        return $selection;
    }

    ### Precheck to Evaluate the Bank Datas ###

    function pre_confirmation_check() {
        global $HTTP_POST_VARS, $_POST, $order, $messageStack;
        $billing_iso_code = strtolower($order->customer['country']['iso_code_2']);
        if (count($HTTP_POST_VARS) == 0 || $HTTP_POST_VARS == '')
            $HTTP_POST_VARS = $_POST;

        $HTTP_POST_VARS['bank_account_holder_at'] = trim($HTTP_POST_VARS['bank_account_holder_at']);
        $HTTP_POST_VARS['bank_account_at'] = trim($HTTP_POST_VARS['bank_account_at']);
        $HTTP_POST_VARS['bank_code_at'] = trim($HTTP_POST_VARS['bank_code_at']);


        if (isset($HTTP_POST_VARS['user_tel_elv_at']))
            $HTTP_POST_VARS['user_tel_elv_at'] = trim($HTTP_POST_VARS['user_tel_elv_at']);

        if (isset($HTTP_POST_VARS['user_email_elv_at']))
            $HTTP_POST_VARS['user_email_elv_at'] = trim($HTTP_POST_VARS['user_email_elv_at']);

        if (isset($HTTP_POST_VARS['novalnet_pin_elv_at']))
            $HTTP_POST_VARS['novalnet_pin_elv_at'] = trim($HTTP_POST_VARS['novalnet_pin_elv_at']);


        // Callback stuff....

        if ($_SESSION['nn_tid_elv_at']) {
            //check the amount is equal with the first call or not
            $amount = $this->findTotalAmount();
            if ($_SESSION['elv_at_order_amount'] != $amount) {

                if (MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_BY_CALLBACK_SMS == 'Email Reply') {
                    $error_message = MODULE_PAYMENT_NOVALNET_ELV_AT_AMOUNT_VARIATION_MESSAGE_EMAIL;
                } elseif (MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_BY_CALLBACK_SMS == 'Callback (Telefon & Handy)' || MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_BY_CALLBACK_SMS == 'SMS (nur Handy)') {
                    $error_message = MODULE_PAYMENT_NOVALNET_ELV_AT_AMOUNT_VARIATION_MESSAGE;
                }

                unset($_SESSION['nn_tid_elv_at']);
                unset($_SESSION['elv_at_order_amount']);

                if (isset($_SESSION['invalid_count_at'])) {
                    unset($_SESSION['invalid_count_at']);
                }
                $payment_error_return = 'payment_error=' . $this->code;
                $messageStack->add_session('checkout_payment', $error_message . '<!-- [' . $this->code . '] -->', 'error');
                zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
            }
            if (isset($HTTP_POST_VARS['novalnet_pin_elv_at']) && isset($_SESSION['nn_tid_elv_at'])) {
                // check pin
                //if( !is_numeric( $HTTP_POST_VARS['novalnet_pin_elv_at'] ) || strlen( $HTTP_POST_VARS['novalnet_pin_elv_at'] ) != 4 )
                if ($HTTP_POST_VARS['novalnet_pin_elv_at'] == '' || (preg_match('/[&_#%\^<>@$=*!]/', $HTTP_POST_VARS['novalnet_pin_elv_at']))) {
                    $payment_error_return = 'payment_error=' . $this->code . '&error=' . utf8_encode(MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_BY_CALLBACK_SMS_PIN_NOTVALID);
                    zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
                } else {
                    if ($HTTP_POST_VARS['novalnet_pin_elv_at'])
                        $_SESSION['novalnet_pin_elv_at'] = $HTTP_POST_VARS['novalnet_pin_elv_at'];
                }
            }
            return;
        }else {
            $error = '';

            if (!function_exists('curl_init') && ($this->_code == 'novalnet_elv_at')) {
                ini_set('display_errors', 1);
                ini_set('error_reporting', E_ALL);

                $error = MODULE_PAYMENT_NOVALNET_ELV_AT_CURL_MESSAGE;
            }

            if (!isset($_SESSION['nn_tid_elv_at'])) {


                if (!$this->vendor_id || !$this->auth_code || !$this->product_id || !$this->tariff_id) {
                    $error = MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_JS_NN_MISSING;
                } elseif (!empty($this->manual_check_limit) && (!$this->product_id2 || !$this->tariff_id2)) {

                    $error = MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_JS_NN_ID2_MISSING;
                } elseif (!$HTTP_POST_VARS['bank_account_holder_at'] || (preg_match('/[#%\^<>@$=*!]/', $HTTP_POST_VARS['bank_account_holder_at']))) {
                    $error = MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_JS_BANK_ACCOUNT_OWNER;
                } elseif (preg_match('/[^\d]/', $HTTP_POST_VARS['bank_account_at'])) {
                    $error = MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_JS_BANK_ERROR_ACCOUNT_NUMBER;
                } elseif (!$HTTP_POST_VARS['bank_account_at'] || strlen($HTTP_POST_VARS['bank_account_at']) < MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_BANK_ACCOUNT_NUMBER_LENGTH) {
                    $error = MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_JS_BANK_ACCOUNT_NUMBER;
                } elseif (preg_match('/[^\d]/', $HTTP_POST_VARS['bank_code_at'])) {
                    $error = MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_JS_BANK_CODE_ERROR;
                } elseif (!$HTTP_POST_VARS['bank_code_at'] || strlen($HTTP_POST_VARS['bank_code_at']) < MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_BANK_CODE_LENGTH) {
                    $error = MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_JS_BANK_CODE;
                }
                $_SESSION['bank_account_holder_at'] = $HTTP_POST_VARS['bank_account_holder_at'];
                $_SESSION['bank_code_at'] = $HTTP_POST_VARS['bank_code_at'];
                $_SESSION['bank_account_at'] = $HTTP_POST_VARS['bank_account_at'];

                if (isset($HTTP_POST_VARS['user_email_elv_at'])) {
                    $_SESSION['user_email_elv_at'] = $HTTP_POST_VARS['user_email_elv_at'];
                }

                if (isset($HTTP_POST_VARS['user_tel_elv_at'])) {
                    $_SESSION['user_tel_elv_at'] = $HTTP_POST_VARS['user_tel_elv_at'];
                }


                // Callback stuff....
                //$_SESSION['nn_amount_elv_at'] = $this->findTotalAmount();
                $amount_check = $this->findTotalAmount();
                if ($this->isActivatedCallback && in_array($billing_iso_code, $this->nnelvat_allowed_pin_country_list) && $amount_check >= MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_BY_CALLBACK_MIN_LIMIT) {


                    //checking email address
                    if (isset($HTTP_POST_VARS['user_email_elv_at'])) {
                        if (!trim($HTTP_POST_VARS['user_email_elv_at']) || !eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $HTTP_POST_VARS['user_email_elv_at'])) {
                            $error = MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_BY_CALLBACK_EMAIL_NOTVALID;
                        }
                    }

                    //checking telephone number
                    if (isset($HTTP_POST_VARS['user_tel_elv_at'])) {
                        if (strlen($HTTP_POST_VARS['user_tel_elv_at']) < 8 || !is_numeric($HTTP_POST_VARS['user_tel_elv_at'])) {
                            $error = MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_BY_CALLBACK_SMS_TEL_NOTVALID;
                        }
                    }
                    if ($error != '') {
                        /* $payment_error_return = 'payment_error=' . $this->code . '&error=' . utf8_encode($error);
                          zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false)); */

                        $payment_error_return = 'payment_error=' . $this->code;
                        $messageStack->add_session('checkout_payment', utf8_encode($error) . '<!-- [' . $this->code . '] -->', 'error');
                        zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
                    } else {

                        $_SESSION['user_tel_elv_at'] = $HTTP_POST_VARS['user_tel_elv_at'];
                        if (isset($HTTP_POST_VARS['user_email_elv_at'])) {
                            $error_msg = MODULE_PAYMENT_NOVALNET_ELV_AT_EMAIL_INPUT_REQUEST_DESC;
                        } else {
                            $error_msg = MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_INPUT_REQUEST_DESC;
                        }
                        // firstcall()
                        $this->before_process();
                        $payment_error_return = 'payment_error=' . $this->code;
                        $messageStack->add_session('checkout_payment', $error_msg . '<!-- [' . $this->code . '] -->', 'error');
                        zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
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
        global $HTTP_POST_VARS, $_POST, $order;

        if (count($HTTP_POST_VARS) == 0 || $HTTP_POST_VARS == '')
            $HTTP_POST_VARS = $_POST;

        $cardnoInfo_at = $_SESSION['bank_account_at'];
        $codeInfo_at = $_SESSION['bank_code_at'];

        if ($cardnoInfo_at) {
            $cardnoInfo_at = str_pad(substr($cardnoInfo_at, 0, -4), strlen($cardnoInfo_at), '*', STR_PAD_RIGHT);
        }
        if ($codeInfo_at) {
            $codeInfo_at = str_pad(substr($codeInfo_at, 0, -4), strlen($codeInfo_at), '*', STR_PAD_RIGHT);
        }

        $confirmation = array('fields' => array(array('title' => MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_BANK_ACCOUNT_OWNER,
                    'field' => $_SESSION['bank_account_holder_at']),
                array('title' => MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_BANK_ACCOUNT_NUMBER,
                    'field' => $cardnoInfo_at),
                array('title' => MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_BANK_CODE,
                    'field' => $codeInfo_at)
                ));

        return $confirmation;
    }

    ### Build the data and actions to process when the "Submit" button is pressed on the order-confirmation screen. ###
    ### These are hidden fields on the checkout confirmation page ###
    // @return string

    function process_button() {
        global $HTTP_POST_VARS, $_POST;

        if (count($HTTP_POST_VARS) == 0 || $HTTP_POST_VARS == '')
            $HTTP_POST_VARS = $_POST;
        $process_button_string = zen_draw_hidden_field('bank_account_holder_at', $_SESSION['bank_account_holder_at']) .
                zen_draw_hidden_field('bank_account_at', $_SESSION['bank_account_at']) .
                zen_draw_hidden_field('bank_code_at', $_SESSION['bank_code_at']);

        return $process_button_string;
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

    public function secondCall() {
        $xmlresponse_erros = '';
        // If customer forgets PIN, send a new PIN
        if ($_SESSION['new_novalnet_pin_elv_at'])
            $request_type = 'TRANSMIT_PIN_AGAIN';
        else
            $request_type = 'PIN_STATUS';
        if ($_SESSION['email_reply_check_elv_at'] == 'Email Reply')
            $request_type = 'REPLY_EMAIL_STATUS';

        if ($_SESSION['new_novalnet_pin_elv_at'])
            $_SESSION['new_novalnet_pin_elv_at'] = false;

        $xml = '';
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
				<nnxml>                               
		  			<info_request>
			    		<vendor_id>' . $this->vendor_id . '</vendor_id>
			    		<vendor_authcode>' . $this->auth_code . '</vendor_authcode>
			    		<request_type>' . $request_type . '</request_type>
			    		<tid>' . $_SESSION['nn_tid_elv_at'] . '</tid>';
        if ($request_type != 'REPLY_EMAIL_STATUS')
            $xml .= '<pin>' . $_SESSION['novalnet_pin_elv_at'] . '</pin>';$xml .= '
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
                    $_SESSION['invalid_count_at'] = $_SESSION['invalid_count_at'] + 1;
                    if ($_SESSION['invalid_count_at'] == 3) {
                        $payment_error_return = 'payment_error=' . $this->code . '&error=' . utf8_encode(MODULE_PAYMENT_NOVALNET_ELV_AT_MAX_TIME_ERROR);
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
                $array['tid'] = $_SESSION['nn_tid_elv_at'];
                $array['statusdesc'] = $array['status_message']; // Param-name is changed
                $array['test_mode'] = $_SESSION['test_mode_elv_at'];
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

    ### Store the BANK info to the order ###
    ### This sends the data to the payment gateway for processing and Evaluates the Bankdatas for acceptance and the validity of the Bank Details ###

    function before_process() {
        global $_POST, $order, $db, $currencies, $messageStack;
        $billing_iso_code = strtolower($order->customer['country']['iso_code_2']);
        if (count($HTTP_POST_VARS) == 0 || $HTTP_POST_VARS == '')
            $HTTP_POST_VARS = $_POST;
		$_SESSION['nn_amount_elv_at'] = $this->findTotalAmount();

        //Test mode based on the responsone test mode value
        $test_mode = (strtolower(MODULE_PAYMENT_NOVALNET_ELV_AT_TEST_MODE) == 'true' or MODULE_PAYMENT_NOVALNET_ELV_AT_TEST_MODE == '1') ? 1 : 0;

        // Setting callback type // see constructor
        // First call is done, so check PIN / second call...
        if ($_SESSION['nn_tid_elv_at'] && $this->isActivatedCallback) {
            if (MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_BY_CALLBACK_SMS == 'Email Reply')
                $_SESSION['email_reply_check_elv_at'] = 'Email Reply';
            else
                unset($_SESSION['email_reply_check_elv_at']);
            $_SESSION['new_novalnet_pin_elv_at'] = false;

            if ($aryResponse = $this->secondCall()) {
                if ($this->order_status)
                    $order->info['order_status'] = $this->order_status;
                if ($_SESSION['test_mode_elv_at'] == 1 || $test_mode)
                    $order->info['comments'] .= '<B><br>' . MODULE_PAYMENT_NOVALNET_ELV_AT_TEST_ORDER_MESSAGE . '</B>';
                $order->info['comments'] .= '<B><br>' . MODULE_PAYMENT_NOVALNET_ELV_AT_TID_MESSAGE . $_SESSION['nn_tid_elv_at'] . '</B><br />';
                $order->info['comments'] = str_replace(array('<b>', '</b>', '<B>', '</B>', '<br>', '<br />', '<BR>'), array('', '', '', '', "\n", "\n", "\n"), $order->info['comments']);
            }
            return;
        }



        #Get the required additional customer details from DB
        $nn_customer_id = (isset($_SESSION['customer_id'])) ? $_SESSION['customer_id'] : '';
        $customer_values = $db->Execute("SELECT customers_gender, customers_firstname, customers_lastname, customers_dob, customers_email_address, customers_telephone, customers_fax, customers_email_format FROM " . TABLE_CUSTOMERS . " WHERE customers_id='" . (int) $nn_customer_id . "'");
        while (!$customer_values->EOF) {
            $customer_values->MoveNext();
        }
        list($customer_values->fields['customers_dob'], $extra) = explode(' ', $customer_values->fields['customers_dob']);

        ### Process the payment to paygate ##
        $url = 'https://payport.novalnet.de/paygate.jsp';


        $amount = $_SESSION['nn_amount_elv_at'];
        $user_ip = $this->getRealIpAddr();

        $vendor_id = $this->vendor_id;
        $auth_code = $this->auth_code;
        $product_id = $this->product_id;
        $tariff_id = $this->tariff_id;

        $customer_id = $_SESSION['customer_id'];

        $manual_check_limit = $this->manual_check_limit;
        $manual_check_limit = str_replace(',', '', $manual_check_limit);
        $manual_check_limit = str_replace('.', '', $manual_check_limit);

        if ($manual_check_limit && $amount >= $manual_check_limit) {
            $product_id = $this->product_id2;
            $tariff_id = $this->tariff_id2;
        }


        //set the user telephone
        $tel_param = '&tel=';
        if ($_SESSION['user_tel_elv_at'])
            $user_telephone = $_SESSION['user_tel_elv_at'];
        else
            $user_telephone = $order->customer['telephone'];
        //set the user email
        if ($_SESSION['user_email_elv_at'])
            $user_email = $_SESSION['user_email_elv_at'];
        else
            $user_email = $order->customer['email_address'];

        //set the user telephone
        if ($_SESSION['user_tel_elv_at']) {
            $user_telephone = $_SESSION['user_tel_elv_at'];
        } else {
            $user_telephone = $order->customer['telephone'];
        }
        // set post params
        if ($this->isActivatedCallback && in_array($billing_iso_code, $this->nnelvat_allowed_pin_country_list) && $amount >= MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_BY_CALLBACK_MIN_LIMIT) {
            if (MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_BY_CALLBACK_SMS == 'Callback (Telefon & Handy)') {
                $this->callback_type = '&pin_by_callback=1';
                $user_telephone = '&tel=' . $user_telephone;
            }
            if (MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_BY_CALLBACK_SMS == 'SMS (nur Handy)') {
                $this->callback_type = '&pin_by_sms=1';
                $user_telephone = '&mobile=' . $user_telephone;
            }
            if (MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_BY_CALLBACK_SMS == 'Email Reply') {
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
        $customer_no = ($customer['customers_status'] != 1) ? $nn_customer_id : MODULE_PAYMENT_NOVALNET_ELV_AT_GUEST_USER;

        $user_ip = $this->getRealIpAddr();

        $urlparam = 'vendor=' . $vendor_id . '&product=' . $product_id . '&key=' . $this->payment_key . '&tariff=' . $tariff_id . '&auth_code=' . $auth_code . '&currency=' . $order->info['currency'];
        $urlparam .= '&bank_account_holder=' . $HTTP_POST_VARS['bank_account_holder_at'] . '&bank_account=' . $HTTP_POST_VARS['bank_account_at'];
        $urlparam .= '&bank_code=' . $HTTP_POST_VARS['bank_code_at'] . '&first_name=' . $firstname . '&last_name=' . $lastname;
        $urlparam .= '&street=' . $street_address . '&city=' . $city . '&zip=' . $postcode;
        $urlparam .= '&country=' . $country_iso_code_2 . '&email=' . $email_address;
        $urlparam .= '&search_in_street=1' . '&tel=' . $user_telephone . '&remote_ip=' . $user_ip;
        $urlparam .= '&gender=' . $customer['customers_gender'] . '&birth_date=' . $customer_values->fields['customers_dob'] . '&fax=' . $customer['customers_fax'];
        $urlparam .= '&language=' . MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_LANG;
        $urlparam .= '&lang=' . MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_LANG;
        $urlparam .= '&test_mode=' . $test_mode;
        $urlparam .= '&customer_no=' . $customer_no;
        $urlparam .= '&use_utf8=1';
        $urlparam .= '&amount=' . $amount;
        $urlparam .= $this->callback_type;
		
		list($errno, $errmsg, $data) = $this->perform_https_request($url, $urlparam);

// echo '<br> Result: '. print_r($data); exit;
        $aryResponse = array();
        #capture the result and message and other parameters from response data '$data' in an array
        $aryPaygateResponse = explode('&', $data);
        foreach ($aryPaygateResponse as $key => $value) {
            if ($value != "") {
                $aryKeyVal = explode("=", $value);
                $aryResponse[$aryKeyVal[0]] = $aryKeyVal[1];
            }
        }
//echo '<br>Result :'.print_r($aryResponse); exit;
        if ($aryResponse['status'] == 100) {
            ### Passing through the Transaction ID from Novalnet's paygate into order-info ###
            if ($this->isActivatedCallback && in_array($billing_iso_code, $this->nnelvat_allowed_pin_country_list) && $amount >= MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_BY_CALLBACK_MIN_LIMIT) {
                $_SESSION['elv_at_order_amount'] = $amount;
                $_SESSION['nn_tid_elv_at'] = $aryResponse['tid'];
                // To avoide payment method confussion add code in session
                //set session for maximum time limit to 30 minutes
                $_SESSION['max_time_elv_at'] = time() + (30 * 60);
                //TEST BILLING MESSAGE BASED ON THE RESPONSE TEST MODE
                $_SESSION['test_mode_elv_at'] = $aryResponse['test_mode'];
            } else {

                $test_mode = (strtolower(MODULE_PAYMENT_NOVALNET_ELV_AT_TEST_MODE) == 'true' or MODULE_PAYMENT_NOVALNET_ELV_AT_TEST_MODE == '1') ? 1 : 0;
                if ($aryResponse['test_mode'] == 1 || $test_mode)
                    $order->info['comments'] .= '<B><br>' . MODULE_PAYMENT_NOVALNET_ELV_AT_TEST_ORDER_MESSAGE . '</B>';

                if ($this->order_status)
                    $order->info['order_status'] = $this->order_status;

                $order->info['comments'] .= '<B><br>' . MODULE_PAYMENT_NOVALNET_ELV_AT_TID_MESSAGE . $aryResponse['tid'] . '</B><br />';
                $_SESSION['nn_tid_elv_at'] = $aryResponse['tid'];
                $order->info['comments'] = str_replace(array('<b>', '</b>', '<B>', '</B>', '<br>', '<br />', '<BR>'), array('', '', '', '', "\n", "\n", "\n"), $order->info['comments']);
            }
        }
        else {
            ### Passing through the Error Response from Novalnet's paygate into order-info ###
            $order->info['comments'] .= 'Novalnet Error Code : ' . $aryResponse['status'] . ', Novalnet Error Message : ' . $aryResponse['status_desc'];

            $payment_error_return = 'payment_error=' . $this->code;

            $messageStack->add_session('checkout_payment', $aryResponse['status_desc'] . '<!-- [' . $this->code . '] -->', 'error');
            zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
        }
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

    ### replace the Special German Charectors ###

    function ReplaceSpecialGermanChars($string) {
        $what = array("ä", "ö", "ü", "Ä", "Ö", "Ü", "ß");
        $how = array("ae", "oe", "ue", "Ae", "Oe", "Ue", "ss");

        $string = str_replace($what, $how, $string);

        return $string;
    }

    ### get the real Ip Adress of the User ###

    function getRealIpAddr() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {   //check ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {   //to check ip is pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        /*
          $num="(\\d|[1-9]\\d|1\\d\\d|2[0-4]\\d|25[0-5])";
          if (!preg_match("/^$num\\.$num\\.$num\\.$num$/", $ip)){
          $ip='127.0.0.1';

         */
        return $ip;

        //   }
    }

    ### Send additional information about bankdata via email to the store owner ###
    ### Send the order detail to Novalnet ###

    function after_process() {
        global $order, $customer_id, $insert_id, $db;

        $url = 'https://payport.novalnet.de/paygate.jsp';
        $amount = $_SESSION['nn_amount_elv_at'];
        $vendor_id = $this->vendor_id;
        $auth_code = $this->auth_code;
        $product_id = $this->product_id;
        $tariff_id = $this->tariff_id;


        $manual_check_limit = $this->manual_check_limit;
        $manual_check_limit = str_replace(',', '', $manual_check_limit);
        $manual_check_limit = str_replace('.', '', $manual_check_limit);

        if ($manual_check_limit && $amount >= $manual_check_limit) {
            $product_id = $this->product_id2;
            $tariff_id = $this->tariff_id2;
        }
        $urlparam = 'vendor=' . $vendor_id . '&product=' . $product_id . '&key=' . $this->payment_key . '&tariff=' . $tariff_id;
        $urlparam .= '&auth_code=' . $auth_code . '&status=100&tid=' . $_SESSION['nn_tid_elv_at'] . '&vwz3=' . $insert_id . '&vwz3_prefix=' . MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_ORDERNO . '&vwz4=' . date('Y.m.d') . '&vwz4_prefix=' . MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_ORDERDATE . '&order_no=' . $insert_id;

        list($errno, $errmsg, $data) = $this->perform_https_request($url, $urlparam);


        unset($_SESSION['nn_tid_elv_at']);
        unset($_SESSION['bank_account_at']);
        unset($_SESSION['bank_code_at']);
        unset($_SESSION['bank_account_holder_at']);
        unset($_SESSION['max_time_elv_at']);
        unset($_SESSION['test_mode_elv_at']);
        unset($_SESSION['user_tel_elv_at']);
        unset($_SESSION['nn_amount_elv_at']);
        unset($_SESSION['user_email_elv_at']);
        unset($_SESSION['email_reply_check_elv_at']);
        unset($_SESSION['new_novalnet_pin_elv_at']);
        unset($_SESSION['elv_at_order_amount']);
        if (isset($_SESSION['invalid_count_at'])) {
            unset($_SESSION['invalid_count_at']);
        }
        return false;
    }

    ### Store additional order information ###
    ### not in use ###
    // @param int $zf_order_id

    function after_order_create($zf_order_id) {
        return false;
    }

    ### Used to display error message details ###
    // @return array

    function get_error() {
        global $_GET;

        $error = array('title' => MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_ERROR,
            'error' => stripslashes(urldecode($_GET['error'])));

        return $error;
    }

    ### Check to see whether module is installed ###
    // @return boolean

    function check() {
        global $db;
        if (!isset($this->_check)) {
            $check_query = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_NOVALNET_ELV_AT_STATUS'");
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

        #Booking amount limit
        $install_text['booking_title'] = array('en' => "Manual checking amount in cents",
            'de' => "Manuelle &Uuml;berpr&uuml;fung des Betrags in Cent");
        $install_text['booking_desc'] = array('en' => "Please enter the amount in cents",
            'de' => "Bitte den Betrag in Cent eingeben");

        #Second Product id
        $install_text['secondproduct_title'] = array('en' => "Second Product ID in Novalnet",
            'de' => "Zweite Novalnet Produkt ID");
        $install_text['secondproduct_desc'] = array('en' => "for the manual checking",
            'de' => "zur manuellen &Uuml;berpr&uuml;fung");

        #Second Tariff id
        $install_text['secondtariff_title'] = array('en' => "Second Tariff ID in Novalnet",
            'de' => "Zweite Novalnet Tarif ID");
        $install_text['secondtariff_desc'] = array('en' => "for the manual checking",
            'de' => "zur manuellen &Uuml;berpr&uuml;fung");

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


        #Activate Logo Mode
        $install_text['logo_title'] = array('en' => "Activate logo mode:",
            'de' => "Aktivieren Sie Logo Modus:");
        $install_text['logo_desc'] = array('en' => "Do you want to activate logo mode?",
            'de' => "Wollen Sie Logo-Modus zu aktivieren?");

        #Pin by callback sms
        $install_text['pinbycallback_title'] = array('en' => "PIN by Callback/SMS/E-Mail",
            'de' => "PIN by Callback/SMS/E-Mail");
        $install_text['pinbycallback_desc'] = array('en' => "When activated by PIN Callback / SMS / E-Mail the customer to enter their phone / mobile number / E-Mail requested. By phone or SMS, the customer receives a PIN from Novalnet AG, which must enter before ordering. If the PIN is valid, the payment process has been completed successfully, otherwise the customer will be prompted again to enter the PIN. This service is only available for customers from specified countries.",
            'de' => "Wenn durch PIN Callback / SMS / E-Mail des Kunden aktiviert, um ihre Telefonnummer / Handynummer / E-Mail angefordert geben. Per Telefon oder SMS, erh&auml;lt der Kunde eine PIN von Novalnet AG, die vor der Bestellung eingeben m&uuml;ssen. Wenn die PIN g&uuml;ltig ist, hat die Zahlung Prozess erfolgreich beendet wurde, andernfalls hat der Kunde erneut aufgefordert, die PIN einzugeben. Dieser Service ist nur f&uuml;r Kunden aus bestimmten L&auml;ndern.");

        #Manual Amount Limit For Pin by callback/sms
        $install_text['amountlimitpin_title'] = array('en' => "Minimum Amount Limit for Callback in cents",
            'de' => "Grenzwert (Mindestbetrag) in Cent für Rückruf");
        $install_text['amountlimitpin_desc'] = array('en' => "Please enter minimum amount limit to enable Pin by CallBackmodul (In Cents, e.g. 100,200)",
            'de' => "Bitte geben Sie Mindestbetrag Grenze zu Pin durch CallBack Modul (in Cent, z. B. 100,200) erm&Ouml;glichen");

        #ACDC CONTROL FOR DE

        $install_text['acdccontrol_title'] = array('en' => "Enable ACDC Control",
            'de' => "ACDC-Check aktivieren");
        $install_text['acdccontrol_desc'] = array('en' => "Do you want to activate the ACDC Control?",
            'de' => "Wollen Sie ACDC Control aktivieren?");

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

        $booking_title = $this->install_lang('booking_title', DEFAULT_LANGUAGE);
        $booking_desc = $this->install_lang('booking_desc', DEFAULT_LANGUAGE);

        $secondproduct_title = $this->install_lang('secondproduct_title', DEFAULT_LANGUAGE);
        $secondproduct_desc = $this->install_lang('secondproduct_desc', DEFAULT_LANGUAGE);

        $secondtariff_title = $this->install_lang('secondtariff_title', DEFAULT_LANGUAGE);
        $secondtariff_desc = $this->install_lang('secondtariff_desc', DEFAULT_LANGUAGE);

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

        $logo_title = $this->install_lang('logo_title', DEFAULT_LANGUAGE);
        $logo_desc = $this->install_lang('logo_desc', DEFAULT_LANGUAGE);

        $pinbycallback_title = $this->install_lang('pinbycallback_title', DEFAULT_LANGUAGE);
        $pinbycallback_desc = $this->install_lang('pinbycallback_desc', DEFAULT_LANGUAGE);

        $amountlimitpin_title = $this->install_lang('amountlimitpin_title', DEFAULT_LANGUAGE);
        $amountlimitpin_desc = $this->install_lang('amountlimitpin_desc', DEFAULT_LANGUAGE);

        /* $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('".$allowed_title."','MODULE_PAYMENT_NOVALNET_ELV_AT_ALLOWED', '','".$allowed_desc."', '6', '0', now())");  */

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('" . $enable_title . "', 'MODULE_PAYMENT_NOVALNET_ELV_AT_STATUS', 'True', '" . $enable_desc . "', '6', '0', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('" . $pinbycallback_title . "','MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_BY_CALLBACK_SMS','False','" . $pinbycallback_desc . "', '6', '1', 'zen_cfg_select_option(array( \'False\', \'Callback (Telefon & Handy)\', \'SMS (nur Handy)\',\'Email Reply\'), ', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . $amountlimitpin_title . "','MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_BY_CALLBACK_MIN_LIMIT', '','" . $amountlimitpin_desc . "', '6', '2', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('" . $test_title . "', 'MODULE_PAYMENT_NOVALNET_ELV_AT_TEST_MODE', 'True', '" . $test_desc . "', '6', '3', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . $vendor_title . "', 'MODULE_PAYMENT_NOVALNET_ELV_AT_VENDOR_ID', '', '" . $vendor_desc . "', '6', '4', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . $auth_title . "', 'MODULE_PAYMENT_NOVALNET_ELV_AT_AUTH_CODE', '', '" . $auth_desc . "', '6', '5', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . $product_title . "', 'MODULE_PAYMENT_NOVALNET_ELV_AT_PRODUCT_ID', '', '" . $product_desc . "', '6', '6', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . $tariff_title . "', 'MODULE_PAYMENT_NOVALNET_ELV_AT_TARIFF_ID', '', '" . $tariff_desc . "', '6', '7', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . $booking_title . "', 'MODULE_PAYMENT_NOVALNET_ELV_AT_MANUAL_CHECK_LIMIT', '', '" . $booking_desc . "', '6', '8', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . $secondproduct_title . "', 'MODULE_PAYMENT_NOVALNET_ELV_AT_PRODUCT_ID2', '', '" . $secondproduct_desc . "', '6', '9', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . $secondtariff_title . "', 'MODULE_PAYMENT_NOVALNET_ELV_AT_TARIFF_ID2', '', '" . $secondtariff_desc . "', '6', '10', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . $enduser_title . "', 'MODULE_PAYMENT_NOVALNET_ELV_AT_INFO', '', '" . $enduser_desc . "', '6', '11', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . $sortorder_title . "', 'MODULE_PAYMENT_NOVALNET_ELV_AT_SORT_ORDER', '0', '" . $sortorder_desc . "', '6', '12', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('" . $setorderstatus_title . "', 'MODULE_PAYMENT_NOVALNET_ELV_AT_ORDER_STATUS_ID', '0', '" . $setorderstatus_desc . "', '6', '13', 'zen_cfg_pull_down_order_statuses(', 'zen_get_order_status_name', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('" . $paymnetzone_title . "', 'MODULE_PAYMENT_NOVALNET_ELV_AT_ZONE', '0', '" . $paymnetzone_desc . "', '6', '14', 'zen_get_zone_class_title', 'zen_cfg_pull_down_zone_classes(', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . $proxy_title . "', 'MODULE_PAYMENT_NOVALNET_ELV_AT_PROXY', '', '" . $proxy_desc . "', '6', '15', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('" . $logo_title . "', 'MODULE_PAYMENT_NOVALNET_ELV_AT_LOGO_STATUS', 'True', '" . $logo_desc . "', '6', '16', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
    }

    ### Remove the module and all its settings ###

    function remove() {
        global $db;
        $db->Execute("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    ### Internal list of configuration keys used for configuration of the module ###
    // @return array

    function keys() {
        return array( 'MODULE_PAYMENT_NOVALNET_ELV_AT_LOGO_STATUS','MODULE_PAYMENT_NOVALNET_ELV_AT_STATUS', 'MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_BY_CALLBACK_SMS', 'MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_BY_CALLBACK_MIN_LIMIT', 'MODULE_PAYMENT_NOVALNET_ELV_AT_TEST_MODE', 'MODULE_PAYMENT_NOVALNET_ELV_AT_VENDOR_ID', 'MODULE_PAYMENT_NOVALNET_ELV_AT_AUTH_CODE', 'MODULE_PAYMENT_NOVALNET_ELV_AT_PRODUCT_ID', 'MODULE_PAYMENT_NOVALNET_ELV_AT_TARIFF_ID', 'MODULE_PAYMENT_NOVALNET_ELV_AT_MANUAL_CHECK_LIMIT', 'MODULE_PAYMENT_NOVALNET_ELV_AT_PRODUCT_ID2', 'MODULE_PAYMENT_NOVALNET_ELV_AT_TARIFF_ID2', 'MODULE_PAYMENT_NOVALNET_ELV_AT_INFO', 'MODULE_PAYMENT_NOVALNET_ELV_AT_SORT_ORDER', 'MODULE_PAYMENT_NOVALNET_ELV_AT_ORDER_STATUS_ID', 'MODULE_PAYMENT_NOVALNET_ELV_AT_ZONE', 'MODULE_PAYMENT_NOVALNET_ELV_AT_PROXY');
    }

}

?>
