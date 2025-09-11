<?php

use App\Factory\MetatraderFactory;
use App\Models\Account;
use App\Models\Helper;

$apiTerminal = MetatraderFactory::apiTerminal();
$accountLogin = Helper::form_input($_GET['account'] ?? 0);

/** Check Account */
$account = Account::realAccountDetail_byLogin($accountLogin);
if(empty($account) || $account['ACC_MBR'] != $user['MBR_ID']) {
    ApiResponse([
        'status' => false,
        'message' => "Invalid Account",
        'response' => []
    ], 400);
}

$token = MetatraderFactory::autoConnect($accountLogin);
if(!$token) {
    ApiResponse([
        'status' => false,
        'message' => "Invalid Connection (2)",
        'response' => []
    ], 400);
}

$symbols = $apiTerminal->symbols(['id' => $token]);
if(!$symbols || !is_object($symbols)) {
    ApiResponse([
        'status' => false,
        'message' => "Invalid Data",
        'response' => []
    ], 400);
}

if(!$symbols->success) {
    ApiResponse([
        'status' => false,
        'message' => $symbols->message,
        'response' => []
    ], 400);
}

$result = [];
foreach($symbols->data as $symbol) {
    $result[] = [
        'symbol' => $symbol->currency,
        'contract_size' => $symbol->contractSize,
        'spread' => $symbol->spread,
        'digits' => $symbol->digits,
        'trademode' => $symbol->trademode,
        'volume_min' => $symbol->volumeMin,
        'volume_max' => $symbol->volumeMax,
    ];
}

ApiResponse([
    'status' => true,
    'message' => 'Symbols',
    'response' => $result
], 200);