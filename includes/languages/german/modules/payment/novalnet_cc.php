<?php
/**
* This script is used for German language content
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
define('MODULE_PAYMENT_NOVALNET_CC_TEXT_TITLE', 'Kredit- / Debitkarte');
define('MODULE_PAYMENT_NOVALNET_CC_PUBLIC_TITLE', 'Kredit- / Debitkarte ');
define('MODULE_PAYMENT_NOVALNET_CC_REDIRECTION_TEXT_DESCRIPTION', '<br>Der Betrag wird Ihrer Kredit-/Debitkarte belastet<br />');
define('MODULE_PAYMENT_NOVALNET_VALID_CC_DETAILS', 'Ihre Kreditkartendaten sind ungültig.');

define('MODULE_PAYMENT_NOVALNET_CC_LOGO', ((defined('MODULE_PAYMENT_NOVALNET_PAYMENT_LOGO_DISPLAY') && MODULE_PAYMENT_NOVALNET_PAYMENT_LOGO_DISPLAY == 'True') ? zen_image(DIR_WS_IMAGES . 'icons/novalnet/novalnet_cc_visa.png', 'Kredit- / Debitkarte') . zen_image(DIR_WS_IMAGES . 'icons/novalnet/novalnet_cc_master.png', 'Kredit- / Debitkarte') : '') . ((!defined('MODULE_PAYMENT_NOVALNET_CC_AMEX_LOGO') || MODULE_PAYMENT_NOVALNET_CC_AMEX_LOGO == 'True' && (defined('MODULE_PAYMENT_NOVALNET_PAYMENT_LOGO_DISPLAY') && MODULE_PAYMENT_NOVALNET_PAYMENT_LOGO_DISPLAY == 'True')) ? zen_image(DIR_WS_IMAGES . 'icons/novalnet/novalnet_cc_amex.png', 'Kredit- / Debitkarte') : '') . ((!defined('MODULE_PAYMENT_NOVALNET_CC_MAESTRO_LOGO') || MODULE_PAYMENT_NOVALNET_CC_MAESTRO_LOGO == 'True' && (defined('MODULE_PAYMENT_NOVALNET_PAYMENT_LOGO_DISPLAY') && MODULE_PAYMENT_NOVALNET_PAYMENT_LOGO_DISPLAY == 'True')) ? zen_image(DIR_WS_IMAGES . 'icons/novalnet/novalnet_cc_maestro.png', 'Kredit- / Debitkarte') : ''));

define('MODULE_PAYMENT_NOVALNET_CC_STATUS_TITLE',MODULE_PAYMENT_NOVALNET_STATUS_TITLE);
define('MODULE_PAYMENT_NOVALNET_CC_STATUS_DESC',MODULE_PAYMENT_NOVALNET_STATUS_DESC);

define('MODULE_PAYMENT_NOVALNET_CC_TEST_MODE_TITLE', MODULE_PAYMENT_NOVALNET_TEST_MODE_TITLE);
define('MODULE_PAYMENT_NOVALNET_CC_TEST_MODE_DESC', MODULE_PAYMENT_NOVALNET_TEST_MODE_DESC);

define('MODULE_PAYMENT_NOVALNET_CC_ONHOLD_TITLE', MODULE_PAYMENT_NOVALNET_PAYMENT_ONHOLD_TITLE);
define('MODULE_PAYMENT_NOVALNET_CC_ONHOLD_DESC', MODULE_PAYMENT_NOVALNET_PAYMENT_ONHOLD_DESC);

define('MODULE_PAYMENT_NOVALNET_CC_ONHOLD_LIMIT_TITLE', MODULE_PAYMENT_NOVALNET_PAYMENT_ONHOLD_LIMIT_TITLE);
define('MODULE_PAYMENT_NOVALNET_CC_ONHOLD_LIMIT_DESC', MODULE_PAYMENT_NOVALNET_PAYMENT_ONHOLD_LIMIT_DESC);

define('MODULE_PAYMENT_NOVALNET_CC_AMEX_LOGO_TITLE', 'AMEX-Logo anzeigen');
define('MODULE_PAYMENT_NOVALNET_CC_AMEX_LOGO_DESC','AMEX-Logo auf der Checkout-Seite anzeigen');

define('MODULE_PAYMENT_NOVALNET_CC_MAESTRO_LOGO_TITLE', 'Maestro-Logo anzeigen');
define('MODULE_PAYMENT_NOVALNET_CC_MAESTRO_LOGO_DESC','Maestro-Logo auf der Checkout-Seite anzeigen');

define('MODULE_PAYMENT_NOVALNET_CC_CUSTOMER_INFO_TITLE', MODULE_PAYMENT_NOVALNET_CUSTOMER_INFO_TITLE);
define('MODULE_PAYMENT_NOVALNET_CC_CUSTOMER_INFO_DESC', MODULE_PAYMENT_NOVALNET_CUSTOMER_INFO_DESC);

define('MODULE_PAYMENT_NOVALNET_CC_FORM_LABEL_STYLE_TITLE', '<h5><b>CSS-Einstellungen für den iFrame mit Kreditkartendaten</b></h5>Beschriftung');
define('MODULE_PAYMENT_NOVALNET_CC_FORM_LABEL_STYLE_DESC','');

define('MODULE_PAYMENT_NOVALNET_CC_FORM_INPUT_STYLE_TITLE', 'Eingabe');
define('MODULE_PAYMENT_NOVALNET_CC_FORM_INPUT_STYLE_DESC','');

define('MODULE_PAYMENT_NOVALNET_CC_FORM_CSS_STYLE_TITLE', 'Text für das CSS');
define('MODULE_PAYMENT_NOVALNET_CC_FORM_CSS_STYLE_DESC','');

define('MODULE_PAYMENT_NOVALNET_CC_SORT_ORDER_TITLE', MODULE_PAYMENT_NOVALNET_SORT_ORDER_TITLE);
define('MODULE_PAYMENT_NOVALNET_CC_SORT_ORDER_DESC', MODULE_PAYMENT_NOVALNET_SORT_ORDER_DESC);

define('MODULE_PAYMENT_NOVALNET_CC_ORDER_STATUS_ID_TITLE', MODULE_PAYMENT_NOVALNET_ORDER_STATUS_TITLE);
define('MODULE_PAYMENT_NOVALNET_CC_ORDER_STATUS_ID_DESC', MODULE_PAYMENT_NOVALNET_ORDER_STATUS_DESC);

define('MODULE_PAYMENT_NOVALNET_CC_ZONE_TITLE', MODULE_PAYMENT_NOVALNET_PAYMENT_ZONE_TITLE);
define('MODULE_PAYMENT_NOVALNET_CC_ZONE_DESC', MODULE_PAYMENT_NOVALNET_PAYMENT_ZONE_DESC);

 
?>
