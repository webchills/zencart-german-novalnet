<?php

#########################################################
#                                                       #
#  ELV_DE / DIRECT DEBIT payment text creator script    #
#  This script is used for translating the text for     #
#  real time processing of German Bankdata of customer  #
#                                                       #
#  Copyright (c) 2009 Novalnet AG                       #
#                                                       #
#  Released under the GNU General Public License        #
#  Novalnet_elv_de module Created By Dixon Rajdaniel    #
#  This free contribution made by request.              #
#  If you have found this script usefull a small        #
#  recommendation as well as a comment on merchant form #
#  would be greatly appreciated.                        #
#							                            #
#  Version: novalnet_elv_de.php vxtcDEde1.3.2 2009-03-01#
#                                                       #
#########################################################

  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_TITLE', '<NOBR><A HREF="http://www.novalnet.de" TARGET="_new"><IMG SRC="http://www.novalnet.de/img/NN_Logo_T.png" ALT="Payment - Novalnet AG" BORDER="0"></A>&nbsp;German Direct Debit</NOBR>');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_DESCRIPTION', 'Pay safe and easy through Novalnet AG<BR>Before activating please enter the required Novalnet IDs in Edit mode!');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_LANG', 'DE');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_INFO', '');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_STATUS_TITLE', 'Enable ELV-DE Module');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_STATUS_DESC', 'Do you want to activate the German Direct Debit Method(ELV-DE) of Novalnet AG?');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_VENDOR_ID_TITLE', 'Your Novalnet Merchant ID');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_VENDOR_ID_DESC', 'Your Novalnet Merchant ID');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_AUTH_CODE_TITLE', 'Your Novalnet Merchant Authorisationcode');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_AUTH_CODE_DESC', 'Your Novalnet Merchant Authorisationcode');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PRODUCT_ID_TITLE', 'Your Novalnet Product ID');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PRODUCT_ID_DESC', 'Your Product ID in Novalnet');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TARIFF_ID_TITLE', 'Your Novalnet Tariff ID');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TARIFF_ID_DESC', 'the Tariff ID of the product');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_MANUAL_CHECK_LIMIT_TITLE', 'Manual checking amount in cents');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_MANUAL_CHECK_LIMIT_DESC', 'Please enter the amount in cents');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PRODUCT_ID2_TITLE', 'Your second Product ID in Novalnet');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PRODUCT_ID2_DESC', 'for the manual checking');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TARIFF_ID2_TITLE', 'the Tariff ID of the second product');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TARIFF_ID2_DESC', 'for the manual checking');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_INFO_TITLE', 'Information to the Customer');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_INFO_DESC', 'will be shown on the payment formula');  
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_ACDC_TITLE', 'Enable ACDC Control');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_ACDC_DESC', 'Do you want to activate the ACDC Control?');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_ORDER_STATUS_ID_TITLE', 'Set Order Status');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_ORDER_STATUS_ID_DESC', 'Set the status of orders made with this payment module to this value');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_SORT_ORDER_TITLE', 'Sort order of display.');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_SORT_ORDER_DESC', 'Sort order of display. Lowest is displayed first.');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_ZONE_TITLE', 'Payment Zone');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_ZONE_DESC', 'If a zone is selected, only enable this payment method for that zone.');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_ALLOWED_TITLE', 'Allowed zones');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_ALLOWED_DESC', 'Please enter the zones <b>separately</b> which should be allowed to use this modul (e. g. AT,DE (leave empty if you want to allow all zones))');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_PUBLIC_TITLE', '<DIV><TABLE><TR><TD WIDTH="230" HEIGHT="25" VALIGN="middle"><NOBR><A HREF="http://www.novalnet.de" TARGET="_new"><IMG SRC="http://www.novalnet.de/img/NN_Logo_T.png" ALT="Payment - Novalnet AG" BORDER="0"></A>&nbsp;German Direct Debit</NOBR></TD><TD VALIGN="top"><NOBR><A HREF="http://www.novalnet.de" TARGET="_new"><IMG SRC="images/ELV_Logo.png" ALT="Payment - Novalnet AG" BORDER="0"></A></NOBR></TD></TR></TABLE></DIV>');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_BANK_ACCOUNT_OWNER', 'Account holder:');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_BANK_ACCOUNT_OWNER_LENGTH', '3');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_BANK_ACCOUNT_NUMBER', 'Account number:');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_BANK_ACCOUNT_NUMBER_LENGTH', '3');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_BANK_CODE', 'Bankleitzahl:');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_BANK_CODE_LENGTH', '8');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_ACDC_INFO', "Der <B><A HREF='javascript:show_acdc_info()' ONMOUSEOVER='show_acdc_info()'>acdc-Check</A></B> Accepted");
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_ACDC_DIV', "<SCRIPT>var showbaby;function show_acdc_info(){var url=parent.location.href;url=url.substring(0,url.lastIndexOf('/'))+'/images/acdc_info.png';w='550';h='300';x=screen.availWidth/2-w/2;y=screen.availHeight/2-h/2;showbaby=window.open(url,'showbaby','toolbar=0,location=0,directories=0,status=0,menubar=0,resizable=1,width='+w+',height='+h+',left='+x+',top='+y+',screenX='+x+',screenY='+y);showbaby.focus();}function hide_acdc_info(){showbaby.close();}</SCRIPT>");
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_JS_NN_MISSING', '* Basic Paramater Missing!');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_JS_BANK_ACCOUNT_OWNER', '* German Direct Debit Account holder should be atleast 3 digits long!');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_JS_BANK_ACCOUNT_NUMBER', '* German Direct Debit Account number should be atleast 3 digits long!');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_JS_BANK_CODE', '* German Direct Debit Bankcode should be atleast 8 digits long!');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_JS_ACDC', '* Please accept the acdc-Check or select other payment method!');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_JS_ACDC', '* Please accept the acdc-Check or select other payment method!');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_ERROR', 'Account data Error:');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_CUST_INFORM', 'We will obtain an evaluation of your credit rating before processing your order, as we can only process your order when your credit rating in positive. You will be debited when the goods are shipped. In case of a return debit note/revocation we will charge an amount of 10 Euro for our time and effort and the whole process will be handed over to debt collection immediately.');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_ORDERNO', 'Order no. ');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_ORDERDATE', 'Order date ');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEST_MODE', 'Test Mode');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEST_MODE_TITLE', 'Enable Test Mode');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEST_MODE_DESC', 'Do you want to activate test mode?');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PROXY_TITLE', 'Proxy');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PROXY_DESC', 'If you use a Proxy Server, enter the Proxy Server IP here (e.g. www.proxy.de:80)');

  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_BY_CALLBACK_SMS_TITLE', 'PIN by Callback/SMS');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_BY_CALLBACK_SMS_DESC', 'When activated by PIN Callback / SMS the customer to enter their phone / mobile number requested. By phone or SMS, the customer receives from the AG Novalnet a PIN, which he must enter before ordering. If the PIN is valid, the payment process has been completed successfully, otherwise the customer will be prompted again to enter the PIN. This service is only available for customers from Germany. This service is only available for German customers. ');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_BY_CALLBACK_SMS_TEL', 'Phone Number:*');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_BY_CALLBACK_SMS_PIN', 'PIN:');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_BY_CALLBACK_SMS_NEW_PIN', 'Forgot PIN? [New PIN Request]');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_BY_CALLBACK_SMS_TEL_NOTVALID', 'The telephone number entered is not valid!');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_BY_CALLBACK_SMS_PIN_NOTVALID', 'The entered PIN is incorrect or blank!');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_BY_CALLBACK_SMS_CALL_MESSAGE', 'You will shortly receive a PIN via phone / SMS. Please enter the PIN in the appropriate text box.');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_BY_CALLBACK_MIN_LIMIT_TITLE', 'Minimum Amount Limit for PIN by Callback');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_BY_CALLBACK_MIN_LIMIT_DESC', 'Please enter minimum amount limit to enable "Pin by CallBack" modul (In Cents, e.g. 100,200)');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_INPUT_REQUEST_DESC',"<b>Please Enter PIN number</b>");
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_BY_CALLBACK_SESSION_ERROR',"<b>Your PIN session has expired, Please try again</b>");
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TID_MESSAGE',". Novalnet Transaction ID : ");
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_AMOUNT_VARIATION_MESSAGE',"You have changed the cart amount after getting PIN number, please try again with new call");  
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEST_ORDER_MESSAGE',"TESTORDER <br>");
?>
