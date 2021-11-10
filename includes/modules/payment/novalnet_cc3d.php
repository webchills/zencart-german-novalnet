<?php

#########################################################
#                                                       #
#  CC / CREDIT CARD 3d secure payment method class      #
#  This module is used for real time processing of      #
#  Credit card data of customers on 3d secure mode.     #
#                                                       #
#  Copyright (c) 2009 Novalnet AG                       #
#                                                       #
#  Released under the GNU General Public License        #
#  novalnet_cc3d module Created By Dixon Rajdaniel      #
#  This free contribution made by request.              #
#  If you have found this script usefull a small        #
#  recommendation as well as a comment on merchant form #
#  would be greatly appreciated.                        #
#                                                       #
#  Version : novalnet_cc3d.php vxtcCC3D1.3.1 2009-03-01 #
#                                                       #
#########################################################


class novalnet_cc3d {
  var $code;
  var $title;
  var $description;
  var $enabled;
  var $proxy;  

  function novalnet_cc3d() {
    global $order;
    $this->code = 'novalnet_cc3d';
    $this->title = MODULE_PAYMENT_NOVALNET_CC3D_TEXT_TITLE;
    #$this->public_title = MODULE_PAYMENT_NOVALNET_CC3D_TEXT_PUBLIC_TITLE;
    $this->description = MODULE_PAYMENT_NOVALNET_CC3D_TEXT_DESCRIPTION;
    $this->sort_order = MODULE_PAYMENT_NOVALNET_CC3D_SORT_ORDER;
    $this->enabled = ((MODULE_PAYMENT_NOVALNET_CC3D_STATUS == 'True') ? true : false);
    $this->proxy        = MODULE_PAYMENT_NOVALNET_CC3D_PROXY;    

    if ((int)MODULE_PAYMENT_NOVALNET_CC3D_ORDER_STATUS_ID > 0) {
      $this->order_status = MODULE_PAYMENT_NOVALNET_CC3D_ORDER_STATUS_ID;
    }

    if (is_object($order)) $this->update_status();
    $this->form_action_url = 'https://payport.novalnet.de/global_pci_payport';
  }
  
  ### calculate zone matches and flag settings to determine whether this module should display to customers or not ###
  function update_status() {
    global $order, $db;

    if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_NOVALNET_CC3D_ZONE > 0) ) {
      $check_flag = false;
      $check = $db->Execute("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_NOVALNET_CC3D_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
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
    $onFocus = ' onfocus="methodSelect(\'pmt-' . $this->code . '\')"';
     if(count($HTTP_POST_VARS)==0 || $HTTP_POST_VARS=='') $HTTP_POST_VARS = $_POST;

    $card_holder = '';
    if (isset($HTTP_POST_VARS['cc3d_holder'])) {$card_holder = $HTTP_POST_VARS['cc3d_holder'];}
    if(!$card_holder and isset($_SESSION['cc3d_holder'])){$card_holder = $_SESSION['cc3d_holder'];}
    if(!$card_holder){$card_holder=$order->billing['firstname'].' '.$order->billing['lastname'];}
    $cc3d_no = '';
    if (isset($HTTP_POST_VARS['cc3d_no'])) {$cc3d_no = $HTTP_POST_VARS['cc3d_no'];}
    if(!$cc3d_no and isset($_SESSION['cc3d_no'])){$cc3d_no = $_SESSION['cc3d_no'];}
    $cc3d_exp_month = '';
    if (isset($HTTP_POST_VARS['cc3d_exp_month'])) {$cc3d_exp_month = $HTTP_POST_VARS['cc3d_exp_month'];}
    if(!$cc3d_exp_month and isset($_SESSION['cc3d_exp_month'])){$cc3d_exp_month = $_SESSION['cc3d_exp_month'];}
    $cc3d_exp_year = '';
    if (isset($HTTP_POST_VARS['cc3d_exp_year'])) {$cc3d_exp_year = $HTTP_POST_VARS['cc3d_exp_year'];}
    if(!$cc3d_exp_year and isset($_SESSION['cc3d_exp_year'])){$cc3d_exp_year = $_SESSION['cc3d_exp_year'];}
    $cc3d_cvc2 = '';
    if (isset($HTTP_POST_VARS['cc3d_cvc2'])) {$cc3d_cvc2 = $HTTP_POST_VARS['cc3d_cvc2'];}
    if(!$cc3d_cvc2 and isset($_SESSION['cc3d_cvc2'])){$cc3d_cvc2 = $_SESSION['cc3d_cvc2'];}

    $book_info = str_replace('$BOOKINFO', MODULE_PAYMENT_NOVALNET_CC3D_BOOK_REF, MODULE_PAYMENT_NOVALNET_CC3D_TEXT_BOOKING_INFO);

    $expires_month[] = array ('id' => '', 'text' => MODULE_PAYMENT_NOVALNET_CC3D_TEXT_SELECT);
    for ($i = 1; $i < 13; $i ++) 
    {
       $expires_month[] = array ('id' => sprintf('%02d', $i), 'text' => strftime('%B', mktime(0, 0, 0, $i, 1, 2000)));
    }

    $today = getdate();
    $expires_year[] = array ('id' => '', 'text' => MODULE_PAYMENT_NOVALNET_CC3D_TEXT_SELECT);
    for ($i = $today['year']; $i < $today['year'] + 10; $i ++) 
    {
       $expires_year[] = array ('id' => strftime('%y', mktime(0, 0, 0, 1, 1, $i)), 'text' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)));
    }

    $selection = array('id' => $this->code,
                       'module' => $this->title,
                       'fields' => array(array('title' => MODULE_PAYMENT_NOVALNET_CC3D_TEXT_CARD_OWNER,
                                               'field' => zen_draw_input_field('cc3d_holder', $card_holder, 'id="'.$this->code.'-cc3d_holder"' . $onFocus),
                                               'tag' => $this->code.'-cc3d_holder'),
                                         array('title' => MODULE_PAYMENT_NOVALNET_CC3D_TEXT_CC_NO,
                                               'field' => zen_draw_input_field('cc3d_no', '', 'id="' . $this->code . '-cc3d_no"' . $onFocus),
                                               'tag' => $this->code . '-cc3d_no'),
                                         array('title' => MODULE_PAYMENT_NOVALNET_CC3D_TEXT_EXP_MONTH,
						'field' =>  zen_draw_pull_down_menu('cc3d_exp_month', $expires_month, $_SESSION['cc3d_exp_month'], 'id="' . $this->code . '-cc3d_exp_month"' . $onFocus),
						'tag' => $this->code . '-cc3d_exp_month'),
					 array('title' => MODULE_PAYMENT_NOVALNET_CC3D_TEXT_EXP_YEAR,
						'field' =>  zen_draw_pull_down_menu('cc3d_exp_year', $expires_year, $_SESSION['cc3d_exp_year'], 'id="' . $this->code . '-cc3d_exp_year"' . $onFocus),
                                                'tag' => $this->code . '-cc3d_exp_year'),
					 array('title' => MODULE_PAYMENT_NOVALNET_CC3D_TEXT_CVC,
                                                'field' => zen_draw_input_field('cc3d_cvc2', '' /*$cc3d_cvc2*/, 'id="' . $this->code . '-cc3d_cvc2"' . $onFocus. 'maxlength=3').MODULE_PAYMENT_NOVALNET_CC3D_TEXT_CVC2.$book_info,
                                                'tag' => $this->code . '-cc3d_cvc2')
		               ));

    /*if(function_exists('get_percent'))
    {
        $selection['module_cost'] = $GLOBALS['ot_payment']->get_percent($this->code);
    }*/

    return $selection;
  }

  ### Precheck to Evaluate the Bank Datas ###
  function pre_confirmation_check() {
    global $HTTP_POST_VARS, $_POST,$messageStack;
    if(count($HTTP_POST_VARS)==0 || $HTTP_POST_VARS=='') $HTTP_POST_VARS = $_POST;
	
	
    $HTTP_POST_VARS['cc3d_holder'] = trim($HTTP_POST_VARS['cc3d_holder']);
    $HTTP_POST_VARS['cc3d_no'] = trim($HTTP_POST_VARS['cc3d_no']);
    $HTTP_POST_VARS['cc3d_exp_month'] = trim($HTTP_POST_VARS['cc3d_exp_month']);
    $HTTP_POST_VARS['cc3d_exp_year'] = trim($HTTP_POST_VARS['cc3d_exp_year']);
    $HTTP_POST_VARS['cc3d_cvc2'] = trim($HTTP_POST_VARS['cc3d_cvc2']);	
    #echo'<pre>';var_dump($_REQUEST); exit;
    $error = '';
	
    if (defined('MODULE_PAYMENT_NOVALNET_CC3D_MANUAL_CHECK_LIMIT') and MODULE_PAYMENT_NOVALNET_CC3D_MANUAL_CHECK_LIMIT){
      if ( (!defined('MODULE_PAYMENT_NOVALNET_CC3D_PRODUCT_ID2') or !MODULE_PAYMENT_NOVALNET_CC3D_PRODUCT_ID2 or preg_match('/[^\d]/', MODULE_PAYMENT_NOVALNET_CC3D_PRODUCT_ID2)) or (!defined('MODULE_PAYMENT_NOVALNET_CC3D_TARIFF_ID2') or !MODULE_PAYMENT_NOVALNET_CC3D_TARIFF_ID2 or preg_match('/[^\d]/', MODULE_PAYMENT_NOVALNET_CC3D_TARIFF_ID2))){
          $error = 'Product-ID2 and/or Tariff-ID2 missing';
      }
    }

  
    if(!$HTTP_POST_VARS['cc3d_holder'] || strlen($HTTP_POST_VARS['cc3d_holder'])<MODULE_PAYMENT_NOVALNET_CC3D_TEXT_CARD_OWNER_LENGTH) $error = MODULE_PAYMENT_NOVALNET_CC3D_TEXT_JS_CARD_OWNER;
    if(!$HTTP_POST_VARS['cc3d_no'] || strlen($HTTP_POST_VARS['cc3d_no'])<MODULE_PAYMENT_NOVALNET_CC3D_TEXT_CC_NO_LENGTH) $error = MODULE_PAYMENT_NOVALNET_CC3D_TEXT_JS_CC_NO;
    if(!$HTTP_POST_VARS['cc3d_exp_month'] || strlen($HTTP_POST_VARS['cc3d_exp_month'])<MODULE_PAYMENT_NOVALNET_CC3D_TEXT_EXP_MONTH_LENGTH) $error = MODULE_PAYMENT_NOVALNET_CC3D_TEXT_JS_EXP_MONTH;
    if(!$HTTP_POST_VARS['cc3d_exp_year'] || strlen($HTTP_POST_VARS['cc3d_exp_year'])<MODULE_PAYMENT_NOVALNET_CC3D_TEXT_EXP_YEAR_LENGTH) $error = MODULE_PAYMENT_NOVALNET_CC3D_TEXT_JS_EXP_YEAR;
    if(!$HTTP_POST_VARS['cc3d_cvc2'] || strlen($HTTP_POST_VARS['cc3d_cvc2'])<MODULE_PAYMENT_NOVALNET_CC3D_TEXT_CVC_LENGTH) $error = MODULE_PAYMENT_NOVALNET_CC3D_TEXT_JS_CVC;
    if($HTTP_POST_VARS['cc3d_cvc2']=='000' || $HTTP_POST_VARS['cc3d_cvc2']<1) $error = MODULE_PAYMENT_NOVALNET_CC3D_TEXT_JS_CVC2;

    if($HTTP_POST_VARS['cc3d_exp_year'] == date(y))
    {
    if($HTTP_POST_VARS['cc3d_exp_month'] < date(m)){
	$error = "Select Valid Expire Month/Year";
    }
    }

    if($HTTP_POST_VARS['cc3d_exp_year'] < date(y))
    {
   	$error = "Select Valid Expire Year";
    }
    $_SESSION['cc3d_holder'] = $HTTP_POST_VARS['cc3d_holder'];
    $_SESSION['cc3d_no'] = $HTTP_POST_VARS['cc3d_no'];
    $_SESSION['cc3d_exp_month'] = $HTTP_POST_VARS['cc3d_exp_month'];
    $_SESSION['cc3d_exp_year'] = $HTTP_POST_VARS['cc3d_exp_year'];
    $_SESSION['cc3d_cvc2'] = $HTTP_POST_VARS['cc3d_cvc2'];
	  if(!MODULE_PAYMENT_NOVALNET_CC3D_VENDOR_ID || !MODULE_PAYMENT_NOVALNET_CC3D_AUTH_CODE || !MODULE_PAYMENT_NOVALNET_CC3D_PRODUCT_ID || !MODULE_PAYMENT_NOVALNET_CC3D_TARIFF_ID)
    {
      $error = MODULE_PAYMENT_NOVALNET_CC3D_TEXT_JS_NN_MISSING;
    }
    if(trim($error)!='') {    
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


      $cardnoLength = strlen(str_replace(' ','',$HTTP_POST_VARS['cc3d_no']));
      $crdNo = str_replace(' ','',$HTTP_POST_VARS['cc3d_no']);
      $cardnoInfo = '';
      $chkLength = $cardnoLength-5;
      for($i=0;$i<$cardnoLength;$i++){
	if($i >= $chkLength){
	$cardnoInfo .= '*';
	}else{
	$cardnoInfo .= $crdNo[$i];
	}
      }

      $dcardnoLength = strlen(str_replace(' ','',$HTTP_POST_VARS['cc3d_cvc2']));
      $card3dInfo = '';
      for($i=0;$i<$dcardnoLength;$i++){
      $card3dInfo .= '*';
      }

      $confirmation = array('fields' => array(array('title' => MODULE_PAYMENT_NOVALNET_CC3D_TEXT_CARD_OWNER,
                          'field' => $HTTP_POST_VARS['cc3d_holder']),
                    array('title' => MODULE_PAYMENT_NOVALNET_CC3D_TEXT_CC_NO,
                          'field' => $cardnoInfo),
                    array('title' => MODULE_PAYMENT_NOVALNET_CC3D_TEXT_EXP_MONTH,
			   'field' => $HTTP_POST_VARS['cc3d_exp_month']),
		    array('title' => MODULE_PAYMENT_NOVALNET_CC3D_TEXT_EXP_YEAR,
                           'field' => $HTTP_POST_VARS['cc3d_exp_year']),
		    array('title' => MODULE_PAYMENT_NOVALNET_CC3D_TEXT_CVC,
                           'field' => $card3dInfo)
                          ));

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
      $payment_error_return     = 'payment_error='.$this->code.'&error='.$err;
      zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
    }
    $amount = preg_replace('/^0+/', '', $amount);
    $amount = sprintf('%0.2f', $amount);
    $amount = str_replace('.', '', $amount);
    #echo __CLASS__.' : '.$order->info['total']." <=> $amount<hr />";

    $product_id         = MODULE_PAYMENT_NOVALNET_CC3D_PRODUCT_ID;
    $tariff_id          = MODULE_PAYMENT_NOVALNET_CC3D_TARIFF_ID;
    $manual_check_limit = trim(MODULE_PAYMENT_NOVALNET_CC3D_MANUAL_CHECK_LIMIT);
    $manual_check_limit = str_replace(',', '', $manual_check_limit);
    $manual_check_limit = str_replace('.', '', $manual_check_limit);

    if($manual_check_limit && $amount>=$manual_check_limit)
    {
      $product_id = MODULE_PAYMENT_NOVALNET_CC3D_PRODUCT_ID2;
      $tariff_id = MODULE_PAYMENT_NOVALNET_CC3D_TARIFF_ID2;
    }

    $user_ip = $this->getRealIpAddr();

    $checkout_url = zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL');

    if(strstr($checkout_url, '?'))
    {
      $checkout_url = str_replace(' ', '', $checkout_url);
      if(substr($checkout_url,-1)=='?')$error_url = $checkout_url.'payment_error=novalnet_cc3d&error=$ERROR_MESSAGE ($STATUS)';
      else $error_url = $checkout_url.'&payment_error=novalnet_cc3d&error=$ERROR_MESSAGE ($STATUS)';
    }
    else $error_url = $checkout_url.'?payment_error=novalnet_cc3d&error=$ERROR_MESSAGE ($STATUS)';

    $test_mode = (strtolower(MODULE_PAYMENT_NOVALNET_CC3D_TEST_MODE) == 'true' or MODULE_PAYMENT_NOVALNET_CC3D_TEST_MODE == '1')? 1: 0;


    $oldreturnurl=zen_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL');
    $old_value=explode(':',$oldreturnurl);
    $new_protocol_value=(empty($_SERVER["HTTPS"])) ? 'http' : 'https';
    $return_url=$new_protocol_value.':'.$old_value[1];

    $process_button_string = zen_draw_hidden_field('vendor', MODULE_PAYMENT_NOVALNET_CC3D_VENDOR_ID) .
       zen_draw_hidden_field('product', $product_id) .
       zen_draw_hidden_field('key', '6') .
       zen_draw_hidden_field('tariff', $tariff_id) .
       zen_draw_hidden_field('auth_code', MODULE_PAYMENT_NOVALNET_CC3D_AUTH_CODE) .
       zen_draw_hidden_field('currency', $order->info['currency']) .
       zen_draw_hidden_field('amount', $amount) .
       zen_draw_hidden_field('first_name', $this->html_to_utf8($order->customer['firstname'])) .
       zen_draw_hidden_field('last_name', $this->html_to_utf8($order->customer['lastname'])) .
       zen_draw_hidden_field('email', $order->customer['email_address']) .
       zen_draw_hidden_field('street', $this->html_to_utf8($order->customer['street_address'])) .
       zen_draw_hidden_field('search_in_street', '1') .
       zen_draw_hidden_field('city', $this->html_to_utf8($order->customer['city'])) .
       zen_draw_hidden_field('zip', $order->customer['postcode']) .
       zen_draw_hidden_field('country_code', $order->customer['country']['iso_code_2']) .
       zen_draw_hidden_field('lang', MODULE_PAYMENT_NOVALNET_CC3D_TEXT_LANG) .
       zen_draw_hidden_field('remote_ip', $user_ip) .
       zen_draw_hidden_field('tel', $order->customer['telephone']) .
       zen_draw_hidden_field('fax', $customer['customers_fax']) .
       zen_draw_hidden_field('birth_date', $customer['customers_dob']) .
       zen_draw_hidden_field('session', zen_session_id()) .
       zen_draw_hidden_field('cc_holder', $this->html_to_utf8($HTTP_POST_VARS['cc3d_holder'])) .
       zen_draw_hidden_field('cc_no', $HTTP_POST_VARS['cc3d_no']) .
       zen_draw_hidden_field('cc_exp_month', $HTTP_POST_VARS['cc3d_exp_month']) .
       zen_draw_hidden_field('cc_exp_year', $HTTP_POST_VARS['cc3d_exp_year']) .
       zen_draw_hidden_field('cc_cvc2', $HTTP_POST_VARS['cc3d_cvc2']) . 
       zen_draw_hidden_field('return_url', $return_url) .
       zen_draw_hidden_field('return_method', 'POST') .
       zen_draw_hidden_field('error_return_url', $error_url) .
       zen_draw_hidden_field('test_mode', $test_mode) .
       zen_draw_hidden_field('error_return_method', 'POST');

    return $process_button_string;
  }

  ### Insert the Novalnet Transaction ID in DB ###
  function before_process() {
    global $HTTP_POST_VARS, $_POST, $order, $currencies, $customer_id,$messageStack;
	if($_POST['tid'] && $_POST['status'] == '100'){
		if( $this->order_status ) {
				$order->info['order_status'] = $this->order_status;
		}
		$test_mode = (strtolower(MODULE_PAYMENT_NOVALNET_CC3D_TEST_MODE) == 'true' or MODULE_PAYMENT_NOVALNET_CC3D_TEST_MODE == '1')? 1: 0;
		$test_mode_value=( $_POST['test_mode'] == 1) ? $_POST['test_mode'] : $test_mode;
		if ($test_mode_value){
			$order->info['comments'] .= MODULE_PAYMENT_NOVALNET_CC3D_TEST_ORDER_MESSAGE;
		}
		if(count($HTTP_POST_VARS)==0 || $HTTP_POST_VARS=='') $HTTP_POST_VARS = $_POST;
		$order->info['comments'] .= 'Novalnet Transaction ID : '.$HTTP_POST_VARS['tid'];
		$_SESSION['nn_tid'] = $HTTP_POST_VARS['tid'];
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
    global $order, $customer_id, $insert_id,$db,$_POST;

    $product_id = MODULE_PAYMENT_NOVALNET_CC3D_PRODUCT_ID;
    $tariff_id = MODULE_PAYMENT_NOVALNET_CC3D_TARIFF_ID;
	if($_SESSION['nn_tid']){
		### Pass the Order Reference to paygate ##
		$url = 'https://payport.novalnet.de/paygate.jsp';
		$urlparam = 'vendor='.MODULE_PAYMENT_NOVALNET_CC3D_VENDOR_ID.'&product='.$product_id.'&key=6&tariff='.$tariff_id;
		$urlparam .= '&auth_code='.MODULE_PAYMENT_NOVALNET_CC3D_AUTH_CODE.'&status=100&tid='.$_SESSION['nn_tid'].'&reference=BNR-'.$insert_id.'&vwz2='.MODULE_PAYMENT_NOVALNET_CC3D_TEXT_ORDERNO.''.$insert_id.'&vwz3='.MODULE_PAYMENT_NOVALNET_CC3D_TEXT_ORDERDATE.''.date('Y-m-d H:i:s');
		$urlparam .= '&order_no='.$insert_id;
		list($errno, $errmsg, $data) = $this->perform_https_request($url, $urlparam);
		unset($_SESSION['nn_tid']);
		### Implement here the Emailversand and further functions, incase if you want to send a own email ###
	}
    return false;
  }

  ### Used to display error message details ###
  // @return array
  function get_error() {
    global $HTTP_GET_VARS, $_GET;
    if(count($HTTP_GET_VARS)==0 || $HTTP_GET_VARS=='') $HTTP_GET_VARS = $_GET;

    $error = array('title' => MODULE_PAYMENT_NOVALNET_CC3D_TEXT_ERROR, 'error' => stripslashes(utf8_decode($HTTP_GET_VARS['error'])));

    return $error;
  }

  ### Check to see whether module is installed ###
  // @return boolean
  function check() {
    global $db;
    if (!isset($this->_check)) {
      $check_query = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_NOVALNET_CC3D_STATUS'");
      $this->_check = $check_query->RecordCount();
    }
    return $this->_check;
  }

  ### Install the payment module and its configuration settings ###
  function install() {
    global $db;
    $db->Execute("alter table ".TABLE_ORDERS." modify payment_method varchar(250)");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Allowed zones','MODULE_PAYMENT_NOVALNET_CC3D_ALLOWED', '','Please enter the zones <b>separately</b> which should be allowed to use this modul (e. g. AT,DE (leave empty if you want to allow all zones))', '6', '0', now())");    
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable CC3D Secure Module', 'MODULE_PAYMENT_NOVALNET_CC3D_STATUS', 'True', 'Do you want to activate the Credit Card 3D Secure Method(CC3D) of Novalnet AG?', '6', '1', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Test Mode', 'MODULE_PAYMENT_NOVALNET_CC3D_TEST_MODE', 'True', 'Do you want to enable the test mode?', '6', '2', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Please contact sales@novalnet.de if you do not have any of the following Novalnet IDs!<BR><P>Wenn Sie keine oder irgendeine der folgenden Novalnet IDs nicht haben sollten, bitte sich an sales@novalnet.de wenden!<BR><P>Novalnet Merchant ID', 'MODULE_PAYMENT_NOVALNET_CC3D_VENDOR_ID', '', 'Your Merchant ID of Novalnet', '6', '3', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Novalnet Authorisation Code', 'MODULE_PAYMENT_NOVALNET_CC3D_AUTH_CODE', '', 'Your Authorisation Code of Novalnet', '6', '4', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Novalnet Product ID', 'MODULE_PAYMENT_NOVALNET_CC3D_PRODUCT_ID', '', 'Your Product ID of Novalnet', '6', '5', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Novalnet Tariff ID', 'MODULE_PAYMENT_NOVALNET_CC3D_TARIFF_ID', '', 'Your Tariff ID of Novalnet', '6', '6', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Manual checking amount in cents', 'MODULE_PAYMENT_NOVALNET_CC3D_MANUAL_CHECK_LIMIT', '', 'Please enter the amount in cents', '6', '7', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Your second Product ID in Novalnet', 'MODULE_PAYMENT_NOVALNET_CC3D_PRODUCT_ID2', '', 'for the manual checking', '6', '8', now())"); 

    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('The Tariff ID of the second product', 'MODULE_PAYMENT_NOVALNET_CC3D_TARIFF_ID2', '', 'for the manual checking', '6', '9', now())");    
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Your Booking Reference at Novalnet', 'MODULE_PAYMENT_NOVALNET_CC3D_BOOK_REF', '', 'Your Booking Reference at Novalnet', '6', '10', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort order of display.', 'MODULE_PAYMENT_NOVALNET_CC3D_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '11', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('Set Order Status', 'MODULE_PAYMENT_NOVALNET_CC3D_ORDER_STATUS_ID', '0', 'Set the status of orders made with this payment module to this value', '6', '12', 'zen_cfg_pull_down_order_statuses(', 'zen_get_order_status_name', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Payment Zone', 'MODULE_PAYMENT_NOVALNET_CC3D_ZONE', '0', 'If a zone is selected, only enable this payment method for that zone.', '6', '13', 'zen_get_zone_class_title', 'zen_cfg_pull_down_zone_classes(', now())");
     $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Proxy', 'MODULE_PAYMENT_NOVALNET_CC3D_PROXY', '0', 'If you use a Proxy Server, enter the Proxy Server IP here (e.g. www.proxy.de:80)', '6', '14', now())");    
  }
   
  ### Remove the module and all its settings ###
  function remove() {
    global $db;
    $db->Execute("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
  }

  ### Internal list of configuration keys used for configuration of the module ###
  // @return array
  function keys() {
    return array('MODULE_PAYMENT_NOVALNET_CC3D_ALLOWED', 'MODULE_PAYMENT_NOVALNET_CC3D_STATUS', 'MODULE_PAYMENT_NOVALNET_CC3D_TEST_MODE', 'MODULE_PAYMENT_NOVALNET_CC3D_VENDOR_ID', 'MODULE_PAYMENT_NOVALNET_CC3D_AUTH_CODE', 'MODULE_PAYMENT_NOVALNET_CC3D_PRODUCT_ID', 'MODULE_PAYMENT_NOVALNET_CC3D_TARIFF_ID', 'MODULE_PAYMENT_NOVALNET_CC3D_MANUAL_CHECK_LIMIT', 'MODULE_PAYMENT_NOVALNET_CC3D_PRODUCT_ID2', 'MODULE_PAYMENT_NOVALNET_CC3D_TARIFF_ID2', 'MODULE_PAYMENT_NOVALNET_CC3D_BOOK_REF', 'MODULE_PAYMENT_NOVALNET_CC3D_SORT_ORDER', 'MODULE_PAYMENT_NOVALNET_CC3D_ORDER_STATUS_ID', 'MODULE_PAYMENT_NOVALNET_CC3D_ZONE', 'MODULE_PAYMENT_NOVALNET_CC3D_PROXY');
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
