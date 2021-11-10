<?php

#########################################################
#                                                       #
#  CC / CREDIT CARD payment method class                #
#  This module is used for real time processing of      #
#  Credit card data of customers.                       #
#                                                       #
#  Released under the GNU General Public License.       #
#  This free contribution made by request.              #
#  If you have found this script useful a small         #
#  recommendation as well as a comment on merchant form #
#  would be greatly appreciated.                        #
#                                                       #
#  Script : novalnet_cc.php                             #
#                                                       #
#########################################################


 define('MODULE_PAYMENT_NOVALNET_CC_TEXT_TITLE', '<nobr>Credit Card <a href="http://www.novalnet.com " target="_new"><img src="http://www.novalnet.com/img/creditcard_small.jpg" alt="Visa & Mastercard" /></a></nobr>');
  define('MODULE_PAYMENT_NOVALNET_CC_TEXT_DESCRIPTION', 'Pay safe and easy through Novalnet AG<BR>Before activating please enter the required Novalnet IDs in Edit mode!');
  define('MODULE_PAYMENT_NOVALNET_CC_TEXT_LANG', 'EN');
  define('MODULE_PAYMENT_NOVALNET_CC_TEXT_INFO', '');
  
  define('MODULE_PAYMENT_NOVALNET_CC_TEXT_PUBLIC_TITLE', '<nobr>Credit Card <a href="http://www.novalnet.com " target="_new"><img src="http://www.novalnet.com/img/creditcard_small.jpg" alt="Visa & Mastercard" /></a></nobr>');
  
  define('MODULE_PAYMENT_NOVALNET_CC_IN_TEST_MODE', ' (in Testing mode)');
  define('MODULE_PAYMENT_NOVALNET_CC_NOT_CONFIGURED', ' (Not Configured)');
  define('MODULE_PAYMENT_NOVALNET_CC_GUEST_USER', 'Guest');
  
  
  define('MODULE_PAYMENT_NOVALNET_CC_TEXT_JS_NN_MISSING', '* Basic Parameter Missing!');
   define('MODULE_PAYMENT_NOVALNET_CC_TEXT_JS_NN_ID2_MISSING', '* Product-ID2 and/or Tariff-ID2 missing!');
  define('MODULE_PAYMENT_NOVALNET_CC_TEXT_ERROR', 'Credit card data Error:');
   define('MODULE_PAYMENT_NOVALNET_CC_TEXT_ORDERNO', 'Order No..: ');
  define('MODULE_PAYMENT_NOVALNET_CC_TEXT_ORDERDATE', 'Best-date: ');
  define('MODULE_PAYMENT_NOVALNET_CC_TEST_MODE', 'Test mode');
  define('MODULE_PAYMENT_NOVALNET_CC_TEST_ORDER_MESSAGE',"Test Order");
  define('MODULE_PAYMENT_NOVALNET_CC_TID_MESSAGE',"Novalnet Transaction ID : ");
  define('MODULE_PAYMENT_NOVALNET_CC_AMOUNT_VARIATION_MESSAGE',"You have changed the cart amount after getting PIN number, please try again with new call");   
  define('MODULE_PAYMENT_NOVALNET_CC_CURL_MESSAGE',"* You have to enable the CURL function on server, please check with your hosting provider about it!");  
  define('MODULE_PAYMENT_NOVALNET_INFORMATION_PAYMENT_CC', "The amount will be booked immediatley from your credit card when you submit the order."); 
?>
