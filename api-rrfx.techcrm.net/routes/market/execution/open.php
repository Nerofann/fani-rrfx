<?php

use App\Factory\MetatraderFactory;
use App\Models\Account;
use App\Models\Helper;

$apiTerminal = MetatraderFactory::apiTerminal();
$data = Helper::getSafeInput($_POST);
foreach(['login', 'symbol', 'operation', 'volume'] as $key) {
    if(empty($data[$key])) {
        ApiResponse([
            'status' => false,
            'message' => "{$key} is required",
            'response' => []
        ], 400);
    }
}

if(is_numeric($data['login']) === FALSE) {
    ApiResponse([
        'status' => false,
        'message' => "login must be numeric",
        'response' => []
    ], 400);
}

/** Check Account */
$account = Account::realAccountDetail_byLogin($data['login']);
if(empty($account) || $account['ACC_MBR'] != $user['MBR_ID']) {
    ApiResponse([
        'status' => false,
        'message' => "Invalid Account",
        'response' => []
    ], 400);
}

$token = MetatraderFactory::autoConnect($account["ACC_LOGIN"]);
if(!$token) {
    ApiResponse([
        'status' => false,
        'message' => "Invalid Token Connection",
        'response' => []
    ], 400);
}

/** Check Operation */
if(in_array($data['operation'], Allmedia\Shared\Metatrader\ApiVariable::operations()) === FALSE) {
    ApiResponse([
        'status' => false,
        'message' => "Invalid Operation",
        'response' => []
    ], 400);
}

/** Check Volume */
if(is_numeric($data['volume']) === FALSE) {
    ApiResponse([
        'status' => false,
        'message' => "Volume must be numeric",
        'response' => []
    ], 400);
}

if($data['volume'] <= 0) {
    ApiResponse([
        'status' => false,
        'message' => "Volume must be greater than 0",
        'response' => []
    ], 400);
}

/** Request Order Send */
$orderData = [
    'id' => $token,
    'symbol' => $data['symbol'],
    'operation' => $data['operation'],
    'volume' => $data['volume'],
];

$orderSend = $apiTerminal->orderSend($orderData);
if($orderSend->success === FALSE) {
    ApiResponse([
        'status' => false,
        'message' => $orderSend->message,
        'response' => []
    ], 400);
}

ApiResponse([
    'status' => true,
    'message' => "Order successfully opened with ticket: #{$orderSend->data->ticket}",
    'response' => $orderSend->data
], 200);