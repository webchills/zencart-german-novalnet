<?php
#########################################################
#                                                       #
#  iDEAL / iDEAL payment      							#
#  method class                                         #
#  This module is used for real time processing of      #
#  German Bankdata of customers.                        #
#                                                       #
#  Released under the GNU General Public License.       #
#  This free contribution made by request.              #
#  If you have found this script useful a small         #
#  recommendation as well as a comment on merchant form #
#  would be greatly appreciated.                        #
#                                                       #
#  Script : novalnet_instantbanktransfer.php     		#
#                                                       #
#########################################################

class novalnet_ideal {
  
  var $code;
  var $title;
  var $description;
  var $enabled;
  var $blnDebug;
  var $key;
  var $implementation;
  var $payment_key = '49';
  var $vendor_id;
  var $auth_code;
  var $product_id;
  var $tariff_id;

  function novalnet_ideal() {
    global $order, $db,$insert_id;
    
	$this->key          = trim(MODULE_PAYMENT_NOVALNET_IDEAL_PASSWORD); #'z2Vw3E4j';
	$this->vendor_id    = trim(MODULE_PAYMENT_NOVALNET_IDEAL_VENDOR_ID);
	$this->auth_code    = trim(MODULE_PAYMENT_NOVALNET_IDEAL_AUTH_CODE);
	$this->product_id   = trim(MODULE_PAYMENT_NOVALNET_IDEAL_PRODUCT_ID);
	$this->tariff_id    = trim(MODULE_PAYMENT_NOVALNET_IDEAL_TARIFF_ID);
	$this->test_mode    = (strtolower(MODULE_PAYMENT_NOVALNET_IDEAL_TEST_MODE) == 'true' or MODULE_PAYMENT_NOVALNET_IDEAL_TEST_MODE == '1')? 1: 0;
	
    $this->code            = 'novalnet_ideal';
    $this->form_action_url = 'https://payport.novalnet.de/online_transfer_payport';
    $this->title           = MODULE_PAYMENT_NOVALNET_IDEAL_TEXT_TITLE;
    $this->public_title    = MODULE_PAYMENT_NOVALNET_IDEAL_TEXT_PUBLIC_TITLE;
    $this->description     = MODULE_PAYMENT_NOVALNET_IDEAL_TEXT_DESCRIPTION;
    $this->sort_order      = MODULE_PAYMENT_NOVALNET_IDEAL_SORT_ORDER;
    $this->enabled         = ((MODULE_PAYMENT_NOVALNET_IDEAL_STATUS == 'True') ? true : false);
    $this->blnDebug        = false; #todo: set to false for live system
    $this->proxy           = MODULE_PAYMENT_NOVALNET_IDEAL_PROXY;   
    $this->implementation  = ''; 
	
	
	
	if(MODULE_PAYMENT_NOVALNET_IDEAL_LOGO_STATUS == 'True'){
		$this->public_title = 'Novalnet'.' '.MODULE_PAYMENT_NOVALNET_IDEAL_TEXT_PUBLIC_TITLE;
		$this->title        = 'Novalnet'.' '.MODULE_PAYMENT_NOVALNET_IDEAL_TEXT_TITLE;
	}
	$this->checkConfigure();

 if ((int)MODULE_PAYMENT_NOVALNET_IDEAL_ORDER_STATUS_ID > 0) {
      $this->order_status = MODULE_PAYMENT_NOVALNET_IDEAL_ORDER_STATUS_ID;
    }
    #check encoded data
     #check encoded data
    if ($_REQUEST['hash2']){
      if (!$this->checkHash($_REQUEST)){
        $err = MODULE_PAYMENT_NOVALNET_IDEAL_TEXT_HASH_ERROR;
        $payment_error_return = 'payment_error=novalnet_ideal&error='.$_REQUEST['status_text'].'; '.$err;
        zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
      }else{
      $_REQUEST['auth_code']  = $this->decode($_REQUEST['auth_code']);
      $_REQUEST['product_id'] = $this->decode($_REQUEST['product_id']);
      $_REQUEST['tariff_id']  = $this->decode($_REQUEST['tariff_id']);
      $_REQUEST['amount']     = $this->decode($_REQUEST['amount']);
      $_REQUEST['test_mode']  = $this->decode($_REQUEST['test_mode']);
      $_REQUEST['uniqid']     = $this->decode($_REQUEST['uniqid']);
      }
    }

  if ((int)MODULE_PAYMENT_NOVALNET_IDEAL_ORDER_STATUS_ID > 0) {
      $this->order_status = MODULE_PAYMENT_NOVALNET_IDEAL_ORDER_STATUS_ID;
    }

    if (is_object($order)) $this->update_status();
  }
  
  
  function checkConfigure() {
		if (IS_ADMIN_FLAG == true) {
			$this->title = MODULE_PAYMENT_NOVALNET_IDEAL_TEXT_TITLE; // Payment module title in Admin
			if(MODULE_PAYMENT_NOVALNET_IDEAL_LOGO_STATUS == 'True'){
				$this->public_title = 'Novalnet'.' '.MODULE_PAYMENT_NOVALNET_IDEAL_TEXT_PUBLIC_TITLE;
				$this->title        = 'Novalnet'.' '.MODULE_PAYMENT_NOVALNET_IDEAL_TEXT_TITLE;
			}
			if ($this->enabled == 'true' && (!$this->vendor_id || !$this->auth_code || !$this->product_id || !$this->tariff_id || !$this->key )) {
				$this->title .=  '<span class="alert">'.MODULE_PAYMENT_NOVALNET_IDEAL_NOT_CONFIGURED.'</span>';
			} elseif ($this->test_mode == '1') {
				$this->title .= '<span class="alert">'.MODULE_PAYMENT_NOVALNET_IDEAL_IN_TEST_MODE.'</span>';
			}
			
		}
	}
  ### calculate zone matches and flag settings to determine whether this module should display to customers or not ###
  function update_status() {
    global $order, $db;

    if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_NOVALNET_IDEAL_ZONE > 0) ) {
      $check_flag = false;
      $check = $db->Execute("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_NOVALNET_IDEAL_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
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
                       'module' => $this->public_title,
                       'fields' => array(array('title' => '', 'field' => MODULE_PAYMENT_NOVALNET_INFORMATION_PAYMENT_IDEAL),
					   array('title' => '', 'field' => MODULE_PAYMENT_NOVALNET_IDEAL_INFO)
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
    
	if (!function_exists('curl_init') && ($this->code=='novalnet_ideal')){
			ini_set('display_errors', 1);
			ini_set('error_reporting', E_ALL);
			$error =  MODULE_PAYMENT_NOVALNET_IDEAL_CURL_MESSAGE;
		}
    		
    if(!$this->vendor_id || !$this->auth_code || !$this->product_id || !$this->tariff_id || !$this->key)
    {
      $error = MODULE_PAYMENT_NOVALNET_IDEAL_TEXT_JS_NN_MISSING;
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
      $payment_error_return     = 'payment_error='.$this->code;
      $messageStack->add_session('checkout_payment', $err . '<!-- ['.$this->code.'] -->', 'error');	  
      zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
    }
    // $amount = preg_replace('/^0+/', '', $amount);
    // $amount = sprintf('%0.2f', $amount);
    // $amount = str_replace('.', '', $amount);
    //$amount       = $amount;
	$vendor_id    = $this->vendor_id;
	$auth_code    = $this->auth_code;
    $product_id   = $this->product_id;
    $tariff_id    = $this->tariff_id;
	$customer_id  =  $_SESSION['customer_id'];
	$uniqid       = uniqid();
	
    $auth_code    = $this->encode($auth_code);
    $product_id   = $this->encode($product_id);
    $tariff_id    = $this->encode($tariff_id);
    $amount       = $this->encode($amount);
    $test_mode    = $this->encode((strtolower(MODULE_PAYMENT_NOVALNET_IDEAL_TEST_MODE) == 'true' or MODULE_PAYMENT_NOVALNET_IDEAL_TEST_MODE == '1')? 1: 0);
    $uniqid       = $this->encode(uniqid());
    $hash         = $this->hash(array('auth_code' => $auth_code, 'product_id' => $product_id, 'tariff' => $tariff_id, 'amount' => $amount, 'test_mode' => $test_mode, 'uniqid' => $uniqid));
    
    $user_ip      = $this->getRealIpAddr();
    $checkout_url = zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL');



    if(strstr($checkout_url, '?'))
    {
      $checkout_url = str_replace(' ', '', $checkout_url);
      if(substr($checkout_url,-1)=='?')
           $error_url = $checkout_url.'payment_error=novalnet_ideal&error=$ERROR_MESSAGE';
      else $error_url = $checkout_url.'&payment_error=novalnet_ideal&error=$ERROR_MESSAGE';
    }
    else $error_url = $checkout_url.'?payment_error=novalnet_ideal&error=$ERROR_MESSAGE';
    $oldreturnurl=zen_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL');
    $old_value=explode(':',$oldreturnurl);
    $new_protocol_value=(empty($_SERVER["HTTPS"])) ? 'http' : 'https';
    $return_url=$new_protocol_value.':'.$old_value[1];
	$_SESSION['pymt_method'] = 'ideal';
	
	
		$firstname = !empty($order->customer['firstname']) ? $order->customer['firstname'] : $order->billing['firstname'];
		$lastname = !empty($order->customer['lastname']) ? $order->customer['lastname'] : $order->billing['lastname'];
		$email_address = !empty($order->customer['email_address']) ? $order->customer['email_address'] : $order->billing['email_address'];
		$street_address = !empty($order->customer['street_address']) ? $order->customer['street_address'] : $order->billing['street_address'];
		$city = !empty($order->customer['city']) ? $order->customer['city'] : $order->billing['city'];
		$postcode = !empty($order->customer['postcode']) ? $order->customer['postcode'] : $order->billing['postcode'];
		$country_iso_code_2 = !empty($order->customer['country']['iso_code_2']) ? $order->customer['country']['iso_code_2'] : $order->billing['country']['iso_code_2'];
		$customer_no = ($customer['customers_status'] != 1) ? $nn_customer_id : MODULE_PAYMENT_NOVALNET_IDEAL_GUEST_USER;

    $process_button_string =
      zen_draw_hidden_field('vendor',    $vendor_id) .#Pflicht
      zen_draw_hidden_field('auth_code', $auth_code) .
      zen_draw_hidden_field('product',   $product_id) .
      zen_draw_hidden_field('tariff',    $tariff_id) .
      zen_draw_hidden_field('test_mode', $test_mode) .
      zen_draw_hidden_field('uniqid',    $uniqid) .
      zen_draw_hidden_field('amount',    $amount) .
      zen_draw_hidden_field('hash',      $hash) .	  
      zen_draw_hidden_field('nnpayment','ideal') .	  	  
      zen_draw_hidden_field('key', $this->payment_key) .#Pflicht
      zen_draw_hidden_field('currency',  $order->info['currency']) .
      zen_draw_hidden_field('first_name', $firstname) .
      zen_draw_hidden_field('last_name', $lastname) .
      zen_draw_hidden_field('gender',    'u') .
      zen_draw_hidden_field('email',     $email_address) .
      zen_draw_hidden_field('street',    $street_address) .
      zen_draw_hidden_field('search_in_street', '1') .
      zen_draw_hidden_field('city',     $order->customer['city']) .
      zen_draw_hidden_field('zip',       $postcode) .
      zen_draw_hidden_field('country',   $country_iso_code_2) .
      zen_draw_hidden_field('country_code', $country_iso_code_2) .
      zen_draw_hidden_field('lang',      MODULE_PAYMENT_NOVALNET_IDEAL_TEXT_LANG) .#default: 'DE'
      zen_draw_hidden_field('language',  MODULE_PAYMENT_NOVALNET_IDEAL_TEXT_LANG) .#default: 'DE'
      zen_draw_hidden_field('remote_ip', $user_ip) . #Pflicht
      zen_draw_hidden_field('tel', $order->customer['telephone']) .
      zen_draw_hidden_field('fax', $customer['customers_fax']) .
      zen_draw_hidden_field('birth_date', $customer['customers_dob']) .
      zen_draw_hidden_field('session', zen_session_id()) .
      zen_draw_hidden_field('return_url', $return_url) .
      zen_draw_hidden_field('return_method', 'POST') .
      zen_draw_hidden_field('error_return_url', $error_url) . 
      //zen_draw_hidden_field('user_variable_0', str_replace(array($new_protocol_value.'://', 'www.'), array('', ''), $_SERVER['SERVER_NAME'])) .
      zen_draw_hidden_field('error_return_method', 'POST').
      zen_draw_hidden_field('customer_no', $customer_no) .
      zen_draw_hidden_field('use_utf8', '1') .
      zen_draw_hidden_field('proxy', $this->proxy);

    $process_button_string .= $this->getParams4Ideal();

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
		if ($_REQUEST['test_mode'] == 1){
		$order->info['comments'] .= '<B><br>'.MODULE_PAYMENT_NOVALNET_IDEAL_TEST_ORDER_MESSAGE.'</B>';
		}

		$order->info['comments'] .= '<B><br>'.MODULE_PAYMENT_NOVALNET_IDEAL_TID_MESSAGE.$HTTP_POST_VARS['tid'].'</B><br />';
		$_SESSION['nn_tid'] = $HTTP_POST_VARS['tid'];#todo: 
		$order->info['comments']  = str_replace(array('<b>', '</b>','<B>','</B>', '<br>','<br />','<BR>'), array('', '', '','',"\n", "\n","\n"), $order->info['comments']);	 
	}
  }

 ### Send the order detail to Novalnet ###
  function after_process() {
    global $order, $customer_id, $insert_id;
    $product_id = MODULE_PAYMENT_NOVALNET_IDEAL_PRODUCT_ID;
    $tariff_id = MODULE_PAYMENT_NOVALNET_IDEAL_TARIFF_ID;
	if(  $_SESSION['nn_tid'] != ''){
		### Pass the Order Reference to paygate ##
		$url = 'https://payport.novalnet.de/paygate.jsp';
		$urlparam = 'vendor='.$this->vendor_id.'&product='.$this->product_id.'&key='.$this->payment_key.'&tariff='.$this->tariff_id;
		$urlparam .= '&auth_code='.$this->auth_code.'&status=100&tid='.$_SESSION['nn_tid'].'&vwz2='.MODULE_PAYMENT_NOVALNET_IDEAL_TEXT_ORDERNO.''.$insert_id.'&vwz3='.MODULE_PAYMENT_NOVALNET_IDEAL_TEXT_ORDERDATE.''.date('Y-m-d H:i:s').'&order_no='.$insert_id;
		list($errno, $errmsg, $data) = $this->perform_https_request($url, $urlparam);
	}
    unset($_SESSION['nn_tid']);

    #print "$customer_id, $insert_id"; exit;
    ### Implement here the Emailversand and further functions, incase if you want to send a own email ###
   //$_SESSION['t_id']=$insert_id;  
    return false;
  }

  ### Used to display error message details ###
  // @return array
  function get_error() {
    global $HTTP_GET_VARS, $_GET;
    if(count($HTTP_GET_VARS)==0 || $HTTP_GET_VARS=='') $HTTP_GET_VARS = $_GET;

    #print $HTTP_GET_VARS['error']; exit;
    $error = array('title' => MODULE_PAYMENT_NOVALNET_IDEAL_TEXT_ERROR, 'error' => stripslashes(utf8_decode($HTTP_GET_VARS['error'])));

    return $error;
  }

  ### Check to see whether module is installed ###
  // @return boolean
  function check() {
    global $db;
    if (!isset($this->_check)) {
      $check_query = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_NOVALNET_IDEAL_STATUS'");
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
									   
	 #Novalnet Password
    $install_text['password_title'] = array('en' => "Novalnet Password",
										'de' => "Novalnet Passwort");  
    $install_text['password_desc'] = array('en' => "Enter your Novalnet Password.",
										   'de' => "Geben Sie Ihr Novalnet Passwort ein.");
									   
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
	
    $password_title = $this->install_lang('password_title', DEFAULT_LANGUAGE);
    $password_desc = $this->install_lang('password_desc', DEFAULT_LANGUAGE);
	
	$logo_title = $this->install_lang('logo_title', DEFAULT_LANGUAGE);
    $logo_desc = $this->install_lang('logo_desc', DEFAULT_LANGUAGE);
	
	
	/*$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('".$allowed_title."','MODULE_PAYMENT_NOVALNET_IDEAL_ALLOWED', '','".$allowed_desc."', '6', '0', now())"); */	
   
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('".$enable_title."', 'MODULE_PAYMENT_NOVALNET_IDEAL_STATUS', 'True', '".$enable_desc."', '6', '0', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
	
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('".$test_title."', 'MODULE_PAYMENT_NOVALNET_IDEAL_TEST_MODE', 'True', '".$test_desc."', '6', '1', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
	
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('".$vendor_title."', 'MODULE_PAYMENT_NOVALNET_IDEAL_VENDOR_ID', '', '".$vendor_desc."', '6', '2', now())");
	
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('".$auth_title."', 'MODULE_PAYMENT_NOVALNET_IDEAL_AUTH_CODE', '', '".$auth_desc."', '6', '3', now())");
	
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('".$product_title."','MODULE_PAYMENT_NOVALNET_IDEAL_PRODUCT_ID', '', '".$product_desc."', '6', '4', now())");
	
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('".$tariff_title."', 'MODULE_PAYMENT_NOVALNET_IDEAL_TARIFF_ID', '', '".$tariff_desc."', '6', '5', now())");
	
	$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('".$password_title."', 'MODULE_PAYMENT_NOVALNET_IDEAL_PASSWORD', '', '".$password_desc."', '6', '6', now())");
	
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('".$sortorder_title."', 'MODULE_PAYMENT_NOVALNET_IDEAL_SORT_ORDER', '0', '".$sortorder_desc."', '6', '7', now())");
	
	$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('".$enduser_title."', 'MODULE_PAYMENT_NOVALNET_IDEAL_INFO', '', '".$enduser_desc."', '6', '8', now())");
	
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('".$setorderstatus_title."', 'MODULE_PAYMENT_NOVALNET_IDEAL_ORDER_STATUS_ID', '0', '".$setorderstatus_desc."', '6', '9', 'zen_cfg_pull_down_order_statuses(', 'zen_get_order_status_name', now())");
	
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('".$paymnetzone_title."', 'MODULE_PAYMENT_NOVALNET_IDEAL_ZONE', '0', '".$paymnetzone_desc."', '6', '10', 'zen_get_zone_class_title', 'zen_cfg_pull_down_zone_classes(', now())");
	
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('".$proxy_title."', 'MODULE_PAYMENT_NOVALNET_IDEAL_PROXY', '', '".$proxy_desc."', '6', '11', now())");	
	
	$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('".$logo_title."', 'MODULE_PAYMENT_NOVALNET_IDEAL_LOGO_STATUS', 'True', '".$logo_desc."', '6', '12', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
   
  }
   
  ### Remove the module and all its settings ###
  function remove() {
    global $db;
    $db->Execute("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
  }

  ### Internal list of configuration keys used for configuration of the module ###
  // @return array
  function keys(){
    return array('MODULE_PAYMENT_NOVALNET_IDEAL_LOGO_STATUS','MODULE_PAYMENT_NOVALNET_IDEAL_STATUS', 
    'MODULE_PAYMENT_NOVALNET_IDEAL_TEST_MODE','MODULE_PAYMENT_NOVALNET_IDEAL_VENDOR_ID', 'MODULE_PAYMENT_NOVALNET_IDEAL_AUTH_CODE', 'MODULE_PAYMENT_NOVALNET_IDEAL_PRODUCT_ID', 'MODULE_PAYMENT_NOVALNET_IDEAL_TARIFF_ID', 'MODULE_PAYMENT_NOVALNET_IDEAL_INFO', 'MODULE_PAYMENT_NOVALNET_IDEAL_SORT_ORDER', 'MODULE_PAYMENT_NOVALNET_IDEAL_ORDER_STATUS_ID', 
    'MODULE_PAYMENT_NOVALNET_IDEAL_ZONE', 'MODULE_PAYMENT_NOVALNET_IDEAL_PASSWORD', 'MODULE_PAYMENT_NOVALNET_IDEAL_PROXY');
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
	function getParams4Ideal()
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
		//$params = 
		#zen_draw_hidden_field('amount', str_replace(',', '.', $_SESSION['nn_total'])).#todo:form check
		#zen_draw_hidden_field('sender_bank_code', $HTTP_POST_VARS['bank_code']).
		#zen_draw_hidden_field('sender_account_number', $HTTP_POST_VARS['bank_account']).
		#zen_draw_hidden_field('sender_holder=', $this->html_to_utf8($HTTP_POST_VARS['bank_account_holder'])).
		#zen_draw_hidden_field('sender_country_id', 'DE').
		#zen_draw_hidden_field('currency_id', 'EUR').
		#zen_draw_hidden_field('language_id', MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEXT_LANG);
		#zen_draw_hidden_field('customer_id', $_SESSION['nn_tid']).
		//zen_draw_hidden_field('user_variable_0', (str_replace(array('http://', 'www.'), array('', ''), HTTP_SERVER)));
		$params = zen_draw_hidden_field('user_variable_0',  HTTP_SERVER);
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
