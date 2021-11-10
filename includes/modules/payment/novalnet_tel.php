<?php
//ini_set('display_errors',1);
#########################################################
#                                                       #
#  Telephone payment method class                       #
#  This module is used for real time processing of      #
#  Telephone Payment of customers.                      #
#                                                       #
#  Copyright (c) 2009 Novalnet AG                       #
#                                                       #
#  Released under the GNU General Public License        #
#  Novalnet_tel module Created By Dixon Rajdaniel       #
#  This free contribution made by request.              #
#  If you have found this script usefull a small        #
#  recommendation as well as a comment on merchant form #
#  would be greatly appreciated.                        #
#                                                       #
#  Version : novalnet_tel.php vzenTel1.3.1 2009-07-01 	#
#                                                       #
#########################################################

class novalnet_tel {


  var $code;
  var $title;
  var $description;
  var $enabled;
  var $proxy;  

  function novalnet_tel() {
    global $order;


    $this->code = 'novalnet_tel';
    $this->title = MODULE_PAYMENT_NOVALNET_TEL_TEXT_TITLE;
    $this->public_title = MODULE_PAYMENT_NOVALNET_TEL_TEXT_PUBLIC_TITLE;
    $this->description = MODULE_PAYMENT_NOVALNET_TEL_TEXT_DESCRIPTION;
    $this->sort_order = MODULE_PAYMENT_NOVALNET_TEL_SORT_ORDER;
    $this->enabled = ((MODULE_PAYMENT_NOVALNET_TEL_STATUS == 'True') ? true : false);
    $this->proxy        = MODULE_PAYMENT_NOVALNET_TEL_PROXY;    

    if ((int)MODULE_PAYMENT_NOVALNET_TEL_ORDER_STATUS_ID > 0) {
      $this->order_status = MODULE_PAYMENT_NOVALNET_TEL_ORDER_STATUS_ID;
    }

//echo "ya"; exit;
    if (is_object($order)) $this->update_status();
  }
  
  ### calculate zone matches and flag settings to determine whether this module should display to customers or not ###
  function update_status() {
    global $order, $db;

    if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_NOVALNET_TEL_ZONE > 0) ) {
      $check_flag = false;
      $check = $db->Execute("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_NOVALNET_TEL_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
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
    global $order, $order_total_modules, $currencies;
    /*
    require_once (DIR_WS_CLASSES.'shipping.php');echo'hihi';
    $shipping_modules = new shipping($_SESSION['shipping']);
    $order_total_modules->process();
    $_SESSION['nn_total'] = sprintf('%0.2f', trim($order->info['total']));
    */
    $onFocus = '';



    $_SESSION['nn_total'] = sprintf('%0.2f', trim($order->info['total']));
//    $amount = $_SESSION['nn_total'];

    if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status_add_tax_ot'] == 1) {
	$totalamount=$order->info['total'] + $order->info['tax'];
	} else { 
	$totalamount=$order->info['total'];
    }
    $amount =sprintf('%.2f', $totalamount);
    $err    = '';
    if (preg_match('/[^\d\.]/', $amount) or !$amount){
      ### $amount contains some unallowed chars or empty ###
      $err                      = '$amount ('.$amount.') is empty or has a wrong format';
      $order->info['comments'] .= '. Novalnet Error Message : '.$err;
      $payment_error_return     = 'payment_error='.$this->code;
	  $messageStack->add_session('checkout_payment', $err . '<!-- ['.$this->code.'] -->', 'error');	  
      zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
    }
    $amount = preg_replace('/^0+/', '', $amount);
    $orig_amount = $amount;
    #$amount      = sprintf('%0.2f', $amount);
    $amount      = str_replace('.', '', $amount);
    #echo''.__CLASS__.$order->info['total']." <=> $amount<hr />";
    if($amount>90 && $amount<=1000000000)
    {
      if(!isset($_SESSION['tid']) or empty($_SESSION['tid']))
      {
	      ### FIRST CALL ###
	      $selection = array('id' => $this->code,
                       'module' => $this->title,
                       'fields' => array(array('title' => '',
                                               'field' => '' /*MODULE_PAYMENT_NOVALNET_TEL_INFO*/)
                               ));
      }
      else
      {
        ### SECOND CALL ###
        $sess_tel = trim($_SESSION['novaltel_no']);

        if($sess_tel)
        {
          $aryTelDigits = str_split($sess_tel, 4);
          $count = 0;
          $str_sess_tel = '';
          foreach ($aryTelDigits as $ind=>$digits)
          {
            $count++;
            $str_sess_tel .= $digits;
            if($count==1) $str_sess_tel .= '-';
            else $str_sess_tel .= ' ';
          }
          $str_sess_tel=trim($str_sess_tel);
          if($str_sess_tel) $sess_tel=$str_sess_tel;
        }

        $selection = array('id' => $this->code,
                       'module' => $this->public_title,
                       'fields' => array(array('title' => '',
                                               'field' => "<BR>".MODULE_PAYMENT_NOVALNET_TEL_TEXT_STEP_INFO."</B>"),
                   array('title' => MODULE_PAYMENT_NOVALNET_TEL_TEXT_STEP1,
                                               'field' => MODULE_PAYMENT_NOVALNET_TEL_TEXT_STEP1_DESC." <B>$sess_tel</B><BR>".MODULE_PAYMENT_NOVALNET_TEL_TEXT_COST_INFO.$orig_amount.MODULE_PAYMENT_NOVALNET_TEL_TEXT_TAX_INFO),
                   array('title' => MODULE_PAYMENT_NOVALNET_TEL_TEXT_STEP2,
                                               'field' => MODULE_PAYMENT_NOVALNET_TEL_TEXT_STEP2_DESC)
                               ));
      }
      /*if(function_exists('get_percent'))
      {
        $selection['module_cost'] = $GLOBALS['ot_payment']->get_percent($this->code);
      }*/
    }#end of if($amount>90 && $amount<=1000)
    else{ }#phonepayment not allowed because of amount beeing too large

    return $selection;
  }

  ### Precheck to Evaluate the Bank Datas ###
  function pre_confirmation_check() {
    global $order, $currencies, $customer_id, $db;
    $error = '';
    $focus_on = '';

    if(!MODULE_PAYMENT_NOVALNET_TEL_VENDOR_ID || !MODULE_PAYMENT_NOVALNET_TEL_AUTH_CODE || !MODULE_PAYMENT_NOVALNET_TEL_PRODUCT_ID || !MODULE_PAYMENT_NOVALNET_TEL_TARIFF_ID)
    {
      $error = MODULE_PAYMENT_NOVALNET_TEL_TEXT_JS_NN_MISSING;
    }
    elseif(!$_SESSION['tid'])
    {
      #Get the required additional customer details from DB
      $customer = $db->Execute("SELECT customers_gender, customers_dob, customers_fax FROM ". TABLE_CUSTOMERS . " WHERE customers_id='". (int)$_SESSION['customer_id']."'");

      if ($customer->RecordCount() > 0){
        $customer = $customer->fields;
      }
      list($customer['customers_dob'], $extra) = explode(' ', $customer['customers_dob']);

      ### Process the payment to paygate ##
      $url = 'https://payport.novalnet.de/paygate.jsp';

  //  $amount = $_SESSION['nn_total'];
      if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status_add_tax_ot'] == 1) {
	  $totalamount=$_SESSION['nn_total'] + $order->info['tax'];
	  } else { 
	  $totalamount=$_SESSION['nn_total'];
    }
    $amount =sprintf('%.2f', $totalamount);

    if (preg_match('/[^\d\.]/', $amount) or !$amount){
      ### $amount contains some unallowed chars or empty ###
      $err                      = '$amount ('.$amount.') is empty or has a wrong format';
      $order->info['comments'] .= '. Novalnet Error Message : '.$err;
      $payment_error_return     = 'payment_error='.$this->code.'&error='.$err;
      zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
    }
    $amount = preg_replace('/^0+/', '', $amount);
    $orig_amount = $amount;
    $amount      = sprintf('%0.2f', $amount);
    $amount      = str_replace('.', '', $amount);
    #echo''.__CLASS__.$order->info['total']." <=> $amount<hr />";

    $product_id = MODULE_PAYMENT_NOVALNET_TEL_PRODUCT_ID;
    $tariff_id  = MODULE_PAYMENT_NOVALNET_TEL_TARIFF_ID;
    $user_ip    = $this->getRealIpAddr();

    ### Process the payment to paygate ##
    $url = 'https://payport.novalnet.de/paygate.jsp';
    $urlparam = 'vendor='.MODULE_PAYMENT_NOVALNET_TEL_VENDOR_ID.'&product='.$product_id.'&key=18&tariff='.$tariff_id;
    $urlparam .= '&auth_code='.MODULE_PAYMENT_NOVALNET_TEL_AUTH_CODE.'&currency='.$order->info['currency'];
    $urlparam .= '&amount='.$amount.'&first_name='.$this->html_to_utf8($order->customer['firstname']).'&last_name='.$this->html_to_utf8($order->customer['lastname']);
    $urlparam .= '&street='.$this->html_to_utf8($order->customer['street_address']).'&city='.$this->html_to_utf8($order->customer['city']);
    $urlparam .= '&zip='.$order->customer['postcode'];
    $urlparam .= '&country='.$order->customer['country']['iso_code_2'].'&email='.$order->customer['email_address'];
    $urlparam .= '&search_in_street=1&tel='.$order->customer['telephone'].'&remote_ip='.$user_ip;
    $urlparam .= '&gender='.$customer['customers_gender'].'&birth_date='.$customer['customers_dob'].'&fax='.$customer['customers_fax'];
    $urlparam .= '&language='.MODULE_PAYMENT_NOVALNET_TEL_TEXT_LANG;
  // $test_mode = (strtolower(MODULE_PAYMENT_NOVALNET_TEL_TEST_MODE) == 'true' or MODULE_PAYMENT_NOVALNET_TEL_TEST_MODE == '1')? 1: 0;
    $urlparam .= '&test_mode='.$test_mode;


	list($errno, $errmsg, $data) = $this->perform_https_request($url, $urlparam);

 	$aryPaygateResponse = array();
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


//print_r($aryResponse); exit;

	if($aryResponse['status']==100 && $aryResponse['tid'])
        {
      $_SESSION['t_id']=$aryResponse['tid'];  
	  if( $this->order_status ) {
			$order->info['order_status'] = $this->order_status;
		}
          $aryResponse['status_desc']='';
          if(!$_SESSION['tid'])
          {
             $_SESSION['tid'] = $aryResponse['tid'];
             $_SESSION['novaltel_no'] = $aryResponse['novaltel_number'];
          }
        }
        elseif($aryResponse['status']==18){}
        elseif($aryResponse['status']==19)
        {
           $_SESSION['tid'] = '';
           $_SESSION['novaltel_no'] = '';
        }
        else $status = $aryResponse['status'];
	if($aryResponse['status']==100){
      $_SESSION['t_id']=$aryResponse['tid'];  
      $error=' ';$focus_on='#novalnet_tel';}
	else{$error=$aryResponse['status_desc'];}
    }

    if($error!='') {
     // $payment_error_return = 'payment_error='.$this->code.'&error='.urlencode($error);
	$error_value=substr($error,0,43);
	$payment_error_return="payment_error=".$this->code."&error=".$error_value;

        zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));

//      zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
    }

  }

  ### Display Bank Information on the Checkout Confirmation Page ###
  // @return array
  function confirmation() {

    $confirmation = array('fields' => array(array('title' => '', 'field' => '')));

    return $confirmation;
  }

  ### Build the data and actions to process when the "Submit" button is pressed on the order-confirmation screen. ###
  ### These are hidden fields on the checkout confirmation page ###
  // @return string
  function process_button() {
    $process_button_string = '';

    return $process_button_string;
  }

  ### This sends the data to the payment gateway for processing and Evaluates the Payment for acceptance and the validity of the Telephone Details ###
  function before_process() {
    global $order, $currencies, $customer_id, $db;
    
 //   $test_mode = (strtolower(MODULE_PAYMENT_NOVALNET_TEL_TEST_MODE) == 'true' or MODULE_PAYMENT_NOVALNET_TEL_TEST_MODE == '1')? 1: 0;
 //   if ($test_mode){
 //     $order->info['comments'] .= 'TESTBESTELLUNG<br />';
 //   }

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
	$totalamount=$_SESSION['nn_total'] + $order->info['tax'];
	} else { 
	$totalamount=$_SESSION['nn_total'];
      }
    $amount =sprintf('%.2f', $totalamount);

    if(!$amount)$amount = $order->info['total'];
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

    $product_id = MODULE_PAYMENT_NOVALNET_TEL_PRODUCT_ID;
    $tariff_id = MODULE_PAYMENT_NOVALNET_TEL_TARIFF_ID;

    $user_ip = $this->getRealIpAddr();
    $aryPaygateResponse = array();

    if($_SESSION['tid'])
    {
        ### Process the payment to payport ##
        $url = 'https://payport.novalnet.de/nn_infoport.xml';

        $urlparam = '<nnxml><info_request><vendor_id>'.MODULE_PAYMENT_NOVALNET_TEL_VENDOR_ID.'</vendor_id>';
        $urlparam .= '<vendor_authcode>'.MODULE_PAYMENT_NOVALNET_TEL_AUTH_CODE.'</vendor_authcode>';
        $urlparam .= '<request_type>NOVALTEL_STATUS</request_type><tid>'.$_SESSION['tid'].'</tid>';
        $urlparam .= '<lang>'.MODULE_PAYMENT_NOVALNET_TEL_TEXT_LANG.'</lang></info_request></nnxml>';

       list($errno, $errmsg, $data) = $this->perform_https_request($url, $urlparam);

       if(strstr($data, '<novaltel_status>'))
       {
         preg_match('/novaltel_status>?([^<]+)/i', $data, $matches);
         $aryResponse['status'] = $matches[1];

         preg_match('/novaltel_status_message>?([^<]+)/i', $data, $matches);
         $aryResponse['status_desc'] = $matches[1];
       }
    } 
    #var_dump($aryResponse); exit;
    if($_SESSION['tid'] && $aryResponse['status']==100) #### On successful payment ####
    {
       #### Redirecting the user to the checkout page ####
       $order->info['comments'] .= '. Novalnet Transaction ID : '.$_SESSION['tid'];
       $_SESSION['tid'] = '';
       $_SESSION['novaltel_no'] = '';
    }
    else #### On payment failure ####
    {
       ### Passing the Error Response from Novalnet's paygate to payment error ###
       $status = '';
       if($wrong_amount==1){$status = '1';$aryResponse['status_desc'] = MODULE_PAYMENT_NOVALNET_TEL_TEXT_AMOUNT_ERROR1;}
       elseif($aryResponse['status']==18){}
       elseif($aryResponse['status']==19)
       {
           $_SESSION['tid'] = '';
           $_SESSION['novaltel_no'] = '';
       }
       else $status = $aryResponse['status'];

       ### Passing through the Error Response from Novalnet's paygate into order-info ###
       #$order->info['comments'] .= '. Novalnet Error Code : '.$aryResponse['status'].', Novalnet Error Message : '.$aryResponse['status_desc'];

       $payment_error_return = 'payment_error=' . $this->code . '&error1=' . substr(urlencode($aryResponse['status_desc']),0,43);

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
      $data = utf8_decode($this->ReplaceSpecialGermanChars($data));
      #print "$data"; exit;

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

      $order->info['comments'] .= '. Novalnet Transaction ID : '.$_SESSION['t_id'];
      $db->Execute("update ".TABLE_ORDERS_STATUS_HISTORY." set comments = '".$order->info['comments']."' , orders_status_id= '".$this->order_status."' where orders_id = '".$insert_id."'");
      $db->Execute("update ".TABLE_ORDERS." set orders_status = '".$this->order_status."' where orders_id = '".$insert_id."'");
	  if($_SESSION['tid']){
		### Pass the Order Reference to paygate ##
		$url = 'https://payport.novalnet.de/paygate.jsp';
		$urlparam = 'vendor='.MODULE_PAYMENT_NOVALNET_TEL_VENDOR_ID.'&product='.$product_id.'&key=27&tariff='.$tariff_id;
		$urlparam .= '&auth_code='.MODULE_PAYMENT_NOVALNET_TEL_AUTH_CODE.'&status=100&tid='.$_SESSION['tid'].'&reference=BNR-'.$insert_id.'&vwz2='.MODULE_PAYMENT_NOVALNET_TEL_TEXT_ORDERNO.''.$insert_id.'&vwz3='.MODULE_PAYMENT_NOVALNET_TEL_TEXT_ORDERDATE.''.date('Y-m-d H:i:s');
		$urlparam .= '&order_no='.$insert_id;
		list($errno, $errmsg, $data) = $this->perform_https_request($url, $urlparam);
	  }
	  
	  
	  
      unset($_SESSION['tid']);
      unset($_SESSION['novaltel_no']);

    return false;
  }

  ### Used to display error message details ###
  // @return array
  function get_error() {
    global $HTTP_GET_VARS, $_GET;
    if(count($HTTP_GET_VARS)==0 || $HTTP_GET_VARS=='') $HTTP_GET_VARS = $_GET;

    $error = array('title' => MODULE_PAYMENT_NOVALNET_TEL_TEXT_ERROR,
                   'error' => stripslashes(urldecode($HTTP_GET_VARS['error'])));

    return $error;
  }

  ### Check to see whether module is installed ###
  // @return boolean
  function check() {
    global $db;
    if (!isset($this->_check)) {
      $check_query = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_NOVALNET_TEL_STATUS'");
      $this->_check = $check_query->RecordCount();
    }
    return $this->_check;
  }

  ### Install the payment module and its configuration settings ###
  function install() {
    global $db;
    $db->Execute("alter table ".TABLE_ORDERS." modify payment_method varchar(250)");  	
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Allowed zones','MODULE_PAYMENT_NOVALNET_TEL_ALLOWED', '','Please enter the zones <b>separately</b> which should be allowed to use this modul (e. g. AT,DE (leave empty if you want to allow all zones))', '6', '0', now())");    
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Phone Payment Module', 'MODULE_PAYMENT_NOVALNET_TEL_STATUS', 'True', 'Do you want to activate the Phone Payment Method of Novalnet AG?', '6', '1', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
 //   $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Test Mode', 'MODULE_PAYMENT_NOVALNET_TEL_TEST_MODE', 'True', 'Do you want to activate Test Mode?', '6', '2', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Please contact sales@novalnet.de if you do not have any of the following Novalnet IDs!<BR><P>Wenn Sie keine oder irgendeine der folgenden Novalnet IDs nicht haben sollten, bitte sich an sales@novalnet.de wenden!<BR><P>Novalnet Merchant ID', 'MODULE_PAYMENT_NOVALNET_TEL_VENDOR_ID', '', 'Your Merchant ID of Novalnet', '6', '3', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Novalnet Authorisation Code', 'MODULE_PAYMENT_NOVALNET_TEL_AUTH_CODE', '', 'Your Authorisation Code of Novalnet', '6', '4', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Novalnet Product ID', 'MODULE_PAYMENT_NOVALNET_TEL_PRODUCT_ID', '', 'Your Product ID of Novalnet', '6', '5', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Novalnet Tariff ID', 'MODULE_PAYMENT_NOVALNET_TEL_TARIFF_ID', '', 'Your Tariff ID of Novalnet', '6', '6', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Information to the Customer', 'MODULE_PAYMENT_NOVALNET_TEL_INFO', '', 'Will be shown on the payment formula', '6', '7', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort order of display.', 'MODULE_PAYMENT_NOVALNET_TEL_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '8', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('Set Order Status', 'MODULE_PAYMENT_NOVALNET_TEL_ORDER_STATUS_ID', '0', 'Set the status of orders made with this payment module to this value', '6', '9', 'zen_cfg_pull_down_order_statuses(', 'zen_get_order_status_name', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Payment Zone', 'MODULE_PAYMENT_NOVALNET_TEL_ZONE', '0', 'If a zone is selected, only enable this payment method for that zone.', '6', '10', 'zen_get_zone_class_title', 'zen_cfg_pull_down_zone_classes(', now())");
     $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Proxy', 'MODULE_PAYMENT_NOVALNET_TEL_PROXY', '0', 'If you use a Proxy Server, enter the Proxy Server IP here (e.g. www.proxy.de:80)', '6', '11', now())");     

  }
   
  ### Remove the module and all its settings ###
  function remove() {
    global $db;
    $db->Execute("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
  }

  ### Internal list of configuration keys used for configuration of the module ###
  // @return array
  function keys() {
    return array('MODULE_PAYMENT_NOVALNET_TEL_ALLOWED','MODULE_PAYMENT_NOVALNET_TEL_STATUS', /* 'MODULE_PAYMENT_NOVALNET_TEL_TEST_MODE',*/ 'MODULE_PAYMENT_NOVALNET_TEL_VENDOR_ID', 'MODULE_PAYMENT_NOVALNET_TEL_AUTH_CODE', 'MODULE_PAYMENT_NOVALNET_TEL_PRODUCT_ID', 'MODULE_PAYMENT_NOVALNET_TEL_TARIFF_ID', 'MODULE_PAYMENT_NOVALNET_TEL_INFO', 'MODULE_PAYMENT_NOVALNET_TEL_SORT_ORDER', 'MODULE_PAYMENT_NOVALNET_TEL_ORDER_STATUS_ID', 'MODULE_PAYMENT_NOVALNET_TEL_ZONE','MODULE_PAYMENT_NOVALNET_TEL_PROXY');
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
}
?>
