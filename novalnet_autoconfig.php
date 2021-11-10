<?php
/**
* This script is used for generate merchant details
*
* @author Novalnet AG
* @copyright Copyright (c) Novalnet
* @license https://www.novalnet.de/payment-plugins/kostenlos/lizenz
* @link https://www.novalnet.de
*
* This free contribution made by request.
*
* If you have found this script useful a small
* recommendation as well as a comment on merchant
*
* Script : novalnet_autoconfig.php
*/
class AutoConfig
{
   /**
    * Constructor
    *
    */
    public function __construct()
    {        
        $request = array_map('trim', $_POST);
            
        if (!empty($request['hash']) && !empty($request['lang'])) {
            
            require ('includes/classes/class.novalnetutil.php');
            
            $data = array(
                'hash'      => $request['hash'],
                'lang'      => $request['lang']
            );
            
            $response =  json_decode(NovalnetUtil::doPaymentCurlCall('https://payport.novalnet.de/autoconfig', $data, 'novalnet_autoconfig'));
            
            $json_error = json_last_error();

            if (empty($json_error)) {
                    if ($response->status == '100') {
                        $merchant_details = array(
                            'vendor_id'   => $response->vendor,
                            'auth_code'   => $response->auth_code,
                            'product_id'  => $response->product,
                            'access_key'  => $response->access_key,
                            'test_mode'   => $response->test_mode,
                            'tariff'      => $response->tariff,
                        );
                        echo json_encode($merchant_details);
                        exit();
                    } else {
                        if ($response->status == '106') {
                        echo sprintf(MODULE_PAYMENT_NOVALNET_CONFIG_MESSAGE, $_SERVER['SERVER_ADDR']);
                        $result = sprintf(MODULE_PAYMENT_NOVALNET_CONFIG_MESSAGE, $_SERVER['SERVER_ADDR']);
                       } else { 
                          $result = !empty($response->config_result) ? $response->config_result : $response->status_desc;
                        }
                }
                echo json_encode(array('status_desc' => $result));
                exit();
            }
        }
        echo json_encode(array('status_desc' => 'empty'));
        exit();
    }
}
new AutoConfig();
