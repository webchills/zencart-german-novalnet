<?php

#########################################################
#                                                       #
#  ELV_AT_PCI / DIRECT DEBIT payment text creator script#
#  This script is used for translating the text for     #
#  real time processing of Austrian Bankdata of customer#
#                                                       #
#  Copyright (c) 2009-2010 Novalnet AG                  #
#                                                       #
#  Released under the GNU General Public License        #
#  Novalnet_elv_at_pci module Created By Dixon Rajdaniel#
#  This free contribution made by request.              #
#  If you have found this script usefull a small        #
#  recommendation as well as a comment on merchant form #
#  would be greatly appreciated.                        #
#                                                       #
# Version: novalnet_elv_at_pci.php 1.3.2 2010-06-17     #
#                                                       #
#########################################################
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_TEXT_TITLE', '<NOBR><A HREF="http://www.novalnet.de" TARGET="_new"><IMG SRC="images/NN_Logo_T.png" ALT="Payment - Novalnet AG" BORDER="0"></A>&nbsp;Austrian Direct Debit PCI Standard</NOBR>');  
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_TEXT_DESCRIPTION', 'Pay safe and easy through Novalnet AG<BR>Before activating please enter the required Novalnet IDs in Edit mode');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_TEXT_LANG', 'EN');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_TEXT_INFO', '');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_STATUS_TITLE', 'Enable ELV-AT Module');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_STATUS_DESC', 'Do you want to activate the Austrian Direct Debit Method(ELV-AT) of Novalnet AG?');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_VENDOR_ID_TITLE', 'Your Novalnet Merchant ID');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_VENDOR_ID_DESC', 'Your Novalnet Merchant ID');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_AUTH_CODE_TITLE', 'Your Novalnet Merchant Authorisationcode');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_AUTH_CODE_DESC', 'Your Novalnet Merchant Authorisationcode');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_PRODUCT_ID_TITLE', 'Your Novalnet Product ID');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_PRODUCT_ID_DESC', 'Your Product ID in Novalnet');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_TARIFF_ID_TITLE', 'Your Novalnet Tariff ID');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_TARIFF_ID_DESC', 'the Tariff ID of the product');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_MANUAL_CHECK_LIMIT_TITLE', 'Manual checking amount in cents');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_MANUAL_CHECK_LIMIT_DESC', 'Please enter the amount in cents');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_PRODUCT_ID2_TITLE', 'Your second Product ID in Novalnet');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_PRODUCT_ID2_DESC', 'for the manual checking');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_TARIFF_ID2_TITLE', 'the Tariff ID of the second product');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_TARIFF_ID2_DESC', 'for the manual checking');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_INFO_TITLE', 'Information to the Customer');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_INFO_DESC', 'will be shown on the payment formula');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_ORDER_STATUS_ID_TITLE', 'Set Order Status');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_ORDER_STATUS_ID_DESC', 'Set the status of orders made with this payment module to this value');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_SORT_ORDER_TITLE', 'Sort order of display.');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_SORT_ORDER_DESC', 'Sort order of display. Lowest is displayed first.');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_ZONE_TITLE', 'Payment Zone');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_ZONE_DESC', 'If a zone is selected, only enable this payment method for that zone.');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_ALLOWED_TITLE', 'Allowed zones');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_ALLOWED_DESC', 'Please enter the zones <b>separately</b> which should be allowed to use this modul (e. g. AT,DE (leave empty if you want to allow all zones))');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_TEXT_PUBLIC_TITLE', '<DIV><TABLE><TR><TD WIDTH="100%" HEIGHT="25" VALIGN="middle"><A HREF="http://www.novalnet.de" TARGET="_new"><IMG SRC="images/NN_Logo_T.png" ALT="Payment - Novalnet AG" BORDER="0"></A><b>&nbsp; Austrian Direct Debit PCI Standard </b><TD VALIGN="top"><NOBR><A HREF="http://www.novalnet.de" TARGET="_new"><IMG SRC="images/ELV_Logo.png" ALT="Payment - Novalnet AG" BORDER="0"></A></NOBR></TD></TR></TABLE></DIV>');  
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_TEXT_BANK_ACCOUNT_OWNER', 'Account holder:');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_TEXT_BANK_ACCOUNT_OWNER_LENGTH', '3');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_TEXT_BANK_ACCOUNT_NUMBER', 'Account number:');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_TEXT_BANK_ACCOUNT_NUMBER_LENGTH', '3');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_TEXT_BANK_CODE', 'Bankcode:');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_TEXT_BANK_CODE_LENGTH', '5');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_TEXT_JS_NN_MISSING', '* Basic Paramater Missing!');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_TEXT_JS_BANK_ACCOUNT_OWNER', '* Austrian Direct Debit Account holder should be atleast 3 digits long!');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_TEXT_JS_BANK_ACCOUNT_NUMBER', '* Austrian Direct Debit Account number should be atleast 3 digits long!');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_TEXT_JS_BANK_CODE', '* Austrian Direct Debit Bankcode should be atleast 8 digits long!');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_TEXT_ERROR', 'Account data Error:');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_TEXT_CUST_INFORM', '');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_TEXT_ORDERNO', 'Order no. ');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_TEXT_ORDERDATE', 'Order date ');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_TEST_MODE', 'Test Mode');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_TEST_MODE_TITLE', 'Enable Test Mode');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_TEST_MODE_DESC', 'Do you want to activate test mode?');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_PROXY_TITLE', 'Proxy');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_PROXY_DESC', 'If you use a Proxy Server, enter the Proxy Server IP here (e.g. www.proxy.de:80)');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_BOOK_REF_TITLE', 'Your Booking Reference at Novalnet');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_BOOK_REF_DESC', 'Your Booking Reference at Novalnet');
    define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_TEXT_HASH_ERROR', 'checkHash failed');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_PASSWORD_TITLE', 'Enter Password');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_PASSWORD_DESC', 'Enter Password');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PCI_TEST_ORDER_MESSAGE'," TESTORDER \n"); 
  
?>
