<?php
//ini_set('display_errors',1);
#########################################################
#                                                       #
#  ELV_AT / DIRECT DEBIT payment method class           #
#  This module is used for real time processing of      #
#  Austrian Bankdata of customers.                      #
#                                                       #
#  Copyright (c) 2007 Novalnet AG                       #
#                                                       #
#  Released under the GNU General Public License        #
#  Novalnet_elv_at module Created By Dixon Rajdaniel    #
#                 Modified By Panneerselvam             #
#  This free contribution made by request.              #
#  If you have found this script usefull a small        #
#  recommendation as well as a comment on merchant form #
#  would be greatly appreciated.                        #
#                                                       #
#  Version : novalnet_elv_at.php vZenAT1.2.1 2008-08-26 #
#                                                       #
#########################################################


class novalnet_elv_at {

  var $code;
  var $title;
  var $description;
  var $enabled;
  var $proxy;    

  function novalnet_elv_at() {
    global $order;

    $this->code = 'novalnet_elv_at';
    $this->title = MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_TITLE;
    $this->description = MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_DESCRIPTION;
    $this->sort_order = MODULE_PAYMENT_NOVALNET_ELV_AT_SORT_ORDER;
    $this->enabled = ((MODULE_PAYMENT_NOVALNET_ELV_AT_STATUS == 'True') ? true : false);
    $this->proxy        = MODULE_PAYMENT_NOVALNET_ELV_AT_PROXY;    

    if ((int)MODULE_PAYMENT_NOVALNET_ELV_AT_ORDER_STATUS_ID > 0) {
      $this->order_status = MODULE_PAYMENT_NOVALNET_ELV_AT_ORDER_STATUS_ID;
    }
    if (is_object($order)) $this->update_status();
  }
  
  ### calculate zone matches and flag settings to determine whether this module should display to customers or not ###
  function update_status() {
    global $order, $db;

    if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_NOVALNET_ELV_AT_ZONE > 0) ) {
      $check_flag = false;
      $check = $db->Execute("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_NOVALNET_ELV_AT_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
      while (!$check->EOF) {
        if ($check->fields['zone_id'] < 1) {
          $check_flag = true;
          break;
        } elseif ($check->fields['zone_id'] == $order->billing['zone_id']) {
          $check_flag = true;
          break;
        }
        $check->MoveNext();
      }

      if ($check_flag == false) {
        $this->enabled = false;
      }
    }
  }
  
  ### JS validation which does error-checking of data-entry if this module is selected for use ###
  ### the fields to be cheked are (Bank Owner, Bank Account Number and Bank Code Lengths)      ###
  ### currently this function is not in use ###
  // @return string
  function javascript_validation() {
    return false;
  }
  
  ### Builds set of input fields for collecting Bankdetail info ###
  // @return array
  function selection() {
    global $order;

    $onFocus = ' onfocus="methodSelect(\'pmt-' . $this->code . '\')"';

    $bank_account = '';
    if (isset($_POST['bank_account_at'])) {$bank_account = $_POST['bank_account_at'];}
    if(!$bank_account and isset($_GET['bank_account_at'])) {$bank_account = $_GET['bank_account_at'];}
    $bank_code = '';
    if (isset($_POST['bank_code_at'])) {$bank_code = $_POST['bank_code_at'];}
    if(!$bank_code and isset($_GET['bank_code_at']) ) {$bank_code = $_GET['bank_code_at'];}

    $selection = array('id' => $this->code,
                       'module' => $this->title,
                       'fields' => array(array('title' => MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_BANK_ACCOUNT_OWNER,
                                               'field' => zen_draw_input_field('bank_account_holder_at', $order->billing['firstname'] . ' ' . $order->billing['lastname'], 'id="'.$this->code.'-bank_account_holder_at"' . $onFocus),
                                               'tag' => $this->code.'-bank_account_holder_at'),
                                         array('title' => MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_BANK_ACCOUNT_NUMBER,
                                               'field' => zen_draw_input_field('bank_account_at', $_SESSION['bank_account_at'], 'id="' . $this->code . '-bank_account_at"' . $onFocus),
                                               'tag' => $this->code . '-bank_account_at'),
                                         array('title' => MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_BANK_CODE,
            					'field' => zen_draw_input_field('bank_code_at', $_SESSION['bank_code_at'], 'id="' . $this->code . '-bank_code_at"' . $onFocus),
						'tag' => $this->code . '-bank_code_at')
		               ));

    return $selection;
  }

  ### Precheck to Evaluate the Bank Datas ###
  function pre_confirmation_check() {
    global $_POST, $messageStack;
    $_POST['bank_account_at'] = trim($_POST['bank_account_at']);
    $_POST['bank_code_at'] = trim($_POST['bank_code_at']);
	$_SESSION['bank_account_at'] = $_POST['bank_account_at'];
	$_SESSION['bank_code_at'] = $_POST['bank_code_at'];	
    $error = '';
	
	
 if (defined('MODULE_PAYMENT_NOVALNET_ELV_AT_MANUAL_CHECK_LIMIT') and MODULE_PAYMENT_NOVALNET_ELV_AT_MANUAL_CHECK_LIMIT){
      if ( (!defined('MODULE_PAYMENT_NOVALNET_ELV_AT_PRODUCT_ID2') or !MODULE_PAYMENT_NOVALNET_ELV_AT_PRODUCT_ID2 or preg_match('/[^\d]/', MODULE_PAYMENT_NOVALNET_ELV_AT_PRODUCT_ID2)) or (!defined('MODULE_PAYMENT_NOVALNET_ELV_AT_TARIFF_ID2') or !MODULE_PAYMENT_NOVALNET_ELV_AT_TARIFF_ID2 or preg_match('/[^\d]/', MODULE_PAYMENT_NOVALNET_ELV_AT_TARIFF_ID2))){
          $error = 'Product-ID2 and/or Tariff-ID2 missing';
      }
    }

    if(!$_POST['bank_account_holder_at'] || strlen($_POST['bank_account_holder_at'])<MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_BANK_ACCOUNT_OWNER_LENGTH) $error = MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_JS_BANK_ACCOUNT_OWNER;
    elseif(!$_POST['bank_account_at'] || strlen($_POST['bank_account_at'])<MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_BANK_ACCOUNT_NUMBER_LENGTH) $error = MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_JS_BANK_ACCOUNT_NUMBER;
    elseif(!$_POST['bank_code_at'] || strlen($_POST['bank_code_at'])<MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_BANK_CODE_LENGTH) $error = MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_JS_BANK_CODE;
	
	if(!MODULE_PAYMENT_NOVALNET_ELV_AT_VENDOR_ID || !MODULE_PAYMENT_NOVALNET_ELV_AT_AUTH_CODE || !MODULE_PAYMENT_NOVALNET_ELV_AT_PRODUCT_ID || !MODULE_PAYMENT_NOVALNET_ELV_AT_TARIFF_ID)
    {
      $error = MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_JS_NN_MISSING;
    }
	
    if($error!='') {
      $payment_error_return = 'payment_error=' . $this->code . '&bank_account_holder_at=' . urlencode($_POST['bank_account_holder_at']) . '&bank_account_at=' . $_POST['bank_account_at'] . '&bank_code_at=' . $_POST['bank_code_at'];

      $messageStack->add_session('checkout_payment', $error . '<!-- ['.$this->code.'] -->', 'error');
      zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
    }
  }

  ### Display Bank Information on the Checkout Confirmation Page ###
  // @return array
  function confirmation() {
    global $_POST;

      $cardnoLength = strlen(str_replace(' ','',$_POST['bank_account_at']));
      $crdNo = str_replace(' ','',$_POST['bank_account_at']);
      $cardnoInfo = '';
      $chkLength = $cardnoLength-4;
      for($i=0;$i<$cardnoLength;$i++){
	if($i >= $chkLength){
	$cardnoInfo .= '*';
	}else{
	$cardnoInfo .= $crdNo[$i];
	}
      }

      $cardnoLength1 = strlen(str_replace(' ','',$_POST['bank_code_at']));
      $crdNo1 = str_replace(' ','',$_POST['bank_code_at']);
      $cardnoInfo1 = '';
      $chkLength1 = $cardnoLength1-3;
      for($i=0;$i<$cardnoLength1;$i++){
	if($i >= $chkLength1){
	$cardnoInfo1 .= '*';
	}else{
	$cardnoInfo1 .= $crdNo1[$i];
	}
      }


    $confirmation = array('fields' => array(array('title' => MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_BANK_ACCOUNT_OWNER,
                          'field' => $_POST['bank_account_holder_at']),
                    array('title' => MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_BANK_ACCOUNT_NUMBER,
                          'field' => $cardnoInfo),
                    array('title' => MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_BANK_CODE,
			   'field' => $cardnoInfo1)
                          ));

    return $confirmation;
  }

  ### Build the data and actions to process when the "Submit" button is pressed on the order-confirmation screen. ###
  ### These are hidden fields on the checkout confirmation page ###
  // @return string
  function process_button() {
    global $_POST;

    $process_button_string = zen_draw_hidden_field('bank_account_holder_at', $_POST['bank_account_holder_at']) .
                             zen_draw_hidden_field('bank_account_at', $_POST['bank_account_at']) .
                             zen_draw_hidden_field('bank_code_at', $_POST['bank_code_at']);

    return $process_button_string;
  }

  ### Store the BANK info to the order ###
  ### This sends the data to the payment gateway for processing and Evaluates the Bankdatas for acceptance and the validity of the Bank Details ###
  function before_process() {
    global $_POST, $order, $db, $currencies, $messageStack;

    $test_mode = (strtolower(MODULE_PAYMENT_NOVALNET_ELV_AT_TEST_MODE) == 'true' or MODULE_PAYMENT_NOVALNET_ELV_AT_TEST_MODE == '1')? 1: 0;



    $order->info['bank_account_at'] = $_POST['bank_account_at'];
    $order->info['bank_account_holder_at'] = $_POST['bank_account_holder_at'];
    $order->info['bank_code_at'] = $_POST['bank_code_at'];

    $len = strlen($_POST['bank_account_at']);
    $this->novalnet_elv_at_middle = substr($_POST['bank_account_at'], 4, ($len-8));
    if ( (defined('MODULE_PAYMENT_NOVALNET_ELV_AT_EMAIL')) && (zen_validate_email(MODULE_PAYMENT_NOVALNET_ELV_AT_EMAIL)) ) {
      $order->info['bank_account_at'] = substr($_POST['bank_account_at'], 0, 4) . str_repeat('X', (strlen($_POST['bank_account_at']) - 8)) . substr($_POST['bank_account_at'], -4);
    }

    #Get the required additional customer details from DB
    $customer_values = $db->Execute("SELECT customers_gender, customers_firstname, customers_lastname, customers_dob, customers_email_address, customers_telephone, customers_fax, customers_email_format FROM ". TABLE_CUSTOMERS . " WHERE customers_id='".(int)$_SESSION['customer_id']."'");
    while(!$customer_values->EOF)
    {
       $customer_values->MoveNext();
    }
    list($customer_values->fields['customers_dob'], $extra) = explode(' ', $customer_values->fields['customers_dob']);

    ### Process the payment to paygate ##
    $url = 'https://payport.novalnet.de/paygate.jsp';


if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status_add_tax_ot'] == 1) {
$totalamount=$order->info['total'] + $order->info['tax'];
} else { 
$totalamount=$order->info['total'];
}
   $amount =sprintf('%.2f', $totalamount);
    if(preg_match('/[,.]$/', $amount))
    {
      $amount = $amount . '00';
    }
    else if(preg_match('/[,.][0-9]$/', $amount))
    {
      $amount = $amount . '0';
    }
    $amount = preg_replace('/^0+/', '', $amount);
    $amount = str_replace('.', '', $amount);
    $amount = str_replace(',', '', $amount);
    $product_id = MODULE_PAYMENT_NOVALNET_ELV_AT_PRODUCT_ID;
    $tariff_id = MODULE_PAYMENT_NOVALNET_ELV_AT_TARIFF_ID;
    $manual_check_limit = trim(MODULE_PAYMENT_NOVALNET_ELV_AT_MANUAL_CHECK_LIMIT);
    $manual_check_limit = str_replace(',', '', $manual_check_limit);
    $manual_check_limit = str_replace('.', '', $manual_check_limit);

    if($manual_check_limit && $amount>=$manual_check_limit)
    {
      $product_id = MODULE_PAYMENT_NOVALNET_ELV_AT_PRODUCT_ID2;
      $tariff_id = MODULE_PAYMENT_NOVALNET_ELV_AT_TARIFF_ID2;
    }

    $user_ip = $this->getRealIpAddr();

    $urlparam = 'vendor='.MODULE_PAYMENT_NOVALNET_ELV_AT_VENDOR_ID.'&product='.$product_id.'&key=8&tariff='.$tariff_id.'&auth_code='.MODULE_PAYMENT_NOVALNET_ELV_AT_AUTH_CODE.'&currency='.$order->info['currency'];
    $urlparam .= '&amount='.$amount.'&bank_account_holder='.$_POST['bank_account_holder_at'].'&bank_account='.$_POST['bank_account_at'];
    $urlparam .= '&bank_code='.$_POST['bank_code_at'].'&first_name='.$order->billing['firstname'].'&last_name='.$order->billing['lastname'];
    $urlparam .= '&street='.$order->billing['street_address'].'&city='.$order->billing['city'].'&zip='.$order->billing['postcode'];
    $urlparam .= '&country='.$order->billing['country']['iso_code_2'].'&email='.$customer_values->fields['customers_email_address'];
    $urlparam .= '&birth_date='.$customer_values->fields['customers_dob'].'&tel='.$customer_values->fields['customers_telephone'];
    $urlparam .= '&fax='.$customer_values->fields['customers_fax'].'&gender='.$customer_values->fields['customers_gender'];
    $urlparam .= '&search_in_street=1&remote_ip='.$user_ip.'&input1=email_format';
    $urlparam .= '&input_val1='.$customer_values->fields['customers_email_format'].'&language='.MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_LANG; 

    list($errno, $errmsg, $data) = $this->perform_https_request($url, $urlparam);
// echo '<br> Result: '. print_r($data); exit;
    $aryResponse = array();
    #capture the result and message and other parameters from response data '$data' in an array
    $aryPaygateResponse = explode('&', $data);
    foreach($aryPaygateResponse as $key => $value)
    {
       if($value!="")
       {
          $aryKeyVal = explode("=",$value);
          $aryResponse[$aryKeyVal[0]] = $aryKeyVal[1];
       }
    }
//echo '<br>Result :'.print_r($aryResponse); exit;
    if($aryResponse['status']==100)
    {
		if( $this->order_status ) {
				$order->info['order_status'] = $this->order_status;
		}
      ### Passing through the Transaction ID from Novalnet's paygate into order-info ###
    $test_mode = (strtolower(MODULE_PAYMENT_NOVALNET_ELV_AT_TEST_MODE) == 'true' or MODULE_PAYMENT_NOVALNET_ELV_AT_TEST_MODE == '1')? 1: 0;
    $test_mode_value=( $aryResponse['test_mode'] == 1) ? $aryResponse['test_mode'] : $test_mode;
    if ($test_mode_value){
    $order->info['comments'] .= MODULE_PAYMENT_NOVALNET_ELV_AT_TEST_ORDER_MESSAGE;
    }
      $order->info['comments'] .= 'Novalnet Transaction ID : '.$aryResponse['tid'];
   $_SESSION['nn_tid']=$aryResponse['tid'];  
    }
    else
    {
      ### Passing through the Error Response from Novalnet's paygate into order-info ###
      $order->info['comments'] .= 'Novalnet Error Code : '.$aryResponse['status'].', Novalnet Error Message : '.$aryResponse['status_desc'];

      $payment_error_return = 'payment_error=' . $this->code;

      $messageStack->add_session('checkout_payment', $aryResponse['status_desc'] . '<!-- ['.$this->code.'] -->', 'error');
      zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
    }

  }

  ### Realtime accesspoint for communication to the Novalnet paygate ###
  function perform_https_request($nn_url, $urlparam)
  {
      $debug = 0;#set it to 1 if you want to activate the debug mode

      if($debug) print "<BR>perform_https_request: $nn_url<BR>\n\r\n";
      if($debug) print "perform_https_request: $urlparam<BR>\n\r\n";

      ## some prerquisites for the connection
      $ch = curl_init($nn_url);
      curl_setopt($ch, CURLOPT_POST, 1);  // a non-zero parameter tells the library to do a regular HTTP post.
      curl_setopt($ch, CURLOPT_POSTFIELDS, $urlparam);  // add POST fields
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);  // don't allow redirects
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  // decomment it if you want to have effective ssl checking
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  // decomment it if you want to have effective ssl checking
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  // return into a variable
      curl_setopt($ch, CURLOPT_TIMEOUT, 240);  // maximum time, in seconds, that you'll allow the CURL functions to take
      if ($this->proxy) {curl_setopt($ch, CURLOPT_PROXY, $this->proxy); }

      ## establish connection
      $data = curl_exec($ch);
      $data = $this->ReplaceSpecialGermanChars($data);

      ## determine if there were some problems on cURL execution
      $errno = curl_errno($ch);
      $errmsg = curl_error($ch);

      ###bug fix for PHP 4.1.0/4.1.2 (curl_errno() returns high negative value in case of successful termination)
      if($errno < 0) $errno = 0;
      ##bug fix for PHP 4.1.0/4.1.2

      if($debug)
      {
        print_r(curl_getinfo($ch));
        echo "\n<BR><BR>\n\n\nperform_https_request: cURL error number:" . $errno . "\n<BR>\n\n";
        echo "\n\n\nperform_https_request: cURL error:" . $errmsg . "\n<BR>\n\n";
      }

      #close connection
      curl_close($ch);

      ## read and return data from novalnet paygate
      if($debug) print "<BR>\n\n" . $data . "\n<BR>\n\n";

      return array ($errno, $errmsg, $data);
  }
  
  ### replace the Special German Charectors ###
  function ReplaceSpecialGermanChars($string)
  {
     $what = array("ä", "ö", "ü", "Ä", "Ö", "Ü", "ß");
     $how = array("ae", "oe", "ue", "Ae", "Oe", "Ue", "ss");

     $string = str_replace($what, $how, $string);

     return $string;
  }  

  ### get the real Ip Adress of the User ###

  function getRealIpAddr()
  {
     if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
     {
       $ip=$_SERVER['HTTP_CLIENT_IP'];
     }
     elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
     {
       $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
     }
     else
     {
       $ip=$_SERVER['REMOTE_ADDR'];
     }
  /* 
    $num="(\\d|[1-9]\\d|1\\d\\d|2[0-4]\\d|25[0-5])";
    if (!preg_match("/^$num\\.$num\\.$num\\.$num$/", $ip)){
    $ip='127.0.0.1';

*/
    return $ip;

 //   }


  }
   ### Send additional information about bankdata via email to the store owner ###
  ### Send the order detail to Novalnet ###
  function after_process() {
    global $order, $customer_id, $insert_id,$db;

    $product_id = MODULE_PAYMENT_NOVALNET_ELV_AT_PRODUCT_ID;
    $tariff_id = MODULE_PAYMENT_NOVALNET_ELV_AT_TARIFF_ID;
    ### Pass the Order Reference to paygate ##
    $url = 'https://payport.novalnet.de/paygate.jsp';
    $urlparam = 'vendor='.MODULE_PAYMENT_NOVALNET_ELV_AT_VENDOR_ID.'&product='.$product_id.'&key=8&tariff='.$tariff_id;
    $urlparam .= '&auth_code='.MODULE_PAYMENT_NOVALNET_ELV_AT_AUTH_CODE.'&status=100&tid='.$_SESSION['nn_tid'].'&reference=BNR-'.$insert_id.'&vwz3='.$insert_id.'&vwz3_prefix='.MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_ORDERNO.'&vwz4='.date('Y.m.d').'&vwz4_prefix='.MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_ORDERDATE;
	$urlparam .= '&order_no='.$insert_id;

    list($errno, $errmsg, $data) = $this->perform_https_request($url, $urlparam);
    #$this->debug2($urlparam, $filename='xtcomm_urlparam');
    unset($_SESSION['nn_tid']);
	unset($_SESSION['bank_account_at']);
	unset($_SESSION['bank_code_at']);
    ### Implement here the Emailversand and further functions, incase if you want to send a own email ###
    return false;
  }

  ### Store additional order information ###
  ### not in use ###
  // @param int $zf_order_id
  function after_order_create($zf_order_id) {
    return false;
  }

  ### Used to display error message details ###
  // @return array
  function get_error() {
    global $_GET;

    $error = array('title' => MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_ERROR,
                   'error' => stripslashes(urldecode($_GET['error'])));

    return $error;
  }

  ### Check to see whether module is installed ###
  // @return boolean
  function check() {
    global $db;
    if (!isset($this->_check)) {
      $check_query = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_NOVALNET_ELV_AT_STATUS'");
      $this->_check = $check_query->RecordCount();
    }
    return $this->_check;
  }

  ### Install the payment module and its configuration settings ###
  function install() {
    global $db;
    $db->Execute("alter table ".TABLE_ORDERS." modify payment_method varchar(250)");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Allowed zones', 'MODULE_PAYMENT_NOVALNET_ELV_AT_ALLOWED', '', 'Please enter the zones <b>separately</b> which should be allowed to use this modul (e. g. AT,DE (leave empty if you want to allow all zones))', '6', '0', now())");  
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable ELV-AT Module', 'MODULE_PAYMENT_NOVALNET_ELV_AT_STATUS', 'True', 'Do you want to activate the Austrian Direct Debit Method(ELV-AT) of Novalnet AG?', '6', '1', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Test Mode', 'MODULE_PAYMENT_NOVALNET_ELV_AT_TEST_MODE', 'True', 'Do you want to enable the test mode?', '6', '2', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Please contact sales@novalnet.de if you do not have any of the following Novalnet IDs!<BR><P>Wenn Sie keine oder irgendeine der folgenden Novalnet IDs nicht haben sollten, bitte sich an sales@novalnet.de wenden!<BR><P>Novalnet Merchant ID', 'MODULE_PAYMENT_NOVALNET_ELV_AT_VENDOR_ID', '', 'Your Merchant ID of Novalnet', '6', '3', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Novalnet Authorisation Code', 'MODULE_PAYMENT_NOVALNET_ELV_AT_AUTH_CODE', '', 'Your Authorisation Code of Novalnet', '6', '4', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Novalnet Product ID', 'MODULE_PAYMENT_NOVALNET_ELV_AT_PRODUCT_ID', '', 'Your Product ID of Novalnet', '6', '5', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Novalnet Tariff ID', 'MODULE_PAYMENT_NOVALNET_ELV_AT_TARIFF_ID', '', 'Your Tariff ID of Novalnet', '6', '6', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Booking Amount Limit', 'MODULE_PAYMENT_NOVALNET_ELV_AT_MANUAL_CHECK_LIMIT', '', 'The amount from which the manual booking control should occur', '6', '7', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Novalnet Product ID2', 'MODULE_PAYMENT_NOVALNET_ELV_AT_PRODUCT_ID2', '', 'Your 2nd Product ID of Novalnet', '6', '8', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Novalnet Tariff ID2', 'MODULE_PAYMENT_NOVALNET_ELV_AT_TARIFF_ID2', '', 'Your 2nd Tariff ID of Novalnet', '6', '9', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Information to the Customer', 'MODULE_PAYMENT_NOVALNET_ELV_AT_INFO', '','will be shown on the payment formula', '6', '10', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort order of display.', 'MODULE_PAYMENT_NOVALNET_ELV_AT_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '11', now())");
     $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('Set Order Status', 'MODULE_PAYMENT_NOVALNET_ELV_AT_ORDER_STATUS_ID', '0', 'Set the status of orders made with this payment module to this value', '6', '12', 'zen_cfg_pull_down_order_statuses(', 'zen_get_order_status_name', now())");
     $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Payment Zone', 'MODULE_PAYMENT_NOVALNET_ELV_AT_ZONE', '0', 'If a zone is selected, only enable this payment method for that zone.', '6', '13', 'zen_get_zone_class_title', 'zen_cfg_pull_down_zone_classes(', now())");
     $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Proxy', 'MODULE_PAYMENT_NOVALNET_ELV_AT_PROXY', '0', 'If you use a Proxy Server, enter the Proxy Server IP here (e.g. www.proxy.de:80)', '6', '14', now())");     
  }
   
  ### Remove the module and all its settings ###
  function remove() {
    global $db;
    $db->Execute("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
  }

  ### Internal list of configuration keys used for configuration of the module ###
  // @return array
  function keys() {
    return array('MODULE_PAYMENT_NOVALNET_ELV_AT_ALLOWED', 'MODULE_PAYMENT_NOVALNET_ELV_AT_STATUS', 'MODULE_PAYMENT_NOVALNET_ELV_AT_TEST_MODE', 'MODULE_PAYMENT_NOVALNET_ELV_AT_VENDOR_ID', 'MODULE_PAYMENT_NOVALNET_ELV_AT_AUTH_CODE', 'MODULE_PAYMENT_NOVALNET_ELV_AT_PRODUCT_ID', 'MODULE_PAYMENT_NOVALNET_ELV_AT_TARIFF_ID', 'MODULE_PAYMENT_NOVALNET_ELV_AT_MANUAL_CHECK_LIMIT', 'MODULE_PAYMENT_NOVALNET_ELV_AT_PRODUCT_ID2', 'MODULE_PAYMENT_NOVALNET_ELV_AT_TARIFF_ID2', 'MODULE_PAYMENT_NOVALNET_ELV_AT_INFO', 'MODULE_PAYMENT_NOVALNET_ELV_AT_SORT_ORDER', 'MODULE_PAYMENT_NOVALNET_ELV_AT_ORDER_STATUS_ID', 'MODULE_PAYMENT_NOVALNET_ELV_AT_ZONE','MODULE_PAYMENT_NOVALNET_ELV_AT_PROXY');
  }


}

?>
