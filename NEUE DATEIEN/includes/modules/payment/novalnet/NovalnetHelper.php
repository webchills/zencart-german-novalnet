<?php
/**
 * Novalnet payment module
 * This script is used for helper functions to handle payment process
 *
 * @author     Novalnet AG
 * @copyright  Copyright (c) Novalnet
 * @license    https://www.novalnet.de/payment-plugins/kostenlos/lizenz
 * @link       https://www.novalnet.de
 *
 * Script : NovalnetHelper.php
 */

class NovalnetHelper
{

    /**
     * Check the merchant credentials are empty
     *
     * @return boolean
     */
    public static function isMerchantCredentialsValid()
    {
        if (defined('MODULE_PAYMENT_NOVALNET_PUBLIC_KEY') &&
            defined('MODULE_PAYMENT_NOVALNET_ACCESS_KEY') &&
            !empty(MODULE_PAYMENT_NOVALNET_PUBLIC_KEY) &&
            !empty(MODULE_PAYMENT_NOVALNET_ACCESS_KEY)
        ) {
            return true;
        }
        return false;
    }

    /**
     * Get system version
     *
     * @return string
     */
    public static function getSystemVersion()
    {
        return PROJECT_VERSION_MAJOR . '.' . PROJECT_VERSION_MINOR . '-NN' . NOVALNET_MODULE_VERSION;
    }

    /**
     * Build request parameters for curl call processing
     *
     * @param $params
     */
    public static function buildRequestParams(&$params)
    {
        NovalnetHelper::getMerchantData($params);
        NovalnetHelper::getCustomerData($params);
        NovalnetHelper::getTransactionData($params);
        NovalnetHelper::getCustomData($params);
    }

    /**
     * Get hosted payment page data
     *
     * @return $data
     */
    public static function getHostedPageData(&$params)
    {
        $params['hosted_page'] = [
            'type' => 'PAYMENTFORM'
        ];
    }

    /**
     * Get transaction data
     *
     * @return $data
     */
    public static function getTransactionData(&$params)
    {
        global $order;
        $params['transaction'] = [
            'amount' 			=> self::getOrderAmount($order->info['total']),
            'currency' 			=> $order->info['currency'],
            'system_name' 		=> 'Zen_Cart',
            'system_version' 	=> NovalnetHelper::getSystemVersion(),
            'system_url' 		=> (defined('ENABLE_SSL') ? (ENABLE_SSL == true ? HTTPS_SERVER : HTTP_SERVER . DIR_WS_CATALOG) : (HTTPS_CATALOG_SERVER . DIR_WS_HTTPS_CATALOG)),
            'system_ip' 		=> $_SERVER['SERVER_ADDR']
        ];

        if (isset($_SESSION['nn_booking_details']) && ($_SESSION['nn_payment_details'])) {
            $booking_details = $_SESSION['nn_booking_details'];
            $payment_details = $_SESSION['nn_payment_details'];

            $params['transaction']['payment_type'] = !empty($payment_details->type) ? $payment_details->type : '';
            $payment_action = !empty($booking_details->payment_action) ? $booking_details->payment_action : '';

            if (isset($booking_details->test_mode)) {
                $params['transaction']['test_mode'] = $booking_details->test_mode;
            }

            $payment_data_keys = ['token', 'pan_hash', 'unique_id', 'iban', 'wallet_token', 'bic'];

            foreach ($payment_data_keys as $key) {
                if (!empty($booking_details->{$key})) {
                    $params['transaction']['payment_data'][$key] = $booking_details->{$key};
                }
            }
            
            if (!empty($booking_details->create_token)) {
                $params['transaction']['create_token'] = $booking_details->create_token;
                unset($_SESSION['nn_booking_details']->create_token);
            }
                                 
            if (!empty($booking_details->enforce_3d)) {
                $params['transaction']['enforce_3d'] = $booking_details->enforce_3d;
            }

            if (!empty($booking_details->due_date)) {
                $params['transaction']['due_date'] = date("Y-m-d", strtotime('+' . $booking_details->due_date . ' days'));
            }

            if ($params['transaction']['payment_type'] == 'PAYPAL') {
                self::paypal_sheet_details($params);
            }

            if ($payment_action == 'zero_amount') {
                $params['transaction']['amount'] = 0;
                $params['transaction']['create_token'] = 1;
            }
            
            if (!empty($booking_details->payment_ref->token)) {
                $params['transaction']['payment_data']['token'] = $booking_details->payment_ref->token;
                unset($_SESSION['nn_booking_details']->payment_ref->token);
                unset($params['transaction']['create_token']);
            }
            
            if (!empty($booking_details->cycle)) {
                $params['instalment'] = [
                    'interval' 	=> '1m',
                    'cycles'    => $booking_details->cycle,
                ];
            }

            if ((!empty($payment_details->process_mode) && $payment_details->process_mode == 'redirect') ||
                (!empty($booking_details->do_redirect) && ($booking_details->do_redirect == '1' || $booking_details->do_redirect == true))
            ) {
                $params['transaction']['return_url'] = ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") ? 'https://' : 'http://') . $_SERVER['HTTP_HOST']. $_SERVER['REQUEST_URI'];
            }
        }
    }

    /**
     * Get billing country
     * @param $order
     *
     * @return string
     */
    public static function getBillingCountry($order)
    {
        global $db;
        if (isset($order->billing['country']['id']) && !empty($order->billing['country']['id'])) {
            $billing_country_code = $db->Execute("select countries_iso_code_2  from ".TABLE_COUNTRIES." where countries_id = '".$order->billing['country']['id']."'");
        } else {
            $billing_country_query = $db->Execute("select countries_id from ".TABLE_COUNTRIES_NAME." where countries_name = '".$order->billing['country']."'");
            if (isset($billing_country_query->fields['countries_id'])) {
                $billing_country_code = $db->Execute("select countries_iso_code_2  from ".TABLE_COUNTRIES." where countries_id = '".$billing_country_query->fields['countries_id']."'");
            }
        }
        return !empty($billing_country_code->fields['countries_iso_code_2']) ? $billing_country_code->fields['countries_iso_code_2'] : '';
    }

    /**
     * Get delivery country
     * @param $order
     *
     * @return string
     */
    public static function getDeliveryCountry($order)
    {
        global $db;
        if (isset($order->delivery['country']['id']) && !empty($order->delivery['country']['id'])) {
            $delivery_country_code = $db->Execute("select countries_iso_code_2  from ".TABLE_COUNTRIES." where countries_id = '".$order->delivery['country']['id']."'");
        } else {
            $delivery_country_query = $db->Execute("select countries_id from ".TABLE_COUNTRIES_NAME." where countries_name = '".$order->delivery['country']."'");
            if (isset($delivery_country_query->fields['countries_id'])) {
                $delivery_country_code = $db->Execute("select countries_iso_code_2  from ".TABLE_COUNTRIES." where countries_id = '".$delivery_country_query->fields['countries_id']."'");
            }
        }

        return !empty($delivery_country_code->fields['countries_iso_code_2']) ? $delivery_country_code->fields['countries_iso_code_2'] : '';
    }

    /**
     * Get customer data
     *
     * @return $params
     */
    public static function getCustomerData(&$params)
    {
        global $order;
        $billingCountry_code = !isset($order->billing['country']['iso_code_2']) ? self::getBillingCountry($order) : $order->billing['country']['iso_code_2'];
        $deliveryCountry_code = !isset($order->delivery['country']['iso_code_2']) ? self::getDeliveryCountry($order) : $order->delivery['country']['iso_code_2'];

        $params['customer'] = [
            'gender' 		=> !empty($order->billing['gender']) ? $order->billing['gender'] : 'u',
            'first_name' 	=> !empty($order->billing['firstname']) ? $order->billing['firstname'] : $order->billing['name'],
            'last_name' 	=> !empty($order->billing['lastname']) ? $order->billing['lastname'] : $order->billing['name'],
            'email' 		=> $order->customer['email_address'],
            'customer_ip' 	=> zen_get_ip_address(),
            'customer_no' 	=> !empty($_SESSION['customer_id']) ? $_SESSION['customer_id'] : $order->customer['id'],
            'billing' 		=> [
                'street' 		=> !empty($order->billing['suburb']) ? ($order->billing['street_address'] . ',' . $order->billing['suburb']) : $order->billing['street_address'],
                'city' 			=> $order->billing['city'],
                'zip' 			=> $order->billing['postcode'],
                'country_code' 	=> $billingCountry_code
            ]
        ];
        if (isset($order->billing['state'])) {
             $params['customer']['billing']['state'] = $order->billing['state'];
        }
        if (!empty($order->customer['telephone'])) {
            $params['customer']['tel'] = $order->customer['telephone'];
        }

        if (self::isBillingShippingsame($billingCountry_code, $deliveryCountry_code)) {
            $params['customer']['shipping']['same_as_billing'] = 1;
        } else {
            $params['customer']['shipping'] = [
                'street' 		=> !empty($order->delivery['suburb']) ? $order->delivery['street_address'] . ',' . $order->delivery['suburb'] : $order->delivery['street_address'],
                'city' 			=> $order->delivery['city'],
                'zip' 			=> $order->delivery['postcode'],
                'country_code' 	=> $deliveryCountry_code
            ];

            if (!empty($order->delivery['company'])) {
                $params['customer']['shipping']['company'] = $order->delivery['company'];
            }
            if (isset($order->delivery['state'])) {
                $params['customer']['shipping']['state'] = $order->delivery['state'];
            }
        }

        if (!empty($_SESSION['nn_booking_details']->birth_date)) {
            $params['customer']['birth_date'] = date("Y-m-d", strtotime($_SESSION['nn_booking_details']->birth_date));
        } elseif (!empty($order->billing['company'])) {
            $params['customer']['billing']['company'] = $order->billing['company'];
        }
    }

    /**
     * Get request custom data
     */
    public static function getCustomData(&$params)
    {
        $params['custom'] = [
            'lang' => (isset($_SESSION['languages_code'])) ? strtoupper($_SESSION['languages_code']) : 'DE',
        ];
    }

    /**
     * Check billing and shpping address is same
     * @param $billingCountry_code
     * @param $deliveryCountry_code
     * 
     * @return boolean
     */
    public static function isBillingShippingsame($billingCountry_code, $deliveryCountry_code)
    {
        global $order;

        $delivery_address = array(
            'street' => $order->delivery['street_address'],
            'city' => $order->delivery['city'],
            'postcode' => $order->delivery['postcode'],
            'country' => $deliveryCountry_code
        );
        $billing_address = array(
            'street' => $order->billing['street_address'],
            'city' => ($order->billing['city']),
            'postcode' => ($order->billing['postcode']),
            'country' => $billingCountry_code
        );
        
        if ((empty($delivery_address['street']) && empty($delivery_address['city']) && empty($delivery_address['postcode']) && empty($delivery_address['country'])) &&
            !empty($billing_address)
        ) {
            return true;
        } elseif ($billing_address === $delivery_address) {
            return true;
        }
        
        return false;
    }

    /**
     * Get the order total amount and convert it into minimum unit amount (cents in Euro)
     * @param $order_amount
     *
     * @return int
     */
    public static function getOrderAmount($order_amount)
    {
        global $order;
        $amount = 0;
        $amount = ($order->info['currency_value'] != 0) ? ($order_amount * $order->info['currency_value']) : $order_amount;
        return (sprintf('%0.2f', $amount) * 100);
    }

    /**
     * Get parameter for GooglePay process
     *
     * @return string Hidden fields with GooglePay data.
     */
    public static function getWalletParam()
    {
        global $order, $db;
        $tax_value = $coupon_amount = $discount = $currency_value = 0;
        $currency_value = ($order->info['currency_value'] != 0) ? ($order->info['currency_value']) : '';
        $articleDetails = [];

        foreach ($order->products as $key => $products) {
            if (!empty($order->info['coupon_code'])) {
                $coupon_amount = $db->Execute("select coupon_amount from ".TABLE_COUPONS." where coupon_code = '".$order->info['coupon_code']."'");
            }

            $productAmount = (DISPLAY_PRICE_WITH_TAX == 'true') ? (!empty($currency_value) ? (string)(($products['qty'] * (round(($products['final_price'] * $currency_value) + zen_calculate_tax($products['final_price'], $products['tax']), 2)))*100) : (string)(($products['qty'] * (round(($products['final_price']) + zen_calculate_tax($products['final_price'], $products['tax']), 2)))*100)) : (!empty($currency_value) ? (string)((round(($products['qty'] * $products['final_price'] * $currency_value), 2))*100) : (string)((round(($products['qty'] * $products['final_price']), 2))*100));
            $articleDetails[] = array(
                 'label'=> str_replace("'", "#single_quote", $products['name']). ' x ' .$products['qty'],
                 'amount' => $productAmount,
                 'type' => 'SUBTOTAL',
            );
        }

        if ($order->info['tax'] != 0) {
            foreach ($order->info['tax_groups'] as $key => $value) {
                $tax_value += zen_round($value, 2);
            }
            
            $articleDetails[] = array(
                'label'     => (DISPLAY_PRICE_WITH_TAX == 'true') ? (defined('MODULE_PAYMENT_NOVALNET_INCL_TAX_LABEL') ? MODULE_PAYMENT_NOVALNET_INCL_TAX_LABEL : '') : (defined('MODULE_PAYMENT_NOVALNET_EXCL_TAX_LABEL') ? MODULE_PAYMENT_NOVALNET_EXCL_TAX_LABEL : ''),
                'amount'    => !empty($currency_value) ? (string) (round(($tax_value * $currency_value), 2) * 100) : (string) (round(($tax_value), 2) * 100),
                'type'      => 'SUBTOTAL'
            );
        }

        if ($_SESSION['cot_gv'] != 0.00 || (isset($order->info['coupon_code']) && !empty($order->info['coupon_code']))) {
            $discount = (isset($order->info['coupon_amount']) && !empty($order->info['coupon_amount'])) ? $order->info['coupon_amount'] : ($coupon_amount->fields['coupon_amount']);
            $deduction = $discount + $_SESSION['cot_gv'];
            $articleDetails[] = array(
                'label'=> defined('MODULE_PAYMENT_NOVALNET_DISCOUNT_AND_GIFT_VOUCHER_LABEL') ? MODULE_PAYMENT_NOVALNET_DISCOUNT_AND_GIFT_VOUCHER_LABEL : '',
                'amount' => !empty($currency_value) ? (string) (round(($deduction * $currency_value), 2) * 100) : (string) (round(($deduction), 2) * 100),
                'type' => 'SUBTOTAL'
            );
        }

        $articleDetails[] = array(
            'label'=> defined('MODULE_PAYMENT_NOVALNET_SHIPPING_LABEL') ? MODULE_PAYMENT_NOVALNET_SHIPPING_LABEL : '',
            'amount' => !empty($currency_value) ? (string)(($order->info['shipping_cost'] * $currency_value)*100) :   (string)(($order->info['shipping_cost'])*100),
            'type' => 'SUBTOTAL'
        );
        return "<input type='hidden' value='". (!empty($articleDetails) ? json_encode($articleDetails) : [])."' id='nn_article_details'>";
    }

    /**
     * Handle redirect payments success response
     * @param $request
     *
     * @return $response
     */
    public static function handleRedirectSuccessResponse($request)
    {
        $transaction_details = array('transaction' => array('tid' => $request['tid']));
        return self::sendRequest($transaction_details, self::getActionEndpoint('transaction_details'));
    }

    /**
     * Get Novalnet transaction details from novalnet table
     *
     * @param array $order_no
     * 
     * @return object
     */
    public static function getNovalnetTransDetails($order_no)
    {
        global $db;
        return $db->Execute("SELECT * FROM ".TABLE_NOVALNET_TRANSACTION_DETAIL." WHERE order_no='" . zen_db_input($order_no) . "'");
    }

    /**
     * Send request to server
     *
     * @param array $data
     * @param string paygate_url
     * @param string access_key
     */
    public static function sendRequest($data, $paygate_url, $access_key = null)
    {
        $headers = self::getHeadersParam($access_key);
        $json_data = !empty($data) ? json_encode($data) : '{}';
		
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $paygate_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($curl);

        if (curl_errno($curl)) {
            return ['status_code' => 106, 'status_text' => curl_error($curl)];
        }
        curl_close($curl);      
		
        return !empty($result) ? json_decode($result, true) : [];
    }

    /**
     * Get transaction details
     * @param $response
     *
     * @return $txn_details
     */
    public static function getTransactionDetails($response)
    {
        global $currencies;
        $txn_details = '';
        $payment_type = !empty($response['transaction']['payment_type']) ? $response['transaction']['payment_type'] : '';

        if (!empty($response ['transaction']['tid'])) {
            if ($payment_type == 'GOOGLEPAY' && isset($response['transaction']['payment_data'])) {
                $txn_details .= PHP_EOL. sprintf(MODULE_PAYMENT_NOVALNET_WALLET_PAYMENT_SUCCESS_TEXT, $response['transaction']['payment_data']['last_four']);
            }
            $txn_details .= PHP_EOL. MODULE_PAYMENT_NOVALNET_TRANSACTION_ID .$response['transaction']['tid'];
            $txn_details .= ($response ['transaction']['test_mode'] == 1) ? PHP_EOL. MODULE_PAYMENT_NOVALNET_PAYMENT_MODE : '';
        }

        // to do
        if (($response ['transaction']['amount'] == 0) && ($_SESSION['nn_booking_details']->payment_action == 'zero_amount')) {
            $txn_details .= PHP_EOL. MODULE_PAYMENT_NOVALNET_ZEROAMOUNT_BOOKING_MESSAGE;
        }

        if ($response['transaction']['status_code'] == 75 && $response['transaction']['status'] == 'PENDING') {
            $txn_details .= PHP_EOL . MODULE_PAYMENT_NOVALNET_MENTION_GUARANTEE_PAYMENT_PENDING_TEXT.PHP_EOL;
        }

        if (!empty($response['transaction']['partner_payment_reference'])) {
            $amount = $currencies->format($response['transaction']['amount'] / 100, true, $response['transaction']['currency']);
            $txn_details .= PHP_EOL . PHP_EOL . sprintf(MODULE_PAYMENT_NOVALNET_MULTIBANCO_NOTE, $amount);
            $txn_details .= PHP_EOL . sprintf(MODULE_PAYMENT_NOVALNET_PARTNER_PAYMENT_REFERENCE, $response['transaction']['partner_payment_reference']) . PHP_EOL;
        }

        return $txn_details;
    }

    /**
     * Get Novalnet bank details and its reference
     * @param $response
     *
     * @return $note
     */
    public static function getBankDetails($response)
    {
        global $currencies;
        $note = '';
        $amount = 0;
        $bank_details = [];
        $amount = $currencies->format($response['transaction']['amount']/100, false, $response['transaction']['currency']);

        if (!empty($response['instalment']['cycle_amount'])) {
            $amount = $currencies->format($response['instalment']['cycle_amount']/100, false, $response['transaction']['currency']);
        }

        $note = !empty($response['instalment']['cycle_amount']) ? (PHP_EOL . sprintf(MODULE_PAYMENT_NOVALNET_INSTALMENT_AMOUNT_TRANSFER_NOTE, $amount) . PHP_EOL . PHP_EOL) :
            (PHP_EOL .PHP_EOL.sprintf(MODULE_PAYMENT_NOVALNET_AMOUNT_TRANSFER_NOTE, $amount) . PHP_EOL .PHP_EOL);

        if ($response['transaction']['status'] != 'ON_HOLD' && !empty($response['transaction']['due_date'])) { // If due date is not empty
            $note = !empty($response['instalment']['cycle_amount']) ? (PHP_EOL . PHP_EOL.sprintf(MODULE_PAYMENT_NOVALNET_INSTALMENT_AMOUNT_TRANSFER_NOTE_DUE_DATE, $amount, $response ['transaction']['due_date']) . PHP_EOL . PHP_EOL) :
                (PHP_EOL .PHP_EOL.sprintf(MODULE_PAYMENT_NOVALNET_AMOUNT_TRANSFER_NOTE_DUE_DATE, $amount, $response['transaction']['due_date']) . PHP_EOL .PHP_EOL);
        }

        $bank_details = array(
            'account_holder' => PHP_EOL.MODULE_PAYMENT_NOVALNET_ACCOUNT_HOLDER ,
            'bank_name'      => MODULE_PAYMENT_NOVALNET_BANK_NAME ,
            'bank_place'     => MODULE_PAYMENT_NOVALNET_BANK_PLACE  ,
            'iban'           => MODULE_PAYMENT_NOVALNET_IBAN  ,
            'bic'            => MODULE_PAYMENT_NOVALNET_BIC ,
        );

        foreach ($bank_details as $key => $text) {
            if (! empty($response ['transaction']['bank_details'][ $key ])) {
                $note .= $text. $response['transaction']['bank_details'][ $key ] . PHP_EOL;
            }
        }

        $note .= PHP_EOL. MODULE_PAYMENT_NOVALNET_PAYMENT_REFERENCE_TEXT .PHP_EOL;
        $note .= sprintf(MODULE_PAYMENT_NOVALNET_PAYMENT_REFERENCE, ('TID ' . $response['transaction']['tid'])) . PHP_EOL;

        return $note;
    }

     /**
     * Get nearest Cashpayment supported stores
     * @param $response
     *
     * @return $txn_details
     */
    public static function getNearestStoreDetails($response)
    {
        global $db;
        $txn_details = '';

        if (!empty($response['transaction']['due_date'])) {
            $txn_details .= PHP_EOL . PHP_EOL.MODULE_PAYMENT_NOVALNET_TRANS_SLIP_EXPIRY_DATE .date(DATE_FORMAT, strtotime($response['transaction']['due_date']));
        }

        $txn_details .= PHP_EOL . PHP_EOL .MODULE_PAYMENT_NOVALNET_NEAREST_STORE_DETAILS . PHP_EOL ;

        if (!empty($response['transaction']['nearest_stores'])) {
            foreach ($response['transaction']['nearest_stores'] as $store) {
                $txn_details .= PHP_EOL . $store['store_name'];
                $txn_details .= PHP_EOL . $store['street'];
                $txn_details .= PHP_EOL . $store['zip'] . ' ' . $store['city'];
                $country_name = $db->Execute("select countries_name from " . TABLE_COUNTRIES . " where countries_iso_code_2 = '" . $store['country_code'] . "'");
                if ($country_name->RecordCount()) {
                    $txn_details .= PHP_EOL . $country_name->fields['countries_name'];
                }

                $txn_details .= PHP_EOL . PHP_EOL;
            }
        }

        return $txn_details;
    }

    /**
     * Add instalment details in end customer comments
     *
     * @param $response
     *
     * @return $txn_details
     */
    public static function getInstalmentDetails($response)
    {
        global $currencies;
        $txn_details = '';
        $amount = 0;
        $amount = $currencies->format($response['instalment']['cycle_amount']/100, false, $response['instalment']['currency']);

        if ($response['transaction']['status'] == 'CONFIRMED') {
            $txn_details .=  PHP_EOL.PHP_EOL.MODULE_PAYMENT_NOVALNET_INSTALMENT_INSTALMENTS_INFO.PHP_EOL.MODULE_PAYMENT_NOVALNET_INSTALMENT_PROCESSED_INSTALMENTS.$response['instalment']['cycles_executed'] . PHP_EOL;
            $txn_details .=  MODULE_PAYMENT_NOVALNET_INSTALMENT_DUE_INSTALMENTS.$response['instalment']['pending_cycles']. PHP_EOL;
            $txn_details .=  MODULE_PAYMENT_NOVALNET_INSTALMENT_NEXT_INSTALMENT_AMOUNT.$amount. PHP_EOL;

            if (!empty($response['instalment']['next_cycle_date'])) {
                $txn_details .=  MODULE_PAYMENT_NOVALNET_INSTALMENT_NEXT_INSTALMENT_DATE. date('Y-m-d', strtotime($response['instalment']['next_cycle_date'])). PHP_EOL;
            }
        }

        return $txn_details;
    }

    /**
     * Get shop order status id
     *
     * @param $transaction_status
     * @param $payment_name
     *
     * @return $order_status_id
     */
    public static function getOrderStatus($transaction_status, $payment_name)
    {
        $order_status_id = '';

        if ($transaction_status == 'PENDING' && $payment_name == 'INVOICE') {
            $order_status_id = 2;
        } elseif ($transaction_status == 'PENDING') {
            $order_status_id = 1;
        } elseif ($transaction_status == 'CONFIRMED') {
            $order_status_id = 2;
        } elseif ($transaction_status == 'ON_HOLD') {
            $order_status_id = 99;
        }

        return $order_status_id;
    }

    /**
     * Update order status and insert the transaction details in the database
     * @param $order_id
     * @param $txn_details
     * @param $response
     *
     * @return mixed
     */
    public static function updateOrderStatus($order_id, $txn_details, $response)
    {
        global $order;
        $payment_status = [];
        $status_update  = [];
        $payment_details = [];
        $payment_type = !empty($response['transaction']['payment_type']) ? $response['transaction']['payment_type'] : '';
        $payment_status['orders_status'] = $status_update['orders_status_id'] = self::getOrderStatus($response['transaction']['status'], $payment_type);
        $status_update['comments']  = $order->info['comments'] = zen_db_prepare_input($txn_details);
        $novalnet_transaction_details = array(
            'order_no' 		=> $order_id,
            'tid' 			=> $response['transaction']['tid'],
            'amount' 		=> $response['transaction']['amount'],
            'payment_type' 	=> $payment_type,
            'status' 		=> $response['transaction']['status']
        );

        if (!empty($response['instalment']) && $response['transaction']['status'] == 'CONFIRMED') {
                $order_total = self::getOrderAmount($order->info['total']);
                $total_amount = ($response['transaction']['amount'] < $order_total) ? $order_total : $response['transaction']['amount'];
                $novalnet_transaction_details['instalment_cycle_details'] = self::storeInstalmentdetails($response, $total_amount);
        }

        if (!empty($response['transaction']['bank_details'])) {
            $payment_details = $response['transaction']['bank_details'];

            if (isset($response['transaction']['due_date'])) {
                $payment_details['novalnet_due_date'] = $response['transaction']['due_date'];
            }
        } elseif (!empty($response['transaction']['nearest_stores'])) {
            $payment_details['nearest_stores'] = $response['transaction']['nearest_stores'];
            $_SESSION['novalnet_checkout_js'] 	 = $response['transaction']['checkout_js'];
			$_SESSION['novalnet_checkout_token'] = $response['transaction']['checkout_token'];
        } elseif (!empty($response['transaction']['payment_data']['token']) && isset($_SESSION['nn_booking_details']->create_token) && $_SESSION['nn_booking_details']->create_token == '1') {
            $payment_details = $response['transaction']['payment_data'];
            if ($_SESSION['nn_booking_details']->payment_action == 'zero_amount') {
                $payment_details['zero_amount_booking'] = 1;
            }
        } elseif (!empty($response['transaction']['payment_data']['token'])
            && !empty($_SESSION['nn_booking_details']->payment_action)
            && $_SESSION['nn_booking_details']->payment_action == 'zero_amount'
        ) {
            $payment_details = array(
                'token' => $response['transaction']['payment_data']['token'],
                'zero_amount_booking' => 1
            );
        }
		$novalnet_transaction_details['payment_details'] = !empty($payment_details) ? json_encode($payment_details) : '{}';
        zen_db_perform(TABLE_NOVALNET_TRANSACTION_DETAIL, $novalnet_transaction_details, 'insert');
        zen_db_perform(TABLE_ORDERS, $payment_status, "update", "orders_id='$order_id'");
        zen_db_perform(TABLE_ORDERS_STATUS_HISTORY, $status_update, "update", "orders_id='$order_id'");
    }

    /**
     * Get instalment details to store in Novalnet Transaction details
     *
     * @param $response
     * @param $total_amount
     *
     * @return string
     */
    public static function storeInstalmentdetails($response, $total_amount)
    {
        if (empty($response['instalment'])) {
            return '{}';
        }
        $instalment = $response['instalment'];
        if (isset($instalment['cycle_dates'])) {
            $total_cycles = count($instalment['cycle_dates']);
        }
        $cycle_amount = $instalment['cycle_amount'];
        $last_cycle_amount = $total_amount - ($cycle_amount * ($total_cycles - 1)) ;
        $cycles = $instalment['cycle_dates'];
        $cycle_details = array();

        foreach ($cycles as $cycle => $cycle_date) {
            $cycle_details[$cycle -1 ]['date'] = $cycle_date;
            $cycle_details[$cycle -1 ]['next_instalment_date'] = $cycle_date;
            $cycle_details[$cycle -1 ]['status'] = 'Pending';
            if (!empty($instalment['cycles_executed']) && $cycle == $instalment['cycles_executed']) {
                $cycle_details[$cycle -1 ]['reference_tid'] = !empty($instalment['tid']) ? $instalment['tid'] : (!empty($response['transaction']['tid']) ? $response['transaction']['tid'] : '');
                $cycle_details[$cycle -1 ]['status'] = 'Paid';
                $cycle_details[$cycle -1 ]['paid_date'] = date('Y-m-d H:i:s');
            }
            $cycle_details[$cycle -1 ]['instalment_cycle_amount'] = ($cycle == $total_cycles)?$last_cycle_amount : $instalment['cycle_amount'];
        }
        return (!empty($cycle_details) ? json_encode($cycle_details) : '{}');
    }

    /**
     * Send transaction update call to update order_no in Novalnet
     * @param $order_no
     *
     * @return none
     */
    public static function sendTransactionUpdate($order_no)
    {
        $params = [
            'transaction' => [
                'tid' 		=> $_SESSION['nn_response']['transaction']['tid'],
                'order_no' 	=> $order_no,
            ],
        ];

        self::getCustomData($params);
        self::sendRequest($params, self::getActionEndpoint('transaction_update'));

        if (isset($_SESSION['nn_response'])) {
            unset($_SESSION['nn_response']);
        }
    }

    /**
     * Hadnle temporary created order for the failure transaction
     *
     * @param array $response
     * @param string $error_text
     *
     * @return none
     */
    public static function processTempOrderFail($response, $error_text = '')
    {
        global $messageStack;
        $status_text = self::getServerResponse($response);
        $status_text = (!empty($status_text)) ? $status_text : $error_text;
        $messageStack->add_session('checkout_payment', $status_text . '<!-- -->', 'error');
        zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', true, false));
    }

    /**
     * Validate checksum
     *
     * @param $data
     *
     * @return boolean
     */
    public static function validateCheckSum($data)
    {
        if (!empty($data['checksum']) && !empty($data['tid']) && !empty($data['status']) && !empty($_SESSION['nn_txn_secret']) && !empty(MODULE_PAYMENT_NOVALNET_ACCESS_KEY)) {
            $checksum = hash('sha256', $data['tid'] . $_SESSION['nn_txn_secret'] . $data['status'] . strrev(MODULE_PAYMENT_NOVALNET_ACCESS_KEY));
            if ($checksum == $data['checksum']) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get payment response text
     *
     * @param array $response
     * 
     * @return string
     */
    public static function getServerResponse($response)
    {
        if (!empty($response['status_desc'])) {
            return $response['status_desc'];
        } elseif (!empty($response['status_text'])) {
            return $response['status_text'];
        } elseif (!empty($response['status_message'])) {
            return $response['status_message'];
        } elseif (!empty($response['result']['status_text'])) {
            return $response['result']['status_text'];
        }

        return MODULE_PAYMENT_NOVALNET_TRANSACTION_ERROR;
    }

    /**
     * Get payment request url
     *
     * @param string $action
     * @return string
     */
    public static function getActionEndpoint($action)
    {
        $endpoint = 'https://payport.novalnet.de/v2/';
        return $endpoint . str_replace('_', '/', $action);
    }

    /**
     * Get merchant data
     *
     * @return $params
     */
    public static function getMerchantData(&$params)
    {
        $params['merchant'] = [
            'signature' => defined('MODULE_PAYMENT_NOVALNET_PUBLIC_KEY') ? MODULE_PAYMENT_NOVALNET_PUBLIC_KEY : '',
            'tariff'    => defined('MODULE_PAYMENT_NOVALNET_TARIFF_ID') ? MODULE_PAYMENT_NOVALNET_TARIFF_ID : '',
        ];
    }

    /**
     * Get request header
     */
    public static function getHeadersParam($access_key = null)
    {
        $access_key = !empty($access_key) ? $access_key : MODULE_PAYMENT_NOVALNET_ACCESS_KEY;
        return [
            'Content-Type:application/json',
            'Charset:utf-8',
            'Accept:application/json',
            'X-NN-Access-Key:' . base64_encode($access_key)
        ];
    }

    /**
     * Check for the success status of the Novalnet payment call.
     *
     * @param $data.
     *
     * @return boolean
     */
    public static function is_success_status($data)
    {
        return ((!empty($data['result']['status']) && $data['result']['status'] === 'SUCCESS') || (!empty($data['status']) && $data['status'] === 'SUCCESS'));
    }

    /**
     * Insert transaction, bank and nearest store details in the database
     * @param $order_no
     * @param $payment_method
     * @param $response
     * @param $status_update
     *
     * @return mixed
     */
    public static function insertTransactionDetails($response, $order_no = '')
    {
        $txn_details = '';

        if ($response['result']['status'] == 'SUCCESS') {
            $txn_details = self::getTransactionDetails($response);
            $payment_type = !empty($response['transaction']['payment_type']) ? $response['transaction']['payment_type'] : '';

            // Invoice payments
            if (!empty($response['transaction']['bank_details']) && $response['transaction']['status_code'] != 75) {
                $txn_details .= self::getBankDetails($response);
            }

            // Cashpayment
            if (!empty($response['transaction']['nearest_stores'])) {
                $txn_details .= self::getNearestStoreDetails($response);
            }

            if (!empty($response['instalment']) && ($response['transaction']['status'] == 'CONFIRMED')) {
                $txn_details .= self::getInstalmentDetails($response);
            }
        }
        return $txn_details;
    }

    /**
     * Validate customer email
     *
     * @param $emails
     *
     * @return boolean
     */
    public static function validateEmail($emails)
    {
        include_once(DIR_FS_CATALOG. 'includes/functions/functions_email.php');
        $email = explode(',', $emails);

        foreach ($email as $value) {
            if (!zen_validate_email($value)) {
                return '';
            }

            return $value;
        }
    }

    /**
     * Paypal sheet details
     *
     * @return $params
     */
    public static function paypal_sheet_details(&$params)
    {
        global $order, $db;
        $currency_value = 0;
        $currency_value = ($order->info['currency_value'] != 0) ? ($order->info['currency_value']) : '';
        $coupon_amount = $db->Execute("select coupon_amount from ".TABLE_COUPONS." where coupon_code = '".$order->info['coupon_code']."'");
        $nn_total = 0;

        foreach ($order->products as $products) {
            $attributes = '';
            if (!empty($products['attributes'])) {
                foreach ($products['attributes'] as $attr => $value) {
                    $attributes .= ', ' . $value['option'] . ':' . $value['value'];
                }
            }

            $product_type = ($products['products_weight'] != 0) ? 'physical' : 'digital';
            $productId = !empty($products['id']) ? explode(":", $products['id']) : [];
            $product_desc = $db->Execute("select products_description from " . TABLE_PRODUCTS_DESCRIPTION . " where language_id = '" . $_SESSION['languages_id'] . "' and products_id = '" . $productId[0] . "'");
            $params['cart_info']['line_items'][] = array(
                'name' => $products['name'] . $attributes,
                'price' => !empty($currency_value) ? (string) ((round((float) ($products['final_price'] * $currency_value), 2)) * 100) : (string) ((round((float) ($products['final_price']), 2)) * 100),
                'quantity' => $products['qty'],
                'description' => !empty($product_desc->fields['products_description']) ? $product_desc->fields['products_description'] : '',
                'category' => $product_type,
            );
            $nn_total += (string) ((round((float) ($products['final_price'] * $products['qty']), 2)) * 100);
        }

        if (!empty($order->info['coupon_code']) || !empty($order->info['coupon_amount'])) {
            $discount_amount = 0;
            $discount_amount = !empty($order->info['coupon_amount']) ? ((string) ((round((float) $order->info['coupon_amount'], 2)) * 100)) : ((string) ((round((float) ($coupon_amount->fields['coupon_amount']), 2)) * 100));
            $params['cart_info']['line_items'][] = array(
                'name' => 'Discount coupon',
                'price' => '-'.$discount_amount,
                'quantity' => 1,
            );
            $nn_total -= $discount_amount;
        }

        if ($_SESSION['cot_gv'] != 0.00) {
            $params['cart_info']['line_items'][] = array(
                'name' => 'Gift certificate',
                'price' => '-'.(string) ((round((float) $_SESSION['cot_gv'], 2)) * 100),
                'quantity' => 1,
            );
            $nn_total -= (string) ((round((float) $_SESSION['cot_gv'], 2)) * 100);
        }

        $params['cart_info']['items_tax_price'] = !empty($currency_value) ? (string) ((round((float) (($order->info['tax']) * $currency_value), 2)) * 100) : (string) ((round((float) (($order->info['tax'])), 2)) * 100);
        $nn_total += $params['cart_info']['items_tax_price'];
        $params['cart_info']['items_shipping_price'] = !empty($currency_value) ? (string) ((round((float) ($order->info['shipping_cost'] * $currency_value), 2)) * 100) : (string) ((round((float) ($order->info['shipping_cost'] * $currency_value), 2)) * 100);
        $nn_total += $params['cart_info']['items_shipping_price'];
        $nn_diff_amount = $params['transaction']['amount'] - $nn_total;

        if (!empty($nn_diff_amount)) {
            $params['cart_info']['items_tax_price'] = $nn_diff_amount + $params['cart_info']['items_tax_price'];
        }
    }

    /**
     * Get order status ID.
     *
     * @return $status_id
     */
    public static function getOrderStatusId()
    {
        global $db;
        $status_id = '';
        $order_status = $db->Execute("SELECT orders_status_name, orders_status_id FROM ".TABLE_ORDERS_STATUS." WHERE orders_status_name LIKE '%cancel%'");
        if ($order_status->link->affected_rows > 0) {
            $status_id = $order_status->fields['orders_status_id'];
        }

        return $status_id;
    }

    /**
     * send payment confirmation mail to customer.
     *
     * @param $comments, $order_no
     *
     * @return boolean
     */
    public static function sendPaymentConfirmationMail($comments, $order_no)
    {
        global $db;
        $customer_info = '';
        $customer_info = $db->Execute("SELECT * FROM ".TABLE_ORDERS." WHERE orders_id = '".$order_no."'");
        $customer_details = $customer_info->fields;
        $customer_name = $customer_details['customers_name'];
        $email_subject   = sprintf(MODULE_PAYMENT_NOVALNET_ORDER_MAIL_SUBJECT, $order_no, STORE_NAME);
        $email_content   = sprintf(MODULE_PAYMENT_NOVALNET_ORDER_MAIL_MESSAGE, STORE_NAME). PHP_EOL. PHP_EOL. MODULE_PAYMENT_NOVALNET_CUSTOMER_SALUTATION. '<b>'. $customer_details['customers_name'].PHP_EOL .PHP_EOL. sprintf(MODULE_PAYMENT_NOVALNET_ORDER_NUMBER, $order_no) . PHP_EOL. PHP_EOL. sprintf(MODULE_PAYMENT_NOVALNET_ORDER_MAIL_DATE, strftime(DATE_FORMAT_LONG)).PHP_EOL. PHP_EOL.  MODULE_PAYMENT_NOVALNET_ORDER_CONFIRMATION .zen_href_link(FILENAME_ACCOUNT_HISTORY_INFO, 'order_id=' . $order_no, 'SSL', false) . PHP_EOL . nl2br($comments);
        $email_content .= PHP_EOL .PHP_EOL.MODULE_PAYMENT_NOVALNET_DELIVERY_ADDRESS. PHP_EOL. $customer_details['delivery_name'] .PHP_EOL .  $customer_details['delivery_street_address'] . PHP_EOL. $customer_details['delivery_postcode'] . PHP_EOL . $customer_details['delivery_city'] . PHP_EOL . $customer_details['delivery_country'] . PHP_EOL;
        $email_content .= PHP_EOL .MODULE_PAYMENT_NOVALNET_BILLING_ADDRESS. PHP_EOL. $customer_details['billing_name'] .PHP_EOL .  $customer_details['billing_street_address'] . PHP_EOL. $customer_details['billing_postcode'] . PHP_EOL . $customer_details['billing_city'] . PHP_EOL . $customer_details['billing_country'] . PHP_EOL;
        zen_mail($customer_name, $customer_details['customers_email_address'], $email_subject, str_replace('</br>', PHP_EOL, $email_content), '', '', array(), '', '', STORE_NAME, EMAIL_FROM);
    }

    /**
    * Update order status in the shop
    *
    * @param integer $order_id
    * @param string $order_status
    * @param string $message
    */
    public static function novalnetUpdateOrderStatus($order_id, $message, $order_status = '')
    {
        global $db;
        zen_db_perform(TABLE_ORDERS, array(
            'orders_status' => $order_status,
        ), "update", "orders_id='$order_id'");
        $db->Execute("insert into " . TABLE_ORDERS_STATUS_HISTORY . " (orders_id, orders_status_id, date_added, customer_notified, comments) values ('".zen_db_input($order_id)."', '".zen_db_input($order_status)."', '" .date('Y-m-d H:i:s') . "', '1', '".zen_db_input($message)."')");
    }
}
