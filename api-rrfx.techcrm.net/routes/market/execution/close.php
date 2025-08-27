<?php

use App\Models\AccountTrade;

$data = $helperClass->getSafeInput($_POST);
foreach(['login', 'ticket'] as $key) {
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

/** Check Ticket */
if(is_numeric($data['ticket']) === FALSE) {
    ApiResponse([
        'status' => false,
        'message' => "ticket must be numeric",
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

/** Request Order Send */
$login = $account['ACCTRADE_LOGIN'];
$mbrid = $userData['MBR_ID'];
$ticket = $data['ticket'];

$token = $ApiMeta->connect(['login' => $login, 'mbrid' => md5(md5($mbrid)), 'mobile' => true]);
if($token->success === FALSE) {
    ApiResponse([
        'status' => false,
        'message' => $token->error,
        'response' => []
    ], 400);
}

$token = $token->message;
$orderClose = $ApiMeta->orderClose([
    'id' => $token,
    'ticket' => $ticket,
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
    'message' => "Order closed successfully from ticket: {$ticket}",
    'response' => $orderClose->message
], 200);