<?php

#########################################################
#                                                       #
#  PREPAYMENT payment method class                      #
#  This module is used for real time processing of      #
#  PREPAYMENT payment of customers.                     #
#                                                       #
#  Copyright (c) 2009 Novalnet AG                       #
#                                                       #
#  Released under the GNU General Public License        #
#  Novalnet_prepayment module Created By Dixon Rajdaniel#
#  This free contribution made by request.              #
#  If you have found this script usefull a small        #
#  recommendation as well as a comment on merchant form #
#  would be greatly appreciated.                        #
#                                                       #
# Ver : novalnet_prepayment.php vzenPP1.3.1 2009-03-01  #
#                                                       #
#########################################################


class novalnet_prepayment {


  var $code;
  var $title;
  var $description;
  var $enabled;
  var $blnDebug = false; #todo: set to false for live system
  var $proxy;  

  function novalnet_prepayment() {
    global $order;
	if ($this->blnDebug) {$this->debug2(__FUNCTION__);}

    $this->code = 'novalnet_prepayment';
    $this->title = MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_TITLE;
    $this->public_title = MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_PUBLIC_TITLE;
    $this->description = MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_DESCRIPTION;
    $this->sort_order = MODULE_PAYMENT_NOVALNET_PREPAYMENT_SORT_ORDER;
    $this->enabled = ((MODULE_PAYMENT_NOVALNET_PREPAYMENT_STATUS == 'True') ? true : false);
    $this->proxy        = MODULE_PAYMENT_NOVALNET_PREPAYMENT_PROXY;    

    if ((int)MODULE_PAYMENT_NOVALNET_PREPAYMENT_ORDER_STATUS_ID > 0) {
      $this->order_status = MODULE_PAYMENT_NOVALNET_PREPAYMENT_ORDER_STATUS_ID;
    }

    if (is_object($order)) $this->update_status();
  }
  
  ### calculate zone matches and flag settings to determine whether this module should display to customers or not ###
  function update_status() {
    global $order, $db;
	if ($this->blnDebug) {$this->debug2(__FUNCTION__);}

    if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_NOVALNET_PREPAYMENT_ZONE > 0) ) {
      $check_flag = false;
      $check = $db->Execute("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_NOVALNET_PREPAYMENT_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
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
	if ($this->blnDebug) {$this->debug2(__FUNCTION__);}
    return false;
  }
  
  ### Builds set of input fields for collecting Bankdetail info ###
  // @return array
  function selection() {
    global $xtPrice, $order, $HTTP_POST_VARS, $_POST;
	if ($this->blnDebug) {$this->debug2(__FUNCTION__);}

    $onFocus = '';
    if(count($HTTP_POST_VARS)==0 || $HTTP_POST_VARS=='') $HTTP_POST_VARS = $_POST;

    $selection = array('id' => $this->code,
                       'module' => $this->title,
			'fields' => array(array('title' => '', 'field' => MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_BANK_INFO))
		      );

    if(function_exists('get_percent'))
    {
        $selection['module_cost'] = $GLOBALS['ot_payment']->get_percent($this->code);
    }

    return $selection;
  }

  ### Precheck to Evaluate the Bank Datas ###
  function pre_confirmation_check() {
    global $HTTP_POST_VARS, $_POST, $messageStack;
	if ($this->blnDebug) {$this->debug2(__FUNCTION__);}

    if(count($HTTP_POST_VARS)==0 || $HTTP_POST_VARS=='') $HTTP_POST_VARS = $_POST;
    #var_dump($HTTP_POST_VARS); exit;

    $error = '';

    if(!MODULE_PAYMENT_NOVALNET_PREPAYMENT_VENDOR_ID || !MODULE_PAYMENT_NOVALNET_PREPAYMENT_AUTH_CODE || !MODULE_PAYMENT_NOVALNET_PREPAYMENT_PRODUCT_ID || !MODULE_PAYMENT_NOVALNET_PREPAYMENT_TARIFF_ID)
    {
      $error = MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_JS_NN_MISSING;
    }

    if($error!='') {
		$payment_error_return = 'payment_error=' . $this->code;
		$messageStack->add_session('checkout_payment', $error . '<!-- ['.$this->code.'] -->', 'error');	  
		zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
    }
  }

  ### Display Bank Information on the Checkout Confirmation Page ###
  // @return array
  function confirmation() {
    global $HTTP_POST_VARS, $_POST, $order;
    $_SESSION['nn_total'] = $order->info['total'];
	if ($this->blnDebug) {$this->debug2(__FUNCTION__);}
    if(count($HTTP_POST_VARS)==0 || $HTTP_POST_VARS=='') $HTTP_POST_VARS = $_POST;

    $confirmation = array('fields' => array(array('title' => $this->public_title, 'field' => MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_BANK_INFO)));

    return $confirmation;
  }

  ### Build the data and actions to process when the "Submit" button is pressed on the order-confirmation screen. ###
  ### These are hidden fields on the checkout confirmation page ###
  // @return string
  function process_button() {
    global $HTTP_POST_VARS, $_POST;
	if ($this->blnDebug) {$this->debug2(__FUNCTION__);}
    if(count($HTTP_POST_VARS)==0 || $HTTP_POST_VARS=='') $HTTP_POST_VARS = $_POST;

    return $process_button_string;
  }

  ### Store the BANK info to the order ###
  ### This sends the data to the payment gateway for processing and Evaluates the Bankdatas for acceptance and the validity of the Bank Details ###
  function before_process() {
    global $HTTP_POST_VARS, $_POST, $order, $xtPrice, $currencies, $customer_id, $db, $messageStack;
	if ($this->blnDebug) {$this->debug2(__FUNCTION__);}

    if(count($HTTP_POST_VARS)==0 || $HTTP_POST_VARS=='') $HTTP_POST_VARS = $_POST;

    #Get the required additional customer details from DB
    $customer = $db->Execute("SELECT customers_gender, customers_dob, customers_fax FROM ". TABLE_CUSTOMERS . " WHERE customers_id='". (int)$_SESSION['customer_id']."'");

    if ($customer->RecordCount() > 0){
      $customer = $customer->fields;
    }
    list($customer['customers_dob'], $extra) = explode(' ', $customer['customers_dob']);

    ### Process the payment to paygate ##
    $url = 'https://payport.novalnet.de/paygate.jsp';

//    $amount = $_SESSION['nn_total'];

    if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status_add_tax_ot'] == 1) {
	$totalamount=$order->info['total'] + $order->info['tax'];
	} else { 
	$totalamount=$order->info['total'];
    }
    $p_amount =sprintf('%.2f', $totalamount);

    $amount = number_format($p_amount * $currencies->currencies['EUR']['value'], $currencies->currencies['EUR']['decimal_places']);


    if (preg_match('/[^\d\.]/', $amount) or !$amount){
      ### $amount contains some unallowed chars or empty ###
      $err                      = '$amount ('.$amount.') is empty or has a wrong format';
      $order->info['comments'] .= 'Novalnet Error Message : '.$err;
      $payment_error_return     = 'payment_error='.$this->code;
	  $messageStack->add_session('checkout_payment', $err . '<!-- ['.$this->code.'] -->', 'error');		  
      zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
    }
    $amount = preg_replace('/^0+/', '', $amount);
    $orig_amount = $amount;
    $amount = sprintf('%0.2f', $amount);
    $amount = str_replace('.', '', $amount);
    #echo __CLASS__.' : '.$order->info['total']." <=> $amount<hr />";exit;

    $product_id = MODULE_PAYMENT_NOVALNET_PREPAYMENT_PRODUCT_ID;
    $tariff_id = MODULE_PAYMENT_NOVALNET_PREPAYMENT_TARIFF_ID;

    $user_ip = $this->getRealIpAddr();

    $urlparam = 'vendor='.MODULE_PAYMENT_NOVALNET_PREPAYMENT_VENDOR_ID.'&product='.$product_id.'&key=27&tariff='.$tariff_id;
    $urlparam .= '&auth_code='.MODULE_PAYMENT_NOVALNET_PREPAYMENT_AUTH_CODE.'&currency='.$order->info['currency'];
    $urlparam .= '&amount='.$amount.'&invoice_type=PREPAYMENT';
    $urlparam .= '&first_name='.$this->html_to_utf8($order->customer['firstname']).'&last_name='.$this->html_to_utf8($order->customer['lastname']);
    $urlparam .= '&street='.$this->html_to_utf8($order->customer['street_address']).'&city='.$this->html_to_utf8($order->customer['city']).'&zip='.$order->customer['postcode'];
    $urlparam .= '&country='.$order->customer['country']['iso_code_2'].'&email='.$order->customer['email_address'];
    $urlparam .= '&search_in_street=1&tel='.$order->customer['telephone'].'&remote_ip='.$user_ip;
    $urlparam .= '&gender='.$customer['customers_gender'].'&birth_date='.$customer['customers_dob'].'&fax='.$customer['customers_fax'];
    $urlparam .= '&language='.MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_LANG;
    $test_mode = (strtolower(MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEST_MODE) == 'true' or MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEST_MODE == '1')? 1: 0;
    $urlparam .= '&test_mode='.$test_mode;

    #print str_replace('&', '<br />', "$urlparam"); exit;
    list($errno, $errmsg, $data) = $this->perform_https_request($url, $urlparam);

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

    #Get the type of the comments field on TABLE_ORDERS
    $customer = $db->Execute("SHOW FIELDS FROM ". TABLE_ORDERS_STATUS_HISTORY . " WHERE FIELD='comments'");
    if ($customer->RecordCount() > 0){
      $customer = $customer->fields;
    }
    if(strtolower($customer['Type']) != 'text')
    {
	    ### ALTER TABLE ORDERS modify the column comments ###
	    $db->Execute("ALTER TABLE ". TABLE_ORDERS_STATUS_HISTORY . " MODIFY comments text");
    }

    if($aryResponse['status']==100)
    {
      $_SESSION['nn_tid'] = $aryResponse['tid'];
      $_SESSION['t_id']=$aryResponse['tid'];  
      $old_comments = $order->info['comments'];
	  $order->info['comments'] = '';
      $amount = str_replace('.', ',', sprintf("%.2f", $amount/100));
	  if( $this->order_status ) {
		$order->info['order_status'] = $this->order_status;
	  }
      $test_mode = (strtolower(MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEST_MODE) == 'true' or MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEST_MODE == '1')? 1: 0;
      $test_mode_value=( $aryResponse['test_mode'] == 1) ? $aryResponse['test_mode'] : $test_mode;
      if ($test_mode_value){
      $order->info['comments'] .= MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEST_ORDER_MESSAGE;
      }

      $order->info['comments'] .= '\n'.MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_TRANSFER_INFO.'\n\n';
      $order->info['comments'] .= MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_BANK_ACCOUNT_OWNER.' NOVALNET AG\n';
      $order->info['comments'] .= MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_BANK_ACCOUNT_NUMBER.' '.$aryResponse['invoice_account'].'\n';
      $order->info['comments'] .= MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_BANK_CODE.' '.$aryResponse['invoice_bankcode'].'\n';
      $order->info['comments'] .= MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_BANK_BANK.' '.$aryResponse['invoice_bankname'].', '.$aryResponse['invoice_bankplace'].'\n\n';
      $order->info['comments'] .= MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_BANK_IBAN.' '.$aryResponse['invoice_iban'].'\n';
      $order->info['comments'] .= MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_BANK_BIC.' '.$aryResponse['invoice_bic'].'\n\n';
      $order->info['comments'] .= MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_AMOUNT.' '.$orig_amount.' '.$order->info['currency'].'\n';
      $order->info['comments'] .= MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_REFERENCE.'TID '.$aryResponse['tid'].'\n';
      $order->info['comments'] .= MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_REFERENCE_INFO.'\n';

      $order->info['comments'] .= $old_comments; 

      ### WRITE THE PREPAYMENT BANK DATA ON SESSION ###     
      $_SESSION['nn_invoice_account'] = $aryResponse['invoice_account'];
      $_SESSION['nn_invoice_bankcode'] = $aryResponse['invoice_bankcode'];
      $_SESSION['nn_invoice_iban'] = $aryResponse['invoice_iban'];
      $_SESSION['nn_invoice_bic'] = $aryResponse['invoice_bic'];
      $_SESSION['nn_invoice_bankname'] = $aryResponse['invoice_bankname'];
      $_SESSION['nn_invoice_bankplace'] = $aryResponse['invoice_bankplace'];
    }
    else
    {
      ### Passing through the Error Response from Novalnet's paygate into order-info ###
      $order->info['comments'] .= 'Novalnet Error Code : '.$aryResponse['status'].', Novalnet Error Message : '.$aryResponse['status_desc'];
 
      $payment_error_return = 'payment_error=' . $this->code;
	  $messageStack->add_session('checkout_payment', $aryResponse['status_desc'] . '<!-- ['.$this->code.'] -->', 'error');	
      zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
    }

    return;

  }

  ### Realtime accesspoint for communication to the Novalnet paygate ###
  function perform_https_request($nn_url, $urlparam)
  {
	  if ($this->blnDebug) {$this->debug2(__FUNCTION__);}
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

  function isPublicIP($value)
  {
        if(!$value || count(explode('.',$value))!=4) return false;
        return !preg_match('~^((0|10|172\.16|192\.168|169\.254|255|127\.0)\.)~', $value);
  }

  ### get the real Ip Adress of the User ###
  function getRealIpAddr()
  {
        if($this->isPublicIP($_SERVER['HTTP_X_FORWARDED_FOR'])) return $_SERVER['HTTP_X_FORWARDED_FOR'];
        if($iplist=explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            if($this->isPublicIP($iplist[0])) return $iplist[0];
        }
        if ($this->isPublicIP($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
        if ($this->isPublicIP($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
        if ($this->isPublicIP($_SERVER['HTTP_FORWARDED_FOR']) ) return $_SERVER['HTTP_FORWARDED_FOR'];

        return $_SERVER['REMOTE_ADDR'];
  }

 
  ### replace the Special German Charectors ###
  function ReplaceSpecialGermanChars($string)
  {
     $what = array("ä", "ö", "ü", "Ä", "Ö", "Ü", "ß");
     $how = array("ae", "oe", "ue", "Ae", "Oe", "Ue", "ss");

     $string = str_replace($what, $how, $string);

     return $string;
  }

  ### Send the order detail to Novalnet ###
  function after_process() {
    global $order, $customer_id, $insert_id,$db;
	if ($this->blnDebug) {$this->debug2(__FUNCTION__);}

    $product_id = MODULE_PAYMENT_NOVALNET_PREPAYMENT_PRODUCT_ID;
    $tariff_id = MODULE_PAYMENT_NOVALNET_PREPAYMENT_TARIFF_ID;
	if( $_SESSION['nn_tid'] != ''){
		### Pass the Order Reference to paygate ##
		$url = 'https://payport.novalnet.de/paygate.jsp';
		$urlparam = 'vendor='.MODULE_PAYMENT_NOVALNET_PREPAYMENT_VENDOR_ID.'&product='.$product_id.'&key=27&tariff='.$tariff_id;
		$urlparam .= '&auth_code='.MODULE_PAYMENT_NOVALNET_PREPAYMENT_AUTH_CODE.'&status=100&tid='.$_SESSION['nn_tid'].'&reference=BNR-'.$insert_id.'&vwz2='.MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_ORDERNO.''.$insert_id.'&vwz3='.MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_ORDERDATE.''.date('Y-m-d H:i:s');
		$urlparam .= '&order_no='.$insert_id;
		$urlparam .= "&invoice_ref=BNR-".$product_id."-".$insert_id;
		list($errno, $errmsg, $data) = $this->perform_https_request($url, $urlparam);
    }
    unset($_SESSION['nn_tid']);

    #print "$customer_id, $insert_id"; exit;
    ### Implement here the Emailversand and further functions, incase if you want to send a own email ###

      $db->Execute("update ".TABLE_ORDERS_STATUS_HISTORY." set comments = '".$order->info['comments']."' , orders_status_id= '".$this->order_status."' where orders_id = '".$insert_id."'");
      $db->Execute("update ".TABLE_ORDERS." set orders_status = '".$this->order_status."' where orders_id = '".$insert_id."'");


    return false;
  }  

  ### Used to display error message details ###
  // @return array
  function get_error() {
    global $HTTP_GET_VARS, $_GET;
	if ($this->blnDebug) {$this->debug2(__FUNCTION__);}
    if(count($HTTP_GET_VARS)==0 || $HTTP_GET_VARS=='') $HTTP_GET_VARS = $_GET;

    $error = array('title' => MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_ERROR,
                   'error' => stripslashes(urldecode($HTTP_GET_VARS['error'])));

    return $error;
  }

  ### Check to see whether module is installed ###
  // @return boolean
  function check() {
    global $db;
    if ($this->blnDebug) {$this->debug2(__FUNCTION__);}
    if (!isset($this->_check)) {
      $check_query = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_NOVALNET_PREPAYMENT_STATUS'");
      $this->_check = $check_query->RecordCount();
    }
    return $this->_check;
  }

  ### Install the payment module and its configuration settings ###
  function install() {
    global $db;
    if ($this->blnDebug) {$this->debug2(__FUNCTION__);}
    $db->Execute("alter table ".TABLE_ORDERS." modify payment_method varchar(250)"); 	
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Allowed zones','MODULE_PAYMENT_NOVALNET_PREPAYMENT_ALLOWED', '','Please enter the zones <b>separately</b> which should be allowed to use this modul (e. g. AT,DE (leave empty if you want to allow all zones))', '6', '0', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Prepayment Module', 'MODULE_PAYMENT_NOVALNET_PREPAYMENT_STATUS', 'True', 'Do you want to activate the Prepayment Method of Novalnet AG?', '6', '1', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Test Mode', 'MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEST_MODE', 'True', 'Do you want to enable the test mode?', '6', '2', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Please contact sales@novalnet.de if you do not have any of the following Novalnet IDs!<BR><P>Wenn Sie keine oder irgendeine der folgenden Novalnet IDs nicht haben sollten, bitte sich an sales@novalnet.de wenden!<BR><P>Novalnet Merchant ID', 'MODULE_PAYMENT_NOVALNET_PREPAYMENT_VENDOR_ID', '', 'Your Merchant ID of Novalnet', '6', '3', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Novalnet Authorisation Code', 'MODULE_PAYMENT_NOVALNET_PREPAYMENT_AUTH_CODE', '', 'Your Authorisation Code of Novalnet', '6', '4', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Novalnet Product ID', 'MODULE_PAYMENT_NOVALNET_PREPAYMENT_PRODUCT_ID', '', 'Your Product ID of Novalnet', '6', '5', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Novalnet Tariff ID', 'MODULE_PAYMENT_NOVALNET_PREPAYMENT_TARIFF_ID', '', 'Your Tariff ID of Novalnet', '6', '6', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Information to the Customer', 'MODULE_PAYMENT_NOVALNET_PREPAYMENT_INFO', '','will be shown on the payment formula', '6', '8', now())");    
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort order of display.', 'MODULE_PAYMENT_NOVALNET_PREPAYMENT_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '8', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('Set Order Status', 'MODULE_PAYMENT_NOVALNET_PREPAYMENT_ORDER_STATUS_ID', '0', 'Set the status of orders made with this payment module to this value', '6', '9', 'zen_cfg_pull_down_order_statuses(', 'zen_get_order_status_name', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Payment Zone', 'MODULE_PAYMENT_NOVALNET_PREPAYMENT_ZONE', '0', 'If a zone is selected, only enable this payment method for that zone.', '6', '10', 'zen_get_zone_class_title', 'zen_cfg_pull_down_zone_classes(', now())");
     $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Proxy', 'MODULE_PAYMENT_NOVALNET_PREPAYMENT_PROXY', '0', 'If you use a Proxy Server, enter the Proxy Server IP here (e.g. www.proxy.de:80)', '6', '11', now())"); 
  }
   
  ### Remove the module and all its settings ###
  function remove() {
    global $db;
    if ($this->blnDebug) {$this->debug2(__FUNCTION__);}
    $db->Execute("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
  }

  ### Internal list of configuration keys used for configuration of the module ###
  // @return array
  function keys() {
	if ($this->blnDebug) {$this->debug2(__FUNCTION__);}
    return array('MODULE_PAYMENT_NOVALNET_PREPAYMENT_ALLOWED', 'MODULE_PAYMENT_NOVALNET_PREPAYMENT_STATUS', 'MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEST_MODE', 
    'MODULE_PAYMENT_NOVALNET_PREPAYMENT_VENDOR_ID', 'MODULE_PAYMENT_NOVALNET_PREPAYMENT_AUTH_CODE', 'MODULE_PAYMENT_NOVALNET_PREPAYMENT_PRODUCT_ID', 
    'MODULE_PAYMENT_NOVALNET_PREPAYMENT_TARIFF_ID', 'MODULE_PAYMENT_NOVALNET_PREPAYMENT_INFO', 'MODULE_PAYMENT_NOVALNET_PREPAYMENT_SORT_ORDER', 
    'MODULE_PAYMENT_NOVALNET_PREPAYMENT_ORDER_STATUS_ID', 'MODULE_PAYMENT_NOVALNET_PREPAYMENT_ZONE', 'MODULE_PAYMENT_NOVALNET_PREPAYMENT_PROXY');
  }  

	function html_to_utf8 ($data)
	{
		return preg_replace("/\\&\\#([0-9]{3,10})\\;/e", '$this->_html_to_utf8("\\1")', $data);
	}

	function _html_to_utf8 ($data)
	{
		if ($data > 127)
		{
			$i = 5;
			while (($i--) > 0)
			{
				if ($data != ($a = $data % ($p = pow(64, $i))))
				{
					$ret = chr(base_convert(str_pad(str_repeat(1, $i + 1), 8, "0"), 2, 10) + (($data - $a) / $p));
					for ($i; $i > 0; $i--)
						$ret .= chr(128 + ((($data % pow(64, $i)) - ($data % ($p = pow(64, $i - 1)))) / $p));
					break;
				}
			}
		}
		else
		{
			$ret = "&#$data;";
		}
		return $ret;
	}
	function debug2($funcname)
	{
		$fh = fopen('/tmp/debug2.txt', 'a+');
		fwrite($fh, date('H:i:s ').$funcname."\n");
		fclose($fh);
	}
}
/*
order of functions:
selection              -> $order-info['total'] wrong, cause shipping_cost is net
pre_confirmation_check -> $order-info['total'] wrong, cause shipping_cost is net
confirmation           -> $order-info['total'] right, cause shipping_cost is gross
process_button         -> $order-info['total'] right, cause shipping_cost is gross
before_process         -> $order-info['total'] wrong, cause shipping_cost is net
after_process          -> $order-info['total'] right, cause shipping_cost is gross
*/

?>
