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
 * Script : NovalnetHelper.class.php
 *
 */
include_once(DIR_FS_CATALOG . DIR_WS_LANGUAGES. $_SESSION['language']."/modules/payment/novalnet_payments.php");
include_once(DIR_FS_CATALOG. 'includes/functions/functions_email.php');
class NovalnetHelper{

	/**
	 * Check the merchant credentials are empty
	 *
	 * @return boolean
	 */
	public static function checkMerchantCredentials() {
		if ((!defined('MODULE_PAYMENT_NOVALNET_PUBLIC_KEY') || MODULE_PAYMENT_NOVALNET_PUBLIC_KEY == '' )
		|| (!defined('MODULE_PAYMENT_NOVALNET_PAYMENT_ACCESS_KEY') || MODULE_PAYMENT_NOVALNET_PAYMENT_ACCESS_KEY == '' )) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Get customer data
	 *
	 * @return $data
	 */
	public static function getCustomerData() {
		global $order;
		$data['customer'] = [
			'gender'      => !empty($order->billing['gender']) ? $order->billing['gender'] : 'u',
			'first_name'  => !empty($order->billing['firstname']) ? $order->billing['firstname'] : $order->billing['name'],
			'last_name'   => !empty($order->billing['lastname']) ? $order->billing['lastname'] : $order->billing['name'],
			'email'       => $order->customer['email_address'],
			'customer_ip' => zen_get_ip_address(),
			'customer_no' => $_SESSION['customer_id'],
			'billing'     => [
				'street'            => !empty($order->billing['suburb']) ? $order->billing['street_address'] . ',' . $order->billing['suburb'] : $order->billing['street_address'],
				'city'              => $order->billing['city'],
				'state'              => $order->billing['state'],
				'zip'               => $order->billing['postcode'],
				'country_code'      => $order->billing['country']['iso_code_2'],
				'search_in_street'  => '1',
			],
		];
		if (!empty($order->customer['telephone'])) {
			$data['customer']['tel'] = $order->customer['telephone'];
		}
		if (!empty($order->billing['company'])) {
			$data['customer']['billing']['company'] = $order->billing['company'];
		}
		if (self::isBillingShippingsame()) {
			$data['customer']['shipping']['same_as_billing'] = 1;
		} else {
			$data['customer']['shipping']    = [
				'street'        => !empty($order->delivery['suburb']) ? $order->delivery['street_address'] . ',' . $order->delivery['suburb'] : $order->delivery['street_address'],
				'city'          => $order->delivery['city'],
				'state'          => $order->delivery['state'],
				'zip'           => $order->delivery['postcode'],
				'country_code'  => $order->delivery['country']['iso_code_2'],
			];
			if (!empty($order->delivery['company'])) {
				$data['customer']['shipping']['company'] = $order->delivery['company'];
			}
		}
		if (!empty($_SESSION['nn_booking_details']->birth_date) && empty($order->billing['company'])) {
			$data['customer']['birth_date'] = date("Y-m-d",strtotime('+'.$_SESSION['nn_booking_details']->birth_date.' days'));;
		}
		return $data;
	}

	/**
	 * Get transaction data
	 *
	 * @return $data
	 */
	public static function getTransactionData() {
		global $order;
		$data['transaction'] = [
			'amount'           => self::getOrderAmount($order->info['total']),
			'currency'         => $order->info['currency'],
			'system_name'      => 'Zen_Cart',
			'system_version'   => PROJECT_VERSION_MAJOR . '.' . PROJECT_VERSION_MINOR.'-NN13.0.0',
			'system_url'       => ((ENABLE_SSL == true) ? HTTPS_SERVER : HTTP_SERVER . DIR_WS_CATALOG),
			'system_ip'        => $_SERVER['SERVER_ADDR'],
		];
		if (isset($_SESSION['nn_booking_details']->test_mode)) {
			$data['transaction']['test_mode'] = $_SESSION['nn_booking_details']->test_mode;
		}
		if (isset($_SESSION['nn_payment_details']->type)) {
            $data['transaction']['payment_type'] = $_SESSION['nn_payment_details']->type;
        }
		if (!empty($_SESSION['nn_booking_details']->due_date)) {
			$due_date = date("Y-m-d",strtotime('+'.$_SESSION['nn_booking_details']->due_date.' days'));
			$data['transaction']['due_date'] = $due_date;
		}
		return $data;
	}

	/**
	 * Get Card details like pesudo hash etc.,
	 *
	 * @return $data
	 */
	public static function getAccountDetails() {
		$data = [];
            if ($_SESSION['nn_payment_details']->type == 'CREDITCARD') {
				if (!empty($_SESSION['nn_booking_details']->pan_hash) || !empty($_SESSION['nn_booking_details']->unique_id))
                $data['transaction']['payment_data'] = [
                    'pan_hash'   => $_SESSION['nn_booking_details']->pan_hash,
                    'unique_id'  => $_SESSION['nn_booking_details']->unique_id
                ];             
            }
            if (in_array($_SESSION['nn_payment_details']->type, array('DIRECT_DEBIT_SEPA', 'INSTALMENT_DIRECT_DEBIT_SEPA', 'GUARANTEED_DIRECT_DEBIT_SEPA'))) {
				if ($_SESSION['nn_booking_details']->iban != '') {
					$data['transaction']['payment_data'] ['iban'] = $_SESSION['nn_booking_details']->iban;
					if($_SESSION['nn_booking_details']->bic != '') {
						$data['transaction']['payment_data'] ['bic'] = $_SESSION['nn_booking_details']->bic;
					}
				}
			}
			if(!empty($_SESSION['nn_booking_details']->payment_ref->token) && (empty($_SESSION['nn_booking_details']->pan_hash) || empty($_SESSION['nn_booking_details']->unique_id))) { // Reference transaction
				$data['transaction']['payment_data']['token'] = $_SESSION['nn_booking_details']->payment_ref->token;
				unset($_SESSION['nn_booking_details']->payment_ref->token);
			} elseif(($_SESSION['nn_booking_details']->create_token == '1') && ((!empty($_SESSION['nn_booking_details']->pan_hash) || !empty($_SESSION['nn_booking_details']->unique_id)) || !empty($_SESSION['nn_booking_details']->iban)) ){ // New transaction
				$data['transaction']['create_token'] = 1;
				unset($_SESSION['nn_booking_details']->create_token);
			} 
            if (in_array($_SESSION['nn_payment_details']->type, array('GOOGLEPAY', 'CREDITCARD')) && (isset($_SESSION['nn_booking_details']->enforce_3d))) {
                if ($_SESSION['nn_booking_details']->enforce_3d == 1) {
					$data['transaction']['enforce_3d'] = '1';
				} 
            }
		return $data;
	}

	/**
	 * Get request custom data
	 */
	public static function getCustomData() {
		$data = [];
		$data['custom'] = [
			'lang' => (isset($_SESSION['languages_code'])) ? strtoupper($_SESSION['languages_code']) : 'DE',
		];
		return $data;
	}

	/**
	 * Get hosted payment page data
	 *
	 * @return $data
	 */
	public static function getHostedPageData() {
		$data = [];
		$data['hosted_page'] = [
			'hide_blocks' => ['ADDRESS_FORM', 'SHOP_INFO', 'LANGUAGE_MENU', 'HEADER', 'TARIFF'],
			'skip_pages'  => ['CONFIRMATION_PAGE', 'SUCCESS_PAGE', 'PAYMENT_PAGE'],
			'type' => 'PAYMENTFORM',
		];
		return $data;
	}

	/**
	 * Initial Call to get Redirect URL
	 *
	 * @param int $order_no Temp order number.
	 * @param string $payment_name End customer choosen payment name.
	 *
	 * @return $response
	 */
	public static function getRedirectData(&$params) {
		if ($_SESSION['nn_payment_details']->type == 'PAYPAL') {
			self::paypal_sheet_details($params);
		}
		if ($_SESSION['nn_payment_details']->type == 'GOOGLEPAY') {
            $params['transaction']['payment_data']['wallet_token'] = $_SESSION['nn_booking_details']->wallet_token;
        }
        if(!empty($_SESSION['nn_booking_details']->token)) {
            $params['transaction'] = array_merge(NovalnetHelper::getTransactionData()['transaction'], NovalnetHelper::getAccountDetails()['transaction']);
        }
        if ($_SESSION['nn_payment_details']->type == 'CREDITCARD') {
            $params['transaction'] = array_merge(NovalnetHelper::getTransactionData()['transaction'], NovalnetHelper::getAccountDetails()['transaction']);
        }
        $params['transaction']['return_url']       = ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") ? 'https://' : 'http://') . $_SERVER['HTTP_HOST']. $_SERVER['REQUEST_URI'];
        $params['transaction']['error_return_url'] = ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") ? 'https://' : 'http://') . $_SERVER['HTTP_HOST']. $_SERVER['REQUEST_URI'];
        if ($_SESSION['nn_booking_details']->payment_action == 'authorized') {
            $response = self::sendRequest($params, self::getActionEndpoint('authorize'));
        } else {
            $response = self::sendRequest($params, self::getActionEndpoint('payment'));
        }
		return $response;
	}

	/**
	 * Check billing and shpping address is same
	 *
	 * @return boolean
	 */
	public static function isBillingShippingsame() {
		global $order;
		$delivery_address = array(
			'street'   => ($order->delivery['street_address']),
			'city'     => ( $order->delivery['city']),
			'postcode' => ( $order->delivery['postcode']),
			'country'  => ($order->delivery['country']['iso_code_2']),
		);
		$billing_address = array(
			'street'   => ($order->billing['street_address']),
			'city'     => ($order->billing['city']),
			'postcode' => ($order->billing['postcode']),
			'country'  => ($order->billing['country']['iso_code_2']),
		);
		return ($delivery_address === $billing_address);
	}

	/**
	 * Get the order total amount and convert it into minimum unit amount (cents in Euro)
	 * @param $order_amount
	 *
	 * @return int
	 */
	public static function getOrderAmount($order_amount) {
		return (sprintf('%0.2f', $order_amount) * 100);
	}

	/**
	 * Get parameter for GooglePay process
	 *
	 * @return string Hidden fields with GooglePay data.
	 */
	public static function getWalletParam() {
		 global $order, $db;
		 $articleDetails = [];
		 foreach($order->products as $key => $products) {
			 if (!empty($order->info['coupon_code'])) {
				$coupon_amount = $db->Execute("select coupon_amount from ".TABLE_COUPONS." where coupon_code = '".$order->info['coupon_code']."'");
				if ($coupon_amount->RecordCount())
					$discountTax = zen_calculate_tax($coupon_amount->fields['coupon_amount'], $products['tax']);
			 }
			 if (DISPLAY_PRICE_WITH_TAX == 'true') {
				$articleDetails[] = array(			// To add product details
					  'label'=> str_replace("'", "#single_quote", $products['name']). ' x ' .$products['qty'],
					  'amount' => (string)(($products['qty'] * (round($products['final_price'] + zen_calculate_tax($products['price'], $products['tax']), 2)))*100),
					  'type' => 'SUBTOTAL',
				);
			} else {
				$articleDetails[] = array(			// To add product details
					  'label'=> str_replace("'", "#single_quote", $products['name']). ' x ' .$products['qty'],
					  'amount' => (string)(($products['qty'] * $products['final_price'])*100),
					  'type' => 'SUBTOTAL',
				);
			}
			if ($order->info['tax'] != 0) {
				if(DISPLAY_PRICE_WITH_TAX == 'true') {	// Price incl tax
					$articleDetails[] = array(
						'label'		=> 'Incl.Tax',
						'amount' 	=> (string) (round(($order->info['tax']), 2) * 100),
						'type' 		=> 'SUBTOTAL'
					);
				} else {	// Price excl tax
					$articleDetails[] = array(
						'label'		=> 'Excl.Tax',
						'amount' 	=> (string) (round(($order->info['tax'] ), 2) * 100),
						'type' 		=> 'SUBTOTAL'
					);
				}
			}
			if ($_SESSION['cot_gv'] != 0.00 || !empty($order->info['coupon_code'])) {		// To add discount coupon or gift certificate
				if (DISPLAY_PRICE_WITH_TAX == 'true'){ 		//  Discount Incl Tax
					$deduction = $coupon_amount->fields['coupon_amount']  + $_SESSION['cot_gv'];
				} else {			//  Discount Excl Tax
					$deduction = $coupon_amount->fields['coupon_amount'] + $discountTax + $_SESSION['cot_gv'];
				}
				$articleDetails[] = array(
					'label'=> 'Discount',
					'amount' => (string) (round($deduction, 2) * 100),
					'type' => 'SUBTOTAL'
				);
			}
		 }
		 $articleDetails[] = array(		// To add shipping
				 'label'=> 'Shipping',
				'amount' => (string)($order->info['shipping_cost']*100),
				'type' => 'SUBTOTAL'
		 );
		$wallet_hidden_field = "<input type='hidden' value='". json_encode($articleDetails)."' id='nn_article_details'>";
		return $wallet_hidden_field;
	}

	/**
	 * Handle redirect payments success response
	 * @param $request
	 *
	 * @return $response
	 * */
	public static function handleRedirectSuccessResponse($request) {
		$transaction_details = array('transaction' =>array('tid' => $request['tid']));
		$action = self::getActionEndpoint('transaction_details');
		$response = self::sendRequest($transaction_details, $action);
		return $response;
	}

	/**
	 * Get Novalnet transaction details from novalnet table
	 *
	 * @param array $order_no
	 * @return integer
	 */
	public static function getNovalnetTransDetails($order_no) {
		global $db;
        $txn_details = $db->Execute("SELECT * FROM novalnet_transaction_detail WHERE order_no='" . zen_db_input($order_no) . "'");
        return $txn_details;
    }

    /**
	 * Send request to server
	 *
	 *  @param array $data
	 * @param string request url
	 */
	public static function sendRequest($data, $paygate_url) {
		$headers = self::getHeadersParam();
		$json_data = json_encode($data);
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $paygate_url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $json_data);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		$result = curl_exec($curl);
		if (curl_errno($curl)) {
			echo 'Request Error:' . curl_error($curl);
			return $result;
		}
		curl_close($curl);
		$result = json_decode($result, true);
		return $result;
	}

	/**
	 * Get transaction details
	 * @param $response
	 *
	 * @return $note
	 */
	public static function getTransactionDetails($response) {
		global $currencies;
		$txn_details = '';
		if (! empty($response ['transaction']['tid'])) {
			if ($response ['transaction']['payment_type'] == 'GOOGLEPAY') {
				$txn_details .= PHP_EOL. sprintf(MODULE_PAYMENT_NOVALNET_WALLET_PAYMENT_SUCCESS_TEXT, $response['transaction']['payment_data']['last_four']);
			}
			$txn_details .= PHP_EOL. MODULE_PAYMENT_NOVALNET_TRANSACTION_ID .$response['transaction']['tid'];
			$txn_details .= ($response ['transaction']['test_mode'] == 1) ? PHP_EOL. MODULE_PAYMENT_NOVALNET_PAYMENT_MODE : '';
		}
		if ($response ['transaction']['amount'] == 0) {
			$txn_details .= PHP_EOL. MODULE_PAYMENT_NOVALNET_ZEROAMOUNT_BOOKING_MESSAGE;
		}
		// Only for Guarantee and instalment payments
		if (in_array($response['transaction']['payment_type'], array('GUARANTEED_INVOICE', 'GUARANTEED_DIRECT_DEBIT_SEPA', 'INSTALMENT_INVOICE', 'INSTALMENT_DIRECT_DEBIT_SEPA'))
			&& $response['transaction']['status'] == 'PENDING') {
			$txn_details .= PHP_EOL . MODULE_PAYMENT_NOVALNET_MENTION_GUARANTEE_PAYMENT_PENDING_TEXT.PHP_EOL;
		}
		// Only for Multibanco
		if ($response['transaction']['payment_type'] == 'MULTIBANCO') {
			$amount = $currencies->format($response['transaction']['amount']/100, true, $response['transaction']['currency']);
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
	public static function getBankDetails($response) {
		global $currencies;
		$note = '';
		$amount = $currencies->format($response['transaction']['amount']/100, true, $response['transaction']['currency']);
		if (!empty($response['instalment']['cycle_amount'])) {
			$amount = $currencies->format($response['instalment']['cycle_amount']/100, true, $response['transaction']['currency']);
		}
		$note = PHP_EOL .PHP_EOL.sprintf(MODULE_PAYMENT_NOVALNET_AMOUNT_TRANSFER_NOTE, $amount) . PHP_EOL .PHP_EOL;
		if($response['transaction']['status'] != 'ON_HOLD' && !empty($response['transaction']['due_date'])) { // If due date is not empty
			if(!empty($response['instalment']['cycle_amount'])) { // For Instalment payment
				$note  = PHP_EOL . PHP_EOL.sprintf(MODULE_PAYMENT_NOVALNET_INSTALMENT_AMOUNT_TRANSFER_NOTE_DUE_DATE, $amount, $response ['transaction']['due_date'] ) . PHP_EOL . PHP_EOL;
			} else {
				$note = PHP_EOL .PHP_EOL.sprintf(MODULE_PAYMENT_NOVALNET_AMOUNT_TRANSFER_NOTE_DUE_DATE, $amount, $response['transaction']['due_date']) . PHP_EOL .PHP_EOL;
			}

		} else if(!empty( $response['instalment']['cycle_amount'] )) { // For Instalment payment
			$note  = PHP_EOL . sprintf(MODULE_PAYMENT_NOVALNET_INSTALMENT_AMOUNT_TRANSFER_NOTE, $amount) . PHP_EOL . PHP_EOL;
		}
		$bank_details = array(
			'account_holder' => PHP_EOL.MODULE_PAYMENT_NOVALNET_ACCOUNT_HOLDER .$response['transaction']['bank_details']['account_holder'],
			'bank_name'      => MODULE_PAYMENT_NOVALNET_BANK_NAME .$response['transaction']['bank_details']['bank_name'],
			'bank_place'     => MODULE_PAYMENT_NOVALNET_BANK_PLACE .$response['transaction']['bank_details']['bank_place'] ,
			'iban'           => MODULE_PAYMENT_NOVALNET_IBAN .$response['transaction']['bank_details']['iban'] ,
			'bic'            => MODULE_PAYMENT_NOVALNET_BIC .$response['transaction']['bank_details']['bic'] ,
		);
		foreach ($bank_details  as $key => $text) {
			if (! empty($response ['transaction']['bank_details'][ $key ])) {
				$note .= sprintf($text, $response['transaction']['bank_details'][ $key ]) . PHP_EOL;
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
	public static function getNearestStoreDetails($response) {
		global $db;
		$txn_details = '';
		$length = count($response['transaction']['nearest_stores']);
		if (! empty($response['transaction']['due_date'])) {
			$txn_details .= PHP_EOL . PHP_EOL.MODULE_PAYMENT_NOVALNET_TRANS_SLIP_EXPIRY_DATE .date(DATE_FORMAT,strtotime($response['transaction']['due_date']));
		}
		$txn_details .= PHP_EOL . PHP_EOL .MODULE_PAYMENT_NOVALNET_NEAREST_STORE_DETAILS . PHP_EOL ;
		if (!empty($response['transaction']['nearest_stores'])) {
			for($i=1; $i <= $length; $i++) {
				$txn_details .= PHP_EOL . $response['transaction']['nearest_stores'][$i]['store_name'];
				$txn_details .= PHP_EOL . $response['transaction']['nearest_stores'][$i]['street'];
				$txn_details .= PHP_EOL . $response['transaction']['nearest_stores'][$i]['zip'] . ' ' . $response['transaction']['nearest_stores'][$i]['city'];
				$country_name = $db->Execute("select countries_name from " . TABLE_COUNTRIES . " where countries_iso_code_2 = '" . $response['transaction']['nearest_stores'][$i]['country_code'] . "'");
				if ($country_name->RecordCount())
				$txn_details .= PHP_EOL . $country_name->fields['countries_name'];
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
	public static function getInstalmentDetails($response) {
		global $currencies;
		$txn_details = '';
		$amount = $currencies->format($response['instalment']['cycle_amount']/100, true, $response['instalment']['currency']);
		if ($response['transaction']['status'] == 'CONFIRMED') {
			$txn_details .=  PHP_EOL.PHP_EOL.MODULE_PAYMENT_NOVALNET_INSTALMENT_INSTALMENTS_INFO.PHP_EOL.MODULE_PAYMENT_NOVALNET_INSTALMENT_PROCESSED_INSTALMENTS.$response['instalment']['cycles_executed'] . PHP_EOL;
			$txn_details .=  MODULE_PAYMENT_NOVALNET_INSTALMENT_DUE_INSTALMENTS.$response['instalment']['pending_cycles']. PHP_EOL;
			$txn_details .=  MODULE_PAYMENT_NOVALNET_INSTALMENT_NEXT_INSTALMENT_AMOUNT.$amount. PHP_EOL;
			if(!empty($response['instalment']['next_cycle_date'])) {
				$txn_details .=  MODULE_PAYMENT_NOVALNET_INSTALMENT_NEXT_INSTALMENT_DATE. date('Y-m-d', strtotime($response['instalment']['next_cycle_date'])). PHP_EOL;
			}
		}
		return $txn_details;
	 }

	/**
	 * Get shop order status id
	 *
	 * @param $transaction_status
	 * @param $payment_method
	 *
	 * @return $order_status_id
	 */
	public static function getOrderStatus($transaction_status, $payment_name) {
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
	public static function updateOrderStatus($order_id, $txn_details, $response) {
		global $order;
		$payment_status = [];
		$status_update  = [];
		$payment_status['orders_status'] = $status_update['orders_status_id'] = self::getOrderStatus($response['transaction']['status'], $response['transaction']['payment_type']);
		$status_update['comments']  = $order->info['comments'] = zen_db_prepare_input($txn_details);
		$novalnet_transaction_details = array(
			'order_no'      => $order_id,
			'tid'           => $response['transaction']['tid'],
			'amount'        => $response['transaction']['amount'],
			'currency'      => $response['transaction']['currency'],
			'payment_type'  => $response['transaction']['payment_type'],
			'status'        => $response['transaction']['status'],
		);
		if (in_array($response['transaction']['payment_type'], array('INSTALMENT_INVOICE', 'INSTALMENT_DIRECT_DEBIT_SEPA'))
			&& ($response['transaction']['status'] == 'CONFIRMED')) {
				$order_total = self::getOrderAmount($order->info['total']);
				$total_amount = ($response['transaction']['amount'] < $order_total) ? $order_total : $response['transaction']['amount'];
				$novalnet_transaction_details['instalment_cycle_details'] = self::storeInstalmentdetails($response, $total_amount);
		}
		if (in_array($response['transaction']['payment_type'], array('INVOICE', 'PREPAYMENT', 'GUARANTEED_INVOICE', 'INSTALMENT_INVOICE'))) {
			$payment_details = $response['transaction']['bank_details'];
			$payment_details['novalnet_due_date'] = $response['transaction']['due_date'];
			$novalnet_transaction_details['payment_details'] = json_encode($payment_details);
		} elseif ($response['transaction']['payment_type'] === 'CASHPAYMENT') {
			$payment_details = $response['transaction']['nearest_stores'];
			$payment_details['novalnet_due_date'] = $response['transaction']['due_date'];
			$novalnet_transaction_details['payment_details'] = json_encode($payment_details);
		} elseif (!empty($response['transaction']['payment_data']['token']) && $_SESSION['nn_booking_details']->create_token == '1') {
			$payment_data = $response['transaction']['payment_data'];
			if ($_SESSION['nn_booking_details']->payment_action == 'zero_amount') {
				$payment_data['zero_amount_booking'] = 1;
			}
			$novalnet_transaction_details['payment_details'] = json_encode($payment_data);
		} elseif (!empty($response['transaction']['payment_data']['token']) && $_SESSION['nn_booking_details']->payment_action == 'zero_amount' && $_SESSION['nn_booking_details']->create_token == '0') {
			$cardDetails = array(
					'token' => $response['transaction']['payment_data']['token'],
					'zero_amount_booking' => 1
				);
			$novalnet_transaction_details['payment_details'] = json_encode($cardDetails);
		}
		zen_db_perform('novalnet_transaction_detail', $novalnet_transaction_details, 'insert');
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
	public static function storeInstalmentdetails($response, $total_amount) {
		if(empty($response['instalment'])) {
			return false;
		}
		$instalment = $response['instalment'];
		$total_cycles = count($instalment['cycle_dates']);
		$cycle_amount = $instalment['cycle_amount'];
		$last_cycle_amount = $total_amount - ($cycle_amount * ($total_cycles - 1)) ;
		$cycles = $instalment['cycle_dates'];
		$cycle_details = array();
		foreach($cycles as $cycle => $cycle_date) {
			$cycle_details[$cycle -1 ]['date'] = $cycle_date;
			if(!empty($cycles[$cycle + 1])) {
				$cycle_details[$cycle -1 ]['next_instalment_date'] = $cycles[$cycle + 1];
			}
			$cycle_details[$cycle -1 ]['status'] = 'Pending';
			if (!empty($instalment['cycles_executed']) && $cycle == $instalment['cycles_executed']) {
				if (isset($instalment['tid']) && !empty($instalment['tid'])) {
					$cycle_details[$cycle -1 ]['reference_tid'] = (!empty($instalment['tid']))?$instalment['tid'] : '';
				} else if(isset($response['transaction']['tid']) && !empty($response['transaction']['tid'])) {
					$cycle_details[$cycle -1 ]['reference_tid'] = (!empty($response['transaction']['tid']))?$response['transaction']['tid'] : '';
				}
				$cycle_details[$cycle -1 ]['status'] = 'Paid';
				$cycle_details[$cycle -1 ]['paid_date'] = date('Y-m-d H:i:s');
			}
			$cycle_details[$cycle -1 ]['instalment_cycle_amount'] = ($cycle == $total_cycles)?$last_cycle_amount : $instalment['cycle_amount'];
		}
		return json_encode($cycle_details);
	}


	/**
	 * Send transaction update call to update order_no in Novalnet
	 * @param $order_no
	 *
	 * @return none
	 */
	public static function sendTransactionUpdate($order_no) {
		$transaction_param = [
			'transaction' => [
				'tid'       => $_SESSION['response']['transaction']['tid'],
				'order_no'  => $order_no,
			],
		];
		$params = array_merge($transaction_param, self::getCustomData());
		self::sendRequest($params, self::getActionEndpoint('transaction_update'));
		if (isset($_SESSION['response'])) {
			unset($_SESSION['response']);
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
	public static function processTempOrderFail( $response, $error_text = '') {
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
	public static function validateCheckSum($data) {
		if (!empty($data['checksum']) && !empty($data['tid']) && !empty($data['status']) && !empty($_SESSION['nn_txn_secret']) && !empty(MODULE_PAYMENT_NOVALNET_PAYMENT_ACCESS_KEY)) {
		$checksum = hash('sha256', $data['tid'] . $_SESSION['nn_txn_secret'] . $data['status'] . strrev(MODULE_PAYMENT_NOVALNET_PAYMENT_ACCESS_KEY));
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
	 * @param string
	 */
	public static function getServerResponse($response) {
		if (!empty($response['status_desc'])) {
			return $response['status_desc'];
		} elseif (!empty($response['status_text'])) {
			return $response['status_text'];
		} elseif (!empty($response['status_message'])) {
			return $response['status_message'];
		} elseif (!empty($response['result']['status_text'])) {
			return $response['result']['status_text'];
		} else {
			return MODULE_PAYMENT_NOVALNET_TRANSACTION_ERROR;
		}
	}

	/**
	 * Get payment request url
	 *
	 * @param string $action
	 * @return string
	 */
	public static function getActionEndpoint($action) {
		$endpoint = 'https://payport.novalnet.de/v2/';
		return $endpoint . str_replace('_', '/', $action);
	}

	/**
	 * Get merchant data
	 *
	 * @return $data
	 */
	public static function getMerchantData() {
		$data = [];
		$data['merchant'] = [
			'signature' => defined('MODULE_PAYMENT_NOVALNET_PUBLIC_KEY') ? MODULE_PAYMENT_NOVALNET_PUBLIC_KEY : '',
			'tariff'    => defined('MODULE_PAYMENT_NOVALNET_TARIFF_ID') ? MODULE_PAYMENT_NOVALNET_TARIFF_ID : '',
		];
		return $data;
	}

	/**
	 * Get request header
	 */
	public static function getHeadersParam() {
		$headers = [
			'Content-Type:application/json',
			'Charset:utf-8',
			'Accept:application/json',
			'X-NN-Access-Key:' . base64_encode(MODULE_PAYMENT_NOVALNET_PAYMENT_ACCESS_KEY)
		];
		return $headers;
	}

	/**
	 * Get tokenization details
	 * @param $payment_name
	 *
	 * @return $data
	 */
	public static function getToeknizationDetails(&$transaction_data) {
			if(!empty($_SESSION['nn_booking_details']->payment_ref_token) && (empty($_SESSION['nn_booking_details']->pan_hash) || empty($_SESSION['nn_booking_details']->unique_id))) { // Reference transaction
				$transaction_data['transaction']['payment_data']['token'] = $_SESSION['nn_booking_details']->payment_ref_token;
				unset($_SESSION['nn_booking_details']->payment_ref_token);
			} elseif(($_SESSION['nn_booking_details']->create_token == '1') && (!empty($_SESSION['nn_booking_details']->pan_hash) || !empty($_SESSION['nn_booking_details']->unique_id))) { // New transaction
				$transaction_data['transaction']['create_token'] = 1;
				unset($_SESSION['nn_booking_details']->create_token);
			}
	}

	/**
	 * Check for the success status of the Novalnet payment call.
	 *
	 * @param $data.
	 *
	 * @return boolean
	 */
	public static function is_success_status( $data ) {
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
	public static function insertTransactionDetails($response, $status_update = false, $order_no = '') {
		if ($response['result']['status'] == 'SUCCESS') {
			$txn_details = self::getTransactionDetails($response);
			// Invoice payments
			if((in_array($response['transaction']['payment_type'], array('INVOICE', 'PREPAYMENT')))
			|| (in_array($response['transaction']['payment_type'], array('GUARANTEED_INVOICE', 'INSTALMENT_INVOICE'))
			&& $response['transaction']['status'] != 'PENDING')) {
				$txn_details .= self::getBankDetails($response);
			}
			// Cashpayment
			if ($response['transaction']['payment_type'] == 'CASHPAYMENT') {
				$txn_details .= self::getNearestStoreDetails($response);
			}
			if (in_array($response['transaction']['payment_type'], array('INSTALMENT_INVOICE', 'INSTALMENT_DIRECT_DEBIT_SEPA')) && ($response['transaction']['status'] == 'CONFIRMED')) {
				$txn_details .= self::getInstalmentDetails($response);
			}
			if ($status_update) {
				self::updateOrderStatus($order_no, $txn_details, $response , $response['transaction']['payment_type']);
			} else {
				return $txn_details;
			}
		}
	}

	/**
	 * Validate customer email
	 *
	 * @param $emails
	 *
	 * @return boolean
	 */
	public static function validateEmail($emails) {
		$email = explode(',', $emails);
		foreach ($email as $value) {
			// Validate E-mail.
			if (!zen_validate_email($value)) {
				return false;
			}
		}
		return $value;
	}
	
	/**
	 * Paypal sheet details
	 *
	 * @return $params
	 */
	public static function paypal_sheet_details(&$params) {
		global $order, $db;				
		foreach ($order->products as $products){
			if (isset($products['attributes'])) {
				foreach ($products['attributes'] as $attr => $value) {
					$attributes[] = ', ' . $value['option'] . ':' . $value['value'];
				}
			}
			if ($products['products_weight'] != 0) {
				$product_type = 'physical';
			} else {
				$product_type = 'digital';
			}
			$productId = str_split($products['id']);
			$product_desc = $db->Execute("select products_description from " . TABLE_PRODUCTS_DESCRIPTION . " where language_id = '" . $_SESSION['languages_id'] . "' and products_id = '" . $productId[0] . "'");
			$params['cart_info']['line_items'][] = array(
						'name'        => $products['name']. ' x ' .$products['qty'] . $attributes,
						'price'       => (string) (round((float) $products['price'] * 100)),
						'quantity'    => $products['qty'],
						'description' => !empty($product_desc->products_description) ? $product_desc->products_description : '',
						'category'    => $product_type,
			);

		}
	
		if (!empty($order->info['coupon_code'])) {
		$discount_amount = (string) (round((float) $order->info['coupon_amount'] * 100));
			$params['cart_info']['line_items'][] = array(
						'name'        => 'Discount',
						'price'       => $discount_amount,
						'quantity'    => 1,
						'description' => '',
						'category'    => '',
			);
		}
		$params['cart_info']['items_tax_price'] = (string) (round((float) $order->info['tax'] * 100));
		$params['cart_info']['items_shipping_price'] = (string) (round((float) $order->info['shipping_cost'] * 100));
	}
}
?>
