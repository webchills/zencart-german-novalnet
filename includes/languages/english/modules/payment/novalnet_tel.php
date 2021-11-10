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

  define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_TITLE', '<nobr>Novalnet Telephone Payment - Pay by call<a href="http://www.novalnet.com" target="_new"><img src="http://www.novalnet.com/img/novaltel_logo.png" alt="Telephone Payment" border="0"></a></nobr>');
  define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_DESCRIPTION', 'Pay safe and easy through Novalnet AG<BR>Before activating please enter the required Novalnet IDs in Edit mode');
  define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_LANG', 'EN');
  define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_INFO', '');
 
  define('MODULE_PAYMENT_NOVALNET_TEL_IN_TEST_MODE', ' (in Testing mode)');
  define('MODULE_PAYMENT_NOVALNET_TEL_NOT_CONFIGURED', ' (Not Configured)');
  define('MODULE_PAYMENT_NOVALNET_TEL_GUEST_USER', 'Guest');
 
  define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_PUBLIC_TITLE', '<nobr>Telephone Payment<a href="http://www.novalnet.com" target="_new"><img src="https://www.novalnet.de/img/novaltel_reciever.png" alt="Telephone Payment" border="0"></a></nobr>');
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
  define('MODULE_PAYMENT_NOVALNET_TEL_CURL_MESSAGE',"* You have to enable the CURL function on server, please check with your hosting provider about it!");
 
?>
