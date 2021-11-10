<?php

#########################################################
#                                                       #
#  INVOICE payment text creator script                  #
#  This script is used for translating the text for     #
#  real time processing of payment of customers.        #
#                                                       #
#  Copyright (c) 2009 Novalnet AG                       #
#                                                       #
#  Released under the GNU General Public License        #
#  Novalnet_invoice module Created By Dixon Rajdaniel   #
#  This free contribution made by request.              #
#  If you have found this script usefull a small        #
#  recommendation as well as a comment on merchant form #
#  would be greatly appreciated.                        #
#                                                       #
# Version: novalnet_invoice.php vxtcINde1.3.2 2009-03-01#
#                                                       #
#########################################################

  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_TITLE', '<NOBR><A HREF="http://www.novalnet.de" TARGET="_new"><IMG SRC="http://www.novalnet.de/img/NN_Logo_T.png" ALT="Payment - Novalnet AG" BORDER="0"></A>&nbsp;Invoice</NOBR>');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_DESCRIPTION', 'Pay safe and easy through Novalnet AG<BR>Before activating please enter the required Novalnet IDs in Edit mode!');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_LANG', 'EN');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_INFO', '');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_STATUS_TITLE', 'Enable INVOICE Module');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_STATUS_DESC', 'Do you want to activate the Invoice Method of Novalnet AG?');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_VENDOR_ID_TITLE', 'Your Novalnet Merchant ID');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_VENDOR_ID_DESC', 'Your Novalnet Merchant ID');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_AUTH_CODE_TITLE', 'Your Novalnet Merchant Authorisationcode');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_AUTH_CODE_DESC', 'Your Novalnet Merchant Authorisationcode');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_PRODUCT_ID_TITLE', 'Your Novalnet Product ID');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_PRODUCT_ID_DESC', 'Your Product ID in Novalnet');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TARIFF_ID_TITLE', 'Your Novalnet Tariff ID');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TARIFF_ID_DESC', 'the Tariff ID of the product');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_DURATION_TITLE', 'Payment duration');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_DURATION_DESC', 'will be shown on the payment formula');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_INFO_TITLE', 'Information to the Customer');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_INFO_DESC', 'will be shown on the payment formula');  
  define('MODULE_PAYMENT_NOVALNET_INVOICE_ORDER_STATUS_ID_TITLE', 'Set Order Status');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_ORDER_STATUS_ID_DESC', 'Set the status of orders made with this payment module to this value');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_SORT_ORDER_TITLE', 'Sort order of display.');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_SORT_ORDER_DESC', 'Sort order of display. Lowest is displayed first.');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_ZONE_TITLE', 'Payment Zone');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_ZONE_DESC', 'If a zone is selected, only enable this payment method for that zone.');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_ALLOWED_TITLE', 'Allowed zones');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_ALLOWED_DESC', 'Please enter the zones <b>separately</b> which should be allowed to use this modul (e. g. AT,DE (leave empty if you want to allow all zones');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_PUBLIC_TITLE', '<DIV><TABLE><TR><TD WIDTH="230" HEIGHT="25" VALIGN="middle"><NOBR><A HREF="http://www.novalnet.de" TARGET="_new"><IMG SRC="http://www.novalnet.de/img/NN_Logo_T.png" ALT="Payment - Novalnet AG" BORDER="0"></A>&nbsp;Invoice</NOBR></TD><TD VALIGN="top"><NOBR><A HREF="http://www.novalnet.de" TARGET="_new"></A></NOBR></TD></TR></TABLE></DIV>');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_BANK_ACCOUNT_OWNER', 'Account holder:');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_BANK_ACCOUNT_NUMBER', 'Account number:');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_BANK_CODE', 'Bankcode:');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_BANK_IBAN', 'IBAN:');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_BANK_BIC', 'SWIFT / BIC:');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_BANK_BANK', 'Bank:');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_BANK_CITY', 'City:');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_AMOUNT', 'Amount:');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_REFERENCE', 'Reference:');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_REFERENCE_INFO', 'Please note that the Transfer can only be identified with the above mentioned Reference.');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_TRANSFER_INFO', 'Please transfer the amount to following account:');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_BANK_INFO', 'The Bank details will be emailed to you soon after the completion of checkout process');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_DURATION_INFO', 'Payment duration:');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_DURATION_INFO_DAYS', 'days');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_DURATION_LIMIT_INFO', 'Please transfer the amount at the latest, untill');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_DURATION_LIMIT_END_INFO', 'to following account:');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_JS_NN_MISSING', '* Basic Paramater Missing!');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_ORDERNO', 'Order no. ');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_ORDERDATE', 'Order date ');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEST_MODE', 'Test Mode');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEST_MODE_TITLE', 'Enable Test Mode');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEST_MODE_DESC', 'Do you want to activate test mode?');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_PROXY_TITLE', 'Proxy');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_PROXY_DESC', 'If you use a Proxy Server, enter the Proxy Server IP here (e.g. www.proxy.de:80)');
  
  define('MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS_TITLE', 'PIN by Callback/SMS');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS_DESC', 'When activated by PIN Callback / SMS the customer to enter their phone / mobile number requested. By phone or SMS, the customer receives from the AG Novalnet a PIN, which he must enter before ordering. If the PIN is valid, the payment process has been completed successfully, otherwise the customer will be prompted again to enter the PIN. This service is only available for customers from Germany. ');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS_TEL', 'Phone Number:*');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS_PIN', 'PIN:');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS_NEW_PIN', 'Forgot PIN? [New PIN Request]');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS_TEL_NOTVALID', 'The telephone number entered is not valid!');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS_PIN_NOTVALID', 'The entered PIN is incorrect or blank!');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS_CALL_MESSAGE', 'You will shortly receive a PIN via phone / SMS. Please enter the PIN in the appropriate text box.');  
  define('MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_MIN_LIMIT_TITLE', 'Minimum Amount Limit for PIN by Callback');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_MIN_LIMIT_DESC', 'Please enter minimum amount limit to enable "Pin by CallBack" modul (In Cents, e.g. 100,200)');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_PIN_INPUT_REQUEST_DESC',"<b>Please Enter PIN number</b>");
  define('MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SESSION_ERROR',"<b>Your PIN session has expired, Please try again</b>");
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEST_ORDER_MESSAGE',"TESTORDER <br />");
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TID_MESSAGE',". Novalnet Transaction ID : ");
  define('MODULE_PAYMENT_NOVALNET_INVOICE_AMOUNT_VARIATION_MESSAGE',"You have changed the cart amount after getting PIN number, please try again with new call");  
?>
