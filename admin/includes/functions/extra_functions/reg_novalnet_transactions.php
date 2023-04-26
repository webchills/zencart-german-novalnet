<?php
/**
 * Novalnet payment module
 * admin component by webchills (www.webchills.at)
 * @author     Novalnet AG
 * @copyright  Copyright (c) Novalnet
 * @license    https://www.novalnet.de/payment-plugins/kostenlos/lizenz
 * @link       https://www.novalnet.de
 *
 * Script : reg_novalnet_transactions.php
 */
 
if (!defined('IS_ADMIN_FLAG')) {
   die('Illegal Access');
 } 
 
// This file should normally only need to be run once, but if the user hasn't installed the software properly it
// may need to be run again. Flag tracks the situation
$can_autodelete = true;
    
if (function_exists('zen_register_admin_page')) {
   if (!zen_page_key_exists('customersNovalnetTransactions')) {
   	// Quick sanity check in case user hasn't uploaded a necessary file on which this depends
		$error_messages = array();
		
		if (!defined('FILENAME_NOVALNET_TRANSACTIONS')) {
			$error_messages[] = 'The Novalnet transactions filename define is missing. Please check that the file ' .
				DIR_WS_INCLUDES . 'extra_datafiles/' . 'novalnet.php has been uploaded.';			
			$can_autodelete = false;			
		}
		
    if (count($error_messages) > 0) {
			// Let the user know that there are problem(s) with the installation
			foreach ($error_messages as $error_message) {
				print '<p style="background: #fcc; border: 1px solid #f00; margin: 1em; padding: 0.4em;">' .
					'Error: ' . $error_message . "</p>\n";
			}
		} else {
        zen_register_admin_page('customersNovalnetTransactions', 'BOX_CUSTOMERS_NOVALNET_TRANSACTIONS', 'FILENAME_NOVALNET_TRANSACTIONS','' , 'customers', 'Y', 400);
      }    
}

if ($can_autodelete) {
	// Either the config utility file has been registered, or it doesn't need to be. Can stop the wasteful process
	// of having this script run again by having it delete itself
	@unlink(DIR_WS_INCLUDES . 'functions/extra_functions/reg_novalnet_transactions.php');
}
}