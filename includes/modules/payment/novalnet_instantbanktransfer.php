<?php
#########################################################
#                                                       #
#  Sofortüberweisung / INSTANTBANKTRANSFER payment      #
#  method class                                         #
#  This module is used for real time processing of      #
#  German Bankdata of customers.                        #
#                                                       #
#  Copyright (c) 2009 Novalnet AG                       #
#                                                       #
#  Released under the GNU General Public License        #
#  Created by Zhang jz@novalnet.de                      #
#  This free contribution made by request.              #
#  If you have found this script usefull a small        #
#  recommendation as well as a comment on merchant form #
#  would be greatly appreciated.                        #
#                                                       #
#  Script : novalnet_instantbanktransfer.php            #
#  Version: 1.0 2009-10-14                              #
#                                                       #
#########################################################

class novalnet_instantbanktransfer {
  var $code;
  var $title;
  var $description;
  var $enabled;
  var $blnDebug;
  var $key;
  var $proxy;   
  var $implementation;
  const KEY = 33;

  function novalnet_instantbanktransfer() {
    global $order, $db,$insert_id;
    $this->key = MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_PASSWORD; #'z2Vw3E4j';
    $this->code            = 'novalnet_instantbanktransfer';
    $this->form_action_url = 'https://payport.novalnet.de/online_transfer_payport';
    $this->title           = MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEXT_TITLE;
    $this->public_title    = MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEXT_PUBLIC_TITLE;
    $this->description     = MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEXT_DESCRIPTION;
    $this->sort_order      = MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_SORT_ORDER;
    $this->enabled         = ((MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_STATUS == 'True') ? true : false);
    $this->blnDebug        = false; #todo: set to false for live system
    $this->proxy           = MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_PROXY;   
    $this->implementation  = ''; 

 if ((int)MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_ORDER_STATUS_ID > 0) {
      $this->order_status = MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_ORDER_STATUS_ID;
    }
    #check encoded data
    if ($_REQUEST['hash2'] && $_SESSION['payment'] == $this->code){
      if (strtoupper($this->implementation) == 'JAVA'){#Java encoded
        if ( $_REQUEST['auth_code'] != md5(MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_AUTH_CODE.strrev($this->key)) ){
          $err = MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEXT_HASH_ERROR.'; wrong auth_code!';
          $payment_error_return = 'payment_error=novalnet_instantbanktransfer&error='.$_REQUEST['status_text'].'; '.$err;
          xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
        }
        $_REQUEST['auth_code']  = MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_AUTH_CODE;#todo: check?
        $_REQUEST['product_id'] = $this->encode4java($_REQUEST['product'],   'bindec');
        $_REQUEST['tariff_id']  = $this->encode4java($_REQUEST['tariff'],    'bindec');
        $_REQUEST['amount']     = $this->encode4java($_REQUEST['amount'],    'bindec');
        $_REQUEST['test_mode']  = $this->encode4java($_REQUEST['test_mode'], 'bindec');
        $_REQUEST['uniqid']     = $this->encode4java($_REQUEST['uniqid'],    'bindec');

        if (!$this->checkHash4java($_REQUEST)){#PHP encoded
          $err = MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEXT_HASH_ERROR;
          $payment_error_return = 'payment_error=novalnet_instantbanktransfer&error='.$_REQUEST['status_text'].'; '.$err;
          xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
        }
      }else{#PHP encoded
        if (!$this->checkHash($_REQUEST)){
          $err = MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEXT_HASH_ERROR;
          $payment_error_return = 'payment_error=novalnet_instantbanktransfer&error='.$_REQUEST['status_text'].'; '.$err;
          xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
        }else{
        $_REQUEST['auth_code']  = $this->decode($_REQUEST['auth_code']);
        $_REQUEST['product_id'] = $this->decode($_REQUEST['product_id']);
        $_REQUEST['tariff_id']  = $this->decode($_REQUEST['tariff_id']);
        $_REQUEST['amount']     = $this->decode($_REQUEST['amount']);
        $_REQUEST['test_mode']  = $this->decode($_REQUEST['test_mode']);
        $_REQUEST['uniqid']     = $this->decode($_REQUEST['uniqid']);
        }
      }
    }

  if ((int)MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_ORDER_STATUS_ID > 0) {
      $this->order_status = MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_ORDER_STATUS_ID;
    }

    if (is_object($order)) $this->update_status();
  }
  
  ### calculate zone matches and flag settings to determine whether this module should display to customers or not ###
  function update_status() {
    global $order, $db;

    if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_ZONE > 0) ) {
      $check_flag = false;
      $check = $db->Execute("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
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
    global $xtPrice, $order, $HTTP_POST_VARS, $_POST;

    $onFocus = '';
    if(count($HTTP_POST_VARS)==0 || $HTTP_POST_VARS=='') $HTTP_POST_VARS = $_POST;

    $selection = array('id' => $this->code,
                       'module' => $this->title,
                       'fields' => array(array('title' => '<!--b><font color="red">Achtung:<br />Klicken Sie bitte UNBEDINGT auf den Button &lt;Zur&uuml;ck zum Shop&gt;, wenn Sie auf der Transaktionsabschlu&szlig;seite Ihrer Bank die Transaktion durchgef&uuml;hrt haben. Ansonsten wird Ihe Shop-Bestellung trotz erfolgter Transaktion nicht abgeschlossen.</font></b-->')
                   ));

    if(function_exists('get_percent'))
    {
        $selection['module_cost'] = $GLOBALS['ot_payment']->get_percent($this->code);
    }
    return $selection;
  }

  ### Precheck to Evaluate the Bank Datas ###
  function pre_confirmation_check() {
    global $HTTP_POST_VARS, $_POST, $messageStack;

    if(count($HTTP_POST_VARS)==0 || $HTTP_POST_VARS=='') $HTTP_POST_VARS = $_POST;

    $error = '';
    
    if(!MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_VENDOR_ID || !MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_AUTH_CODE || !MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_PRODUCT_ID || !MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TARIFF_ID || !MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_PASSWORD )
    {
      $error = MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEXT_JS_NN_MISSING;
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
    if(count($HTTP_POST_VARS)==0 || $HTTP_POST_VARS=='') $HTTP_POST_VARS = $_POST;

    $confirmation = array();

    return $confirmation;
  }

  ### Build the data and actions to process when the "Submit" button is pressed on the order-confirmation screen. ###
  ### These are hidden fields on the checkout confirmation page ###
  // @return string
  function process_button() {
    global $HTTP_POST_VARS, $_POST, $order, $currencies, $customer_id, $db;
    if(count($HTTP_POST_VARS)==0 || $HTTP_POST_VARS=='') $HTTP_POST_VARS = $_POST;

    #Get the required additional customer details from DB
    $customer = $db->Execute("SELECT customers_gender, customers_dob, customers_fax FROM ". TABLE_CUSTOMERS . " WHERE customers_id='". (int)$_SESSION['customer_id']."'");

    if ($customer->RecordCount() > 0){
      $customer = $customer->fields;
    }
    list($customer['customers_dob'], $extra) = explode(' ', $customer['customers_dob']);

    if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status_add_tax_ot'] == 1) {
      $totalamount=$order->info['total'] + $order->info['tax'];
      } else { 
      $totalamount=$order->info['total'];
    }
    $amount =sprintf('%.2f', $totalamount);
    if (preg_match('/[^\d\.]/', $amount) or !$amount){
      ### $amount contains some unallowed chars or empty ###
      $err                      = '$amount ('.$amount.') is empty or has a wrong format';
      $order->info['comments'] .= 'Novalnet Error Message : '.$err;
      $payment_error_return     = 'payment_error='.$this->code;
      $messageStack->add_session('checkout_payment', $err . '<!-- ['.$this->code.'] -->', 'error');	  
      zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
    }
    $amount = preg_replace('/^0+/', '', $amount);
    $amount = sprintf('%0.2f', $amount);
    $amount = str_replace('.', '', $amount);
    $vendor_id    = MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_VENDOR_ID;
    $auth_code    = MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_AUTH_CODE;
    $product_id   = MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_PRODUCT_ID;
    $tariff_id    = MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TARIFF_ID;
    $amount       = $amount;
    $test_mode    = (strtolower(MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEST_MODE) == 'true' or MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEST_MODE == '1')? 1: 0;
    $uniqid       = uniqid();
    $user_ip      = $this->getRealIpAddr();
    $checkout_url = zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL');

    if (strtoupper($this->implementation) == 'JAVA'){
      $uniqid       = time();#must ne a long integer
      $hash         = md5($auth_code.$product_id.$tariff_id.$amount.$test_mode.$uniqid.strrev($this->key));
      $auth_code    = md5($auth_code.strrev($this->key));
      $product_id   = $this->encode4java($product_id, 'decbin');
      $tariff_id    = $this->encode4java($tariff_id, 'decbin');
      $amount       = $this->encode4java($amount, 'decbin');
      $test_mode    = $this->encode4java($test_mode, 'decbin');
      $uniqid       = $this->encode4java($uniqid, 'decbin');
    }else{
      $auth_code    = $this->encode($auth_code);
      $product_id   = $this->encode($product_id);
      $tariff_id    = $this->encode($tariff_id);
      $amount       = $this->encode($amount);
      $test_mode    = $this->encode($test_mode);
      $uniqid       = $this->encode($uniqid);
      $hash         = $this->hash(array('auth_code' => $auth_code, 'product_id' => $product_id, 'tariff' => $tariff_id, 'amount' => $amount, 'test_mode' => $test_mode, 'uniqid' => $uniqid));
    }

    if(strstr($checkout_url, '?'))
    {
      $checkout_url = str_replace(' ', '', $checkout_url);
      if(substr($checkout_url,-1)=='?')
           $error_url = $checkout_url.'payment_error=novalnet_instantbanktransfer&error=$ERROR_MESSAGE ($STATUS)';
      else $error_url = $checkout_url.'&payment_error=novalnet_instantbanktransfer&error=$ERROR_MESSAGE ($STATUS)';
    }
    else $error_url = $checkout_url.'?payment_error=novalnet_instantbanktransfer&error=$ERROR_MESSAGE ($STATUS)';
    $oldreturnurl=zen_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL');
    $old_value=explode(':',$oldreturnurl);
    $new_protocol_value=(empty($_SERVER["HTTPS"])) ? 'http' : 'https';
    $return_url=$new_protocol_value.':'.$old_value[1];
	$_SESSION['pymt_method'] = 'instantbanktransfer';

    $process_button_string =
      zen_draw_hidden_field('vendor',    $vendor_id) .#Pflicht
      zen_draw_hidden_field('auth_code', $auth_code) .
      zen_draw_hidden_field('product',   $product_id) .
      zen_draw_hidden_field('tariff',    $tariff_id) .
      zen_draw_hidden_field('test_mode', $test_mode) .
      zen_draw_hidden_field('uniqid',    $uniqid) .
      zen_draw_hidden_field('amount',    $amount) .
      zen_draw_hidden_field('hash',      $hash) .	  
      zen_draw_hidden_field('nnpayment','onlinetransfer') .	  	  
      zen_draw_hidden_field('key', self::KEY) .#Pflicht
      zen_draw_hidden_field('currency',  $order->info['currency']) .
      zen_draw_hidden_field('first_name', $this->html_to_utf8($order->customer['firstname'])) .
      zen_draw_hidden_field('last_name', $this->html_to_utf8($order->customer['lastname'])) .
      zen_draw_hidden_field('gender',    'u') .
      zen_draw_hidden_field('email',     $order->customer['email_address']) .
      zen_draw_hidden_field('street',    $this->html_to_utf8($order->customer['street_address'])) .
      zen_draw_hidden_field('search_in_street', '1') .
      zen_draw_hidden_field('city',      $this->html_to_utf8($order->customer['city'])) .
      zen_draw_hidden_field('zip',       $order->customer['postcode']) .
      zen_draw_hidden_field('country',   $order->customer['country']['iso_code_2']) .
      zen_draw_hidden_field('country_code', $order->customer['country']['iso_code_2']) .
      zen_draw_hidden_field('lang',      MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEXT_LANG) .#default: 'DE'
      zen_draw_hidden_field('language',  MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEXT_LANG) .#default: 'DE'
      zen_draw_hidden_field('remote_ip', $user_ip) . #Pflicht
      zen_draw_hidden_field('tel', $order->customer['telephone']) .
      zen_draw_hidden_field('fax', $customer['customers_fax']) .
      zen_draw_hidden_field('birth_date', $customer['customers_dob']) .
      zen_draw_hidden_field('session', zen_session_id()) .
      zen_draw_hidden_field('return_url', $return_url) .
      zen_draw_hidden_field('return_method', 'POST') .
      zen_draw_hidden_field('error_return_url', $error_url) . 
      zen_draw_hidden_field('user_variable_0', str_replace(array($new_protocol_value.'://', 'www.'), array('', ''), $_SERVER['SERVER_NAME'])) .
      zen_draw_hidden_field('error_return_method', 'POST').
      zen_draw_hidden_field('implementation', strtoupper($this->implementation)) .
      zen_draw_hidden_field('proxy', $this->proxy);

    $process_button_string .= $this->getParams4InstantBankTransfer();

    return $process_button_string;
  }

  ### Insert the Novalnet Transaction ID in DB ###
  function before_process() {
    global $HTTP_POST_VARS, $_POST, $order, $currencies, $customer_id;
		if( isset( $_POST['status']  ) && $_POST['status'] == 100 ) {
		if( $this->order_status ) {
			$order->info['order_status'] = $this->order_status;
		}
		
		if(count($HTTP_POST_VARS)==0 || $HTTP_POST_VARS=='') $HTTP_POST_VARS = $_POST;
		$testvalue=(strtolower(MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEST_MODE) == 'true' or MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEST_MODE == '1')? 1: 0;
		$test_mode    = (strtolower(MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEST_MODE) == 'true' or MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEST_MODE == '1')? 1: 0;
		$test_mode_value=( $_REQUEST['test_mode'] == 1) ? $_REQUEST['test_mode'] : $test_mode;
		if ($test_mode_value){
		$order->info['comments'] .= MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEST_ORDER_MESSAGE;
		}

		$order->info['comments'] .= MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TID_MESSAGE.' '.$HTTP_POST_VARS['tid'];
		$_SESSION['nn_tid'] = $HTTP_POST_VARS['tid'];#todo: 
	}
  }

 ### Send the order detail to Novalnet ###
  function after_process() {
    global $order, $customer_id, $insert_id;
    $product_id = MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_PRODUCT_ID;
    $tariff_id = MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TARIFF_ID;
	if(  $_SESSION['nn_tid'] != ''){
		### Pass the Order Reference to paygate ##
		$url = 'https://payport.novalnet.de/paygate.jsp';
		$urlparam = 'vendor='.MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_VENDOR_ID.'&product='.$product_id.'&key=6&tariff='.$tariff_id;
		$urlparam .= '&auth_code='.MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_AUTH_CODE.'&status=100&tid='.$_SESSION['nn_tid'].'&reference=BNR-'.$insert_id.'&vwz2='.MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEXT_ORDERNO.''.$insert_id.'&vwz3='.MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEXT_ORDERDATE.''.date('Y-m-d H:i:s');
		$urlparam .= '&order_no='.$insert_id;
		list($errno, $errmsg, $data) = $this->perform_https_request($url, $urlparam);
	}
    unset($_SESSION['nn_tid']);

    #print "$customer_id, $insert_id"; exit;
    ### Implement here the Emailversand and further functions, incase if you want to send a own email ###
   $_SESSION['t_id']=$insert_id;  
    return false;
  }

  ### Used to display error message details ###
  // @return array
  function get_error() {
    global $HTTP_GET_VARS, $_GET;
    if(count($HTTP_GET_VARS)==0 || $HTTP_GET_VARS=='') $HTTP_GET_VARS = $_GET;

    #print $HTTP_GET_VARS['error']; exit;
    $error = array('title' => MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEXT_ERROR, 'error' => stripslashes(utf8_decode($HTTP_GET_VARS['error'])));

    return $error;
  }

  ### Check to see whether module is installed ###
  // @return boolean
  function check() {
    global $db;
    if (!isset($this->_check)) {
      $check_query = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_STATUS'");
      $this->_check = $check_query->RecordCount();
    }
    return $this->_check;
  }

  ### Install the payment module and its configuration settings ###
  function install() {
    global $db;
    $db->Execute("alter table ".TABLE_ORDERS." modify payment_method varchar(250)");
	
   $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Allowed zones','MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_ALLOWED', '','Please enter the zones <b>separately</b> which should be allowed to use this modul (e. g. AT,DE (leave empty if you want to allow all zones))', '6', '0', now())");
//    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Enable Instant Bank Transfer Module', 'MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_ALLOWED', '', 'Do you want to activate the Instant Bank Transfer Method of Novalnet AG?', '6', '1', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Instant Bank Transfer Module', 'MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_STATUS', 'True', 'Do you want to activate the Instant Bank Transfer Method of Novalnet AG?', '6', '1', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enbale Test Mode', 'MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEST_MODE', 'True', 'Do you want to activate the test Mode?', '6', '2', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Password you can get from http://admin.novalnet.de -> Stammdaten -> Paymentzugriffsschluessel', 'MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_PASSWORD', '', 'Enter your password at Novalnet', '6', '3', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Please contact sales@novalnet.de if you do not have any of the following Novalnet IDs!<BR><P>Wenn Sie keine oder irgendeine der folgenden Novalnet IDs nicht haben sollten, bitte sich an sales@novalnet.de wenden!<BR><P>Novalnet Merchant ID', 'MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_VENDOR_ID', '', 'Your Merchant ID of Novalnet', '6', '4', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Novalnet Authorisation Code', 'MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_AUTH_CODE', '', 'Your Authorisation Code of Novalnet', '6', '5', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Novalnet Product ID', 'MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_PRODUCT_ID', '', 'Your Product ID of Novalnet', '6', '6', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Novalnet Tariff ID', 'MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TARIFF_ID', '', 'Your Tariff ID of Novalnet', '6', '7', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort order of display.', 'MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '8', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('Set Order Status', 'MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_ORDER_STATUS_ID', '0', 'Set the status of orders made with this payment module to this value', '6', '9', 'zen_cfg_pull_down_order_statuses(', 'zen_get_order_status_name', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Payment Zone', 'MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_ZONE', '0', 'If a zone is selected, only enable this payment method for that zone.', '6', '10', 'zen_get_zone_class_title', 'zen_cfg_pull_down_zone_classes(', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Information to your customer', 'MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_INFO', '', 'Information for your customer', '6', '11', now())");
         $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Proxy', 'MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_PROXY', '0', 'If you use a Proxy Server, enter the Proxy Server IP here (e.g. www.proxy.de:80)', '6', '12', now())");     
  }
   
  ### Remove the module and all its settings ###
  function remove() {
    global $db;
    $db->Execute("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
  }

  ### Internal list of configuration keys used for configuration of the module ###
  // @return array
  function keys(){
    return array('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_ALLOWED', 'MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_STATUS', 
    'MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEST_MODE','MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_VENDOR_ID', 'MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_AUTH_CODE', 'MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_PRODUCT_ID', 'MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TARIFF_ID', 'MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_INFO', 'MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_SORT_ORDER', 'MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_ORDER_STATUS_ID', 
    'MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_ZONE', 'MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_PASSWORD', 'MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_PROXY');
  }

	function html_to_utf8 ($data)
	{
		$data = utf8_encode($data);
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
			#$this->debug2("&#$data;");
			$ret = "&#$data;";
		}
		return $ret;
	}

	function debug2($text)
	{
		$fh = fopen('/tmp/debug2.txt', 'a+');
		if (gettype($text) == 'class' or gettype($text) == 'array')
		{
			$text = serialize($text);
			fwrite($fh, $text);
		}
		else
		{
			fwrite($fh, date('H:i:s ').$text."\n");
		}
		fclose($fh);
	}

	function getAmount($amount)
	{
		if(!$amount)$amount = $order->info['total'];
		if(preg_match('/[,.]$/', $amount))
		{
			$amount = $amount . '00';
		}
		else if(preg_match('/[,.][0-9]$/', $amount))
		{
			$amount = $amount . '0';
		}
		$amount = str_replace(array('.', ','),array('',''), $amount);
		return$amount;
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
	function getParams4InstantBankTransfer()
	{
		if(count($HTTP_POST_VARS)==0 || $HTTP_POST_VARS=='') $HTTP_POST_VARS = $_POST;
		/*for instant bank transfer via www.sofortueberweisung.de
			required params:
				project_id= must be registred at via www.sofortueberweisung.de
				user_id = Kundennr. ($_SESSION['nn_tid'])

			optional params:
				Parameter Bedeutung Typ (Länge) Erklärung
				amount Betrag Double (8,2) Der zu überweisende Betrag (Minimum: 0.10 EURO, wichtig für Testbestellungen) Bitte keine Trennzeichen bei Tausender-Beträgen, z.B. 1010.50 Euro, correct: 

				reason_1 Verwendungszweck1 String (27) Der Verwendungszweck in Zeile 1 (max. 27 Zeichen). Dieser sollte bei jeder Bestellung unterschiedliche Zuordnungsmerkmale aufweisen (z.B. Bestellnummer, Datum der Bestellung) und ist damit eindeutig.

				reason_2

				sender_bank_code Bankleitzahl des Kunden String (30) Absender-Bankleitzahl
				sender_account_number Kontonummer des Kunden String (30) Absender-Kontonummer
				sender_holder Kontoinhaber des Kunden String (27) Absender-Kontoinhaber
				sender_country_id Kontoinhaber Länderkürzel String (2) Absender-Land(zweistellig,z.B. DE, CH, AT)
				hash Hash-Wert String (>=32) Input-Prüfung, siehe Kapitel 3.2.5
				currency_id Transaktionswährung String (3) Werte sind EUR, CHF und GBP* (* Voraussetzung: englischesKonto)
				language_id Sprache des Zahlformulars String (2) Legen Sie mit diesem Parameter die Sprache des Zahlformulars fest, Werte, z.B. DE, EN

				user_variable_0 bis user_variable_5 Kundenvariable 0-5 String (255) Zu Ihrer freien Verwendung (z.B. Session-ID)

				#to deposit at www.sofortueberweisung.de:
				Erfolgslink: http://zencart.gsoftpro.de/checkout_process.php
				Abbruchlink: http://zencart.gsoftpro.de/.php

				####Plausicheck error von XT:
				http://localhost/zencart/checkout_payment.php?payment_error=novalnet_instantbanktransfer&error=*+Deutsche+Kontonummer+muss+mindestens+3+stellig+sein!
				####wrong bank code error von Novalnet:
				http://localhost/zencart/checkout_payment.php?payment_error=novalnet_instantbanktransfer&error=Die+angegebene+Bankleitzahl+gibt+es+nicht+%28501007%29
				http://zencart.gsoftpro.de/checkout_payment.php?payment_error=novalnet_INSTANTBANKTRANSFER&error=zh
				####sucess
		*/
		$params = 
		#zen_draw_hidden_field('amount', str_replace(',', '.', $_SESSION['nn_total'])).#todo:form check
		#zen_draw_hidden_field('sender_bank_code', $HTTP_POST_VARS['bank_code']).
		#zen_draw_hidden_field('sender_account_number', $HTTP_POST_VARS['bank_account']).
		#zen_draw_hidden_field('sender_holder=', $this->html_to_utf8($HTTP_POST_VARS['bank_account_holder'])).
		#zen_draw_hidden_field('sender_country_id', 'DE').
		#zen_draw_hidden_field('currency_id', 'EUR').
		#zen_draw_hidden_field('language_id', MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEXT_LANG);
		#zen_draw_hidden_field('customer_id', $_SESSION['nn_tid']).
		zen_draw_hidden_field('user_variable_0', (str_replace(array('http://', 'www.'), array('', ''), HTTP_SERVER)));
		return$params;
		#this link is stored at www.soforueberweisung.de: https://payport.novalnet.de/online_transfer_payport?status=ok&customer_id=-CUSTOMER_ID-&transaction=-TRANSACTION-&sender_holder=-SENDER_HOLDER-&sender_holder_urlencode=-SENDER_HOLDER_URLENCODE-&sender_account_number=-SENDER_ACCOUNT_NUMBER-&sender_bank_code=-SENDER_BANK_CODE-&sender_bank_name=-SENDER_BANK_NAME-&sender_bank_name_urlencode=-SENDER_BANK_NAME_URLENCODE-&sender_bank_bic=-SENDER_BANK_BIC-&sender_iban=-SENDER_IBAN-&user_variable_0=-USER_VARIABLE_0-
	}

  function encode($data)
  {
    $data = trim($data);
    if ($data == '') return'Error: no data';
    if (!function_exists('base64_encode') or !function_exists('pack') or !function_exists('crc32')){return'Error: func n/a';}

    try {
      $crc = sprintf('%u', crc32($data));# %u is a must for ccrc32 returns a signed value
      $data = $crc."|".$data;
      $data = bin2hex($data.$this->key);
      $data = strrev(base64_encode($data));
    }catch (Exception $e){
      echo('Error: '.$e);
    }
    return $data;
  }
  function decode($data)
  {
    $data = trim($data);
    if ($data == '') {return'Error: no data';}
    if (!function_exists('base64_decode') or !function_exists('pack') or !function_exists('crc32')){return'Error: func n/a';}

    try {
      $data =  base64_decode(strrev($data));
      $data = pack("H".strlen($data), $data);
      $data = substr($data, 0, stripos($data, $this->key));
      $pos = strpos($data, "|");
      if ($pos === false){
        return("Error: CKSum not found!");
      }
      $crc = substr($data, 0, $pos);
      $value = trim(substr($data, $pos+1));
      if ($crc !=  sprintf('%u', crc32($value))){
        return("Error; CKSum invalid!");
      }
      return $value;
    }catch (Exception $e){
      echo('Error: '.$e);
    }
  }
  function hash($h)#$h contains encoded data
  {
    global $amount_zh;
    if (!$h) return'Error: no data';
    if (!function_exists('md5')){return'Error: func n/a';}
	//echo '<br>rev pass : ';
	//echo strrev($this->key);
	//echo '<br> stright pass : ';
	//echo $this->key;
	//echo '<br>';
    return md5($h['auth_code'].$h['product_id'].$h['tariff'].$h['amount'].$h['test_mode'].$h['uniqid'].strrev($this->key));
  }
  function checkHash($request)
  {
	//echo 'check hash called ';
    if (!$request) return false; #'Error: no data';
    $h['auth_code']  = $request['auth_code'];#encoded
    $h['product_id'] = $request['product'];#encoded
    $h['tariff']     = $request['tariff'];#encoded
    $h['amount']     = $request['amount'];#encoded
    $h['test_mode']  = $request['test_mode'];#encoded
    $h['uniqid']     = $request['uniqid'];#encoded
    if ($request['hash2'] != $this->hash($h)){
      return false;
    }
    return true;
  }
  
  function checkHash4java($request)
  {
    if (!$request) return false; #'Error: no data';
    $h['auth_code']  = $request['auth_code'];#encoded
    $h['product_id'] = $request['product_id'];#encoded
    $h['tariff']     = $request['tariff_id'];#encoded
    $h['amount']     = $request['amount'];#encoded
    $h['test_mode']  = $request['test_mode'];#encoded
    $h['uniqid']     = $request['uniqid'];#encoded
    if ($request['hash2'] != $this->hash($h)){
      return false;
    }
    return true;
  }

  function encode4java($data = '', $func = ''){
    $salt = 1010;
    if (!isset($data) or trim($data) == '' or !$func){
      return'Error: missing arguments: $str and/or $func!';
    }
    if ($func != 'decbin' and $func != 'bindec'){
      return'Error: $func has wrong value!';
    }
    if ($func == 'decbin'){
      return decbin(intval($data) + intval($salt));
    }else{
      return bindec($data) - intval($salt);
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
  
}
/*
  Parameters passed on Novalnet:
  vendor
  product
  key
  tariff
  auth_code
  currency
  amount
  first_name
  last_name
  email
  street
  search_in_street
  city
  zip
  country_code
  lang
  remote_ip
  tel
  fax
  birth_date
  session
  return_url
  return_method
  error_return_url
  test_mode
  error_return_method
  amount
  user_variable_0
*/
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
