<?php

use App\Factory\MetatraderFactory;
use App\Models\Account;
use App\Models\Helper;

$accountLogin = Helper::form_input($_POST['account'] ?? 0);
$apiTerminal = MetatraderFactory::apiTerminal();

/** Check Account */
$account = Account::realAccountDetail_byLogin($accountLogin);
if(empty($account) || $account['ACC_MBR'] != $user['MBR_ID']) {
    ApiResponse([
        'status' => false,
        'message' => "Invalid Account",
        'response' => []
    ], 400);
}

/** Connect meta */
$connectData = [
    'login' => $account['ACC_LOGIN'], 
    'password' => $account['ACC_PASS']
];

$token = $apiTerminal->connect($connectData);
if(!$token) {
    ApiResponse([
        'status' => false,
        'message' => "Invalid Connection",
        'response' => []
    ], 404);
}

ApiResponse([
    'status' => true,
    'message' => "Successfull",
    'response' => []
]);