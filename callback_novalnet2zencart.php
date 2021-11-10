<?php

/**
 * Novalnet Callback Script for Zencart
 *
 * NOTICE
 *
 * This script is used for real time capturing of parameters passed 
 * from Novalnet AG after Payment processing of customers.
 *
 * This script is only free to the use for Merchants of Novalnet AG
 *
 * If you have found this script useful a small recommendation as well
 * as a comment on merchant form would be greatly appreciated.
 *
 * Please contact sales@novalnet.de for enquiry or info
 *
 * ABSTRACT: This script is called from Novalnet, as soon as a payment 
 * done for payment methods, e.g. Prepayment, Invoice.
 * An email will be sent if an error occurs
 *
 *
 * @category   Novalnet
 * @package    Novalnet
 * @version    1.0
 * @copyright  Copyright (c) 2012 Novalnet AG. (http://www.novalnet.de)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @notice     1. This script must be placed in Zencart root folder
 *                to avoid rewrite rules (mod_rewrite)
 *             2. You have to adapt the value of all the variables
 *                commented with 'adapt ...'
 *             3. Set $test/$debug to false for live system
 */
require('includes/application_top.php');

//Variable Settings
$debug = true; //false|true; adapt: set to false for go-live
$test = true; //false|true; adapt: set to false for go-live
$lineBreak = empty($_SERVER['HTTP_HOST']) ? PHP_EOL : '<br />';
$addSubsequentTidToDb = true; //whether to add the new tid to db; adapt if necessary
// Order State/Status Settings
/*   4. Standard Types of Status:
  1. Pending = 1
  2. Processing = 2
  3. Delivered  = 3
  4. Update  = 4
  
 */
$orderState = 3; //Note: Indicates Payment accepted.
//Security Setting; only this IP is allowed for call back script
$ipAllowed = '195.143.189.210'; //Novalnet IP, is a fixed value, DO NOT CHANGE!!!!!
//Reporting Email Addresses Settings
$shopInfo = 'Zencart Shop' . $lineBreak; //manditory;adapt for your need
$mailHost = 'mail.novalnet.de'; //adapt
$mailPort = 25; //adapt
$emailFromAddr = ''; //sender email addr., manditory, adapt it
$emailToAddr = ''; //recipient email addr., manditory, adapt it
$emailSubject = 'Novalnet Callback Script Access Report'; //adapt if necessary; 
$emailBody = ''; //Email text, adapt
$emailFromName = ""; // Sender name, adapt
$emailToName = ""; // Recipient name, adapt
//Parameters Settings
$hParamsRequired = array(
    'vendor_id' => '',
    'tid' => '',
    'payment_type' => '',
    'status' => '',
    'amount' => '',
    'tid_payment' => '');

$hParamsTest = array(
    'vendor_id' => '4',
    'status' => '100',
    'amount' => '52679', //must be avail. in shop database; 850 = 8.50
    'payment_type' => 'INVOICE_CREDIT',
    'tid_payment' => '12613900002304354', //orig. tid; must be avail. in shop database
    'tid' => '12345678901234567', //subsequent tid, from Novalnet backend; can be a fake for test
);

//Test Data Settings
if ($test) {
    $_REQUEST = $hParamsTest;
    $emailFromName = "Novalnet"; // Sender name, adapt
    $emailToName = "Novalnet"; // Recipient name, adapt
    $emailFromAddr = 'test@novalnet.de'; //manditory for test; adapt
    $emailToAddr = 'test@novalnet.de'; //manditory for test; adapt
    $emailSubject = $emailSubject . ' - TEST'; //adapt
}

// ################### Main Prog. ##########################
try {
    //Check Params
    if (checkIP($_REQUEST)) {
        if (checkParams($_REQUEST)) {
            //Get Order ID and Set New Order Status
            if ($orderIncrementId = getIncrementId($_REQUEST)) {
                setOrderStatus($orderIncrementId); //and send error mails if any
            }
        }
    }
    if (!$emailBody) {
        $emailBody .= 'Novalnet Callback Script called for StoreId Parameters: ' . print_r($_POST, true) . $lineBreak;
        $emailBody .= 'Novalnet callback succ. ' . $lineBreak;
        $emailBody .= 'Params: ' . print_r($_REQUEST, true) . $lineBreak;
    }
} catch (Exception $e) {
    $emailBody .= "Exception catched: $lineBreak\$e:" . $e->getMessage() . $lineBreak;
}

if ($emailBody) {
    if (!sendEmailZencart($emailBody)) {
        if ($debug) {
            echo "Mailing failed!" . $lineBreak;
            echo "This mail text should be sent: " . $lineBreak;
            echo $emailBody;
        }
    }
}

// ############## Sub Routines #####################
function sendEmailZencart($emailBody) {
    global $lineBreak, $debug, $test, $emailFromAddr, $emailToAddr, $emailFromName, $emailToName, $emailSubject, $shopInfo, $mailHost, $mailPort;
    $emailBodyT = str_replace('<br />', PHP_EOL, $emailBody);
    //Send Email
    ini_set('SMTP', $mailHost);
    ini_set('smtp_port', $mailPort);
    header('Content-Type: text/html; charset=iso-8859-1');
    $headers = 'From: ' . $emailFromAddr . "\r\n";
    try {
        if ($debug) {
            echo __FUNCTION__ . ': Sending Email suceeded!' . $lineBreak;
        }
        $sendmail = mail($emailToAddr, $emailSubject, $emailBodyT, $headers);
    } catch (Exception $e) {
        if ($debug) {
            echo 'Email sending failed: ' . $e->getMessage();
        }
        return false;
    }
    if ($debug) {
        echo 'This text has been sent:' . $lineBreak . $emailBody;
    }
    return true;
}

function checkParams($_request) {
    global $lineBreak, $hParamsRequired, $emailBody;
    $error = false;
    $emailBody = '';
    if (!$_request) {
        $emailBody .= 'No params passed over!' . $lineBreak;
        return false;
    } elseif ($hParamsRequired) {
        foreach ($hParamsRequired as $k => $v) {
            if (empty($_request[$k])) {
                $error = true;
                $emailBody .= 'Required param (' . $k . ') missing!' . $lineBreak;
            }
        }
        if ($error) {
            return false;
        }
    }
    //Only Payment Type 'INVOICE_CREDIT' allowed; Otherwise you have to adapt the logic
    if (!empty($_request['payment_type']) and 'INVOICE_CREDIT' != strtoupper($_request['payment_type'])) {
        // Nothing to do
        $emailBody .= "Novalnet callback received. But payment_type != INVOICE_CREDIT (" . $_request['payment_type'] . ")$lineBreak";
        return false;
    }

    if (!empty($_request['status']) and 100 != $_request['status']) {
        $emailBody .= 'The status codes [' . $_request['status'] . '] is not valid: Only 100 is allowed.' . "$lineBreak$lineBreak" . $lineBreak;
        return false;
    }
    return true;
}

function getIncrementId($_request) {
    global $lineBreak, $tableOrderPayment, $tableOrder, $emailBody, $debug, $db;
    $orderDetails = array();

    if (!empty($_request['order_no'])) {
        return $_request['order_no'];
    } elseif (!empty($_request['order_id'])) {
        return $_request['order_id'];
    }
	if(strlen($_request['tid_payment'])==17){
		$query = "SELECT orders_id, orders_status_id from " . TABLE_ORDERS_STATUS_HISTORY . " WHERE comments LIKE '%" . $_request['tid_payment'] . "%'";
		try {
			$orders = $db->Execute($query);
			$orders_id = $orders->fields['orders_id'];
			$order_status = $orders->fields['orders_status_id'];
		} catch (Exception $e) {
			$emailBody .= 'The original order not found in the shop database table (`' . TABLE_ORDERS_STATUS_HISTORY . '`);';
			$emailBody .= 'Reason: ' . $e->getMessage() . $lineBreak . $lineBreak;
			$emailBody .= 'Query : ' . $qry . $lineBreak . $lineBreak;
			return false;
		}
	}
	require(DIR_WS_CLASSES . 'order.php');
    $orderDetails = new order($orders_id);
    if ($debug) {
        echo'Order Details:<pre>';
        //print_r($orderDetails);
		echo $orderDetails->info['total'].'<br>';
		echo $orderDetails->info['payment_module_code'];
        echo'</pre>';
    }
    if (!$orders or empty($orders_id) or !$orderDetails) {
        //$emailBody .= 'increment_id n/a' . $lineBreak;
		$emailBody .= 'No Order for TID : '.$_request['tid_payment']. $lineBreak;
        return false;
    }
    //check amount
    $amount = $_request['amount'];
    $order_total = $orderDetails->info['total'];
	$_amount = intval(round($order_total * 100));
	
    // $final_price = round($order_total->fields['value'], 2);
    // $_amount = isset($final_price) ? $final_price * 100 : 0;
    if (!$_amount || (intval("$_amount") != intval("$amount"))) {
        $emailBody .= "The order amount ($_amount) does not match with the request amount ($amount)$lineBreak$lineBreak";
        return false;
    }
    $paymentType = strtolower($orderDetails->info['payment_module_code']);
    if (!in_array($paymentType, array('novalnet_prepayment', 'novalnet_invoice','novalnet kauf auf rechnung','novalnet vorauskasse'))) {
        $emailBody .= "The order payment type ($paymentType) is not Prepayment!$lineBreak$lineBreak";
        return false;
    }
    return $orders_id; // == true
}

function setOrderStatus($incrementId) {
    global $lineBreak, $createInvoice, $emailBody, $orderStatus, $orderState, $tableOrderPayment, $addSubsequentTidToDb, $db;

	if ($incrementId) {
	  if ($addSubsequentTidToDb){
		$comments = ' Novalnet Callback Script executed successfully. The subsequent TID: (' . $_REQUEST['tid'] . ') on ' . date('Y-m-d H:i:s');
	  }
		$query = "SELECT orders_status from " . TABLE_ORDERS . " WHERE orders_id = '".$incrementId."' ";
		$order_qry = $db->Execute($query);
		$orders_status_id = $order_qry->fields['orders_status'];
		if($orders_status_id!= $orderState){
			$qry ="update ".TABLE_ORDERS." set orders_status = '$orderState', last_modified = now() where orders_id = '".$incrementId."' ";
			$random_query = $db->Execute($qry);
			// if ($num_rows > 1){
				### INSERT HISTORY RECORDS ###
				$customer_notified = '1';
				$new_status_qry = $db->Execute("INSERT INTO ".TABLE_ORDERS_STATUS_HISTORY." (orders_id, orders_status_id, date_added, customer_notified, comments) VALUES (".$incrementId.", ".$orderState.", NOW(), '".$customer_notified."', '".$comments."')");
				
			// }else{
				// $emailBody .= 'Updating database table ('.TABLE_ORDERS.') failed;';
				// //$emailBody .= 'Reason: '.$e->getMessage().$lineBreak.$lineBreak;
				// $emailBody .= 'Query : '.$qry.$lineBreak.$lineBreak;
				// return false;
			// }
		}
		else{
			  $emailBody .= 'Updating database table ('.TABLE_ORDERS.') failed;';
			  return false;
			}
  } else {
    $emailBody .= "Novalnet Callback: No order for Increment-ID $incrementId found.";
    return false;
  }
  $emailBody .= "succeeded.";
	
    return true;
}

function checkIP($_REQUEST) {
    global $lineBreak, $ipAllowed, $test, $emailBody;
    if ($test) {
        $ipAllowed = getRealIpAddr();
    }
    $callerIp = $_SERVER['REMOTE_ADDR'];
    if ($ipAllowed != $callerIp) {
        $emailBody .= 'Unauthorised access from the IP [' . $callerIp . ']' . $lineBreak . $lineBreak;
        $emailBody .= 'Request Params: ' . print_r($_REQUEST, true);
        return false;
    }
    return true;
}

function isPublicIP($value) {
    if (!$value || count(explode('.', $value)) != 4)
        return false;
    return !preg_match('~^((0|10|172\.16|192\.168|169\.254|255|127\.0)\.)~', $value);
}

function getRealIpAddr() {
    if (isPublicIP($_SERVER['HTTP_X_FORWARDED_FOR']))
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    if ($iplist = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])) {
        if (isPublicIP($iplist[0]))
            return $iplist[0];
    }
    if (isPublicIP($_SERVER['HTTP_CLIENT_IP']))
        return $_SERVER['HTTP_CLIENT_IP'];
    if (isPublicIP($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
        return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
    if (isPublicIP($_SERVER['HTTP_FORWARDED_FOR']))
        return $_SERVER['HTTP_FORWARDED_FOR'];
    return $_SERVER['REMOTE_ADDR'];
}
include ('includes/application_bottom.php');
?>
