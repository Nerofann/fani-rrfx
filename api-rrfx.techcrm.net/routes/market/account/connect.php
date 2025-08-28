<?php

use App\Models\AccountTrade;

$accountId = form_input($_POST['account_id'] ?? 0);

/** Check Account */
$account = $classAcc->realAccountDetail($accountId);
if(empty($account)) {
    ApiResponse([
        'status' => false,
        'message' => "Invalid Account",
        'response' => []
    ], 400);
}

if($account['ACC_MBR'] != $userData['MBR_ID']) {
    ApiResponse([
        'status' => false,
        'message' => "Invalid Authorization on Account",
        'response' => []
    ], 400);
}

$accountTrade = AccountTrade::get($account['ACC_LOGIN']);
if(!$accountTrade) {
    $insert = $helperClass->insertWithArray("tb_racc_trade", [
        'ACCTRADE_MBR' => $account['ACC_MBR'],
        'ACCTRADE_LOGIN' => $account['ACC_LOGIN'],
        'ACCTRADE_PASS' => $account['ACC_PASS'],
        'ACCTRADE_TOKEN' => $account['ACC_TOKEN'],
        'ACCTRADE_DATETIME' => date("Y-m-d H:i:s"),
    ]);
    
    if(!$insert) {
        ApiResponse([
            'status' => false,
            'message' => "Failed to create account trading",
            'response' => []
        ], 400);
    }
}

/** Connect meta */
$connectData = [
    'login' => $account['ACC_LOGIN'], 
    'mbrid' => md5(md5($userData['MBR_ID'])), 
    'mobile' => true
];

$connect = $ApiMeta->connect($connectData);
if(!$connect->success) {
    ApiResponse([
        'status' => false,
        'message' => !empty($connect->message)? $connect->message : "Invalid Account, please update your account password",
        'response' => []
    ], 404);
}

ApiResponse([
    'status' => true,
    'message' => "Successfull",
    'response' => []
]);