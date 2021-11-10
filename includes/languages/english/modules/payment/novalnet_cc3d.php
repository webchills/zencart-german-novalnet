<?php

#########################################################
#                                                       #
#  CC3D / CREDIT CARD 3d secure payment method class    #
#  This module is used for real time processing of      #
#  Credit card data of customers.                       #
#                                                       #
#  Released under the GNU General Public License.       #
#  This free contribution made by request.              #
#  If you have found this script useful a small         #
#  recommendation as well as a comment on merchant form #
#  would be greatly appreciated.                        #
#                                                       #
#  Script : novalnet_cc3d.php                           #
#                                                       #
#########################################################

  
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_TITLE', '<nobr>Credit Card 3D Secure <a href="http://www.novalnet.com" target="_new"><img src="http://www.novalnet.com/img/creditcard_small.jpg" alt="Visa & Mastercard" /></a></nobr>');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_DESCRIPTION', 'Pay safe and easy through Novalnet AG<BR>Before activating please enter the required Novalnet IDs in Edit mode!');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_LANG', 'EN');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_INFO', '');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_PUBLIC_TITLE', '<nobr>Credit Card 3D Secure <a href="http://www.novalnet.com" target="_new"><img src="http://www.novalnet.com/img/creditcard_small.jpg" alt="Visa & Mastercard" /></a></nobr>');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_CARD_OWNER', 'Credit card holder:');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_CARD_OWNER_LENGTH', '3');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_CC_NO', 'Card number:');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_CC_NO_LENGTH', '12');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_EXP_MONTH', 'Month :');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_EXP_MONTH_LENGTH', '2');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_EXP_YEAR', 'Year :');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_EXP_YEAR_LENGTH', '2');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_EXP_MONTHS_YEARS', 'Expiration Date :');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_SELECT', 'Please select');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_CVC', 'CVC (Verification Code): ');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_CVC_LENGTH', '3');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_CVC2', '<BR>* On Visa-, Master- and Eurocard you will find the 3 digit CVC-Code<BR>near the signature field at the rearside of the creditcard.');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_BOOKING_INFO', 'The amount will be booked immediatley from your credit card when you submit the order.');
  
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_JS_NN_ID2_MISSING', '* Product-ID2 and/or Tariff-ID2 missing!');
  define('MODULE_PAYMENT_NOVALNET_CC3D_IN_TEST_MODE', ' (in Testing mode)');
  define('MODULE_PAYMENT_NOVALNET_CC3D_NOT_CONFIGURED', ' (Not Configured)');
  define('MODULE_PAYMENT_NOVALNET_CC3D_GUEST_USER', 'Guest');
  
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_JS_NN_MISSING', '* Basic Parameter Missing!');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_JS_CARD_OWNER', '* Please enter valid credit card details!');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_JS_CC_NO', '* Please enter valid credit card details!');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_JS_EXP_MONTH', '* Please enter valid credit card details!');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_JS_EXP_YEAR', '* Please enter valid credit card details!');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_JS_CVC', '* Please enter valid credit card details!');
   define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_ERROR_EXP_MONTH', '* Please enter valid credit card details!');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_ERROR_EXP_YEAR', '* Please enter valid credit card details!');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_JS_CVC2', '* Please enter valid credit card details!');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_ERROR', 'Credit card data Error:');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_ORDERNO', 'Order no. ');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_ORDERDATE', 'Order date ');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEST_MODE', 'Test Mode');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEST_ORDER_MESSAGE',"Test Order");
  define('MODULE_PAYMENT_NOVALNET_CC3D_TID_MESSAGE',"Novalnet Transaction ID : ");
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_JS_CC_NO_ERR', '* Please enter valid credit card details!');
  define('MODULE_PAYMENT_NOVALNET_CC3D_CURL_MESSAGE',"* You have to enable the CURL function on server, please check with your hosting provider about it!");
?>
