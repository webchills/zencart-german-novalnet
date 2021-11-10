<?php

#########################################################
#                                                       #
#  ELV_DE / DIRECT DEBIT payment method class           #
#  This module is used for real time processing of      #
#  German Bankdata of customers.                        #
#                                                       #
#  Copyright (c) 2007 Novalnet AG                       #
#                                                       #
#  Released under the GNU General Public License        #
#  Novalnet_elv_de module Created By Dixon Rajdaniel    #
#                           Modified By Panneerselvam   #
#  This free contribution made by request.              #
#  If you have found this script usefull a small        #
#  recommendation as well as a comment on merchant form #
#  would be greatly appreciated.                        #
#                                                       #
#  Version : novalnet_elv_de.php vZenDE1.2.1 2008-08-26 #
#                                                       #
#########################################################

class novalnet_elv_de {
  var $code;
  var $title;
  var $description;
  var $enabled;
  var $proxy;  

  function novalnet_elv_de() {
    global $order,$messageStack;
    $this->code = 'novalnet_elv_de';
    $this->title = MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_TITLE;
    $this->description = MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_DESCRIPTION;
    $this->sort_order = MODULE_PAYMENT_NOVALNET_ELV_DE_SORT_ORDER;
    $this->enabled = ((MODULE_PAYMENT_NOVALNET_ELV_DE_STATUS == 'True') ? true : false);
    $this->proxy        = MODULE_PAYMENT_NOVALNET_ELV_DE_PROXY;

	// Check the tid in session and make the second call
	if($_SESSION['nn_tid_elv_de'])
  	{
	//Check the time limit
		if($_SESSION['max_time_elv_de'] && time() > $_SESSION['max_time_elv_de'])
		{
			unset($_SESSION['nn_tid_elv_de']);
			$payment_error_return = 'payment_error=' . $this->code;
			$messageStack->add_session('checkout_payment', MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_BY_CALLBACK_SESSION_ERROR . '<!-- ['.$this->code.'] -->', 'error');			
			zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));		
		}	

		if( $_GET['new_novalnet_pin_elv_de'] == 'true')
		{
			$_SESSION['new_novalnet_pin_elv_de'] = true;
			$this->secondcall();
		}
	}
	
    // define callback types
    $this->isActivatedCallback = false;	
	if(MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_BY_CALLBACK_SMS != 'False')
	{
		$this->isActivatedCallback = true;
	}    

    if ((int)MODULE_PAYMENT_NOVALNET_ELV_DE_ORDER_STATUS_ID > 0) {
      $this->order_status = MODULE_PAYMENT_NOVALNET_ELV_DE_ORDER_STATUS_ID;
    }

    if (is_object($order)) $this->update_status();
  }
  
  ### calculate zone matches and flag settings to determine whether this module should display to customers or not ###
  function update_status() {
    global $order, $db;

    if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_NOVALNET_ELV_DE_ZONE > 0) ) {
      $check_flag = false;
      $check = $db->Execute("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_NOVALNET_ELV_DE_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
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
    global $order, $_POST, $_GET;

    $onFocus = ' onfocus="methodSelect(\'pmt-' . $this->code . '\')"';

    $bank_account = '';
    if (isset($_POST['bank_account'])) {$bank_account = $_POST['bank_account'];}
    if(!$bank_account and isset($_GET['bank_account'])) {$bank_account = $_GET['bank_account'];}
    if (isset($_POST['bank_code'])){$bank_code = $_POST['bank_code'];}
    $bank_code  = '';
    if(!$bank_code and isset($_GET['bank_code'])) {$bank_code=$_GET['bank_code'];}
	
	if(!$_SESSION['nn_tid_elv_de']){
		$selection = array('id' => $this->code,
						   'module' => $this->title,
						   'fields' => array(array('title' => MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_BANK_ACCOUNT_OWNER,
												   'field' => zen_draw_input_field('bank_account_holder', $order->billing['firstname'] . ' ' . $order->billing['lastname'], 'id="'.$this->code.'-bank_account_holder"' . $onFocus),
												   'tag' => $this->code.'-bank_account_holder'),
											 array('title' => MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_BANK_ACCOUNT_NUMBER,
												   'field' => zen_draw_input_field('bank_account', $_SESSION['bank_account'], 'id="' . $this->code . '-bank_account"' . $onFocus),
												   'tag' => $this->code . '-bank_account'),
											 array('title' => MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_BANK_CODE,
									'field' => zen_draw_input_field('bank_code', $_SESSION['bank_code'], 'id="' . $this->code . '-bank_code"' . $onFocus),
							'tag' => $this->code . '-bank_code'),
										  #array('title' => 'INFO:', 'field' => MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_CUST_INFORM),
						 array('title' => '', 'field' => MODULE_PAYMENT_NOVALNET_ELV_DE_INFO)
						   ));

		if(MODULE_PAYMENT_NOVALNET_ELV_DE_ACDC=="True")
		{
		  $aryAcdc = array('title' => '', 'field' => zen_draw_checkbox_field('acdc', '1', false, 'id="' . $this->code . '-acdc"' . $onFocus).MODULE_PAYMENT_NOVALNET_ELV_DE_ACDC_INFO);
		  array_push($selection['fields'], $aryAcdc);
		  $aryAcdc = array('title' => '', 'field' => MODULE_PAYMENT_NOVALNET_ELV_DE_ACDC_DIV);
		  array_push($selection['fields'], $aryAcdc);
		}
		
		// Display callback fields
		$amount_check = $this->findTotalAmount();
		if($this->isActivatedCallback && strtolower($order->customer['country']['iso_code_2']) == 'de' && $amount_check >= MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_BY_CALLBACK_MIN_LIMIT )	
		{				   
			$selection['fields'][] = array( 'title' => MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_BY_CALLBACK_SMS_TEL, 'field' => zen_draw_input_field ( 'user_tel_elv_de', $order->customer['telephone'], 'id="'.$this->code.'-callback" '.$onFocus ) );
	    }
    }else{
		$selection = array('id' => $this->code,
						   'module' => $this->title);
  			// Show PIN field, after first call
  		$selection['fields'][] = array( 'title' => MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_INPUT_REQUEST_DESC, 'field' => zen_draw_input_field( 'novalnet_pin_elv_de', '', 'id="'.$this->code.'-callback" '.$onFocus.' maxlength="4" size="4" ') );
		$selection['fields'][] = array( 'title' => '<a href="'.zen_href_link( FILENAME_CHECKOUT_PAYMENT, 'new_novalnet_pin_elv_de=true', 'SSL', true, false).'">'.MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_BY_CALLBACK_SMS_NEW_PIN.'</a>') ;
	}	               
    return $selection;
  }

  ### Precheck to Evaluate the Bank Datas ###
  function pre_confirmation_check() {
	global $HTTP_POST_VARS, $_POST, $order,$messageStack;
	$error = '';
	if(count($HTTP_POST_VARS)==0 || $HTTP_POST_VARS=='') $HTTP_POST_VARS = $_POST; 
	if(!MODULE_PAYMENT_NOVALNET_ELV_DE_VENDOR_ID || !MODULE_PAYMENT_NOVALNET_ELV_DE_AUTH_CODE || !MODULE_PAYMENT_NOVALNET_ELV_DE_PRODUCT_ID || !MODULE_PAYMENT_NOVALNET_ELV_DE_TARIFF_ID)
    {
      $error = MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_JS_NN_MISSING;
    }
	$HTTP_POST_VARS['bank_account_holder']        	= trim($HTTP_POST_VARS['bank_account_holder']);
	$HTTP_POST_VARS['bank_account'] 	= trim($HTTP_POST_VARS['bank_account']);
	$HTTP_POST_VARS['bank_code']  	= trim($HTTP_POST_VARS['bank_code']);
	$HTTP_POST_VARS['acdc']      	= trim($HTTP_POST_VARS['acdc']);
	$HTTP_POST_VARS['user_tel_elv_de'] 		= trim($HTTP_POST_VARS['user_tel_elv_de']);
	$HTTP_POST_VARS['novalnet_pin_elv_de'] 	=  trim($HTTP_POST_VARS['novalnet_pin_elv_de']);
    	
      // Callback stuff....
	  
    if($_SESSION['nn_tid_elv_de'])
    {
		//check the amount is equal with the first call or not
		$amount = $this->findTotalAmount();
		if($_SESSION['elv_de_order_amount'] != $amount){
			unset($_SESSION['nn_tid_elv_de']);
			unset($_SESSION['elv_de_order_amount']);
			$payment_error_return = 'payment_error=' . $this->code;
			$messageStack->add_session('checkout_payment', MODULE_PAYMENT_NOVALNET_ELV_DE_AMOUNT_VARIATION_MESSAGE . '<!-- ['.$this->code.'] -->', 'error');			
			zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));			
		}	
		// check pin
		if( !is_numeric( $HTTP_POST_VARS['novalnet_pin_elv_de'] ) || strlen( $HTTP_POST_VARS['novalnet_pin_elv_de'] ) != 4 )
		{	
			$payment_error_return = 'payment_error=' . $this->code;
			$messageStack->add_session('checkout_payment', MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_BY_CALLBACK_SMS_PIN_NOTVALID . '<!-- ['.$this->code.'] -->', 'error');			
			zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
		}
		else
		{
			if( $HTTP_POST_VARS['novalnet_pin_elv_de'] )
				$_SESSION['novalnet_pin_elv_de'] = $HTTP_POST_VARS['novalnet_pin_elv_de'];
		}
	}
	else
	{
	   if (defined('MODULE_PAYMENT_NOVALNET_ELV_DE_MANUAL_CHECK_LIMIT') and MODULE_PAYMENT_NOVALNET_ELV_DE_MANUAL_CHECK_LIMIT)
	   {
			if ( (!defined('MODULE_PAYMENT_NOVALNET_ELV_DE_PRODUCT_ID2') or !MODULE_PAYMENT_NOVALNET_ELV_DE_PRODUCT_ID2 or preg_match('/[^\d]/', MODULE_PAYMENT_NOVALNET_ELV_DE_PRODUCT_ID2)) or (!defined('MODULE_PAYMENT_NOVALNET_ELV_DE_TARIFF_ID2') or !MODULE_PAYMENT_NOVALNET_ELV_DE_TARIFF_ID2 or preg_match('/[^\d]/', MODULE_PAYMENT_NOVALNET_ELV_DE_TARIFF_ID2)))
			{
			  $error = 'Product-ID2 and/or Tariff-ID2 missing';
			}
		}

		if(!$_POST['bank_account_holder'] || strlen($_POST['bank_account_holder'])<MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_BANK_ACCOUNT_OWNER_LENGTH) $error = MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_JS_BANK_ACCOUNT_OWNER;
		elseif(!$_POST['bank_account'] || strlen($_POST['bank_account'])<MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_BANK_ACCOUNT_NUMBER_LENGTH) $error = MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_JS_BANK_ACCOUNT_NUMBER;
		elseif(!$_POST['bank_code'] || strlen($_POST['bank_code'])<MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_BANK_CODE_LENGTH) $error = MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_JS_BANK_CODE;
		if(MODULE_PAYMENT_NOVALNET_ELV_DE_ACDC=="True" || MODULE_PAYMENT_NOVALNET_ELV_DE_ACDC=="1"){
			if(!$_POST['acdc']) $error .= MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_JS_ACDC;    
		}
		
		$_SESSION['bank_account_holder'] = $HTTP_POST_VARS['bank_account_holder'];
		$_SESSION['bank_code'] = $HTTP_POST_VARS['bank_code'];
		$_SESSION['bank_account'] = $HTTP_POST_VARS['bank_account'];
		// Callback stuff....
		$amount_check = $this->findTotalAmount();
		if( $this->isActivatedCallback && strtolower($order->customer['country']['iso_code_2']) == 'de' && $amount_check >= MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_BY_CALLBACK_MIN_LIMIT )
		{
			if( strlen( $HTTP_POST_VARS['user_tel_elv_de'] ) < 8 || !is_numeric( $HTTP_POST_VARS['user_tel_elv_de'] )  ){
				$error .= utf8_decode( MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_BY_CALLBACK_SMS_TEL_NOTVALID );
			}
			if($error!='') {
				$payment_error_return = 'payment_error=' . $this->code;
				$messageStack->add_session('checkout_payment', $error . '<!-- ['.$this->code.'] -->', 'error');					
				zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
			}else{
				$_SESSION['user_tel_elv_de'] = $HTTP_POST_VARS['user_tel_elv_de'];
				// firstcall()
				$this->before_process();
				$messageStack->add_session('checkout_payment', MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_INPUT_REQUEST_DESC . '<!-- ['.$this->code.'] -->', 'error');				
				zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', true, false)); 
			}
		}
		//echo $error;
		if($error!='') {
		  $payment_error_return = 'payment_error=' . $this->code . '&bank_account_holder=' . urlencode($_POST['bank_account_holder']) . '&bank_account=' . $_POST['bank_account'] . '&bank_code=' . $_POST['bank_code'];
		  $messageStack->add_session('checkout_payment', $error . '<!-- ['.$this->code.'] -->', 'error');
		  zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
		}
	}
  }
  
  //This is user defined function used for getting order amount in cents with tax
  public function findTotalAmount(){
		global $order;
		if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
		  $total = $order->info['total'] + $order->info['tax'];
		} else {
		  $total = $order->info['total'];
		}
		if (preg_match('/[^\d\.]/', $total) or !$total){
		  ### $amount contains some unallowed chars or empty ###
		  $err                      = 'amount ('.$total.') is empty or has a wrong format';
		  $payment_error_return     = 'payment_error='.$this->code;
		  $messageStack->add_session('checkout_payment', $err . '<!-- ['.$this->code.'] -->', 'error');		  
		  zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
		}
		$amount = sprintf('%0.2f', $total);
		$amount = preg_replace('/^0+/', '', $amount);
		$amount = str_replace('.', '', $amount);
	return $amount;
  }   
  
  
  	public function secondCall()
	{
		global $messageStack;
		// If customer forgets PIN, send a new PIN
		if( $_SESSION['new_novalnet_pin_elv_de'] )
			$request_type = 'TRANSMIT_PIN_AGAIN';
		else
	        $request_type = 'PIN_STATUS';
	        
		$_SESSION['new_novalnet_pin_elv_de'] = false;	
					
		$xml = '<?xml version="1.0" encoding="UTF-8"?>
				<nnxml>                               
		  			<info_request>
			    		<vendor_id>'.MODULE_PAYMENT_NOVALNET_ELV_DE_VENDOR_ID.'</vendor_id>
			    		<vendor_authcode>'.MODULE_PAYMENT_NOVALNET_ELV_DE_AUTH_CODE.'</vendor_authcode>
			    		<request_type>'.$request_type.'</request_type>
			    		<tid>'.$_SESSION['nn_tid_elv_de'].'</tid>
			    		<pin>'.$_SESSION['novalnet_pin_elv_de'].'</pin>
		  			</info_request>
				</nnxml>'; 	
				
		$xml_response = $this->curl_xml_post( $xml );			
		
		// Parse XML Response to object
		$xml_response = simplexml_load_string( $xml_response );
		#$_SESSION['status'] = $xml_response->status;

		if( $xml_response->status != 100 )		
		{		
			$payment_error_return = 'payment_error='.$this->code;
			$messageStack->add_session('checkout_payment', utf8_decode($xml_response->status_message) . '<!-- ['.$this->code.'] -->', 'error');						
			zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
		}
		else
		{             	
			$array = (array) $xml_response;
			
			// add tid, because it's missing in the answer
			$array['tid'] = $_SESSION['nn_tid_elv_de'];
			$array['statusdesc'] = $array['status_message']; // Param-name is changed
			$array['test_mode'] = $_SESSION['test_mode_elv_de'];
			return $array;			
		}						 	
	}
  
  
    public function curl_xml_post( $request )
	{
	    $ch = curl_init( "https://payport.novalnet.de/nn_infoport.xml" );
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: close'));
		curl_setopt($ch, CURLOPT_POST, 1);  // a non-zero parameter tells the library to do a regular HTTP post.
		curl_setopt($ch, CURLOPT_POSTFIELDS, $request);  // add POST fields
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);  // don't allow redirects
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  // decomment it if you want to have effective ssl checking
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  // decomment it if you want to have effective ssl checking
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  // return into a variable
		curl_setopt($ch, CURLOPT_TIMEOUT, 240);  // maximum time, in seconds, that you'll allow the CURL functions to take		  
		
		## establish connection
		$xml_response = curl_exec($ch);
		
		## determine if there were some problems on cURL execution
		$errno = curl_errno($ch);
		$errmsg = curl_error($ch);
		
		###bug fix for PHP 4.1.0/4.1.2 (curl_errno() returns high negative value in case of successful termination)
		if($errno < 0) $errno = 0;
		##bug fix for PHP 4.1.0/4.1.2
		
		if($debug)
		{
			/*
			print_r(curl_getinfo($ch));
			echo "\n<BR><BR>\n\n\nperform_https_request: cURL error number:" . $errno . "\n<BR>\n\n";
			echo "\n\n\nperform_https_request: cURL error:" . $errmsg . "\n<BR>\n\n";
			*/
		}
		
		#close connection
		curl_close($ch);
		
		return $xml_response;
	}  

  ### Display Bank Information on the Checkout Confirmation Page ###
  // @return array
  function confirmation() {
    global $_POST;

      $cardnoLength = strlen(str_replace(' ','',$_POST['bank_account']));
      $crdNo = str_replace(' ','',$_POST['bank_account']);
      $cardnoInfo = '';
      $chkLength = $cardnoLength-4;
      for($i=0;$i<$cardnoLength;$i++){
	if($i >= $chkLength){
	$cardnoInfo .= '*';
	}else{
	$cardnoInfo .= $crdNo[$i];
	}
      }

      $cardnoLength1 = strlen(str_replace(' ','',$_POST['bank_code']));
      $crdNo1 = str_replace(' ','',$_POST['bank_code']);
      $cardnoInfo1 = '';
      $chkLength1 = $cardnoLength1-3;
      for($i=0;$i<$cardnoLength1;$i++){
	if($i >= $chkLength1){
	$cardnoInfo1 .= '*';
	}else{
	$cardnoInfo1 .= $crdNo1[$i];
	}
      }

    $confirmation = array('fields' => array(array('title' => MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_BANK_ACCOUNT_OWNER,
                          'field' => $_POST['bank_account_holder']),
                    array('title' => MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_BANK_ACCOUNT_NUMBER,
                          'field' => $cardnoInfo),
                    array('title' => MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_BANK_CODE,
			   'field' => $cardnoInfo1)
                          ));

    return $confirmation;
  }

  ### Build the data and actions to process when the "Submit" button is pressed on the order-confirmation screen. ###
  ### These are hidden fields on the checkout confirmation page ###
  // @return string
  function process_button() {
    global $_POST;

    $process_button_string = zen_draw_hidden_field('bank_account_holder', $_POST['bank_account_holder']) .
                             zen_draw_hidden_field('bank_account', $_POST['bank_account']) .
                             zen_draw_hidden_field('bank_code', $_POST['bank_code']).zen_draw_hidden_field('acdc', $_POST['acdc']); 

    return $process_button_string;
  }

  ### Store the BANK info to the order ###
  ### This sends the data to the payment gateway for processing and Evaluates the Bankdatas for acceptance and the validity of the Bank Details ###
  function before_process() {
    global $_POST, $order, $db, $currencies, $messageStack;
	
	// Setting callback type // see constructor
	// First call is done, so check PIN / second call...
	if( $_SESSION['nn_tid_elv_de'] && $this->isActivatedCallback )
	{	
		//Test mode based on the responsone test mode value
		if($_SESSION['test_mode_elv_de'] ==  1){
			$order->info['comments'] .= MODULE_PAYMENT_NOVALNET_ELV_DE_TEST_ORDER_MESSAGE;	
		}
		$order->info['comments'] .= MODULE_PAYMENT_NOVALNET_ELV_DE_TID_MESSAGE.$_SESSION['nn_tid_elv_de'];
		$aryResponse = $this->secondCall();
		return;
	}	

    $test_mode = (strtolower(MODULE_PAYMENT_NOVALNET_ELV_DE_TEST_MODE) == 'true' or MODULE_PAYMENT_NOVALNET_ELV_DE_TEST_MODE == '1')? 1: 0;

    $order->info['bank_account'] = $_POST['bank_account'];
    $order->info['bank_account_holder'] = $_POST['bank_account_holder'];
    $order->info['bank_code'] = $_POST['bank_code'];

    #Get the required additional customer details from DB
    $customer_values = $db->Execute("SELECT customers_gender, customers_firstname, customers_lastname, customers_dob, customers_email_address, customers_telephone, customers_fax, customers_email_format FROM ". TABLE_CUSTOMERS . " WHERE customers_id='".(int)$_SESSION['customer_id']."'");
    while(!$customer_values->EOF) 
    {
       $customer_values->MoveNext();
    }
    list($customer_values->fields['customers_dob'], $extra) = explode(' ', $customer_values->fields['customers_dob']);
    ### Process the payment to paygate ##
    $url = 'https://payport.novalnet.de/paygate.jsp';
	$amount = $this->findTotalAmount();
    $product_id = MODULE_PAYMENT_NOVALNET_ELV_DE_PRODUCT_ID;
    $tariff_id = MODULE_PAYMENT_NOVALNET_ELV_DE_TARIFF_ID;
    $manual_check_limit = trim(MODULE_PAYMENT_NOVALNET_ELV_DE_MANUAL_CHECK_LIMIT);
    $manual_check_limit = str_replace(',', '', $manual_check_limit);
    $manual_check_limit = str_replace('.', '', $manual_check_limit);

    if($manual_check_limit && $amount>=$manual_check_limit)
    {
      $product_id = MODULE_PAYMENT_NOVALNET_ELV_DE_PRODUCT_ID2;
      $tariff_id = MODULE_PAYMENT_NOVALNET_ELV_DE_TARIFF_ID2;
    }


    $user_ip = $this->getRealIpAddr();
    $acdc = '';
    if($_POST['acdc']){$acdc = "&acdc=1";}
	
	//set the user telephone
	if($_SESSION['user_tel_elv_de']){
		$user_telephone = $_SESSION['user_tel_elv_de'];
	}else{
		$user_telephone	= $order->customer['telephone'];
	}
	// set post params
	if( $this->isActivatedCallback && strtolower($order->customer['country']['iso_code_2']) == 'de' && $amount >= MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_BY_CALLBACK_MIN_LIMIT )
	{			
		if( MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_BY_CALLBACK_SMS == 'Callback (Telefon & Handy)' )
			{ 
				$this->callback_type = '&pin_by_callback=1'; 
			}
		else   
			{ 
				$this->callback_type = '&pin_by_sms=1'; 
			}
	}	
    
    $urlparam = 'vendor='.MODULE_PAYMENT_NOVALNET_ELV_DE_VENDOR_ID.'&product='.MODULE_PAYMENT_NOVALNET_ELV_DE_PRODUCT_ID.'&key=2&tariff='.MODULE_PAYMENT_NOVALNET_ELV_DE_TARIFF_ID.'&auth_code='.MODULE_PAYMENT_NOVALNET_ELV_DE_AUTH_CODE.'&currency='.$order->info['currency'];
    $urlparam .= '&amount='.$amount.'&bank_account_holder='.$_POST['bank_account_holder'].'&bank_account='.$_POST['bank_account'];
    $urlparam .= '&bank_code='.$_POST['bank_code'].'&first_name='.$order->billing['firstname'].'&last_name='.$order->billing['lastname'];
    $urlparam .= '&street='.$order->billing['street_address'].'&city='.$order->billing['city'].'&zip='.$order->billing['postcode'];
    $urlparam .= '&country='.$order->billing['country']['iso_code_2'].'&email='.$customer_values->fields['customers_email_address'];
    $urlparam .= '&birth_date='.$customer_values->fields['customers_dob'].'&tel='.$user_telephone;
    $urlparam .= '&fax='.$customer_values->fields['customers_fax'].'&gender='.$customer_values->fields['customers_gender'].'&search_in_street=1';
    $urlparam .= '&input1=Bestellnummer&input_val1='.$order->info['order_status'].'&remote_ip='.$user_ip.$acdc;
    $urlparam .= '&language='.MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_LANG;
    // Setting callback type // see constructor
	$urlparam .= $this->callback_type;
	//echo $urlparam; exit;	
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

    if($aryResponse['status']==100)
    {
		### Passing through the Transaction ID from Novalnet's paygate into order-info ###
		if( $this->isActivatedCallback && strtolower($order->customer['country']['iso_code_2']) == 'de' && $amount >= MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_BY_CALLBACK_MIN_LIMIT ){
			$_SESSION['elv_de_order_amount']= $amount;
			$_SESSION['nn_tid_elv_de'] = $aryResponse['tid'];
			// To avoide payment method confussion add code in session
			//set session for maximum time limit to 30 minutes
			$_SESSION['max_time_elv_de'] = time() + (30 * 60);
			//TEST BILLING MESSAGE BASED ON THE RESPONSE TEST MODE
			$_SESSION['test_mode_elv_de'] = $aryResponse['test_mode'];			
		}else{
			$test_mode = (strtolower(MODULE_PAYMENT_NOVALNET_ELV_DE_TEST_MODE) == 'true' or MODULE_PAYMENT_NOVALNET_ELV_DE_TEST_MODE == '1')? 1: 0;

			$test_mode_value=( $aryResponse['test_mode'] == 1) ? $aryResponse['test_mode']: $test_mode;
			if ($test_mode_value){
				$order->info['comments'] .= MODULE_PAYMENT_NOVALNET_ELV_DE_TEST_ORDER_MESSAGE;
			}
		
			if( $this->order_status ) {
				$order->info['order_status'] = $this->order_status;
			}
			$order->info['comments'] .= MODULE_PAYMENT_NOVALNET_ELV_DE_TID_MESSAGE.$aryResponse['tid'];		
			$_SESSION['nn_tid_elv_de'] = $aryResponse['tid'];
		} 
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

    return $ip;
  }

  ### Send additional information about bankdata via email to the store owner ###
  ### Send the order detail to Novalnet ###
  function after_process() {
    global $order, $customer_id, $insert_id;

    $product_id = MODULE_PAYMENT_NOVALNET_ELV_DE_PRODUCT_ID;
    $tariff_id = MODULE_PAYMENT_NOVALNET_ELV_DE_TARIFF_ID;
	if($_SESSION['nn_tid_elv_de'] != ''){
    ### Pass the Order Reference to paygate ##
    $url = 'https://payport.novalnet.de/paygate.jsp';
    $urlparam = 'vendor='.MODULE_PAYMENT_NOVALNET_ELV_DE_VENDOR_ID.'&product='.$product_id.'&key=2&tariff='.$tariff_id;
    $urlparam .= '&auth_code='.MODULE_PAYMENT_NOVALNET_ELV_DE_AUTH_CODE.'&status=100&tid='.$_SESSION['nn_tid_elv_de'].'&reference=BNR-'.$insert_id.'&vwz3='.$insert_id.'&vwz3_prefix='.MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_ORDERNO.'&vwz4='.date('Y.m.d').'&vwz4_prefix='.MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_ORDERDATE;
	$urlparam .= '&order_no='.$insert_id;
    list($errno, $errmsg, $data) = $this->perform_https_request($url, $urlparam);
	}
    unset($_SESSION['nn_tid_elv_de']);
    unset($_SESSION['bank_account']);
    unset($_SESSION['bank_code']);
	unset($_SESSION['bank_account_holder']);
	unset($_SESSION['max_time_elv_de']);
	unset($_SESSION['test_mode_elv_de']);
	unset($_SESSION['user_tel_elv_de']);

    #print "$customer_id, $insert_id"; exit;
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

    $error = array('title' => MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_ERROR,
                   'error' => stripslashes(urldecode($_GET['error'])));

    return $error;
  }

  ### Check to see whether module is installed ###
  // @return boolean
  function check() {
    global $db;
    if (!isset($this->_check)) {
      $check_query = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_NOVALNET_ELV_DE_STATUS'");
      $this->_check = $check_query->RecordCount();
    }
    return $this->_check;
  }

  ### Install the payment module and its configuration settings ###
  function install() {
    global $db;
    $db->Execute("alter table ".TABLE_ORDERS." modify payment_method varchar(250)");
   $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Allowed zones', 'MODULE_PAYMENT_NOVALNET_ELV_DE_ALLOWED', '', 'Please enter the zones <b>separately</b> which should be allowed to use this modul (e. g. AT,DE (leave empty if you want to allow all zones))', '6', '0', now())");    
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable ELV-DE Module', 'MODULE_PAYMENT_NOVALNET_ELV_DE_STATUS', 'True', 'Do you want to activate the German Direct Debit Method(ELV-DE) of Novalnet AG?', '6', '1', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_BY_CALLBACK_SMS', 'False','When activated by PIN Callback / SMS the customer to enter their phone / mobile number requested. By phone or SMS, the customer receives from the AG Novalnet a PIN, which he must enter before ordering. If the PIN is valid, the payment process has been completed successfully, otherwise the customer will be prompted again to enter the PIN. This service is only available for customers from Germany.', '6', '3', 'zen_cfg_select_option(array( \'False\', \'Callback (Telefon & Handy)\', \'SMS (nur Handy)\'), ', now())");	
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_BY_CALLBACK_MIN_LIMIT', '','Please enter minimum amount limit to enable \"Pin by CallBack\" modul (In Cents, e.g. 100,200)', '6', '2', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Test Mode', 'MODULE_PAYMENT_NOVALNET_ELV_DE_TEST_MODE', 'True', 'Do you want to enable the test mode?', '6', '2', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");	
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Please contact sales@novalnet.de if you do not have any of the following Novalnet IDs!<BR><P>Wenn Sie keine oder irgendeine der folgenden Novalnet IDs nicht haben sollten, bitte sich an sales@novalnet.de wenden!<BR><P>Novalnet Merchant ID', 'MODULE_PAYMENT_NOVALNET_ELV_DE_VENDOR_ID', '', 'Your Merchant ID of Novalnet', '6', '3', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Novalnet Authorisation Code', 'MODULE_PAYMENT_NOVALNET_ELV_DE_AUTH_CODE', '', 'Your Authorisation Code of Novalnet', '6', '4', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Novalnet Product ID', 'MODULE_PAYMENT_NOVALNET_ELV_DE_PRODUCT_ID', '', 'Your Product ID of Novalnet', '6', '5', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Novalnet Tariff ID', 'MODULE_PAYMENT_NOVALNET_ELV_DE_TARIFF_ID', '', 'Your Tariff ID of Novalnet', '6', '6', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Manual checking amount in cents', 'MODULE_PAYMENT_NOVALNET_ELV_DE_MANUAL_CHECK_LIMIT', '', 'Please enter the amount in cents', '6', '7', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Your second Product ID in Novalnet', 'MODULE_PAYMENT_NOVALNET_ELV_DE_PRODUCT_ID2', '', 'for the manual checking', '6', '8', now())"); 

    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('The Tariff ID of the second product', 'MODULE_PAYMENT_NOVALNET_ELV_DE_TARIFF_ID2', '', 'for the manual checking', '6', '9', now())");
   $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable ACDC Control','MODULE_PAYMENT_NOVALNET_ELV_DE_ACDC', 'False', 'Do you want to activate the ACDC Control of Novalnet AG?', '6', '10','zen_cfg_select_option(array(\'True\', \'False\'), ', now())");

   $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Information to the Customer', 'MODULE_PAYMENT_NOVALNET_ELV_DE_INFO', '','will be shown on the payment formula', '6', '11', now())");

     $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort order of display.', 'MODULE_PAYMENT_NOVALNET_ELV_DE_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '12', now())");
     $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('Set Order Status', 'MODULE_PAYMENT_NOVALNET_ELV_DE_ORDER_STATUS_ID', '0', 'Set the status of orders made with this payment module to this value', '6', '13', 'zen_cfg_pull_down_order_statuses(', 'zen_get_order_status_name', now())");
     $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Payment Zone', 'MODULE_PAYMENT_NOVALNET_ELV_DE_ZONE', '0', 'If a zone is selected, only enable this payment method for that zone.', '6', '14', 'zen_get_zone_class_title', 'zen_cfg_pull_down_zone_classes(', now())");
     $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Proxy', 'MODULE_PAYMENT_NOVALNET_ELV_DE_PROXY', '0', 'If you use a Proxy Server, enter the Proxy Server IP here (e.g. www.proxy.de:80)', '6', '15', now())");      
  }
   
  ### Remove the module and all its settings ###
  function remove() {
    global $db;
    $db->Execute("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
  }

  ### Internal list of configuration keys used for configuration of the module ###
  // @return array
  function keys() {
    return array('MODULE_PAYMENT_NOVALNET_ELV_DE_ALLOWED','MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_BY_CALLBACK_SMS','MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_BY_CALLBACK_MIN_LIMIT', 'MODULE_PAYMENT_NOVALNET_ELV_DE_STATUS', 'MODULE_PAYMENT_NOVALNET_ELV_DE_TEST_MODE', 'MODULE_PAYMENT_NOVALNET_ELV_DE_VENDOR_ID', 'MODULE_PAYMENT_NOVALNET_ELV_DE_AUTH_CODE', 'MODULE_PAYMENT_NOVALNET_ELV_DE_PRODUCT_ID', 'MODULE_PAYMENT_NOVALNET_ELV_DE_TARIFF_ID', 'MODULE_PAYMENT_NOVALNET_ELV_DE_MANUAL_CHECK_LIMIT', 'MODULE_PAYMENT_NOVALNET_ELV_DE_PRODUCT_ID2', 'MODULE_PAYMENT_NOVALNET_ELV_DE_TARIFF_ID2', 'MODULE_PAYMENT_NOVALNET_ELV_DE_ACDC', 'MODULE_PAYMENT_NOVALNET_ELV_DE_INFO', 'MODULE_PAYMENT_NOVALNET_ELV_DE_SORT_ORDER', 'MODULE_PAYMENT_NOVALNET_ELV_DE_ORDER_STATUS_ID', 'MODULE_PAYMENT_NOVALNET_ELV_DE_ZONE','MODULE_PAYMENT_NOVALNET_ELV_DE_PROXY');
  }


}

?>
