<?php

use App\Factory\MetatraderFactory;
use App\Models\Account;
use App\Models\Helper;

$apiTerminal = MetatraderFactory::apiTerminal();
$data = Helper::getSafeInput($_POST);
foreach(['login', 'ticket'] as $key) {
    if(empty($data[$key])) {
        ApiResponse([
            'status' => false,
            'message' => "{$key} is required",
            'response' => []
        ], 400);
    }
}

$account = Account::realAccountDetail_byLogin($data['login']);
if(!$account || $account['ACC_MBR'] != $user['MBR_ID']) {
    ApiResponse([
        'status' => false,
        'message' => 'Invalid Account',
        'response' => []
    ]);
}

/** Get Token */
$token = MetatraderFactory::autoConnect($account['ACC_LOGIN']);
if(!$token) {
    ApiResponse([
        'status' => false,
        'message' => "Invalid Token",
        'response' => []
    ]);
}

/** Request order close */
$orderClose = $apiTerminal->orderClose([
    'id' => $token,
    'ticket' => $data['ticket'],
    'placed' => 'false'
]);

if($orderClose->success === FALSE) {
    ApiResponse([
        'status' => false,
        'message' => "Failed to close order, please check your ticket",
        'response' => []
    ], 400);
}

ApiResponse([
    'status' => true,
    'message' => "Order closed successfully from ticket: {$orderClose->data->ticket}",
    'response' => []
]);