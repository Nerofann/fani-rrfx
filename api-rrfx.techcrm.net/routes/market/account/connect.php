<?php

use App\Factory\MetatraderFactory;
use App\Models\Account;
use App\Models\Helper;
use Config\Core\Database;

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

$token = MetatraderFactory::autoConnect($account['ACC_LOGIN']);
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
    'response' => [
        // 'token' => $token
    ]
]);