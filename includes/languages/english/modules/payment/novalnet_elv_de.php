<?php
#########################################################
#                                                       #
#  ELVDE / DIRECT DEBIT payment method class            #
#  This module is used for real time processing of      #
#  German Bankdata of customers.                      	#
#                                                       #
#  Released under the GNU General Public License.       #
#  This free contribution made by request.              #
#  If you have found this script useful a small         #
#  recommendation as well as a comment on merchant form #
#  would be greatly appreciated.                        #
#                                                       #
#  Script : novalnet_elv_de.php                         #
#                                                       #
#########################################################

 define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_TITLE', '<nobr>Direct Debit German <a href="http://www.novalnet.com" target="_new"><img src="http://www.novalnet.com/img/ELV_Logo.png" alt="German direct debit"/></a></nobr>');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_DESCRIPTION', 'Pay safe and easy through Novalnet AG<BR>Before activating please enter the required Novalnet IDs in Edit mode!');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_LANG', 'EN');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_INFO', '');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_PUBLIC_TITLE', '<nobr>Direct Debit German <a href="http://www.novalnet.com" target="_new"><img src="http://www.novalnet.com/img/ELV_Logo.png" alt="German direct debit"/></a></nobr>');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_BANK_ACCOUNT_OWNER', 'Account holder:*');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_BANK_ACCOUNT_OWNER_LENGTH', '3');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_BANK_ACCOUNT_NUMBER', 'Account number:*');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_BANK_ACCOUNT_NUMBER_LENGTH', '5');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_BANK_CODE', 'Bankcode:*');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_BANK_CODE_LENGTH', '3');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_ACDC_INFO', "The <B><A HREF='javascript:show_acdc_info()' ONMOUSEOVER='show_acdc_info()'>ACDC-Check</A></B> Accepted");
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_ACDC_DIV', "<SCRIPT>var showbaby;function show_acdc_info(){var url=parent.location.href;url='http://www.novalnet.com/img/acdc_info.png';w='550';h='300';x=screen.availWidth/2-w/2;y=screen.availHeight/2-h/2;showbaby=window.open(url,'showbaby','toolbar=0,location=0,directories=0,status=0,menubar=0,resizable=1,width='+w+',height='+h+',left='+x+',top='+y+',screenX='+x+',screenY='+y);showbaby.focus();}function hide_acdc_info(){showbaby.close();}</SCRIPT>");
  
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_JS_NN_ID2_MISSING', '* Product-ID2 and/or Tariff-ID2 missing!');
  
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_IN_TEST_MODE', ' (in Testing mode)');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_NOT_CONFIGURED', ' (Not Configured)');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_GUEST_USER', 'Guest');
  
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_JS_NN_MISSING', '* Basic Parameter Missing!');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_JS_BANK_ACCOUNT_OWNER', '* Please enter valid account details!');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_JS_BANK_ACCOUNT_NUMBER', '* Please enter valid account details!');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_JS_BANK_CODE', '* Please enter valid account details!');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_JS_ACDC', '*Please enable ACDC Check!');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_JS_ACDC', '*Please enable ACDC Check!');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_ERROR', 'Account data Error:');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_CUST_INFORM', '"First of all we carry out a credit check, only after this check has returned a positive result will the order be processed. The goods can be shipped after debiting. In cases of non-payment / cancellation we will charge an administration fee of 10.00 € (Ten) and the case is then immediately transferred to the debt collection process."');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_ORDERNO', 'Order no.: ');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_ORDERDATE', 'Order date: ');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEST_MODE', 'Test Mode');
  //Start : Pin by call back  
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_BY_CALLBACK_TEL_REQ', 'Phone Number:*');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_BY_CALLBACK_SMS_REQ', 'Mobile Number:*');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_BY_CALLBACK_SMS_PIN', 'Enter your PIN Number:*');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_BY_CALLBACK_SMS_NEW_PIN', 'Forgot PIN? [New PIN Request]');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_BY_CALLBACK_SMS_TEL_NOTVALID', 'Please enter the Telephone / Mobilenumber!');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_BY_CALLBACK_SMS_PIN_NOTVALID', 'PIN you have entered is incorrect or empty!');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_BY_CALLBACK_SMS_CALL_MESSAGE', 'You will shortly receive a PIN via phone / SMS. Please enter the PIN in the appropriate text box.');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_BY_CALLBACK_MIN_LIMIT_TITLE', 'Minimum Amount Limit for PIN by Callback');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_BY_CALLBACK_MIN_LIMIT_DESC', 'Please enter minimum amount limit to enable "Pin by CallBack" modul (In Cents, e.g. 100,200)');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_INPUT_REQUEST_DESC',"You will shortly receive a PIN by phone / SMS. Please enter the PIN in the appropriate text box.");
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_BY_CALLBACK_SESSION_ERROR',"Your PIN session has expired. Please try again with a new call");
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_BY_CALLBACK_EMAIL_REQ', 'Email Address:*');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_BY_CALLBACK_EMAIL_NOTVALID', 'Please enter the E-Mail Address!'); 
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_EMAIL_INPUT_REQUEST_DESC',"We have sent a email, please answer");
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_AMOUNT_VARIATION_MESSAGE',"You have changed the cart amount after getting PIN number, please try again with new call");  
   define('MODULE_PAYMENT_NOVALNET_ELV_DE_AMOUNT_VARIATION_MESSAGE_EMAIL',"You have changed the order amount after getting e-mail, please try again with a new call");
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_EMAIL_PHONE_INPUT_REQUEST_DESC',"<b>* Please enter your phonenumber/email.</b>");  
  //End : Pin by call back
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TID_MESSAGE'," Novalnet Transaction ID : ");
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEST_ORDER_MESSAGE',"Test Order");
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_CURL_MESSAGE',"* You have to enable the CURL function on server, please check with your hosting provider about it!");
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_JS_BANK_ERROR_ACCOUNT_NUMBER', '* Please enter valid account details!');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_JS_BANK_CODE_ERROR', '* Please enter valid account details!');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_MAX_TIME_ERROR', '*Maximum number of PIN entries exceeded!');
  define('MODULE_PAYMENT_NOVALNET_INFORMATION_PAYMENT_DE', 'Your account will be debited upon delivery of goods.');  
 
?>
