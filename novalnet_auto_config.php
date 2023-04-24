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
$signature = $_REQUEST['signature'];
$access_key = $_REQUEST['access_key'];

if (!empty($signature) && !empty($access_key)) {
    $action = $_REQUEST['action'];
    $data = [
        'merchant' => [
            'signature' => $signature,
        ],
        'custom' => [
            'lang' => (isset($_REQUEST['lang'])) ? strtoupper($_REQUEST['lang']) : 'DE',
        ]
    ];

    if ($action == 'webhook_configure') {
        $data['webhook'] = [
            'url' => $_REQUEST['webhook_url']
        ];
    }

    $endpoint = NovalnetHelper::getActionEndpoint($action);
    $response = NovalnetHelper::sendRequest($data, $endpoint, $access_key);
    $response = !empty($response) ? json_encode($response) : '{}';
    echo $response;
    exit();
}
