<?php
/**
* This script is used for Novalnet Global Configuration
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
* Script : novalnet_config.php
*/
include_once(DIR_FS_CATALOG . DIR_WS_CLASSES.'class.novalnetutil.php');

class novalnet_config {

    var $code, $title, $enabled, $sort_order;
    /**
     * Function : novalnet_config()
     *
     */
    function __construct() {

        $this->code = 'novalnet_config';
        $this->title = MODULE_PAYMENT_NOVALNET_CONFIG_TEXT_TITLE;
        $this->enabled = true;
        $this->sort_order = 0;
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
        return false;
    }
    /**
     * Function : pre_confirmation_check()
     *
     */
    function pre_confirmation_check() {
        return false;
    }
    /**
     * Function : check()
     *
     */
    function check() {
        global $db;
        if (!isset($this->_check)) {
            $check_query = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_NOVALNET_CONFIG_ALLOWED'");
            $this->_check = $check_query->RecordCount();
        }
        return $this->_check;
    }
    /**
     * Function : install()
     *
     */
    function install() {
        global $db, $request_type;

        include(DIR_FS_CATALOG . DIR_WS_LANGUAGES . $_SESSION['language'] . '/modules/payment/novalnet_config.php');

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('', 'MODULE_PAYMENT_NOVALNET_CONFIG_ALLOWED', '', '', '6', '0', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . MODULE_PAYMENT_NOVALNET_PUBLIC_KEY_TITLE. "', 'MODULE_PAYMENT_NOVALNET_PUBLIC_KEY', '', '" .MODULE_PAYMENT_NOVALNET_PUBLIC_KEY_DESC. "', '6', '1', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . MODULE_PAYMENT_NOVALNET_VENDOR_ID_TITLE. "', 'MODULE_PAYMENT_NOVALNET_VENDOR_ID', '', '" . MODULE_PAYMENT_NOVALNET_VENDOR_ID_DESC . "', '6', '2', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . MODULE_PAYMENT_NOVALNET_AUTH_CODE_TITLE . "', 'MODULE_PAYMENT_NOVALNET_AUTH_CODE', '', '" . MODULE_PAYMENT_NOVALNET_AUTH_CODE_DESC . "', '6', '3', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . MODULE_PAYMENT_NOVALNET_PRODUCT_ID_TITLE . "', 'MODULE_PAYMENT_NOVALNET_PRODUCT_ID', '', '" . MODULE_PAYMENT_NOVALNET_PRODUCT_ID_DESC . "', '6', '4', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . MODULE_PAYMENT_NOVALNET_TARIFF_ID_TITLE . "', 'MODULE_PAYMENT_NOVALNET_TARIFF_ID', '', '" . MODULE_PAYMENT_NOVALNET_TARIFF_ID_DESC . "', '6', '5', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . MODULE_PAYMENT_NOVALNET_PAYMENT_ACCESS_KEY_TITLE . "', 'MODULE_PAYMENT_NOVALNET_PAYMENT_ACCESS_KEY', '', '" . MODULE_PAYMENT_NOVALNET_PAYMENT_ACCESS_KEY_DESC . "', '6', '6', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('" . MODULE_PAYMENT_NOVALNET_PAYMENT_LOGO_DISPLAY_TITLE . "', 'MODULE_PAYMENT_NOVALNET_PAYMENT_LOGO_DISPLAY', 'True', '" . MODULE_PAYMENT_NOVALNET_PAYMENT_LOGO_DISPLAY_DESC. "', '6', '7', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('".MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_COMPLETE_STATUS_ID_TITLE."', 'MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_COMPLETE_STATUS_ID', '0', '".MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_COMPLETE_STATUS_ID_DESC."', '6', '8', 'zen_cfg_pull_down_order_statuses(', 'zen_get_order_status_name', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('".MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_CANCELLED_STATUS_ID_TITLE."', 'MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_CANCELLED_STATUS_ID', '0', '".MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_CANCELLED_STATUS_ID_DESC."', '6', '9', 'zen_cfg_pull_down_order_statuses(', 'zen_get_order_status_name', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('" . MODULE_PAYMENT_NOVALNET_CALLBACK_TEST_MODE_TITLE . "', 'MODULE_PAYMENT_NOVALNET_CALLBACK_TEST_MODE', 'False', '" . MODULE_PAYMENT_NOVALNET_CALLBACK_TEST_MODE_DESC . "', '6', '10', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('" . MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_SEND_TITLE . "', 'MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_SEND', 'True', '" . MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_SEND_DESC . "', '6', '11', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_TO_TITLE . "', 'MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_TO', '', '" . MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_TO_DESC . "', '6', '12', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . MODULE_PAYMENT_NOVALNET_CALLBACK_NOTIFY_URL_TITLE. "', 'MODULE_PAYMENT_NOVALNET_CALLBACK_NOTIFY_URL', '".((($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG) . 'extras/novalnet_callback.php'."', '" .MODULE_PAYMENT_NOVALNET_CALLBACK_NOTIFY_URL_DESC. "', '6', '13', now())");

        return $this->createcustom_novalnet_table();
    }

    /**
     * Function : remove()
     *
     */
     function remove() {
        global $db;
        $keys = array_merge($this->keys(), array('MODULE_PAYMENT_NOVALNET_CONFIG_ALLOWED'));
        $db->Execute("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $keys) . "')");
    }

    /**
     * Function : keys()
     *
     * @return array
     */
     function keys() {
         // Assign hidden values
		$lang = ($_SESSION['language'] == 'english') ? 'EN' : 'DE';
        echo '<input type="hidden" id="email_validation_error" value="'.MODULE_PAYMENT_NOVALNET_VALID_EMAIL_ERROR.'"><input type="hidden" id="merchant_credentials_error" value="'.MODULE_PAYMENT_NOVALNET_VALID_MERCHANT_CREDENTIALS_ERROR.'"><input type="hidden" id="novalnet_ajax_complete" value="1" /><input type= "hidden" id="nn_language" value="'.$lang.'"><script src="'.DIR_WS_CATALOG.'includes/ext/novalnet/js/novalnet_api.js" type="text/javascript"></script>';
        return array( 'MODULE_PAYMENT_NOVALNET_PUBLIC_KEY',
        'MODULE_PAYMENT_NOVALNET_VENDOR_ID',
        'MODULE_PAYMENT_NOVALNET_AUTH_CODE',
        'MODULE_PAYMENT_NOVALNET_PRODUCT_ID',
        'MODULE_PAYMENT_NOVALNET_TARIFF_ID',
        'MODULE_PAYMENT_NOVALNET_PAYMENT_ACCESS_KEY',
        'MODULE_PAYMENT_NOVALNET_PAYMENT_LOGO_DISPLAY',
        'MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_COMPLETE_STATUS_ID',
        'MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_CANCELLED_STATUS_ID',
        'MODULE_PAYMENT_NOVALNET_CALLBACK_TEST_MODE',
        'MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_SEND',
        'MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_TO',        
        'MODULE_PAYMENT_NOVALNET_CALLBACK_NOTIFY_URL'

        );
    }

    function createcustom_novalnet_table()
    {
        global $db;
        $insert_novalnet_tables = true;
        $result  = $db->Execute('select table_name from information_schema.columns where table_schema = "' . DB_DATABASE . '"');
           if ($result->table_name == 'novalnet_transaction_detail')
                $insert_novalnet_tables = false;


        if ($insert_novalnet_tables) {
            //Import Novalnet package SQL tables
            $sql_file = DIR_FS_CATALOG . 'includes/ext/novalnet/install/install.sql';
            $sql_lines = file_get_contents($sql_file);
            $sql_linesArr = explode(";", $sql_lines);
            foreach ($sql_linesArr as $sql) {
                if (trim($sql) > '') {
                    $db->Execute($sql);
                }
            }
        }
    }
}

?>
