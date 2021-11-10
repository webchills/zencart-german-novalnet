<?php

#########################################################
#                                                       #
#  ELVDEPCI / DIRECT DEBIT PCI payment method class     #
#  This module is used for real time processing of      #
#  German Bankdata of customers.                        #
#                                                       #
#  Released under the GNU General Public License.       #
#  This free contribution made by request.              #
#  If you have found this script useful a small         #
#  recommendation as well as a comment on merchant form #
#  would be greatly appreciated.                        #
#                                                       #
#  Script : novalnet_elv_de_pci.php                     #
#                                                       # 
#########################################################

  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_TEXT_TITLE', '<nobr>Direct Debit German PCI <a href="http://www.novalnet.com" target="_new"><img src="http://www.novalnet.com/img/ELV_Logo.png" alt="German direct debit"/></a></nobr>');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_TEXT_DESCRIPTION', 'Pay safe and easy through Novalnet AG<BR>Before activating please enter the required Novalnet IDs in Edit mode!');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_TEXT_LANG', 'EN');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_TEXT_INFO', '');
 
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_TEXT_PUBLIC_TITLE', '<nobr>Direct Debit German PCI <a href="http://www.novalnet.com" target="_new"><img src="http://www.novalnet.com/img/ELV_Logo.png" alt="German direct debit"/></a></nobr>');
  
  define('MODULE_PAYMENT_NOVALNET_DE_PCI_IN_TEST_MODE', ' (in Testing mode)');
  define('MODULE_PAYMENT_NOVALNET_DE_PCI_NOT_CONFIGURED', ' (Not Configured)');
  define('MODULE_PAYMENT_NOVALNET_DE_PCI_GUEST_USER', 'Guest');
  
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_TEXT_JS_NN_ID2_MISSING', '* Product-ID2 and/or Tariff-ID2 missing!');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_TEXT_JS_NN_MISSING', '* Basic Parameter Missing!');  
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_TEXT_ERROR', 'Account data Error:');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_TEXT_CUST_INFORM', '');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_TEXT_ORDERNO', 'Order no. ');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_TEXT_ORDERDATE', 'Order date ');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_TEST_MODE', 'Test Mode');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_TEXT_HASH_ERROR', 'checkHash failed');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_TEST_ORDER_MESSAGE',"Test Order");
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_TID_MESSAGE',"Novalnet Transaction ID : ");
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_CURL_MESSAGE',"* You have to enable the CURL function on server, please check with your hosting provider about it!");
  define('MODULE_PAYMENT_NOVALNET_INFORMATION_PAYMENT_DE_PCI', 'You will be redirected to Novalnet AG website when you place the order.');

?>
