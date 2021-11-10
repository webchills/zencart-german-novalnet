<?php

#########################################################
#                                                       #
#  ELVAT / DIRECT DEBIT payment method class            #
#  This module is used for real time processing of      #
#  Austrian Bankdata of customers.                      #
#                                                       #
#  Released under the GNU General Public License.       #
#  This free contribution made by request.              #
#  If you have found this script useful a small         #
#  recommendation as well as a comment on merchant form #
#  would be greatly appreciated.                        #
#                                                       #
#  Script : novalnet_elv_at.php                         #
#                                                       #
#########################################################

 define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_TITLE', '<nobr>Direct Debit Austria <a href="http://www.novalnet.com" target="_new"><img src="http://www.novalnet.com/img/ELV_Logo.png" alt="Austrian direct debit" /></a></nobr>');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_DESCRIPTION', 'Pay safe and easy through Novalnet AG<BR>Before activating please enter the required Novalnet IDs in Edit mode');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_LANG', 'EN');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_INFO', '');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_PUBLIC_TITLE', '<nobr>Direct Debit Austria <a href="http://www.novalnet.com" target="_new"><img src="http://www.novalnet.com/img/ELV_Logo.png" alt="Austrian direct debit" /></a></nobr>');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_BANK_ACCOUNT_OWNER', 'Account holder:');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_BANK_ACCOUNT_OWNER_LENGTH', '3');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_BANK_ACCOUNT_NUMBER', 'Account number:');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_BANK_ACCOUNT_NUMBER_LENGTH', '5');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_BANK_CODE', 'Bankcode:');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_BANK_CODE_LENGTH', '3');
  
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_IN_TEST_MODE', ' (in Testing mode)');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_NOT_CONFIGURED', ' (Not Configured)');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_GUEST_USER', 'Guest');
  
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_JS_NN_ID2_MISSING', '* Product-ID2 and/or Tariff-ID2 missing!');
  
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_JS_NN_MISSING', '* Basic Parameter Missing!');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_JS_BANK_ACCOUNT_OWNER', '* Please enter valid account details!');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_JS_BANK_ACCOUNT_NUMBER', '* Please enter valid account details!');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_JS_BANK_CODE', '* Please enter valid account details!');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_ERROR', 'Account data Error:');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_CUST_INFORM', '');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_ORDERNO', 'Order no. ');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_ORDERDATE', 'Order date ');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEST_MODE', 'Test Mode');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEST_ORDER_MESSAGE',"Test Order");
  
  //Start : Pin by call back  
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_BY_CALLBACK_TEL_REQ', 'Phone Number:*');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_BY_CALLBACK_SMS_REQ', 'Mobile Number:*');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_BY_CALLBACK_SMS_PIN', 'Enter your PIN Number:*');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_BY_CALLBACK_SMS_NEW_PIN', 'Forgot PIN? [New PIN Request]');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_BY_CALLBACK_SMS_TEL_NOTVALID', 'Please enter the Telephone / Mobilenumber!');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_BY_CALLBACK_SMS_PIN_NOTVALID', 'PIN you have entered is incorrect or empty!');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_BY_CALLBACK_SMS_CALL_MESSAGE', 'You will shortly receive a PIN via phone / SMS. Please enter the PIN in the appropriate text box.');

  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_INPUT_REQUEST_DESC',"You will shortly receive a PIN by phone / SMS. Please enter the PIN in the appropriate text box.");
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_BY_CALLBACK_SESSION_ERROR',"Your PIN session has expired. Please try again with a new call!");
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_BY_CALLBACK_EMAIL_REQ', 'Email Address:*');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_BY_CALLBACK_EMAIL_NOTVALID', 'Please enter the E-Mail Address!'); 
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_EMAIL_INPUT_REQUEST_DESC',"We have sent a email, please answer");
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_AMOUNT_VARIATION_MESSAGE',"You have changed the order amount after getting PIN number, please try again with new call"); 
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_AMOUNT_VARIATION_MESSAGE_EMAIL',"You have changed the order amount after getting e-mail, please try again with a new call"); 
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_EMAIL_PHONE_INPUT_REQUEST_DESC',"* Please enter your phonenumber/email.");    
  //End : Pin by call back
   define('MODULE_PAYMENT_NOVALNET_ELV_AT_TID_MESSAGE'," Novalnet Transaction ID : ");
   define('MODULE_PAYMENT_NOVALNET_ELV_AT_CURL_MESSAGE',"* You have to enable the CURL function on server, please check with your hosting provider about it!");
   define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_JS_BANK_ERROR_ACCOUNT_NUMBER', '* Please enter valid account details!');
   define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_JS_BANK_CODE_ERROR', '* Please enter valid account details!');
   define('MODULE_PAYMENT_NOVALNET_ELV_AT_MAX_TIME_ERROR', '*Maximum number of PIN entries exceeded!');
   define('MODULE_PAYMENT_NOVALNET_INFORMATION_PAYMENT_AT', 'Your account will be debited upon delivery of goods.');

?>
