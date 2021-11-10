<?php

#########################################################
#                                                       #
#  PREPAYMENT payment method class                      #
#  This module is used for real time processing of      #
#  PREPAYMENT payment of customers.                     #
#                                                       #
#  Released under the GNU General Public License.       #
#  This free contribution made by request.              #
#  If you have found this script useful a small         #
#  recommendation as well as a comment on merchant form #
#  would be greatly appreciated.                        #
#                                                       #
#  Script : novalnet_prepayment.php                     #
#                                                       #
#########################################################

  
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_TITLE', 'Prepayment');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_DESCRIPTION', 'Pay safe and easy through Novalnet AG<BR>Before activating please enter the required Novalnet IDs in Edit mode!');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_LANG', 'EN');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_INFO', '');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_PUBLIC_TITLE', 'Prepayment');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_BANK_ACCOUNT_OWNER', 'Account holder :');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_BANK_ACCOUNT_NUMBER', 'Account number :');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_BANK_CODE', 'Bankcode :');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_BANK_IBAN', 'IBAN :');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_BANK_BIC', 'SWIFT / BIC :');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_BANK_BANK', 'Bank :');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_BANK_CITY', 'City :');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_REFERENCE', 'Reference : TID');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_REFERENCE_INFO', 'Please note that the Transfer can only be identified with the above mentioned Reference.');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_AMOUNT', 'Amount :');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_TRANSFER_INFO', 'Please transfer the amount to the following information to our payment service Novalnet AG');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_IN_TEST_MODE', ' (in Testing mode)');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_NOT_CONFIGURED', ' (Not Configured)');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_GUEST_USER', 'Guest');
  
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_BANK_INFO', 'The bank details will be emailed to you soon after the completion of checkout process.');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_JS_NN_MISSING', '* Basic Parameter Missing!');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_ORDERNO', 'Order no. ');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_ORDERDATE', 'Order date ');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEST_MODE', 'Test Mode');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEST_ORDER_MESSAGE',"Test Order");
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TID_MESSAGE',"Novalnet Transaction ID : ");
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_ERROR', 'Account data Error:');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_CURL_MESSAGE',"* You have to enable the CURL function on server, please check with your hosting provider about it!");
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_NAME', 'NOVALNET AG');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_PAYMNETNAME', 'Novalnet Prepayment');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TID', 'TID :');
  define('MODULE_PAYMENT_NOVALNET_TEXT_DETAILS_PREPAYMENT_INTERNATIONAL_INFO', 'Only for international transfers:');
 

?>
