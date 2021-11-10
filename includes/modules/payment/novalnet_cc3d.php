<?php
 
#########################################################
#                                                       #
#  CC3D / CREDIT CARD 3d secure payment method class    #
#  This module is used for real time processing of      #
#  Credit card data of customers.                       #
#                                                       #
#  Released under the GNU General Public License.       #
#  This free contribution made by request.              #
#  If you have found this script useful a small         #
#  recommendation as well as a comment on merchant form #
#  would be greatly appreciated.                        #
#                                                       #
#  Script : novalnet_cc3d.php                           #
#                                                       #
#########################################################

class novalnet_cc3d {
  
  var $code;
  var $title;
  var $description;
  var $enabled;
  var $payment_key = '6';
  var $vendor_id;
  var $auth_code;
  var $product_id;
  var $tariff_id;
  var $manual_check_limit;
  var $product_id2;
  var $tariff_id2;
  var $proxy;
  
  function novalnet_cc3d() {
    global $order;
	
	
	$this->vendor_id   = trim(MODULE_PAYMENT_NOVALNET_CC3D_VENDOR_ID);
    $this->auth_code   = trim(MODULE_PAYMENT_NOVALNET_CC3D_AUTH_CODE);
    $this->product_id  = trim(MODULE_PAYMENT_NOVALNET_CC3D_PRODUCT_ID);
    $this->tariff_id   = trim(MODULE_PAYMENT_NOVALNET_CC3D_TARIFF_ID);
	$this->manual_check_limit = trim(MODULE_PAYMENT_NOVALNET_CC3D_MANUAL_CHECK_LIMIT);
	$this->product_id2 = trim(MODULE_PAYMENT_NOVALNET_CC3D_PRODUCT_ID2);
    $this->tariff_id2 =  trim(MODULE_PAYMENT_NOVALNET_CC3D_TARIFF_ID2);
	$this->test_mode = (strtolower(MODULE_PAYMENT_NOVALNET_CC3D_TEST_MODE) == 'true' or MODULE_PAYMENT_NOVALNET_CC3D_TEST_MODE == '1')? 1: 0;
	
    $this->code = 'novalnet_cc3d';
    $this->title = MODULE_PAYMENT_NOVALNET_CC3D_TEXT_TITLE;
    $this->public_title = MODULE_PAYMENT_NOVALNET_CC3D_TEXT_PUBLIC_TITLE;
    $this->description = MODULE_PAYMENT_NOVALNET_CC3D_TEXT_DESCRIPTION;
    $this->sort_order = MODULE_PAYMENT_NOVALNET_CC3D_SORT_ORDER;
    $this->enabled = ((MODULE_PAYMENT_NOVALNET_CC3D_STATUS == 'True') ? true : false);
    $this->proxy        = MODULE_PAYMENT_NOVALNET_CC3D_PROXY;    
    
	
	if(MODULE_PAYMENT_NOVALNET_CC3D_LOGO_STATUS == 'True'){
		$this->public_title = 'Novalnet'.' '.MODULE_PAYMENT_NOVALNET_CC3D_TEXT_PUBLIC_TITLE;
		$this->title = 'Novalnet'.' '.MODULE_PAYMENT_NOVALNET_CC3D_TEXT_TITLE;
	}
	
	$this->checkConfigure();	
	
    if ((int)MODULE_PAYMENT_NOVALNET_CC3D_ORDER_STATUS_ID > 0) {
      $this->order_status = MODULE_PAYMENT_NOVALNET_CC3D_ORDER_STATUS_ID;
    }

    if (is_object($order)) $this->update_status();
    $this->form_action_url = 'https://payport.novalnet.de/global_pci_payport';
	
	if($_POST['session'] && $_SESSION['payment'] == $this->code){
			$this->checkSecurity();
		}
	
  }
  
  function checkConfigure() {
		if (IS_ADMIN_FLAG == true) {
			$this->title = MODULE_PAYMENT_NOVALNET_CC3D_TEXT_TITLE; // Payment module title in Admin
			if(MODULE_PAYMENT_NOVALNET_CC3D_LOGO_STATUS == 'True'){
				$this->public_title = 'Novalnet'.' '.MODULE_PAYMENT_NOVALNET_CC3D_TEXT_PUBLIC_TITLE;
				$this->title = 'Novalnet'.' '.MODULE_PAYMENT_NOVALNET_CC3D_TEXT_TITLE;
			}
			if ($this->enabled == 'true' && (!$this->vendor_id || !$this->auth_code || !$this->product_id || !$this->tariff_id )) {
				$this->title .=  '<span class="alert">'.MODULE_PAYMENT_NOVALNET_CC3D_NOT_CONFIGURED.'</span>';
			} elseif ($this->test_mode == '1') {
				$this->title .= '<span class="alert">'.MODULE_PAYMENT_NOVALNET_CC3D_IN_TEST_MODE.'</span>';
			}
			
		}
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

    //$book_info = str_replace('$BOOKINFO', MODULE_PAYMENT_NOVALNET_CC3D_BOOK_REF, MODULE_PAYMENT_NOVALNET_CC3D_TEXT_BOOKING_INFO);

    //$expires_month[] = array ('id' => '', 'text' => MODULE_PAYMENT_NOVALNET_CC3D_TEXT_SELECT);
    for ($i = 1; $i < 13; $i ++) 
    {
       $expires_month[] = array ('id' => sprintf('%02d', $i), 'text' => strftime('%B', mktime(0, 0, 0, $i, 1, 2000)));
	   if($i==3) $expires_month[$i-1]['text']= (MODULE_PAYMENT_NOVALNET_CC3D_TEXT_LANG=='DE')?'März':'March';
    }

    $today = getdate();
    //$expires_year[] = array ('id' => '', 'text' => MODULE_PAYMENT_NOVALNET_CC3D_TEXT_SELECT);
    for ($i = $today['year']; $i < $today['year'] + 10; $i ++) 
    {
       $expires_year[] = array ('id' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)), 'text' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)));
    }

    $selection = array('id' => $this->code,
                       'module' => $this->public_title,
                       'fields' => array(array('title' => '', 'field' => MODULE_PAYMENT_NOVALNET_CC3D_TEXT_BOOKING_INFO),
										array('title' => MODULE_PAYMENT_NOVALNET_CC3D_TEXT_CARD_OWNER,
                                               'field' => zen_draw_input_field('cc3d_holder', $card_holder, 'id="'.$this->code.'-cc3d_holder"' . $onFocus),
                                               'tag' => $this->code.'-cc3d_holder'),
                                         array('title' => MODULE_PAYMENT_NOVALNET_CC3D_TEXT_CC_NO,
                                               'field' => zen_draw_input_field('cc3d_no', '', 'id="' . $this->code . '-cc3d_no"' . $onFocus),
                                               'tag' => $this->code . '-cc3d_no'),
                                         array('title' => MODULE_PAYMENT_NOVALNET_CC3D_TEXT_EXP_MONTHS_YEARS,
						'field' =>  zen_draw_pull_down_menu('cc3d_exp_month', $expires_month,  'id="' . $this->code . '-cc3d_exp_month"' . $onFocus)." ".zen_draw_pull_down_menu('cc3d_exp_year', $expires_year, 'id="' . $this->code . '-cc3d_exp_year"' . $onFocus),
						'tag' => $this->code . '-cc3d_exp_month',$this->code . '-cc3d_exp_year'),
					 /*array('title' => MODULE_PAYMENT_NOVALNET_CC3D_TEXT_EXP_YEAR,
						'field' =>  zen_draw_pull_down_menu('cc3d_exp_year', $expires_year, $_SESSION['cc3d_exp_year'], 'id="' . $this->code . '-cc3d_exp_year"' . $onFocus),
                                                'tag' => $this->code . '-cc3d_exp_year'),*/
					 array('title' => MODULE_PAYMENT_NOVALNET_CC3D_TEXT_CVC,
                                                'field' => zen_draw_input_field('cc3d_cvc2', '' /*$cc3d_cvc2*/, 'id="' . $this->code . '-cc3d_cvc2"' . $onFocus. 'maxlength=3').MODULE_PAYMENT_NOVALNET_CC3D_TEXT_CVC2,
                                                'tag' => $this->code . '-cc3d_cvc2'),
					array('title' => '', 'field' => MODULE_PAYMENT_NOVALNET_CC3D_BOOK_REF)
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
	
		$cc3d_holder = str_replace(' ','',trim($HTTP_POST_VARS['cc3d_holder']));
		$cc3d_no = str_replace(' ','',trim($HTTP_POST_VARS['cc3d_no']));
		$cc3d_cvc2 = str_replace(' ','',trim($HTTP_POST_VARS['cc3d_cvc2']));
		$cc3d_year = $HTTP_POST_VARS['cc3d_exp_year'];
		$cc3d_month = $HTTP_POST_VARS['cc3d_exp_month'];
  	
    #echo'<pre>';var_dump($_REQUEST); exit;
    $error = '';
	
		if (!function_exists('curl_init') && ($this->code=='novalnet_cc3d')){
		ini_set('display_errors', 1);
		ini_set('error_reporting', E_ALL);

		$error =  MODULE_PAYMENT_NOVALNET_CC3D_CURL_MESSAGE;
		}
		
		if(!$this->vendor_id || !$this->auth_code || !$this->product_id || !$this->tariff_id)
    {
      $error = MODULE_PAYMENT_NOVALNET_CC3D_TEXT_JS_NN_MISSING;
    }
	elseif(!empty($this->manual_check_limit) && (!$this->product_id2 || !$this->tariff_id2)){
					$error = MODULE_PAYMENT_NOVALNET_CC3D_TEXT_JS_NN_ID2_MISSING;				
				}
    elseif(!$cc3d_holder || (preg_match('/[#%\^<>@$=*!]/',$cc3d_holder))) $error = MODULE_PAYMENT_NOVALNET_CC3D_TEXT_JS_CARD_OWNER;
	elseif(preg_match('/[^\d]/',$cc3d_no)) $error = MODULE_PAYMENT_NOVALNET_CC3D_TEXT_JS_CC_NO_ERR;
    elseif(!$cc3d_no || strlen($cc3d_no)<MODULE_PAYMENT_NOVALNET_CC3D_TEXT_CC_NO_LENGTH) $error = MODULE_PAYMENT_NOVALNET_CC3D_TEXT_JS_CC_NO;
	elseif($cc3d_year <= date('Y')){
	if($cc3d_year == date(Y)){
		     if($cc3d_month < date('m'))$error = MODULE_PAYMENT_NOVALNET_CC3D_TEXT_ERROR_EXP_MONTH;
			}else $error = MODULE_PAYMENT_NOVALNET_CC3D_TEXT_ERROR_EXP_YEAR;
}
    elseif(!$cc3d_cvc2 || strlen($cc3d_cvc2)<MODULE_PAYMENT_NOVALNET_CC3D_TEXT_CVC_LENGTH) $error = MODULE_PAYMENT_NOVALNET_CC3D_TEXT_JS_CVC2;
    elseif($cc3d_cvc2=='000' || $cc3d_cvc2<3) $error = MODULE_PAYMENT_NOVALNET_CC3D_TEXT_JS_CVC2;

    $_SESSION['cc3d_holder']    = $cc3d_holder;
    $_SESSION['cc3d_no']        = $cc3d_no;
    $_SESSION['cc3d_exp_month'] = $cc3d_month;
    $_SESSION['cc3d_exp_year']  = $cc3d_year;
    $_SESSION['cc3d_cvc2']      = $cc3d_cvc2;
	
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
    
	$cc_number = $HTTP_POST_VARS['cc3d_no'];
	$cc3d_exp_month = $HTTP_POST_VARS['cc3d_exp_month'];
	$cc3d_exp_year = $HTTP_POST_VARS['cc3d_exp_year'];
	$cvv_cvc = $HTTP_POST_VARS['cc3d_cvc2'];

	if($cc_number) {
		$cc_number=str_replace(' ','',$cc_number);
		$cc_number=str_pad(substr($cc_number,0,6),strlen($cc_number)-4,'*',STR_PAD_RIGHT).substr($cc_number,-4);
	}
	if($cc3d_exp_month) {
		$cc3d_exp_month=str_pad('',2,'*',STR_PAD_RIGHT);
	}
	if($cc3d_exp_year) {
	
		$cc3d_exp_year=str_pad(substr($cc3d_exp_year,0,-2),strlen($cc3d_exp_year),'*',STR_PAD_RIGHT);
	}
	if($cvv_cvc) {
		$cvv_cvc=str_pad('',strlen($cvv_cvc),'*',STR_PAD_RIGHT);
	}
	
    $confirmation = array('fields' => array(array('title' => MODULE_PAYMENT_NOVALNET_CC3D_TEXT_CARD_OWNER,
                          'field' => $HTTP_POST_VARS['cc3d_holder']),
                    array('title' => MODULE_PAYMENT_NOVALNET_CC3D_TEXT_CC_NO,
                          'field' => $cc_number),
                    array('title' => MODULE_PAYMENT_NOVALNET_CC3D_TEXT_EXP_MONTH,
						  'field' => $cc3d_exp_month),
					array('title' => MODULE_PAYMENT_NOVALNET_CC3D_TEXT_EXP_YEAR,
                          'field' => $cc3d_exp_year),
					array('title' => MODULE_PAYMENT_NOVALNET_CC3D_TEXT_CVC,
                          'field' => $cvv_cvc)
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
	$nn_customer_id = (isset($_SESSION['customer_id'])) ? $_SESSION['customer_id'] : '';
    $customer = $db->Execute("SELECT customers_gender, customers_dob, customers_fax FROM ". TABLE_CUSTOMERS . " WHERE customers_id='". (int)$nn_customer_id."'");

    if ($customer->RecordCount() > 0){
      $customer = $customer->fields;
    }
    list($customer['customers_dob'], $extra) = explode(' ', $customer['customers_dob']);

    if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status_add_tax_ot'] == 1) {
       $totalamount=$order->info['total'] + $order->info['tax'];
       } else { 
       $totalamount=$order->info['total'];
    }
	
	$totalamount = number_format($totalamount * $currencies->get_value($order->info['currency']),2);
	$amount = str_replace(',', '', $totalamount);
	$amount = intval(round($amount*100));
	
   //$amount =sprintf('%.2f', $totalamount);

    if (preg_match('/[^\d\.]/', $amount) or !$amount){
      ### $amount contains some unallowed chars or empty ###
      $err                      = '$amount ('.$amount.') is empty or has a wrong format';
      $order->info['comments'] .= 'Novalnet Error Message : '.$err;
      $payment_error_return     = 'payment_error='.$this->code.'&error='.$err;
      zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
    }
    // $amount = preg_replace('/^0+/', '', $amount);
    // $amount = sprintf('%0.2f', $amount);
    // $amount = str_replace('.', '', $amount);
    #echo __CLASS__.' : '.$order->info['total']." <=> $amount<hr />";
   $_SESSION['nn_amount_cc3d'] = $amount;
	 $vendor_id   = $this->vendor_id;
    $auth_code   = $this->auth_code;
    $product_id  = $this->product_id;
    $tariff_id   = $this->tariff_id;
	//$customer_id = $_SESSION['customer_id'];

    $manual_check_limit = $this->manual_check_limit;
    $manual_check_limit = str_replace(',', '', $manual_check_limit);
    $manual_check_limit = str_replace('.', '', $manual_check_limit);

    if($manual_check_limit && $amount>=$manual_check_limit)
    {
      $product_id = $this->product_id2;
      $tariff_id  = $this->tariff_id2;
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
	
		$firstname = !empty($order->customer['firstname']) ? $order->customer['firstname'] : $order->billing['firstname'];
		$lastname = !empty($order->customer['lastname']) ? $order->customer['lastname'] : $order->billing['lastname'];
		$email_address = !empty($order->customer['email_address']) ? $order->customer['email_address'] : $order->billing['email_address'];
		$street_address = !empty($order->customer['street_address']) ? $order->customer['street_address'] : $order->billing['street_address'];
		$city = !empty($order->customer['city']) ? $order->customer['city'] : $order->billing['city'];
		$postcode = !empty($order->customer['postcode']) ? $order->customer['postcode'] : $order->billing['postcode'];
		$country_iso_code_2 = !empty($order->customer['country']['iso_code_2']) ? $order->customer['country']['iso_code_2'] : $order->billing['country']['iso_code_2'];
		$customer_no = ($customer['customers_status'] != 1) ? $nn_customer_id : MODULE_PAYMENT_NOVALNET_CC3D_GUEST_USER;

    $process_button_string = zen_draw_hidden_field('vendor', $vendor_id) .
       zen_draw_hidden_field('product', $product_id) .
       zen_draw_hidden_field('key', $this->payment_key) .
       zen_draw_hidden_field('tariff', $tariff_id) .
       zen_draw_hidden_field('auth_code', $auth_code) .
       zen_draw_hidden_field('currency', $order->info['currency']) .
       zen_draw_hidden_field('amount', $amount) .
       zen_draw_hidden_field('first_name', $this->html_to_utf8($firstname)) .
       zen_draw_hidden_field('last_name', $this->html_to_utf8($lastname)) .
       zen_draw_hidden_field('email', $email_address) .
       zen_draw_hidden_field('street', $this->html_to_utf8($street_address)) .
       zen_draw_hidden_field('search_in_street', '1') .
       zen_draw_hidden_field('city', $this->html_to_utf8($city)) .
       zen_draw_hidden_field('zip', $postcode) .
       zen_draw_hidden_field('country', $country_iso_code_2) .
       zen_draw_hidden_field('country_code', $country_iso_code_2) .
       zen_draw_hidden_field('lang', MODULE_PAYMENT_NOVALNET_CC3D_TEXT_LANG) .
       zen_draw_hidden_field('language', MODULE_PAYMENT_NOVALNET_CC3D_TEXT_LANG) .
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
	   zen_draw_hidden_field('customer_no', $customer_no) .
	   zen_draw_hidden_field('use_utf8', '1') .
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
		if($_POST['test_mode'] == 1	){
			$order->info['comments'] .= '<B><br>'.MODULE_PAYMENT_NOVALNET_CC3D_TEST_ORDER_MESSAGE.'</B>';
			}
			if(count($HTTP_POST_VARS)==0 || $HTTP_POST_VARS=='') $HTTP_POST_VARS = $_POST;
			$order->info['comments'] .= '<B><br>'.MODULE_PAYMENT_NOVALNET_CC3D_TID_MESSAGE.$HTTP_POST_VARS['tid'].'</B><br />';
			$order->info['comments']  = str_replace(array('<b>', '</b>','<B>','</B>', '<br>','<br />','<BR>'), array('', '', '','',"\n", "\n","\n"), $order->info['comments']);			
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
	
	$vendor_id = $this->vendor_id;
	$auth_code = $this->auth_code;
	$product_id = $this->product_id;
	$tariff_id  =  $this->tariff_id;
	
	$manual_check_limit = $this->manual_check_limit;
    $manual_check_limit = str_replace(',', '', $manual_check_limit);
    $manual_check_limit = str_replace('.', '', $manual_check_limit);

    if($manual_check_limit && $_SESSION['nn_amount_cc3d']>=$manual_check_limit)
    {
      $product_id  =  $this->product_id2;;
      $tariff_id   =  $this->tariff_id2;
    }
	
	if($_SESSION['nn_tid']){
		### Pass the Order Reference to paygate ##
		$url = 'https://payport.novalnet.de/paygate.jsp';
		
		$urlparam = 'vendor='.$vendor_id.'&product='.$product_id.'&key='.$this->payment_key.'&tariff='.$tariff_id;
		$urlparam .= '&auth_code='.$auth_code.'&status=100&tid='.$_SESSION['nn_tid'].'&reference=BNR-'.$insert_id.'&vwz2='.MODULE_PAYMENT_NOVALNET_CC3D_TEXT_ORDERNO.''.$insert_id.'&vwz3='.MODULE_PAYMENT_NOVALNET_CC3D_TEXT_ORDERDATE.''.date('Y-m-d H:i:s').'&order_no='.$insert_id;
		
		list($errno, $errmsg, $data) = $this->perform_https_request($url, $urlparam);
		 $_SESSION['nn_tid'] = '';
	unset($_SESSION['nn_tid']);
	unset($_SESSION['nn_amount_cc3d']);
	unset($_SESSION['cc3d_holder']);
	unset($_SESSION['cc3d_no']);
	unset($_SESSION['cc3d_exp_month']);
	unset($_SESSION['cc3d_exp_year']);
	unset($_SESSION['cc3d_cvc2']);
		
		### Implement here the Emailversand and further functions, incase if you want to send a own email ###
	}
	
    return false;
  }

  function checkSecurity() {
		global $_POST, $order, $insert_id, $messageStack; 
   
		if(strlen(trim($_POST['tid']))==17 && $_POST['status']==100 && $_POST['session']== zen_session_id()){
			#xtc_redirect(zen_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL'));
		}else{
			if($_POST['status_text']){
				$error_status = $_POST['status_text'];
			}else {
				$error_status = "There was an error and your payment could not be completed ";
			}
			  $err  = $error_status;
			  #'session missing or returned session is wrong';
			  $order->info['comments'] .= '. Novalnet Error Message : '.$err;
			  $payment_error_return     = 'payment_error='.$this->code;
			  $messageStack->add_session('checkout_payment', $err . '<!-- ['.$this->code.'] -->', 'error');      
			  zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
		}
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
  
  function install_lang($field_text,$lang)
  {
    #Allowed Zones
   $install_text['allowed_title'] = array('en' => "Allowed zones",
										'de' => "erlaubte Zonen");  
    $install_text['allowed_desc'] = array('en' => "Please enter the desired zones separated by comma (Eg: AT, DE) or leave it blank",
									   'de' => "Bitte die gew&uuml;nschten Zonen durch Komma getrennt eingeben (z.B: AT,DE) oder einfach leer lassen");
	#Enable Module								   
    $install_text['enable_title'] = array('en' => "Enable Module",
										'de' => "Modul aktivieren");  
    $install_text['enable_desc'] = array('en' => "Do you want to activate the Credit Card of Novalnet AG?",
									   'de' => "Wollen Sie das Kreditkarten Modul des Novalnet AG aktivieren?");
	#Test Mode								   
	$install_text['test_title'] = array('en' => "Enable Test Mode:",
										'de' => "Testmodus einschalten");  
    $install_text['test_desc'] = array('en' => "Do you want to activate test mode?",
									   'de' => "Wollen Sie den Test-Modus aktivieren?");
	#Vendor id							   
    $install_text['vendor_title'] = array('en' => "Novalnet Merchant ID",
										'de' => "Novalnet H&auml;ndler ID");  
    $install_text['vendor_desc'] = array('en' => "Enter your Novalnet Merchant ID ",
									   'de' => "Geben Sie Ihre Novalnet H&auml;ndler-ID ein ");	
    #Auth Code
    $install_text['auth_title'] = array('en' => "Novalnet Merchant Authorisation Code",
										'de' => "Novalnet Authorisierungsschl&uuml;ssel");  
    $install_text['auth_desc'] = array('en' => "Enter your Novalnet Merchant Authorisation code ",
									   'de' => "Geben Sie Ihren Novalnet-Authorisierungsschl&uuml;ssel ein");		
									   
	#Product id
    $install_text['product_title'] = array('en' => "Novalnet Product ID",
										'de' => "Novalnet Produkt ID");  
    $install_text['product_desc'] = array('en' => "Enter your Novalnet Product ID",
									   'de' => "Geben Sie Ihre Novalnet Produkt-ID ein");	
									   
    #Tariff id
    $install_text['tariff_title'] = array('en' => "Novalnet Tariff ID",
										'de' => "Novalnet Tarif ID");  
    $install_text['tariff_desc'] = array('en' => "Enter your Novalnet Tariff ID ",
									   'de' => "Geben Sie Ihre Novalnet Tarif-ID ein");

    #Booking amount limit
    $install_text['booking_title'] = array('en' => "Manual checking amount in cents",
										'de' => "Manuelle &Uuml;berpr&uuml;fung des Betrags in Cent");  
    $install_text['booking_desc'] = array('en' => "Please enter the amount in cents",
									   'de' => "Bitte den Betrag in Cent eingeben");	

    #Second Product id
    $install_text['secondproduct_title'] = array('en' => "Second Product ID in Novalnet",
										'de' => "Zweite Novalnet Produkt ID");  
    $install_text['secondproduct_desc'] = array('en' => "for the manual checking",
									   'de' => "zur manuellen &Uuml;berpr&uuml;fung");	
    
    #Second Tariff id
    $install_text['secondtariff_title'] = array('en' => "Second Tariff ID in Novalnet",
										'de' => "Zweite Novalnet Tarif ID");  
    $install_text['secondtariff_desc'] = array('en' => "for the manual checking",
									   'de' => "zur manuellen &Uuml;berpr&uuml;fung");	
									   
	#Enduser info
    $install_text['enduser_title'] = array('en' => "Information to the end customer",
										'de' => "Informationen f&uuml;r den Endkunden");  
    $install_text['enduser_desc'] = array('en' => "will appear in the payment form",
									   'de' => "wird im Bezahlformular erscheinen");	
	
    #Sortorder display
    $install_text['sortorder_title'] = array('en' => "Sort order of display",
										'de' => "Sortierung nach");  
    $install_text['sortorder_desc'] = array('en' => "Sort order of display. Lowest is displayed first.",
									   'de' => "Sortierung der Anzeige. Der niedrigste Wert wird zuerst angezeigt.");

    #Setorder status display
    $install_text['setorderstatus_title'] = array('en' => "Set Order Status",
												  'de' => "Bestellungsstatus setzen");  
    $install_text['setorderstatus_desc'] = array('en' => "Set the status of orders made with this payment module to this value.",
												 'de' => "Setzen Sie den Status von &uuml;ber dieses Zahlungsmodul durchgef&uuml;hrten Bestellungen auf diesen Wert.");

     #Proxy
    $install_text['proxy_title'] = array('en' => "Proxy-Server",
										'de' => "Proxy-Server");  
    $install_text['proxy_desc'] = array('en' => " If you use a Proxy Server, enter the Proxy Server IP with port here (e.g. www.proxy.de:80).",
									   'de' => "Wenn Sie einen Proxy-Server einsetzen, tragen Sie hier Ihre Proxy-IP und den Port ein (z.B. www.proxy.de:80).");

     #Payment Zone
    $install_text['paymnetzone_title'] = array('en' => "Payment Zone",
										'de' => "Zahlungsgebiet");  
    $install_text['paymnetzone_desc'] = array('en' => "If a zone is selected then this module is activated only for Selected zone. ",
											  'de' => "Wird ein Bereich ausgew&auml;hlt, dann wird dieses Modul nur f&uuml;r den ausgew&auml;hlten Bereich aktiviert.");
									   
									   
    #Activate Logo Mode
    $install_text['logo_title'] = array('en' => "Activate logo mode:",
										'de' => "Aktivieren Sie Logo Modus:");  
    $install_text['logo_desc'] = array('en' => "Do you want to activate logo mode?",
									   'de' => "Wollen Sie Logo-Modus zu aktivieren?");
									   
	return $install_text[$field_text][$lang];
  }
  

  ### Install the payment module and its configuration settings ###
  function install() {
    global $db;
	
	$allowed_title = $this->install_lang('allowed_title', DEFAULT_LANGUAGE);
    $allowed_desc = $this->install_lang('allowed_desc', DEFAULT_LANGUAGE);
	
	$enable_title = $this->install_lang('enable_title', DEFAULT_LANGUAGE);
    $enable_desc = $this->install_lang('enable_desc', DEFAULT_LANGUAGE);
	
	$test_title = $this->install_lang('test_title', DEFAULT_LANGUAGE);
    $test_desc = $this->install_lang('test_desc', DEFAULT_LANGUAGE);
	
	$vendor_title = $this->install_lang('vendor_title', DEFAULT_LANGUAGE);
    $vendor_desc = $this->install_lang('vendor_desc', DEFAULT_LANGUAGE);
	
	$auth_title = $this->install_lang('auth_title', DEFAULT_LANGUAGE);
    $auth_desc = $this->install_lang('auth_desc', DEFAULT_LANGUAGE);
	
	$product_title = $this->install_lang('product_title', DEFAULT_LANGUAGE);
    $product_desc = $this->install_lang('product_desc', DEFAULT_LANGUAGE);
	
    $tariff_title = $this->install_lang('tariff_title', DEFAULT_LANGUAGE);
    $tariff_desc = $this->install_lang('tariff_desc', DEFAULT_LANGUAGE);
	
	$booking_title = $this->install_lang('booking_title', DEFAULT_LANGUAGE);
    $booking_desc = $this->install_lang('booking_desc', DEFAULT_LANGUAGE);
	
	$secondproduct_title = $this->install_lang('secondproduct_title', DEFAULT_LANGUAGE);
    $secondproduct_desc = $this->install_lang('secondproduct_desc', DEFAULT_LANGUAGE);
	
    $secondtariff_title = $this->install_lang('secondtariff_title', DEFAULT_LANGUAGE);
    $secondtariff_desc = $this->install_lang('secondtariff_desc', DEFAULT_LANGUAGE);
	
	$enduser_title = $this->install_lang('enduser_title', DEFAULT_LANGUAGE);
    $enduser_desc = $this->install_lang('enduser_desc', DEFAULT_LANGUAGE);
	
    $sortorder_title = $this->install_lang('sortorder_title', DEFAULT_LANGUAGE);
    $sortorder_desc = $this->install_lang('sortorder_desc', DEFAULT_LANGUAGE);
	
	$setorderstatus_title = $this->install_lang('setorderstatus_title', DEFAULT_LANGUAGE);
    $setorderstatus_desc = $this->install_lang('setorderstatus_desc', DEFAULT_LANGUAGE);
	
	$proxy_title = $this->install_lang('proxy_title', DEFAULT_LANGUAGE);
    $proxy_desc = $this->install_lang('proxy_desc', DEFAULT_LANGUAGE);
	
    $paymnetzone_title = $this->install_lang('paymnetzone_title', DEFAULT_LANGUAGE);
    $paymnetzone_desc = $this->install_lang('paymnetzone_desc', DEFAULT_LANGUAGE);
   	
	$logo_title = $this->install_lang('logo_title', DEFAULT_LANGUAGE);
    $logo_desc = $this->install_lang('logo_desc', DEFAULT_LANGUAGE);
	
	/*$db->Execute("alter table ".TABLE_ORDERS." modify payment_method varchar(250)");*/
	/*$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('".$allowed_title."','MODULE_PAYMENT_NOVALNET_CC3D_ALLOWED', '','".$allowed_desc."', '6', '0', now())");	*/ 
   
   $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('".$enable_title."', 'MODULE_PAYMENT_NOVALNET_CC3D_STATUS', 'True', '".$enable_desc."', '6', '0', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
   
	$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('".$test_title."', 'MODULE_PAYMENT_NOVALNET_CC3D_TEST_MODE', 'True', '".$test_desc."', '6', '1', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
	
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('".$vendor_title."', 'MODULE_PAYMENT_NOVALNET_CC3D_VENDOR_ID', '', '".$vendor_desc."', '6', '2', now())");
	
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('".$auth_title."', 'MODULE_PAYMENT_NOVALNET_CC3D_AUTH_CODE', '', '".$auth_desc."', '6', '3', now())");
	
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('".$product_title."', 'MODULE_PAYMENT_NOVALNET_CC3D_PRODUCT_ID', '', '".$product_desc."', '6', '4', now())");
	
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('".$tariff_title."', 'MODULE_PAYMENT_NOVALNET_CC3D_TARIFF_ID', '', '".$tariff_desc."', '6', '5', now())");
	
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('".$booking_title."', 'MODULE_PAYMENT_NOVALNET_CC3D_MANUAL_CHECK_LIMIT', '', '".$booking_desc."', '6', '6', now())");
	
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('".$secondproduct_title."', 'MODULE_PAYMENT_NOVALNET_CC3D_PRODUCT_ID2', '', '".$secondproduct_desc."', '6', '7', now())");
	
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('".$secondtariff_title."', 'MODULE_PAYMENT_NOVALNET_CC3D_TARIFF_ID2', '', '".$secondtariff_desc."', '6', '8', now())");
	
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('".$enduser_title."', 'MODULE_PAYMENT_NOVALNET_CC3D_BOOK_REF', '', '".$enduser_desc."', '6', '9', now())");
	
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('".$sortorder_title."', 'MODULE_PAYMENT_NOVALNET_CC3D_SORT_ORDER', '0', '".$sortorder_desc."', '6', '10', now())");
	
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('".$setorderstatus_title."', 'MODULE_PAYMENT_NOVALNET_CC3D_ORDER_STATUS_ID', '0', '".$setorderstatus_desc."', '6', '11', 'zen_cfg_pull_down_order_statuses(', 'zen_get_order_status_name', now())");
	
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('".$paymnetzone_title."', 'MODULE_PAYMENT_NOVALNET_CC3D_ZONE', '0', '".$paymnetzone_desc."', '6', '12', 'zen_get_zone_class_title', 'zen_cfg_pull_down_zone_classes(', now())");
	
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('".$proxy_title."', 'MODULE_PAYMENT_NOVALNET_CC3D_PROXY', '', '".$proxy_desc."', '6', '13', now())");	
	
	$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('".$logo_title."', 'MODULE_PAYMENT_NOVALNET_CC3D_LOGO_STATUS', 'True', '".$logo_desc."', '6', '14', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
  
 
	 
  }
   
  ### Remove the module and all its settings ###
  function remove() {
    global $db;
    $db->Execute("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
  }

  ### Internal list of configuration keys used for configuration of the module ###
  // @return array
  function keys() {
    return array('MODULE_PAYMENT_NOVALNET_CC3D_LOGO_STATUS','MODULE_PAYMENT_NOVALNET_CC3D_STATUS', 'MODULE_PAYMENT_NOVALNET_CC3D_TEST_MODE', 'MODULE_PAYMENT_NOVALNET_CC3D_VENDOR_ID', 'MODULE_PAYMENT_NOVALNET_CC3D_AUTH_CODE', 'MODULE_PAYMENT_NOVALNET_CC3D_PRODUCT_ID', 'MODULE_PAYMENT_NOVALNET_CC3D_TARIFF_ID', 'MODULE_PAYMENT_NOVALNET_CC3D_MANUAL_CHECK_LIMIT', 'MODULE_PAYMENT_NOVALNET_CC3D_PRODUCT_ID2', 'MODULE_PAYMENT_NOVALNET_CC3D_TARIFF_ID2', 'MODULE_PAYMENT_NOVALNET_CC3D_BOOK_REF', 'MODULE_PAYMENT_NOVALNET_CC3D_SORT_ORDER', 'MODULE_PAYMENT_NOVALNET_CC3D_ORDER_STATUS_ID', 'MODULE_PAYMENT_NOVALNET_CC3D_ZONE', 'MODULE_PAYMENT_NOVALNET_CC3D_PROXY');
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
      //$data = $this->ReplaceSpecialGermanChars($data);

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
