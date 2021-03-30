<?php
/**
* This script is used for English language content
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
include_once(dirname(__FILE__).'/novalnet.php');
define('MODULE_PAYMENT_NOVALNET_CC_TEXT_TITLE', 'Credit/Debit Cards');
define('MODULE_PAYMENT_NOVALNET_CC_PUBLIC_TITLE', 'Credit/Debit Cards ');
define('MODULE_PAYMENT_NOVALNET_CC_REDIRECTION_TEXT_DESCRIPTION', '<br>The amount will be debited from your credit/debit card<br />');
define('MODULE_PAYMENT_NOVALNET_VALID_CC_DETAILS', 'Your credit card details are invalid');

define('MODULE_PAYMENT_NOVALNET_CC_LOGO', ((defined('MODULE_PAYMENT_NOVALNET_PAYMENT_LOGO_DISPLAY') && MODULE_PAYMENT_NOVALNET_PAYMENT_LOGO_DISPLAY == 'True') ? zen_image(DIR_WS_IMAGES . 'icons/novalnet/novalnet_cc_visa.png', 'Credit/Debit Cards') . zen_image(DIR_WS_IMAGES . 'icons/novalnet/novalnet_cc_master.png', 'Credit/Debit Cards') : '') . ((!defined('MODULE_PAYMENT_NOVALNET_CC_AMEX_LOGO') || MODULE_PAYMENT_NOVALNET_CC_AMEX_LOGO == 'True' && (defined('MODULE_PAYMENT_NOVALNET_PAYMENT_LOGO_DISPLAY') && MODULE_PAYMENT_NOVALNET_PAYMENT_LOGO_DISPLAY == 'True')) ? zen_image(DIR_WS_IMAGES . 'icons/novalnet/novalnet_cc_amex.png', 'Credit/Debit Cards') : '') . ((!defined('MODULE_PAYMENT_NOVALNET_CC_MAESTRO_LOGO') || MODULE_PAYMENT_NOVALNET_CC_MAESTRO_LOGO == 'True' && (defined('MODULE_PAYMENT_NOVALNET_PAYMENT_LOGO_DISPLAY') && MODULE_PAYMENT_NOVALNET_PAYMENT_LOGO_DISPLAY == 'True')) ? zen_image(DIR_WS_IMAGES . 'icons/novalnet/novalnet_cc_maestro.png', 'Credit/Debit Cards') : ''));


define('MODULE_PAYMENT_NOVALNET_CC_STATUS_TITLE',MODULE_PAYMENT_NOVALNET_STATUS_TITLE);
define('MODULE_PAYMENT_NOVALNET_CC_STATUS_DESC',MODULE_PAYMENT_NOVALNET_STATUS_DESC);

define('MODULE_PAYMENT_NOVALNET_CC_TEST_MODE_TITLE', MODULE_PAYMENT_NOVALNET_TEST_MODE_TITLE);
define('MODULE_PAYMENT_NOVALNET_CC_TEST_MODE_DESC', MODULE_PAYMENT_NOVALNET_TEST_MODE_DESC);

define('MODULE_PAYMENT_NOVALNET_CC_ONHOLD_TITLE', MODULE_PAYMENT_NOVALNET_PAYMENT_ONHOLD_TITLE);
define('MODULE_PAYMENT_NOVALNET_CC_ONHOLD_DESC', MODULE_PAYMENT_NOVALNET_PAYMENT_ONHOLD_DESC);

define('MODULE_PAYMENT_NOVALNET_CC_ONHOLD_LIMIT_TITLE', MODULE_PAYMENT_NOVALNET_PAYMENT_ONHOLD_LIMIT_TITLE);
define('MODULE_PAYMENT_NOVALNET_CC_ONHOLD_LIMIT_DESC', MODULE_PAYMENT_NOVALNET_PAYMENT_ONHOLD_LIMIT_DESC);

define('MODULE_PAYMENT_NOVALNET_CC_AMEX_LOGO_TITLE', 'Display AMEX logo');
define('MODULE_PAYMENT_NOVALNET_CC_AMEX_LOGO_DESC','Display AMEX logo at the checkout page');

define('MODULE_PAYMENT_NOVALNET_CC_MAESTRO_LOGO_TITLE', 'Display Maestro logo');
define('MODULE_PAYMENT_NOVALNET_CC_MAESTRO_LOGO_DESC','Display Maestro logo at the checkout page');

define('MODULE_PAYMENT_NOVALNET_CC_CUSTOMER_INFO_TITLE', MODULE_PAYMENT_NOVALNET_CUSTOMER_INFO_TITLE);
define('MODULE_PAYMENT_NOVALNET_CC_CUSTOMER_INFO_DESC', MODULE_PAYMENT_NOVALNET_CUSTOMER_INFO_DESC);

define('MODULE_PAYMENT_NOVALNET_CC_FORM_LABEL_STYLE_TITLE', '<h5><b>CSS settings for Credit Card iframe</b></h5>Label');
define('MODULE_PAYMENT_NOVALNET_CC_FORM_LABEL_STYLE_DESC','');

define('MODULE_PAYMENT_NOVALNET_CC_FORM_INPUT_STYLE_TITLE', 'Input');
define('MODULE_PAYMENT_NOVALNET_CC_FORM_INPUT_STYLE_DESC','');

define('MODULE_PAYMENT_NOVALNET_CC_FORM_CSS_STYLE_TITLE', 'CSS Text');
define('MODULE_PAYMENT_NOVALNET_CC_FORM_CSS_STYLE_DESC','');

define('MODULE_PAYMENT_NOVALNET_CC_SORT_ORDER_TITLE', MODULE_PAYMENT_NOVALNET_SORT_ORDER_TITLE);
define('MODULE_PAYMENT_NOVALNET_CC_SORT_ORDER_DESC', MODULE_PAYMENT_NOVALNET_SORT_ORDER_DESC);

define('MODULE_PAYMENT_NOVALNET_CC_ORDER_STATUS_ID_TITLE', MODULE_PAYMENT_NOVALNET_ORDER_STATUS_TITLE);
define('MODULE_PAYMENT_NOVALNET_CC_ORDER_STATUS_ID_DESC', MODULE_PAYMENT_NOVALNET_ORDER_STATUS_DESC);

define('MODULE_PAYMENT_NOVALNET_CC_ZONE_TITLE', MODULE_PAYMENT_NOVALNET_PAYMENT_ZONE_TITLE);
define('MODULE_PAYMENT_NOVALNET_CC_ZONE_DESC', MODULE_PAYMENT_NOVALNET_PAYMENT_ZONE_DESC);

?>
