<?php
/**
 * Novalnet payment module
 * 
 * This script is used for processing payments in Novalnet
 *
 * @author     Novalnet AG
 * @copyright  Copyright (c) Novalnet
 * @license    https://www.novalnet.de/payment-plugins/kostenlos/lizenz
 * @link       https://www.novalnet.de
 *
 * Script : novalnet_payments.php
 */

require_once(DIR_FS_CATALOG . DIR_WS_MODULES.'payment/novalnet/NovalnetHelper.php');

class novalnet_payments extends base
{
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
     *
     * @var int
     */
    public $sort_order;

    /**
     * $_check is used to check the configuration key set up
     *
     * @var int
     */
    protected $_check;

    /**
     * class constructor
     */
    function __construct()
    {
        $this->code        = 'novalnet_payments';
        $this->enabled     = (defined('MODULE_PAYMENT_NOVALNET_STATUS') && MODULE_PAYMENT_NOVALNET_STATUS == 'True');
        $this->sort_order  = 0;
        $this->title       = defined('MODULE_PAYMENT_NOVALNET_CONFIG_TEXT_TITLE') ? MODULE_PAYMENT_NOVALNET_CONFIG_TEXT_TITLE : '';
        $this->description = defined('MODULE_PAYMENT_NOVALNET_CONFIG_TEXT_DESCRIPTION') ? MODULE_PAYMENT_NOVALNET_CONFIG_TEXT_DESCRIPTION :'';
    }

    /**
     * Javascript for payment selection
     *
     * @return boolean
     */
    function javascript_validation()
    {
        return false;
    }

    /**
     * Is payment method installed
     *
     * @global queryFactory $db
     * @return booleam
     */
    function check()
    {
        global $db;
        if (!isset($this->_check)) {
            $check_query = $db->Execute("select configuration_value from ".TABLE_CONFIGURATION." where configuration_key = 'MODULE_PAYMENT_NOVALNET_STATUS'");
            $this->_check = $check_query->RecordCount();
        }
        if ($this->_check > 0) {
            $this->keys(); // install any missing keys
        }
        return $this->_check;
    }

    /**
     * Display payment on checkout page
     *
     * @return array
     */
    function selection()
    {
        global $db, $order;
        $theme = $db->Execute("SELECT template_dir FROM " . TABLE_TEMPLATE_SELECT . " limit 1");
        $theme_name = !empty($theme->fields['template_dir']) ? $theme->fields['template_dir'] : '';
        $selection = [];

        if (!empty($_SESSION['nn_payment_details']) || !empty($_SESSION['nn_booking_details'])) {
            unset($_SESSION['nn_payment_details']);
            unset($_SESSION['nn_booking_details']);
        }

        if (!NovalnetHelper::isMerchantCredentialsValid() || strpos(MODULE_PAYMENT_INSTALLED, 'novalnet_payments') === false) {
            if ($_SESSION['payment'] == $this->code && !empty($_SESSION['payment'])) {
                unset($_SESSION['payment']);
            }
            return false;
        }

        if (defined('MODULE_PAYMENT_NOVALNET_STATUS') && (MODULE_PAYMENT_NOVALNET_STATUS == 'True')) {
            $params = [];
            NovalnetHelper::buildRequestParams($params);
            NovalnetHelper::getHostedPageData($params);
            $params['transaction']['system_version'] = NovalnetHelper::getSystemVersion() . '-NNT' . $theme_name;
            $response = NovalnetHelper::sendRequest($params, NovalnetHelper::getActionEndpoint('seamless_payment'));

            if ($response['result']['status'] == 'SUCCESS' && !empty($response['result']['redirect_url'])) {
                $selection = [
                    'id'          => $this->code,
                    'module'      => ''
                ];
                $selection['fields'][] = ['field' => '
                                <iframe  style = "width:100%;border: 0;" id = "novalnet_iframe" src = "' . $response['result']['redirect_url'] . '" allow = "payment"></iframe>
                                <script type="text/javascript" src="https://cdn.novalnet.de/js/pv13/checkout.js"></script>
                                <script src="' . DIR_WS_CATALOG . DIR_WS_MODULES . 'payment/novalnet/novalnet_payment_form.js" type="text/javascript"></script>' .NovalnetHelper::getWalletParam().zen_draw_hidden_field('nn_payment_details', '', 'id="nn_payment_details"').zen_draw_hidden_field('nn_wallet_total_label', (defined('MODULE_PAYMENT_NOVALNET_WALLET_TOTAL_LABEL') ? MODULE_PAYMENT_NOVALNET_WALLET_TOTAL_LABEL : ''), 'id="nn_wallet_total_label"')

                ];
                return $selection;
            }
        }
        return $selection;
    }

    /**
     * Check payment selection submit
     *
     * @return void
     */
    function pre_confirmation_check()
    {
        global $order, $messageStack;

        if (!empty($_SESSION['nn_payment_details']) || !empty($_SESSION['nn_booking_details'])) {
            unset($_SESSION['nn_payment_details']);
            unset($_SESSION['nn_booking_details']);
        }

        $response = !empty($_REQUEST['nn_payment_details']) ? json_decode($_REQUEST['nn_payment_details']) : [];
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
     * Return confirmation payment data
     *
     * @return boolean
     */
    function confirmation()
    {
        return false;
    }

    /**
     * Return process button string
     *
     * @return boolean
     */
    function process_button()
    {
        return false;
    }

    /**
     * Process payment before the order creation
     *
     * @return void
     */
    function before_process()
    {
        global $order, $messageStack;
        
        $post_redirect_response = $_REQUEST;
        $this->title = $_SESSION['nn_payment_details']->name;
        $order->info['payment_method'] = $_SESSION['nn_payment_details']->name;
        $response = [];

        if (isset($post_redirect_response['tid'])) {
            if ($post_redirect_response['status'] == 'SUCCESS') {   // Success
                if (NovalnetHelper::validateCheckSum($post_redirect_response)) {    // Checksum success
                    $response = NovalnetHelper::handleRedirectSuccessResponse($post_redirect_response);
                } else {    // Checksum fail
                    NovalnetHelper::processTempOrderFail($post_redirect_response, MODULE_PAYMENT_NOVALNET_ERROR_MSG);
                }
            } else {    // Failure
                NovalnetHelper::processTempOrderFail($post_redirect_response);
            }
        } else {
            $params = [];
            NovalnetHelper::buildRequestParams($params);    // Get request parameters
            $payment_action = !empty($_SESSION['nn_booking_details']->payment_action) ? $_SESSION['nn_booking_details']->payment_action : '';
            $payment_action = ($payment_action == 'authorized') ? 'authorize' : 'payment';      // Captue and Authorize transaction
            $response = NovalnetHelper::sendRequest($params, NovalnetHelper::getActionEndpoint($payment_action));   // Send params to Novalnet server
            if ($response['result']['status'] == 'SUCCESS') {
                if (!empty($response['result']['redirect_url'])) {    // For redirect payments handling
                    $_SESSION['nn_txn_secret'] = $response['transaction']['txn_secret'];
                    zen_redirect($response['result']['redirect_url']);
                }
            } else {
                $_SESSION['nn_response'] = $response;
                NovalnetHelper::processTempOrderFail($response);    // Failure response handling
            }
        }
		
        $_SESSION['nn_response'] = $response;
        $order->info['comments'] .= NovalnetHelper::insertTransactionDetails($_SESSION['nn_response']);
    }

    /**
     * Process payment after order creation
     *
     * @return void
     */
    function after_process()
    {
        global $order, $insert_id;
        NovalnetHelper::updateOrderStatus($insert_id, $order->info['comments'], $_SESSION['nn_response']);
        NovalnetHelper::sendTransactionUpdate($insert_id);
        unset($_SESSION['nn_response']);
        unset($_SESSION['nn_payment_details']);
        unset($_SESSION['nn_booking_details']);
        unset($_SESSION['nn_wallet_doredirect']);
    }

    /**
     * Handle error message
     *
     * @return array
     */
    function get_error()
    {
        if ($_GET['error']) {
            return [
                'title' => $this->code,
                'error' => stripslashes(urldecode($_GET['error']))
            ];
        }
    }

    /**
     * Plugin installation routine
     *
     * @return void
     */
    function install()
    {
        global $db;

        $this->checkAdminAccess();

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Product activation key', 'MODULE_PAYMENT_NOVALNET_PUBLIC_KEY', '', 'Get your Product activation key from the <a href=https://admin.novalnet.de target=_blank style=text-decoration: underline; font-weight: bold; color:#0080c9;>Novalnet Admin Portal</a> Project > Choose your project > API credentials >API Signature (Product activation key)', '6', '0', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Payment access key', 'MODULE_PAYMENT_NOVALNET_ACCESS_KEY', '', 'Get your Payment access key from the <a href=https://admin.novalnet.de target=_blank style=text-decoration: underline; font-weight: bold; color:#0080c9;>Novalnet Admin Portal</a> Project > Choose your project > API credentials >Payment access key', '6', '0', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Select tariff ID.', 'MODULE_PAYMENT_NOVALNET_TARIFF_ID', '', 'Select a Tariff ID to match the preferred tariff plan you created at the Novalnet Admin Portal for this project', '6', '0', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order,set_function, date_added) VALUES ('Display payment method', 'MODULE_PAYMENT_NOVALNET_STATUS', 'False', 'Do you want to display payments via Novalnet?', '6', '0', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order,set_function, date_added) VALUES ('<h2>Notification / Webhook URL Setup</h2>Allow manual testing of the Notification / Webhook URL', 'MODULE_PAYMENT_NOVALNET_CALLBACK_TEST_MODE', 'False', 'Enable this to test the Novalnet Notification / Webhook URL manually. Disable this before setting your shop live to block unauthorized calls from external parties', '6', '0', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('<script src=../includes/modules/payment/novalnet/novalnet_auto_config.js type=text/javascript></script><input type=button id=webhook_url_button style=font-weight:bold;color:#0080c9 value=Configure> <br> Send e-mail to', 'MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_TO', '', 'Notification / Webhook URL execution messages will be sent to this e-mail', '6', '0', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order,set_function, use_function, date_added) VALUES ('Notification / Webhook URL', 'MODULE_PAYMENT_NOVALNET_CALLBACK_URL', '" . ((defined('ENABLE_SSL_CATALOG') && ENABLE_SSL_CATALOG === true) ? HTTPS_SERVER : HTTP_SERVER ) .DIR_WS_CATALOG. 'extras/novalnet_callback.php' . "', 'Notification / Webhook URL is required to keep the merchant’s database/system synchronized with the Novalnet account (e.g. delivery status). Refer the Installation Guide for more information', '6', '0','','', now())");
      // www.zen-cart-pro.at german admin languages_id==43 START
        $db->Execute("replace into " . TABLE_CONFIGURATION_LANGUAGE   . " (configuration_title, configuration_key, configuration_language_id, configuration_description, date_added) values ('Aktivierungsschlüssel des Produkts', 'MODULE_PAYMENT_NOVALNET_PUBLIC_KEY', '43', 'Ihren Produktaktivierungsschlüssel finden Sie im <a href=https://admin.novalnet.de target=_blank style=text-decoration:underline;font-weight:bold;color:#0080c9>Novalnet Admin-Portal</a> Projekte > Wählen Sie Ihr Projekt > API-Anmeldeinformationen > API-Signatur (Produktaktivierungsschlüssel)', now())");
        $db->Execute("replace into " . TABLE_CONFIGURATION_LANGUAGE   . " (configuration_title, configuration_key, configuration_language_id, configuration_description, date_added) values ('Zahlungs-Zugriffsschlüssel', 'MODULE_PAYMENT_NOVALNET_ACCESS_KEY', '43', 'Ihren Paymentzugriffsschlüssel finden Sie im <a href=https://admin.novalnet.de target=_blank style=text-decoration:underline;font-weight:bold;color:#0080c9>Novalnet Admin-Portal</a> Projekte > Wählen Sie Ihr Projekt > API-Anmeldeinformationen > Paymentzugriffsschlüssel', now())");
        $db->Execute("replace into " . TABLE_CONFIGURATION_LANGUAGE   . " (configuration_title, configuration_key, configuration_language_id, configuration_description, date_added) values ('Auswahl der Tarif-ID', 'MODULE_PAYMENT_NOVALNET_TARIFF_ID', '43', 'Wählen Sie eine Tarif-ID, die dem bevorzugten Tarifplan entspricht, den Sie im Novalnet Admin-Portal für dieses Projekt erstellt haben', now())");
        $db->Execute("replace into " . TABLE_CONFIGURATION_LANGUAGE   . " (configuration_title, configuration_key, configuration_language_id, configuration_description, date_added) values ('Zahlungsart anzeigen', 'MODULE_PAYMENT_NOVALNET_STATUS', '43', 'Möchten Sie Zahlungen über Novalnet anzeigen?', now())");
        $db->Execute("replace into " . TABLE_CONFIGURATION_LANGUAGE   . " (configuration_title, configuration_key, configuration_language_id, configuration_description, date_added) values ('<h2>Benachrichtigungs- / Webhook-URL festlegen</h2><br> Manuelles Testen der Benachrichtigungs / Webhook-URL erlauben', 'MODULE_PAYMENT_NOVALNET_CALLBACK_TEST_MODE', '43', 'Aktivieren Sie diese Option, um die Novalnet-Benachrichtigungs-/Webhook-URL manuell zu testen. Deaktivieren Sie die Option, bevor Sie Ihren Shop liveschalten, um unautorisierte Zugriffe von Dritten zu blockieren', now())");
        $db->Execute("replace into " . TABLE_CONFIGURATION_LANGUAGE   . " (configuration_title, configuration_key, configuration_language_id, configuration_description, date_added) values ('Benachrichtigung / Webhook-URL im Novalnet-Verwaltungsportal', 'MODULE_PAYMENT_NOVALNET_CALLBACK_URL', '43', 'Sie müssen die folgende Webhook-URL im <a href=https://admin.novalnet.de target=_blank style=text-decoration:underline;font-weight:bold;color:#0080c9>Novalnet Admin-Portal</a> hinzufügen. Dadurch können Sie Benachrichtigungen über den Transaktionsstatus erhalten', now())");
        $db->Execute("replace into " . TABLE_CONFIGURATION_LANGUAGE   . " (configuration_title, configuration_key, configuration_language_id, configuration_description, date_added) values ('<script src=../includes/modules/payment/novalnet/novalnet_auto_config.js type=text/javascript></script><input type=button id=webhook_url_button style=font-weight:bold;color:#0080c9 value=Konfigurieren> <br> E-Mails senden an', 'MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_TO', '43', 'E-Mail-Benachrichtigungen werden an diese E-Mail-Adresse gesendet', now())");
    }

    /**
     * Plugin uninstall routine
     *
     * @return void
     */
    function remove()
    {
        global $db;
        $db->Execute("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key LIKE 'MODULE\_PAYMENT\_NOVALNET\_%'");
        $db->Execute("DELETE FROM ".TABLE_ORDERS_STATUS." WHERE orders_status_name LIKE '%Novalnet%'");
    }

    /**
     * Get list of the plugin config keys
     *
     * @return array
     */
    function keys()
    {
        echo '<input type="hidden" id="nn_language" value="'. strtoupper($_SESSION['languages_code']) .'" />
			  <input type="hidden" id="nn_key_error" value="'. MODULE_PAYMENT_NOVALNET_CREDENTIALS_ERROR .'" />
			  <input type="hidden" id="nn_webhook_error" value="'. MODULE_PAYMENT_NOVALNET_WEBHOOKURL_ERROR .'" />
			  <input type="hidden" id="nn_webhook_text" value="'. MODULE_PAYMENT_NOVALNET_WEBHOOKURL_CONFIGURE_SUCCESS_TEXT .'" />
			  <input type="hidden" id="nn_webhook_alert" value="'. MODULE_PAYMENT_NOVALNET_WEBHOOKURL_CONFIGURE_ALERT_TEXT .'" />
			  <input type="hidden" id="merchant_credentials_error" value="'.MODULE_PAYMENT_NOVALNET_VALID_MERCHANT_CREDENTIALS_ERROR.'"/>';
              
        return array (
            'MODULE_PAYMENT_NOVALNET_STATUS',
            'MODULE_PAYMENT_NOVALNET_PUBLIC_KEY',
            'MODULE_PAYMENT_NOVALNET_ACCESS_KEY',
            'MODULE_PAYMENT_NOVALNET_TARIFF_ID',
            'MODULE_PAYMENT_NOVALNET_CALLBACK_TEST_MODE',
            'MODULE_PAYMENT_NOVALNET_CALLBACK_URL',
            'MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_TO',
        );
    }

    /**
     * Check Novalnet column in admin access table
     *
     * @return array
     */
    public function checkAdminAccess()
    {
        global $db;
        $sql_file = DIR_FS_CATALOG . DIR_WS_MODULES . 'payment/novalnet/sql/db_13_0_0.sql';
        $sql_lines = file_get_contents($sql_file);
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
     * @return boolean
     */
    public function createNovalnetOrderStatus()
    {
        global $db;
        $languages = zen_get_languages();

        foreach ($languages as $key => $value) {
            if (file_exists(DIR_FS_CATALOG. DIR_WS_LANGUAGES .$value['directory'].'/modules/payment/novalnet_payments.php')) {
                include_once(DIR_FS_CATALOG. DIR_WS_LANGUAGES .$value['directory'].'/modules/payment/novalnet_payments.php');
            }

            $canceled_status = $db->Execute("select orders_status_name from ". TABLE_ORDERS_STATUS ." WHERE orders_status_name LIKE '%cancel%'");

            if ($value['code'] == 'en' || $value['code'] == 'de') {
                $lang_code = strtoupper($value['code']);
                $nn_cancelled_status = $db->Execute("SELECT * FROM " . TABLE_ORDERS_STATUS . " WHERE language_id = '".$value['id']."' AND orders_status_name = '". constant("MODULE_PAYMENT_NOVALNET_CANCELED_" . $lang_code . "_ORDER_STATUS") ."'");
                $nn_onhold_status = $db->Execute("SELECT * FROM " . TABLE_ORDERS_STATUS . " WHERE language_id = '".$value['id']."' AND orders_status_name = '". constant("MODULE_PAYMENT_NOVALNET_ONHOLD_" . $lang_code . "_ORDER_STATUS") ."'");

                if (empty($canceled_status->RecordCount()) && $nn_cancelled_status->RecordCount()) {        // For cancelled status
                    $db->Execute("insert into " . TABLE_ORDERS_STATUS . " (orders_status_id, language_id, orders_status_name, sort_order) values ('98', '".$value['id']."', '" . constant("MODULE_PAYMENT_NOVALNET_CANCELED_" . $lang_code . "_ORDER_STATUS") . "', '0')");
                }

                if (empty($nn_onhold_status->RecordCount())) {
                    // For authorised status
                    $db->Execute("insert into " . TABLE_ORDERS_STATUS . " (orders_status_id, language_id, orders_status_name, sort_order) values ('99', '".$value['id']."', '" . constant("MODULE_PAYMENT_NOVALNET_ONHOLD_" . $lang_code . "_ORDER_STATUS") . "', '0')");
                }
            }
        }
        return true;
    }

    /**
    * Build admin-page components
    *
    * @param int $zf_order_id
    * @return string
    */
    function admin_notification($zf_order_id)
    {
        global $db, $currencies,$order;
        $output = '';
		
        if (defined('MODULE_PAYMENT_NOVALNET_STATUS') && MODULE_PAYMENT_NOVALNET_STATUS == 'True') {
            $transaction_details = NovalnetHelper::getNovalnetTransDetails($zf_order_id);

            if ($transaction_details->RecordCount()) {
                $payment_details = !empty($transaction_details->fields['payment_details']) ? json_decode($transaction_details->fields['payment_details'], true) : [];
                if (($transaction_details->fields['amount'] == 0 &&
                    isset($payment_details['zero_amount_booking'])) ||
                    (!empty($transaction_details->fields['instalment_cycle_details']))
                ) {
                    require(DIR_FS_CATALOG . DIR_WS_MODULES . 'payment/novalnet/novalnet_extension.php');
                } else {
                    require(DIR_FS_CATALOG . DIR_WS_MODULES . 'payment/novalnet/novalnet_admin_notification.php');
                }

                return $output;
            } else {
                return '{}';
            }
        }
    }

    function _doRefund($oID)
    {
        global $messageStack, $db, $currencies, $order;
        $data = [];
        $request = $_REQUEST;
        $txn_details = NovalnetHelper::getNovalnetTransDetails($oID);
        $current_order_status = $db->Execute("SELECT orders_status from " . TABLE_ORDERS . " where orders_id = " . zen_db_input($oID));

        if ($txn_details->RecordCount()) {
            if (!empty($request['nn_refund_confirm']) && ($request['refund_trans_amount'] != '' ) && $txn_details->fields['status'] != 'Canceled') {
                $refunded_amount = 0;
                $data = [
                    'transaction' => [
                        'tid' => (!empty($request['refund_tid'])) ? $request['refund_tid'] : $txn_details->fields['tid'],
                        'amount' => $request['refund_trans_amount'],
                    ],
                    'custom' => [
                        'lang' => (isset($_SESSION['languages_code'])) ? strtoupper($_SESSION['languages_code']) : 'DE',
                        'shop_invoked' => 1
                    ]
                ];

                if (!empty($request['refund_reason'])) {
                    $data['transaction']['reason'] = $request['refund_reason'];
                }

                $response = NovalnetHelper::sendRequest($data, NovalnetHelper::getActionEndpoint('transaction_refund'));

                if ($response['result']['status'] == 'SUCCESS') {
                    $refunded_amount = $response['transaction']['refund']['amount'];
                    if (!empty($txn_details->fields['instalment_cycle_details'])) {
                        $instalment_details = json_decode($txn_details->fields['instalment_cycle_details'], true);

                        if (!empty($instalment_details)) {
                            $cycle = $request['instalment_cycle'];
                            $instalment_amount = (strpos((string) $instalment_details[$cycle]['instalment_cycle_amount'], '.')) ? $instalment_details[$cycle]['instalment_cycle_amount'] * 100 : $instalment_details[$cycle]['instalment_cycle_amount'];
                            $instalment_amount = ($instalment_amount - $refunded_amount);
                            $instalment_details[$cycle]['instalment_cycle_amount'] = $instalment_amount;

                            if ($instalment_details[$cycle]['instalment_cycle_amount'] <= 0) {
                                $instalment_details[$cycle]['status'] = 'Refunded';
                            }

                            $update_data = [
                                'instalment_cycle_details' => !empty($instalment_details) ? json_encode($instalment_details) : '{}',
                            ];
                        }
                    }

                    $update_data['refund_amount'] = (!empty($txn_details->fields['refund_amount'])) ? ($refunded_amount + $txn_details->fields['refund_amount']) : $refunded_amount;
                    $message = PHP_EOL. sprintf((MODULE_PAYMENT_NOVALNET_REFUND_PARENT_TID_MSG), $txn_details->fields['tid'], $currencies->format(($refunded_amount/100), 1, $order->info['currency']));
                    // Check for refund TID
                    if (!empty($response['transaction']['refund']['tid'])) {
                        $message .= PHP_EOL. sprintf((MODULE_PAYMENT_NOVALNET_REFUND_CHILD_TID_MSG), $response['transaction']['refund']['tid']);
                    }

                    if (!empty($oID)) {
                        zen_db_perform(TABLE_NOVALNET_TRANSACTION_DETAIL, $update_data, 'update', 'order_no='.$oID);
                    }

                    $order_status_value = ($update_data['refund_amount'] >= $txn_details->fields['amount']) ? NovalnetHelper::getOrderStatusId() : $current_order_status->fields['orders_status'];

                    NovalnetHelper::novalnetUpdateOrderStatus($oID, $message, $order_status_value);
                    $messageStack->add_session($response['result']['status_text'], 'success');
                } else {
                    $messageStack->add_session($response['result']['status_text'], 'error');
                }

                zen_redirect(zen_href_link(FILENAME_ORDERS, zen_get_all_get_params(['action']) . 'action=edit' . '&oID=' . (int)$oID));
            }
        }
    }

    function _doVoid($oID)
    {
        global $messageStack;
        $comments = '';
        $update_data = [];
        $txn_details = NovalnetHelper::getNovalnetTransDetails($oID);

        if ($txn_details->RecordCount()) {
            $data = [
                'transaction' => [
                    'tid' => $txn_details->fields['tid']
                ],
                'custom' => [
                    'lang' => (isset($_SESSION['languages_code'])) ? strtoupper($_SESSION['languages_code']) : 'DE',
                    'shop_invoked' => 1
                ]
            ];

            $response = NovalnetHelper::sendRequest($data, NovalnetHelper::getActionEndpoint('transaction_cancel'));
            if ($response['result']['status'] == 'SUCCESS') {
				$update_data = [
                    'status' => $response['transaction']['status']
                ];
                $comments .= PHP_EOL.sprintf(MODULE_PAYMENT_NOVALNET_TRANS_DEACTIVATED_MESSAGE, date('d.m.Y', strtotime(date('d.m.Y'))), date('H:i:s'));
                NovalnetHelper::novalnetUpdateOrderStatus($oID, $comments, NovalnetHelper::getOrderStatusId());
                $messageStack->add_session($response['result']['status_text'], 'success');
            } else {
                $messageStack->add_session($response['result']['status_text'], 'error');
            }

            if (!empty($oID) && isset($response['transaction']['status'])) {
                zen_db_perform(TABLE_NOVALNET_TRANSACTION_DETAIL, $update_data, 'update', 'order_no='.$oID);
            }

            zen_redirect(zen_href_link(FILENAME_ORDERS, zen_get_all_get_params(['action']) . 'action=edit' . '&oID=' . (int)$oID));
        }
    }

    function _doCapt($oID)
    {
        global $messageStack;
        $request = $_REQUEST;
        $order_status = $comments = '';
        $update_data = $data = [];
        $txn_details    = NovalnetHelper::getNovalnetTransDetails($oID);

        if ($txn_details->RecordCount()) {
            $data = [
                'transaction' => [
                    'tid' => $txn_details->fields['tid']
                ],
                'custom' => [
                    'lang' => (isset($_SESSION['languages_code'])) ? strtoupper($_SESSION['languages_code']) : 'DE',
                    'shop_invoked' => 1
                ]
            ];
            $response = NovalnetHelper::sendRequest($data, NovalnetHelper::getActionEndpoint('transaction_capture'));
            if ($response['result']['status'] == 'SUCCESS') {
				$payment_type = $response['transaction']['payment_type'];
				$update_data = [
                    'status' => $response['transaction']['status'],
                ];
                $order_status = NovalnetHelper::getOrderStatus($update_data['status'], $payment_type);
                $comments .= PHP_EOL . sprintf(MODULE_PAYMENT_NOVALNET_TRANS_CONFIRM_SUCCESSFUL_MESSAGE_TEXT, date('d.m.Y', strtotime(date('d.m.Y'))), date('H:i:s')) . PHP_EOL;
                $comments .= NovalnetHelper::getTransactionDetails($response);

                if (in_array($payment_type, array('INSTALMENT_INVOICE','GUARANTEED_INVOICE', 'INVOICE', 'PREPAYMENT'))) {
                    if (empty($response['transaction']['bank_details'])) {
						$payment_details = !empty($txn_details->fields['payment_details']) ? json_decode($txn_details->fields['payment_details'], true) : [];
                        $response['transaction']['bank_details'] = $payment_details;
                    }
                    $comments .= NovalnetHelper::getBankDetails($response);
                }
                if (!empty($response['instalment'])) {
                    $comments .= NovalnetHelper::getInstalmentDetails($response);
                    if (in_array($response['transaction']['status'], array('CONFIRMED', 'PENDING'))) {
                        $total_amount = ($txn_details->fields['amount'] < $response['transaction']['amount']) ? $response['transaction']['amount'] : $txn_details->fields['amount'];
                        $instalment_details = NovalnetHelper::storeInstalmentdetails($response, $total_amount);
                        $update_data['instalment_cycle_details'] = $instalment_details;
                    }
                }

                if (in_array($payment_type, array('INSTALMENT_INVOICE','GUARANTEED_INVOICE'))) {
                    NovalnetHelper::sendPaymentConfirmationMail($comments, $oID);
                }

                NovalnetHelper::novalnetUpdateOrderStatus($oID, $comments, $order_status);
                $messageStack->add_session($response['result']['status_text'], 'success');
            } else {
                $messageStack->add_session($response['result']['status_text'], 'error');
            }

            if (!empty($oID) &&  !empty($update_data)) {
                zen_db_perform(TABLE_NOVALNET_TRANSACTION_DETAIL, $update_data, 'update', 'order_no='.$oID);
            }

            zen_redirect(zen_href_link(FILENAME_ORDERS, zen_get_all_get_params(['action']) . 'action=edit' . '&oID=' . (int)$oID));
        }
    }
}
