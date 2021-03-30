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
* Script : novalnet_instalment_sepa.php
*/
include_once(dirname(__FILE__).'/novalnet.php');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_SEPA_TEXT_TITLE', 'Instalment by Direct Debit SEPA');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_SEPA_TEXT_DESCRIPTION', '<br>The amount will be debited from your account by Novalnet<br/>');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_SEPA_PUBLIC_TITLE', 'Instalment by Direct Debit SEPA ');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_SEPA_LOGO', (defined('MODULE_PAYMENT_NOVALNET_PAYMENT_LOGO_DISPLAY') && MODULE_PAYMENT_NOVALNET_PAYMENT_LOGO_DISPLAY == 'True') ? zen_image(DIR_WS_IMAGES . 'icons/novalnet/novalnet_sepa.png', 'Instalment by Direct Debit SEPA') : '');

define('MODULE_PAYMENT_NOVALNET_INSTALMENT_SEPA_FORM_MANDATE_CONFIRM_TEXT', '<strong><a id="instalment_sepa_mandate_toggle"  style="color:#0080c9;" onclick="return showmandate(\'instalment_sepa_mandate_details\');">I hereby grant the mandate for the SEPA direct debit (electronic transmission) and confirm that the given bank details are correct!</a></strong><div id="instalment_sepa_mandate_details" style="display:none"><p>I authorise (A) Novalnet AG to send instructions to my bank to debit my account and (B) my bank to debit my account in accordance with the instructions from Novalnet AG.</p> <p><strong>Creditor identifier: DE53ZZZ00000004253</strong></p> <p><strong>Note:</strong> You are entitled to a refund from your bank under the terms and conditions of your agreement with bank. A refund must be claimed within 8 weeks starting from the date on which your account was debited.</p></div>');

define('MODULE_PAYMENT_NOVALNET_INSTALMENT_SEPA_STATUS_TITLE', MODULE_PAYMENT_INSTALMENT_REQUIREMENT.MODULE_PAYMENT_NOVALNET_STATUS_TITLE);
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_SEPA_STATUS_DESC', MODULE_PAYMENT_NOVALNET_STATUS_DESC);

define('MODULE_PAYMENT_NOVALNET_INSTALMENT_SEPA_TEST_MODE_TITLE', MODULE_PAYMENT_NOVALNET_TEST_MODE_TITLE);
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_SEPA_TEST_MODE_DESC', MODULE_PAYMENT_NOVALNET_TEST_MODE_DESC);

define('MODULE_PAYMENT_NOVALNET_INSTALMENT_SEPA_ONHOLD_TITLE', MODULE_PAYMENT_NOVALNET_PAYMENT_ONHOLD_TITLE);
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_SEPA_ONHOLD_DESC', MODULE_PAYMENT_NOVALNET_PAYMENT_ONHOLD_DESC);

define('MODULE_PAYMENT_NOVALNET_INSTALMENT_SEPA_ONHOLD_LIMIT_TITLE', MODULE_PAYMENT_NOVALNET_PAYMENT_ONHOLD_LIMIT_TITLE);
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_SEPA_ONHOLD_LIMIT_DESC', MODULE_PAYMENT_NOVALNET_PAYMENT_ONHOLD_LIMIT_DESC);

define('MODULE_PAYMENT_NOVALNET_INSTALMENT_SEPA_PERIOD_TITLE', 'Recurring period');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_SEPA_PERIOD_DESC', 'Choose the recurring period for the instalment payment.');

define('MODULE_PAYMENT_NOVALNET_INSTALMENT_SEPA_CYCLE_TITLE', 'Instalment cycles');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_SEPA_CYCLE_DESC', 'Select the available instalment cycles');

define('MODULE_PAYMENT_NOVALNET_INSTALMENT_SEPA_MIN_AMOUNT_LIMIT_TITLE', 'Minimum order amount (in minimum unit of currency. E.g. enter 100 which is equal to 1.00)');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_SEPA_MIN_AMOUNT_LIMIT_DESC', 'This setting will override the default setting made in the minimum order amount. Note Minimum amount should be greater than or equal to 19,98 EUR.');

define('MODULE_PAYMENT_NOVALNET_INSTALMENT_SEPA_CUSTOMER_INFO_TITLE', MODULE_PAYMENT_NOVALNET_CUSTOMER_INFO_TITLE);
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_SEPA_CUSTOMER_INFO_DESC', MODULE_PAYMENT_NOVALNET_CUSTOMER_INFO_DESC);

define('MODULE_PAYMENT_NOVALNET_INSTALMENT_SEPA_SORT_ORDER_TITLE', MODULE_PAYMENT_NOVALNET_SORT_ORDER_TITLE);
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_SEPA_SORT_ORDER_DESC', MODULE_PAYMENT_NOVALNET_SORT_ORDER_DESC);

define('MODULE_PAYMENT_NOVALNET_INSTALMENT_SEPA_PENDING_ORDER_STATUS_ID_TITLE', 'Pending payment order status');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_SEPA_PENDING_ORDER_STATUS_ID_DESC', 'Status to be used for pending transactions.');

define('MODULE_PAYMENT_NOVALNET_INSTALMENT_SEPA_ORDER_STATUS_ID_TITLE', MODULE_PAYMENT_NOVALNET_ORDER_STATUS_TITLE);
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_SEPA_ORDER_STATUS_ID_DESC', MODULE_PAYMENT_NOVALNET_ORDER_STATUS_DESC);

define('MODULE_PAYMENT_NOVALNET_INSTALMENT_SEPA_ZONE_TITLE', MODULE_PAYMENT_NOVALNET_PAYMENT_ZONE_TITLE);
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_SEPA_ZONE_DESC', MODULE_PAYMENT_NOVALNET_PAYMENT_ZONE_DESC)

?>
