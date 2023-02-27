<?php
/**
 * Novalnet payment module
 * This script is used for processing payments in Novalnet
 *
 * @author     Novalnet AG
 * @copyright  Copyright (c) Novalnet
 * @license    https://www.novalnet.de/payment-plugins/kostenlos/lizenz
 * @link       https://www.novalnet.de
 *
 * Script : novalnet_payments.php
 *
 */
require_once(DIR_FS_CATALOG . DIR_WS_MODULES.'payment/novalnet/NovalnetHelper.class.php');
class novalnet_payments extends base {
	/**
	* $code determines the internal 'code' name used to designate "this" payment module
	*
	* @var string
	*/
	public $code;

	/**
	* $title is the displayed name for this payment method
	*
	* @var string
	*/
	public $title;
	/**
	* $description is used to display instructions in the admin
	*
	* @var string
	*/
	public $description;
	/**
	* $enabled determines whether this module shows or not... in catalog.
	*
	* @var boolean
	*/
	public $enabled;
	/**
	* $sort_order is the order priority of this payment module when displayed
	* @var int
	*/
	public $sort_order;
	/**
	* $_check is used to check the configuration key set up
	* @var int
	*/
	protected $_check;

	/**
	 * Constructor
	 *
	 */
	function __construct() {
		$this->code        = 'novalnet_payments';
		$this->enabled     = true;
		$this->sort_order  = 0;
		$this->title       = defined('MODULE_PAYMENT_NOVALNET_CONFIG_TEXT_TITLE') ? MODULE_PAYMENT_NOVALNET_CONFIG_TEXT_TITLE : '';
		$this->description = defined('MODULE_PAYMENT_NOVALNET_CONFIG_TEXT_DESCRIPTION') ? MODULE_PAYMENT_NOVALNET_CONFIG_TEXT_DESCRIPTION :'';
	}

	/**
	 * Core Function : javascript_validation()
	 *
	 */
	public function javascript_validation() {
		return false;
	}

	/**
	 * Core Function : check()
	 *
	 */
	public function check() {
		global $db;
		if (!isset($this->_check)) {
			$check_query = $db->Execute("select configuration_value from ".TABLE_CONFIGURATION." where configuration_key = 'MODULE_PAYMENT_NOVALNET_ENABLE_PAYMENT_METHOD'");
			$this->_check = $check_query->RecordCount();
		}
		if ($this->_check > 0) $this->keys(); // install any missing keys
		return $this->_check;
	}

	/**
	 * Core Function : selection()
	 *
	 */
	public function selection() {
		global $db, $order;
		$theme = $db->Execute("SELECT template_dir FROM " . TABLE_TEMPLATE_SELECT . " limit 1");
		if (!empty($_SESSION['nn_payment_details']) || !empty($_SESSION['nn_booking_details'])) {
			unset($_SESSION['nn_payment_details']);
			unset($_SESSION['nn_booking_details']);
		}
		if (NovalnetHelper::checkMerchantCredentials() || strpos(MODULE_PAYMENT_INSTALLED, 'novalnet_payments') === false) {
			if (!empty($_SESSION['payment']) && $_SESSION['payment'] == $this->code) {
				unset($_SESSION['payment']);
			}
			return false;
		}
		if (defined('MODULE_PAYMENT_NOVALNET_ENABLE_PAYMENT_METHOD') && (MODULE_PAYMENT_NOVALNET_ENABLE_PAYMENT_METHOD == 'True')) {
			$merchant_data     = NovalnetHelper::getMerchantData();
			$customer_data     = NovalnetHelper::getCustomerData();
			$transaction_data  = NovalnetHelper::getTransactionData();
			$custom_data       = NovalnetHelper::getCustomData();
			$hosted_page_data  = NovalnetHelper::getHostedPageData();
			$params = array_merge($merchant_data, $customer_data, $transaction_data, $custom_data, $hosted_page_data);
			$params['transaction']['system_version'] = PROJECT_VERSION_MAJOR . '.' . PROJECT_VERSION_MINOR.'-NN13.0.0-NNT'.$theme->fields['template_dir'];
			$response = NovalnetHelper::sendRequest($params, 'https://payport.novalnet.de/v2/seamless/payment');
			if ($response['result']['status'] == 'SUCCESS') {
				$selection = [
					'id'          => $this->code,
				];
				$selection['fields'][] = ['field' => '
								<iframe  style = "width:100%;border: 0;" id = "v13PaymentForm" src = "' . $response['result']['redirect_url'] . '" allow = "payment"></iframe>
								<script type="text/javascript" src="https://cdn.novalnet.de/js/pv13/checkout.js"></script>
								<script src="' . DIR_WS_CATALOG . DIR_WS_MODULES . 'payment/novalnet/novalnet_payment_form.js" type="text/javascript"></script>' .NovalnetHelper::getWalletParam().
								'<input type="hidden" id="nn_payment_details" name="nn_payment_details" value="" />'
				];
				return $selection;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/**
	 * Core Function : pre_confirmation_check()
	 *
	 */
	function pre_confirmation_check() {
		global $order, $messageStack;
		if (!empty($_SESSION['nn_payment_details']) || !empty($_SESSION['nn_booking_details'])) {
			unset($_SESSION['nn_payment_details']);
			unset($_SESSION['nn_booking_details']);
		}
		$response = json_decode($_REQUEST['nn_payment_details']);
		if ($response->result->status == 'SUCCESS') {
			$_SESSION['nn_payment_details'] = $response->payment_details;
			$_SESSION['nn_booking_details'] = $response->booking_details;
			$this->title = $_SESSION['nn_payment_details']->name;
			$order->info['payment_method'] = $_SESSION['nn_payment_details']->name;
		} else {
			$messageStack->add_session('checkout_payment', $response->result->message . '<!-- -->', 'error');
            zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', true, false));
		}
	}

	/**
	 * Core Function : confirmation()
	 *
	 */
	function confirmation() {
		return false;

	}

	/**
	 * Core Function : before_process()
	 *
	 */
	function before_process() {
		global $order, $messageStack;
		$post = $_REQUEST;
		$this->title = $_SESSION['nn_payment_details']->name;
		$order->info['payment_method'] = $_SESSION['nn_payment_details']->name;
		if (isset($post['tid'])) { // For redirection payment transaction status
			if ($post['status'] == 'SUCCESS') { // Success
				if (NovalnetHelper::validateCheckSum($post)) { // Checksum success
					$response = NovalnetHelper::handleRedirectSuccessResponse($post);
				} else { // Checksum fail
					NovalnetHelper::processTempOrderFail($post, MODULE_PAYMENT_NOVALNET_ERROR_MSG);
				}
			} else { // Failure
				NovalnetHelper::processTempOrderFail($post);
			}
		} else {
			$merchant_data     = NovalnetHelper::getMerchantData();
			$customer_data     = NovalnetHelper::getCustomerData();
			$custom_data       = NovalnetHelper::getCustomData();
			$transaction_data  = NovalnetHelper::getTransactionData();
			$params = array_merge($merchant_data, $customer_data, $transaction_data, $custom_data);
			if ($_SESSION['nn_payment_details']->process_mode == 'redirect') { // For redirection payments
				$response = NovalnetHelper::getRedirectData($params);				
				if ($response['result']['status'] == 'SUCCESS' && !empty($response['result']['redirect_url'])) { // Success
					$_SESSION['nn_txn_secret'] = $response['transaction']['txn_secret'];
					zen_redirect($response['result']['redirect_url']);
				} else { // Failure
					NovalnetHelper::processTempOrderFail($response);
				}
			} else if ($_SESSION['nn_payment_details']->process_mode == 'direct') {
				if (in_array($_SESSION['nn_payment_details']->type, array('CREDITCARD', 'DIRECT_DEBIT_SEPA', 'GUARANTEED_DIRECT_DEBIT_SEPA','INSTALMENT_DIRECT_DEBIT_SEPA'))) {
					$params['transaction'] = array_merge(NovalnetHelper::getTransactionData()['transaction'], NovalnetHelper::getAccountDetails()['transaction']);
					if ($_SESSION['nn_booking_details']->create_token == '1' || !empty($_SESSION['nn_booking_details']->payment_ref->token))
					NovalnetHelper::getToeknizationDetails($params);
				}
				if ($_SESSION['nn_payment_details']->type == 'GOOGLEPAY' || $_SESSION['nn_payment_details']->type == 'APPLEPAY') {
					$params['transaction']['payment_data']['wallet_token'] = $_SESSION['nn_booking_details']->wallet_token;
				}
				$params['transaction']['payment_type'] = $_SESSION['nn_payment_details']->type;
				$params['transaction']['test_mode'] = $_SESSION['nn_booking_details']->test_mode;
				if (in_array($_SESSION['nn_payment_details']->type, array('INSTALMENT_DIRECT_DEBIT_SEPA','INSTALMENT_INVOICE'))) {
					$params['instalment'] = [
						'interval' => '1m',
						'cycles'    => $_SESSION['nn_booking_details']->cycle,
					];
				}
				if ($_SESSION['nn_booking_details']->payment_action == 'authorized') {
					$response = NovalnetHelper::sendRequest($params, NovalnetHelper::getActionEndpoint('authorize'));
				} else {
					if ($_SESSION['nn_booking_details']->payment_action == 'zero_amount') {
						$params['transaction']['amount'] = 0;
						$params['transaction']['create_token'] = 1;
					}
					$response = NovalnetHelper::sendRequest($params, NovalnetHelper::getActionEndpoint('payment'));
				}
			}
		}
		$_SESSION['response'] = $response;
		if ($response['result']['status'] != 'SUCCESS' ) {
			$error = (!empty($response['result']['status_text']) ? $response['result']['status_text'] : '');
			$messageStack->add_session('checkout_payment', $error . '<!-- -->', 'error');
            zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', true, false));
		}
		$order->info['comments'].= NovalnetHelper::insertTransactionDetails($_SESSION['response']);
	}

	/**
	 * Core Function : after_process()
	 *
	 */
	function after_process() {
		global $order, $insert_id;
		NovalnetHelper::updateOrderStatus($insert_id, $order->info['comments'], $_SESSION['response']);
		NovalnetHelper::sendTransactionUpdate($insert_id);
		unset($_SESSION['response']);
	    unset($_SESSION['nn_payment_details']);
	    unset($_SESSION['nn_booking_details']);
	    unset($_SESSION['nn_wallet_doredirect']);
	}

	/**
	 *  Core Function : install()
	 *
	 */
	public function install() {
        global $db;
        $this->checkAdminAccess();
        $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('".MODULE_PAYMENT_NOVALNET_PUBLIC_KEY_TITLE."', 'MODULE_PAYMENT_NOVALNET_PUBLIC_KEY', '', '".MODULE_PAYMENT_NOVALNET_PUBLIC_KEY_DESCRIPTION."', '6', '0', now())");
        $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('".MODULE_PAYMENT_NOVALNET_PAYMENT_ACCESS_KEY_TITLE."', 'MODULE_PAYMENT_NOVALNET_PAYMENT_ACCESS_KEY', '', '".MODULE_PAYMENT_NOVALNET_PAYMENT_ACCESS_KEY_DESCRIPTION."', '6', '0', now())");
        $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('".MODULE_PAYMENT_NOVALNET_TARIFF_ID_TITLE."', 'MODULE_PAYMENT_NOVALNET_TARIFF_ID', '', '".MODULE_PAYMENT_NOVALNET_TARIFF_ID_DESCRIPTION."', '6', '0', now())");
        $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order,set_function, date_added) VALUES ('".MODULE_PAYMENT_NOVALNET_ENABLE_PAYMENT_METHOD_TITLE."', 'MODULE_PAYMENT_NOVALNET_ENABLE_PAYMENT_METHOD', 'False', '".MODULE_PAYMENT_NOVALNET_ENABLE_PAYMENT_METHOD_DESCRIPTION."', '6', '0', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
        $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order,set_function, date_added) VALUES ('".MODULE_PAYMENT_NOVALNET_CALLBACK_TEST_MODE_TITLE."', 'MODULE_PAYMENT_NOVALNET_CALLBACK_TEST_MODE', 'False', '".MODULE_PAYMENT_NOVALNET_CALLBACK_TEST_MODE_DESC."', '6', '0', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
        $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('".MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_TO_TITLE."', 'MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_TO', '', '".MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_TO_DESC."', '6', '0', now())");
        $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order,set_function, use_function, date_added) VALUES ('".MODULE_PAYMENT_NOVALNET_CALLBACK_URL_TITLE."', 'MODULE_PAYMENT_NOVALNET_CALLBACK_URL', '" . ((defined('ENABLE_SSL_CATALOG') && ENABLE_SSL_CATALOG === true) ? HTTPS_SERVER : HTTP_SERVER ) .DIR_WS_CATALOG. 'extras/novalnet_callback.php' . "', '".MODULE_PAYMENT_NOVALNET_CALLBACK_URL_DESC."', '6', '0','','', now())");
	}

	/**
	 * Core Function : remove()
	 *
	 */
	public function remove() {
		global $db;
		$db->Execute("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key LIKE 'MODULE\_PAYMENT\_NOVALNET\_%'");		
	}

	/**
	 * Core Function : keys()
	 *
	 */
	public function keys() {
		echo '<input type="hidden" id="nn_language" value="'. strtoupper($_SESSION['languages_code']) .'" />;
			  <input type="hidden" id="nn_key_error" value="'. MODULE_PAYMENT_NOVALNET_CREDENTIALS_ERROR .'" />;
			  <input type="hidden" id="nn_webhook_error" value="'. MODULE_PAYMENT_NOVALNET_WEBHOOKURL_ERROR .'" />;
			  <input type="hidden" id="nn_webhook_text" value="'. MODULE_PAYMENT_NOVALNET_WEBHOOKURL_CONFIGURE_SUCCESS_TEXT .'" />;
			  <input type="hidden" id="nn_webhook_alert" value="'. MODULE_PAYMENT_NOVALNET_WEBHOOKURL_CONFIGURE_ALERT_TEXT .'" />;
			  <input type="hidden" id="merchant_credentials_error" value="'.MODULE_PAYMENT_NOVALNET_VALID_MERCHANT_CREDENTIALS_ERROR .'" />;';
		return array (
			'MODULE_PAYMENT_NOVALNET_PUBLIC_KEY',
			'MODULE_PAYMENT_NOVALNET_PAYMENT_ACCESS_KEY',
			'MODULE_PAYMENT_NOVALNET_TARIFF_ID',
			'MODULE_PAYMENT_NOVALNET_ENABLE_PAYMENT_METHOD',
			'MODULE_PAYMENT_NOVALNET_CALLBACK_TEST_MODE',
			'MODULE_PAYMENT_NOVALNET_CALLBACK_URL',
			'MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_TO',
		);
	}

	/**
     * Check Novalnet column in admin access table
     *
     */
    function checkAdminAccess() {
		global $db;
		$sql_file     = DIR_FS_CATALOG . DIR_WS_MODULES . 'payment/novalnet/sql/db_13_0_0.sql';
		$sql_lines    = file_get_contents($sql_file);
		$sql_linesArr = explode(";", $sql_lines);
		foreach ($sql_linesArr as $sql) {
			if (trim($sql) > '') {
				$db->Execute($sql);
			}
		}
		return $this->createNovalnetOrderStatus();
	}

	/**
     * Create the Novalnet pending status
     *
     * @return int
     */
    function createNovalnetOrderStatus() {
		global $db;
		$languages = $db->Execute("select * from " . TABLE_LANGUAGES . " order by sort_order");
		$query = $db->Execute("select max(orders_status_id) as status_id from " . TABLE_ORDERS_STATUS);
		if ($query->RecordCount())
			$status_id = $query->fields['status_id'];
		if ($languages->RecordCount())
			if(file_exists(DIR_FS_CATALOG. DIR_WS_LANGUAGES .$languages->fields['directory'].'/modules/payment/novalnet_payments.php')) {
				include_once(DIR_FS_CATALOG. DIR_WS_LANGUAGES .$languages->fields['directory'].'/modules/payment/novalnet_payments.php');
			}
			$novalnet_onhold_status_text = 'Payment authorized in Novalnet';
			$status = $db->Execute("SELECT orders_status_id FROM " . TABLE_ORDERS_STATUS . " WHERE orders_status_name = '" . $novalnet_onhold_status_text . "'");
			if (empty($status->RecordCount())) {
				$db->Execute("insert into " . TABLE_ORDERS_STATUS . " (orders_status_id, language_id, orders_status_name, sort_order) values ('99', '1', '" . $novalnet_onhold_status_text . "', '0')");
			}
			return true;
	}

	function admin_notification($order_id) {
		$output = '';
		require(DIR_FS_CATALOG . DIR_WS_MODULES . 'payment/novalnet/novalnet_extension.php');
		return $output;	
	}
}
