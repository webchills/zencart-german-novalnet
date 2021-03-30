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
* Script : novalnet_config.php
*/
define('MODULE_PAYMENT_NOVALNET_CONFIG_TEXT_TITLE', 'Novalnet Global Configuration');
define('MODULE_PAYMENT_NOVALNET_PUBLIC_KEY_TITLE', 'Product activation key');  
define('MODULE_PAYMENT_NOVALNET_PUBLIC_KEY_DESC', 'Product Activation Key connects the Zencart shop system to your Novalnet merchant account. Use Novalnet’s Product Activation Key to populate your merchant credentials in the Global Configuration page.<br> Get your Product activation key from the <a href="https://admin.novalnet.de" target="_blank" style="text-decoration: underline; font-weight: bold; color:#0080c9;">Novalnet Admin Portal</a>: <b>PROJECT > Choose your project > Shop Parameters > API Signature (Product activation key)</b>');  

define('MODULE_PAYMENT_NOVALNET_VENDOR_ID_TITLE', 'Merchant ID');  
define('MODULE_PAYMENT_NOVALNET_VENDOR_ID_DESC', '');  

define('MODULE_PAYMENT_NOVALNET_AUTH_CODE_TITLE', 'Authentication code');  
define('MODULE_PAYMENT_NOVALNET_AUTH_CODE_DESC', '');  

define('MODULE_PAYMENT_NOVALNET_PRODUCT_ID_TITLE', 'Project ID');  
define('MODULE_PAYMENT_NOVALNET_PRODUCT_ID_DESC', '');  

define('MODULE_PAYMENT_NOVALNET_TARIFF_ID_TITLE', 'Select Tariff ID');  
define('MODULE_PAYMENT_NOVALNET_TARIFF_ID_DESC', 'Select a Tariff ID to match the preferred tariff plan you created at the Novalnet Admin Portal for this project.');  

define('MODULE_PAYMENT_NOVALNET_PAYMENT_ACCESS_KEY_TITLE', 'Payment access key');  
define('MODULE_PAYMENT_NOVALNET_PAYMENT_ACCESS_KEY_DESC', '');  

define('MODULE_PAYMENT_NOVALNET_PAYMENT_LOGO_DISPLAY_TITLE', 'Display payment logo');  
define('MODULE_PAYMENT_NOVALNET_PAYMENT_LOGO_DISPLAY_DESC', 'The payment method logo(s) will be displayed on the checkout page.');  

define('MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_COMPLETE_STATUS_ID_TITLE', '<h5><b>Order Status Management</b></h5>On-hold order status');  
define('MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_COMPLETE_STATUS_ID_DESC', 'Status to be used for on-hold orders until the transaction is confirmed or canceled.');  

define('MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_CANCELLED_STATUS_ID_TITLE', 'Canceled order status');  
define('MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_CANCELLED_STATUS_ID_DESC', 'Status to be used when order is canceled or fully refunded.'); 

define('MODULE_PAYMENT_NOVALNET_CALLBACK_TEST_MODE_TITLE', '<h5><b>Notification / Webhook URL Setup</b></h5>Allow manual testing of the Notification / Webhook URL');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_TEST_MODE_DESC', 'Enable this to test the Novalnet Notification / Webhook URL manually. Disable this before setting your shop live to block unauthorized calls from external parties.');

define('MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_SEND_TITLE', 'Enable e-mail notification');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_SEND_DESC', 'Enable this option to notify the given e-mail address when the Notification / Webhook URL is executed successfully.');

define('MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_TO_TITLE', 'Send e-mail to');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_TO_DESC', 'Notification / Webhook URL execution messages will be sent to this e-mail.'); 

define('MODULE_PAYMENT_NOVALNET_CALLBACK_NOTIFY_URL_TITLE', 'Notification / Webhook URL');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_NOTIFY_URL_DESC', 'Notification / Webhook URL is required to keep the merchant’s database/system synchronized with the Novalnet account (e.g. delivery status). Refer the <b>Installation Guide</b> for more information.');

?>
