<?php

#########################################################
#                                                       #
#  CC / CREDIT CARD 3d secure payment method class      #
#  This module is used for real time processing of      #
#  Credit card data of customers on 3d secure mode.     #
#                                                       #
#  Copyright (c) 2009-2010 Novalnet AG                  #
#                                                       #
#  Released under the GNU General Public License        #
#  novalnet_elv_de_pci module Created By Dixon Rajdaniel#
#                 Modified By Panneerselvam             #
#  This free contribution made by request.              #
#  If you have found this script usefull a small        #
#  recommendation as well as a comment on merchant form #
#  would be greatly appreciated.                        #
#                                                       #
#  Version : novalnet_elv_de_pci.php 1.0.0 2010-06-16   #
#                                                       #
#########################################################


class novalnet_elv_de_pci 
{
	var $code;
	var $title;
	var $description;
	var $enabled;
	var $key;
	var $implementation;

   function novalnet_elv_de_pci() 
   {
		global $order;
		$this->key			= MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_PASSWORD;
		$this->code         = 'novalnet_elv_de_pci';
		$this->title        = MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_TEXT_TITLE;
		$this->public_title = MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_TEXT_PUBLIC_TITLE;
		$this->description  = MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_TEXT_DESCRIPTION;
		$this->sort_order   = MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_SORT_ORDER;
		$this->enabled      = ((MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_STATUS == 'True') ? true : false);
		$this->proxy        = MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_PROXY;
		$this->implementation = 'PHP_PCI';
		
		$this->checkReturnedData();

		if ((int)MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_ORDER_STATUS_ID > 0) 
		{
			$this->order_status = MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_ORDER_STATUS_ID;
		}

		if (is_object($order)) $this->update_status();
		$this->form_action_url = 'https://payport.novalnet.de/pci_payport';
		
		if($_POST['session'] && $_SESSION['payment'] == $this->code){
			$this->checkSecurity();
		}
		
	
  }
  
  ### calculate zone matches and flag settings to determine whether this module should display to customers or not ###
  function update_status()
  {
		global $order, $db;

		if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_ZONE > 0) ) 
		{
			$check_flag = false;
			$check = $db->Execute("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
			while (!$check->EOF)
			{
				if ($check->fields['zone_id'] < 1) 
				{
				  $check_flag = true;
				  break;
				}
				elseif ($check->fields['zone_id'] == $order->billing['zone_id'])
				{
				  $check_flag = true;
				  break;
				}
				$check->MoveNext();
			}

			 if ($check_flag == false) 
			 {
				$this->enabled = false;
			 }
		}
  }
  
  ### JS validation which does error-checking of data-entry if this module is selected for use ###
  ### the fields to be cheked are (Bank Owner, Bank Account Number and Bank Code Lengths)      ###
  ### currently this function is not in use ###
  // @return string
  function javascript_validation() 
  {
		return false;
  }
 
  ### Builds set of input fields for collecting Bankdetail info ###
  // @return array
  function selection() 
  {
		global $xtPrice, $order, $HTTP_POST_VARS, $_POST;
		$onFocus = '';
		if(count($HTTP_POST_VARS)==0 || $HTTP_POST_VARS=='') $HTTP_POST_VARS = $_POST;

		$selection = array('id' => $this->code,
						   'module' => $this->public_title,
							'fields' => array(array())
						  );

		if(function_exists(get_percent))
		{
			$selection['module_cost'] = $GLOBALS['ot_payment']->get_percent($this->code);
		}

		return $selection;
  }

  ### Precheck to Evaluate the Bank Datas ###
  function pre_confirmation_check() 
  {
		global $HTTP_POST_VARS, $_POST,$messageStack;
		if(count($HTTP_POST_VARS)==0 || $HTTP_POST_VARS=='') $HTTP_POST_VARS = $_POST;
		#echo'<pre>';var_dump($_REQUEST); exit;
		$error = '';

		if (defined('MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_MANUAL_CHECK_LIMIT') and MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_MANUAL_CHECK_LIMIT)
		{
			  if ( (!defined('MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_PRODUCT_ID2') or !MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_PRODUCT_ID2 or preg_match('/[^\d]/', MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_PRODUCT_ID2)) or (!defined('MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_TARIFF_ID2') or !MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_TARIFF_ID2 or preg_match('/[^\d]/', MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_TARIFF_ID2)))
			  {
				  $error = 'Product-ID2 and/or Tariff-ID2 missing';
			  }
		}

		if(!MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_VENDOR_ID || !MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_AUTH_CODE || !MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_PRODUCT_ID || !MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_TARIFF_ID || !MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_PASSWORD )
		{
		  $error = MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_TEXT_JS_NN_MISSING;
		}

		if($error!='') 
		{
		  $payment_error_return = 'payment_error=' . $this->code;
		  $messageStack->add_session('checkout_payment', $error . '<!-- ['.$this->code.'] -->', 'error');
		  zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));      
		}
  }

  ### Display Information on the Checkout Confirmation Page ###
  // @return array
  function confirmation() 
  {
		global $HTTP_POST_VARS, $_POST, $order;
		$_SESSION['nn_total'] = $order->info['total'];
		if(count($HTTP_POST_VARS)==0 || $HTTP_POST_VARS=='') $HTTP_POST_VARS = $_POST;
		#print "in confirmation"; exit;

		return $confirmation;
  }

  ### Build the data and actions to process when the "Submit" button is pressed on the order-confirmation screen. ###
  ### These are hidden fields on the checkout confirmation page ###
  // @return string
  function process_button()
  {
    
		global $HTTP_POST_VARS, $_POST, $order, $currencies, $db,$messageStack;
		if(count($HTTP_POST_VARS)==0 || $HTTP_POST_VARS=='') $HTTP_POST_VARS = $_POST;

		#Get the required additional customer details from DB
		$customer_values = $db->Execute("SELECT customers_gender, customers_dob, customers_fax FROM ". TABLE_CUSTOMERS . " WHERE customers_id='". (int)$_SESSION['customer_id'] ."'");
		
		while(!$customer_values->EOF) 
		{
			$customer_values->MoveNext();
		}
		
		list($customer_values->fields['customers_dob'], $extra) = explode(' ', $customer_values->fields['customers_dob']);

		if($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status_add_tax_ot'] == 1) 
		{
			$totalamount=$order->info['total'] + $order->info['tax'];
		}
		else
		{ 
			$totalamount=$order->info['total'];
		}
		$amount =sprintf('%.2f', $totalamount);

		//$amount = number_format($p_amount * $currencies->currencies['EUR']['value'], $currencies->currencies['EUR']['decimal_places']);


		// $amount = $_SESSION['nn_total'];
		if(preg_match('/[^\d\.]/', $amount) or !$amount)
		{
			  ### $amount contains some unallowed chars or empty ###
			  $err                      = '$amount ('.$amount.') is empty or has a wrong format';
			  $order->info['comments'] .= '. Novalnet Error Message : '.$err;
			  $payment_error_return     = 'payment_error='.$this->code;
			  $messageStack->add_session('checkout_payment', $err . '<!-- ['.$this->code.'] -->', 'error');	  
			  zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
		}
		
		$amount = preg_replace('/^0+/', '', $amount);
		$amount = sprintf('%0.2f', $amount);
		$amount = str_replace('.', '', $amount);
		#echo __CLASS__.' : '.$order->info['total']." <=> $amount<hr />";

		$product_id = MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_PRODUCT_ID;
		$tariff_id = MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_TARIFF_ID;
		$manual_check_limit = trim(MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_MANUAL_CHECK_LIMIT);
		$manual_check_limit = str_replace(',', '', $manual_check_limit);
		$manual_check_limit = str_replace('.', '', $manual_check_limit);

		if($manual_check_limit && $amount>=$manual_check_limit)
		{
			  $product_id = MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_PRODUCT_ID2;
			  $tariff_id = MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_TARIFF_ID2;
		}

		$user_ip = $this->getRealIpAddr();

		$checkout_url = zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL');

		if(strstr($checkout_url, '?'))
		{
			  $checkout_url = str_replace(' ', '', $checkout_url);
			  if(substr($checkout_url,-1)=='?')$error_url = $checkout_url.'payment_error=novalnet_elv_de_pci&error=$ERROR_MESSAGE ($STATUS)';
			  else $error_url = $checkout_url.'&payment_error=novalnet_elv_de_pci&error=$ERROR_MESSAGE ($STATUS)';
		}
		else $error_url = $checkout_url.'?payment_error=novalnet_elv_de_pci&error=$ERROR_MESSAGE ($STATUS)';

		$test_mode = (strtolower(MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_TEST_MODE) == 'true' or MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_TEST_MODE == '1')? 1: 0;

        $_SESSION['order_status_id_value']=$this->order_status;

		$oldreturnurl=zen_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL');
		$old_value=explode(':',$oldreturnurl);
		$new_protocol_value=(empty($_SERVER["HTTPS"])) ? 'http' : 'https';
		$return_url=$new_protocol_value.':'.$old_value[1];
		
		$uniqid     = uniqid();
		
		list($auth_code, $product_id, $tariff_id, $amount, $test_mode, $uniqid, $hash) = $this->encodeParams($auth_code=MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_AUTH_CODE, $product_id, $tariff_id, $amount, $test_mode, $uniqid);

	    $process_button_string  =  zen_draw_hidden_field('vendor_id', MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_VENDOR_ID) .
								   zen_draw_hidden_field('product_id', $product_id) .
								   zen_draw_hidden_field('payment_id', '2') .
								   zen_draw_hidden_field('tariff_id', $tariff_id) .
								   zen_draw_hidden_field('vendor_authcode', $auth_code) .
								   zen_draw_hidden_field('currency', $order->info['currency']) .
								   zen_draw_hidden_field('amount', $amount) .
								   zen_draw_hidden_field('hash', $hash) .
								   zen_draw_hidden_field('uniqid', $uniqid) .
								   zen_draw_hidden_field('gender', 'u') .
								   zen_draw_hidden_field('firstname', $this->html_to_utf8($order->customer['firstname'])) .
								   zen_draw_hidden_field('lastname', $this->html_to_utf8($order->customer['lastname'])) .
								   zen_draw_hidden_field('email', $order->customer['email_address']) .
								   zen_draw_hidden_field('street', $this->html_to_utf8($order->customer['street_address'])) .
								   zen_draw_hidden_field('search_in_street', '1') .
								   zen_draw_hidden_field('city', $this->html_to_utf8($order->customer['city'])) .
								   zen_draw_hidden_field('zip', $order->customer['postcode']) .
								   zen_draw_hidden_field('country_code', $order->customer['country']['iso_code_2']) .
								   zen_draw_hidden_field('lang', MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_TEXT_LANG) .
								   zen_draw_hidden_field('remote_ip', $user_ip) .
								   zen_draw_hidden_field('tel', $order->customer['telephone']) .
								   zen_draw_hidden_field('fax', $customer_values->fields['customers_fax']) .
								   zen_draw_hidden_field('birthday', $customer_values->fields['customers_dob']) .
								   zen_draw_hidden_field('session', zen_session_id()) .
								   zen_draw_hidden_field('return_url', $return_url) .
								   zen_draw_hidden_field('return_method', 'POST') .
								   zen_draw_hidden_field('error_return_url', $error_url) .
								   zen_draw_hidden_field('test_mode', $test_mode) .
								   zen_draw_hidden_field('error_return_method', 'POST').
								   zen_draw_hidden_field('implementation', strtoupper($this->implementation)) .
								   zen_draw_hidden_field('proxy', $this->proxy);

		return $process_button_string;
  }

  ### Insert the Novalnet Transaction ID in DB ###
  function before_process() 
  {
		global $_POST, $order;
		if($_POST['tid'] && $_POST['status'] == '100'){
			if( $this->order_status ) {
				$order->info['order_status'] = $this->order_status;
			}
			$test_mode = (strtolower(MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_TEST_MODE) == 'true' or MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_TEST_MODE == '1')? 1: 0;

			$test_mode_value=( $_POST['test_mode'] == 1) ? $_POST['test_mode'] : $test_mode;
			
			if ($test_mode_value)
			{
				$order->info['comments'] .= MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_TEST_ORDER_MESSAGE;
			}

			$order->info['comments'] .= 'Novalnet Transaction ID : '.$_POST['tid'];
		}
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

  ### Send the order detail to Novalnet ###
  function after_process()
  {
		global $order, $customer_id, $insert_id,$db,$_POST;

		$product_id = MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_PRODUCT_ID;
		$tariff_id = MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_TARIFF_ID;
		if($_POST['tid'] != ''){
			### Pass the Order Reference to paygate ##
			$url = 'https://payport.novalnet.de/paygate.jsp';
			$urlparam = 'vendor='.MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_VENDOR_ID.'&product='.$product_id.'&key=2&tariff='.$tariff_id;
			$urlparam .= '&auth_code='.MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_AUTH_CODE.'&status=100&tid='.$_POST['tid'].'&reference=BNR-'.$insert_id.'&vwz2='.MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_TEXT_ORDERNO.''.$insert_id.'&vwz3='.MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_TEXT_ORDERDATE.''.date('Y-m-d H:i:s');
			$urlparam .= '&order_no='.$insert_id;
			list($errno, $errmsg, $data) = $this->perform_https_request($url, $urlparam);
			$_POST['tid'] = '';
			### Implement here the Emailversand and further functions, incase if you want to send a own email ###
		}
		return false;
  }

  ### Used to display error message details ###
  // @return array
  function get_error()
  {
		global $HTTP_GET_VARS, $_GET;
		if(count($HTTP_GET_VARS)==0 || $HTTP_GET_VARS=='') $HTTP_GET_VARS = $_GET;
		$error = array('title' => MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_TEXT_ERROR, 'error' => stripslashes(utf8_decode($HTTP_GET_VARS['error'])));

		return $error;
  }

  ### Check to see whether module is installed ###
  // @return boolean
  function check() 
  {
		global $db;  
		if (!isset($this->_check))
		{
			  $check_query = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_STATUS'");
			  $this->_check = $check_query->RecordCount();      
		}
		return $this->_check;
  }

  ### Install the payment module and its configuration settings ###
  function install() 
  {
    global $db;
    $db->Execute("alter table ".TABLE_ORDERS." modify payment_method varchar(250)");	
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Allowed zones', 'MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_ALLOWED', '', 'Please enter the zones <b>separately</b> which should be allowed to use this modul (e. g. AT,DE (leave empty if you want to allow all zones))', '6', '0', now())");
     
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable ELV-DE Module', 'MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_STATUS', 'True', 'Do you want to activate the German Direct Debit Method(ELV-DE) of Novalnet AG?', '6', '1', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
    
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Test Mode', 'MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_TEST_MODE', 'True', 'Do you want to enable the test mode?', '6', '2', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");

    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Please contact sales@novalnet.de if you do not have any of the following Novalnet IDs!<BR><P>Wenn Sie keine oder irgendeine der folgenden Novalnet IDs nicht haben sollten, bitte sich an sales@novalnet.de wenden!<BR><P>Novalnet Merchant ID', 'MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_VENDOR_ID', '', 'Your Merchant ID of Novalnet', '6', '3', now())");

    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Novalnet Authorisation Code', 'MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_AUTH_CODE', '', 'Your Authorisation Code of Novalnet', '6', '4', now())");    

    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Novalnet Product ID', 'MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_PRODUCT_ID', '', 'Your Product ID of Novalnet', '6', '5', now())");
    
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Novalnet Tariff ID', 'MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_TARIFF_ID', '', 'Your Tariff ID of Novalnet', '6', '6', now())");
    
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Manual checking amount in cents', 'MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_MANUAL_CHECK_LIMIT', '', 'Please enter the amount in cents', '6', '7', now())"); 

    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Your second Product ID in Novalnet', 'MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_PRODUCT_ID2', '', 'for the manual checking', '6', '8', now())"); 

    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('The Tariff ID of the second product', 'MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_TARIFF_ID2', '', 'for the manual checking', '6', '9', now())");
	
	 $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Password please', 'MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_PASSWORD', '', 'for the manual checking', '6', '10', now())");
    
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Your Booking Reference at Novalnet', 'MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_BOOK_REF', '', 'Your Booking Reference at Novalnet', '6', '11', now())");

    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort order of display.', 'MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '12', now())");

     $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('Set Order Status', 'MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_ORDER_STATUS_ID', '0', 'Set the status of orders made with this payment module to this value', '6', '13', 'zen_cfg_pull_down_order_statuses(', 'zen_get_order_status_name', now())");

     $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Payment Zone', 'MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_ZONE', '0', 'If a zone is selected, only enable this payment method for that zone.', '6', '14', 'zen_get_zone_class_title', 'zen_cfg_pull_down_zone_classes(', now())");
                  
     $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Proxy', 'MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_PROXY', '0', 'If you use a Proxy Server, enter the Proxy Server IP with port here (e.g. www.proxy.de:80)', '6', '15', now())");
  }
   
  ### Remove the module and all its settings ###
  function remove()
  {
		global $db;
		$db->Execute("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
  }

  ### Internal list of configuration keys used for configuration of the module ###
  // @return array
  function keys() 
  {
		return array('MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_ALLOWED', 'MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_STATUS', 'MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_TEST_MODE', 'MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_VENDOR_ID', 'MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_AUTH_CODE', 'MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_PRODUCT_ID', 'MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_TARIFF_ID', 'MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_MANUAL_CHECK_LIMIT', 'MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_PRODUCT_ID2', 'MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_TARIFF_ID2','MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_PASSWORD', 'MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_BOOK_REF', 'MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_SORT_ORDER', 'MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_ORDER_STATUS_ID', 'MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_ZONE', 'MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_PROXY');
  }
   ### replace the Special German Charectors ###
  function ReplaceSpecialGermanChars($string){
     $what = array("ä", "ö", "ü", "Ä", "Ö", "Ü", "ß");
     $how = array("ae", "oe", "ue", "Ae", "Oe", "Ue", "ss");

     $string = str_replace($what, $how, $string);

     return $string;
  }

	function html_to_utf8 ($data){
		return preg_replace("/\\&\\#([0-9]{3,10})\\;/e", '$this->_html_to_utf8("\\1")', $data);
	}

	function _html_to_utf8 ($data){
		if ($data > 127){
			$i = 5;
			while (($i--) > 0){
				if ($data != ($a = $data % ($p = pow(64, $i)))){
					$ret = chr(base_convert(str_pad(str_repeat(1, $i + 1), 8, "0"), 2, 10) + (($data - $a) / $p));
					for ($i; $i > 0; $i--)
						$ret .= chr(128 + ((($data % pow(64, $i)) - ($data % ($p = pow(64, $i - 1)))) / $p));
					break;
				}
			}
		}else{
			$ret = "&#$data;";
		}
		return $ret;
	}

	### Realtime accesspoint for communication to the Novalnet paygate ###
  function perform_https_request($nn_url, $urlparam){
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
		  
		  if ($this->proxy){
			curl_setopt($ch, CURLOPT_PROXY, $this->proxy); 
		  }

		  ## establish connection
		  $data = curl_exec($ch);
		  $data = $this->ReplaceSpecialGermanChars($data);

		  ## determine if there were some problems on cURL execution
		  $errno = curl_errno($ch);
		  $errmsg = curl_error($ch);

		  ###bug fix for PHP 4.1.0/4.1.2 (curl_errno() returns high negative value in case of successful termination)
		  if($errno < 0) $errno = 0;
		  ##bug fix for PHP 4.1.0/4.1.2

		  if($debug){
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

  function debug2($object, $filename, $debug = false){
		if (!$debug){return;}
		$fh = fopen("/tmp/$filename", 'a+');
		  fwrite($fh, date('Y-m-d H:i:s').' '.print_r($object, true));
		fwrite($fh, "<hr />\n");
		fclose($fh);
  }

  function checkSecurity() {
		global $_POST, $order, $insert_id, $messageStack; 
   
		if(strlen(trim($_POST['tid']))==17 && $_POST['status']==100 && $_POST['session']== zen_session_id()){
			#xtc_redirect(zen_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL'));
		}else{
			if($_POST['status_desc']){
				$error_status = $_POST['status_desc'];
			}else {
				$error_status = "There was an error and your payment could not be completed ";
			}
			  $err  = $error_status." (".$_POST['status'].")";
			  #'session missing or returned session is wrong';
			  $order->info['comments'] .= '. Novalnet Error Message : '.$err;
			  $payment_error_return     = 'payment_error='.$this->code/*.'&error='.$err*/;
			  $messageStack->add_session('checkout_payment', $err . '<!-- ['.$this->code.'] -->', 'error');      
			  zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
		}
  }
  function encode($data){
	  	$data = trim($data);
		if ($data == '') return'Error: no data';
		if (!function_exists('base64_encode') or !function_exists('pack') or !function_exists('crc32')){return'Error: func n/a';}

		try {
		  $crc = sprintf('%u', crc32($data));# %u is a must for ccrc32 returns a signed value
		  $data = $crc."|".$data;
		  $data = bin2hex($data.$this->key);
		  $data = strrev(base64_encode($data));
		}
		catch (Exception $e){
		  echo('Error: '.$e);
		}
		return $data;
  }
  
  function decode($data){
		$data = trim($data);
		if ($data == ''){
			return'Error: no data';
		}
		if (!function_exists('base64_decode') or !function_exists('pack') or !function_exists('crc32')){
			return'Error: func n/a';
		}

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
		}
		catch (Exception $e){
		  echo('Error: '.$e);
		}
  }
  
  function hash($h){ #$h contains encoded data
	  	global $amount_zh;
		if (!$h) return'Error: no data';
		if (!function_exists('md5')){return'Error: func n/a';}
		return md5($h['auth_code'].$h['product_id'].$h['tariff'].$h['amount'].$h['test_mode'].$h['uniqid'].strrev($this->key));
  }
  
  function checkHash($request){
	  	if (!$request) return false; #'Error: no data';
		$h['auth_code']  = $request['vendor_authcode'];#encoded
		$h['product_id'] = $request['product_id'];#encoded
		$h['tariff']     = $request['tariff_id'];#encoded
		$h['amount']     = $request['amount'];#encoded
		$h['test_mode']  = $request['test_mode'];#encoded
		$h['uniqid']     = $request['uniqid'];#encoded	
		if ($request['hash2']!= $this->hash($h)){
			return false;
		}
		return true;
  }

  function checkHash4java($request){
		if (!$request) return false; #'Error: no data';
		$h['auth_code']  = $request['auth_code'];#encoded
		$h['product_id'] = $request['product_id'];#encoded
		$h['tariff']     = $request['tariff_id'];#encoded
		$h['amount']     = $request['amount'];#encoded
		$h['test_mode']  = $request['test_mode'];#encoded
		$h['uniqid']     = $request['uniqid'];#encoded
		
		if ($request['hash2'] != $this->hash($h))
		{
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
    function checkReturnedData(){
  	 if ($_POST['hash2'] && $_SESSION['payment'] == $this->code){
			if (strtoupper($this->implementation) == 'JAVA_PCI'){
				#Java encoded
				if( $_POST['vendor_authcode'] != md5(MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_AUTH_CODE.strrev($this->key)) ){
					  $err = MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_TEXT_HASH_ERROR.'; wrong auth_code!';
					  $payment_error_return = 'payment_error=novalnet_elv_de_pci&error='.$_POST['status_text'].'; '.$err;
					  zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
				}
				$_POST['auth_code']  = MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_AUTH_CODE;#todo: check?
				$_POST['product_id'] = $this->encode4java($_POST['product_id'],   'bindec');
				$_POST['tariff_id']  = $this->encode4java($_POST['tariff_id'],    'bindec');
				$_POST['amount']     = $this->encode4java($_POST['amount'],    'bindec');
				$_POST['test_mode']  = $this->encode4java($_POST['test_mode'], 'bindec');
				$_POST['uniqid']     = $this->encode4java($_POST['uniqid'],    'bindec');

				if (!$this->checkHash4java($_POST)){     #PHP encoded
					  $err = MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_TEXT_HASH_ERROR;
					  $payment_error_return = 'payment_error=novalnet_elv_de_pci&error='.$_POST['status_text'].'; '.$err;
					  zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
				}
			}else{		#PHP encoded
				if (!$this->checkHash($_POST)){
					  $err = MODULE_PAYMENT_NOVALNET_ELV_DE_PCI_TEXT_HASH_ERROR;
					  $payment_error_return = 'payment_error=novalnet_elv_de_pci&error='.$_POST['status_text'].'; '.$err;
					  zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
				}else{
					  $_POST['test_mode']  = $this->decode($_POST['test_mode']);
				}
			}
		}
  }
  
   function encodeParams($auth_code, $product_id, $tariff_id, $amount, $test_mode, $uniqid){
		if (strtoupper($this->implementation) == 'JAVA_PCI'){
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
			return array($auth_code, $product_id, $tariff_id, $amount, $test_mode, $uniqid, $hash);
  }
  
}
/*
flow of functions:
selection              -> $order-info['total'] wrong, cause shipping_cost is net
pre_confirmation_check -> $order-info['total'] wrong, cause shipping_cost is net
confirmation           -> $order-info['total'] right, cause shipping_cost is gross
process_button         -> $order-info['total'] right, cause shipping_cost is gross
before_process         -> $order-info['total'] wrong, cause shipping_cost is net
after_process          -> $order-info['total'] right, cause shipping_cost is gross
---------------
flow of url/path:
/xtcommerce/account.php
/xtcommerce/account_history_info.php
/xtcommerce/address_book.php
/xtcommerce/checkout_shipping.php
/xtcommerce/checkout_shipping.php
/xtcommerce/checkout_payment.php
/xtcommerce/checkout_confirmation.php
*/

?>
