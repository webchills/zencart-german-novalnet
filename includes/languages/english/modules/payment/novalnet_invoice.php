<?php

#########################################################
#                                                       #
#  Invoice payment method class                         #
#  This module is used for real time processing of      #
#  Invoice data of customers.                       	#
#                                                       #
#  Released under the GNU General Public License.       #
#  This free contribution made by request.              #
#  If you have found this script useful a small         #
#  recommendation as well as a comment on merchant form #
#  would be greatly appreciated.                        #
#                                                       #
#  Script : novalnet_invoice.php                        #
#                                                       #
#########################################################

  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_TITLE', 'Invoice');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_DESCRIPTION', 'Pay safe and easy through Novalnet AG<BR>Before activating please enter the required Novalnet IDs in Edit mode!');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_LANG', 'EN');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_INFO', '');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_INVOICE_TEST_MODE', ' (in Testing mode)');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_NOT_CONFIGURED', ' (Not Configured)');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_GUEST_USER', 'Guest');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_PUBLIC_TITLE', 'Invoice');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_BANK_ACCOUNT_OWNER', 'Account holder :');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_BANK_ACCOUNT_NUMBER', 'Account number :');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_BANK_CODE', 'Bankcode :');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_BANK_IBAN', 'IBAN :');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_BANK_BIC', 'SWIFT / BIC :');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_BANK_BANK', 'Bank :');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_BANK_CITY', 'City :');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_REFERENCE', 'Reference : TID');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_REFERENCE_INFO', 'Please note that the Transfer can only be identified with the above mentioned Reference.');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_AMOUNT', 'Amount :');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_TRANSFER_INFO', 'Please transfer the amount to the following information to our payment service Novalnet AG');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_BANK_INFO', 'The bank details will be emailed to you soon after the completion of checkout process.');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_DURATION_INFO', 'Payment duration:');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_DURATION_INFO_DAYS', 'days');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_DURATION_LIMIT_INFO', 'Please transfer the amount to the following information to our payment service Novalnet AG');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_DURATION_LIMIT_END_INFO', 'Due date :');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_JS_NN_MISSING', '* Basic Parameter Missing!');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_ORDERNO', 'Order no. ');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_ORDERDATE', 'Order date ');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEST_MODE', 'Test Mode');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_ERROR', 'Account data Error:');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_TEL_REQ', 'Phone Number:*');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS_REQ', 'Mobile phone number:*');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS_PIN', 'Enter your PIN Number:*');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS_NEW_PIN', 'Forgot PIN? [New PIN Request]');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS_TEL_NOTVALID', 'Please enter the Telephone / Mobilenumber!');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS_PIN_NOTVALID', 'PIN you have entered is incorrect or empty!');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS_CALL_MESSAGE', 'You will shortly receive a PIN via phone / SMS. Please enter the PIN in the appropriate text box.');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_PIN_INPUT_REQUEST_DESC',"You will shortly receive a PIN by phone / SMS. Please enter the PIN in the appropriate text box.");
  define('MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SESSION_ERROR',"Your PIN session has expired. Please try again with a new call");
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEST_ORDER_MESSAGE',"Test Order");
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TID_MESSAGE',"Novalnet Transaction ID : ");
  define('MODULE_PAYMENT_NOVALNET_INVOICE_AMOUNT_VARIATION_MESSAGE',"You have changed the cart amount after getting PIN number, please try again with new call");    
  define('MODULE_PAYMENT_NOVALNET_INVOICE_AMOUNT_VARIATION_MESSAGE_EMAIL',"You have changed the order amount after getting e-mail, please try again with a new call");    
  define('MODULE_PAYMENT_NOVALNET_INVOICE_EMAIL_PHONE_INPUT_REQUEST_DESC',"<b>* Please enter your phonenumber/email.</b>");    
  define('MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_EMAIL_REQ', 'Email Address:*');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_EMAIL_NOTVALID', 'Please enter the E-Mail Address!'); 
  define('MODULE_PAYMENT_NOVALNET_INVOICE_EMAIL_INPUT_REQUEST_DESC',"We have sent a email, please answer");
  define('MODULE_PAYMENT_NOVALNET_INVOICE_CURL_MESSAGE',"* You have to enable the CURL function on server, please check with your hosting provider about it!");
  define('MODULE_PAYMENT_NOVALNET_INVOICE_MAX_TIME_ERROR', '*Maximum number of PIN entries exceeded!');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_NAME', 'NOVALNET AG');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_PAYMNETNAME', 'Novalnet Invoice');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TID', 'TID :');
  define('MODULE_PAYMENT_NOVALNET_TEXT_DETAILS_INVOICE_INTERNATIONAL_INFO', 'Only for international transfers:'); 
?>
