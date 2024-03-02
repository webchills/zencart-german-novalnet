<?php
/**
 * Novalnet payment module
 * This script is used for auto configuring the merchant details
 *
 * @author     Novalnet AG
 * @copyright  Copyright (c) Novalnet
 * @license    https://www.novalnet.de/payment-plugins/kostenlos/lizenz
 * @link       https://www.novalnet.de
 *
 * Script : novalnet_auto_config.php
 */

require_once(__DIR__ . '/includes/modules/payment/novalnet/NovalnetHelper.php');
$request = $_REQUEST;

if (!empty($request['signature']) && !empty($request['access_key'])) {
    $action = $request['action'];
    $data = [
        'merchant' => [
            'signature' => $request['signature'],
        ],
        'custom' => [
            'lang' => (isset($request['lang'])) ? strtoupper($request['lang']) : 'DE',
        ]
    ];

    if ($action == 'webhook_configure') {
        $data['webhook'] = [
            'url' => $request['webhook_url']
        ];
    }

    $response = NovalnetHelper::sendRequest($data, NovalnetHelper::getActionEndpoint($action), $request['access_key']);
    $response = !empty($response) ? json_encode($response) : '{}';
    echo $response;
    exit();
}
