<?php

#########################################################
#                                                       #
#  Paypal payment text creator script                   #
#  This script is used for translating the text for     #
#  real time processing of German Bankdata of customer  #
#                                                       #
#  Copyright (c) 2010 Novalnet AG                       #
#                                                       #
#  Released under the GNU General Public License        #
#  Novalnet_Paypal module Created By                    #
#  Dixon Rajdaniel                                      #
#  This free contribution made by request.              #
#  If you have found this script usefull a small        #
#  recommendation as well as a comment on merchant form #
#  would be greatly appreciated.                        #
#                                                       #
# Script: novalnet_paypal.php                           #
# Version 1.0.0                                         #
#                                                       #
#########################################################

  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEXT_TITLE', '<NOBR><A HREF="http://www.novalnet.de" TARGET="_new"><IMG SRC="http://www.novalnet.de/img/nn_paypal_small.png" ALT="Payment - Novalnet AG" BORDER="0"></A>&nbsp;Novalnet-Paypal</NOBR>');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEXT_DESCRIPTION', 'Pay safe and easy through Novalnet AG<BR>Before activating please enter the required Novalnet IDs in Edit mode!<br /><b><font color=\'red\'>You must have an Paypal Trader Account in order to use this module.</font></b>');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEXT_LANG', 'EN');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEXT_INFO', '');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_STATUS_TITLE', 'Enable Novalnet Paypal Module');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_STATUS_DESC', 'Do you want to activate the Paypal Module of Novalnet AG?');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_VENDOR_ID_TITLE', 'Your Novalnet Merchant ID');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_VENDOR_ID_DESC', 'Your Novalnet Merchant ID');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_AUTH_CODE_TITLE', 'Your Novalnet Merchant Authorisationcode');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_AUTH_CODE_DESC', 'Your Novalnet Merchant Authorisationcode');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_PRODUCT_ID_TITLE', 'Your Novalnet Product ID');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_PRODUCT_ID_DESC', 'Your Product ID in Novalnet');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TARIFF_ID_TITLE', 'Your Novalnet Tariff ID');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TARIFF_ID_DESC', 'the Tariff ID of the product');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_MANUAL_CHECK_LIMIT_TITLE', 'Manual checking amount in cents');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_MANUAL_CHECK_LIMIT_DESC', 'Please enter the amount in cents');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_PRODUCT_ID2_TITLE', 'Your second Product ID in Novalnet');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_PRODUCT_ID2_DESC', 'for the manual checking');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TARIFF_ID2_TITLE', 'the Tariff ID of the second product');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TARIFF_ID2_DESC', 'for the manual checking');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_INFO_TITLE', 'Information to the Customer');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_INFO_DESC', 'will be shown on the payment formula');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_ACDC_TITLE', 'Enable ACDC Control');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_ACDC_DESC', 'Do you want to activate the ACDC Control?');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_ORDER_STATUS_ID_TITLE', 'Set Order Status');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_ORDER_STATUS_ID_DESC', 'Set the status of orders made with this payment module to this value');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_SORT_ORDER_TITLE', 'Sort order of display.');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_SORT_ORDER_DESC', 'Sort order of display. Lowest is displayed first.');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_ZONE_TITLE', 'Payment Zone');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_ZONE_DESC', 'If a zone is selected, only enable this payment method for that zone.');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_ALLOWED_TITLE', 'Allowed zones');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_ALLOWED_DESC', 'Please enter the zones <b>separately</b> which should be allowed to use this modul (e. g. AT,DE (leave empty if you want to allow all zones))');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEXT_PUBLIC_TITLE', '<DIV><TABLE><TR><TD WIDTH="230" HEIGHT="25" VALIGN="middle"><NOBR><A HREF="http://www.novalnet.de" TARGET="_new"><IMG SRC="http://www.novalnet.de/img/NN_Logo_T.png" ALT="Payment - Novalnet AG" BORDER="0"></A>&nbsp;<b>Novalnet-Paypal</b></NOBR></TD><TD VALIGN="top"><NOBR><A HREF="http://www.novalnet.de" TARGET="_new"><IMG SRC="images/nn_paypal_klein.png" ALT="Payment - Novalnet AG" BORDER="0"></A></NOBR></TD></TR></TABLE></DIV>');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEXT_BANK_ACCOUNT_OWNER', 'Account holder:');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEXT_BANK_ACCOUNT_OWNER_LENGTH', '3');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEXT_BANK_ACCOUNT_NUMBER', 'Account number:');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEXT_BANK_ACCOUNT_NUMBER_LENGTH', '3');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEXT_BANK_CODE', 'Bankcode:');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEXT_BANK_CODE_LENGTH', '8');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEXT_JS_NN_MISSING', '* Basic Parameter Missing!');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEXT_JS_BANK_ACCOUNT_OWNER', '* German Account holder should be atleast 3 digits long!');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEXT_JS_BANK_ACCOUNT_NUMBER', '* German Account number should be atleast 3 digits long!');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEXT_JS_BANK_CODE', '* German Bankcode should be atleast 8 digits long!');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEXT_JS_ACDC', '* Please accept the acdc-Check or select other payment method!');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEXT_ERROR', 'Account data Error:');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEXT_CUST_INFORM', '');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEXT_ORDERNO', 'Order no. ');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEXT_ORDERDATE', 'Order date ');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEST_MODE', 'Test Mode');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEST_MODE_TITLE', 'Enable Test Mode');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEST_MODE_DESC', 'Do you want to activate test mode?');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEXT_HASH_ERROR', 'checkHash failed');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_PASSWORD_TITLE', 'Enter Password');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_PASSWORD_DESC', 'Enter Passwort');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_PROXY_TITLE', 'Proxy');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_PROXY_DESC', 'If you use a Proxy Server, enter the Proxy Server IP with port here (e.g. www.proxy.de:80)');

  define('MODULE_PAYMENT_NOVALNET_PAYPAL_API_USER_TITLE', 'PAYPAL API User Name');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_API_USER_DESC', 'Enter Your PAYPAL API User Name');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_API_USER', 'PAYPAL API User');

  define('MODULE_PAYMENT_NOVALNET_PAYPAL_API_PASSWORD_TITLE', 'PAYPAL API Password');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_API_PASSWORD_DESC', 'Enter Your PAYPAL API Password');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_API_PASSWORD', 'PAYPAL API Password');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEST_ORDER_MESSAGE',"<b>Test Order</b><br />");
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_API_SIGNATURE_TITLE', 'PAYPAL API Signature');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_API_SIGNATURE_DESC', 'Enter Your PAYPAL API Signature');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_API_SIGNATURE', 'PAYPAL API Signature');
  define('MODULE_PAYMENT_PAYPAL_TEXT_INFO','<img src="https://www.paypal.com/de_DE/DE/i/logo/lockbox_150x47.gif" />');

  #define('MODULE_PAYMENT_NOVALNET_PAYPAL_ORDER_STATUS_ABORT_ID_TITLE', 'Order Status "Paypal Cancelled"');
  #define('MODULE_PAYMENT_NOVALNET_PAYPAL_ORDER_STATUS_ABORT_ID_DESC', 'Set Order Status for Cancelation (e.g. PayPal cancelled)');
  #define('MODULE_PAYMENT_NOVALNET_PAYPAL_ORDER_STATUS_PENDING_ID_TITLE', 'Order Status "Open PP pending"');
  #define('MODULE_PAYMENT_NOVALNET_PAYPAL_ORDER_STATUS_PENDING_ID_DESC', 'Set Order Status for pending Transaction (e.g. Open PP pending)');
  #define('MODULE_PAYMENT_NOVALNET_PAYPAL_ORDER_STATUS_REJECTED_ID_TITLE', 'Order Status "Paypal rejected"');
  #define('MODULE_PAYMENT_NOVALNET_PAYPAL_ORDER_STATUS_REJECTED_ID_DESC', 'Set Order Status for rejected Transaction (e.g. PayPal rejected)');
 
?>
