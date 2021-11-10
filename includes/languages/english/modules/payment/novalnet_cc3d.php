<?php

#########################################################
#                                                       #
#  CC / Credit card 3dsecure payment text creator script#
#  This script is used for translating the text for     #
#  real time processing of Credit card data of customer #
#  on 3d secure mode                                    #
#							                            #
#  Copyright (c) 2009 Novalnet AG                       #
#                                                       #
#  Released under the GNU General Public License        #
#  novalnet_cc module Created By Dixon Rajdaniel    	#
#  This free contribution made by request.              #
#  If you have found this script usefull a small        #
#  recommendation as well as a comment on merchant form #
#  would be greatly appreciated.                        #
#							                            #
#  novalnet_cc_3d.php vxtcCC3Den1.3.2 2009-03-01	    #
#                                                       #
#########################################################

  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_TITLE', '<NOBR><A HREF="http://www.novalnet.de" TARGET="_new"><IMG SRC="images/NN_Logo_T.png" ALT="Payment - Novalnet AG" BORDER="0"></A>&nbsp;Credit Card 3DSecure</NOBR>');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_DESCRIPTION', 'Pay safe and easy through Novalnet AG<BR>Before activating please enter the required Novalnet IDs in Edit mode!');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_LANG', 'EN');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_INFO', '');
  define('MODULE_PAYMENT_NOVALNET_CC3D_STATUS_TITLE', 'Enable CC Module');
  define('MODULE_PAYMENT_NOVALNET_CC3D_STATUS_DESC', 'Do you want to activate the Credit card Method(CC) of Novalnet AG?');
  define('MODULE_PAYMENT_NOVALNET_CC3D_VENDOR_ID_TITLE', 'Your Novalnet Merchant ID');
  define('MODULE_PAYMENT_NOVALNET_CC3D_VENDOR_ID_DESC', 'Your Novalnet Merchant ID');
  define('MODULE_PAYMENT_NOVALNET_CC3D_AUTH_CODE_TITLE', 'Your Novalnet Merchant Authorisationcode');
  define('MODULE_PAYMENT_NOVALNET_CC3D_AUTH_CODE_DESC', 'Your Novalnet Merchant Authorisationcode');
  define('MODULE_PAYMENT_NOVALNET_CC3D_PRODUCT_ID_TITLE', 'Your Novalnet Product ID');
  define('MODULE_PAYMENT_NOVALNET_CC3D_PRODUCT_ID_DESC', 'Your Product ID in Novalnet');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TARIFF_ID_TITLE', 'Your Novalnet Tariff ID');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TARIFF_ID_DESC', 'the Tariff ID of the product');
  define('MODULE_PAYMENT_NOVALNET_CC3D_MANUAL_CHECK_LIMIT_TITLE', 'Manual checking amount in cents');
  define('MODULE_PAYMENT_NOVALNET_CC3D_MANUAL_CHECK_LIMIT_DESC', 'Please enter the amount in cents');
  define('MODULE_PAYMENT_NOVALNET_CC3D_PRODUCT_ID2_TITLE', 'Your second Product ID in Novalnet');
  define('MODULE_PAYMENT_NOVALNET_CC3D_PRODUCT_ID2_DESC', 'for the manual checking');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TARIFF_ID2_TITLE', 'the Tariff ID of the second product');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TARIFF_ID2_DESC', 'for the manual checking');
  define('MODULE_PAYMENT_NOVALNET_CC3D_BOOK_REF_TITLE', 'Your Booking Reference at Novalnet');
  define('MODULE_PAYMENT_NOVALNET_CC3D_BOOK_REF_DESC', 'Your Booking Reference at Novalnet');
  define('MODULE_PAYMENT_NOVALNET_CC3D_ORDER_STATUS_ID_TITLE', 'Set Order Status');
  define('MODULE_PAYMENT_NOVALNET_CC3D_ORDER_STATUS_ID_DESC', 'Set the status of orders made with this payment module to this value');
  define('MODULE_PAYMENT_NOVALNET_CC3D_SORT_ORDER_TITLE', 'Sort order of display.');
  define('MODULE_PAYMENT_NOVALNET_CC3D_SORT_ORDER_DESC', 'Sort order of display. Lowest is displayed first.');
  define('MODULE_PAYMENT_NOVALNET_CC3D_ZONE_TITLE', 'Payment Zone');
  define('MODULE_PAYMENT_NOVALNET_CC3D_ZONE_DESC', 'If a zone is selected, only enable this payment method for that zone.');
  define('MODULE_PAYMENT_NOVALNET_CC3D_ALLOWED_TITLE', 'Allowed zones');
  define('MODULE_PAYMENT_NOVALNET_CC3D_ALLOWED_DESC', 'Please enter the zones <b>separately</b> which should be allowed to use this modul (e. g. AT,DE (leave empty if you want to allow all zones))');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_PUBLIC_TITLE', '<DIV><TABLE><TR><TD WIDTH="230" HEIGHT="25" VALIGN="middle">Credit card 3DSecure</TD><TD VALIGN="top"><NOBR><A HREF="http://www.novalnet.de" TARGET="_new"><IMG SRC="images/VI_Logo.png" ALT="Payment - Novalnet AG" BORDER="0">&nbsp;<IMG SRC="images/MC_Logo.png" ALT="Payment - Novalnet AG" BORDER="0"></A></NOBR></TD></TR></TABLE></DIV>');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_CARD_OWNER', 'Credit card holder:');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_CARD_OWNER_LENGTH', '3');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_CC_NO', 'Credit card number:');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_CC_NO_LENGTH', '12');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_EXP_MONTH', 'Month:');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_EXP_MONTH_LENGTH', '2');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_EXP_YEAR', ' Year:');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_EXP_YEAR_LENGTH', '2');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_SELECT', 'Please select');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_CVC', 'CVC (Verification Code):<BR>&nbsp;<BR>&nbsp;<BR>&nbsp;<BR>&nbsp;<BR>&nbsp;<BR>');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_CVC_LENGTH', '3');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_CVC2', '<BR>* On Visa-, Master- and Eurocard you will find the 3 digit CVC-Code<BR>near the signature field at the rearside of the creditcard.');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_BOOKING_INFO', '<BR><BR>The amount will be booked immediatley from your credit card<BR>with <B>$BOOKINFO</B> note.');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_JS_NN_MISSING', '* Basic Paramater Missing!');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_JS_CARD_OWNER', '* Credit card holder should be atleast 3 digits long!');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_JS_CC_NO', '* Credit card number should be atleast 12 digits long!');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_JS_EXP_MONTH', '* Credit card expiry month should be atleast 2 digits long!');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_JS_EXP_YEAR', '* Credit card expiry year should be atleast 2 digits long!');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_JS_CVC', '* CVC (Verification Code) should be atleast 3 digits long!');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_JS_CVC2', '* Invalid CVC!');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_ERROR', 'Credit card data Error:');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_ORDERNO', 'Order no. ');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_ORDERDATE', 'Order date ');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEST_MODE', 'Test Mode');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEST_MODE_TITLE', 'Enable Test Mode');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEST_MODE_DESC', 'Do you want to activate test mode?');
  define('MODULE_PAYMENT_NOVALNET_CC3D_PROXY_TITLE', 'Proxy');
  define('MODULE_PAYMENT_NOVALNET_CC4D_PROXY_DESC', 'If you use a Proxy Server, enter the Proxy Server IP with port here (e.g. www.proxy.de:80)');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEST_ORDER_MESSAGE',"TESTORDER <br>");
?>
