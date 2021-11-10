<?php

#########################################################
#                                                       #
#  Telephone payment text creator script                #
#  This script is used for translating the text for     #
#  real time processing of Telephone Payment of customer#
#                                                       #
#  Copyright (c) 2009 Novalnet AG                       #
#                                                       #
#  Released under the GNU General Public License        #
#  Novalnet_tel module Created By Dixon Rajdaniel       #
#  This free contribution made by request.              #
#  If you have found this script usefull a small        #
#  recommendation as well as a comment on merchant form #
#  would be greatly appreciated.                        #
#                                                       #
#  Version : novalnet_tel.php vxtcTELen1.3.2 2009-03-01 #
#                                                       #
#########################################################

  define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_TITLE', '<NOBR><A HREF="http://www.novalnet.de" TARGET="_new"><IMG SRC="images/NN_Logo_T.png" ALT="Payment - Novalnet AG" BORDER="0"></A>&nbsp;Telephone Payment - Pay by call<A HREF="http://www.novalnet.de" TARGET="_new"><IMG SRC="images/novaltel_logo.png" ALT="Payment - Novalnet AG" BORDER="0"></A></NOBR>');
  define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_DESCRIPTION', 'Pay safe and easy through Novalnet AG<BR>Before activating please enter the required Novalnet IDs in Edit mode');
  define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_LANG', 'EN');
  define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_INFO', '');
  define('MODULE_PAYMENT_NOVALNET_TEL_STATUS_TITLE', 'Enable TEL Module');
  define('MODULE_PAYMENT_NOVALNET_TEL_STATUS_DESC', 'Do you want to activate the Telephone Payment(TEL) of Novalnet AG?');
  define('MODULE_PAYMENT_NOVALNET_TEL_VENDOR_ID_TITLE', 'Your Novalnet Merchant ID');
  define('MODULE_PAYMENT_NOVALNET_TEL_VENDOR_ID_DESC', 'Your Novalnet Merchant ID');
  define('MODULE_PAYMENT_NOVALNET_TEL_AUTH_CODE_TITLE', 'Your Novalnet Merchant Authorisationcode');
  define('MODULE_PAYMENT_NOVALNET_TEL_AUTH_CODE_DESC', 'Your Novalnet Merchant Authorisationcode');
  define('MODULE_PAYMENT_NOVALNET_TEL_PRODUCT_ID_TITLE', 'Your Novalnet Product ID');
  define('MODULE_PAYMENT_NOVALNET_TEL_PRODUCT_ID_DESC', 'Your Product ID in Novalnet');
  define('MODULE_PAYMENT_NOVALNET_TEL_TARIFF_ID_TITLE', 'Your Novalnet Tariff ID');
  define('MODULE_PAYMENT_NOVALNET_TEL_TARIFF_ID_DESC', 'the Tariff ID of the product');
  define('MODULE_PAYMENT_NOVALNET_TEL_INFO_TITLE', 'Information to the Customer');
  define('MODULE_PAYMENT_NOVALNET_TEL_INFO_DESC', 'will be shown on the payment formula');
  define('MODULE_PAYMENT_NOVALNET_TEL_ORDER_STATUS_ID_TITLE', 'Set Order Status');
  define('MODULE_PAYMENT_NOVALNET_TEL_ORDER_STATUS_ID_DESC', 'Set the status of orders made with this payment module to this value');
  define('MODULE_PAYMENT_NOVALNET_TEL_SORT_ORDER_TITLE', 'Sort order of display.');
  define('MODULE_PAYMENT_NOVALNET_TEL_SORT_ORDER_DESC', 'Sort order of display. Lowest is displayed first.');
  define('MODULE_PAYMENT_NOVALNET_TEL_ZONE_TITLE', 'Payment Zone');
  define('MODULE_PAYMENT_NOVALNET_TEL_ZONE_DESC', 'If a zone is selected, only enable this payment method for that zone.');
  define('MODULE_PAYMENT_NOVALNET_TEL_ALLOWED_TITLE', 'Allowed zones');
  define('MODULE_PAYMENT_NOVALNET_TEL_ALLOWED_DESC', 'Please enter the zones <b>separately</b> which should be allowed to use this modul (e. g. AT,DE (leave empty if you want to allow all zones))');
  define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_PUBLIC_TITLE', '<A NAME="novalnet_tel"></A><DIV><TABLE><TR><TD WIDTH="230" HEIGHT="25" VALIGN="middle"><A HREF="http://www.novalnet.de" TARGET="_new"><IMG SRC="images/NN_Logo_T.png" ALT="Payment - Novalnet AG" BORDER="0"></A>Telephone Payment<TD VALIGN="top"><NOBR><A HREF="http://www.novalnet.de" TARGET="_new"><IMG SRC="images/novaltel_reciever.png" ALT="Payment - Novalnet AG" BORDER="0"></A></NOBR></TD></TR></TABLE></DIV>');
  define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_STEP_INFO', '<B>Following steps are required to complete the telephone payment process:'); 
  define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_STEP1', '<B>Step 1:</B>');
  define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_STEP2', '<B>Step 2:</B>');
  define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_STEP1_DESC', 'Please dial the following number:');
  define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_STEP2_DESC', 'Please wait for the Signal ton and hangup the reciever.<BR>Please click on continue after your successive call.');
  define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_COST_INFO', '* This call costs <B>');
  define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_TAX_INFO', '&euro;</B> (inclusive tax) and is only possible from German Landline Telefon connection! *');
  define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_AMOUNT_ERROR1', 'Amount below 0.90 Euro and above 10.00 Euro is not accepted!');
  define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_AMOUNT_ERROR2', 'Amount below 0.90 Euro is not accepted!');
  define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_ERROR', 'Telephone Payment not possible!');
  define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_JS_NN_MISSING', '* Basic Paramater Missing!');
  define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_ORDERNO', 'Order no. ');
  define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_ORDERDATE', 'Order date ');
  define('MODULE_PAYMENT_NOVALNET_TEL_TEST_MODE', 'Test Mode');
  define('MODULE_PAYMENT_NOVALNET_TEL_TEST_ORDER_MESSAGE'," TESTORDER <br>");
  define('MODULE_PAYMENT_NOVALNET_TEL_TEST_MODE_TITLE', 'Enable Test Mode');
  define('MODULE_PAYMENT_NOVALNET_TEL_TEST_MODE_DESC', 'Do you want to activate test mode?');
  define('MODULE_PAYMENT_NOVALNET_TEL_PROXY_TITLE', 'Proxy');
  define('MODULE_PAYMENT_NOVALNET_TEL_PROXY_DESC', 'If you use a Proxy Server, enter the Proxy Server IP here (e.g. www.proxy.de:80)');

?>
