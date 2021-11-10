<?php
#########################################################
#                                                       #
#  IDEAL / IDEAL payment     							#
#  method class                                         #
#  This module is used for real time processing of      #
#  German Bankdata of customers.                        #
#                                                       #
#  Released under the GNU General Public License.       #
#  This free contribution made by request.              #
#  If you have found this script useful a small         #
#  recommendation as well as a comment on merchant form #
#  would be greatly appreciated.                        #
#                                                       #
#  Script : novalnet_ideal.php     						#
#                                                       #
#########################################################

   define('MODULE_PAYMENT_NOVALNET_IDEAL_TEXT_TITLE', '<nobr>iDEAL <a href="http://www.novalnet.com" target="_new"><img src="https://www.novalnet.de/img/ideal_payment_small.png" height="47" alt="iDEAL" border="0"/></a></nobr>');
  define('MODULE_PAYMENT_NOVALNET_IDEAL_TEXT_DESCRIPTION', 'Pay safe and easy through Novalnet AG<BR>Before activating please enter the required Novalnet IDs in Edit mode!');
  define('MODULE_PAYMENT_NOVALNET_IDEAL_TEXT_LANG', 'EN');
  define('MODULE_PAYMENT_NOVALNET_IDEAL_TEXT_INFO', '');
  define('MODULE_PAYMENT_NOVALNET_IDEAL_TEXT_PUBLIC_TITLE', '<nobr>iDEAL <a href="http://www.novalnet.com" target="_new"><img src="https://www.novalnet.de/img/ideal_payment_small.png" height="47" alt="iDEAL"/></a></nobr>');
  
  define('MODULE_PAYMENT_NOVALNET_IDEAL_IN_TEST_MODE', ' (in Testing mode)');
  define('MODULE_PAYMENT_NOVALNET_IDEAL_NOT_CONFIGURED', ' (Not Configured)');
  define('MODULE_PAYMENT_NOVALNET_IDEAL_GUEST_USER', 'Guest');
  
  define('MODULE_PAYMENT_NOVALNET_IDEAL_TEXT_JS_NN_MISSING', '* Basic Parameter Missing!');
  define('MODULE_PAYMENT_NOVALNET_IDEAL_TEXT_ERROR', 'Account data Error:');
  define('MODULE_PAYMENT_NOVALNET_IDEAL_TEXT_CUST_INFORM', '');
  define('MODULE_PAYMENT_NOVALNET_IDEAL_TEXT_ORDERNO', 'Order no. ');
  define('MODULE_PAYMENT_NOVALNET_IDEAL_TEXT_ORDERDATE', 'Order date ');
  define('MODULE_PAYMENT_NOVALNET_IDEAL_TEST_MODE', 'Test Mode');
  define('MODULE_PAYMENT_NOVALNET_IDEAL_TEXT_HASH_ERROR', 'checkHash failed');
  define('MODULE_PAYMENT_NOVALNET_IDEAL_TEST_ORDER_MESSAGE',"Test Order");
  define('MODULE_PAYMENT_NOVALNET_IDEAL_TID_MESSAGE',"Novalnet Transaction ID : ");
  define('MODULE_PAYMENT_NOVALNET_IDEAL_CURL_MESSAGE',"* You have to enable the CURL function on server, please check with your hosting provider about it!");
  define('MODULE_PAYMENT_NOVALNET_INFORMATION_PAYMENT_IDEAL', 'You will be redirected to Novalnet AG website when you place the order.'); 
?>
