<?php

use App\Models\AccountTrade;

$data = $helperClass->getSafeInput($_POST);
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

/** Check Trade Account */
$account = AccountTrade::get($data['login']);
if(empty($account)) {
    ApiResponse([
        'status' => false,
        'message' => "Invalid Account",
        'response' => []
    ], 400);
}

/** Check UserID */
if($account['ACCTRADE_MBR'] != $userData['MBR_ID']) {
    ApiResponse([
        'status' => false,
        'message' => "Authorization Failed",
        'response' => []
    ], 400);
}

/** Check Operation */
if(in_array($data['operation'], ['buy', 'sell']) === FALSE) {
    ApiResponse([
        'status' => false,
        'message' => "Operation must be buy or sell",
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
$login = $account['ACCTRADE_LOGIN'];
$mbrid = $userData['MBR_ID'];
$amount = $data['volume'];
$token = $ApiMeta->connect(['login' => $login, 'mbrid' => md5(md5($mbrid)), 'mobile' => true]);
if($token->success === FALSE) {
    ApiResponse([
        'status' => false,
        'message' => $token->error,
        'response' => []
    ], 400);
}

$token = $token->message;
$orderData = [
    'id' => $token,
    'symbol' => $data['symbol'],
    'operation' => $data['operation'],
    'volume' => $data['volume'],
];

$orderSend = $ApiMeta->orderSend($orderData);
if($orderSend->success === FALSE) {
    ApiResponse([
        'status' => false,
        'message' => $orderSend->error,
        'response' => []
    ], 400);
}

ApiResponse([
    'status' => true,
    'message' => "Order successfully opened with ticket: {$orderSend->message->ticket}",
    'response' => $orderSend->message
], 200);