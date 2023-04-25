<?php
/**
 * Novalnet payment module
 * admin component by webchills (www.webchills.at)
 * @author     Novalnet AG
 * @copyright  Copyright (c) Novalnet
 * @license    https://www.novalnet.de/payment-plugins/kostenlos/lizenz
 * @link       https://www.novalnet.de
 *
 * Script : novalnet_transactions.php
 */
define('HEADING_ADMIN_TITLE', 'Novalnet Transactions');
define('AMOUNT_INFO', 'Amounts are given in the smallest currency unit e.g. 100 cents = equal to 1.00 Euro');
define('TABLE_HEADING_ORDER_NUMBER', 'Shop Order Number');
define('TABLE_HEADING_AMOUNT', 'Amount');
define('TABLE_HEADING_CURRENCY', 'Currency');
define('NOVALNET_REFUND_AMOUNT', 'Refund Amount');
define('NOVALNET_CALLBACK_AMOUNT', 'Callback Amount');
define('NOVALNET_PAYMENT_TYPE', 'Payment Method');

define('NOVALNET_REFERENCE_ID', 'Novalnet Transaction ID');
define('TABLE_HEADING_TRANSACTION_ID', 'ID');

define('TABLE_HEADING_PAYMENT_TYPE', 'Payment Method');
define('TABLE_HEADING_PAYMENT_STATUS', 'Status');
define('TABLE_HEADING_PAYMENT_MESSAGE', 'Response');
define('TABLE_HEADING_PAYMENT_REFNUM', 'Reference Number');
define('TABLE_HEADING_ACTION', 'Action');
define('MAX_DISPLAY_SEARCH_RESULTS_NOVALNET_IPN', 50);
define('TEXT_INFO_NOVALNET_RESPONSE_BEGIN', 'Novalnet Transaction ');
define('TEXT_INFO_NOVALNET_RESPONSE_END', ' for order ');
define('HEADING_NOVALNET_STATUS', 'Status');
define('TEXT_NOVALNET_SORT_ORDER_INFO', 'Sort Order');
define('TEXT_SORT_NOVALNET_ID_DESC', 'Novalnet Sort Order (new-old)');
define('TEXT_SORT_NOVALNET_ID', 'Novalnet Sort Order (old-new)');
define('TEXT_SORT_ZEN_ORDER_ID_DESC', 'Shop Order Number (new-old)');
define('TEXT_SORT_ZEN_ORDER_ID', 'Shop Order Number (old-new)');
define('TEXT_SORT_NOVALNET_STATUS_DESC', 'Status desc');
define('TEXT_SORT_NOVALNET_STATUS', 'Status asc');
define('TEXT_SORT_NOVALNET_PAYMENT_TYPE_DESC', 'Payment Method desc');
define('TEXT_SORT_NOVALNET_PAYMENT_TYPE', 'Payment Method asc');
define('TEXT_SORT_NOVALNET_STATE', 'Status');
define('TEXT_ALL_IPNS', 'All');
define('NOVALNET_TRANSACTION_ID', 'Novalnet TID');
define('NOVALNET_STATUS', 'Status');
define('NOVALNET_VIEW_ORDER', 'View order');