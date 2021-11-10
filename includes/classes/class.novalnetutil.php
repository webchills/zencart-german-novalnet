<?php
/**
* This script contains helper functions
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
* Script : class.novalnetutil.php
*/
class NovalnetUtil {

    /**
     * Validate the global configuration and display the error/warning message
     *
     * @return boolean
     */
    public static function checkMerchantConfiguration()
    {
        $merchantApiError = true;

        if (empty(MODULE_PAYMENT_NOVALNET_PUBLIC_KEY)) {
            $merchantApiError = false;
        } 

        return $merchantApiError;
    }

    /**
     * Validate E-mail address
     *
     * @param $emails string
     *
     * @return boolean
     */
    public static function validateEmail($emails)
    {
        $email = explode(',', $emails);
        foreach ($email as $value) {
            // Validate E-mail.
            if (!zen_validate_email($value)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Generate Novalnet gateway parameters based on payment selection
     * 
     * @param $data array
     * @param $payment string
     *
     * @return array
     */
    public static function getCommonRequestParams($data, $payment)
    {
        $request = array();
        self::getMerchantDetails($request, $payment);
        self::getCustomerDetails($request, $data);
        self::getOrderDetails($request, $data);
        self::getPaymentDetails($request, $data, $payment);
        self::getSystemDetails($request, $data);
        return $request;
    }

    /**
     * Get Merchant details
     * 
     * @param $request array
     * @param $payment string
     *
     * @return none
     */
    public static function getMerchantDetails(&$request, $payment) {
        $tariffDetails = explode('-', MODULE_PAYMENT_NOVALNET_TARIFF_ID);        
        $tariff         = $tariffDetails[1];
        $testMode = constant('MODULE_PAYMENT_'.strtoupper($payment).'_TEST_MODE');
        $request = array(
            'vendor'        => MODULE_PAYMENT_NOVALNET_VENDOR_ID,
            'product'       => MODULE_PAYMENT_NOVALNET_PRODUCT_ID,
            'tariff'        => $tariff,
            'auth_code'     => MODULE_PAYMENT_NOVALNET_AUTH_CODE,
            'test_mode'     => ($testMode == 'True') ? 1 : 0,
      );

    }

    /**
     * Get customer details
     *
     * @return array
     */
    public static function getCustomerfields()
    {
        global $db;

        $customerId = (isset($_SESSION['customer_id'])) ? $_SESSION['customer_id'] : '';
        if (!empty($customerId)) {
            $customer = $db->Execute("SELECT customers_gender, customers_dob, customers_fax FROM " . TABLE_CUSTOMERS . " WHERE customers_id='" . (int) $customerId . "'");

            if ($customer->RecordCount() > 0) {
                $customer = $customer->fields;
            }        
            return $customer;
        }
    }
    
    /**
     * Form Customer details
     * 
     * @param $request array
     * @param $data array
     *
     * @return none
     */
    public static function getCustomerDetails(&$request, $data)
    {
        $customer = self::getCustomerfields();

        $nn_customer_id = (isset($_SESSION['customer_id'])) ? $_SESSION['customer_id'] : '';
        $customer_birthdate = ($customer['customers_dob'] != '0001-01-01 00:00:00') ? date('Y-m-d', strtotime($customer['customers_dob'])) : '';
        $request['first_name'] = !empty($data['billing']['firstname']) ? $data['billing']['firstname'] : $data['customer']['firstname'];
        $request['last_name'] = !empty($data['billing']['lastname']) ? $data['billing']['lastname'] : $data['customer']['lastname'];
        $request['street'] = !empty($data['billing']['street_address']) ? $data['billing']['street_address'] : $data['customer']['street_address'];
        $request['search_in_street'] = 1;
        $request['city'] = !empty($data['billing']['city']) ? $data['billing']['city'] : $data['customer']['city'];
        $request['zip'] = !empty($data['billing']['postcode']) ? $data['billing']['postcode'] : $data['customer']['postcode'];
        $request['email'] = !empty($data['billing']['email_address']) ? $data['billing']['email_address'] : $data['customer']['email_address'];
        $request['country_code'] = !empty($data['billing']['country']['iso_code_2']) ? $data['billing']['country']['iso_code_2'] : $data['customer']['country']['iso_code_2'];
        $request['customer_no'] = !empty($nn_customer_id) ? $nn_customer_id : 'guest';
        $request['tel'] = !empty($data['billing']['telephone']) ? $data['billing']['telephone'] : $data['customer']['telephone'];            $request['lang'] = ((isset($_SESSION['language']) && $_SESSION['language'] == 'english') ? 'EN' : 'DE');
        $company = !empty($data['billing']['company']) ? $data['billing']['company'] : $data['customer']['company'];

        if (!empty($company))
            $request['company'] = !empty($data['billing']['company']) ? $data['billing']['company'] : $data['customer']['company'];
        
        if (!empty($customer['customers_gender'])) 
            $request['gender'] = $customer['customers_gender'];
        
        if (!empty($customer_birthdate))
            $request['birth_date'] = $customer_birthdate;

        if (!empty($customer['customers_fax']))
            $request['fax'] = $customer['customers_fax'];                   
       
    }

    /**
     * Get Order details
     * 
     * @param $request array
     * @param $data array
     *
     * @return none
     */
    public static function getOrderDetails(&$request, $data)
    {
        $request['amount'] = $data['order_amount'];
        $request['currency'] = $data['info']['currency'];
    }

    /**
     * Get Payment details
     * 
     * @param $request array
     * @param $data array
     * @param $payment string
     *
     * @return none
     */
    public static function getPaymentDetails(&$request, $data, $payment)
    {
        $redirectPayments = array('novalnet_cc','novalnet_ideal', 'novalnet_PayPal', 'novalnet_banktransfer', 'novalnet_eps', 'novalnet_giropay', 'novalnet_przelewy24', 'novalnet_postfinance', 'novalnet_postfinance_card');
        
        $request['payment_type'] = self::getPaymentTypeKey($payment,'type');
        $request['key']  = self::getPaymentTypeKey($payment,'key');
        
        if (in_array($payment, array('novalnet_invoice', 'novalnet_cc', 'novalnet_sepa', 'novalnet_PayPal', 'novalnet_instalment_sepa', 'novalnet_instalment_invoice', 'novalnet_guarantee_invoice', 'novalnet_guarantee_sepa'))) {
            $paymentName = strtoupper($payment);
            $onholdLimit = constant('MODULE_PAYMENT_'.$paymentName.'_ONHOLD_LIMIT');
            // To process on hold product
            if ((constant('MODULE_PAYMENT_'.$paymentName.'_ONHOLD') == 'Authorize') && (!empty($onholdLimit) && ($request['amount'] >= trim($onholdLimit)) || empty($onholdLimit))) {
                $request['on_hold'] = 1;
            }
        }
        
       if ($payment == 'novalnet_invoice') {
            $dueDate = MODULE_PAYMENT_NOVALNET_INVOICE_DUE_DATE;
            $dueDate = trim($dueDate);
            if ($dueDate != '') {
                $request['due_date'] = date('Y-m-d', strtotime('+' .$dueDate . ' days'));
            }
        }
        
        if (in_array($payment, array('novalnet_sepa', 'novalnet_guarantee_sepa'))) {
            $sepaDueDate = self::sepaDuedate($paymentName);
            
            if( !empty($sepaDueDate) ) {
                $request['sepa_due_date'] = date('Y-m-d', strtotime('+'.$sepaDueDate.' days'));
            }  
        }
        
        if (in_array($payment, array('novalnet_sepa', 'novalnet_guarantee_sepa', 'novalnet_instalment_sepa'))) {
            $request['bank_account_holder'] = strip_tags($data[$payment.'_bank_account_holder']);
            $request['iban'] = $data[$payment.'_bank_iban'];
        }
        
        if (in_array($payment, array('novalnet_guarantee_invoice', 'novalnet_guarantee_sepa', 'novalnet_instalment_invoice', 'novalnet_instalment_sepa'))) {
            if (isset($data[$payment.'_birthdate']) && !empty($data[$payment.'_birthdate'])) {
                $request['birth_date'] = date('Y-m-d', strtotime($data[$payment.'_birthdate']));
            }
            if (in_array($payment, array('novalnet_instalment_invoice', 'novalnet_instalment_sepa'))) {
                $period = constant('MODULE_PAYMENT_'.$paymentName.'_PERIOD');
                $request['instalment_cycles'] = $data[$payment.'_period'];
                $request['instalment_period'] = strtolower($period);
            }
        }
        
        if ($payment == 'novalnet_invoice') {
            $request['invoice_type'] = 'INVOICE';
        } elseif ($payment == 'novalnet_prepayment') {
            $request['invoice_type'] = 'PREPAYMENT';
        } elseif ($payment == 'novalnet_cashpayment') {
            $dueDate = trim(MODULE_PAYMENT_NOVALNET_CASHPAYMENT_SLIP_EXPIRY_DATE);
            $barzahlenDueDate = ($dueDate) ? (date('Y-m-d', strtotime('+' . $dueDate . ' days'))) : '';
            if ($barzahlenDueDate != '') {
                $request['cp_due_date'] = $barzahlenDueDate;
            }
        } elseif ($payment == 'novalnet_cc') {
            $request['unique_id'] = $data['nn_cc_uniqueid'];
            $request['pan_hash'] = $data['nn_cc_pan_hash'];
            $request['nn_it'] = 'iframe';
            $request['cc_3d'] = 1;
        } 
        if (in_array($payment, $redirectPayments)) {
            self::getRedirectParams($request, $payment);
        }
    }
    
    /**
     * Get SEPA due date
     * 
     * @param $paymentName string
     * 
     * @return string
     */
    public static function sepaDuedate($paymentName)
    {
       $sepaDueDate = constant('MODULE_PAYMENT_'.$paymentName. '_PAYMENT_DUE_DATE');        
       $sepaDueDate = trim($sepaDueDate);
        
       if ($sepaDueDate != '' && $sepaDueDate <= 14 && $sepaDueDate >= 2) {
         return $sepaDueDate;
       }
    }

    /**
     * Get payment key & payment type
     * 
     * @param $paymentName string
     * @param $field string
     *
     * @return string
     */
    public static function getPaymentTypeKey($paymentName, $field)
    {
        $payment = array(
                    'novalnet_sepa' => array('key' =>37, 'type' => "DIRECT_DEBIT_SEPA"),
                    'novalnet_cc' => array('key' => 6, 'type' => "CREDITCARD"),
                    'novalnet_invoice' => array('key' => 27, 'type' => "INVOICE"),
                    'novalnet_prepayment' => array('key' => 27, 'type' => "PREPAYMENT"),
                    'novalnet_guarantee_invoice' => array('key' => 41, 'type' => "GUARANTEED_INVOICE"),
                    'novalnet_guarantee_sepa' => array('key' => 40, 'type' => "GUARANTEED_DIRECT_DEBIT_SEPA"),
                    'novalnet_ideal' => array('key' => 49, 'type' => "IDEAL"),
                    'novalnet_banktransfer' => array('key' => 33, 'type' => "ONLINE_TRANSFER"),
                    'novalnet_giropay' => array('key' => 69, 'type' => "GIROPAY"),
                    'novalnet_cashpayment' => array('key' => 59, 'type' => "CASHPAYMENT"),
                    'novalnet_przelewy24' => array('key' => 78, 'type' => "PRZELEWY24"),
                    'novalnet_eps' => array('key' => 50, 'type' => "EPS"),
                    'novalnet_instalment_invoice' => array('key' => 96, 'type' => "INSTALMENT_INVOICE"),
                    'novalnet_instalment_sepa' => array('key' => 97, 'type' => "INSTALMENT_DIRECT_DEBIT_SEPA"),
                    'novalnet_PayPal' => array('key' => 34, 'type' => "PAYPAL"),
                    'novalnet_postfinance_card' => array('key' => 87, 'type' => "POSTFINANCE_CARD"),
                    'novalnet_postfinance' => array('key' => 88, 'type' => "POSTFINANCE"),
                );
                return $payment[$paymentName][$field];        
    }

    /**
     * Get system details
     * 
     * @param $request array
     * @param $data array
     *
     * @return none
     */
    public static function getSystemDetails(&$request, $data)
    {
        $remoteIp  = zen_get_ip_address();
        $systemIp  = $_SERVER['SERVER_ADDR'];
        $request['system_name'] = 'Zencart';
        $request['system_version'] = PROJECT_VERSION_MAJOR.'.'.PROJECT_VERSION_MINOR . '-NN-2.0.0';
        $request['remote_ip']      = (filter_var($remoteIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) || empty($remoteIp)) ? '127.0.0.1' : $remoteIp;
        $request['system_ip']      = (filter_var($systemIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) || empty($systemIp)) ? '127.0.0.1' : $systemIp;
        $request['system_url'] = ((ENABLE_SSL == true) ? HTTPS_SERVER : HTTP_SERVER);
        $notifyUrl = trim(MODULE_PAYMENT_NOVALNET_CALLBACK_NOTIFY_URL);
       if (!empty($notifyUrl))
            $request['notify_url'] = $notifyUrl;
    }

    /**
     * Return payment amount of given order
     * 
     * @param $data array
     * @param $payment string
     *
     * @return integer
     */
    public static function getPaymentAmount($data, $payment)
    {
        global $currencies, $messageStack;

        $total = ((isset($_SESSION['customers_status']) && $_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1)) ? ($data['info']['total'] + $data['info']['tax']) : $data['info']['total'];

        $totalAmount = number_format($total * $currencies->get_value($data['info']['currency']), 2);
        $amount = str_replace(',', '', $totalAmount);
        $amount = intval(round($amount * 100));

        if (preg_match('/[^\d\.]/', $amount)) {
            $messageStack->add_session('checkout_payment', MODULE_PAYMENT_NOVALNET_AMOUNT_ERROR_MESSAGE . '<!-- ['.$payment.'] -->', 'error');
            zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', true, false));
        }

        return $amount;
    }

    /**
     * Get Redirect request
     * 
     * @param $request array
     * @param $payment string
     *
     * @return none
     */
    public static function getRedirectParams(&$request, $payment)
    {
        $request['return_method'] = $request['error_return_method'] = 'POST';
        $request['return_url'] = $request['error_return_url'] = zen_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL');
        $request['user_variable_0']  = ((ENABLE_SSL == true) ? HTTPS_SERVER:HTTP_SERVER);
        $request['implementation']   = 'ENC';
        $request['input3']   = 'cart_id';
        $request['inputval3'] = $_SESSION['cartID'];
        $request['uniqid']    = self::getUniqueid();
    }

    /**
     * Generate encode data
     * 
     * @param $request array
     *
     * @return none
     */
    public static function generateEncodeValue(&$request)
    {
        foreach (array('auth_code', 'product', 'tariff', 'amount', 'test_mode') as $key) {
            if (isset($request[$key])) {
                // Encoding process
                $request[$key] = htmlentities(base64_encode(openssl_encrypt($request[$key], "aes-256-cbc", MODULE_PAYMENT_NOVALNET_PAYMENT_ACCESS_KEY, true, $request['uniqid'])));
            }
        }

         // Generate hash value
         $request['hash'] = self::generateHashValue($request);
    }

    /**
     * Generate decode data
     * @param $data
     *
     * @return none
     */
    public static function decodePaygateResponse(&$data)
    {
        foreach (array('auth_code','product','tariff','amount','test_mode') as $key) {
            if (isset($data[$key])) {
                // Decoding process
                $data[$key] = openssl_decrypt(base64_decode($data[$key]), "aes-256-cbc", MODULE_PAYMENT_NOVALNET_PAYMENT_ACCESS_KEY, true, $data['uniqid']);
            }
        }

        return $data;
    }

     /**
     * Perform HASH Validation with paygate response
     * 
     * @param $data array
     *
     * @return boolean
     */
    public static function validateHashResponse($data)
    {
        // Check for hash error
        return ($data['hash2'] != self::generateHashValue($data));
    }

     /**
     * Get hash value
     * 
     * @param $request array
     *
     * @return mixed
     */
    public static function generateHashValue($request)
    {
        // Hash generation using sha256 and encoded merchant details
        return hash('sha256', ($request['auth_code'].$request['product'].$request['tariff'].$request['amount'].$request['test_mode'].$request['uniqid'].strrev(MODULE_PAYMENT_NOVALNET_PAYMENT_ACCESS_KEY)));

    }

    /**
     * Gets the Unique Id
     *
     * @return string
     */
    public static function getUniqueid()
    {
        $randomArray = array('8','7','6','5','4','3','2','1','9','0','9','7','6','1','2','3','4','5','6','7','8','9','0');
        shuffle($randomArray);
        return substr(implode($randomArray, ''), 0, 16);
    }

    /**
     * Function to communicate transaction parameters with Novalnet Paygate
     * 
     * @param $paygateUrl string
     * @param $data array
     * @param $payment string
     *
     * @return array
     */
    public static function doPaymentCurlCall($paygateUrl, $data, $payment = '')
    {       
        // Initiate cURL.
        $curlProcess = curl_init($paygateUrl);
        // Set cURL options.
        curl_setopt($curlProcess, CURLOPT_POST, 1);
        curl_setopt($curlProcess, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curlProcess, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($curlProcess, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curlProcess, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curlProcess, CURLOPT_RETURNTRANSFER, 1);

        // Custom CURL time-out.
        curl_setopt($curlProcess, CURLOPT_TIMEOUT,  240);

        // Execute cURL.
        $response = curl_exec($curlProcess);

        // Handle cURL error.
        if (curl_errno($curlProcess)) {
            $messageStack->add_session('checkout_payment', utf8_decode('error_message=' .curl_error($curlProcess)) . '<!-- ['.$payment.'] -->', 'error');
            zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', true, false));
        }

        // Close cURL.
        curl_close($curlProcess);
        
        return $response;
    }

    /**
     * Check order status
     * 
     * @param $orderStatus integer
     *
     * @return integer
     */
    public static function checkDefaultOrderStatus($orderStatus)
    {
        return !empty($orderStatus) ? $orderStatus : DEFAULT_ORDERS_STATUS_ID;
    }

    /**
     * Form transaction comments
     * 
     * @param $tid integer
     * @param $testMode integer
     *
     * @return string
     */
    public static function formPaymentComments($tid, $testMode)
    {
        $transactionComments = '';
        if ($tid) {
            $transactionComments .= PHP_EOL.MODULE_PAYMENT_NOVALNET_TRANSACTION_DETAILS.PHP_EOL.MODULE_PAYMENT_NOVALNET_TRANSACTION_ID . $tid;
        }

        // Add test_mode text
        if ($testMode) {
            $transactionComments .= PHP_EOL.MODULE_PAYMENT_NOVALNET_TEST_ORDER_MESSAGE.PHP_EOL;
        }

        return $transactionComments;
    }

    /**
     * Return Invoice / Prepayment comments
     * 
     * @param $data array
     * @param $amount integer
     * 
     * @return string
     */
    public static function formInvoicePrepaymentComments($data, $amount = '')
    {
        $amount = !empty($amount) ? $amount : number_format($data['amount'], 2, ',', '');
        
        $transComments = PHP_EOL. MODULE_PAYMENT_NOVALNET_INVOICE_COMMENTS_PARAGRAPH.PHP_EOL;        
        $dueDate = $data['due_date'];
        $transComments .= ($dueDate != '') ? MODULE_PAYMENT_NOVALNET_DUE_DATE.': '.date(DATE_FORMAT, strtotime($dueDate)).PHP_EOL : '';        
        $transComments .= MODULE_PAYMENT_NOVALNET_ACCOUNT_HOLDER.': '.$data['invoice_account_holder']. PHP_EOL;
        $transComments .= MODULE_PAYMENT_NOVALNET_IBAN.': '.$data['invoice_iban']. PHP_EOL;
        $transComments .= MODULE_PAYMENT_NOVALNET_SWIFT_BIC.': '.$data['invoice_bic'].PHP_EOL;
        $transComments .= MODULE_PAYMENT_NOVALNET_BANK.': '.$data['invoice_bankname'].' '.$data['invoice_bankplace'].PHP_EOL;
        $transComments .= MODULE_PAYMENT_NOVALNET_AMOUNT.': '. $amount. ' ' . $data['currency'].PHP_EOL;
        return $transComments;
    }

    /**
     * Check transaction status message
     * 
     * @param $response array
     *
     * @return string
     */
    public static function getTransactionMessage($response)
    {
        return (!empty($response['status_message']) ? $response['status_message'] : !empty($response['status_desc']) ? $response['status_desc'] : (!empty($response['status_text']) ? $response['status_text'] : ''));
    }

    /**
     * Return Invoice / Prepayment payment reference comments
     * 
     * @param $orderId integer
     * @param $data array
     * @param $payment string
     *
     * @return string
     */
    public static function novalnetReferenceComments($orderId, $data, $payment)
    {
        $comments = MODULE_PAYMENT_NOVALNET_PAYMENT_MULTI_TEXT . PHP_EOL;
        $comments .=  MODULE_PAYMENT_NOVALNET_INVPRE_REF1. ': TID'.' '. $data['tid'] . PHP_EOL;
        if ($payment != 'novalnet_instalment_invoice') {
            $comments .=  MODULE_PAYMENT_NOVALNET_INVPRE_REF2 .':  BNR-' . (!empty($data['product']) ? $data['product'] : MODULE_PAYMENT_NOVALNET_PRODUCT_ID) . '-' .  $orderId. PHP_EOL;
        }
        

        return $comments;
    }    

    /**
     * Build the postback call for updating order_no
     * 
     * @param $data array
     *
     * @return none
     */
    public static function postBackCall($data)
    {
        $payment = $data['payment'];
        // Second call for updating order_no
        $urlparam = array(
            'vendor'    => $_SESSION['novalnet'][$payment]['vendor'],
            'product'   => $_SESSION['novalnet'][$payment]['product'],
            'tariff'    => $_SESSION['novalnet'][$payment]['tariff'],
            'auth_code' => $_SESSION['novalnet'][$payment]['auth_code'],
            'key'       => $_SESSION['novalnet'][$payment]['payment_id'],
            'status'    => 100,
            'tid'       => $_SESSION['novalnet'][$payment]['tid'],
            'order_no'  => $data['order_no'],
        );

        // Add invoice_ref parameter for Invoice and Prepayment
        if (in_array($_SESSION['novalnet'][$payment], array('novalnet_invoice', 'novalnet_guarantee_invoice'))) {
            $urlparam['invoice_ref'] .= 'BNR-'.$_SESSION['novalnet'][$payment]['product'].'-'.$data['order_no'];
        }

        // Send parameters to Novalnet paygate
        self::doPaymentCurlCall('https://payport.novalnet.de/paygate.jsp', $urlparam, $payment);

        // Unset all Novalnet session value
        if (isset($_SESSION['novalnet'])) {
            unset($_SESSION['novalnet']);
        }
    }

    /**
     * Merchant details
     * 
     * @param $inputParams array
     * 
     * @return array
     */
    public static function paymentInitialParams($inputParams)
    {
        return array(
            'vendor'           => $inputParams['vendor'],
            'product'          => $inputParams['product'],
            'tariff'           => $inputParams['tariff'],
            'auth_code'        => $inputParams['auth_code'],
            'payment_id'       => !empty($inputParams['key']) ? $inputParams['key'] : $inputParams['payment_id'],
            );
    }

    /**
     * Built Cashpayment comments
     * 
     * @param $response array
     *
     * @return string
     */
    public static function formCashpaymentComments($response)
    {
        global $db;
        $barzahlenComments = '';

        $slipDueDate = !empty($response['cp_due_date']) ? $response['cp_due_date']: $response['due_date'];

        $barzahlenComments .= MODULE_PAYMENT_NOVALNET_CASHPAYMENT_SLIP_EXPIRY_DATE_TEXT . ': '.date('d.m.Y', strtotime($slipDueDate)).PHP_EOL;

        $nearestStore =  self::getNearestStore($response);
        $nearestStore['nearest_store'] = $nearestStore;
        if (!empty($nearestStore)) {
            $barzahlenComments .= PHP_EOL . MODULE_PAYMENT_NOVALNET_CASHPAYMENT_NEAREST_STORE_DETAILS_TEXT.PHP_EOL;
        }

        $nearestStore['cp_due_date'] = $slipDueDate;
        $i =0;
        foreach ($nearestStore as $key => $values) {
            $i++;
            if (!empty($nearestStore['nearest_store_title_'.$i])) {
                $barzahlenComments .= PHP_EOL . $nearestStore['nearest_store_title_'.$i];
            }
            if (!empty($nearestStore['nearest_store_street_'.$i])) {
                $barzahlenComments .= PHP_EOL . $nearestStore['nearest_store_street_'.$i];
            }
            if (!empty($nearestStore['nearest_store_city_'.$i])) {
                $barzahlenComments .= PHP_EOL . $nearestStore['nearest_store_city_'.$i];
            }
            if (!empty($nearestStore['nearest_store_zipcode_'.$i])) {
                $barzahlenComments .= PHP_EOL . $nearestStore['nearest_store_zipcode_'.$i];
            }

            if (!empty($nearestStore['nearest_store_country_'.$i])) {
                $result = $db->Execute("select countries_name from countries where countries_iso_code_2='". $nearestStore['nearest_store_country_'.$i] ."'");            
                $barzahlenComments .= PHP_EOL . $result->fields['countries_name'].PHP_EOL;
            }
        }
        
        return $barzahlenComments;
    }
    
    /**
     * Get nearest store details
     * @param $response
     *
     * @return array
     */
    public static function getNearestStore($response)
    {
        $stores = array();
        foreach ($response as $sKey => $values) {
            if (stripos($sKey, 'nearest_store')!==false) {
                $stores[$sKey] = $values;
            }
        }
        return $stores;
    }
    
    /**
     * Get Instalment Cycles from Instalment payment settings.
     *
     * @param $payment string
     * @param $order object
     *
     * @return array
     */
    public static function getInstalmentCycles($payment, $order)
    {
        $paymentName   = strtoupper($payment);

        $paymentCycle = constant('MODULE_PAYMENT_' . $paymentName . '_PERIOD');
        $totalPeriod  = constant('MODULE_PAYMENT_' . $paymentName . '_CYCLE');
        
        if (!empty($totalPeriod)) {
            $totalPeriod  = explode(',',$totalPeriod);
        
        $i             = 0;
        $cycles        = array(array('id'=> $i,'text'=>'Select'));
        if ( 1 == $paymentCycle ) {
            $paymentCycle = '';
        }
        sort($totalPeriod);

        $clength=count($totalPeriod);
        for($x=0;$x<$clength;$x++){
          $totalPeriod[$x];
         }

        $totalPeriod = array_unique($totalPeriod);

        foreach ( $totalPeriod as $period ) {
            $amount = self::getPaymentAmount((array)$order, $payment);          
            $cycle = ($amount / $period);          
            if ( $cycle >= 999 ) {
                $cycles[] = array('id' => $period,'text' => sprintf(MODULE_PAYMENT_NOVALNET_CYCLES, $period ) . ' / ' .  sprintf( '%0.2f', $cycle/100 ).' ' . $order->info['currency'] . sprintf(MODULE_PAYMENT_NOVALNET_PER_MONTH, $paymentCycle ));
                $i++;
            }
         }
       }

        if ( $i == 0 ) {
            return $i;
        } else {
            return $cycles;
        }
    }
    
    /**
     * Get Instalment PlanF Details.
     *
     * @param $payment string
     *
     * @return array
     */
    public static function getInstalmentPlanDetails($payment)
    {
        $paymentName = strtoupper($payment);
        $cyclePeriod = constant('MODULE_PAYMENT_' . $paymentName . '_PERIOD');
        $totalPeriod = constant('MODULE_PAYMENT_' . $paymentName . '_CYCLE');
        $totalPeriod = explode(',',$totalPeriod);
        sort($totalPeriod);

        $clength=count($totalPeriod);
        for($x=0;$x<$clength;$x++){
          $totalPeriod[$x];
         }

        $totalInstalmentCycle = !empty( $totalPeriod ) ? $totalPeriod[count($totalPeriod)-1]:'';
        $currentMonthInvoice = date('m');

        for ( $i=0; $i<$totalInstalmentCycle; $i++ ) {
          $lastDay = date('Y-m-d', strtotime( '+'.$cyclePeriod * $i.'months' ) );
          $instlmentDateMonth[] = date('m', strtotime( '+'.$cyclePeriod * $i.'months' ) );
          if( $currentMonthInvoice > 12 ) {
            $currentMonthInvoice = $currentMonthInvoice - 12;
          }
          if ( $currentMonthInvoice == $instlmentDateMonth[$i] ) {
              $instlmentDateInvoice[] = date('Y-m-d', strtotime( '+'.$cyclePeriod * $i.'months' ) );
          } else {
              $instlmentDateInvoice[] = date('Y-m-d', strtotime( $instlmentDateInvoice[$i].' last day of previous month' , strtotime ( $lastDay ) ) );
           }
             $currentMonthInvoice = $currentMonthInvoice + $cyclePeriod;
        }

        return $currentMonthInvoice;
    }

    /**
     * Form guarantee field
     * 
     * @param $name string
     * @param $customerDetails array
     *
     * @return string
     */
    public static function getGuaranteeField($name, $customerDetails)
    {
       $birthDate = (isset($customerDetails['customers_dob']) && $customerDetails['customers_dob'] != '0001-01-01 00:00:00') ? date('Y-m-d', strtotime($customerDetails['customers_dob'])) : '';         
         
        return zen_draw_input_field($name, $birthDate, 'id="'.$name.'" placeholder="'.MODULE_PAYMENT_NOVALNET_GUARANTEE_DOB_FORMAT.'" autocomplete="OFF" maxlength="10" ') . '<br/><span id="'.$name.'-alert" style="color:red;"></span><script>document.getElementById("'.$name.'").onblur=function(){
            var pattern =/^([0-9]{4})\-(0[1-9]|1[012])\-(0[1-9]|[12][0-9]|3[01])$/;
            var date_value = this.value.trim();
            if(date_value != "" && !pattern.test(date_value)) {
                document.getElementById("'.$name.'").value="";
                document.getElementById("'.$name.'-alert").innerHTML = "'.MODULE_PAYMENT_NOVALNET_GUARANTEE_DOB_FORMAT_ERROR.'";
                return false;
            }
            document.getElementById("'.$name.'").value = date_value;
            document.getElementById("'.$name.'-alert").innerHTML = "";
        };
        </script>';
    }

    /**
     * Check guarantee payment condition
     * 
     * @param $order array
     * @param $payment string
     *
     * @return string
     */
    public static function checkGuaranteeConditions($order, $payment)
    {
        // Get payment name in caps
        $paymentName = strtoupper($payment);
        
        $min_amount = (in_array($payment, array('novalnet_instalment_invoice', 'novalnet_instalment_sepa'))) ? '1998' : '999';
        
        // Get guarantee minimum and maximum amount value
        $minimumAmount = trim(constant('MODULE_PAYMENT_'.$paymentName.'_MIN_AMOUNT_LIMIT')) ? trim(constant('MODULE_PAYMENT_'.$paymentName.'_MIN_AMOUNT_LIMIT')) : $min_amount;

        // Get order details
        $customerIsoCode = strtoupper($order['customer']['country']['iso_code_2']);
        $amount = self::getPaymentAmount((array)$order, $payment);

        // Delivery address
        $deliveryAddress = array(
            'street_address' => $order['delivery']['street_address'],
            'city'           => $order['delivery']['city'],
            'postcode'       => $order['delivery']['postcode'],
            'country'        => $order['delivery']['country']['iso_code_2'],
        );

        // Billing address
        $billingAddress = array(
            'street_address' => $order['billing']['street_address'],
            'city'           => $order['billing']['city'],
            'postcode'       => $order['billing']['postcode'],
            'country'        => $order['billing']['country']['iso_code_2'],
        );
   
        if ((((int) $amount >= (int) $minimumAmount) && in_array($customerIsoCode, array('DE', 'AT', 'CH')) && $order['info']['currency'] == 'EUR' && $deliveryAddress === $billingAddress)) {
            return array('guarantee', '');
        } else {
            $guaranteeError = '';
            if (!in_array($customerIsoCode, array('DE', 'AT', 'CH'))) {
                $guaranteeError .= MODULE_PAYMENT_NOVALNET_FORCE_GUARANTEE_ERROR_MESSAGE_COUNTRY;
            }
            if ($order['info']['currency'] !== 'EUR' ) {
                $guaranteeError .= '<br/>'.MODULE_PAYMENT_NOVALNET_FORCE_GUARANTEE_ERROR_MESSAGE_CURRENCY;
            }
            if ( ! empty( array_diff( $billingAddress, $deliveryAddress ) ) ) {
                $guaranteeError .= '<br/>'.MODULE_PAYMENT_NOVALNET_FORCE_GUARANTEE_ERROR_MESSAGE_ADDRESS;
            }
            if ( (int) $amount < (int) $minimumAmount ) {
                $guaranteeError .= '<br/>'.sprintf(MODULE_PAYMENT_NOVALNET_FORCE_GUARANTEE_ERROR_MESSAGE_AMOUNT, str_replace('.', ',', $minimumAmount/100) .' '. $order['info']['currency']);
            }
            $errorMessage = PHP_EOL.$guaranteeError;

            return array('error', $errorMessage);
        }
    }

    /**
     * Validate for users over 18 only
     * 
     * @param $birthDate integer
     *
     * @return boolean
     */
    public static function validateAge($birthDate)
    {
        return (empty($birthDate) || time() < strtotime('+18 years', strtotime($birthDate)));
    }

    /**
     * Prepare Instalment payment transaction detail comments
     * 
     * @param $response array
     * @param $payment string
     *
     * @return string
     */
    public static function instalmentComments($response, $payment)
    {
      $transactionComments = '';
      if (!empty($response['next_instalment_date'])) {
        if (!in_array( $response ['tid_status'], array( '91', '99' ), true ) ) {
            if ($payment == 'novalnet_instalment_sepa') {
                $comments .= PHP_EOL.PHP_EOL . sprintf(MODULE_PAYMENT_NOVALNET_INSTALMENT_SEPA_INFO, $response['amount'], $response['currency']);
            }
            $transactionComments .= MODULE_PAYMENT_NOVALNET_INSTALMENT_INFO;
            $transactionComments .= PHP_EOL . MODULE_PAYMENT_NOVALNET_INSTALMENT_PROCESSED . ( ! empty( $response ['instalment_cycles_executed'] ) ? $response ['instalment_cycles_executed'] : ( ! empty ( $response ['instalment1']['instalment_cycles_executed'] ) ? $response ['instalment1']['instalment_cycles_executed'] : '' ) );
            $transactionComments .= PHP_EOL . MODULE_PAYMENT_NOVALNET_INSTALMENT_DUE . ( isset( $response ['due_instalment_cycles'] ) ? $response ['due_instalment_cycles'] : ( ! empty ( $response ['instalment1']['due_instalment_cycles'] ) ? $response ['instalment1']['due_instalment_cycles'] : '' ) );
       
            $transactionComments .= PHP_EOL . MODULE_PAYMENT_NOVALNET_INSTALMENT_NXT_AMOUNT . ( ! empty( $response ['instalment_cycle_amount'] ) ? $response ['instalment_cycle_amount'] :  $response ['amount'] );

            if ( $response ['payment_id'] == '97' && $response['instalment_billing'] == '1' ) {
                $transactionComments .= PHP_EOL . PHP_EOL . sprintf( MODULE_PAYMENT_NOVALNET_INSTALMENT_DEBIT_TEXT, $response ['amount']);
            }
        }
        return $transactionComments;
      }
      return $transactionComments;
    }

    /**
     * Update the transaction details in novalnet table.
     * 
     * @param $data array
     *
     * @return none
     */
    public static function logInitialTransaction($data)
    {
        $payment = $data['payment'];
        $sessionValue = $_SESSION['novalnet'][$payment];

        $tableValues = array(
            'tid'                   => $sessionValue['tid'],
            'order_no'              => $data['order_no'],
            'payment_id'            => $sessionValue['payment_id'],
            'payment_type'          => $data['payment'],
            'amount'                => $sessionValue['amount'],
            'callback_amount'       => $sessionValue['total_amount'],
            'gateway_status'        => $sessionValue['gateway_status'],                        
            'date'                  => date('Y-m-d H:i:s'),
            'language'              => $_SESSION['language'],
            'instalment_details' => (in_array($payment, array('novalnet_instalment_sepa', 'novalnet_instalment_invoice'))) ? $sessionValue['instalment_details'] : ''
        );

        zen_db_perform('novalnet_transaction_detail', $tableValues, "insert");
    }
}
?>
