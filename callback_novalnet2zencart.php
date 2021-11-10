<?php

#########################################################
#                                                       #
#  This script is used for real time capturing of       #
#  parameters passed from Novalnet AG after Payment     #
#  processing of customers.                             #
#                                                       #
#  Copyright (c) 2008 Novalnet AG                       #
#                                                       #
#  This script is only free to the use for Merchants of	#
#  Novalnet AG                                          #
#                                                       #
#  If you have found this script useful a small         #
#  recommendation as well as a comment on merchant form #
#  would be greatly appreciated.                        #
#                                                       #
#  Version : vPayportVendor1.0.0 2008-10-04             #
#                                                       #
#  Please contact sales@novalnet.de for enquiry or Info	#
#                                                       #
#########################################################
    #### TEST ###
    $test = true; #todo: set to false if going live or tests are finished
    if ($test){
      $t = 'test_mode=0&product_id=316&product=Testshop&marker=&payment_type=INVOICE_CREDIT&tid=12408500000401182&currency=EUR&amount=7345&status=100&subs_billing=&tariff_id=582&status_message=erfolgreich&paid_until=&signup_tid=&signup_date=&signup_time=&login=&password=&firstname=xxx&lastname=xxx&street=xxx&house_no=10&zip=12345&city=xxx&email=xxx@xxx.com&birthday=&country=Deutschland&country_code=DE&tid_payment=12408500000401182&reference=40&vendor_id=4';
      $t = explode('&', $t);
      foreach($t as $index => $value){
        $t2 = explode('=', $value);
        $_POST[$t2[0]] = $t2[1];
      }
      #echo'<pre>'; print_r($_POST);echo'</pre>';exit;
    }
    ### /TEST ###

    ### BASIC AND CONFIGURATION DATA ###
    $tid      = '';
    $status   = '';
    $today    = date("d.m.Y");
    ### DATA FOR DATABASE ###
    $mysqlhost = "localhost"; // MySQL-Host angeben
    $mysqluser = "root";      // MySQL-User angeben
    $mysqlpwd  = "";        // Passwort angeben
    $mysqldb   = ""; // Gewuenschte Datenbank angeben
    $order_table = 'orders';  # DB - ORDER Table to be updated
    $order_history_table = 'orders_status_history';  # DB - ORDER HISTORY Table
    $status_2b_set = '3'; # The Order Status which is to be set 
    $max_status = '3'; # The Status till where the order status can be changed

	$caller_ip = getRealIpAddr();
     
    ### NOVALNET DATA ###
    $vendor     = '4'; # vendor id at novalnet
    $product    = '99'; # product id at novalnet
    $ip_allowed = '195.143.189.210'; ## NOVALNET IP
    if ($test) {$caller_ip = $ip_allowed;}

    ### get params ###
    $aryCaptureParams = array();
    # passing/packing the parameters passed from the last page, in to an array
    $aryParams = array_merge($_POST,$_GET);
    # looping through the array and passing the parameters and values as variables

    if (!$aryParams){
      echo'Error: no parameters received!';
      exit;
    }

    foreach($aryParams as $blub=>$value){
      #echo $blub."=$value<br>";
      if($value!="")
      {
        $blub = remove_string($blub);
        $value = remove_string($value);
        eval("\$$blub=\"$value\";");
        $aryCaptureParams[$blub] = $value;
      }
    }
	$comments = "Bestellstatus aenderung: Gutschrifteingang bei Novalnet am $today"; # The comment to be entered  on order history table
    $from = ''; # email from which the notice email to be sent to technic
    $reply_to = ''; # email where the reply to notice email to be sent
    $email = ''; # email adress where the notice email to be sent
    $copy_to = ''; # copy email where the notice email to be sent
   
    $subject = 'FEHLER beim Aufruf zencart Novalnet Schnittstelle'; # subject of the email
    $body  = "Sehr geehrte Damen und Herren,\n\nfolgender Aufruf der Status Aenderung fuer TID '".$aryCaptureParams['tid']."' konnte nicht erfolgreich bearbeitet werden!\n\nBei Fragen wenden Sie sich bitte an Novalnet\n\ncaller_ip = $caller_ip\n\nnovalnet/callback_novalnet2zencart.php"; # body of the email
    #var_dump($aryCaptureParams);
	
    if(!isset($aryCaptureParams['reference']))   {$aryCaptureParams['reference']='';}
    if(!isset($aryCaptureParams['vendor_id']))   {$aryCaptureParams['vendor_id']='';}
    if(!isset($aryCaptureParams['product_id']))  {$aryCaptureParams['product_id']='';}
    if(!isset($aryCaptureParams['tid']))         {$aryCaptureParams['tid']='';}
    if(!isset($aryCaptureParams['tid_payment'])) {$aryCaptureParams['tid_payment']='';} #for payment types: invoice ans prepayment
    if(!isset($aryCaptureParams['payment_type'])){$aryCaptureParams['tid_payment']='';}
    if(isset($aryCaptureParams['status'])){
      $status = $aryCaptureParams['status'];
      if ($status == 100){
        echo 'Status ok';
      }else{
        echo 'Status not ok; Error msg: '.$aryCaptureParams['status_message'];
        exit;
      }
    }else{
      echo 'Param status n/a!';
      exit;
    }

    # we update the db only if status is available and has the value 100
    $tid = $aryCaptureParams['tid'];
    if (!empty($aryCaptureParams['payment_type']) and $aryCaptureParams['payment_type'] == 'INVOICE_CREDIT')#Zahlungseingang bei Vprkasse/Rechnung
    {
      $tid = $aryCaptureParams['tid_payment'];#tid_payment is the original tid
    }

    if($caller_ip == $ip_allowed && /*$aryCaptureParams['reference'] &&*/ $tid && $aryCaptureParams['vendor_id'] == $vendor){
      ### MAKE DB CONNECTION ###
      $connection = mysql_connect($mysqlhost, $mysqluser, $mysqlpwd) or die("Database connection failed!");
      mysql_select_db($mysqldb, $connection) or die("Can't select the Database!");

      $qry = "show tables like '$order_table'";
      $res = mysql_query($qry) or die("<center>".mysql_errno().";".mysql_error()."</center><br>\n");
      list ($table) = mysql_fetch_array($res, MYSQL_NUM);

      if($table){
		
        $qry = "select ot.customers_name, ot.customers_email_address, ot.orders_status, ot.date_purchased, ot.orders_id from $order_table as ot, $order_history_table as oht where ot.orders_id=oht.orders_id and oht.comments like '%$tid%'";
        $res = mysql_query($qry) or die("<center>".mysql_errno().";".mysql_error()."</center><br>\n");
        list ($customers_name, $customers_email_address, $orders_status, $date_purchased, $orders_id) = mysql_fetch_array($res, MYSQL_NUM);

        if (!mysql_num_rows($res)){
          echo'No order found for comments with tid: '.$tid;
          exit;
        }
        if($customers_name && $orders_status < $status_2b_set){ # && $orders_status!=$status_2b_set
          $qry = "update $order_table set orders_status = '$status_2b_set', last_modified = now() where orders_id = '$orders_id'";
          $res = mysql_query($qry) or die("<center>".mysql_errno().";".mysql_error()."</center><br>\n");
          $updated = mysql_affected_rows();
 
          if($updated){
            $qry = "show tables like '$order_history_table'";
            $res = mysql_query($qry) or die("<center>".mysql_errno().";".mysql_error()."</center><br>\n");
	          list ($table) = mysql_fetch_array($res, MYSQL_NUM);

            if($table){
              ### INSERT HISTORY RECORDS ###
              $qry = "insert into $order_history_table (orders_id, orders_status_id, date_added, customer_notified, comments) values ('".$aryCaptureParams['reference']."', '$status_2b_set', now(), '1', '$comments')";
              $res = mysql_query($qry) or die("<center>".mysql_errno().";".mysql_error()."</center><br>\n");
            }
            else{
              echo "table order_history_table n/a!";
            }
	        }
          else{
            echo'Error: status update failed or already updated for status ($status_2b_set)for orders_id: '.$orders_id;
            exit;
          }
        }
        else{
          echo"Nothing to update because order status ($orders_status) less or equal to new status ($status_2b_set)";
        }
      }
      else{
        echo 'Error: order table n/a!';
      }
      if ($test){
        echo'<br />Fertig!';
      }
    }
    else{
      print "important params (tid/vendor_id) missing!\r\n";
      ### ERRONEOUS CALL, SO SEND EMAIL TO TECHNIC

      if($from){
        header('Content-Type: text/html; charset=iso-8859-1');
        $headers = 'From: ' . $from . "\r\n";
        if($reply_to)$headers .= 'Reply-To: ' . $reply_to . "\r\n";
        else$headers .= 'Reply-To: ' . $from . "\r\n";

        if($copy_to)$headers .= 'Cc: ' . $copy_to . "\r\n";

        // calling the standard PHP Mail function
        $mailOk = mail($email, $subject, $body, $headers);
      }
    }

    function isPublicIP($value)
    {
        if(!$value || count(explode('.',$value))!=4) return false;
        return !preg_match('~^((0|10|172\.16|192\.168|169\.254|255|127\.0)\.)~', $value);
    }

    function getRealIpAddr()
    {
        if(isPublicIP($_SERVER['HTTP_X_FORWARDED_FOR'])) return $_SERVER['HTTP_X_FORWARDED_FOR'];
        if($iplist=explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            if(isPublicIP($iplist[0])) return $iplist[0];
        }
        if (isPublicIP($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
        if (isPublicIP($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
        if (isPublicIP($_SERVER['HTTP_FORWARDED_FOR']) ) return $_SERVER['HTTP_FORWARDED_FOR'];

        return $_SERVER['REMOTE_ADDR'];
    }

    ###########################################
    #  Function to remove unwanted string
    ###########################################
    function remove_string($string)
    {
        #$aryUnwantedString = ('"','^','`');
        $string = str_replace("'", "", $string);
        $string = str_replace('"', '', $string);
        $string = str_replace('^', '', $string);
        $string = str_replace('`', '', $string);

        return $string;
    }

/*
######### Note for Prepayment Invoice ###############################################
BEMERKUNG: Da bei Vorkasse/per Rechnung der eigentliche Zahlungseingang erst später erfolgt,  muss man das Vendor Skript einsetzen (es auf Ihrem Webserver unterbringen und den Link im Novalnet-Admin-Tool eintragen via https://admin.novalnet.de -> Angebote -> Das entsprechend Angebot auswählen bzw. anklicken -> Händlerskript URL), an das bei jeder Transaktion eine Rückmeldung vom Novalnet-Server gesendet wird. Hier ein Beispiel, wenn ein Endkunde im Shop per Vorkasse/Rechnung bezahlt und später das Geld überwiesen hat, das 
dann auf dem Novalnet-Konto eingegangen ist:
 
test_mode=0&product_id=316&product=Testshop&marker=&payment_type=INVOICE_CREDIT&tid=12112700001500028&currency=EUR&amount=2995&status=100&subs_billing=&tariff_id=582&status_message=erfolgreich&paid_until=&signup_tid=&signup_date=&signup_time=&login=&password=&firstname=xxx&lastname=xxx&street=xxx&house_no=10&zip=12345&city=xxx&email=xxx@xxx.com&birthday=&country=Deutschland&country_code=DE&tid_payment=12112700001429137&reference=&vendor_id=229

Der Parameter payment_type=INVOICE_CREDIT bedeutet, dass ein Geldeingang erfolgt ist. Der Parameter tid_payment stellt die Original-TID (als der Endkunde im Shop bestellt hat) dar, der Parameter tid die Folge-TID (als der Endkunde das Geld überwiesen/bezahlt hat). Anhand der tid_payment läßt sich dann die tid zuordnen.
######### /Note for Prepayment Invoice ###############################################

######### Overview Payment Types ###############################
        'DIRECT_DEBIT_DE'; #german direct debit
        'DIRECT_DEBIT_AT'; #Austrian direct debit
        'CREDITCARD';
        'PAYSAFECARD';
        'INVOICE_START'; #Invoice/Prepayment transaction request
        'NOVALTEL_DE';   #telephone payment
        'ONLINE_TRANSFER'; # payment per online bank transfer

        ### TYPE OF CHARGEBACKS AVAILABLE ###
        'RETURN_DEBIT_DE'; #german return debit
        'RETURN_DEBIT_AT'; #Austrian return debit
        'CREDITCARD_CHARGEBACK'; 
        'COLLECTION_REVERSAL_DE'; #return debit of a collection (Inkasso RLS)
        'NOVALTEL_DE_CHARGEBACK'; #chargeback of a telephone payment	

        ### TYPE OF PAYMENT COLLECTIONS AVAILABLE ###
        'DEBT_COLLECTION_DE'; #durch Inkasso beigetreibene ELV-DE
        'DEBT_COLLECTION_AT'; #durch Inkasso beigetreibene ELV-AT
        'INVOICE_CREDIT';  # Gutschrift Eingang (Vorkasse/Rechnung)
        'CREDIT_ENTRY_DE'; # Gutschrift Eingang DE (Endkunden Üerweisung)
        'CREDIT_ENTRY_AT'; # Gutschrift Eingang AT (Endkunden Üerweisung)
        'NOVALTEL_DE_COLLECTION'; #durch Inkasso beigetreibene telephone payment
        'NOVALTEL_DE_CB_REVERSAL'; #reclamation of a credit card chargeback
######### /Overview Payment Types ###############################
*/

?>
